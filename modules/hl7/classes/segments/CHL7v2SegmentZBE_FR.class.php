<?php

/**
 * Represents an HL7 ZBE message segment (Movement) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZBE_FR
 * ZBE - Represents an HL7 ZBE message segment (Movement)
 */

class CHL7v2SegmentZBE_FR extends CHL7v2SegmentZBE {
  /**
   * Fill other identifiers
   *
   * @param array       &$data Datas
   * @param array       $ufs   UF
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function fillOtherSegments(&$data, $ufs = array(), CHL7v2Event $event) {    
    // ZBE-8: Ward of care responsibility in the period starting with this movement (XON) (optional)
    $uf_type = $event->_receiver->_configs["build_ZBE_8"];
    /** @var CUniteFonctionnelle $uf_soins */
    $uf_soins = isset($ufs[$uf_type]) ? $ufs[$uf_type] : null;
    if (isset($uf_soins->_id)) {
      $data[] = array(
        array(
          // ZBE-8.1 : Libell� de l'UF
          $uf_soins->libelle,
          null,
          null,
          null,
          null,
          // ZBE-8.6 : Identifiant de l'autorit� d'affectation  qui a attribu� l'identifiant de l'UF de responsabilit� m�dicale
          $this->getAssigningAuthority("mediboard"),
          // ZBE-8.7 : La seule valeur utilisable de la table 203 est "UF"
          "UF",
          null,
          null,
          // ZBE-8.10 : Identifiant de l'UF de responsabilit� m�dicale
          $uf_soins->code
        )
      );
    }
    else {
      $data[] = null;
    }
    
    // ZBE-9: Nature of this movement (CWE)
    // S - Changement de responsabilit� de soins uniquement
    // H - Changement de �responsabilit� �d'h�bergement �soins uniquement
    // M - Changement de responsabilit� m�dicale uniquement
    // L - Changement de lit uniquement
    // D - Changement de prise en charge m�dico-administrative laissant les responsabilit�s et la localisation du patient inchang�es 
    //     (ex : changement de tarif du s�jour en unit� de soins)
    // SM - Changement de responsabilit� soins + m�dicale
    // SH - Changement de responsabilit� soins + h�bergement
    // MH - Changement de responsabilit� h�bergement + m�dicale
    // LD - Changement de prise en charge m�dico-administrative et de lit, laissant les responsabilit�s inchang�es
    // HMS - Changement conjoint des trois responsabilit�s.
    /* @todo Voir comment g�rer ceci... */
   
    // Changement d'UF m�dicale
    if (CMbArray::in($event->code, "Z80 Z81 Z82 Z83")) {
      $data[] = "M";
    }
    // Changement d'UF de soins
    elseif (CMbArray::in($event->code, "Z84 Z85 Z86 Z87")) {
      $data[] = "S";
    }
    else {
      $data[] = "HMS";
    }
  }
}