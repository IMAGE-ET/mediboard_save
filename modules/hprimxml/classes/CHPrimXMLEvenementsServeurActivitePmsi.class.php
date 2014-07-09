<?php

/**
 * Serveur d'activit� PMSI
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: CHPrimXMLEvenementsServeurActes.class.php 18339 2013-03-07 12:43:07Z lryo $
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEvenementsServeurActivitePmsi
 * Serveur d'activit� PMSI
 */
class CHPrimXMLEvenementsServeurActivitePmsi extends CHPrimXMLEvenements {
  /**
   * @see parent::__construct
   */
  function __construct($dirschemaname = null, $schemafilename = null) {
    $this->type = "pmsi";

    if (!$this->evenement) {
      return;
    }

    $version = CAppUI::conf("hprimxml $this->evenement version");
    // Version 1.01 : schemaPMSI - schemaServeurActe
    if ($version == "1.01") {
      parent::__construct($dirschemaname, $schemafilename."101");
    }
    // Version 1.04 - 1.05 - 1.06 - 1.07
    else {
      $version = str_replace(".", "", $version);
      parent::__construct("serveurActivitePmsi_v$version", $schemafilename.$version);
    }
  }

  /**
   * Mapping des actes
   *
   * @param array $data Datas
   *
   * @return array
   */
  function mappingServeurActes($data) {
    // Mapping patient
    $patient = $this->mappingPatient($data);

    // Mapping actes CCAM
    $actesCCAM = $this->mappingActesCCAM($data);

    return array (
      "patient"   => $patient,
      "actesCCAM" => $actesCCAM
    );
  }

