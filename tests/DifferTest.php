<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @param string $file1
     * @param string $file2
     * @param string $format
     * @param string $diffFile
     * @throws \Exception
     *
     * @dataProvider genDiffOkDataProvider
     *
     * @covers       \Differ\Differ\genDiff
     * @covers       \Differ\Differ\getDiffTree
     * @covers       \Differ\Parsers\Json\parse
     * @covers       \Differ\Parsers\Yaml\parse
     * @covers       \Differ\Formatters\Stylish\format
     * @covers       \Differ\Formatters\Stylish\formatInner
     * @covers       \Differ\Formatters\Stylish\formatValue
     * @covers       \Differ\Formatters\Plain\format
     * @covers       \Differ\Formatters\Plain\appendKeyToPath
     * @covers       \Differ\Formatters\Plain\formatInner
     * @covers       \Differ\Formatters\Plain\formatKeyWithPath
     * @covers       \Differ\Formatters\Plain\formatValue
     * @covers       \Differ\Formatters\Json\format
     */
    public function testGenDiffOk(string $file1, string $file2, string $format, string $diffFile): void
    {
        $actualDiffContent = genDiff($this->getFullFixturePath($file1), $this->getFullFixturePath($file2), $format);
        $this->assertStringEqualsFile($this->getFullFixturePath($diffFile), $actualDiffContent);
    }

    public function genDiffOkDataProvider(): array
    {
        return [
            ['1.json', '2.json', 'stylish', 'diff_stylish.txt'],
            ['1.yml', '2.yml', 'stylish', 'diff_stylish.txt'],
            ['1.json', '2.json', 'plain', 'diff_plain.txt'],
            ['1.json', '2.json', 'json', 'diff_json.txt'],
        ];
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFile1(): void
    {
        $wrongPath = $this->getFullFixturePath('wrong_path.json');
        $this->expectExceptionMessage("First file '$wrongPath' is not readable");
        genDiff(
            $wrongPath,
            $this->getFullFixturePath('2.json')
        );
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFile2(): void
    {
        $wrongPath = $this->getFullFixturePath('wrong_path.json');
        $this->expectExceptionMessage("Second file '$wrongPath' is not readable");
        genDiff(
            $this->getFullFixturePath('1.json'),
            $wrongPath
        );
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadExtension1(): void
    {
        $this->expectExceptionMessage("Extension 'txt' is unsupported'");
        genDiff(
            $this->getFullFixturePath('bad_extension.txt'),
            $this->getFullFixturePath('2.json')
        );
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadExtension2(): void
    {
        $this->expectExceptionMessage("Extension 'txt' is unsupported'");
        genDiff(
            $this->getFullFixturePath('1.json'),
            $this->getFullFixturePath('bad_extension.txt')
        );
    }

    /**
     * @covers \Differ\Differ\genDiff
     */
    public function testGenDiffBadFormat(): void
    {
        $this->expectExceptionMessage("Format 'bad_format' is unsupported'");
        genDiff(
            $this->getFullFixturePath('1.json'),
            $this->getFullFixturePath('2.json'),
            'bad_format'
        );
    }

    private function getFullFixturePath(string $relativeFileName): string
    {
        return implode(DIRECTORY_SEPARATOR, ['tests', 'fixtures', $relativeFileName]);
    }
}
