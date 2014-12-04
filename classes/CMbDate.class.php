<?php

/**
 * $Id$
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * Date utility class
 */
class CMbDate {
  static $secs_per = array (
    "year"   => 31536000, // 60 * 60 * 24 * 365
    "month"  =>  2592000, // 60 * 60 * 24 * 30
    "week"   =>   604800, // 60 * 60 * 24 * 7
    "day"    =>    86400, // 60 * 60 * 24
    "hour"   =>     3600, // 60 * 60
    "minute" =>       60, // 60
    "second" =>        1, // 1
  );

  static $xmlDate     = "%Y-%m-%d";
  static $xmlTime     = "%H:%M:%S";
  static $xmlDateTime = "%Y-%m-%dT%H:%M:%S";

  /**
   * Compute real relative achieved gregorian durations in years and months
   *
   * @param string $from Starting time
   * @param string $to   Ending time, now if null
   *
   * @return array[int] Number of years and months
   */
  static function achievedDurations($from, $to = null) {
    $achieved = array(
      "year"  => "??",
      "month" => "??",
    );

    if ($from == "0000-00-00" || !$from) {
      return $achieved;
    }

    if (!$to) {
      $to = CMbDT::date();
    }

    list($yf, $mf, $df) = explode("-", $from);
    list($yt, $mt, $dt) = explode("-", $to);

    $achieved["month"] = 12*($yt-$yf) + ($mt-$mf);
    if ($mt == $mf && $dt < $df) {
      $achieved["month"]--;
    }

    $achieved["year"] = intval($achieved["month"] / 12);
    return $achieved;
  }


  /**
   * Compute duration between two date time
   *
   * @param string $from      From time (datetime)
   * @param string $to        To time, now if null (datetime)
   * @param int    $min_count return only positive units
   *
   * @return array array("unit" => string, "count" => int)
   */
  static function duration($from, $to = null, $min_count = 0) {
    $duration = array();
    if (!$from) {
      return null;
    }

    if (!$to) {
      $to = CMbDT::dateTime();
    }

    $diff = strtotime($to) - strtotime($from);

    // Find the best unit
    foreach (self::$secs_per as $unit => $secs) {
      if (abs($diff / $secs) > $min_count) {
        $duration[$unit] = intval($diff / $secs);
        $diff= $diff / $secs + ($diff%$secs);
      }
    }

    return $duration;
  }

  /**
   * Compute user friendly approximative duration between two date time
   *
   * @param string $from      From time
   * @param string $to        To time, now if null
   * @param int    $min_count The minimum count to reach the upper unit, 2 if undefined
   *
   * @return array array("unit" => string, "count" => int)
   */
  static function relative($from, $to = null, $min_count = 2) {
    if (!$from) {
      return null;
    }

    if (!$to) {
      $to = CMbDT::dateTime();
    }

    // Compute diff in seconds
    $diff = strtotime($to) - strtotime($from);

    // Find the best unit
    foreach (self::$secs_per as $unit => $secs) {
      if (abs($diff / $secs) > $min_count) {
        break;
      }
    }

    return array (
      "unit"  => $unit,
      "count" => intval($diff / $secs),
    );
  }

  /**
   * Get the month number for a given datetime
   *
   * @param string $date Datetime
   *
   * @return int The month number
   */
  static function monthNumber($date) {
    return intval(CMbDT::format($date, "%m"));
  }

  /**
   * Get the week number for a given datetime
   *
   * @param string $date Datetime
   *
   * @return int The week number
   */
  static function weekNumber($date) {
    return intval(date("W", strtotime($date)));
  }

  /**
   * Get the week number in the month
   *
   * @param string $date Date
   *
   * @return int The week number
   */
  static function weekNumberInMonth($date) {
    $month = self::monthNumber($date);
    $week_number = 0;

    do {
      $date = CMbDT::date("-1 WEEK", $date);
      $_month = self::monthNumber($date);
      $week_number++;
    } while ($_month == $month);

    return $week_number;
  }

