<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 8/2/18
 * Time: 10:48 AM
 */

class ParserInnerTest extends TestCase
{
    public function testCourseTitleFormat()
    {
        $res = \App\Utilities\ExcelParser::formatCourseTitle('AAA111');
        self::assertEquals('AAA-111', $res);
        $res = \App\Utilities\ExcelParser::formatCourseTitle('AAAA111');
        self::assertEquals('AAAA-111', $res);
    }

    public function testSanitize()
    {
        $result = \App\Utilities\ExcelParser::sanitize('ACS101A');
        $this->assertCount(1, $result);
        $result = \App\Utilities\ExcelParser::sanitize('ACS101A/B');
        $this->assertCount(2, $result);
        $result = \App\Utilities\ExcelParser::sanitize('ACS101A/ACS113A');
        $this->assertCount(2, $result);
        $result = \App\Utilities\ExcelParser::sanitize('ACS101/ACS113A');
        $this->assertCount(2, $result);
        $result = \App\Utilities\ExcelParser::sanitize('MUS119/219/319/419');
        $this->assertCount(4, $result);
        $result = \App\Utilities\ExcelParser::sanitize('MUS119/219/319/419A');
        $this->assertCount(4, $result);
        $result = \App\Utilities\ExcelParser::sanitize('ACS261/MIS224A');
        $this->assertCount(2, $result);
    }
}