<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::loadClass("CHTMLResourceLoader");

abstract class CJSLoader extends CHTMLResourceLoader {
  static $files = array();
  
  /**
   * Loads a javascript file
   */
  static function loadFile($file, $cc = null, $build = null, $type = "text/javascript") {
    $tag = self::getTag("script", array(
      "type" => $type ? $type : "text/javascript",
      "src"  => "$file?".self::getBuild($build),
    ), null, false);
    return self::conditionalComments($tag, $cc);
  }
  
  static function loadFiles($compress = false, $type = "text/javascript") {
    $result = "";
    $compress = CAppUI::conf("minify_javascript");
    
    /** 
     * There is a speed boost on the page load when using compression 
     * between the top of the head and the dom:loaded event of about 25%.
     * This is because of parse time that is reduced (compare the global __pageLoad variable)
     * The number of requests from a regular page goes down from 100 to 70.
     * The total size of the JS goes down from 300kB to 230kB (gzipped).
     */
    if ($compress) {
      $compress = 1; // force Normal compression
      
      $files = self::$files;
      $excluded = array();
      $uptodate = false;
      
      // We exclude files already in the tmp dir
      foreach($files as $index => $file) {
        if (strpos($file, "tmp/") === 0) {
          $excluded[] = $file;
          unset($files[$index]);
        }
      }
      
      $hash = self::getHash(implode("", $files)."-level-$compress");
      $cachefile = "tmp/$hash.js";
      
      // If it exists, we check if it is up to date
      if (file_exists($cachefile)) {
        $uptodate = true;
        $last_update = filemtime($cachefile);
        foreach($files as $file) {
          if (filemtime($file) > $last_update) {
            $uptodate = false;
            break;
          }
        }
      }
      
      if (!$uptodate) {
        $all_scripts = "";
        foreach($files as $file) {
          $all_scripts .= file_get_contents($file)."\n";
        }
        
        if($compress == 2) {
          $all_scripts = self::minify($all_scripts);
        }
        
        file_put_contents($cachefile, $all_scripts);
        $last_update = time();
      }
      
      foreach($excluded as $file) {
        $result .= self::loadFile($file, null, filemtime($file), $type)."\n";
      }
      
      $result .= self::loadFile($cachefile, null, $last_update, $type)."\n";
    }
    else {
      foreach(self::$files as $file) {
        $result .= self::loadFile($file, null, filemtime($file), $type)."\n";
      }
    }
    
    return $result;
  }
  
  static function minify($js) {
    return JSMin::minify($js);
  }
  
  static function writeLocaleFile($language = null, $locales = null, $label = null) {
    global $version;
    
    // It will update all the locale files
    if (!$language) {
      $languages = array();
      foreach (glob("./locales/*", GLOB_ONLYDIR) as $lng)
        $languages[] = basename($lng);
    }
    else {
      $languages = array($language);
    }
    if (!$locales) {
      foreach($languages as $language) {
        $localeFiles = array_merge(
          glob("./locales/$language/*.php"), 
          glob("./modules/*/locales/$language.php")
        );
      }
      
      foreach ($localeFiles as $localeFile) {
        if (basename($localeFile) != "meta.php") {
          require $localeFile;
        }
      }
    }
    
    foreach($languages as $language) {
      $path = self::getLocaleFilePath($language, $label);
      if ($fp = fopen($path, 'w')) {
        // The callback will filter on empty strings (without it, "0" will be removed too).
        $locales = array_filter($locales, "stringNotEmpty");
        // TODO: change the invalid keys (with accents) of the locales to simplify this
        $keys = array_map('utf8_encode', array_keys($locales));
        $values = array_map('utf8_encode', $locales);
        
        foreach($values as &$_value) {
          $_value = CMbString::unslash($_value);
        }
        
        $script = '//'.$version['build']."\nwindow.locales=".json_encode(array_combine($keys, $values)).";";
        fwrite($fp, $script);
        fclose($fp);
      }
    }
  }

  static function getLocaleFile($locales = null, $label = null) {
    $language = CAppUI::pref("LOCALE");
    
    $path = self::getLocaleFilePath($language, $label);
    
    if (!is_file($path)) {
      self::writeLocaleFile($language, $locales, $label);
    }
    
    return $path;
  }
  
  static function getLocaleFilePath($language, $label = null) {
    return "tmp/locales".($label ? ".$label" : "")."-$language.js";
  }
}
