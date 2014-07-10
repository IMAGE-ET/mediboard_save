<?php
/**
 * $Id: CDomPDFConverter.class.php $
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

/**
 * Frontend permettant la conversion html to pdf via dompdf 
 * Cette classe n'est pas un MbObject et les objets ne sont pas enregistr�s en base
 */
class CDomPDFConverter extends CHtmlToPDFConverter {
  /** @var DOMPDF */
  public $dompdf;
  
  /**
   * Pr�paration de dompdf pour la conversion
   * 
   * @param string $format      format de la page
   * @param string $orientation orientation de la page
   * 
   * @return void
   */
  function prepare($format, $orientation) {
    CAppUI::requireModuleFile("dPcompteRendu", "dompdf_config");
    CAppUI::requireLibraryFile("dompdf/dompdf_config.inc");
    
    $this->dompdf = new dompdf();
    $this->dompdf->set_base_path(realpath(dirname(__FILE__)."/../../../../"));
    $this->dompdf->set_paper($format, $orientation);
    if (CAppUI::conf("dPcompteRendu CCompteRendu dompdf_host")) {
      $this->dompdf->set_protocol(isset($_SERVER["HTTPS"]) ? "https://" : "http://");
      $this->dompdf->set_host($_SERVER["SERVER_NAME"]);
    }
  }
  
  /**
   * Effectue le rendu du contenu html en pdf
   * 
   * @return void
   */
  function render() {
    $this->dompdf->load_html($this->html);
    $this->dompdf->render();
    if (CHtmlToPDFConverter::$_page_ordonnance) {
      $this->dompdf->get_canvas()->page_text(273, 730, "Page {PAGE_NUM} / {PAGE_COUNT}", Font_Metrics::get_font("arial"), 10);
    }

    $this->result = $this->dompdf->output();
  }
}
