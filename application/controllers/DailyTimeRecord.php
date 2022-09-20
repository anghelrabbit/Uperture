<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DailyTimeRecord
 *
 * @author MIS
 */
class DailyTimeRecord extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('model_structure', 'M_structure');
        $this->load->model('model_dtr', 'M_dtr');
        $this->load->model('model_workschedule', 'M_workschedule');
        $this->load->model('model_holiday', 'M_holiday');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Daily Time Record";
            $data['page'] = 'pages/menu/my_account/daily_time_record/daily_time_record';

            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/bower_components/fullcalendar/dist/fullcalendar.min.css',
                'assets/vendors/bower_components/sweetalert/sweetalert.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
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
                'assets/vendors/bower_components/moment/moment.js',
                'assets/vendors/bower_components/fullcalendar/dist/fullcalendar.min.js',
                'assets/myjs/my_account/daily_time_record/daily_time_record.js'
            );

            $this->InspectUser('menu/menu/my_account/daily_time_record/daily_time_record', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function MySample() {
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $datein = date('Y-m-d', strtotime($year . "-" . $month . "-" . "01"));
        $dateout = date('Y-m-d', strtotime($year . "-" . $month . "-" . date('t', strtotime($datein))));
        $dates = $this->SetupDates($datein, $dateout);
        $schedules = $this->M_workschedule->UserWorkSchedule($datein, $dateout, $this->session->userdata('profileno'));
        $calendar = array();
        foreach ($schedules as $row) {
            $result = $this->CheckLeaveAndUndertime($this->session->userdata('profileno'), $row->timein, $row->timeout);
            $data = array();
            $data['date'] = date('Y-m-d', strtotime($row->timein));
            if (isset($result['schedule'])) {
                $data['work_schedule'] = $this->ConvertTo12Format($result['schedule'][0]) . " - " . $this->ConvertTo12Format($result['schedule'][1]);
                $data_in = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($result['schedule'][0]) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($result['schedule'][0]) + 60 * 60 * 8));
                $data_out = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($result['schedule'][1]) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($result['schedule'][1]) + 60 * 60 * 11));
                $timein = $this->M_dtr->FetchUserDTR($this->session->userdata('profileno'), $data_in, 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                $timeout = $this->M_dtr->FetchUserDTR($this->session->userdata('profileno'), $data_out, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
                if (count($timein) > 0) {
                    if (strtotime(date('F d, Y', strtotime($row->timein)) . " " . $this->Convert24FormatNoSeconds($row->timein)) >= strtotime(date('F d, Y', strtotime($timein[0]->timein)) . " " . $this->Convert24FormatNoSeconds($timein[0]->timein))) {
                        $data['timein_category'] = 1;
                    } else {
                        $data['timein_category'] = 2;
                    }
                    $data['timein'] = $this->ConvertTo12Format($timein[0]->timein);
                } else {
                    $data['timein_category'] = 3;
                    $data['timein'] = 'Missing In';
                }
                if (count($timeout) > 0) {
                    if (strtotime(date('F d, Y', strtotime($row->timeout)) . " " . $this->Convert24FormatNoSeconds($row->timeout)) <= strtotime(date('F d, Y', strtotime($timeout[0]->timeout)) . " " . $this->Convert24FormatNoSeconds($timeout[0]->timeout))) {
                        $data['timeout_category'] = 1;
                    } else {
                        $data['timeout_category'] = 2;
                    }

                    $data['timeout'] = $this->ConvertTo12Format($timeout[0]->timeout);
                } else {
                    $data['timeout_category'] = 3;
                    $data['timeout'] = 'Missing Out';
                }
                if ($data['timein_category'] == 3 && $data['timeout_category'] == 3) {
                    $data['timein_category'] = 6;
                    $data['absent'] = 'Absent';
                }
            } else {
                $data['timein_category'] = 4;
                if (isset($result['leave'])) {
                    $data['on_leave'] = $result['leave'];
                } else {
                    $data['on_leave'] = 'possible';
                }
            }
            $calendar[] = $data;
            unset($dates[date('Y-m-d', strtotime($row->timein))]);
        }
        foreach ($dates as $row) {
            $data = array();
            $data['timein_category'] = 5;
            $data['date'] = $row;
            $data_in = array(
                'time_start' => date('Y-m-d H:i:s', strtotime($row . " 00:00:00")),
                'time_end' => date('Y-m-d H:i:s', strtotime($row . " 23:59:59")));
            $timein = $this->M_dtr->FetchUserDTR($this->session->userdata('profileno'), $data_in, 'timein', 'tbl_biometric_time_in', 'profileno_timein');
            if (count($timein) > 0) {
                $data['timein'] = $this->ConvertTo12Format($timein[0]->timein);
                $data_out = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($timein[0]->timein)),
                    'time_end' => date('Y-m-d H:i:s', strtotime($timein[0]->timein) + 60 * 60 * 11));
                $timeout = $this->M_dtr->FetchUserDTR($this->session->userdata('profileno'), $data_out, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
                if (count($timeout) > 0) {
                    $data['timeout'] = $this->ConvertTo12Format($timeout[0]->timeout);
                }
            }
            $calendar[] = $data;
        }
        echo json_encode($calendar);
    }

    public function RefreshCurrentDTR() {
        $date_in = date('Y-m-d');
        $date_out = date('Y-m-d');
        $fetch_workschedule = $this->M_workschedule->UserWorkSchedule($date_in, $date_out, $this->session->userdata('profileno'));
        $biometric_in = array();
        $biometric_out = array();

        if (count($fetch_workschedule) > 0) {
            $result = $this->CheckLeaveAndUndertime($this->session->userdata('profileno'), date('Y-m-d H:i:s', strtotime($fetch_workschedule[0]->timein)), date('Y-m-d H:i:s', strtotime($fetch_workschedule[0]->timeout)));
            if (isset($result['schedule'])) {

                $data_in = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($result['schedule'][0]) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($result['schedule'][0]) + 60 * 60 * 8));
                $data_out = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($result['schedule'][1]) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($result['schedule'][1]) + 60 * 60 * 11));
                $punchin = $this->M_dtr->FetchUserDTR($this->session->userdata('profileno'), $data_in, 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                $punchout = $this->M_dtr->FetchUserDTR($this->session->userdata('profileno'), $data_out, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');

                if ($result['schedule'][2] != '(Undertime)') {
                    $result['schedule'][0] = date('Y-m-d H:i:s', strtotime($result['schedule'][0] . ' ' . $this->session->userdata('flex') . ' minutes'));
                }
                if (count($punchin) > 0) {
                    $minutes_in = $this->ComputeLate($result['schedule'][0], $punchin, 0);
                    $biometric_in[] = date('M d, Y', strtotime($punchin[0]->timein)) . " " . $this->ConvertTo12Format($punchin[0]->timein);
                    if ($minutes_in > 0) {
                        $biometric_in[] = array('#FF392E', 'white');
                    } else {
                        $biometric_in[] = array('#3EB3A3', 'white');
                    }
                } else {
                    $biometric_in[] = 'Missing In';
                    $biometric_in[] = array('#FFB347', 'white');
                }
                if (count($punchout) > 0) {
                    $minutes_out = $this->ComputeLate($result['schedule'][1], $punchout, 1);
                    $biometric_out[] = date('M d, Y', strtotime($punchout[0]->timeout)) . " " . $this->ConvertTo12Format($punchout[0]->timeout);
                    if ($minutes_out > 0) {
                        $biometric_out[] = array('#FF392E', 'white');
                    } else {
                        $biometric_out[] = array('#3EB3A3', 'white');
                    }
                } else {
                    $biometric_out[] = 'Missing Out';
                    $biometric_out[] = array('#FFB347', 'white');
                }
            } else {
                $biometric_in[] = date('M d,Y', strtotime($date_in)) . " " . "On Leave";
                $biometric_in[] = array('#6A55AE', 'white');
                $biometric_out[] = date('M d,Y', strtotime($date_in)) . " " . "On Leave";
                $biometric_out[] = array('#6A55AE', 'white');
            }
        } else {
            $biometric_in[] = date('M d,Y', strtotime($date_in)) . " " . "Day off";
            $biometric_in[] = array('#749AC5', 'white');
            $biometric_out[] = date('M d,Y', strtotime($date_in)) . " " . "Day off";
            $biometric_out[] = array('#749AC5', 'white');
        }

        $biometric = array();
        $biometric[] = $biometric_in;
        $biometric[] = $biometric_out;
        echo json_encode($biometric);
    }

    public function FetchMyDTR() {
        $data = array();
        $datein = date('Y-m-d', strtotime($this->input->post('datein')));
        $dateout = date('Y-m-d', strtotime($this->input->post('dateout')));
        $fetch_workschedule = $this->M_workschedule->UserWorkSchedule($datein, $dateout, $this->session->userdata('profileno'));

        $setupDate = array();

        while ($datein <= $dateout) {
            $data_in = array(
                'time_start' => date('Y-m-d', strtotime($datein)) . " 00:00:00",
                'time_end' => date('Y-m-d', strtotime($datein)) . " 23:59:59");
            $data_out = array(
                'time_start' => date('Y-m-d', strtotime($datein)) . " 00:00:00",
                'time_end' => date('Y-m-d', strtotime($datein)) . " 23:59:59");
            $sched = array();
            $sched[0] = 'Day Off';
            $sched[1] = $datein;
            $sched[2] = $datein;
            $sched[3] = $data_in;
            $sched[4] = $data_out;
            $sched[5] = '';
            $setupDate[date('Y-m-d', strtotime($datein))] = $sched;
            $datein = date('Y-m-d', strtotime($datein . '+1 days'));
        }
        foreach ($fetch_workschedule as $val) {
            $schedule = $this->CheckLeaveAndUndertime($this->session->userdata('profileno'), $val->timein, $val->timeout);
            $sched = array();
            if (isset($schedule['leave'])) {
                $sched[0] = $schedule['leave'];
                $sched[1] = $setupDate[date('Y-m-d', strtotime($val->timein))][1];
                $sched[2] = $setupDate[date('Y-m-d', strtotime($val->timein))][2];
                $sched[3] = $setupDate[date('Y-m-d', strtotime($val->timein))][3];
                $sched[4] = $setupDate[date('Y-m-d', strtotime($val->timein))][4];
                $sched[5] = '';
            } else {
                $sched[0] = 'With Schedule';
                $sched[1] = $schedule['schedule'][0];
                $sched[2] = $schedule['schedule'][1];
                $sched[3] = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($schedule['schedule'][0]) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($schedule['schedule'][0]) + 60 * 60 * 8));
                $sched[4] = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($schedule['schedule'][1]) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($schedule['schedule'][1]) + 60 * 60 * 11));
                $sched[5] = $schedule['schedule'][2];
            }
            $setupDate[date('Y-m-d', strtotime($val->timein))] = $sched;
        }

        foreach ($setupDate as $row) {
            $sub_array = array();
            $sub_array[0] = '';
            $timein = $this->M_dtr->FetchUserDTR('', $row[3], 'timein', 'tbl_biometric_time_in', 'profileno_timein');
            $timeout = $this->M_dtr->FetchUserDTR('', $row[4], 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
            $has_holiday = $this->M_holiday->FetchHoliday(array('datex' => date('Y-m-d', strtotime($row[3]['time_start']))));
            $holiday = '';
            $holiday_type = '';
            if (count($has_holiday) > 0) {
                $holiday = '<br><span style="font-size:12px;letter-spacing:1px">' . $has_holiday[0]->description . '</span>';
                $holiday_type = '<br><span style="font-size:12px;letter-spacing:0.5px">' . $has_holiday[0]->type . '</span>';
            }
            if ($row[0] == 'Day Off') {
                $sub_array[2] = 0;
                $sub_array[5] = 0;
                $sub_array[1] = date('F d, Y', strtotime($row[1]));
                $sub_array[4] = date('F d, Y', strtotime($row[2]));
                if (count($timein) > 0) {
                    $sub_array[3] = $this->ConvertTo12Format($timein[0]->timein) . "<br><b>(Day Off)</b>";
                } else {
                    $sub_array[3] = 'Day Off';
                }
                if (count($timeout) > 0) {
                    $sub_array[6] = $this->ConvertTo12Format($timeout[0]->timeout) . "<br><b>(Day Off)</b>";
                } else {
                    $sub_array[6] = 'Day Off';
                }
            } else if ($row[0] == 'With Schedule') {
                $sub_array[1] = date('F d, Y', strtotime($row[1])) . "<span>&nbsp;&nbsp;&nbsp;</span>" . $this->ConvertTo12Format($row[1]) . " " . $row[5];
                $sub_array[4] = date('F d, Y', strtotime($row[2])) . "<span>&nbsp;&nbsp;&nbsp;</span>" . $this->ConvertTo12Format($row[2]) . " " . $row[5];

                if (count($timein) > 0) {
                    if (strtotime(date('Y-m-d H:i', strtotime($row[1]  . '+2 hours'))) >= strtotime(date('Y-m-d', strtotime($timein[0]->timein)) . " " . $this->Convert24FormatNoSeconds($timein[0]->timein))) {
                        $sub_array[2] = 1;
                    } else {
                        $sub_array[2] = 2;
                    }
                    $sub_array[3] = $this->ConvertTo12Format($timein[0]->timein);
                } else {
                    $sub_array[2] = 3;
                    $sub_array[3] = 'Missing in';
                }

                if (count($timeout) > 0) {
                    if (strtotime(date('Y-m-d H:i', strtotime($row[2]))) <= strtotime(date('Y-m-d', strtotime($timeout[0]->timeout)) . " " . $this->Convert24FormatNoSeconds($timeout [0]->timeout))) {
                        $sub_array[5] = 1;
                    } else {
                        $sub_array[5] = 2;
                    }
                    $sub_array[6] = $this->ConvertTo12Format($timeout [0]->timeout);
                } else {
                    $sub_array[5] = 3;
                    $sub_array[6] = 'Missing out';
                }
            } else {
                $sub_array[1] = date('F d, Y', strtotime($row[1])) . "<span>&nbsp;&nbsp;&nbsp;</span>" . $this->ConvertTo12Format($row[1]);
                $sub_array[2] = 4;
                $sub_array[3] = $row[0];

                $sub_array[4] = date('F d, Y', strtotime($row[2])) . "<span>&nbsp;&nbsp;&nbsp;</span>" . $this->ConvertTo12Format($row[2]);
                $sub_array[5] = 4;
                $sub_array[6] = $row[0];
            }
            $sub_array[1] = '<span style="font-size:15px">' . $sub_array[1] . '<span>' . $holiday;
            $sub_array[4] = '<span style="font-size:15px">' . $sub_array[4] . '<span>' . $holiday;
            $sub_array[3] = $sub_array[3] . $holiday_type;
            $sub_array[6] = $sub_array[6] . $holiday_type;
            $sub_array[7] = count($has_holiday);
            $data[] = $sub_array;
        }

        $output = array
            (
            "data" => $data
        );

        echo json_encode($output);
    }

    public function EmployeePunch() {
        $range_in = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 00:00:00"));
        $range_out = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . " 23:59:59"));
        $is_timein = boolval($this->input->post('is_timein'));
        $result = array('result' => false, 'has_sched' => false, 'has_biom' => false, 'is_early' => false);
        $has_sched = $this->M_dtr->EmployeeHasSched(
                $this->session->userdata('profileno'),
                $range_in,
                $range_out,
                $is_timein
        );
        if (count($has_sched) > 0) {
            $datein = ($is_timein == true) ? $has_sched[0]->timein : $has_sched[0]->timeout;
            $dateout = ($is_timein == true) ? $has_sched[0]->timein : $has_sched[0]->timeout;
            $sched_in = date('Y-m-d', strtotime($datein)) . " 00:00:00";
            $sched_out = date('Y-m-d', strtotime($dateout)) . " 23:59:59";
            $has_biom = $this->M_dtr->FetchEmpPunch(
                    $this->session->userdata('profileno'),
                    $sched_in,
                    $sched_out,
                    $is_timein
            );
            if ($is_timein == 1 && date('Y-m-d H:i:s') < date('Y-m-d H:is', strtotime($has_sched[0]->timein . ' -10 minutes'))) {
                $result['result'] = false;
                $result['is_early'] = true;
            } else {
                if (($is_timein == 1 && $has_sched[0]->timeout > date('Y-m-d H:i:s')) || $is_timein == 0) {
                    if (count($has_biom) <= 0) {
                        $column = ($is_timein == true) ? 'timein' : 'timeout';
                        $punchtype = ($is_timein == 1) ? 0 : 1;
                        $timein_data = array(
                            'profileno' => $this->session->userdata('profileno'),
                            'biometric' => $this->session->userdata('biometric'),
                            'fullname' => $this->session->userdata('empname'),
                            $column => date('Y-m-d H:i:s'),
                            'schedtype' => 0,
                            'punchtype' => $punchtype,
                        );
                        $result['result'] = $this->M_dtr->InsertBiometric($timein_data, $is_timein);
                    } else {
                        $result['result'] = false;
                        $result['has_biom'] = true;
                    }
                } else {
                    $result['result'] = false;
                    $result['has_sched'] = true;
                }
            }
        } else {
            $result['result'] = false;
            $result['has_sched'] = true;
        }
        echo json_encode($result);
    }

}
