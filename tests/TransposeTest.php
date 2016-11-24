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
    private function prepareTmpDir()
    {
        $tmp = new Temp();
        $fs = new Filesystem();
        $fs->mkdir($tmp->getTmpFolder() . "/data/in/files");
        $fs->mkdir($tmp->getTmpFolder() . "/data/out/files");
        $fs->copy(__DIR__ . "/data/in/files/input.csv", $tmp->getTmpFolder() . "/data/in/files/input.csv");

        return $tmp->getTmpFolder();
    }

    public function testTranspose()
    {
        $config = [
            "filename" => "input.csv",
            "header_rows_count" => 2,
            "header_column_names" => ["Obchodnik","Team","Manager","Mesto","Region"],
            "header_transpose_row" => 1,
            "header_transpose_column_name" => "month",
            "header_sanitize" => true,
            "transpose_from_column" => 6
        ];
        $tmpDir = $this->prepareTmpDir();
        $processor = new Transpose($tmpDir . '/data');
        $processor->process($config);

        $finder = new Finder();
        $files = (array) $finder->files()->in($tmpDir . "/data/out/files/")->sortByName()->getIterator();

        $this->assertEquals(1, count($files));
        $this->assertFileEquals(__DIR__ . "/data/out/files/expected.csv", $tmpDir . "/data/out/files/input.csv");
    }

//    /**
//     * @expectedException \Keboola\Processor\Exception
//     * @expectedExceptionMessage No files found with tag 'tag5'.
//     */
//    public function testLastFileTag5()
//    {
//        $tmp = new Temp();
//        $this->prepareTmpDir($tmp);
//        $tmpDir = $tmp->getTmpFolder();
//        $lastFile = new LastFile($tmpDir . "/data/in/files/");
//        $lastFile->filterTag("tag5", $tmpDir . "/data/out/files/");
//    }
}
