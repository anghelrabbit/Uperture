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
class backup extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_workschedule', 'M_worksched');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_dtr', 'M_dtr');
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
            $schedules = $this->M_workschedule->UserWorkSchedule($datein, $dateout, $val->profileno);
            $undertime = $this->MY_Model->ApprovedForms($val->profileno, $datein, $dateout, 'tbl_undertime');
            $cs = $this->MY_Model->ApprovedForms($val->profileno, $datein, $dateout, 'tbl_change_schedule_copy');
            $overtime = $this->MY_Model->ApprovedForms($val->profileno, $datein, $dateout, 'tbl_overtime');
            foreach ($schedules as $row) {
                $result = $this->CheckLeaveAndUndertime($this->session->userdata('profileno'), $row->timein, $row->timeout);
                if (isset($result['schedule'])) {
                    $data_in = array(
                        'time_start' => date('Y-m-d H:i:s', strtotime($result['schedule'][0]) - 60 * 60 * 7),
                        'time_end' => date('Y-m-d H:i:s', strtotime($result['schedule'][0]) + 60 * 60 * 8));
                    $data_out = array(
                        'time_start' => date('Y-m-d H:i:s', strtotime($result['schedule'][1]) - 60 * 60 * 7),
                        'time_end' => date('Y-m-d H:i:s', strtotime($result['schedule'][1]) + 60 * 60 * 11));
                    $timein = $this->M_dtr->FetchUserDTR($val->profileno, $data_in, 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                    $timeout = $this->M_dtr->FetchUserDTR($val->profileno, $data_out, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
                    if (count($timein) > 0 && count($timeout) > 0) {
                        $attended++;
                    }
                } else {
                    $leave++;
                }
            }
            $sub_array[] = $val->lastname . ", " . $val->firstname;
            $sub_array[] = count($schedules);
            $sub_array[] = $attended;
            $sub_array[] = $leave;
            $sub_array[] = count($undertime);
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

    public function LateReport($company, $location, $department, $employees, $setupDates, $datein, $dateout) {
        $table = '';
        $emp_string = '';
        $empcount = 0;
        $total = count($employees);
        $diff = strtotime($dateout) - strtotime($datein);
        foreach ($employees as $val) {
            $timeins = $this->M_worksched->UserWorkSchedule($datein, $dateout, $val->profileno);
            $daysWithSched = $setupDates[0];
            $dayOffs = $setupDates[1];
            $tbody = $this->CheckLate($timeins, $val->profileno, $val->timein, $daysWithSched, $dayOffs);
            if ($tbody != '') {
                $emp_string = $emp_string . '<tr><td style="border:solid;border-width: thin; font-size: 11px; font-weight: bold" colspan="1">' . $val->lastname . ", " . $val->firstname . " " . $val->midname . '</td>' . $tbody . '</tr>';
                $empcount++;
                if ($empcount == 6) {
                    if ($emp_string != '') {

                        $table = $table . $this->ConstructTableReport($company, $location, $diff / (60 * 60 * 24), $department, $setupDates[2], $emp_string);
                        $empcount = 0;
                        $emp_string = '';
                    }
                }
            }
            $total--;
            if ($total == 0 && $emp_string != '') {
                $table = $table . $this->ConstructTableReport($company, $location, $diff / (60 * 60 * 24), $department, $setupDates[2], $emp_string);
            }
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

    public function GenerateWorkscheduleReport() {
        $structure = (array) json_decode($this->input->post('structure'));
        $title = array('Work Schedule Report', 'DTR Report', 'Late Report');
        $category = $this->input->post('category');
        $is_pdf = $this->input->post('is_pdf');
        $worksched_in = date('Y-m-d', strtotime($this->input->post('worksched_in')));
        $worksched_out = date('Y-m-d', strtotime($this->input->post('worksched_out')));
        $employees = $this->CategoriesEmployees($this->M_employee->FetchEmployeesReport(array(), $this->StructureChecker($structure, "=")));
        $setupDates = $this->SetDates($worksched_in, $worksched_out);
        $department = $this->ReportDepartment($structure);
        $table = '';
        $tab = 0;
        $spreadsheet = new Spreadsheet();
        foreach ($employees as $val) {
            $company = $this->M_structure->FetchStructureName(array('refno' => $val['company']), 'tbl_company');
            $location = $val['location'];
            if ($val['location'] != 'No assigned location') {
                $loc = $this->M_structure->FetchStructureName(array('refno' => $val['location']), 'tbl_location');
                $location = $loc[0]->name;
            }
            if ($is_pdf == 1) {
                $table = $table . $this->GenerateWorkschedules($company[0]->name, $location, $department, $val['employees'], $setupDates, $worksched_in, $worksched_out, $category);
            } else {
                $company_first_word = explode(' ', $company[0]->name);
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($tab);
                $sheet = $spreadsheet->getActiveSheet($tab);
                $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
                $spreadsheet->getActiveSheet()->setTitle($company_first_word[0]);
                $this->ExcelHeader($worksched_in, $worksched_out, $sheet);
                $this->ExcelWorkSchedule($val['employees'], $worksched_in, $worksched_out, $category, $sheet);
                $sheet->freezePane('E5');
                $tab++;
            }
        }
        if ($is_pdf == 1) {
            if ($table != '') {
                $this->GeneratePDFreport($table, $title[$category]);
            } else {
                echo json_encode('<h1>No Report can be generated on ' . date('F d, Y', strtotime($worksched_in)));
            }
        } else {
            $writer = new Xlsx($spreadsheet);
            $filename = $title[$category] . ' Report as of ' . date('F, d', strtotime($worksched_in)) . ' - ' . date('d', strtotime($worksched_out));

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        }
    }

   

    public function GenerateWorkschedules($company, $location, $department, $employees, $setupDates, $datein, $dateout, $category) {
        $table = '';
        $emp_string = '';
        $empcount = 0;
        $total = count($employees);
        $diff = strtotime($dateout) - strtotime($datein);
        foreach ($employees as $employee) {
            $table_body = '';
            $sched = $this->M_worksched->UserWorkSchedule($datein, $dateout, $employee->profileno);
            if (count($sched) > 0) {
                $data = $this->CalculateSchedule($sched, $this->SetupDates($datein, $dateout), $employee, $category);
                foreach ($data as $val) {
                    if ($val == '') {
                        $table_body = $table_body . '<td style="border:solid;border-width: thin;text-align:center" colspan="2">Off</td>';
                    } else if (isset($val['leave'])) {
                        $table_body = $table_body . '<td style="border:solid;border-width: thin;text-align:center" colspan="2">' . $val['leave'] . '</td>';
                    } else {
                        $table_body = $table_body . '<td style="border:solid;border-width: thin;padding:6px;font-size: 10px; font-weight: lighter;" colspan="2">' . $val['schedule'][0] . '<br>' . $val['schedule'][1];
                        $table_body = ($val['schedule'][2] != '') ? $table_body . '<br>' . $val['schedule'][2] . '</td>' : $table_body . '</td>';
                    }
                }
                $emp_string = $emp_string . '<tr><td style="border:solid;border-width: thin; font-size: 11px; font-weight: bold" colspan="1">' . $employee->lastname . ", " . $employee->firstname . " " . $employee->midname . '</td>' . $table_body . '</tr>';
                $empcount++;
                if ($empcount == 16) {
                    if ($emp_string != '') {
                        $table = $table . $this->ConstructTableReport($company, $location, $diff / (60 * 60 * 24), $department, $setupDates[2], $emp_string);
                        $empcount = 0;
                        $emp_string = '';
                    }
                }
            }
            $total--;
        }
        if ($total == 0 && $emp_string != '') {
            $table = $table . $this->ConstructTableReport($company, $location, $diff / (60 * 60 * 24), $department, $setupDates[2], $emp_string);
        }

        return $table;
    }

    public function sampling() {
        foreach ($schedule as $row) {
            $y_m_d = date('Y-m-d', strtotime($row->timein));
            if ($category == 0) {
                $dates[$y_m_d] = array('schedule' => array(0 => date('g:i A', strtotime($row->timein)), 1 => date('g:i A', strtotime($row->timeout)), 2 => '', 3 => ''));
            } else if ($category == 1) {
                $updated_sched = $this->CheckLeaveAndUndertime($employee->profileno, $row->timein, $row->timeout);
                if (isset($updated_sched['schedule'])) {
                    $timein = $this->M_dtr->FetchUserDTR($employee->profileno, array('time_start' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][0]) - 60 * 60 * 7), 'time_end' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][0]) + 60 * 60 * 8)), 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                    $timeout = $this->M_dtr->FetchUserDTR($employee->profileno, array('time_start' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][1]) - 60 * 60 * 7), 'time_end' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][1]) + 60 * 60 * 11)), 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
                    $updated_sched['schedule'][2] = ($updated_sched['schedule'][2] == '(Undertime)') ? ' (UT: ' . number_format((float) $this->ComputeTotalUndertime((object) array('worksched_in' => $row->timein, 'worksched_out' => $row->timeout, 'actual_in' => $updated_sched['schedule'][0], 'actual_out' => $updated_sched['schedule'][1], 'undertime_type' => $updated_sched['schedule'][3])), 2, '.', '') . ')' : '';
                    if (count($timein) <= 0 && count($timeout) <= 0) {
                        $updated_sched['schedule'][0] = (date('Y-m-d', strtotime($row->timein)) < date('Y-m-d')) ? ' Absent' : ' Pending';
                        $updated_sched['schedule'][1] = '';
                    } else {
                        $updated_sched['schedule'][0] = (count($timein) > 0) ? date('g:i A', strtotime($timein[0]->timein)) : "Missing In -";
                        $updated_sched['schedule'][1] = (count($timeout) > 0) ? date('g:i A', strtotime($timeout[0]->timeout)) : " Missing Out";
                    }
                }
                $dates[$y_m_d] = $updated_sched;
            } else if ($category == 2) {
                
            }
        }
    }

    public function CalculateSchedule($schedule, $dates, $employee, $category) {
        foreach ($schedule as $row) {
            $y_m_d = date('Y-m-d', strtotime($row->timein));
            if ($category == 0) {
                $dates[$y_m_d] = array('schedule' => array(0 => date('g:i A', strtotime($row->timein)), 1 => date('g:i A', strtotime($row->timeout)), 2 => '', 3 => ''));
            } else if ($category == 1) {
                $updated_sched = $this->CheckLeaveAndUndertime($employee->profileno, $row->timein, $row->timeout);
                if (isset($updated_sched['schedule'])) {
                    $timein = $this->M_dtr->FetchUserDTR($employee->profileno, array('time_start' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][0]) - 60 * 60 * 7), 'time_end' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][0]) + 60 * 60 * 8)), 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                    $timeout = $this->M_dtr->FetchUserDTR($employee->profileno, array('time_start' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][1]) - 60 * 60 * 7), 'time_end' => date('Y-m-d H:i:s', strtotime($updated_sched['schedule'][1]) + 60 * 60 * 11)), 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
                    $updated_sched['schedule'][2] = ($updated_sched['schedule'][2] == '(Undertime)') ? ' (UT: ' . number_format((float) $this->ComputeTotalUndertime((object) array('worksched_in' => $row->timein, 'worksched_out' => $row->timeout, 'actual_in' => $updated_sched['schedule'][0], 'actual_out' => $updated_sched['schedule'][1], 'undertime_type' => $updated_sched['schedule'][3])), 2, '.', '') . ')' : '';
                    if (count($timein) <= 0 && count($timeout) <= 0) {
                        $updated_sched['schedule'][0] = (date('Y-m-d', strtotime($row->timein)) < date('Y-m-d')) ? ' Absent' : ' Pending';
                        $updated_sched['schedule'][1] = '';
                    } else {
                        $updated_sched['schedule'][0] = (count($timein) > 0) ? date('g:i A', strtotime($timein[0]->timein)) : "Missing In -";
                        $updated_sched['schedule'][1] = (count($timeout) > 0) ? date('g:i A', strtotime($timeout[0]->timeout)) : " Missing Out";
                    }
                }
                $dates[$y_m_d] = $updated_sched;
            } else if ($category == 2) {
                
            }
        }
        return $dates;
    }

    public function ExcelWorkSchedule($employees, $datein, $dateout, $category, $sheet) {
        $from = 5;
        $to = 6;
        $rowindex = 5;
        foreach ($employees as $employee) {
            $sched = $this->M_worksched->UserWorkSchedule($datein, $dateout, $employee->profileno);
            if (count($sched) > 0) {
                $sheet->setCellValue($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate(), $employee->lastname . ", " . $employee->firstname);
                $sheet->mergeCells($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(4, $rowindex)->getCoordinate());
                $data = $this->CalculateSchedule($sched, $this->SetupDates($datein, $dateout), $employee, $category);
                foreach ($data as $val) {
                    $sheet->getRowDimension($rowindex)->setRowHeight(37.5);
                    $column_coordinate = $sheet->getCellByColumnAndRow($from, $rowindex)->getCoordinate();
                    $row_coordinate = $sheet->getCellByColumnAndRow($to, $rowindex)->getCoordinate();
                    if ($val != '') {
                        $timestring = '';
                        if ($val['timein'] == ' Absent' || $val['timein'] == ' Pending') {
                            $timestring = $val['timein'];
                        } else {
                            $timestring = "Hi\nHi";
                        }
                        ($val['undertime'] != '') ? $timestring = $timestring . "\n" . $val['undertime'] : '';
                        $sheet->setCellValue($column_coordinate, $timestring);
                        $sheet->getStyle($column_coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($column_coordinate)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->mergeCells($column_coordinate . ':' . $row_coordinate);
                    } else {
                        $sheet->setCellValue($column_coordinate, 'Off');
                        $sheet->getStyle($column_coordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($column_coordinate)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->mergeCells($column_coordinate . ':' . $row_coordinate);
                    }

                    $from += 2;
                    $to += 2;
                }
                $from = 5;
                $to = 6;
                $rowindex++;
            }
        }
    }

    public function ExcelHeader($datein, $dateout, $sheet) {
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
    }

}
