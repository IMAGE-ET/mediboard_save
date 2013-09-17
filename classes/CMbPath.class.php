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

// PEAR Throws 
require_once "Archive/Tar.php";

abstract class CMbPath {
  /**
   * Ensures a directory exists by building all tree sub-directories if possible
   *
   * @param string $dir  Directory path
   * @param int    $mode chmod like value
   *
   * @return boolean job done
   */
  static function forceDir($dir, $mode = 0755) {
    if (!$dir) {
      trigger_error("Directory is null", E_USER_WARNING);
      return false;
    }
    
    if (is_dir($dir) || $dir === "/") {
      return true;
    }
    
    if (self::forceDir(dirname($dir))) {
      return mkdir($dir, $mode);
    }
  
    return false;
  }

  /**
   * Checks if a directory is empty
   *
   * @param string $dir Directory path
   *
   * @return bool true if directory is empty
   */
  static function isEmptyDir($dir) {
    if (false === $dh = opendir($dir)) {
      trigger_error("Passed argument is not a valid directory or couldn't be opened'", E_USER_WARNING);
      return false;
    }
    
    readdir($dh); // for ./
    readdir($dh); // for ../
    $file = readdir($dh); // for real first child

    closedir($dh);

    return $file === null;
  }

  /**
   * Removes all empty sub-directories of a given directory
   *
   * @param string $dir Directory from which we want to remove empty directories
   *
   * @return integer Removed directories count
   */
  static function purgeEmptySubdirs($dir) {
    $removedDirsCount = 0;
    
    if (false === $dh = opendir($dir)) {
      trigger_error("Passed argument is not a valid directory or couldn't be opened'", E_USER_WARNING);
      return 0;
    }
    
    while ($node = readdir($dh)) {
      $path = "$dir/$node";
      if (is_dir($path) && $node !== "." && $node !== "..") {
        $removedDirsCount += self::purgeEmptySubdirs($path);
      }
    }
    closedir($dh);
    
    if (self::isEmptyDir($dir)) {
      if (rmdir($dir)) {
        mbTrace($dir, "Removed directory");
        $removedDirsCount++;
      }
    }
    
    return $removedDirsCount;
  }

  /**
   * Get the extension of a file
   *
   * @param string $path Path from which we want the extension
   *
   * @return string|null The extension
   */
  static function getExtension($path) {
    $info = pathinfo($path);

    if (array_key_exists('extension', $info)) {
      return $info['extension'];
    }

    return null;
  }

  /**
   * Guess the mime type of a file from its extension
   *
   * @param string $file The file from which we want to guess the mime type
   *
   * @return string The mime type
   */
  static function guessMimeType($file) {
    $ext = strtolower(self::getExtension($file));
    
    // http://us3.php.net/manual/en/function.mime-content-type.php#84361
    switch ($ext) {
      default: 
        return "unknown/$ext";
        
      case "js" :
        return "application/x-javascript";

      case "json" :
        return "application/json";

      case "jpg" :
      case "jpeg" :
      case "jpe" :
        return "image/jpg";

      case "png" :
      case "gif" :
      case "bmp" :
      case "tiff" :
      case "tif" :
        return "image/$ext";

      case "css" :
        return "text/css";

      case "xml" :
        return "application/xml";

      case "doc" :
      case "docx" :
      case "dot" :
        return "application/msword";

      case "xls" :
      case "xlt" :
      case "xlm" :
      case "xld" :
      case "xla" :
      case "xlc" :
      case "xlw" :
      case "xll" :
        return "application/vnd.ms-excel";

      case "ppt" :
      case "pps" :
        return "application/vnd.ms-powerpoint";

      case "rtf" :
        return "application/rtf";

      case "pdf" :
        return "application/pdf";

      case "html" :
      case "htm" :
      case "php" :
        return "text/html";

      case "txt" :
      case "ini" :
        return "text/plain";

      case "mpeg" :
      case "mpg" :
      case "mpe" :
        return "video/mpeg";

      case "mp3" :
        return "audio/mpeg3";

      case "wav" :
        return "audio/wav";

      case "aiff" :
      case "aif" :
        return "audio/aiff";

      case "avi" :
        return "video/msvideo";

      case "wmv" :
        return "video/x-ms-wmv";

      case "mov" :
        return "video/quicktime";

      case "zip" :
        return "application/zip";

      case "tar" :
        return "application/x-tar";

      case "swf" :
        return "application/x-shockwave-flash";
        
      case "nfs" :
        return "application/vnd.lotus-notes";
        
      case "spl" :
      case "rlb" :
        return "application/vnd.sante400";
    }
  }

  /**
   * Extracts an archive into a destination directory
   *
   * @param string $archivePath    Path to the archive file
   * @param string $destinationDir Destination forlder
   *
   * @return integer The number of extracted files or false if failed
   */
  static function extract($archivePath, $destinationDir) {
    if (!is_file($archivePath)) {
      trigger_error("Archive could not be found", E_USER_WARNING);
      return false;
    }
    
    if (!self::forceDir($destinationDir)) {
      trigger_error("Destination directory not existing", E_USER_WARNING);
      return false;
    }
    
    $nbFiles = 0;
    $extract = false;
    switch (self::getExtension($archivePath)) {
      case "gz"  :
      case "tgz" : 
        $archive = new Archive_Tar($archivePath);
        $nbFiles = count($archive->listContent());
        $extract = $archive->extract($destinationDir);
        break;
      
      case "zip" : 
        if (class_exists("ZipArchive", false)) {
          $archive = new ZipArchive();
          $archive->open($archivePath);
          $nbFiles = $archive->numFiles;
          $extract = $archive->extractTo($destinationDir);
        }
        else {
          require_once "Archive/Zip.php";
          $archive = new Archive_Zip($archivePath);
          $nbFiles = count($archive->listContent());
          $extract = $archive->extract(array("add_path" => $destinationDir));
        }
        break;
    }
    
    if (!$extract) {
      return false;
    }
    
    return $nbFiles;
  }

