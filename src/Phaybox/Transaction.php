<?php

namespace Phaybox;

class Transaction
{
 /**
  * @var Phaybox\Client
  */
  protected $client;

 /**
  * @var array
  */
  protected $params;

 /**
  * Constructor.
  *
  * @param string $client_id
  * @param string $client_rang
  * @param string $client_site
  * @param array  $opts
  */
  public function __construct($client, $params = array())
  {
    $this->client = $client;
    $this->setParams($params);
  }

 /**
  * Get client property
  *
  * @return mixed
  */
  public function getClient()
  {
    return $this->client ?: null;
  }

 /**
  * Get a specific value from a key of the params property
  *
  * @return mixed
  */
  public function getParam($key)
  {
    return $this->params[$key] ?: null;
  }

 /**
  * Get params property
  *
  * @return mixed
  */
  public function getParams()
  {
    return $this->params ?: null;
  }

 /**
  * Set data fields
  *
  * @params array $params The data for the transaction
  */
  public function setParams($params)
  {
    // Cast PBX_TOTAL to a floating point number
    if (isset($params['PBX_TOTAL'])) {
      $params['PBX_TOTAL'] = (float) $params['PBX_TOTAL'];
    }

    // Validate data
    $this->validateParams($params);

    // So far, so good, set data
    $this->params = $params;
  }

 /**
  * Generate a signature based on client properties/options and the transaction specific data
  *
  * @param string $params The required variables to create the signature as an http query
  *
  * @return string
  */
  public function generateSignature($params = '')
  {
    $params = urldecode($params);
    return hash_hmac($this->client->options['algorithm'], $params, $this->client->getSecret(true));
  }

 /**
  * Create an array with the required form fields
  *
  * @param array $opts Additional optional fields to include in the generated array
  *
  * @return array
  */
  public function getFormattedParams()
  {
    // copy params var
    $params = $this->params;

    // create base array
    $fields = array(
        'PBX_SITE'        => $this->client->getSite()
      , 'PBX_RANG'        => $this->client->getRang()
      , 'PBX_IDENTIFIANT' => $this->client->getId()
      , 'PBX_TOTAL'       => $params['PBX_TOTAL']
      , 'PBX_DEVISE'      => $params['PBX_DEVISE']
      , 'PBX_CMD'         => $params['PBX_CMD']
      , 'PBX_PORTEUR'     => $params['PBX_PORTEUR']
      , 'PBX_RETOUR'      => $this->client->options['callback']
      , 'PBX_HASH'        => strtoupper($this->client->options['algorithm'])
      , 'PBX_TIME'        => date(DATE_W3C)
    );

    // Unset setted params
    unset($params['PBX_TOTAL'], $params['PBX_DEVISE'], $params['PBX_CMD'], $params['PBX_PORTEUR']);

    // Merge remaining params
    $fields = array_merge($fields, $params);

    // generate signature from base array
    $fields['PBX_HMAC'] = strtoupper($this->generateSignature(http_build_query($fields)));

    return $fields;
  }

 /**
  * Validate a currency against ISO 4217
  *
  * @param string $currency_code The currency code we want to validate against ISO 4217
  *
  * @return boolean
  */
  protected function validateCurrency($currency_code = '')
  {
    // load currencies from a remote address and store all truthy currency codes in an array
    $xml        = new \SimpleXMLElement('http://www.currency-iso.org/dl_iso_table_a1.xml', 0, true);
    $result     = $xml->xpath('ISO_CURRENCY');
    $currencies = array();
    foreach ($result as $currency) {
      if (empty($currency->NUMERIC_CODE) || $currency->NUMERIC_CODE === 'Nil') continue;
      $currencies[] = $currency->NUMERIC_CODE;
    }

    return in_array($currency_code, $currencies);
  }

 /**
  * Validate form data submitted by the end-user for the impending request
  *
  * @param array $params The form data submitted by the end-user
  *
  * @return mixed
  */
  protected function validateParams($params = array())
  {
    // error var
    $error = null;

    // put all $params keys in an array for convenience
    $paramsKeys = array_keys($params);

    // check for required params and throw error if one of them is missing or falsy
    $requiredParams = array('PBX_TOTAL', 'PBX_DEVISE', 'PBX_CMD', 'PBX_PORTEUR');
    for ($i = 0, $count = count($requiredParams); $i < $count; $i++) {
      if (!array_key_exists($requiredParams[$i], $params)) {
        throw new \ErrorException(sprintf('No value given for one or more required parameters (%s).', $requiredParams[$i]));
      }
    }

    // PBX_TOTAL shouldn't be a falsy value
    if (in_array($params['PBX_TOTAL'], array(0, null, false, ''))) {
      throw new \ErrorException('PBX_TOTAL shouldn\'t be a falsy value.');
    }

    // PBX_DEVISE should respect ISO 4217
    if (!$this->validateCurrency($params['PBX_DEVISE'])) {
      throw new \ErrorException('PBX_DEVISE should validate against ISO 4217.');
    }

    // PBX_CMD shouldn't be a falsy value
    if (in_array($params['PBX_CMD'], array('0', null, false, ''))) {
      throw new \ErrorException('PBX_CMD shouldn\'t be a falsy value.');
    }

    // PBX_PORTEUR should be a well formed email address
    if (!filter_var($params['PBX_PORTEUR'], FILTER_VALIDATE_EMAIL)) {
      throw new \ErrorException('The "PBX_PORTEUR" param (aka email) is malformed.');
    }

    // if we reach this point everything's good so ret
    return true;
  }
}
