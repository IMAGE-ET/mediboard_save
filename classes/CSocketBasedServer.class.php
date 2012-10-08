<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license http://www.gnu.org/licenses/ GNU GPLv3
 */

$dir = dirname(__FILE__);

$socket_server_class = "$dir/SocketServer.class.php";

// Library not installed
if (!file_exists($socket_server_class)) {
  return;
}

if (!class_exists("SocketServer", false)) {
  require $socket_server_class;
}

require "$dir/../includes/version.php"; 
 
class CSocketBasedServer {
  
  /**
   * Root URL called when receiving data on the $port
   * 
   * @var string 
   */
  protected $call_url = null;
  
  /**
   * The controller who will receive the messages
   * 
   * @var string
   */
  protected $controller = null;
  
  /**
   * The module 
   * 
   * @var string
   */
  protected $module = null;
  
  /**
   * Username used to connect to the Mediboard instance pointed by $call_url
   * 
   * @var string 
   */
  protected $username = null;
  
  /**
   * Password associated to $username
   * 
   * @var string
   */
  protected $password = null;
  
  /**
   * Port to listen on
   * 
   * @var int
   */
  protected $port = null;
  
  /**
   * The SSL certificate path (PEM format)
   * 
   * @var string
   */
  protected $certificate = null;
  
  /**
   * The SSL passphrase
   * 
   *  @var string
   */
  protected $passphrase = null;
  
  /**
   * The SocketServer instance
   * 
   * @var SocketServer
   */
  protected $server = null;
  
  /**
   * Request count
   * 
   * @var integer
   */
  protected $request_count = 0;
  
  /**
   * The start date time
   * 
   * @var string
   */
  protected $started_datetime = null;
  
  /**
   * The SocketClient instance
   * 
   * @var SocketClient 
   */
  static $client = null;
  
  /**
   * The clients
   * 
   * @var array
   */
  protected $clients = array();
  
  /**
   * The socket based server constructor
   * 
   * @param string  $call_url    The Mediboard root URL to call
   * @param string  $username    The Mediboard user name
   * @param string  $password    The Mediboard user password
   * @param integer $port        The port number to listen on
   * @param string  $certificate The path to the SSL/TLS certificate
   * @param string  $passphrase  The SSL/TLS certificate passphrase
   * 
   * @return void
   */
  function __construct($call_url, $username, $password, $port, $certificate = null, $passphrase = null){
    $this->call_url    = $call_url;
    $this->username    = $username;
    $this->password    = $password;
    $this->port        = $port;
    $this->certificate = $certificate;
    $this->passphrase  = $passphrase;
    
    $this->server = new SocketServer(AF_INET, SOCK_STREAM, SOL_TCP);
  }
  
  /**
   * Return the call url
   * 
   * @return string
   */
  function getCallUrl() {
    return $this->call_url;
  }
  
  /**
   * Set the call url
   * 
   * @param string $url The url
   * 
   * @return null
   */
  function setCallUrl($url) {
    $this->call_url = $url;
  }
  
  /**
   * Set the username
   * 
   * @param string $username The userame
   * 
   * @return null
   */
  function setUsername($username) {
    $this->username = $username;
  }
  
  /**
   * Set the password
   * 
   * @param string $password The password
   * 
   * @return null
   */
  function setPassword($password) {
    $this->password = $password;
  }
  
  /**
   * Return the port
   * 
   * @return integer
   */
  function getPort() {
    return $this->port;
  }
  
  /**
   * Set the port
   * 
   * @param integer $url The port
   * 
   * @return null
   */
  function setPort($port) {
    $this->port = $port;
  }
  
  /**
   * Set the certificate
   * 
   * @param string $certificate The certificate
   * 
   * @return null
   */
  function setCertificate($certificate) {
    $this->certificate = $certificate;
  }
  
  /**
   * Set the passphrase
   * 
   * @param string $passphrase The passphrase
   * 
   * @return null
   */
  function setPassphrase($passphrase) {
    $this->passphrase = $passphrase;
  }
  
  /**
   * Return the request count
   * 
   * @return integer
   */
  function getRequestCount() {
    return $this->request_count;
  }
  
  /**
   * Return the startedDateTime
   * 
   * @return integer
   */
  function getStartedDateTime() {
    return $this->started_datetime;
  }
  
  /**
   * Return the client
   * 
   * @return SocketClient
   */
  function getClient() {
    return self::$client;
  }
  
