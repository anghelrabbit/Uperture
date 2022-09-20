<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PendingForms
 *
 * @author MIS
 */
class PendingForms extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('model_leave', 'M_leave');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_changeschedule', 'M_changeschedule');
        $this->load->model('model_workschedule', 'M_workschedule');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Pending Forms";
            $data['page'] = 'pages/menu/pendingforms/pending_forms';
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
                'assets/myjs/pendingforms/pending_forms.js',
                'assets/myjs/pendingforms/pending_forms_modal.js',
                'assets/myjs/utilities/approving_buttons.js',
                'assets/myjs/utilities/leave_workdays.js',
            );

            $this->InspectUser('menu/pendingforms/pending_forms', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function ApproveDeclineForm() {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $user = $this->input->post('user');
        $action = $this->input->post('action');
        $for_cancellation = $this->input->post('for_cancellation');
        $data = array();
        if ($for_cancellation == 0) {
            if ($user == 'Head') {
                $data['noted_by'] = $this->session->userdata('empname');
                $data['noted_date'] = date('Y-m-d H:i:s');
                $data['noted_status'] = $action;
            } else if ($user == 'HR') {
                $data['approved_by'] = $this->session->userdata('empname');
                $data['approved_date'] = date('Y-m-d H:i:s');
                $data['approved_status'] = $action;
            } else if ($user == 'Reliever') {
                $data['reliever_status'] = $action;
                $data['reliever_date_status'] = date('Y-m-d H:i:s');
            } else if ($user == 'Supervisor') {
                $data['counter_signed_by'] = $this->session->userdata('empname');
                $data['counter_sign_date'] = date('Y-m-d H:i:s');
                $data['counter_signed_status'] = $action;
            } else if ($user == 'reliever') {
                $data['reliever_date_status'] = date('Y-m-d H:i:s');
                $data['reliever_status'] = $action;
            }
        } else {
            if ($user == 'Head') {
                $data['head_name_deleter'] = $this->session->userdata('empname');
                $data['head_deleter_profileno'] = $this->session->userdata('profileno');
                $data['head_delete_date'] = date('Y-m-d H:i:s');
                $data['head_cancel_status'] = $action;
            } else if ($user == 'HR') {
                $data['hr_name_deleter'] = $this->session->userdata('empname');
                $data['hr_deleter_profileno'] = $this->session->userdata('profileno');
                $data['hr_delete_date'] = date('Y-m-d H:i:s');
                $data['hr_cancel_status'] = $action;
            } else if ($user == 'Supervisor') {
                $data['supervisor_name_deleter'] = $this->session->userdata('empname');
                $data['supervisor_deleter_profileno'] = $this->session->userdata('profileno');
                $data['supervisor_delete_date'] = date('Y-m-d H:i:s');
                $data['supervisor_cancel_status'] = $action;
            } else if ($user == 'reliever') {
                $data['reliever_cancel_date'] = date('Y-m-d H:i:s');
                $data['reliever_cancel_status'] = $action;
            }
        }
        
        if ($table == 0 && $this->input->post('payment_type') == 1) {
            $result = $this->M_leave->FetchLeaveUsdingID($this->CleanArray(array('ID' => $id)));
            if ($for_cancellation == 0 && $action == 2) {
                $this->ReturnEmployeeCredits($result);
            } else if ($for_cancellation == 1 && $action == 1 && $result[0]->approved_status < 2) {
                $this->ReturnEmployeeCredits($result);
            }
        }
        if ($table == 2 && $user == 'HR' && $action == 1) {
            $this->UpdateSchedule($id);
        }

        $res = $this->MY_Model->UpdatePendingForm($this->CleanArray($data), $id, $table);
        echo json_encode($res);
    }

    function ReturnEmployeeCredits($result) {
        $emp_credits = $this->M_leavecredits->FetchEmployeeLeaveCredits(array('profileno' => $result[0]->profileno, 'leavetype' => $result[0]->leavetype));

        $remaining = $emp_credits[0]->remaining_days + $result[0]->leavedays;
        $taken = 0;
        if ($emp_credits[0]->taken_days > $result[0]->leavedays) {
            $taken = $emp_credits[0]->taken_days - $result[0]->leavedays;
        } else {
            $taken = $result[0]->leavedays - $emp_credits[0]->taken_days;
        }
        if ($remaining > $emp_credits[0]->total_days) {
            $remaining = $emp_credits[0]->total_days;
        }
        $this->M_leavecredits->UpdateLeaveCredits(array('id' => $emp_credits[0]->id), array('taken_days' => $taken, 'remaining_days' => $remaining));
    }

    function UpdateSchedule($id) {
        $cs = $this->M_changeschedule->FetchChangeSchedUsdingID(array('id' => $id));
        $change_date = $this->ManipulateScheduleDate(
                date('Y-m-d', strtotime($cs[0]->worksched_in)), date('Y-m-d', strtotime($cs[0]->worksched_out)), date('Y-m-d', strtotime($cs[0]->toshift_datetimein)), date('Y-m-d', strtotime($cs[0]->toshift_datetimeout)));
        $worksched_data = array();
        $toshift_data = array();
        if ($cs[0]->shiftchange == 1 || $cs[0]->canceldayoff == 1 || $cs[0]->changedayoff) {
            if (date('H:i:s', strtotime($cs[0]->toshift_datetimein)) == '11:59:59' && date('H:i:s', strtotime($cs[0]->worksched_in)) == '11:59:59') {
                
            } else {
                if (date('H:i:s', strtotime($cs[0]->toshift_datetimein)) == '11:59:59') {
                    if ($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) {
                        $reliever_sched = $this->SetUpdatedSchedule($cs[0]->reliever_profileno, $cs[0]->toshift_datetimein, $cs[0]->toshift_datetimeout, $cs[0]->worksched_in, $cs[0]->worksched_out);
                        $this->M_workschedule->UpdateUserWorkSchedule($reliever_sched['indx'], $reliever_sched['schedule']);
                        $this->M_workschedule->RemoveSchedule(array('timein' => $cs[0]->worksched_in, 'timeout' => $cs[0]->worksched_out, 'profileno' => $cs[0]->profileno));
                    } else {
                        $worksched_data = $this->SetUpdatedSchedule($cs[0]->profileno, $cs[0]->worksched_in, $cs[0]->worksched_out, date('Y-m-d', strtotime($cs[0]->toshift_datetimein)) . " " . date('H:i:s', strtotime($cs[0]->worksched_in)), $change_date['toshift_out'] . " " . date('H:i:s', strtotime($cs[0]->worksched_out)));
                        $this->M_workschedule->UpdateUserWorkSchedule($worksched_data['indx'], $worksched_data['schedule']);
                    }
                } else if (date('H:i:s', strtotime($cs[0]->worksched_in)) == '11:59:59') {
                    if ($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) {
                        $filer_sched = $this->SetUpdatedSchedule($cs[0]->profileno, $cs[0]->worksched_in, $cs[0]->worksched_out, $cs[0]->toshift_datetimein, $cs[0]->toshift_datetimeout);
                        $this->M_workschedule->UpdateUserWorkSchedule($filer_sched['indx'], $filer_sched['schedule']);
                        $this->M_workschedule->RemoveSchedule(array('timein' => $cs[0]->toshift_datetimein, 'timeout' => $cs[0]->toshift_datetimeout, 'profileno' => $cs[0]->reliever_profileno));
                    } else {
                        $toshift_data = $this->SetUpdatedSchedule($cs[0]->profileno, $cs[0]->toshift_datetimein, $cs[0]->toshift_datetimeout, date('Y-m-d', strtotime($cs[0]->worksched_in)) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimein)), $change_date['fromshift_out'] . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimeout)));
                        $this->M_workschedule->UpdateUserWorkSchedule($toshift_data['indx'], $toshift_data['schedule']);
                    }
                } else {
                    if ($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null && $cs[0]->reliever_profileno !='none' ) {
                        $worksched_data = $this->SetUpdatedSchedule($cs[0]->profileno, $cs[0]->worksched_in, $cs[0]->worksched_out, date('Y-m-d H:i:s', strtotime($cs[0]->toshift_datetimein)), $change_date['toshift_out'] . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimeout)));
                        $this->M_workschedule->UpdateUserWorkSchedule($worksched_data['indx'], $worksched_data['schedule']);
                        $toshift_data = $this->SetUpdatedSchedule($cs[0]->reliever_profileno, $cs[0]->toshift_datetimein, $cs[0]->toshift_datetimeout, date('Y-m-d H:i:s', strtotime($cs[0]->worksched_in)), $change_date['fromshift_out'] . " " . date('H:i:s', strtotime($cs[0]->worksched_out)));
                        $this->M_workschedule->UpdateUserWorkSchedule($toshift_data['indx'], $toshift_data['schedule']);
                    } else {
                        $worksched_data = $this->SetUpdatedSchedule($cs[0]->profileno, $cs[0]->worksched_in, $cs[0]->worksched_out, date('Y-m-d', strtotime($cs[0]->toshift_datetimein)) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimein)), $change_date['toshift_out'] . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimeout)));
                       
                        $this->M_workschedule->UpdateUserWorkSchedule($worksched_data['indx'], $worksched_data['schedule']);
