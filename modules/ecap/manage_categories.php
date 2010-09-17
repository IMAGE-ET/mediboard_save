<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

class CEcapTypeDocument {
  var $level = 0;
  var $id = null;
  var $libelle = "";
  var $cnCode = "";
  var $cnType = "";

	static function flattenTypes($listeTypeDocument, $level = 0) {
	  $types = array();
	  
	  foreach ($listeTypeDocument->typeDocument as $typeDocument) {
	    $type = new CEcapTypeDocument();
	    $type->level = $level;
	    $type->id      = utf8_decode($typeDocument->idTypeDocument);
	    $type->libelle = utf8_decode($typeDocument->libelleTypeDocument);
	    $type->cnCode  = utf8_decode($typeDocument->classificationNationaleCode);
	    $type->cnType  = utf8_decode($typeDocument->classificationNationaleType);
	    $types[] = $type;
	    
	    $types = array_merge($types, self::flattenTypes($typeDocument->listeTypeDocument, $level+1));
	  }
	  
	  return $types;
	}
}

$source = CExchangeSource::get("ecap_files");
$params = CEcDocumentSender::getParams();
$typesEcapByMbClass= array();
foreach (CEcDocumentSender::$sendables as $mbClass => $typesEcapByEcObject) {
  foreach ($typesEcapByEcObject as $ecObject) {
    $params["aTypeObjet"] = $ecObject;
    $source->setData($params);
    $source->send("ListerTypeDocument");
    $result = simplexml_load_string($source->receive()->ListerTypeDocumentResult->any);
		if ($result->codeRetour != "0") {
		  $warning = sprintf("Erreur d'appel au service web e-Cap avec les param�tres '%s' : [%s] %s ", 
		    http_build_query($params),
		    $result->codeRetour,
		    utf8_decode($result->descriptionRetour));
		  trigger_error($warning, E_USER_WARNING);
		  continue;
		}
		
		$typesEcapByMbClass[$mbClass][$ecObject] = CEcapTypeDocument::flattenTypes($result->listeTypeDocument);
  }
}

$categories = CFilesCategory::loadListByClass();
foreach ($categories as &$_catsByClass) {
  foreach ($_catsByClass as &$_category) {
    $idEcap = new CIdSante400();

    $idEcap->loadLatestFor($_category, CMedicap::getTag("DT"));
    $idsEcap[$_category->_id] = $idEcap;
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("typesEcapByMbClass", $typesEcapByMbClass);
$smarty->assign("idsEcap"           , $idsEcap);
$smarty->assign("categories"        , $categories);

$smarty->display("manage_categories.tpl");

?>