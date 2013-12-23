<?php
/**
 * $Id: CWkHtmlToPDFConverter.class.php 19055 2013-05-07 14:09:27Z mytto $
 *
 * @package    Mediboard
 * @subpackage CompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19055 $
 */

/**
 * WkHtmlToPDF Converter
 */
class CWkHtmlToPDFConverter extends CHtmlToPDFConverter {
  public $file;
  public $width;
  public $height;
  public $format;
  public $orientation;
  public $header;
  public $header_height;
  public $header_spacing = 0;
  public $footer;
  public $footer_height;
  public $footer_spacing = 0;
  public $body;
  public $margins;
  public $temp_name;

  /**
   * @see parent::prepare()
   */
  function prepare($format, $orientation) {
    global $rootName;
    
    // Changer les srs pour les images
    $this->html = preg_replace("/src=\"\/".$rootName."/", "src=\"../", $this->html);
    
    if (is_array($format)) {
      $this->width  = $format[2];
      $this->height = $format[3];
    }
    else {
      $this->format = $format;
      $this->orientation = $orientation; 
    }
    
    // Racine du ou des fichiers html
    $this->temp_name = tempnam("./tmp", "wkhtmltopdf");
    
    // Extraire les marges
    preg_match(
      "/@page\s*{\s*margin-top:\s*([0-9.]+)cm;\s*".
      "margin-right:\s*([0-9.]+)cm;\s*".
      "margin-bottom:\s*([0-9.]+)cm;\s*margin-left:\s*([0-9.]+)cm;/",
      $this->html,
      $matches
    );

    if (count($matches)) {
      // Le facteur 10 est pour la conversion en mm
      $this->margins = array(
        "top"    => $matches[1] * 10,
        "right"  => $matches[2] * 10,
        "bottom" => $matches[3] * 10,
        "left"   => $matches[4] * 10,
      );

      $pos_header = strpos($this->html, "<div id=\"header\"");
      $pos_footer = strpos($this->html, "<div id=\"footer\"");
      $pos_body   = strpos($this->html, "<div id=\"body\">");

      /* header / footer sans body */
      if (!$pos_body) {
        $pos_body = strlen($this->html) - 16;
      }

      $header     = null;
      $footer     = null;
      $header_footer_common = null;
      $page_number = "<script type='text/javascript'>
        function subst() {
          var vars = {},
              x = document.location.search.substring(1).split('&');
          for (var i in x) {
            var z = x[i].split('=', 2);
            vars[z[0]] = decodeURI(z[1]);
          }
          x = ['page'];
          for (var j in x) {
            z = x[j];
            var y = document.getElementsByClassName(z);
            for (var k = 0; k < y.length; ++k) {
              y[k].textContent = vars[z];
            }
          }
        }
      </script>";
      // Extraire l'ent�te
      if ($pos_header) {
        $header_footer_common = substr($this->html, 0, $pos_header);
        if ($pos_footer) {
          $header = substr($this->html, $pos_header, $pos_footer - $pos_header);
        }
        else {
          $header = substr($this->html, $pos_header, $pos_body - $pos_header);
        }

        // On trouve la taille du header dans le style
        preg_match("/#header\s*\{\s*height:\s*([0-9]+[\.0-9]*)px;/", $this->html, $matches);
        $this->header_height = $matches[1];
      }

      // Extraire le pied de page
      if ($pos_footer) {
        if (!$pos_header) {
          $header_footer_common = substr($this->html, 0, $pos_footer);
        }
        $footer = substr($this->html, $pos_footer, $pos_body - $pos_footer);

        $this->html = str_replace($footer, '', $this->html);

        preg_match("/#footer\s*{\s*height:\s*([0-9]+[\.0-9]*)px;/", $this->html, $matches);
        $this->footer_height = $matches[1];
      }

      // Supprimer le padding-top du hr et le margin-top du body
      $this->html = preg_replace("/body\s*\{\s*margin-top:\s*[0-9]*px;\s*\}/", "", $this->html);
      if ($header_footer_common != null) {
        $header_footer_common = preg_replace("/body\s*\{\s*margin-top:\s*[0-9]*px;\s*\}/", "", $header_footer_common);
        $header_footer_common = preg_replace("/<body>/", "<body onload='subst()'>", $header_footer_common);
      }
      $this->html = preg_replace("/hr.pagebreak\s*{\s*padding-top:\s*[0-9]*px;\s*}/", "", $this->html);

      // Supprimer le margin-bottom du body
      $this->html = preg_replace("/body\s*\{\s*margin-bottom:\s*[0-9]*px;\s*\}/", "", $this->html);

      if ($header_footer_common != null) {
        $header_footer_common = preg_replace("/body\s*\{\s*margin-bottom:\s*[0-9]*px;\s*\}/", "", $header_footer_common);
      }

      // Suppression de la balise script pour l'impression
      $this->html = preg_replace("/(<script type=[\'\"]text\/javascript[\'\"]>.*<\/script>)/msU", "", $this->html);

      // Supression du margin: 0 et padding: 0
      $this->html = preg_replace("/body\s*{([a-zA-Z0-9:;\-\n\s\t]*)(margin:\s*0;[\n\t\s]*padding:\s*0;)/", 'body {�$1', $this->html);

      // Suppression du position fixed du header et du footer
      if ($header_footer_common) {
        $header_footer_common = preg_replace("/position:\s*fixed;/", "", $header_footer_common);
        $header_footer_common = preg_replace("/(<script type=[\'\"]text\/javascript[\'\"]>.*<\/script>)/msU", "", $header_footer_common);
      }

      // Store de l'ent�te / pied de page
      if ($header) {
        // On supprime l'ent�te que maintenant sinon les positions de cha�nes seront erron�es
        $this->html = str_replace($header, '', $this->html);
        $this->header = $this->temp_name . "-header.html";
        file_put_contents($this->header, $header_footer_common.$page_number.$header."</body></html>");
      }
      if ($footer) {
        $this->footer = $this->temp_name . "-footer.html";
        file_put_contents($this->footer, $header_footer_common.$page_number.$footer."</body></html>");
      }

      if (!$pos_body) {
        $this->html = $header_footer_common . "</body></html>";
      }
    }

    $this->file = $this->temp_name.".html";
    file_put_contents($this->file, $this->html);
    
  }

