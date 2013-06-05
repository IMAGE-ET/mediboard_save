<?php

/**
 * Transfert de donn�es d'admission - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrim21ADMC
 * Transfert de donn�es d'admission - Liaisons entre laboratoires et �tablissements cliniques ou hospitaliers
 */
class CHPrim21ADMC extends CHPrim21ADM {
  function __construct() {
    $this->type_liaison = "C";
    
    parent::__construct();
  }
}

