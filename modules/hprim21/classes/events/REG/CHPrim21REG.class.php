<?php

/**
 * Transfert de donn�es de regl�ment - H'2.1
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REG 
 * Transfert de donn�es de regl�ment
 */
class CHPrim21REG extends CHPREvent {
  function __construct() {
    $this->type = "REG";
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);
    
    /* @todo Pas de cr�ation de message pour le moment */
  }
}

