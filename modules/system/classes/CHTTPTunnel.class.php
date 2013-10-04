<?php
/**
 * $Id: CHTTPTunnel.class.php 17637 2013-01-03 10:13:19Z phenxdesign $
 *
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 17637 $
 */

/**
 * An HTTP tunnel
 */
class CHTTPTunnel {
  /** @var Resource */
  public $listen_socket;
  /** @var Resource */
  public $target_socket;
  /** @var Resource */
  public $context;
  public $target_host;
  public $timer;
  public $log = array("start_date" => "", "timer" => 0, "memory" => 0,
                      "memory_peak" => 0, "hits" => 0, "data_sent" => 0,
                      "data_received" => 0, "clients" => array());
  public $running = true;
  public $restart = false;
  public $header_continue = false;

  const DATA_LENGTH = 1500;

  /**
   * Construct
   *
   * @param String $target     Target address
   * @param string $listen     Listen address
   * @param String $path_cert  Local certificate path
   * @param String $passphrase Local certificate passphrase
   * @param String $ca         Authority certificate
   */
  function __construct($target, $listen = 'tcp://0.0.0.0:8080', $path_cert = null, $passphrase = null, $ca = null) {
    echo "------------ Construct HTTP Proxy ------------\n";

    //Initialize a socket server for listen the client request
    $context = $this->createContext($path_cert, $passphrase, $ca, true);
    $socket = stream_socket_server($listen, $errno, $errstr, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $context);
    if (!$socket) {
      echo "$errstr ($errno) \n";
      exit(-1);
    }

    stream_set_blocking($socket, false);

    $this->listen_socket = $socket;
    $this->target_host   = $target;
    $this->log["start_date"] = date("d-m-Y H:i:s");
  }

  /**
   * Open a connection to the target address
   *
   * @return void
   */
  function openTarget(){
    $old_timer = $this->timer;
    if ($old_timer && ((time() - $old_timer) < 3600)) {
      return;
    }
    echo "-------------- Create the Target -------------\n";

    //Add the context of the connection
    $context = null;
    if ($this->context) {
      $context = $this->context;
    }

    if ($this->target_socket) {
      stream_socket_shutdown($this->target_socket, STREAM_SHUT_RDWR);
    }

    //Create the client for request the target address
    $target = stream_socket_client($this->target_host, $errno, $errstr, 3600, STREAM_CLIENT_CONNECT, $context);
    if (!$target) {
      echo "$errstr ($errno) \n";
      exit(-1);
    }

    //Active the blocking
    stream_set_blocking($target, true);
    //Active the crypto SSL/TLS for the connection
    stream_socket_enable_crypto($target, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);

    $this->target_socket = $target;
    $this->timer = time();
  }

