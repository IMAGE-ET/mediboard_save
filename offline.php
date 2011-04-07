<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require("includes/config_all.php");

switch($_GET["reason"]) {
  case "bdd" :
    $msg = "La base de donn�es n'est pas accessible.";
    break;
  default :
		if ($dPconfig["offline"] != 1) {
		  header("Location: index.php");
		}

  	$msg = "Le syst�me est d�sactiv� pour cause de maintenance.";
}

header("Content-type: text/html; charset=iso-8859-1");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
  <title>Mediboard SIH &mdash; Service inaccessible</title>
  <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Sant�" />
  
  <link rel="shortcut icon" type="image/ico" href="style/mediboard/images/icons/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="style/mediboard/main.css" media="all" />
</head>

<body style="padding-top: 3em; text-align: center;">
  <h1>MEDIBOARD EST INACCESSIBLE POUR LE MOMENT</h1>
  <img src="images/pictures/logo.png" width="350" />
  <h2>
    <?php echo $msg; ?>
    <br />
    Merci de r�essayer ult�rieurement.
  </h2>
  <br />
  <a class="button change" href="index.php">Acc�der � Mediboard</a>
</body>

</html>