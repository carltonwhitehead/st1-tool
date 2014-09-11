#!/usr/bin/env php
<?php
namespace St1Tool;

use St1Tool\Names\AnonymizeCommand;
use St1Tool\Names\SetupCensusDataCommand;
use St1Tool\Results\PaxCommand;
use St1Tool\Results\RawCommand;
use St1Tool\Results\ConeKillerCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

define('ST1_TOOL_VERSION', '0.1-git');
define('ST1_TOOL_PATH', __DIR__);

$app = new Application('st1-tool', ST1_TOOL_VERSION);
// Names
$app->add(new SetupCensusDataCommand());
$app->add(new AnonymizeCommand());
// Results
$app->add(new RawCommand());
$app->add(new PaxCommand());
$app->add(new ConeKillerCommand());
$app->run();
