<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

 
 // Pr�f�rences par Module
CPreferences::$modules["common"] = array (
  "LOCALE",
  "UISTYLE",
  "MenuPosition",
  "DEFMODULE",
  "touchscreen",
  "tooltipAppearenceTimeout",
  "showLastUpdate",
  "directory_to_watch",
  "debug_yoplet"
);  
  
CPreferences::$modules["system"] = array (
  "INFOSYSTEM",
  "showTemplateSpans"
);
?>