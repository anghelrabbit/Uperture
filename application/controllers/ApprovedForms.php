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
 * Description of ApprovedForms
 *
 * @author MIS
 */
class ApprovedForms extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_overtime', 'M_overtime');
        $this->load->model('model_structure', 'M_structure');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('MY_Model');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Approved Forms";
            $data['page'] = 'pages/menu/reports/approved_forms/approved_forms';
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
                'assets/vendors/bootstrap-toggle-master/css/bootstrap-toggle.min.css'
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
                'assets/vendors/bootstrap-toggle-master/js/bootstrap-toggle.min.js',
                'assets/myjs/utilities/structure.js',
                'assets/myjs/utilities/payperiod.js',
                'assets/myjs/reports/approved_forms/approved_forms.js'
            );

            $this->InspectUser('menu/reports/approved_forms/approved_forms', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FormReport() {
        $category = $this->input->post('category');
        $worksched_in = date('Y-m-d', strtotime($this->input->post('worksched_in'))) . " 00:00:00";
        $worksched_out = date('Y-m-d', strtotime($this->input->post('worksched_out'))) . " 23:59:59";
        $employees = $this->CategoriesEmployees($this->MY_Model->FetchReport($this->StructureChecker((array) json_decode($this->input->post('structure')), "="), $worksched_in, $worksched_out, $category));
        $table = '';
        foreach ($employees as $val) {
            $company = $this->M_structure->FetchStructureName(array('refno' => $val['company']), 'tbl_company');
            $location = $val['location'];
            if ($val['location'] != 'No assigned location') {
                $loc = $this->M_structure->FetchStructureName(array('refno' => $val['location']), 'tbl_location');
                $location = $loc[0]->name;
            }
            $table = $table . $this->GenerateFormReport($company[0]->name, $location, $val['employees'], $category);
        }
        if ($table != '') {
            $this->GenerateApproveFormsReport($table, $category);
        } else {
            echo json_encode('<h1>No Report can be generated on ' . date('F d, Y', strtotime($worksched_in)));
        }
    }

    public function GenerateApproveFormsReport($table, $category) {
        $this->load->library('pdf');
        $mypdf = array();
        $mypdf['table'] = $table;
        if ($category == 0) {
            $mypdf['title'] = "Leave Report";
        } else if ($category == 1) {
            $mypdf['title'] = "Undertime Report";
        } else if ($category == 2) {
            $mypdf['title'] = "Change Schedule Report";
        } else if ($category == 3) {
            $mypdf['title'] = "Overtime Report";
        }
        $this->pdf->set_paper('Legal', 'landscape');
        $this->pdf->load_view('reports/approved_forms/approved_forms_report', $mypdf);
        $this->pdf->render();
        $this->pdf->stream($mypdf['title'] . ".pdf", array('Attachment' => 0));
    }

    public function GenerateFormReport($company, $location, $employees, $category) {
        $row_count = 0;
        $rows = 0;
        $thead = $this->ReportTableHead($category);
        $tbody = '';
        $empname = '';
        $page = '';
        $table = '';
        foreach ($employees as $val) {
            if ($empname != $val->empname && $empname != '') {
                $page = $page . str_replace('rowspanchange', $rows, $tbody);
                $rows = 0;
                $tbody = '';
                $empname = $val->empname;
            } else {
                $empname = $val->empname;
            }
            $tbody = $this->ReportTableBody($rows, $val, $tbody, $category, $empname);
            $row_count++;
            $rows++;
            if ($row_count == 20) {
                $page = $page . str_replace('rowspanchange', $rows, $tbody);
                $table = $table . $this->ConstrucTableReport($company, $location, $thead, $page);
                $tbody = '';
                $row_count = 0;
                $page = '';
                $empname = '';
                $rows = 0;
            }
        }
        if ($tbody != '') {
            $page = $page . str_replace('rowspanchange', $rows, $tbody);
            $table = $table . $this->ConstrucTableReport($company, $location, $thead, $page);
        }
        return $table;
    }

    public function ReportTableBody($rows, $val, $tbody, $category, $empname) {
        if ($rows == 0) {
            $tbody = $tbody . '<tr><td style="border:solid;border-width: thin;font-size:12px;padding:5px;letter-spacing:1px" rowspan="rowspanchange" colspan="1">' . $empname . '</td>';
        } else {
            $tbody = $tbody . '<tr>';
        }
        if ($category == 0) {

            if (strtotime($val->fromdate) == strtotime($val->todate)) {
                $tbody = $tbody . '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:0.5px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->fromdate)) . '</td>';
            } else {
                $tbody = $tbody . '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:0.5px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->fromdate)) . ' - ' . date('F d, Y', strtotime($val->todate)) . '</td>';
            }
            $explode_leave_type = explode('+', $val->leavetype);
            $val->leavetype = $explode_leave_type[0];
            $tbody = $tbody . '<td  style="border:solid;border-width: thin;font-size:11px;padding:5px" rowspan="1" colspan="1">' . $val->leavedays . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:0.5px" rowspan="1" colspan="1">' . $val->leavetype . '</td>';
            if ($val->payment_type == 0) {
                $tbody = $tbody . '<td  style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">Without Pay</td>';
            } else {
                $tbody = $tbody . '<td  style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">With Pay</td>';
            }
            $tbody = $tbody . '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:0.5px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->date_requested)) . '</td>';
            $tbody = $tbody . ' <td style="border:solid;border-width: thin;font-size:11px;padding:5px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->approved_date)) . ' by ' . $val->approved_by . '</td></tr>';
        } else if ($category == 1) {
            $tbody = $tbody .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->worksched_in)) . ' ' . $this->ConvertTo12Format($val->worksched_in) . ' - ' . $this->ConvertTo12Format($val->worksched_out) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->actual_in)) . ' ' . $this->ConvertTo12Format($val->actual_in) . ' - ' . $this->ConvertTo12Format($val->actual_out) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . $this->ComputeTotalUndertime($val) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->date_requested)) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->approved_date)) . ' by ' . $val->approved_by . '</td></tr>';
        } else if ($category == 2) {
            $category = '';
            $reliever = $this->M_employee->FetchEmployee(array('profileno' => $val->reliever_profileno));
            $reliever = (count($reliever) > 0) ? $reliever[0]->lastname . ", " . $reliever[0]->firstname : 'None';
            $category = ($val->shiftchange == 1) ? $category = 'Shift Change' : $category;
            $category = ($val->straightduty == 1) ? ($category != '') ? $category . ' / Straight Duty' : 'Straight Duty' : $category;
            $category = ($val->canceldayoff == 1) ? ($category != '') ? $category . ' / Cancel Day-off' : 'Cancel Day-off' : $category;
            $category = ($val->changedayoff == 1) ? ($category != '') ? $category . ' / Change Day-off' : ' Change Day-off' : $category;
            $tbody = $tbody .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . $category . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->worksched_in)) . ' ' . $this->ConvertTo12Format($val->worksched_in) . ' - ' . $this->ConvertTo12Format($val->worksched_out) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('F d, Y', strtotime($val->toshift_datetimein)) . ' ' . $this->ConvertTo12Format($val->toshift_datetimein) . ' - ' . $this->ConvertTo12Format($val->toshift_datetimeout) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . $reliever . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->date_requested)) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->approved_date)) . ' by ' . $val->approved_by . '</td></tr>';
        } else if ($category == 3) {
            $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('refno' => $val->overtime_type), 'where');
            $explode_type = array(0 => '');
            if (count($overtime_type) > 0) {
                $explode_type = explode('(', $overtime_type[0]->incentive);
            }
            $tbody = $tbody .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . $explode_type[0] . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->worksched_in)) . ' ' . $this->ConvertTo12Format($val->worksched_in) . ' - ' . $this->ConvertTo12Format($val->worksched_out) . '</td>' .
                    '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->ot_timein)) . ' ' . $this->ConvertTo12Format($val->ot_timein) . ' - ' . $this->ConvertTo12Format($val->ot_timeout) . '</td>';

            if ($val->excess_rdot_refno != '' && $val->excess_rdot_refno != null) {
                $total = abs(strtotime($val->excess_rdot_timein) - strtotime($val->excess_rdot_timeout)) / (60 * 60);
                $tbody = $tbody .
                        '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->excess_rdot_timein)) . ' ' . $this->ConvertTo12Format($val->excess_rdot_timein) . ' - ' . $this->ConvertTo12Format($val->excess_rdot_timeout) . '</td>' .
                        '<td style="border:solid;border-width: thin;font-size:13px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . $total . ' hour/s</td>';
            } else {
                $tbody = $tbody . '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="2">N/A</td>';
            }
            $tbody = $tbody . '<td style="border:solid;border-width: thin;font-size:11px;padding:5px;letter-spacing:1px" rowspan="1" colspan="1">' . date('m/d/Y', strtotime($val->approved_date)) . ' by ' . $val->approved_by . '</td>';
        }
        return $tbody;
    }

    public function ReportTableHead($category) {
        $thead = '';
        if ($category == 0) {
            $thead = '<tr><td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Employee</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Leave Days</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Total Days<br><span style="font-size:10px">(With Schedule)</span></td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Leave Type</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"   colspan="1">Payment Type</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"   colspan="1">Date Requested</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"   colspan="1">Approved Date</td></tr>';
        } else if ($category == 1) {
            $thead = '<tr>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Employee</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Work Schedule</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">Actual In/Out</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">Total Hours</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">Date Requested</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Approved Date</td>'
                    . '</tr>';
        } else if ($category == 2) {
            $thead = '<tr>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Employee</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Category</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">From Shift</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">To Shift</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">Reliever</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">Date Requested</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Approved Date</td>'
                    . '</tr>';
        } else if ($category == 3) {
            $thead = '<tr>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" rowspan="2" colspan="1">Employee</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" rowspan="2" colspan="1">Overtime Type</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" rowspan="2"  colspan="1">Work Schedule</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" rowspan="2" colspan="1">Overtime</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" rowspan="1" colspan="2">Excess RDOT</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" rowspan="2"  colspan="1">Approved Date</td>'
                    . '</tr>'
                    . '<tr>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center"  colspan="1">Time</td>'
                    . '<td style="border:solid;border-width: thin;font-size:14px;text-align:center" colspan="1">Total Hours</td>'
                    . '</tr>';
        }

        return $thead;
    }

    public function ConstrucTableReport($company, $location, $thead, $page) {
        $table = '  <table frame="box" rules="cols" width="100%">
                    <caption style="font-size:12px">' . $company . '</caption>' .
                '<caption style="font-size:12px">' . $location . '</caption>';
        $table = $table . $thead . $page;
        $table = $table . '</table><p style="page-break-after: always;">&nbsp;</p>';

        return $table;
    }

    public function ApprovedFormsExcelReport() {
        $category = $this->input->post('excel_category');
        $title = array('Leave', 'Undertime', 'Change Schedule', 'Overtime');
        $worksched_in = date('Y-m-d', strtotime($this->input->post('excel_worksched_in'))) . " 00:00:00";
        $worksched_out = date('Y-m-d', strtotime($this->input->post('excel_worksched_out'))) . " 23:59:59";
        $employees = $this->CategoriesEmployees($this->MY_Model->FetchReport($this->StructureChecker((array) json_decode($this->input->post('excel_structure')), "="), $worksched_in, $worksched_out, $category));
        $spreadsheet = new Spreadsheet();
        $tab = 0;
        foreach ($employees as $val) {
            $company = $this->M_structure->FetchStructureName(array('refno' => $val['company']), 'tbl_company');
            $location = $val['location'];
            if ($val['location'] != 'No assigned location') {
                $loc = $this->M_structure->FetchStructureName(array('refno' => $val['location']), 'tbl_location');
                $location = $loc[0]->name;
            }
            $company_first_word = explode(' ', $company[0]->name);
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($tab);
            $sheet = $spreadsheet->getActiveSheet($tab);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
            $spreadsheet->getActiveSheet()->setTitle($company_first_word[0]);
            $this->GenerateExcelReport($company[0]->name, $location, $val['employees'], $category, $sheet);
            $sheet->freezePane('E5');
            $tab++;
        }
        $writer = new Xlsx($spreadsheet);

        $filename = $title[$category] . ' Report as of ' . date('F, d', strtotime($worksched_in)) . ' - ' . date('d', strtotime($worksched_out));

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function GenerateExcelReport($company, $location, $employees, $category, $sheet) {
        $profileno = (count($employees) > 0) ? $employees[0]->profileno : '';
        $this->SetupExcelHeader($sheet, $category, $company, $location);
        $merge_cell_from = 5;
        $merge_cell_to = 5;
        $rowindex = 5;
        $is_last = 0;
        foreach ($employees as $val) {
            $is_last++;
            if ($val->profileno != $profileno) {
                $sheet->mergeCells($sheet->getCellByColumnAndRow(1, $merge_cell_from)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(4, $merge_cell_to - 1)->getCoordinate());
                $merge_cell_from = $merge_cell_to;
                $profileno = $val->profileno;
            }
            if ($is_last == count($employees) && $val->profileno == $profileno) {
                $sheet->mergeCells($sheet->getCellByColumnAndRow(1, $merge_cell_from)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(4, $merge_cell_to)->getCoordinate());
            }
            if ($merge_cell_from == $merge_cell_to) {
                $sheet->setCellValueByColumnAndRow(1, $rowindex, $val->empname);
                $sheet->getStyle($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }
            $data = $this->FormData($val, $category);
            foreach ($data as $row) {
                if ($row['horizontal'] == true) {
                    $sheet->getStyle($sheet->getCellByColumnAndRow($row['from'], $rowindex)->getCoordinate())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $sheet->setCellValueByColumnAndRow($row['from'], $rowindex, $row['value']);
                $sheet->getStyle($sheet->getCellByColumnAndRow($row['from'], $rowindex)->getCoordinate())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->mergeCells($sheet->getCellByColumnAndRow($row['from'], $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow($row['to'], $rowindex)->getCoordinate());
            }
            $sheet->getRowDimension($rowindex)->setRowHeight(21.5);
            $merge_cell_to++;
            $rowindex++;
        }
    }

    public function SetupExcelHeader($sheet, $category, $company, $location) {
        $header_columns = array();
        $last_column = 'T';
        if ($category == 0) {
            $header_columns = array(
                array('from' => 'A', 'to' => 'D', 'value' => 'Employee'),
                array('from' => 'E', 'to' => 'H', 'value' => 'Leave Days'),
                array('from' => 'I', 'to' => 'J', 'value' => "Total Days\n(With Schedule)"),
                array('from' => 'K', 'to' => 'L', 'value' => 'Leave Type'),
                array('from' => 'M', 'to' => 'N', 'value' => 'Payment Type'),
                array('from' => 'O', 'to' => 'P', 'value' => 'Date Requested'),
                array('from' => 'Q', 'to' => 'T', 'value' => 'Approved Date')
            );
        } else if ($category == 1) {
            $header_columns = array(
                array('from' => 'A', 'to' => 'D', 'value' => 'Employee'),
                array('from' => 'E', 'to' => 'H', 'value' => 'Work Schedule'),
                array('from' => 'I', 'to' => 'L', 'value' => "Actual In/Out"),
                array('from' => 'M', 'to' => 'N', 'value' => 'Total Hours'),
                array('from' => 'O', 'to' => 'P', 'value' => 'Date Requested'),
                array('from' => 'Q', 'to' => 'T', 'value' => 'Approved Date')
            );
        } else if ($category == 2) {
            $header_columns = array(
                array('from' => 'A', 'to' => 'D', 'value' => 'Employee'),
                array('from' => 'E', 'to' => 'F', 'value' => 'Category'),
                array('from' => 'G', 'to' => 'J', 'value' => "From Shift"),
                array('from' => 'K', 'to' => 'N', 'value' => 'To Shift'),
                array('from' => 'O', 'to' => 'Q', 'value' => 'Reliever'),
                array('from' => 'R', 'to' => 'S', 'value' => 'Date Requested'),
                array('from' => 'T', 'to' => 'W', 'value' => 'Approved Date')
            );
            $last_column = 'W';
        }
        foreach ($header_columns as $val) {
            $sheet->setCellValue($val['from'] . '3', $val['value']);
            $sheet->mergeCells($val['from'] . '3:' . $val['to'] . '4');
            $sheet->getStyle($val['from'] . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($val['from'] . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($val['from'] . '3')->getFont()->setSize(13);
            $sheet->getStyle($val['from'] . '3')->getAlignment()->setWrapText(true);
        }

        $sheet->setCellValue('A1', $company);
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->mergeCells('A1:' . $last_column . '1');
        $sheet->setCellValue('A2', $location);
        $sheet->getStyle('A2')->getFont()->setSize(15);
        $sheet->mergeCells('A2:' . $last_column . '2');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function FormData($val, $category) {
        $data = array();
        if ($category == 0) {
            $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id'=>$val->leavetype));
            $type = ' ';
            if(count($leavetype)>0){
                $type = $leavetype[0]->name;
            }
            $payment_type = ($val->payment_type == 1) ? 'With Pay' : 'Without Pay';
            $data[] = array('horizontal' => false, 'from' => 5, 'to' => 8, 'value' => date('F d, Y', strtotime($val->fromdate)) . " - " . date('F d, Y', strtotime($val->todate)));
            $data[] = array('horizontal' => true, 'from' => 9, 'to' => 10, 'value' => $val->leavedays);
            $data[] = array('horizontal' => true, 'from' => 11, 'to' => 12, 'value' => $type);
            $data[] = array('horizontal' => true, 'from' => 13, 'to' => 14, 'value' => $payment_type);
            $data[] = array('horizontal' => true, 'from' => 15, 'to' => 16, 'value' => date('m/d/Y', strtotime($val->date_requested)));
            $data[] = array('horizontal' => false, 'from' => 17, 'to' => 20, 'value' => date('m/d/Y', strtotime($val->approved_date)) . " by " . $val->approved_by);
        } else if ($category == 1) {
            $data[] = array('horizontal' => false, 'from' => 5, 'to' => 8, 'value' => date('F d, Y', strtotime($val->worksched_in)) . ' ' . $this->ConvertTo12Format($val->worksched_in) . ' - ' . $this->ConvertTo12Format($val->worksched_out));
            $data[] = array('horizontal' => false, 'from' => 9, 'to' => 12, 'value' => date('F d, Y', strtotime($val->actual_in)) . ' ' . $this->ConvertTo12Format($val->actual_in) . ' - ' . $this->ConvertTo12Format($val->actual_out));
            $data[] = array('horizontal' => true, 'from' => 13, 'to' => 14, 'value' => $this->ComputeTotalUndertime($val));
            $data[] = array('horizontal' => true, 'from' => 15, 'to' => 16, 'value' => date('m/d/Y', strtotime($val->date_requested)));
            $data[] = array('horizontal' => false, 'from' => 17, 'to' => 20, 'value' => date('m/d/Y', strtotime($val->approved_date)) . ' by ' . $val->approved_by);
        } else if ($category == 2) {
            $category = '';
            $reliever = $this->M_employee->FetchEmployee(array('profileno' => $val->reliever_profileno));
            $reliever = (count($reliever) > 0) ? $reliever[0]->lastname . ", " . $reliever[0]->firstname : 'None';
            $category = ($val->shiftchange == 1) ? $category = 'Shift Change' : $category;
            $category = ($val->straightduty == 1) ? ($category != '') ? $category . ' / Straight Duty' : 'Straight Duty' : $category;
            $category = ($val->canceldayoff == 1) ? ($category != '') ? $category . ' / Cancel Day-off' : 'Cancel Day-off' : $category;
            $category = ($val->changedayoff == 1) ? ($category != '') ? $category . ' / Change Day-off' : ' Change Day-off' : $category;
            $data[] = array('horizontal' => true, 'from' => 5, 'to' => 6, 'value' => $category);
            $data[] = array('horizontal' => false, 'from' => 7, 'to' => 10, 'value' => date('F d, Y', strtotime($val->worksched_in)) . ' ' . $this->ConvertTo12Format($val->worksched_in) . ' - ' . $this->ConvertTo12Format($val->worksched_out));
            $data[] = array('horizontal' => false, 'from' => 11, 'to' => 14, 'value' => date('F d, Y', strtotime($val->toshift_datetimein)) . ' ' . $this->ConvertTo12Format($val->toshift_datetimein) . ' - ' . $this->ConvertTo12Format($val->toshift_datetimeout));
            $data[] = array('horizontal' => true, 'from' => 15, 'to' => 17, 'value' => $reliever);
            $data[] = array('horizontal' => true, 'from' => 18, 'to' => 19, 'value' => date('m/d/Y', strtotime($val->date_requested)));
            $data[] = array('horizontal' => false, 'from' => 20, 'to' => 23, 'value' => date('m/d/Y', strtotime($val->approved_date)) . ' by ' . $val->approved_by);
        }
        return $data;
    }

}
