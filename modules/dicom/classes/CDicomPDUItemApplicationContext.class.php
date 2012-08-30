<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents an Application context PDU Item
 */
class CDicomPDUItemApplicationContext extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = 0x10;
  
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * The transfer syntax name, coded as a UID
   * 
   * @var string
   */
  var $name = null;
  
  /**
   * Decode the Transfer Syntax
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodeItem(CDicomStreamReader $stream_reader) {
    // On passe le 2�me octet, r�serv� par Dicom et �gal � 00
    $stream_reader->skip(1);
    $this->length = $stream_reader->readUnsignedInt16();
    $this->name = $stream_reader->readUID($this->length);
  }
  
  /**
   * Encode the Transfer Syntax
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   *  
   * @return null
   */
  function encodeItem(CDicomStreamWriter $stream_writer) {
    
  }

  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    return "<ul><li>Item type : $this->type</li><li>Item length : $this->length</li><li>Application context name : $this->name</li></ul>";
  }
}
?>