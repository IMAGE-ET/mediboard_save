<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>Les global inutiles</title>
<script type="text/javascript">
function $(str) {
  return document.getElementById(str);
}

function toggle(element) {
  if (element.style.display != 'none')
    element.style.display = 'none';
  else 
    element.style.display = '';
}
</script>
</head>

<body>
<p>La recherche s'effectue de la mani�re suivante :<br />
On rep�re chaque variable d�clar�e en global, et si elle n'est pas r�pet�e dans le fichier, alors elle est jug�e inutile.
Il peut y avoir des variables inutiles non reper�es, mais il ne peut pas y avoir de variables jug�es inutiles alors qu'elle ne le sont pas.</p> 

<?php
$list = array_merge(
  glob ("../*.php"),
  glob ("../classes/*.php"),
  glob ("../includes/*.php"),
  glob ("../legacy/*.php"),
  glob ("../locales/*.php"),
  glob ("../modules/*/*.php")
);

$n = 0;
function display($title, $data) {
  global $n;
  echo '<a href="javascript:toggle($(\'elt-'.$n.'\'))" style="display: block;">'.$title.'</a>';
  echo '<blockquote style="display: none;" id="elt-'.$n.'">' . $data . '</blockquote>';
  $n++;
}

$count = 0;
foreach ($list as $file) {
  $f = fopen($file, 'r');
  $variables = array();
  $variables_used = array();
  
  // for every line
  while ($line = fgets($f)) {
    
    // if it declares global variables
    if (substr(trim($line), 0, 6) == 'global') {
      $vars = array();
      preg_match_all('/\$[A-z0-9_]*/', $line, $vars);
      if (isset ($vars[0]))
        $variables += $vars[0];
    } 
    
    // if it's a normal line
    else {
      foreach ($variables as $v) {
        //if (preg_match('/\\'.$v.'[^A-z0-9_]/', $line) && !in_array($v, $variables_used)) { // to avoid thinking $module is $m
        if (strstr($line, $v) && !in_array($v, $variables_used)) {
          //echo '/\\'.$v.'^[A-z0-9_]/';
          $variables_used[] = $v;
        }
      }
    }
  }
  
  //var_dump($variables_used);
  
  $unused_vars = array_diff($variables, $variables_used);
  if (count($unused_vars)) {
    //display ($file .' >> '. implode(', ', $unused_vars), highlight_file($file, true), true);
    $count += count($unused_vars);
    echo '<b>'.$file.'</b><br />&nbsp;&nbsp;&nbsp; >> '. implode(', ', $unused_vars);
    echo '<br />';
  }
}
echo 'Au moins '.$count.' variables globales susceptibles d\'etre inutiles';
?>
</body>
</html>