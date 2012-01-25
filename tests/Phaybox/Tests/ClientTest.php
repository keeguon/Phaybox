<?php

namespace Phaybox\Tests;

class ClientTest extends \Phaybox\Tests\TestCase
{
 /**
  * @var Phaybox\Client
  */
  protected $client;

 /**
  * Setup fixtures
  */
  protected function setUp()
  {
    $this->client = new \Phaybox\Client('abc', 'def', 'ghi', 'https://example.com');
  }

  protected function tearDown()
  {
    unset($this->client);
  }

 /**
  * @cover Phaybox\Client::__construct()
  * @cover Phaybox\Client::getId()
  * @cover Phaybox\Client::getRang()
  * @cover Phaybox\Client::getSite()
  */
  public function testConstructorBuildsClient()
  {
    // client id and rang should be assigned
    $this->assertEquals('abc', $this->client->getId());
    $this->assertEquals('ghi', $this->client->getRang());

    // client site should be assigned
    $this->assertEquals('https://example.com', $this->client->getSite());

    // algorithm option should be equals to 'sha512'
    $this->assertEquals('sha512', $this->client->options['algorithm']);
  }

 /**
  * @cover Phaybox\Client::__construct()
  */
  public function testConstructorFiltersBadAlgorithm()
  {
    $this->setExpectedException('\ErrorException', 'The chosen algorithm doesn\'t seem to be available on this system');
    $client = new \Phaybox\Client('abc', 'def', 'ghi', 'https://example.com', array('algorithm' => 'tea'));
  }
}
