<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CHPrimXMLEvenementsPatients
 * Patients
 */

class CHPrimXMLEvenementsPatients extends CHPrimXMLEvenements {
  static $evenements = array(
    'enregistrementPatient' => "CHPrimXMLEnregistrementPatient",
    'fusionPatient'         => "CHPrimXMLFusionPatient",
    'venuePatient'          => "CHPrimXMLVenuePatient",
    'fusionVenue'           => "CHPrimXMLFusionVenue",
    'mouvementPatient'      => "CHPrimXMLMouvementPatient",
    'debiteursVenue'        => "CHPrimXMLDebiteursVenue"
  );

  /**
   * Get version
   *
   * @return string
   */
  static function getVersionEvenementsPatients() {    
    return "msgEvenementsPatients".str_replace(".", "", CAppUI::conf('hprimxml evt_patients version'));
  }

  /**
   * Get event
   *
   * @param string $messagePatient Message
   *
   * @return CHPrimXMLEvenementsPatients|void
   */
  static function getHPrimXMLEvenements($messagePatient) {
    $hprimxmldoc = new CHPrimXMLDocument("patient", self::getVersionEvenementsPatients());
    // R�cup�ration des informations du message XML
    $hprimxmldoc->loadXML($messagePatient);
    
    $type = $hprimxmldoc->getTypeEvenementPatient();

    if ($type) {
      return new self::$evenements[$type];
    } 

    return new CHPrimXMLEvenementsPatients();
  }

  /**
   * Construct
   *
   * @return CHPrimXMLEvenementsPatients
   */
  function __construct() {
    $this->evenement = "evt_patients";
    $this->type = "patients";
                
    parent::__construct("patients", self::getVersionEvenementsPatients());
  }

  /**
   * Get events
   *
   * @return array
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsPatients", false);
  }

  /**
   * Mapping patient
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  function mappingPatient(DOMNode $node, CPatient $mbPatient) {
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    $sender = $this->_ref_echange_hprim->_ref_sender;

    if (isset($sender->_configs) && array_key_exists("fully_qualified", $sender->_configs) && !$sender->_configs["fully_qualified"]) {
      $mbPatient->nullifyAlteredFields();
    }
    
    return $mbPatient;
  }

  /**
   * Get
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Person
   *
   * @return CMbObject|CMediusers|CPatient
   */
  static function getPersonnePhysique(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    // Cr�ation de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $node);
    
    $sexe = $xpath->queryAttributNode("hprim:personnePhysique", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );
    $mbPatient->sexe = $sexeConversion[$sexe];
    
    // R�cup�ration du typePersonne
    $mbPatient = self::getPersonne($personnePhysique, $mbPatient);
    
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $xpath->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance       = $xpath->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $xpath->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance         = $xpath->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }

  /**
   * R�cup�rer l'activit� socio-professionnelle
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  static function getActiviteSocioProfessionnelle(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $activiteSocioProfessionnelle = $xpath->queryTextNode("hprim:activiteSocioProfessionnelle", $node);
    
    $mbPatient->profession = $activiteSocioProfessionnelle ? $activiteSocioProfessionnelle : null;
    
    return $mbPatient;
  }

  /**
   * R�cup�rer les personnes � pr�venir
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  static function getPersonnesPrevenir(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $personnesPrevenir = $xpath->query("hprim:personnesPrevenir/*", $node);
    
    foreach ($personnesPrevenir as $personnePrevenir) {
      $prevenir = new CCorrespondantPatient;
      $prevenir->relation = "prevenir";
      $prevenir->nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePrevenir);
      $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePrevenir);
      $prevenir->prenom = CMbArray::get($prenoms, 0);
      
      $adresses = $xpath->queryUniqueNode("hprim:adresses", $personnePrevenir);
      $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
      $prevenir->adresse = $xpath->queryTextNode("hprim:ligne", $adresse);
      $prevenir->ville = $xpath->queryTextNode("hprim:ville", $adresse);
      $prevenir->cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
      
      $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $personnePrevenir);
      $prevenir->tel = CMbArray::get($telephones, 0);
      
      $mbPatient->_ref_correspondants_patient[] = $prevenir;
    }
    
    return $mbPatient;
  }

  /**
   * V�rifier si les patients sont similaires
   *
   * @param CPatient $mbPatient  Patient
   * @param DOMNode  $xmlPatient Patient provenant des donn�es XML
   *
   * @return string
   */
  function checkSimilarPatient(CPatient $mbPatient, $xmlPatient) {
    $sender = $this->_ref_sender;

    if (!$sender->_configs || (isset($sender->_configs) && array_key_exists("check_similar", $sender->_configs) && !$sender->_configs["check_similar"])) {
      return null;
    }

    $xpath = new CHPrimXPath($this);
        
    // Cr�ation de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);

    $nom     = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom  = CMbArray::get($prenoms, 0);

    $commentaire = null;

    if (!$mbPatient->checkSimilar($nom, $prenom)) {
      $commentaire = "Le nom ($nom/$mbPatient->nom) et/ou le pr�nom ($prenom/$mbPatient->prenom) sont tr�s diff�rents.";
    }

    return $commentaire;
  }

