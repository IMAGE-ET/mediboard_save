<?php

/**
 * $Id$
 *
 * @category Forms
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
class CPatientXMLImport extends CMbXMLObjectImport {
  protected $name_suffix;

  protected $imported = array();

  protected $import_order = array(
    // Structure objects
    "//object[@class='CGroups']",
    "//object[@class='CMediusers']",
    "//object[@class='CUser']",
    "//object[@class='CService']",
    "//object[@class='CFunctions']",
    "//object[@class='CBlocOperatoire']",
    "//object[@class='CSalle']",

    "//object[@class='CPatient']",
    "//object[@class='CDossierMedical']",
    "//object[@class='CSejour']",
    "//object[@class='COperation']",
    "//object[@class='CConsultation']",
    "//object[@class='CConstanteMedicale']",
    "//object[@class='CFile']",
    "//object",
  );

  protected $directory;

  protected $files_directory;

  static $_ignored_classes = array("CGroups", "CMediusers", "CUser", "CService", "CFunctions", "CBlocOperatoire", "CSalle");

  /**
   * @see parent::importObject()
   */
  function importObject(DOMElement $element) {
    $id = $element->getAttribute("id");

    if (isset($this->imported[$id])) {
      return;
    }

    $this->name_suffix = " (import du " . CMbDT::dateTime() . ")";

    $_class = $element->getAttribute("class");
    $imported_object = null;

    $idex = self::lookupObject($id);
    if ($idex->_id) {
      CAppUI::stepAjax("'$id' pr�sent en base", UI_MSG_OK);
      $this->imported[$id] = true;
      $this->map[$id] = $idex->loadTargetObject()->_guid;
      return;
    }

    switch ($_class) {
      case "CPatient":
        /** @var CPatient $_patient */
        $_patient = $this->getObjectFromElement($element);
        $_patient->loadMatchingPatient();

        $is_new = !$_patient->_id;

        $_patient->_merging = true; // TODO a supprimer

        if ($msg = $_patient->store()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
          throw new Exception($msg);
        }

        if ($is_new) {
          CAppUI::stepAjax("Patient '%s' cr��", UI_MSG_OK, $_patient->_view);
        }
        else {
          CAppUI::stepAjax("Patient '%s' retrouv�", UI_MSG_OK, $_patient->_view);
        }

        $imported_object = $_patient;
        break;

      case "CDossierMedical":
        /** @var CDossierMedical $_object */
        $_object = $this->getObjectFromElement($element);

        $_dossier               = new CDossierMedical();
        $_dossier->object_id    = $_object->object_id;
        $_dossier->object_class = $_object->object_class;
        $_dossier->loadMatchingObject();

        if (!$_dossier->_id) {
          if ($msg = $_object->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }

          CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' cr��", UI_MSG_OK, $_object);
          $imported_object = $_object;
        }
        else {
          $imported_object = $_dossier;
        }
        break;

      case "CAntecedent":
        /** @var CAntecedent $_new_atcd */
        $_new_atcd = $this->getObjectFromElement($element);

        // On cherche un ATCD similaire
        $_empty_atcd                     = new CAntecedent();
        $_empty_atcd->dossier_medical_id = $_new_atcd->dossier_medical_id;
        $_empty_atcd->type               = $_new_atcd->type ?: null;
        $_empty_atcd->appareil           = $_new_atcd->appareil ?: null;
        $_empty_atcd->annule             = $_new_atcd->annule ?: null;
        $_empty_atcd->date               = $_new_atcd->date ?: null;
        $_empty_atcd->rques              = $_new_atcd->rques ?: null;
        $_empty_atcd->loadMatchingObject();

        if (!$_empty_atcd->_id) {
          $_new_atcd->_forwardRefMerging = true; // To accept any ATCD type
          if ($msg = $_new_atcd->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }

          CAppUI::stepAjax("Ant�c�dent '%s' cr��", UI_MSG_OK, $_new_atcd->_view);
        }

        $imported_object = $_new_atcd;
        break;

      case "CPlageOp":
      case "CPlageconsult":
        /** @var CPlageOp|CPlageconsult $_plage */
        $_plage = $this->getObjectFromElement($element);
        $_plage->hasCollisions();

        if (count($_plage->_colliding_plages)) {
          $_plage = reset($_plage->_colliding_plages);
          CAppUI::stepAjax("%s '%s' retrouv�e", UI_MSG_OK, CAppUI::tr($_plage), $_plage->_view);
        }
        else {
          if ($msg = $_plage->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }

          CAppUI::stepAjax("%s '%s' cr��e", UI_MSG_OK, CAppUI::tr($_plage), $_plage->_view);
        }

        $imported_object = $_plage;
        break;

      case "CFile":
        /** @var CFile $_file */
        $_file    = $this->getObjectFromElement($element);
        $_filedir = $this->getCFileDirectory($element);

        if (!$_file->moveFile($_filedir)) {
          CAppUI::stepAjax("Fichier '%s' non trouv� dans '%s'", UI_MSG_WARNING, $_file->_view, $_filedir);
        }
        else {
          if ($msg = $_file->store()) {
            throw new Exception($msg);
          }

          CAppUI::stepAjax("Fichier '%s' cr��", UI_MSG_OK, $_file->_view);
        }

        $imported_object = $_file;
        break;

      case "CConsultation":
        /** @var CConsultation $_object */
        $_object = $this->getObjectFromElement($element);

        $_new_consult                  = new CConsultation();
        $_new_consult->patient_id      = $_object->patient_id;
        $_new_consult->plageconsult_id = $_object->plageconsult_id;
        $_new_consult->loadMatchingObject();

        if ($_new_consult->_id) {
          $_object = $_new_consult;

          CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' retrouv�", UI_MSG_OK, $_object);
        }
        else {
          if ($msg = $_object->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }
          CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' cr��", UI_MSG_OK, $_object);
        }

        $imported_object = $_object;
        break;

      case "CSejour":
        /** @var CSejour $_object */
        $_object = $this->getObjectFromElement($element);

        $_collisions = $_object->getCollisions();

        if (count($_collisions)) {
          $_object = reset($_collisions);

          CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' retrouv�", UI_MSG_OK, $_object);
        }
        else {

          if ($msg = $_object->store()) {
            CAppUI::stepAjax($msg, UI_MSG_WARNING);
            break;
          }
          CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' cr��", UI_MSG_OK, $_object);
        }

        $imported_object = $_object;
        break;

      default:
        // Ignored classes
        if (in_array($_class, self::$_ignored_classes)) {
          break;
        }

        $_object = $this->getObjectFromElement($element);

        if ($msg = $_object->store()) {
          CAppUI::stepAjax($msg, UI_MSG_WARNING);
          break;
        }
        CAppUI::stepAjax(CAppUI::tr($_object->_class) . " '%s' cr��", UI_MSG_OK, $_object);

        $imported_object = $_object;
        break;
    }

    // Store idex on new object
    if ($imported_object) {
      $idex->setObject($imported_object);
      $idex->id400 = $id;
      if ($msg = $idex->store()) {
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
      }
    }
    else {
      if (!in_array($_class, self::$_ignored_classes)) {
        CAppUI::stepAjax("$id sans objet", UI_MSG_WARNING);
      }
    }

    if ($imported_object) {
      $this->map[$id] = $imported_object->_guid;
    }

    $this->imported[$id] = true;
  }

  /**
   * @param DOMElement $element
   */
  function getCFileDirectory(DOMElement $element) {
    list($object_class, $object_id) = explode("-", $element->getAttribute("object_id"));
    $uid = $this->getNamedValueFromElement($element, "file_real_filename");

    $dir = "/$object_class/" . intval($object_id / 1000) . "/$object_id/$uid";

    if ($this->files_directory) {
      $dir = rtrim($this->files_directory, "/\\") . $dir;
    }
    else {
      $dir = rtrim($this->directory, "/\\") . $dir;
    }

    return $dir;
  }

  /**
   * @see parent::importObjectByGuid()
   */
  function importObjectByGuid($guid) {
    list($class, $id) = explode("-", $guid);

    if (in_array($class, self::$_ignored_classes)) {
      $lookup_guid = $guid;

      if ($class == "CMediusers") {
        // Idex are stored on the CUser
        $lookup_guid = "CUser-$id";
      }

      $idex = $this->lookupObject($lookup_guid);

      if ($idex->_id) {
        $this->map[$guid] = "$class-$idex->object_id";
        $this->imported[$guid] = true;
      }
      else {
        if ($class == "CMediusers") {
          $this->map[$guid] = CMediusers::get()->_guid;
          $this->imported[$guid] = true;
        }
      }
    }
    else {
      /** @var DOMElement $_element */
      $_element = $this->xpath->query("//*[@id='$guid']")->item(0);
      $this->importObject($_element);
    }
  }

  /**
   * Lookup an object already imported
   *
   * @param string $guid Guid of the object to lookup
   * @param string $tag  Tag of it's Idex
   *
   * @return CIdSante400
   */
  function lookupObject($guid, $tag = "migration") {
    list($class, $id) = explode("-", $guid);

    $idex = CIdSante400::getMatch($class, $tag, null, $id);

    return $idex;
  }

  function setFilesDirectory($directory) {
    $this->files_directory = $directory;
  }

  function setDirectory($directory) {
    $this->directory = $directory;
  }
}