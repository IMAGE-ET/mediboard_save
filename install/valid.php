<?php

// Check the check.php page
if (!(class_exists("CPrerequisite"))) {
  class CPrerequisite {
    var $name = "";
    var $description = "";
    var $mandatory = false;
    var $reason = array();
  
    function check() {
      return false;
    }
  }
  
  class CPearPackage extends CPrerequisite {
    var $status = "stable";
  
    function check() {
      return @include_once("$this->name.php");
    }
  }
  
  class CPHPExtension  extends CPrerequisite {
    function check() {
      return extension_loaded(strtolower($this->name));
    }
  }
  
  class CPHPVersion extends CPrerequisite {
    function check() {
      return phpversion() >= $this->name;
    }
  }
}


// Data from check.php file
$packages = array();

$package = new CPearPackage;
$package->name = "Archive/Tar";
$package->description = "Package de manipulation d'archives au format GNU TAR";
$package->mandatory = true;
$package->reasons[] = "Installation de Mediboard";
$package->reasons[] = "Import des fonctions de GHM";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Config";
$package->description = "Package de manipulation de fichiers de configuration";
$package->mandatory = true;
$package->reasons[] = "Configuration g�n�rale de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "DB";
$package->description = "Package de manipulation de base de donn�es";
$package->mandatory = true;
$package->reasons[] = "Assistant d'installation de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Auth";
$package->description = "Package d'authentification multi-support";
$package->mandatory = true;
$package->reasons[] = "Assistant d'installation de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "PHP/CodeSniffer";
$package->description = "Analyseur syntaxique de code source";
$package->status = "beta";
$package->mandatory = false;
$package->reasons[] = "Outil de g�nie logiciel pour v�rifier la qualit� du code source de Mediboard";
$packages[] = $package;

/*
$package = new CPearPackage;
$package->name = "phpUnit";
$package->description = "Package de test unitaire";
$package->mandatory = false;
$package->reasons[] = "Tests unitaires et fonctionnels de Mediboard";
$package->reasons[] = "cf. <a href='http://www.phpunit.de/wiki/Documentation' style='display: inline;'>http://www.phpunit.de/wiki/Documentation</a>";
$packages[] = $package;
*/

$extensions = array();

$extension = new CPHPExtension;
$extension->name = "MySQL";
$extension->description = "Extension d'acc�s aux bases de donn�es MySQL";
$extension->mandatory = true;
$extension->reasons[] = "Acc�s � la base de donn�e de principale Mediboard";
$extension->reasons[] = "Acc�s aux bases de donn�es de codage CCAM, CIM et GHM";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "MBString";
$extension->description = "Extension de gestion des cha�nes de caract�res multi-octets";
$extension->mandatory = true;
$extension->reasons[] = "Internationalisation de Mediboard";
$extension->reasons[] = "Interop�rabilit� Unicode";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "ZLib";
$extension->description = "Extension de compression au format GNU ZIP (gz)";
$extension->mandatory = true;
$extension->reasons[] = "Installation de Mediboard";
$extension->reasons[] = "Accel�ration substancielle de l'application via une communication web compress�e";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "Zip";
$extension->description = "Extension de compression au format zip";
$extension->mandatory = true;
$extension->reasons[] = "Installation de Mediboard";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "JSON";
$extension->description = "Extension de manipulation de donn�es au format JSON. Inclus par d�faut avec PHP 5.2+";
$extension->mandatory = true;
$extension->reasons[] = "Passage de donn�es de PHP vers Javascript.";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "DOM";
$extension->description = "Extension de manipulation de fichier XML avec l'API DOM";
$extension->mandatory = true;
$extension->reasons[] = "Import de base de donn�es m�decin";
$extension->reasons[] = "Interop�rabilit� HPRIM XML, notamment pour le PMSI";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "SOAP";
$extension->description = "Extension permettant d'effectuer des requetes";
$extension->reasons[] = "Requetes vers les serveurs de r�sultats de laboratoire";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "FTP";
$extension->description = "Extension d'acc�s aux serveur FTP";
$extension->reasons[] = "Envoi HPRIM vers des serveurs de facturation";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "BCMath";
$extension->description = "Extension de calculs sur des nombres de pr�cision arbitraire";
$extension->reasons[] = "Validation des codes INSEE et ADELI";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "CURL";
$extension->description = "Extension permettant de communiquer avec des serveurs distants, gr�ce � de nombreux protocoles";
$extension->reasons[] = "Connexion au site web du Conseil National l'Ordre des M�decins";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "GD";
$extension->description = "Extension de manipulation d'image. \nGD version 2 est recommand�e car elle permet un meilleur rendu, gr�ce � de nombreux protocoles";
$extension->reasons[] = "Module de statistiques graphiques";
$extension->reasons[] = "Fonction d'audiogrammes";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "PDO";
$extension->description = "Extension de connectivit� aux bases de donn�es";
$extension->reasons[] = "Interop�rabilit� avec des syst�mes tiers";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "PDO_ODBC";
$extension->description = "Pilote ODBC pour PDO";
$extension->reasons[] = "Interop�rabilit� avec des syst�mes tiers";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "APC";
$extension->description = "Extension d'optimsation d'OPCODE et de m�moire partag�e";
$extension->reasons[] = "Acc�l�ration globale du syst�me";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "GnuPG";
$extension->description = "GNU Privacy Guard (GPG ou GnuPG)";
$extension->reasons[] = "Transmettre des messages sign�s et/ou chiffr�s";
$extensions[] = $extension;

