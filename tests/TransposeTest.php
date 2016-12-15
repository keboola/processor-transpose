<?php

namespace Keboola\Processor\LastFile\Tests;

use Keboola\Processor\LastFile;
use Keboola\Processor\Transpose;
use Keboola\Temp\Temp;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class TransposeTest extends TestCase
{
    private function prepareTmpDir(Temp $tmp)
    {
        $fs = new Filesystem();
        $fs->mkdir($tmp->getTmpFolder() . "/data/in/tables");
        $fs->mkdir($tmp->getTmpFolder() . "/data/out/tables");
        $fs->copy(__DIR__ . "/data/in/tables/input.csv", $tmp->getTmpFolder() . "/data/in/tables/input.csv");

        return $tmp->getTmpFolder();
    }

    public function testTranspose()
    {
        $tmp = new Temp();
        $tmpDir = $this->prepareTmpDir($tmp);

        $config = [
            "filename" => "input.csv",
            "header_rows_count" => 2,
            "header_column_names" => ["Obchodnik","Team","Manager","Mesto","Region"],
            "header_transpose_row" => 1,
            "header_transpose_column_name" => "month",
            "header_sanitize" => true,
            "transpose_from_column" => 6
        ];

        $processor = new Transpose($tmpDir . '/data');
        $processor->process($config);

        $finder = new Finder();
        $tables = (array) $finder->files()->in($tmpDir . "/data/out/tables/")->sortByName()->getIterator();

        $this->assertEquals(1, count($tables));
        $this->assertFileEquals(__DIR__ . "/data/out/tables/expected.csv", $tmpDir . "/data/out/tables/input.csv");
    }
}
