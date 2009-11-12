<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsPatients extends CHPrimXMLDocument {
  static function getHPrimXMLEvenementsPatients($messagePatient) {
    $hprimxmldoc = new CHPrimXMLDocument("patient", "msgEvenementsPatients105");
    // R�cup�ration des informations du message XML
    $hprimxmldoc->loadXML(utf8_decode($messagePatient));
    
    $type = $hprimxmldoc->getTypeEvenementPatient();
    // Un �v�nement concernant un patient appartient � l'une des six cat�gories suivantes
    switch ($type) {
      case "enregistrementPatient" :
        return new CHPrimXMLEnregistrementPatient();
        break;
      case "fusionPatient" :
        return new CHPrimXMLFusionPatient();
        break;
      case "venuePatient" :
        return new CHPrimXMLVenuePatient();
        break;
      case "fusionVenue" :
        return new CHPrimXMLFusionVenue();
        break;
      default;
        return new CHPrimXMLEvenementsPatients();
    }
  }
  
  function __construct() {
    $this->evenement = "evt_patients";
    $this->destinataire_libelle = "";
    $this->type = "patients";
                
    parent::__construct("patients", "msgEvenementsPatients105");
  }

  function generateEnteteMessageEvenementsPatients() {
    $evenementsPatients = $this->addElement($this, "evenementsPatients", null, "http://www.hprim.org/hprimXML");
    // Retourne un message d'acquittement par le r�cepteur
    $this->addAttribute($evenementsPatients, "acquittementAttendu", "oui");
    
    $this->addEnteteMessage($evenementsPatients);
  }
  
  function getIdSource($node) {
    $xpath = new CMbXPath($this, true);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $emetteur = $xpath->queryUniqueNode("hprim:emetteur", $identifiant);
    $referentEmetteur = $xpath->queryAttributNode("hprim:emetteur", $node, "referent");
    return $xpath->queryTextNode("hprim:valeur", $emetteur);
  }
  
  function getIdCible($node) {
    $xpath = new CMbXPath($this, true);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $recepteur = $xpath->queryUniqueNode("hprim:recepteur", $identifiant);
    $referentRecepteur = $xpath->queryAttributNode("hprim:recepteur", $node, "referent");
    return $xpath->queryTextNode("hprim:valeur", $recepteur);
  }
  
  function mappingPatient($node, CPatient $mbPatient) {    
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $mbPatient;
  }
  
  static function getPersonnePhysique($node, CPatient $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);

    // Cr�ation de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $node);
    
    $sexe = $xpath->queryAttributNode("hprim:personnePhysique", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );
    $mbPatient->sexe = $sexeConversion[$sexe];
    
    // R�cup�ration du typePersonne
    $mbPatient = self::getPersonne($personnePhysique,$mbPatient);
    
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $xpath->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance = $xpath->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $xpath->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance = $xpath->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }
  
  static function getPersonne($node, CMbObject $mbPersonne) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $civilite = $xpath->queryAttributNode("hprim:civiliteHprim", $node, "valeur");
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "mlle"  => "melle",
      "mr"    => "m",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $node);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $node);
    $adresses = $xpath->queryUniqueNode("hprim:adresses", $node);
    $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
    $ligne = $xpath->getMultipleTextNodes("hprim:ligne", $adresse);
    $ville = $xpath->queryTextNode("hprim:ville", $adresse);
    $cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
    $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $node);
    $emails = $xpath->getMultipleTextNodes("hprim:emails/*", $node);    
    
    if ($mbPersonne instanceof CPatient) {
      if ($civilite)
        $mbPersonne->civilite = $civiliteHprimConversion[$civilite];    
      $mbPersonne->nom = $nom;
      $mbPersonne->_nom_naissance = $xpath->queryTextNode("hprim:nomNaissance", $node);
      $mbPersonne->prenom = $prenoms[0];
      $mbPersonne->prenom_2 = isset($prenoms[1]) ? $prenoms[1] : "";
      $mbPersonne->prenom_3 = isset($prenoms[2]) ? $prenoms[2] : "";
      $mbPersonne->adresse  = $ligne[0];
      if (isset($ligne[1]))
        $mbPersonne->adresse .= " $ligne[1]";
      if (isset($ligne[2]))
        $mbPersonne->adresse .= " $ligne[2]";
      $mbPersonne->ville = $ville;
      $mbPersonne->pays_insee = $xpath->queryTextNode("hprim:pays", $adresse);
      $pays = new CPaysInsee();
      $pays->numerique = $mbPersonne->pays_insee;
      $pays->loadMatchingObject();
      $mbPersonne->pays = $pays->nom_fr;
      $mbPersonne->cp = $cp;
      $mbPersonne->tel = isset($telephones[0]) ? $telephones[0] : "";
      $mbPersonne->tel2 = isset($telephones[1]) ? $telephones[1] : "";
      $mbPersonne->email = isset($emails[0]) ? $emails[0] : "";
    } elseif ($mbPersonne instanceof CMediusers) {
      $mbPersonne->_user_last_name  = $nom;
      $mbPersonne->_user_first_name = $prenoms[0];
      $mbPersonne->_user_email      = isset($emails[0]) ? $emails[0] : "";
      $mbPersonne->_user_phone      = isset($telephones[0]) ? $telephones[0] : "";
      $mbPersonne->_user_adresse    = $ligne[0];
      if (isset($ligne[1]))
        $mbPersonne->_user_adresse  .= " $ligne[1]";
      if (isset($ligne[2]))
        $mbPersonne->_user_adresse  .= " $ligne[2]";
      $mbPersonne->_user_cp         = $cp;
      $mbPersonne->_user_ville      = $ville;
    }
    
    return $mbPersonne;
  }
  
  static function getActiviteSocioProfessionnelle($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $mbPatient->profession = $xpath->queryTextNode("hprim:activiteSocioProfessionnelle", $node); 
    
    return $mbPatient;
  }
  
  static function getPersonnesPrevenir($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $personnesPrevenir = $xpath->query("hprim:personnesPrevenir/*", $node);
    foreach ($personnesPrevenir as $personnePrevenir) {
      $mbPatient->prevenir_nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePrevenir);
      $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePrevenir);
      $mbPatient->prevenir_prenom = $prenoms[0];
      
      $adresses = $xpath->queryUniqueNode("hprim:adresses", $personnePrevenir);
      $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
      $mbPatient->prevenir_adresse = $xpath->queryTextNode("hprim:ligne", $adresse);
      $mbPatient->prevenir_ville = $xpath->queryTextNode("hprim:ville", $adresse);
      $mbPatient->prevenir_cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
      
      $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $personnePrevenir);
      $mbPatient->prevenir_tel = isset($telephones[0]) ? $telephones[0] : "";
    }
        
    return $mbPatient;
  }
  
  function checkSimilarPatient($mbPatient, $xmlPatient) {
    $xpath = new CMbXPath($this, true);
        
    // Cr�ation de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom = $prenoms[0];
    
    return $mbPatient->checkSimilar($nom, $prenom);
  }
  
  function getIdSourceObject($query_evt, $query_type) { 
    $xpath = new CMbXPath($this, true);
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $typeEvenement = $xpath->queryUniqueNode($query_evt, $evenementPatient);
    
    $object = $xpath->queryUniqueNode($query_type, $typeEvenement);

    return $this->getIdSource($object);
  }
  
  function getEvenementPatientXML() { 
    $xpath = new CMbXPath($this, true);
    
    $data = array();
    $data['acquittement'] = $xpath->queryAttributNode("/hprim:evenementsPatients", null, "acquittementAttendu");

    $query = "/hprim:evenementsPatients/hprim:enteteMessage";
    $entete = $xpath->queryUniqueNode($query);

    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='syst�me']", $agents);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    return $data;
  }
  
  static function getActionEvenement($query, $node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    return $xpath->queryAttributNode($query, $node, "action");    
  }
  
  function mappingVenue($node, $mbVenue) {  
    $mbVenue = $this->getNatureVenue($node, $mbVenue);
    $mbVenue = $this->getEntree($node, $mbVenue);
    $mbVenue = $this->getMedecins($node, $mbVenue);
    $mbVenue = $this->getPlacement($node, $mbVenue);
    $mbVenue = $this->getSortie($node, $mbVenue);

    return $mbVenue;
  }
  
  static function getAttributesVenue($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
        
    $attributes = array();
    $attributes['confidentiel'] = $xpath->getValueAttributNode($node, "confidentiel"); 
    $attributes['etat'] = $xpath->getValueAttributNode($node, "etat"); 
    $attributes['facturable'] = $xpath->getValueAttributNode($node, "facturable"); 
    $attributes['declarationMedecinTraitant'] = $xpath->getValueAttributNode($node, "declarationMedecinTraitant"); 
    
    return $attributes;
  }
  
  static function getEtatVenue($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    return $xpath->getValueAttributNode($node, "etat"); 
  }
  
  static function getNatureVenue($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $nature = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur");
    $attrNatureVenueHprim = array (
      "hsp"  => "comp",
      "cslt" => "consult",
      "sc" => "seances",
    );
    $mbVenue->type =  $attrNatureVenueHprim[$nature];
    
    return $mbVenue;
  }
  
  static function getEntree($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $entree = $xpath->queryUniqueNode("hprim:entree", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $entree);
    $heure = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $entree);
    $modeEntree = $xpath->queryAttributNode("hprim:modeEntree", $entree, "valeur");
    
    $etat = self::getEtatVenue($node);
    if ($etat == "pr�admission") {
      $mbVenue->entree_prevue = "$date $heure";
    } else if (($etat == "encours") || ($etat == "cl�tur�e")) {
      $mbVenue->entree_reelle = "$date $heure";
    }
       
    return $mbVenue;
  }
  
  function getMedecins($node, $mbVenue) {
    global $g;
    
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $medecins = $xpath->queryUniqueNode("hprim:medecins", $node);
    $medecin = $medecins->childNodes;
    foreach ($medecin as $_med) {
   	  $code = $xpath->queryTextNode("hprim:identification/hprim:code", $_med);
   	  $mediuser = new CMediusers();
   	  $id400 = new CIdSante400();
   	  //Param�trage de l'id 400
      $id400->object_class = "CMediusers";
      $id400->tag = $this->destinataire;
   	  $id400->id400 = $code;
   	  $id400->loadMatchingObject();
   	  if ($id400->_id) {
   	  	$mediuser->_id = $id400->object_id;
   	  } else {
   	  	$functions = new CFunctions();
	      $functions->text = CAppUI::conf("hprimxml functionPratImport");
	      $functions->loadMatchingObject();
        if (!$functions->loadMatchingObject()) {
          $functions->group_id = CGroups::loadCurrent()->_id;
          $functions->type = "cabinet";
          $functions->compta_partagee = 0;
          $functions->store();
        }
	      $mediuser->function_id = $functions->_id;
        // R�cup�ration du typePersonne
        $personne =  $xpath->queryUniqueNode("hprim:personne", $_med);
        $mediuser = self::getPersonne($personne, $mediuser);
        $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name);
        $mediuser->_user_type = 13; // Medecin
        $mediuser->actif = 0; // Non actif	
        if (!$mediuser->loadMatchingObject()) {
          $mediuser->store();
        }
        $id400->object_id = $mediuser->_id;
        $id400->last_update = mbDateTime();
        $id400->store(); 
   	  }
      $lien = $xpath->getValueAttributNode($_med, "lien");
      if ($lien == "rsp") {
        $mbVenue->praticien_id = $mediuser->_id;
      }
    } 
    
    return $mbVenue;
  }

  static function getPlacement($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $placement = $xpath->queryUniqueNode("hprim:Placement", $node);
    
    $mbVenue->modalite = $xpath->queryAttributNode("hprim:modePlacement", $placement, "modaliteHospitalisation");
    
    return $mbVenue;
  }
  
  static function getSortie($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $sortie = $xpath->queryUniqueNode("hprim:sortie", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $sortie);
    $heure = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $sortie);
    
    $etat = self::getEtatVenue($node);
    if (($etat == "pr�admission") || ($etat == "encours")) {
      $mbVenue->sortie_prevue = "$date $heure";
    } else if ($etat == "cl�tur�e") {
      $mbVenue->sortie_reelle = "$date $heure";
    }
    
    return $mbVenue;
  }
}
?>