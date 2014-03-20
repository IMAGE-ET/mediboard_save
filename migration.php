<?php 
/**
 * Mediboard URL migration page
 * 
 * @package Mediboard
 * @author  SARL OpenXtrem <dev@openxtrem.com>
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version SVN: $Id: $
 */

require __DIR__."/includes/mb_functions.php";
require __DIR__."/includes/config_all.php";

$config = $dPconfig["migration"];

$ip = get_remote_address();
$url = is_intranet_ip($ip["remote"]) ? $config["intranet_url"] : $config["extranet_url"];
$limit_date = strtotime($config["limit_date"]);

setlocale(LC_TIME, "fr_FR", "fr_FR@euro", "fr_FR.utf8", "fra");

header("Content-type: text/html; charset=iso-8859-1");

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="iso-8859-1" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Sant�" />

  <title>Mediboard a chang� d'adresse</title>

  <link rel="shortcut icon" type="image/ico" href="style/mediboard/images/icons/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="style/mediboard/main.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/e-cap/main.css" media="all" />

  <script type="text/javascript">
    function addBookmark(title, url) {
      if (window.sidebar) { // Firefox
        window.sidebar.addPanel(title, url, "");
      }
      else if (document.all) { // IE
        window.external.AddFavorite(url, title);
      }
      else {
        alert("Ajoutez la nouvelle adresse dans vos favoris ou comme page de d�marrage");
      }
    }
  </script>

  <style type="text/css">
    body {
      padding-top: 3em;
      text-align: center;
      font-size: 1.2em;
      background: #fff;
    }

    p.new-address {
      background: #eee;
      width: 600px;
      margin: auto;
      padding: 0.5em;
      font-size: 1.3em;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <h1>Mediboard a chang� d'adresse</h1>

  <img src="images/pictures/logo.png" width="350" alt="Mediboard" />

  <p>
    La nouvelle adresse de Mediboard est maintenant :
  </p>

  <p class="new-address">
    <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
  </p>

  <p>
    <strong>
      L'ancienne adresse n'existera plus � partir du
      <?php echo strftime("%A %d %B %Y", $limit_date); ?>.
    </strong>
    <br />Nous sommes le <?php echo strftime("%A %d %B %Y"); ?>
  </p>

  <button class="add" onclick="addBookmark('Mediboard', '<?php echo $url; ?>')">
    Ajouter � mes favoris
  </button>
  <button class="right" onclick="location.href='<?php echo $url; ?>'">
    Acc�der � la nouvelle adresse
  </button>
</body>

</html>