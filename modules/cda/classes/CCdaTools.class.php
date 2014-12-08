<?php 
/**
 * $Id$
 *
 * Classe permettant de manipuler le document CDA
 * 
 * @package    Mediboard
 * @subpackage CDA
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */
/**
 * Outils pour le CDA
 */
class CCdaTools {

  /**
   * Retourne un tableau issu du jeux de valeur
   *
   * @param String $name         Nom du jeux de valeurs
   * @param Bool   $array_unique Cr�ation d'un tableau avec "codeSytem^Code" comme cl�
   *
   * @return array
   */
  static function loadJV($name, $array_unique=false) {
    $path = "modules/cda/resources/jeuxDeValeurs/$name";
    $dom = new CMbXMLDocument();
    $dom->load($path);
    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("svs", "urn:ihe:iti:svs:2008");
    $nodes = $xpath->query("//svs:Concept");

    $jeux_valeurs = array();
    foreach ($nodes as $_node) {
      $code        = $xpath->queryAttributNode(".", $_node, "code");
      $displayName = $xpath->queryAttributNode(".", $_node, "displayName");
      $codeSystem  = $xpath->queryAttributNode(".", $_node, "codeSystem");
      if ($array_unique) {
        $jeux_valeurs["$codeSystem^$code"] = $displayName;
        continue;
      }
      $jeux_valeurs[] = array("code" => $code,
                              "displayName" => $displayName,
                              "codeSystem" => $codeSystem);
    }

    return $jeux_valeurs;
  }

  /**
   * Retourne une entr�e dans un jeux de valeur
   *
   * @param String $name Nom du jeux de valeur
   * @param String $code Identifiant de la valeur voulut
   *
   * @return array
   */
  static function loadEntryJV($name, $code) {
    $path = "modules/cda/resources/jeuxDeValeurs/$name";
    $dom = new CMbXMLDocument();
    $dom->load($path);
    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("svs", "urn:ihe:iti:svs:2008");
    $node = $xpath->queryUniqueNode("//svs:Concept[@code='$code']");
    $valeur = array("code" => $xpath->queryAttributNode(".", $node, "code"),
                          "displayName" => $xpath->queryAttributNode(".", $node, "displayName"),
                          "codeSystem" => $xpath->queryAttributNode(".", $node, "codeSystem"));

    return $valeur;
  }

  /**
   * Permet de r�cup�rer les attributs d'un noeud xml sous forme de tableau
   *
   * @param DOMNode $node Node
   *
   * @return array[nom_attribut]
   */
  static function parseattribute($node) {
    $tabAttribute = array();
    foreach ($node->attributes as $_attribute) {
      $tabAttribute[$_attribute->nodeName] = utf8_decode($_attribute->nodeValue);
    }
    return $tabAttribute;
  }

  /**
   * Permet de faire un parcours en profondeur du document
   * et renvoi le document sous forme d'un tableau
   *
   * @param DOMNode $node Node
   *
   * @return array
   */
  static function parsedeep ($node) {
    /**
     * On renseigne les informations de notre noeud dans un tableau
     */
    $tabNode = array("name" => $node->localName,
                     "child" => array(),
                     "data" => utf8_decode($node->nodeValue),
                     "attribute" => self::parseattribute($node));
    /**
     * On v�rifie que l'�l�ment est un DOMElement pour �viter les noeuds Text et autres
     */
    if ($node instanceof DOMElement) {
      /**
       * On parcours les fils de notre noeud courant
       */
      foreach ($node->childNodes as $_childNode) {
        /**
         * On v�rifie que notre noeud poss�dent un nom pour �viter les noeud contenant
         * des commentaires
         */
        if ($_childNode->localName) {
          /**
           * On affecte � notre tableau ces fils en appelant notre fonction
           */
          $tabNode["child"][] = self::parsedeep($_childNode);
        }
      }
    }
    /**
     * Retourne le tableau de notre noeud courant dans le child du noeud parent
     * ou retourne le tableau complet � la fin du processus
     */
    return $tabNode;
  }

