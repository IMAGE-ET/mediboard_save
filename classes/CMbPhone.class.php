<?php

/**
 * $Id$
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
 
 
class CMbPhone {

  /**
   * transform a phone number to an internation format
   *
   * @param string $phonenumber the phone number
   *
   * @return string $phonenumber the phone internationalized
   */
  static function phoneToInternational($phonenumber) {

    //french default format
    if (self::checkMobileNumber($phonenumber)) {
      return preg_replace("/0/", "33", $phonenumber, 1);
    }

    return $phonenumber;
  }

  /**
   * check for mobile number (06..., 07...)
   *
   * @param string $number  the number
   * @param string $country the country to check
   *
   * @return bool
   */
  static function checkMobileNumber($number, $country ="fr") {
    $phones = array(
      "fr"  => "/(0|\+33\s?)(6|7)(\s?\d{2}){4}/"
    );

    if (!array_key_exists($country, $phones)) {
      return false;
    }

    switch ($country) {
      case "fr":
        if (preg_match($phones[$country], $number)) {
          return true;
        }
        break;
    }

    return false;
  }

  /**
   * get the phone from guid
   *
   * @param string $guid        object guid
   * @param bool   $checkmobile check for a valide mobile number
   *
   * @return bool|string number found or false if not found or invalid
   */
  static function getPhoneFromGuid($guid, $checkmobile=false) {
    $object = CMbObject::loadFromGuid($guid);
    $object->updateFormFields();

    if ($object instanceof CPerson) {
      $phone_number =  $object->_p_phone_number;
      if (isset($phone_number)) {
        if ($checkmobile && !self::checkMobileNumber($phone_number)) {
          return false;
        }
        return $phone_number;
      }

      $mobile_phone_number = $object->_p_mobile_phone_number;
      if (isset($mobile_phone_number)) {
        if ($checkmobile && !self::checkMobileNumber($mobile_phone_number)) {
          return false;
        }
        return $mobile_phone_number;
      }
    }
    return false;
  }


  /**
   * get the mobine phone of an user object
   *
   * @param string $guid MB guid
   *
   * @return string
   */
  static function getMobilePhoneFromGuid($guid) {
    $object = CMbObject::loadFromGuid($guid);
    $object->updateFormFields();

    if ($object instanceof CPerson) {
      return $object->_p_mobile_phone_number;
    }

    return null;
  }
}