  /**
   * Return the server
   * 
   * @return SocketServer
   */
  function getServer() {
    return $this->server;
  }
  
  /**
   * Return the server type
   * 
   * @return string
   */
  function getServerType() {
    $class_name = get_class($this);
    $length = strpos($class_name, "Server") - 1;
    return substr($class_name, 1, $length);
  }
  
  /**
   * Handle request callback
   * 
   * @param string  $request The request to handle
   * @param integer $id      The client's ID
   * 
   * @return string A hash of the handled request
   */
  function handle($request, $id) {
    $client = $this->clients[$id];
    $buffer = &$this->clients[$id]["buffer"];
    
    // Commands
    switch($request) {
      case "__STOP__":
        $buffer = "";
        return false;
      
      case "__RESTART__":
        if (function_exists("quit")) {
          quit("restart");
        }
      
      case "__STATS__":
        return json_encode($this->getStats());
    }
    
    // Verification qu'on ne recoit pas un en-tete de message en ayant deja des données en buffer
    if ($buffer && $this->isHeader($request)) {
      echo sprintf(" !!! Got a header, while having data in the buffer from %d\n", $id);
    }
    
    echo sprintf(" > Got %d bytes from %d\n", strlen($request), $id);
    
    // Si on recoit le flag de fin de message, on effectue la requete web
    if ($this->isMessageFull($request)) {
      $buffer .= substr($request, 0, -1);
      
      echo sprintf(" > Got a full message from %d\n", $id);
      
      $post = array(
        "m"       => $this->module,
        "dosql"   => $this->controller,
        "port"    => $this->port,
        "message" => $buffer,
        "client_addr" => $client["addr"],
        "client_port" => $client["port"],
        "suppressHeaders" => 1,
        //"login" => "$this->username:$this->password",
      );
      
      $start = microtime(true);
      
      $url = $this->call_url."/index.php?login=$this->username:$this->password";
      $ack = $this->requestHttpPost($url, $post);
      
      $this->request_count++;
      $time = microtime(true) - $start;
      echo sprintf(" > Request done in %f s\n", $time);
      
      $buffer = "";
      return $this->formatAck($ack);
    }
    else {
      // Mise en buffer du message
      $buffer .= "$request\n";
    }
    
    return "";
  }

  /**
   * Check if the message is complete
   * 
   * @param string $message The message
   * 
   * @return boolean
   */
  function isMessageFull($message) {
    
  }
  
  /**
   * Format the acknowledgement
   * 
   * @param string  $ack     The acknowledgement
   * 
   * @param integer $conn_id The connection id
   * 
   * @return string
   */
  function formatAck($ack, $conn_id = null) {
    return $ack;
  }
  
  /**
   * Check if the request is a header message
   * 
   * @param string $request The request
   * 
   * @return boolean
   */
  function isHeader($request) {
    return false;
  }
  
  /**
   * The open connection callback
   * 
   * @param integer $id   The client's ID
   * @param string  $addr The client's IP address
   * @param integer $port The client's port
   * 
   * @return boolean true
   */
  function onOpen($id, $addr, $port = null) {
    if (!isset($this->clients[$id])) {
      $this->clients[$id] = array(
        "buffer" => "",
        "addr"   => $addr,
        "port"   => $port,
      );
    }
    
    echo sprintf(" > New connection [%d] arrived from %s:%d\n", $id, $addr, $port);
    return true;
  }
  
  /**
   * Connection cleanup callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function onCleanup($id) {
    unset($this->clients[$id]);
    echo sprintf(" > Connection [%d] cleaned-up\n", $id);
  }
  
  /**
   * Connection close callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function onClose($id) {
    echo sprintf(" > Connection [%d] closed\n", $id);
  }
  
  /**
   * Write error callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function writeError($id) {
    echo sprintf(" !!! Write error to [%d]\n", $id);
  }
  
  /**
   * Get the server's stats
   * 
   * @return array An array of various stats
   */
  function getStats(){
    return array(
      "request_count" => $this->request_count,
      "started"       => $this->started_datetime,
      "memory"        => memory_get_usage(true),
      "memory_peak"   => memory_get_peak_usage(true),
    );
  }
  