  /**
   * Run the proxy
   *
   * @return void
   */
  function run(){
    echo "----------- Running the HTTP Proxy -----------\n\n";
    $client_socks = array();
    $master_sock = $this->listen_socket;

    while ($this->running) {
      //we place the client for read the socket
      $read_socks = $client_socks;
      //we place our server for read the socket
      $read_socks[] = $master_sock;
      //We check the evolution of master socket
      if (!@stream_select($read_socks, $write, $except, null) && $this->running) {
        echo "$except \n";
        exit(-1);
      }

      if (in_array($master_sock, $read_socks)) {
        //We continue the program when a client to connect
        $new_client = stream_socket_accept($master_sock, 15, $peer_name);
        if ($new_client) {
          echo '** Connection accepted from ' . $peer_name . " **\n";
          //We place the new client to the list
          $client_socks[] = $new_client;
        }
        //We delete the master sock for the suite
        unset($read_socks[array_search($master_sock, $read_socks)]);
      }

      foreach ($read_socks as $sock) {
        echo "----------- Receive request client -----------\n";
        //Active the crypto SSL/TLS for the connection for a new connection
        if (!$this->header_continue) {
          stream_set_blocking($sock, true);
          stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_SSLv23_SERVER);
        }

        $request = $this->read($sock);
        $peer_name = $this->getAddresse(stream_socket_get_name($sock, true));

        $t = microtime(true);
        if (strpos($request, "CMD") !== false) {
          echo "--------------- Command system ---------------\n";
          $result = $this->executeCommand($request);
          $response = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n$result";
        }
        else {
          $this->openTarget();
          $response = $this->serve($request);
          $this->setLogClient($peer_name, "hits", 1);
          $this->setLogClient($peer_name, "data_received", strlen($response));
          $this->setLogClient($peer_name, "data_sent", strlen($request));
        }

        echo "** Request done in ".((microtime(true) - $t) * 1000)." ms ! **\n";

        //return the response to the client
        echo "---------- Send response for client ----------\n";
        fwrite($sock, $response);
        $this->header_continue = true;
        if (strpos($response, "100 Continue") === false) {
          $this->header_continue = false;
          //We delete the client and close her connection
          unset($client_socks[array_search($sock, $client_socks)]);
          stream_socket_shutdown($sock, STREAM_SHUT_RDWR);
          echo '** Connection closed from ' . $peer_name . " **\n\n";
        }
      }
    }
    if ($this->restart) {
      $this->restartScript();
    }
    $this->quit();
  }

  /**
   * Send the request and return the response of the target
   *
   * @param String $data Request to send
   *
   * @return string
   */
  function serve($data) {
    $this->timer = time();
    echo "------------ Send request client ------------\n";
    fwrite($this->target_socket, $data);
    echo "--------- Receive response for client --------\n";
    $response = $this->read($this->target_socket);
    return $response;
  }

  /**
   * Read the data on a connection
   *
   * @param Resource $target Connection to read
   *
   * @return string
   */
  function read($target) {
    $content = "";
    $length = self::DATA_LENGTH;
    while ($data = fread($target, $length)) {
      $content .= $data;
      $meta = stream_get_meta_data($target);
      $length = min(self::DATA_LENGTH, $meta["unread_bytes"]);

      if ($meta["unread_bytes"] === 0) {
        stream_set_blocking($target, false);
        if ($test = fread($target, 1)) {
          $content .= $test;
          $meta = stream_get_meta_data($target);
          $length = min(self::DATA_LENGTH, $meta["unread_bytes"]-1);
        }
        else {
          stream_set_blocking($target, true);
          break;
        }
        stream_set_blocking($target, true);
      }
    }
    return $content;
  }

  /**
   * Set the authentification by certificate
   *
   * @param String $path_cert  Path certificate
   * @param String $passphrase Passphrase of the certificate
   * @param String $path_ca    Path ca certificate
   *
   * @return void
   */
  function setAuthentificationCertificate($path_cert, $passphrase, $path_ca) {
    echo "--------- Configurate the HTTP Proxy ---------\n";
    $this->context = $this->createContext($path_cert, $passphrase, $path_ca);
  }

  /**
   * Create a SSL context
   *
   * @param String $path_cert   Local certificate path
   * @param String $passphrase  Local certificate passphrase
   * @param String $path_ca     Authority certificate path
   * @param Bool   $self_signed Allow the local certificate self signed
   *
   * @return resource
   */
  private function createContext($path_cert = null, $passphrase = null, $path_ca = null, $self_signed = false) {
    $context = stream_context_create();

    if ($path_ca) {
      stream_context_set_option($context, "ssl", "cafile"     , $path_ca);
      stream_context_set_option($context, "ssl", "verify_peer", true);
    }

    if ($self_signed) {
      stream_context_set_option($context, 'ssl', 'allow_self_signed', $self_signed);
      stream_context_set_option($context, "ssl", "verify_peer", false);
    }

    if ($path_cert) {
      stream_context_set_option($context, "ssl", "local_cert" , $path_cert);
      stream_context_set_option($context, "ssl", "passphrase" , $passphrase);
    }

    return $context;
  }

  /**
   * Add the value into the general information
   *
   * @param String $type  Type log
   * @param String $value value log
   *
   * @return void
   */
  function setLog($type, $value) {
    $this->log[$type] += $value;
  }

  /**
   * Add the value into the client information
   *
   * @param String $entity Client
   * @param String $type   Type log
   * @param String $value  Value log
   *
   * @return void
   */
  function setLogClient($entity, $type, $value) {
    $this->setLog($type, $value);
    if (!array_key_exists($entity, $this->log["clients"])) {
      $this->log["clients"][$entity] = array();
    }
    if (array_key_exists($type, $this->log["clients"][$entity])) {
      $this->log["clients"][$entity][$type] += $value;
    }
    else {
      $this->log["clients"][$entity][$type] = $value;
    }
  }

  /**
   * Return the address without the port
   *
   * @param String $addresse_port address
   *
   * @return String
   */
  function getAddresse($addresse_port) {
    $addresse = explode(":", $addresse_port);
    return $addresse[0];
  }

  /**
   * Execute the command system for the tunnel
   *
   * @param String $request command system to execute
   *
   * @return string
   */
  function executeCommand($request) {
    $command = explode(" ", $request);
    switch ($command[1]) {
      case "RESTART":
        $this->running   = false;
        $this->restart   = true;
        break;
      case "STOP":
          $this->running = false;
        break;
      case "STAT":
        $this->log["timer"] = $this->timer ? time() - $this->timer : "NI";
        $this->log["memory"] = memory_get_usage(true);
        $this->log["memory_peak"] = memory_get_peak_usage(true);
        return json_encode($this->log);
        break;
    }
    return "";
  }

  /**
   * Function to execute on shutdown
   *
   * @return void
   */
  function onShutdown() {
    global $pid_file;
    unlink($pid_file);
  }

  /**
   * SIG number manager
   *
   * @param integer $signo The signal number to handle
   *
   * @return void
   */
  function sigHandler($signo) {
    switch ($signo) {
      case SIGTERM:
      case SIGINT:
        $this->quit();
        break;
      case SIGHUP:
        $this->quit("restart");
        break;
    }
  }

  /**
   * Restarts the current server
   * Only works on Linux (not MacOS and Windows)
   *
   * @return void
   */
  function restartScript(){
    if (!function_exists("pcntl_exec")) {
      return;
    }
    $this->quit("restart");
    echo "----------- Restart the HTTP Proxy -----------\n";
    pcntl_exec($_SERVER["_"], $_SERVER["argv"]);
  }

  /**
   * Exit the script, with a status
   *
   * @param string $status Exit status : "ok" or "restart"
   *
   * @return void
   */
  function quit($status = "ok"){
    echo "------------- Stop the HTTP Proxy ------------\n";

    $this->running = false;
    if ($this->target_socket) {
      stream_socket_shutdown($this->target_socket, STREAM_SHUT_RDWR);
    }
    stream_socket_shutdown($this->listen_socket, STREAM_SHUT_RDWR);

    if ($status !== "restart") {
      exit(0);
    }
  }
}