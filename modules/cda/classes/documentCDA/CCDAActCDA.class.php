<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe regroupant les fonctions de type Act
 */
class CCDAActCDA extends CCDADocumentCDA {

  /**
   * Cr�ation d'un clinicalDocument
   *
   * @return CCDAPOCD_MT000040_ClinicalDocument
   */
  function setClinicalDocument() {
    $factory         = self::$cda_factory;
    $participation   = parent::$participation;
    $actRelationship = parent::$actRelationship;

    //d�claration du document
    $clinicaldocument = new CCDAPOCD_MT000040_ClinicalDocument();

    /**
     * Cr�ation de l'ent�te
     */

    //Cr�ation de l'Id du document
    $ii = new CCDAII();
    $ii->setRoot($factory->id_cda);
    $clinicaldocument->setId($ii);

    //cr�ation du typeId
    $clinicaldocument->setTypeId();

    //Ajout du realmCode FR
    $cs = new CCDACS();
    $cs->setCode($factory->realm_code);
    $clinicaldocument->appendRealmCode($cs);

    //Ajout du code langage fr-FR
    //@todo voir langue
    $cs = new CCDACS();
    $cs->setCode($factory->langage);
    $clinicaldocument->setLanguageCode($cs);

    //Ajout de la confidentialit� du document
    $confidentialite = $factory->confidentialite;
    $ce = new CCDACE();
    $ce->setCode($confidentialite["code"]);
    $ce->setCodeSystem($confidentialite["codeSystem"]);
    $ce->setDisplayName($confidentialite["displayName"]);

    $clinicaldocument->setConfidentialityCode($ce);

    //Ajout de la date de cr�ation du document
    $ts = new CCDATS();
    $ts->setValue($this->getTimeToUtc($factory->date_creation));
    $clinicaldocument->setEffectiveTime($ts);

    //Ajout du num�ro de version
    $int = new CCDAINT();
    $int->setValue($factory->version);
    $clinicaldocument->setVersionNumber($int);

    //Ajout de l'identifiant du lot
    $ii = new CCDAII();
    $ii->setRoot($factory->id_cda_lot);
    $clinicaldocument->setSetId($ii);

    //Ajout du nom du document
    $st = new CCDAST();
    $st->setData($factory->nom);
    $clinicaldocument->setTitle($st);

    //Ajout du code du document (Jeux de valeurs)
    $ce = new CCDACE();
    $code = $factory->code;
    $ce->setCode($code["code"]);
    $ce->setCodeSystem($code["codeSystem"]);
    $ce->setDisplayName($code["displayName"]);
    $clinicaldocument->setCode($ce);

    /**
     * D�claration Template
     */
    //conformit� HL7
    foreach ($factory->templateId as $_templateId) {
      $clinicaldocument->appendTemplateId($_templateId);
    }

    /**
     * Cr�ation des �l�ments obligatoire constituant le document
     */
    $clinicaldocument->appendRecordTarget($participation->setRecordTarget());
    $clinicaldocument->setCustodian($participation->setCustodian());
    $clinicaldocument->appendAuthor($participation->setAuthor());
    $clinicaldocument->setLegalAuthenticator($participation->setLegalAuthenticator());
    $this->setDocumentationOF($clinicaldocument);
    $clinicaldocument->setComponentOf($actRelationship->setComponentOf());

    /**
     * Cr�ation du corp du document
     */
    $clinicaldocument->setComponent($actRelationship->setComponent2());
    return $clinicaldocument;
  }

  /**
   * Cr�ation d'un corps non structur�
   *
   * @return CCDAPOCD_MT000040_NonXMLBody
   */
  function setNonXMLBody() {
    $file      = self::$cda_factory->file;
    $mediaType = self::$cda_factory->mediaType;
    $nonXMLBody = new CCDAPOCD_MT000040_NonXMLBody();

    $ed = new CCDAED();
    $ed->setMediaType($mediaType);
    $ed->setRepresentation("B64");
    $ed->setData(base64_encode(file_get_contents($file->_file_path)));

    $nonXMLBody->setText($ed);
    return $nonXMLBody;
  }

  /**
   * Cr�ation encompassingEncounter
   *
   * @return CCDAPOCD_MT000040_EncompassingEncounter
   */
  function setEncompassingEncounter() {
    $encompassingEncounter = new CCDAPOCD_MT000040_EncompassingEncounter();
    /** @var CSejour|COperation|CConsultation $object CSejour*/
    $object = self::$cda_factory->targetObject;
    $ivl = "";
    switch (get_class($object)) {
      case "CSejour":
        $low = $object->entree_reelle;
        if (!$low) {
          $low = $object->entree_prevue;
        }

        $high = $object->sortie_reelle;
        if (!$high) {
          $high = $object->sortie_prevue;
        }

        $ivl = $this->createIvlTs($low, $high);

        break;
      case "COperation":
        $ivl = $this->createIvlTs($object->debut_op, $object->fin_op);
        $encompassingEncounter->setEffectiveTime($ivl);

        break;
      case "CConsultation":
        $object->loadRefPlageConsult();
        $ivl = $this->createIvlTs($object->_datetime, $object->_date_fin, true);
        break;
    }
    $encompassingEncounter->setEffectiveTime($ivl);

    $encompassingEncounter->setLocation(parent::$participation->setLocation());

    return $encompassingEncounter;
  }
}