<?php

require_once(dirname(__FILE__) . "/../vendor/autoload.php");

$arguments = getopt("d::", array("data::"));
if (!isset($arguments["data"])) {
    print "Data folder not set.";
    exit(1);
}

if (!file_exists($arguments["data"] . "/config.json")) {
    print "config.json file not found";
    exit(1);
}

$config = json_decode(file_get_contents($arguments["data"] . "/config.json"), true);

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
