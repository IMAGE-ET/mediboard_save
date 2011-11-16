<?php /* $Id:codeCCAM.class.php 8143 2010-02-25 10:59:37Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision:8143 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCodeCCAM {
  
  var $code          = null; // Code de l'acte 
  var $chapitres     = null; // Chapitres de la CCAM concernes
  var $libelleCourt  = null; // Libelles
  var $libelleLong   = null;
  var $place         = null; // Place dans la CCAM
  var $remarques     = null; // Remarques sur le code
  var $activites     = array(); // Activites correspondantes
  var $phases        = array(); // Nombre de phases par activit�s
  var $incomps       = array(); // Incompatibilite
  var $assos         = array(); // Associabilite
  var $procedure     = null; // Procedure
  var $remboursement = null; // Remboursement
  var $forfait       = null; // Forfait sp�cifique (SEH1, SEH2, SEH3, SEH4)
  var $couleur       = null; // Couleur du code par rapport � son chapitre
  
  // Variable calcul�es
  var $_code7    = null; // Possibilit� d'ajouter le modificateur 7 (0 : non, 1 : oui)
  var $_default  = null;
  
  // Activit�s et phases recuper�es depuis le code CCAM
  var $_activite = null;
  var $_phase    = null;
  
  var $_couleursChap = null;
  
	// niveaux de chargement
	const LITE   = 1;
	const MEDIUM = 2;
	const FULL   = 3;
  
  // table de chargement
	static $loadLevel = array();
	static $loadedCodes = array();
	static $cacheCount = 0;
	static $useCount = array(
	  CCodeCCAM::LITE   => 0,
		CCodeCCAM::MEDIUM => 0,
	  CCodeCCAM::FULL   => 0,
	);
	
  static $spec = null;
  
  /**
   * Constructeur � partir du code CCAM
   */
  function CCodeCCAM($code) {
    // Static initialisation
    if (!self::$spec) {
      self::$spec = new CMbObjectSpec();
      self::$spec->dsn = "ccamV2";
      self::$spec->init();
    }
    
    $this->_spec = self::$spec;
    
    $this->_couleursChap = array(1  => "669966", 2  => "6666cc", 3  => "6699ee", 4  => "cc6633", 5  => "ee6699",
                                 6  => "ff66ee", 7  => "33cc33", 8  => "66cc99", 9  => "99ccee", 10 => "cccc33",
                                 11 => "eecc99", 12 => "ffccee", 13 => "33ff33", 14 => "66ff99", 15 => "99ffee",
                                 16 => "ccff33", 17 => "eeff99", 18 => "ffffee", 19 => "cccccc"
                                );
    
    if (strlen($code) > 7){
      if (!preg_match ("/^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$/i", $code)) {
         return "Le code $code n'est pas format� correctement";
      }

      // Cas ou l'activite et la phase sont indiqu�es dans le code (ex: BFGA004-1-0)
      $detailCode = explode("-", $code);
      $this->code = strtoupper($detailCode[0]);
      $this->_activite = $detailCode[1];
      if(count($detailCode) > 2){
        $this->_phase = $detailCode[2];
      }
    } else {
      $this->code = strtoupper($code);
    }
  }
  
	// Chargement optimis� des codes
	static function get($code, $niv = self::MEDIUM) {
		self::$useCount[$niv]++;
		
    if (!CAppUI::conf("dPccam CCodeCCAM use_cache")) {
		  $codeCCAM = new CCodeCCAM($code);
		  $codeCCAM->load($niv);
		  return $codeCCAM;
		}
	  
		// Si le code n'a encore jamais �t� charg�, on instancie et on met son niveau de chargement � z�ro
		if (!isset(self::$loadedCodes[$code])) {
			self::$loadedCodes[$code] = new CCodeCCAM($code);
		  self::$loadLevel[$code] = null;
		} 
		
  	$code_ccam =& self::$loadedCodes[$code];

  	// Si le niveau demand� est inf�rieur au niveau courant, on retourne le code 
   	if ($niv <= self::$loadLevel[$code]) {
			self::$cacheCount++;
			return $code_ccam->copy();
		}

		// Chargement
		$code_ccam->load($niv);
		self::$loadLevel[$code] = $niv;

    return $code_ccam->copy();
	}
	
	/**
	 * Should use clone with appropriate behaviour
	 * But a bit complicated to implement
	 */
	function copy() {
	  $obj = unserialize(serialize($this));
	  $obj->_spec = self::$spec;
	  return $obj;
	}
	
	function load($niv) {
		if (!$this->getLibelles()) return;
    
	  if ($niv == self::LITE) {
			$this->getActivite7();
		}

		if ($niv >= self::LITE) {
			$this->getTarification();
      $this->getForfaitSpec();
		}

		if ($niv >= self::MEDIUM) {
			$this->getChaps();
			$this->getRemarques();
			$this->getActivites();
		}

		if ($niv == self::FULL) {
			$this->getActesAsso();
			$this->getActesIncomp();
			$this->getProcedure();
    }
	}
  
  function getLibelles() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->code);
    $result = $ds->exec($query);
    if($ds->numRows($result) == 0) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "Acte inconnu ou supprim�";
      $this->libelleLong = "Acte inconnu ou supprim�";
      $this->_code7 = 1;
      return false;
    } else {
      $row = $ds->fetchArray($result);
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
      return true;
    }
  }
  
  function getActivite7() {
    $ds =& $this->_spec->ds;
    // recherche de la derni�re date d'effet
    $query1 = "SELECT MAX(DATEEFFET) as LASTDATE FROM modificateuracte WHERE ";
    $query1 .= $ds->prepare("CODEACTE = %", $this->code);
    $query1 .= " GROUP BY CODEACTE";
    $result1 = $ds->exec($query1);
    // Chargement des modificateurs
    if($ds->numRows($result1)) {
      $row = $ds->fetchArray($result1);
      $lastDate = $row["LASTDATE"];
      $query2 = "SELECT * FROM modificateuracte WHERE ";
      $query2 .= $ds->prepare("CODEACTE = %", $this->code);
      $query2 .= " AND CODEACTIVITE = '4'";
      $query2 .= " AND MODIFICATEUR = '7'";
      $query2 .= " AND DATEEFFET = '$lastDate'";
      $result2 = $ds->exec($query2);
      $this->_code7 = $ds->numRows($result2);
    } else {
      $this->_code7 = 1;
    }
  }
  
  function getTarification() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM infotarif WHERE CODEACTE = % ORDER BY DATEEFFET DESC", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->remboursement = $row["REMBOURSEMENT"];
  }
  
  function getForfaitSpec() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM forfaits WHERE CODE = %", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->forfait = $row["forfait"];
  } 
  
  function getChaps() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);

    // On rentre les champs de la table actes
    $this->couleur = $this->_couleursChap[intval($row["ARBORESCENCE1"])];
    $this->chapitres[0]["db"] = $row["ARBORESCENCE1"];
    $this->chapitres[1]["db"] = $row["ARBORESCENCE2"];
    $this->chapitres[2]["db"] = $row["ARBORESCENCE3"];
    $this->chapitres[3]["db"] = $row["ARBORESCENCE4"];
    $pere = "000001";
    $track = "";
    
    // On rentre les infos sur les chapitres
    foreach($this->chapitres as $key => $value) {
      $rang = $this->chapitres[$key]["db"];
      $query = $ds->prepare("SELECT * FROM arborescence WHERE CODEPERE = %1 AND rang = %2", $pere, $rang);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      
      $query = $ds->prepare("SELECT * FROM notesarborescence WHERE CODEMENU = %", $row["CODEMENU"]);
      $result2 = $ds->exec($query);
      
      $track .= substr($row["RANG"], -2) . ".";
      $this->chapitres[$key]["rang"] = $track;
      $this->chapitres[$key]["code"] = $row["CODEMENU"];
      $this->chapitres[$key]["nom"] = $row["LIBELLE"];
      $this->chapitres[$key]["rq"] = "";
      while($row2 = $ds->fetchArray($result2)) {
        $this->chapitres[$key]["rq"] .= "* " . str_replace("�", "\n", $row2["TEXTE"]) . "\n";
      }
      $pere = $this->chapitres[$key]["code"];
    }
    $this->place = $this->chapitres[3]["rang"];
  }
  
  function getRemarques() {
    $ds =& $this->_spec->ds;
    $this->remarques = array();
    $query = $ds->prepare("SELECT * FROM notes WHERE CODEACTE = %", $this->code);
    $result = $ds->exec($query);
    while ($row = $ds->fetchArray($result)) {
      $this->remarques[] = str_replace("�", "\n", $row["TEXTE"]);
    }
  }
  
  function getActivites() {
    $ds =& $this->_spec->ds;
    // Extraction des activit�s
    $query = "SELECT ACTIVITE AS numero
				      FROM activiteacte
				      WHERE CODEACTE = %";
    $query = $ds->prepare($query, $this->code);
    $result = $ds->exec($query);
    while($obj = $ds->fetchObject($result)) {
      $obj->libelle = "";
      $this->activites[$obj->numero] = $obj;
    }
    // Libell�s des activit�s
    foreach($this->remarques as $remarque) {
      $match = null;
      if (preg_match("/Activit� (\d) : (.*)/i", $remarque, $match)) {
        $this->activites[$match[1]]->libelle = $match[2];
      }
    }
    // D�tail des activit�s
    foreach($this->activites as &$activite) {
      // Type de l'activit�
      $query = "SELECT LIBELLE AS `type`
			          FROM activite
			          WHERE CODE = %";
      $query = $ds->prepare($query, $activite->numero);
      $result = $ds->exec($query);
      $obj = $ds->fetchObject($result);
      $activite->type = $obj->type;
      // Modificateurs de l'activite
      $this->getModificateursFromActivite($activite);
      $this->getPhasesFromActivite($activite);
    }
    // Test de la pr�sence d'activit� virtuelle
    if(isset($this->activites[1]) && isset($this->activites[4])) {
      if(isset($this->activites[1]->phases[0]) && isset($this->activites[4]->phases[0])) {
        if($this->activites[1]->phases[0]->tarif && !$this->activites[4]->phases[0]->tarif) {
          unset($this->activites[4]);
        }
        if(!$this->activites[1]->phases[0]->tarif && $this->activites[4]->phases[0]->tarif) {
          unset($this->activites[1]);
        }
      }
    }
    $this->_default = reset($this->activites);
    if(isset($this->_default->phases[0])){
      $this->_default = $this->_default->phases[0]->tarif;
    } else {
    	$this->_default = 0;
    }
  }
  
  function getModificateursFromActivite(&$activite) {
    $ds =& $this->_spec->ds;
    // recherche de la derni�re date d'effet
    $query = "SELECT MAX(DATEEFFET) AS LASTDATE
			        FROM modificateuracte
			        WHERE CODEACTE = %1
			        GROUP BY CODEACTE";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $lastDate = $row["LASTDATE"];
    // Extraction des modificateurs
    $activite->modificateurs = array();
    $modificateurs =& $activite->modificateurs;
    $query = "SELECT * FROM modificateuracte
			        WHERE CODEACTE = %1
			        AND CODEACTIVITE = %2
			        AND DATEEFFET = '$lastDate'
			        GROUP BY MODIFICATEUR";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
    
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT CODE AS code, LIBELLE AS libelle
			          FROM modificateur
			          WHERE CODE = %
			          ORDER BY CODE";
      $query = $ds->prepare($query, $row["MODIFICATEUR"]);
      $modificateurs[] = $ds->fetchObject($ds->exec($query));
    }
  }
  
  function getPhasesFromActivite(&$activite) {
    $ds =& $this->_spec->ds;
    // Extraction des phases
    $activite->phases = array();
    $phases =& $activite->phases;
    $query = "SELECT PHASE AS phase, PRIXUNITAIRE AS tarif, CHARGESCAB charges
			        FROM phaseacte
			        WHERE CODEACTE = %1
			        AND ACTIVITE = %2
			        GROUP BY PHASE
			        ORDER BY PHASE, DATE1 DESC";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
          
    while($obj = $ds->fetchObject($result)) {
      $phases[$obj->phase] = $obj;
      $phase =& $phases[$obj->phase];
      $phase->tarif = floatval($obj->tarif)/100;
      $phase->libelle = "Phase Principale";
      $phase->charges = floatval($obj->charges)/100;
      
      // Copie des modificateurs pour chaque phase. Utile pour dPsalleOp
      $phase->_modificateurs = $activite->modificateurs;
    }
    
    // Libell�s des phases
    foreach($this->remarques as $remarque) {
      if (preg_match("/Phase (\d) : (.*)/i", $remarque, $match)) {
        if (isset($phases[$match[1]])) {
          $phases[$match[1]]->libelle = $match[2];
        }
      }
    }
  }
  
  function getActesAsso() {
    $ds =& $this->_spec->ds;
    $queryEffet = $ds->prepare("SELECT MAX(DATEEFFET) as LASTDATE FROM associabilite WHERE CODEACTE = % GROUP BY CODEACTE", $this->code);
    $resultEffet = $ds->exec($queryEffet);
    $rowEffet = $ds->fetchArray($resultEffet);
    $lastDate = $rowEffet["LASTDATE"];
    $query = $ds->prepare("SELECT * FROM associabilite WHERE CODEACTE = % AND DATEEFFET = '$lastDate' GROUP BY ACTEASSO", $this->code);
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $this->assos[$i]["code"] = $row["ACTEASSO"];
      $query2 = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", trim($row["ACTEASSO"]));
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->assos[$i]["texte"] = $row2["LIBELLELONG"];
      $i++;
    }
  }
  
  function getActesIncomp() {
    $ds =& $this->_spec->ds;
    $queryEffet = $ds->prepare("SELECT MAX(DATEEFFET) as LASTDATE FROM incompatibilite WHERE CODEACTE = % GROUP BY CODEACTE", $this->code);
    $resultEffet = $ds->exec($queryEffet);
    $rowEffet = $ds->fetchArray($resultEffet);
    $lastDate = $rowEffet["LASTDATE"];
    $query = $ds->prepare("SELECT * FROM incompatibilite WHERE CODEACTE = % AND DATEEFFET = '$lastDate' GROUP BY INCOMPATIBLE", $this->code);
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $this->incomps[$i]["code"] = trim($row["INCOMPATIBLE"]);
      $query2 = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", trim($row["INCOMPATIBLE"]));
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->incomps[$i]["texte"] = $row2["LIBELLELONG"];
      $i++;
    }
  }
  
  function getProcedure() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM procedures WHERE CODEACTE = % GROUP BY CODEACTE ORDER BY DATEEFFET DESC", $this->code);
    $result = $ds->exec($query);
    if($ds->numRows($result) > 0) {
      $row = $ds->fetchArray($result);
      $this->procedure["code"] = $row["CODEPROCEDURE"];
      $query2 = $ds->prepare("SELECT LIBELLELONG FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->procedure["code"]);
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->procedure["texte"] = $row2["LIBELLELONG"];
    } else {
      $this->procedure["code"] = "aucune";
      $this->procedure["texte"] = "";
    }
  }
  
  function getForfait($modificateur) {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM modificateurforfait WHERE CODE = % AND DATEFIN = '00000000'", $modificateur);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $valeur = array();
    $valeur["forfait"] = $row["FORFAIT"] / 100;
    $valeur["coefficient"] = $row["COEFFICIENT"] / 10;
    return $valeur;
  }
  
  function getCoeffAsso($code) {
    if($code == "X")
      return 0;
    if(!$code) {
      return 100;
    }
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM association WHERE CODE = % AND DATEFIN = '00000000'", $code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $valeur = $row["COEFFICIENT"] / 10;
    return $valeur;
  }
  
