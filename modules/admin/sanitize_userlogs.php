<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

/** @var int $step */
$step    = CView::get("step", "num default|100000");

/** @var int $offset */
$offset  = CView::get("offset", "num default|1");

/** @var bool $execute */
$execute = CView::get("execute", "bool default|0", true);

/** @var bool $auto */
$auto    = CView::get("auto", "bool", true);

CView::checkin();

$copies = array(
  //  ||       object_class       ||        field       ||      target_field       ||
  array("CConstantesMedicales",     "user_id",            "user_id"),
  array("CConstantesMedicales",     "date",               "creation_date"),
  array("CAlert",                   "date",               "creation_date"),
  array("CCompteRendu",             "date",               "creation_date"),
);

// Ajout des classes de formulaires
if (CModule::getActive("forms")) {
  $ex_class = new CExClass();
  $ex_class_ids = $ex_class->loadIds();
  foreach ($ex_class_ids as $_id) {
    $copies[] = array("CExObject_$_id", "user_id", "owner_id");
    $copies[] = array("CExObject_$_id", "date",    "datetime_create");
  }
}

/**
 * Create user authentication entry
 *
 * @param CSQLDataSource $ds   Data source
 * @param array          $data Data
 *
 * @return string The SQL query
 */
$create_user_auth = function (CSQLDataSource $ds, $data) {
  $query = "INSERT INTO user_authentication
              (user_id, auth_method, datetime_login, id_address)
              VALUES (?1, ?2, ?3, ?4);";
  return $ds->prepare(
    $query,
    $data["user_id"],
    'basic',
    $data["date"],
    ($data["ip_address"] ? inet_ntop($data["ip_address"]) : '')
  );
};

$inserts = array(
  array("CUser", "store", "user_last_login", $create_user_auth),
  array("CUser", "store", "user_password user_last_login", $create_user_auth),
  array("CUser", "store", "user_password user_salt user_last_login", $create_user_auth),
);

// Triplets defining user log entries to be removed
$removers = array(
  array("CSourceFTP"              , "store", "counter"        ),

  array("CPrisePosologie"         , "store", "last_generation"),

  array("CPrescriptionLineElement", "store", "last_generation"),

  array("CTriggerMark", "create", ""),
  array("CTriggerMark", "store", "mark"),
  array("CTriggerMark", "store", "mark done"),

  array("CMovement", "create", ""),
  array("CMovement", "store", "last_update"),
  array("CMovement", "store", "affectation_id"),
  array("CMovement", "store", "affectation_id last_update"),
  array("CMovement", "store", "movement_type last_update"),
  array("CMovement", "store", "start_of_movement last_update"),
  array("CMovement", "delete", ""),

  // IMPORTANT: Leave at list one empty array() or you will purge all logs
  array("", "", ""),
);

$counts = array(
  "delete" => 0,
  "copy"   => 0,
  "insert" => 0,
);

$log = new CUserLog();
$ds = $log->getDS();

// Primary key clauses
$min = $offset;
$max = $offset + $step - 1;

//<editor-fold desc=" ----- Remove ----- ">
$request = new CRequest();
$request->addWhereClause("user_log_id", "BETWEEN $min AND $max");
$request->addForceIndex("PRIMARY");

// Removers clauses
$triplets = array();
foreach ($removers as $_remover) {
  list($object_class, $type, $fields) = $_remover;
  $triplets[] = "$object_class-$type".($fields ? "-$fields" : "");
}
$where = "CONCAT_WS('-', `object_class`, `type`, `fields`) " . CSQLDataSource::prepareIn($triplets);
$request->addWhere($where);

// Actual query
if ($execute) {
  $query = $request->makeDelete($log);
  $ds->exec($query);
  $counts["delete"] = $ds->affectedRows();
}
else {
  $query = $request->makeSelectCount($log);
  $counts["delete"] = $ds->loadResult($query);
}
//</editor-fold>

//<editor-fold desc=" ----- Copy ----- ">
$request = new CRequest();
$request->addWhereClause("user_log_id", "BETWEEN $min AND $max");
$request->addForceIndex("PRIMARY");

// Copy clauses
$triplets = array();
foreach ($copies as $_copy) {
  list($object_class, $field, $target) = $_copy;
  $triplets[] = "$object_class-create";
}
$triplets = array_unique($triplets);

