<?php

/**
 * Transfert de donn�es d'admission - H'2.1
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21ADM 
 * Transfert de donn�es d'admission
 */
class CHPrim21ADM {
  var $type_liaison = null;
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);
    
    /* @todo Pas de cr�ation de message pour le moment */
  }
}

?>