$versions = array();

// Do not use $version which is a Mediboard global
$php = new CPHPVersion;
$php->name = "5.1";
$php->mandatory = true;
$php->description = "Version de PHP5 r�cente";
$php->reasons[] = "Int�gration du support XML natif : utilisation pour l'int�rop�rabilit� HPRIM XML'";
$php->reasons[] = "Int�gration de PDO : acc�s universel et s�curis� aux base de donn�es";
$php->reasons[] = "Conception objet plus �volu�e";
$versions[] = $php;

// Start of checking
$i = 0;
$validCheck = array();

foreach ($versions as $prereq) {
  $a = 0;
  
  if ($prereq->mandatory) {
    $a = 1;
  }
  
  if ($a) {
    $validCheck[$i] = $prereq->check();
    $i++;
  }
}

foreach ($extensions as $prereq) {
  $a = 0;
  
  if ($prereq->mandatory) {
    $a = 1;
  }
  
  if ($a) {
    $validCheck[$i] = $prereq->check();
    $i++;
  }
}

foreach ($packages as $prereq) {
  $a = 0;
  
  if ($prereq->mandatory) {
    $a = 1;
  }
  
  if ($a) {
    $validCheck[$i] = $prereq->check();
    $i++;
  }
}

$valid['check'] = (array_sum($validCheck) == count($validCheck));

unset($validCheck);
unset($packages);
unset($extensions);
unset($versions);
// End of checking

// Check the fileaccess.php page
if (!(class_exists("CPathAccess"))) {
  class CPathAccess {
    var $path = "";
    var $description = "";
    var $reason = array();
  
    function check() {
      global $mbpath;
      return is_writable($mbpath.$this->path);
    }
  }  
}

$pathAccesses = array();

$pathAccess = new CPathAccess;
$pathAccess->path = "tmp/";
$pathAccess->description = "R�pertoire des fichiers temporaires";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "files/";
$pathAccess->description = "R�pertoire de tous les fichiers attach�s";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "lib/";
$pathAccess->description = "R�pertoire d'installation des biblioth�ques tierces";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "includes/";
$pathAccess->description = "R�pertoire du fichier de configuration du syst�me";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "modules/hprimxml/xsd";
$pathAccess->description = "R�pertoire des schemas HPRIM";

$pathAccesses[] = $pathAccess;

// Start of checking
$i = 0;
$validFileAccess = array();

foreach($pathAccesses as $pathAccess) {
  $validFileAccess[$i] = $pathAccess->check();
  $i++;
}

$valid['fileaccess'] = (array_sum($validFileAccess) == count($validFileAccess));

unset($validFileAccess);
unset($pathAccesses);
// End of checking

// Check the install.php page

$mbpath = "../";
if (!file_exists($mbpath."classes/CMbPath.class.php")){
  $mbpath = "./";
}

require_once($mbpath."classes/CMbPath.class.php");
require_once("libs.php");

// Start of checking
$i=0;
$validInstall = array();
foreach(CLibrary::$all as $library) {
  $validInstall[$i] = $library->getUpdateState();
  $i++;
}

$valid['install'] = (array_sum($validInstall) == count($validInstall));

unset($validInstall);
// End of checking

$valid['configure'] = null;
$valid['initialize'] = null;
$valid['feed'] = null;
$valid['finish'] = null;
$valid['phpinfo'] = null;
$valid['errorlog'] = null;
$valid['update'] = null;

showToc($valid);

?>
