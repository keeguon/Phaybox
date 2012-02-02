<?php

namespace Phaybox;

class Error extends \Exception
{
  /**
   * Error codes translation table
   *
   * @var array
   */
  public static $errorTexts = array(
      '00001' => 'Connection failed'
    , '001xx' => 'Payment denied'
    , '00003' => 'Paybox error'
    , '00004' => 'Invalid user ID or security code'
    , '00006' => 'Access denied (wrong site, rang or client id)'
    , '00008' => 'Invalid expiration date'
    , '00009' => 'Error creating subscription'
    , '00010' => 'Unknown currency'
    , '00011' => 'Incorrect amount'
    , '00015' => 'Payment already made'
    , '00016' => 'Existing subscriber'
    , '00021' => 'Unauthorized credit card'
    , '00029' => 'Non-compliant credit card'
    , '00030' => 'Payment timed out'
    , '00031' => 'Reserved'
    , '00032' => 'Reserved'
    , '00033' => 'Country code refused based on your current IP'
    , '00040' => 'Transaction made without 3DSecure, blocked by filter'
  );

  /**
   * Constructor
   *
   * @param string $errorCode
   */
  public function __construct($errorCode = '00000')
  {
    $message = '';

    if (preg_match('/001[0-9]{2}$/i', $errorCode)) {
      $message = self::$errorTexts['001xx'];
    } else if (array_key_exists($errorCode, self::$errorTexts)) {
      $message = self::$errorTexts[$errorCode];
    }

    if ($message !== '') {
      parent::__construct($message, (int) $errorCode);
    } else {
      parent::__construct('Unknown error code error.', 99);
    }
  }
}
