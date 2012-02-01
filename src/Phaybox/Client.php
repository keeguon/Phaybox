<?php

namespace Phaybox;

class Client
{
 /**
  * @var array
  */
  public $options;

 /**
  * @var string
  */
  protected $id;

 /**
  * @var string
  */
  protected $secret;

 /**
  * @var string
  */
  protected $rang;

 /**
  * @var string
  */
  protected $site;

 /**
  * Constructor.
  *
  * @param string $client_id
  * @param string $client_rang
  * @param string $client_site
  * @param array  $opts
  */
  public function __construct($clientId, $clientSecret, $clientRang, $clientSite, $opts = array())
  {
    // check for required params
    if (!$clientId || !$clientSecret || !$clientRang || !$clientSite) {
      throw new \ErrorException('InvalidConstructArgs');
    }
    
    // set options
    $this->options = array_replace_recursive(array(
        'algorithm'    => 'sha512'
      , 'callback'     => 'Amt:M;Ref:R;Auth:A;Err:E'
    ), $opts);

    // check algorithm availability
    if (!in_array($this->options['algorithm'], hash_algos())) {
      throw new \ErrorException('The chosen algorithm doesn\'t seem to be available on this system');
    }

    // set properties
    $this->id     = $clientId;
    $this->secret = $clientSecret;
    $this->rang   = $clientRang;
    $this->site   = $clientSite;
  }

 /**
  * Get id property
  *
  * @return mixed
  */
  public function getId()
  {
    return $this->id ?: null;
  }

 /**
  * Get secret property
  *
  * @param boolean $binary Wether to return the secret in binary or not
  *
  * @return mixed
  */
  public function getSecret($binary = false)
  {
    $secret = $binary ? pack('H*', $this->secret) : $this->secret;
    return $secret ?: null;
  }

 /**
  * Get rang property
  *
  * @return mixed
  */
  public function getRang()
  {
    return $this->rang ?: null;
  }

 /**
  * Get site property
  *
  * @return mixed
  */
  public function getSite()
  {
    return $this->site ?: null;
  }

 /**
  * Get a transaction
  *
  * @param array $data The needed transaction data
  *
  * @return mixed
  */
  public function getTransaction($data = array())
  {
    return new \Phaybox\Transaction($this, $data);
  }
}
