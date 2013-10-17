<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage webservices
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

CAppUI::requireLibraryFile("NuSOAP/nusoap", false);

if (!class_exists("nusoap_client", false)) {
  return;
}

/**
 * The CNuSOAPClient class
 */
class CNuSOAPClient extends nusoap_client {
  public $wsdl_url;
  public $type_echange_soap;
  public $soap_client_error = false;
  public $flatten;
  public $loggable;
  public $encoding;
  
  /**
   * The constructor
   * 
   * @param string  $rooturl     The URL of the wsdl file
   * @param string  $type        The type of exchange
   * @param array   $options     An array of options
   * @param boolean $loggable    True if you want to log all the exchanges with the web service
   * @param string  $local_cert  Path of the certifacte
   * @param string  $passphrase  Pass phrase for the certificate
   * @param boolean $safe_mode   Safe mode
   * @param boolean $verify_peer Require verification of SSL certificate used
   * @param string  $cafile      Location of Certificate Authority file on local filesystem
   *
   * @throws CMbException
   *
   * @return CNuSOAPClient
   */
  function __construct(
      $rooturl, $type = null, $options = array(), $loggable = null, $local_cert = null, $passphrase = null, $safe_mode = false,
      $verify_peer = false, $cafile = null
  ) {
    $this->wsdl_url = $rooturl;

    if ($loggable) {
      $this->loggable = $loggable;
    }
    
    if ($type) {
      $this->type_echange_soap = $type;
    }

    if (!$safe_mode) {
      if (!$html = file_get_contents($this->wsdl_url)) {
        $this->soap_client_error = true;
        throw new CMbException("CSourceSOAP-unable-to-parse-url", $this->wsdl_url);
      }

      if (strpos($html, "<?xml") === false) {
        $this->soap_client_error = true;
        throw new CMbException("CSourceSOAP-wsdl-invalid");
      }
    }
    
    if (array_key_exists("encoding", $options)) {
      $encoding = $options["encoding"];
      $this->soap_defencoding = $encoding;
      if ($encoding == "UTF-8") {
        $this->decode_utf8 = false;
      }
    }
    
    if ($local_cert) {
      $this->certRequest = array(
        "sslcertfile" => $local_cert
      );
    }
    
    if ($passphrase) {
      $this->certRequest = array(
        "passphrase" => $passphrase
      );
    }

    parent::__construct($rooturl, true, false, false, false, false, CAppUI::conf("webservices connection_timeout"));

    $this->wsdl_url = $rooturl;
  }

  /** Calls a SOAP function
   *
   * @param string $function_name   The name of the SOAP function to call
   * @param array  $arguments       An array of the arguments to pass to the function
   * @param array  $options         An associative array of options to pass to the client
   * @param mixed  $input_headers   An array of headers to be sent along with the SOAP request
   * @param array  &$output_headers If supplied, this array will be filled with the headers from the SOAP response
   *
   * @throws Exception|SoapFault
   *
   * @return mixed SOAP functions may return one, or multiple values
   */
  public function call($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {      
    $output = parent::call($function_name, $arguments);
    
    if ($this->fault) {
      throw new SoapFault($this->faultcode, $this->faultstring, $this->fault->faultactor, $this->faultdetail);
    }
    elseif ($this->getError()) {
      throw new SoapFault("1", $this->getError());
    }
    
    return $output;
  }
  
  /**
   * Set the request and the response of the exchange and return it
   * 
   * @param CEchangeSOAP $exchange The exchangeSOAP
   * 
   * @return CEchangeSOAP
   */
  public function getTrace(CEchangeSOAP $exchange) {
    $exchange->trace                 = true;
    $exchange->last_request_headers   = $this->requestHeaders;
    $exchange->last_request           = $this->request;
    $exchange->last_response_headers  = $this->responseHeader;
    $exchange->last_response          = $this->response;
    
    return $exchange;
  }
  
  /**
   * Return the list of the operation of the WSDL
   * 
   * @return array An array who contains all the operation of the WSDL
   */
  public function __getFunctions() {
    $wsdl = new wsdl($this->wsdl);
    $operations = $wsdl->getOperations();
    $return = array();
    
    foreach ($operations as $_operation) {
      $output = $_operation['output']['parts'];
      $output_type = 'void';
      if (array_key_exists('return', $output)) {
        $output_type = end(explode(':', $output['return']));
      }
      $name = $_operation['name'];
      
      $input = array();
      
      foreach ($_operation['input']['parts'] as $_param => $_type) {
        $input[] = end(explode(':', $_type)) . ' $' . $_param;
      }
      $input = join(', ', $input);
      
      $return[] = "$output_type $name($input)";
    }

    return $return;
  }
  
  /**
   * Returns an array of functions described in the WSDL for the Web service. 
   * 
   * @return array The array of SOAP function prototype
   */
  public function __getTypes() {
    /* TODO Retourner les types */
    return array();
  } 
  
  /**
   * Defines headers to be sent along with the SOAP requests
   * 
   * @param array $soapheaders The headers to be set
   * 
   * @return boolean True on success or False on failure 
   */
  public function setHeaders($soapheaders) {
  }

  /**
   * Check service availability
   *
   * @throws CMbException
   *
   * @return void
   */
  public function checkServiceAvailability() {
  }
}