<?php

// HL7v2 Tables
$dPconfig["hl7"] = array(
  "assigning_authority_namespace_id"        => "Mediboard",
  "assigning_authority_universal_id"      => "1.2.250.1.2.3.4",
  "assigning_authority_universal_type_id" => "OX",
  "sending_application"                   => "Mediboard",
  "sending_facility"                      => "Mediboard",
  "strictSejourMatch"                     => "1",
  "indeterminateDoctor"                   => "Medecin ind�termin�",
  "doctorActif"                           => "0",
  "importFunctionName"                    => "Import",
  "default_version"                       => "2.5",
  "default_fr_version"                    => "FR_2.3",
  "CHL7v2Segment"                         => array(
    "PV1_3_2" => "",
    "PV1_3_3" => "",
    "ignore_unexpected_z_segment" => "0",
  ),
  "tag_default" => ""
);

$dPconfig["db"]["hl7v2"] = array(
  "dbtype" => "mysql",
  "dbhost" => "localhost",
  "dbname" => "hl7v2",
  "dbuser" => "",
  "dbpass" => "",
);