  /**
   * Give a Dirac hash of given datetime
   *
   * @param string $period   One of minute, hour, day, week, month or year
   * @param string $datetime Datetime
   *
   * @return datetime Hash
   */
  static function dirac($period, $datetime) {
    switch ($period) {
      case "min":
        return CMbDT::format($datetime, "%Y-%m-%d %H:%M:00");
      case "hour":
        return CMbDT::format($datetime, "%Y-%m-%d %H:00:00");
      case "day":
        return CMbDT::format($datetime, "%Y-%m-%d 00:00:00");
      case "week":
        return CMbDT::transform("last sunday +1 day", $datetime, "%Y-%m-%d 00:00:00");
      case "month":
        return CMbDT::format($datetime, "%Y-%m-01 00:00:00");
      case "year":
        return CMbDT::format($datetime, "%Y-01-01 00:00:00");
      default:
        trigger_error("Can't make a Dirac hash for unknown '$period' period", E_USER_WARNING);
    }
  }

  /**
   * Give a position to a datetime relative to a reference
   *
   * @param string $datetime  Datetime
   * @param string $reference Reference
   * @param string $period    One of 1hour, 6hours, 1day
   *
   * @return float
   */
  static function position($datetime, $reference, $period) {
    $diff = strtotime($datetime) - strtotime($reference);

    switch ($period) {
      case "1hour":
        return $diff / CMbDate::$secs_per["hour"];
      case "6hours":
        return $diff / (CMbDate::$secs_per["hour"] * 6);
      case "1day":
        return $diff / CMbDate::$secs_per["day"];
      default:
        trigger_error("Can't proceed for unknown '$period' period", E_USER_WARNING);
    }
  }

  /**
   * Turn a datetime to its UTC timestamp equivalent
   *
   * @param string $datetime Datetime
   *
   * @return int
   */
  static function toUTCTimestamp($datetime) {
    static $default_timezone;

    if (!$default_timezone) {
      $default_timezone = date_default_timezone_get();
    }

    // Temporary change timezone to UTC
    date_default_timezone_set("UTC");
    $datetime = strtotime($datetime) * 1000; // in ms;
    date_default_timezone_set($default_timezone);

    return $datetime;
  }


  /**
   * return an array of dates non worked
   *
   * @param string  $date          date to check (used to analyse the year)
   *
   * @param bool    $includeRegion add region holidays (cantons, regions)
   *
   * @param CGroups $group         group used for the check, null = current
   *
   * @return array
   */
  static function getHolidays($date = null, $includeRegion = true, $group = null) {
    $calendar = array();

    //no date => today
    if (!$date) {
      $date = CMbDT::date();
    }

    $year = CMbDT::transform("+0 DAY", $date, "%Y");
    $previous_year = $year-1;
    $next_year = $year+1;
    $code_pays = CAppUI::conf("ref_pays");

    switch ($code_pays) {
      // France
      case '1':
        $paques = CMbDT::getEasterDate($date);
        $calendar["$year-01-01"] = "Jour de l'an";                            // Jour de l'an
        $calendar["$year-05-01"] = "F�te du travail";                         // F�te du travail
        $calendar["$year-05-08"] = "Victoire de 1945";                        // Victoire de 1945
        $calendar[CMbDT::date("+1 DAY", $paques)] = "Lundi de paques";        // Lundi de paques
        $calendar[CMbDT::date("+39 DAYS", $paques)] = "Jeudi de l'ascension"; // Jeudi de l'ascension
        $calendar[CMbDT::date("+50 DAYS", $paques)] = "Lundi de pentec�te";   // Lundi de pentec�te
        $calendar["$year-07-14"] = "F�te Nationnale";                         // F�te nationnale
        $calendar["$year-08-15"] = "Assomption";                              // Assomption
        $calendar["$year-11-01"] = "Toussaint";                                // Toussaint
        $calendar["$year-11-11"] = "Armistice 1918";                          // Armistice 1918
        $calendar["$year-12-25"] = "No�l";                                    // No�l
        $calendar["$next_year-01-01"] = "Jour de l'an";
        break;

      // Switzerland
      case '2':
        $calendar["$year-01-01"] = "Jour de l'an";                // Jour de l'an
        $calendar["$year-08-01"] = "F�te Nationnale Suisse";      // fete nationnale suisse
        $calendar["$year-12-25"] = "No�l";                        // No�l
        $calendar["$next_year-01-01"] = "Jour de l'an";                // Jour de l'an
        break;

      default:
        return $calendar;
        break;
    }

    if ($includeRegion) {
      $holidaysSub = self::getCpHolidays($date, $group); //r�cup�ration des r�gions
      $calendar = array_merge($calendar, $holidaysSub);
    }

    return $calendar;
  }

