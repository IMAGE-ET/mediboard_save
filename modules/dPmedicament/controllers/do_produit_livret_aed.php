<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $g;

$livret = new CBcbProduitLivretTherapeutique();

$group_id          = CValue::post("group_id");
$code_cip          = CValue::post("code_cip");
$prix_hopital      = CValue::post("prix_hopital");
$date_prix_hopital = CValue::post("date_prix_hopital");
$prix_ville        = CValue::post("prix_ville");
$date_prix_ville   = CValue::post("date_prix_ville");
$code_interne      = CValue::post("code_interne");
$commentaire       = CValue::post("commentaire");

$del = CValue::post("del");

if($del == 1){ 
  // Suppression du produit
  if($livret->distObj->Delete($g, $code_cip) < 0){
    // Affichage de l'erreur
    CAppUI::setMsg("Produit supprim�".$livret->distObj->GetLastError(), UI_MSG_ERROR );
  } else {
    CAppUI::setMsg("Produit supprim�", UI_MSG_OK );
  }  
} else {
  $produitLivret = new CBcbProduitLivretTherapeutique();
  if($produitLivret->load($code_cip)){
    // Si le produit existe, on lance l'update
    $livret->distObj->Code = $g;
	  $livret->distObj->CIP = $code_cip;
	  $livret->distObj->PrixHopital = $prix_hopital;
	  $livret->distObj->PrixVille = $prix_ville;
	  $livret->distObj->DatePrixHopital = $date_prix_hopital;
	  $livret->distObj->DatePrixVille = $date_prix_ville;
	  $livret->distObj->Commentaire = $commentaire;
	  $livret->distObj->CodeInterne = $code_interne;

	  $livret->updateDBFields();

	  if($livret->distObj->Update() < 0){
      // Affichage de l'erreur
      CAppUI::setMsg("Produit modifi�".$livret->distObj->GetLastError(), UI_MSG_ERROR );
    } else {
      CAppUI::setMsg("Produit modifi�", UI_MSG_OK );
    }    
  } else {
    // on cr�e le produit
		$livret->distObj->Code = $g;
		$livret->distObj->CIP = $code_cip;
    if($livret->distObj->Insert() < 0){
      // Affichage de l'erreur
      CAppUI::setMsg("Produit ajout� au livret th�rapeutique".$livret->distObj->GetLastError(), UI_MSG_ERROR );
    } else {
      CAppUI::setMsg("Produit ajout� au livret th�rapeutique", UI_MSG_OK );
      
      $produitLivret = new CBcbProduitLivretTherapeutique();
      $produitLivret->load($code_cip);
      if ($produitLivret->addToStocks()) {
        CAppUI::setMsg("Produit ajout� aux stocks", UI_MSG_OK );
      }
    }
  }
}
 
echo CAppUI::getMsg();
CApp::rip();
?>