//                        $toshift_data = $this->SetUpdatedSchedule($cs[0]->profileno, $cs[0]->toshift_datetimein, $cs[0]->toshift_datetimeout, date('Y-m-d', strtotime($cs[0]->worksched_in)) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimein)), $change_date['fromshift_out'] . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimeout)));
//                        $this->M_workschedule->UpdateUserWorkSchedule($toshift_data['indx'], $toshift_data['schedule']);
                    }
                }
            }
        }
        $this->UpdateOtherChangeScheduleForms($cs, $id, $change_date);
    }

    public function SetUpdatedSchedule($profileno, $search_schedin, $search_schedout, $new_schedin, $new_schedout) {
        $day_of_week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $schedule = $this->M_workschedule->FetchSpecificUserSchedule(array('timein' => $search_schedin, 'timeout' => $search_schedout, 'profileno' => $profileno));
        $sched_code = $this->M_workschedule->FetchScheduleCode(array('TimeIn' => date('H:i', strtotime($new_schedin)), 'TimeOut' => date('H:i', strtotime($new_schedout))));
        $code = '';
        if (count($sched_code) > 0) {
            $code = $sched_code[0]->Code;
        }
        $new_sched = array();
        if (count($schedule) > 0) {
            $new_sched['indx'] = $schedule[0]->indx;
        } else {
            $new_sched['indx'] = 0;
        }
        $new_sched['schedule'] = array('profileno' => $profileno, 'dayofweek' => $day_of_week[date('w', strtotime($new_schedin))], 'code' => $code, 'timein' => $new_schedin, 'timeout' => $new_schedout, 'updatorDATE'=>date('Y-m-d H:i:s'));
        return $new_sched;
    }

    public function ManipulateScheduleDate($worksched_in, $worksched_out, $toshift_in, $toshift_out) {
        $data = array();
        if (strtotime($worksched_out) > strtotime($worksched_in) && strtotime($toshift_in) == strtotime($toshift_out)) {
            $data['toshift_out'] = date('Y-m-d', strtotime($toshift_out . ' +1 day'));
            $data['fromshift_out'] = date('Y-m-d', strtotime($worksched_out . ' -1 day'));
        }
        if (strtotime($toshift_out) > strtotime($toshift_in) && strtotime($worksched_in) == strtotime($worksched_out)) {
            $data['fromshift_out'] = date('Y-m-d', strtotime($worksched_out . ' +1 day'));
            $data['toshift_out'] = date('Y-m-d', strtotime($toshift_out . ' -1 day'));
        }
        if ((strtotime($worksched_in) == strtotime($worksched_out) && strtotime($toshift_in) == strtotime($toshift_out)) ||
                (strtotime($toshift_out) > strtotime($toshift_in) && strtotime($worksched_out) > strtotime($worksched_in))) {
            $data['fromshift_out'] = $worksched_out;
            $data['toshift_out'] = $toshift_out;
        }
        return $data;
    }

    function UpdateOtherChangeScheduleForms($cs, $id, $change_date) {
        $other_cs_worksched = $this->M_changeschedule->FetchOtherCSForms($cs[0]->worksched_in, $cs[0]->worksched_out, $id, $cs[0]->profileno);
        $other_cs_toshift = $this->M_changeschedule->FetchOtherCSForms($cs[0]->toshift_datetimein, $cs[0]->toshift_datetimeout, $id, ($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->reliever_profileno : $cs[0]->profileno);
        foreach ($other_cs_worksched as $val) {
            $data = array();
            if ($cs[0]->worksched_in == $val->worksched_in) {
                $data['worksched_in'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->toshift_datetimein : $val->worksched_in)) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimein));
                $data['worksched_out'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->toshift_datetimeout : $change_date['fromshift_out'])) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimeout));
            }
            if ($cs[0]->worksched_in == $val->toshift_datetimein) {
                $data['toshift_datetimein'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->toshift_datetimein : $val->toshift_datetimein)) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimein));
                $data['toshift_datetimeout'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->toshift_datetimeout : $change_date['fromshift_out'])) . " " . date('H:i:s', strtotime($cs[0]->toshift_datetimeout));
            }
            $this->M_changeschedule->SaveUpdateChangeSchedule($val->id, $data);
        }
        foreach ($other_cs_toshift as $val) {
            $data = array();
            if ($cs[0]->toshift_datetimein == $val->worksched_in) {
                $data['worksched_in'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->worksched_in : $val->worksched_in)) . " " . date('H:i:s', strtotime($cs[0]->worksched_in));
                $data['worksched_out'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->worksched_out : $change_date['toshift_out'])) . " " . date('H:i:s', strtotime($cs[0]->worksched_out));
            }
            if ($cs[0]->toshift_datetimein == $val->toshift_datetimein) {
                $data['toshift_datetimein'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->worksched_in : $val->toshift_datetimein)) . " " . date('H:i:s', strtotime($cs[0]->worksched_in));
                $data['toshift_datetimeout'] = date('Y-m-d', strtotime(($cs[0]->reliever_profileno != '' && $cs[0]->reliever_profileno != null) ? $cs[0]->worksched_out : $change_date['toshift_out'])) . " " . date('H:i:s', strtotime($cs[0]->worksched_out));
            }
            $this->M_changeschedule->SaveUpdateChangeSchedule($val->id, $data);
        }
    }

}
