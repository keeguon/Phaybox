<?php

// Load Mustache.php
// -----------------

require_once __DIR__.'/../vendor/mustache.php/Mustache.php';


// View class
// ----------

class PaymentView extends Mustache
{
  public $vars;

  public function site()
  {
    return $this->vars['PBX_SITE'];
  }

  public function rang()
  {
    return $this->vars['PBX_RANG'];
  }

  public function identifiant()
  {
    return $this->vars['PBX_IDENTIFIANT'];
  }

  public function total()
  {
    return urlencode($this->vars['PBX_TOTAL']);
  }

  public function devise()
  {
    return $this->vars['PBX_DEVISE'];
  }

  public function cmd()
  {
    return urlencode($this->vars['PBX_CMD']);
  }

  public function porteur()
  {
    return urlencode($this->vars['PBX_PORTEUR']);
  }

  public function retour()
  {
    return $this->vars['PBX_RETOUR'];
  }

  public function algorithm()
  {
    return strtoupper($this->vars['PBX_HASH']);
  }

  public function timestamp()
  {
    return urlencode($this->vars['PBX_TIME']);
  }

  public function optionals()
  {
    $optionals = array();
    $vars      = $this->vars;

    unset($vars['PBX_SITE'], $vars['PBX_RANG'], $vars['PBX_IDENTIFIANT'], $vars['PBX_TOTAL'], $vars['PBX_DEVISE'], $vars['PBX_CMD'], $vars['PBX_PORTEUR'], $vars['PBX_RETOUR'], $vars['PBX_HASH'], $vars['PBX_TIME'], $vars['PBX_HMAC']);

    foreach ($vars as $name => $value) {
      $optionals[] = array('name' => $name, 'value' => urlencode($value));
    }

    return $optionals;
  }

  public function signature()
  {
    return urlencode($this->vars['PBX_HMAC']);
  }
}
