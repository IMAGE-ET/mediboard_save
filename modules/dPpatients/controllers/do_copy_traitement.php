<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CDoCopyTraitement extends CDoObjectAddEdit {
  function CDoCopyTraitement() {
    $this->CDoObjectAddEdit("CTraitement", "traitement_id");
  }  

  function doBind() {
    parent::doBind();

    // recuperation du sejour_id
    $_sejour_id = CValue::post("_sejour_id"  , null);

    // si pas de sejour_id, redirection
    if (!$_sejour_id) {
       $this->doRedirect();
    }

    // Creation du nouveau traitement affect�e au sejour
    unset($_POST["traitement_id"]);
    $this->_obj = $this->_old;
    $this->_obj->_id = null;
    $this->_obj->traitement_id = null;

    // Calcul de la valeur de l'id du dossier_medical du sejour
    $this->_obj->dossier_medical_id = CDossierMedical::dossierMedicalId($_sejour_id, "CSejour");
  }
}

$do = new CDoCopyTraitement();
$do->doIt();
