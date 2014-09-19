<?php

/**
 * dPccam
 *
 * Classe des activit�s des actes CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CActiviteCCAM
 * Table p_activite
 *
 * Activit�s
 * Niveau Acte
*/
class CActiviteCCAM extends CCCAM {
  public $code_activite;
  public $_libelle_activite;

  // R�f�rences

  // Classification historis�e de l'activit�
  /** @var  CActiviteClassifCCAM[] */
  public $_ref_classif;
  // Actes et activit�s associables
  /** @var  CActiviteAssociationCCAM[][] */
  public $_ref_associations;
  // Modificateurs de l'activit�
  /** @var  CActiviteModificateurCCAM[][] */
  public $_ref_modificateurs;
  // Phases de l'activit�
  /** @var  CPhaseCCAM[] */
  public $_ref_phases;

  // Elements de r�f�rence pour la r�cup�ration d'informations
  public $_code;
  public $_phase;

  /**
   * Mapping des donn�es depuis la base de donn�es
   *
   * @param array $row Ligne d'enregistrement de de base de donn�es
   *
   * @return void
  */
  function map($row) {
    $this->code_activite = $row["ACTIVITE"];
  }

  /**
   * Chargement de a liste des activit�s pour un code
   *
   * @param string $code    Code CCAM
   * @param array  $exclude Liste des activit�s � exclure
   *
   * @return self[] Liste des activit�s
   */
  static function loadListFromCode($code, $exclude = array()) {

    $ds = self::$spec->ds;
    $exclude_list = "";
    if (count($exclude)) {
      $exclude_list = "AND p_activite.ACTIVITE NOT IN (".implode(",", $exclude).")";
    }
    $query = "SELECT p_activite.*
      FROM p_activite
      WHERE p_activite.CODEACTE = %
        $exclude_list
      ORDER BY p_activite.ACTIVITE ASC";
    $query = $ds->prepare($query, $code);
    $result = $ds->exec($query);

    $list_activites = array();
    while ($row = $ds->fetchArray($result)) {
      $activite = new CActiviteCCAM();
      $activite->_code = $code;
      $activite->map($row);
      $list_activites[$row["ACTIVITE"]] = $activite;
    }

    return $list_activites;
  }

  /**
   * Chargement du libell� standard de l'activit�
   * Table c_activite
   *
   * @return string Le libell� de l'activit�
   */
  function loadLibelle() {

    $ds = self::$spec->ds;

    $query = "SELECT c_activite.*
      FROM c_activite
      WHERE c_activite.CODE = %";
    $query = $ds->prepare($query, $this->code_activite);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    return $this->_libelle_activite = $row["LIBELLE"];
  }

  /**
   * Chargement des informations historis�es de l'acte
   * Table p_activite_classif
   *
   * @return array La liste des informations historis�es
   */
  function loadRefClassif() {
    return $this->_ref_classif = CActiviteClassifCCAM::loadListFromCodeActivite($this->_code, $this->code_activite);
  }

  /**
   * Chargement des actes et activit�s associables
   * Table p_activite_associabilite
   *
   * @return array La liste des actes et activit�s associables
   */
  function loadRefAssociations() {
    return $this->_ref_associations = CActiviteAssociationCCAM::loadListFromCodeActivite($this->_code, $this->code_activite);
  }

  /**
   * Chargement des modificateurs disponibles
   * Table p_activite_modificateur
   *
   * @return array La liste des modificateurs
   */
  function loadRefModificateurs() {
    return $this->_ref_modificateurs = CActiviteModificateurCCAM::loadListFromCodeActivite($this->_code, $this->code_activite);
  }

  /**
   * Chargement des phases disponibles
   * Table p_phase
   *
   * @return array La liste des phases
   */
  function loadRefPhases() {
    return $this->_ref_phases = CPhaseCCAM::loadListFromCodeActivite($this->_code, $this->code_activite);
  }
}