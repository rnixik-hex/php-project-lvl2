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
            common: {
              + follow: false
                setting1: Value 1
              - setting2: 200
              - setting3: true
              + setting3: null
              + setting4: blah blah
              + setting5: {
                    key5: value5
                }
                setting6: {
                    doge: {
                      - wow: 
                      + wow: so much
                    }
                    key: value
                  + ops: vops
                }
            }
            group1: {
              - baz: bas
              + baz: bars
                foo: bar
              - nest: {
                    key: value
                }
              + nest: str
            }
          - group2: {
                abc: 12345
                deep: {
                    id: 45
                }
            }
          + group3: {
                fee: 100500
                deep: {
                    id: {
                        number: 45
                    }
                }
            }
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
            common: {
              + follow: false
                setting1: Value 1
              - setting2: 200
              - setting3: true
              + setting3: null
              + setting4: blah blah
              + setting5: {
                    key5: value5
                }
                setting6: {
                    doge: {
                      - wow: 
                      + wow: so much
                    }
                    key: value
                  + ops: vops
                }
            }
            group1: {
              - baz: bas
              + baz: bars
                foo: bar
              - nest: {
                    key: value
                }
              + nest: str
            }
          - group2: {
                abc: 12345
                deep: {
                    id: 45
                }
            }
          + group3: {
                fee: 100500
                deep: {
                    id: {
                        number: 45
                    }
                }
            }
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
