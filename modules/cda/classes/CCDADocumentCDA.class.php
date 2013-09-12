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
 * Regroupe les fonctions pour cr�er un document CDA
 */
class CCDADocumentCDA extends CCDAClasseCda{

  /** @var CCDARoleCDA */
  static $role;
  /** @var CCDAParticipationCDA */
  static $participation;
  /** @var CCDAActRelationshipCDA */
  static $actRelationship;
  /** @var CCDAActCDA */
  static $act;
  /** @var CCDAEntiteCDA */
  static $entite;
  /** @var  CCDAFactory */
  static $cda_factory;

  /**
   * Cr�ation du CDA
   *
   * @param CCDAFactory $cda_factory cda factory
   *
   * @return CCDAPOCD_MT000040_ClinicalDocument
   */
  function generateCDA($cda_factory) {
    self::$participation   = new CCDAParticipationCDA();
    self::$entite          = new CCDAEntiteCDA();
    self::$act             = new CCDAActCDA();
    self::$actRelationship = new CCDAActRelationshipCDA();
    self::$role            = new CCDARoleCDA();
    self::$cda_factory     = $cda_factory;

    $act = new CCDAActCDA();
    $CDA = $act->setClinicalDocument();

    return $CDA;
  }

  /**
   * Transforme une chaine date au format time CDA
   *
   * @param String $date      String
   * @param bool   $naissance false
   *
   * @return string
   */
  function getTimeToUtc($date, $naissance = false) {
    $timezone = new DateTimeZone(CAppUI::conf("timezone"));
    $date     = new DateTime($date, $timezone);
    if ($naissance) {
      return $date->format("Ymd");
    }
    return $date->format("YmdHisO");
  }

  /**
   * Cr�ation de l'adresse de la personne pass� en param�tre
   *
   * @param CPerson $user CPerson
   *
   * @return CCDAAD
   */
  function setAddress($user) {
    $userCity =  $user->_p_country;
    $userPostalCode = $user->_p_postal_code;
    $userStreetAddress = $user->_p_street_address;

    $ad = new CCDAAD();
    if (!$userCity && !$userPostalCode && !$userStreetAddress) {
      $ad->setNullFlavor("NASK");
      return $ad;
    }

    $street = new CCDA_adxp_streetAddressLine();
    $street->setData($userStreetAddress);
    $street2 = new CCDA_adxp_streetAddressLine();
    $street2->setData($userPostalCode." ".$userCity);

    $ad->append("streetAddressLine", $street);
    $ad->append("streetAddressLine", $street2);

    return $ad;
  }

  /**
   * Ajoute les t�l�phone de la personne dans l'objet qui a appel� cette m�thode
   *
   * @param CCDAPOCD_MT000040_PatientRole $object  CCDAPOCD_MT000040_PatientRole
   * @param CPerson                       $patient CPerson
   *
   * @return void
   */
  function setTelecom($object, $patient) {
    $patientPhoneNumber = $patient->_p_phone_number;
    $patientMobilePhoneNumber = $patient->_p_mobile_phone_number;
    $patientEmail = $patient->_p_email;

    $tel = new CCDATEL();
    if (!$patientPhoneNumber && !$patientMobilePhoneNumber && !$patientEmail) {
      $tel->setNullFlavor("NASK");
      $object->appendTelecom($tel);
      return;
    }

    $tel->setValue($patientPhoneNumber ? "tel:".$patientPhoneNumber : "");
    $object->appendTelecom($tel);

    $tel = new CCDATEL();
    $tel->setValue($patientMobilePhoneNumber ? "tel:".$patientMobilePhoneNumber : "");
    $object->appendTelecom($tel);

    $tel = new CCDATEL();
    $tel->setValue($patientEmail ? "mailto:".$patientEmail : "");
    $object->appendTelecom($tel);
  }

