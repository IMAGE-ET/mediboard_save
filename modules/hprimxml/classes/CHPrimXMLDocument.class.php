<?php

/**
 * H'XML Document
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLDocument
 * H'XML Document
 */

class CHPrimXMLDocument extends CMbXMLDocument {  
  public $evenement;
  public $finalpath;
  public $documentfinalprefix;
  public $documentfinalfilename;
  public $sentFiles = array();
  
  public $group_id;
  
  public $type;
  public $sous_type;
  
  // Behaviour fields

  /**
   * @var CInteropSender
   */
  public $_ref_sender;

  /**
   * @var CInteropReceiver
   */
  public $_ref_receiver;

  /**
   * @var CEchangeHprim
   */
  public $_ref_echange_hprim;

  /**
   * @var array Liste des constantes disponibles dans le sch�ma et dans Mediboard
   */
  static $list_constantes = array (
    "poids"  => "pds",
    "taille" => "tll",
  );

  /**
   * Construct
   *
   * @param string $dirschemaname  Schema name directory
   * @param string $schemafilename Schema filename
   * @param string $mod_name       Module name
   *
   * @return CHPrimXMLDocument
   */
  function __construct($dirschemaname, $schemafilename = null, $mod_name = "hprimxml") {
    parent::__construct();

    $this->formatOutput = false;
    
    $this->patharchiveschema = "modules/$mod_name/xsd";
    $this->schemapath        = "$this->patharchiveschema/$dirschemaname";
    $this->schemafilename    = ($schemafilename) ? 
                                ((!CAppUI::conf("hprimxml concatenate_xsd")) ? 
                                  "$this->schemapath/$schemafilename.xsd" : 
                                  "$this->schemapath/$schemafilename.xml") :
                                "$this->schemapath/schema.xml";
    $this->documentfilename  = "$this->schemapath/document.xml";
    $this->finalpath         = CFile::$directory . "/$mod_name/$dirschemaname";
    
    $this->now               = time();
  }

  /**
   * Try to validate the document against a schema will trigger errors when not validating
   *
   * @param string $filename       Path of schema, use document inline schema if null
   * @param bool   $returnErrors   Return errors
   * @param bool   $display_errors Display errors
   *
   * @return boolean
   */
  function schemaValidate($filename = null, $returnErrors = false, $display_errors = true) {
    if (!CAppUI::conf("hprimxml ".$this->evenement." validation")) {
      return true;
    }
    return parent::schemaValidate($filename, $returnErrors, $display_errors);
  }

  /**
   * Check schema
   *
   * @return bool
   */
  function checkSchema() {
    if (!is_dir($this->schemapath)) {
      $msg = "HPRIMXML schemas are missing. Please extract them from archive in '$this->schemapath/' directory";
      trigger_error($msg, E_USER_WARNING);
      return false;
    }
    
    if (!is_file($this->schemafilename)) {
      $schema = new CHPrimXMLSchema();
      $schema->importSchemaPackage($this->schemapath);
      $schema->purgeIncludes();
      $schema->purgeImportedNamespaces();
      $schema->save($this->schemafilename);
    }
    
    return true;
  }

