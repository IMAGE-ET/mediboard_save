<?php /* $Id: vw_idx_etiquette.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CModeleEtiquette extends CMbMetaObject {
	
	// DB Table key
  var $modele_etiquette_id = null;
  
  // DB Fields
  var $nom           = null;
  var $texte         = null;
	var $texte_2       = null;
	var $texte_3       = null;
	var $texte_4       = null;
  var $largeur_page  = null;
  var $hauteur_page  = null;
  var $nb_lignes     = null;
  var $nb_colonnes   = null;
  var $marge_horiz   = null;
  var $marge_vert    = null;
  var $hauteur_ligne = null;
  var $font          = null;
  var $group_id      = null;
  var $show_border   = null;
  var $_width_etiq   = null;
  var $_height_etiq  = null;
  
	// Form fields
	var $_write_bold   = null;
	
  static $fields = array("CPatient" =>
      array("DATE NAISS", "IPP", "LIEU NAISSANCE",
            "NOM", "NOM JF",  "NUM SECU",
            "PRENOM", "SEXE", "CIVILITE", "CIVILITE LONGUE",
            "ACCORD GENRE"),
      "CSejour" => array("NDOS", "DATE ENT", "PRAT RESPONSABLE"),
      "General" => array("DATE COURANTE", "HEURE COURANTE"));
  
  static $listfonts =
      array("dejavusansmono" => "DejaVu Sans Mono",
            "freemono"       => "Free Mono",
            "veramo"         => "Vera Sans Mono");
      
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modele_etiquette';
    $spec->key   = 'modele_etiquette_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]           = "str notNull";
    $specs["texte"]         = "text notNull";
		$specs["texte_2"]       = "text";
		$specs["texte_3"]       = "text";
		$specs["texte_4"]       = "text";
    $specs["largeur_page"]  = "float notNull default|21";
    $specs["hauteur_page"]  = "float notNull default|29.7";
    $specs["marge_horiz"]   = "float notNull default|0.3";
    $specs["marge_vert"]    = "float notNull default|1.3";
    $specs["nb_lignes"]     = "num notNull default|8";
    $specs["nb_colonnes"]   = "num notNull default|4";
    $specs["hauteur_ligne"] = "float notNull default|8";
    $specs["object_id"]     = "ref class|CMbObject meta|object_class purgeable";
    $specs["object_class"]  = "str notNull class show|0";
    $specs["font"]          = "text show|0";
    $specs["group_id"]      = "ref class|CGroups notNull";
    $specs["show_border"]   = "bool default|0";
		$specs["_write_bold"]   = "bool";
		$specs["_width_etiq"]   = "float";
		$specs["_height_etiq"]   = "float";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->_view = $this->nom;
    $this->_width_etiq = round(($this->largeur_page - 2 * $this->marge_horiz) / $this->nb_colonnes, 2);
    $this->_height_etiq = round(($this->hauteur_page - 2 * $this->marge_vert) / $this->nb_lignes, 2);
  }
  
  function replaceFields($array_fields) {
  	foreach($array_fields as $_key=>$_field) {
  	  $search = array("[".$_key."]", "*".$_key."*");
  	  $replace = array($_field, "<b>$_field</b>");
  		$this->texte = str_replace($search, $replace, $this->texte);
			$this->texte_2 = str_replace($search, $replace, $this->texte_2);
			$this->texte_3 = str_replace($search, $replace, $this->texte_3);
			$this->texte_4 = str_replace($search, $replace, $this->texte_4);
  	}
  }
  
  function completeLabelFields() {
    return array(
      "DATE COURANTE" => mbDateToLocale(mbDate()),
      "HEURE COURANTE" => mbTime()
    );
  }
  
  function printEtiquettes() {
  	// Affectation de la police par d�fault si aucune n'est choisie
		if ($this->font == "")
		  $this->font = "dejavusansmono";
		
		// Calcul des dimensions de l'�tiquette
		$largeur_etiq = ($this->largeur_page - 2 * $this->marge_horiz) / $this->nb_colonnes;
		$hauteur_etiq = ($this->hauteur_page - 2 * $this->marge_vert) / $this->nb_lignes;
		
		// Cr�ation du PDF
		$pdf = new CMbPdf('P', 'cm', array($this->largeur_page, $this->hauteur_page));
		$pdf->setFont($this->font, '', $this->hauteur_ligne);
		
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		$pdf->SetMargins($this->marge_horiz, $this->marge_vert, $this->marge_horiz);
		$pdf->SetAutoPageBreak(0, $this->marge_vert);
		
		$pdf->AddPage();
		
		$distinct_texts = 1;
		$textes = array();
		$textes[1] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte)));
		
		if ($this->texte_2) {
			$distinct_texts++;
		  $textes[] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte_2)));
	  }
		
		if ($this->texte_3) {
			$distinct_texts++;
      $textes[] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte_3)));
		}
    
		if ($this->texte_4) {
      $distinct_texts++;
      $textes[] = preg_replace("/[\t\r\n\f]/", '', utf8_encode(nl2br($this->texte_4)));
    }
		
    $nb_etiqs = $this->nb_lignes * $this->nb_colonnes;
    $increment = floor( $nb_etiqs/ $distinct_texts);
    $current_text = 1;

		// Cr�ation de la grille d'�tiquettes et �criture du contenu.
		for ($i = 0; $i < $nb_etiqs; $i++) {
		  if ($i != 0 && $i % $increment == 0 && isset($textes[$current_text+1])) {
		    $current_text++;
		  }
		  
		  if (round($pdf->GetX()) >= ($this->largeur_page - 2 * $this->marge_horiz)) {
		    $pdf->SetX(0);
		    $pdf->SetLeftMargin($this->marge_horiz);
		    $pdf->SetY($pdf->GetY() + $hauteur_etiq);
		  }
		  
		  if ($this->show_border) {
		    $pdf->Rect($pdf->GetX(),$pdf->GetY(),$largeur_etiq, $hauteur_etiq, 'D');
		  }
		  
		  $x = $pdf->GetX();
		  $y = $pdf->GetY();
		  $pdf->SetLeftMargin($x);
		  
		  // On affecte la marge droite de mani�re � ce que la m�thode Write fasse un retour chariot
		  // lorsque le contenu �crit va d�passer la largeur de l'�tiquette
		  $pdf->SetRightMargin($this->largeur_page - $x - $largeur_etiq);
      
		  // La fonction nl2br ne fait qu'ajouter la balise <br />, elle ne supprime pas le \n.
		  // Il faut donc le faire manuellement.
		  $pdf->WriteHTML("<div>" . $textes[$current_text] . "</div>", false);
		  $x = $x + $largeur_etiq;
		  $pdf->SetY($y);
		  $pdf->SetX($x);
		}
		$pdf->Output($this->nom.'.pdf', "I");
  }
}
?>