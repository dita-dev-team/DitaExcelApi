<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 8/2/18
 * Time: 12:26 PM
 */

class ParserTestJan2018 extends TestCase
{
    public function test()
    {
        $path = storage_path('testing/excel-new3.xls');
        \App\Utilities\ExcelParser::copyToDatabase($path);
        $this->assertDatabaseHas('units', [
            'name' => 'ACS-404A'
        ])->assertDatabaseHas('units', [
            'name' => 'ACS-454A'
        ])->assertDatabaseHas('units', [
            'name' => 'ACS-451A'
        ])->assertDatabaseHas('units', [
            'name' => 'ENG-214T'
        ])->assertDatabaseHas('units', [
            'name' => 'PGM-614X'
        ])->assertDatabaseHas('units', [
            'name' => 'PEA-141T'
        ]);
    }

}