<?php

namespace Keboola\Processor\LastFile\Tests;

use Keboola\Csv\CsvFile;
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
        $fs->copy(
            __DIR__ . "/data/in/tables/input.csv",
            $tmp->getTmpFolder() . "/data/in/tables/input.csv"
        );
        $fs->copy(
            __DIR__ . "/data/in/tables/input.csv.manifest",
            $tmp->getTmpFolder() . "/data/in/tables/input.csv.manifest"
        );
        $fs->copy(
            __DIR__ . "/data/in/tables/input_2.csv",
            $tmp->getTmpFolder() . "/data/in/tables/input_2.csv"
        );
        $fs->copy(
            __DIR__ . "/data/in/tables/input_2.csv.manifest",
            $tmp->getTmpFolder() . "/data/in/tables/input_2.csv.manifest"
        );

        return $tmp->getTmpFolder();
    }

    public function testTranspose()
    {
        $tmp = new Temp();
        $tmpDir = $this->prepareTmpDir($tmp);

        $config = [
            "transpose" => true,
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
        $tables = (array) $finder->files()->in($tmpDir . "/data/out/tables/")->name('*.csv')->sortByName()->getIterator();

        $this->assertEquals(2, count($tables));
        $this->assertFileEquals(__DIR__ . "/data/out/tables/expected.csv", $tmpDir . "/data/out/tables/input.csv");
        $this->assertFileExists($tmpDir . "/data/out/tables/input.csv.manifest");
        $this->assertFileExists($tmpDir . "/data/out/tables/input_2.csv");
        $this->assertFileExists($tmpDir . "/data/out/tables/input_2.csv.manifest");
    }

    public function testTransposeDisabled()
    {
        $tmp = new Temp();
        $tmpDir = $this->prepareTmpDir($tmp);

        $config = [
            "transpose" => false,
            "filename" => "input.csv",
            "header_rows_count" => 1,
            "header_sanitize" => false
        ];

        $processor = new Transpose($tmpDir . '/data');
        $processor->process($config);

        $finder = new Finder();
        $tables = (array) $finder->files()->in($tmpDir . "/data/out/tables/")->name('*.csv')->sortByName()->getIterator();

        $this->assertEquals(2, count($tables));
        $this->assertFileEquals(__DIR__ . "/data/in/tables/input.csv", $tmpDir . "/data/out/tables/input.csv");
        $this->assertFileExists($tmpDir . "/data/out/tables/input.csv.manifest");
        $this->assertFileExists($tmpDir . "/data/out/tables/input_2.csv");
        $this->assertFileExists($tmpDir . "/data/out/tables/input_2.csv.manifest");
    }

    public function testJustReplaceHeader()
    {
        $tmp = new Temp();
        $tmp->setPreserveRunFolder(true);
        $tmpDir = $this->prepareTmpDir($tmp);

        $header = ["Obchodnik","Team","Manager","Mesto","Region","Marze","Bonus","Marze","Bonus","Marze","Bonus","Marze","Bonus"];
        $config = [
            "transpose" => true,
            "filename" => "input.csv",
            "header_rows_count" => 2,
            "header_column_names" => $header
        ];

        $processor = new Transpose($tmpDir . '/data');
        $processor->process($config);

        $finder = new Finder();
        $tables = (array) $finder->files()->in($tmpDir . "/data/out/tables/")->name('*.csv')->sortByName()->getIterator();

        $this->assertEquals(2, count($tables));
        $this->assertFileExists($tmpDir . "/data/out/tables/input.csv");
        $this->assertFileExists($tmpDir . "/data/out/tables/input.csv.manifest");
        $this->assertFileExists($tmpDir . "/data/out/tables/input_2.csv");
        $this->assertFileExists($tmpDir . "/data/out/tables/input_2.csv.manifest");
        $resultCsv = new CsvFile($tmpDir . "/data/out/tables/input.csv");
        $this->assertEquals($header, $resultCsv->getHeader());
    }
}
