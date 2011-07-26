<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdPstock extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'dPstock';
    $this->makeRevision('all');

    $query = 'CREATE TABLE `product` (
       `product_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
       `name` VARCHAR(50) NOT NULL, 
       `description` TEXT, 
       `code` VARCHAR(32) NULL,
       `category_id` INT(11) UNSIGNED NOT NULL, 
       `societe_id` INT(11) UNSIGNED NOT NULL, 
      PRIMARY KEY (`product_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product`
      ADD UNIQUE INDEX (code),
      ADD INDEX (category_id),
      ADD INDEX (societe_id)';
    $this->addQuery($query);

    $query = 'CREATE TABLE `product_category` (
       `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
       `name` VARCHAR(50) NOT NULL, 
      PRIMARY KEY (`category_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);

    $query = 'CREATE TABLE `product_delivery` (
     `delivery_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `product_id` INT(11) UNSIGNED NOT NULL, 
     `date` DATETIME NOT NULL, 
     `target_class` VARCHAR(25) NOT NULL, 
     `target_id` INT(11) UNSIGNED NOT NULL, 
     `code` VARCHAR(32) NULL, 
     `description` TEXT, 
    PRIMARY KEY (`delivery_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_delivery`
      ADD INDEX (code),
      ADD INDEX (product_id),
      ADD INDEX (target_id),
      ADD INDEX (target_class),
      ADD INDEX (date)';
    $this->addQuery($query);

    $query = 'CREATE TABLE `product_order` (
     `order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `date_ordered` DATETIME, 
     `societe_id` INT(11) UNSIGNED NOT NULL, 
     `group_id` INT(11) UNSIGNED NOT NULL, 
     `locked` BOOL NULL DEFAULT \'0\',
     `cancelled` BOOL NULL DEFAULT \'0\',
     `deleted` BOOL NULL DEFAULT \'0\',
     `order_number` VARCHAR(64) NOT NULL, 
    PRIMARY KEY (`order_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_order`
      ADD UNIQUE INDEX (order_number),
      ADD INDEX (societe_id),
      ADD INDEX (group_id),
      ADD INDEX (date_ordered)';
    $this->addQuery($query);

    $query = 'CREATE TABLE `product_order_item` (
     `order_item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `reference_id` INT(11) UNSIGNED NOT NULL, 
     `order_id` INT(11) UNSIGNED NOT NULL, 
     `quantity` INT(11) UNSIGNED NOT NULL, 
     `unit_price` FLOAT, 
     `date_received` DATETIME, 
     `quantity_received` INT(11) UNSIGNED DEFAULT \'0\',
    PRIMARY KEY (`order_item_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_order_item`
      ADD INDEX (reference_id),
      ADD INDEX (order_id),
      ADD INDEX (date_received)';
    $this->addQuery($query);

    $query = 'CREATE TABLE `product_reference` (
     `reference_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `product_id` INT(11) UNSIGNED NOT NULL, 
     `societe_id` INT(11) UNSIGNED NOT NULL, 
     `quantity` INT(11) UNSIGNED NOT NULL, 
     `code` VARCHAR(32) NULL, 
     `price` FLOAT NOT NULL, 
    PRIMARY KEY (`reference_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_reference`
      ADD UNIQUE INDEX (code),
      ADD INDEX (product_id),
      ADD INDEX (societe_id)';
    $this->addQuery($query);

    $query = 'CREATE TABLE `product_stock` (
     `stock_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `product_id` INT(11) UNSIGNED NOT NULL, 
     `group_id` INT(11) UNSIGNED NOT NULL, 
     `quantity` INT(11) UNSIGNED NOT NULL, 
     `order_threshold_critical` INT(11) UNSIGNED, 
     `order_threshold_min` INT(11) UNSIGNED NOT NULL, 
     `order_threshold_optimum` INT(11) UNSIGNED, 
     `order_threshold_max` INT(11) UNSIGNED NOT NULL, 
    PRIMARY KEY (`stock_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_stock`
      ADD INDEX (product_id),
      ADD INDEX (group_id)';
    $this->addQuery($query);

    $query = 'CREATE TABLE `societe` (
     `societe_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `name` VARCHAR(50) NOT NULL, 
     `address` VARCHAR(255), 
     `postal_code` INT(11), 
     `city` VARCHAR(255), 
     `phone` VARCHAR(255), 
     `email` VARCHAR(50), 
     `fax` VARCHAR(16) NULL,
     `siret` VARCHAR(14) NULL,
     `contact_name` VARCHAR(50), 
     `contact_surname` VARCHAR(50), 
    PRIMARY KEY (`societe_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'CREATE TABLE `product_stock_out` (
     `stock_out_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `stock_id` INT(11) UNSIGNED NOT NULL, 
     `date` DATETIME NOT NULL, 
     `quantity` INT(11) NOT NULL, 
     `code` VARCHAR(32), 
     `function_id` INT(11) UNSIGNED, 
    PRIMARY KEY (`stock_out_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_stock_out` 
      ADD INDEX (code),
      ADD INDEX (stock_id),
      ADD INDEX (function_id)';
    $this->addQuery($query);
    
    
    $this->makeRevision('0.2');
    $query = 'CREATE TABLE `product_stock_service` (
      `stock_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `product_id` INT (11) UNSIGNED NOT NULL,
      `function_id` INT (11) UNSIGNED NOT NULL,
      `quantity` INT (11) NOT NULL
    ) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_stock_service` 
      ADD INDEX (`product_id`),
      ADD INDEX (`function_id`);';
    $this->addQuery($query);
    
    $query = 'RENAME TABLE `product_stock` TO `product_stock_group` ;';
    $this->addQuery($query);
    
    $this->makeRevision('0.3');
    $query = 'DROP TABLE `product_delivery`;';
    $this->addQuery($query);
    
    $this->makeRevision('0.4');
    $query = 'RENAME TABLE `product_stock_out` TO `product_delivery`;';
    $this->addQuery($query);
    
    $this->makeRevision('0.5');
    $query = 'ALTER TABLE `product_delivery` 
            CHANGE `stock_out_id` `delivery_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT'; 
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_delivery` CHANGE `function_id` `service_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_stock_service` CHANGE `function_id` `service_id` INT( 11 ) UNSIGNED NOT NULL';
    $this->addQuery($query);
    
    $this->makeRevision('0.6');
    $query = 'ALTER TABLE `product_delivery` 
      ADD `status` ENUM (\'planned\',\'done\') NOT NULL,
      ADD INDEX (`date`);';
    $this->addQuery($query);
    
    $this->makeRevision('0.7');
    $query = 'ALTER TABLE `product_delivery` 
      CHANGE `date` `date_dispensation` DATETIME NOT NULL';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_delivery` 
      ADD `date_delivery` DATETIME NULL AFTER `date_dispensation`,
      DROP `status`,
      CHANGE `service_id` `service_id` INT( 11 ) UNSIGNED NOT NULL,
      ADD INDEX (`date_delivery`);';
    $this->addQuery($query);
    
    $this->makeRevision('0.8');
    $query = 'ALTER TABLE `product_delivery` ADD `patient_id` INT( 11 ) UNSIGNED NULL ;';
    $this->addQuery($query);
    
    $this->makeRevision('0.9');
    $query = 'ALTER TABLE `product_delivery` ADD `date_reception` DATETIME NULL AFTER `date_delivery`;';
    $this->addQuery($query);
    
    $this->makeRevision('0.91');
    $query = 'ALTER TABLE `product_order_item` 
      DROP `date_received`,
      DROP `quantity_received`';
    $this->addQuery($query);
    
    $query = 'CREATE TABLE `product_order_item_reception` (
      `order_item_reception_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `order_item_id` INT (11) UNSIGNED NULL,
      `quantity` INT (11) NOT NULL,
      `code` VARCHAR (255),
      `date` DATETIME NOT NULL
    ) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_order_item_reception` 
      ADD INDEX (`order_item_id`),
      ADD INDEX (`date`),
      ADD INDEX (`code`)';
    $this->addQuery($query);
    
    $this->makeRevision('0.92');
    $query = 'CREATE TABLE `product_delivery_trace` (
      `delivery_trace_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `delivery_id` INT (11) UNSIGNED NOT NULL,
      `quantity` INT (11) NOT NULL,
      `code` VARCHAR (255),
      `date_delivery` DATETIME NOT NULL,
      `date_reception` DATETIME) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_delivery_trace` 
      ADD INDEX (`delivery_id`),
      ADD INDEX (`code`),
      ADD INDEX (`date_delivery`),
      ADD INDEX (`date_reception`);';
    $this->addQuery($query);
    
    $query = 'INSERT INTO `product_delivery_trace`
      SELECT \'\', product_delivery.delivery_id, 
                   product_delivery.quantity, 
                   product_delivery.code, 
                   product_delivery.date_delivery, 
                   product_delivery.date_reception
      FROM product_delivery';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_delivery`
      DROP `code`, 
      DROP `date_delivery`,
      DROP `date_reception`';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_reference` CHANGE `price` `price` DECIMAL(10, 5) NOT NULL';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_stock_service`
      ADD `order_threshold_critical` INT(11) UNSIGNED, 
      ADD `order_threshold_min` INT(11) UNSIGNED NOT NULL, 
      ADD `order_threshold_optimum` INT(11) UNSIGNED, 
      ADD `order_threshold_max` INT(11) UNSIGNED NOT NULL';
    $this->addQuery($query);
    
    $this->makeRevision('0.93');
    $query = 'CREATE TABLE `product_discrepancy` (
      `discrepancy_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `quantity` INT (11) NOT NULL,
      `date` DATETIME NOT NULL,
      `description` TEXT,
      `object_id` INT (11) UNSIGNED NOT NULL,
      `object_class` ENUM (\'CProductStockGroup\',\'CProductStockService\') NOT NULL) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_discrepancy` 
      ADD INDEX (`date`),
      ADD INDEX (`object_id`);';
    $this->addQuery($query);
    
    $query = 'ALTER TABLE `product_delivery_trace` CHANGE `date_delivery` `date_delivery` DATETIME NULL';
    $this->addQuery($query);
    
    $this->makeRevision('0.94');
    $query = 'ALTER TABLE `product` 
      CHANGE `societe_id` `societe_id` INT (11) UNSIGNED,
      ADD `quantity` INT (10) UNSIGNED NOT NULL,
      ADD `item_title` VARCHAR (255),
      ADD `unit_quantity` DECIMAL (10,4) UNSIGNED,
      ADD `unit_title` VARCHAR (255),
      ADD `packaging` VARCHAR (255)';
    $this->addQuery($query);
    
    $this->makeRevision('0.95');
    $query = 'ALTER TABLE `product_order_item_reception` ADD `lapsing_date` DATE NOT NULL AFTER `code`';
    $this->addQuery($query);
    
    $query = "ALTER TABLE `product` ADD `renewable` ENUM( '0', '1', '2' ) NOT NULL";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `product_order_item_reception` ADD `barcode_printed` ENUM( '0', '1' ) NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.96");
    $query = "ALTER TABLE `product` ADD `code_lpp` INT (7) UNSIGNED ZEROFILL";
    $this->addQuery($query);
    
    $this->makeRevision("0.97");
    $query = "ALTER TABLE `product_stock_group` ADD `location_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_stock_group` ADD INDEX (`location_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `product_stock_location` (
            `stock_location_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
            `name` VARCHAR (255) NOT NULL,
            `desc` TEXT,
            `position` INT (11),
            `group_id` INT (11) UNSIGNED NOT NULL
          ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_stock_location` ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision('0.98');
    $query = "ALTER TABLE `product_delivery` ADD `order` ENUM('0','1')";
    $this->addQuery($query);
    
    $this->makeRevision('0.99');
    $query = "ALTER TABLE `product` ADD `cancelled` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision('1.00');
    $query = "ALTER TABLE `product_stock_service` ADD `common` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision('1.01');
    $query = "ALTER TABLE `product_delivery` 
              ADD INDEX (`patient_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order_item_reception` 
              ADD INDEX (`lapsing_date`)";
    $this->addQuery($query); 
       
    $this->makeRevision('1.02');
    $query = "UPDATE `product_stock_service` SET `common` = '1' WHERE `common` IS NULL OR `common` = ''";
    $this->addQuery($query);
       
    $this->makeRevision('1.03');
    $query = "ALTER TABLE `product_order_item` CHANGE `order_id` `order_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision('1.04');
    $query = "CREATE TABLE `product_reception` (
              `reception_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `date` DATETIME,
              `societe_id` INT (11) UNSIGNED NOT NULL,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `reference` VARCHAR (255) NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_reception` 
              ADD INDEX (`date`),
              ADD INDEX (`societe_id`),
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision('1.05');
    $query = "ALTER TABLE `product_stock_group` CHANGE `order_threshold_max` `order_threshold_max` INT(11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_stock_service` CHANGE `order_threshold_max` `order_threshold_max` INT(11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision('1.06');
    $query = "ALTER TABLE `product_delivery` ADD `comments` TEXT;";
    $this->addQuery($query);
    
     $this->makeRevision('1.07');
     $query = "ALTER TABLE `societe` 
              CHANGE `address` `address` TEXT,
              CHANGE `postal_code` `postal_code` VARCHAR (5),
              CHANGE `contact_name` `contact_name` VARCHAR (255),
              DROP `contact_surname`,
              ADD `code` VARCHAR (80),
              ADD `carriage_paid` VARCHAR (255),
              ADD `delivery_time` VARCHAR (255);";
    $this->addQuery($query);
       
    $this->makeRevision('1.08');
    $query = "ALTER TABLE `product_order` CHANGE `societe_id` `societe_id` INT( 11 ) UNSIGNED NULL";
    $this->addQuery($query);
    $query = "ALTER TABLE `product` CHANGE `name` `name` VARCHAR( 255 ) NOT NULL";
    $this->addQuery($query);
       
    $this->makeRevision('1.09');
    $query = "ALTER TABLE `societe` ADD `department` INT (11) UNSIGNED";
    $this->addQuery($query);
       
    $this->makeRevision('1.10');
    $query = "ALTER TABLE `societe` 
              CHANGE `department` `departments` TEXT";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order_item_reception` 
              CHANGE `order_item_id` `order_item_id` INT (11) UNSIGNED NOT NULL,
              ADD `reception_id` INT (11) UNSIGNED NOT NULL,
              CHANGE `lapsing_date` `lapsing_date` DATE,
              CHANGE `barcode_printed` `barcode_printed` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order_item_reception` 
              ADD INDEX (`reception_id`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `product` DROP `code_lpp`";
    $this->addQuery($query);
       
    $this->makeRevision('1.11');
    $query = "ALTER TABLE `product_order` 
              ADD `received` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_reception` 
              CHANGE `societe_id` `societe_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision('1.12');
    $query = "ALTER TABLE `product_order` 
              CHANGE `societe_id` `societe_id` INT (11) UNSIGNED NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision('1.13');
    $query = "ALTER TABLE `product_reference` 
              ADD `mdq` INT (11);";
    $this->addQuery($query);
    
    $this->makeRevision('1.14');
    $query = "ALTER TABLE `societe` 
              ADD `distributor_for_id` INT (11) UNSIGNED,
              ADD INDEX (`distributor_for_id`)";
    $this->addQuery($query);
    
    $this->makeRevision('1.15');
    $query = "ALTER TABLE `societe` 
              DROP `distributor_for_id`,
              ADD `distributor_code` VARCHAR (80)";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_reference` 
              ADD `supplier_code` VARCHAR (80)";
    $this->addQuery($query);
    
    $this->makeRevision('1.16');
    $query = "ALTER TABLE `product` ADD `classe_comptable` INT (7) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_reference` ADD `tva` FLOAT UNSIGNED DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision('1.17');
    $query = "ALTER TABLE `product_order` ADD `comments` TEXT";
    $this->addQuery($query);
    
    $this->makeRevision('1.18');
    $query = "ALTER TABLE `product_delivery` 
              CHANGE `stock_id` `stock_id` INT (11) UNSIGNED,
              ADD `date_delivery` DATETIME,
              ADD INDEX (`date_delivery`);";
    $this->addQuery($query);

    $this->makeRevision('1.19');
    $query = "ALTER TABLE `product_reference`
              CHANGE `code` `code` VARCHAR (20),
              CHANGE `supplier_code` `supplier_code` VARCHAR (40),
              CHANGE `mdq` `mdq` INT (11) UNSIGNED,
              ADD `cancelled` ENUM ('0','1');";
    $this->addQuery($query);

    $this->makeRevision('1.20');
    $query = "ALTER TABLE `societe` 
              ADD `customer_code` VARCHAR (80);";
    $this->addQuery($query);
    
    $this->makeRevision("1.21");
    $query = "CREATE TABLE `product_equivalence` (
              `equivalence_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `name` VARCHAR (255) NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product` 
              ADD `equivalence_id` INT (11) UNSIGNED,
              ADD INDEX (`equivalence_id`)";
    $this->addQuery($query);
    $query = "CREATE TABLE `product_selection` (
              `selection_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `name` VARCHAR (255) NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "CREATE TABLE `product_selection_item` (
              `selection_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `product_id` INT (11) UNSIGNED NOT NULL,
              `selection_id` INT (11) UNSIGNED NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_selection_item` 
              ADD INDEX (`product_id`),
              ADD INDEX (`selection_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `product_endowment` (
              `endowment_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `name` VARCHAR (255) NOT NULL,
              `service_id` INT (11) UNSIGNED NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_endowment` ADD INDEX (`service_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `product_endowment_item` (
              `endowment_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `quantity` INT (11) UNSIGNED NOT NULL,
              `endowment_id` INT (11) UNSIGNED NOT NULL,
              `product_id` INT (11) UNSIGNED NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_endowment_item` 
              ADD INDEX (`endowment_id`),
              ADD INDEX (`product_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.22");
    $query = "CREATE TABLE `product_bill` (
              `bill_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `date` DATETIME,
              `societe_id` INT (11) UNSIGNED NOT NULL,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `reference` VARCHAR (80) NOT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_bill` 
              ADD INDEX (`date`),
              ADD INDEX (`societe_id`),
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `product_reception_bill_item` (
              `reception_bill_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `bill_id` INT (11) UNSIGNED,
              `reception_item_id` INT (11) UNSIGNED,
              `quantity` INT (11) UNSIGNED,
              `unit_price` DECIMAL  (12,5)
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_reception_bill_item` 
              ADD INDEX (`bill_id`),
              ADD INDEX (`reception_item_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.23");
    $query = "ALTER TABLE `product` ADD `auto_dispensed` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.24");
    $query = "ALTER TABLE `product_order` 
              ADD `object_id` INT (11) UNSIGNED,
              ADD `object_class` VARCHAR (255);";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order`
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.25");
    $query = "ALTER TABLE `product_delivery` 
              CHANGE `service_id` `service_id` INT (11) UNSIGNED NULL";
    $this->addQuery($query);
              
    $this->makeRevision("1.26");
    $query = "ALTER TABLE `product_delivery` ADD `manual` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $query = "UPDATE `product_delivery` SET `manual` = '1' WHERE 
              `service_id` IS NULL OR 
              `service_id` = '' OR 
              `service_id` = '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.27");
    $query = "ALTER TABLE `product_stock_group` CHANGE `quantity` `quantity` INT( 11 ) NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.28");
    
    // @todo ce callback pourra etre supprim�, car devenu inutile
    // (toutes les cliniques l'ont pass� et a l'install il n'existe aucune commande)
    function updateOrdersReceivedStatus() {
      $order = new CProductOrder;
      
      $where = array(
        "product_order.received"  => " = '0'", // enum('0', '1')
        "product_order.cancelled" => " = 0",
        "product_order.deleted"   => " = 0",
      );
      
      $orders = $order->loadList($where);
      
      foreach ($orders as $_order) {
        if ($_order->countReceivedItems() >= $_order->countBackRefs("order_items")) {
          $_order->received = 1;
          $_order->store();
          mbTrace($_order->_id, "Order is received", true);
        }
      }
      
      return true;
    }
    $this->addFunction("updateOrdersReceivedStatus");
    
    $this->makeRevision("1.29");
    $query = "ALTER TABLE `product` ADD `scc_code` BIGINT (10) UNSIGNED ZEROFILL";
    $this->addQuery($query);
    $query = "ALTER TABLE `product` ADD INDEX (`scc_code`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `societe` ADD `manufacturer_code` INT (5) UNSIGNED ZEROFILL";
    $this->addQuery($query);
    $query = "ALTER TABLE `societe` ADD INDEX (`manufacturer_code`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.30");
    $query = "ALTER TABLE `product_order` ADD `bill_number` VARCHAR (64)";
    $this->addQuery($query);
              
    $this->makeRevision("1.31");
    $query = "ALTER TABLE `product` ADD `code_canonical` VARCHAR (32) AFTER `code`";
    $this->addQuery($query);
    $query = "ALTER TABLE `product` ADD INDEX (`code_canonical`)";
    $this->addQuery($query);
    $chars = str_split('-+*/\\\'\"$_\.\ -()[]&|#@%!?;,:=`~');
    $query = "UPDATE `product` SET `code_canonical` = ".
           CMySQLDataSource::getReplaceQuery($chars, "", '`code`');
    $this->addQuery($query);
    
    $this->makeRevision("1.32");
    $query = "ALTER TABLE `product_reception` 
              ADD `locked` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.33");
    $query = "ALTER TABLE `product_order_item` ADD `lot_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order_item` ADD INDEX (`lot_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.34");
    $query = "ALTER TABLE `product_order_item` ADD `renewal` ENUM ('0','1') NOT NULL DEFAULT '1'";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order_item` ADD INDEX (`renewal`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.35");
    $query = "ALTER TABLE `product_order_item` ADD `septic` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_order_item` ADD INDEX (`septic`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.36");
    $query = "ALTER TABLE `product_order_item` ADD `tva` FLOAT";
    $this->addQuery($query);
    $query = "UPDATE `product_order_item` 
              LEFT JOIN `product_reference` ON `product_reference`.`reference_id` = `product_order_item`.`reference_id`
              SET `product_order_item`.`tva` = `product_reference`.`tva`";
    $this->addQuery($query);
    
    $this->makeRevision("1.37");
    $query = "ALTER TABLE `product` CHANGE `scc_code` `scc_code` VARCHAR( 10 )";
    $this->addQuery($query);

    $this->makeRevision("1.38");   
    $query = "ALTER TABLE `product_order_item_reception` 
              ADD `cancelled` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.39");
    $query = "ALTER TABLE `product_delivery` 
              ADD `type` ENUM ('other','expired','breakage','loss','gift','discrepancy','notused','toomany');";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_delivery` ADD INDEX (`type`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.40");
    $query = "ALTER TABLE `product_stock_location` 
              ADD `object_class` VARCHAR (255) NOT NULL,
              ADD `object_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_stock_location` 
              ADD INDEX (`object_class`),
              ADD INDEX (`object_id`);";
    $this->addQuery($query);
    $query = "UPDATE `product_stock_location` SET
              `object_class` = 'CGroups'";
    $this->addQuery($query);
    $query = "UPDATE `product_stock_location` SET
              `object_id` = `product_stock_location`.`group_id`";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `product_stock_service` 
              ADD `location_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_stock_service` 
              ADD INDEX (`location_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.41");
    $query = "UPDATE `product_stock_location` SET
              `object_class` = 'CGroups' WHERE `object_class` = ''";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_delivery` 
              CHANGE `service_id` `service_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `product_delivery` 
              SET `product_delivery`.`service_id` = NULL WHERE `product_delivery`.`service_id` = 0;";
    $this->addQuery($query);
    $query = "UPDATE `product` 
              SET `product`.`quantity` = 1 WHERE `product`.`quantity` = 0;";
    $this->addQuery($query);
    
    $this->makeRevision("1.42");
    $query = "ALTER TABLE `product_delivery` 
              ADD `stock_class` VARCHAR (80) NOT NULL AFTER `stock_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_delivery` 
              ADD INDEX (`stock_class`)";
    $this->addQuery($query);
    $query = "UPDATE `product_delivery` 
              SET `stock_class` = 'CProductStockGroup'";
    $this->addQuery($query);
    
    $this->makeRevision("1.43");
    $query = "ALTER TABLE `product_delivery_trace` 
              ADD `target_location_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_delivery_trace` 
              ADD INDEX (`target_location_id`)";
    $this->addQuery($query);
    
    // creation des emplacements par defaut pour chaque service et etablissement
      
    // Emplacements de CGroups
    $this->makeRevision("1.44");
    $query = "INSERT INTO product_stock_location (
       name, position, group_id, object_class, object_id
    ) SELECT 'Lieu par d�faut', 0, groups_mediboard.group_id, 'CGroups', groups_mediboard.group_id 
      FROM groups_mediboard";
    $this->addQuery($query);
    
    // Emplacements de CService
    $this->addDependency("dPhospi", "0.16");
    $query = "INSERT INTO product_stock_location (
       name, position, group_id, object_class, object_id
    ) SELECT 'Lieu par d�faut', 0, service.group_id, 'CService', service.service_id 
      FROM service";
    $this->addQuery($query);
    
    // Association des emplacements aux stocks sans emplacement
    $query = "UPDATE `product_stock_group` SET `location_id` = (
       SELECT `product_stock_location`.`stock_location_id` 
       FROM `product_stock_location` 
       WHERE 
         `product_stock_group`.`group_id` = `product_stock_location`.`object_id` AND
         `product_stock_location`.`object_class` = 'CGroups' AND
         `product_stock_location`.`position` = 0 AND
         `product_stock_location`.`name` = 'Lieu par d�faut'
       LIMIT 1
    ) WHERE`product_stock_group`.`location_id` IS NULL";
    $this->addQuery($query);
    
    $query = "UPDATE `product_stock_service` SET `location_id` = (
       SELECT `product_stock_location`.`stock_location_id` 
       FROM `product_stock_location` 
       WHERE 
         `product_stock_service`.`service_id` = `product_stock_location`.`object_id` AND
         `product_stock_location`.`object_class` = 'CService' AND
         `product_stock_location`.`position` = 0 AND
         `product_stock_location`.`name` = 'Lieu par d�faut'
       LIMIT 1
    ) WHERE `product_stock_service`.`location_id` IS NULL";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `product_stock_group` 
              CHANGE `location_id` `location_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `product_stock_service` 
              CHANGE `location_id` `location_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    //ALTER TABLE `product_delivery_trace` CHANGE `code` `code` CHAR( 40 ) NULL;
    //ALTER TABLE `product_order_item_reception` CHANGE `code` `code` CHAR( 40 ) NULL;
    //ALTER TABLE `product_reference` 
    // CHANGE `code` `code` CHAR( 20 ) NULL,
    // CHANGE `supplier_code` `supplier_code` CHAR( 40 ) NULL;
    //ALTER TABLE `product_reception` CHANGE `reference` `reference` CHAR( 40 ) NOT NULL;
    
    $this->makeRevision("1.45");
    // CProductOrderItemReception
    $query = "ALTER TABLE `product_order_item_reception` 
              ADD `units_fixed` ENUM ('0','1') DEFAULT '1',
              ADD `orig_quantity` INT (11);";
    $this->addQuery($query);
    $query = "UPDATE `product_order_item_reception` SET 
              `orig_quantity` = `quantity`,
              `units_fixed` = '0'";
    $this->addQuery($query);
    
    // CProductOrderItem
    $query = "ALTER TABLE `product_order_item` 
              CHANGE `unit_price` `unit_price` DECIMAL (12,5),
              ADD `units_fixed` ENUM ('0','1') DEFAULT '1',
              ADD `orig_quantity` INT (11),
              ADD `orig_unit_price` DECIMAL (12,5);";
    $this->addQuery($query);
    $query = "UPDATE `product_order_item` SET 
              `orig_quantity` = `quantity`,
              `orig_unit_price` = `unit_price`,
              `units_fixed` = '0'";
    $this->addQuery($query);
    
    // CProductReference
    $query = "ALTER TABLE `product_reference` 
              CHANGE `price` `price` DECIMAL (12,5) NOT NULL,
              ADD `units_fixed` ENUM ('0','1') DEFAULT '1',
              ADD `orig_quantity` INT (11),
              ADD `orig_price` DECIMAL (12,5);";
    $this->addQuery($query);
    $query = "UPDATE `product_reference` SET 
              `units_fixed` = '0',
              `orig_quantity` = `quantity`,
              `orig_price` = `price`";
    $this->addQuery($query);
    $query = "UPDATE `product_reference` 
              LEFT JOIN `product` ON `product_reference`.`product_id` = `product`.`product_id`
              SET 
              `product_reference`.`price` = `product_reference`.`orig_price` / ( `product`.`quantity` * `product_reference`.`orig_quantity`),
              `product_reference`.`quantity` = `product_reference`.`orig_quantity` * `product`.`quantity`";
    $this->addQuery($query);
    
    $this->makeRevision("1.46");
    $query = "ALTER TABLE `product` CHANGE `classe_comptable` `classe_comptable` INT (9) UNSIGNED";
    $this->addQuery($query);
    $query = "UPDATE `product` SET `classe_comptable` = RPAD(`classe_comptable`, 9, '0')
              WHERE LENGTH(`classe_comptable`) < 9";
    $this->addQuery($query);
    
    $this->makeRevision("1.47");
    $query = "ALTER TABLE `product_stock_service` 
              ADD `object_class` CHAR (80) NOT NULL AFTER `object_id`,
              CHANGE `service_id` `object_id` INT (11) UNSIGNED NOT NULL";
    $this->addQuery($query);
    $query = "ALTER TABLE `product_stock_service` 
              ADD INDEX (`object_id`)";
    $this->addQuery($query);
    $query = "UPDATE `product_stock_service` SET 
              `object_class` = 'CService'";
    $this->addQuery($query);
    
    $this->makeRevision("1.48");
    $query = "ALTER TABLE `product_delivery` 
              ADD `datetime_min` DATETIME NOT NULL,
              ADD `datetime_max` DATETIME NOT NULL";
    $this->addQuery($query);
    $query = "UPDATE `product_delivery` SET 
              `datetime_min` = `date_dispensation`,
              `datetime_max` = ADDDATE(`date_dispensation`, 1)";
    $this->addQuery($query);
		
		$this->makeRevision("1.49");
		$query = "ALTER TABLE `product` 
              ADD `cladimed` VARCHAR (7);";
    $this->addQuery($query);
		
    $this->makeRevision("1.50");
		$query = "ALTER TABLE `product_order_item_reception` 
              ADD `serial` CHAR (40) AFTER `code`";
    $this->addQuery($query);
    
    $this->mod_version = "1.51";
  }
}
