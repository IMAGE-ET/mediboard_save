<?php

/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CSearchIndexing extends CStoredObject {
  /**
   * @var integer Primary key
   */
  public $search_indexing_id;

  // DB Fields
  public $type;
  public $object_class;
  public $object_id;
  public $date;
  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = "search_indexing";
    $spec->key   = "search_indexing_id";
    $spec->loggable = false;
    return $spec;
  }

  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"] = "str notNull maxLength|50";
    $props["object_id"] = "ref class meta|object_class notNull unlink";
    $props["type"] = "enum list|create|store|delete|merge default|create";
    $props["date"] = "dateTime notNull";

    return $props;
  }

  /**
   * @see parent::store()
   */
  function store() {
    return parent::store();
  }

  /**
   * store into temporary table all the data from CMbObject
   *
   * @param string $name_type the name of the CMbObject required
   *
   * @return string
   */
  function firstIndexingStore($name_type) {
    $date = CMbDT::dateTime();
    switch ($name_type) {
      case 'CCompteRendu':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CCompteRendu', `compte_rendu`.`compte_rendu_id`, 'create', '$date'
          FROM `compte_rendu`, `users_mediboard`, `functions_mediboard`
          WHERE `compte_rendu`.`object_id` IS NOT NULL
          AND   `compte_rendu`.`object_class` != 'CPatient'
          AND `users_mediboard`.`user_id` =  `compte_rendu`.`author_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CTransmissionMedicale':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CTransmissionMedicale', `transmission_medicale`.`transmission_medicale_id`, 'create', '$date'
          FROM `transmission_medicale`, `users_mediboard`, `functions_mediboard`
          WHERE `users_mediboard`.`user_id` =  `transmission_medicale`.`user_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CObservationMedicale':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CObservationMedicale', `observation_medicale`.`observation_medicale_id`, 'create', '$date'
          FROM `observation_medicale`, `users_mediboard`, `functions_mediboard`
          WHERE `users_mediboard`.`user_id` =  `observation_medicale`.`user_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CConsultation':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CConsultation', `consultation`.`consultation_id`, 'create', '$date'
          FROM `consultation`, `users_mediboard`, `functions_mediboard`, `plageconsult`
          WHERE `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
          AND `users_mediboard`.`user_id` =  `plageconsult`.`chir_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CConsultAnesth':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CConsultAnesth', `consultation_anesth`.`consultation_anesth_id`, 'create', '$date'
          FROM `consultation_anesth`, `users_mediboard`, `functions_mediboard`, `plageconsult`, `consultation`
          WHERE   `consultation_anesth`.`consultation_id` = `consultation`.`consultation_id`
          AND `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
          AND `users_mediboard`.`user_id` =  `plageconsult`.`chir_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CFile':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CFile', `files_mediboard`.`file_id`, 'create', '$date'
          FROM `files_mediboard`
          WHERE `files_mediboard`.`object_class` != 'CCompteRendu'
          AND `files_mediboard`.`file_type` NOT LIKE 'image/%'
          AND `files_mediboard`.`file_type` NOT LIKE 'video/%'
          AND `files_mediboard`.`file_type` NOT LIKE 'audio/%'";
        break;
      default: $query ="";
    }
    return $query;
  }

}
