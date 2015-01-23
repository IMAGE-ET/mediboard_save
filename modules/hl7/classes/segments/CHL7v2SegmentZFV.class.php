<?php

/**
 * Represents an HL7 ZFV message segment (Compl�ment d'information sur la venue) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZFV
 * ZFV - Represents an HL7 ZFV message segment (Compl�ment d'information sur la venue)
 */

class CHL7v2SegmentZFV extends CHL7v2Segment {

  /** @var string */
  public $name   = "ZFV";
  

  /** @var CSejour */
  public $sejour;

  /**
   * Build ZFV segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $sejour = $this->sejour;
    
    // ZFV-1: Etablissement de provenance (DLD)
    if ($sejour->etablissement_entree_id) {
      $etab_provenance = $sejour->loadRefEtablissementProvenance();
      $data[] = array(
        $etab_provenance->finess
      );
    }
    else {
      $data[] = null;
    }
    
    // ZFV-2: Mode de transport de sortie
    $data[] = null;
    
    // ZFV-3: Type de pr�admission
    $data[] = null;
    
    // ZFV-4: Date de d�but de placement (psy)
    $data[] = null;
    
    // ZFV-5: Date de fin de placement (psy)
    $data[] = null;
    
    // ZFV-6: Adresse de la provenance ou de la destination (XAD)
    $adresses = array();
    if ($sejour->etablissement_entree_id) {
      $adresses[] = array(
        str_replace("\n", "", $etab_provenance->adresse),
        null,
        $etab_provenance->ville,
        null,
        $etab_provenance->cp,
        null,
        "ORI"
      );
    }
    if ($sejour->etablissement_sortie_id) {
      $etab_destination = $sejour->loadRefEtablissementTransfert();
      $adresses[] = array(
        str_replace("\n", "", $etab_destination->adresse),
        null,
        $etab_destination->ville,
        null,
        $etab_destination->cp,
        null,
        "DST"
      );
    }
    $data[] = $adresses;
    
    // ZFV-7: NDA de l'�tablissement de provenance
    $data[] = null;

    // ZFV-8: Num�ros d'archives
    $data[] = null;

    // ZFV-9: Mode de sortie personnalis�e
    $data[] = CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie") ? $sejour->loadRefModeSortie()->code : null;

    $this->fill($data);
  }
}