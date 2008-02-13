<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 1706 $
 */

global $AppUI, $canRead, $canEdit, $m;

class CDoObjectAddEdit {
  var $className           = null;
  var $objectKeyGetVarName = null;
  var $createMsg           = null;
  var $modifyMsg           = null;
  var $deleteMsg           = null;
  var $refTab              = null;
  var $redirect            = null;
  var $redirectStore       = null;
  var $redirectError       = null;
  var $redirectDelete      = null;
  var $isNotNew            = null;
  var $ajax                = null;
  var $callBack            = null;
  var $suppressHeaders     = null;
  var $_obj                = null;
  var $_objBefore          = null;
  var $_logIt              = null;

  function CDoObjectAddEdit($className, $objectKeyGetVarName) {
    global $m,$AppUI;

    $this->className           = $className;
    $this->objectKeyGetVarName = $objectKeyGetVarName;
    $this->redirect            = "m={$m}";
    $this->redirectStore       = null;
    $this->redirectError       = null;
    $this->redirectDelete      = null;

    $this->createMsg           = CAppUI::tr("msg-$className-create");
    $this->modifyMsg           = CAppUI::tr("msg-$className-modify");
    $this->deleteMsg           = CAppUI::tr("msg-$className-delete");
    
    $this->refTab              =& $_POST;

    $this->_logIt              = true;
    $this->_obj                = new $this->className();
    $this->_objBefore          = new $this->className();
  }

  function doBind() {
    global $AppUI;

    $this->ajax            = CMbArray::extract($this->refTab, "ajax");
    $this->suppressHeaders = CMbArray::extract($this->refTab, "suppressHeaders");
    $this->callBack        = CMbArray::extract($this->refTab, "callback");
    
    // Object binding
    if (!$this->_obj->bind( $this->refTab )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }

    $this->_objBefore->load($this->_obj->_id);
  }

  function doDelete() {
    global $AppUI;

    if ($msg = $this->_obj->delete()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } else {
      mbSetValueToSession($this->objectKeyGetVarName);
      $AppUI->setMsg($this->deleteMsg, UI_MSG_ALERT);
      if ($this->redirectDelete) {
        $this->redirect =& $this->redirectDelete;
      }
    }
  }

  function doStore() {
    global $AppUI;
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    } else {
      $id = $this->objectKeyGetVarName;
      mbSetValueToSession($id, $this->_obj->$id);
      $this->isNotNew = @$this->refTab[$this->objectKeyGetVarName];
      $AppUI->setMsg($this->isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      if ($this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
    }
  }

  function doRedirect() {
    global $AppUI;
  
    if ($this->redirect === null) {
      return;
    }
    
    
    // Cas ajax
    if ($this->ajax) {
      $idName = $this->objectKeyGetVarName;
      $idValue = $this->_obj->$idName;
      $callBack = $this->callBack;
      echo nl2br($AppUI->getMsg());
      if ($callBack) {
        echo "\n<script type='text/javascript'>$callBack($idValue);</script>";
      }
      exit;
    }

    // Cas normal
    $AppUI->redirect($this->redirect);
    
  }

  function doIt() {
    $this->doBind();
    
    if (intval(dPgetParam($this->refTab, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }
    $this->doRedirect();
  }

  /**
   * Sets a error messages and redirects
   * @param string $msg 
   */
  function errorRedirect($msg) {
    global $AppUI;
	  $AppUI->setMsg($msg, UI_MSG_ERROR );
	  $this->redirect =& $this->redirectError;
	  $this->doRedirect();
  }
}
