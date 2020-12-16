<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @covers \Differ\Differ\genDiff
     * @covers \Differ\Differ\getDiffTree
     * @covers \Differ\Parsers\Json\parse
     * @covers \Differ\Formatters\Stylish\format
     * @covers \Differ\Formatters\Stylish\formatInner
     * @covers \Differ\Formatters\Stylish\formatValue
     */
    public function testGenDiffJsonStylishOk(): void
    {
        $actual = genDiff('tests/fixtures/1.json', 'tests/fixtures/2.json', 'stylish');
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
     * @covers \Differ\Differ\getDiffTree
     * @covers \Differ\Parsers\Yaml\parse
     * @covers \Differ\Formatters\Stylish\format
     * @covers \Differ\Formatters\Stylish\formatInner
     * @covers \Differ\Formatters\Stylish\formatValue
     */
    public function testGenDiffYamlStylishOk(): void
    {
        $actual = genDiff('tests/fixtures/1.yml', 'tests/fixtures/2.yml', 'stylish');
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
     * @covers \Differ\Differ\getDiffTree
     * @covers \Differ\Parsers\Json\parse
     * @covers \Differ\Formatters\Plain\format
     * @covers \Differ\Formatters\Plain\appendKeyToPath
     * @covers \Differ\Formatters\Plain\formatInner
     * @covers \Differ\Formatters\Plain\formatKeyWithPath
     * @covers \Differ\Formatters\Plain\formatValue
     */
    public function testGenDiffJsonPlainOk(): void
    {
        $actual = genDiff('tests/fixtures/1.json', 'tests/fixtures/2.json', 'plain');
        $expected = <<<EXP
        Property 'common.follow' was added with value: false
        Property 'common.setting2' was removed
        Property 'common.setting3' was updated. From true to null
        Property 'common.setting4' was added with value: 'blah blah'
        Property 'common.setting5' was added with value: [complex value]
        Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
        Property 'common.setting6.ops' was added with value: 'vops'
        Property 'group1.baz' was updated. From 'bas' to 'bars'
        Property 'group1.nest' was updated. From [complex value] to 'str'
        Property 'group2' was removed
        Property 'group3' was added with value: [complex value]
        EXP;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Differ\Differ\genDiff
     * @covers \Differ\Differ\getDiffTree
     * @covers \Differ\Parsers\Json\parse
     * @covers \Differ\Formatters\Json\format
     */
    public function testGenDiffJsonFormatJsonOk(): void
    {
        $actual = genDiff('tests/fixtures/1.json', 'tests/fixtures/2.json', 'json');
        $expected = <<<EXP
        [
            {
                "key": "common",
                "diff_type": "updated_children",
                "children": [
                    {
                        "key": "setting1",
                        "diff_type": "unchanged",
                        "old_value": "Value 1"
                    },
                    {
                        "key": "setting2",
                        "diff_type": "removed",
                        "old_value": 200
                    },
                    {
                        "key": "setting3",
                        "diff_type": "update",
                        "old_value": true,
                        "new_value": null
                    },
                    {
                        "key": "setting6",
                        "diff_type": "updated_children",
                        "children": [
                            {
                                "key": "key",
                                "diff_type": "unchanged",
                                "old_value": "value"
                            },
                            {
                                "key": "doge",
                                "diff_type": "updated_children",
                                "children": [
                                    {
                                        "key": "wow",
                                        "diff_type": "update",
                                        "old_value": "",
                                        "new_value": "so much"
                                    }
                                ]
                            },
                            {
                                "key": "ops",
                                "diff_type": "added",
                                "new_value": "vops"
                            }
                        ]
                    },
                    {
                        "key": "follow",
                        "diff_type": "added",
                        "new_value": false
                    },
                    {
                        "key": "setting4",
                        "diff_type": "added",
                        "new_value": "blah blah"
                    },
                    {
                        "key": "setting5",
                        "diff_type": "added",
                        "new_value": {
                            "key5": "value5"
                        }
                    }
                ]
            },
            {
                "key": "group1",
                "diff_type": "updated_children",
                "children": [
                    {
                        "key": "baz",
                        "diff_type": "update",
                        "old_value": "bas",
                        "new_value": "bars"
                    },
                    {
                        "key": "foo",
                        "diff_type": "unchanged",
                        "old_value": "bar"
                    },
                    {
                        "key": "nest",
                        "diff_type": "update",
                        "old_value": {
                            "key": "value"
                        },
                        "new_value": "str"
                    }
                ]
            },
            {
                "key": "group2",
                "diff_type": "removed",
                "old_value": {
                    "abc": 12345,
                    "deep": {
                        "id": 45
                    }
                }
            },
            {
                "key": "group3",
                "diff_type": "added",
                "new_value": {
                    "fee": 100500,
                    "deep": {
                        "id": {
                            "number": 45
                        }
                    }
                }
            }
        ]
        EXP;

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFile1(): void
    {
        $this->expectException(\Exception::class);
        genDiff('wrong_path.json', 'tests/fixtures/2.json', 'stylish');
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFile2(): void
    {
        $this->expectException(\Exception::class);
        genDiff('tests/fixtures/1.json', 'wrong_path.json', 'stylish');
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadExtension1(): void
    {
        $this->expectException(\Exception::class);
        genDiff('tests/fixtures/bad_extension.txt', 'tests/fixtures/2.json', 'stylish');
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadExtension2(): void
    {
        $this->expectException(\Exception::class);
        genDiff('tests/fixtures/1.json', 'tests/fixtures/bad_extension.txt', 'stylish');
    }
}
