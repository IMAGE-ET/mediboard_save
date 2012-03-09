<?php

// HL7v2 Tables
$dPconfig["hl7"] = array(
  "assigningAuthorityUniversalID" => "1.2.250.1.2.3.4",
  "strictSejourMatch"             => "1",
  "indeterminateDoctor"           => "Medecin ind�termin�",
  "doctorActif"                   => "0",
  "importFunctionName"            => "Import",
  "default_version"               => "2.5",
  "CHL7v2Segment"                 => array(
    "PV1_3_2" => ""
  ),  
);

$dPconfig["db"]["hl7v2"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "hl7v2",
  "dbuser" => "",
  "dbpass" => "",
);

?>