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
 * Class CHPrimSanteADM
 * Transfert de donn�es d'admission
 */
class CHPrimSanteADM extends CHPrimSanteEvent {
  /**
   * construct
   */
  function __construct() {
    $this->type = "ADM";
  }

  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);

    /* @todo Pas de cr�ation de message pour le moment */
  }
}

