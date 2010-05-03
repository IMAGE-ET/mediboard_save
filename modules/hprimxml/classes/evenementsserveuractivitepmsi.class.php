<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsServeurActivitePmsi extends CHPrimXMLDocument {
	static $evenements = array(
    'evenementPMSI'        => "CHPrimXMLEvenementsPmsi",
    'evenementServeurActe' => "CHPrimXMLEvenementsServeurActes",
  );
	
  function __construct($dirschemaname, $schemafilename = null) {
    $this->type = "pmsi";
    
    $version = CAppUI::conf("hprimxml $this->evenement version");
    if ($version == "1.01") {
      parent::__construct($dirschemaname, $schemafilename."101");
    } else if ($version == "1.05") {
      parent::__construct("serveurActivitePmsi", $schemafilename."105");
    }   
  }
	
	function getDateInterv($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    // Obligatoire pour MB
    $debut = $xpath->queryUniqueNode("hprim:debut", $node, false);
    
    return $xpath->queryTextNode("hprim:date", $debut);
  }
  
  function mappingActesCCAM($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
		
		mbTrace($node, "node", true);
  }
	
}

?>