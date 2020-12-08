<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffPlain()
    {
        $actual = genDiff('tests/fixtures/1.json', 'tests/fixtures/2.json');
        $expected = <<<EXP
        - follow: false
          host: hexlet.io
        - proxy: 123.234.53.22
        - timeout: 50
        + timeout: 20
        + verbose: true
        
        EXP;

        $this->assertEquals($expected, $actual);
    }
}
