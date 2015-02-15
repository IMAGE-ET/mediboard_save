<?php

/**
 * dPccam
 *
 * Classe des dents CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CDentCCAM
 * Table t_localisationdents
 *
 * Dents dans la CCAM
 */
class CDentCCAM extends CCCAM {

  public $date_debut;
  public $date_fin;
  public $localisation;
  public $_libelle;

  /**
   * Mapping des donn�es depuis la base de donn�es
   *
   * @param array $row Ligne d'enregistrement de de base de donn�es
   *
   * @return void
   */
  function map($row) {
    $this->date_debut   = $row["DATEDEBUT"];
    $this->date_fin     = $row["DATEFIN"];
    $this->localisation = $row["LOCDENT"];
  }

  /**
   * Chargement de a liste des dents disponibles
   *
   * @return self[] Liste des dents
   */
  static function loadList() {
    $cache = new Cache(__METHOD__, func_get_args(), Cache::INNER_OUTER);
    if ($cache->exists()) {
      return $cache->get();
    }

    $ds = self::getSpec()->ds;

    $query = "SELECT t_localisationdents.*
      FROM t_localisationdents
      ORDER BY t_localisationdents.LOCDENT ASC,
        t_localisationdents.DATEFIN ASC";
    $result = $ds->exec($query);

    $listDents = array();
    while ($row = $ds->fetchArray($result)) {
      $dent = new CDentCCAM();
      $dent->map($row);
      $dent->loadLibelle();
      $listDents[$row["DATEFIN"]][] = $dent;
    }

    return $cache->put($listDents);
  }

  /**
   * Chargement d'une dent � partir de son num�ro
   *
   * @param string $localisation Numero de la dent
   *
   * @return bool r�ussite du chargement
   */
  function load($localisation) {
    $ds = self::$spec->ds;
    $localisation = (int) $localisation;
    $query = "SELECT t_localisationdents.*
      FROM t_localisationdents
      WHERE t_localisationdents.LOCDENT = %";
    $query = $ds->prepare($query, $localisation);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    if (!count($row)) {
      return false;
    }
    $this->map($row);
    return true;
  }

  /**
   * Chargement du libell� de la dent
   * Table c_dentsincomp
   *
   * @return string libell� de la dent
   */
  function loadLibelle() {
    $ds = self::$spec->ds;
    $query = "SELECT *
      FROM c_dentsincomp
      WHERE c_dentsincomp.CODE = %";
      $query = $ds->prepare($query, str_pad($this->localisation, 2, "0", STR_PAD_LEFT));
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
    $this->_libelle = $row["LIBELLE"];
    return $this->_libelle;
  }
}