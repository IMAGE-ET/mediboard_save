<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Segment extends CHL7v2Entity {
  var $name        = null;
  var $description = null;
  var $fields      = array();
  
  /**
   * @var CHL7v2SegmentGroup
   */
  var $parent = null;
    
  function __construct(CHL7v2SegmentGroup $parent) {
    parent::__construct($parent);
    
    $this->parent = $parent;
  }
  
  function _toXML(DOMNode $node, $hl7_datatypes, $encoding) {
    $doc = $node->ownerDocument;
    $new_node = $doc->createElement($this->name);
    
    foreach($this->fields as $_field) {
      $_field->_toXML($new_node, $hl7_datatypes, $encoding);
    }
    
    $node->appendChild($new_node);
  }
  
  function parse($data) {
    parent::parse($data);
    
    $message = $this->getMessage();

    $fields = CHL7v2::split($message->fieldSeparator, $this->data);
    $this->name = array_shift($fields);
    
    $specs = $this->getSpecs();
    
    $this->description = (string)$specs->description;
    
    if ($this->name === "MSH") {
      array_unshift($fields, $message->fieldSeparator);
    }
    
    $_segment_specs = $specs->getItems();
    
    // Check the number of fields
    if (count($fields) > count($_segment_specs)) {
      $this->error(CHL7v2Exception::TOO_MANY_FIELDS, $this->data, $this, CHL7v2Error::E_WARNING);
    }
   
    foreach($_segment_specs as $i => $_spec){
      $field = new CHL7v2Field($this, $_spec);
      
      if (array_key_exists($i, $fields)) {
        $field->parse($fields[$i]);
        
        $this->fields[] = $field;
      }
      elseif($_spec->isRequired()) {
        $this->error(CHL7v2Exception::FIELD_EMPTY, null, $field);
      }
    }
  }
  
  function fill($fields) {
    if (!$this->name) return;
    
    $specs = $this->getSpecs();
    $message = $this->getMessage();

    $_segment_specs = $specs->getItems();
    foreach($_segment_specs as $i => $_spec){
      $field = new CHL7v2Field($this, $_spec);
      
      if (array_key_exists($i, $fields)) {
        $_data = $fields[$i];
        
        if ($_data === null || $_data === "") {
          if ($_spec->isRequired()) {
            $this->error(CHL7v2Exception::FIELD_EMPTY, null, $field);
          }
        }
        else {
          if ($_spec->isForbidden()) {
            $this->error(CHL7v2Exception::FIELD_FORBIDDEN, $_data, $field);
          }
        }
        
        if ($_data instanceof CMbObject) {
          throw new CHL7v2Exception($_data->_class, CHL7v2Exception::UNEXPECTED_DATA_TYPE);
        }
        
        $field->fill($_data);
        
        $this->fields[] = $field;
      }
      elseif($_spec->isRequired()) {
        $this->error(CHL7v2Exception::FIELD_EMPTY, null, $field);
      }
    }
  }
  
  function validate() {
    foreach($this->fields as $field) {
      $field->validate();
    }
  }
  
  function getMessage() {
    return $this->parent->getMessage();
  }
  
  /**
   * @return CHL7v2Segment
   */
  function getSegment(){
    return $this;
  }
  
  function getVersion() {
    return $this->getMessage()->getVersion();
  }

  /**
   * @return CHL7v2SimpleXMLElement
   */
  function getSpecs(){
    return $this->getSchema(self::PREFIX_SEGMENT_NAME, $this->name, $this->getMessage()->extension);
  }
  
  function getPath($separator = ".", $with_name = false){
    if (!$with_name) {
      return array();
    }
    
    return array($this->name);
  }
  
  /**
   * @param string             $name
   * @param CHL7v2SegmentGroup $parent
   * @return CHL7v2Segment
   */
  static function create($name, CHL7v2SegmentGroup $parent) {
    $class = "CHL7v2Segment$name";
    
    if (class_exists($class)) {
      $segment = new $class($parent);
    }
    else {
      $segment = new self($parent);
    }
    
    $segment->name = substr($name, 0, 3);
    
    return $segment;
  }
  
  function __toString(){
    $sep = $this->getMessage()->fieldSeparator;
    $name = $this->name;
    
    if (CHL7v2Message::$decorateToString) {
      $sep = "<span class='fs'>$sep</span>";
      $name = "<strong>$name</strong>";
    }
    
    $fields = $this->fields;
    
    if ($this->name === "MSH") {
      array_shift($fields);
    }
    
    $str = $name.$sep.implode($sep, $fields);
    
    if (CHL7v2Message::$decorateToString) {
      $str = "<div class='entity segment' id='entity-er7-$this->id' data-title='$this->description'>$str</div>";
    }
    
    return $str;
  }
  
  function build(CHL7Event $event, $name = null) {
    if (!$event->msg_codes) {
      throw new CHL7v2Exception(CHL7v2Exception::MSG_CODE_MISSING);
    }
    
    // This segment has the following fields
    if ($name) {
      $this->name = $name;
    }
    
    $this->getMessage()->appendChild($this);
  }
  
  function getAssigningAuthority($name = "mediboard", $value= null) {
    switch ($name) {
      case "mediboard" :
        return array(
          "Mediboard",
          CAppUI::conf("hl7 assigningAuthorityUniversalID"),
          "OX"
        );
        break;
      case "INS-C" :
        return array(
          null,
          "1.2.250.1.213.1.4.2",
          "ISO"
        );
        break;
      case "ADELI" :
        return array(
          null,
          "1.2.250.1.71.4.2.1",
          "ISO"
        );
        break;
      case "RPPS" :
        return array(
          null,
          "1.2.250.1.71.4.2.1",
          "ISO"
        );
        break; 
      case "FINESS" :
        return array(
          $value,
          null,
          null
        );
        break; 
      default :
        throw new CHL7v2Exception(CHL7v2Exception::UNKNOWN_AUTHORITY);
        break;
    }
  }
  
  function getGroupAssigningAuthority(CGroups $group) {
    return $this->getAssigningAuthority("FINESS", $group->finess);
  }
  
  function getPersonIdentifiers(CPatient $patient, CGroups $group) {
    // Table - 0203
    // RI - Resource identifier
    // PI - Patient internal identifier
    // INS-C - Identifiant national de sant� calcul�
    $identifiers = array();
    $patient->loadIPP($group->_id);
    if (CHL7v2Message::$build_mode == "simple") {
      $identifiers[] = array(
        (!$patient->_IPP) ? 0 : $patient->_IPP
      );
      
      return $identifiers;
    }
    
    if ($patient->_IPP) {
      $identifiers[] = array(
        $patient->_IPP,
        null,
        null,
        // PID-3-4 Autorit� d'affectation
        $this->getAssigningAuthority("FINESS", $group->finess),
        "PI"
      );
    }

    $identifiers[] = array(
      $patient->_id,
      null,
      null,
      // PID-3-4 Autorit� d'affectation
      $this->getAssigningAuthority("mediboard"),
      "RI"
    );
    if ($patient->INSC) {
      $identifiers[] = array(
        $patient->INSC,
        null,
        null,
        // PID-3-4 Autorit� d'affectation
        $this->getAssigningAuthority("INS-C"),
        "INS-C",
        null,
        mbDate($patient->INSC_date)
      );
    }
    
    return $identifiers;
  }
  
  function getXCN(CMbObject $object) {
    $xcn1 = $xcn2 = $xcn3 = $xcn9 = $xcn13 = null;
    
    if ($object instanceof CMedecin) {
      $xcn1  = CValue::first($object->rpps, $object->adeli, $object->_id);
      $xcn2  = $object->nom;
      $xcn3  = $object->prenom;
      $xcn9  = $this->getAssigningAuthority($object->rpps ? "RPPS" : ($object->adeli ? "ADELI" : "mediboard"));
      $xcn13 = $object->rpps ? "RPPS" : ($object->adeli ? "ADELI" : "RI");
    }
    if ($object instanceof CUser) {
      $xcn1  = $object->_id;
      $xcn2  = $object->user_last_name;
      $xcn3  = $object->user_first_name;
      $xcn9  = $this->getAssigningAuthority("mediboard");
      $xcn13 = "RI";
    }
    if ($object instanceof CMediusers) {
      $xcn1  = CValue::first($object->rpps, $object->adeli, $object->_id);
      $xcn2  = $object->_user_last_name;
      $xcn3  = $object->_user_first_name;
      $xcn9  = $this->getAssigningAuthority($object->rpps ? "RPPS" : ($object->adeli ? "ADELI" : "mediboard"));
      $xcn13 = $object->rpps ? "RPPS" : ($object->adeli ? "ADELI" : "RI");
    }
    
    return array(
      array (
        // XCN-1
        $xcn1,
        // XCN-2
        $xcn2,
        // XCN-3
        $xcn3,
        // XCN-4
        null,
        // XCN-5
        null,
        // XCN-6
        null,
        // XCN-7
        null,
        // XCN-8
        null,
        // XCN-9
        // Autorit� d'affectation
        $xcn9,
        // XCN-10
        // Table - 0200
        // L - Legal Name - Nom de famille
        "L",
        // XCN-11
        null,
        // XCN-12
        null,
        // XCN-13
        // Table - 0203
        // ADELI - Num�ro au r�pertoire ADELI du professionnel de sant�
        // RPPS  - N� d'inscription au RPPS du professionnel de sant� 
        // RI    - Ressource interne
        $xcn13,
        // XCN-14
        null,
        // XCN-15
        null,
        // XCN-16
        null,
        // XCN-17
        null,
        // XCN-18
        null,
        // XCN-19
        null,
        // XCN-20
        null,
        // XCN-21
        null,
        // XCN-22
        null,
        // XCN-23
        null,
      )
    );
  }
  
  function getXPN(CMbObject $object) {
    $names = array();
    
    if ($object instanceof CPatient) {
      $prenoms = array($object->prenom_2, $object->prenom_3, $object->prenom_4);
      CMbArray::removeValue("", $prenoms);
 
      // Nom usuel
      $patient_usualname = array(
        $object->nom,
        $object->prenom,
        implode(",", $prenoms),
        null,
        $object->civilite,
        null,
        // Table 0200
        // A - Alias Name
        // B - Name at Birth
        // C - Adopted Name
        // D - Display Name
        // I - Licensing Name
        // L - Legal Name
        // M - Maiden Name
        // N - Nickname /_Call me_ Name/Street Name
        // P - Name of Partner/Spouse (retained for backward compatibility only)
        // R - Registered Name (animals only)
        // S - Coded Pseudo-Name to ensure anonymity
        // T - Indigenous/Tribal/Community Name
        // U - Unspecified
        (is_numeric($object->nom)) ? "S" : "L",
        // Table 465
        // A - Alphabetic (i.e., Default or some single-byte)
        // I - Ideographic (i.e., Kanji)  
        // P - Phonetic (i.e., ASCII, Katakana, Hiragana, etc.) 
        "A"
      );
      // Cas nom de jeune fille
      if ($object->nom_jeune_fille) {
        $patient_birthname = $patient_usualname;
        $patient_birthname[0] = $object->nom_jeune_fille;
        // Legal Name devient Display Name
        $patient_usualname[6] = "D"; 
      }
      $names[] = $patient_usualname;
      if ($object->nom_jeune_fille) {
        $names[] = $patient_birthname;
      } 
    }
    if ($object instanceof CCorrespondantPatient) {
      $names[] = array(
        $object->nom,
        $object->prenom,
        null,
        null,
        null,
        null,
        (is_numeric($object->nom)) ? "S" : "L",
        "A"
      );
    }

    return $names;
  }

  function getPL2 (CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    // Chambre
    switch ($receiver->_configs["build_PV1_3_2"]) {
      // Valeur en config
      case 'config_value':
        return CAppUI::conf("hl7 CHL7v2Segment PV1_3_2");
      // Nom de la chambre
      default:
        return ($affectation->_id && $affectation->_ref_lit) ? $affectation->_ref_lit->_ref_chambre->nom : null;
    }
  }
  
  function getPL3 (CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    // Lit
    switch ($receiver->_configs["build_PV1_3_3"]) {
      // Valeur en config
      case 'config_value':
        return CAppUI::conf("hl7 CHL7v2Segment PV1_3_3");
      // Nom du lit
      default:
        return ($affectation->_id && $affectation->_ref_lit) ? $affectation->_ref_lit->nom : null;
    }
  }
  
  function getPL5 (CInteropReceiver $receiver) {
    // Statut du lit
    switch ($receiver->_configs["build_PV1_3_5"]) {
      // Ne rien envoyer
      case 'null':
        return null;
      // Occup� - Libre
      default:
        // O - Occup�
        // U - Libre
        return "O";
    }
  }
  
  function getPV110 (CInteropReceiver $receiver, CSejour $sejour) {
    // Hospital Service
    switch ($receiver->_configs["build_PV1_10"]) {
      // idex du service
      case 'service':
        return CIdSante400::getMatch("CService", $receiver->_tag_service, null, $sejour->service_id)->id400;
      // Discipline m�dico-tarifaire
      default:
        return $sejour->discipline_id;
    }
  }
  
  function getPL(CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    $group       = $sejour->loadRefEtablissement();
    if (!$affectation) {
      $affectation = $sejour->getCurrAffectation();
    }
    $affectation->loadRefLit()->loadRefChambre();
    $current_uf  = $sejour->getUF();
      
    return array(
      array(
        // PL-1 - Code UF h�bergement
        $current_uf["hebergement"]->code,
        // PL-2 - Chambre
        $this->getPL2($receiver, $sejour, $affectation),
        // PL-3 - Lit
        $this->getPL3($receiver, $sejour, $affectation),
        // PL-4 - Etablissement hospitalier
        $this->getGroupAssigningAuthority($sejour->loadRefEtablissement()),
        // PL-5 - Statut du lit
        // Table - 0116
        // O - Occup�
        // U - Libre
        $this->getPL5($receiver),
        // PL-6 - Person location type
        null,
        // PL-7 - Building
        CHL7v2TableEntry::mapTo("307", $group->_id),
      )
    );
  }
  
  function getPreviousPL(CInteropReceiver $receiver, CSejour $sejour) {
    $sejour->loadSurrAffectations();
    if ($prev_affectation = $sejour->_ref_prev_affectation) {
      return $this->getPL($receiver, $sejour, $prev_affectation);
    }
  }
  
  function getModeTraitement(CSejour $sejour) {
    $code  = $sejour->type;
    $code .= $sejour->type_pec ? "_$sejour->type_pec" : null;

    return CHL7v2TableEntry::mapTo("32", CMbString::lower($code));
  }
}

?>