  /**
   * Clears out any file or sub-directory from target path
   *
   * @param string $dir Remove any file / folder from the directory
   *
   * @return boolean true on success, false otherwise
   */
  static function emptyDir($dir) {
    /** @var Directory $dir */
    $dir = dir($dir);

    if (!$dir) {
      return false;
    }
    
    while (false !== $item = $dir->read()) {
      if ($item !== '.' && $item !== '..' && !self::remove($dir->path . DIRECTORY_SEPARATOR . $item)) {
        $dir->close();
        return false;
      }
    }
    
    $dir->close();
    return true;
  }

  /**
   * Recursively removes target path
   *
   * @param string $path Removes a directory
   *
   * @return boolean true on success, false otherwise
   */
  static function remove($path) {
    if (!$path) {
      trigger_error("Path undefined", E_USER_WARNING);
    }
    
    if (is_dir($path)) {
      if (self::emptyDir($path)) {
        return rmdir($path);
      }
      return false;
    }
    
    return unlink($path);
  }
  
  
  /**
   * Reduces a path, removing "folder/.." occurences
   *
   * @param string $path The path to reduce
   *
   * @return string The reduced path
   * @todo Use realpath instead
   */ 
  static function reduce($path) {
    while (preg_match('/([A-z0-9-_])+\/\.\.\//', $path)) {
      $path = preg_replace('/([A-z0-9-_])+\/\.\.\//', '', $path);
    }

    return $path;
  }
  
  /**
   * Count the files under $path
   *
   * @param string $path The path to read
   *
   * @return string The number of files
   */
  static function countFiles($path) {
    return count(glob("$path/*")) - count(glob("$path/*", GLOB_ONLYDIR));
  }

  /**
   * Coompare file names, with directory first
   *
   * @param string $a File A
   * @param string $b File B
   *
   * @return int Comparison result as an integer, 0 being tie
   */
  static function cmpFiles($a, $b) {
    $dira = is_dir($a);
    $dirb = is_dir($b);
    return $dira == $dirb ? strcmp($a, $b) : $dirb - $dira;
  }
  
  /**
   * Get the path tree under a given directory
   *
   * @param string $dir        Directory to get the tree of
   * @param array  $ignored    Ignored patterns
   * @param array  $extensions Restricted extensions, if not null
   *
   * @return array|null Recursive array of basenames
   */
  static function getPathTreeUnder($dir, $ignored = array(), $extensions = null) {
    // Restricted extensions
    if (!is_dir($dir) && is_array($extensions) && !in_array(self::getExtension($dir), $extensions)) {
      return null;
    }
    
    // Ignored patterns
    foreach ($ignored as $_ignored) {
      $replacements = array(
        "*" => ".*",
        "." => "[.]",
      );
      
      $_ignored = strtr($_ignored, $replacements);
      
      if (preg_match("|{$_ignored}|i", $dir) === 1) {
        return null;
      }
    }
            
    // File case
    if (!is_dir($dir)) {
      return true;
    }

    // Directory case
    $tree = array();
    $files = glob("$dir/*");
    usort($files, array("CMbPath", "cmpFiles"));
    
    foreach ($files as $file) {
      $branch = self::getPathTreeUnder($file, $ignored, $extensions);
      if ($branch == null || (is_array($branch) && !count($branch))) {
        continue;
      }
      $tree[basename($file)] = $branch;
    }
    
    return $tree;
  }
  
  /**
   * Add a directory to include path
   *
   * @param string $dir Directory to add
   *
   * @return string The former include path
   */
  static function addInclude($dir) {
    if (!is_dir($dir)) {
      trigger_error("'$dir' is not an actual directory", E_USER_WARNING);
    }
    
    $paths = explode(PATH_SEPARATOR, get_include_path());
    $paths[] = $dir;
    return set_include_path(implode(PATH_SEPARATOR, array_unique($paths)));
  }

  /**
   * Get a list of the files inside a direcory (not the subdirectories)
   *
   * @param string $path The directory
   *
   * @return string[] The list of files
   */
  static function getFiles($path) {
    return array_diff(glob("$path/*"), glob("$path/*", GLOB_ONLYDIR));
  }

  /**
   * Get a file pointer to a temporary file in binary write mode (rb+)
   *
   * @return resource The resource to the temporary file
   */
  static function getTempFile() {
    // PHP 5.1+
    $f = @fopen("php://temp", "rb+");
    
    if (!$f) {
      $f = fopen(tempnam(sys_get_temp_dir(), "mb_"), "rb+");
    }
    
    return $f;
  }

  /**
   * Recursively zip a folder
   *
   * @param string $source      Source folder
   * @param string $destination Desctination archive
   *
   * @link http://stackoverflow.com/a/1334949/92315
   *
   * @return bool
   */
  static function zip($source, $destination) {
    if (!extension_loaded('zip') || !file_exists($source)) {
      return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
      return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) {
      $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

      foreach ($files as $file) {
        $file = str_replace('\\', '/', $file);

        // Ignore "." and ".." folders
        if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
          continue;
        }

        $file = realpath($file);
        $file = str_replace('\\', '/', $file); // For Windows

        if (is_dir($file) === true) {
          $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
        }
        else if (is_file($file) === true) {
          $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
        }
      }
    }
    else if (is_file($source) === true) {
      $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
  }
}
