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
        $rst = hash($algo, $chars, false);
        $hash_rst[str_replace(",", "-", $algo)] = array('rst' => $rst, 'color' => substr($rst, -6));
    }
    return $hash_rst;
}

function get_randoms($n) {
	$random = array();
	require_once 'lib/Faker/autoload.php';
	$faker = Faker\Factory::create();
	for ($i = 0; $i < $n; $i ++) {
 		$random[] = $faker->word;
	}
	return $random;
}

function do_history($chars, $max = 20) {
    require_once 'lib/php-file-cache/fcache.inc.php';
    $fcache = new FCache();
    $history = $fcache->get('hash_history');
    if (! $history) {
        $history = array();
    }
    if (array_key_exists($chars, $history)) {
        return array_keys($history);
    }
    if (count($history) > $max) {
        // array_shift($history);
        $key_del = array_rand($history, 1);
        unset($history[$key_del]);
    }
    $history[$chars] = '';
    $fcache->add('hash_history', $history);
    return array_keys($history);
}

// GET route
$app->get('/', function () use ($app) {
    $chars = 'DigHash';
    $random = get_randoms(20);
    $history = do_history($chars);
    $app->render('hash.html', array('chars' => $chars, 'hash_rst' => cal_hash($chars), 'random' => $random, 'history' => $history));
});

$app->get('/:chars.html', function ($chars) use ($app) {
	$random = get_randoms(20);
    $history = do_history($chars);
    $app->render('hash.html', array('chars' => $chars, 'hash_rst' => cal_hash($chars), 'random' => $random, 'history' => $history));
});

$app->run();
