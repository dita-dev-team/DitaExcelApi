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
}