<?php /* $Id: CMbCalendar.class.php $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12920 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile("iCalcreator/iCalcreator.class");

if (!class_exists("vcalendar", false)) {
  return;
}

class CMbCalendar extends vcalendar {
  function __construct($name, $description = ""){
    parent::__construct();
    
    //Ajout de quelques propori�t�s
    $this->setProperty("method", "PUBLISH");
    $this->setProperty("x-wr-calname", $name);
    
    if ($description) {
      $this->setProperty("X-WR-CALDESC", $description);
    }
    
    $this->setProperty("X-WR-TIMEZONE", CAppUI::conf("timezone"));
  }
  
  //fonction permettant de cr�er un ev�nement de calendrier de fa�on simplifi�e
  function addEvent($location, $summary, $description, $comment, $guid, $start, $end) {
    $date_re = "/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/";
    
    preg_match($date_re, $start, $matches_start);
    $start = array(
      "year"  => $matches_start[1], 
      "month" => $matches_start[2], 
      "day"   => $matches_start[3], 
      "hour"  => $matches_start[4], 
      "min"   => $matches_start[5], 
      "sec"   => 0
    );
    
    preg_match($date_re, $end, $matches_end);
    $end = array(
      "year"  => $matches_end[1], 
      "month" => $matches_end[2], 
      "day"   => $matches_end[3], 
      "hour"  => $matches_end[4], 
      "min"   => $matches_end[5], 
      "sec"   => 0
    );
    
    $vevent = $this->newComponent("vevent");
    
    $vevent->setProperty("dtstart", $start);
    $vevent->setProperty("dtend", $end);
    $vevent->setProperty("LOCATION", $location);
    $vevent->setProperty("UID", $guid);
    $vevent->setProperty("summary", $summary);
    
    if ($description) {
      $vevent->setProperty("description", $description);
    }
    
    if ($comment) {
      $vevent->setProperty("comment", $comment);
    }
    
    $this->setComponent($vevent, $guid);
  }
}