  /**
   * Mapping du patient
   *
   * @param array $data Datas
   *
   * @return array
   */
  function mappingPatient($data) {
    $node = $data['patient'];
    $xpath = new CHPrimXPath($node->ownerDocument);

    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $node);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);

    return array (
      "idSourcePatient" => $data['idSourcePatient'],
      "idCiblePatient"  => $data['idCiblePatient'],
      "nom"             => $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique),
      "prenom"          => $prenoms[0],
      "naissance"       => $xpath->queryTextNode("hprim:date", $elementDateNaissance)
    );
  }

  /**
   * Mapping de la venue
   *
   * @param DOMNode $node   Node
   * @param CSejour $sejour S�jour
   *
   * @return array
   */
  function mappingVenue(DOMNode $node, CSejour $sejour) {
    // On ne r�cup�re que l'entr�e et la sortie 
    $sejour = CHPrimXMLEvenementsPatients::getEntree($node, $sejour);
    $sejour = CHPrimXMLEvenementsPatients::getSortie($node, $sejour);

    // On ne check pas la coh�rence des dates des consults/intervs
    $sejour->_skip_date_consistencies = true;

    return $sejour;
  }

  /**
   * Mapping de l'intervention
   *
   * @param array      $data      Datas
   * @param COperation $operation Intervention
   *
   * @return array
   */
  function mappingIntervention($data, COperation $operation) {
    // Intervention annul�e ?
    if ($data['action'] == "suppression") {
      $operation->annulee = 1;

      return;
    }

    $node  = $data['intervention'];

    $xpath = new CHPrimXPath($node->ownerDocument);

    $debut = $this->getDebutInterv($node);
    $fin   = $this->getFinInterv($node);

    // Traitement de la date/heure d�but, et dur�e de l'op�ration
    $operation->temp_operation = CMbDT::subTime(CMbDT::time($debut), CMbDT::time($fin));
    $operation->_time_op       = null;

    // Si une intervention du pass�e    
    if (CMbDT::date($debut) < CMbDT::date()) {
      // On affecte le d�but de l'op�ration
      if (!$operation->debut_op) {
        $operation->debut_op = CMbDT::time($debut);
      }
      // On affecte la fin de l'op�ration
      if (!$operation->fin_op) {
        $operation->fin_op = CMbDT::time($fin);
      }
    }
    // Si dans le futur
    else {
      $operation->_time_urgence  = null;
      $operation->time_operation = CMbDT::time($debut);
    }

    $operation->libelle = CMbString::capitalize($xpath->queryTextNode("hprim:libelle", $node));
    $operation->rques   = CMbString::capitalize($xpath->queryTextNode("hprim:commentaire", $node));

    // C�t�
    $cote = array (
      "D" => "droit",
      "G" => "gauche",
      "B" => "bilat�ral",
      "T" => "total",
      "I" => "inconnu"
    );
    $code_cote = $xpath->queryTextNode("hprim:cote/hprim:code", $node);
    $operation->cote = isset($cote[$code_cote]) ? $cote[$code_cote] : ($operation->cote ? $operation->cote : "inconnu");

    // Conventionn�e ?
    $operation->conventionne = $xpath->queryTextNode("hprim:convention", $node);

    // Extemporan�
    $indicateurs = $xpath->query("hprim:indicateurs/*", $node);
    foreach ($indicateurs as $_indicateur) {
      if ($xpath->queryTextNode("hprim:code", $_indicateur) == "EXT") {
        $operation->exam_extempo = true;
      }
    }

    // TypeAnesth�sie
    $this->getTypeAnesthesie($node, $operation);

    $operation->duree_uscpo = $xpath->queryTextNode("hprim:dureeUscpo", $node);
  }

  /**
   * R�cup�ration du type d'anesth�sie
   *
   * @param DOMNode    $node      Node
   * @param COperation $operation Intervention
   *
   * @return void
   */
  function getTypeAnesthesie(DOMNode $node, COperation $operation) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    if (!$typeAnesthesie = $xpath->queryTextNode("hprim:typeAnesthesie", $node)) {
      return;
    }

    $operation->type_anesth = CIdSante400::getMatch("CTypeAnesth", $this->_ref_sender->_tag_hprimxml, $typeAnesthesie)->object_id;
  }

  /**
   * R�cup�ration de la plage de l'intervention
   *
   * @param DOMNode    $node      Node
   * @param COperation $operation Intervention
   *
   * @return void
   */
  function mappingPlage(DOMNode $node, COperation $operation) {
    $debut = $this->getDebutInterv($node);

    // Traitement de la date/heure d�but, et dur�e de l'op�ration
    $date_op  = CMbDT::date($debut);
    $time_op  = CMbDT::time($debut);

    // Recherche d'une �ventuelle plageOp avec la salle
    $plageOp           = new CPlageOp();
    $plageOp->chir_id  = $operation->chir_id;
    $plageOp->salle_id = $operation->salle_id;
    $plageOp->date     = $date_op;
    $plageOps          = $plageOp->loadMatchingList();

    // Si on a pas de plage on recherche �ventuellement une plage dans une autre salle
    if (count($plageOps) == 0) {
      $plageOp->salle_id = null;
      $plageOps = $plageOp->loadMatchingList();

      // Si on retrouve des plages alors on ne prend pas en compte la salle du flux
      if (count($plageOps) > 0) {
        $operation->salle_id = null;
      }
    }

    foreach ($plageOps as $_plage) {
      // Si notre intervention est dans la plage Mediboard
      if (CMbRange::in($time_op, $_plage->debut, $_plage->fin)) {
        $plageOp = $_plage;
        break;
      }
    }

    if ($plageOp->_id) {
      $operation->plageop_id = $plageOp->_id;
    }
    else {
      // Dans le cas o� l'on avait une plage sur l'interv on la supprime
      $operation->plageop_id = "";

      $operation->date = $date_op;
    }
  }

  /**
   * R�cup�ration du d�but de l'intervention
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  function getDebutInterv(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    return $this->getDateHeure($xpath->queryUniqueNode("hprim:debut", $node, false));
  }

  /**
   * R�cup�ration de la fin de l'intervention
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  function getFinInterv(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    return $this->getDateHeure($xpath->queryUniqueNode("hprim:fin", $node, false));
  }

  /**
   * R�cup�ration des participants de l'intervention
   *
   * @param DOMNode $node   Node
   * @param CSejour $sejour S�jour
   *
   * @return string
   */
  function getParticipant(DOMNode $node, CSejour $sejour = null) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    $adeli = $xpath->queryTextNode("hprim:participants/hprim:participant/hprim:medecin/hprim:numeroAdeli", $node);

    // Recherche du mediuser
    $mediuser = new CMediusers();
    if (!$adeli) {
      return $mediuser;
    }

    $where = array(
      "users_mediboard.adeli"        => $mediuser->_spec->ds->prepare("=%", $adeli),
      "functions_mediboard.group_id" => "= '$sejour->group_id'"
    );
    $ljoin = array(
      "functions_mediboard" => "functions_mediboard.function_id = users_mediboard.function_id"
    );

    $mediuser->loadObject($where, null, null, $ljoin);

    return $mediuser;
  }

  /**
   * R�cup�ration de la salle
   *
   * @param DOMNode $node   Node
   * @param CSejour $sejour S�jour
   *
   * @return string
   */
  function getSalle(DOMNode $node, CSejour $sejour) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    $name = $xpath->queryTextNode("hprim:uniteFonctionnelle/hprim:code", $node);

    // Recherche de la salle
    $salle = new CSalle();

    $where = array(
      "sallesbloc.nom"           => $salle->_spec->ds->prepare("=%", $name),
      "bloc_operatoire.group_id" => "= '$sejour->group_id'"
    );
    $ljoin = array(
      "bloc_operatoire" => "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id"
    );

    $salle->loadObject($where, null, null, $ljoin);

    return $salle;
  }

  /**
   * Mapping des actes CCAM
   *
   * @param array $data Datas
   *
   * @return array
   */
  function mappingActesCCAM($data) {
    $node = $data['actesCCAM'];

    $actesCCAM = array();

    if (!$node) {
      return $actesCCAM;
    }

    foreach ($node->childNodes as $_acteCCAM) {
      $actesCCAM[] = $this->mappingActeCCAM($_acteCCAM, $data);
    }

    return $actesCCAM;
  }

  /**
   * Mapping des actes CCAM
   *
   * @param array $data Datas
   *
   * @return array
   */
  function mappingActesNGAP($data) {
    $node = $data['actesNGAP'];
    $actesNGAP = array();

    if (!$node) {
      return $actesNGAP;
    }

    foreach ($node->childNodes as $_acteNGAP) {
      $actesNGAP[] = $this->mappingActeNGAP($_acteNGAP, $data);
    }

    return $actesNGAP;
  }

  /**
   * Mapping des actes CCAM
   *
   * @param DOMNode $node Node
   * @param array   $data Datas
   *
   * @return array
   */
  function mappingActeCCAM(DOMNode $node, $data) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    $acteCCAM = array();
    $acteCCAM["code_acte"]     = $xpath->queryTextNode("hprim:codeActe"           , $node);
    $acteCCAM["code_activite"] = $xpath->queryTextNode("hprim:codeActivite"       , $node);
    $acteCCAM["code_phase"]    = $xpath->queryTextNode("hprim:codePhase"          , $node);
    $acteCCAM["date"]          = $xpath->queryTextNode("hprim:execute/hprim:date" , $node);
    $acteCCAM["heure"]         = $xpath->queryTextNode("hprim:execute/hprim:heure", $node);

    $acteCCAM["modificateur"] = array();
    $modificateurs = $xpath->query("hprim:modificateurs/hprim:modificateur", $node);
    foreach ($modificateurs as $_modificateur) {
      if ($modificateur = $xpath->queryTextNode(".", $_modificateur)) {
        $acteCCAM["modificateur"][] = $modificateur;
      }
    }

    $acteCCAM["commentaire"]              = $xpath->queryTextNode("hprim:commentaire", $node);
    $acteCCAM["signe"]                    = $xpath->queryAttributNode(".", $node, "signe");
    $acteCCAM["facturable"]               = $xpath->queryAttributNode(".", $node, "facturable");
    $acteCCAM["rembourse"]                = $xpath->queryAttributNode(".", $node, "remboursementExceptionnel");
    $acteCCAM["charges_sup"]              = $xpath->queryAttributNode(".", $node, "supplementCharges");
    $acteCCAM                             = array_merge($acteCCAM, $this->getMontant($node));

    $position_dentaire                    = $xpath->query("hprim:positionsDentaires/hprim:positionDentaire");
    $acteCCAM["position_dentaire"] = array();
    foreach ($position_dentaire as $_position_dentaire) {
      if ($dent = $xpath->queryTextNode(".", $_position_dentaire)) {
        $acteCCAM["position_dentaire"][] = $dent;
      }
    }

    $acteCCAM["code_association"]    = $xpath->queryTextNode("hprim:codeAssociationNonPrevue", $node);
    $acteCCAM["code_extension"]      = $xpath->queryTextNode("hprim:codeExtensionDocumentaire", $node);
    $acteCCAM["rapport_exoneration"] = $xpath->queryAttributNode(".", $node, "rapportExoneration");

    $idSourceActesCCAM = $this->getIdSource($node, false);
    $idCibleActesCCAM  = $this->getIdCible($node , false);

    $medecin = $xpath->queryUniqueNode("hprim:executant/hprim:medecins/hprim:medecinExecutant[@principal='oui']/hprim:medecin", $node);
    //si pas de medecin principal, on recherche le premier m�decin ex�cutant
    if (!$medecin) {
      $medecin = $xpath->getNode("hprim:executant/hprim:medecins/hprim:medecinExecutant/hprim:medecin", $node);
    }
    $mediuser_id = $this->getMedecin($medecin);
    $action = $xpath->queryAttributNode(".", $node, "action");

    return array (
      "idSourceIntervention" => $data['idSourceIntervention'],
      "idCibleIntervention"  => $data['idCibleIntervention'],
      "idSourceActeCCAM"     => $idSourceActesCCAM,
      "idCibleActeCCAM"      => $idCibleActesCCAM,
      "action"               => $action,
      "acteCCAM"             => $acteCCAM,
      "executant_id"         => $mediuser_id,
    );
  }

  /**
   * Mapping des actes NGAP
   *
   * @param DOMNode $node Node
   * @param array   $data Datas
   *
   * @return array
   */
  function mappingActeNGAP(DOMNode $node, $data) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    $acteNGAP = array();
    $acteNGAP["code"]                       = $xpath->queryTextNode("hprim:lettreCle"          , $node);
    $acteNGAP["coefficient"]                = $xpath->queryTextNode("hprim:coefficient"        , $node);
    $acteNGAP["quantite"]                   = $xpath->queryTextNode("hprim:quantite"           , $node);
    $acteNGAP["date"]                       = $xpath->queryTextNode("hprim:execute/hprim:date" , $node);
    $acteNGAP["heure"]                      = $xpath->queryTextNode("hprim:execute/hprim:heure", $node);
    $acteNGAP["numero_dent"]                = $xpath->queryTextNode("hprim:positionDentaire"   , $node);
    $acteNGAP["comment"]                    = $xpath->queryTextNode("hprim:commentaire"        , $node);
    $acteNGAP                               = array_merge($acteNGAP, $this->getMontant($node));

    $minoration                             = $xpath->queryUniqueNode("hprim:minorMajor/hprim:minoration", $node);
    $acteNGAP["minor_pct"]                  = $xpath->queryTextNode("hprim:pourcentage", $minoration);
    $acteNGAP["minor_coef"]                 = $xpath->queryTextNode("hprim:coefficient", $minoration);
    $majoration                             = $xpath->queryUniqueNode("hprim:minorMajor/hprim:majoration", $node);
    $acteNGAP["major_pct"]                  = $xpath->queryTextNode("hprim:pourcentage", $majoration);
    $acteNGAP["major_coef"]                 = $xpath->queryTextNode("hprim:coefficient", $majoration);

    $acteNGAP["facturable"]                 = $xpath->queryAttributNode(".", $node, "facturable");
    $acteNGAP["rapportExoneration"]         = $xpath->queryAttributNode(".", $node, "rapportExoneration");
    $acteNGAP["executionNuit"]              = $xpath->queryAttributNode(".", $node, "executionNuit");
    $acteNGAP["executionDimancheJourFerie"] = $xpath->queryAttributNode(".", $node, "executionDimancheJourFerie");

    $medecin  = $xpath->query("hprim:prestataire/hprim:medecins/hprim:medecin", $node);
    $mediuser_id = $this->getMedecin($medecin->item(0));

    $idSourceActeNGAP = $this->getIdSource($node, false);
    $idCibleActeNGAP  = $this->getIdCible($node, false);
    $action = $xpath->queryAttributNode(".", $node, "action");

    return array (
      "idSourceIntervention" => $data['idSourceIntervention'],
      "idCibleIntervention"  => $data['idCibleIntervention'],
      "idSourceActeNGAP"     => $idSourceActeNGAP,
      "idCibleActeNGAP"      => $idCibleActeNGAP,
      "action"               => $action,
      "acteNGAP"             => $acteNGAP,
      "executant_id"         => $mediuser_id,
    );
  }

  /**
   * Mapp the montant node
   *
   * @param DOMNode $node Node
   *
   * @return array
   */
  function getMontant($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    $data = array();
    $montant = $xpath->queryUniqueNode("hprim:montant", $node);
    $data["montantTotal"]           = $xpath->queryTextNode("montantTotal"          , $montant);
    $data["numeroForfaitTechnique"] = $xpath->queryTextNode("numeroForfaitTechnique", $montant);
    $data["numeroAgrementAppareil"] = $xpath->queryTextNode("numeroAgrementAppareil", $montant);
    $data["montantDepassement"]     = $xpath->queryTextNode("montantDepassement"    , $montant);

    return $data;
  }

  /**
   * Enregistrement des donn�es du serveur d'activit� PMSI
   *
   * @param CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq  DOM Acquittement
   * @param CMbObject                                 $mbObject Object
   * @param array                                     $data     Data that contain the nodes
   *
   * @return string Acquittement
   **/
  function handle(CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq, CMbObject $mbObject, $data) {
  }
}