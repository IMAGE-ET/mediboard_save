<?php 
/**
 * Setup FTP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupftp extends CSetup {
  
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "ftp";
    $this->makeRevision("all");
      
    $query = "CREATE TABLE IF NOT EXISTS `source_ftp` (
                `source_ftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `port` INT (11) DEFAULT '21',
                `timeout` INT (11) DEFAULT '90',
                `pasv` ENUM ('0','1') DEFAULT '0',
                `mode` ENUM ('FTP_ASCII','FTP_BINARY') DEFAULT 'FTP_ASCII',
                `fileprefix` VARCHAR (255),
                `fileextension` VARCHAR (255),
                `filenbroll` ENUM ('1','2','3','4'),
                `fileextension_write_end` VARCHAR (255),
                `counter` VARCHAR (255),
                `name` VARCHAR (255) NOT NULL,
                `host` TEXT NOT NULL,
                `user` VARCHAR (255),
                `password` VARCHAR (50)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
  
    $query = "ALTER TABLE `source_ftp` 
               ADD `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif';";
    $this->addQuery($query, true);
    
    $query = "ALTER TABLE `source_ftp` 
               ADD `type_echange` VARCHAR (255);";
    $this->addQuery($query, true);
    
    $this->makeRevision("0.01");
    
    $query = "CREATE TABLE `sender_ftp` (
                `sender_ftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sender_ftp` 
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.02");
    
    $query = "CREATE TABLE `echange_ftp` (
                `echange_ftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `ftp_fault` ENUM ('0','1') DEFAULT '0',
                `emetteur` VARCHAR (255),
                `destinataire` VARCHAR (255),
                `date_echange` DATETIME NOT NULL,
                `function_name` VARCHAR (255) NOT NULL,
                `input` MEDIUMTEXT,
                `output` MEDIUMTEXT,
                `purge` ENUM ('0','1') DEFAULT '0',
                `response_time` FLOAT
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `echange_ftp` 
                ADD INDEX (`date_echange`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.03");
    
    $query = "ALTER TABLE `source_ftp` 
                ADD `active` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.04");
    
    $query = "ALTER TABLE `source_ftp` 
              CHANGE `timeout` `timeout` INT (11) DEFAULT '5';";
    $this->addQuery($query);

    $this->makeRevision("0.05");
    
    $query = "ALTER TABLE `sender_ftp` 
                ADD `user_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sender_ftp` 
                ADD INDEX (`user_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.06");
    
    $query = "ALTER TABLE `source_ftp` 
              ADD `loggable` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.07");
    
    $query = "ALTER TABLE `echange_ftp` 
                ADD INDEX (`purge`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.08");
    
    $query = "ALTER TABLE `sender_ftp` 
                ADD `save_unsupported_message` ENUM ('0','1') DEFAULT '1',
                ADD `create_ack_file` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.09");
    
    $query = "ALTER TABLE `echange_ftp` 
                ADD INDEX (`function_name`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.10");
    
    $query = "ALTER TABLE `sender_ftp` 
                ADD `delete_file` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.12");
    $query = "ALTER TABLE `source_ftp`
                CHANGE `password` `password` VARCHAR (255),
                ADD `iv` VARCHAR (16) AFTER `password`;";
    $this->addQuery($query);

    $this->makeRevision("0.13");
    $query = "UPDATE source_ftp
                SET source_ftp.name = Replace(name, 'CReceiverIHE', 'CReceiverHL7v2')
                WHERE source_ftp.name LIKE 'CReceiverIHE-%';";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $query = "ALTER TABLE `source_ftp`
               ADD `ssl` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query, true);

    $this->makeRevision("0.15");
    $query = "CREATE TABLE `source_sftp` (
                `source_sftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `port` INT (11) DEFAULT '22',
                `timeout` INT (11) DEFAULT '10',
                `name` VARCHAR (255) NOT NULL,
                `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif',
                `host` TEXT NOT NULL,
                `user` VARCHAR (255),
                `password` VARCHAR (50),
                `iv` VARCHAR (255),
                `type_echange` VARCHAR (255),
                `active` ENUM ('0','1') NOT NULL DEFAULT '1',
                `loggable` ENUM ('0','1') NOT NULL DEFAULT '1',
                `fileprefix` VARCHAR (255),
                `fileextension_write_end` VARCHAR (255),
                `fileextension` VARCHAR (255)
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "CREATE TABLE `sender_sftp` (
                `sender_sftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `user_id` INT (11) UNSIGNED,
                `save_unsupported_message` ENUM ('0','1') DEFAULT '1',
                `create_ack_file` ENUM ('0','1') DEFAULT '1',
                `delete_file` ENUM ('0','1') DEFAULT '1',
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.17");
    $query = "ALTER TABLE `sender_sftp`
                ADD INDEX (`user_id`),
                ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.18");
    $query = "ALTER TABLE `source_ftp`
                ADD `libelle` VARCHAR (255);";
    $this->addQuery($query);

    $query = "ALTER TABLE `source_sftp`
                ADD `libelle` VARCHAR (255);";
    $this->addQuery($query);

    $this->mod_version = "0.19";
  }
}
