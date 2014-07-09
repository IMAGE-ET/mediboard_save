<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

/**
 * Description
 */
class CComplementCCAM {
  /**
   * @var integer Primary key
   */
  public $complement_ccam_id;

  /**
   * @var array
   */
  public $_ordered_acts;

  /**
   * @var CCodable
   */
  public $_ref_object;

  /**
   * @var CPatient
   */
  public $_ref_patient;

  /**
   * @var CMediusers
   */
  public $_ref_praticien;

  /**
   * @var CActeCCAM[]
   */
  public $_ref_actes_ccam;

  protected static $association_rules = array('G1', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG1', 'EG2', 'EG3', 'EG4', 'EG5', 'EG6', 'EG7', 'EH', 'EI', 'GA', 'GB', 'G2');

  /**
   * Check the modifiers of the given act
   *
   * @param CObject  $modifiers The modifiers to check
   * @param string   $execution The dateTime of the execution of the act
   * @param CCodable $codable   The codable
   *
   * @return void
   */
  public static function checkModifiers($modifiers, $execution, $codable) {
    $date = CMbDT::date(null, $execution);
    $time = CMbDT::time(null, $execution);
    $discipline = $codable->_ref_praticien->_ref_discipline;
    $patient = $codable->_ref_patient;

    foreach ($modifiers as $_modifier) {
      switch ($_modifier->code) {
        case 'A':
          $_modifier->_checked = ($patient->_annees < 4 || $patient->_annees > 80);
          break;
        case 'E':
          $_modifier->_checked = $patient->_annees < 5;
          break;
        case 'F':
          $_modifier->_checked = (CMbDT::transform('', $execution, '%w') == 0 || CMbDate::isHoliday(CMbDT::date(null, $execution)));
          break;
        case 'N':
          $_modifier->_checked = $patient->_annees < 13;
          break;
        case 'P':
          // gerer specialite cpam?
          $_modifier->_checked = (in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
            ($time >= "20:00:00" && $time < "00:00:00"));
          break;
        case 'S':
          // G�rer : 'ou autres med. pr acte th�rapeutique sous anesth�sie
          $_modifier->_checked = (in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) ||
              ($codable->_class == "COperation" && $codable->_lu_type_anesth)) &&
            ($time >= "00:00:00" && $time <= "08:00:00");
          break;
        case 'U':
          $_modifier->_checked = !in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
            ($time >= '20:00:00' || $time <= '08:00:00');
          break;
        case "7":
          $_modifier->_checked = CAppUI::conf("dPccam CCodable precode_modificateur_7");
          break;
        default:
          $_modifier->_checked = 0;
          break;
      }
    }
  }

  /**
   * Order the acts by price
   *
   * @param CActeCCAM[] $acts The CCAM acts
   *
   * @return array
   */
  protected static function getActsByTarif($acts) {
    $ordered_acts = array();

    foreach ($acts as $_act) {
      $ordered_acts[$_act->_id] = $_act->getTarifSansAssociationNiCharge();
    }

    return self::orderActsByTarif($ordered_acts);
  }

  /**
   * Reorder the acts by price
   *
   * @param array $disordered_acts The acts to reorder
   *
   * @return array
   */
  protected static function orderActsByTarif($disordered_acts) {
    ksort($disordered_acts);
    arsort($disordered_acts);

    return $disordered_acts;
  }

  /**
   * Load the linked acts of the given act
   *
   * @param CActeCCAM $act The CCAM act
   *
   * @return CActeCCAM[]
   */
  protected static function loadActesCCAM($act) {
    $acts = $act->getLinkedActes();
    $acts[$act->_id] = $act;
    foreach ($acts as $_act_ccam) {
      $_act_ccam->loadRefCodeCCAM();
    }
    return $acts;
  }

