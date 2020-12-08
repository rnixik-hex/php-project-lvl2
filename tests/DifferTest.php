<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @covers \Differ\Differ\genDiff
     * @covers \Differ\Differ\convertDiffToOutput
     * @covers \Differ\Differ\getDiff
     * @covers \Differ\Parsers\parseJson
     */
    public function testGenDiffJsonOk()
    {
        $actual = genDiff('tests/fixtures/1.json', 'tests/fixtures/2.json');
        $expected = <<<EXP
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        EXP;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Differ\Differ\genDiff
     * @covers \Differ\Differ\convertDiffToOutput
     * @covers \Differ\Differ\getDiff
     * @covers \Differ\Parsers\parseYaml
     */
    public function testGenDiffYamlOk()
    {
        $actual = genDiff('tests/fixtures/1.yml', 'tests/fixtures/2.yml');
        $expected = <<<EXP
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        EXP;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFile1()
    {
        $this->expectException(\Exception::class);
        genDiff('wrong_path.json', 'tests/fixtures/2.json');
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFile2()
    {
        $this->expectException(\Exception::class);
        genDiff('tests/fixtures/1.json', 'wrong_path.json');
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadExtension1()
    {
        $this->expectException(\Exception::class);
        genDiff('tests/fixtures/bad_extension.txt', 'tests/fixtures/2.json');
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadExtension2()
    {
        $this->expectException(\Exception::class);
        genDiff('tests/fixtures/1.json', 'tests/fixtures/bad_extension.txt');
    }
}
