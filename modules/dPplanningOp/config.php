<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$dPconfig["dPplanningOp"]= array(
  "COperation" => array (
    "easy_materiel"      => "0",
    "easy_remarques"     => "0",
    "easy_regime"        => "1",
    "easy_accident"      => "0",
    "easy_assurances"    => "0",
    "easy_type_anesth"   => "0",
    "easy_length_input_label" => "50",
    "duree_deb"          => "0",
    "duree_fin"          => "10",
    "hour_urgence_deb"   => "0",
    "hour_urgence_fin"   => "23",
    "min_intervalle"     => "15",
    "locked"             => "0",
    "horaire_voulu"      => "0",
    "use_ccam"           => "1",
    "verif_cote"         => "0",
    "delete_only_admin"  => "1",
    "duree_preop_adulte" => "45",
    "duree_preop_enfant" => "15",
    "show_duree_preop"   => "0",
    "show_duree_uscpo"   => "0",
    "save_rank_annulee_validee" => "0",
    "cancel_only_for_resp_bloc" => "0",
    "fiche_examen"       => "0",
    "fiche_materiel"     => "0",
    "fiche_rques"        => "0",
    "nb_jours_urgence"   => "1",
    "use_poste"          => "0",
    "show_secondary_function" => "0",
    "show_presence_op"   => "1",
    "show_remarques"     => "1",
    "show_montant_dp"    => "1",
    "show_asa_position"  => "1",
    "show_print_dhe_info"  => "1",
    "default_week_stat_uscpo" => "last",
    "use_session_praticien" => true
   ),
  "CSejour" => array (
    "check_collisions"    => "date",
    "easy_cim10"          => "0",
    "easy_service"        => "0",
    "easy_chambre_simple" => "1",
    "easy_ald_cmu"        => "0",
    "easy_atnc"           => "0",
    "patient_id"          => "1",
    "entree_modifiee"     => "1",
    "heure_deb"           => "0",
    "heure_fin"           => "23",
    "min_intervalle"      => "15",
    "heure_entree_veille" => "17",
    "heure_entree_jour"   => "10",
    "heure_sortie_ambu"   => "18",
    "heure_sortie_autre"  => "8",
    "locked"              => "0",
    "tag_dossier"         => "",
    "tag_dossier_group_idex"=> "",
    "tag_dossier_pa"      => "pa_",
    "tag_dossier_cancel"  => "cancel_",
    "tag_dossier_trash"   => "trash_",
    "tag_dossier_rang"    => "",
    "use_dossier_rang"    => "0",
    "blocage_occupation"  => "0",
    "service_id_notNull"  => "0",
    "consult_accomp"      => "0",
    "accident"            => "0",
    "assurances"          => "0",
    "fix_doc_edit"        => "0",
    "sortie_prevue"       => array(
      "comp"    => "24",
      "ambu"    => "04",
      "exte"    => "04",
      "seances" => "04",
      "ssr"     => "24",
      "psy"     => "24",
      "urg"     => "24",
      "consult" => "04",
    ),
    "delete_only_admin"   => "1",
    "max_cancel_time"     => "0",
    "hours_sejour_proche" => "48",
    "show_modal_identifiant"    => "0",
    "show_discipline_tarifaire" => "0",
    "show_type_pec" => "0",
    "create_anonymous_pat" => "0",
    "anonymous_sexe" => "m",
    "anonymous_naissance" => "1970-01-01",
    "use_recuse" => "0",
    "fiche_rques_sej"    => "0",
    "fiche_conval"       => "0",
    "systeme_isolement"  => "standard",
    "easy_isolement"     => "0",
    "show_cmu_ald"       => "1",
    "show_days_duree"    => "0",
    "show_isolement"     => "1",
    "show_chambre_part"  => "1",
    "show_facturable"    => "1",
    "show_atnc"          => "0",

    "show_only_charge_price_indicator" => "0",
    "use_custom_mode_entree" => "0",
    "use_custom_mode_sortie" => "0",
    "specified_output_mode"  => "1",
  ),
  "CProtocole" => array(
    "nicer" => "0",
  ),
  "CFactureEtablissement" => array (
    "use_facture_etab"    =>  "0",
    "show_type_facture"   =>  "1",
    "show_statut_pro"     =>  "1",
    "show_assur_accident" =>  "1",
    "show_dialyse"        =>  "0",
    "show_cession"        =>  "0"
  ),
  "CRegleSectorisation" => array (
    "use_sectorisation"   =>  "0",
  ),
);
