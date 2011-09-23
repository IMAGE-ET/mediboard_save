<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision$
 * @author Thomas Despoix
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Permet une forme de publi-postage pour les documents produits dans Mediboard
 * Cette classe n'est pas un MbObject et les objets ne sont pas enregistr�s en base
 */
class CDestinataire {
   var $nom = null;
  var $adresse = null;
  var $cpville = null;
  var $email = null;
  var $tag = null;

  static $destByClass = array();

  /**
   * Constructeur standard
   * @param string $tag Tag par d�faut, optionnel
   */
  function __construct($tag = null) {
    $this->tag = $tag;
  }
  
  /**
   * Construit les destinataires pour un MbObject
   * @param CMbObject $mbObject L'objet en question
   * @param string $tag tag par d�faut,  optionnel
   */
  static function makeFor(CMbObject & $mbObject, $tag = null) {
    $destinataires = array();

    if (!$mbObject->_id) {
      return;
    }
    
    if ($mbObject instanceof CPatient) {
      $patient = $mbObject;
      $patient->loadRefsCorrespondantsPatient();
      
      // Patient
      $dest = new CDestinataire($tag);
      $dest->tag     = "patient";
      $dest->nom     = $patient->_view;
      $dest->adresse = $patient->adresse;
      $dest->cpville = "$patient->cp $patient->ville";
      $dest->email   = $patient->email;
      self::$destByClass[$mbObject->_class][] = $dest;
      
      // Assur�
      $dest = new CDestinataire($tag);
      $dest->tag     = "assure";
      $dest->nom     = "$patient->assure_nom $patient->assure_prenom";
      $dest->adresse = $patient->assure_adresse;
      $dest->cpville = "$patient->assure_cp $patient->assure_ville";
      $dest->email   = "";
      self::$destByClass[$mbObject->_class][] = $dest;
      
      // Personne � pr�venir et employeur
      foreach ($patient->_ref_correspondants_patient as $_corres) {
        if ($_corres->relation == "confiance") continue;
        $dest = new CDestinataire($tag);
        $dest->tag = $_corres->relation;
        $dest->nom = $_corres->nom;
        $dest->adresse = $_corres->adresse;
        $dest->cpville = $_corres->cp;
        $dest->email = $_corres->email;
        self::$destByClass[$mbObject->_class][] = $dest;
      }
    }
    
    if ($mbObject instanceof CMedecin) {
      $medecin = $mbObject;
      
      $dest = new CDestinataire($tag);
      $dest->nom     = $medecin->_view;
      $dest->adresse = $medecin->adresse;
      $dest->cpville = "$medecin->cp $medecin->ville";
      $dest->email   = $medecin->email;
      self::$destByClass[$mbObject->_class][] = $dest;
    }
    
  }

  /**
   * Construit les destinataires pour un MbObject et ses d�pendances
   * @param CMbObject $mbObject L'objet en question 
   */
  static function makeAllFor(CMbObject & $mbObject) {
    self::$destByClass = array();
    
    if ($mbObject instanceof CPatient) {
      $patient = $mbObject;
      
      self::makeFor($patient);
      
      $patient->loadRefsFwd();
      self::makeFor($patient->_ref_medecin_traitant, "traitant");
      
      foreach ($patient->_ref_medecins_correspondants as &$corresp) {
        self::makeFor($corresp->_ref_medecin, "correspondant");
      }
    }

    if ($mbObject instanceof CConsultation) {
      $consult = $mbObject;
      
      $consult->loadRefPatient();
      self::makeAllFor($consult->_ref_patient);
    }
    
    if ($mbObject instanceof CConsultAnesth) {
      $consultAnesth = $mbObject;
      
      $consultAnesth->loadRefConsultation();
      self::makeAllFor($consultAnesth->_ref_consultation);
    }
    
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      
      $sejour->loadRefPatient();
      self::makeAllFor($sejour->_ref_patient);
    }
    
    if ($mbObject instanceof COperation) {
      $operation = $mbObject;
      
      $operation->loadRefSejour();
      self::makeAllFor($operation->_ref_sejour);
    }
  }
}
?>