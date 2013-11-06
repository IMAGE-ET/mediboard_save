<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * The actual application class
 * Responsibilities:
 *  - application kill
 *  - class management
 *  - file inclusion
 *  - memory and performance
 */
class CApp {
  static $inPeace    = false;
  static $encoding   = "utf-8";
  static $classPaths = array();
  static $is_robot   = false;

  /** @var string Current request unique identifier */
  private static $requestUID = null;
  
  /* 
   * The order of the keys is important (only the first keys 
   * are displayed in the short view of the Firebug console).
   */
  static $performance = array(
    // Performance
    "genere"  => null,
    "memoire" => null,
    "size"    => null,
    "objets"  => 0,
    "ip"      => null,
    
    // Errors
    "error"   => 0,
    "warning" => 0,
    "notice"  => 0,
    
    // Cache
    "cachableCount"  => null,
    "cachableCounts" => null,
    
    // Objects
    "objectCounts" => null,
    
    // Function cache
    "functionCache" => null,
  );

  /**
   * @var Chronometer Main application chronometer
   */
  static $chrono;
    
  static $handlers;
  
  /**
   * Will trigger an error for logging purpose whenever the application dies unexpectedly
   * 
   * @return void
   */
  static function checkPeace() {
    if (!self::$inPeace) {
      if (!headers_sent()) {
        header("HTTP/1.1 500 Application died unexpectedly");
      }
      CModelObject::error("Application-died-unexpectedly");
    }
  }
  
  /**
   * Make application die properly
   * 
   * @return void
   */
  static function rip() {
    // If the client doesn't support cookies, we destroy its session
    // Sometimes, the cookie is empty while the client support cookies (url auth in IE)
    /*if (empty($_COOKIE)) {
      CSessionHandler::end();
    }*/

    //access log
    include "./includes/access_log.php";
    // Long request log
    include "./includes/long_request_log.php";

    // Explicit close of the session before object destruction
    CSessionHandler::writeClose();

    self::$inPeace = true;
    die;
  }
  
  /**
   * Apply a ratio multiplicator to current memory limit
   * 
   * @param float $ratio Ratio to apply
   * 
   * @return int Previous memory limit
   */
  static function memoryRatio($ratio) {
    $limit = CMbString::fromDecaSI(ini_get("memory_limit"), "") * $ratio;
    $limit = CMbString::toDecaSI($limit, "");
    return ini_set("memory_limit", $limit);
  }

  /**
   * Set time limit in seconds
   *
   * @param integer $seconds The time limit in seconds
   *
   * @return string
   */
  static function setTimeLimit($seconds) {
    return self::setMaxPhpConfig("max_execution_time", $seconds);
  }

  /**
   * Set memory limit in megabytes
   *
   * @param string $megabytes The memory limit, suffixed with K, M, G
   *
   * @return string
   */
  static function setMemoryLimit($megabytes) {
    return self::setMaxPhpConfig("memory_limit", $megabytes);
  }

  /**
   * set a php configuration limit with a minimal value
   * if the value is < actual, the old value is used
   *
   * @param string     $config the php parameter
   * @param string|int $limit  the limit required
   *
   * @return string
   */
  static function setMaxPhpConfig($config, $limit) {
    $actual = CMbString::fromDecaBinary(ini_get($config));
    $new    = CMbString::fromDecaBinary($limit);

    //new value is superior => change the config
    if ($new > $actual) {
      return ini_set($config, $limit);
    }

    return ini_get($config);
  }


  /**
   * Redirect to empty the POST data, 
   * so that it is not posted back when refreshing the page.
   * Use it instead of CApp::rip() directly
   * 
   * @param bool $redirect Try to redirect if true
   * 
   * @return void
   */
  static function emptyPostData($redirect = true){
    if ($redirect && !empty($_POST) && !headers_sent()) {
      CAppUI::redirect(/*CValue::read($_SERVER, "QUERY_STRING")*/);
    }
    self::rip();
  }
  
