<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

/**
 * Description
 */
class CPop{

  public $source;

  public $_mailbox; //ressource id
  public $_server;
  public $_mailbox_info;

  public $_parts = array();

  public $content = array (
    "text" => array(
      "plain"       => null,
      "html"        => null,
      "is_apicrypt" => null
    ),
    "attachments" => array()
  );


  /**
   * constructor
   *
   * @param object $source sourcePOP
   */
  function __construct($source) {
    //stock the source
    $this->source = $source;

    if (!function_exists("imap_open")) {
      CModelObject::error("FE-IMAP-support-not-available");
    }

    //initialise open TIMEOUT
    imap_timeout(1, $this->source->timeout);


    //lets create the string for stream
    $type = ($this->source->type == "pop3")?"/".$this->source->type:"";
    $ssl  = ($this->source->auth_ssl == "SSL/TLS")?"/ssl/novalidate-cert":"/notls";
    $port = ($this->source->port) ? ":".$this->source->port: "";
    return $this->_server = "{".$this->source->host.$port.$type.$ssl."}";
  }

  /**
   * Open the remote mailbox
   *
   * @return ressource|bool
   */
  function open() {
    $port = ($this->source->port) ? ":".$this->source->port : "";
    $protocole = ($this->source->auth_ssl) ? "https://" : "http://" ;
    $url = $protocole.$this->source->host.$port;

    if (!isset($this->_server)) {
      CAppUI::stepAjax("CPop-error-notInitiated", UI_MSG_ERROR);
      return false;
    }

    mbLog($this->_server);
    //@TODO: fix this
    /*if (!url_exists($url)) {
      //CAppUI::stepAjax("CPop-server-unreachable", UI_MSG_ALERT);
      mbLog("server-not-reachable");
      return false;
    }*/

    $password = $this->source->getPassword();
    $this->_mailbox = @imap_open($this->_server, $this->source->user, $password, 0, 0);
    if ($this->_mailbox === false ) {
      return false;
    }
    //get the basics
    $this->_mailbox_info = @imap_check($this->_mailbox);
    return $this->_mailbox;
  }

  /**
   * return the mailbox check : new msgs
   *
   * @return object
   */
  function check() {
      $this->_mailbox_info = @imap_check($this->_mailbox);
    return $this->_mailbox_info;
  }


  /**
   * search for a mail using specific string
   *
   * @param string $string TEST
   * @param bool   $uid    TEST
   *
   * @return array
   */
  function search($string,$uid=true) {
    if (!is_string($string)) {
      CAppUI::stepAjax("CPop-error-search-notString", UI_MSG_ERROR);
    }
    if ($uid) {
      return imap_sort($this->_mailbox, SORTDATE, 1, SE_UID, $string);
    }
    return imap_sort($this->_mailbox, SORTDATE, 1, 0, $string);
  }

  /**
   * get the header of the mail
   *
   * @param int $id mail id
   *
   * @return array
   */
  function header($id) {
    return imap_fetch_overview($this->_mailbox, $id, FT_UID);
  }

  /**
   * get the structure of the mail (parts)
   *
   * @param int $id uid of the mail
   *
   * @return object
   */
  function structure($id) {
    return imap_fetchstructure($this->_mailbox, $id, FT_UID);
  }

  /**
   * send a flag to the server
   *
   * @param int    $id   test
   * @param string $flag test
   *
   * @return bool
   */
  function setFlag($id, $flag) {
    return imap_setflag_full($this->_mailbox, $id, $flag, ST_UID);
  }


  /**
   * Open a part of an email
   *
   * @param integer $msgId  test
   * @param string  $partId test
   * @param boolean $uid    test
   *
   * @return object $object
   */
  function openPart($msgId,$partId,$uid=true) {
    if ($uid) {
      return imap_fetchbody($this->_mailbox, $msgId, $partId, FT_UID | FT_PEEK);
    }
    return imap_fetchbody($this->_mailbox, $msgId, $partId, FT_PEEK);
  }

