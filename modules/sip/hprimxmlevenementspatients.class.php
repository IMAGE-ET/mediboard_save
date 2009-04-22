<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("dPinterop", "mbxmldocument");
CAppUI::requireModuleClass("dPinterop", "hprimxmldocument");

if (!class_exists("CHPrimXMLDocument")) {
  return;
}

class CHPrimXMLEvenementsPatients extends CHPrimXMLDocument { 
  function __construct() {            
    parent::__construct("evenementPatient", "msgEvenementsPatients105", "sip");
  }
  
  function generateEnteteMessageEvenementsPatients() {
    global $AppUI, $g, $m;

    $evenementsPatients = $this->addElement($this, "evenementsPatients", null, "http://www.hprim.org/hprimXML");
    // Retourne un message d'acquittement par le r�cepteur
    $this->addAttribute($evenementsPatients, "acquittementAttendu", "oui");
    
    $enteteMessage = $this->addElement($evenementsPatients, "enteteMessage");
    $this->addElement($enteteMessage, "identifiantMessage", $this->_identifiant);
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction", $this->_date_production);
    
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Sant�");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "acteur", "user$AppUI->user_id", "$AppUI->user_first_name $AppUI->user_last_name");
    $this->addAgent($agents, "syst�me", $this->_emetteur, $group->text);
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->destinataire = "MediBoard";
    $this->addAgent($agents, "application", $this->_destinataire, "Gestion des Etablissements de Sant�");
  }
  
  function getTypeEvenementPatient() {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient/*";
    
    $evenementPatient = $xpath->queryUniqueNode($query);
    
   return $evenementPatient->tagName;
  }
  
  function getIdSource($node) {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $emetteur = $xpath->queryUniqueNode("hprim:emetteur", $identifiant);
    $referentEmetteur = $xpath->queryAttributNode("hprim:emetteur", $node, "referent");
    return $xpath->queryTextNode("hprim:valeur", $emetteur);
  }
  
  function getIdCible($node) {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $recepteur = $xpath->queryUniqueNode("hprim:recepteur", $identifiant);
    $referentRecepteur = $xpath->queryAttributNode("hprim:recepteur", $node, "referent");
    return $xpath->queryTextNode("hprim:valeur", $recepteur);
  }
  
  function createPatient($node, $mbPatient) {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    $mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $mbPatient;
  }
  
  function getPersonnePhysique($node, $mbPatient) {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    // Cr�ation de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $node);
    $sexe = $xpath->queryAttributNode("hprim:personnePhysique", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );
    $mbPatient->sexe = $sexeConversion[$sexe];
    $mbPatient->nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $mbPatient->_nom_naissance = $xpath->queryTextNode("hprim:nomNaissance", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $mbPatient->prenom = $prenoms[0];
    $mbPatient->prenom_2 = isset($prenoms[1]) ? $prenoms[1] : "";
    $mbPatient->prenom_3 = isset($prenoms[2]) ? $prenoms[2] : "";
    
    $adresses = $xpath->queryUniqueNode("hprim:adresses", $personnePhysique);
    $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
    $mbPatient->adresse = $xpath->queryTextNode("hprim:ligne", $adresse);
    $mbPatient->ville = $xpath->queryTextNode("hprim:ville", $adresse);
    $mbPatient->pays_insee = $xpath->queryTextNode("hprim:pays", $adresse);
    $pays = new CPaysInsee();
    $pays->numerique = $mbPatient->pays_insee;
    $pays->loadMatchingObject();
    $mbPatient->pays = $pays->nom_fr;
    $mbPatient->cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
    
    $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $personnePhysique);
    $mbPatient->tel = isset($telephones[0]) ? $telephones[0] : "";
    $mbPatient->tel2 = isset($telephones[1]) ? $telephones[1] : "";
    
    $emails = $xpath->getMultipleTextNodes("hprim:emails/*", $personnePhysique);
    $mbPatient->email = isset($emails[0]) ? $emails[0] : "";
    
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $xpath->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance = $xpath->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $xpath->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance = $xpath->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }
  
  function getActiviteSocioProfessionnelle($node, $mbPatient) {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $mbPatient->profession = $xpath->queryTextNode("hprim:activiteSocioProfessionnelle", $node); 
    
    return $mbPatient;
  }
  
  function getPersonnesPrevenir($node, $mbPatient) {
  	$xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
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
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
        
    // Cr�ation de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom = $prenoms[0];
    
    return $mbPatient->checkSimilar($nom, $prenom);
  }
  
  function generateFromOperation($mbPatient, $referent) {}
  
  function generateEvenementsPatients($mbObject, $referent = null, $initiateur = null) {}
  
  function getEvenementPatientXML() { }
  
  function getIPPPatient() { }
  
  function getActionEvenement($node) { }
}
?>