  /**
   * Outputs JSON data after removing the Output Buffer, with a custom mime type
   * 
   * @param object|array $data     The data to output
   * @param string       $mimeType [optional] The mime type of the data, application/json by default
   * 
   * @return void
   */
  static function json($data, $mimeType = "application/json") {
    ob_clean();
    header("Content-Type: $mimeType");
    echo json_encode($data);
    self::rip();
  }

  /**
   * Fetch an HTML content of a module view, as a HTTP GET call would do
   * Very useful to assemble multiple views
   *
   * @param string $module    The module name or the file path
   * @param string $file      [optional] The file of the module, or null
   * @param array  $arguments [optional] The GET arguments
   *
   * @return string The fetched content
   */
  static function fetch($module, $file = null, $arguments = array()) {
    $save = array();
    foreach ($arguments as $_key => $_value) {
      if (!isset($_GET[$_key])) {
        continue;
      }
      
      $save[$_key] = $_GET[$_key];
    }
    
    foreach ($arguments as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    ob_start();
    if (isset($file)) {
      include "./modules/$module/$file.php";
    }
    else {
      include $module;
    }
    $output = ob_get_clean();
   
    foreach ($save as $_key => $_value) {
      $_GET[$_key] = $_value;
    }
    
    return $output;
  }
  
  /**
   * Get the base application URL
   * 
   * @return string The URL
   */
  static function getBaseUrl(){
    $scheme = "http".(isset($_SERVER["HTTPS"]) ? "s" : "");
    $host = $_SERVER["SERVER_NAME"];
    $port = ($_SERVER["SERVER_PORT"] == 80) ? "" : ":{$_SERVER['SERVER_PORT']}";
    $path = dirname($_SERVER["SCRIPT_NAME"]);
    
    return $scheme."://".$host.$port.$path;
  }
  
  /**
   * Include all the classes of the framework and modules
   * 
   * @return void
   */
  static function getAllClasses() {
    $root_dir = CAppUI::conf("root_dir");
    
    // Ordered paths
    $dirs = array(
      // Require all global classes
      "classes/*.class.php",
      "classes/*/*.class.php",

      // Require mobile classes
      "mobile/*/*.class.php",

      // Require all modules classes
      // Don't include classes in subdirectories (there are a lot !)
      "modules/*/classes/*.class.php",
      //"modules/*/classes/*/*.class.php",
      //"modules/*/classes/*/*/*.class.php",

      // Require all modules setups 
      "modules/*/setup.php",
    );

    // Actual requires
    foreach ($dirs as $dir) {
      $files = glob("$root_dir/$dir");
      foreach ($files as $fileName) {
        include_once $fileName;
      }
    }
  }

  /**
   * Return all child classes of a given class having given properties
   *
   * @param string $parent        [optional] Parent class
   * @param array  $properties    [optional] No property checking if empty
   * @param bool   $active_module [optional] If true, filter on active modules
   *
   * @return array Class names
   * @todo Default parent class should probably be CModelObject
   */
  static function getChildClasses($parent = "CMbObject", $properties = array(), $active_module = false) {
    $childclasses = SHM::get("child-classes");

    // Do not cache when we want all the classes
    if ($parent && empty($properties) && isset($childclasses[$parent][$active_module])) {
      return $childclasses[$parent][$active_module];
    }
    
    self::getAllClasses();
    
    $classes = get_declared_classes();
    foreach ($classes as $key => $class) {
      // Filter on parent class
      if ($parent && !is_subclass_of($class, $parent)) {
        unset($classes[$key]);
        continue;
      }
  
      // Filter on properties
      foreach ($properties as $prop) {
        if (!array_key_exists($prop, get_class_vars($class))) {
          unset($classes[$key]);
        }
      }
      
      // Filter on active module
      if ($active_module) {
        $object = new $class; 
        if (!isset($object->_ref_module)) {
          unset($classes[$key]);
        }
      }
    }
    
    sort($classes);
    
    // Caching
    if ($parent && empty($properties)) {
      $childclasses[$parent][$active_module] = $classes;
      SHM::put("child-classes", $childclasses);
    }
    
    return $classes;
  }
  
  /**
   * Return all CMbObject child classes
   * 
   * @param array $properties [optional] Filter on properties
   * @param array &$instances [optional] If not null, retrieve an array of all object instances
   * 
   * @return array Class names
   */
  static function getMbClasses($properties = array(), &$instances = null) {
    $classes = self::getChildClasses("CStoredObject", $properties);
    
    foreach ($classes as $key => $class) {
      // In case we removed a class and it's still in the cache
      if (!class_exists($class, true)) {
        unset($classes[$key]);
        continue;
      }
      
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
     
      // Instanciated class?
      // @todo All class should be instanciable 
      if (!$object->_class) {
        unset($classes[$key]);
        continue;
      }
      
      $instances[$class] = $object;
    }
    
    return $classes;
  }
  
  /**
   * Return all storable CMbObject classes which module is installed
   *
   * @param array $classes [optional] Restrain to given classes
   * 
   * @return array Class names
   */
  static function getInstalledClasses($classes = array()) {
    if (empty($classes)) {
      $classes = self::getMbClasses();
    }
    
    foreach ($classes as $key => $class) {
      // Escaped instanciation in case of DSN errors
      $object = @new $class;
      
      // Installed module ?
      if ($object->_ref_module === null) {
        unset($classes[$key]);
        continue;
      }
  
      // Storable class ?
      if (!$object->_spec->table) {
        unset($classes[$key]);
        continue;
      }
    }
    
    return $classes;
  }
  
  /**
   * Group installed classes by module names
   * 
   * @param array $classes Class names
   * 
   * @return array Array with module names as key and class names as values
   */
  static function groupClassesByModule($classes) {
    $grouped = array();
    foreach ($classes as $class) {
      $object = new $class;
      if ($module = $object->_ref_module) {
        $grouped[$module->mod_name][] = $class;
      }
    }
    return $grouped;
  }
  
  /**
   * Staticly build index handlers array
   * 
   * @return void
   */
  public static final function makeHandlers() {
    if (is_array(self::$handlers)) {
      return;
    }
    
    // Static initialisations
    self::$handlers = array();
    foreach (CAppUI::conf("index_handlers") as $_class => $_active) {      
      if ($_active) {
        if (!class_exists($_class)) {
          CModelObject::error("application-index-handler-missing-class%s", $_class);
          continue;
        }
        self::$handlers[$_class] = new $_class;
      }
    }
  }
  
  /**
   * Subject notification mechanism 
   * TODO Implement to factorize 
   *   on[Before|After][Store|Merge|Delete]()
   *   which have to get back de CPersistantObject layer
   * 
   * @param string $message The notification type
   * 
   * @return void
   */
  static function notify($message) {
    // Event Handlers
    self::makeHandlers();
    foreach (self::$handlers as $_handler) {
      try {
        call_user_func(array($_handler, "on$message"));
      } 
      catch (Exception $e) {
        CAppUI::setMsg($e, UI_MSG_ERROR);
      }
    }
  }

  /**
   * Prepare performance data to be displayed
   *
   * @return void
   */
  static function preparePerformance(){
    arsort(CMbObject::$cachableCounts);
    arsort(CMbObject::$objectCounts);
    arsort(self::$performance["autoload"]);

    self::$performance["genere"]         = round(self::$chrono->total, 3);
    self::$performance["memoire"]        = CHTMLResourceLoader::getOutputMemory();
    self::$performance["objets"]         = CMbObject::$objectCount;
    self::$performance["cachableCount"]  = array_sum(CMbObject::$cachableCounts);
    self::$performance["cachableCounts"] = CMbObject::$cachableCounts;
    self::$performance["objectCounts"]   = CMbObject::$objectCounts;
    self::$performance["ip"]             = $_SERVER["SERVER_ADDR"];

    self::$performance["size"] = CHTMLResourceLoader::getOutputLength();
    self::$performance["ccam"] = array (
      "cacheCount" => class_exists("CCodeCCAM") ? CCodeCCAM::$cacheCount : 0,
      "useCount"   => class_exists("CCodeCCAM") ? CCodeCCAM::$useCount   : 0
    );
    
    self::$performance["functionCache"] = array(
      "totals" => CFunctionCache::$totals,
      "total"  => CFunctionCache::$total,
    );
    
    // Data sources performance
    foreach (CSQLDataSource::$dataSources as $dsn => $ds) {
      if (!$ds) {
        continue;
      }

      $chrono      = $ds->chrono;
      $chronoFetch = $ds->chronoFetch;

      self::$performance["dataSources"][$dsn] = array(
        "count"      => $chrono->nbSteps,
        "time"       => $chrono->total,
        "countFetch" => $chronoFetch->nbSteps,
        "timeFetch"  => $chronoFetch->total,
      );
    }
  }

  /**
   * Must be the same here and SHM::init
   * We don't use CApp because it can be called in /install
   *
   * @return string Application identifier, in a pool of servers
   */
  static function getAppIdentifier(){
    $root_dir = CAppUI::conf("root_dir");

    return preg_replace("/[^\w]+/", "_", $root_dir);
  }

  /**
   * Initializes a unique request ID to identify current request
   *
   * @return string
   */
  private static function initRequestUID(){
    $user_id = CUser::get()->_id;
    $uid     = uniqid("", true);

    $address = get_remote_address();
    $ip      = $address["remote"];

    // MD5 is enough as it doesn't have to be crypto proof
    self::$requestUID = md5("$user_id/$uid/$ip");
  }

  /**
   * Get the current request unique ID
   *
   * @return string
   */
  static function getRequestUID(){
    if (self::$requestUID === null) {
      self::initRequestUID();
    }

    return self::$requestUID;
  }

  /**
   * Execute a script on all servers
   *
   * @param String[] $get  Parameters GET
   * @param String[] $post Parameters POST
   *
   * @return Array
   */
  static function multipleServerCall($get, $post = null) {
    $base = "mediboard/index.php?";
    $address = array("127.0.0.1" => "");
    foreach ($get as $_param => $_value) {
      $base .= "$_param=$_value&";
    }
    $base = substr($base, 0, -1);

    $servers = array();
    $list_ip = trim(CAppUI::conf("servers_ip"));
    if ($list_ip) {
      $servers = preg_split("/\s*,\s*/", $list_ip, -1, PREG_SPLIT_NO_EMPTY);
      $servers = array_flip($servers);
    }
    $address = array_merge($address, $servers);

    foreach ($address as $_ip => $_value) {
      $address[$_ip] = self::serverCall("http://$_ip/$base", $post);
    }

    return $address;
  }

  /**
   * Send the request on the server
   *
   * @param String   $url  URL
   * @param String[] $post Parameters POST
   *
   * @return bool|string
   */
  private static function serverCall($url, $post = null) {
    CSessionHandler::writeClose();
    global $rootName, $version;
    $session_name = preg_replace("/[^a-z0-9]/i", "", $rootName);
    $cookie = CValue::cookie($session_name);
    $result = array("code" => "", "body" => "");
    try {
      $http_client = new CHTTPClient($url);
      $http_client->setCookie("$session_name=$cookie");
      $http_client->setUserAgent("Mediboard-".$version["version"]);
      if ($post) {
        $request = $http_client->post(http_build_query($post));
      }
      else {
        $request = $http_client->get();
      }
    }
    catch (Exception $e) {
      CSessionHandler::start();
      $result["body"] = $e->getMessage();
      return $result;
    }
    CSessionHandler::start();

    $result["code"]   = $http_client->last_information["http_code"];
    $result["body"]   = $request;

    return $result;
  }
}
