<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimSanteREGR
 * Transfert de donn�es de regl�ment - Liaisons entre cabinets de radiologie et �tablissements cliniques ou hospitaliers
 */
class CHPrimSanteREGR extends CHPrimSanteREG {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->type_liaison = "R";

    parent::__construct();
  }
}

