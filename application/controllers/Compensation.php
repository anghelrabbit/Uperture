<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Compensation extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('model_compensation', 'M_compensation');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_structure', 'M_structure');
        $this->load->model('model_payslip', 'M_payslip');
    }

    public function OrganizeCompensation() {
        $selected_departments = (array) json_decode($this->input->post('selected_departments'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_departments'));
        $selectd_profileno = (array) json_decode($this->input->post('selectd_profileno'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselectd_profileno'));
        $emp_restrict = (array) json_decode($this->input->post('emp_restricts'));
        $onemonth = $this->input->post('onemonth');
        $batch_params = array(
            'payschedyear' => $this->input->post('year'),
            'recover' => 0
        );
        if ($onemonth != 0) {
            $explode_onemonth = explode('+', $onemonth);
            $batch_params['payschedmonth'] = $explode_onemonth[1];
        }
        $result = false;

        $batch = $this->M_compensation->FetchPayrollBatches($batch_params);
            $emp_data = array();
        if ((count($selected_departments) != 0 || count($selectd_profileno) != 0) && count($batch) > 0) {
            $result = true;
            $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);

            $column_index = ($this->input->post('category') == 0) ? 'lastname' : 'firstname';
            $column_array = array(0 => $column_index, 1 => "asc");
            $emp = $this->M_employee->FetchEmployeeTable(0, $where_selected, $column_array, array('status' => 0));
            foreach ($emp as $val) {
                $organize_batch = $this->OrganizeBatches($val->profileno, $batch, $emp_restrict);
                $where_batches = $organize_batch['sql'];
                $emp_batches = $organize_batch['emp_batches'];
                $exclude_batch = $organize_batch['exclude_batch'];
                $sql_statement = str_replace('profileno_emp', $val->profileno, $where_batches);
                $emp_adjutments = $this->M_compensation->FetchEmployeeAdjustments($sql_statement);
                $emp_compensate = $this->EmployeeMonthlyAdjustments($emp_adjutments, $emp_batches, $val, $exclude_batch);
                $emp_data[$val->comID . "*" . $val->locID][] = array(
                    'compensation' => $emp_compensate,
                    'employee' => $val
                );
            }
        } else {
            $result = false;
        }

        echo json_encode(array('result' => $result, 'emp_data' => $emp_data));
    }

    public function OrganizeBatches($profileno, $batches_raw, $restrict) {
        $where_batches = '';
        $month_color = array('9BC2E6', 'F4B084', 'A9D08E', 'C9C9C9', '8497B0', 'FFE699', '92D050', '9933FF', '339933', 'FCE4D6', '4472C4', '70AD47');
        $exclude_month = array();
        $emp_batches = array();
        foreach ($batches_raw as $val) {
            if (isset($restrict[$profileno])) {
                $arr = (object) $restrict[$profileno];
                $month_num = intval(date('m', strtotime($val->payschedmonth . ' 01, 2021')));
                if (isset($arr->{strval($month_num)})) {
                    $exclude_month[$val->batchcode] = $val->batchcode;
                }
            }

            $where_batches .= ($where_batches != '') ? ' UNION ' : '';
            $where_batches .= "(SELECT `batchcode`, `profileno`, `name`, `grouptype`, sum(amount)as amount FROM tbl_payroll_adjustments use index (`idx_batch_profileno`) WHERE 
                `profileno` = 'profileno_emp' AND `batchcode` = '" . $val->batchcode . "' GROUP BY `name`)";
            $month_num = (date('m', strtotime($val->payschedmonth . ' 01, 2021')) - 1);
            $month_column = (($month_num * 7) + 6);
            $color = $month_color[intval(date('m', strtotime($val->payschedmonth))) - 1];
            $emp_batches[$val->batchcode] = array('month' => $val->payschedmonth, 'schedtype' => $val->payschedtype, 'column' => $month_column, 'color' => $color);
        }
        return array('sql' => $where_batches, 'emp_batches' => $emp_batches, 'exclude_batch' => $exclude_month);
    }

    public function OrganizeEmpPayroll($emp, $batches, $monthly_compensation) {
        $where = '';
        $emp_compensation = array();

        foreach ($batches as $batch => $val) {
            $where .= ($where != '') ? ' UNION ' : '';
            $where .= "(SELECT `batchcode`,`profileno`,`basic`,`basicnet` FROM tbl_payroll use index (`idx_profileno_batchcode`) WHERE 
`profileno` = '" . $emp->profileno . "' AND `batchcode` = '" . $batch . "' AND `paytype`='" . $val['schedtype'] . "' AND payrollstatus = 1)";
            if (isset($emp_compensation[$val['month']]) == false) {
                $monthly_compensation['column'] = $val['column'];
                $emp_compensation[$val['month']] = $monthly_compensation;
            }
        }

        return array('sql' => $where, 'compensation_month' => $emp_compensation);
    }

    public function EmployeeMonthlyAdjustments($adjusmtents, $emp_batches, $emp, $exclude) {
        $monthly_compensation = array('holiday_pay' => 0, 'gross' => 0, 'benefits' => 0, 'tax' => 0, 'net_taxable' => 0, 'Tardiness' => 0, 'Absent' => 0, 'Undertime' => 0, 'column' => 0, 'color' => '');
        $anual_compensation = array('holiday_pay' => 0, 'gross' => 0, 'benefits' => 0, 'tax' => 0, 'net_taxable' => 0, 'Tardiness' => 0, 'Absent' => 0, 'Undertime' => 0, 'basic' => 0);
        $payroll_sql = $this->OrganizeEmpPayroll($emp, $emp_batches, $monthly_compensation);
        $payroll = $this->M_compensation->FetchBatchPayroll($payroll_sql['sql']);
        $payroll_batches = array();
        $emp_compensation = $payroll_sql['compensation_month'];
        foreach ($payroll as $val) {
            if (!isset($exclude[$val->batchcode])) {

                $payroll_batches[$val->batchcode] = $val->batchcode;
                $emp_compensation[$emp_batches[$val->batchcode]['month']]['gross'] += $val->basic;
                $emp_compensation[$emp_batches[$val->batchcode]['month']]['net_taxable'] += $val->basicnet;
                $anual_compensation['gross'] += $val->basic;
                $anual_compensation['net_taxable'] += $val->basicnet;
                $anual_compensation['basic'] += $val->basic;

                $emp_compensation[$emp_batches[$val->batchcode]['month']]['color'] = $emp_batches[$val->batchcode]['color'];
            }
        }
        foreach ($adjusmtents as $val) {
            if (!isset($exclude[$val->batchcode]) && isset($payroll_batches[$val->batchcode])) {
                $month = $emp_batches[$val->batchcode]['month'];
                if ($val->grouptype == 'HOLIDAY' || $val->grouptype == 'ADJUSTMENTS' || $val->grouptype == 'OTHER INCENTIVES') {
//                $emp_compensation[$month]['holiday_pay'] += ($val->grouptype == 'HOLIDAY') ? $val->amount : 0;
//                $anual_compensation['holiday_pay'] += ($val->grouptype == 'HOLIDAY') ? $val->amount : 0;
                    $emp_compensation[$month]['gross'] += $val->amount;
                    $anual_compensation['gross'] += $val->amount;
                }
                if ($val->grouptype == 'CONTRIBUTIONS') {
                    if ($val->name != 'TAX Deduction') {
                        $emp_compensation[$month]['benefits'] += $val->amount;
                        $anual_compensation['benefits'] += $val->amount;
                    } else {
                        $emp_compensation[$month]['tax'] += $val->amount;
						$emp_compensation[$emp_batches[$val->batchcode]['month']]['net_taxable'] += $val->amount;
                        $anual_compensation['tax'] += $val->amount;
                    }
                }
                if ($val->grouptype == 'TIME') {
                    if ($val->name == 'Tardiness' || $val->name == 'Absent' || $val->name == 'Undertime') {
                        $amount = $val->amount;

                        if ($amount > 0) {
                            $amount = $amount * (-1);
                        }
					
                        $emp_compensation[$month][$val->name] += $amount;
                        $anual_compensation[$val->name] += $amount;
//                        if ($val->name == 'Undertime') {
                        $emp_compensation[$month]['gross'] += $amount;
                        $anual_compensation['gross'] += $amount;
//                        }
                    } else if ($val->name == 'Night Differential') {
                        $emp_compensation[$month]['gross'] += $val->amount;
                        $anual_compensation['gross'] += $val->amount;
                    }
                }
            }
        }

        return array('monthly' => $emp_compensation, 'anual' => $anual_compensation);
    }

    public function GenerateCompensationReport() {
        $emps = (array) json_decode($this->input->post('compensate_emps'));
        $year = date('Y', strtotime($this->input->post('compensate_year') . '-01-01'));
        $spreadsheet = new Spreadsheet();
        $this->CompensationBody($emps, $spreadsheet, $year);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . 'Compensation Report of ' . $year . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function CompensationExcelHeader($sheet, $company, $year) {
        $month = 1;
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
        while ($month <= 14) {
            $month_name = ($month < 13) ? date("M", strtotime($year . '-' . $month . '-01')) . "-" . $year : 'TOTAL COMPENSATION FOR THE YEAR';
            $month_name = ($month == 14) ? '13TH MONTH PAY' : $month_name;
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
            $month++;
            $column += 7;
        }
        $sheet->getStyle('A2:DG3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:DG3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function CompensationBody($emps, $spreadsheet, $year) {
        $tab = 0;
        foreach ($emps as $com => $val) {
            $explode_structure = explode('*', $com);
            $comp = $this->M_structure->FetchStructureName(array('refno' => $explode_structure[0]), 'tbl_company');
            $explode_comp = explode(' ', $comp[0]->name);
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($tab);
            $sheet = $spreadsheet->getActiveSheet($tab);
            $sheet->setTitle($explode_comp[0]);
            $this->CompensationExcelHeader($sheet, $comp, $year);
            $grand_total = array();
            $rowindex = 5;
            foreach ($val as $row) {
                $sheet->setCellValueByColumnAndRow(1, $rowindex, $row->employee->lastname);
                $sheet->setCellValueByColumnAndRow(2, $rowindex, $row->employee->firstname);
                $sheet->setCellValueByColumnAndRow(3, $rowindex, $row->employee->midname);
                $sheet->setCellValueByColumnAndRow(4, $rowindex, $row->employee->suffix);
                $sheet->setCellValueByColumnAndRow(5, $rowindex, $row->employee->taxno);
                $anual = array(0, 0, 0, 0, 0, 0, 0);

                foreach ($row->compensation->monthly as $emp_compensation) {
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
                for ($cv = 0; $cv <= 6; $cv++) {
                    $sheet->setCellValueByColumnAndRow($cv + 90, $rowindex, number_format(floatval($anual[$cv]), 2));
                    (isset($grand_total[$cv + 90])) ? $grand_total[$cv + 90] += $anual[$cv] : $grand_total[$cv + 90] = $anual[$cv];
                }
                //13th month
                $_13thMonth = ($row->employee->ratecom == 'Monthly') ? ($row->compensation->anual->basic / 12) : (($row->compensation->anual->basic / 12) + $row->compensation->anual->Tardiness) + $row->compensation->anual->Undertime;
                $_13thmonth_partial = $_13thMonth / 2;
                $_13month_data = array(97 => $_13thmonth_partial, 98 => $_13thmonth_partial, 99 => $_13thMonth, 100 => ($row->compensation->anual->gross + $_13thMonth), 101 => $_13thMonth, 104 => $row->compensation->anual->benefits, 105 => ($row->compensation->anual->gross - $row->compensation->anual->benefits));
                foreach ($_13month_data as $key => $value) {
                    $sheet->setCellValueByColumnAndRow($key, $rowindex, number_format(floatval($value), 2));
                    (isset($grand_total[$key])) ? $grand_total[$key] += $_13month_data[$key] : $grand_total[$key] = $_13month_data[$key];
                }

                $rowindex++;
            }
            foreach ($grand_total as $key => $value) {
                $sheet->setCellValueByColumnAndRow($key, $rowindex, number_format(floatval(($value)), 2));
            }
            $tab++;
            $spreadsheet->getActiveSheet()->getStyle('CL1:CR' . ($rowindex - 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('695D97');
            $spreadsheet->getActiveSheet()->getStyle('CS1:CU' . ($rowindex - 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('5B9BD5');
            $spreadsheet->getActiveSheet()->getStyle('CV1:DF' . ($rowindex - 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
            $spreadsheet->getActiveSheet()->getStyle('A' . $rowindex . ':DF' . $rowindex)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            $sheet->getStyle('A1:' . $sheet->getCellByColumnAndRow(110, $rowindex)->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->freezePane('F5');
        }
    }

    public function OrganizeJobPositionNet() {
        $worksched_in = $this->input->post('worksched_from');
        $worksched_out = $this->input->post('worksched_to');
        $batches = $this->M_compensation->FetchPayrollBatches(array('payschedfrom' => $worksched_in, 'payschedto' => $worksched_out, 'recover' => 0));
        $where_batch = '';
        $result = false;
        $divs = array();
        foreach ($batches as $val) {
            $where_batch .= ($where_batch == '') ? "batchcode = '" . $val->batchcode . "' " : " OR batchcode = '" . $val->batchcode . "'";
        }
        $where_batch = '(' . $where_batch . ')';
        if ($where_batch != '()') {
            $payroll = $this->M_compensation->FetchEmployeesInPayroll($where_batch);
            foreach ($payroll as $val) {
                $result = true;
                (isset($divs[$val->comcode]['divs'][$val->divcode])) ? $divs[$val->comcode]['divs'][$val->divcode]['total'] += $val->basicnet : $divs[$val->comcode]['divs'][$val->divcode]['total'] = $val->basicnet;
                if (isset($divs[$val->comcode]['divs'][$val->divcode]['jobs'][$val->jobcode])) {
                    $divs[$val->comcode]['divs'][$val->divcode]['jobs'][$val->jobcode]['net'] += $val->basicnet;
                } else {
                    $divs[$val->comcode]['name'] = $val->comname;
                    $divs[$val->comcode]['divs'][$val->divcode]['divname'] = $val->divname;
                    $divs[$val->comcode]['divs'][$val->divcode]['jobs'][$val->jobcode] = array('net' => $val->basicnet, 'jobname' => $val->jobposition);
                }
            }
        }
        echo json_encode(array('result' => $result, 'emp_data' => $divs));
    }

    public function GenerateJobPosNetReport() {
        $divs = (array) json_decode($this->input->post('jobpos_net_emps'));
        $worksched_in = $this->input->post('worksched_from');
        $worksched_out = $this->input->post('worksched_to');
        $spreadsheet = new Spreadsheet();
        $this->JobPosBody($divs, $spreadsheet, $worksched_in, $worksched_out);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . 'Job Position Net Pay' . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function JobPosBody($divs, $spreadsheet, $worksched_in, $worksched_out) {
        $tab = 0;
        foreach ($divs as $index) {
            $rowindex = 3;
            $spreadsheet->createSheet();
            $explode_comp = explode(' ', $index->name);
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($tab);
            $sheet = $spreadsheet->getActiveSheet($tab);
            $sheet->setTitle($explode_comp[0]);
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValueByColumnAndRow(1, 1, $index->name);
            $sheet->setCellValueByColumnAndRow(1, 2, "Pay-period of: " . date('M d', strtotime($worksched_in)) . " - " . date('M d, Y', strtotime($worksched_out)));
            foreach ($index->divs as $val) {
                $sheet->setCellValueByColumnAndRow(1, $rowindex, $val->divname);
                $sheet->mergeCells('A' . $rowindex . ':D' . $rowindex);
                $sheet->mergeCells('E' . $rowindex . ':G' . $rowindex);
                $sheet->getStyle('A' . $rowindex . ':G' . $rowindex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValueByColumnAndRow(5, $rowindex, 'TOTAL: ' . number_format(floatval($val->total), 2));
                $sheet->getStyle('A' . $rowindex . ':G' . $rowindex)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
                $sheet->getStyle('A' . $rowindex . ':' . $sheet->getCellByColumnAndRow(7, $rowindex)->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THICK);
                $rowindex++;
                $row_start = $rowindex;
                foreach ($val->jobs as $row) {
                    $sheet->mergeCells('A' . $rowindex . ':D' . $rowindex);
                    $sheet->mergeCells('E' . $rowindex . ':G' . $rowindex);
                    $sheet->setCellValueByColumnAndRow(1, $rowindex, $row->jobname);
                    $sheet->setCellValueByColumnAndRow(5, $rowindex, number_format(floatval($row->net), 2));
                    $rowindex++;
                }
                $sheet->getStyle('A' . $row_start . ':G' . ($rowindex - 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C5D9F1');
                $sheet->getStyle('A' . $row_start . ':' . $sheet->getCellByColumnAndRow(7, ($rowindex - 1))->getCoordinate())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $rowindex += 2;
            }
            $tab++;
        }
    }

}
