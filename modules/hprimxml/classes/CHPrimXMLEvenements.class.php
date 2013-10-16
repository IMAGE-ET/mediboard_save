<?php

/**
 * �v�nements H'XML
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLEvenements
 */
class CHPrimXMLEvenements extends CHPrimXMLDocument {  
  static $documentElements = array(
    'evenementsPatients'           => "CHPrimXMLEvenementsPatients",
    'evenementsServeurActes'       => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementsPMSI'               => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementsFraisDivers'        => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementServeurIntervention' => "CHPrimXMLEvenementsServeurActivitePmsi",
  );

  /**
   * R�cup�ration de l'�v�nement H'XML
   *
   * @return CHPrimXMLEvenements
   */
  static function getHPrimXMLEvenements() {
  }

  /**
   * R�cup�ration des �v�nements disponibles
   *
   * @return array
   */
  function getDocumentElements() {
    return self::$documentElements;
  }

  /**
   * Construction de l'ent�te du message
   *
   * @param string $type    Type de l'�v�nement
   * @param bool   $version Version
   *
   * @return void
   */
  function generateEnteteMessage($type, $version = true) {
    $evenements = $this->addElement($this, $type, null, "http://www.hprim.org/hprimXML");
    if ($version) {
      $this->addAttribute($evenements, "version", CAppUI::conf("hprimxml $this->evenement version"));
    }
    
    $this->addEnteteMessage($evenements);
  }

  /**
   * R�cup�ration des �l�ments de l'ent�te du message
   *
   * @param string $type Type de l'�v�nement
   *
   * @return array
   */
  function getEnteteEvenementXML($type) {
    $data = array();
    $xpath = new CHPrimXPath($this);   

    $entete = $xpath->queryUniqueNode("/hprim:$type/hprim:enteteMessage");
    
    $data['dateHeureProduction'] = CMbDT::dateTime($xpath->queryTextNode("hprim:dateHeureProduction", $entete));
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='".$this->getAttSysteme()."']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);    
    
    return $data;
  }

  /**
   * R�cup�ration de l'action de l'�v�nement
   *
   * @param string  $query Query
   * @param DOMNode $node  Node
   *
   * @return string
   */
  function getActionEvenement($query, DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryAttributNode($query, $node, "action");    
  }

  /**
   * Est-ce que l'action est possible par rapport � l'�v�nement ?
   *
   * @param string                 $action  Action
   * @param CHPrimXMLAcquittements $dom_acq Acquittement
   *
   * @return null|string
   */
  function isActionValide($action, CHPrimXMLAcquittements $dom_acq) {
    $acq = null;
    $echange_hprim = $this->_ref_echange_hprim;

    if (!$action || array_key_exists($action, $this->actions)) {
      return $acq;
    }
    
    $acq       = $dom_acq->generateAcquittements("erreur", "E008");
    $doc_valid = $dom_acq->schemaValidate(null, false, $this->_ref_receiver->display_errors);
    
    $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
    $echange_hprim->_acquittement       = $acq;
    $echange_hprim->statut_acquittement = "erreur";
    $echange_hprim->store();
    
    return $acq;
  }

  /**
   * R�cup�ration de la date
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  function getDate(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryTextNode("hprim:date", $node);
  }

  /**
   * R�cup�ration de l'heure
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  function getHeure(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryTextNode("hprim:heure", $node);
  }

  /**
   * R�cup�ration de la date et heure
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  function getDateHeure(DOMNode $node) {
    if (!$node) {
      return null;
    }

    $date  = $this->getDate($node);
    $heure = $this->getHeure($node);

    if (!$date || !$heure) {
      return null;
    }

    return "$date $heure";
  }
}