  /**
   * Guess the association code for an act
   *
   * @param CActeCCAM $act     The act
   * @param CCodable  $codable The codable
   *
   * @return string
   */
  public static function guessAssociation($act, $codable) {
    $acts = self::loadActesCCAM($act);
    $ordered_acts = self::getActsByTarif($acts);
    $act->_position = array_search($act->_id, array_keys($ordered_acts));

    foreach (self::$association_rules as $_rule) {
      if (self::isRuleAllowed($_rule)) {
        $function = "associationRule$_rule";
        if (call_user_func(array('CComplementCCAM', $function), $act, $acts, $ordered_acts)) {
          break;
        }
      }
    }

    return $act->_guess_association;
  }

  /**
   * Check if the rule is allowed to be used
   *
   * @param string $rule The name of the rule
   *
   * @return boolean
   */
  protected static function isRuleAllowed($rule) {
    $feature = "dPccam associations rules $rule";
    if (strpos($rule, 'G') === 0) {
      $feature = "dPccam associations rules G";
    }

    return CAppUI::conf($feature, CGroups::loadCurrent()->_guid);
  }

  /** Association rules **/

  /**
   * Check the association rule G1
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleG1($act, $acts, $ordered_acts) {
    if (count($acts) != 1) {
      return false;
    }

    $act->_guess_association = '';
    $act->_guess_regle_asso = 'G1';
    return true;
  }

  /**
   * ### R�gle d'association g�n�rale A ###
   *
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Dans le cas d'une association de __2 actes seulement__, dont l'un est un soit un geste
   * compl�mentaire, soit un suppl�ment, soit un acte d'imagerie pour acte de radiologie interventionnelle ou cardiologie
   * interventionnelle (Paragraphe 19.01.09.02), il ne faut pas indiquer de code d'association
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleGA($act, $acts, $ordered_acts) {
    if (count($acts) != 2) {
      return false;
    }

    $cond = 0;
    foreach ($acts as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;

      if (
          ($chapters[0]['db'] == '000019' && (($chapters[1]['db'] == '000001' && $chapters[2]['db'] == '000009' &&
          $chapters[2]['db'] == '000002') || $chapters[1]['db'] == '000002')) || $chapters[0]['db'] == '000018' &&
          $chapters[1]['db'] == '000002'
      ) {
        $cond++;
      }
    }

    if ($cond != 1) {
      return false;
    }

    $act->_guess_association = '';
    $act->_guess_regle_asso = 'GA';
    return true;
  }

  /**
   * ### R�gle d'association g�n�rale B ###
   * * Nombre d'actes : 3
   * * Cas d'utilisation : Si un acte est associ� � un geste compl�mentaire et � un suppl�ment, le code d'assciation est 1 pour
   * chacun des actes.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleGB($act, $acts, $ordered_acts) {
    if (count($acts) != 3) {
      return false;
    }

    $supp = 0;
    $comp = 0;
    foreach ($acts as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if ($chapters[0]['db'] == '000018' && $chapters[1]['db'] == '000002') {
        $comp++;
      }
      if ($chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000002') {
        $supp++;
      }
    }

    if ($supp != 1 || $comp != 1) {
      return false;
    }

    $act->_guess_association = '1';
    $act->_guess_regle_asso = 'GB';
  }

  /**
   * Check the association rule G2
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleG2($act, $acts, $ordered_acts) {
    foreach ($acts as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          ($chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000002') ||
          ($chapters[0]['db'] == '000018' && $chapters[1]['db'] == '000002')
      ) {
        unset($ordered_acts[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }

    if ($act->_position != -1) {
      self::orderActsByTarif($ordered_acts);
      $act->_position = array_search($act->_id, array_keys($ordered_acts));
    }

    // gerer le cas ou le supplements est plus cher que les actes principaux
    switch ($act->_position) {
      case -1:
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'G2';
        break;
      case 0:
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'G2';
        break;
      case 1:
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'G2';
        break;
      default:
        $act->facturable = '0';
    }

    return true;
  }

  /**
   * ### Exception sur les actes de chirugie (membres diff�rents) ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Pour les __actes de chirurgie portant sur des membres diff�rents__ (sur le tronc et un membre,
   * sur la t�te et un membre), l'acte dont le tarif (hors modificateurs) est le moins �lev� est tarif� � 75% de sa valeur
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEA($act, $acts, $ordered_acts) {
    if (count($acts) != 2 /*&& $codable instanceof COperation*/) {
      return false;
    }

    $chap11 = 0;
    $chap12 = 0;
    $chap13 = 0;
    $chap14 = 0;
    foreach ($acts as $_act) {
      switch ($_act->_ref_code_ccam->chapitres[0]['db']) {
        case '000011':
          $chap11++;
          break;
        case '000012':
          $chap12++;
          break;
        case '000013':
          $chap13++;
          break;
        case '000014':
          $chap14++;
          break;
        default:
      }
    }

    if ($chap13 != 2 && $chap14 != 2 && ((!$chap11 && !$chap12) || (!$chap13 && !$chap14))) {
      return false;
    }

    // choix de l'utilisateur
    switch ($act->_position) {
      case 0:
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EA';
        break;
      default:
        $act->_guess_association = '3';
        $act->_guess_regle_asso = 'EA';
    }
    return true;
  }

  /**
   * ### Exception sur les actes de chirugie (l�sions traumatiques multiples et r�centes) ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Pour les __actes de chirurgie pour l�sions traumatiques et r�centes__, l'association de
   * trois actes au plus, y comprit les gestes compl�mentaires, peut �tre tarif�e.
   * L'acte dont le tarif (hors modificateurs) est le plus �lev� est tarif� � taux plein. Le deuxi�me est tarif� �
   * 75% de sa valeur, et le troisi�me � 50%.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEB($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3))) {
      return false;
    }

    // choix de l'utilisateur
    switch ($act->_position) {
      case 0:
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EB';
        break;
      case 1:
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'EB';
        break;
      default:
        $act->_guess_association = '3';
        $act->_guess_regle_asso = 'EB';
    }
    return true;
  }

  /**
   * ### Actes de chirugie carcinologique en ORL associant une ex�r�se, un curage et une reconstruction ###
   * * Nombre d'actes : 3
   * * Cas d'utilisation : Pour les __actes de chirugie carcinologique en ORL associant une ex�r�se, un curage et une reconstruction__,
   * l'acte dont le tarif (hots modificateurs) est le plus �lev� est tarif� � taux plein, le deuxi�me et le troisi�me sont tarif�s
   * � 50% de leurs valeurs.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEC($act, $acts, $ordered_acts) {
    $discipline = $act->loadRefExecutant()->loadRefDiscipline();
    if (count($acts) != 3 || $discipline->_compat != 'ORL') {
      return false;
    }

    $exerese = false;
    $curage = false;
    $reconst = false;
    foreach ($acts as $_acte_ccam) {
      $libelle = $_acte_ccam->_ref_code_ccam->libelleLong;
      if (stripos($libelle, 'ex�r�se') !== false) {
        $exerese = true;
      }
      elseif (stripos($libelle, 'curage') !== false) {
        $curage = true;
      }
      elseif (stripos($libelle, 'reconstruction') !== false) {
        $reconst = true;
      }
    }

    if (!$exerese || !$curage || !$reconst) {
      return false;
    }

    switch ($act->_position) {
      case 0:
        $act->_guess_association = '1';
        $act->_guess_regle_asso = 'EC';
        break;
      default:
        $act->_guess_association = '2';
        $act->_guess_regle_asso = 'EC';
    }
    return true;
  }

  /**
   * Actes d'�chographie portant sur plusieurs r�gions anatomiques
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleED($act, $acts, $ordered_acts) {
    return false;
  }

  /**
   * ### Actes de scanographie ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Pour les __actes de scanographie, lorsque l'examen porte sur plusieurs r�gions anatomiques__,
   * un seul acte doit �tre tarif�, sauf dans le cas ou l'examen effectu� est conjoint des r�gions anatomiques suivantes :
   * membres et t�te, membres et thorax, membres et abdomen, t�te et abdomen, thorax et abdomen complet, t�te et thorax,
   * quel que soit le nombres de coupes n�c�ssaires, avec ou sans injection de produit de contraste.
   *
   * Dans ce cas, deux actes ou plus peuvent �tre tarif�s � taux plein. Deux forfaits techniques peuvent alors �tre factur�s,
   * le second avec une minaration de 85% de son tarfi.
   *
   * Quand un libell� d�crit l'examen conjoint de plusieurs r�gions anatomiques, il ne peut �tre tarif� avec aucun autre acte
   * de scanographie. Deux forfaits techniques peuvent alors �tre tarif�s, le second avec une minoration de 85% de son tarfi.
   *
   * L'acte de guidage scanographique ne peut �tre tarfi� qu'avec les actes dont le libell� pr�cise qu'ils n�cessitent un
   * guidage scanoraphique. Dans ce cas, deux acte au plus peuvent �tre tarif�s � taux plein.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEE($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3))) {
      return false;
    }

    // choix de l'utilisateur
    // g�rer la minoration de 85% du tarif du second forfait technique
    switch ($act->_position) {
      case 0:
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EE';
        break;
      case 1:
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EE';
        break;
      default:
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EE';
    }
    return true;
  }

  /**
   * Association rule EF
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEF($act, $acts, $ordered_acts) {
    return false;
  }

  /**
   * ### Eception actes de radiologie vasculaire et imagerie conventionnelle ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Les __actes du sous paragraphe 19.01.09.02__ (radiologie vasculaire et imagerie conventionnelle)
   * sont associ�s � taux plein, deux actes au plus peuvent tarif�s.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG1($act, $acts, $ordered_acts) {
    if (count($acts) != 2) {
      return false;
    }

    $cond = 0;
    foreach ($acts as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          $chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000001' &&
          $chapters[2]['db'] == '000009' && $chapters[3]['db'] == '000002'
      ) {
        $cond++;
      }
    }

    if ($cond != 2) {
      return false;
    }

    $act->_guess_association = '1';
    $act->_guess_regle_asso = 'EG1';

    return true;
  }

  /**
   * ### Exception : actes d'anatomie et de cytologie pathologique ###
   * * Nombre d'actes : 2 ou3
   * * Cas d'utilisation : Les __actes d'anatomie et de cytologie pathologique__ peuvent �tre associ�s �
   * taux plein entre eux et/ou � un autre acte, quelque soit le nombre d'acte d'anatomie et de cytologie pathologique.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG2($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3))) {
      return false;
    }

    $ordered_acts_eg2 = $ordered_acts;
    $nb_anapath = 0;
    foreach ($acts as $_act) {
      $chap = $_act->_ref_code_ccam->chapitres;
      if (
          ($chap[0]['db'] == '000001' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000011', '000014'))) ||
          ($chap[0]['db'] == '000002' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000008', '000009', '000010'))) ||
          ($chap[0]['db'] == '000003' && $chap[1]['db'] == '000001' && $chap[2]['db'] == '000003') ||
          ($chap[0]['db'] == '000004' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000009', '000010'))) ||
          ($chap[0]['db'] == '000005' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000006', '000008'))) ||
          ($chap[0]['db'] == '000006' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000009', '000011'))) ||
          ($chap[0]['db'] == '000007' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000011', '000013'))) ||
          ($chap[0]['db'] == '000008' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000007', '000009'))) ||
          ($chap[0]['db'] == '000001' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000011', '000014'))) ||
          ($chap[0]['db'] == '000009' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000006', '000007'))) ||
          ($chap[0]['db'] == '000010' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000004', '000005'))) ||
          ($chap[0]['db'] == '000011' && $chap[1]['db'] == '000001' && $chap[2]['db'] == '000006') ||
          ($chap[0]['db'] == '000012' && $chap[1]['db'] == '000001' && $chap[2]['db'] == '000006') ||
          ($chap[0]['db'] == '000013' && $chap[1]['db'] == '000001' && $chap[2]['db'] == '000005') ||
          ($chap[0]['db'] == '000014' && $chap[1]['db'] == '000001' && $chap[2]['db'] == '000006') ||
          ($chap[0]['db'] == '000015' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000006', '000007'))) ||
          ($chap[0]['db'] == '000016' && in_array($chap[1]['db'], array('000001', '000002')) && in_array($chap[2]['db'], array('000005', '000006'))) ||
          ($chap[0]['db'] == '000017' && $chap[1]['db'] == '000001' && in_array($chap[2]['db'], array('000005', '000006')))
      ) {
        $nb_anapath++;
        unset($ordered_acts_eg2[$_act->_id]);
        if ($_act->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }
    if ($act->_position != -1) {
      self::orderActsByTarif($ordered_acts_eg2);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_eg2));
    }

    if (!$nb_anapath) {
      return false;
    }

    if ($nb_anapath == 2 || ($nb_anapath == 1 && count($ordered_acts) == 1)) {
      $act->_guess_association = '4';
      $act->_guess_regle_asso = 'EG2';
    }
    else {
      switch ($act->_position) {
        case 1:
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EG2';
          break;
        default:
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG2';
      }
    }
    return true;
  }

  /**
   * ### Exception : actes d'�lectromyographie, de mesure de vitesse de conduction, d'�tudes des lances et des r�flexes ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Les __actes d'�lectromyographie, de mesure de vitesse de conduction, d'�tudes des lances et des r�flexes__
   * (figurants aux paragraphes 01.01.01.01, 01.01.01.02, 01.01.01.03 de la CCAM) peuvent �tre associ�s � taux plein entre eux ou �
   * un autre acte, quelque soit le nombre d'actes
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG3($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3))) {
      return false;
    }

    $ordered_acts_eg3 = $ordered_acts;
    $nb_electromyo = 0;
    foreach ($acts as $_acte_ccam) {
      $chapters = $_acte_ccam->_ref_code_ccam->chapitres;
      if (
          $chapters[0]['db'] == '000001' && $chapters[1]['db'] == '000001' && $chapters[2]['db'] == '000001' &&
          ($chapters[3]['db'] == '000001' || $chapters[3]['db'] == '000002' || $chapters[3]['db'] == '000003' )
      ) {
        $nb_electromyo++;
        unset($ordered_acts_eg3[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
      elseif ($chapters[0]['db'] == '000019' && $chapters[1]['db'] == '000002') {
        unset($ordered_acts_eg3[$_acte_ccam->_id]);
        if ($_acte_ccam->_id == $act->_id) {
          $act->_position = -1;
        }
      }
    }
    if ($act->_position != -1) {
      self::orderActsByTarif($ordered_acts_eg3);
      $act->_position = array_search($act->_id, array_keys($ordered_acts_eg3));
    }

    if (!$nb_electromyo) {
      return false;
    }

    if ($nb_electromyo == 2) {
      $act->_guess_association = '4';
      $act->_guess_regle_asso = 'EG3';
    }
    else {
      switch ($act->_position) {
        case 1:
          $act->_guess_association = '2';
          $act->_guess_regle_asso = 'EG3';
          break;
        default:
          $act->_guess_association = '1';
          $act->_guess_regle_asso = 'EG3';
      }
    }
  }

  /**
   * ### Exception : actes d'irradiation en radioth�rapie ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Les __actes d'irradiation en radioth�rapie__, ainsi que les suppl�ments autoris�s avec ces actes,
   * peuvent �tre associ�s � taux plein, quel que soit le nombre d'actes.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG4($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3))) {
      return false;
    }

    /* @todo Calculer les actes d'irradiation en radioth�rapie */
    $cond = 0;

    if (!$cond) {
      return false;
    }

    $act->_guess_association = '4';
    $act->_guess_regle_asso = 'EG4';

    return true;
  }

  /**
   * ### Exception : actes de m�decin nucl�aire ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Les __actes de m�decin nucl�aire__ sont associ�s � taux plein, deux actes au plus peuvent
   * �tre tarfi�s. Il en est de m�me pour un acte de m�decine nucl�aire associ� � un autre acte.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG5($act, $acts, $ordered_acts) {
    if (count($acts) != 2) {
      return false;
    }

    /* @todo Identifier les actes de m�decin nuc�laire */
    $cond = 0;

    if (!$cond) {
      return false;
    }

    $act->_guess_association = '4';
    $act->_guess_regle_asso = 'EG5';
    return true;
  }

  /**
   * ### Exception : forfait de cardilogie, de r�animation, actes de surveillance post-op�ratoire, actes d'acocuchements ###
   * * Nombre d'actes : 2
   * * Cas d'utilisation : Les __forfait de cardilogie, de r�animation, actes de surveillance post-op�ratoire (d'un patient de
   * chirurgie cardiaque avec CEC), actes d'acocuchements__ peuvent �tre associ�s � taux plein � un seul des actes introduits
   * par la note "facturation : �ventuellement en suppl�ment".
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG6($act, $acts, $ordered_acts) {
    if (count($acts) != 2) {
      return false;
    }

    $cond = 0;
    /* @todo d�tecter les forfaits de cardiologie, de r�animation, de surveillance post-op d'un patient en chirurgie cardiaque avec CEC et les actes d'accouchements */
    if (!$cond) {
      return false;
    }

    $act->_guess_association = 4;
    $act->_guess_regle_asso = 'EG6';

    return true;
  }

  /**
   * ### Exception : actes bucco-dentaires ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Les __actes bucco-dentaires__, y comprit les suppl�ments autoris�s avec ces actes, peuvent
   * �tre associ�s � taux plein ente eux ou � eux-m�me ou � un autre acte, quel que soit le nombre d'actes bucco-dentaires.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEG7($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3))) {
      return false;
    }

    $cond = 1;
    /* d�tecter les actes bucco-dentaires, et les supprimer du ordered_actes */
    if (!$cond) {
      return false;
    }


    if ($cond == 2) {
      $act->_guess_association = '4';
      $act->_guess_regle_asso = 'EG7';
    }
    else {
      if (count($acts) == 1) {
        $act->_guess_association = '4';
        $act->_guess_regle_asso = 'EG7';
      }
      else {
        switch ($act->_position) {
          case 2:
            $act->_guess_association = '2';
            $act->_guess_regle_asso = 'EG7';
            break;
          default:
            $act->_guess_association = '1';
            $act->_guess_regle_asso = 'EG7';
        }
      }
    }

    return true;
  }

  /**
   * ### Exception : actes discontinus ###
   * * Nombre d'actes : 2 ou 3
   * * Cas d'utilisation : Actes effectu�s dans un temps diff�rent et discontinu de la m�me journ�e.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEH($act, $acts, $ordered_acts) {
    return false;
  }

  /**
   * ### Exception : actes de radiologie conventionnelle ###
   * * Nombre d'actes : 2, 3, ou 4
   * * Cas d'utilisation : Les __actes de radiologie conventionnelle__ peuvent �tre associ�s entre eux (quel que soit
   * leur nombre), ou � d'autres actes.
   *
   * @param CActeCCAM   $act          The act
   * @param CActeCCAM[] $acts         The list of the acts
   * @param array       $ordered_acts The acts, ordered by price
   *
   * @return bool
   */
  protected static function associationRuleEI($act, $acts, $ordered_acts) {
    if (!in_array(count($acts), array(2, 3, 4, 5))) {
      return false;
    }
    return false;
  }
}