  /**
   * Execute an HTTP POST request
   * 
   * @param string $url  The URL to call
   * @param array  $data The data to pass to $url via POST
   * 
   * @return string HTTP Response
   */
  function requestHttpPost($url, $data) {
    $data_url = http_build_query($data, null, "&");
    $data_len = strlen($data_url);
    
    $scheme = substr($url, 0, strpos($url, ":"));
    $options = array(
      $scheme => array(
        "method" => "POST",
        "header" => array (
          "Content-Type: application/x-www-form-urlencoded",
          "Content-Length: $data_len", 
        ),
        "content" => $data_url
      )
    );
    
    $ctx = stream_context_create($options);
    
    return file_get_contents($url, false, $ctx);
  }
  
  /**
   * Run the server
   * 
   * @return void
   */
  function run(){
    global $version;
    
    $time = strftime("%Y-%m-%d %H:%M:%S");
    $v    = $version['string'];
    $motd = <<<EOT
-------------------------------------------------------
|   Welcome to the Mediboard {$this->getServerType()} Server v.$v   |
|   Started at $time                    |
-------------------------------------------------------

EOT;
    $this->started_datetime = $time;

    $server = $this->server->bind("0.0.0.0", $this->port, $this->certificate, $this->passphrase);
    
    $server->setRequestHandler(array($this, "handle"));
    $server->setOnOpenHandler(array($this, "onOpen"));
    $server->setOnCleanupHandler(array($this, "onCleanup"));
    $server->setOnCloseHandler(array($this, "onClose"));
    $server->setOnWriteErrorHandler(array($this, "writeError"));
    $server->run();
  }
  
  /**
   * Send a request
   * 
   * @param string  $host    The client's IP to send the request to
   * @param integer $port    The client's port number
   * @param string  $message The message to send
   * 
   * @return string The client's response
   */
  static function send($host, $port, $message) {
    $root_dir = dirname(__FILE__);
    
    require_once "$root_dir/SocketClient.class.php";
    
    try {
      if (!self::$client) {
        self::$client = new SocketClient();
        self::$client->connect($host, $port);
      }
      
      return self::$client->sendAndReceive($message);
    }
    catch(Exception $e) {
      throw $e;
    }
  }
  
  /**
   * Get a list of the current servers processes
   * 
   * @return array A list of structures containing the processes information
   */
  static function getPsStatus(){
    $tmp_dir = self::getTmpDir();
    
    $pid_files = glob("$tmp_dir/pid.*");
    $processes = array();
    
    foreach ($pid_files as $_file) {
      $_pid = substr($_file, strrpos($_file, ".")+1);
      $launched = strftime("%Y-%m-%d %H:%M:%S", filemtime($_file));
      $content = file($_file);
      $processes[$_pid] = array(
        "port"     => trim($content[0]),
        "launched" => $launched,
        "launched_rel" => null,//CMbDate::relative($launched)
        "type" => trim($content[1]),
      );
    }
    
    if (PHP_OS == "WINNT") {
      exec("tasklist", $out);
      $out = array_slice($out, 2); 
      
      foreach ($out as $_line) {
        $_pid = (int)substr($_line, 26, 8);
        if (!isset($processes[$_pid])) {
          continue;
        }
        
        $_ps_name = trim(substr($_line, 0, 25));
        $processes[$_pid]["ps_name"] = $_ps_name;
      }
    }
    else {
      exec("ps -e", $out);
      $out = array_slice($out, 1); 
      
      foreach ($out as $_line) {
        $_pid = (int)substr($_line, 0, 5);
        if (!isset($processes[$_pid])) {
          continue;
        }
      
        $_ps_name = trim(substr($_line, 24));
        $processes[$_pid]["ps_name"] = $_ps_name;
      }
    }
    
    return $processes;
  }
  
  /**
   * Returns the temp directory
   * 
   * @return string The temp directory path
   */
  static function getTmpDir() {
    $root_dir = dirname(__FILE__);
    
    require_once "$root_dir/CMbPath.class.php";
    
    $tmp_dir = "$root_dir/../tmp/socket_server";
    CMbPath::forceDir($tmp_dir);
    
    return $tmp_dir;
  }
  
  /**
   * Return the list of sample messages defined in this class
   * 
   * @return array The list of available test messages
   */
  static function getList(){
    $reflection = new ReflectionClass(get_class($this));
    $list = $reflection->getMethods(ReflectionMethod::IS_FINAL);
    
    $types = array();
    foreach ($list as $_method) {
      $types[] = substr($_method->name, 6);
    }
    
    return $types;
  }
}
?>