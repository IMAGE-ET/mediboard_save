<?php

/**
 * SA H'XML object handler
 *
 * @category hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * SA H'XML object handler
 *
 */
class CSaHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  /**
   * @var array
   */
  static $handled = array ("CSejour", "COperation", "CConsultation");

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
    if (!$this->isHandled($mbObject)) {
      return;
    }

    $receiver = $mbObject->_receiver;
    if (CGroups::loadCurrent()->_id != $receiver->group_id) {
      return;
    }

    $send_diags = false;
    switch ($mbObject->_class) {
      // CSejour 
      // Envoi des actes / diags soit quand le s�jour est factur�, soit quand le sejour a une sortie r�elle,
      // soit quand on a la cl�ture sur le sejour
      case 'CSejour': 
        $sejour = $mbObject;
        $sejour->loadNDA($receiver->group_id);
        
        $patient = $sejour->loadRefPatient();
        $patient->loadIPP($receiver->group_id);
        
        if (CAppUI::conf("sa send_only_with_ipp_nda")) {
          if (!$patient->_IPP || !$sejour->_NDA) {
            throw new CMbException("CSaObjectHandler-send_only_with_ipp_nda", UI_MSG_ERROR);
          }
        }
        
        if ($sejour->DP || $sejour->DR || (count($sejour->loadRefDossierMedical()->_codes_cim) > 0)) {
          $evt = (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ? 
                     "CHPrimXMLEvenementsServeurEtatsPatient" : "CHPrimXMLEvenementsPmsi";
                     
          $this->sendEvenementPMSI($evt, $sejour);

          $send_diags = true;
        }
      
        break;
      // COperation
      // Envoi des actes soit quand l'interv est factur�e, soit quand on a la cl�ture sur l'interv
      case 'COperation':
        $operation = $mbObject;
        
        $sejour  = $operation->_ref_sejour;
        $sejour->loadNDA($receiver->group_id);
        
        $patient = $sejour->loadRefPatient();
        $patient->loadIPP($receiver->group_id);
        
        break;
      // CConsultation
      // Envoi des actes dans le cas de la cl�ture de la cotation
      case 'CConsultation':
        $consultation = $mbObject;
        
        $patient = $consultation->loadRefPatient();
        $patient->loadIPP($receiver->group_id);
        
        $sejour  = $consultation->_ref_sejour;
        $sejour->loadNDA($receiver->group_id);
        
        break; 
    }
    
    if (CAppUI::conf("sa send_only_with_ipp_nda")) {
      if (!$patient->_IPP || !$sejour->_NDA) {
        throw new CMbException("CSaObjectHandler-send_only_with_ipp_nda", UI_MSG_ERROR);
      }
    }

    if (CAppUI::conf("sa send_diags_with_actes") && !$send_diags) {
      if ($sejour->DP || $sejour->DR || (count($sejour->loadRefDossierMedical()->_codes_cim) > 0)) {
        $evt = (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ?
          "CHPrimXMLEvenementsServeurEtatsPatient" : "CHPrimXMLEvenementsPmsi";

        $this->sendEvenementPMSI($evt, $sejour);
      }
    }

    $codable = $mbObject;

    // Chargement des actes du codable
    $codable->loadRefsActes();  
    
    // Envoi des actes CCAM / NGAP
    if (empty($codable->_ref_actes_ccam) && empty($codable->_ref_actes_ngap)) {
      return;
    }
    
    $this->sendEvenementPMSI("CHPrimXMLEvenementsServeurActes", $codable);   
  }
}