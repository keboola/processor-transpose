<?php

use Symfony\Component\Yaml\Yaml;

require_once(dirname(__FILE__) . "/../vendor/autoload.php");

$arguments = getopt("d::", array("data::"));
if (!isset($arguments["data"])) {
    print "Data folder not set.";
    exit(1);
}

$config = null;
if (file_exists($arguments['data'] . '/config.json')) {
    $config = json_decode(file_get_contents($arguments['data'] . '/config.json'), true);
} else if (file_exists($arguments['data'] . '/config.yml')) {
    $config = Yaml::parse(file_get_contents($arguments['data'] . '/config.yml'));
} else {
    print "Neither config.json or config.yml found";
    exit(1);
}

//@todo check config parameters

try {
    $processor = new \Keboola\Processor\Transpose($arguments['data']);
    $rows = $processor->process($config['parameters']);
} catch (\Keboola\Processor\Exception $e) {
    print $e->getMessage();
    exit(1);
}

print sprintf("Processed %s rows.", $rows);
exit(0);
