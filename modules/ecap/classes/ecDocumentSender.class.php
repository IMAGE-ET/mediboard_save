<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision: 6226 $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
CAppUI::requireModuleClass('dPfiles', 'documentSender');

class CEcDocumentSender extends CDocumentSender { 
  public static $sendables = array(
	  "CPatient"       => array ("PA"),
	  "CSejour"        => array ("SJ", "AT"),
	  "COperation"     => array ("IN"),
	  "CConsultation"  => array ("PA"),
	  "CConsultAnesth" => array ("PA"),
  );
  
  public static function getParams() {
    $source = CExchangeSource::get("ecap_files");
    
    $idExt = new CIdSante400;
    $idExt->loadLatestFor(CGroups::loadCurrent(), "eCap");
   
    return array(
      "aLoginApplicatif"       => $source->user,
      "aPasswordApplicatif"    => $source->password,
      "aTypeIdentifiantActeur" => "LoginUser",
      "aIdentifiantActeur"     => CAppUI::conf("ecap WebServices user_login_prefix").$idExt->id400,
      "aIdClinique"            => $idExt->id400,
    );
  }
  
  static $ids = array();
  
  /**
   * Get cached target Id for object and target ecap type
   * @param CMbObject $object Mediboard Object
   * @param string $ecType ecap Type : [PA|SJ|AT|IN]
   * @return string ecap identifier, null on error
   */
  static function getIdFor(CMbObject $object, $ecType) {
    if (!in_array($ecType, self::$sendables[$object->_class_name])) {
      trigger_error("Mauvaise association de la classe Mediboard '$object->_class_name' avec le type eCap '$ecType'");
      return;
    }
    
    if (!@array_key_exists($object->_guid, self::$ids[$ecType])) {
	    $ident = new CIdSante400();
	    
	    if ($object instanceof CPatient) {
	   	  $ident->loadLatestFor($object, CMedicap::getTag($ecType));
	   	}
	    
	   	if ($object instanceof CSejour) {
	   	  $ident->loadLatestFor($object, CMedicap::getTag($ecType));
	   	}
	    
	   	if ($object instanceof COperation) {
	   	  $ident->loadLatestFor($object, CMedicap::getTag($ecType));
	   	}
	
	   	if ($object instanceof CConsultation) {
	      $object->loadRefPatient();
	   	  $ident->loadLatestFor($object->_ref_patient, CMedicap::getTag($ecType));
	   	}
	
	    self::$ids[$ecType][$object->_guid] = $ident->id400;
    }
    
    return self::$ids[$ecType][$object->_guid];  
  }
    
  /**
   * Get Patient Id for object
   * @param CMbObject $object Mediboard Object
   * @return string ecap identifier, null on error
   */
  static function getPatientIdFor(CMbObject $object) {
    if ($object instanceof CPatient) {
    	return self::getIdFor($object, "PA");
    }
    
    if ($object instanceof CCodable) {
      $object->loadRefPatient();
    	return self::getIdFor($object->_ref_patient, "PA");
    }

    return null;
  }
    
  function send(CDocumentItem $docItem) {
    $docItem->load();
    $docItem->loadTargetObject();
    
    // identifiant externe du document 
    $idDocItem = new CIdSante400();
    $idDocItem->loadLatestFor($docItem, CMedicap::getTag("DO"));
    $idDocItem->last_update = mbDateTime(); 
    @list($ecDocument, $ecVersion) = explode("-", $idDocItem->id400); 
    $ecDocument = $ecDocument ? $ecDocument : "0";
    $ecVersion  = $ecVersion  ? $ecVersion  : "0";
    
    // Identifiant externe de la cat�gorie
    $docItem->loadRefCategory();
    $idCategory = new CIdSante400();
    $idCategory->loadLatestFor($docItem->_ref_category, CMedicap::getTag("DT"));
    list($ecTypeObjet, $ecTypeDocument) = explode("-", $idCategory->id400);
    
    // Identifiant externe du patient, quelle que soit la cible
    $ecPatient = self::getPatientIdFor($docItem->_ref_object);
    
    // Chargement de la cible
    $ecObject = self::getIdFor($docItem->_ref_object, $ecTypeObjet);
    
    if (null == $params = self::getParams()) {
      return false;
    }
    
    // Param�tres SOAP
    $params["aIPMachine"      ] = $_SERVER["REMOTE_ADDR"];
    $params["aIdDocument"     ] = (int) $ecDocument;
    $params["aIdVersion"      ] = (int) $ecVersion;
    $params["aIdPatient"      ] = $ecPatient;
    $params["aTypeObjet"      ] = $ecTypeObjet;
    $params["aIdTypeDocument" ] = $ecTypeDocument;
    $params["aCommentaire"    ] = "Pas de commentaire";
    $params["aIdObjet"        ] = $ecObject;
		
		$extensioned = $docItem->getExtensioned();
    $params["aLibelleDocument"] = utf8_encode($extensioned);
    $params["aNomFichier"     ] = utf8_encode($extensioned);
    $params["aTypeIdentifiantResponsableLegal"] = "texteLibre";
    
		$responsable = "Ind�termin�";
		$codable = $docItem->_ref_object;
		if ($codable instanceof CCodable) {
			$codable->loadRefPraticien();
			$responsable = utf8_encode($codable->_ref_praticien->_view);
		}
		
		$params["aIdentifiantResponsableLegal"] = $responsable;
    $params["aTexteResponsableLegal"] = $responsable;
		
    $params["aFichierByte"    ] = $docItem->getContent();
		
    $source = CExchangeSource::get("ecap_files");
    $source->setData($params);
    $source->send("DeposerDocumentPatient");
    $result = simplexml_load_string($source->receive()->DeposerDocumentPatientResult->any);
		$result->descriptionRetour = utf8_decode($result->descriptionRetour);
		if ($result->codeRetour != "0") {
	    trigger_error("ecDocumentSender SOAP error [$result->codeRetour] for '$docItem->_guid': $result->descriptionRetour", E_USER_WARNING);
	    return false;
		}

		// Assocation des identifiant 
		$ecDocument = $result->document;
		if (is_array($ecDocument)) $ecDocument = $ecDocument[0];
		$idDocItem->id400 = "$ecDocument->id-$ecDocument->numeroVersion";
		if ($msg = $idDocItem->store()) {
	    trigger_error("ecDocumentSender Identifier store error for '$docItem->_guid': $msg",E_USER_WARNING);
	    return false;
		}
		
    // Change l'etat du document
    $docItem->etat_envoi = "oui";
    
    return true; 
  }
  
