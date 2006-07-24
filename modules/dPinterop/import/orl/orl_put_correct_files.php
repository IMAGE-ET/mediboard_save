<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("dPfiles", "files"));
require_once($AppUI->getModuleClass("dPcabinet", "consultation"));

$chrono = new Chronometer;

set_time_limit( 1800 );
ignore_user_abort( 1 );

$step = 200;

$current = mbGetValueFromGet("current", 0);
$total = mbGetValueFromGet("total", 0);
$modif = mbGetValueFromGet("modif", 0);
$noconsult = mbGetValueFromGet("noconsult", 0);

$sql = "SELECT * FROM import_fichiers" .
      "\nWHERE mb_id IS NOT NULL" .
      "\nLIMIT ".($current*$step).", $step";
$listImport = db_loadlist($sql);


foreach($listImport as $key => $value) {
  
  $chrono->start();
  
  $file = new CFile;
  $file->load($value["mb_id"]);
  $consult = new CConsultation;
  $consult->load($file->file_consultation);
  $consult->loadRefsFwd();
  $consult->_ref_plageconsult->loadRefsFwd();
  if($consult->_ref_plageconsult->_ref_chir->function_id != 11) {
    //mbTrace($consult, "consult");
    $sql = "SELECT *" .
        "\nFROM consultation, plageconsult, users_mediboard" .
        "\nWHERE consultation.patient_id = '".$consult->patient_id."'" .
        "\nAND consultation.plageconsult_id = plageconsult.plageconsult_id" .
        "\nAND plageconsult.chir_id = users_mediboard.user_id" .
        "\nAND users_mediboard.function_id = '11'" .
        "\nORDER by plageconsult.date DESC, plageconsult.debut DESC";
    $result = db_loadlist($sql);
    if(!count($result)) {
      $noconsult++;
    } else {
      //mbTrace($file, "avant : files/consultations/".$result[0]["consultation_id"]);
      rename("files/consultations/".$file->file_consultation, "files/consultations/".$result[0]["consultation_id"]);
      $file->file_consultation = $result[0]["consultation_id"];
      $file->store();
      //mbTrace($file, "apr�s : files/consultations/".$file->file_consultation);
      $modif++;
    }
  }
  $total++;
  $chrono->stop();
}
$current++;

// @todo : forcer l'affichage � chaque �tape

mbTrace($chrono, "Chrono :");

echo '<p>Op�ration termin�e.</p>';
echo '<p>'.$total.' ligne lues</p>';
echo '<p>'.$modif.' �l�ments modifi�s, '.$noconsult.' consultations non trouv�e</p><hr>';

if(count($listImport) == $step) {
  echo '<a onclick="javascript:next();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script language="JavaScript" type="text/javascript">
    function next() {
      var url = "index.php?m=dPinterop&dialog=1&a=put_correct_files";
      url += "&current=<?php echo $current; ?>";
      url += "&total=<?php echo $total; ?>";
      url += "&modif=<?php echo $modif; ?>";
      url += "&noconsult=<?php echo $noconsult; ?>";
      window.location.href = url;
    }
    next();
  </script>
  <?php
}

?>