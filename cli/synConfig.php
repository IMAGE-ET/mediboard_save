<?php /** $Id:$ **/

/**
 * @category Cli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

// CLI or die
PHP_SAPI === "cli" or die;

require_once "utils.php";

global $argv;

$hostname = "";
$username = "";
$port = "22";
$file = "";
$help = false;
$i = 0;
$command = array_shift($argv);

if (count($argv) >= 3 && count($argv) <= 5) {
  foreach ($argv as $key=>$arg) {
    switch ($arg) {
      case "-h":
        $help = true;
        break;
        
      default:
        switch ($i) {
          case 0:
            $hostname = $arg;
            break;
            
          case 1:
            $username = $arg;
            break;
            
          case 2:
            $file = $arg;
            break;
          
          case 3:
            $port = $arg;
            break;
        }
        
        $i++;
    }
  }
}
else {
  $help = true;
}

echo chr(27)."[1m--- Synchronize files (".date("l d F H:i:s").") ---".chr(27)."[0m"."\n";

if ($help) {
  echo "Usage : ".basename($command)." <hostname> <username> <ssh port> <file>
<hostname> : host to connect
<username> : username requesting
<file>     : file to get or push
<ssh port> : SSH port\n";

  return;
}

// Create the temporary directory
if (!(is_file("/tmp/synConfig"))) {
  if (!(is_dir("/tmp/synConfig"))) {
    if (!(check_errs(
      mkdir("/tmp/synConfig", 0, true),
      false,
      "Unable to create temporay directory.",
      "Temporary directory created!"
    ))) {
      return;
    }
  }
}
else {
  cecho("Error, /tmp/synConfig directory cannot be created.", "red");
  return;
}

$ID = uniqid(basename($file)."_");
exec("scp -P ".$port." -p ".$username."@".$hostname.":".$file." /tmp/synConfig/".$ID, $result, $returnVar);
if (!(check_errs($returnVar, true, "Unable to get the file.", "File received!"))) {
  return;
}

if (!(check_errs(is_readable($file), false, $file." is not readable.", $file." is readable!"))) {
  return;
}

$localFileMTime  = filemtime($file);
$remoteFileMTime = filemtime("/tmp/synConfig/".$ID);

// If files have the same last modification time
if ($remoteFileMTime == $localFileMTime) {
  unlink("/tmp/synConfig/".$ID);
  echo "Files have the same last modification time.\n";
  return;
}

// If remote file is younger, we get
if ($remoteFileMTime > $localFileMTime) {
  echo "Remote file is younger. Older will be replaced.\n";

  if (!(check_errs(
    rename("/tmp/synConfig/".$ID, $file),
    false,
    "Unable to replace the file.",
    "The file has been replaced!"
  ))) {
    return;
  }

  // Set the owner group to APACHE_GROUP
  $APACHE_USER = trim(shell_exec("ps -ef|grep apache|grep -v grep|head -2|tail -1|cut -d' ' -f1"));
  $APACHE_GROUP = trim(shell_exec("groups ".$APACHE_USER."| cut -d' ' -f3"));
  exec("chgrp ".$APACHE_GROUP." ".$file, $result, $returnVar);

  if (!(check_errs(
    $returnVar,
    true,
    "Unable to change owner group of ".$file.".",
    "Owner group of ".$file." set to ".$APACHE_GROUP."!"
  ))) {
    return;
  }

  // Set group permissions to file
  exec("chmod g+w ".$file, $result, $returnVar);

  if (!(check_errs(
    $returnVar,
    true,
    "Unable to change permissions of ".$file.".",
    "Permissions of ".$file." changed!"
  ))) {
    return;
  }
}
// Else, we push
else {
  echo "Remote file is older. It will be replaced.\n";
  unlink("/tmp/synConfig/".$ID);
  exec("scp -P ".$port." -p ".$file." ".$username."@".$hostname.":".$file, $result, $returnVar);

  if (!(check_errs($returnVar, true, "Unable to push the file.", "File sent!"))) {
    return;
  }
}