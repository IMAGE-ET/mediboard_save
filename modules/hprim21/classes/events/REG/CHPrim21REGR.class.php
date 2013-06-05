<?php

/**
 * Transfert de donn�es de regl�ment - Liaisons entre cabinets de radiologie et �tablissements cliniques ou hospitaliers
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21REGR
 * Transfert de donn�es de regl�ment - Liaisons entre cabinets de radiologie et �tablissements cliniques ou hospitaliers
 */
class CHPrim21REGR extends CHPrim21REG {
  function __construct() {
    $this->type_liaison = "R";
    
    parent::__construct();
  }
}

