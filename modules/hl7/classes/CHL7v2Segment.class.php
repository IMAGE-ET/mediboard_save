<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CHL7v2Segment extends CHL7v2Entity {
  public $name;
  public $description;
  public $fields      = array();
  

  /** @var CHL7v2SegmentGroup */
  public $parent;
    
  function __construct(CHL7v2SegmentGroup $parent) {
    parent::__construct($parent);
    
    $this->parent = $parent;
  }
  
  function _toXML(DOMNode $node, $hl7_datatypes, $encoding) {
    $doc = $node->ownerDocument;
    $new_node = $doc->createElement($this->name);
    
    foreach ($this->fields as $_field) {
      $_field->_toXML($new_node, $hl7_datatypes, $encoding);
    }
    
    $node->appendChild($new_node);
  }
  
  function parse($data) {
    //parent::parse($data);
    
    $message = $this->getMessage();

    $fields = CHL7v2::split($message->fieldSeparator, $data);
    $this->name = array_shift($fields);
    
    $specs = $this->getSpecs();
    
    $this->description = $specs->queryTextNode("description");
    
    if ($this->name === $message->getHeaderSegmentName()) {
      array_unshift($fields, $message->fieldSeparator);
    }

    // Don't count empty fields on the right
    $count_fields = count($fields);
    for ($i = $count_fields-1; $i >= 0; $i--) {
      if ($fields[$i] === "") {
        $count_fields--;
      }
      else {
        break;
      }
    }
    
    $_segment_specs = $specs->getItems();
    
    // Check the number of fields
    if ($count_fields > count($_segment_specs)) {
      $this->error(CHL7v2Exception::TOO_MANY_FIELDS, $data, $this, CHL7v2Error::E_WARNING);
    }
   
    foreach ($_segment_specs as $i => $_spec) {
      if (array_key_exists($i, $fields)) {
        $field = new CHL7v2Field($this, $_spec);
        $field->parse($fields[$i]);
        
        $this->fields[] = $field;
      }
      elseif ($_spec->isRequired()) {
        $field = new CHL7v2Field($this, $_spec);
        $this->error(CHL7v2Exception::FIELD_EMPTY, $field->getPathString(), $field);
      }
    }
  }
  
  function fill($fields) {
    if (!$this->name) {
      return;
    }

    $specs = $this->getSpecs();
    
    $_segment_specs = $specs->getItems();
    foreach ($_segment_specs as $i => $_spec) {
      if (array_key_exists($i, $fields)) {
        $_data = $fields[$i];

        $field = new CHL7v2Field($this, $_spec);
        
        if ($_data === null || $_data === "" || $_data === array()) {
          if ($_spec->isRequired()) {
            $this->error(CHL7v2Exception::FIELD_EMPTY, $field->getPathString(), $field);
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
      elseif ($_spec->isRequired()) {
        $field = new CHL7v2Field($this, $_spec);
        $this->error(CHL7v2Exception::FIELD_EMPTY, $field->getPathString(), $field);
      }
    }
  }
  
  function validate() {
    foreach ($this->fields as $field) {
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
   * @return CHL7v2DOMDocument
   */
  function getSpecs(){
    return $this->getMessage()->getSchema(self::PREFIX_SEGMENT_NAME, $this->name);
  }
  
  function getPath($separator = ".", $with_name = false){
    if (!$with_name) {
      return array();
    }
    
    return array($this->name);
  }
  
  /**
   * Build an HL7v2 segment
   * 
   * @param string             $name   The name of the segment
   * @param CHL7v2SegmentGroup $parent The parent of the segment to create
   * 
   * @return CHL7v2Segment The segment
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
    
    if ($this->name === $this->getMessage()->getHeaderSegmentName()) {
      array_shift($fields);
    }
    
    $str = $name.$sep.implode($sep, $fields);
    
    if (CHL7v2Message::$decorateToString) {
      $str = "<div class='entity segment' id='entity-er7-$this->id' data-title='$this->description'>$str</div>";
    }
    else {
      $str .= $this->getMessage()->segmentTerminator;
    }
  
    return $str;
  }
  
  function build(/*CHL7Event*/ $event, $name = null) {
    if (!$event->msg_codes) {
      throw new CHL7v2Exception(CHL7v2Exception::MSG_CODE_MISSING);
    }
    
    // This segment has the following fields
    if ($name) {
      $this->name = $name;
    }
    
    $this->getMessage()->appendChild($this);
  }
  
  function getAssigningAuthority($name = "mediboard", $value= null, CInteropActor $actor = null, CDomain $domain = null) {
    switch ($name) {
      case "domain" :
        return array(
          $domain->libelle,
          $domain->OID,
          "ISO"
        );

      case "actor" :
        $configs = $actor->_configs;
        return array(
          $configs["assigning_authority_namespace_id"],
          $configs["assigning_authority_universal_id"],
          $configs["assigning_authority_universal_type_id"],
        );
      
      case "mediboard" :
        return array(
          CAppUI::conf("hl7 assigning_authority_namespace_id"),
          CAppUI::conf("hl7 assigning_authority_universal_id"),
          CAppUI::conf("hl7 assigning_authority_universal_type_id"),
        );
      
      case "INS-C" :
        return array(
          "ASIP-SANTE-INS-C",
          "1.2.250.1.213.1.4.2",
          "ISO"
        );
      
      case "ADELI" :
        return array(
          "ASIP-SANTE-PS",
          "1.2.250.1.71.4.2.1",
          "ISO"
        );
      
      case "RPPS" :
        return array(
          "ASIP-SANTE-PS",
          "1.2.250.1.71.4.2.1",
          "ISO"
        );
      
      case "FINESS" :
        return array(
          $value,
          null,
          "M"
        );
        
      default :
        throw new CHL7v2Exception(CHL7v2Exception::UNKNOWN_AUTHORITY);
    }
  }
  
  function getGroupAssigningAuthority(CGroups $group) {
    return $this->getAssigningAuthority("FINESS", $group->finess);
  }
  
  function getPersonIdentifiers(CPatient $patient, CGroups $group, CInteropActor $actor = null) {
    if (!$patient->_IPP) {
      $patient->loadIPP($group->_id);
    }

    $assigning_authority = $this->getAssigningAuthority("FINESS", $group->finess);

    if (CValue::read($actor->_configs, "build_PID_34") === "actor") {
      $assigning_authority = $this->getAssigningAuthority("actor", null, $actor);
    }
    elseif (CValue::read($actor->_configs, "build_PID_34") === "domain") {
      // Master domain
      $group_domain = new CGroupDomain();
      $group_domain->group_id     = $group->_id;
      $group_domain->master       = 1;
      $group_domain->object_class = "CPatient";
      $group_domain->loadMatchingObject();

      $domain = $group_domain->loadRefDomain();

      $assigning_authority = $this->getAssigningAuthority("domain", null, null, $domain);
    }

    // Table - 0203
    // RI - Resource identifier
    // PI - Patient internal identifier
    // INS-C - Identifiant national de sant� calcul�
    $identifiers = array();
    if (CHL7v2Message::$build_mode == "simple") {
      if ($actor->_configs["send_own_identifier"]) {
        $identifiers[] = array(
          $patient->_IPP,
          null,
          null,
          // PID-3-4 Autorit� d'affectation
          (!$actor->_configs["send_assigning_authority"]) ? null : $assigning_authority,
          "PI"
        );
        
        $identifiers[] = array(
          $patient->_id,
          null,
          null,
          // PID-3-4 Autorit� d'affectation
          (!$actor->_configs["send_assigning_authority"]) ? null : $this->getAssigningAuthority("mediboard"),
          "RI"
        );  
      }
      else {
        $identifiers[] = array(
          (!$patient->_IPP) ? 0 : $patient->_IPP
        );
      }  
      
      return $identifiers;
    }  

    if ($patient->_IPP) {
      $identifiers[] = array(
        $patient->_IPP,
        null,
        null,
        // PID-3-4 Autorit� d'affectation
        (empty($actor->_configs["send_assigning_authority"])) ? null : $assigning_authority,
        "PI"
      );
    }

    // Ajout des identifiants des acteurs d'int�gration
    $this->fillActorsIdentifiers($identifiers, $patient, $actor);

    // Ajout d'auutres identifiants
    $this->fillOtherIdentifiers($identifiers, $patient, $actor);
    
    return $identifiers;
  }

  function fillActorsIdentifiers(&$identifiers, CMbObject $object, CInteropActor $actor = null) {
    if (!$actor->_configs["send_actor_identifier"]) {
      return;
    }

    $domain = new CDomain();
    $where = array();
    $where["incrementer_id"] = "IS NULL";
    $where["actor_id"]       = "IS NOT NULL";
    /** @var CDomain[] $domains */
    $domains = $domain->loadList($where);

    foreach ($domains as $_domain) {
      $value = CIdSante400::getValueFor($object, $_domain->tag);
      if (!$value) {
        continue;
      }

      $identifiers[] = array(
        $value,
        null,
        null,
        // PID-3-4 Autorit� d'affectation
        $this->getAssigningAuthority("domain", null, null, $_domain),
        "RI"
      );
    }
  }

  function fillOtherIdentifiers(&$identifiers, CPatient $patient, CInteropActor $actor = null) {
  }
  
  function getXCN9(CMbObject $object, CIdSante400 $idex = null, CInteropReceiver $actor = null) {
    if (empty($actor->_configs["send_assigning_authority"])) {
      return;
    }

    // Autorit� d'affectation de l'ADELI
    if ($object->adeli) {
      return $this->getAssigningAuthority("ADELI");
    } 
    
    // Autorit� d'affectation du RPPS
    elseif ($object->rpps) {
      return $this->getAssigningAuthority("RPPS");
    } 
    
    // Autorit� d'affectation de l'idex
    elseif ($idex && $idex->id400) {
      return $this->getAssigningAuthority("actor", null, $actor);
    }     
    
    // Autorit� d'affectation de Mediboard
    return $this->getAssigningAuthority("mediboard");
  }
  
  function getXCN(CMbObject $object, CInteropReceiver $actor, $repeatable = false) {
    $xcn1 = $xcn2 = $xcn3 = $xcn9 = $xcn13 = null;
    
    if ($object instanceof CMedecin) {
      $object->completeField("adeli", "rpps");

      $idex = $object->loadLastId400();
      
      $xcn1  = CValue::first($object->adeli, $object->rpps, $idex->id400, $object->_id);
      $xcn2  = $object->nom;
      $xcn3  = $object->prenom;      
      $xcn9  = $this->getXCN9($object, $idex, $actor);
      $xcn13 = ($object->adeli ? "ADELI" : ($object->rpps ? "RPPS" : "RI"));
    }
    if ($object instanceof CUser) {
      $xcn1  = $object->_id;
      $xcn2  = $object->user_last_name;
      $xcn3  = $object->user_first_name;
      $xcn9  = $this->getXCN9($object);
      $xcn13 = "RI";
    }
    if ($object instanceof CMediusers) {
      $object->completeField("adeli", "rpps");

      $idex = CIdSante400::getMatch("CMediusers", $actor->_tag_mediuser, null, $object->_id);

      $xcn1  = CValue::first($object->adeli, $object->rpps, $idex->id400, $object->_id);
      $xcn2  = $object->_user_last_name;
      $xcn3  = $object->_user_first_name;
      $xcn9  = $this->getXCN9($object, $idex, $actor);
      $xcn13 = ($object->adeli ? "ADELI" : ($object->rpps ? "RPPS" : "RI"));
    }
    
    if ($repeatable && ($actor->_configs["build_PV1_7"] == "repeatable") && $object instanceof CMediusers) {
      $xcn = array (
        null,
        $xcn2,
        $xcn3,
        null,
        null,
        null,
        null,
        null,
        $xcn9,
        "L",
        null,
        null,
        null
      );
      
      $xncs = array();
      
      // Ajout du RPPS
      if ($object->rpps) {
        $xcn[0]  = $object->rpps;
        $xcn[8]  = $this->getAssigningAuthority("RPPS");
        $xcn[12] = "RPPS";
        
        $xncs[] = $xcn; 
      }
      // Ajout de l'ADELI
      if ($object->adeli) {
        $xcn[0]  = $object->adeli;
        $xcn[8]  = $this->getAssigningAuthority("ADELI");
        $xcn[12] = "ADELI";
        
        $xncs[] = $xcn; 
      }
      // Ajout de l'Idex
      if ($idex->id400) {
        $xcn[0]  = $idex->id400;
        $xcn[8]  = $this->getAssigningAuthority("actor", null, $actor);
        $xcn[12] = "RI";
        
        $xncs[] = $xcn;
      }
      // Ajout de l'ID Mediboard
      $xcn[0]  = $object->_id;
      $xcn[8]  = $this->getAssigningAuthority("mediboard");
      $xcn[12] = "RI";
      
      $xncs[]  = $xcn;

      return $xncs;
    }
    else {
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
        $patient_birthname    = $patient_usualname;
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

  function getXTN(CInteropReceiver $receiver, $tel_number, $tel_use_code, $tel_equipment_type) {
    return array(
      ($receiver->_configs["build_telephone_number"] == "XTN_1") ? $tel_number : null,
      // Table - 0201
      $tel_use_code,
      // Table - 0202
      $tel_equipment_type,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      ($receiver->_configs["build_telephone_number"] == "XTN_12") ? $tel_number : null,
    );
  }

  function getPL2 (CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    $value = null;
    if (!empty($receiver->_configs["build_PV1_3_2"])) {
      $value = $receiver->_configs["build_PV1_3_2"];
    }

    // Chambre
    switch ($value) {
      // Valeur en config
      case 'config_value':
        return CAppUI::conf("hl7 CHL7v2Segment PV1_3_2");
      // Identifiant externe
      case 'idex':
        if (!$affectation->_id || !$affectation->_ref_lit) {
          return null;
        } 
        
        return CIdSante400::getMatch("CChambre", $receiver->_tag_chambre, null, $affectation->_ref_lit->_ref_chambre->_id)->id400;
      // Nom de la chambre
      default:
        if (!$affectation->_id || !$affectation->_ref_lit) {
          return null;
        }
        
        return $affectation->_ref_lit->_ref_chambre->nom ;
    }
  }
  
  function getPL3 (CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    $value = null;
    if (!empty($receiver->_configs["build_PV1_3_3"])) {
      $value = $receiver->_configs["build_PV1_3_3"];
    }

    // Lit
    switch ($value) {
      // Valeur en config
      case 'config_value':
        return CAppUI::conf("hl7 CHL7v2Segment PV1_3_3");
      // Identifiant externe
      case 'idex':
        if (!$affectation->_id || !$affectation->_ref_lit) {
          return null;
        } 
        return CIdSante400::getMatch("CLit", $receiver->_tag_lit, null, $affectation->_ref_lit->_id)->id400;
      // Nom du lit
      default:
        if (!$affectation->_id || !$affectation->_ref_lit) {
          return null;
        }
        
        return $affectation->_ref_lit->nom;
    }
  }
  
  function getPL5 (CInteropReceiver $receiver) {
    $value = null;
    if (!empty($receiver->_configs["build_PV1_3_5"])) {
      $value = $receiver->_configs["build_PV1_3_5"];
    }

    // Statut du lit
    switch ($value) {
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
  
  function getPV110 (CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    $value = null;
    if (!empty($receiver->_configs["build_PV1_10"])) {
      $value = $receiver->_configs["build_PV1_10"];
    }

    // Hospital Service
    switch ($value) {
      // idex du service
      case 'service':
        if (!$affectation) {
          // Chargement de l'affectation courante
          $affectation = $sejour->getCurrAffectation();

          // Si on n'a pas d'affectation on va essayer de chercher la premi�re
          if (!$affectation->_id) {
            $sejour->loadSurrAffectations();
            $affectation = $sejour->_ref_prev_affectation;
          }
        }

        $service_id = $affectation->service_id;
        if (!$service_id) {
          if (!$sejour->service_id) {
            return null;
          }

          $service_id = $sejour->service_id;
        }
        
        return CIdSante400::getMatch("CService", $receiver->_tag_service, null, $service_id)->id400;
        
      // Discipline m�dico-tarifaire
      default:
        return $sejour->discipline_id;
    }
  }
  
  function getPV114 (CInteropReceiver $receiver, CSejour $sejour) {
    // Mode d'entr�e personnalisable
    if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_entree")) {
      return $sejour->loadRefModeEntree()->code;
    }

    $value = null;
    if (!empty($receiver->_configs["build_PV1_14"])) {
      $value = $receiver->_configs["build_PV1_14"];
    }

    // Admit source
    switch ($value) {
      // Combinaison du ZFM
      // ZFM.1 + ZFM.3
      case 'ZFM' :
        // Si mutation des urgences
        if ($sejour->provenance == "8" || $sejour->provenance == "5") {
          return $sejour->mode_entree;
        }
        
        // Sinon concat�nation du code mode d'entr�e et du code de provenance
        return $sejour->mode_entree.$sejour->provenance;

      // Mode d'entr�e
      default:
        // 1  - Envoy� par un m�decin ext�rieur
        // 3  - Convocation � l'h�pital
        // 4  - Transfert depuis un autre centre hospitalier
        // 6  - Entr�e par transfert interne
        // 7  - Entr�e en urgence
        // 8  - Entr�e sous contrainte des forces de l'ordre
        // 90 - S�jour programm�
        // 91 - D�cision personnelle
        if ($sejour->adresse_par_prat_id) {
          return 1;
        }
        if ($sejour->etablissement_entree_id) {
          return 4;
        }
        if ($sejour->service_entree_id) {
          return 6;
        }
        if ($sejour->type == "urg") {
          return 7;
        }
        
        return 90;
    }
  }

  function getPV126 (CInteropReceiver $receiver, CSejour $sejour) {
    $value = null;
    if (!empty($receiver->_configs["build_PV1_26"])) {
      $value = $receiver->_configs["build_PV1_26"];
    }

    // Identifiant du mouvement
    switch ($value) {
      case 'movement_id':
        return $sejour->_ref_hl7_movement->_id;

      // Ne rien envoyer
      default:
        return null;
    }
  }
  
  function getPV136 (CInteropReceiver $receiver, CSejour $sejour) {
    // Mode de sortie personnalisable
    if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
      return $sejour->loadRefModeSortie()->code;
    }

    // Discharge Disposition
    $value = null;
    if (!empty($receiver->_configs["build_PV1_36"])) {
      $value = $receiver->_configs["build_PV1_36"];
    }

    switch ($value) {
      // Combinaison du ZFM
      // ZFM.2 + ZFM.4
      case 'ZFM':
        $mode_sortie = $this->getModeSortie($sejour);
        // Si d�c�s
        if ($mode_sortie == "9") {
          return $mode_sortie;
        }
        
        // Sinon concat�nation du code mode de sortie et du code destination
        return $mode_sortie.$sejour->destination;
        
      // Circonstance de sortie
      default:
        // 2 - Messures disciplinaires
        // 3 - D�cision m�dicale (valeur par d�faut)
        // 4 - Contre avis m�dicale 
        // 5 - En attente d'examen
        // 6 - Convenances personnelles
        // R - Essai (contexte psychatrique)
        // E - Evasion 
        // F - Fugue
        $discharge_disposition = $sejour->confirme ? "3": "4";
        return CHL7v2TableEntry::mapTo("112", $discharge_disposition);    
    }
  }

  function getPV245(CInteropReceiver $receiver, CSejour $sejour, COperation $operation = null) {
    $value = null;
    if (!empty($receiver->_configs["build_PV2_45"])) {
      $value = $receiver->_configs["build_PV2_45"];
    }

    // Advance Directive Code
    switch ($value) {
      // Transmission de l'intervention
      case 'operation' :
        if (!$operation) {
          return null;
        }
        
        $datetime = CHL7v2::getDateTime($operation->_datetime);

        $type_anesth = new CIdSante400();
        if ($operation->type_anesth) {
          $tag_hl7     = $receiver->_tag_hl7;
          $type_anesth = CIdSante400::getMatch("CTypeAnesth", $tag_hl7, null, $operation->type_anesth);
        }
        
        $idex_chir = CIdSante400::getMatchFor($operation->loadRefChir(), $receiver->_tag_mediuser);

        $anesth = $operation->loadRefAnesth();
        $idex_anesth = new CIdSante400();
        if ($anesth->_id) {
          $idex_anesth = CIdSante400::getMatchFor($anesth, $receiver->_tag_mediuser);
        }

        $libelle = $operation->libelle;

        $PV2_45_2 = "";
        // Datetime
        if ($datetime) {
          $PV2_45_2 .= "$datetime";
        }
        $PV2_45_2 .= "#";
        // Type anesth
        if ($type_anesth->id400) {
          $PV2_45_2 .= "$type_anesth->id400";
        }
        $PV2_45_2 .= "#";
        // Idex chir
        if ($idex_chir->id400) {
          $PV2_45_2 .= "$idex_chir->id400";
        }
        $PV2_45_2 .= "#";
        // Idex anesth
        if ($idex_anesth->id400) {
          $PV2_45_2 .= "$idex_anesth->id400";
        }
        $PV2_45_2 .= "#";
        // Libelle
        if ($libelle) {
          $PV2_45_2 .= "$libelle";
        }

        if (CModule::getActive("mvsante")) {
          $PV2_45_2 .= CMVSante::getPV245($receiver, $sejour, $operation);
        }

        return array(
          array(
            // CE-1
            null,
            // CE-2
            $PV2_45_2
          )
        );
      default:
        return null;
    }
  }     
  
  function getPL(CInteropReceiver $receiver, CSejour $sejour, CAffectation $affectation = null) {
    $group       = $sejour->loadRefEtablissement();
    if (!$affectation) {
      // Chargement de l'affectation courante
      $affectation = $sejour->getCurrAffectation();

      // Si on n'a pas d'affectation on va essayer de chercher la premi�re
      if (!$affectation->_id) {
        $sejour->loadSurrAffectations();
        $affectation = $sejour->_ref_prev_affectation;
      } 
    }
    $affectation->loadRefLit()->loadRefChambre();

    $current_uf = $sejour->getUFs(null, $affectation->_id);
    
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
    $charge = new CChargePriceIndicator();
    $charge->type     = $sejour->type;
    $charge->type_pec = $sejour->type_pec;
    $charge->group_id = $sejour->group_id;
    $charge->actif    = 1;
    $charge->loadMatchingObject();

    return $charge->code;
  }
  
  function getModeSortie(CSejour $sejour) {
    switch ($sejour->mode_sortie) {
      case "mutation" :
        return 6;
      case "transfert" :
        return 7;
      case "normal" :
        return 8;
      case "deces" :
        return 9;
    }
  }
  
  function getModeProvenance(CSejour $sejour) {
    return ($sejour->provenance == "8") ? "5" : $sejour->provenance;  
  }
  
  function getSegmentActionCode(CHL7v2Event $event) {
    switch ($event->code) {
      case 'S12':
        return "A";
      case 'S13' : case 'S14' :
        return "U";
      case 'S15' :
        return "D";
    }
  }
  
  function getFillerStatutsCode(CConsultation $appointment) {
    // Table - 0278
    // Pending   - Appointment has not yet been confirmed  
    // Waitlist  - Appointment has been placed on a waiting list for a particular slot, or set of slots  
    // Booked    - The indicated appointment is booked   
    // Started   - The indicated appointment has begun and is currently in progress  
    // Complete  - The indicated appointment has completed normally (was not discontinued, canceled, or deleted)   
    // Cancelled - The indicated appointment was stopped from occurring (canceled prior to starting)   
    // Dc        - The indicated appointment was discontinued (DC'ed while in progress, discontinued parent appointment, or discontinued child appointment)  
    // Deleted   - The indicated appointment was deleted from the filler application   
    // Blocked   - The indicated time slot(s) is(are) blocked  
    // Overbook  - The appointment has been confirmed; however it is confirmed in an overbooked state  
    // Noshow    - The patient did not show up for the appointment 
    
    switch ($appointment->chrono) {
      case '32': case '48':
        return "Started";
      case '64':
        return "Complete";
    }
    
    if ($appointment->annule) {
      return "Cancelled";
    }
    
    return "Booked";
  }

  /**
   * Get segment struct
   *
   * @return array
   */
  function getStruct() {
    $data = array();

    foreach ($this->fields as $_field) {
      $data[] = $_field->getStruct();
    }

    return $data;
  }
}