  /**
   * Get source ID
   *
   * @param string $query_evt  Event
   * @param string $query_type Type
   *
   * @return string
   */
  function getIdSourceObject($query_evt, $query_type) { 
    $xpath = new CHPrimXPath($this);
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $typeEvenement    = $xpath->queryUniqueNode($query_evt, $evenementPatient);
    
    $object = $xpath->queryUniqueNode($query_type, $typeEvenement);

    return $this->getIdSource($object);
  }

  /**
   * Mapping admit
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Admit
   * @param bool    $cancel  Cancel
   *
   * @return CSejour
   */
  function mappingVenue(DOMNode $node, CSejour $mbVenue, $cancel = false) {
    // Si annulation
    if ($cancel) {
      $mbVenue->annule = 1;
      
      return $mbVenue;
    }
    
    $mbVenue = $this->getNatureVenue($node, $mbVenue);
    $mbVenue = self::getEntree($node, $mbVenue);
    $mbVenue = $this->getMedecins($node, $mbVenue);
    $mbVenue = self::getPlacement($node, $mbVenue);
    $mbVenue = self::getSortie($node, $mbVenue);

    /* TODO Supprimer ceci apr�s l'ajout des times picker */
    $mbVenue->_hour_entree_prevue = null;
    $mbVenue->_min_entree_prevue = null;
    $mbVenue->_hour_sortie_prevue = null;
    $mbVenue->_min_sortie_prevue = null;
    
    return $mbVenue;
  }

