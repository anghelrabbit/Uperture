<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BankTransmittal extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('MY_Model');
        $this->load->model('model_payslip', 'M_payslip');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_structure', 'M_structure');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Payroll";
            $data['page'] = 'pages/menu/reports/bank_transmittal/bank_transmittal';
            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
                'assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css',
                'assets/vendors/bower_components/sweetalert/sweetalert.css',
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
                'assets/myjs/utilities/payperiod.js',
                'assets/myjs/utilities/structure.js',
                'assets/myjs/utilities/selecting_employees.js',
				'assets/myjs/utilities/compensation_monthly_restriction.js',
                'assets/myjs/reports/bank_transmittal/bank_transmittal.js',
            );

            $this->InspectUser('menu/reports/bank_transmittal/bank_transmittal', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function GenerateExcel() {
        $spreadsheet = new Spreadsheet();
        $emps = json_decode($this->input->post('emps'));
        $sched_in = $this->input->post('sched_in');
        $sched_out = $this->input->post('sched_out');
        $category = $this->input->post('category');
        $file_format = $this->input->post('file_format');
        $prepared_by = $this->input->post('prepared_by');
        $checked_by = $this->input->post('checked_by');
        $noted_by = $this->input->post('noted_by');
        $approved_by = $this->input->post('approved_by');
        $title = '';
        if ($file_format == 0) {
            if ($category == 0) {
                $checker = array(
                    'Prepared by:' => $prepared_by,
                    'Checked by:' => $checked_by,
                    'Noted by:' => $noted_by,
                    'Approved by:' => $approved_by,
                );
                $this->PayrollSummaryTable($spreadsheet, $emps, $sched_in, $sched_out, $checker);
                $title = 'Payroll Summary (' . $sched_in . " TO " . $sched_out . ')';
            } else {
                $this->BankTransmittalExcel($spreadsheet, $emps);
                $title = 'Bank Transmittal (' . $sched_in . "-" . $sched_out . ')';
            }
//        $sheet->freezePane('B14');
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        } else {
            $this->GeneratePdf($emps, $sched_in, $sched_out, $prepared_by, $checked_by, $noted_by, $approved_by);
        }
    }

    public function SetupBankTransmittal() {
        $selected_departments = (array) json_decode($this->input->post('selected_departments'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_departments'));
        $selectd_profileno = (array) json_decode($this->input->post('selectd_profileno'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselectd_profileno'));
        $from = $this->input->post('worksched_from');
        $to = $this->input->post('worksched_to');
        $result = false;
        $data = array();
        if (count($selected_departments) != 0 || count($selectd_profileno) != 0) {
            $result = true;
            $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);
            $column_index = ($this->input->post('category') == 0) ? 'lastname' : 'firstname';
            $column_array = array(0 => $column_index, 1 => "asc");
            $emp = $this->M_employee->FetchEmployeeTable(0, $where_selected, $column_array, array());
            foreach ($emp as $val) {
                $batches = $this->M_payslip->SpecificBatchcode($this->CleanArray(array('category' => $val->comID, 'payschedfrom' => $from, 'payschedto' => $to)));
                $emp_payroll = array(
                    'basic' => 0, 'absent_total' => 0, 'loans_total' => 0, 'ca_total' => 0, 'leave' => 0, 'incentives' => 0,
                    'adjustment' => 0, 'legal_holiday' => 0, 'special_holiday' => 0, 'night_diff' => 0, 'absent' => 0,
                    'late_undertime_absent' => 0, 'late' => 0, 'undertime' => 0, 'sss' => 0, 'phic' => 0, 'deduct' => 0,
                    'hdmf' => 0, 'wtx' => 0, 'gross' => 0, 'net' => 0, 'final_net' => 0, 'sss_loan' => 0, 'pag_ibig_loan' => 0
                );
                $basicnet = 0;
                foreach ($batches as $row) {
                    $payslip = $this->M_payslip->FetchPayslips($this->CleanArray(array('batchcode' => $row->batchcode, 'profileno' => $val->profileno)));
                    if (count($payslip) > 0) {
                        $emp_payroll = $this->FetchEmployeePayroll($emp_payroll, $val->profileno, $row->payschedtype, $row->batchcode);
                        $basicnet = $payslip[0]->basicnet;
                    }
                }
                $loc = ($val->locID == null || $val->locID == '') ? 'none' : $val->locID;
                $data[$val->comID . "*" . $loc][$val->depID][] = array(
                    'emp_data' => $val,
                    'payroll' => $emp_payroll,
                    'basicnet' => $basicnet
                );
            }
        }
        echo json_encode(array('result' => $result, 'data' => $data));
    }

    public function FetchEmployeePayroll($emp_payroll, $profileno, $paytype, $batchcode) {
        $gross_total = 0;
        $deduct_total = 0;
        $net_pay = 0;

        //HDMF = PAG-IBIG
        //PHIC = PhilHealth
        //WTX = TAX

        $payroll = $this->M_payslip->FetchPayroll($this->CleanArray(array('profileno' => $profileno, 'paytype' => $paytype, 'batchcode' => $batchcode)));
        $adjustment = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'ADJUSTMENTS', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $incentives = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'OTHER INCENTIVES', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');

        $loans = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'LOAN', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $ca = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'CASH ADVANCE', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $holidays = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'HOLIDAY', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $time = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'TIME', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $contributions = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'CONTRIBUTIONS', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');

        $loans_total = 0;
        $CA_total = 0;
        foreach ($incentives as $val) {
            $emp_payroll['incentives'] = $emp_payroll['incentives'] + number_format((float) $val->amount, 2, '.', '');
            $gross_total = $gross_total + $val->amount;
        }
        foreach ($holidays as $val) {
            if ($val->name == 'REGULAR HOLIDAY') {
                $emp_payroll['legal_holiday'] = $emp_payroll['legal_holiday'] + number_format((float) $val->amount, 2, '.', '');
            } else if ($val->name == 'SPECIAL NON WORKING HOLIDAY') {
                $emp_payroll['special_holiday'] = $emp_payroll['special_holiday'] + number_format((float) $val->amount, 2, '.', '');
            }
            $gross_total = $gross_total + $val->amount;
        }
        $late_undertime_absent = 0;
        foreach ($time as $val) {
            if ($val->name == 'Night Differential') {
                $emp_payroll['night_diff'] = $emp_payroll['night_diff'] + number_format((float) $val->amount, 2, '.', '');
            } else {
                if ($val->name == 'Tardiness') {
                    $emp_payroll['late'] = $emp_payroll['late'] + number_format((float) $val->amount, 2, '.', '');
                } else if ($val->name == 'Absent') {
                    $emp_payroll['absent'] = $emp_payroll['absent'] + number_format((float) $val->amount, 2, '.', '');
                } else if ($val->name == 'Undertime') {
                    $emp_payroll['undertime'] = $emp_payroll['undertime'] + number_format((float) $val->amount, 2, '.', '');
                } else if ($val->name == 'Leave Pay') {
                    $emp_payroll['leave'] = $emp_payroll['leave'] + number_format((float) $val->amount, 2, '.', '');
                }
                $gross_total = $gross_total + $val->amount;
                $emp_payroll['late_undertime_absent'] = $emp_payroll['late_undertime_absent'] + number_format((float) $val->amount, 2, '.', '');
            }
        }

        foreach ($contributions as $val) {
            if ($val->name == 'SSS Deduction') {
                $emp_payroll['sss'] = $emp_payroll['sss'] + number_format((float) $val->amount, 2, '.', '');
            } else if ($val->name == 'PhilHealth Deduction') {
                $emp_payroll['phic'] = $emp_payroll['phic'] + number_format((float) $val->amount, 2, '.', '');
            } else if ($val->name == 'TAX Deduction') {
                $emp_payroll['wtx'] = $emp_payroll['wtx'] + number_format((float) $val->amount, 2, '.', '');
            } else if ($val->name == 'HDMF Deduction') {
                $emp_payroll['hdmf'] = $emp_payroll['hdmf'] + number_format((float) $val->amount, 2, '.', '');
            }
            $deduct_total = $deduct_total + $val->amount;
        }
        foreach ($loans as $val) {
            if ($val->name == 'SSS SALARY LOAN' || $val->name == 'SSS CALAMITY LOAN') {
                $emp_payroll['sss_loan'] = number_format((float) ($emp_payroll['sss_loan'] + $val->amount), 2, '.', '');
            } else if ($val->name == 'PAG-IBIG SALARY LOAN' || $val->name == 'PAG-IBIG CALAMITY LOAN') {
                $emp_payroll['pag_ibig_loan'] = number_format((float) ( $emp_payroll['pag_ibig_loan'] + $val->amount), 2, '.', '');
            }
            $loans_total = $loans_total + $val->amount;
            $deduct_total = $deduct_total + $val->amount;
        }

        foreach ($ca as $val) {
            $CA_total = $CA_total + $val->amount;
//            $deduct_total = $deduct_total + $val->amount;
        }
        $total_adjustments = 0;
        foreach ($adjustment as $val) {
            $emp_payroll['adjustment'] = $emp_payroll['adjustment'] + number_format((float) $val->amount, 2, '.', '');
            $total_adjustments = $total_adjustments + $val->amount;
        }
        $emp_payroll['loans_total'] = number_format((float) ($emp_payroll['loans_total'] + $loans_total), 2, '.', '');
        $emp_payroll['ca_total'] = number_format((float) ( $emp_payroll['ca_total'] + $CA_total), 2, '.', '');



        if (count($payroll) > 0) {
            $paytype = 0;
            if ($payroll[0]->ratecom == 'Daily') {
                $paytype = number_format((float) ($payroll[0]->ratedaily * $payroll[0]->attended), 2, '.', '');
            } else if ($payroll[0]->ratecom == 'Monthly') {
                if ($payroll[0]->paytype == 'Weekly') {
                    $paytype = ((float) trim($payroll[0]->ratemonthly, ',')) / 2;
                } else if ($payroll[0]->paytype == 'Semi-Month') {
                    $paytype = ((float) str_replace(',', '', $payroll[0]->ratemonthly) / 2);
                } else {
                    $paytype = ((float) str_replace(',', '', $payroll[0]->ratemonthly));
                }
            }
            $gross_total = number_format((float) (($payroll[0]->basic + $gross_total)), 2, '.', '');
            $net_pay = number_format((float) ($gross_total - $deduct_total), 2, '.', '');

            $emp_payroll['gross'] = $emp_payroll['gross'] + $gross_total;
            $emp_payroll['deduct'] = $emp_payroll['deduct'] + $deduct_total;
            $emp_payroll['net'] = $emp_payroll['net'] + $net_pay;
            $emp_payroll['final_net'] = $emp_payroll['final_net'] + ($net_pay + $total_adjustments);
            $emp_payroll['basic'] = $paytype;
        }
        return $emp_payroll;
    }

    public function PayrollSummaryTable($spreadsheet, $emps, $sched_in, $sched_out, $checker) {
        $tab = 0;
        $grand_total = array(
            'name' => 'GRAND TOTAL', 'MONTHLY_RATE' => 0, 'BASIC_RATE' => 0, 'NIGHT_SHIFT_HOURS' => 0,
            'NIGHT_SHIFT_AMOUNT' => 0, 'LEGAL_HOLIDAY_HOURS' => 0, 'LEGAL_HOLIDAY_AMOUNT' => 0,
            'SPECIAL_HOLIDAY_HOURS' => 0, 'SPECIAL_HOLIDAY_AMOUNT' => 0, 'INCENTIVES' => 0, 'LATE_UNDERTIME_ABSENCES' => 0,
            'GROSS_SALARY' => 0, 'SSS' => 0, 'PHIC' => 0, 'HDMF' => 0, 'ITW' => 0, 'OTHERS' => 0,
            'SSS_LOAN' => 0, 'HDMF_LOAN' => 0, 'TOTAL_DEDUCTIONS' => 0, 'NET_PAY' => 0, 'ADJUSTMENTS' => 0, 'FINAL_NET' => 0
        );
        foreach ($emps as $com => $val) {
            $rowindex = 15;
            $cell = 14;
            $explode_structure = explode('*', $com);
            $comp = $this->M_structure->FetchStructureName(array('refno' => $explode_structure[0]), 'tbl_company');
            $loc = $this->M_structure->FetchStructureName(array('refno' => $explode_structure[1]), 'tbl_location');
            $loc_name = (count($loc) > 0) ? $loc[0]->name : '';
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($tab);
            $sheet = $spreadsheet->getActiveSheet($tab);
            $this->ParollExcelHeader($sheet);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
            $spreadsheet->getActiveSheet()->setTitle('Payroll Summary');
            $this->PayrollExcelTitle($sheet, $comp[0]->name, $loc_name, $sched_in, $sched_out);
            foreach ($val as $dep => $row) {
                $footer = array(
                    'name' => 'SUB TOTALS', 'MONTHLY_RATE' => 0, 'BASIC_RATE' => 0, 'NIGHT_SHIFT_HOURS' => 0,
                    'NIGHT_SHIFT_AMOUNT' => 0, 'LEGAL_HOLIDAY_HOURS' => 0, 'LEGAL_HOLIDAY_AMOUNT' => 0,
                    'SPECIAL_HOLIDAY_HOURS' => 0, 'SPECIAL_HOLIDAY_AMOUNT' => 0, 'INCENTIVES' => 0, 'LATE_UNDERTIME_ABSENCES' => 0,
                    'GROSS_SALARY' => 0, 'SSS' => 0, 'PHIC' => 0, 'HDMF' => 0, 'ITW' => 0, 'OTHERS' => 0,
                    'SSS_LOAN' => 0, 'HDMF_LOAN' => 0, 'TOTAL_DEDUCTIONS' => 0, 'NET_PAY' => 0, 'ADJUSTMENTS' => 0, 'FINAL_NET' => 0
                );

                $dept = $this->M_structure->FetchStructureName(array('refno' => $dep), 'tbl_departments');
                $deptname = (count($dept) > 0) ? $dept[0]->name : '';
                $this->PayrollDepartmentTitle($sheet, $deptname, $cell);
                foreach ($row as $emp) {
                    $styleArray = [
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];
                    $sheet->getStyle('A' . $rowindex . ':' . 'W' . $rowindex)->applyFromArray($styleArray);
                    $sheet->setCellValueByColumnAndRow(1, $rowindex, $emp->emp_data->lastname . ", " . $emp->emp_data->firstname . " " . $emp->emp_data->suffix . " " . $emp->emp_data->midname); //employee
                    $sheet->setCellValueByColumnAndRow(2, $rowindex, $emp->emp_data->ratemonth); //monthly rate
                    $sheet->setCellValueByColumnAndRow(3, $rowindex, number_format(floatval($emp->payroll->basic), 2)); //basic rate
                    $sheet->setCellValueByColumnAndRow(4, $rowindex, ''); //night diff hours
                    $sheet->setCellValueByColumnAndRow(5, $rowindex, $emp->payroll->night_diff); //night diff amount
                    $sheet->setCellValueByColumnAndRow(6, $rowindex, ''); //legal holiday hours
                    $sheet->setCellValueByColumnAndRow(7, $rowindex, $emp->payroll->legal_holiday); //legal holiday amount
                    $sheet->setCellValueByColumnAndRow(8, $rowindex, ''); //special holiday hours
                    $sheet->setCellValueByColumnAndRow(9, $rowindex, $emp->payroll->special_holiday); //special holiday amount
                    $sheet->setCellValueByColumnAndRow(10, $rowindex, $emp->payroll->incentives); //incentives
                    $sheet->setCellValueByColumnAndRow(11, $rowindex, ($emp->payroll->late_undertime_absent)); //late,undertime,absences
                    $sheet->setCellValueByColumnAndRow(12, $rowindex, $emp->payroll->gross); //gross salary
                    $sheet->setCellValueByColumnAndRow(13, $rowindex, $emp->payroll->sss); //sss
                    $sheet->setCellValueByColumnAndRow(14, $rowindex, $emp->payroll->phic); //phic
                    $sheet->setCellValueByColumnAndRow(15, $rowindex, $emp->payroll->hdmf); //hdmf
                    $sheet->setCellValueByColumnAndRow(16, $rowindex, $emp->payroll->wtx); //itw
                    $sheet->setCellValueByColumnAndRow(17, $rowindex, $emp->payroll->ca_total); //others
                    $sheet->setCellValueByColumnAndRow(18, $rowindex, $emp->payroll->sss_loan); //sss loan
                    $sheet->setCellValueByColumnAndRow(19, $rowindex, $emp->payroll->pag_ibig_loan); //hdmf loan
                    $sheet->setCellValueByColumnAndRow(20, $rowindex, $emp->payroll->deduct); //total deduction
                    $sheet->setCellValueByColumnAndRow(21, $rowindex, $emp->payroll->net); //net pay
                    $sheet->setCellValueByColumnAndRow(22, $rowindex, $emp->payroll->adjustment); //other adjustments
                    $sheet->setCellValueByColumnAndRow(23, $rowindex, $emp->payroll->final_net); //finat net pay
                    $footer = $this->FooterValues($footer, $emp);
                    $grand_total = $this->FooterValues($grand_total, $emp);
                    $rowindex++;
                }
                $this->PayrollFooter($sheet, $footer, ($rowindex), false, $checker);
                $rowindex += 3;
                $cell = ($rowindex - 1);
            }
            $sheet->getStyle("A14:W" . $rowindex)->getFont()->setSize(8);
            $this->PayrollFooter($sheet, $grand_total, ($rowindex), true, $checker);
            $rowindex += 3;

            $tab++;
        }
//        $objPHPExcel->getActiveSheet()->getStyle("F1:G1")->getFont()->setSize(16);
    }

    public function FooterValues($footer, $emp) {
        $footer['MONTHLY_RATE'] = $footer['MONTHLY_RATE'] + $emp->emp_data->ratemonth;
        $footer['BASIC_RATE'] = $footer['BASIC_RATE'] + $emp->payroll->basic;
        $footer['NIGHT_SHIFT_AMOUNT'] = $footer['NIGHT_SHIFT_AMOUNT'] + $emp->payroll->night_diff;
        $footer['LEGAL_HOLIDAY_AMOUNT'] = $footer['LEGAL_HOLIDAY_AMOUNT'] + $emp->payroll->legal_holiday;
        $footer['SPECIAL_HOLIDAY_AMOUNT'] = $footer['SPECIAL_HOLIDAY_AMOUNT'] + $emp->payroll->special_holiday;
        $footer['INCENTIVES'] = $footer['INCENTIVES'] + $emp->payroll->incentives;
        $footer['LATE_UNDERTIME_ABSENCES'] = $footer['LATE_UNDERTIME_ABSENCES'] + $emp->payroll->late + $emp->payroll->absent + $emp->payroll->undertime;
        $footer['GROSS_SALARY'] = $footer['GROSS_SALARY'] + $emp->payroll->gross;
        $footer['SSS'] = $footer['SSS'] + $emp->payroll->sss;
        $footer['PHIC'] = $footer['PHIC'] + $emp->payroll->phic;
        $footer['HDMF'] = $footer['HDMF'] + $emp->payroll->hdmf;
        $footer['ITW'] = $footer['ITW'] + $emp->payroll->wtx;
        $footer['SSS_LOAN'] = $footer['SSS_LOAN'] + $emp->payroll->sss_loan;
        $footer['HDMF_LOAN'] = $footer['HDMF_LOAN'] + $emp->payroll->pag_ibig_loan;
        $footer['TOTAL_DEDUCTIONS'] = $footer['TOTAL_DEDUCTIONS'] + $emp->payroll->deduct;
        $footer['NET_PAY'] = $footer['NET_PAY'] + $emp->payroll->net;
        $footer['ADJUSTMENTS'] = $footer['ADJUSTMENTS'] + $emp->payroll->adjustment;
        $footer['FINAL_NET'] = $footer['FINAL_NET'] + $emp->payroll->final_net;
        return $footer;
    }

    public function PayrollExcelTitle($sheet, $company, $location, $sched_in, $sched_out) {
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->mergeCells('A1:W2');
        $sheet->mergeCells('A3:W3');
        $sheet->mergeCells('A6:F6');
        $sheet->mergeCells('A7:F7');
        $sheet->setCellValue('A1', $company);
        $sheet->setCellValue('A3', $location);
        $sheet->setCellValue('A6', 'Payroll Summary Report');
        $sheet->setCellValue('A7', 'Payroll period from: ' . date('F d,Y', strtotime($sched_in)) . " TO " . date('F d,Y', strtotime($sched_out)));
        $sheet->getStyle('A1:F7')->applyFromArray($styleArray);
        $sheet->getStyle('A1:F2')->getFont()->setSize(16);
    }

    public function PayrollDepartmentTitle($sheet, $department, $cell) {
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->mergeCells('A' . $cell . ':' . 'W' . $cell);
        $sheet->setCellValue('A' . $cell, $department);
        $sheet->getStyle('A' . $cell . ':' . 'W' . $cell)->applyFromArray($styleArray);
        $sheet->getStyle('A' . $cell . ':' . 'W' . $cell)->getFont()->setSize(16);
    }

    public function PayrollFooter($sheet, $footer, $row, $last_row, $checker) {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN //fine border
                ]
            ],
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->getStyle('A' . $row . ':' . 'W' . $row)->applyFromArray($styleArray);
        $coordinate = 1;
        foreach ($footer as $key => $val) {
            $column = $sheet->getCellByColumnAndRow($coordinate, $row)->getCoordinate();
            ($key == 'name') ? $sheet->setCellValue($column, $val) : $sheet->setCellValue($column, number_format(floatval($val), 2));
            $coordinate++;
        }
        $styleCheckers = [
            'font' => [
                'bold' => true,
            ],
        ];
        $coordinate = 1;
        $row += 2;
        $sheet->getStyle('A' . $row . ':' . 'W' . ($row + 1))->getFont()->setSize(13);
        $sheet->getStyle('A' . $row . ':' . 'W' . ($row + 1))->applyFromArray($styleCheckers);
        if ($last_row) {
            foreach ($checker as $key => $val) {
                $column = $sheet->getCellByColumnAndRow($coordinate, $row)->getCoordinate();
                $column_name = $sheet->getCellByColumnAndRow($coordinate, $row + 1)->getCoordinate();
                $sheet->setCellValue($column, $key);
                $sheet->setCellValue($column_name, $val);
                $coordinate += 5;
            }
            $coordinate = 1;
            $row += 3;
            $column = $sheet->getCellByColumnAndRow($coordinate, $row)->getCoordinate();
            $sheet->setCellValue($column, 'Prepared by: ' . $checker['Prepared by:']);
            $column = $sheet->getCellByColumnAndRow($coordinate, $row + 1)->getCoordinate();
            $sheet->setCellValue($column, 'Date and time Printed: ' . date('m/d/Y g:i A'));
        }
    }

    public function ParollExcelHeader($sheet) {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN //fine border
                ]
            ],
            'font' => [
                'bold' => true,
                'size' => 8
            ],
        ];

        $data = array(
            'EMPLOYEE', 'MONTHLY RATE', 'BASIC RATE', 'NIGHT SHIFT DIFFERENTIAL HOURS',
            'NIGHT SHIFT DIFFERENTIAL AMOUNT', 'LEGAL HOLIDAY HOURS', 'LEGAL HOLIDAY AMOUNT',
            'SPECIAL HOLIDAY HOURS', 'SPECIAL HOLIDAY AMOUNT', 'INCENTIVES', 'LATE, UNDERTIME, ABSENCES',
            'GROSS SALARY', 'SSS', 'PHIC', 'HDMF', 'ITW', 'OTHERS', 'SSS LOAN', 'HDMF LOAN', 'TOTAL DEDUCTIONS', 'NET PAY', 'OTHER ADJUSTMENTS', 'FINAL NET PAY'
        );
        $sheet->getStyle('A11:W13')->applyFromArray($styleArray);
        $coordinate = 1;

        while ($coordinate <= 23) {
            $column = $sheet->getCellByColumnAndRow($coordinate, 11)->getCoordinate();
            $row = $sheet->getCellByColumnAndRow($coordinate, 13)->getCoordinate();
            $sheet->getStyle($column)->getAlignment()->setWrapText(true);
            $sheet->setCellValue($column, $data[($coordinate - 1)]);
            $sheet->mergeCells($column . ':' . $row);
            $sheet->getStyle($column)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($column)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            if ($coordinate == 1) {
                $sheet->getColumnDimension($sheet->getCell($column)->getColumn())->setWidth(20);
            } else {
                $sheet->getColumnDimension($sheet->getCell($column)->getColumn())->setWidth(8);
            }
            $coordinate++;
        }
    }

    public function BankTransmittalHeader($sheet) {
        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->setCellValue('A2', 'Fullname');
        $sheet->setCellValue('B2', 'Account Number');
        $sheet->setCellValue('C2', 'Net Pay');
        $sheet->getColumnDimension('A')->setWidth(27);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getStyle('A2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C2')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A2:C2')->applyFromArray($border);
    }

    public function BankTransmittalExcel($spreadsheet, $emps) {

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet(0);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $this->BankTransmittalHeader($sheet);
        $rowindex = 3;
        foreach ($emps as $val) {
            foreach ($val as $row) {
                foreach ($row as $emp) {
                    $sheet->setCellValueByColumnAndRow(1, $rowindex, $emp->emp_data->firstname . " " . $emp->emp_data->lastname . " " . $emp->emp_data->suffix . " " . $emp->emp_data->midname);
                    $sheet->setCellValueByColumnAndRow(2, $rowindex, $this->decrypt_pass($emp->emp_data->accountno));
                    $sheet->setCellValueByColumnAndRow(3, $rowindex, $emp->payroll->net);
                    $rowindex++;
                }
            }
        }
    }

    //============================GENERATE PDF================================================================
    //========================================================================================================

    public function GeneratePdf($emps, $sched_in, $sched_out, $prepared, $checked, $noted, $approved) {
        $category = $this->input->post('category');
        if ($category == 0) {
            $this->ConstrucTableBody($emps, $sched_in, $sched_out, $prepared, $checked, $noted, $approved);
        }
    }

    public function ConstructTableReport($company, $location, $department, $table_body, $sched_in, $sched_out, $show_sub_total, $sub_total, $show_grand_total, $grand_total, $prepared, $checked, $noted, $approved) {
        $table = '  <table frame="box" rules="cols" width="100%">
                    <caption style="font-size:12px">' . $company . '</caption>' .
                '<caption style="font-size:12px">' . $location . '</caption>' .
                '<caption style="font-size:14px;font-weight:bold">Payroll Summary Report ' . $sched_in . ' TO ' . $sched_out . '</caption>' .
                ' <tr><th style="border:solid;border-width: thin;" colspan="23">' . $department . '</th></tr>' .
                '<tr><th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">Employee Name</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">MONTHLY RATE</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">BASIC RATE</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">NIGHT SHIFT DIFFERENTIAL HOURS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">NIGHT SHIFT DIFFERENTIAL AMOUNT</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">LEGAL HOLIDAY HOURS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">LEGAL HOLIDAY AMOUNT</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">SPECIAL HOLIDAY HOURS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">SPECIAL HOLIDAY AMOUNT</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">INCENTIVES</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">LATE, UNDERTIME, ABSENCES</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">GROSS SALARY</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">SSS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">PHIC</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">HDMF</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">ITW</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">OTHERS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">SSS LOAN</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">HDMF LOAN</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">TOTAL DEDUCTIONS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">NET PAY</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">OTHER ADJUSTMENTS</th>'
                . '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">FINAL NET PAY</th>'
                . '</tr>';
        $table = $table . $table_body;
        if ($show_sub_total) {
            $table .= '<tr>' . $this->ExtraFooter($sub_total) . '</tr>';
        }
        $extra_footer = '';

        if ($show_grand_total) {
            $table .= '<tr>' . $this->ExtraFooter($grand_total) . '</tr>';
            $extra_footer .= '<table width="100%"><tr><th>Prepared by:</th><th>Checked by:</th><th>Noted by:</th><th>Approved by:</th></tr>' .
                    '<tbody><tr><th></th><th></th><th></th><th></th></tr>' .
                    '<tr><th>' . $prepared . '</th><th>' . $checked . '</th><th>' . $noted . '</th><th>' . $approved . '</th></tr>' . '</tbody>'
                    . '</table><br><table width="100%"><tr><th style="text-align:left;font-size:14px">Prepared by: ' . $prepared . '</th></tr><tr><th style="text-align:left;font-size:14px">Date and Time Printed: ' . date('m/d/Y g:i A') . '</th></tr></table>';
        }
        $table = $table . '</table><br>' . $extra_footer . '<p style="page-break-after: always;">&nbsp;</p>';
        return $table;
    }

    public function ExtraFooter($footer) {
        $table_footer = '';
        foreach ($footer as $key => $val) {
            if ($key == 'name') {
                $table_footer .= '<th  style="border:solid;border-width: thin;font-size:13px;font-weight:bold;padding:5px" rowspan="1" colspan="1">' . $val . '</th>';
            } else {
                $table_footer .= '<th  style="border:solid;border-width: thin;font-size:13px;font-weight:bold;padding:5px" rowspan="1" colspan="1">' . number_format(floatval($val), 2) . '</th>';
            }
        }
        return $table_footer;
    }

    public function ConstrucTableBody($emps, $sched_in, $sched_out, $prepared, $checked, $noted, $approved) {
        $table = '';
        $grand_total = array(
            'name' => 'GRAND TOTAL', 'MONTHLY_RATE' => 0, 'BASIC_RATE' => 0, 'NIGHT_SHIFT_HOURS' => 0,
            'NIGHT_SHIFT_AMOUNT' => 0, 'LEGAL_HOLIDAY_HOURS' => 0, 'LEGAL_HOLIDAY_AMOUNT' => 0,
            'SPECIAL_HOLIDAY_HOURS' => 0, 'SPECIAL_HOLIDAY_AMOUNT' => 0, 'INCENTIVES' => 0, 'LATE_UNDERTIME_ABSENCES' => 0,
            'GROSS_SALARY' => 0, 'SSS' => 0, 'PHIC' => 0, 'HDMF' => 0, 'ITW' => 0, 'OTHERS' => 0,
            'SSS_LOAN' => 0, 'HDMF_LOAN' => 0, 'TOTAL_DEDUCTIONS' => 0, 'NET_PAY' => 0, 'ADJUSTMENTS' => 0, 'FINAL_NET' => 0
        );
        $dep_counter = 0;
        foreach ($emps as $com => $val) {
            $explode_structure = explode('*', $com);
            $comp = $this->M_structure->FetchStructureName(array('refno' => $explode_structure[0]), 'tbl_company');
            $loc = $this->M_structure->FetchStructureName(array('refno' => $explode_structure[1]), 'tbl_location');
            $loc_name = (count($loc) > 0) ? $loc[0]->name : '';
            $tbody = '';
            foreach ($val as $dep => $row) {
                $dep_counter++;
                $sub_total = array(
                    'name' => 'SUB TOTALS', 'MONTHLY_RATE' => 0, 'BASIC_RATE' => 0, 'NIGHT_SHIFT_HOURS' => 0,
                    'NIGHT_SHIFT_AMOUNT' => 0, 'LEGAL_HOLIDAY_HOURS' => 0, 'LEGAL_HOLIDAY_AMOUNT' => 0,
                    'SPECIAL_HOLIDAY_HOURS' => 0, 'SPECIAL_HOLIDAY_AMOUNT' => 0, 'INCENTIVES' => 0, 'LATE_UNDERTIME_ABSENCES' => 0,
                    'GROSS_SALARY' => 0, 'SSS' => 0, 'PHIC' => 0, 'HDMF' => 0, 'ITW' => 0, 'OTHERS' => 0,
                    'SSS_LOAN' => 0, 'HDMF_LOAN' => 0, 'TOTAL_DEDUCTIONS' => 0, 'NET_PAY' => 0, 'ADJUSTMENTS' => 0, 'FINAL_NET' => 0
                );
                $row_index = 0;
                $dept = $this->M_structure->FetchStructureName(array('refno' => $dep), 'tbl_departments');
                $deptname = (count($dept) > 0) ? $dept[0]->name : '';
                foreach ($row as $emp) {
                    $tbody .= '<tr><th  style="text-align:left;border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->emp_data->lastname . ", " . $emp->emp_data->firstname . " " . $emp->emp_data->suffix . " " . $emp->emp_data->midname . '</th>';
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->emp_data->ratemonth . '</th>';
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . number_format(floatval($emp->payroll->basic), 2) . '</th>';
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1"></th>';
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->night_diff . '</th>'; //night diff amount
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . '' . '</th>'; //legal holiday hours
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->legal_holiday . '</th>'; //legal holiday amount
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . '' . '</th>'; //special holiday hours
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->special_holiday . '</th>'; //special holiday amount
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->incentives . '</th>'; //incentives
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . ($emp->payroll->late_undertime_absent) . '</th>'; //late,undertime,absences
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->gross . '</th>'; //gross salary
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->sss . '</th>'; //sss
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->phic . '</th>'; //phic
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->hdmf . '</th>'; //hdmf
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->wtx . '</th>'; //itw
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->ca_total . '</th>'; //others
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->sss_loan . '</th>'; //sss loan
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->pag_ibig_loan . '</th>'; //hdmf loan
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->deduct . '</th>'; //total deduction
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->net . '</th>'; //net pay
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->adjustment . '</th>'; //other adjustments
                    $tbody .= '<th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">' . $emp->payroll->final_net . '</th></tr>'; //finat net pay
                    $row_index++;
                    $sub_total = $this->FooterValues($sub_total, $emp);
                    $grand_total = $this->FooterValues($grand_total, $emp);
                    if ($row_index == 28) {
                        $show_sub_total = (count($row) > 28) ? false : true;
                        $show_grand_total = (count($val) == $row) ? true : false;
                        $table .= $this->ConstructTableReport($comp[0]->name, $loc_name, $deptname, $tbody, $sched_in, $sched_out, $show_sub_total, $sub_total, $show_grand_total, $grand_total, $prepared, $checked, $noted, $approved);
                        $row_index = 0;
                        $tbody = '';
                    }
                }
                if ($row_index > 0) {
                    $show_grand_total = (end($val) == $row) ? true : false;
                    $table .= $this->ConstructTableReport($comp[0]->name, $loc_name, $deptname, $tbody, $sched_in, $sched_out, true, $sub_total, $show_grand_total, $grand_total, $prepared, $checked, $noted, $approved);
                    $row_index = 0;
                    $tbody = '';
                }
            }
        }
        $mypdf = array();
        $mypdf['table'] = $table;
        $mypdf['title'] = "Payroll Summary as of " . date('F d', strtotime($sched_in)) . "-" . date('d', strtotime($sched_out)) . " " . date('Y', strtotime($sched_out));
        $this->load->view('reports/workschedule_dtr_late/workschedule_dtr_late', $mypdf);
    }

}
