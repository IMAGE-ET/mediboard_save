<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("CMbFieldSpec");

class CCodeSpec extends CMbFieldSpec {
  
  var $ccam  = null;
  var $cim10 = null;
  var $adeli = null;
  var $insee = null;
  var $rib   = null;
  var $siret = null;
  var $order_number = null;
  
  function getSpecType() {
    return "code";
  }
  
  function getDBSpec(){
    $type_sql = null;
    
    if($this->ccam)
      $type_sql = "VARCHAR(7)";
    elseif($this->cim10)
      $type_sql = "VARCHAR(5)";
    elseif($this->adeli)
      $type_sql = "VARCHAR(9)";
    elseif($this->insee)
      $type_sql = "VARCHAR(15)";
    elseif($this->rib)
      $type_sql = "VARCHAR(23)";
    elseif($this->siret)
      $type_sql = "VARCHAR(14)";

    return $type_sql;
  }
  
  function getOptions(){
    return array(
      'ccam'         => 'bool',
      'cim10'        => 'bool',
      'adeli'        => 'bool',
      'insee'        => 'bool',
      'rib'          => 'bool',
      'siret'        => 'bool',
      'order_number' => 'bool',
    ) + parent::getOptions();
  }
  
  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
       
    // ccam
    if($this->ccam){
      //^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$
      // ancienne expression reguliere ([a-z0-9]){0,7}
      if (!preg_match ("/^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$/i", $propValue)) {
        return "Code CCAM incorrect";
      }
    }
    
    // cim10
    elseif ($this->cim10) {
      if (!preg_match ("/^[a-z][0-9x]{2,4}$/i", $propValue)) {
//        $codeCim = new CCodeCIM10($propValue);
//        if ($codeCim->loadLite()) {
//          return "Code CIM inconnu";
//        }
        
        return "Code CIM incorrect, doit contenir une lettre, puis de 2 � 4 chiffres ou la lettre X";
      }
    }
    
    // adeli
    elseif($this->adeli) {
      if (!preg_match ("/^([0-9]){9}$/i", $propValue)) {
        return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
      }
    }

    // RIB
    elseif($this->rib) {
      $compte_banque  = substr($propValue, 0, 5);
      $compte_guichet = substr($propValue, 5, 5);
      $compte_numero  = substr($propValue, 10, 11);
      $compte_cle     = substr($propValue, 21, 2);
      $tabcompte = "";
      $len = strlen($compte_numero);
      for ($i = 0; $i < $len; $i++) {
        $car = substr($compte_numero, $i, 1);
        if (!is_numeric($car)) {
           $c = ord($car) - 64;
           $b = ($c < 10) ? $c : (($c < 19) ? $c - 9 : $c - 17);
           $tabcompte .= $b;
        }
        else {
           $tabcompte .= $car;
        }
      }
      $int = $compte_banque . $compte_guichet . $tabcompte . $compte_cle;
      if (!((strlen($int) >= 21) && (bcmod($int, 97) == 0))){
        return "Rib incorrect";
      }
    }
     
    // INSEE
    elseif($this->insee){
      
      if (preg_match ("/^([0-9]{7,8}[A-Z])$/i", $propValue)) {
        return;
      }
      
      $matches = null;
      if (!preg_match ("/^([12478][0-9]{2}[0-9]{2}[0-9][0-9ab][0-9]{3}[0-9]{3})([0-9]{2})$/i", $propValue, $matches)) {
        return "Matricule incorrect";
      }
 
      $code = preg_replace(array('/2A/i', '/2B/i'), array(19, 18), $matches[1]);
      $cle  = $matches[2];
      
      if (97 - bcmod($code, 97) != $cle) {
        return "Matricule incorrect, la cl� n'est pas valide";
      }
    }
    
    // siret
    elseif($this->siret) {
      if (!luhn($propValue)) {
        return "Code SIRET incorrect, doit contenir exactement 14 chiffres";
      }
    }
    
    // order_number
    elseif($this->order_number) {
      if (!preg_match('#\%id#', $propValue)) {
        return "Format de num�ro de serie incorrect, doit contenir au moins une fois %id";
      }
    }
    
    else {
      return "Sp�cification de code invalide";
    }
  }

  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $propValue = &$object->{$this->fieldName};
    
    // ccam
    if($this->ccam){
      $propValue = "BFGA004";
    
    // cim10
    }elseif($this->cim10){
      $propValue = "H251";
      
    // adeli
    }elseif($this->adeli){
      $propValue = "123456789";

    // rib
    }elseif($this->rib){
      $propValue = "11111111111111111111111";
    
    // siret
    }elseif($this->siret){
      $propValue = "73282932000074";
    
    // insee
    }elseif($this->insee){
      $propValue = "100000000000047";
    }
  }
}

?>