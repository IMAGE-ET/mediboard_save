<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * The HPRIM 2.1 medecin class
 */
class CHprim21Medecin extends CHprim21Object {
  // DB Table key
	var $hprim21_medecin_id = null;
  
  // DB references
  var $user_id = null;
	
  // DB Fields
  var $nom       = null;
  var $prenom    = null;
  var $prenom2   = null;
  var $alias     = null;
  var $civilite  = null;
  var $diplome   = null;
  var $type_code = null;
  
  var $_ref_user = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_medecin';
    $spec->key   = 'hprim21_medecin_id';
    return $spec;
  }
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "user_id"     => "ref class|CMediusers",
      "nom"         => "str",
      "prenom"      => "str",
      "prenom2"     => "str",
      "alias"       => "str",
      "civilite"    => "str",
      "diplome"     => "str",
      "type_code"   => "str",
    );
    return array_merge($specsParent, $specs);
  }

  function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["hprim21_sejours"] = "CHprim21Sejour hprim21_medecin_id";
	  return $backProps;
	}  
  
	function bindToLine($line, &$reader) {
    $this->setHprim21ReaderVars($reader);
    
    $elements = explode($reader->separateur_champ, $line);
  
    if(count($elements) < 1) {
      $reader->error_log[] = "Champs manquant dans le segment patient (m�decin)";
      return false;
    }
    
    $identite = explode($reader->separateur_sous_champ, $elements[13]);
    if(!$identite[0]) {
      return false;
    }
    $this->external_id = $identite[0];
    $this->loadMatchingObject();
    $this->nom         = $identite[1];
    $this->prenom      = $identite[2];
    $this->prenom2     = $identite[3];
    $this->alias       = $identite[4];
    $this->civilite    = $identite[5];
    
    return true;
  }
  
  function updateFormFields() {
    $this->_view = $this->nom;
  }
  
  function loadRefsFwd(){
    // Chargement du s�jour correspondant
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
}
?>