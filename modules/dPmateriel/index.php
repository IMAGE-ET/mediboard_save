<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author S�bastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_stock"      , "Edition des stocks"              , TAB_READ);
$module->registerTab("vw_idx_materiel"   , "Edition des Fiches mat�riel"     , TAB_READ);
$module->registerTab("vw_idx_category"   , "G�rer les cat�gories de mat�riel", TAB_READ);
$module->registerTab("vw_idx_fournisseur", "Fournisseurs"                    , TAB_READ);
$module->registerTab("vw_idx_refmateriel", "R�f�rences fournisseurs"         , TAB_READ);

?>