<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Note concernant une hi�rarchie CsARR
 */
class CNoteHierarchieCsARR extends CCsARRObject {

  public $hierarchie;
  public $idnote;
  public $typenote;
  public $niveau;
  public $libelle;
  public $ordre;
  public $hierarchie_exclue;
  public $code_exclu;

  public $_ref_hierarchie;
  public $_ref_hierarchie_exclue;
  public $_ref_activite_exclue;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'note_hierarchie';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["hierarchie"]        = "str notNull length|11 seekable";
    $props["idnote"]            = "str notNull length|10";
    $props["typenote"]          = "enum notNull list|aut_note|avec_sans|codage|compr_tit|def|exclusion|inclus";
    $props["niveau"]            = "num show|0";
    $props["libelle"]           = "text notNull seekable";
    $props["ordre"]             = "num show|0";
    $props["hierarchie_exclue"] = "str length|11 seekable";
    $props["code_exclu"       ] = "str length|7 seekable";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->hierarchie ($this->typenote): $this->libelle";
    $this->_shortview = $this->idnote;
  }

  function loadRefHierarchie() {
    return $this->_ref_hierarchie = CHierarchieCsARR::get($this->hierarchie);
  }

  function loadRefHierarchieExlue() {
    return $this->_ref_hierarchie_exclue = CHierarchieCsARR::get($this->code_exclus);
  }

  function loadRefCodeExclu() {
    return $this->_ref_code_exclu = CActiviteCsARR::get($this->code_exclus);
  }

  function loadView(){
    parent::loadView();
    $this->loadRefHierarchie();
    $this->loadRefHierarchieExlue();
    $this->loadRefCodeExclu();
  }
}
