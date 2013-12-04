#!/usr/bin/env php
<?php
namespace St1Tool;

use Symfony\Component\Console\Application;
use St1Tool\Names\SetupCensusDataCommand;
use St1Tool\Names\AnonymizeCommand;

require 'vendor/autoload.php';

define('ST1_TOOL_VERSION', '0.1-git');
define('ST1_TOOL_PATH', __DIR__);

$app = new Application('st1-tool', ST1_TOOL_VERSION);
$app->add(new SetupCensusDataCommand());
$app->add(new AnonymizeCommand());
$app->run();
