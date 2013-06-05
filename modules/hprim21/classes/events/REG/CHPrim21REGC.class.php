<?php

/**
 * Transfert de donn�es de regl�ment - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REGC
 * Transfert de donn�es de regl�ment - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
 */
class CHPrim21REGC extends CHPrim21REG {
  function __construct() {
    $this->type_liaison = "C";
    
    parent::__construct();
  }
}

