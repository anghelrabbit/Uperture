<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MonthlyRestriction extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->load->model('model_employee', 'M_employee');
    }

    public function EmployeeRestrict() {
        $where = array();
        $column = $this->input->post('columns');
        $selected_departments = (array) json_decode($this->input->post('selected_department'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_department'));
        $selectd_profileno = (array) json_decode($this->input->post('selected_profilen'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselected_profilen'));
        $emp_restricts = array();

        if ($column[1]['search']['value'] != '') {
            $empname = $column[1]['search']['value'];
            $explode_name = explode('/', $empname);
            if (count($explode_name) > 3) {
                $where[$explode_name[0]] = $explode_name[1];
                $where[$explode_name[2]] = $explode_name[3];
                $emp_restricts = (array) json_decode($explode_name[4]);
            } else {
                $where[$explode_name[0]] = $explode_name[1];
                $emp_restricts = (array) json_decode($explode_name[2]);
            }
        }
        $column_index = ($this->input->post('category') == 0) ? 'lastname' : 'firstname';
        $column_array = array(0 => $column_index, 1 => "asc");
        $datax = array();
        $emp = array();
        $filter = 0;
        if (count($selected_departments) > 0 || count($selectd_profileno) > 0) {
            $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);
            $filter = $this->M_employee->EmployeeTableFilter($where_selected, '', $this->CleanArray($where));
            $emp = $this->M_employee->FetchEmployeeTable(1, $where_selected, $column_array, $where);
            foreach ($emp as $row) {
                $sub_array = array();
                $sub_array[] = $row->lastname . ", " . $row->firstname;
                for ($cv = 1; $cv <= 12; $cv++) {
                    $checked = 'checked';
                    if (isset($emp_restricts[$row->profileno])) {
                        $arr = (object) $emp_restricts[$row->profileno];
                        $month_num = $cv;
                        if (isset($arr->{strval($month_num)})) {
                            $checked = '';
                        }
                    }
                    $sub_array[] = '<input style="width:20px;height:20px;" type="checkbox" ' . $checked . ' onchange="restrictEmployee(' . "this,'" . $row->profileno . "'," . $cv . ')">';
                }

                $datax[] = $sub_array;
            }
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($emp),
            "recordsFiltered" => $filter,
            "data" => $datax,
        );
        echo json_encode($output);
    }

    public function GenerateOneMonthCompensation() {
        $emps = (array) json_decode($this->input->post('compensate_onemonth_emps'));
        $year = date('Y', strtotime($this->input->post('compensate_onemonth_year') . '-01-01'));
        $month = explode('+', $this->input->post('compensate_onemonth_month'));
        $spreadsheet = new Spreadsheet();
        $this->CompensationBody($emps, $spreadsheet, $year, $month[0]);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . 'Compensation Report of ' . $year . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function CompensationExcelHeader($sheet, $company, $year, $month) {
        $column = 6;
        $sub_header = array('Salary', 'Holiday Pay', 'Overtime Pay', 'Gross', 'Benefits', 'Tax', 'Net Taxable');
        $other_header = array('1ST HALF', '2ND HALF', 'TOTAL', 'Gross' . "\n" . 'Compensation' . "\n" . ' Income', 'Non-Taxable' . "\n" . ' 13TH MONTH', 'Holiday Pay', 'Overtime Pay', 'Non - Taxable' . "\n" . ' SSS, GSIS, ' . "\n" . 'PAGIBIG & Union Dues', 'Non Taxable ' . "\n" . 'Salaries & Other Forms', 'TAXABLE' . "\n" . ' BASIC SALARY', 'Tax Due', 'TAX Paid ' . "\n" . 'Jan- Nov.', 'Amount' . "\n" . ' Withheld - December', 'Over Wtax');
        $sheet->setCellValueByColumnAndRow(1, 3, 'Employee');
        $sheet->setCellValueByColumnAndRow(5, 3, 'TIN');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', $company[0]->name);
        $sheet->getColumnDimension($sheet->getCell('A3')->getColumn())->setWidth(15);
        $sheet->getColumnDimension($sheet->getCell('B3')->getColumn())->setWidth(15);
        $sheet->getColumnDimension($sheet->getCell('C3')->getColumn())->setWidth(15);
        $sheet->getColumnDimension($sheet->getCell('D3')->getColumn())->setWidth(6);
        $sheet->getColumnDimension($sheet->getCell('E3')->getColumn())->setWidth(15);
        $month_name = date("M", strtotime($year . '-' . $month . '-01')) . "-" . $year;
        $column_for_month = $sheet->getCellByColumnAndRow($column, 2)->getCoordinate();
        $added_column = ($month <= 13) ? 6 : 2;
        $merge_cells = $sheet->getCellByColumnAndRow(($column + $added_column), 2)->getCoordinate();
        $sheet->mergeCells($column_for_month . ':' . $merge_cells);
        $sheet->setCellValue($column_for_month, $month_name);
        $index = 0;
        $header_use = ($month <= 13) ? $sub_header : $other_header;
        foreach ($header_use as $val) {
            $sub_column = $column + $index;
            $sub_header_column = $sheet->getCellByColumnAndRow($sub_column, 3)->getCoordinate();
            $sheet->setCellValue($sub_header_column, $val);
            $sheet->getStyle($sub_header_column)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($sheet->getCell($sub_header_column)->getColumn())->setWidth(15);
            $sheet->getRowDimension(3)->setRowHeight(60);
            $index++;
        }
        $column += 7;
        $sheet->getStyle('A2:DG3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:DG3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function CompensationBody($emps, $spreadsheet, $year, $month) {
        $tab = 0;
        foreach ($emps as $com => $val) {
            $explode_structure = explode('*', $com);
            $comp = $this->M_structure->FetchStructureName(array('refno' => $explode_structure[0]), 'tbl_company');
            $explode_comp = explode(' ', $comp[0]->name);
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($tab);
            $sheet = $spreadsheet->getActiveSheet($tab);
            $sheet->setTitle($explode_comp[0]);
            $this->CompensationExcelHeader($sheet, $comp, $year, $month);
            $grand_total = array();
            $rowindex = 5;
            foreach ($val as $row) {
                $sheet->setCellValueByColumnAndRow(1, $rowindex, $row->employee->lastname);
                $sheet->setCellValueByColumnAndRow(2, $rowindex, $row->employee->firstname);
                $sheet->setCellValueByColumnAndRow(3, $rowindex, $row->employee->midname);
                $sheet->setCellValueByColumnAndRow(4, $rowindex, $row->employee->suffix);
                $sheet->setCellValueByColumnAndRow(5, $rowindex, $row->employee->taxno);
                $anual = array(0, 0, 0, 0, 0, 0, 0);
                $is_changed = 0;
                foreach ($row->compensation->monthly as $emp_compensation) {
                    if ($is_changed == 0) {
                        $is_changed = 1;
                        $emp_compensation->column = 6;
                    }
                    $column_row_start = $sheet->getCellByColumnAndRow($emp_compensation->column, 1)->getCoordinate();
                    $column_row_end = $sheet->getCellByColumnAndRow($emp_compensation->column + 6, $rowindex)->getCoordinate();
                    $data = array($emp_compensation->gross, $emp_compensation->holiday_pay, 0, $emp_compensation->gross, $emp_compensation->benefits, $emp_compensation->tax, $emp_compensation->net_taxable);
                    for ($cv = 0; $cv <= 6; $cv++) {
                        $sheet->setCellValueByColumnAndRow(($emp_compensation->column + $cv), $rowindex, number_format(floatval($data[$cv]), 2));

                        $anual[$cv] += $data[$cv];
                        (isset($grand_total[$emp_compensation->column + $cv])) ? $grand_total[$emp_compensation->column + $cv] += $data[$cv] : $grand_total[$emp_compensation->column + $cv] = $data[$cv];
                    }
                    if ($emp_compensation->color != '') {
                        $spreadsheet->getActiveSheet()->getStyle($column_row_start . ':' . $column_row_end)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($emp_compensation->color);
                    }
                }

                $rowindex++;
            }
            foreach ($grand_total as $key => $value) {
                $sheet->setCellValueByColumnAndRow($key, $rowindex, number_format(floatval(($value)), 2));
            }
            $tab++;
            $spreadsheet->getActiveSheet()->getStyle('A' . $rowindex . ':LF' . $rowindex)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            $sheet->getStyle('A1:' . $sheet->getCellByColumnAndRow(12, $rowindex)->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->freezePane('F5');
        }
    }

}
