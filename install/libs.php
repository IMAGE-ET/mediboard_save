<?php /* $Id: install.php 6974 2009-09-30 13:11:38Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision: 6974 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CLibraryPatch {
  var $dirName    = "";
  var $subDirName = "";
  var $sourceName = "";
  var $targetDir  = "";
  
  function apply() {
    global $mbpath;
    $pkgsDir = $mbpath."libpkg";
    $libsDir = $mbpath."lib";
    $patchDir = "$pkgsDir/patches";
    $sourcePath = "$patchDir/$this->dirName/";
    if($this->subDirName) {
      $sourcePath .= "$this->subDirName/";
    }
    $sourcePath .= "$this->sourceName";
    $targetPath = "$libsDir/$this->dirName/$this->targetDir/$this->sourceName";
    $oldPath = $targetPath . ".old";
    
    @unlink($oldPath);
    @rename($targetPath, $oldPath);
    return copy($sourcePath, $targetPath);
  }
}

class CLibrary {
  static $all = array();
  
  var $name = "";
  var $url = "";
  var $fileName = "";
  var $extraDir = "";
  var $description = "";
  var $nbFiles = 0;
  var $sourceDir = null;
  var $targetDir = null;
  var $versionFile = "";
  var $versionString = "";
  var $patches = array();
  var $lib_ = null;
  
  function clearLibraries($libSel) {
    global $mbpath;
    $libsDir = $mbpath."lib";
    
    /// Clear out all libraries
    if (!$libSel){
      foreach (glob("$libsDir/*") as $libDir) {
        if (strpos($libDir, '.svn') === false) {
          CMbPath::remove($libDir);
        }
      }
      return;
    } 

    // Clear out selected lib
    $library = self::$all[$libSel];
    if ($targetDir = $library->targetDir) {
      return @CMbPath::remove("$libsDir/$targetDir");
    }
  }
  
  function getUpdateState() {
    global $mbpath;
    $dir = $mbpath."lib/$this->targetDir";
    
    if ($this->versionFile && $this->versionString) {
      return (file_exists("$dir/$this->versionFile") &&
              strpos(file_get_contents("$dir/$this->versionFile"), $this->versionString) !== false);
    }
    return null;
  }
  
  function isInstalled() {
    global $mbpath;
    return is_dir($mbpath."lib/$this->targetDir");
  }
    
  function countLibraries() {
    global $mbpath;
    $libsDir = $mbpath."lib";
    $libsCount = 0;
    
    foreach (glob("$libsDir/*") as $libDir) {
      $libsCount++;
    }
    
    return $libsCount;
  }
  
  function install() {
    global $mbpath;
    $pkgsDir = $mbpath."libpkg";
    $libsDir = $mbpath."lib";
    $filePath = "$pkgsDir/$this->fileName";
    
    // For libraries archive not contained in directory
    if ($this->extraDir) {
      $libsDir .= "/$this->extraDir";
    }
    
    return CMbPath::extract($filePath, $libsDir);
  }
  
  function apply() {
    global $mbpath;
    $libsDir = $mbpath."lib";
    $sourceDir = "$libsDir/$this->sourceDir";
    $targetDir = "$libsDir/$this->targetDir";
    assert(is_dir($sourceDir));
    return rename($sourceDir, $targetDir);
  }
}

$libSel = CValue::post("libSel","");

$library = new CLibrary;
$library->name = "Smarty";
$library->url = "http://smarty.php.net/";
$library->fileName = "Smarty-2.6.18.tar.gz";
$library->description = "Moteur de templates PHP et framework de pr�sentation";
$library->sourceDir = "Smarty-2.6.18";
$library->targetDir = "smarty";
$library->versionFile = "NEWS";
$library->versionString = "Version 2.6.18";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JPGraph";
$library->url = "http://www.aditus.nu/jpgraph/";
$library->fileName = "jpgraph-2.1.4.tar.gz";
$library->description = "Composant PHP de g�n�ration de graphs aux formats d'image";
$library->sourceDir = "jpgraph-2.1.4";
$library->targetDir = "jpgraph";
$library->versionFile = "VERSION";
$library->versionString = "Revision: r793";
//$library->versionFile = "src/mbjpgraph.php";
//$library->versionString = "2010-12-15";

$patch = new CLibraryPatch;
$patch->dirName = "jpgraph";
$patch->sourceName = "mbjpgraph.php";
$patch->targetDir = "src";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "TCPDF";
$library->url = "http://sourceforge.net/projects/tcpdf/";
$library->fileName = "tcpdf_1_53_0_TC034.zip";
$library->description = "Composant de g�n�ration de fichiers PDF avec codes barres";
$library->sourceDir = "tcpdf";
$library->targetDir = "tcpdf";
$library->versionFile = "barcode/cmb128bobject.php";
$library->versionString = "public function DrawObject";

$patch = new CLibraryPatch;
$patch->dirName = "tcpdf";
$patch->sourceName = "cmb128bobject.php";
$patch->targetDir = "barcode";
$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "tcpdf";
$patch->sourceName = "tcpdf.php";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "PHPMailer";
$library->url = "http://phpmailer.sourceforge.net/";
$library->fileName = "PHPMailer_v5.1.tar.gz";
$library->description = "Composant PHP d'envoi d'email";
$library->sourceDir = "PHPMailer_v5.1";
$library->targetDir = "phpmailer";
$library->versionFile = "changelog.txt";
$library->versionString = "Version 5.1";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Scriptaculous";
$library->url = "http://script.aculo.us/";
$library->fileName = "scriptaculous-js-1.8.3.tar.gz";
$library->description = "Composant Javascript d'effets sp�ciaux, accompagn� du framework prototype.js";
$library->sourceDir = "scriptaculous-js-1.8.3";
$library->targetDir = "scriptaculous";
$library->versionFile = "CHANGELOG";
$library->versionString = "*V1.8.3*";

$patch = new CLibraryPatch;
$patch->dirName = "scriptaculous";
$patch->sourceName = "scriptaculous.js";
$patch->targetDir = "src";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "DatePicker";
$library->url = "http://home.jongsma.org/software/js/datepicker";
$library->fileName = "datepicker.tar.gz";
$library->description = "Composant Javascript de s�lecteur de date/heure";
$library->sourceDir = "datepicker";
$library->targetDir = "datepicker";
$library->versionFile = "datepicker.js";
$library->versionString = "2011-05-31";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "phpThumb";
$library->url = "http://phpthumb.sourceforge.net/";
$library->fileName = "phpThumb_1.7.5.tar.gz";
$library->description = "Composant de cr�ation de thumbnails";
$library->extraDir = "phpThumb";
$library->sourceDir = "phpThumb";
$library->targetDir = "phpThumb";
$library->versionFile = "docs/phpthumb.changelog.txt";
$library->versionString = "v1.7.5b";

$patch = new CLibraryPatch;
$patch->dirName = "phpThumb";
$patch->sourceName = "phpThumb.config.php";
$patch->targetDir = "";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "CKEditor";
$library->url = "http://ckeditor.com/";
$library->fileName = "ckeditor_3.5.3.tar.gz";
$library->description = "Composant Javascript d'�dition de texte au format HTML";
$library->sourceDir = "ckeditor";
$library->targetDir = "ckeditor";
$library->versionFile = "ckeditor.js";
$library->versionString = "version:'3.5.3b'";

$patch = new CLibraryPatch;
$patch->dirName = "ckeditor";
$patch->subDirName = "skins/v2/";
$patch->sourceName = "editor.css";
$patch->targetDir = "skins/v2/";
$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "ckeditor";
$patch->subDirName = "/";
$patch->sourceName = "ckeditor.js";
$patch->targetDir = "/";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "kcfinder";
$library->url = "http://kcfinder.sunhater.com/";
$library->fileName = "kcfinder-2.2.tar.gz";
$library->description = "Gestionnaire de fichier en ajax";
$library->sourceDir = "kcfinder-2.2";
$library->targetDir = "kcfinder";
$library->versionFile = "browse.php";
$library->versionString = "@version 2.2";

$patch = new CLibraryPatch;
$patch->dirName = "kcfinder";
$patch->sourceName="config.php";
$patch->targetDir="";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Dojo";
$library->url = "http://www.dojotoolkit.org/";
$library->fileName = "dojo-0.4.1-storage.tar.gz";
$library->description = "Composant Javascript de sauvegarde de donn�es";
$library->sourceDir = "dojo-0.4.1-storage";
$library->targetDir = "dojo";
$library->versionFile = "dojo.js.uncompressed.js";
$library->versionString = "bootstrap1.js 6824";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Livepipe UI";
$library->url = "http://livepipe.net/";
$library->fileName = "livepipe.tar.gz";
$library->description = "High Quality Controls & Widgets for Prototype";
$library->extraDir = "livepipe";
$library->sourceDir = "livepipe";
$library->targetDir = "livepipe";
$library->versionFile = "livepipe.js";
$library->versionString = "@copyright 2010 PersonalGrid";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Flotr plotting library";
$library->url = "http://solutoire.com/flotr/";
$library->fileName = "flotr.r301.tar.gz";
$library->description = "Cr�ation de graphiques en JS";
$library->sourceDir = "flotr";
$library->targetDir = "flotr";
$library->versionFile = "flotr.js";
$library->versionString = 'draw:function(a,b,c,d){d=d||';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "GeSHi";
$library->url = "http://qbnz.com/highlighter/";
$library->fileName = "GeSHi-1.0.8.3.tar.gz";
$library->description = "Generic Syntax Highlighter";
$library->sourceDir = "geshi";
$library->targetDir = "geshi";
$library->versionFile = "geshi.php";
$library->versionString = "define('GESHI_VERSION', '1.0.8.3.1')";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "dompdf";
$library->url = "http://code.google.com/p/dompdf";
$library->fileName = "dompdf-19-07-11.tar.gz";
$library->description ="HTML to PDF Converter";
$library->sourceDir = "dompdf";
$library->targetDir = "dompdf";
$library->versionFile = "include/page_frame_decorator.cls.php";
$library->versionString = "/*if";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "RequireJS";
$library->url = "http://requirejs.org/";
$library->fileName = "requireJS.tar.gz";
$library->description = "Chargement JavaScript en Ajax";
$library->sourceDir = "requireJS";
$library->targetDir = "requireJS";
$library->versionFile = "require.js";
$library->versionString = '0.11.0';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "DBase reader class";
$library->url = "http://www.phpclasses.org/package/1302.html";
$library->fileName = "dbase.tar.gz";
$library->description = "Lecture de fichiers DBF";
$library->sourceDir = "dbase";
$library->targetDir = "dbase";
$library->versionFile = "dbf_class.php";
$library->versionString = 'v0.05 by Nicholas Vrtis';

CLibrary::$all[$library->name] = $library;

/*$library = new CLibrary;
$library->name = "Growler";
$library->url = "http://code.google.com/p/kproto/wiki/Growler";
$library->fileName = "growler1.0.0.tar.gz";
$library->description = "Lecture de fichiers DBF";
$library->sourceDir = "growler";
$library->targetDir = "growler";
$library->versionFile = "src/Growler.js";
$library->versionString = 'k.Growler 1.0.0';

CLibrary::$all[$library->name] = $library;
*/

$library = new CLibrary;
$library->name = "PDFMerger class";
$library->url = "http://pdfmerger.codeplex.com/";
$library->fileName = "PDFMerger-1.0.tar.gz";
$library->description = "Fusion de PDF";
$library->sourceDir = "PDFMerger";
$library->targetDir = "PDFMerger";
$library->versionFile = "PDFMerger.php";
$library->versionString = 'v1.0';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "jsExpressionEval";
$library->url = "https://github.com/silentmatt/js-expression-eval";
$library->fileName = "jsExpressionEval.tar.gz";
$library->description = "A JavaScript math expression evaluator";
$library->sourceDir = "jsExpressionEval";
$library->targetDir = "jsExpressionEval";
$library->versionFile = "parser.js";
$library->versionString = "updated:2011-01-07";

CLibrary::$all[$library->name] = $library;

?>