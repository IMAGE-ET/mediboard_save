<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/
global $AppUI;

$autoadd_default = isset($AppUI->user_prefs["AUTOADDSIGN"]) ? $AppUI->user_prefs["AUTOADDSIGN"] : 1 ;

class CDoAddictionAddEdit extends CDoObjectAddEdit {
  function CDoAddictionAddEdit() {
    $this->CDoObjectAddEdit("CAddiction", "addiction_id");
    
    $this->createMsg = "Addiction cr��e";
    $this->modifyMsg = "Addiction modifi�e";
    $this->deleteMsg = "Addiction supprim�e";

  }
  
  function doIt() {
    global $autoadd_default ;
    $this->doBind();

    if (intval(dPgetParam($_POST, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }
    if(! (!$this->isNotNew && isset($_POST["consultation_anesth_id"]) && $this->_obj->object_class == "CPatient" && $autoadd_default == 1)){
      $this->doRedirect();
    }
  }
}

$do = new CDoAddictionAddEdit;
$do->doIt();

$_POST["object_class"] = "CConsultAnesth";
$_POST["object_id"]    = $_POST["consultation_anesth_id"];

$copyDo = new CDoObjectAddEdit("CAddiction", "addiction_id");
$copyDo->createMsg = "Addiction cr��e";
$copyDo->modifyMsg = "Addiction modifi�e";
$copyDo->deleteMsg = "Addiction supprim�e";

$copyDo->doBind();
$copyDo->ajax            = $do->ajax;
$copyDo->suppressHeaders = $do->suppressHeaders;
$copyDo->callBack        = "reloadAntecedentsAnesth";
$copyDo->doStore();
$copyDo->doRedirect();
?>