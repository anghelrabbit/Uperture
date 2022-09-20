<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Undertime
 *
 * @author MIS
 */
class Undertime extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_undertime', 'M_undertime');
        $this->load->model('model_structure', 'M_structure');
        $this->load->model('MY_Model');
    }

    public function FetchUndertimeForms() {
        $struct = (array) json_decode($this->input->post('structure'));
        $where = $this->StructureChecker($struct, "=");
        $page = $this->input->post('page');
        $column = $this->input->post('columns');
        $cancellation_page = $this->input->post('tab_category');

        $datein = $this->input->post('datefiledin');
        $dateout = $this->input->post('datefiledout');
        $type = array();
        $empname = $column[4]['search']['value'];
        if ($column[5]['search']['value'] != '') {
            $type['undertime_type'] = $column[5]['search']['value'];
        }


        $data = array();
        $result = $this->MY_Model->MyDataTable($this->FormTable('tbl_undertime', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type);
        foreach ($result as $val) {
            $sub_array = array();
            $worksched_in_date = date('m/d/Y', strtotime($val->worksched_in));
            $worksched_in_time = $this->ConvertTo12Format($val->worksched_in);
            $worksched_out_time = $this->ConvertTo12Format($val->worksched_out);

            $actual_in_date = date('m/d/Y', strtotime($val->actual_in));
            $actual_in_time = $this->ConvertTo12Format($val->actual_in);
            $actual_out_time = $this->ConvertTo12Format($val->actual_out);
            if ($page == 1) {
                $is_head = 0;
                $is_hr = 0;
                $is_supervisor = 0;
                $roles = $this->CheckRole($val);
                if (isset($roles['HR'])) {
                    $is_hr = 1;
                }
                if (isset($roles['Head'])) {
                    $is_head = 1;
                }
                if (isset($roles['Scheduler'])) {
                    $is_supervisor = 1;
                }
                $sub_array[] = '<button class="btn" style="background-color:#3ED03E;color:white" onclick="undertimeModal(' . $is_supervisor . "," . $is_head . "," . $is_hr . ",0," . $val->id . ')">Check Details</button>';
                $sub_array[] = $val->noted_status;
            } else {
                $sub_array[] = '<label>' . date('m/d/Y', strtotime($val->approved_date)) . '</label><br> <p style="font-size:12px">by ' . $val->approved_by . '</p>';
            }
            $department = $this->FetchDepartmentAssigned($val);
            $sub_array[] = $department[0]->name;
            $sub_array[] = date('F d, Y', strtotime($val->date_requested));
            $sub_array[] = $val->empname;
            if ($val->undertime_type == null) {
                $sub_array[] = 'Not Specified';
            } else if ($val->undertime_type == 0) {
                $sub_array[] = 'Time-in';
            } else {
                $sub_array[] = 'Time-out';
            }
            $sub_array[] = $worksched_in_date . " " . $worksched_in_time . " - " . $worksched_out_time;
            $sub_array[] = $actual_in_date . " " . $actual_in_time . " - " . $actual_out_time;

            $data[] = $sub_array;
        }


        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tbl_undertime', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchSpecificUndertime() {
        $id = $this->input->post('id');
        $result = $this->M_undertime->FetchUndertime($this->CleanArray(array('id' => $id)));
        $company = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $result[0]->comID)), 'tbl_company');
        $data = array();
        $result[0]->company = $company[0]->name;

        $result[0]->sched_datein = date('Y-m-d', strtotime($result[0]->worksched_in));
        $result[0]->sched_dateout = date('Y-m-d', strtotime($result[0]->worksched_out));
        $result[0]->sched_timein = $this->ConvertTo24Format($result[0]->worksched_in);
        $result[0]->sched_timeout = $this->ConvertTo24Format($result[0]->worksched_out);

        $result[0]->actual_datein = date('Y-m-d', strtotime($result[0]->actual_in));
        $result[0]->actual_dateout = date('Y-m-d', strtotime($result[0]->actual_out));
        $result[0]->actual_timein = $this->ConvertTo24Format($result[0]->actual_in);
        $result[0]->actual_timeout = $this->ConvertTo24Format($result[0]->actual_out);



        $result[0]->approved_date = date('F d, Y', strtotime($result[0]->approved_date));
        $result[0]->noted_date = date('F d, Y', strtotime($result[0]->noted_date));
        $result[0]->counter_sign_date = date('F d, Y', strtotime($result[0]->counter_sign_date));


        $result[0]->hr_delete_date = date('F d, Y', strtotime($result[0]->hr_delete_date));
        $result[0]->head_delete_date = date('F d, Y', strtotime($result[0]->head_delete_date));
        $result[0]->supervisor_delete_date = date('F d, Y', strtotime($result[0]->supervisor_delete_date));

        if ($result[0]->noted_status != 0 || $result[0]->approved_status != 0 || $result[0]->counter_signed_status != 0) {
            $result[0]->is_updated = 1;
        } else {
            $result[0]->is_updated = 0;
        }
        echo json_encode($result[0]);
    }

    public function FetchMyUndertime() {
        $datein = date('Y-m-d H:i:s', strtotime($this->input->post('datein') . " 00:00:00"));
        $dateout = date('Y-m-d H:i:s', strtotime($this->input->post('dateout') . " 23:59:59"));
        $result = $this->MY_Model->MyDataTable($this->FormTable('tbl_undertime', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array());
        $data = array();
        foreach ($result as $val) {
            $cancel_string = '';
            $sub_array = array();
            $sub_array[] = $val->is_deleted;
            if ($val->is_deleted == 1 && $val->hr_cancel_status == 1) {
                $cancel_string = 'Canceled';
            } else if ($val->is_deleted == 1) {
                $cancel_string = 'For Cancellation';
            }
            $sub_array[] = '<button class="btn" style="background-color:#3ED03E;color:white" onclick="fetchSelectedUndertime(' . $val->id . ')">Check Details</button><br><div style="text-align: center"><span style="color:white;letter-spacing: 0.4px">' . $cancel_string . '</span></div>';
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
                $sub_array[0] = 0;
                $sub_array[] = 'Approved on ' . date('m/d/Y', strtotime($val->approved_date));
            } else {
                $sub_array[] = 'Declined on ' . date('m/d/Y', strtotime($val->approved_date));
            }
            $sub_array[] = date('m/d/Y', strtotime($val->worksched_in)) . " " . $this->ConvertTo12Format($val->worksched_in) . " - " . $this->ConvertTo12Format($val->worksched_out);
            $sub_array[] = date('m/d/Y', strtotime($val->actual_in)) . " " . $this->ConvertTo12Format($val->actual_in) . " - " . $this->ConvertTo12Format($val->actual_out);

            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tbl_undertime', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array()),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function SaveUpdateUndertime() {

        $this->FormRestrictions('undertime');
        $result = $this->ValidateErrorsSample($_POST);
        if (strtotime($this->input->post('undertime_actual_datein')) == false) {
            $result['success'] = false;
            $result['messages']['undertime_actual_datein'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('undertime_actual_dateout')) == false) {
            $result['success'] = false;
            $result['messages']['undertime_actual_dateout'] = 'Invalid Date';
        }
        if ($this->input->post('undertime_actualin') == '') {
            $result['success'] = false;
            $result['messages']['undertime_actualin'] = 'Invalid Time';
        }
        if ($this->input->post('undertime_actualout') == '') {
            $result['messages']['undertime_actualout'] = 'Invalid Time';
            $result['success'] = false;
        }
        if ($result['success'] == true) {
            if ($this->input->post('id') <= 0) {
                $data['date_requested'] = date('Y-m-d H:i:s');
                $data['noted_status'] = 0;
                $data['approved_status'] = 0;
                $data['counter_signed_status'] = 0;
                $data['is_deleted'] = 0;
                $data['hr_cancel_status'] = 0;
                $data['head_cancel_status'] = 0;
                $data['supervisor_cancel_status'] = 0;
                $data['profileno'] = $this->session->userdata('profileno');
                $data['empname'] = $this->session->userdata('empname');
            }
            $data['comID'] = $this->session->userdata('company');
            $data['locID'] = $this->session->userdata('location');
            $data['divID'] = $this->session->userdata('division');
            $data['depID'] = $this->session->userdata('department');
            $data['secID'] = $this->session->userdata('section');
            $data['areID'] = $this->session->userdata('area');
//            $department = $this->FetchDepartmentAssigned($data); for mail

            $data['worksched_in'] = date('Y-m-d', strtotime($this->input->post('undertime_worksched_datein'))) . " " . $this->ConvertTo24Format($this->input->post('undertime_worksched_timein'));
            $data['worksched_out'] = date('Y-m-d', strtotime($this->input->post('undertime_worksched_dateout'))) . " " . $this->ConvertTo24Format($this->input->post('undertime_worksched_out'));
            $data['actual_in'] = date('Y-m-d', strtotime($this->input->post('undertime_actual_datein'))) . " " . $this->ConvertTo24Format($this->input->post('undertime_actualin'));
            $data['actual_out'] = date('Y-m-d', strtotime($this->input->post('undertime_actual_dateout'))) . " " . $this->ConvertTo24Format($this->input->post('undertime_actualout'));
            $data['reason'] = $this->input->post('undertime_reason');
            $data['undertime_type'] = $this->input->post('undertime_type');
            $this->M_undertime->SaveUpdateUndertime($this->CleanArray($data), $this->input->post('id'));
        }
        echo json_encode($result);
    }

    public function RequestCancellation() {
        $id = $this->input->post('id');
        $data = array();
        $data['is_deleted'] = 1;
        $data['request_cancel_date'] = date('Y-m-d H:i:s');
        echo json_encode($this->M_undertime->SaveUpdateUndertime($this->CleanArray($data), $id));
    }

    

}
