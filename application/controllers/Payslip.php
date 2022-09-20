<?php

class Payslip extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->model('model_payslip', 'M_payslip');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_structure', 'M_structure');
    }

    public function FetchPayslipTable() {
        $profileno = $this->input->post('profileno');
        $company = $this->input->post('company');
        $batches = $this->M_payslip->PayslipDataTable($this->CleanArray(array('category' => $company)));
        $sub_array = array();
        $data = array();
        foreach ($batches as $val) {
            $payslip = $this->M_payslip->FetchPayslips($this->CleanArray(array('batchcode' => $val->batchcode, 'profileno' => $profileno)));
            if (count($payslip) > 0) {
                $from = date('Y-m-d', strtotime($val->payschedfrom));
                $to = date('Y-m-d', strtotime($val->payschedto));
                $explode_from = explode('-', $from);
                $explode_to = explode('-', $to);
                $payment = '';
                if ($explode_from[2] > $explode_to[2]) {
                    $payment = date('F', strtotime($to)) . " 15, " . date('Y', strtotime($from));
                } else {
                    $payment = date('F', strtotime($from)) . " " . date('t', strtotime($from)) . " " . date('Y', strtotime($from));
                }
                $recieved = '';

                if ($payslip[0]->is_received == 1) {
                    $recieved = '<label style="color:white;letter-spacing:0.5px">(Recieved)</label>';
                }
                if (date('Y-m-d', strtotime($payment)) <= date('Y-m-d')) {
                    $sub_array = array();
                    $sub_array[] = $val->payschedterm . '+' . $val->payschedtype . '+' . $val->batchcode . '+' . $val->payschedfrom . '+' . $val->payschedto . '+' . $payslip[0]->profileno . '+' . $this->session->userdata('monthly');
                    $sub_array[] = '<span class="btn" style="background-color:#3ED03E;color:white"><i class="glyphicon glyphicon-download-alt"></i></span><br>' . $recieved;
                    $sub_array[] = '<label style="letter-spacing:1px">' . $payment . '</label><br>' . " " .
                            '<span style="font-size:12px">' . date('F d, Y', strtotime($val->payschedfrom)) . " - " . date('F d, Y', strtotime($val->payschedto)) . '</span>';
                    $sub_array[] = $val->payschedterm;
                    $sub_array[] = $payslip[0]->is_received;
                    $data[] = $sub_array;
                }
            }
        }
        $output = array
            (
//            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($batches),
            "recordsFiltered" => $this->M_payslip->PayslipDataTableFiler($this->CleanArray(array('category' => $company))),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function GeneratePayslip() {
        $this->load->library('pdf');
        $info = $this->input->post('data');
        $explode_info = explode('+', $info);

        $data = $this->OrganizePayslip($explode_info[5], $explode_info[0], $explode_info[1], $explode_info[2], $explode_info[3], $explode_info[4], $explode_info[6]);
        if ($this->session->userdata('profileno') == $explode_info[5]) {
            $this->M_payslip->SaveUpdatePayslip(array('is_received' => 1), array('batchcode' => $explode_info[2], 'profileno' => $explode_info[5]));
        }
        $data[6] = date('F d', strtotime($explode_info[3])) . ' - ' . date('F d, Y', strtotime($explode_info[4]));
        $mypdf = array();
        $mypdf['title'] = "Payslip";
        $mypdf['complogo'] = $this->session->userdata('complogo');
        $mypdf['data'] = $data;
        $customPaper = array(0, 0, 500, 300);
        $this->pdf->set_paper($customPaper);
        $this->pdf->load_view('reports/payslip/payslip_form', $mypdf);
        $this->pdf->render();

        $canvas = $this->pdf->get_canvas();
        $font = Font_Metrics::get_font("helvetica", "bold");

        $this->pdf->stream('"' . 'Payslip' . ".pdf", array('Attachment' => 0));
    }

    public function OrganizePayslip($profileno, $schedterm, $paytype, $batchcode, $date_in, $date_out, $is_monthly) {
        $gross_total = 0;
        $deduct_total = 0;
        $loans_total = 0;
        $CA_total = 0;
        $less_total = 0;
        $incentive_total = 0;
        $contribution_total = 0;

        $emp_payroll = array( 'add' => array('Night Differential' => 0, 'Leave Pay' => 0));

        $payroll = $this->M_payslip->FetchPayroll($this->CleanArray(array('profileno' => $profileno, 'paytype' => $paytype, 'batchcode' => $batchcode)));
        $adjustment = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'ADJUSTMENTS', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $incentives = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'OTHER INCENTIVES', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');

        $loans = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'LOAN', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $ca = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'CASH ADVANCE', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $holidays = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'HOLIDAY', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $time = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'TIME', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');
        $contributions = $this->M_payslip->FetchPayrollAdjustments(array('batchcode' => $batchcode, 'grouptype' => 'CONTRIBUTIONS', 'profileno' => $profileno), 'idx_batchcode_profileno_grouptype');

        foreach ($incentives as $val) {
            if (isset($emp_payroll['incentives'][$val->name])) {
                $emp_payroll['incentives'][$val->name] = $emp_payroll['incentives'][$val->name] + number_format((float) $val->amount, 2, '.', '');
            } else {
                $emp_payroll['incentives'][$val->name] = number_format((float) $val->amount, 2, '.', '');
            }
            $incentive_total = $incentive_total + $val->amount;
            $gross_total = $gross_total + $val->amount;
        }
        foreach ($adjustment as $val) {
            $name = ucwords(strtolower($val->name));

            if (isset($emp_payroll['add'][$name])) {
                $emp_payroll['add'][$name] = $emp_payroll['add'][$name] + number_format((float) $val->amount, 2, '.', '');
            } else {
                $emp_payroll['add'][$name] = number_format((float) $val->amount, 2, '.', '');
            }

            $gross_total = $gross_total + $val->amount;
        }
        foreach ($holidays as $val) {
            $name = ucwords(strtolower($val->name));
            if (isset($emp_payroll['add'][$name])) {
                $emp_payroll['add'][$name] = $emp_payroll['add'][$name] + number_format((float) $val->amount, 2, '.', '');
            } else {
                $emp_payroll['add'][$name] = number_format((float) $val->amount, 2, '.', '');
            }
            $gross_total = $gross_total + $val->amount;
        }
        $leavepay = 0;
        foreach ($time as $val) {
            if ($val->name == 'Tardiness' || $val->name == 'Absent' || $val->name == 'Undertime') {
                if (isset($emp_payroll['less'][$val->name])) {
                    $emp_payroll['less'][$val->name] = $emp_payroll['less'][$val->name] + number_format((float) $val->amount, 2, '.', '');
                } else {
                    $emp_payroll['less'][$val->name] = number_format((float) $val->amount, 2, '.', '');
                }
                $less_total = $less_total + $val->amount;
            } else {
                if ($is_monthly == 'Monthly' && $val->name == 'Leave Pay') {
                    $leavepay += $val->amount;
                    $less_total = $less_total + $val->amount;
                } else {
                    if (isset($emp_payroll['add'][$val->name])) {
                        $emp_payroll['add'][$val->name] = $emp_payroll['add'][$val->name] + number_format((float) $val->amount, 2, '.', '');
                    } else {
                        $emp_payroll['add'][$val->name] = number_format((float) $val->amount, 2, '.', '');
                    }
                    $gross_total = $gross_total + $val->amount;
                }
            }
        }
        if (isset($emp_payroll['less']['Absent'])) {
            $emp_payroll['less']['Absent'] += $leavepay;
        }
        foreach ($contributions as $val) {
            if (isset($emp_payroll['deduction'][$val->name])) {
                $emp_payroll['deduction'][$val->name] = $emp_payroll['less'][$val->name] + number_format((float) $val->amount, 2, '.', '');
            } else {
                $emp_payroll['deduction'][$val->name] = number_format((float) $val->amount, 2, '.', '');
            }
            $contribution_total = $contribution_total + $val->amount;
            $deduct_total = $deduct_total + $val->amount;
        }
        foreach ($loans as $val) {
            if (isset($emp_payroll['loans'][$val->name])) {
                $emp_payroll['loans'][$val->name] = $emp_payroll['loans'][$val->name] + number_format((float) $val->amount, 2, '.', '');
            } else {
                $emp_payroll['loans'][$val->name] = number_format((float) $val->amount, 2, '.', '');
            }
            $loans_total = $loans_total + $val->amount;
            $deduct_total = $deduct_total + $val->amount;
        }

        foreach ($ca as $val) {
            if (isset($emp_payroll['ca'][$val->name])) {
                $emp_payroll['ca'][$val->name] = $emp_payroll['loans'][$val->name] + number_format((float) $val->amount, 2, '.', '');
            } else {
                $emp_payroll['ca'][$val->name] = number_format((float) $val->amount, 2, '.', '');
            }
            $CA_total = $CA_total + $val->amount;
            $deduct_total = $deduct_total + $val->amount;
        }
        if (count($payroll) > 0) {
            $paytype = 0;
            if ($payroll[0]->ratecom == 'Daily') {
                $paytype = number_format((float) ($payroll[0]->ratedaily * $payroll[0]->totaldays), 2, '.', '');
            } else if ($payroll[0]->ratecom == 'Monthly') {
                if ($payroll[0]->paytype == 'Weekly') {

                    $paytype = number_format((float) ($payroll[0]->ratemonthly / 4), 2, '.', '');
                } else if ($payroll[0]->paytype == 'Semi-Month') {
                    $paytype = number_format((float) ( $payroll[0]->ratemonthly / 2), 2, '.', '');
                } else {
                    $paytype = number_format((float) $payroll[0]->ratemonthly, 2, '.', '');
                }
            }
            $others['payslip_date'] = date('F d', strtotime($date_in)) . ' - ' . date('F d, Y', strtotime($date_out));
            $others['days_attended'] = $payroll[0]->attended;
            $others['empid'] = $payroll[0]->empid;
            $others['deduct_total'] = $contribution_total;
            $others['less_total'] = number_format((float) $less_total, 2, '.', '');
            $others['ca_total'] = $CA_total;
            $others['loans_total'] = $loans_total;
            $others['incentive_total'] = number_format((float) $incentive_total, 2, '.', '');
            $others['fullname'] = $payroll[0]->lastname . ", " . $payroll[0]->firstname . " " . $payroll[0]->midname;
            $others['company'] = $payroll[0]->comname;
            $others['jobposition'] = $payroll[0]->jobposition;
            $others['daily_rate'] = $payroll[0]->ratecom;
            $others['paytype'] = $payroll[0]->paytype;
            $others['rate_daily'] = number_format((float) $payroll[0]->ratedaily, 2, '.', '');
            $others['rate_monthly'] = number_format((float) $payroll[0]->ratemonthly, 2, '.', '');
            $others['basic'] = $paytype;
            $emp_payroll['add']['Days Attended'] = number_format((float) $payroll[0]->basic, 2, '.', '');
            $gross_total = number_format((float) (($payroll[0]->basic + $gross_total)), 2, '.', '');
            $others['net_pay'] = number_format((float) (($gross_total - $deduct_total) + $others['less_total']), 2, '.', '');

            if ($others['paytype'] == 'Semi-Month') {
                if ($schedterm == 'Payment 1') {
                    $datetoAdded = date('F', strtotime($date_in . ' +1 month'));
                    $others['schedterm'] = $datetoAdded . ' ' . '15';
                } else {
                    $others['schedterm'] = date('F', strtotime($date_in)) . ' ' . date('t', strtotime($date_in));
                }
            } else {
                $others['schedterm'] = 'Weekly';
            }
            $emp_payroll['others'] = $others;
        }
        return $this->PayrollTables($emp_payroll);
    }

    public function PayslipTableRow($key, $attended, $val) {
        $table = '<tr>
                 <td style="border:solid;border-width: thin;font-size:10px"colspan="1">' . $key . '</td>' .
                '<td style="border:solid;border-width: thin;font-size:10px"colspan="1">' . $attended . '</td>
                 <td style="border:solid;border-width: thin;text-align: right;font-size:10px"colspan="1">' . $this->OrganizeNumberFormat($val) . '</td>
                 </tr>';
        return $table;
    }

    public function PayrollTables($emp_payroll) {
        $add_table = '';
        $less_table = '';
        $deduct_table = '';
        $loans_table = '';
        $ca_table = '';
        $incentives_table = '';

        if (isset($emp_payroll['add'])) {
            foreach ($emp_payroll['add'] as $key => $val) {
                $attended = ($key == 'Days Attended') ? $emp_payroll['others']['days_attended'] : '';
                $temp_table = $this->PayslipTableRow($key, $attended, $val);
                if ($key == 'Days Attended') {
                    $add_table = $temp_table . $add_table;
                } else {
                    $add_table = $add_table . $temp_table;
                }
            }
        }
        if (isset($emp_payroll['less'])) {
            foreach ($emp_payroll['less'] as $key => $val) {
                $less_table = $less_table . $this->PayslipTableRow($key, '', $val);
            }
        }
        if (isset($emp_payroll['incentives'])) {
            foreach ($emp_payroll['incentives'] as $key => $val) {
                $incentives_table = $incentives_table . $this->PayslipTableRow($key, '', $val);
            }
        }
        if (isset($emp_payroll['deduction'])) {
            foreach ($emp_payroll['deduction'] as $key => $val) {
                $deduct_table = $deduct_table . $this->PayslipTableRow($key, '', $val);
            }
        }
        if (isset($emp_payroll['loans'])) {
            foreach ($emp_payroll['loans'] as $key => $val) {
                $loans_table = $loans_table . $this->PayslipTableRow($key, '', $val);
            }
        }
        if (isset($emp_payroll['ca'])) {
            foreach ($emp_payroll['ca'] as $key => $val) {
                $ca_table = $ca_table . $this->PayslipTableRow($key, '', $val);
            }
        }
        $data = array(
            'add_table' => $add_table,
            'less_table' => $less_table,
            'deduct_table' => $deduct_table,
            'loans_table' => $loans_table,
            'ca_table' => $ca_table,
            'incentives_table' => $incentives_table,
        );
        if (isset($emp_payroll['others'])) {
            $data['others'] = $emp_payroll['others'];
        }
        return $data;
    }

    public function GenerateEmployeePayslips() {
//        $this->load->library('pdf');
        $emps = json_decode($this->input->post('emps_payslip'));


        $mypdf = array();
        $mypdf['title'] = "Payslip";
        $mypdf['complogo'] = $this->session->userdata('complogo');
        $mypdf['table'] = $emps;
        $this->load->view('reports/payslip/payslip_sample', $mypdf);
//        $customPaper = array(0, 0, 500, 300);
////        $this->pdf->set_paper($customPaper);
//        $this->pdf->set_paper('Legal', 'landscape');
//        $this->pdf->load_view('reports/payslip/payslip_sample', $mypdf);
//        $this->pdf->render();
//
//        $canvas = $this->pdf->get_canvas();
//        $font = Font_Metrics::get_font("helvetica", "bold");
//
//        $this->pdf->stream('"' . 'Payslip' . ".pdf", array('Attachment' => 0));
    }

    public function SetupEmployeePayslips() {
        $selected_departments = (array) json_decode($this->input->post('selected_departments'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_departments'));
        $selectd_profileno = (array) json_decode($this->input->post('selectd_profileno'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselectd_profileno'));
        $from = $this->input->post('worksched_from');
        $to = $this->input->post('worksched_to');
        $result = false;
        $paper_form = '';
        if (count($selected_departments) != 0 || count($selectd_profileno) != 0) {
            $result = true;
            $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);
            $column_index = ($this->input->post('category') == 0) ? 'lastname' : 'firstname';
            $column_array = array(0 => $column_index, 1 => "asc");
            $emp = $this->M_employee->FetchEmployeeTable(0, $where_selected, $column_array, array());
            $count = 0;
            $payslip = '';
            $table = '';
            foreach ($emp as $val) {
                $batches = $this->M_payslip->SpecificBatchcode($this->CleanArray(array('category' => $val->comID, 'payschedfrom' => $from, 'payschedto' => $to)));
               
				foreach ($batches as $row) {
                    $emp_payslip = $this->OrganizePayslip($val->profileno, $row->payschedterm, $row->payschedtype, $row->batchcode, $row->payschedfrom, $row->payschedto, $val->ratecom);
                 
				   if (isset($emp_payslip['others'])) {
                        $payslip = $payslip . $this->OrganizeForms($this->PayslipForm($emp_payslip));
                        $count++;
                        if ($count == 4) {
                            $paper_form = $paper_form . $this->PaperForm($payslip);
                            $payslip = '';
                            $table = '';
                            $count = 0;
                        }else if ($count == count($batches)){
							 $paper_form = $paper_form . $this->PaperForm($payslip);
						}
						
                    }
                }
            }
        }
	
//        $this->GenerateEmployeePayslips($paper_form);
        echo json_encode(array('result' => $result, 'data' => strval($paper_form)));
    }

    public function PaperForm($table) {
        $paper_form = ' <<div style="width:100%;float:left">
       <div style="top:0;left:0;right:0;left:0">'
                . $table .
                '</div></div><p style="page-break-after: always;">&nbsp;</p>';
        return $paper_form;
    }

    public function OrganizeForms($table) {
        $form_table = ' <div style=" width: 49%;float:left">' . $table . '</div>';
        return $form_table;
    }

    public function PayslipForm($data) {
        $table = '<div style=" width: 100%;margin:10px">' .
                '<div style="border:solid;border-width: thin; width:98.3%;height:33px;text-align: center;font-weight: bold">' .
                'PAYSLIP<br><span style="font-size:10px">' . $data['others']['company'] . '</span><br>' .
                '<span>' . $data['others']['schedterm'] . " (" . $data['others']['payslip_date'] . ")" . '</span>' .
                '</div>' .
                '<div style=" width: 100%;">' .
                '<div style=" width: 49%;float:left;border:solid;border-width: thin;">' .
                '<table width="100%">' .
                '<tr style="font-size: 10px; font-weight: bold; ">' .
                '<td style="font-size:10px">' . $data['others']['empid'] . '</td>' .
                '<td style="text-align: right;font-size:10px">' . $data['others']['fullname'] . '</td>' .
                '</tr>' .
                '<tr>' .
                '<td style="font-size:10px">Position: </td>' .
                '<td style="text-align: right;font-size:10px">' . $data['others']['jobposition'] . '</td>' .
                '</tr>' .
                '</table>' .
                ' <h3 style="margin-left: 6px">Gross Earnings:</h3>' .
                ' <table  frame="box" rules="cols"  width="97%" style="margin:5px; margin-top: -5px;border-width: thin;">' .
                ' <tr >' .
                '<td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">Adjustments/Others' .
                '</td>' .
                '<td  style="background-color: #C0C0C0;border:none;" colspan="1">' .
                ' </td>' .
                '<td  style="background-color: #C0C0C0;text-align: right;border:none;font-size:10px" colspan="1">AMOUNT' .
                '</td>' .
                '</tr>' . $data['add_table'] .
                '</table>' .
                '<table  frame="box" rules="cols"  width="97%" style="margin:5px;border-width: thin;">' .
                ' <tr >' .
                '<td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">Less' .
                '</td>' .
                ' <td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">' .
                ' </td>' .
                ' <td  style="background-color: #C0C0C0;text-align: right;border:none;font-size:10px" colspan="1">AMOUNT' .
                ' </td>' .
                ' </tr>' .
                $data['less_table'] .
                '<tr>' .
                '<td rowspan="1" style="border:none;font-size:10px">TOTAL</td>' .
                '<td rowspan="1" style="border:none;font-size:10px"></td>' .
                ' <td rowspan="1" style="border:none;text-align:right;font-size:10px">' . $data['others']['less_total'] . '</td>' .
                '</tr>' .
                ' </table>' .
                '<table  frame="box" rules="cols"  width="97%" style="margin:5px;border-width: thin;">' .
                '<tr >' .
                ' <td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">Incentives' .
                ' </td>' .
                ' <td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">' .
                ' </td>' .
                ' <td  style="background-color: #C0C0C0;text-align: right;border:none;font-size:10px" colspan="1">AMOUNT' .
                ' </td>' .
                '</tr>' .
                $data['incentives_table'] .
                ' <tr>' .
                '<td rowspan="1" style="border:none;font-size:10px">TOTAL</td>' .
                '<td rowspan="1" style="border:none;font-size:10px"></td>' .
                '<td rowspan="1" style="border:none;text-align:right;font-size:10px">' . $data['others']['incentive_total'] . '</td>' .
                '</tr>' .
                '</table>' .
                '</div>' .
                '<div style=" width: 49%; float:left;border:solid;border-width: thin;">' .
                '<div>' .
                '<table width="100%" style="margin-top: -2px">' .
                '<tr>' .
                '<td style="width:70px;font-size:10px">Rate Class: </td>' .
                '<td style="font-size:10px">' . $data['others']['daily_rate'] . '</td>' .
                '<td style="text-align: right; font-size:10px">Rate: </td>' .
                '<td style="text-align: right;font-size:10px">' . $this->OrganizeNumberFormat($data['others']['rate_monthly']) . '</td>' .
                '</tr>' .
                '<tr>' .
                '<td style="font-size:10px">Paytype: </td>' .
                '<td style="font-size:10px">' . $data['others']['paytype'] . '</td>' .
                '<td style="font-size:10px;text-align: right;"> Rate: </td>' .
                ' <td style="text-align: right;font-size:10px">' . $this->OrganizeNumberFormat($data['others']['basic']) . '</td>' .
                '</tr>' .
                '<tr>' .
                '<td style="font-size:10px"></td>' .
                '<td style="font-size:10px"></td>' .
                '<td style="font-size:10px;text-align:right;font-size:10px">Daily Rate:</td>' .
                ' <td style="text-align: right;font-size:10px">' . $this->OrganizeNumberFormat($data['others']['rate_daily']) . '</td>' .
                '</tr>' .
                '</table>' .
                '</div>' .
                '<div>' .
                '<h3 style="margin-left: 6px">Government Contributions:</h3>' .
                '<table  frame="box" rules="cols"  width="97%" style="margin:5px;margin-top: -5px;border-width: thin;">' .
                '<tr >' .
                '<td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">Deductions</td>' .
                '<td  style="background-color: #C0C0C0;border:none;" colspan="1"></td>' .
                '<td  style="background-color: #C0C0C0;text-align: right;border:none;font-size:10px" colspan="1">AMOUNT</td>' .
                '</tr>' .
                $data['deduct_table'] .
                '<tr>' .
                '<td rowspan="1" style="border:none;font-size:10px">TOTAL</td>' .
                '<td rowspan="1" style="border:none;"></td>' .
                '<td rowspan="1" style="text-align: right;border:none;font-size:10px">' . $data['others']['deduct_total'] . '</td>' .
                '</tr>' .
                '</table>' .
                '</div>' .
                '<br>' .
                '<div>' .
                '<table  frame="box" rules="cols"  width="97%" style="margin:5px;margin-top: -5px;border-width: thin;">' .
                '<tr >' .
                '<td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">Loans</td>' .
                '<td  style="background-color: #C0C0C0;border:none;" colspan="1"></td>' .
                '<td  style="background-color: #C0C0C0;text-align: right;border:none;font-size:10px" colspan="1">AMOUNT</td>' .
                '</tr>' .
                $data['loans_table'] .
                '<tr>' .
                '<td rowspan="1" style="border:none;font-size:10px">TOTAL</td>' .
                '<td rowspan="1" style="border:none;"></td>' .
                '<td rowspan="1" style="text-align: right;border:none;font-size:10px">' . $data['others']['loans_total'] . '</td>' .
                '</tr>' .
                '</table>' .
                '<br>' .
                '<table  frame="box" rules="cols"  width="97%" style="margin:5px;margin-top: -5px;border-width: thin;">' .
                '<tr >' .
                '<td  style="background-color: #C0C0C0;border:none;font-size:10px" colspan="1">Cash Advance</td>' .
                '<td  style="background-color: #C0C0C0;border:none;" colspan="1"></td>' .
                '<td  style="background-color: #C0C0C0;text-align: right;border:none;font-size:10px" colspan="1">AMOUNT</td>' .
                '</tr>' .
                $data['ca_table'] .
                '<tr>' .
                '<td rowspan="1" style="border:none;font-size:10px">TOTAL</td>' .
                '<td rowspan="1" style="border:none;"></td>' .
                '<td rowspan="1" style="text-align: right;border:none;font-size:10px">' . $data['others']['ca_total'] . '</td>' .
                '</tr>' .
                '</table>' .
                '</div>' .
                '<div style="border:solid;border-width: thin;margin: 5px;margin-top: 20.0px;padding-top:-5px;padding-bottom: -5px">' .
                '<div style="width: 55%;display: inline-block;margin-left:10px">' .
                '<h3>Net Pay:</h3>' .
                '</div>' .
                '<div style="width: 30%;display: inline-block;">' .
                '<h2 style="text-align: right;margin-right: 10px">' . $this->OrganizeNumberFormat($data['others']['net_pay']) . '</h2>' .
                '</div>' .
                '</div>' .
                '</div>' .
                '</div>' .
                '</div>';
        return $table;
    }

}
