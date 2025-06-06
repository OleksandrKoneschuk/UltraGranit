<?php
require 'core/Core.php';

spl_autoload_register(static function ($className) {
    $path = str_replace('\\', '/', $className. '.php');
    if (file_exists($path))
        include_once($path);
});

if (isset($_GET['route']))
    $route = $_GET['route'];
else
    $route = '';

use core\CurrencyUpdater;
CurrencyUpdater::updateIfNeeded();

$core = \core\Core::get();
$core->run($route);
$core->done();