  /**
   * Get the holidays by region
   *
   * @param string       $date  date to check
   *
   * @param null|CGroups $group group, null = current
   *
   * @return array
   */
  static function getCpHolidays($date, $group = null) {
    $subdivisionHoliday = array();
    $pays = CAppUI::conf("ref_pays");

    //no group, load current
    if (!$group) {
      $group = CGroups::loadCurrent();
    }

    //no CP, abord
    if (!$group->cp) {
      return $subdivisionHoliday;
    }

    $year = CMbDT::transform("+0 DAY", $date, "%Y");
    $paques = CMbDT::getEasterDate($date);

    switch ($pays) {
      // France
      case '1':
        return $subdivisionHoliday;
        break;

      // Switzerland
      case '2':
        $firstSundaySeptember = CMbDT::transform("next sunday", $year."-09-00", "%Y-%m-%d");
        $thirdSundaySeptember = CMbDT::transform("+2 WEEK", $firstSundaySeptember, "%Y-%m-%d");

        $canton = substr($group->cp, 0, 2);
        switch ($canton) {
          // Vaud
          case '10':
            $subdivisionHoliday["$year-01-02"] = "Saint-Berchtold";
            $subdivisionHoliday[CMbDT::transform("last friday", $paques, "%Y-%m-%d")] = "vendredi saint";
            $subdivisionHoliday[CMbDT::transform("+1 DAY", $paques, "%Y-%m-%d")] = "lundi de paques";
            $subdivisionHoliday[CMbDT::transform("+39 DAY", $paques, "%Y-%m-%d")] = "Ascension";
            $subdivisionHoliday[CMbDT::transform("+50 DAY", $paques, "%Y-%m-%d")] = "lundi de pantecote";
            $subdivisionHoliday[CMbDT::transform("+1 DAY", $thirdSundaySeptember, "%Y-%m-%d")] = "Lundi du Je�ne f�d�ral";
            break;

          // Gen�ve
          case '12':
            $subdivisionHoliday[ CMbDT::transform("next thursday", $firstSundaySeptember, "%Y-%m-%d")] = "Je�ne Genevois";
            $subdivisionHoliday[CMbDT::transform("last friday", $paques, "%Y-%m-%d")] = "vendredi saint";
            $subdivisionHoliday[CMbDT::transform("+1 DAY", $paques, "%Y-%m-%d")] = "lundi de paques";
            $subdivisionHoliday[CMbDT::transform("+39 DAY", $paques, "%Y-%m-%d")] = "Ascension";
            $subdivisionHoliday[CMbDT::transform("+50 DAY", $paques, "%Y-%m-%d")] = "lundi de pantecote";
            $subdivisionHoliday["$year-12-31"] = "fete du travail";
            break;
          default:
            return $subdivisionHoliday;
            break;
        }
        break;

      default:
        return $subdivisionHoliday;
        break;
    }
    return $subdivisionHoliday;
  }

  /**
   * Check if the given date is an holyday
   *
   * @param string  $date          date to check (used to analyse the year)
   * @param bool    $includeRegion add region holidays (cantons, regions)
   * @param CGroups $group         group used for the check, null = current
   *
   * @return bool
   */
  static function isHoliday($date, $includeRegion = true, $group = null) {
    $holidays = self::getHolidays($date, $includeRegion, $group);

    return array_key_exists($date, $holidays);
  }

