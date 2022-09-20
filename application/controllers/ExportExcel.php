<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportExcel
 *
 * @author MIS
 */
class ExportExcel extends MY_Controller {

    public function __construct() {
        parent::__construct();
     
    }

    public function Excel201Report() {
        $letters = range('A', 'R');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $header = array('EMP.NO.', 'DATE HIRED', 'LAST NAME', ',', 'GIVEN NAME', 'MIDDLE NAME',
            'TENURESHIP', 'EMPLOYMENT STATUS', 'POSITION', 'DESIGNATION', 'BIRTHDATE', 'AGE', 'GENDER', 'ADDRESS', 'CONTACT NO.',
            'CONTACT PERSON DURING EMERGENCY AND THEIR  CONTACT NO.', 'TRAININGS COMPLETED', 'LICENSE');
        $index = 0;
        foreach ($letters as $val) {
            $sheet->setCellValue($val . '7', $header[$index]);
            $sheet->mergeCells($val . '7:' . $val . '8');
            $index++;
        }
        $sheet->setCellValue('S7', 'Other Benfits');
        $sheet->setCellValue('Y7', 'GOVERNMENT BENEFITS');
        $sheet->setCellValue('AC7', 'ACCOUNTABILITIES');
        $sheet->setCellValue('S8', 'Incentive set up');
        $sheet->setCellValue('T8', 'Free meal');
        $sheet->setCellValue('U8', 'DTR Set up');
        $sheet->setCellValue('V8', 'PRINCIPAL INSURANCE');
        $sheet->setCellValue('W8', 'DEPENDENT INSURANCE');
        $sheet->setCellValue('X8', 'Others');
        $sheet->setCellValue('Y8', 'SSS NO.');
        $sheet->setCellValue('Z8', 'PHIC NO.');
        $sheet->setCellValue('AA8', 'PAGIBIG NO.');
        $sheet->setCellValue('AB8', 'TIN NO.');
        $sheet->setCellValue('AC8', 'BONDS');
        $sheet->setCellValue('AD8', 'EQUIPMENTS');
        $sheet->setCellValue('AE8', 'DEVICES');
        $sheet->setCellValue('AF8', 'OTHERS');
        $sheet->mergeCells('S7:X7');
        $sheet->mergeCells('Y7:AB7');
        $sheet->mergeCells('AC7:AF7');
        $sheet->setCellValueByColumnAndRow(1, 9, 'sample');
        $sheet->setCellValueByColumnAndRow(2, 9, 'sample');
        $sheet->setCellValueByColumnAndRow(3, 9, 'sample');
        $sheet->setCellValueByColumnAndRow(4, 9, 'sample');





        $writer = new Xlsx($spreadsheet);

        $filename = 'Employee 201';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file 
    }

  

}