  /**
   * Admit ?
   *
   * @param CSejour $mbVenue Admit
   * @param array   $data    Datas
   *
   * @return bool
   */
  function admitFound(CSejour $mbVenue, $data) {
    $sender  = $this->_ref_sender;

    $idSourceVenue = CValue::read($data, "idSourceVenue");
    $idCibleVenue  = CValue::read($data, "idCibleVenue");

    $NDA = new CIdSante400();
    if ($idSourceVenue) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $idSourceVenue);
    }

    if ($NDA->_id) {
      $mbVenue->load($NDA->object_id);

      return true;
    }

    if ($mbVenue->load($idCibleVenue)) {
      return true;
    }

    return false;
  }

  /**
   * Get admit attributes
   *
   * @param DOMNode $node Node
   *
   * @return array
   */
  static function getAttributesVenue(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
        
    $attributes = array();
    $attributes['confidentiel'] = $xpath->getValueAttributNode($node, "confidentiel"); 
    $attributes['etat'] = $xpath->getValueAttributNode($node, "etat"); 
    $attributes['facturable'] = $xpath->getValueAttributNode($node, "facturable"); 
    $attributes['declarationMedecinTraitant'] = $xpath->getValueAttributNode($node, "declarationMedecinTraitant"); 
    
    return $attributes;
  }

  /**
   * Get admit state
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  static function getEtatVenue(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->getValueAttributNode($node, "etat"); 
  }

  /**
   * R�cup�ration de la nature de la venue
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Venue
   *
   * @return CSejour
   */
  function getNatureVenue(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    if ((CAppUI::conf("dPpmsi passage_facture") == "reception") && self::getEtatVenue($node) == "cl�tur�e") {
      $mbVenue->facture = 1;
    }
        
    $sender = $this->_ref_echange_hprim->_ref_sender;

    // Obligatoire pour MB
    $nature = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur", "", false);
    $attrNatureVenueHprim = array (
      "hsp"  => "comp",
      "cslt" => "consult",
      "sc"   => "seances",
      "ambu" => "ambu",
      "exte" => "exte",
    );

    // D�termine le type de venue depuis la config des num�ros de dossier 
    $type_config = self::getVenueType($sender, $mbVenue->_NDA);
    if ($type_config) {
      $mbVenue->type = $type_config;
    }

    // Cas des urgences : dans tous les cas ce sera de l'hospi comp.
    $rpu = $mbVenue->loadRefRPU();
    if ($rpu && $rpu->_id && $rpu->sejour_id == $rpu->mutation_sejour_id) {
      $mbVenue->type = "comp";
    }

    if (!$mbVenue->type) {
      if ($nature) {
        $mbVenue->type = $attrNatureVenueHprim[$nature];
      }
    }

    if (!$mbVenue->type) {      
      $mbVenue->type = "comp";
    }
    
    return $mbVenue;
  }

  /**
   * Mapping des types de la venue
   *
   * @param CInteropSender $sender Sender
   * @param string         $nda    NDA
   *
   * @return string|null
   */
  static function getVenueType(CInteropSender $sender, $nda) {
    $types = array(
      "type_sej_hospi"   => "comp",
      "type_sej_ambu"    => "ambu",
      "type_sej_urg"     => "urg",
      "type_sej_exte"    => "exte",
      "type_sej_scanner" => "seances",
      "type_sej_chimio"  => "seances",
      "type_sej_dialyse" => "seances",
    );

    if (!$sender->_configs) {
      return null;
    }

    foreach ($types as $config => $type) {
      if (!$sender->_configs[$config]) {
        continue;
      }
      
      if (preg_match($sender->_configs[$config], $nda)) {
        return $type;
      }
    }

    return null;
  }

  /**
   * R�cup�ration de l'entr�e
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Venue
   *
   * @return CSejour
   */
  static function getEntree(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $entree = $xpath->queryUniqueNode("hprim:entree", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $entree);
    $heure = CMbDT::transform($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $entree), null , "%H:%M:%S");

    $xpath->queryAttributNode("hprim:modeEntree", $entree, "valeur");
    
    $dateHeure = "$date $heure";

    if ($mbVenue->entree_reelle && CAppUI::conf("hprimxml notifier_entree_reelle")) {
      $mbVenue->entree_reelle = $dateHeure;
    }
    else {
      $mbVenue->entree_prevue = $dateHeure;
    }
       
    return $mbVenue;
  }

  /**
   * Est-ce que la venue � un praticien ?
   *
   * @param DOMNode $node Node
   *
   * @return bool
   */
  static function isVenuePraticien(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $medecins = $xpath->queryUniqueNode("hprim:medecins", $node);
    
    if (!is_array($medecins)) {
      return false;
    }
    
    $medecin = $medecins->childNodes;
    foreach ($medecin as $_med) {
      $lien = $xpath->getValueAttributNode($_med, "lien");
      if ($lien != "rsp") {
        return false;
      }
    }
    
    return true;
  }

  /**
   * R�cup�ration des m�decins
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Venue
   *
   * @return CSejour
   */
  function getMedecins(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $sender = $this->_ref_echange_hprim->_ref_sender;
    $medecins = $xpath->queryUniqueNode("hprim:medecins", $node);
    if ($medecins instanceof DOMElement) {
      $medecin = $medecins->childNodes;
      
      foreach ($medecin as $_med) {
        $mediuser_id = $this->getMedecin($_med);
        $lien = $xpath->getValueAttributNode($_med, "lien");
        if ($lien == "rsp") {
          $mbVenue->praticien_id = $mediuser_id;
        }
      } 
    }
    
    // Dans le cas ou la venue ne contient pas de medecin responsable
    // Attribution d'un medecin indetermin�
    if (!$mbVenue->praticien_id) {
      $user = new CUser();
      $mediuser = new CMediusers();
      $user->user_last_name = CAppUI::conf("hprimxml medecinIndetermine")." $sender->group_id";
      if (!$user->loadMatchingObject()) {
        $mediuser->_user_last_name = $user->user_last_name;
        $mediuser->_id = $this->createPraticien($mediuser);
      }
      else {
        $user->loadRefMediuser();
        $mediuser = $user->_ref_mediuser;
      }
      $mbVenue->praticien_id = $mediuser->_id;
    }

    return $mbVenue;
  }

  /**
   * R�cup�ration du placement
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Venue
   *
   * @return CSejour
   */
  static function getPlacement(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $placement = $xpath->queryUniqueNode("hprim:Placement", $node);
    
    if ($placement) {
      $mbVenue->modalite = $xpath->queryAttributNode("hprim:modePlacement", $placement, "modaliteHospitalisation");
    }
    
    return $mbVenue;
  }

  /**
   * R�cup�ration de la sortie
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Venue
   *
   * @return CSejour
   */
  static function getSortie(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $sortie = $xpath->queryUniqueNode("hprim:sortie", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $sortie);
    $heure = CMbDT::transform($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $sortie), null , "%H:%M:%S");
    if ($date) {
      $dateHeure = "$date $heure";
    }
    elseif (!$date && !$mbVenue->sortie_prevue) {
      $config = CAppUI::conf("dPplanningOp CSejour sortie_prevue ".$mbVenue->type);
      $dateHeure = CMbDT::addDateTime($config.":00:00", $mbVenue->entree_reelle ? $mbVenue->entree_reelle : $mbVenue->entree_prevue);
    }
    else {
      $dateHeure = $mbVenue->sortie_reelle ? $mbVenue->sortie_reelle : $mbVenue->sortie_prevue;
    }
    
    // Cas dans lequel on ne r�cup�re pas de sortie tant que l'on a pas la sortie r�elle
    if ($mbVenue->sortie_reelle && CAppUI::conf("hprimxml notifier_sortie_reelle")) {
      $mbVenue->sortie_reelle = $dateHeure;
    }
    else {
      $mbVenue->sortie_prevue = $dateHeure;
    }
    
    $modeSortieHprim = $xpath->queryAttributNode("hprim:modeSortieHprim", $sortie, "valeur");
    if (!$modeSortieHprim) {
      return $mbVenue;

    }
    // d�c�s
    switch ($modeSortieHprim) {
      case "05" :
        $mbVenue->mode_sortie = "deces";
        break;

      case "02" :
        // autre transfert dans un autre CH
        $mbVenue->mode_sortie = "transfert";

        $destination = $xpath->queryUniqueNode("hprim:destination", $sortie);
        if ($destination) {
          $mbVenue = self::getEtablissementTransfert($mbVenue);
        }
        break;

      default :
        //retour au domicile
        $mbVenue->mode_sortie = "normal";
        break;
    }

    return $mbVenue;
  }

  /**
   * R�cup�ration de l'�tablissement de transfert
   *
   * @param CSejour $mbVenue Venue
   *
   * @return mixed
   */
  static function getEtablissementTransfert(CSejour $mbVenue) {
    return $mbVenue->etablissement_sortie_id;
  }

  /**
   * Mapping mouvements
   *
   * @param DOMNode $node     Node
   * @param CSejour $newVenue Venue
   *
   * @return CSejour
   */
  function mappingMouvements(DOMNode $node, CSejour $newVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    $movements = $xpath->query("hprim:mouvement", $node);

    foreach ($movements as $_movement) {
      $affectation = new CAffectation();

      if ($msg = $this->mappingMovement($_movement, $newVenue, $affectation)) {
        return $msg;
      }
    }

    return null;
  }

  /**
   * Mapping mouvements
   *
   * @param DOMNode      $node        Node
   * @param CSejour      $newVenue    Venue
   * @param CAffectation $affectation Affectation
   *
   * @return string
   */
  function mappingMovement(DOMNode $node, CSejour $newVenue, CAffectation $affectation) {
    $xpath  = new CHPrimXPath($node->ownerDocument);
    $sender = $this->_ref_echange_hprim->_ref_sender;

    // Recherche d'une affectation existante
    $id = $newVenue->_guid."-".$xpath->queryTextNode("hprim:identifiant/hprim:emetteur", $node);

    $tag = $sender->_tag_hprimxml;

    $idex = CIdSante400::getMatch("CAffectation", $tag, $id);
    if ($idex->_id) {
      $affectation->load($idex->object_id);

      if ($affectation->sejour_id != $newVenue->_id) {
        return CAppUI::tr("hprimxml-error-E301");
      }
    }

    $affectation->sejour_id = $newVenue->_id;

    // Praticien responsable
    $medecinResponsable = $xpath->queryUniqueNode("hprim:medecinResponsable", $node);
    $affectation->praticien_id = $this->getMedecin($medecinResponsable);

    // Emplacement
    $this->getEmplacement($node, $newVenue, $affectation);

    // D�but de l'affectation
    $debut = $xpath->queryUniqueNode("hprim:debut", $node);
    $date  = $xpath->queryTextNode("hprim:date", $debut);
    $heure = CMbDT::transform($xpath->queryTextNode("hprim:heure", $debut), null , "%H:%M:%S");

    $affectation->entree = "$date $heure";

    // Fin de l'affectation
    $fin = $xpath->queryUniqueNode("hprim:fin", $node);
    if ($fin) {
      $date  = $xpath->queryTextNode("hprim:date", $fin);
      $heure = CMbDT::transform($xpath->queryTextNode("hprim:heure", $fin), null , "%H:%M:%S");

      $affectation->sortie = "$date $heure";
    }

    if (!$affectation->_id) {
      $affectation = $newVenue->forceAffectation($affectation);
      if (is_string($affectation)) {
        return $affectation;
      }
    }
    else {
      if ($msg = $affectation->store()) {
        return $msg;
      }
    }

    if (!$idex->_id) {
      $idex->object_id = $affectation->_id;
      if ($msg = $idex->store()) {
        return $msg;
      }
    }

    return null;
  }

  /**
   * R�cup�ration de l'emplacement du patient
   *
   * @param DOMNode      $node        Node
   * @param CSejour      $newVenue    Sejour
   * @param CAffectation $affectation Affectation
   *
   * @return void
   */
  function getEmplacement(DOMNode $node, CSejour $newVenue, CAffectation $affectation) {
    $xpath  = new CHPrimXPath($node->ownerDocument);
    $sender = $this->_ref_echange_hprim->_ref_sender;

    $chambreSeul = $xpath->queryAttributNode("hprim:emplacement", $node, "chambreSeul");
    if ($chambreSeul) {
      $newVenue->chambre_seule = $chambreSeul == "oui" ? 1 : 0;
    }

    $emplacement = $xpath->queryUniqueNode("hprim:emplacement", $node);

    // R�cup�ration de la chambre
    $chambre_node = $xpath->queryUniqueNode("hprim:chambre", $emplacement);
    $nom_chambre  = $xpath->queryTextNode("hprim:code", $chambre_node);
    $chambre = new CChambre();

    // R�cup�ration du lit
    $lit_node = $xpath->queryUniqueNode("hprim:lit", $emplacement);
    $nom_lit  = $xpath->queryTextNode("hprim:code", $lit_node);
    $lit = new CLit();

    $where = $ljoin = array();
    $ljoin["service"]     = "service.service_id = chambre.service_id";
    $where["chambre.nom"] = " = '$nom_chambre'";
    $where["group_id"]    = " = '$sender->group_id'";

    $chambre->escapeValues();
    $chambre->loadObject($where, null, null, $ljoin);
    $chambre->unescapeValues();

    $where = $ljoin = array();

    $ljoin["chambre"]  = "chambre.chambre_id = lit.chambre_id";
    $ljoin["service"]  = "service.service_id = chambre.service_id";
    $where["lit.nom"]      = " = '$nom_lit'";
    $where["group_id"] = " = '$sender->group_id'";
    if ($chambre->_id) {
      $where["chambre.chambre_id"] = " = '$chambre->_id'";
    }

    $lit->escapeValues();
    $lit->loadObject($where, null, null, $ljoin);
    $lit->unescapeValues();

    // Affectation du lit
    $affectation->lit_id = $lit->_id;
  }

  /**
   * R�cup�ration du m�decin responsable
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Venue
   *
   * @return CSejour
   */
  function getMedecinResponsable(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);    
    
    $medecinResponsable = $xpath->queryUniqueNode("hprim:medecinResponsable", $node);
    
    if ($medecinResponsable) {
      $mbVenue->praticien_id = $this->getMedecin($medecinResponsable);
    }
    
    return $mbVenue;
  }

  /**
   * Mapping d�biteurs
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  function mappingDebiteurs(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    /* @FIXME Penser a parcourir tous les debiteurs par la suite */
    $debiteur = $xpath->queryUniqueNode("hprim:debiteur", $node);

    $mbPatient = $this->getAssurance($debiteur, $mbPatient);
     
    return $mbPatient;
  }

  /**
   * R�cup�r�ration de l'assurance
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  static function getAssurance(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);  
    
    $assurance = $xpath->queryUniqueNode("hprim:assurance", $node);
    
    // Obligatoire pour MB
    $assure = $xpath->queryUniqueNode("hprim:assure", $assurance, false);
    $mbPatient = self::getAssure($assure, $mbPatient);
    
    $dates = $xpath->queryUniqueNode("hprim:dates", $assurance);
    $mbPatient->deb_amo = $xpath->queryTextNode("hprim:dateDebutDroit", $dates);
    $mbPatient->fin_amo = $xpath->queryTextNode("hprim:dateFinDroit", $dates);
    
    $obligatoire = $xpath->queryUniqueNode("hprim:obligatoire", $assurance);
    $mbPatient->code_regime = $xpath->queryTextNode("hprim:grandRegime", $obligatoire);
    $mbPatient->caisse_gest = $xpath->queryTextNode("hprim:caisseAffiliation", $obligatoire);
    $mbPatient->centre_gest = $xpath->queryTextNode("hprim:centrePaiement", $obligatoire);
    
    return $mbPatient;
  }

  /**
   * R�cup�ration de l'assur�
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  static function getAssure(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);  
    
    $immatriculation = $xpath->queryTextNode("hprim:immatriculation", $node);
    $mbPatient->matricule = $immatriculation;
    $mbPatient->assure_matricule = $immatriculation;
    
    $personne = $xpath->queryUniqueNode("hprim:personne", $node);
    if (!$personne) {
      return $mbPatient;
    }

    $sexe = $xpath->queryAttributNode("hprim:personne", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );

    $mbPatient->assure_sexe = $sexeConversion[$sexe];
    $mbPatient->assure_nom = $xpath->queryTextNode("hprim:nomUsuel", $personne);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personne);
    $mbPatient->assure_prenom   = CMbArray::get($prenoms, 0);
    $mbPatient->assure_prenom_2 = CMbArray::get($prenoms, 1);
    $mbPatient->assure_prenom_3 = CMbArray::get($prenoms, 2);
    $mbPatient->assure_naissance = $xpath->queryTextNode("hprim:naissance", $personne);

    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personne);
    $mbPatient->assure_naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    $mbPatient->rang_beneficiaire = $xpath->queryTextNode("hprim:lienAssure", $node);
    $mbPatient->qual_beneficiaire = CValue::read(CPatient::$rangToQualBenef, $mbPatient->rang_beneficiaire);

    return $mbPatient;
  }

  /**
   * Annulation du s�jour ?
   *
   * @param CSejour                        $venue      Venue
   * @param CHPrimXMLAcquittementsPatients $dom_acq    Acquittement
   * @param CEchangeHprim                  $echg_hprim Echange H'XML
   *
   * @return null|string
   */
  function doNotCancelVenue(CSejour $venue, $dom_acq, $echg_hprim) {
    // Impossible d'annuler un s�jour en cours 
    if ($venue->entree_reelle) {
      $commentaire = "La venue $venue->_id que vous souhaitez annuler est impossible.";
      return $echg_hprim->setAckError($dom_acq, "E108", $commentaire, $venue);
    }
    
    // Impossible d'annuler un dossier ayant une intervention
    $where = array();
    $where['annulee'] = " = '0'";
    $venue->loadRefsOperations($where);
    if (count($venue->_ref_operations) > 0) {
      $commentaire = "La venue $venue->_id que vous souhaitez annuler est impossible.";
      return $echg_hprim->setAckError($dom_acq, "E109", $commentaire, $venue);
    }

    return null;
  }

  /**
   * Passage en trash du NDA
   *
   * @param CSejour        $venue  Venue
   * @param CInteropSender $sender Exp�diteur
   *
   * @return bool
   */
  function trashNDA(CSejour $venue, CInteropSender $sender) {
    if (isset($sender->_configs["type_sej_pa"])) {
      if ($venue->_NDA && preg_match($sender->_configs["type_sej_pa"], $venue->_NDA)) {
        // Passage en pa_ de l'id externe

        $num_pa = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venue->_NDA);
        if ($num_pa->_id) {
          $num_pa->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$sender->_tag_sejour;
          $num_pa->last_update = CMbDT::dateTime();
          $num_pa->store();
        }
        return false;
      }
    }
    if ($venue->_NDA) {
      return true;
    }

    return false;
  }
}