<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$rpu_id = CValue::get("rpu_id");

$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

$sejour = $rpu->_ref_sejour;
$sejour->_ref_rpu = $rpu;
$sejour->loadRefsFwd();
$sejour->_ref_rpu->loadRefSejourMutation();
$sejour->loadNumDossier();
$sejour->loadRefsConsultations();
$sejour->_ref_rpu->_ref_consult->loadRefsActes();
// Chargement de l'IPP
$sejour->_ref_patient->loadIPP();

// Chargement des etablissements externes
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, "nom");

// Contraintes sur le mode de sortie / destination
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);
$smarty->assign("listEtab", $listEtab);

$smarty->assign("sejour" , $sejour);

$smarty->display("inc_sortie_rpu.tpl");
?>