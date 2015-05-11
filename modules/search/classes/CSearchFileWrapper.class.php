<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

/**
 * Class CSearchFileWrapper
 * Manage in order to index CFile
 */
class CSearchFileWrapper {
  public $_fichier;
  public $_file_id;

  /**
   * Constructor
   *
   * @param string  $fichier Le fichier dont on doit extraire le contenu
   * @param integer $file_id L'id du fichier dont on doit extraire le contenu
   *
   */
  function __construct ($fichier, $file_id) {
    $this->_fichier = $fichier;
    $this->_file_id = $file_id;
  }

  /**
   * M�thode permettant de d�marrer le jar d'apache tika afin d'en extraire le contenu souhait�
   *
   * @return string
   */
  function run () {

    $conf_host = trim(CAppUI::conf("search tika_host"));
    $conf_port = trim(CAppUI::conf("search tika_port"));
    $client = new CHTTPClient("http://$conf_host:$conf_port/tika");
    $client->header = array("\"Accept: text/plain\"");
    $content = $client->putFile($this->_fichier);

    return $content;
  }

  /**
   * M�thode permettant de tester si le service d'extraction Tika est actif
   *
   * @return string
   */
  function loadTikaInfos () {

    $conf_host = trim(CAppUI::conf("search tika_host"));
    $conf_port = trim(CAppUI::conf("search tika_port"));
    $client = new CHTTPClient("http://$conf_host:$conf_port/tika");

    return $client->get();
  }

}