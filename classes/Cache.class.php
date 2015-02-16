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

require_once __DIR__."/DSHM.class.php";

/**
 * Multi-layers cache utility class
 * Using inner, outer or distributed layer or any combination of those
 */
class Cache {
  /**
   * No cache layer used
   * Useful for testing purposes
   */
  const NONE = 0;
  /**
   * INNER layer will use PHP static storage
   * Cache is available at an HTTP request level
   * Be aware: Values are manipulated by *reference* and subject to contextualisation issues
   */
  const INNER = 1;

  /**
   * OUTER strategy will use the shared memory active engine, like APC or FileSystem
   * Cache is available at an HTTP server level
   * Values are manipulated by copy (serialization)
   */
  const OUTER = 2;

  /**
   * DISTR stragery will user the distributed active shared memory, like Redis or any distributed key-value facility
   * Cache is available at a web servers farm level.
   * Be aware: so far no mechanism would allow the DISTR layer to prune other servers OUTER
   * So use OUTER and DISTR together very cautiously
   * Values are manipulated by copy (serialization)
   */
  const DISTR = 4;

  /**
   * The standard and default INNER and OUTER layer combination
   * Very performant, should be used in most cases
   **/
  const INNER_OUTER = 3;

  /** @var array Count cache usage per key and layer */
  static $hits = array();

  static $totals = array();
  static $total = 0;

  /** @var array The actual PHP static cache data */
  private static $data = array();

  /**
   * Get information about key existence and usage among the different layers
   *
   * @param string $key The key to get information about
   *
   * @return array
   */
  static function info($key) {
    return array(
      "INNER" => array(
        "exist" => isset(self::$data[$key]),
        "usage" => isset(self::$hits[$key][Cache::INNER]) ? self::$hits[$key]["INNER"] : 0,
      ),
      "OUTER" => array(
        "exist" => SHM::exists(self::$data[$key]),
        "usage" => isset(self::$hits[$key][Cache::OUTER]) ? self::$hits[$key]["OUTER"] : 0,
      ),
      "DISTR" => array(
        "exist" => DSHM::exists(self::$data[$key]),
        "usage" => isset(self::$hits[$key][Cache::DISTR]) ? self::$hits[$key]["DISTR"] : 0,
      )
    );
  }

  /** @var string */
  public $prefix;
  /** @var string */
  public $key;
  /** @var integer */
  public $layers;
  /** @var mixed */
  public $value;

  /**
   * Construct a cache operator
   *
   * @param string          $prefix Prefix to the key, for categorizing, typically __METHOD__
   * @param string|string[] $key    The key of the value to access, a string or array of strings, typically func_get_args()
   * @param integer         $layers Any combination of cache layers
   *
   * @return mixed
   */
  public function __construct($prefix, $key, $layers) {
    $this->key = is_array($key) ? implode("-", $key) : "$key";
    $this->prefix = $prefix;
    $this->layers = $layers;
  }

  /**
   * Record usage for a key and layer, for stats and info purpose
   *
   * @param integer $layer The used layer to retrieve the value
   */
  private function _hit($layer) {
    if (!isset(self::$hits[$this->prefix][$this->key][$layer])) {
      self::$hits[$this->prefix][$this->key]= array(
        "NONE"  => 0,
        "INNER" => 0,
        "OUTER" => 0,
        "DISTR" => 0,
      );
    }

    if (!isset(self::$totals[$this->prefix][$layer])) {
      self::$totals[$this->prefix] = array(
        "NONE"  => 0,
        "INNER" => 0,
        "OUTER" => 0,
        "DISTR" => 0,
      );
    }

    self::$hits[$this->prefix][$this->key][$layer]++;
    self::$totals[$this->prefix][$layer]++;
    self::$total++;
  }

  /**
   * Inform whether value if avaialble in one of the defined cache layers
   *
   * @return bool
   */
  public function exists() {
    $layers = $this->layers;

    // Inner cache
    if ($layers & Cache::INNER) {
      if (array_key_exists($this->prefix, self::$data) && array_key_exists($this->key, self::$data[$this->prefix])) {
        return true;
      }
    }

    // Flat key for outer and distributed layers
    $key = "$this->prefix-$this->key";

    // Outer cache
    if ($layers & Cache::OUTER) {
      if (SHM::exists($key)) {
        return true;
      }
    }

    // Distributed cache
    if ($layers & Cache::DISTR) {
      if (SHM::exists($key)) {
        return true;
      }
    }

    $this->_hit("NONE");
    return false;
  }

  /**
   * Get a value from the cache
   *
   * @return mixed
   */
  public function get() {
    $layers = $this->layers;

    // Inner cache
    if ($layers & Cache::INNER) {
      if (isset(self::$data[$this->prefix][$this->key])) {
        $this->_hit("INNER");
        return self::$data[$this->prefix][$this->key];
      }
    }

    // Flat key for outer and distributed layers
    $key = "$this->prefix-$this->key";

    // Outer cache
    if ($layers & Cache::OUTER) {
      if (null !== $value = SHM::get($key)) {
        if ($layers & Cache::INNER) {
          self::$data[$this->prefix][$this->key] = $value;
        }

        $this->_hit("OUTER");
        return $value;
      }
    }

    // Distributed cache
    if ($layers & Cache::DISTR) {
      if (null !== $value = DSHM::get($key)) {
        if ($layers & Cache::OUTER) {
          SHM::put($key, $value);
        }

        if ($layers & Cache::INNER) {
          self::$data[$this->prefix][$this->key] = $value;
        }

        $this->_hit("DISTR");
        return $value;
      }
    }

    $this->_hit("NONE");
    return null;
  }

  /**
   * Put a value to the cache
   *
   * @param mixed $value    The value to set
   * @param bool  $compress Compress data for copy strategy layers
   *
   * @return mixed The value, for return chaining
   */
  public function put($value, $compress = false) {
    $layers = $this->layers;

    // Inner cache
    if ($layers & Cache::INNER) {
      self::$data[$this->prefix][$this->key] = $value;
    }

    // Flat key for outer and distributed layers
    $key = "$this->prefix-$this->key";

    // Outer cache
    if ($layers & Cache::OUTER) {
      SHM::put($key, $value, $compress);
    }

    // Distributed cache
    if ($layers & Cache::DISTR) {
      DSHM::put($key, $value, $compress);
    }

    return $value;
  }

  /**
   * Remove a value from all defined layers of the cache
   *
   * @return void
   */
  public function rem() {
    $layers = $this->layers;

    // Inner cache
    if ($layers & Cache::INNER) {
      unset(self::$data[$this->prefix][$this->key]);
    }

    // Flat key for outer and distributed layers
    $key = "$this->prefix-$this->key";

    // Outer cache
    if ($layers & Cache::OUTER) {
      SHM::rem($key);
    }

    // Distributed cache
    if ($layers & Cache::DISTR) {
      DSHM::rem($key);
    }
  }
}
