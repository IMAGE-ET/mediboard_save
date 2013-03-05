<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * The quantity data type is an abstract generalization
 * for all data types (1) whose value set has an order
 * relation (less-or-equal) and (2) where difference is
 * defined in all of the data type's totally ordered value
 * subsets.  The quantity type abstraction is needed in
 * defining certain other types, such as the interval and
 * the probability distribution.
 */
class CCDAQTY extends CCDAANY {

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    
    return $props;
  }
}
