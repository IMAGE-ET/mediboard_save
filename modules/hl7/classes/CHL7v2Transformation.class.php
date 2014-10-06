<?php

/**
 * HL7v2 transformation
 *
 * @category HL7v2
 * @package  �Mediboard
 * @author   � SARL OpenXtrem <dev@openxtrem.com>
 * @license  �GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  �SVN: $Id:$
 * @link     � � http://www.mediboard.org
 */

/**
 * Description
 */
class CHL7v2Transformation {
  protected $message;
  protected $messageName;

  function __construct($version, $extension, $messageName) {
    $hl7v2 = new CHL7v2Message();
    $hl7v2->version = $version;
    $hl7v2->extension = $extension;

    $this->message     = $hl7v2;
    $this->messageName = $messageName;
  }

  function getTree() {
    $message_schema = $this->message->getSchema("message", $this->messageName);

    $xpath = new CHL7v2MessageXPath($message_schema);

    $segment = $xpath->queryUniqueNode("//segments");

    $tree = array();
    $this->readMessageSchema($segment, $tree);

    return $tree;
  }

  function readMessageSchema(DOMElement $element, &$tree) {
    foreach ($element->childNodes as $_element) {
      /** @var DOMElement $_element */
      switch ($_element->nodeName) {
        case "segment":
          $segment_schema = $this->message->getSchema("segment", $_element->nodeValue);
          $xpath = new CHL7v2MessageXPath($segment_schema);

          $children = array();
          $fields = $xpath->query("//field");
          foreach ($fields as $_field) {
            $segment_name = $xpath->queryTextNode("name", $_field);
            $segment_datatype = $xpath->queryTextNode("datatype", $_field);

            $_fields = array();

            $field_schema = $this->message->getSchema("composite", $segment_datatype);
            $field_xpath = new CHL7v2MessageXPath($field_schema);

            $components = $field_xpath->query("//field");
            foreach ($components as $_component) {
              $component_name     = $field_xpath->queryTextNode("name"    , $_component);
              $component_datatype = $field_xpath->queryTextNode("datatype", $_component);

              $fullpath_component = "$_element->nodeValue/$segment_name/$component_name";

              $_datatypes = array();
              $this->readDataTypeSchema($_datatypes, $segment_datatype, $fullpath_component);

              $_fields[] = array(
                "name"      => $component_name,
                "fullpath"  => $fullpath_component,
                "forbidden" => $_field->getAttribute("forbidden") == "true",
                "datatype"  => $component_datatype,
                "children"  => $_datatypes
              );
            }

            $fullpath_segment = "$_element->nodeValue/$segment_name";

            $children[] = array(
              "name"      => $segment_name,
              "fullpath"  => $fullpath_segment,
              "forbidden" => $_field->getAttribute("forbidden") == "true",
              "datatype"  => $segment_datatype,
              "children"  => $_fields,
            );
          }

          $tree[] = array(
            "type"      => $_element->nodeName,
            "name"      => $_element->nodeValue,
            "fullpath"  => $_element->nodeValue,
            "forbidden" => $_element->getAttribute("forbidden") == "true",
            "children"  => $children,
          );
          break;

        case "group":
          $subtree = array();
          $this->readMessageSchema($_element, $subtree);

          $tree[] = array(
            "type"     => $_element->nodeName,
            "name"     => $_element->getAttribute("name"),
            "fullpath" => $_element->getAttribute("name"),
            "children" => $subtree,
          );
          break;

        default:
      }
    }
  }

  function readDataTypeSchema(&$_datatypes, $datatype_name, $fullpath_component) {
    if (array_key_exists($datatype_name, CHDataType::$typesMap)) {
      return array();
    }

    $datatype_schema = $this->message->getSchema("composite", $datatype_name);

    $datatype_xpath  = new CHL7v2MessageXPath($datatype_schema);
    $fields          = $datatype_xpath->query("//field");

    foreach ($fields as $_field) {
      $_datatype_name      = $datatype_xpath->queryTextNode("name"    , $_field);
      $_component_datatype = $datatype_xpath->queryTextNode("datatype", $_field);

      $children = array();
      $this->readDataTypeSchema($children, $_component_datatype, $fullpath_component);

      $_datatypes[] = array(
        "name"     => $_datatype_name,
        "fullpath" => "$fullpath_component/$_datatype_name",
        "datatype" => $_component_datatype,
        "children" => $children
      );
    }
  }
}