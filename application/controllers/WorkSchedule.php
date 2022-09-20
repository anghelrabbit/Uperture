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
 * Description of WorkSchedule
 *
 * @author MIS
 */
class WorkSchedule extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_workschedule', 'M_worksched');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_dtr', 'M_dtr');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('model_workschedule', 'M_workschedule');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Work Schedule";
            $data['page'] = 'pages/menu/reports/workschedule_dtr_late/workschedule_dtr_late';
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
                'assets/myjs/utilities/structure.js',
                'assets/myjs/utilities/payperiod.js',
                'assets/myjs/reports/workschedule_dtr_late/workschedule_dtr_late.js'
            );

            $this->InspectUser('menu/reports/workschedule_dtr_late/workschedule_dtr_late', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function ScheduleSummary() {
        $struct = (array) json_decode($this->input->post('structure'));
        $datein = $this->input->post('datein');
        $dateout = $this->input->post('dateout');
        $where = $this->StructureChecker($struct, "=");
        $emps = $this->M_employee->FetchEmployeeTable(1, $where, array('lastname', 'ASC'), array());
        $data = array();
        foreach ($emps as $val) {
            $sub_array = array();
            $attended = 0;
            $leave = 0;
            $undertime = 0;
            $sched = 0;
            $schedules = $this->SetupEmployeeSchedule($this->SetupDates($datein, $dateout), $val->profileno, $datein, $dateout, $val->timein);
            $cs = $this->MY_Model->ApprovedForms($val->profileno, $datein, $dateout, 'tbl_change_schedule');
            $overtime = $this->MY_Model->ApprovedForms($val->profileno, $datein, $dateout, 'tbl_overtime');
            foreach ($schedules as $row) {
                if ($row['has_undertime'] == true) {
                    $undertime++;
                }
                if ($row['has_leave'] == true) {
                    $leave++;
                }
                if (($row['punch_in'] != 'Missing' && $row['punch_in'] != 'Absent' && $row['punch_in'] != 'Pending' && $row['punch_in'] != '') &&
                        ($row['punch_out'] != 'Missing' && $row['punch_out'] != '')) {
                    $attended++;
                }
                if ($row['worksched_in'] != '') {
                    $sched++;
                }
            }
            $sub_array[] = $val->lastname . ", " . $val->firstname;
            $sub_array[] = $sched;
            $sub_array[] = $attended;
            $sub_array[] = $leave;
            $sub_array[] = $undertime;
            $sub_array[] = count($cs);
            $sub_array[] = count($overtime);
            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($emps),
            "recordsFiltered" => $this->M_employee->EmployeeTableFilter($where, '', array()),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchWorkschedule() {

        $schedin = $this->input->post('schedin');
        $schedout = $this->input->post('schedout');
        $profileno = $this->input->post('profileno');
        if ($profileno == "") {
            $profileno = $this->session->userdata('profileno');
        }
        $data = array(
            'profileno' => $profileno,
            'schedin' => date('Y-m-d', strtotime($schedin)) . " 00:00:00",
            'schedout' => date('Y-m-d', strtotime($schedout)) . " 23:59:59",
        );
        $fetch_data = $this->M_worksched->WorkScheduleTable($this->CleanArray($data));
        $result = array();

        foreach ($fetch_data as $row) {
            $sub_array = array();
            $sub_array[] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($row->code))));
            $sub_array[] = date('F d, Y', strtotime($row->timein)) . " (" . $row->dayofweek . ")";
            $sub_array[] = date('g:i A', strtotime($row->timein)) . " to " . date('g:i A', strtotime($row->timeout));

            $result[] = $sub_array;
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($fetch_data),
            "recordsFiltered" => $this->M_worksched->WorkScheduleTableFiler($this->CleanArray($data)),
            "data" => $result
        );
        echo json_encode($output);
    }

    public function GenerateWorkscheduleReport() {
        $structure = (array) json_decode($this->input->post('structure'));
        $data = (array) json_decode($this->input->post('emp_data'));
        $title = array('Work Schedule Report', 'DTR Report', 'Late Report', 'Days Attended');
        $category = $this->input->post('category');
        $is_pdf = $this->input->post('is_pdf');
        $worksched_in = date('Y-m-d', strtotime($this->input->post('worksched_in')));
        $worksched_out = date('Y-m-d', strtotime($this->input->post('worksched_out')));
        $setupDates = $this->SetDates($worksched_in, $worksched_out);
        $department = $this->ReportDepartment($structure);
        $table = '';
        $tab = 0;
        $spreadsheet = new Spreadsheet();
        foreach ($data as $val) {
            if ($is_pdf == 1) {
                $table = $table . $this->GenerateWorkschedules($val->company, $val->location, $department, $val->employees, $setupDates, $worksched_in, $worksched_out, $category);
            } else {
                $company_first_word = explode(' ', $val->company);
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($tab);
                $sheet = $spreadsheet->getActiveSheet($tab);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
                $spreadsheet->getActiveSheet()->setTitle($company_first_word[0]);
                if ($category != 3) {
                    $this->ExcelHeader($worksched_in, $worksched_out, $sheet, $val->company, $val->location, $department);
                    $this->ExcelWorkSchedule($val->employees, $category, $sheet);
                } else {
                    $this->DaysAttendedHeader($sheet, $val->company, $val->location);
                    $this->DaysAttendedExcelBody($val->employees, $sheet, $worksched_in, $worksched_out);
                }
                $sheet->freezePane('E5');
                $tab++;
            }
        }
        if ($is_pdf == 1) {
            if ($table != '') {
                $this->GeneratePDFreport($table, $title[$category], $worksched_in, $worksched_out);
            } else {
                echo json_encode('<h1>No Report can be generated on ' . date('F d, Y', strtotime($worksched_in)));
            }
        } else {
            $writer = new Xlsx($spreadsheet);
            $filename = $title[$category] . ' Report as of ' . date('F, d', strtotime($worksched_in)) . ' - ' . date('d', strtotime($worksched_out)) . " " . date('Y');

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        }
    }

    public function GeneratePDFreport($table, $pdf_title, $worksched_in, $worksched_out) {
        $this->load->library('pdf');
        $mypdf = array();
        $mypdf['table'] = $table;
        $mypdf['title'] = $pdf_title . " as of " . date('F d', strtotime($worksched_in)) . "-" . date('d', strtotime($worksched_out)) . " " . date('Y');
        $this->pdf->set_paper('Legal', 'landscape');
        $this->pdf->load_view('reports/workschedule_dtr_late/workschedule_dtr_late', $mypdf);
        $this->pdf->render();

        $canvas = $this->pdf->get_canvas();
        $font = Font_Metrics::get_font("helvetica", "bold");

        $this->pdf->stream($pdf_title . " as of " . date('F d', strtotime($worksched_in)) . "-" . date('d', strtotime($worksched_out)) . " " . date('Y') . ".pdf", array('Attachment' => 0));
    }

    public function GenerateWorkschedules($company, $location, $department, $employees, $setupDates, $datein, $dateout, $category) {
        $table = '';
        $emp_string = '';
        $empcount = 0;
        $empcount_total = ($category != 2) ? 16 : 8;
        $total = count($employees);


        $diff = strtotime($dateout) - strtotime($datein);
        foreach ($employees as $schedules) {
            $table_body = '';
            $has_late = 0;
            if ($category == 2) {
                foreach ($schedules[0] as $val) {
                    if ($val->has_late) {
                        $has_late++;
                    }
                }
            }
            if (($category != 2) || ($category == 2 && $has_late > 0)) {
                foreach ($schedules[0] as $val) {
                    if ($val->worksched_in == '' && $val->worksched_out == '') {
                        $table_body = $table_body . '<td style="border:solid;border-width: thin;text-align:center" colspan="2">Off</td>';
                    } else {
                        if ($category == 0) {
                            $table_body = $table_body . '<td style="border:solid;border-width: thin;padding:6px;font-size: 10px; font-weight: lighter;" colspan="2">' . $val->worksched_in . '<br>' . $val->worksched_out . '</td>';
                        } else {
                            if ($val->has_leave == true) {
                                $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $val->type));
                                $leavename = (count($leavetype) > 0) ? $leavetype[0]->name : 'On leave';
                                $table_body = $table_body . '<td style="border:solid;border-width: thin;text-align:center" colspan="2">' . $leavename . '</td>';
                            } else if ($val->punch_in == 'Pending' || $val->punch_in == "Absent") {
                                $table_body = $table_body . '<td style="border:solid;border-width: thin;text-align:center" colspan="2">' . $val->punch_in . '</td>';
                            } else if ($category == 1) {
                                $table_body = $table_body . '<td style="border:solid;border-width: thin;padding:6px;font-size: 10px; font-weight: lighter;" colspan="2">' . $val->punch_in . '<br>' . $val->punch_out;
                                $table_body = ($val->has_undertime != false) ? $table_body . '<br>' . $val->total_ut . '</td>' : $table_body . '</td>';
                            } else if ($val->has_late == false) {
                                $table_body = $table_body . '<td style="border:solid;border-width: thin;text-align:center" colspan="2"></td>';
                            } else {
                                $table_body = $table_body . ' <td style="border:solid;border-width: thin;text-align:center" colspan="2">
                        <label style="font-size:9px">' . date(' g:i A', strtotime($val->undertime_in)) . ' - ' . date('g:i A', strtotime($val->undertime_out)) . '</label>
                        <br><br>
                        <span style="font-size:10px;font-weight:bold">Actual:</span> 
                        <br>
                        <label style="font-size:9px">' . $val->punch_in . ' - ' . $val->punch_out . '</label>
                        <br><br>
                        <span style="font-size:10px;">Late:</span> <label style="font-size:11px">' . " " . $val->total_late . " mins" . '</label></td>';
                            }
                        }
                    }
                }

                $emp_string = $emp_string . '<tr><td style="border:solid;border-width: thin; font-size: 11px; font-weight: bold" colspan="1">' . $schedules[1]->lastname . ", " . $schedules[1]->firstname . " " . $schedules[1]->midname . '</td>' . $table_body . '</tr>';
                $empcount++;
            }
            if ($empcount == $empcount_total) {
                if ($emp_string != '') {
                    $table = $table . $this->ConstructTableReport($company, $location, $diff / (60 * 60 * 24), $department, $setupDates[2], $emp_string);
                    $empcount = 0;
                    $emp_string = '';
                }
            }
            $total--;
        }
        if ($total == 0 && $emp_string != '') {
            $table = $table . $this->ConstructTableReport($company, $location, $diff / (60 * 60 * 24), $department, $setupDates[2], $emp_string);
        }
        return $table;
    }

    public function ConstructTableReport($company, $location, $inout, $department, $dates, $employeesched) {
        $colspan = ($inout * 2) + 3;
        $table = '  <table frame="box" rules="cols" width="100%">
                    <caption style="font-size:12px">' . $company . '</caption>' .
                '<caption style="font-size:12px">' . $location . '</caption>' .
                ' <tr><th style="border:solid;border-width: thin;" colspan="' . $colspan . '">' . $department . '</th></tr>' .
                '<tr><th  style="border:solid;border-width: thin;font-size:10px" rowspan="1" colspan="1">Employee Name</th>' . $dates . '</tr>';
        $table = $table . $employeesched;
        $table = $table . '</table><p style="page-break-after: always;">&nbsp;</p>';
        return $table;
    }

    public function ExcelWorkSchedule($employees, $category, $sheet) {
        $from = 5;
        $to = 6;
        $rowindex = 5;
        foreach ($employees as $schedules) {
            $has_late = 0;
            if ($category == 2) {
                foreach ($schedules[0] as $val) {
                    if ($val->has_late) {
                        $has_late++;
                    }
                }
            }
            if (($category != 2) || ($category == 2 && $has_late > 0)) {
                $sheet->setCellValue($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate(), $schedules[1]->lastname . ", " . $schedules[1]->firstname);
                $sheet->getStyle($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->mergeCells($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(4, $rowindex)->getCoordinate());
                foreach ($schedules[0] as $val) {
                    $timestring = '';
                    $sheet->getRowDimension($rowindex)->setRowHeight(($category == 2) ? 84 : 50);
                    $column_coordinate = $sheet->getCellByColumnAndRow($from, $rowindex)->getCoordinate();
                    $row_coordinate = $sheet->getCellByColumnAndRow($to, $rowindex)->getCoordinate();
                    if ($val->worksched_in == '' && $val->worksched_out == '') {
                        $sheet->setCellValue($column_coordinate, 'Off');
                    } else {
                        if ($category == 0) {
                            $timestring = $this->ConvertTo12Format($val->worksched_in) . "\n" . $this->ConvertTo12Format($val->worksched_out);
                        } else {
                            if ($val->has_leave == true) {
                                $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $val->type));
                                $leavename = (count($leavetype) > 0) ? $leavetype[0]->name : 'On leave';
                                $timestring = $leavename;
                            } else if ($val->punch_in == 'Pending' || $val->punch_in == "Absent") {
                                $timestring = $val->punch_in;
                            } else if ($category == 1) {
                                $timestring = $this->ConvertTo12Format($val->punch_in) . "\n" . $this->ConvertTo12Format($val->punch_out);
                                ($val->has_undertime != false) ? $timestring = $timestring . "\n" . $val->total_ut : '';
                            } else if ($val->has_late == false) {
                                $timestring = '';
                            } else {
                                $timestring = date(' g:i A', strtotime($val->undertime_in)) . " - " . date(' g:i A', strtotime($val->undertime_out)) . "\n" . "Actual:" . "\n" . $val->punch_in . " - " . $val->punch_out . "\n\n Late: " . $val->total_late . " mins";
                            }
                        }
                        $sheet->setCellValue($column_coordinate, $timestring);
                    }
                    $sheet->getStyle($column_coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($column_coordinate)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle($column_coordinate)->getAlignment()->setWrapText(true);
                    $sheet->mergeCells($column_coordinate . ':' . $row_coordinate);
                    $from += 2;
                    $to += 2;
                }
                $from = 5;
                $to = 6;
                $rowindex ++;
            }
        }
    }

    public function ExcelHeader($datein, $dateout, $sheet, $company, $location, $department) {
        $last_column = (((round(strtotime($dateout) - strtotime($datein)) / (60 * 60 * 24)) + 1) * 2) + 4;

        $day_of_week = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $from = 5;
        $to = 6;
        $sheet->setCellValue('A4', 'Employee');
        $sheet->mergeCells('A4:D4');
        while ($datein <= $dateout) {
            $column_coordinate = $sheet->getCellByColumnAndRow($from, 4)->getCoordinate();
            $row_coordinate = $sheet->getCellByColumnAndRow($to, 4)->getCoordinate();

            $from += 2;
            $to += 2;
            $sheet->setCellValue($column_coordinate, date('M d', strtotime($datein)) . ' (' . $day_of_week[date('w', strtotime($datein))] . ')');
            $sheet->mergeCells($column_coordinate . ':' . $row_coordinate);
            $sheet->getStyle($column_coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($column_coordinate)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $datein = date('Y-m-d', strtotime($datein . '+1 days'));
        }

        $sheet->setCellValue('A1', $company);
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->mergeCells('A1:' . $sheet->getCellByColumnAndRow($last_column, 1)->getCoordinate());

        $sheet->setCellValue('A2', $location);
        $sheet->getStyle('A2')->getFont()->setSize(15);
        $sheet->mergeCells('A2:' . $sheet->getCellByColumnAndRow($last_column, 2)->getCoordinate());

        $sheet->setCellValue('A3', $department);
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->mergeCells('A3:' . $sheet->getCellByColumnAndRow($last_column, 3)->getCoordinate());
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function GenerateEmployeeSchedule() {
        $structure = (array) json_decode($this->input->post('structure'));
        $worksched_in = date('Y-m-d', strtotime($this->input->post('worksched_in')));
        $worksched_out = date('Y-m-d', strtotime($this->input->post('worksched_out')));
        $employees = $this->CategoriesEmployees($this->M_employee->FetchEmployeesReport(array(), $this->StructureChecker($structure, "=")));
        foreach ($employees as $index => $val) {
            $comp = $this->M_structure->FetchStructureName(array('refno' => $val['company']), 'tbl_company');
            $employees[$index]['company'] = $comp[0]->name;
            if ($val['location'] != 'No assigned location') {
                $loc = $this->M_structure->FetchStructureName(array('refno' => $val['location']), 'tbl_location');
                $employees[$index]['location'] = $loc[0]->name;
            }
            foreach ($val['employees'] as $key => $row) {
                $employees[$index]['employees'][$key] = array($this->SetupEmployeeSchedule($this->SetupDates($worksched_in, $worksched_out), $row->profileno, $worksched_in, $worksched_out, $row->timein),
                    array('lastname' => $row->lastname, 'firstname' => $row->firstname, 'midname' => $row->midname, 'profileno' => $row->profileno));
            }
        }
        echo json_encode($employees);
    }

    public function DaysAttendedHeader($sheet, $company, $location) {
        $header = array(
            array('text' => $company, 'merge_from' => 'A1', 'merge_to' => 'T1'),
            array('text' => $location, 'merge_from' => 'A2', 'merge_to' => 'T2'),
            array('text' => 'Employeee', 'merge_from' => 'A3', 'merge_to' => 'D4'),
            array('text' => 'Work Days', 'merge_from' => 'E3', 'merge_to' => 'F4'),
            array('text' => 'Attended', 'merge_from' => 'G3', 'merge_to' => 'H4'),
            array('text' => 'Missing', 'merge_from' => 'I3', 'merge_to' => 'J3'),
            array('text' => 'Approved Forms', 'merge_from' => 'K3', 'merge_to' => 'R3'),
            array('text' => 'In', 'merge_from' => 'I4', 'merge_to' => 'I4'),
            array('text' => 'Out', 'merge_from' => 'J4', 'merge_to' => 'J4'),
            array('text' => 'Leave', 'merge_from' => 'K4', 'merge_to' => 'L4'),
            array('text' => 'Undertime', 'merge_from' => 'M4', 'merge_to' => 'N4'),
            array('text' => 'Change Schedule', 'merge_from' => 'O4', 'merge_to' => 'P4'),
            array('text' => 'Overtime', 'merge_from' => 'Q4', 'merge_to' => 'R4'),
            array('text' => "Total Late\n(in minutes)", 'merge_from' => 'S3', 'merge_to' => 'T4'),
        );
        foreach ($header as $val) {
            $sheet->setCellValue($val['merge_from'], $val['text']);
            $sheet->getStyle($val['merge_from'])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($val['merge_from'])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells($val['merge_from'] . ':' . $val['merge_to']);
            $sheet->getStyle($val['merge_from'])->getAlignment()->setWrapText(true);
        }
    }

    public function DaysAttendedExcelBody($employees, $sheet, $datein, $dateout) {
        $rowindex = 5;
        foreach ($employees as $val) {

            $data = array(
                'name' => array('value' => $val[1]->lastname . ", " . $val[1]->firstname, 'from' => 1, 'to' => 4),
                'sched' => array('value' => 0, 'from' => 5, 'to' => 6),
                'attended' => array('value' => 0, 'from' => 7, 'to' => 8),
                'missing_in' => array('value' => 0, 'from' => 9, 'to' => 9),
                'missing_out' => array('value' => 0, 'from' => 10, 'to' => 10),
                'leave' => array('value' => 0, 'from' => 11, 'to' => 12),
                'undertime' => array('value' => 0, 'from' => 13, 'to' => 14),
                'cs' => array('value' => 0, 'from' => 15, 'to' => 16),
                'ot' => array('value' => 0, 'from' => 17, 'to' => 18),
                'late' => array('value' => 0, 'from' => 19, 'to' => 20),
            );
            $cs = $this->MY_Model->ApprovedForms($val[1]->profileno, $datein, $dateout, 'tbl_change_schedule');
            $overtime = $this->MY_Model->ApprovedForms($val[1]->profileno, $datein, $dateout, 'tbl_overtime');
            foreach ($val[0] as $row) {
                if ($row->has_undertime == true) {
                    $data['undertime']['value'] += 1;
                }
                if ($row->has_leave == true) {
                    $data['leave']['value'] += 1;
                }
                if (($row->punch_in != 'Missing' && $row->punch_in != 'Absent' && $row->punch_in != 'Pending' && $row->punch_in != '') &&
                        ($row->punch_out != 'Missing' && $row->punch_out != '')) {
                    $data['attended']['value'] += 1;
                } else if ($row->punch_in == 'Missing') {
                    $data['missing_in']['value'] += 1;
                } else if ($row->punch_out == 'Missing') {
                    $data['missing_out']['value'] += 1;
                }
                if ($row->worksched_in != '') {
                    $data['sched']['value'] += 1;
                }
                if ($row->has_late == true) {
                    $data['late']['value'] = $data['late']['value'] + $row->total_late;
                }
            }
            foreach ($data as $row) {
                $column_coordinate = $sheet->getCellByColumnAndRow($row['from'], $rowindex)->getCoordinate();
                $row_coordinate = $sheet->getCellByColumnAndRow($row['to'], $rowindex)->getCoordinate();
                $sheet->getStyle($column_coordinate)->getAlignment()->setWrapText(true);
                $sheet->setCellValue($column_coordinate, $row['value']);
                $sheet->mergeCells($column_coordinate . ':' . $row_coordinate);
                if ($row['from'] != 1) {
                    $sheet->getStyle($column_coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                $sheet->getStyle($column_coordinate)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }

            $sheet->getRowDimension($rowindex)->setRowHeight(21);
            $rowindex++;
        }
    }

}
