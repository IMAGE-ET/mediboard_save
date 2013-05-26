<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

//CCanDo::checkRead();

$tag_id       = CValue::get("tag_id");
$columns      = CValue::get("col");
$keywords     = CValue::get("keywords");
$object_class = CValue::get("object_class");
$insertion    = CValue::get("insertion");
$group_id     = CValue::get("group_id");

$tag = new CTag;

$where = array();
if ($group_id) {
  $where[] = "(group_id = '$group_id' OR group_id IS NULL)";
}

/** @var CMbObject $object */

if (strpos($tag_id, "all") === 0) {
  $parts = explode("-", $tag_id);
  $object_class = $parts[1];

  $object = new $object_class;

  if (!$keywords) {
    $keywords = "%";
  }

  /** @var CMbObject[] $objects */
  $objects = $object->seek($keywords, $where, 10000, true);
  foreach ($objects as $_object) {
    $_object->loadRefsTagItems();
  }

  $count_children = $object->_totalSeek;
}
elseif (strpos($tag_id, "none") === 0) {
  $parts = explode("-", $tag_id);
  $object_class = $parts[1];

  $tag->object_class = $object_class;
  $object = new $object_class;

  $where["tag_item_id"] = "IS NULL";

  $ljoin = array(
    "tag_item" => "tag_item.object_id = {$object->_spec->table}.{$object->_spec->key} AND tag_item.object_class = '$object_class'",
  );

  if (!$keywords) {
    $keywords = "%";
  }

  $objects = $object->seek($keywords, $where, 10000, true, $ljoin);
  $count_children = $object->_totalSeek;
}
else {
  $tag->load($tag_id);
  $count_children = $tag->countChildren();
  $objects = $tag->getObjects($keywords);

  // filter by group_id
  if ($group_id) {
    foreach ($objects as $_id => $_object) {
      if ($_object->group_id && $_object->group_id != $group_id) {
        unset($objects[$_id]);
      }
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("objects", $objects);
$smarty->assign("columns", $columns);
$smarty->assign("insertion", $insertion);
$smarty->assign("count_children", $count_children);
$smarty->assign("tag", $tag);
$smarty->display("inc_list_objects_by_tag.tpl");
