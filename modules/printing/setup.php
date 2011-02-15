<?php 
/**
 * Setup PRINTING
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupprinting extends CSetup {
  
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "printing";
    $this->makeRevision("all");
      
    $query = "CREATE TABLE `source_lpr` (
                `source_lpr_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `name` VARCHAR (255) NOT NULL,
                `object_id` BIGINT DEFAULT NULL,
                `object_class` VARCHAR(30) DEFAULT NULL,
                `host` TEXT NOT NULL,
                `port` INT (11) DEFAULT NULL,
                `user` VARCHAR (255),
                `printer_name` VARCHAR (50)
              ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("0.01");
    $query = "CREATE TABLE `source_smb` (
                `source_smb_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `name` VARCHAR (255) NOT NULL,
                `object_id` BIGINT DEFAULT NULL,
                `object_class` VARCHAR(30) DEFAULT NULL,
                `host` TEXT NOT NULL,
                `port` INT (11) DEFAULT NULL,
                `user` VARCHAR (255),
                `password` VARCHAR (50),
                `workgroup` VARCHAR (50), 
                `printer_name` VARCHAR (50)
              ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->mod_version = "0.02";
    
    
  }
}
?>