  /**
   * Permet de remplir la variable $_contain avec la structure du document
   *
   * @param String $message message
   *
   * @return void|array
   */
  static function parse ($message) {
    $result = array();
    $dom = new CMbXMLDocument("UTF-8");

    $returnErrors   = $dom->loadXMLSafe(utf8_encode($message), null, true);
    $tabErrors      = array_filter(explode("\n", $returnErrors));
    $returnErrors   = $dom->schemaValidate("modules/cda/resources/CDA.xsd", true, false);
    $tabErrors      = array_merge($tabErrors, array_filter(explode("\n", $returnErrors)));
    $validate       = array_unique($tabErrors);

    if ($validate[0] != "1") {
      $contain = null;
      return;
    }

    $validateSchematron = self::validateSchematron($message);

    if ($validate[0] === "1" && !CMbArray::get($validate, 1)) {
      $validate = array();
    }

    $contain = self::parsedeep($dom->documentElement);
    $result["validate"]           = $validate;
    $result["validateSchematron"] = $validateSchematron;
    $result["contain"]            = $contain;

    return $result;
  }

  /**
   * Valide le CDA
   *
   * @param String $cda                   CDA
   * @param Bool   $schematron_validation Validation par sch�mtron
   *
   * @throws CMbException
   * @return void
   */
  static function validateCDA($cda, $schematron_validation = true) {
    $dom = new CMbXMLDocument("UTF-8");

    $returnErrors = $dom->loadXMLSafe($cda, null, true);
    if ($returnErrors !== true) {
      throw new CMbException("Erreur lors de la conception du document");
    }

    $validateSchematron = null;
    if ($schematron_validation) {
      $returnErrors = $dom->schemaValidate("modules/cda/resources/CDA.xsd", true, false);
      $validateSchematron = self::validateSchematron($cda);
    }

    if ($returnErrors !== true || $validateSchematron) {
      mbLog($returnErrors);
      throw new CMbException("Probl�me de conformit�, v�rifiez les informations n�cessaires pour le CDA");
    }
  }

  /**
   * Affiche le message au format xml
   *
   * @param String $message message
   *
   * @return String
   */
  static function showxml($message) {
    return CMbString::highlightCode("xml", $message);
  }

  /**
   * Fonction de cr�ation des classes voc
   *
   * @return string
   */
  static function createClass() {
    //On charge le XSD contenant le vocabulaire
    $dom = new CMbXMLDocument("UTF-8");
    $dom->load("modules/cda/resources/voc.xsd");

    //On enregistre le namespace utiliser dans le XSD
    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");

    //On recherche tous les simpleTypes et les complexTypes
    $nodeList = $xpath->query("//xs:complexType|xs:simpleType");
    $listvoc = array();

    //On parcours la liste que retourne la requ�te XPATH
    foreach ($nodeList as $_node) {
      //On r�cup�re le nom du type
      $name = $xpath->queryAttributNode(".", $_node, "name");

      //On r�cup�re la documentation li� au type
      $documentation = $xpath->queryTextNode(".//xs:documentation", $_node);

      //On r�cup�re les unions du type
      $union = $xpath->queryUniqueNode(".//xs:union", $_node);

      //On v�rifie l'existence d'union
      if ($union) {
        $union = $xpath->queryAttributNode(".", $union, "memberTypes");
      }
      //on r�cup�re les �num�rations
      $enumeration = $xpath->query(".//xs:enumeration", $_node);
      $listEnumeration = array();

      //on met chaque enumeration dans un tableau
      foreach ($enumeration as $_enumeration) {
        array_push($listEnumeration, $xpath->queryAttributNode(".", $_enumeration, "value"));
      }
      //On cr�� un tableau rassemblant toutes les informations concernant un voc
      $listvoc[] = array( "name" => $name,
                          "documentation" => $documentation,
                          "union" => $union,
                          "enumeration" => $listEnumeration);
    }

    //On met le lien du dossier contenant les voc
    $cheminBase = "modules/cda/classes/datatypes/voc/";

    //On parcours les voc
    foreach ($listvoc as $_voc) {
      //On affecte comme nom de fichier CCDA et le nom du voc
      $nameFichier = "CCDA".$_voc["name"].".class.php";
      $smarty = new CSmartyDP();
      $smarty->assign("documentation", $_voc["documentation"]);
      $smarty->assign("name", $_voc["name"]);
      $smarty->assign("enumeration", self::formatArray($_voc["enumeration"]));
      $union = self::formatArray(array());
      //on v�rifie la pr�sence d'union
      if (CMbArray::get($_voc, "union")) {
        $union = self::formatArray(explode(" ", $_voc["union"]));
      }
      $smarty->assign("union", $union);
      //on r�cup�re la classe former
      $data = $smarty->fetch("defaultClassVoc.tpl");

      //on cr�� le fichier
      file_put_contents($cheminBase.$nameFichier, str_replace("\r\n", "\n", $data));
    }

    return true;
  }

