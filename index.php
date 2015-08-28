<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->config(array(
    'debug' => true,
    'templates.path' => 'templates'
));

function cal_hash($chars) {
    $algos = hash_algos();
    $hash_rst = array();
    foreach($algos as $algo) {
        // $st = microtime();
        $rst = hash($algo, $chars, false);
        // $et = microtime();
        // list($ss, $si) = explode(' ', $st);
        // list($es, $ei) = explode(' ', $et);
        // $hash_rst[str_replace(",", "_", $algo)] = array('rst' => $rst, 'time' => $ei + $es - $si - $ss);
        $hash_rst[str_replace(",", "_", $algo)] = array('rst' => $rst);
    }
    return $hash_rst;
}

// GET route
$app->get('/', function () use ($app) {
    $chars = 'HASH';
    $app->render('hash.html', array('chars' => $chars, 'hash_rst' => cal_hash($chars)));
});

// POST route
$app->get('/:chars.html', function ($chars) use ($app) {
    $app->render('hash.html', array('chars' => $chars, 'hash_rst' => cal_hash($chars)));
});

$app->run();
