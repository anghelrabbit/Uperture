<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Leave
 *
 * @author MIS
 */
class Leave extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_leave', 'M_leave');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('model_employee', 'M_employee');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Leave Management";
            $data['page'] = 'pages/menu/my_account/leave/leave';
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
                'assets/myjs/my_account/leave/give_credits.js',
            );

            $this->InspectUser('menu/my_account/leave/leave', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FetchLeaveForms() {
        $data = array();
        $struct = (array) json_decode($this->input->post('structure'));
        $where = $this->StructureChecker($struct, "=");
        $page = $this->input->post('page');
        $cancellation_page = $this->input->post('tab_category');
        $column = $this->input->post('columns');
        $empname = $column[4]['search']['value'];
        $type = array();
        if ($column[5]['search']['value'] != '') {
            $type['leavetype'] = $column[5]['search']['value'];
        }
        $datein = date('Y-m-d', strtotime($this->input->post('datefiledin'))) . ' 00:00:00';
        $dateout = date('Y-m-d', strtotime($this->input->post('datefiledout'))) . ' 23:59:59';
        $fetch_data = $this->MY_Model->MyDataTable($this->FormTable('tableleave', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type);
        foreach ($fetch_data as $val) {
            $sub_array = array();
            if ($page == 1) {
                $is_head = 0;
                $is_hr = 0;
                $is_supervisor = 0;
                $roles = $this->CheckRole($val);
                if (isset($roles['HR'])) {
                    $is_hr = 1;
                }
                if (isset($roles['Head']) ||isset($roles['Team Leader'])) {
                    $is_head = 1;
                }
                if (isset($roles['Supervisor'])) {
                    $is_supervisor = 1;
                }
                $sub_array[] = '<button class="btn" style="background-color:#3ED03E;color:white" onclick="leaveModal(' . $is_head . "," . $is_hr . "," . $is_supervisor . ",0," . $val->ID . ')">Check Details</button>';
                $sub_array[] = $val->noted_status;
            } else {
                $sub_array[] = '<label>' . date('m/d/Y', strtotime($val->approved_date)) . '</label><br> <p style="font-size:12px">by ' . $val->approved_by . '</p>';
            }
            $department = $this->FetchDepartmentAssigned($val);
            if (count($department) > 0) {
                $sub_array[] = $department[0]->name;
            } else {
                $sub_array[] = 'None';
            }
            $sub_array[] = date('F d, Y', strtotime($val->date_requested));
            $sub_array[] = $val->empname;
            $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $val->leavetype));
            if (count($leavetype) > 0) {
                if ($leavetype[0]->name == 'Others') {
                    $sub_array[] = $val->ifothers;
                } else {
                    $sub_array[] = $leavetype[0]->name;
                }
            } else {
                $sub_array[] = '';
            }

            if (strtotime($val->fromdate) == strtotime($val->todate)) {
                $sub_array[] = date('F d, Y', strtotime($val->fromdate));
            } else {
                $sub_array[] = date('F d', strtotime($val->fromdate)) . ' to ' . date('F d, Y', strtotime($val->todate));
            }
            $sub_array[] = $val->leavedays;
            $data[] = $sub_array;
        }


        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($fetch_data),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tableleave', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function FetchMyLeave() {
        $datein = date('Y-m-d H:i:s', strtotime($this->input->post('datein') . " 00:00:00"));
        $dateout = date('Y-m-d H:i:s', strtotime($this->input->post('dateout') . " 23:59:59"));
        $result = $this->MY_Model->MyDataTable($this->FormTable('tableleave', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array());
        $data = array();
        foreach ($result as $val) {
            $sub_array = array();
            $cancel_string = '';
            $sub_array[] = $val->is_deleted;
            if ($val->is_deleted == 1 && $val->hr_cancel_status == 1) {
                $cancel_string = 'Canceled';
            } else if ($val->is_deleted == 1) {
                $cancel_string = 'For Cancellation';
            }
            $sub_array[] = '<button class="btn" style="background-color:#3ED03E;color:white" onclick="fetchSpecificLeave(' . $val->ID . ')">Check Details</button><br><div style="text-align: center"><span style="color:white;letter-spacing: 0.4px">' . $cancel_string . '</span></div>';
            $sub_array[] = date('F d, Y', strtotime($val->date_requested));
            if ($val->noted_status == 0) {
                $sub_array[] = 'Approval Pending';
            } else if ($val->noted_status == 1) {
                $sub_array[] = 'Approved on ' . date('m/d/Y', strtotime($val->noted_date));
            } else {
                $sub_array[] = 'Declined on ' . date('m/d/Y', strtotime($val->noted_date));
            }

            if ($val->approved_status == 0) {
                $sub_array[] = 'Approval Pending';
            } else if ($val->approved_status == 1) {
                $sub_array[] = 'Approved on ' . date('m/d/Y', strtotime($val->approved_date));
            } else {
                $sub_array[] = 'Declined on ' . date('m/d/Y', strtotime($val->approved_date));
            }
            $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $val->leavetype));
            if (count($leavetype) > 0) {
                if ($leavetype[0]->name == 'Others') {
                    $sub_array[] = $val->ifothers;
                } else {
                    $sub_array[] = $leavetype[0]->name;
                }
            } else {
                $sub_array[] = '';
            }

            if (strtotime($val->fromdate) == strtotime($val->todate)) {
                $sub_array[] = date('F d, Y', strtotime($val->fromdate));
            } else {
                $sub_array[] = date('F d', strtotime($val->fromdate)) . ' to ' . date('F d, Y', strtotime($val->todate));
            }
            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tableleave', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array()),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function CheckPendingLeave() {
        $result = $this->M_leave->FetchLeaveUsdingID($this->CleanArray(array('id' => $this->input->post('id'))));
        $company = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $result[0]->comID)), 'tbl_company');
        $employee = $this->M_employee->CheckAccount($this->CleanArray(array('profileno' => $result[0]['profileno'])));
        $jobpos = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $employee[0]['jobcode'])));
        $result[0]->fromdate = date('Y-m-d', strtotime($result[0]->fromdate));
        $result[0]->todate = date('Y-m-d', strtotime($result[0]->todate));
        $result[0]->approved_date = date('m/d/Y', strtotime($result[0]->approved_date));
        $result[0]->noted_date = date('m/d/Y', strtotime($result[0]->noted_date));
        $result[0]->counter_sign_date = date('m/d/Y', strtotime($result[0]->counter_sign_date));
        if (count($jobpos) > 0) {
            $result[0]->jobpos = $jobpos[0]->jobname;
        } else {
            $result[0]->jobpos = 'Not assigned';
        }

        $result[0]->hr_delete_date = date('m/d/Y', strtotime($result[0]->hr_delete_date));
        $result[0]->head_delete_date = date('m/d/Y', strtotime($result[0]->head_delete_date));
        $result[0]->supervisor_delete_date = date('m/d/Y', strtotime($result[0]->supervisor_delete_date));

        $result[0]->comID = $company[0]->name;

        if ($result[0]->approved_status == 1 || $result[0]->noted_status == 1 || $result[0]->counter_signed_status == 1) {
            $result[0]->is_updated = 1;
        } else {
            $result[0]->is_updated = 0;
        }

        echo json_encode($result[0]);
    }

    function SaveUpdateLeave() {
        $this->FormRestrictions('leave');
        $result = $this->ValidateErrorsSample($_POST);
        $leave_payment_type = $this->input->post('leave_payment_type');
        $leave_day_category = $this->input->post('leave_day_category');
        $id = $this->input->post('id');
        $leave_type = explode('/', $this->input->post('leave_type'));
        $previous_total = $this->input->post('previous_total');
        $leave_datefrom = $this->input->post('leave_datefrom');
        $leave_dateto = $this->input->post('leave_dateto');
        $leave_reason = $this->input->post('leave_reason');
        $prev_payment_type = $this->input->post('previous_payment_type');
        $total_days = $this->input->post('total_days');

        if (strtotime($this->input->post('leave_datefrom')) == false) {
            $result['success'] = false;
            $result['messages']['leave_datefrom'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('leave_dateto')) == false) {
            $result['success'] = false;
            $result['messages']['leave_dateto'] = 'Invalid Date';
        }
        $emp_credits = $this->M_leavecredits->FetchEmployeeLeaveCredits(array('profileno' => $this->session->userdata('profileno'), 'leavetype' => $leave_type[1]));
        $remaining = -1;
        $taken = 0;
        if (count($emp_credits) > 0) {
            if ($leave_payment_type == 1) {
                if ($id > 0) {
                    if ($prev_payment_type == 1) {
                        $remaining = ($emp_credits[0]->remaining_days + $previous_total) - $total_days;
                        $taken = ($emp_credits[0]->taken_days - $previous_total) + $total_days;
                    } else {
                        $remaining = $emp_credits[0]->remaining_days - $total_days;
                        $taken = $emp_credits[0]->taken_days + $total_days;
                    }
                } else {
                    $remaining = $emp_credits[0]->remaining_days - $total_days;
                    $taken = $emp_credits[0]->taken_days + $total_days;
                }
            } else {
                if ($id > 0) {
                    if ($prev_payment_type == 1) {
                        $remaining = $emp_credits[0]->remaining_days + $previous_total;
                        $taken = $emp_credits[0]->taken_days - $previous_total;
                    }
                } else {
                    $remaining = $emp_credits[0]->remaining_days;
                    $taken = $emp_credits[0]->taken_days;
                }
            }
        }
        if ($remaining < 0 && $leave_payment_type == 1) {
            $result['success'] = false;
            $result['messages'] = $leave_type[0];
            $result['insufficient_credit'] = true;
        }
        if ($result['success'] == true) {
            if (count($emp_credits) > 0 && $leave_payment_type == 1) {
                $this->M_leavecredits->UpdateLeaveCredits(array('id' => $emp_credits[0]->id), array('taken_days' => $taken, 'remaining_days' => $remaining));
            }
            $data = array();
            if ($id == 0) {
                $data['profileno'] = $this->session->userdata('profileno');
                $data['empname'] = $this->session->userdata('empname');
                $data['jobposition'] = $this->session->userdata('jobposition');
                $data['date_requested'] = date('Y-m-d H:i:s');
                $data['year'] = date('Y');
                $data['approved_status'] = 0;
                $data['noted_status'] = 0;
                $data['counter_signed_status'] = 0;
                $data['is_deleted'] = 0;
                $data['hr_cancel_status'] = 0;
                $data['head_cancel_status'] = 0;
                $data['supervisor_cancel_status'] = 0;
            }
            $data['comID'] = $this->session->userdata('company');
            $data['locID'] = $this->session->userdata('location');
            $data['divID'] = $this->session->userdata('division');
            $data['depID'] = $this->session->userdata('department');
            $data['secID'] = $this->session->userdata('section');
            $data['areID'] = $this->session->userdata('area');
            $data['fromdate'] = date("Y-m-d", strtotime($leave_datefrom));
            $data['todate'] = date("Y-m-d", strtotime($leave_dateto));
            $data['reason'] = $leave_reason;
            $data['day_type'] = $leave_day_category;
            $data['payment_type'] = $leave_payment_type;
            $data['leavetype'] = $leave_type[1];
            if ($leave_type[0] == 'Others') {
                $data['ifothers'] = $this->input->post('leave_ifothers');
            }
            $data['leavedays'] = $total_days;
            $this->M_leave->SaveUpdateLeave($id, $this->CleanArray($data));
        }
        echo json_encode($result);
    }

    function FetchSpecificLeave() {
        $id = $this->input->post('id');
        $result = $this->M_leave->FetchLeaveUsdingID($this->CleanArray(array('ID' => $id)));
        $dates = array();
        if (count($result) > 0) {
            $profileno = $result[0]->profileno;
            $company = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $result[0]->comID)), 'tbl_company');
            $employee = $this->M_employee->CheckAccount($this->CleanArray(array('profileno' => $profileno)));
            $jobpos = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $employee[0]->jobcode)));
            $scheds = $this->M_leave->GetWorkDays(
                    date('Y-m-d', strtotime($result[0]->fromdate)), date('Y-m-d', strtotime($result[0]->todate)), $profileno);
            $datein = $result[0]->fromdate;
            while ($datein <= $result[0]->todate) {
                $dates[date('F d, Y', strtotime($datein))] = '';
                $datein = date('Y-m-d', strtotime($datein . '+1 days'));
            }
            foreach ($scheds as $val) {
                $dates[date('F d, Y', strtotime($val->timein))] = date('g:i A', strtotime($val->timein)) . ' - ' . date('g:i A', strtotime($val->timeout));
            }
            $result[0]->fromdate = date('Y-m-d', strtotime($result[0]->fromdate));
            $result[0]->todate = date('Y-m-d', strtotime($result[0]->todate));
            if (count($company) > 0) {

                $result[0]->company = $company[0]->name;
            }
            
            if (count($jobpos) > 0) {
                $result[0]->jobpos = $jobpos[0]->jobname;
            }
            $result[0]->approved_date = date('F d, Y', strtotime($result[0]->approved_date));
            $result[0]->noted_date = date('F d, Y', strtotime($result[0]->noted_date));
            $result[0]->counter_sign_date = date('F d, Y', strtotime($result[0]->counter_sign_date));


            $result[0]->hr_delete_date = date('F d, Y', strtotime($result[0]->hr_delete_date));
            $result[0]->head_delete_date = date('F d, Y', strtotime($result[0]->head_delete_date));
            $result[0]->supervisor_delete_date = date('F d, Y', strtotime($result[0]->supervisor_delete_date));
            $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id'=>$result[0]->leavetype));
            if (count($leavetype) > 0) {
                $result[0]->leavetype = $leavetype[0]->name."/".$leavetype[0]->id;
            }
            if ($result[0]->noted_status != 0 || $result[0]->approved_status != 0 || $result[0]->counter_signed_status != 0) {
                $result[0]->is_updated = 1;
            } else {
                $result[0]->is_updated = 0;
            }
        }

        echo json_encode(array('data' => $result[0], 'dates' => $dates));
    }

    function CheckLeaveDates() {
        $datein = $this->input->post('datein');
        $dateout = $this->input->post('dateout');
        $id = $this->input->post('id');
        $data = array();
        if (strtotime($datein) != false) {

            $result = $this->M_leave->GetWorkDays(
                    date('Y-m-d', strtotime($datein)), date('Y-m-d', strtotime($dateout)), $this->session->userdata('profileno'));
            $leave_dates = array();
            $date_excluded = 0;
            $counter = 0;
            while ($datein <= $dateout) {
                $has_leave = $this->M_leave->FiledLeaveForms($this->session->userdata('profileno'), $this->SetupLeaveSql($this->SetupDates($datein,$datein)), $id);
                if (count($has_leave) > 0) {
                    if ($has_leave[0]->approved_status == 1) {
                        $leave_dates[date('F d, Y', strtotime($datein))] = 'Approved Leave';
                    } else {
                        $leave_dates[date('F d, Y', strtotime($datein))] = 'Pending Leave';
                    }
                    $date_excluded++;
                } else {
                    $leave_dates[date('F d, Y', strtotime($datein))] = '';
                }
                $datein = date('Y-m-d', strtotime($datein . '+1 days'));
            }
            foreach ($result as $val) {
                if ($leave_dates[date('F d, Y', strtotime($val->timein))] == '') {
                    $leave_dates[date('F d, Y', strtotime($val->timein))] = date('g:i A', strtotime($val->timein)) . ' - ' . date('g:i A', strtotime($val->timeout));
                    $counter++;
                }
            }
            $data = array('total_days' => $counter, 'dates' => $leave_dates, 'excluded' => $date_excluded);
        }
        echo json_encode($data);
    }

    function RemoveSpecificLeave() {
        $id = $this->input->post('id');
        $result = $this->M_leave->FetchLeaveUsdingID($this->CleanArray(array('ID' => $id)));
        $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $result[0]->leavetype));
        $emp_credits = $this->M_leavecredits->FetchEmployeeLeaveCredits(array('profileno' => $result[0]->profileno, 'leavetype' => $leavetype[0]->id));
        if ($result[0]->payment_type == 1) {
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
        echo json_encode($this->M_leave->RemoveSpecificLeave($id));
    }

}