  /**
   * Retourne le code associ� � la situation familiale
   *
   * @param String $status String
   *
   * @return CCDACE
   */
  function getMaritalStatus($status) {
    $ce = new CCDACE();
    $ce->setCodeSystem("1.3.6.1.4.1.21367.100.1");
    switch ($status) {
      case "S":
        $ce->setCode("S");
        $ce->setDisplayName("C�libataire");
        break;
      case "M":
        $ce->setCode("M");
        $ce->setDisplayName("Mari�");
        break;
      case "G":
        $ce->setCode("G");
        $ce->setDisplayName("Concubin");
        break;
      case "D":
        $ce->setCode("D");
        $ce->setDisplayName("Divorc�");
        break;
      case "W":
        $ce->setCode("W");
        $ce->setDisplayName("Veuf/Veuve");
        break;
      case "A":
        $ce->setCode("A");
        $ce->setDisplayName("S�par�");
        break;
      case "P":
        $ce->setCode("P");
        $ce->setDisplayName("Pacte civil de solidarit� (PACS)");
        break;
      default:
        $ce->setCode("U");
        $ce->setDisplayName("Inconnu");
    }
    return $ce;
  }

  /**
   * Retourne le code associ� au sexe de la personne
   *
   * @param String $sexe String
   *
   * @return CCDACE
   */
  function getAdministrativeGenderCode($sexe) {
    $ce = new CCDACE();
    $ce->setCode(mb_strtoupper($sexe));
    $ce->setCodeSystem("2.16.840.1.113883.5.1");
    switch ($sexe) {
      case "f":
        $ce->setDisplayName("F�minin");
        break;
      case "m":
        $ce->setDisplayName("Masculin");
        break;
      default:
        $ce->setCode("U");
        $ce->setDisplayName("Inconnu");
    }
    return $ce;
  }

  /**
   * Attribution de l'id au PS
   *
   * @param Object           $assigned Object
   * @param CUser|CMediUsers $user     CUser|CMediUsers
   *
   * @return void
   */
  function setIdPs($assigned, $user) {

    if (!$user->adeli && !$user->rpps) {
      return;
    }

    if ($user->adeli) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.1");
      $ii->setAssigningAuthorityName("GIP-CPS");
      $ii->setExtension("0$user->adeli");
      $assigned->appendId($ii);
    }

