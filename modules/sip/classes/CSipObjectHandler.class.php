<?php

/**
 * SIP Event Handler
 *
 * @category SIP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSipObjectHandler
 * SIP Event Handler
 */

class CSipObjectHandler extends CEAIObjectHandler {
  /**
   * @var array
   */
  static $handled = array ("CPatient", "CCorrespondantPatient", "CIdSante400");

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return void
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    // Si pas de tag patient
    if (!CAppUI::conf("dPpatients CPatient tag_ipp")) {
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas d'IPP sur le patient
    if ((isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1)) && CAppUI::conf('sip server')) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return void
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!parent::onBeforeMerge($mbObject)) {
      return;
    }
    
    // Si pas en mode alternatif
    if (!CAppUI::conf("alternative_mode")) {
      throw new CMbException("no_alternative_mode");
    }
    
    $patient = $mbObject;

    $patient_elimine = new CPatient();
    $patient_elimine->load(reset($mbObject->_merging));

    // Si Client
    if (!CAppUI::conf('sip server')) {
      $mbObject->_fusion = array();
      foreach (CGroups::loadGroups() as $_group) {
        if ($mbObject->_eai_initiateur_group_id == $_group->_id) {
          continue;
        }
        $patient->_IPP = null;
        $patient->loadIPP($_group->_id);
        $patient1_ipp = $patient->_IPP;

        $patient_elimine->_IPP = null;
        $patient_elimine->loadIPP($_group->_id);
        $patient2_ipp = $patient_elimine->_IPP;

        // Passage en trash des IPP des patients
        $tap_IPP = CPatient::getTagIPP($_group->_id);
        if (!$tap_IPP) {
          continue;
        }

        $idexPatient = new CIdSante400();
        $idexPatient->tag          = $tap_IPP;
        $idexPatient->object_class = "CPatient";
        $idexPatient->object_id    = $patient->_id;
        $idexsPatient = $idexPatient->loadMatchingList();
        
        $idexPatientElimine = new CIdSante400();
        $idexPatientElimine->tag          = $tap_IPP;
        $idexPatientElimine->object_class = "CPatient";
        $idexPatientElimine->object_id    = $patient_elimine->_id;
        $idexsPatientElimine = $idexPatientElimine->loadMatchingList();

        $idexs = array_merge($idexsPatient, $idexsPatientElimine);
        if (count($idexs) > 1) {
          foreach ($idexs as $_idex) {
            // On continue pour ne pas mettre en trash l'IPP du patient que l'on garde
            if ($_idex->id400 == $patient1_ipp) {
              continue;
            }

            $old_tag = $_idex->tag;

            $_idex->tag         = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$tap_IPP;
            $_idex->last_update = CMbDT::dateTime();
            if (!$msg = $_idex->store()) {
              if ($_idex->object_id == $patient_elimine->_id) {
                $idexs_changed[$_idex->_id] = $old_tag;
              }
            }
          }
        }
        
        if (!$patient1_ipp && !$patient2_ipp) {
          continue;  
        }
        
        $mbObject->_fusion[$_group->_id] = array (
          "patientElimine" => $patient_elimine,
          "patient1_ipp"   => $patient1_ipp,
          "patient2_ipp"   => $patient2_ipp,
          "idexs_changed" => $idexs_changed
        );
       
      }        
    }

    $this->sendFormatAction("onBeforeMerge", $mbObject);
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!parent::onAfterMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterMerge", $mbObject);
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }
}