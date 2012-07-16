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

if (!class_exists("DOMDocument")) {
  return;
}

class CMbXMLDocument extends DOMDocument {
  var $schemapath       = null;
  var $schemafilename   = null;
  var $documentfilename = null;
  var $now              = null;
  
  function __construct($encoding = "iso-8859-1") {
    parent::__construct("1.0", $encoding);
    
    $this->preserveWhiteSpace = false;
    $this->formatOutput = true;
  }
  
  function setDocument($documentfilename) {
    $this->documentfilename = $documentfilename;
  }
  
  function setSchema($schemafilename) {
    $this->schemapath     = dirname($schemafilename);
    $this->schemafilename = $schemafilename;
  }
  
  /**
   * Try to load and validate XML File
   * @param $docPath string Uploaded file temporary path
   * @return string Store-like message 
   */
  function loadAndValidate($docPath) {
    // Chargement
    if (!$this->load($docPath)) {
      return "Le fichier fourni n'est pas un document XML bien form�";
    }
    
    // Validation
    if ($this->checkSchema() && !$this->schemaValidate()) {
      return "Document invalide";
    }
    
    return null;
  }
  
  function checkSchema() {
    if (!$this->schemafilename) {
      trigger_error("You haven't set the schema", E_USER_WARNING);
      return false;
    }
    if (!is_dir($this->schemapath)) {
      trigger_error("Schema directory is missing ($this->schemapath/)", E_USER_WARNING);
      return false;
    }
    
    if (!is_file($this->schemafilename)) {
      trigger_error("Schema is missing ($this->schemafilename)", E_USER_WARNING);
      return false;
    }
    
    return true;
  }

  function libxml_display_error($error) {
     $return = "<br/>\n";
     switch ($error->level) {
         case LIBXML_ERR_WARNING:
             $return .= "<b>Warning $error->code</b>: ";
             break;
         case LIBXML_ERR_ERROR:
             $return .= "<b>Error $error->code</b>: ";
             break;
         case LIBXML_ERR_FATAL:
             $return .= "<b>Fatal Error $error->code</b>: ";
             break;
     }
     $return .= trim($error->message);
     if ($error->file) {
         $return .=    " in <b>$error->file</b>";
     }
     $return .= " on line <b>$error->line</b>\n";
  
     return $return;
  }
  
  function libxml_display_errors($display_errors = true) {
     $errors = libxml_get_errors();
     $chain_errors = "";
     
     foreach ($errors as $error) {
        $chain_errors .= preg_replace('/( in\ \/(.*))/', '', strip_tags($this->libxml_display_error($error)))."\n";
       if ($display_errors) {
         trigger_error($this->libxml_display_error($error), E_USER_WARNING);
       }
     }
     libxml_clear_errors();

     return $chain_errors;
  }
  
  /**
   * Try to validate the document against a schema
   * will trigger errors when not validating
   * @param string Path of schema, use document inline schema if null 
   * @return boolean  
   */
  function schemaValidate($filename = null, $returnErrors = false, $display_errors = true) {
    if (!$filename) {
      $filename = $this->schemafilename;
    }

    // PHP < 5.1.x
    if (!function_exists("libxml_use_internal_errors")) {
      return parent::schemaValidate($filename);
    }
    
    // Enable user error handling
    libxml_use_internal_errors(true);
    
    if (!parent::schemaValidate($filename)) {
      $errors = $this->libxml_display_errors($display_errors);
      return $returnErrors ? $errors : false;
    }
    
    return true;
  }
  
  function libxml_tabs_erros() {
    
  }
  
  function loadXMLSafe($source, $options = null) {
    $ret = @parent::loadXML($source, $options);
    
    if (!$ret) {
      return parent::loadXML($this->getUTF8($source), $options);
    }
    
    return $ret;
  }
  
  function loadXML($source, $options = null) {
    $source = $this->getUTF8($source);
    
    return parent::loadXML($source, $options);
  }
  
  protected function getUTF8($source) {
    return CMbString::isUTF8($source) ? utf8_decode($source) : $source;
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = null) {
    $elName  = utf8_encode($elName );
    $elValue = utf8_encode($elValue);
    return $elParent->appendChild(new DOMElement($elName, $elValue, $elNS));
  }
  
  function addDateTimeElement($elParent, $elName, $dateValue = null) {
    $this->addElement($elParent, $elName, mbTransformTime(null, $dateValue, "%Y-%m-%dT%H:%M:%S"));
  }
  
  function addAttribute($elParent, $atName, $atValue) {
    $atName  = utf8_encode($atName );
    $atValue = utf8_encode($atValue);
    return $elParent->setAttribute($atName, $atValue);
  }
  
  function addComment($elParent, $comment) {
    return $elParent->appendChild($this->createComment($comment));
  }
  
  function addDocumentation($elParent, $documentation = null) {
    if (!$documentation) {
      return;
    }
    
    $annotation = $this->addElement($elParent, "annotation", null, "http://www.w3.org/2001/XMLSchema");
    $this->addElement($annotation, "documentation", $documentation, "http://www.w3.org/2001/XMLSchema");
  }

  function purgeEmptyElements() {
    $this->purgeEmptyElementsNode($this->documentElement);
  }
  
  function purgeEmptyElementsNode($node) {
    // childNodes undefined for non-element nodes (eg text nodes)
    if ($node->childNodes) {
      // Copy childNodes array
      $childNodes = array();
      foreach($node->childNodes as $childNode) {
        $childNodes[] = $childNode;
      }
 
      // Browse with the copy (recursive call)    
      foreach ($childNodes as $childNode) {
        $this->purgeEmptyElementsNode($childNode);      
      }
      
      // Remove if empty
      if (!$node->hasChildNodes() && !$node->hasAttributes()) {
//        trigger_error("Removing child node $node->nodeName in parent node {$node->parentNode->nodeName}", E_USER_NOTICE);
        $node->parentNode->removeChild($node);
      }
    }
  }
  
  function saveFile() {
    parent::save($this->documentfilename);
  }
  
  /**
   * Create a CFile attachment to given CMbObject
   * @return string store-like message, null if successful
   */
  function addFile(CMbObject $object) {
    $user = CUser::get();
    $this->saveFile();
    $file = new CFile();
    $file->object_id          = $object->_id;
    $file->object_class       = $object->_class;
    $file->file_name          = "$object->_guid.xml";
    $file->file_type          = "text/xml";
    $file->file_size          = filesize($this->documentfilename);
    $file->file_date          = mbDateTime();
    $file->file_real_filename = uniqid(rand());
    $file->author_id          = $user->_id;
    $file->private            = 0;
     if (!$file->moveFile($this->documentfilename)) {
      return "error-CFile-move-file";
    }

    return $file->store();
  }
  
  function getEvenements() {
    return array();
  }
  
  function getDocumentElements() {
    return array();
  }
  
  static function insertTextElement($element, $name, $value, $attrs = null) {
    $root = $element->ownerDocument;
    $tag = $root->createElement($name);
    $value = utf8_encode($value);
    $value_elt = $root->createTextNode($value);
    $tag->appendChild($value_elt);
    $element->appendChild($tag);
    
    if ($attrs) {
      foreach ($attrs as $key => $value) {
        $att = $root->createAttribute($key);
        $value_att = $root->createTextNode($value);
        $att->appendChild($value_att);
        $tag->appendChild($att);
      }
    }
  }
}