  function cancel($docItem) {
    $docItem->load();
    $docItem->loadTargetObject();
    
    // identifiant externe du document 
    $idDocItem = new CIdSante400();
    $idDocItem->loadLatestFor($docItem, CMedicap::getTag("DO"));
    $idDocItem->last_update = mbDateTime(); 
    @list($ecDocument, $ecVersion) = explode("-", $idDocItem->id400); 
    $ecDocument = $ecDocument ? $ecDocument : "0";
    $ecVersion  = $ecVersion  ? $ecVersion  : "0";
    
    // Identifiant externe du patient, quelle que soit la cible
    $ecPatient = self::getPatientIdFor($docItem->_ref_object);
    
    if (null == $params = self::getParams()) {
      return false;
    }
    
    // Param�tres SOAP
    $params["aIPMachine"        ] = $_SERVER["REMOTE_ADDR"];
    $params["aIdDocument"       ] = (int) $ecDocument;
    $params["aIdVersion"        ] = (int) $ecVersion;
    $params["aIdPatient"        ] = $ecPatient;
    $params["aMotifInvalidation"] = "Mediboard user request";
    
    $source = CExchangeSource::get("ecap_files");
    $source->setData($params);
    $source->send("InvaliderDocumentPatient");
    $result = simplexml_load_string($source->receive()->InvaliderDocumentPatientResult->any);
		$result->descriptionRetour = utf8_decode($result->descriptionRetour);
		if ($result->codeRetour != "0") {
	    trigger_error("ecDocumentSender SOAP error [$result->codeRetour] for '$docItem->_guid': $result->descriptionRetour", E_USER_WARNING);
	    return false;
		}
		
    // Change l'etat du document
    $docItem->etat_envoi = "non";
    
    return true; 
  }
  
  function resend($docItem) {
    return $this->send($docItem);
  }
    
  function getSendProblem(CDocumentItem $docItem) {
    // Type de la cible
	  $docItem->loadTargetObject();
    if (!array_key_exists($docItem->_ref_object->_class_name, self::$sendables)) {
      return sprintf("Type d'objet '%s' non pris en charge", 
        CAppUI::tr($docItem->_ref_object->_class_name));
    }
    
    // Cat�gorie obligatoire
    if (!$docItem->file_category_id) {
      return "Ce document n'a pas de cat�gorie";
    }
    
    // Type de document Medicap
    $docItem->loadRefCategory();
    $category = $docItem->_ref_category;
    
    $idCategory = new CIdSante400();
    $idCategory->loadLatestFor($docItem->_ref_category, CMedicap::getTag("DT"));
    if (!$idCategory->id400) {
      return "La cat�gorie de documents n'est pas li� � un type de document e-Cap'";
    }
    list($ecTypeObjet, $ecTypeDocument) = explode("-", $idCategory->id400);
    
    // Identifiant externe du patient, quelle que soit la cible
    if (null == $ecPatient = self::getPatientIdFor($docItem->_ref_object)) {
      return "Patient inconnu par e-Cap pour l'�tablissement " . CMedicap::$cidc;
    }
    
    // Identifiant de la cible
    $ecObject = self::getIdFor($docItem->_ref_object, $ecTypeObjet);
    if (null == $ecObject = self::getPatientIdFor($docItem->_ref_object)) {
      return "Contexte de l'objet inconnu par e-Cap pour '�tablissement " . CMedicap::$cidc;
    }
  }
}

?>