    if ($user->rpps) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.1");
      $ii->setAssigningAuthorityName("GIP-CPS");
      $ii->setExtension("8$user->rpps");
      $assigned->appendId($ii);
    }
  }


  /**
   * Affectation id � l'�tablissement
   *
   * @param CCDAPOCD_MT000040_CustodianOrganization $entite CCDAPOCD_MT000040_CustodianOrganization
   * @param CGroups                                 $etab   CGroups
   *
   * @return void
   */
  function setIdEtablissement($entite, $etab) {

    if ($etab->siret) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.2");
      $ii->setExtension("3".$etab->siret);
      $ii->setAssigningAuthorityName("GIP-CPS");
      $entite->appendId($ii);
    }

    if ($etab->finess) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.2");
      $ii->setExtension("1".$etab->finess);
      $ii->setAssigningAuthorityName("GIP-CPS");
      $entite->appendId($ii);
    }
  }

  /**
   * Cr�ation du performer
   *
   * @param CMediusers $praticien praticien
   *
   * @return CCDAPOCD_MT000040_Performer1
   */
  function setPerformer($praticien) {
    $performer = new CCDAPOCD_MT000040_Performer1();
    $performer->setTypeCode("PRF");
    $performer->setAssignedEntity(self::$role->setAssignedEntity($praticien, true));
    return $performer;
  }

  /**
   * Affectation des documents
   *
   * @param CCDAPOCD_MT000040_ClinicalDocument $clinicalDoc CCDAPOCD_MT000040_ClinicalDocument
   *
   * @return void
   */
  function setDocumentationOF($clinicalDoc) {
    /** @var CConsultation $object CConsultation*/
    $object = self::$cda_factory->targetObject;

    $documentationOf = new CCDAPOCD_MT000040_DocumentationOf();
    $serviceEvent = new CCDAPOCD_MT000040_ServiceEvent();

    switch (get_class($object)) {
      case "CSejour":
        /** @var CSejour $object CSejour */

        $low = $object->entree;
        $ivlTs = $this->createIvlTs($low, $object->sortie);
        $serviceEvent->setEffectiveTime($ivlTs);

        $ii = new CCDAII();
        $ii->setRoot("2.16.840.1.113883.6.3");
        $ii->setExtension($object->DP);
        $serviceEvent->appendId($ii);

        $serviceEvent->appendPerformer($this->setPerformer($object->_ref_praticien));
        $documentationOf->setServiceEvent($serviceEvent);
        $clinicalDoc->appendDocumentationOf($documentationOf);
        break;
      case "COperation":
        /** @var COperation $object COperation */
        $no_acte = 0;
        $object->loadRefsActesCCAM();
        foreach ($object->_ref_actes_ccam as $acteCcam) {
          if (!$acteCcam->_check_coded) {
            continue;
          }

          $acteCcam->loadRefExecutant();
          $documentationOf = new CCDAPOCD_MT000040_DocumentationOf();
          $serviceEvent = new CCDAPOCD_MT000040_ServiceEvent();
          $ce = new CCDACE();
          $ce->setCode($acteCcam->code_acte);
          $ce->setCodeSystem("1.2.250.1.213.2.5");
          $serviceEvent->setCode($ce);
          $ivl = $this->createIvlTs($acteCcam->execution, "");
          $serviceEvent->setEffectiveTime($ivl);

          if ($no_acte >= 1) {
            continue;
          }
          $serviceEvent->appendPerformer($this->setPerformer($acteCcam->_ref_executant));
          $documentationOf->setServiceEvent($serviceEvent);

          $clinicalDoc->appendDocumentationOf($documentationOf);
          $no_acte++;
        }

        if ($no_acte === 0) {
          $ii = new CCDAII();
          $ii->setNullFlavor("UNK");
          $serviceEvent->appendId($ii);
          $ivl = $this->createIvlTs($object->debut_op, $object->fin_op);
          $serviceEvent->setEffectiveTime($ivl);
          $serviceEvent->appendPerformer($this->setPerformer($object->_ref_chir));
          $documentationOf->setServiceEvent($serviceEvent);

          $clinicalDoc->appendDocumentationOf($documentationOf);
        }

        break;
      case "CConsultation":
        /** @var CConsultation $object CConsultation */
        $object->loadRefPlageConsult();

        $no_acte = 0;
        $object->loadRefsActesCCAM();
        foreach ($object->_ref_actes_ccam as $acteCcam) {
          if (!$acteCcam->_check_coded) {
            continue;
          }

          $acteCcam->loadRefExecutant();
          $documentationOf = new CCDAPOCD_MT000040_DocumentationOf();
          $serviceEvent = new CCDAPOCD_MT000040_ServiceEvent();
          $ii = new CCDAII();
          $ii->setRoot("1.2.250.1.213.2.5");
          $ii->setExtension($acteCcam->code_acte);
          $serviceEvent->appendId($ii);
          $ivl = $this->createIvlTs($acteCcam->execution, "");
          $serviceEvent->setEffectiveTime($ivl);

          if ($no_acte >= 1) {
            continue;
          }
          $serviceEvent->appendPerformer($this->setPerformer($acteCcam->_ref_executant));
          $documentationOf->setServiceEvent($serviceEvent);

          $clinicalDoc->appendDocumentationOf($documentationOf);
          $no_acte++;
        }

        if ($no_acte === 0) {
          $ii = new CCDAII();
          $ii->setNullFlavor("UNK");
          $serviceEvent->appendId($ii);
          $ivl = $this->createIvlTs($object->_datetime, $object->_date_fin);
          $serviceEvent->setEffectiveTime($ivl);
          $serviceEvent->appendPerformer($this->setPerformer($object->_ref_chir));
          $documentationOf->setServiceEvent($serviceEvent);

          $clinicalDoc->appendDocumentationOf($documentationOf);
        }
        break;
    }
  }

  /**
   * Cr�ation d'un ivl_ts avec une valeur basse et haute
   *
   * @param String  $low        String
   * @param String  $high       String
   * @param Boolean $nullFlavor false
   *
   * @return CCDAIVL_TS
   */
  function createIvlTs($low, $high, $nullFlavor = false) {
    $ivlTs = new CCDAIVL_TS();
    if ($nullFlavor && !$low && !$high) {
      $ivlTs->setNullFlavor("UNK");
      return $ivlTs;
    }

    $low = $this->getTimeToUtc($low);
    $high = $this->getTimeToUtc($high);

    $ivxbL = new CCDAIVXB_TS();
    $ivxbL->setValue($low);
    $ivlTs->setLow($ivxbL);
    $ivxbH = new CCDAIVXB_TS();
    $ivxbH->setValue($high);
    $ivlTs->setHigh($ivxbH);

    return $ivlTs;
  }
}