  /**
   * Permet de formater le tableau en entr�
   *
   * @param array $array array
   *
   * @return mixed
   */
  static function formatArray($array) {
    return preg_replace('/\)$/', "  )", preg_replace("/\d+ => /", "  ", var_export($array, true)));
  }

  /**
   * Valide le xml avec le sch�matron
   *
   * @param String $xml String
   *
   * @return String[]
   */
  static function validateSchematron($xml) {
    $baseDir = dirname(__FILE__)."/../resources";
    $cmd     = escapeshellarg("java");

    $styleSheet = "$baseDir/schematron/CI-SIS_StructurationCommuneCDAr2.xsl";

    $temp = tempnam("temp", "xml");
    file_put_contents($temp, $xml);

    $cmd = $cmd." -jar $baseDir/saxon9he.jar -s:$temp -xsl:$styleSheet";

    $processorInstance = proc_open($cmd, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    $processorResult = stream_get_contents($pipes[1]);
    $processorErrors = stream_get_contents($pipes[2]);
    proc_close($processorInstance);

    unlink($temp);

    $dom = new CMbXMLDocument();
    $dom->loadXMLSafe($processorResult);
    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("svrl", "http://purl.oclc.org/dsdl/svrl");
    $nodeList = $xpath->query("//svrl:failed-assert");

    $tabErrors = array();
    if ($processorErrors) {
      $tabErrors[] = array("error" => $processorErrors,
                           "location" => "System");
    }

    foreach ($nodeList as $_node) {
      $tabErrors[] = array("error" => utf8_decode($_node->textContent),
                           "location" => $xpath->queryAttributNode(".", $_node, "location"));
    }

    return $tabErrors;
  }

  /**
   * Permet de de retourner la portion xml du noeud choisi par le nom dans le sch�ma sp�cifi�
   *
   * @param String $name   String
   * @param String $schema String
   *
   * @return string
   */
  static function showNodeXSD($name, $schema) {
    $dom = new CMbXMLDocument();
    $dom->load($schema);

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");
    $node = $xpath->queryUniqueNode("//xs:simpleType[@name='".$name."']|//xs:complexType[@name='".$name."']");
    return $dom->saveXML($node);
  }


  /**
   * Permet de lancer toutes les cr�ations des sch�ma de test
   *
   * @return bool
   */
  static function createAllTestSchemaClasses() {
    //URI du fichier
    $nameFile = "modules/cda/resources/TestClasses.xsd";
    $glob = "modules/cda/classes/datatypes/{voc,base,datatype}/*.class.php";
    self::createTestSchemaClasses($nameFile, $glob);

    $nameFile = "modules/cda/resources/TestClassesCDA.xsd";
    $glob = "modules/cda/classes/classesCDA/*.class.php";
    self::createTestSchemaClasses($nameFile, $glob);

    return true;
  }

  /**
   * Permet de cr�er le xsd contenant la d�finition d'�l�ment pour tester les types
   *
   * @param String $nameFile String
   * @param String $glob     String
   *
   * @return bool
   */
  static function createTestSchemaClasses($nameFile, $glob) {

    $dom = new CMbXMLDocument("UTF-8");
    //On enregistre pas les nodeText vide
    $dom->preserveWhiteSpace = false;
    $dom->load($nameFile);

    //on r�cup�re tous les �lements
    $xpath = new CMbXPath($dom);
    $nodeList = $xpath->query("//xs:element");

    //on supprime tous les �lements du fichier
    foreach ($nodeList as $_node) {
      $dom->documentElement->removeChild($_node);
    }

    //On sauvegarde le fichier sans �l�ment
    file_put_contents($nameFile, $dom->saveXML());

    //on r�cup�re tous les class existant dans les dossier voc, base, datatype
    $file = glob($glob, GLOB_BRACE);

    /**
     * Pour chacun des fichier on cr�� un �l�ment avec sont type correspondant
     */
    foreach ($file as $_file) {
      //on cr�� l'�l�ment
      $element = $dom->addElement($dom->documentElement, "xs:element");
      //on formatte le nom du fichier
      $_file = CMbArray::get(explode(".", $_file), 0);
      $_file = substr($_file, strrpos($_file, "/")+1);
      //on cr�� une instance de la classe
      /** @var CCDAClasseBase $instanceClass */
      $instanceClass = new $_file;
      //on r�cup�re le nom quisera �gale au type et au nom de l'�l�ment
      $_file = $instanceClass->getNameClass();
      //On ajoute les attribut type et nom
      $dom->addAttribute($element, "name", $_file);
      $dom->addAttribute($element, "type", $_file);
      //On ajoute un saut de ligne dans le sch�ma
      $dom->documentElement->appendChild($dom->createTextNode("\n"));
    }
    //on sauvegarde le fichier
    file_put_contents($nameFile, $dom->saveXML());

    return true;
  }

  /**
   * Permet la cr�ation de la synth�se des tests
   *
   * @param array $result array
   *
   * @return array
   */
  static function syntheseTest($result) {
    /**
     * on cr�� le tableau qui contiendra le nombre total de test
     * le nombre de succ�s et les classes qui sont en erreur
     */
    $resultSynth = array("total" => 0,
                         "succes" => 0,
                         "erreur" => array());
    //on parcours le tableau des tests
    foreach ($result as $keyClass => $valueClass) {
      //on parcours les r�sultats et on compte les r�sultats
      foreach ($valueClass as $_test) {
        if ($_test["resultat"] === $_test["resultatAttendu"]) {
          $resultSynth["succes"]++;
        }
        else {
          array_push($resultSynth["erreur"], $keyClass);
        }
        $resultSynth["total"]++;
      }
    }
    $resultSynth["erreur"] = array_unique($resultSynth["erreur"]);
    return $resultSynth;
  }

  /**
   * Permet de lancer les tests de toutes les classes renseign�es
   *
   * @param $test $test String
   *
   * @return array
   */
  static function createTest($test) {
    $path = "modules/cda/classes/datatypes/$test/*.class.php";

    if ($test == "CDA") {
      $path = "modules/cda/classes/classesCDA/*.class.php";
    }

    $file = glob($path, GLOB_BRACE);

    $result = array();
    foreach ($file as $_file) {
      $_file = CMbArray::get(explode(".", $_file), 0);
      $_file = substr($_file, strrpos($_file, "/")+1);
      /** @var CCDA_Datatype_Base $class */
      $class = new $_file;
      $result[$class->getNameClass()] = $class->test();
    }
    return $result;
  }

  /**
   * Retourne tous les types pr�sent dans le sch�ma renseign�
   *
   * @param String $schema String
   *
   * @return array
   */
  static function returnType($schema) {
    $dom = new CMbXMLDocument("UTF-8");
    $dom->load($schema);

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");
    $nodelist = $xpath->query("//xs:simpleType[@name]|//xs:complexType[@name]");
    $listName =  array();
    foreach ($nodelist as $_node) {
      array_push($listName, $xpath->queryAttributNode(".", $_node, "name"));
    }

    return $listName;
  }

  /**
   * Retourne les classes manqantes
   *
   * @return array
   */
  static function missclass() {
    /**
     * On r�cup�re les types des diff�rents XSD
     */
    $listAllType = self::returnType("modules/cda/resources/datatypes-base.xsd");
    $voc         = self::returnType("modules/cda/resources/voc.xsd");
    $datatype    = self::returnType("modules/cda/resources/datatypes.xsd");
    $listAllType = array_merge($listAllType, $voc, $datatype);
    $file = glob("modules/cda/classes/{classesCDA,datatypes}/{voc,base,datatype}/*.class.php", GLOB_BRACE);

    $result = array();
    /**
     * On parcours les classes existantes
     */
    foreach ($file as $_file) {
      $_file = CMbArray::get(explode(".", $_file), 0);
      $_file = substr($_file, strrpos($_file, "/")+1);
      /** @var CCDAClasseBase $class */
      $class = new $_file;
      array_push($result, $class->getNameClass());
    }
    //on retourne la diff�rence entre le tableau des types XSd et le tableau des classes existantes
    return array_diff($listAllType, $result);
  }

  /**
   * Permet de nettoyer le XSD (suppression des minOccurs=0 maxOccurs=0 et des abtract)
   * libxml ne g�re pas la prohibition des �l�ment avec maxOccurs = 0;
   * les abtracts emp�che l'instanciation des classes
   *
   * @return bool
   */
  static function clearXSD() {
    $pathSource = "modules/cda/resources/datatypes-base_original.xsd";
    $pathDest   = "modules/cda/resources/datatypes-base.xsd";

    $copyFile = copy($pathSource, $pathDest);

    if (!$copyFile) {
      return false;
    }

    $dom = new CMbXMLDocument("UTF-8");
    $dom->load($pathDest);

    $xpath = new CMbXPath($dom);
    $nodeList = $xpath->query("//xs:complexType[@abstract]|xs:simpleType[@abstract]");

    foreach ($nodeList as $_node) {
      /** @var DOMElement $_node */
      $_node->removeAttribute("abstract");
    }

    $nodeList = $xpath->query("//xs:element[@maxOccurs=\"0\"]");
    foreach ($nodeList as $_node) {
      $_node->parentNode->removeChild($_node);
    }
    file_put_contents($pathDest, $dom->saveXML());

    return true;
  }

  /**
   * Permet de cr�er les props pour une classe
   *
   * @param DOMNodeList $elements    DOMNodeList
   * @param Array       $tabVariable Array
   * @param Array       $tabProps    Array
   *
   * @return Array
   */
  static function createPropsForElement($elements, $tabVariable, $tabProps) {
    $nameAttribute = "";
    $typeAttribute = "";
    foreach ($elements as $_element) {
      $attributes = $_element->attributes;
      $typeXML = "xml|element";

      if ($_element->nodeName == "xs:attribute") {
        $typeXML = "xml|attribute";
      }

      $elementProps = "";
      $maxOccurs    = false;
      $minOccurs    = false;
      foreach ($attributes as $_attribute) {
        switch ($_attribute->nodeName) {
          case "name":
            $nameAttribute = $_attribute->nodeValue;
            break;
          case "type":
            $name = str_replace(".", "_", $_attribute->nodeValue);
            if (ctype_lower($name)) {
              $name = "_base_$name";
            }
            $typeAttribute = $name;
            $elementProps .= "CCDA$name $typeXML";
            break;
          case "minOccurs":
            $minOccurs = true;
            if ($_attribute->nodeValue > 0) {
              $minOccurs = false;
              $elementProps .= " min|$_attribute->nodeValue";
            }
            break;
          case "maxOccurs":
            if ($_attribute->nodeValue == "unbounded") {
              $maxOccurs = true;
            }
            else {
              if ($_attribute->nodeValue > 1) {
                $maxOccurs = true;
                $elementProps .= " max|$_attribute->nodeValue";
              }
            }
            break;
          case "default":
            $elementProps .= " default|$_attribute->nodeValue";
            break;
          case "use":
            if ($_attribute->nodeValue == "required") {
              $elementProps .= " required";
            }
            break;
          case "fixed":
            $elementProps .= " fixed|$_attribute->nodeValue";
            break;
        }
      }
      $tabVariable[$nameAttribute]["type"] = $typeAttribute;
      $tabVariable[$nameAttribute]["max"] = $maxOccurs;
      if (!$maxOccurs && $typeXML == "xml|element") {
        if ($minOccurs) {
          $elementProps .= " max|1";
        }
        else {
          $elementProps .= " required";
        }
      }
      $tabProps[$nameAttribute] = $elementProps;
    }
    return array($tabVariable, $tabProps);
  }

  /**
   * fonction permettant de cr��r la structure principal des classes d'un XSD
   *
   * @return bool
   */
  static function createClassFromXSD() {
    $pathXSD = "modules/cda/resources/POCD_MT000040.xsd";
    $pathDir = "modules/cda/classes/classesCDA/classesGenerate/";
    $dom = new CMbXMLDocument("UTF-8");
    $dom->load($pathXSD);

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("xs", "http://www.w3.org/2001/XMLSchema");
    $nodeList = $xpath->query("//xs:complexType[@name] | //xs:simpleType[@name]");

    foreach ($nodeList as $_node) {
      $tabVariable = array();
      $tabProps    = array();
      /** @var DOMElement $_node */
      $elements       = $_node->getElementsByTagName("element");
      $nodeAttributes = $_node->getElementsByTagName("attribute");
      $nameNode       = $xpath->queryAttributNode(".", $_node, "name");
      $nameNode       = str_replace(".", "_", $nameNode);

      list($tabVariable, $tabProps) = self::createPropsForElement($elements, $tabVariable, $tabProps);
      list($tabVariable, $tabProps) = self::createPropsForElement($nodeAttributes, $tabVariable, $tabProps);

      $smarty = new CSmartyDP();
      $smarty->assign("name"     , $nameNode);
      $smarty->assign("variables", $tabVariable);
      $smarty->assign("props"    , $tabProps);

      $data = $smarty->fetch("defaultClassCDA.tpl");

      file_put_contents($pathDir."CCDA".$nameNode.".class.php", $data);
    }
    return true;
  }

  /**
   * Generate a PDFA with a PDF
   *
   * @param String $path_input path
   *
   * @return String|null
   * @throws CMbException
   */
  static function generatePDFA($path_input) {
    $command_path = CAppUI::conf("cda path_ghostscript");
    $command_path = $command_path ? escapeshellarg($command_path) : "gs";
    $path_output = tempnam("temp", "pdf");
    $cmd = "$command_path -dPDFA -dBATCH -dNOPAUSE -dUseCIEColor -sProcessColorModel=DeviceCMYK -sDEVICE=pdfwrite -sOutputFile=$path_output -dPDFACompatibilityPolicy=1 $path_input";
    $processorInstance = proc_open($cmd, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    $processorErrors = stream_get_contents($pipes[2]);
    proc_close($processorInstance);
    if ($processorErrors) {
      throw new CMbException($processorErrors);
    }
    return $path_output;
  }
}