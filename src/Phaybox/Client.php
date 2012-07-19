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
        'algorithm'   => 'sha512'
      , 'callback'    => 'Amt:M;Ref:R;Auth:A;Err:E'
      , 'path_prefix' => '/paybox'
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
   * Get the path_prefix option
   *
   * @return string
   */
  public function getPathPrefix()
  {
    return array_key_exists('path_prefix', $this->options) ? $this->options['path_prefix'] : '/paybox';
  }

  /**
   * Get the request phase path
   *
   * @return string
   */
  public function getRequestPath()
  {
    return array_key_exists('request_path', $this->options) ? $this->options['request_path'] : $this->getPathPrefix().'/';
  }

  /**
   * Get the callback phase path
   *
   * @return string
   */
  public function getCallbackPath()
  {
    return array_key_exists('callback_path', $this->options) ? $this->options['callback_path'] : $this->getPathPrefix().'/callback';
  }

  /**
   * Get the current path
   *
   * @return string
   */
  public function getCurrentPath()
  {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

    return $parsedUrl['path'];
  }

  /**
   * Get the current query string
   *
   * @return string
   */
  public function getQueryString()
  {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

    return isset($parsedUrl['query']) ? $parsedUrl['query'] : null;
  }

  /**
   * Get the complete HTTP host of the request
   *
   * @return string
   */
  public function getFullHost()
  {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'];
  }

  /**
   * Get the complete callback URI
   *
   * @return string
   */
  public function getCallbackUrl()
  {
    return $this->getQueryString() ? $this->getFullHost().$this->getCallbackPath().'?'.$this->getQueryString() : $this->getFullHost().$this->getCallbackPath();
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
