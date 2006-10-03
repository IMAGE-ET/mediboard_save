<?php /* CLASSES $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Romain Ollivier
 *  @version $Revision: $
 */

// Request
class CRequest {
  
  // params
  var $ljoin = array();
  var $where = array();
  var $group = array();
  var $order = array();
  var $limit = "";
    
  function CRequest() {
  }
  
  function resetParams() {
    $this->ljoin = array();
    $this->where = array();
    $this->group = array();
    $this->order = array();
    $this->limit = "";
  }

  function addLJoin($ljoin) {
    if(is_array($ljoin)) {
      $this->ljoin = array_merge($this->ljoin, $ljoin);
    }
  }

  function addWhere($where) {
    if(is_array($where)) {
      $this->where = array_merge($this->where, $where);
    } elseif(is_string($where)) {
      $this->where[] = $where;
    }
  }
  
  function addWhereClause($key, $value) {
    if($key) {
      $this->where[$key] = $value;
    } else {
      $this->where[] = $value;
    }
  }

  function addGroup($group) {
    if(is_array($group)) {
      $this->group = array_merge($this->group, $group);
    } elseif(is_string($group)) {
      $this->group[] = $group;
    }
  }

  function addOrder($order) {
    if(is_array($order)) {
      $this->order = array_merge($this->order, $order);
    } elseif(is_string($order)) {
      $this->order[] = $order;
    }
  }

  function setLimit($limit) {
    $this->limit = $limit;
  }

  function getRequest($obj) {
    
    $tbl = $obj->_tbl;
    
    $sql = "SELECT `$tbl`.* FROM `$tbl`";

    // Left join clauses
    if ($this->ljoin) {
      assert(is_array($this->ljoin));
      foreach ($this->ljoin as $table => $condition) {
        $sql .= "\nLEFT JOIN `$table` ON $condition";
      }
    }
    
    // Where clauses
    if (is_array($this->where)) {
      foreach ($this->where as $field => $eq) {
        if (is_string($field)) {
          if($pos = strpos($field, ".")) {
            $point_table = substr($field, 0, $pos);
            $point_field = substr($field, $pos + 1);
            $this->where[$field] = "`$point_table`.`$point_field` $eq";
          } else {
            $this->where[$field] = "`$field` $eq";
          }
        }
        
        $this->where[$field] = "(" . $this->where[$field] . ")";
      }
    }
    
    if ($this->where) {
      $sql .= "\nWHERE ";
      $sql .= is_array($this->where) ? implode("\nAND ", $this->where) : $this->where;
    }
      
    // Group by fields
    if (is_array($this->group)) {
      foreach ($this->group as $key => $field) {
        $this->group[$key] = "`$field`";
      }
    }
    
    if ($this->group) {
      $sql .= "\nGROUP BY ";
      $sql .= is_array($this->group) ? implode(", ", $this->group) : $this->group;
    }
      
    // Order by fields
    if (is_array($this->order)) {
      foreach ($this->order as $key => $field) {
        // We cannot use the `` syntax because it wont work
        // with table.field syntax, neither the ASC/DESC one
        //$this->$order[$key] = "`$field`";
        $this->order[$key] = "$field";
      }
    }
    
    if ($this->order) {
      $sql .= "\nORDER BY ";
      $sql .= is_array($this->order) ? implode(", ", $this->order) : $this->order;
    }
    
    // Limits
    if ($this->limit) {
      $sql .= "\nLIMIT $this->limit";
    }
    return $sql;
  }
}
?>