  static $days_name = array(
    1 => array(
      'Jour de l\'an',
      'Basile',
      'Genevi�ve',
      'Odilon',
      'Edouard',
      'Epiphanie',
      'Raymond',
      'Lucien',
      'Alix',
      'Guillaume',
      'Paulin',
      'Tatiana',
      'Yvette',
      'Nina',
      'R�mi',
      'Marcel',
      'Roseline',
      'Prisca',
      'Marius',
      'S�bastien',
      'Agn�s',
      'Vincent',
      'Barnard',
      'Fr. de Sales',
      'Conv. S. Paul',
      'Paule',
      'Ang�le',
      'Th. d\'Aquin, Maureen',
      'Gildas',
      'Martine',
      'Marcelle'
    ),
    2 => array(
      'Ella',
      'Chandeleur',
      'Blaise',
      'V�ronique',
      'Agathe',
      'Gaston',
      'Eug�nie',
      'Jacqueline',
      'Apolline',
      'Arnaud',
      'N-D Lourdes',
      'F�lix',
      'B�atrice',
      'Valentin',
      'Claude',
      'Julienne',
      'Alexis',
      'Bernadette',
      'Gabin',
      'Aim�e',
      'P. Damien',
      'Isabelle',
      'Lazare',
      'Modeste',
      'Rom�o',
      'Nestor',
      'Honorine',
      'Romain',
      'August'
    ),
    3 => array(
      "Aubin",
      "Charles le B.",
      "Gu�nol�",
      "Casimir",
      "Olive",
      "Colette",
      "F�licit�",
      "Jean de Dieu",
      "Fran�oise",
      "Vivien",
      "Rosine",
      "Justine",
      "Rodrigue",
      "Mathilde",
      "Louise",
      "B�n�dicte",
      "Patrice",
      "Cyrille",
      "Joseph",
      "Alessandra",
      "Cl�mence",
      "L�a",
      "Victorien",
      "Catherine De Su�de",
      "Humbert",
      "Larissa",
      "Habib",
      "Gontran",
      "Gwladys",
      "Am�d�e",
      "Benjamin",
    ),
    4 => array(
      "Lundi de P�ques",
      "Sandrine",
      "Richard",
      "Isidore",
      "Ir�ne",
      "Marcellin",
      "Jean-Baptiste de la Salle",
      "Julie",
      "Gautier",
      "Fulbert",
      "Stanislas",
      "Jules",
      "Ida",
      "Maxime",
      "Paterne16",
      "Beno�t-Joseph",
      "Anicet",
      "Parfait",
      "Emma",
      "Odette",
      "Anselme",
      "Alexandre",
      "Georges",
      "Fid�le",
      "Marc",
      "Alida",
      "Zita",
      "Jour du Souv.",
      "Cath. de Si",
      "Robert"
    ),
    5 => array(
      "F�te du Travail",
      "Boris",
      "Phil., Jacq.",
      "Sylvain",
      "Judith",
      "Prudence19",
      "Gis�le",
      "Victoire 1945",
      "Ascension",
      "Solange",
      "Estelle",
      "Achille",
      "Rolande",
      "Matthias",
      "Denise",
      "Honor�",
      "Pascal",
      "�ric",
      "Yves",
      "Bernardin",
      "Constantin",
      "Emile",
      "Didier",
      "Donatien",
      "Sophie",
      "F�te des M�res",
      "Augustin",
      "Germain",
      "Aymar",
      "Ferdinand",
      "Visitation"
    ),
    6 => array(
      "Justin",
      "Blandine",
      "K�vin",
      "Clotilde",
      "Igor",
      "Norbert",
      "Gilbert",
      "M�dard",
      "Diane",
      "Landry",
      "Barnab�",
      "Guy",
      "AntoindP",
      "Elis�e",
      "Germaine",
      "Aur�lien",
      "Herv�",
      "L�once",
      "Romuald",
      "F�te des P�res",
      "Rodolphe",
      "Alban",
      "Audrey",
      "Jean-Baptiste",
      "Prosper",
      "Anthelme",
      "Fernand",
      "Ir�n�e",
      "Pierre, Paul",
      "Martial"
    ),
    7 => array(
      "Thierry",
      "Martinien",
      "Thomas",
      "Florent",
      "Antoine",
      "Mariette",
      "Raoul",
      "Thibault",
      "Amandine",
      "Ulrich",
      "Beno�t",
      "Olivier",
      "Henri, Jo�l",
      "F�te Nationale",
      "Donald",
      "N-Mt-Carmel",
      "Charlotte",
      "Fr�d�ric",
      "Ars�ne",
      "Marina",
      "Victor",
      "Marie-Mad",
      "Brigitte",
      "Christine",
      "Jacques",
      "Anne,Joach",
      "Nathalie",
      "Samson",
      "Marthe",
      "Juliette",
      "IgnacdL"
    ),
    8 => array(
      "Alphonse",
      "Julien-Eym",
      "Lydie",
      "Jean-Marie, Vianney",
      "Abel",
      "Transfiguration",
      "Ga�tan",
      "Dominique",
      "Amour",
      "Laurent",
      "Claire",
      "Clarisse",
      "Hippolyte",
      "Evrard",
      "Assomption",
      "Armel",
      "Hyacinthe",
      "H�l�ne",
      "Jean-Eudes",
      "Bernard",
      "Christophe",
      "Fabrice",
      "RosdL",
      "Barth�lemy",
      "Louis",
      "Natacha",
      "Monique",
      "Augustin",
      "Sabine",
      "Fiacre",
      "Aristide"
    ),
    9 => array(
      "Gilles",
      "Ingrid",
      "Gr�goire",
      "Rosalie",
      "Ra�ssa",
      "Bertrand",
      "Reine",
      "Nativit� N.-D",
      "Alain",
      "In�s",
      "Adelphe",
      "Apollinaire",
      "Aim�",
      "LCroix",
      "Roland",
      "Edith",
      "Renaud",
      "Nad�ge",
      "�milie",
      "Davy",
      "Matthieu",
      "Maurice",
      "Constant",
      "Th�cle",
      "Hermann",
      "C�me, Damien",
      "Vinc. dP",
      "Venceslas",
      "Michel",
      "J�r�me"
    ),
    10 => array(
      "Th�r.de l'E",
      "L�ger",
      "G�rard",
      "Fr. d'Assise",
      "Fleur",
      "Bruno",
      "Serge",
      "P�lagie",
      "Denis",
      "Ghislain",
      "Firmin",
      "Wilfried",
      "G�raud",
      "Juste",
      "Th�r. d'Avila",
      "Edwige",
      "Baudoin",
      "Luc",
      "Ren�",
      "Adeline",
      "C�line",
      "Elodie",
      "JeadC.",
      "Florentin",
      "Cr�pin",
      "Dimitri",
      "Emeline",
      "Simon, Jude",
      "Narcisse",
      "Bienvenue",
      "Quentin"
    ),
    11 => array(
      "Toussaint",
      "D�funt",
      "Hubert",
      "Charles",
      "Sylvie",
      "Bertille",
      "Carine",
      "Geoffroy",
      "Th�odore",
      "L�on",
      "Armistice 1918",
      "Christian",
      "Brice",
      "Sidoine",
      "Albert",
      "Marguerite",
      "Elisabeth",
      "Aude",
      "Tanguy",
      "Edmond",
      "Pr�s. Marie",
      "C�cile",
      "Christ Roi",
      "Flora",
      "Cath. L.",
      "Delphine",
      "S�verin",
      "Jacq. de la M.",
      "Saturnin",
      "Avent"
    ),
    12 => array(
      "Florence",
      "Viviane",
      "Fran�ois-Xavier",
      "Barbara",
      "G�rald",
      "Nicolas",
      "Ambroise",
      "Imm. Conception",
      "Guadalupe",
      "Romaric",
      "Daniel",
      "Chantal",
      "Lucie",
      "Odile",
      "Ninon",
      "Alice",
      "Ga�l",
      "Gatien",
      "Urbain",
      "Th�ophile",
      "PierrCan.",
      "Fr.-Xavi�re",
      "Armand",
      "Ad�le",
      "No�l",
      "Etienne",
      "Jean",
      "Innocents",
      "David",
      "Roger",
      "Sylvestre"
    ),
  );
}


