<?php

namespace Phaybox\Tests;

class TransactionTest extends \Phaybox\Tests\TestCase
{
 /**
  * @var Phaybox\Client
  */
  protected $client;

 /**
  * @var Phaybox\Transaction
  */
  protected $transaction;

 /**
  * Set up fixtures
  */
  protected function setUp()
  {
    $this->client      = new \Phaybox\Client('abc', 'def', 'ghi', 'https://example.com');
    $this->transaction = $this->client->getTransaction(array('PBX_TOTAL' => 1000, 'PBX_DEVISE' => 978, 'PBX_CMD' => 'TEST+Paybox', 'PBX_PORTEUR' => 'test@paybox.com'));
  }

  protected function tearDown()
  {
    unset($this->transaction);
    unset($this->client);
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  */
  public function testConstructorBuildsTransaction()
  {
    // assigns client
    $this->assertEquals($this->client, $this->transaction->getClient());

    // assigns extra params
    $target = new \Phaybox\Transaction($this->client, array_merge($this->transaction->getParams(), array('foo' => 'bar')));
    $this->assertEquals('bar', $target->getParam('foo'));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testParamsRequiresTotal()
  {
    $this->setExpectedException('\ErrorException', 'No value given for one or more required parameters (PBX_TOTAL).');
    new \Phaybox\Transaction($this->client);
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testParamsRequiresDevise()
  {
    $this->setExpectedException('\ErrorException', 'No value given for one or more required parameters (PBX_DEVISE).');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 1000));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testParamsRequiresCmd()
  {
    $this->setExpectedException('\ErrorException', 'No value given for one or more required parameters (PBX_CMD).');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 1000, 'PBX_DEVISE' => 978));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testParamsRequiresPorteur()
  {
    $this->setExpectedException('\ErrorException', 'No value given for one or more required parameters (PBX_PORTEUR).');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 1000, 'PBX_DEVISE' => 978, 'PBX_CMD' => 'TEST+Paybox'));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testTotalParamValidity()
  {
    $this->setExpectedException('\ErrorException', 'PBX_TOTAL shouldn\'t be a falsy value.');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 'zero', 'PBX_DEVISE' => 978, 'PBX_CMD' => 'TEST+Paybox', 'PBX_PORTEUR' => 'test@paybox.com'));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateCurrency()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testDeviseParamValidity()
  {
    $this->setExpectedException('\ErrorException', 'PBX_DEVISE should validate against ISO 4217.');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 1000, 'PBX_DEVISE' => 'EUR', 'PBX_CMD' => 'TEST+Paybox', 'PBX_PORTEUR' => 'test@paybox.com'));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testCmdParamValidity()
  {
    $this->setExpectedException('\ErrorException', 'PBX_CMD shouldn\'t be a falsy value.');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 1000, 'PBX_DEVISE' => 978, 'PBX_CMD' => '', 'PBX_PORTEUR' => 'test@paybox.com'));
  }

 /**
  * @covers Phaybox\Transaction::__construct()
  * @covers Phaybox\Transaction::setParams()
  * @covers Phaybox\Transaction::validateParams()
  */
  public function testPorteurParamValidity()
  {
    $this->setExpectedException('\ErrorException', 'The "PBX_PORTEUR" param (aka email) is malformed.');
    new \Phaybox\Transaction($this->client, array('PBX_TOTAL' => 1000, 'PBX_DEVISE' => 978, 'PBX_CMD' => 'TEST+Paybox', 'PBX_PORTEUR' => 'broken pipe'));
  }

 /**
  * @covers Phaybox\Transaction::generateSignature()
  * @covers Phaybox\Transaction::getFormattedParams()
  */
  public function testFormattedParams()
  {
    $formattedParams = $this->transaction->getFormattedParams();

    // PBX_ID, PBX_RANG, PBX_SITE, PBX_HASH, PBX_RETOUR should match client encapsuled data
    $this->assertEquals($this->client->getId(), $formattedParams['PBX_IDENTIFIANT']);
    $this->assertEquals($this->client->getRang(), $formattedParams['PBX_RANG']);
    $this->assertEquals($this->client->getSite(), $formattedParams['PBX_SITE']);
    $this->assertEquals($this->client->options['algorithm'], $formattedParams['PBX_HASH']);
    $this->assertEquals($this->client->options['callback'], $formattedParams['PBX_RETOUR']);

    // PBX_TOTAL, PBX_DEVISE, PBX_CMD, PBX_PORTEUR should match transaction params
    $this->assertEquals($this->transaction->getParam('PBX_TOTAL'), $formattedParams['PBX_TOTAL']);
    $this->assertEquals($this->transaction->getParam('PBX_DEVISE'), $formattedParams['PBX_DEVISE']);
    $this->assertEquals($this->transaction->getParam('PBX_CMD'), $formattedParams['PBX_CMD']);
    $this->assertEquals($this->transaction->getParam('PBX_PORTEUR'), $formattedParams['PBX_PORTEUR']);

    // PBX_TIME should be formatted as specified in ISO 8601
    $this->assertISO8601($formattedParams['PBX_TIME']);

    // PBX_HMAC shouldn't be an empty string
    $this->assertNotEquals('', $formattedParams['PBX_HMAC']);
  }

 /**
  * Test a date against the ISO 8601
  *
  * @param string $date The date we want to assert against ISO 8601
  *
  * @return boolean
  */
  protected function assertISO8601($date)
  {
    $pattern = "/^(\d{4})(?:-(\d{2})(?:-(\d{2})(?:T(\d{2}):(\d{2})(?::(\d{2})(?:\.\d{3})?)?(Z|(?:[-+]\d{2}:\d{2}))?)?)?)?$/";
    return preg_match($pattern, $date);
  }
}