$where = "CONCAT_WS('-', `object_class`, `type`) " . CSQLDataSource::prepareIn($triplets);
$request->addWhere($where);
$request->addSelect(array("user_log_id", "user_id", "date", "object_class", "object_id"));

// Actual query
if ($execute) {
  $query = $request->makeSelect($log);
  $list = $ds->loadList($query);

  $class_to_table = array();

  foreach ($list as $_row) {
    $_object_class = $_row["object_class"];
    if (!isset($class_to_table[$_object_class])) {
      $_obj = CModelObject::getInstance($_object_class);

      $_spec = $_obj->_spec;
      $class_to_table[$_object_class] = array(
        "table" => $_spec->table,
        "key"   => $_spec->key,
      );
    }

    $_table_info = $class_to_table[$_object_class];
    $_table = $_table_info["table"];
    $_key   = $_table_info["key"];

    foreach ($copies as $_copy) {
      list($object_class, $field, $target) = $_copy;

      if ($object_class === $_object_class) {
        $_query = "UPDATE $_table
                   SET `$target` = ?1
                   WHERE `$_key` = ?2
                   AND `$target` IS NULL;";
        $_query = $ds->prepare($_query, $_row[$field], $_row["object_id"]);
        $ds->exec($_query);
      }
    }
  }
}
else {
  $query = $request->makeSelectCount($log);
  $counts["copy"] = $ds->loadResult($query);
}
//</editor-fold>

//<editor-fold desc=" ----- Insert ----- ">
$request = new CRequest();
$request->addWhereClause("user_log_id", "BETWEEN $min AND $max");
$request->addForceIndex("PRIMARY");

// Insert clauses
$triplets = array();
foreach ($inserts as $_insert) {
  list($object_class, $type, $fields) = $_insert;
  $triplets[] = "$object_class-$type-$fields";
}

$where = "CONCAT_WS('-', `object_class`, `type`, `fields`) " . CSQLDataSource::prepareIn($triplets);
$request->addWhere($where);

// Actual query
if ($execute) {
  $query = $request->makeSelect($log);
  $list = $ds->loadList($query);

  $class_to_table = array();

  foreach ($list as $_row) {
    $_object_class = $_row["object_class"];
    $_type         = $_row["type"];
    $_fields       = $_row["fields"];

    if (!isset($class_to_table[$_object_class])) {
      $_obj = CModelObject::getInstance($_object_class);

      $_spec = $_obj->_spec;
      $class_to_table[$_object_class] = array(
        "table" => $_spec->table,
        "key"   => $_spec->key,
      );
    }

    $_table_info = $class_to_table[$_object_class];
    $_table = $_table_info["table"];
    $_key   = $_table_info["key"];

    foreach ($inserts as $_insert) {
      /** @var callable $callback */
      list($object_class, $type, $fields, $callback) = $_insert;

      if (
          $fields       === $_fields &&
          $object_class === $_object_class &&
          $type         === $_type
      ) {
        $_query = $callback($ds, $_row);
        $ds->exec($_query);

        $counts["insert"] += $ds->affectedRows();
      }
    }
  }

  // Delete user logs entries
  $request = new CRequest();
  $request->addWhereClause("user_log_id", "BETWEEN $min AND $max");
  $request->addForceIndex("PRIMARY");
  $request->addWhere($where);

  $query = $request->makeDelete($log);
  $ds->exec($query);
  $counts["insert"] = $ds->affectedRows();
}
else {
  $query = $request->makeSelectCount($log);
  $counts["insert"] = $ds->loadResult($query);
}
//</editor-fold>

$offset = $max+1;

// Stop auto if end is reached
$log->loadMatchingObject("user_log_id DESC");
if ($log->_id < $offset) {
  $auto = 0;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("log"      , $log);
$smarty->assign("auto"     , $auto);
$smarty->assign("execute"  , $execute);
$smarty->assign("removers" , $removers);
$smarty->assign("copies"   , $copies);
$smarty->assign("inserts"  , $inserts);
$smarty->assign("min"      , $min);
$smarty->assign("max"      , $max);
$smarty->assign("counts"   , $counts);
$smarty->assign("offset"   , $offset);
$smarty->assign("step"     , $step);

$smarty->display("sanitize_userlogs.tpl");