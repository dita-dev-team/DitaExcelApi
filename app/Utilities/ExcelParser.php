<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 15/07/17
 * Time: 22:45
 */

namespace App\Utilities;


use App\Unit;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;


class ExcelParser
{
    STATIC $i = 0;
    STATIC $j = 0;
	STATIC $shift = '';

    public static function copyToDatabase($path)
    {
        Excel::load($path, function (LaravelExcelReader $reader) {
            $reader->noHeading();
            $reader->each(function ($sheet) {
                $title = $sheet->getTitle();
                ExcelParser::$i = 0;
                //echo "${title}\n";
                $sheet->each(function ($row) use ($sheet) {
                    ExcelParser::$j = 0;
                    $row->each(function ($cell) use ($sheet){
                        $cell = trim($cell);
                        if (empty($cell) || strpos(strtolower($cell), 'semester') !== false) {
                            ExcelParser::$j++;
                            return;
                        }
                        $pattern = "/(?:[a-zA-Z]{3,4}[\d]{3}|[a-zA-Z]{3,4}[\s]+[\d]{3}|[a-zA-Z]{3,4}-[\d]{3})/i";
                        //echo ExcelParser::$i . ' ' . ExcelParser::$j;
                        if (preg_match($pattern, $cell) == 1 && ExcelParser::$j > 0) {
                            //echo "Found ${cell}\n";
                            $details = self::getDetails($sheet);
                            $names = self::sanitize($cell);
                            foreach ($names as $name) {
                                $unit = new Unit([
                                    'name' => self::formatCourseTitle(trim($name)),
                                    'room' => $details['room'],
                                    'date' => $details['dateTime'],
                                    'shift' => $details['shift']
                                ]);
                                $unit->save();
                            }
                        }
                        ExcelParser::$j++;
                    });
                    ExcelParser::$i++;
                });
            });
            //Log::info('TOTAL SHEETS: ' . $total);
        });
    }

    public static function split($text)
    {
        if (strpos($text, '-') !== false) {
            return explode("-", $text);
        } else if (strpos($text, ' ') !== false) {
            return explode(" ", $text);
        } else {
            $init_len = preg_match('/^[a-zA-Z]{3}\d/i', $text) == 1 ? 3 : 4;
            return array(substr($text, 0, $init_len), substr($text, $init_len));
        }
    }

    public static function getDetails(&$sheet)
    {
	    self::$shift     = self::getShift( $sheet->getTitle() );
        $dateTimeDetails = self::getDateTimeDetails($sheet);
        $dateTimeDetails = self::stringToDate($dateTimeDetails);
        $dateTimeDetails->subHours(2); // all exams have a duration of two hours
        $room = $sheet->get(ExcelParser::$i)->get(0);
        if (is_null($room)) {
            $room = 'NO ROOM';
        }
        $details = [
	        'dateTime' => $dateTimeDetails,
	        'shift'    => self::$shift,
	        'room'     => $room
        ];

        return $details;

    }

    public static function getDateTimeDetails(&$sheet)
    {
        $row = ExcelParser::$i;
        $col = ExcelParser::$j;

        if(is_null($sheet)) {
            echo "Sheet is null\n";
            return null;
        }

        $match = array();
        $pattern = "/(?:-([\d]+.[\d]+[apm]+))/i";
        for ($i = $row; $i >= 0; $i--) {

            //$cell = $sheet->get($i)->get($col);
            $_row = $sheet->get($i);

            if (is_null($_row)) {
                continue;
            }

            $cell = $_row->get($col);

            if (is_null($cell)) {
                continue;
            }

            if (preg_match($pattern, $cell, $match) == 1) {
                //echo "start\n";
                //echo "row: ${i} col: ${col}\n";
                $date = self::getDate($sheet, $i);
                if (!empty($date)) {
                    //echo $date . ' ' . strtolower($match[1]) . "\n";
                    return $date . ' ' . strtolower($match[1]);
                }
            }
        }

        return null;
    }

    public static function getDate(&$sheet, $row)
    {
        $row--;
        $col = ExcelParser::$j;
        $match = array();
        $pattern = "/[\w]+day[\s]+([\d]+\/[\d]+\/[\d]+)/i";

        for ($j = $col; $j >= 0; $j--) {
            $cell = trim($sheet->get($row)->get($j));

            if (preg_match($pattern, $cell, $match) == 1) {
                //echo $match[1]."\n";
                return $match[1];
            }
        }

        return null;
    }

    public static function stringToDate($string)
    {
        $dateTime = null;
        if (preg_match("/[\d]+\/[\d]+\/[\d]{2}[\s]+[\d]+:[\d]+[amp]+/i", $string) == 1) {
            $dateTime = \DateTime::createFromFormat('d/m/y g:ia', $string);
        } else if (preg_match("/[\d]+\/[\d]+\/[\d]{4}[\s]+[\d]+:[\d]+[amp]+/i", $string) == 1) {
            $dateTime = \DateTime::createFromFormat('d/m/Y g:ia', $string);
        } else if (preg_match("/[\d]+\/[\d]+\/[\d]{2}[\s]+[\d]+\.[\d]+[amp]+/i", $string) == 1) {
            $dateTime = \DateTime::createFromFormat('d/m/y g.ia', $string);
        } else if (preg_match("/[\d]+\/[\d]+\/[\d]{4}[\s]+[\d]+\.[\d]+[amp]+/i", $string) == 1) {
            $dateTime = \DateTime::createFromFormat('d/m/Y g.ia', $string);
        }
        return Carbon::createFromTimestamp($dateTime->getTimestamp());
    }

    public static function getShift($string)
    {
        $string = strtolower($string);
        if (strpos($string, 'athi') !== false) {
            return 'athi';
        } elseif (strpos($string, 'evening') !== false) {
            return 'evening';
        } else {
            return 'day';
        }
    }

    public static function sanitize($string)
    {
        // remove any whitespaces
        $string = preg_replace("/\s/", '', $string);
        if (strpos($string, '/') != false) {
            //echo $string . "\n";
            $course_codes = array();
            if (preg_match("/[a-z]{3,4}[\d]{3}[a-z]{1}(?:\/[a-z]{3,4}[\d]{3}[a-z]{1})/i", $string) == 1) { // handle type YYY111A/YYY222A
                $course_codes = explode('/', $string);
            } else if (preg_match("/[a-z]{3,4}[\d]{3}[a-z]{1}\/[a-z]{1}(?:[\/]*|.{})/i", $string) == 1) { // handle type YYY111A/B
                $prefix = substr($string, 0, 6);
                $sections = explode('/', substr($string, 6));
                foreach ($sections as $section) {
                    array_push($course_codes, $prefix . $section);
                }
            } else if (preg_match("/[A-Z]{3,4}[\d]{3}(?:\/[\d]{3})*/i", $string) == 1) { // handle type YYY111/222/333/444
                $prefix = substr($string, 0, 3);
                $codes = explode('/', substr($string, 3));
                foreach ($codes as $code) {
	                $section = self::$shift == 'athi' ? 'A' : ( self::$shift == 'day' ? 'T' : 'X' );
	                array_push( $course_codes, $prefix . $code . $section );
                }
            }

            return $course_codes;

        } else {
            return array($string);
        }


    }

    public static function formatCourseTitle($text)
    {
        if (strpos($text, '-') !== false) {
            return $text;
        } else {
            $init_len = preg_match('/^[a-zA-Z]{3}\d/i', $text) == 1 ? 3 : 4;
            return substr($text, 0, $init_len) . '-' . substr($text, $init_len);
        }
    }


}