  /**
   * G�n�ration pdf d'une source HTML
   *
   * @return void
   */
  function render() {
    $root_dir = CAppUI::conf("root_dir");

    $arch = CAppUI::conf("dPcompteRendu CCompteRendu arch_wkhtmltopdf");
    if ($arch != "i386" && $arch != "amd64") {
      $arch = "i386";
    }
    $command = "$root_dir/lib/wkhtmltopdf/wkhtmltopdf-".$arch." -q ";
    $result = tempnam("./tmp", "result");
    $options = "--print-media-type ";

    // Ent�te
    if ($this->header) {
      $this->margins["top"] += (25.4*$this->header_height)/96;
      $options .= "--header-html ".escapeshellarg($this->header). " --header-spacing ".escapeshellarg($this->header_spacing)." ";
    }

    // Pied de page
    if ($this->footer) {
      $this->margins["bottom"] += ((25.4*$this->footer_height)/96 + $this->footer_spacing); 
      $options .= "--footer-html ".escapeshellarg($this->footer)." --footer-spacing ".escapeshellarg($this->footer_spacing)." ";
    }
    
    // Marges
    if ($this->margins) {
      foreach ($this->margins as $key=>$_marge) {
        $options .= "--margin-$key ".escapeshellarg($_marge)." ";
      }
    }

    // Format de la page
    if ($this->format && $this->orientation) {
      $options .= "--page-size ".escapeshellarg($this->format)." --orientation ". escapeshellarg($this->orientation)." ";
    }
    
    if ($this->width && $this->height) {
      // Conversion en mm
      $width = (25.4*$this->width)/72;
      $height = (25.4*$this->height)/72;
      $options .= "--page-width ". escapeshellarg($width). " --page-height ". escapeshellarg($height)." ";
    }
    
    $options .= escapeshellarg($this->file) . " " . escapeshellarg($result);
    
    exec($command.$options);
    
    $this->result = file_get_contents($result);
    
    // Supression des fichiers temporaires
    @unlink($this->temp_name);
    @unlink($this->header);
    @unlink($this->footer);
    @unlink($this->file);
    @unlink($result);
  }
}
