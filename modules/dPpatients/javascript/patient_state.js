/**
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

PatientState = {

  getListPatientByState : function (state, page) {
    new Url("dPpatients", "ajax_list_patient_state")
      .addParam("state", state)
      .addParam("page", page)
      .requestUpdate("patient_"+state);
  },

  edit_patient : function (patient_id, state) {
    Patient.editModal(patient_id, false, null, PatientState.getListPatientByState.curry(state))
  },

  changePage : {
    prov : function (page) {
      PatientState.getListPatientByState('prov', page);
    },

    dpot : function (page) {
      PatientState.getListPatientByState('dpot', page);
    },

    anom : function (page) {
      PatientState.getListPatientByState('anom', page);
    },

    cach : function (page) {
      PatientState.getListPatientByState('cach', page);
    }
  },

  mergePatient : function(patients_id) {
    new Url("system", "object_merger")
      .addParam("objects_class", "CPatient")
      .addParam("objects_id", patients_id)
      .popup(800, 600, "merge_patients");
  }
};