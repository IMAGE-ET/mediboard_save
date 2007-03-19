<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

$chrono = new Chronometer;

set_time_limit( 1800 );
ignore_user_abort( 1 );

$step = 10;

$current = mbGetValueFromGet("current", 0);
$new = mbGetValueFromGet("new", 0);
$link = mbGetValueFromGet("link", 0);
$nofile = mbGetValueFromGet("nofile", 0);
$noconsult = mbGetValueFromGet("noconsult", 0);
$totalSize = mbGetValueFromGet("totalSize", 0);
$total = mbGetValueFromGet("total", 0);

$sql = "SELECT * FROM import_fichiers" .
      "\nLIMIT ".($current*$step).", $step";
$listImport = db_loadlist($sql);


foreach($listImport as $key => $value) {
  
  $chrono->start();
  
  $sql = "SELECT * FROM files_mediboard" .
      "\nWHERE files_mediboard.file_name = '".addslashes(trim($value["nom"]))."'";
  $match = db_loadlist($sql);
  //echo "$total : Cas de ".$value["nom"]." :<br>";
  if(!count($match)) {
    $file = new CFile;
    // DB Table key
    $file_id = '';
    // DB Fields
    $file->file_name = trim($value["nom"]);
    $file->file_date = db_unix2dateTime( time() );
    $file->file_real_filename = uniqid( rand() );
    if(strpos($file->file_name, ".txt")) {
      $file->file_type = "text/plain";
    }
    elseif(strpos($file->file_name, ".jpg")) {
      $file->file_type = "image/jpeg";
      $file->file_name = strtoupper($file->file_name);
    }
    else
      $file->file_type = "application/octet-stream";
    $sql = "SELECT *" .
        "\nFROM consultation, plageconsult, import_patients" .
        "\nWHERE consultation.patient_id = import_patients.mb_id" .
        "\nAND import_patients.patient_id = '".$value["pat_id"]."'" .
        "\nAND consultation.plageconsult_id = plageconsult.plageconsult_id" .
        "\nORDER by plageconsult.date DESC, plageconsult.debut DESC";
    $result = db_loadlist($sql);
    if(!count($result))
      $noconsult++;
    elseif(!file_exists("modules/dPinterop/doc_recus/".$file->file_name)) {
      $nofile++;
      echo "modules/dPinterop/doc_recus/".$file->file_name."<br>";
    }
    else {
      $consult = $result[0];
      $file->file_consultation = $consult["consultation_id"];
      $file->file_size = filesize("modules/dPinterop/doc_recus/".$file->file_name);
      $totalSize += $file->file_size;
      if(!file_exists("files/consultations/".$file->file_consultation))
        mkdir("files/consultations/".$file->file_consultation, 0777);
      copy("modules/dPinterop/doc_recus/".$file->file_name, "files/consultations/".$file->file_consultation."/".$file->file_real_filename);
      chmod ("files/consultations/".$file->file_consultation."/".$file->file_real_filename, 0777); 
      $file->store();
      $sql = "UPDATE import_fichiers" .
            "\nSET mb_id = '".$file->file_id."'" .
            "\nWHERE nom = '".$value["nom"]."'";
      db_exec($sql);
      //mbTrace($file);
      $new++;
    }
  } else {
    $sql = "UPDATE import_fichiers" .
        "\nSET mb_id = '".$match[0]["file_id"]."'" .
        "\nWHERE nom = '".$value["nom"]."'";
    db_exec($sql);
    $link++;
  }
  $total++;
  $chrono->stop();
}
$current++;

$bytes = $totalSize;
$value = $bytes;
$unit = "o";

$kbytes = $bytes / 1024;
if ($kbytes >= 1) {
  $value = $kbytes;
  $unit = "Ko";
}

$mbytes = $kbytes / 1024;
if ($mbytes >= 1) {
  $value = $mbytes;
  $unit = "Mo";
}

$gbytes = $mbytes / 1024;
if ($gbytes >= 1) {
  $value = $gbytes;
  $unit = "Go";
}
    
// Value with 3 significant digits, thent the unit
$value = round($value, $value > 99 ? 0 : $value >  9 ? 1 : 2);
$totalSizePrint = "$value $unit";

// @todo : forcer l'affichage � chaque �tape

mbTrace($chrono, "Chrono :");

echo '<p>Op�ration termin�e (step '.$current.'/'.ceil(14000/$step).').</p>';
echo '<p>'.$total.' ligne lues</p>';
echo '<p>'.$new.' �l�ments cr��s ('.$totalSizePrint.'), ';
echo $link.' �l�ments li�s, ';
echo $nofile.' fichiers non trouv�s, ';
echo $noconsult.' consultations non trouv�s</p><hr>';

if(count($listImport) == $step) {
  echo '<a onclick="javascript:next();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script language="JavaScript" type="text/javascript">
    function next() {
      var url = "index.php?m=dPinterop&dialog=1&a=put_files";
      url += "&current=<?php echo $current; ?>";
      url += "&new=<?php echo $new; ?>";
      url += "&link=<?php echo $link; ?>";
      url += "&nofile=<?php echo $nofile; ?>";
      url += "&noconsult=<?php echo $noconsult; ?>";
      url += "&totalSize=<?php echo $totalSize; ?>";
      url += "&total=<?php echo $total; ?>";
      window.location.href = url;
    }
    next();
  </script>
  <?php
}

?>