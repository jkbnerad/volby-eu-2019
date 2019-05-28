#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use App\Command\Kraje;
use App\Command\OkresyObce;
use App\Command\Strany;
use Symfony\Component\Console\Application;

$application = new Application();

// commands
$application->add(new Strany());
$application->add(new Kraje());
$application->add(new OkresyObce());

$application->run();
