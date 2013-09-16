<?php

/**
 * Transfert de donn�es de regl�ment - Liaisons entre laboratoires
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REGL
 * Transfert de donn�es de regl�ment - Liaisons entre laboratoires
 */
class CHPrim21ORUL extends CHPrim21ORU {
  function __construct() {
    $this->type_liaison = "L";
    
    parent::__construct();
  }
}

