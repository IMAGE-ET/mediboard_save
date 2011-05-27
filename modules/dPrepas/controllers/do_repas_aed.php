<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author S�bastien Fillonneau
*/

class CDoRepasAddEdit extends CDoObjectAddEdit {
  
  var $synchro         = null;
  var $synchroConfirm  = null;
  var $synchroDatetime = null;
  var $ds = null;
  
  function CDoRepasAddEdit() {
    global $m;
    
    $this->CDoObjectAddEdit("CRepas", "repas_id");
    
    $this->redirect  = "m=$m&tab=vw_planning_repas";
    
    // Synchronisation Offline
    $this->synchro         = CValue::post("_syncroOffline"  , false);
    $this->synchroConfirm  = CValue::post("_synchroConfirm" , null);
    $this->synchroDatetime = CValue::post("_synchroDatetime", null);
    $this->ds = CSQLDataSource::get("std");
    
  }
  
  function doRedirect($demandeSynchro = false) {

    if ($this->ajax) {
      if($this->synchro){
        $del          = CValue::post("del", 0);
        $tmp_repas_id = CValue::post("_tmp_repas_id", 0);
        $msgSystem    = CAppUI::getMsg();

        $smarty       = new CSmartyDP("modules/dPrepas");

        $smarty->assign("del"            , $del);
        $smarty->assign("tmp_repas_id"   , $tmp_repas_id);
        $smarty->assign("demandeSynchro" , $demandeSynchro);
        $smarty->assign("msgSystem"      , $msgSystem);
        $smarty->assign("callBack"       , $this->callBack);
        if($demandeSynchro){
          $smarty->assign("object"     , $this->_objBefore);
        }
        $smarty->display("add_del_repas_offline.tpl");
      }
			CApp::rip();
    }

    if ($this->redirect !== null) {
      CAppUI::redirect($this->redirect);
    }
  }

  function doIt() {
    $this->doBind();
    
    if($this->synchro){
       if(!$this->_objBefore->_id && $this->_obj->repas_id){
         // Repas supprim� depuis la derniere synchro
         CAppUI::setMsg("Le repas a �t� supprim� depuis la derni�re synchronisation.", UI_MSG_ERROR );
         $this->doRedirect();
       }
       //Test suppression de ref
       $error_ref = null;
       $object    = $this->_obj;
       $plats     = new CPlat;
       $object->loadRemplacements();
       $object->loadRefAffectation();
       $object->loadRefMenu();
       if(!$object->_ref_affectation->affectation_id){
         CAppUI::setMsg("L'affectation n'existe pas.", UI_MSG_ERROR );
         $error_ref = true;
       }
       if($object->menu_id && !$object->_ref_menu->menu_id){
         CAppUI::setMsg("Le menu n'existe pas.", UI_MSG_ERROR );
         $error_ref = true;
       }
       foreach($plats->_specs["type"]->_list as $curr_typePlat){
         if($object->$curr_typePlat && !$object->{"_ref_".$curr_typePlat}){
           CAppUI::setMsg("Le Plat de remplacement ".$curr_typePlat." n'existe pas.", UI_MSG_ERROR );
           $error_ref = true;
         }
       }
       if($error_ref){
         $this->doRedirect();
       }
       if(!$this->synchroConfirm && $this->_objBefore->_id){
         $object = $this->_objBefore;
         
         $select = "count(`user_log_id`) AS `total`";
         $table  = "user_log";
         $where = array();
         $where["object_id"]    = "= '$object->_id'";
         $where["object_class"] = "= '$this->className'";
         $where["date"]         = ">= '".strftime("%Y-%m-%d %H:%M:%S", $this->synchroDatetime)."'";
         
         $sql = new CRequest();
         $sql->addTable($table);
         $sql->addSelect($select);
         $sql->addWhere($where);
         
         $nbLogs = $this->ds->loadResult($sql->getRequest());
         
         if($nbLogs){
           CAppUI::setMsg("Le repas a �t� modifi� depuis la derni�re synchronisation. Voulez-vous tout de m�me l'enregistrer ?", UI_MSG_WARNING);
           $this->doRedirect(true);
         }
       }
    }

    if (intval(CValue::post('del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }

    $this->doRedirect();
  }  
  
}

$do = new CDoRepasAddEdit;
$do->doIt();

?>