// Recherche de codes
  function findCodes($code='', $keys='', $max_length = null, $where = null) {
    $ds =& $this->_spec->ds;
  
    $query = "SELECT CODE, LIBELLELONG
              FROM actes
              WHERE DATEFIN = '00000000' ";

    $keywords = explode(" ", $keys);
    $codes    = explode(" ", $code);
    CMbArray::removeValue("", $keywords);
    CMbArray::removeValue("", $codes);
    
    if ($keys != "") {
      $listLike = array();
      $codeLike = array();
      foreach ($keywords as $key => $value) {
        $listLike[] = "LIBELLELONG LIKE '%".addslashes($value)."%'";
      }
      // Combiner la recherche de code et libell�
      if ($code != "") {
        foreach ($codes as $key => $value) {
          $codeLike[] = "CODE LIKE '".addslashes($value) . "%'";
        }
        $query .= " AND ( (";
        $query .= implode(" OR ", $codeLike);
        $query .= ") OR (";
      }
      // Ou que le libell�
      else {
        $query .= " AND (";
      }
      $query .= implode(" AND ", $listLike);
      if ($code != "") $query .= ") ) ";

    }
    // Ou que le code
    if($code && !$keys) {
      $codeLike = array();
      foreach ($codes as $key => $value) {
        $codeLike[] = "CODE LIKE '".addslashes($value) . "%'";
      }
      $query .= "AND ". implode(" OR ", $codeLike);
    }
    
    if ($max_length) {
      $query .= " AND LENGTH(CODE) < $max_length ";
    }
    
    if ($where) {
      $query .= "AND " . $where;
    }
    
    $query .= " ORDER BY CODE LIMIT 0 , 100";
    
    $result = $ds->exec($query);
    $master = array();
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $master[$i]["LIBELLELONG"] = $row["LIBELLELONG"];
      $master[$i]["CODE"] = $row["CODE"];
      $i++;
    }
  
    return($master);
  }
  
  function getActeRadio() {
    $ds =& $this->_spec->ds;
    $query = "SELECT code
      FROM ccam_radio
      WHERE code_saisi LIKE '%".$this->code."%'";
    return $ds->loadResult($query);
  }
}



?>