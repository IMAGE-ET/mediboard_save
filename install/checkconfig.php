<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("header.php");

if (!@include_once("$mbpath/includes/config.php")) { 
  showHeader();
?>

<div class="big-error">
  Erreur : Le fichier de configuration n'a pas �t� valid�, merci de revenir � l'�tape 
  pr�c�dante.
</div>

<?php
  showFooter();
  die();
}
?>