  /**
   * @see parent::addElement
   */
  function addElement(DOMNode $elParent, $elName, $elValue = null, $elNS = "http://www.hprim.org/hprimXML") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }

  /**
   * @see parent::addNameSpaces
   */
  function addNameSpaces() {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "http://www.hprim.org/hprimXML schema.xml");
  }

  /**
   * @see parent::saveTempFile
   */
  function saveTempFile() {
    parent::save(utf8_encode($this->documentfilename));
  }

  /**
   * @see parent::saveFinalFile
   */
  function saveFinalFile() {
    $this->documentfinalfilename = "$this->finalpath/$this->documentfinalprefix-$this->now.xml";
    CMbPath::forceDir(dirname($this->documentfinalfilename));
    parent::save($this->documentfinalfilename);
  }

  /**
   * @see parent::getSentFiles
   */
  function getSentFiles() {
    $pattern = "$this->finalpath/$this->documentfinalprefix-*.xml";
    foreach (glob($pattern) as $sentFile) {
      $baseName = basename($sentFile);
      $matches = null;
      preg_match("`^[[:alpha:]]{2,3}[[:digit:]]{6}-([[:digit:]]*)\.xml$`", $baseName, $matches);
      $timeStamp = $matches[1];
      $this->sentFiles[] = array (
        "name" => $baseName,
        "path" => $sentFile,
        "datetime" => strftime("%Y-%m-%d %H:%M:%S", $timeStamp)
      );
    }
  }

  /**
   * R�cup�ration de l'attribut syst�me
   *
   * @return string
   */
  function getAttSysteme() {
    $systeme = "syst�me";
    $sender = $this->_ref_sender;

    if ($sender && $sender->_configs) {
      $systeme = $sender->_configs["att_system"];
    }

    return (CAppUI::conf("hprimxml ".$this->evenement." version") < "1.07") ? 
      $systeme : CMbString::removeDiacritics($systeme);
  }

  /**
   * Ajout de l'ent�te du message
   *
   * @param DOMNode $elParent Node
   *
   * @return void
   */
  function addEnteteMessage(DOMNode $elParent) {
    $echg_hprim      = $this->_ref_echange_hprim;
    $dest            = $this->_ref_receiver;
    $identifiant     = $echg_hprim->_id ? str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT) : "ES{$this->now}";
    $date_production = $echg_hprim->_id ? $echg_hprim->date_production : CMbDT::dateTimeXML();
    
    $this->addAttribute($elParent, "acquittementAttendu", $dest->_configs["receive_ack"] ? "oui" : "non");
    
    $enteteMessage = $this->addElement($elParent, "enteteMessage");
    $this->addElement($enteteMessage, "identifiantMessage", $identifiant);
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction", $date_production);
    
    /* @todo MB toujours l'emetteur ? */
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Sant�");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $user = CAppUI::$user;
    $this->addAgent($agents, "acteur", "user$user->_id", $user->_view);
    $code_systeme = (CAppUI::conf('hprimxml code_transmitter_sender') == "finess") ? $group->finess : CAppUI::conf('mb_id');
    $this->addAgent($agents, $this->getAttSysteme(), $code_systeme, $group->text);
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    if ($dest->code_appli) {
      $this->addAgent($agents, "application", $dest->code_appli, "");
    }

    if ($dest->code_acteur) {
      $this->addAgent($agents, "acteur", $dest->code_acteur, "");
    }

    $this->addAgent($agents, $this->getAttSysteme(), $dest->code_syst, $dest->libelle);
  }

  /**
   * G�n�ration de l'�v�nement
   *
   * @param CMbObject $mbObject   Object
   * @param bool      $referent   R�f�rent ?
   * @param bool      $initiateur Initiateur ?
   *
   * @return string
   */
  function generateTypeEvenement(CMbObject $mbObject, $referent = false, $initiateur = false) {
    $echg_hprim = new CEchangeHprim();
    $echg_hprim->date_production = CMbDT::dateTime();
    $echg_hprim->sender_id       = $this->_ref_sender ? $this->_ref_sender->_id : null;
    $echg_hprim->receiver_id     = $this->_ref_receiver->_id;
    $echg_hprim->group_id        = $this->_ref_receiver->group_id;
    $echg_hprim->type            = $this->type;
    $echg_hprim->sous_type       = $this->sous_type;
    $echg_hprim->object_id       = $mbObject->_id;
    $echg_hprim->_message        = utf8_encode($this->saveXML());
    $echg_hprim->initiateur_id   = $initiateur;
    $echg_hprim->setObjectClassIdPermanent($mbObject);
    $echg_hprim->store();
    
    // Chargement des configs du destinataire
    $dest = $this->_ref_receiver;
    $dest->loadConfigValues();
    
    $this->_ref_echange_hprim = $echg_hprim;
            
    $this->generateEnteteMessage();
    $this->generateFromOperation($mbObject, $referent);

    $doc_valid = $this->schemaValidate(null, false, $this->_ref_receiver->display_errors);
    $echg_hprim->message_valide = $doc_valid ? 1 : 0;

    $this->saveTempFile();
    $msg = $this->saveXML();
    
    // On sauvegarde toujours en base le message en UTF-8
    $echg_hprim->_message = utf8_encode($msg);

    $echg_hprim->store();
    
    // On envoie le contenu et NON l'ent�te en UTF-8 si le destinataire est en UTF-8
    return ($dest->_configs["encoding"] == "UTF-8") ? utf8_encode($msg) : $msg;
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
  }

  /**
   * Generate content message
   *
   * @param CMbObject $mbObject Object
   * @param bool      $referent Is referring ?
   *
   * @return void
   */
  function generateFromOperation(CMbObject $mbObject, $referent = false) {
  }

  /**
   * Get content XML
   *
   * @return array
   */
  function getContentsXML() {
  }

  /**
   * R�cup�ration de l'identifiant source (emetteur)
   *
   * @param DOMNode $node   Node
   * @param bool    $valeur Valeur
   *
   * @return string
   */
  function getIdSource(DOMNode $node, $valeur = true) {
    $xpath = new CHPrimXPath($this);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    
    if ($valeur) {
      // Obligatoire pour MB
      $emetteur = $xpath->queryUniqueNode("hprim:emetteur", $identifiant, false);
  
      return $xpath->queryTextNode("hprim:valeur", $emetteur);
    }
    else {
      return $xpath->queryTextNode("hprim:emetteur", $identifiant);
    }
  }

  /**
   * R�cup�ration de l'identifiant source (emetteur)
   *
   * @param DOMNode $node   Node
   * @param bool    $valeur Valeur
   *
   * @return string
   */
  function getIdCible(DOMNode $node, $valeur = true) {
    $xpath = new CHPrimXPath($this);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    
    if ($valeur) {
      $recepteur = $xpath->queryUniqueNode("hprim:recepteur", $identifiant);
      
      return $xpath->queryTextNode("hprim:valeur", $recepteur);
    }
    else {
      return $xpath->queryTextNode("hprim:recepteur", $identifiant);
    }
  }

  /**
   * R�cup�ration du tag du mediuser
   *
   * @return string
   */
  function getTagMediuser() {
    $this->_ref_echange_hprim->loadRefsInteropActor();
    
    return $this->_ref_echange_hprim->_ref_receiver->_tag_mediuser;
  }

  /**
   * Ajout de l'�l�ment texte
   *
   * @param DOMNode $elParent
   * @param     $elName
   * @param     $elValue
   * @param int $elMaxSize
   *
   * @return DOMElement
   */
  function addTexte(DOMNode $elParent, $elName, $elValue, $elMaxSize = 35) {
    $elValue = substr($elValue, 0, $elMaxSize);
    return $this->addElement($elParent, $elName, $elValue);
  }
  
  function addDateHeure(DOMNode $elParent, $dateTime = null) {
    $this->addElement($elParent, "date", CMbDT::date(null, $dateTime));
    $this->addElement($elParent, "heure", CMbDT::time(null, $dateTime));
  }
  
  function addCodeLibelle(DOMNode $elParent, $nodeName, $code, $libelle) {
    $codeLibelle = $this->addElement($elParent, $nodeName);
    $code = str_replace(" ", "", $code);
    $this->addTexte($codeLibelle, "code", $code, 10);

    if ($libelle) {
      $this->addTexte($codeLibelle, "libelle", $libelle, 35);
    }

    return $codeLibelle;
  }

  function addCodeLibelleAttribute(DOMNode $elParent, $code, $libelle, $attName, $attValue) {
    $code = str_replace(" ", "", $code);
    $this->addTexte($elParent, "code"   , $code, 10);
    $this->addTexte($elParent, "libelle", $libelle, 35);

    $this->addAttribute($elParent, $attName, $attValue);
  }

  function addCodeLibelleCommentaire(DOMNode $elParent, $nodeName, $code, $libelle, $dictionnaire = null, $commentaire = null) {
    $codeLibelleCommentaire = $this->addElement($elParent, $nodeName);

    $this->addTexte($codeLibelleCommentaire, "code", str_replace(" ", "", $code), 10);
    $this->addTexte($codeLibelleCommentaire, "libelle", $libelle, 35);
    $this->addTexte($codeLibelleCommentaire, "dictionnaire", $dictionnaire, 12);
    $this->addCommentaire($codeLibelleCommentaire, $commentaire);

    return $codeLibelleCommentaire;
  }

  function addCommentaire(DOMNode $elParent, $commentaire) {
    $this->addTexte($elParent, "commentaire", $commentaire, 4000);
  }
  
  function addAgent(DOMNode $elParent, $categorie, $code, $libelle) {
    $agent = $this->addCodeLibelle($elParent, "agent", $code, $libelle);
    $this->addAttribute($agent, "categorie", $categorie);
    return $agent;
  }
  
  function addIdentifiantPart(DOMNode $elParent, $partName, $partValue, $referent = null) {
    $part = $this->addElement($elParent, $partName);
    $this->addTexte($part, "valeur", $partValue, 17);
    $this->addAttribute($part, "etat", "permanent");
    $this->addAttribute($part, "portee", "local");
    $ref = ($referent) ? "oui" : "non";
    $this->addAttribute($part, "referent", $ref);
  }
    
  function addUniteFonctionnelle(DOMNode $elParent, COperation $operation) {
    $salle = $operation->updateSalle();

    $nom = CMbString::removeDiacritics($salle->nom);
    $nom = str_replace("'", "", $nom);
    $nom = CMbString::convertHTMLToXMLEntities($nom);

    $this->addCodeLibelle($elParent, "uniteFonctionnelle", substr($nom, 0, 10), "");
  }
  
  function addUniteFonctionnelleResponsable(DOMNode $elParent, $mbOp) {
    $this->addCodeLibelle($elParent, "uniteFonctionnelleResponsable", $mbOp->code_uf, $mbOp->libelle_uf);
  }
  
  function addProfessionnelSante(DOMNode $elParent, $mbMediuser) {
    $this->addElement($elParent, "numeroAdeli", $mbMediuser->adeli);
    $identification = $this->addElement($elParent, "identification");

    $idex = CIdSante400::getMatchFor($mbMediuser, $this->getTagMediuser());

    $this->addElement($identification, "code", $idex->_id ? $idex->id400 : "prat$mbMediuser->user_id");
    $this->addElement($identification, "libelle", $mbMediuser->_view);
    $personne = $this->addElement($elParent, "personne");
    $this->addElement($personne, "nomUsuel", $mbMediuser->_user_last_name);
    $prenoms = $this->addElement($personne, "prenoms");
    $this->addElement($prenoms, "prenom", $mbMediuser->_user_first_name);
  }
  
  function addActeCCAM(DOMNode $elParent, CActeCCAM $mbActeCCAM, CCodable $codable) {
    $acteCCAM = $this->addElement($elParent, "acteCCAM");

    // Gestion des attributs
    $this->addAttribute($acteCCAM, "action"      , "cr�ation");
    $this->addAttribute($acteCCAM, "facturable"  , $mbActeCCAM->facturable ? "oui" : "non");
    $this->addAttribute($acteCCAM, "valide"      , "oui");
    $this->addAttribute($acteCCAM, "documentaire", "non");
    $this->addAttribute($acteCCAM, "gratuit"     , "non");
    if ($mbActeCCAM->_rembex) {
      $this->addAttribute($acteCCAM, "remboursementExceptionnel", "oui");
    }
    
    $identifiant = $this->addElement($acteCCAM, "identifiant");
    $this->addElement($identifiant, "emetteur", "acte{$mbActeCCAM->_id}");
    
    $this->addElement($acteCCAM, "codeActe"    , $mbActeCCAM->code_acte);
    $this->addElement($acteCCAM, "codeActivite", $mbActeCCAM->code_activite);
    $this->addElement($acteCCAM, "codePhase"   , $mbActeCCAM->code_phase);
    
    // Date et heure de l'op�ration
    if ((CAppUI::conf("hprimxml date_heure_acte") == "operation") && $codable instanceof COperation) {
      $date  = $codable->date ? $codable->date : $codable->_ref_plageop->date;
      
      $time_operation = ($codable->time_operation == "00:00:00") ? null : $codable->time_operation;
      $heure = CValue::first(
        $codable->debut_op,
        $codable->entree_salle,
        $time_operation,
        $codable->horaire_voulu
      );
      
      $sejour = $codable->_ref_sejour;
      if ("$date $heure" < $sejour->entree) {
        $date  = CMbDT::date($sejour->entree);
        $heure = CMbDT::time($sejour->entree);
      }
      if ("$date $heure" > $sejour->sortie) {
        $date  = CMbDT::date($sejour->sortie);
        $heure = CMbDT::time($sejour->sortie);
      }
    }
    // Date et heure de l'ex�cution de l'acte
    else {
      $date  = CMbDT::date($mbActeCCAM->execution);
      $heure = CMbDT::time($mbActeCCAM->execution);
    }
    
    $execute = $this->addElement($acteCCAM, "execute");
    $this->addElement($execute, "date" , $date);
    $this->addElement($execute, "heure", $heure);
    
    $mbExecutant      = $mbActeCCAM->loadRefExecutant();
    $executant        = $this->addElement($acteCCAM, "executant");
    $medecins         = $this->addElement($executant, "medecins");
    $medecinExecutant = $this->addElement($medecins, "medecinExecutant");
    $this->addAttribute($medecinExecutant, "principal", "oui");
    $medecin          = $this->addElement($medecinExecutant, "medecin");
    $this->addProfessionnelSante($medecin, $mbExecutant);
    //$this->addUniteFonctionnelle($executant, $codable);
    
    $modificateurs = $this->addElement($acteCCAM, "modificateurs");
    foreach ($mbActeCCAM->_modificateurs as $mbModificateur) {
      $this->addElement($modificateurs, "modificateur", $mbModificateur);
    }

    if ($mbActeCCAM->code_association) {
      $this->addElement($acteCCAM, "codeAssociationNonPrevue", $mbActeCCAM->code_association);
    }

    if ($mbActeCCAM->_anesth && $mbActeCCAM->object_class == "COperation") {
      $type_anesth = $mbActeCCAM->loadTargetObject()->_ref_type_anesth;

      $extension_documentaire = $mbActeCCAM->extension_documentaire ? $mbActeCCAM->extension_documentaire : $type_anesth->ext_doc;
      $this->addElement($acteCCAM, "codeExtensionDocumentaire", $extension_documentaire);
    }

    // Gestion des dents
    if ($mbActeCCAM->_dents) {
      $positionsDentaires = $this->addElement($acteCCAM, "positionsDentaires");
      foreach ($mbActeCCAM->_dents as $_dent) {
        $this->addElement($positionsDentaires, "positionDentaire", $_dent);
      }
    }
    
    $montant = $this->addElement($acteCCAM, "montant");
    if ($mbActeCCAM->montant_depassement > 0) {
      $montantDepassement = $this->addElement($montant, "montantDepassement", sprintf("%.2f", $mbActeCCAM->montant_depassement));
      if (CAppUI::conf("dPpmsi systeme_facturation") == "siemens") {
        if (CAppUI::conf("dPsalleOp CActeCCAM envoi_motif_depassement")) {
          $this->addAttribute($montantDepassement, "motif", "d");
        }         
      }
      else {
        if ($mbActeCCAM->motif_depassement) {
          $this->addAttribute($montantDepassement, "motif", $mbActeCCAM->motif_depassement);
        }
      }
    }
    
    return $acteCCAM;
  }
  
  function addActeNGAP(DOMNode $elParent, CActeNGAP $mbActeNGAP, CCodable $codable) {
    $acteNGAP = $this->addElement($elParent, "acteNGAP");
    $this->addAttribute($acteNGAP, "action", "cr�ation");
    
    // executionNuit
    if ($mbActeNGAP->complement == "N") {
      // non     non r�alis� de nuit
      // 1t      r�alis� 1re tranche de nuit
      // 2t      2me tranche
      $executionNuit = "non";

      $time = CMbDT::time($mbActeNGAP->execution);
      if (("20:00:00" <= $time && $time <= "23:59:59") || ("06:00:00" <= $time && $time < "08:00:00")) {
        $executionNuit = "1t";
      }
      elseif ("00:00:00" <= $time && $time < "05:59:59") {
        $executionNuit = "2t";
      }
      $this->addAttribute($acteNGAP, "executionNuit", $executionNuit);
    }
    
    // executionDimancheJourFerie
    if ($mbActeNGAP->complement == "F") {
       $this->addAttribute($acteNGAP, "executionDimancheJourFerie", "oui");
    }    
    
    $identifiant = $this->addElement($acteNGAP, "identifiant");
    $this->addElement($identifiant, "emetteur", "acte{$mbActeNGAP->_id}");
    
    $this->addElement($acteNGAP, "lettreCle"    , $mbActeNGAP->code);
    $this->addElement($acteNGAP, "coefficient"  , $mbActeNGAP->demi ? $mbActeNGAP->coefficient * 0.5 : $mbActeNGAP->coefficient);
    // d�nombrement doit �tre �gale � 1 pour les actes ngap (CS, C etc....), 
    // elle varie seulement pour les actes de Biologie "d�nombrement = nombre de code affin�s"
    // $this->addElement($acteNGAP, "denombrement" , 1);
    $this->addElement($acteNGAP, "quantite"     , $mbActeNGAP->quantite);
    
    $execute = $this->addElement($acteNGAP, "execute");
    $this->addElement($execute, "date" , CMbDT::date($mbActeNGAP->execution));
    $this->addElement($execute, "heure", CMbDT::time($mbActeNGAP->execution));
    
    $mbExecutant      = $mbActeNGAP->_ref_executant;
    $prestataire      = $this->addElement($acteNGAP, "prestataire");
    $medecins         = $this->addElement($prestataire, "medecins");
    $medecin          = $this->addElement($medecins, "medecin");
    $this->addProfessionnelSante($medecin, $mbExecutant);
    
    $montant = $this->addElement($acteNGAP, "montant");
    if ($mbActeNGAP->montant_depassement > 0) {
      $this->addElement($montant, "montantDepassement", sprintf("%.2f", $mbActeNGAP->montant_depassement));
    }
    
    return $acteNGAP;
  }
    
  function addActeCCAMAcquittement(DOMNode $elParent, $acteCCAM) {
    $mbActeCCAM = $acteCCAM["acteCCAM"];
    
    $this->addAttribute($elParent, "valide", "oui");
    
    $intervention = $this->addElement($elParent,     "intervention");
    $identifiant  = $this->addElement($intervention, "identifiant");
    $this->addElement($identifiant, "emetteur",  $acteCCAM["idCibleIntervention"]);
    $this->addElement($identifiant, "recepteur", $acteCCAM["idSourceIntervention"]);
    
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur",  $acteCCAM["idSourceActeCCAM"]);
    $this->addElement($identifiant, "recepteur", $acteCCAM["idCibleActeCCAM"]);

    $this->addElement($elParent, "codeActe",     $mbActeCCAM["code_acte"]);
    $this->addElement($elParent, "codeActivite", $mbActeCCAM["code_activite"]);
    $this->addElement($elParent, "codePhase",    $mbActeCCAM["code_phase"]);
    
    $execute = $this->addElement($elParent, "execute");
    $this->addElement($execute, "date",  CMbDT::date($mbActeCCAM["date"]));
    $this->addElement($execute, "heure", CMbDT::time($mbActeCCAM["heure"]));
  }

  function addActeNGAPAcquittement(DOMNode $elParent, $acteNGAP) {
    $mbActeNGAP = $acteNGAP["acteNGAP"];

    $this->addAttribute($elParent, "valide", "oui");

    $intervention = $this->addElement($elParent,     "intervention");
    $identifiant  = $this->addElement($intervention, "identifiant");
    $this->addElement($identifiant, "emetteur",  $acteNGAP["idCibleIntervention"]);
    $this->addElement($identifiant, "recepteur", $acteNGAP["idSourceIntervention"]);

    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur",  $acteNGAP["idSourceActeNGAP"]);
    $this->addElement($identifiant, "recepteur", $acteNGAP["idCibleActeNGAP"]);

    $this->addElement($elParent, "lettreCle",    $mbActeNGAP["code"]);
    $this->addElement($elParent, "coefficient",  $mbActeNGAP["coefficient"]);

    $execute = $this->addElement($elParent, "execute");
    $this->addElement($execute, "date",  CMbDT::date($mbActeNGAP["date"]));
    $this->addElement($execute, "heure", CMbDT::time($mbActeNGAP["heure"]));
  }

  function addPatientError(DOMNode $elParent, $data) {
    if (!$data) {
      return;
    }
    $patient = $this->importNode($data["patient"], true);
    $elParent->appendChild($patient);
  }

  function addInterventionError(DOMNode $elParent, $data) {
    if (!$data) {
      return;
    }
    $intervention = $this->addElement($elParent, "intervention");
    $identifiant  = $this->addElement($intervention, "identifiant");
    $this->addElement($identifiant, "emetteur", $data["idCibleIntervention"]);
    $this->addElement($identifiant, "recepteur", $data["idSourceIntervention"]);
  }

  function addPatient(DOMNode $elParent, CPatient $mbPatient, $referent = false, $light = false) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    if (!$referent) {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbPatient->_id, $referent);
      if ($mbPatient->_IPP) {
        $this->addIdentifiantPart($identifiant, "recepteur", $mbPatient->_IPP, $referent);
      }
    }
    else {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbPatient->_IPP, $referent);
      
      if (isset($mbPatient->_id400)) {
        $this->addIdentifiantPart($identifiant, "recepteur", $mbPatient->_id400, $referent);
      }
    }
    
    // Ajout typePersonnePhysique
    $this->addPersonnePhysique($elParent, $mbPatient, $light);
  }
  
  function addPersonnePhysique($elParent, $mbPatient, $light = false) {
    $personnePhysique = $this->addElement($elParent, "personnePhysique");
    
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
    );
    $sexe = $mbPatient->sexe ? $sexeConversion[$mbPatient->sexe] : "I";
    $this->addAttribute($personnePhysique, "sexe", $sexe);
    
    // Ajout typePersonne
    $this->addPersonne($personnePhysique, $mbPatient, $light);
    
    $dateNaissance = $this->addElement($personnePhysique, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->naissance);
    
    $lieuNaissance = $this->addElement($personnePhysique, "lieuNaissance");
    $this->addElement($lieuNaissance, "ville", $mbPatient->lieu_naissance);
    if ($mbPatient->pays_naissance_insee) {
      $this->addElement($lieuNaissance, "pays", str_pad($mbPatient->pays_naissance_insee, 3, '0', STR_PAD_LEFT));
    }
    $this->addElement($lieuNaissance, "codePostal", $mbPatient->cp_naissance);
  }
  
  function addPersonne($elParent, $mbPersonne, $light = false) {
    $personne = array();
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "mlle"  => "mlle",
      "m"     => "mr",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
      
    if ($mbPersonne instanceof CPatient) {
      $personne['nom'] = $mbPersonne->nom;
      $personne['nomNaissance'] = $mbPersonne->nom_jeune_fille;
      if (isset($mbPersonne->_prenoms)) {
        foreach ($mbPersonne->_prenoms as $mbKey => $mbPrenom) {
          if ($mbKey < 3) {
            $personne['prenoms'][] = $mbPrenom; 
          }
        }
      }
      if (!$light) {
        $personne['civilite'] = $mbPersonne->civilite;
      }
      $personne['ligne'] = $mbPersonne->adresse;
      $personne['ville'] = $mbPersonne->ville;
      $personne['pays'] = $mbPersonne->pays_insee;
      $personne['codePostal'] = $mbPersonne->cp;
      $personne['tel'] = $mbPersonne->tel;
      $personne['tel2'] = $mbPersonne->tel2;
      if (!$light) {
        $personne['email'] = $mbPersonne->email;
      }
    }
    else if ($mbPersonne instanceof CMedecin) {
      $personne['nom'] = $mbPersonne->nom;
      $personne['nomNaissance'] = $mbPersonne->jeunefille;
      if (!$light) {
        $personne['civilite'] = "";
      }
      $personne['prenoms'][] = $mbPersonne->prenom;
      $personne['ligne'] = $mbPersonne->adresse;
      $personne['ville'] = $mbPersonne->ville;
      $personne['codePostal'] = $mbPersonne->cp;
      $personne['pays'] = "";
      $personne['tel'] = $mbPersonne->tel;
      $personne['tel2'] = $mbPersonne->portable;
      if (!$light) {
        $personne['email'] = $mbPersonne->email;
      }
    }
    else if ($mbPersonne instanceof CMediusers) {
      $personne['nom'] = $mbPersonne->_user_last_name;
      $personne['nomNaissance'] = "";
      if (!$light) {
        $personne['civilite'] = "";
      }
      $personne['prenoms'][] = $mbPersonne->_user_first_name;
      $personne['ligne'] = $mbPersonne->_user_adresse;
      $personne['ville'] = $mbPersonne->_user_ville;
      $personne['codePostal'] = $mbPersonne->_user_cp;
      $personne['pays'] = "";
      $personne['tel'] = $mbPersonne->_user_phone;
      $personne['tel2'] = "";
      if (!$light) {
        $personne['email'] = $mbPersonne->_user_email;
      }
    }
    
    if (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["uppercase_fields"]) {
      $personne['nom']          = CMbString::upper($personne['nom']);
      $personne['nomNaissance'] = CMbString::upper($personne['nomNaissance']);
    }
    
    $this->addTexte($elParent, "nomUsuel", $personne['nom']);
    $this->addTexte($elParent, "nomNaissance", $personne['nomNaissance']);
    $prenoms = $this->addElement($elParent, "prenoms");
    foreach ($personne['prenoms'] as $key => $prenom) {
      if ($key == 0) {
        if (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["uppercase_fields"]) {
          $prenom = CMbString::upper($prenom ? $prenom : $personne['nom']);
        }
        $this->addTexte($prenoms, "prenom", $prenom ? $prenom : $personne['nom']);
      }
      else {
        if (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["uppercase_fields"]) {
          $prenom = CMbString::upper($prenom);
        }
        $this->addTexte($prenoms, "prenom", $prenom);
      }
    }
    if (!$light) {
      if ($personne['civilite']) {
        $civiliteHprim = $this->addElement($elParent, "civiliteHprim");
        $this->addAttribute($civiliteHprim, "valeur", $civiliteHprimConversion[$personne['civilite']]);    
      }
    }
    $adresses = $this->addElement($elParent, "adresses");
    $adresse  = $this->addElement($adresses, "adresse");
    $pattern  = "/[^0-9a-z���������������������������-]/i";
    $this->addTexte($adresse, "ligne", substr(preg_replace($pattern, " ", $personne['ligne']), 0, 35));
    $this->addTexte($adresse, "ville", $personne['ville']);
    if ($personne['pays']) {
      $this->addElement($adresse, "pays", str_pad($personne['pays'], 3, '0', STR_PAD_LEFT));
    }
    $this->addElement($adresse, "codePostal", $personne['codePostal']);

    $telephones = $this->addElement($elParent, "telephones");
    $this->addElement($telephones, "telephone", $personne['tel']);
    if (isset($personne['tel2'])) {
      $this->addElement($telephones, "telephone", $personne['tel2']);
    }
    
    if (!$light) {
      $emails = $this->addElement($elParent, "emails");
      $this->addElement($emails, "email", $personne['email']);
    }
  }
  
  function addErreurAvertissement($elParent, $statut, $code, $libelle, $commentaires = null, $mbObject = null) {
    $erreurAvertissement = $this->addElement($elParent, "erreurAvertissement");
    $this->addAttribute($erreurAvertissement, "statut", $statut);
     
    $dateHeureEvenementConcerne =  $this->addElement($erreurAvertissement, "dateHeureEvenementConcerne");
    $this->addElement($dateHeureEvenementConcerne, "date", CMbDT::date());
    $this->addElement($dateHeureEvenementConcerne, "heure", CMbDT::time());
    
    $evenementPatients = $this->addElement($erreurAvertissement, $this->_sous_type_evt);
    $this->addElement($evenementPatients, "identifiantPatient");
    
    if ($this->_sous_type_evt == "fusionPatient") {
      $this->addElement($evenementPatients, "identifiantPatientElimine");
    }
    
    if ($this->_sous_type_evt == "venuePatient") {
      $this->addElement($evenementPatients, "identifiantVenue");
    }
    
    if ($this->_sous_type_evt == "debiteursVenue") {
      $this->addElement($evenementPatients, "identifiantVenue");
      $debiteurs = $this->addElement($evenementPatients, "debiteurs");
      $debiteur = $this->addElement($debiteurs, "debiteur");
      $this->addElement($debiteur, "identifiantParticulier");
    }
    
    if ($this->_sous_type_evt == "mouvementPatient") {
      $this->addElement($evenementPatients, "identifiantVenue");
      $this->addElement($evenementPatients, "identifiantMouvement");
    }
    
    if ($this->_sous_type_evt == "fusionVenue") {
      $this->addElement($evenementPatients, "identifiantVenue");
      $this->addElement($evenementPatients, "identifiantVenueEliminee");
    }
    
    $observations = $this->addElement($erreurAvertissement, "observations");
    $this->addObservation($observations, $code, $libelle, CMbString::convertHTMLToXMLEntities($commentaires));
  }
  
  function addObservation($elParent, $code, $libelle, $commentaires = null) {
    $observation = $this->addElement($elParent, "observation");
    
    $this->addElement($observation, "code", substr($code, 0, 17));
    $this->addElement($observation, "libelle", substr($libelle, 0, 80));
    $this->addElement($observation, "commentaire", substr($commentaires, 0, 4000)); 
  }
  
  function addReponse($elParent, $statut, $codes, $mbObject = null, $commentaires = null) {
    if ($statut == "ok") {
      return; 
    }  
    
    $erreur = $this->addElement($elParent, "erreur");
    
    $libelle = null;
    if (is_array($codes)) {
      $code = implode("", $codes);
      foreach ($codes as $_code) {
        $libelle .= CAppUI::tr("hprimxml-error-$_code");
      }
    }
    else {
      $code    = $codes;
      $libelle = CAppUI::tr("hprimxml-error-$code");
    }
    $this->addElement($erreur, "code"       , substr($code, 0, 17));
    $this->addElement($erreur, "libelle"    , substr($libelle, 0, 80));
    if ($commentaires) {
      $this->addElement($erreur, "commentaire", substr("$libelle : \"$commentaires\"", 0, 4000));
    }
  }
  
  function addReponseCCAM($elParent, $statut, $codes, $acteCCAM, $mbObject = null, $commentaires = null) {
    $reponse = $this->addElement($elParent, "reponse");
    $this->addAttribute($reponse, "statut", $statut);
      
    $elActeCCAM = $this->addElement($reponse, "acteCCAM");
    $this->addActeCCAMAcquittement($elActeCCAM, $acteCCAM);
    
    $this->addReponse($reponse, $statut, $codes, $mbObject, $commentaires);
  }

  function addReponseNGAP($elParent, $statut, $codes, $acteNGAP, $mbObject = null, $commentaires = null) {
    $reponse = $this->addElement($elParent, "reponse");
    $this->addAttribute($reponse, "statut", $statut);

    $elActeNGAP = $this->addElement($reponse, "acteNGAP");
    $this->addActeNGAPAcquittement($elActeNGAP, $acteNGAP);

    $this->addReponse($reponse, $statut, $codes, $mbObject, $commentaires);
  }

  function addReponseGeneral($elParent, $statut, $codes, $codeErr = null, $mbObject = null, $commentaires = null, $data = null) {
    $reponse = $this->addElement($elParent, "reponse");
    $this->addAttribute($reponse, "statut", $statut);
    if ($codeErr) {
      $this->addAttribute($reponse, "codeErreur", $codeErr);
    }
    $this->addInterventionError($reponse, $data);
    $this->addReponse($reponse, $statut, $codes, $mbObject, $commentaires);
  }
  
  function addReponseIntervention($elParent, $statut, $codes, $acteCCAM, $mbObject = null, $commentaires = null) {
    $reponse = $this->addElement($elParent, "reponse");
    $this->addAttribute($reponse, "statut", $statut);
      
    $intervention = $this->addElement($reponse, "intervention");
    $this->addInterventionAcquittement($intervention, $mbObject);
    
    $this->addReponse($reponse, $statut, $codes, $mbObject, $commentaires);
  }
  
  function addInterventionAcquittement($elParent, $operation = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", isset($operation->_id) ? $operation->_id : 0);
  }
  
  function getTypeEvenementPatient() {
    $xpath = new CHPrimXPath($this);
        
    $evenementPatient = $xpath->query("/hprim:evenementsPatients/hprim:evenementPatient/*");
    $type = null;
    $evenements = CHPrimXMLEvenementsPatients::$evenements;
    foreach ($evenementPatient as $_evenementPatient) {
      if (array_key_exists($_evenementPatient->tagName, $evenements)) {
        $type = $_evenementPatient->tagName;
      }
    }

    return $type;
  }
  
  function addMedecin($elParent, $praticien, $lien) {
    $medecin = $this->addElement($elParent, "medecin");
    $this->addAttribute($medecin, "lien", $lien);
    $this->addElement($medecin, "numeroAdeli", $praticien->adeli);
    $identification = $this->addElement($medecin, "identification");

    $idex = CIdSante400::getMatchFor($praticien, $this->getTagMediuser());

    $this->addElement($identification, "code", $idex->_id ? $idex->id400 : $praticien->_id);
    $this->addElement($identification, "libelle", $praticien->_view);
    $personne = $this->addElement($medecin, "personne");
    $this->addPersonne($personne, $praticien);
  }
  
  function addMedecinResponsable($elParent, $praticien) {
    $medecinResponsable = $this->addElement($elParent, "medecinResponsable");

    $this->addElement($medecinResponsable, "numeroAdeli", $praticien->adeli);

    $identification = $this->addElement($medecinResponsable, "identification");

    $idex = CIdSante400::getMatchFor($praticien, $this->getTagMediuser());

    $this->addElement($identification, "code", $idex->_id ? $idex->id400 : $praticien->_id);
    $this->addElement($identification, "libelle", $praticien->_view);
    $personne = $this->addElement($medecinResponsable, "personne");
    $this->addPersonne($personne, $praticien);
  }
  
  function addVenue($elParent, CSejour $mbVenue, $referent = false, $light = false) {
    if (!$light) {
      // Ajout des attributs du s�jour
      $this->addAttribute($elParent, "confidentiel", "non");
      // Etat d'une venue : encours, cl�tur�e ou pr�admission
      $etatConversion = array (
        "preadmission" => "pr�admission",
        "encours"  => "encours",
        "cloture" => "cl�tur�e"
      );
      $this->addAttribute($elParent, "etat", $etatConversion[$mbVenue->_etat]);
      $this->addAttribute($elParent, "facturable", ($mbVenue->facturable)  ? "oui" : "non");
      $this->addAttribute($elParent, "declarationMedecinTraitant", ($mbVenue->_adresse_par_prat)  ? "oui" : "non");
    }
    
    $identifiant = $this->addElement($elParent, "identifiant");
    
    if (!$referent) {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->sejour_id, $referent);
      if ($mbVenue->_NDA) {
        $this->addIdentifiantPart($identifiant, "recepteur", $mbVenue->_NDA, $referent);
      }
    }
    else {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->_NDA, $referent);
      
      if (isset($mbVenue->_id400)) {
        $this->addIdentifiantPart($identifiant, "recepteur", $mbVenue->_id400, $referent);
      }
    }
    
    $natureVenueHprim = $this->addElement($elParent, "natureVenueHprim");

    $attrNatureVenueHprim = array (
      "comp"    => "hsp",
      "ambu"    => ((CAppUI::conf("hprimxml $this->evenement ") == "1.053") ||
                    (CAppUI::conf("hprimxml $this->evenement version") == "1.07") ||
                    (CAppUI::conf("hprimxml $this->evenement version") == "1.07") ||
                    (CAppUI::conf("hprimxml $this->evenement version") == "1.072")) ?
                      "ambu" : "hsp",
      "urg"     => "hsp",
      "psy"     => "hsp",
      "ssr"     => "hsp",
      "exte"    => ((CAppUI::conf("hprimxml $this->evenement version") == "1.053") ||
                    (CAppUI::conf("hprimxml $this->evenement version") == "1.07") ||
                    (CAppUI::conf("hprimxml $this->evenement version") == "1.07") ||
                    (CAppUI::conf("hprimxml $this->evenement version") == "1.072")) ?
                      (CAppUI::conf("hprimxml $this->evenement version") == "1.053")
                        ? "exte" : "ext"
                      : "hsp",
      "consult" => "cslt",
      "seances" => "sc"
    );
    $this->addAttribute($natureVenueHprim, "valeur", $attrNatureVenueHprim[$mbVenue->type]);
    
    $entree = $this->addElement($elParent, "entree");
    
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", CMbDT::date($mbVenue->entree));
    $this->addElement($dateHeureOptionnelle, "heure", CMbDT::time($mbVenue->entree));
    
    $modeEntree = $this->addElement($entree, "modeEntree");
    // mode d'entr�e inconnu
    $mode = "09";
    // admission apr�s consultation d'un m�decin de l'�tablissement
    if ($mbVenue->_ref_consult_anesth && $mbVenue->_ref_consult_anesth->_id) {
      $mode = "01";
    }
    // malade envoy� par un m�decin ext�rieur
    if ($mbVenue->_ref_adresse_par_prat && $mbVenue->_ref_adresse_par_prat->_id) {
      $mode = "02";
    }
    $this->addAttribute($modeEntree, "valeur", $mode);
    
    if (!$light) {    
      $medecins = $this->addElement($elParent, "medecins");
      
      // Traitement du medecin traitant du patient
      $_ref_medecin_traitant = $mbVenue->_ref_patient->_ref_medecin_traitant;
      if ($_ref_medecin_traitant && $_ref_medecin_traitant->_id) {
        if ($_ref_medecin_traitant->adeli) {
          $this->addMedecin($medecins, $_ref_medecin_traitant, "trt");
        }
      }
      
      // Traitement du medecin adressant
      $_ref_adresse_par_prat = $mbVenue->_ref_adresse_par_prat;
      if ($mbVenue->_adresse_par_prat) {
        if ($_ref_adresse_par_prat && $_ref_adresse_par_prat->adeli) {
          $this->addMedecin($medecins, $_ref_adresse_par_prat, "adrs");
        }
      }
    
      // Traitement du responsable du s�jour
      $this->addMedecin($medecins, $mbVenue->_ref_praticien, "rsp");
      
      // Traitement des prescripteurs
      $_ref_prescripteurs = $mbVenue->_ref_prescripteurs;
      if (is_array($_ref_prescripteurs)) {
        foreach ($_ref_prescripteurs as $prescripteur) {
          $this->addMedecin($medecins, $prescripteur, "prsc");
        }
      }
      
      // Traitement des intervenant (ayant effectu�s des actes)
      $_ref_actes_ccam = $mbVenue->_ref_actes_ccam;
      if (is_array($_ref_actes_ccam)) {
        foreach ($_ref_actes_ccam as $acte_ccam) {
          $intervenant = $acte_ccam->_ref_praticien;
          $this->addMedecin($medecins, $intervenant, "intv");
        }
      }
    }
    
    // Cas dans lequel on transmet pas de sortie tant que l'on a pas la sortie r�elle
    if (!$mbVenue->sortie_reelle && (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["send_sortie_prevue"] == 0)) {
      return;
    }  
    
    $sortie = $this->addElement($elParent, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", CMbDT::date($mbVenue->sortie));
    $this->addElement($dateHeureOptionnelle, "heure", CMbDT::time($mbVenue->sortie));
    
    if ($mbVenue->mode_sortie) {
      $modeSortieHprim = $this->addElement($sortie, "modeSortieHprim");
      //retour au domicile
      if ($mbVenue->mode_sortie == "normal") {
        $modeSortieEtablissementHprim = "04";
      } 
      // d�c�s
      else if ($mbVenue->mode_sortie == "deces") {
        $modeSortieEtablissementHprim = "05";
      } 
      // mutation
      else if ($mbVenue->mode_sortie == "mutation") {
        $modeSortieEtablissementHprim = "08";
      }
      // autre transfert dans un autre CH
      else if ($mbVenue->mode_sortie == "transfert") {
        $modeSortieEtablissementHprim = "02";
      }
      $this->addElement($modeSortieHprim, "code", $modeSortieEtablissementHprim);
      $this->addElement($modeSortieHprim, "libelle", $mbVenue->mode_sortie);
      
      if ($mbVenue->etablissement_sortie_id) {
        $destination = $this->addElement($modeSortieHprim, "destination");
        $this->addElement($destination, "libelle", $mbVenue->etablissement_sortie_id);
      }
        
      $this->addAttribute($modeSortieHprim, "valeur", $modeSortieEtablissementHprim);
    }      
    
    // @todo Voir comment int�grer le placement pour la v. 1.01 et v. 1.05
    /*
    if (!$light) {
      $placement = $this->addElement($elParent, "Placement");
      $modePlacement = $this->addElement($placement, "modePlacement");
      $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbVenue->modalite);
      $this->addElement($modePlacement, "libelle", substr($mbVenue->_view, 0, 80));   
      
      $datePlacement = $this->addElement($placement, "datePlacement");
      $this->addElement($datePlacement, "date", CMbDT::date($mbVenue->entree));
    }*/
  }

  function addVoletMedical($elParent, CSejour $sejour) {
    $sejour->loadRefDossierMedical();

    // constantes
    $this->addConstantes($elParent, $sejour);

    // antecedents
    $this->addAntecedents($elParent, $sejour);

    // allergies
    $this->addAllergies($elParent, $sejour);

    // traitements
    $this->addTraitements($elParent, $sejour);

    // antecedentsFamiliaux
    $this->addAntecedentsFamiliaux($elParent, $sejour);
  }

  function addConstantes($elParent, CSejour $sejour) {
    $constantes_medicales = $sejour->loadListConstantesMedicales();

    $constantes = $this->addElement($elParent, "constantes");
    foreach ($constantes_medicales as $_constante) {
      $this->addListConstante($constantes, $_constante);
    }
  }

  function addListConstante($elParent, CConstantesMedicales $constante_medicale) {
    $list_constantes = CConstantesMedicales::$list_constantes;

    foreach ($list_constantes as $type => $params) {
      if ($constante_medicale->$type == "") {
        continue;
      }

      if (!array_key_exists($type, self::$list_constantes)) {
        continue;
      }

      $this->addConstante($elParent, $constante_medicale, $type);
    }
  }

  function addConstante($elParent, CConstantesMedicales $constante_medicale, $type) {
    $list_constantes = CConstantesMedicales::$list_constantes;

    $constante = $this->addElement($elParent, "constante");
    $this->addAttribute($constante, "nature", CMbArray::get(CHPrimXMLDocument::$list_constantes, $type));

    $valeur = $this->addElement($constante, "valeur", $constante_medicale->$type);
    $this->addAttribute($valeur, "unite", CMbArray::get($list_constantes[$type], "unit"));

    $dateObservation = $this->addElement($constante, "dateObservation");
    $this->addDateHeure($dateObservation, $constante_medicale->datetime);
  }

  function addAntecedents($elParent, CSejour $sejour) {
    $antecedents = $this->addElement($elParent, "antecedents");

    $all_antecedents = $sejour->loadRefDossierMedical()->loadRefsAntecedents();

    foreach ($all_antecedents as $_antecedent) {
      // On exclut les ant�c�dents familliaux et les allergies
      if ($_antecedent->type == "fam" || $_antecedent->type == "alle") {
        continue;
      }

      $this->addAntecedent($antecedents, $_antecedent);
    }
  }

  function addAntecedent($elParent, CAntecedent $antecedent) {
    $elAntecedent = $this->addElement($elParent, "antecedent");

    $rques = CMbString::htmlspecialchars($antecedent->rques);
    $rques = CMbString::convertHTMLToXMLEntities($rques);

    if (preg_match_all("/[A-Z]\d{2}\.?\d{0,2}/i", $rques, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $_matches) {
        foreach ($_matches as $_match) {
          $this->addCodeLibelleCommentaire($elAntecedent, "identification", $_match, $rques, "CIM10");
        }
      }
    }
    else {
      $this->addCodeLibelle($elAntecedent, "identification", $antecedent->_id, $rques);
    }

    if ($antecedent->date) {
      $date_explode = explode("-", $antecedent->date);
      if ($date_explode[1] == "00") {
        $date_explode[1] = "01";
      }
      if ($date_explode[2] == "00") {
        $date_explode[2] = "01";
      }

      $date = $date_explode[0]."-".$date_explode[1]."-".$date_explode[2];

      $this->addElement($elAntecedent, "dateDebutEstimee", $date);
    }
  }

  function addAllergies($elParent, CSejour $sejour) {
    $allergies = $this->addElement($elParent, "allergies");

    $all_antecedents = $sejour->_ref_dossier_medical->_all_antecedents;

    foreach ($all_antecedents as $_antecedent) {
      if ($_antecedent->type != "alle") {
        continue;
      }

      $rques = CMbString::htmlspecialchars($_antecedent->rques);
      $rques = CMbString::convertHTMLToXMLEntities($rques);

      $allergie = $this->addElement($allergies, "allergie");
      if (preg_match_all("/[A-Z]\d{2}\.?\d{0,2}/i", $rques, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $_matches) {
          foreach ($_matches as $_match) {
            $this->addCodeLibelleCommentaire($allergie, "allergene", $_match, $rques, "CIM10");
          }
        }
      }
      else {
        $this->addCodeLibelle($allergie, "allergene", $_antecedent->_id, $rques);
      }

      if ($_antecedent->date) {
        $date_explode = explode("-", $_antecedent->date);
        if ($date_explode[1] == "00") {
          $date_explode[1] = "01";
        }
        if ($date_explode[2] == "00") {
          $date_explode[2] = "01";
        }

        $date = $date_explode[0]."-".$date_explode[1]."-".$date_explode[2];

        $this->addElement($allergie, "dateDebutEstimee", $date);
      }
    }
  }

  function addTraitements($elParent, CSejour $sejour) {
    $elTraitements = $this->addElement($elParent, "traitements");

    // Traitements du patient
    $patient = $sejour->_ref_patient;
    $traitements = $patient->loadRefDossierMedical()->loadRefsTraitements();

    foreach ($traitements as $_traitement) {
      $this->addTraitement($elTraitements, $_traitement);
    }

    $prescription = $patient->_ref_dossier_medical->loadRefPrescription();
    if ($prescription && is_array($prescription->_ref_prescription_lines)) {
      foreach($prescription->_ref_prescription_lines as $_line) {
        $_line->loadRefsPrises();

        $elTraitement = $this->addCodeLibelleCommentaire($elTraitements, "traitement", $_line->code_cip, $_line->_ucd_view, "CIP", $_line->commentaire);

        $this->addElement($elTraitement, "dateDebutEstimee", $_line->debut);
        $this->addElement($elTraitement, "dateFinEstimee", $_line->fin);
      }
    }
  }

  function addTraitement($elParent, CTraitement $traitement) {
    $rques = CMbString::htmlspecialchars($traitement->traitement);
    $rques = CMbString::convertHTMLToXMLEntities($rques);

    $elTraitement = $this->addCodeLibelle($elParent, "traitement", $traitement->_id, $rques);

    $this->addElement($elTraitement, "dateDebutEstimee", $traitement->debut);
    $this->addElement($elTraitement, "dateFinEstimee", $traitement->fin);
  }

  function addAntecedentsFamiliaux($elParent, CSejour $sejour) {
    $antecedentsFamiliaux = $this->addElement($elParent, "antecedentsFamiliaux");

    $all_antecedents = $sejour->_ref_dossier_medical->_all_antecedents;

    foreach ($all_antecedents as $_antecedent) {
      if ($_antecedent->type != "fam") {
        continue;
      }

      $antecedentFamilial = $this->addElement($antecedentsFamiliaux, "antecedentFamilial");
      $this->addElement($antecedentFamilial, "parent", "pr");
      $this->addAntecedent($antecedentFamilial, $_antecedent);
    }
  }
  
  function addIntervention($elParent, COperation $operation, $referent = null, $light = false) {
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", $operation->_id);
    $last_idex = $operation->_ref_last_id400;
    if (isset($last_idex->_id)) {
      $this->addElement($identifiant, "recepteur", $last_idex->id400);
    }
      
    $sejour = $operation->loadRefSejour();
    
    if (!$operation->plageop_id) {
      $operation->completeField("date");
    }
    
    // Calcul du d�but de l'intervention
    $mbOpDate = CValue::first(
      $operation->_ref_plageop->date,
      $operation->date
    );
    
    $time_operation = ($operation->time_operation == "00:00:00") ? null : $operation->time_operation;
    $mbOpHeureDebut = CValue::first(
      $operation->debut_op, 
      $operation->entree_salle, 
      $time_operation,
      $operation->horaire_voulu,
      $operation->_ref_plageop->debut
    );
    $mbOpDebut = CMbRange::forceInside($sejour->entree, $sejour->sortie, "$mbOpDate $mbOpHeureDebut");
    
    // Calcul de la fin de l'intervention
    $mbOpHeureFin = CValue::first(
      $operation->fin_op, 
      $operation->sortie_salle, 
      CMbDT::addTime($operation->temp_operation, CMbDT::time($mbOpDebut))
    );
    $mbOpFin = CMbRange::forceInside($sejour->entree, $sejour->sortie, "$mbOpDate $mbOpHeureFin");

    $debut = $this->addElement($elParent, "debut");
    $this->addElement($debut, "date" , CMbDT::date($mbOpDebut));
    $this->addElement($debut, "heure", CMbDT::time($mbOpDebut));
    
    $fin = $this->addElement($elParent, "fin");
    $this->addElement($fin, "date" , CMbDT::date($mbOpFin));
    $this->addElement($fin, "heure", CMbDT::time($mbOpFin));

    if ($light) {
      // Ajout des participants
      $mbParticipants = array();
      foreach($operation->_ref_actes_ccam as $acte_ccam) {
        $acte_ccam->loadRefExecutant();
        $mbParticipant = $acte_ccam->_ref_executant;
        $mbParticipants[$mbParticipant->user_id] = $mbParticipant;
      }
      
      $participants = $this->addElement($elParent, "participants");
      foreach ($mbParticipants as $mbParticipant) {
        $participant = $this->addElement($participants, "participant");
        $medecin     = $this->addElement($participant, "medecin");
        $this->addProfessionnelSante($medecin, $mbParticipant);
      }
        
      // Libell� de l'op�ration
      $this->addTexte($elParent, "libelle", $operation->libelle, 80);
    }
    else {
      $this->addUniteFonctionnelle($elParent, $operation);

      // Uniquement le responsable de l�'intervention
      $participants = $this->addElement($elParent, "participants");
      $participant  = $this->addElement($participants, "participant");
      $medecin      = $this->addElement($participant, "medecin");
      $this->addProfessionnelSante($medecin, $operation->loadRefChir());
        
      // Libell� de l'op�ration
      $this->addTexte($elParent, "libelle", $operation->libelle, 4000);
    
      // Remarques sur l'op�ration
      $this->addTexte($elParent, "commentaire", CMbString::convertHTMLToXMLEntities("$operation->materiel - $operation->rques"), 4000);
      
      // Conventionn�e ?
      $this->addElement($elParent, "convention", $operation->conventionne ? 1 : 0);
      
      // TypeAnesth�sie : nomemclature externe (idex)
      if ($operation->type_anesth) {
        $tag_hprimxml   = $this->_ref_receiver->_tag_hprimxml;
        $idexTypeAnesth = CIdSante400::getMatch("CTypeAnesth", $tag_hprimxml, null, $operation->type_anesth);
        $this->addElement($elParent, "typeAnesthesie", $idexTypeAnesth->id400);
      }
      
      // Indicateurs
      $indicateurs = $this->addElement($elParent, "indicateurs");
      $dossier_medical = new CDossierMedical();
      $dossier_medical->object_class = "CPatient";
      $dossier_medical->object_id    = $operation->loadRefPatient()->_id;
      $dossier_medical->loadMatchingObject();

      $antecedents = $dossier_medical->loadRefsAntecedents();
      foreach ($antecedents as $_antecedent) {
        $rques = CMbString::htmlspecialchars($_antecedent->rques);
        $rques = CMbString::convertHTMLToXMLEntities($rques);
        $this->addCodeLibelle($indicateurs, "indicateur", $_antecedent->_id, $rques);
      }
      // Extemporan�
      if ($operation->exam_extempo) {
        $this->addCodeLibelle($indicateurs, "indicateur", "EXT", "Extemporan�");
      }
      
      // Recours / Dur�e USCPO
      $this->addElement($elParent, "recoursUscpo", $operation->duree_uscpo ? 1 : 0);
      $this->addElement($elParent, "dureeUscpo"  , $operation->duree_uscpo ? $operation->duree_uscpo : null);
      
      // C�t� (droit|gauche|bilat�ral|total|inconnu)
      // D - Droit
      // G - Gauche
      // B - Bilat�ral
      // T - Total
      // I - Inconnu
      $cote = array (
        "droit"     => "D",
        "gauche"    => "G",
        "bilat�ral" => "B",
        "total"     => "T",
        "inconnu"   => "I",
        "haut"      => "HT",
        "bas"       => "BS"
      );
      
      $this->addCodeLibelle($elParent, "cote", $cote[$operation->cote], CMbString::capitalize($operation->cote));
    }
  }

  /**
   * Ajout des d�biteurs
   *
   * @param DOMNode  $elParent  Node
   * @param CPatient $mbPatient Patient
   *
   * @return void
   */
  function addDebiteurs(DOMNode $elParent, CPatient $mbPatient) {
    $debiteur = $this->addElement($elParent, "debiteur");
    
    $assurance = $this->addElement($debiteur, "assurance");
    $this->addAssurance($assurance, $mbPatient);
  }

  /**
   * Ajout de l'assurance
   *
   * @param DOMNode  $elParent  Node
   * @param CPatient $mbPatient Patient
   *
   * @return void
   */
  function addAssurance(DOMNode $elParent, CPatient $mbPatient) {
    $this->addElement($elParent, "nom", $mbPatient->regime_sante);
    
    $assure = $this->addElement($elParent, "assure");
    $this->addAssure($assure, $mbPatient);
    
    if ($mbPatient->deb_amo && $mbPatient->fin_amo) {
      $dates = $this->addElement($elParent, "dates");
      $this->addElement($dates, "dateDebutDroit", CMbDT::date($mbPatient->deb_amo));
      $this->addElement($dates, "dateFinDroit", CMbDT::date($mbPatient->fin_amo));
    }
    
    $obligatoire = $this->addElement($elParent, "obligatoire");
    $this->addElement($obligatoire, "grandRegime", $mbPatient->code_regime);
    $this->addElement($obligatoire, "caisseAffiliation", $mbPatient->caisse_gest);
    $this->addElement($obligatoire, "centrePaiement", $mbPatient->centre_gest);
    
    // Ajout des exon�rations 
    $mbPatient->guessExoneration();
    if ($mbPatient->_type_exoneration) {
      $exonerationsTM = $this->addElement($obligatoire, "exonerationsTM");
      $exonerationTM = $this->addElement($exonerationsTM, "exonerationTM");
      $this->addAttribute($exonerationTM, "typeExoneration", $mbPatient->_type_exoneration);  
    }
  }

  /**
   * Ajout de l'assur�
   *
   * @param DOMNode  $elParent  Node
   * @param CPatient $mbPatient Patient
   *
   * @return void
   */
  function addAssure(DOMNode $elParent, CPatient $mbPatient) {
    $this->addElement($elParent, "immatriculation", $mbPatient->matricule);     
    
    $personne = $this->addElement($elParent, "personne");
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
    );
    $sexe = $mbPatient->assure_sexe ? $sexeConversion[$mbPatient->assure_sexe] : "I";
    $this->addAttribute($personne, "sexe", $sexe);  
    $this->addTexte($personne, "nomUsuel", $mbPatient->assure_nom);
    $this->addTexte($personne, "nomNaissance", $mbPatient->assure_nom_jeune_fille);
    $prenoms = $this->addElement($personne, "prenoms");
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom);
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom_2);
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom_3);
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom_4);
    $adresses = $this->addElement($personne, "adresses");
    $adresse = $this->addElement($adresses, "adresse");
    $this->addTexte($adresse, "ligne", substr($mbPatient->assure_adresse, 0, 35));
    $this->addTexte($adresse, "ville", $mbPatient->assure_ville);
    if ($mbPatient->assure_pays_insee) {
      $this->addElement($adresse, "pays", str_pad($mbPatient->assure_pays_insee, 3, '0', STR_PAD_LEFT));
    }

    $this->addElement($adresse, "codePostal", $mbPatient->assure_cp);
    $dateNaissance = $this->addElement($personne, "dateNaissance");
    $assureNaissance = $mbPatient->assure_naissance ? $mbPatient->assure_naissance : $mbPatient->naissance;
    $this->addElement($dateNaissance, CMbDT::isLunarDate($assureNaissance) ? "dateLunaire" : "date", $assureNaissance);
    
    $this->addElement($elParent, "lienAssure", $mbPatient->rang_beneficiaire);
  }

  /**
   * Ajout de la saisie d�localis�e
   *
   * @param DOMNode $elParent Node
   * @param CSejour $mbSejour S�jour
   *
   * @return void
   */
  function addSaisieDelocalisee(DOMNode $elParent, CSejour $mbSejour) {
    $this->addAttribute($elParent, "action", "cr�ation");
    $this->addDateTimeElement($elParent, "dateAction");
    $dateHeureOptionnelle = $this->addElement($elParent, "dateHeureReference");
    $this->addDateHeure($dateHeureOptionnelle);

    $mbOp = reset($mbSejour->_ref_operations);
    
    // Identifiant de l'intervention
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", $mbOp->_id);
    
    $this->addUniteFonctionnelleResponsable($elParent, $mbOp);
    
    // M�decin responsable
    $medecinResponsable = $this->addElement($elParent, "medecinResponsable");
    $mbPraticien =& $mbSejour->_ref_praticien;
    $this->addElement($medecinResponsable, "numeroAdeli", $mbPraticien->adeli);
    $this->addAttribute($medecinResponsable, "lien", "rsp");
    $this->addCodeLibelle($medecinResponsable, "identification", "prat$mbPraticien->user_id", $mbPraticien->_user_username);
    
    // Diagnostics RUM
    $diagnosticsRum = $this->addElement($elParent, "diagnosticsRum");
    if (!CAppUI::conf("hprimxml send_only_das_diags")) {
      $diagnosticPrincipal = $this->addElement($diagnosticsRum, "diagnosticPrincipal");
      $this->addElement($diagnosticPrincipal, "codeCim10", strtoupper($mbSejour->DP));
      if ($mbSejour->DR) {
        $diagnosticRelie = $this->addElement($diagnosticsRum, "diagnosticRelie");
        $this->addElement($diagnosticRelie, "codeCim10", strtoupper($mbSejour->DR));
      }
    }

    if (count($mbSejour->_ref_dossier_medical->_codes_cim)) {
      $diagnosticsSignificatifs = $this->addElement($diagnosticsRum, "diagnosticsSignificatifs");
      // Dans le cas o� l'on envoie tous les diagnostics en DAS
      if (CAppUI::conf("hprimxml send_only_das_diags")) {
        if ($mbSejour->DP) {
          $diagnosticSignificatif = $this->addElement($diagnosticsSignificatifs, "diagnosticSignificatif");
          $this->addElement($diagnosticSignificatif, "codeCim10", strtoupper($mbSejour->DP));
        }
        if ($mbSejour->DR) {
          $diagnosticSignificatif = $this->addElement($diagnosticsSignificatifs, "diagnosticSignificatif");
          $this->addElement($diagnosticSignificatif, "codeCim10", strtoupper($mbSejour->DR));
        }
      }
      foreach ($mbSejour->_ref_dossier_medical->_codes_cim as $curr_code) {
        $diagnosticSignificatif = $this->addElement($diagnosticsSignificatifs, "diagnosticSignificatif");
        $this->addElement($diagnosticSignificatif, "codeCim10", strtoupper($curr_code));
      }
    }
  }

  /**
   * Ajout du SSR
   *
   * @param DOMNode $elParent Node
   * @param CSejour $mbSejour S�jour
   *
   * @return void
   */
  function addSsr($elParent, CSejour $mbSejour) {    
    // Identifiant du s�jour
    $identifiant = $this->addElement($elParent, "identifiantSSR");
    $this->addElement($identifiant, "emetteur", $mbSejour->_id);
    
    $mbRhss = CRHS::getAllRHSsFor($mbSejour);
    foreach ($mbRhss as $_mbRhs) {
      $_mbRhs->loadRefSejour();
      $rhs = $this->addElement($elParent, "rhs");
      $this->addRhs($rhs, $mbSejour, $_mbRhs);
    }
  }

  /**
   * Ajout du RHS
   *
   * @param DOMNode $elParent Node
   * @param CSejour $mbSejour S�jour
   * @param CRHS    $mbRhs    RHS
   *
   * @return void
   */
  function addRhs(DOMNode $elParent, CSejour $mbSejour, CRHS $mbRhs) {
    $this->addAttribute($elParent, "action", "cr�ation");
    $this->addAttribute($elParent, "version", "M01");
    
    $this->addElement($elParent, "dateAction", CMbDT::dateTimeXML());
    
    // Identifiant du s�jour
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", $mbRhs->_id);
    
    $dateHeureOptionnelleLundi = $this->addElement($elParent, "dateHeureOptionnelleLundi");
    $this->addElement($dateHeureOptionnelleLundi, "date", $mbRhs->date_monday);
    
    // @todo Voir pour mettre sur un plateau
    $this->addCodeLibelle($elParent, "uniteMedicale", CGroups::loadCurrent()->_id, CGroups::loadCurrent()->_view); 
    
    $joursPresence = $this->addElement($elParent, "joursPresence");
    if ($mbRhs->_in_bounds) {
      $this->addJoursPresence($joursPresence, $mbRhs);
    }
    
    $this->addElement($elParent, "diagnostics");
    
    $actesReeducation = $this->addElement($elParent, "actesReeducation");
    $this->addActesReeducation($actesReeducation, $mbRhs);
    
    $dependances = $this->addElement($elParent, "dependances");
    $this->addDependances($dependances, $mbRhs);
  }

  /**
   * Ajout des jours de pr�sence
   *
   * @param DOMNode $elParent Node
   * @param CRHS    $mbRhs    RHS
   *
   * @return void
   */
  function addJoursPresence(DOMNode $elParent, CRHS $mbRhs) {
    if ($mbRhs->_in_bounds_mon) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "lundi");
    }
    if ($mbRhs->_in_bounds_tue) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "mardi");
    }
    if ($mbRhs->_in_bounds_wed) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "mercredi");
    }
    if ($mbRhs->_in_bounds_thu) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "jeudi");
    }
    if ($mbRhs->_in_bounds_fri) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "vendredi");
    }
    if ($mbRhs->_in_bounds_sat) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "samedi");
    }
    if ($mbRhs->_in_bounds_sun) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "dimanche");
    }
  }

  /**
   * Ajout des d�pendances
   *
   * @param DOMNode $elParent Node
   * @param CRHS    $mbRhs    RHS
   *
   * @return void
   */
  function addDependances(DOMNode $elParent, CRHS $mbRhs) {
    $mbRhs->loadRefDependances();
    $dependances = $mbRhs->_ref_dependances;
    $this->addElement($elParent, "habillage"   , $dependances->habillage);
    $this->addElement($elParent, "deplacement" , $dependances->deplacement);
    $this->addElement($elParent, "alimentation", $dependances->alimentation);
    $this->addElement($elParent, "continence"  , $dependances->continence);
    $this->addElement($elParent, "comportement"  , $dependances->comportement);
    $this->addElement($elParent, "relation"    , $dependances->relation);
  }

  /**
   * Ajout des actes de r��ducation
   *
   * @param DOMNode $elParent Node
   * @param CRHS    $mbRhs    RHS
   *
   * @return void
   */
  function addActesReeducation(DOMNode $elParent, CRHS $mbRhs) {
    $mbRhs->loadRefLignesActivites();
    $lignes = $mbRhs->_ref_lignes_activites;
    
    // Ajout des actes de r��ducation
    foreach ($lignes as $_ligne) {
      $this->addActeReeducation($elParent, $_ligne);
    }
  }

  /**
   * Ajout d'un acte de r��ducation
   *
   * @param DOMNode            $elParent         Node
   * @param CLigneActivitesRHS $ligneActiviteRhs Ligne d'activit� RHS
   *
   * @return void
   */
  function addActeReeducation(DOMNode $elParent, CLigneActivitesRHS $ligneActiviteRhs) {
    $acteReeducation = $this->addElement($elParent, "acteReeducation");

    $this->addElement($acteReeducation, "codeCDARR", $ligneActiviteRhs->code_activite_cdarr);
    $this->addElement($acteReeducation, "duree", $ligneActiviteRhs->_qty_total);
  }

  /**
   * Ajout d'un chapitre d'un acte de r��ducation
   *
   * @param DOMNode $elParent Node
   * @param CRHS    $mbRhs    RHS
   *
   * @return void
   */
  function addChapitreActeReeducation(DOMNode $elParent, CRHS $mbRhs) {
    $totauxType = $mbRhs->countTypeActivite();
    
    foreach ($totauxType as $mnemonique => $_total_type) {
      if (!$_total_type) {
        continue;
      }

      $chapitreActeReeducation = $this->addElement($elParent, "chapitreActeReeducation");

      $this->addAttribute($chapitreActeReeducation, "mnemonique", strtolower($mnemonique));

      $this->addElement($chapitreActeReeducation, "duree", $_total_type);
      $this->addElement($chapitreActeReeducation, "commentaire", CActiviteCdARR::get($mnemonique)->libelle);
    }
  }

  /**
   * Ajout des diagnostics
   *
   * @param DOMNode $elParent Node
   * @param CSejour $mbSejour S�jour
   *
   * @return void
   */
  function addDiagnosticsEtat(DOMNode $elParent, CSejour $mbSejour) {
    $send_only_das_diags = CAppUI::conf("hprimxml send_only_das_diags");
    $this->addDiagnosticEtat($elParent, strtoupper($mbSejour->DP), $send_only_das_diags ? "ds" : "dp");
    if ($mbSejour->DR) {
      $this->addDiagnosticEtat($elParent, strtoupper($mbSejour->DR), $send_only_das_diags ? "ds" : "dr");
    }

    $mbSejour->loadRefDossierMedical();
    $codes_cim = $mbSejour->_ref_dossier_medical->_codes_cim;
    if (count($codes_cim) <= 0) {
      return;
    }

    foreach($codes_cim as $_diag_significatif) {
      $this->addDiagnosticEtat($elParent, strtoupper($_diag_significatif), "ds");
    }
  }

  /**
   * Ajout d'un diagnostic
   *
   * @param DOMNode $elParent       Node
   * @param string  $codeCim10      Code CIM10
   * @param string  $typeDiagnostic Type du diagnostic
   */
  function addDiagnosticEtat(DOMNode $elParent, $codeCim10, $typeDiagnostic) {
    $diagnostic = $this->addElement($elParent, "diagnostic");
    $this->addAttribute($diagnostic, "action", "cr�ation");
    $this->addAttribute($diagnostic, "type", $typeDiagnostic);
    
    $this->addElement($diagnostic, "codeCim10", $codeCim10);
  }

  /**
   * Ajout des frais divers
   *
   * @param DOMNode      $elParent      Node
   * @param CFraisDivers $mbFraisDivers Frais divers
   *
   * @return void
   */
  function addFraisDivers(DOMNode $elParent, CFraisDivers $mbFraisDivers) {
    $fraisDivers = $this->addElement($elParent, "FraisDivers");
    
    // Action r�alis�e
    $this->addAttribute($fraisDivers, "action", "cr�ation");
    //Produit facturable
    $this->addAttribute($fraisDivers, "facturable", "oui");
    
    // Date de l'�v�nement
    $this->addDateTimeElement($fraisDivers, "dateAction");
    
    // Acteur d�clencheur de cet action dans l'application cr�atrice.
    $mbExecutant = $mbFraisDivers->_ref_executant;
    $acteur = $this->addElement($fraisDivers, "acteur");
    $this->addProfessionnelSante($acteur, $mbExecutant);
    
    // Correspond � l'identification de la ligne de saisie
    $identifiant = $this->addElement($fraisDivers, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", "$mbFraisDivers->_id");
    
    // Lettre cl�
    $this->addElement($fraisDivers, "lettreCle"  , $mbFraisDivers->_ref_type->code);
    // Coefficient
    $this->addElement($fraisDivers, "coefficient", $mbFraisDivers->coefficient);
    // Quantit� de produits
    $this->addElement($fraisDivers, "quantite"   , $mbFraisDivers->quantite);

    // Date d'execution
    $execute = $this->addElement($fraisDivers, "execute");
    $this->addDateHeure($execute, $mbFraisDivers->execution);

    // Montant des frais
    $montant = $this->addElement($fraisDivers, "montant");
    $this->addTypeMontant($montant, $mbFraisDivers);
  }

  /**
   * Ajout du type du montant
   *
   * @param DOMNode      $elParent      Node
   * @param CFraisDivers $mbFraisDivers Frais divers
   *
   * @return void
   */
  function addTypeMontant($elParent, CFraisDivers $mbFraisDivers) {
    $this->addElement($elParent, "total", $mbFraisDivers->montant_base);
  }

  /**
   * Ajout du mouvement
   *
   * @param DOMNode      $elParent    Node
   * @param CAffectation $affectation Affectation
   *
   * @return void
   */
  function addMouvement(DOMNode$elParent, CAffectation  $affectation) {
    $receiver = $this->_ref_receiver;

    $mouvement = $this->addElement($elParent, "mouvement");

    // Correspond � l'identification de l'affectation
    $identifiant = $this->addElement($mouvement, "identifiant");

    // Recherche d'une affectation existante
    $tag = $receiver->_tag_hprimxml;

    $idex = CIdSante400::getMatch("CAffectation", $tag, null, $affectation->_id);

    $this->addElement($identifiant, "emetteur", $idex->_id ? $idex->id400 : $affectation->_id);

    // Traitement du m�decin responsable du s�jour
    $this->addMedecinResponsable($mouvement, $affectation->_ref_sejour->_ref_praticien);

    // Emplacement
    $this->addEmplacement($mouvement, $affectation);

    // Debut de l'affectation
    $debut = $this->addElement($mouvement, "debut");
    $this->addDateHeure($debut, $affectation->entree);
    
    // Fin de l'affectation
    $fin = $this->addElement($mouvement, "fin");
    $this->addDateHeure($fin, $affectation->sortie);

    $unitesFonctionnellesResponsables = $this->addElement($mouvement, "unitesFonctionnellesResponsables");

    $this->addUFResponsable($unitesFonctionnellesResponsables, $affectation);
  }

  /**
   * Ajout de l'emplacement du mouvement
   *
   * @param DOMNode      $elParent    Node
   * @param CAffectation $affectation Affectation
   *
   * @return void
   */
  function addEmplacement(DOMNode$elParent, CAffectation $affectation) {
    $receiver = $this->_ref_receiver;

    if (!$receiver->_configs["send_movement_location"]) {
      return;
    }

    $emplacement = $this->addElement($elParent, "emplacement");

    $affectation->loadRefLit()->loadRefChambre()->loadRefService();

    // Chambre
    $lit      = $affectation->_ref_lit;
    $chambre  = $lit->_ref_chambre;
    $idex     = CIdSante400::getMatchFor($chambre, $receiver->_tag_chambre);
    $code     = $idex->_id ? $idex->id400 : $chambre->_id;
    $this->addCodeLibelleCommentaire($emplacement, "chambre", $code, $chambre->nom, null, $chambre->caracteristiques);

    // Lit
    $idex     = CIdSante400::getMatchFor($lit, $receiver->_tag_lit);
    $code     = $idex->_id ? $idex->id400 : $lit->_id;
    $this->addCodeLibelleCommentaire($emplacement, "lit", $code, $lit->nom, null, $lit->nom_complet);

    // Chambre seul
    $this->addAttribute($emplacement, "chambreSeul", $chambre->_chambre_seule ? "oui" : "non");
  }

  /**
   * Ajout de l'UF responsable
   *
   * @param DOMNode      $elParent    Node
   * @param CAffectation $affectation Affectation
   *
   * @return void
   */
  function addUFResponsable(DOMNode $elParent, CAffectation $affectation) {
    $ufs = $affectation->getUFs();

    $uf_hebergement = CMbArray::get($ufs, "hebergement");
    if (isset($uf_hebergement->_id)) {
      $uniteFonctionnelleResponsable = $this->addElement($elParent, "uniteFonctionnelleResponsable");
      $this->addCodeLibelleAttribute($uniteFonctionnelleResponsable, $uf_hebergement->code, $uf_hebergement->libelle, "responsabilite", "h");
    }

    $uf_medicale = CMbArray::get($ufs, "medicale");;
    if (isset($uf_medicale->_id)) {
      $uniteFonctionnelleResponsable = $this->addElement($elParent, "uniteFonctionnelleResponsable");
      $this->addCodeLibelleAttribute($uniteFonctionnelleResponsable, $uf_medicale->code, $uf_medicale->libelle, "responsabilite", "m");
    }

    $uf_soins = CMbArray::get($ufs, "soins");
    if (isset($uf_soins->_id)) {
      $uniteFonctionnelleResponsable = $this->addElement($elParent, "uniteFonctionnelleResponsable");
      $this->addCodeLibelleAttribute($uniteFonctionnelleResponsable, $uf_soins->code, $uf_soins->libelle, "responsabilite", "s");
    }
  }
}