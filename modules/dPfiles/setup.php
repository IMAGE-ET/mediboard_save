<?php /* $Id: setup.php 356 2006-07-17 16:28:06Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 356 $
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPfiles";
$config["mod_version"]     = "0.11";
$config["mod_directory"]   = "dPfiles";
$config["mod_setup_class"] = "CSetupdPfiles";
$config["mod_type"]        = "user";
$config["mod_ui_name"]     = "Gestion de documents";
$config["mod_ui_icon"]     = "dPfiles.png";
$config["mod_description"] = "Gestion électronique de documents";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPfiles {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPfiles&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE files_mediboard;");       db_error();
    db_exec("DROP TABLE files_index_mediboard;"); db_error();
    return null;
  }

  function upgrade( $old_version ) {
    switch ($old_version) {
      case "all":
        $sql = "SHOW TABLE STATUS LIKE 'files_mediboard'";
        $result = db_loadResult($sql);
        if(!$result) {
          $sql = "CREATE TABLE files_mediboard (
                      file_id int(11) NOT NULL auto_increment,
                      file_real_filename varchar(255) NOT NULL default '',
                      file_consultation bigint(20) NOT NULL default '0',
                      file_operation bigint(20) NOT NULL default '0',
                      file_name varchar(255) NOT NULL default '',
                      file_parent int(11) default '0',
                      file_description text,
                      file_type varchar(100) default NULL,
                      file_owner int(11) default '0',
                      file_date datetime default NULL,
                      file_size int(11) default '0',
                      file_version float NOT NULL default '0',
                      file_icon varchar(20) default 'obj/',
                      PRIMARY KEY  (file_id),
                      KEY idx_file_consultation (file_consultation),
                      KEY idx_file_operation (file_operation),
                      KEY idx_file_parent (file_parent)
                    ) TYPE=MyISAM;";
          db_exec( $sql ); db_error();
              $sql = "CREATE TABLE files_index_mediboard (
                      file_id int(11) NOT NULL default '0',
                      word varchar(50) NOT NULL default '',
                      word_placement int(11) default '0',
                      PRIMARY KEY  (file_id,word),
                      KEY idx_fwrd (word),
                      KEY idx_wcnt (word_placement)
                      ) TYPE=MyISAM;";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `files_mediboard`" .
            "\nDROP `file_parent`," .
            "\nDROP `file_description`," .
            "\nDROP `file_version`," .
            "\nDROP `file_icon`;";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `files_mediboard`" .
              "\nADD `file_object_id` INT(11) NOT NULL DEFAULT '0' AFTER `file_real_filename`," .
              "\nADD `file_class` VARCHAR(30) NOT NULL DEFAULT 'CPatients' AFTER `file_object_id`;";
          db_exec( $sql ); db_error();
          $sql = "UPDATE `files_mediboard`" .
              "SET `file_object_id` = `file_consultation`," .
              "\n`file_class` = 'CConsultation'" .
              "\nWHERE `file_consultation` != 0;";
          db_exec( $sql ); db_error();
          $sql = "UPDATE `files_mediboard`" .
              "SET `file_object_id` = `file_consultation_anesth`," .
              "\n`file_class` = 'CConsultAnesth'" .
              "\nWHERE `file_consultation_anesth` != 0;";
          db_exec( $sql ); db_error();
          $sql = "UPDATE `files_mediboard`" .
              "SET `file_object_id` = `file_operation`," .
              "\n`file_class` = 'COperation'" .
              "\nWHERE `file_operation` != 0;";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `files_mediboard`" .
            "\nDROP `file_consultation`," .
            "\nDROP `file_consultation_anesth`," .
            "\nDROP `file_operation`;";
          db_exec( $sql ); db_error();
          
          // Move all files from former to latter strategy
          set_time_limit(120);
          foreach(glob("files/*/*/*") as $filePath) {
            $fileFragment = $filePath;
            $filePathOld = $fileFragment;
            $fileRealName = basename($fileFragment);
            $fileFragment = dirname($fileFragment);
            $fileObjectId = basename($fileFragment);
            $fileFragment = dirname($fileFragment);
            $fileDir = basename($fileFragment);
  
            switch ($fileDir) {
              case "consultations"       : $fileObjectClass = "CConsultation" ; break;
              case "consultations2"      : $fileObjectClass = "CConsultation" ; break;
              case "consultations_anesth": $fileObjectClass = "CConsultAnesth"; break;
              case "operations"          : $fileObjectClass = "COperation"    ; break;
              default: $fileObjectClass = null;
            }
  
            if (!$fileObjectClass) {
              continue;
            }
                      
            $fileDirHash = intval($fileObjectId / 1000);
            $filePathNew = "files/$fileObjectClass/$fileDirHash/$fileObjectId/$fileRealName";
  
            CMbPath::forceDir(dirname($filePathNew));
            if (!rename($filePathOld, $filePathNew)) {
              trigger_error("Impossible to move '$filePathOld' to '$filePathNew'", E_USER_ERROR);
              return false;
            }
          }
          
          CMbPath::purgeEmptySubdirs("files/consultations");
          CMbPath::purgeEmptySubdirs("files/consultations2");
          CMbPath::purgeEmptySubdirs("files/consultations_anesth");
          CMbPath::purgeEmptySubdirs("files/operations");
  
          $sql = "ALTER TABLE `files_mediboard` ADD INDEX (`file_real_filename`);";
          db_exec( $sql ); db_error();
          $sql = "ALTER TABLE `files_mediboard` ADD UNIQUE (`file_real_filename`);";
          db_exec( $sql ); db_error();
          
        }
        
      case "0.1";
        $sql = "ALTER TABLE `files_mediboard` ADD `file_category` INT(11) NOT NULL DEFAULT '1' AFTER `file_type`";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `files_mediboard` ADD INDEX (`file_category`);";
        db_exec($sql); db_error();
        $sql = "CREATE TABLE `files_category` (" .
            "\n`files_category` INT(11) NOT NULL auto_increment, " .
            "\n`nom` VARCHAR(50) NOT NULL DEFAULT ''," .
            "\n`class` VARCHAR(30) DEFAULT NULL," .
            "\nPRIMARY KEY  (files_category)" .
            "\n) TYPE=MyISAM;";
        db_exec($sql); db_error();
        $sql = "INSERT INTO `files_category` VALUES('1', 'divers', NULL)";
        db_exec($sql); db_error();
      
      case "0.11":  
        return "0.11";
    }
    return false;
  }
}

?>