  /**
   * Get the body of the mail : HTML & plain text if available!
   *
   * @param int  $mail_id     test
   * @param bool $structure   test
   * @param bool $part_number test
   * @param bool $only_text   test
   *
   * @return array
   */
  function getFullBody($mail_id, $structure = false, $part_number = false, $only_text = false) {

    if (!$structure) {
      $structure = $this->structure($mail_id);
    }

    if ($structure) {
      if (!isset($structure->parts) && !$part_number) {  //plain text only, no recursive
        $part_number = "1";
      }
      if (!$part_number) {
        $part_number = "0";
      }

      switch ($structure->type) {
        case 0: //text or html
          if ($structure->subtype == "PLAIN") {
            $this->content["text"]["plain"] = self::decodeMail($structure->encoding, self::openPart($mail_id, $part_number), $structure);
          }

          if ($structure->subtype == "HTML") {
            $this->content["text"]["html"] = self::decodeMail($structure->encoding, self::openPart($mail_id, $part_number), $structure);
          }

          //apycript
          if (stripos($this->content["text"]["plain"], '$APICRYPT') !== false) {
            $this->content["text"]["is_apicrypt"] = "plain";
          }

          if (stripos($this->content["text"]["html"], '$APICRYPT') !== false) {
            $this->content["text"]["is_apicrypt"] = "html";
          }

          break;
        case 1: //multipart, alternatived
          while (list($index, $sub_structure) = each($structure->parts)) {
            if ($part_number) {
              $prefix = $part_number.'.';
            }
            else {
              $prefix = null;
            }
            self::getFullBody($mail_id, $sub_structure, $prefix . ($index + 1));
          }
          break;

        case 2:     //message
        case 3:     //application
        case 4:     //audio
        case 5:     //images
        case 6:     //video
        default:    //other
          if ($only_text) {
            $attach  = new CMailAttachments();
            $attach->loadFromHeader($structure);
            $attach->loadContentFromPop($this->openPart($mail_id, $part_number));

            //inline attachments
            if ($attach->id && $attach->subtype!="SVG+XML") {
              $id= 'cid:'.str_replace(array("<",">"), array("",""), $attach->id);
              $url = "data:image/$attach->subtype|strtolower;base64,".$attach->content;
              $this->content["text"]["html"] = str_replace($id, $url, $this->content["text"]["html"]);
            }
            else {  //attachments below
              $this->content["attachments"][] = $attach;
            }
          }
      }
    }
    return $this->content;
  }

  /**
   * Get the attachments of a mail_id
   *
   * @param int  $mail_id     id of the mail (warning, UID and not ID)
   * @param bool $structure   structure
   * @param bool $part_number part number
   * @param bool $part_temp   part for get the part later
   *
   * @return mixed
   */
  function getListAttachments($mail_id, $structure = false, $part_number = false, $part_temp=false) {

    if (!$structure) {
      $structure = $this->structure($mail_id);
    }

    if ($structure) {
      if (!isset($structure->parts) && !$part_number) {  //plain text only, no recursive
        $part_number = "1";
      }
      if (!$part_number) {
        $part_number = "0";
      }

      switch ($structure->type) {
        case 1: //multipart, alternatived
          while (list($index, $sub_structure) = each($structure->parts)) {
            if ($part_number !== false) {
              $prefix = $part_number.'.';
            }
            else {
              $prefix = null;
            }

            if ($part_temp !== false) {
              $prefix_temp = $part_temp.'.';
            }
            else {
              $prefix_temp = null;
            }

            self::getListAttachments($mail_id, $sub_structure, $prefix . ($index + 1), $prefix_temp.$index);
          }
          break;

        case 2:     //message
        case 3:     //application
        case 4:     //audio
        case 5:     //images
        case 6:     //video
          $attach = new CMailAttachments();
          $attach->loadFromHeader($structure);
          $attach->part = $part_temp;

          //inline attachments
          $this->content["attachments"][] = $attach;
          break;
        default:    //other
      }
    }
    return $this->content["attachments"];
  }

  /** TOOLS **/

  /**
   * get the right decoding string from mail structure
   *
   * @param int         $encoding  encoding number (from structure)
   * @param string      $text      the text to decode
   * @param object|null $structure an mail structure for additionnal decoding
   *
   * @return string
   */
  static function decodeMail($encoding, $text, $structure=null) {
    $retour = null;
    switch ($encoding) {
      /* 0 : 7 bit / 1 : 8 bit / 2 ; binary / 5 : other  => default  */
      case(3):  //base64
        $retour = imap_base64($text);
        break;

      case(4):
        $retour = imap_qprint($text);
        break;

      default:
        $retour = $text;
        break;
    }

    //Hack for bad defined encoding
    if (!empty($structure->parameters)) {
      for ($k = 0, $l = count($structure->parameters); $k < $l; $k++) {
        $attribute = $structure->parameters[$k];
        if ($attribute->attribute == 'CHARSET' && strtoupper($attribute->value) == 'UTF-8') {
          return utf8_decode($retour);
        }
      }
    }
    return $retour;
  }


  /**
   * check if imap_lib exist
   *
   * @return null
   */
  static function checkImapLib() {
    if (!function_exists("imap_open")) {
      CMbObject::error("no-imap-lib");
      CAppUI::stepAjax("no-imap-lib", UI_MSG_ERROR);
      return false;
    }
    return true;
  }

  /**
   * close the stream
   *
   * @return bool
   */
  function close() {
    return imap_close($this->_mailbox);
  }


}