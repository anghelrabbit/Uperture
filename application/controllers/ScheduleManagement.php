<?php

class ScheduleManagement extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('sendsms_helper');

        $this->load->model('model_workschedule', 'M_workschedule');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_schedule_management', 'M_sched');
        $this->load->model('model_dtr', 'M_dtr');
        $this->load->model('model_holiday', 'M_holiday');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Schedule Management";
            $data['page'] = 'pages/menu/schedule_management/schedule_management';

            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
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
                'assets/myjs/utilities/selecting_employees.js',
                'assets/myjs/schedule_management/schedule_management.js',
                'assets/myjs/schedule_management/setup_schedule.js',
            );

            $this->InspectUser('menu/pages/schedule_management/schedule_management', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FetchSchedule() {
        $struct = (array) json_decode($this->input->post('structure'));
        $structure_string = $this->StructureChecker($struct, "=");
        $emp = $this->M_sched->FetchEmployees($structure_string, array());
        $profileno = '';
        $emps = array();
        foreach ($emp as $val) {
            $profileno .= ($profileno != '') ? ' OR profileno= ' . "'" . $val->profileno . "'" : ' profileno= ' . "'" . $val->profileno . "'";
            $emps[$val->profileno]['name'] = $val->firstname . " " . $val->midname . " " . $val->lastname . " " . $val->suffix;
            $emps[$val->profileno]['flex'] = $val->flex;
        }
        echo json_encode(array('profileno' => $profileno, 'emps' => $emps));
    }

    public function FetchEmployeeSchedule() {
//        (date('Y-m-d', strtotime($val->timein)) == date('Y-m-d', strtotime($val->timeout))) ?
//                    date('M d, Y  g:i A', strtotime($val->timein)) . " - " . date('g:i A', strtotime($val->timeout)) :
//                    date('M d ,Y  g:i A', strtotime($val->timein)) . " - <br>" . date('M d, Y  g:i A', strtotime($val->timeout));
        $profileno = $this->input->post('profileno');
        $sched_in = $this->input->post('sched_in');
        $sched_out = $this->input->post('sched_out');
        $emps = (array) json_decode($this->input->post('emps'));
        $sched = array();
        $filter = array();
        if ($profileno != '') {
            $sched = $this->M_sched->FetchEmployeeSchedTable('(' . $profileno . ')', $sched_in, $sched_out);
            $filter = $this->M_sched->EmployeeSchedTableFilter('(' . $profileno . ')', $sched_in, $sched_out);
        }
        $data = array();
        foreach ($sched as $val) {
            $sub_array = array();
            $sub_array[] = '<span style="background-color:#FFB347;color:white" class="btn" onclick="fetchEmpDTR(' . "'" . $val->profileno . "'," . $emps[$val->profileno]->flex . ')">View Schedule</span>';
            $sub_array[] = $emps[$val->profileno]->name;
            $sub_array[] = $val->count_sched;
//            $sub_array[] = date('M d,Y g:i A', strtotime($val->updatorDATE));
//            $sub_array[] = $val->updatorNAME;
            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($sched),
            "recordsFiltered" => $filter,
            "data" => $data
        );
        echo json_encode($output);
    }

    public function CheckSaveSchedule() {
        $result = array('success' => true);
        $day_restrict = $this->input->post('day_restrict');
        $sched_type = $this->input->post('sched_type');
        $scheds = (array) json_decode($this->input->post('scheds'));
        $sched_table = (array) json_decode($this->input->post('sched_table'));
        $temp = array(
            ($this->input->post('sun_box') != null) ? 0 : null => 'Sunday',
            ($this->input->post('mon_box') != null) ? 1 : null => 'Monday',
            ($this->input->post('tue_box') != null) ? 2 : null => 'Tuesday',
            ($this->input->post('wed_box') != null) ? 3 : null => 'Wednesday',
            ($this->input->post('thu_box') != null) ? 4 : null => 'Thursday',
            ($this->input->post('fri_box') != null) ? 5 : null => 'Friday',
            ($this->input->post('sat_box') != null) ? 6 : null => 'Saturday',
        );
        if (count($temp) == 1 && isset($temp[''])) {
            $result['success'] = false;
            $result['messages']['days_sched'] = 'Please choose Days';
        }
        if (strtotime($this->input->post('sched_datein')) == false) {
            $result['success'] = false;
            $result['messages']['sched_datein'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('sched_dateout')) == false) {
            $result['success'] = false;
            $result['messages']['sched_dateout'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('sched_timein')) == false) {
            $result['success'] = false;
            $result['messages']['sched_timein'] = 'Invalid Time';
        }
        if (strtotime($this->input->post('sched_timeout')) == false) {
            $result['success'] = false;
            $result['messages']['sched_timeout'] = 'Invalid Time';
        }
        $data = array();
        $weeks = array();
        if ($result['success'] == true) {
            $datein = $this->input->post('sched_datein');
            $dateout = $this->input->post('sched_dateout');
            while ($datein <= $dateout) {
                $index = date('w', strtotime($datein));
                if (isset($temp[$index])) {
                    $data[$datein]['sched_in'] = $datein . " " . date('H:i:s', strtotime($this->input->post('sched_timein')));
                    $data[$datein]['sched_out'] = $datein . " " . date('H:i:s', strtotime($this->input->post('sched_timeout')));
                    $data[$datein]['dayofweek'] = $temp[$index];
                    if ($day_restrict == 2) {
                        $data[$datein]['sched_out'] = date('Y-m-d', strtotime($datein . '+1 days')) . " " . date('H:i:s', strtotime($this->input->post('sched_timeout')));
                    }
                    $weeks[$index] = $index;
                }
                $datein = date('Y-m-d', strtotime($datein . '+1 days'));
            }
            arsort($weeks);
            $unique_id = uniqid();
            $scheds[$sched_type] = array('REF' . $unique_id => $data);
            arsort($scheds);
            $sched_table[] = array(
                'datein' => $this->input->post('sched_datein'),
                'dateout' => $this->input->post('sched_dateout'),
                'timein' => $this->input->post('sched_timein'),
                'timeout' => $this->input->post('sched_timeout'),
                'weeks' => $weeks,
                'sched_index' => 'REF' . $unique_id,
                'sched_type' => $sched_type,
                'day_restrict' => $day_restrict
            );
        }
        echo json_encode(array('result' => $result, 'data' => $scheds, 'sched_table' => $sched_table));
    }

    public function RemoveSchedRow() {
        $sched_type = $this->input->post('sched_type');
        $sched_ref = $this->input->post('sched');
        $table_index = $this->input->post('table_index');
        $scheds = (array) json_decode($this->input->post('scheds'));
        $sched_table = (array) json_decode($this->input->post('sched_table'));
        unset($scheds[$sched_type]->$sched_ref);
        unset($sched_table[$table_index]);
        $sched_table = array_values($sched_table);
        echo json_encode(array('sched' => $scheds, 'table' => $sched_table));
    }

    public function SchedTable() {
        $sched_table = (array) json_decode($this->input->post('sched_table'));
        $data = array();
        $weeks = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $sched_type = array('Schedule 1', 'Schedule 2', 'Schedule 3', 'Schedule 4', 'Schedule 5');
        $table_index = 0;
        foreach ($sched_table as $val) {
            $week_string = '';
            foreach ($val->weeks as $key => $row) {
                $week_string .= ($week_string == '') ? $weeks[$key] : ',' . $weeks[$key];
            }
            $day_restrict = ($val->day_restrict == 1) ? 'Same Day' : 'Next Day';
            $sub_array = array();
            $sub_array[] = '<span class="btn btn-block" onclick="removeRowSched(' . $val->sched_type . ",'" . $val->sched_index . "'," . $table_index . ')" style="background-color:#F8665E;color:white"><i class="fa fa-times"></i></span>';
            $sub_array[] = $sched_type[$val->sched_type];
            $sub_array[] = date('M d, Y', strtotime($val->datein));
            $sub_array[] = date('M d, Y', strtotime($val->dateout));
            $sub_array[] = $week_string;
            $sub_array[] = date('g:i A', strtotime($val->timein));
            $sub_array[] = date('g:i A', strtotime($val->timeout)) . " (" . $day_restrict . ")";
            $data[] = $sub_array;
            $table_index++;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($sched_table),
            "data" => $data
        );
        echo json_encode($output);
    }

    public function SaveUpdateSchedule() {
        $selected_departments = (array) json_decode($this->input->post('selected_department'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_department'));
        $selectd_profileno = (array) json_decode($this->input->post('selectd_profileno'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselectd_profileno'));
        $scheds = (array) json_decode($this->input->post('scheds'));
        $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);
        $emp = array();
        $result = false;
        if ($where_selected != '' && count($scheds) > 0) {
            $result = true;
            $emp = $this->M_employee->FetchEmployeeTable(0, $where_selected, '', array());
            $delete_string = '';
            $batch_insert = array();
            $sched_log = array();
            $sched_refindex = array();
            $counter = 0;
            foreach ($emp as $index) {
                foreach ($scheds as $key => $row) {
                    foreach ($row as $val) {
                        foreach ($val as $sched) {
                            $delete_string = $this->SetupDeleteQuery($index->profileno, date('Y-m-d', strtotime($sched->sched_in)), $delete_string);
                            $batch_insert[] = array(
                                'profileno' => $index->profileno,
                                'dayofweek' => $sched->dayofweek,
                                'timein' => $sched->sched_in,
                                'timeout' => $sched->sched_out,
                                'updatorNAME' => $this->session->userdata('empname'),
                                'updatorDATE' => date('Y-m-d H:i:s'),
                                'straight_in' => 0,
                                'straight_out' => 0,
                                'type' => $key
                            );
                            if (isset($sched_log[$index->profileno . $sched->sched_in])) {
                                $batch_insert[$sched_log[$index->profileno . $sched->sched_in]['index']]['straight_out'] = 1;
                                $batch_insert[$counter]['straight_in'] = 1;
                                $sched_log[$index->profileno . $sched->sched_out] = array(
                                    'index' => $counter,
                                    'ref_profileno' => $sched_log[$index->profileno . $sched->sched_in]['ref_profileno'],
                                    'ref_in' => $sched_log[$index->profileno . $sched->sched_in]['ref_in'],
                                    'ref_out' => $sched_log[$index->profileno . $sched->sched_in]['ref_out'],
                                );
                                $sched_refindex[] = array(
                                    'ref_schedin' => $sched_log[$index->profileno . $sched->sched_in]['ref_in'],
                                    'ref_schedout' => $sched_log[$index->profileno . $sched->sched_in]['ref_out'],
                                    'ref_profileno' => $sched_log[$index->profileno . $sched->sched_in]['ref_profileno'],
                                    'emp_schedin' => $batch_insert[$counter]['timein'],
                                    'emp_schedout' => $batch_insert[$counter]['timeout'],
                                    'emp_profileno' => $batch_insert[$counter]['profileno'],
                                );
                            } else {
                                $sched_log[$index->profileno . $sched->sched_out] = array(
                                    'index' => $counter,
                                    'ref_profileno' => $batch_insert[$counter]['profileno'],
                                    'ref_in' => $batch_insert[$counter]['timein'],
                                    'ref_out' => $batch_insert[$counter]['timeout'],
//                                    'sched_in'=>
                                );
                            }
                            $counter++;
                        }
                    }
                }
            }
            $this->M_sched->BatchDeleteSchedule($delete_string);
            if ($this->M_sched->BatchInsertSchedule($batch_insert) > 0) {
                $this->UpdateSchedRefIndex($sched_refindex);
            }
        }

        echo json_encode($result);
    }

    public function UpdateSchedRefIndex($data) {
        foreach ($data as $val) {
            $ref_sched = $this->M_sched->FetchSpecificSched(array(
                'profileno' => $val['ref_profileno'],
                'timein' => $val['ref_schedin'],
                'timeout' => $val['ref_schedout'],
            ));
            if (count($ref_sched) > 0) {
                $this->M_sched->UpdateSchedRefIndex(
                        array('schedrefindx' => $ref_sched[0]->indx),
                        array('profileno' => $val['emp_profileno'], 'timein' => $val['emp_schedin'], 'timeout' => $val['emp_schedout'])
                );
            }
        }
    }

    public function SetupDeleteQuery($profileno, $datein, $delete_string) {
        if ($delete_string == '') {
            $delete_string .= 'DELETE from tbl_masterlist_sched WHERE (profileno = ' . "'" . $profileno . "' AND timein >=" . "'" . $datein . " 00:00:00" . "' AND timein <= '" . $datein . " 23:59:59" . "'" . ')';
        } else {
            $delete_string .= ' OR (profileno = ' . "'" . $profileno . "' AND timein >=" . "'" . $datein . " 00:00:00" . "' AND timein <= '" . $datein . " 23:59:59" . "'" . ')';
        }
        return $delete_string;
    }

    public function RemoveSchedule() {
        $id = $this->input->post('id');
        echo json_encode($this->M_sched->RemoveSchedule(array('indx' => $id)));
    }

    public function FetchMonthlyEmpSched() {
        $month_year = $this->input->post('month_year');
        $datein = date('Y-m-d H:i:s', strtotime($month_year . "-01 00:00:00"));
        $dateout = date('Y-m-t H:i:s', strtotime($month_year . "-01 23:59:59"));
        $emp_sched = $this->M_sched->FetchSpecificSched(array('datein' => $datein, 'dateout' => $dateout));
        $scheds = array();
        foreach ($emp_sched as $val) {
            $scheds[date('Y-m-d', strtotime($val->timein))][] = array(
                'name' => $val->lastname . ", " . $val->firstname,
                'schedule_in' => $val->timein,
                'schedule_out' => $val->timeout,
            );
        }
        echo json_encode($scheds);
    }

    public function FetchSchedulesMonthlyView() {
        $month_year = $this->input->post('month_year');
        $emp_sched = (array) json_decode($this->input->post('emp_sched'));
        $month_end = date("t", strtotime($month_year . "-" . '01'));
        $day_names = array('Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6);
        $start = 1;
        $data = array();
        $sub_array = array();
        $counter = 0;
        while ($start <= $month_end) {
            $day_name = date('D', strtotime($month_year . "-" . $start));
            if ($counter > 6) {
                $counter = 0;
                $data[] = $sub_array;
                $sub_array = array();
            }
            $counter = $day_names[$day_name];
            if ($start == 1) {
                $null_days = ($day_names[$day_name] - 1);
                while ($null_days >= 0) {
                    $sub_array[$null_days] = '';
                    $null_days--;
                }
            }
            $added_txt = '';
            if (isset($emp_sched[$month_year . "-" . $start])) {
                $temp_container = array();
                foreach ($emp_sched[$month_year . "-" . $start] as $key => $row) {
                    if (isset($temp_container[$row->name])) {
                        $added_txt = $added_txt . "\n" . date('g:i A', strtotime($row->schedule_in)) . "-" . date('g:i A', strtotime($row->schedule_out));
                    } else {
                        $temp_container[$row->name] = '';
                        $added_txt = $added_txt . "\n\n" . $row->name . "\n" . date('g:i A', strtotime($row->schedule_in)) . "-" . date('g:i A', strtotime($row->schedule_out));
                    }
                }
            }
            if ($added_txt != '') {
                $added_txt = '<span name="show_sched_' . $start . '" class="btn" style="background-color:#3ED03E;color:white" onclick="showSchedClick(' . $start . ')">Show Schedules</span><span class="btn btn-danger hidden" name="hide_sched_' . $start . '" onclick="hideSchedClick(' . $start . ')">Hide Schedules</span><br><textarea name="textarea_sched_' . $start . '" class="hidden" style="width:200px;height:150px;font-size:12px;resize: none;border:black 1px solid;margin-top:5px" readonly>' . $added_txt . '</textarea>';
            }
            $has_sched = ($added_txt != 'No Schedule') ? '<span class="hassched"></span>' : '';
            $sub_array[$counter] = '<span class="badge pull-right" style="font-size:15px">' . $start . '</span>' . '<br><br>' . $added_txt . $has_sched;
            if ($start == $month_end) {
                $counter++;
                while ($counter <= 6) {
                    $sub_array[$counter] = '';
                    $counter++;
                }
                $data[] = $sub_array;
            }
            $counter++;
            $start++;
        }

        $output = array
            (
            "data" => $data
        );
        echo json_encode($output);
    }

    public function FetchEmployeeDTR() {
        $data = array();
        $datein = date('Y-m-d', strtotime($this->input->post('datein')));
        $dateout = date('Y-m-d', strtotime($this->input->post('dateout')));
        $fetch_workschedule = $this->SetupEmployeeSchedule($this->SetupDates($datein, $dateout), $this->input->post('profileno'), $datein, $dateout, $this->input->post('flex'));
        $index = 0;
        foreach ($fetch_workschedule as $key => $val) {
            $has_holiday = $this->M_holiday->FetchHoliday(array('datex' => date('Y-m-d', strtotime($key))));
            if (count($val['scheds']) <= 0) {
                $sub_array = array();
                $sub_array[0] = "";
                $sub_array[3] = 'Day Off';
                $sub_array[6] = 'Day Off';
                $sub_array[7] = 1;
                $sub_array[8] = 1;
                $sub_array[9] = 0;
                $sub_array[10] = 0;
                $sub_array[11] = 0;
                $time = array(
                    'time_start' => date('Y-m-d', strtotime($key)) . " 00:00:00",
                    'time_end' => date('Y-m-d', strtotime($key)) . " 23:59:59");
                $timein = $this->M_dtr->FetchUserDTR('', $time, 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                $timeout = $this->M_dtr->FetchUserDTR('', $time, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
                $sub_array[2] = (count($timein) > 0) ? $this->ConvertTo12Format($timein[0]->timein) . "<br><b>(Day Off)</b>" : 'Day Off';
                $sub_array[5] = (count($timeout) > 0) ? $this->ConvertTo12Format($timeout[0]->timeout) . "<br><b>(Day Off)</b>" : 'Day Off';
                $sub_array[1] = date('F d, Y', strtotime($key));
                $sub_array[4] = date('F d, Y', strtotime($key));
                if (count($has_holiday) > 0) {
                    $sub_array[1] = $sub_array[1] . '<br><br><span style="font-size:11px;color:white">' . $has_holiday[0]->type . "</span><br><span style='color:white'>" . $has_holiday[0]->description . "</span>";
                    $sub_array[11] = 5;
                }
                $data[] = $sub_array;
                $index++;
            } else {
                $datein_holder = '';
                $row_index = 0;
                $colspan_in = 1;
                $punch_row_index = 0;
                $rowspan_in = 1;
                $last_ref_type = '';
                foreach ($val['scheds'] as $val) {
                    $sub_array = array();
                    $sub_array[0] = '';
                    $sub_array[2] = $this->ConvertTo12Format($val['worksched_in']);
                    $sub_array[3] = 'none';
                    $sub_array[4] = date('F d, Y', strtotime($val['worksched_out']));
                    $sub_array[5] = $this->ConvertTo12Format($val['worksched_out']);
                    $sub_array[6] = 'none';
                    $sub_array[7] = 1;
                    $sub_array[8] = 1;
                    $sub_array[9] = 1;
                    $sub_array[10] = 1;
                    $sub_array[11] = 0;
                    $scheds = array();
                    if ($datein_holder != $val['schedrefindex']) {
                        $datein_holder = $val['schedrefindex'];
                        $sub_array[1] = date('F d, Y', strtotime($val['worksched_in']));
                        if (count($has_holiday) > 0) {
                            $sub_array[1] = $sub_array[1] . '<br><br><span style="font-size:11px;color:white">' . $has_holiday[0]->type . "</span><br><span style='color:white'>" . $has_holiday[0]->description . "</span>";
                            $sub_array[11] = 5;
                        }
                        $row_index = $index;
                    } else {
                        $sub_array[1] = '';
                        $colspan_in++;
                    }
                    if ($last_ref_type != $val['ref_type']) {
                        $punch_row_index = $index;
                        $last_ref_type = $val['ref_type'];

                        $sub_array[3] = $this->ConvertTo12Format($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in']);
                        $sub_array[6] = $this->ConvertTo12Format($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_out']);
                        $biom_in = $this->ConvertTo12Format($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in']);
                        $biom_out = $this->ConvertTo12Format($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_out']);
                        if ($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_out'] == "Missing" || $fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in'] == 'Absent') {
                            $sub_array[6] = 'Missing';
                            $biom_out = 'Missing';
                            $sub_array[10] = 3;
                        }
                        if ($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in'] == "Missing" || $fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in'] == 'Absent') {
                            $sub_array[3] = 'Missing';
                            $biom_in = 'Missing';
                            $sub_array[9] = 3;
                        } else if ($fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in'] == "Missing" || $fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in'] == 'Pending') {
                            $sub_array[0] = '';
                            $sub_array[3] = 'Pending';
                            $sub_array[9] = 6;
                            $sub_array[6] = 'Pending';
                            $sub_array[10] = 6;
                        }
                        if ($fetch_workschedule[$key]['bioms'][$val['ref_type']]['late_in'] == true) {
                            $sub_array[9] = 4;
                        }
                        if ($fetch_workschedule[$key]['bioms'][$val['ref_type']]['late_out'] == true) {
                            $sub_array[10] = 4;
                        }
                        $sub_array[0] = "<span class='btn btn-success btn-block' onclick='correctDTR(" . json_encode($fetch_workschedule[$key]['bioms']) . ',"' . $biom_in . '","' . $biom_out . '"' . ")'>DTR Correction</span>";
                        $rowspan_in = 1;
                    } else {
                        $rowspan_in++;
                    }

                    if ($val['has_leave'] == true && $val['leave_category'] != 2) {
                        $sub_array[2] = $this->ConvertTo12Format($val['quarter_in']);
                        $sub_array[5] = $this->ConvertTo12Format($val['quarter_out']);
                        $addon_txt = ($val['leave_category'] == 0) ? '1st' : '2nd';
                        $sub_array[3] = $sub_array[3] . '<br><span class="btn" style="font-size:11px;letter-spacing:0.5px;background-color:#6A55AE;color:white">(' . $addon_txt . ' Quarter Leave: ' . $this->ConvertTo12Format($val['raw_quarterin']) . "-" . $this->ConvertTo12Format($val['raw_quarterout']) . ')</span>';
                        $sub_array[6] = $sub_array[6] . '';
                        $sub_array[0] = '';
                    } else if ($val['leave_category'] == 2) {
                        $sub_array[9] = 2;
                        $sub_array[10] = 2;
                        $sub_array[3] = $fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_in'];
                        $sub_array[6] = $fetch_workschedule[$key]['bioms'][$val['ref_type']]['punch_out'];
                        $sub_array[0] = '';
                    }

                    $index++;

                    $data[] = $sub_array;
                    $data[$row_index][7] = $colspan_in;
                    $data[$punch_row_index][8] = $rowspan_in;
                }
            }
        }
        $output = array("data" => $data);
        echo json_encode($output);
    }

}
