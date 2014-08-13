<?php
// This is global bootstrap for autoloading 
include __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/start.php';
$app->boot();
