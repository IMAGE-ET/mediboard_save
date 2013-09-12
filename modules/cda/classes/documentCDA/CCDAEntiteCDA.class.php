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
 * Classe regroupant les fonctions de type Entite
 */
class CCDAEntiteCDA extends CCDADocumentCDA {

  /**
   * Cr�ation d'une personne
   *
   * @param CMediUsers $mediUser CMediUsers
   *
   * @return CCDAPOCD_MT000040_Person
   */
  function setPerson($mediUser) {
    $person = new CCDAPOCD_MT000040_Person();

    $pn = new CCDAPN();

    $enxp = new CCDA_en_family();
    $enxp->setData($mediUser->_p_last_name);
    $pn->append("family", $enxp);

    $enxp = new CCDA_en_given();
    $enxp->setData($mediUser->_p_first_name);
    $pn->append("given", $enxp);

    if ($mediUser instanceof CPatient) {
      $enxp = new CCDA_en_given();
      $enxp->setData($mediUser->prenom_2);
      $pn->append("given", $enxp);

      $enxp = new CCDA_en_given();
      $enxp->setData($mediUser->prenom_3);
      $pn->append("given", $enxp);

      $enxp = new CCDA_en_given();
      $enxp->setData($mediUser->prenom_4);
      $pn->append("given", $enxp);
    }

    $person->appendName($pn);

    return $person;
  }

  /**
   * Cr�ation de patient
   *
   * @return CCDAPOCD_MT000040_Patient
   */
  function setPatient() {
    $patientCDA = new CCDAPOCD_MT000040_Patient();

    $patient = self::$cda_factory->patient;
    $pn = new CCDAPN();

    $enxp = new CCDA_en_family();
    $enxp->setData($patient->_p_last_name);
    $enxp->setQualifier(array("BR"));
    if ($patient->_p_maiden_name) {
      $enxp2 = new CCDA_en_family();
      $enxp2->setQualifier(array("BR"));
      $enxp2->setData($patient->_p_maiden_name);
      $pn->append("family", $enxp2);
      $enxp->setQualifier(array("SP"));
    }
    $pn->append("family", $enxp);

    $enxp = new CCDA_en_given();
    $enxp->setData($patient->_p_first_name);
    $pn->append("given", $enxp);

    $enxp = new CCDA_en_given();
    $enxp->setData($patient->prenom_2);
    $pn->append("given", $enxp);

    $enxp = new CCDA_en_given();
    $enxp->setData($patient->prenom_3);
    $pn->append("given", $enxp);

    $enxp = new CCDA_en_given();
    $enxp->setData($patient->prenom_4);
    $pn->append("given", $enxp);

    $patientCDA->appendName($pn);

    $gender = $this->getAdministrativeGenderCode($patient->sexe);
    $patientCDA->setAdministrativeGenderCode($gender);

    $date = $this->getTimeToUtc($patient->_p_birth_date, true);
    $ts = new CCDATS();
    $ts->setValue($date);
    if (!$date) {
      $ts->setNullFlavor("NASK");
    }
    $patientCDA->setBirthTime($ts);

    $status = $this->getMaritalStatus($patient->situation_famille);
    $patientCDA->setMaritalStatusCode($status);

    $patientCDA->setBirthplace(parent::$role->setBirthPlace());

    return $patientCDA;
  }

  /**
   * Cr�ation d'un endroit
   *
   * @return CCDAPOCD_MT000040_Place
   */
  function setPlace() {
    $place = new CCDAPOCD_MT000040_Place();
    $patient = self::$cda_factory->patient;
    $birthPlace = $patient->lieu_naissance;
    $birthPostalCode = $patient->cp_naissance;

    if (!$birthPlace && !$birthPostalCode) {
      return null;
    }

    $ad = new CCDAAD();
    $adxp = new CCDA_adxp_city();
    $adxp->setData($birthPlace);
    $ad->append("city", $adxp);
    $adxp = new CCDA_adxp_postalCode();
    $adxp->setData($birthPostalCode);
    $ad->append("postalCode", $adxp);
    $place->setAddr($ad);

    return $place;
  }

  /**
   * cr�ation d'un custodianOrgnaization
   *
   * @return CCDAPOCD_MT000040_CustodianOrganization
   */
  function setCustodianOrganization() {
    $etablissement = CGroups::loadCurrent();

    $custOrg = new CCDAPOCD_MT000040_CustodianOrganization();
    $this->setIdEtablissement($custOrg, $etablissement);
    $ii = new CCDAII();
    $ii->setRoot(self::$cda_factory->root);
    $custOrg->appendId($ii);

    if ($etablissement->raison_sociale) {
      $name = $etablissement->raison_sociale;
    }
    else {
      $name = $etablissement->text;
    }

    $on = new CCDAON();
    $on->setData($name);
    $custOrg->setName($on);

    $tel = new CCDATEL();
    $tel->setValue("tel:$etablissement->tel");
    $custOrg->setTelecom($tel);

    $ad = new CCDAAD();
    $street = new CCDA_adxp_streetAddressLine();
    $street->setData($etablissement->adresse);
    $street2 = new CCDA_adxp_streetAddressLine();
    $street2->setData($etablissement->cp." ".$etablissement->ville);

    $ad->append("streetAddressLine", $street);
    $ad->append("streetAddressLine", $street2);
    $custOrg->setAddr($ad);

    return $custOrg;
  }

  /**
   * Cr�ation d'une organisation
   *
   * @param CMediUsers $user CMediUsers
   *
   * @return CCDAPOCD_MT000040_Organization
   */
  function setOrganization($user) {
    $factory = self::$cda_factory;
    $organization = new CCDAPOCD_MT000040_Organization();

    $user->loadRefFunction();
    $etablissement = $user->_ref_function->loadRefGroup();

    $this->setIdEtablissement($organization, $etablissement);
    $ii = new CCDAII();
    $ii->setRoot($factory->root);
    $organization->appendId($ii);

    $insdustry = $factory->industry_code;

    $ce = new CCDACE();
    $ce->setCode($insdustry["code"]);
    $ce->setDisplayName($insdustry["displayName"]);
    $ce->setCodeSystem($insdustry["codeSystem"]);
    $organization->setStandardIndustryClassCode($ce);

    if ($etablissement->raison_sociale) {
      $name = $etablissement->raison_sociale;
    }
    else {
      $name = $etablissement->text;
    }

    $on = new CCDAON();
    $on->setData($name);
    $organization->appendName($on);

    $tel = new CCDATEL();
    $tel->setValue("tel:$etablissement->tel");
    $organization->appendTelecom($tel);

    $ad = new CCDAAD();
    $street = new CCDA_adxp_streetAddressLine();
    $street->setData($etablissement->adresse);
    $street2 = new CCDA_adxp_streetAddressLine();
    $street2->setData($etablissement->cp." ".$etablissement->ville);

    $ad->append("streetAddressLine", $street);
    $ad->append("streetAddressLine", $street2);
    $organization->appendAddr($ad);

    return $organization;
  }
}
