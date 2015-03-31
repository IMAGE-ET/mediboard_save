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
   * @param string $option l'option sp�cifi�e pour l'extraction.
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
   * M�thode permettant la r�cup�ration du contenu principal du document. (body)
   *
   * @return string
   */
  function getPlainText () {
    $option = "--text-main --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * M�thode permettant la r�cup�ration du contenu global du document. (en-t�te, corp, pied-de-page)
   *
   * @return string
   */
  function getText () {
    $option = "--text --encoding=utf-8";
    return $this->run($option);
  }

  /** M�thode permettant de r�cup�rer le contenu du document sous format xml valide.
   *
   * @return string
   */
  function getXHTML () {
    $option = "--xml --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * M�thode permettant de r�cup�rer le contenu du document sous format HTML.
   *
   * @return string
   */
  function getHTML () {
    $option = "--html --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * M�thode permettant de r�cup�rer les m�tadonn�es du document.
   *
   * @return string
   */
  function getMetadata () {
    $option = "--metadata --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * M�thode permettant de r�cup�rer les m�tadonn�es du document au format JSON.
   *
   * @return string
   */
  function getMetadataJson () {
    $option = "--json --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * M�thode permettant de r�cuperer les m�tadonn�es du document au format Xmp
   *
   * @return string
   */
  function getMetadataXmp () {
    $option = "--xmp --encoding=utf-8";
    return $this->run($option);
  }

  /**
   * M�thode permettant de r�cuperer le langage du document.
   *
   * @return string
   */
  function getLanguage () {
    $option = "--language --encoding=utf-8";
    return $this->run($option);
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