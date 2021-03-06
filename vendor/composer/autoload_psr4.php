<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'phpseclib\\' => array($vendorDir . '/phpseclib/phpseclib/phpseclib'),
    'Symfony\\Polyfill\\Mbstring\\' => array($vendorDir . '/symfony/polyfill-mbstring'),
    'Symfony\\Component\\Yaml\\' => array($vendorDir . '/symfony/yaml'),
    'Symfony\\Component\\OptionsResolver\\' => array($vendorDir . '/symfony/options-resolver'),
    'Symfony\\Component\\Finder\\' => array($vendorDir . '/symfony/finder'),
    'Symfony\\Component\\Filesystem\\' => array($vendorDir . '/symfony/filesystem'),
    'Symfony\\Component\\Console\\' => array($vendorDir . '/symfony/console'),
    'Src\\RouterBoard\\' => array($baseDir . '/src/Routerboard', $baseDir . '/src/Routerboard/BackupFilesystem', $baseDir . '/src/Routerboard/GitLab', $baseDir . '/src/Routerboard/SecureConnector', $baseDir . '/src/Routerboard/Validators', $baseDir . '/src/Routerboard/InputParser'),
    'Src\\Logger\\' => array($baseDir . '/src/Logger'),
    'Src\\Adapters\\' => array($baseDir . '/src/Adapters'),
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-message/src'),
    'PHPMailer\\PHPMailer\\' => array($vendorDir . '/phpmailer/phpmailer/src'),
    'Http\\Promise\\' => array($vendorDir . '/php-http/promise/src'),
    'Http\\Message\\MultipartStream\\' => array($vendorDir . '/php-http/multipart-stream-builder/src'),
    'Http\\Message\\' => array($vendorDir . '/php-http/message/src', $vendorDir . '/php-http/message-factory/src'),
    'Http\\Discovery\\' => array($vendorDir . '/php-http/discovery/src'),
    'Http\\Client\\Common\\' => array($vendorDir . '/php-http/client-common/src'),
    'Http\\Client\\' => array($vendorDir . '/php-http/httplug/src'),
    'Http\\Adapter\\Guzzle6\\' => array($vendorDir . '/php-http/guzzle6-adapter/src'),
    'GuzzleHttp\\Psr7\\' => array($vendorDir . '/guzzlehttp/psr7/src'),
    'GuzzleHttp\\Promise\\' => array($vendorDir . '/guzzlehttp/promises/src'),
    'GuzzleHttp\\' => array($vendorDir . '/guzzlehttp/guzzle/src'),
    'Gitlab\\' => array($vendorDir . '/m4tthumphrey/php-gitlab-api/lib/Gitlab'),
    'CodeClimate\\PhpTestReporter\\' => array($vendorDir . '/codeclimate/php-test-reporter/src'),
    'Clue\\StreamFilter\\' => array($vendorDir . '/clue/stream-filter/src'),
    'App\\Console\\' => array($baseDir . '/app/Console'),
    'App\\Config\\' => array($baseDir . '/app/Config'),
);
