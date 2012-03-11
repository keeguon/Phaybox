<?php

// Autoload
// --------

if (false === class_exists('Symfony\Component\ClassLoader\UniversalClassLoader', false)) {
  require_once __DIR__.'/../../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
}

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader;
$loader->registerNamespaces(array(
    'Symfony' => array(__DIR__.'/../../vendor', __DIR__.'/vendor')
  , 'Phaybox' => __DIR__.'/../../src'
));
$loader->register();


// Load PHARs
// ----------

require_once __DIR__.'/silex.phar';


// Use libs
// --------

use Symfony\Component\HttpFoundation\Request
  , Symfony\Component\HttpFoundation\Response
  , Symfony\Component\Yaml\Yaml;


// Load views
// ----------

require_once __DIR__.'/views/PaymentView.php';


// Configure Paybox client
// -----------------------

$payboxConfig = array_merge(array(
    'client_id'      => ''
  , 'client_secret'  => ''
  , 'client_rang'    => ''
  , 'client_site'    => ''
  , 'client_options' => array()
), Yaml::parse(__DIR__.'/config/phaybox.yml'));
$payboxClient = new Phaybox\Client($payboxConfig['client_id'], $payboxConfig['client_secret'], $payboxConfig['client_rang'], $payboxConfig['client_site'], $payboxConfig['client_options']);


// Configure Silex app
// -------------------

$app = new Silex\Application();


// Routes
// ------

$app->get('/payment', function(Request $request) use ($app, $payboxClient) {
  // Create Paybox transaction
  $payboxTransaction = $payboxClient->getTransaction(array(
      'PBX_TOTAL'    => '1000'                                 // 10.00
    , 'PBX_DEVISE'   => 978                                    // See ISO 4217
    , 'PBX_CMD'      => 'My order'                             // Somewhat unique label for the order
    , 'PBX_PORTEUR'  => 'me@mail.com'                          // The email of the user who's making the transaction
    , 'PBX_EFFECTUE' => 'http://phaybox.local/payment/callback' // Success callback URI (optional)
    , 'PBX_REFUSE'   => 'http://phaybox.local/payment/callback' // Error callback URI (optional)
    , 'PBX_ANNULE'   => 'http://phaybox.local/payment/callback' // Cancel callback URI (optional)
  ));

  // Get form fields
  $formFields = $payboxTransaction->getFormattedParams();

  // Get template and instantiate view
  $paymentTemplate   = file_get_contents(__DIR__.'/templates/payment.mustache');
  $paymentView       = new PaymentView;
  $paymentView->vars = $formFields;
  
  // Return response object w/ the rendered view
  return new Response($paymentView->render($paymentTemplate), 200);
});

$app->get('/payment/callback', function(Request $request) use ($app) {
  $payboxError = null;

  // Handle error
  $errorCode = $request->query->get('Err');
  if ($errorCode !== '00000') {
    $payboxError = new Phaybox\Error($errorCode);
  }

  return $payboxError ? new Response($payboxError->getMessage(), 400) : new Response('Payment succeeded', 200);
});


// Run app
// -------

$app->run();
