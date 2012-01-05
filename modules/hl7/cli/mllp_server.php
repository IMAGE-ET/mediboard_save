<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// For sig_handler
declare(ticks = 1);

require dirname(__FILE__)."/mllp_utils.php";

// Ignores user logout
ignore_user_abort(true);
set_time_limit(0);

global $exit_status, $pid_file, $handler;
$exit_status = "error";

// restart the server
function restart(){
  if (!function_exists("pcntl_exec")) return;
  
  global $handler;

  socket_close($handler->server->__socket);
  
  pcntl_exec($_SERVER["_"], $_SERVER["argv"]);
}

function on_shutdown() {
  global $exit_status, $pid_file, $handler;
  
  switch($exit_status) {
    case "error":
      outln("Server stopped unexpectedly, trying to restart.");
      restart();
      break;
    
    case "restart":
      outln("Restarting ...");
      @unlink($pid_file); 
      outln("Server stopped.");
      restart();
      break;
    
    default:
      outln("Server stopped.");
      @unlink($pid_file);
      break;
  }
}

function quit($new_exit_status = "ok"){
  global $exit_status;
  $exit_status = $new_exit_status;
  exit ($exit_status == "error" ? 1 : 0);
}

if (function_exists("pcntl_signal")) {
  // SIG number manager
  function sig_handler($signo) {
    switch ($signo) {
      case SIGTERM:
      case SIGINT:
        quit();
        break;
        
      case SIGHUP:
        quit("restart");
        break;
    }
  }
  
  pcntl_signal(SIGTERM, "sig_handler");
  pcntl_signal(SIGINT , "sig_handler"); // Sent when hitting ctrl+c in the cli
  pcntl_signal(SIGHUP , "sig_handler"); // Restart
}

// ---- Read arguments
$argv = $_SERVER["argv"];
$argc = $_SERVER["argc"];

if (count($argv) < 4) {
  echo <<<EOT
Usage: {$argv[0]} <root_url> <username> <password> [--port port]
  <root_url>      The root url for mediboard, ie https://localhost/mediboard
  <username>      The name of the user requesting, ie cron
  <password>      The password of the user requesting, ie ****
  [--port <port>] The port to listen on

EOT;
  exit(0);
}

$options = array(
  "url"      => $argv[1],
  "username" => $argv[2],
  "password" => $argv[3],
  "debug"    => false,
  "port"     => 7001,
);

for($i = 3; $i < $argc; $i++) {
  switch($argv[$i]){
    case "--debug":
      $options["debug"] = true;
    break;
    
    case "--port":
      $options["port"] = $argv[++$i];
    break;
  }
}
// ---- End read arguments

register_shutdown_function("on_shutdown");

// Write a flag file with the PID and the port
$pid_file = "$tmp_dir/pid.".getmypid();
file_put_contents($pid_file, $options["port"]);

try {
  outln("Starting MLLP Server on port ".$options["port"]." with user '".$options["username"]."'");
  
  $handler = new CMLLPSocketHandler($options["url"], $options["username"], $options["password"], $options["port"]);
  $handler->run();
  
  quit();
}
catch(Exception $e) {
  $message = $e->getMessage();
  
  if ($message == "Address already in use") {
    outln($message);
    quit();
  }
  
  $stderr = fopen("php://stderr", "w");
  fwrite($stderr, $message.PHP_EOL);
}

quit();
