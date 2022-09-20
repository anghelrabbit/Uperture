<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChangeSchedule
 *
 * @author MIS
 */
class ChangeSchedule extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('sendsms_helper');
        $this->load->model('MY_Model');
        $this->load->model('model_structure', 'M_structure');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_changeschedule', 'M_changeschedule');
    }

    public function FetchRequestedSchedule() {
        $struct = (array) json_decode($this->input->post('structure'));
        $where = $this->StructureChecker($struct, "=");
        $page = $this->input->post('page');
        $column = $this->input->post('columns');
        $empname = $column[4]['search']['value'];
        $cancellation_page = $this->input->post('tab_category');
        $datein = $this->input->post('datefiledin');
        $dateout = $this->input->post('datefiledout');
        $type = array(array('shiftchange' => 1), array('straightduty' => 1), array('canceldayoff' => 1), array('changedayoff' => 1));
        if ($column[5]['search']['value'] != '') {
            $type = $type[$column[5]['search']['value']];
        } else {
            $type = array();
        }
        $result = $this->MY_Model->MyDataTable($this->FormTable('tbl_change_schedule', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type);
        $data = array();
        foreach ($result as $val) {
            $sub_array = array();
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
                $sub_array[] = '<button class="btn btn-success" style="background-color:#3ED03E" onclick="changescheduleModal(' . $is_supervisor . "," . $is_head . "," . $is_hr . ",0," . $val->id . ')">Check Details</button>';
                $sub_array[] = $val->noted_status;
            } else {
                $sub_array[] = '<label>' . date('m/d/Y', strtotime($val->approved_date)) . '</label><br> <p style="font-size:12px">by ' . $val->approved_by . '</p>';
            }
            $department = $this->FetchDepartmentAssigned($val);
            $sub_array[] = $department[0]->name;
            $sub_array[] = date('F d, Y', strtotime($val->date_requested));
            $sub_array[] = $val->empname;
            $category = '';
            if ($val->shiftchange == 1) {
                $category = 'Shift Change';
            }
            if ($val->straightduty == 1) {
                if ($category != '') {
                    $category = $category . ' / Straight Duty';
                } else {
                    $category = 'Straight Duty';
                }
            }
            if ($val->canceldayoff == 1) {
                if ($category != '') {
                    $category = $category . ' / Cancel Day-off';
                } else {
                    $category = 'Cancel Day-off';
                }
            }
            if ($val->changedayoff == 1) {
                if ($category != '') {
                    $category = $category . ' / Change Day-off';
                } else {
                    $category = 'Change Day-off';
                }
            }
            $sub_array[] = $category;
            if (date('H:i:s', strtotime($val->worksched_in)) == '11:59:59') {
                $sub_array[] = date('F d, Y', strtotime($val->worksched_in)) . " Day Off";
            } else {
                $sub_array[] = date('F d, Y', strtotime($val->worksched_in)) . " " . $this->ConvertTo12Format($val->worksched_in) . " - " . $this->ConvertTo12Format($val->worksched_out);
            }
            if (date('H:i:s', strtotime($val->toshift_datetimein)) == '11:59:59') {
                $sub_array[] = date('F d, Y', strtotime($val->toshift_datetimein)) . " Day Off";
            } else {
                $sub_array[] = date('F d, Y', strtotime($val->toshift_datetimein)) . " " . $this->ConvertTo12Format($val->toshift_datetimein) . " - " . $this->ConvertTo12Format($val->toshift_datetimeout);
            }
            $sub_array[] = '';
            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tbl_change_schedule', '', '', $datein, $dateout, $page, $cancellation_page, $where), array()),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchMyChangeSchedule() {
        $datein = date('Y-m-d H:i:s', strtotime($this->input->post('datein') . " 00:00:00"));
        $dateout = date('Y-m-d H:i:s', strtotime($this->input->post('dateout') . " 23:59:59"));
        $result = $this->MY_Model->MyDataTable($this->FormTable('tbl_change_schedule', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array());
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
            $sub_array[] = '<button class="btn" style="background-color:#3ED03E;color:white" onclick="fetchSelectedCS(' . $val->id . ')">Check Details</button><br><div style="text-align: center"><span style="color:white;letter-spacing: 0.4px">' . $cancel_string . '</span></div>';
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
            $category = '';
            if ($val->shiftchange == 1) {
                $category = 'Shift Change';
            }
            if ($val->straightduty == 1) {
                if ($category != '') {
                    $category = $category . ' / Straight Duty';
                } else {
                    $category = 'Straight Duty';
                }
            }
            if ($val->canceldayoff == 1) {
                if ($category != '') {
                    $category = $category . ' / Cancel Day-off';
                } else {
                    $category = 'Cancel Day-off';
                }
            }
            if ($val->changedayoff == 1) {
                if ($category != '') {
                    $category = $category . ' / Change Day-off';
                } else {
                    $category = 'Change Day-off';
                }
            }
            $sub_array[] = $category;

            $sub_array[] = date('m/d/Y', strtotime($val->worksched_in)) . " " . $this->ConvertTo12Format($val->worksched_in) . " - " . $this->ConvertTo12Format($val->worksched_out);
            $sub_array[] = date('m/d/Y', strtotime($val->toshift_datetimein)) . " " . $this->ConvertTo12Format($val->toshift_datetimein) . " - " . $this->ConvertTo12Format($val->toshift_datetimeout);

            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tbl_change_schedule', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array()),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function SaveUpdateChangeSchedule() {
        $result = array('success' => true, 'messages' => array());
        $fromshift_is_dayoff = $this->input->post('fromshift_is_dayoff');
        $toshift_is_dayoff = $this->input->post('toshift_is_dayoff');
        $id = $this->input->post('id');


        $shiftchange = $this->input->post('shiftchange');
        $straightduty = $this->input->post('straightduty');
        $canceldayoff = $this->input->post('canceldayoff');
        $changedayoff = $this->input->post('changedayoff');

        if ($shiftchange == 0 && $straightduty == 0 && $canceldayoff == 0 && $changedayoff == 0) {
            $result['success'] = false;
            $result['messages']['cs_category'] = 'Please choose a category.';
        }

        if ($fromshift_is_dayoff != 1) {
            if ($this->input->post('cs_fromshift_timein') == '') {
                $result['success'] = false;
                $result['messages']['cs_fromshift_timein'] = 'Invalid Time';
            }
            if ($this->input->post('cs_fromshift_timeout') == '') {
                $result['success'] = false;
                $result['messages']['cs_fromshift_timeout'] = 'Invalid Time';
            }
        }
        if ($toshift_is_dayoff != 1) {
            if ($this->input->post('cs_toshift_timein') == '') {
                $result['success'] = false;
                $result['messages']['cs_toshift_timein'] = 'Invalid Time';
            }
            if ($this->input->post('cs_toshift_timeout') == '') {
                $result['success'] = false;
                $result['messages']['cs_toshift_timeout'] = 'Invalid Time';
            }
        }
        if (strtotime($this->input->post('cs_fromshift_datein')) == false) {
            $result['success'] = false;
            $result['messages']['cs_fromshift_datein'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('cs_fromshift_dateout')) == false) {
            $result['success'] = false;
            $result['messages']['cs_fromshift_dateout'] = 'Invalid Date';
        }

        if (strtotime($this->input->post('cs_toshift_datein')) == false) {
            $result['success'] = false;
            $result['messages']['cs_toshift_datein'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('cs_toshift_dateout')) == false) {
            $result['success'] = false;
            $result['messages']['cs_toshift_dateout'] = 'Invalid Date';
        }

        if ($result['success'] == true) {
            if ($id == 0) {
                $data['profileno'] = $this->session->userdata('profileno');
                $data['empname'] = $this->session->userdata('empname');
                $data['reliever_status'] = 0;
                $data['noted_status'] = 0;
                $data['approved_status'] = 0;
                $data['counter_signed_status'] = 0;
                $data['hr_cancel_status'] = 0;
                $data['reliever_cancel_status'] = 0;
                $data['is_deleted'] = 0;
                $data['date_requested'] = date('Y-m-d H:i:s');
            }
            $data['reliever_profileno'] = $this->input->post('reliever');
            $data['shiftchange'] = $shiftchange;
            $data['straightduty'] = $straightduty;
            $data['canceldayoff'] = $canceldayoff;
            $data['changedayoff'] = $changedayoff;

            $data['reason'] = $this->input->post('reason');
            $data['comID'] = $this->session->userdata('company');
            $data['locID'] = $this->session->userdata('location');
            $data['divID'] = $this->session->userdata('division');
            $data['depID'] = $this->session->userdata('department');
            $data['secID'] = $this->session->userdata('section');
            $data['areID'] = $this->session->userdata('area');
            $data['reason'] = $this->input->post('reason');

            if ($fromshift_is_dayoff == 1) {
                $data['worksched_in'] = date('Y-m-d', strtotime($this->input->post('cs_fromshift_datein'))) . " 11:59:59";
                $data['worksched_out'] = date('Y-m-d', strtotime($this->input->post('cs_fromshift_dateout'))) . " 11:59:59";
            } else {
                $data['worksched_in'] = date('Y-m-d H:i:s', strtotime($this->input->post('cs_fromshift_datein') . " " . $this->input->post('cs_fromshift_timein')));
                $data['worksched_out'] = date('Y-m-d H:i:s', strtotime($this->input->post('cs_fromshift_dateout') . " " . $this->input->post('cs_fromshift_timeout')));
            }
            if ($toshift_is_dayoff == 1) {
                $data['toshift_datetimein'] = date('Y-m-d', strtotime($this->input->post('cs_toshift_datein'))) . " 11:59:59";
                $data['toshift_datetimeout'] = date('Y-m-d', strtotime($this->input->post('cs_toshift_dateout'))) . " 11:59:59";
                if ($this->input->post('cs_toshift_timein') != '') {
                    $data['toshift_datetimein'] = date('Y-m-d', strtotime($this->input->post('cs_toshift_datein'))) . " " . date('H:i:s', strtotime($this->input->post('cs_toshift_timein')));
                }
                if ($this->input->post('cs_toshift_timeout') != '') {
                    $data['toshift_datetimeout'] = date('Y-m-d', strtotime($this->input->post('cs_toshift_dateout'))) . " " . date('H:i:s', strtotime($this->input->post('cs_toshift_timeout')));
                }
            } else {
                $data['toshift_datetimein'] = date('Y-m-d H:i:s', strtotime($this->input->post('cs_toshift_datein') . " " . $this->input->post('cs_toshift_timein')));
                $data['toshift_datetimeout'] = date('Y-m-d H:i:s', strtotime($this->input->post('cs_toshift_dateout') . " " . $this->input->post('cs_toshift_timeout')));
            }
            $this->M_changeschedule->SaveUpdateChangeSchedule($id, $data);
        }
        echo json_encode($result);
    }

    public function FetchSpecificCS() {
        $id = $this->input->post('id');
        $result = $this->M_changeschedule->FetchSpecificChangeSchedule(array('id' => $id));
        $company = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $result[0]->comID)), 'tbl_company');
        $result[0]->company = $company[0]->name;
        if (count($result) > 0) {
            $result[0]->date_requested = date('m/d/Y', strtotime($result[0]->date_requested));
            $result[0]->worksched_datein = date('Y-m-d', strtotime($result[0]->worksched_in));
            $result[0]->worksched_dateout = date('Y-m-d', strtotime($result[0]->worksched_out));
            $result[0]->worksched_timein = date('H:i:s', strtotime($result[0]->worksched_in));
            $result[0]->worksched_timeout = date('H:i:s', strtotime($result[0]->worksched_out));
            $result[0]->toshift_datein = date('Y-m-d', strtotime($result[0]->toshift_datetimein));
            $result[0]->toshift_dateout = date('Y-m-d', strtotime($result[0]->toshift_datetimeout));
            $result[0]->toshift_timein = date('H:i:s', strtotime($result[0]->toshift_datetimein));
            $result[0]->toshift_timeout = date('H:i:s', strtotime($result[0]->toshift_datetimeout));
            $data_worksched = array('date_in' => $result[0]->worksched_datein, 'date_out' => $result[0]->worksched_dateout);
            $data_toshift = array('date_in' => $result[0]->toshift_datein, 'date_out' => $result[0]->toshift_dateout);
            $result[0]->reliever_name = '';
            if ($result[0]->reliever_profileno != '' && $result[0]->reliever_profileno != 'none' && $result[0]->reliever_profileno != null) {
                $emp = $this->M_employee->FetchEmployee(array('profileno' => $result[0]->reliever_profileno));
                if (count($emp) > 0) {
                    $result[0]->reliever_name = $emp[0]->lastname . ", " . $emp[0]->firstname;
                }
            }
            if ($result[0]->worksched_timein == '11:59:59') {
                $data_worksched['time_in'] = 'Day Off';
                $data_worksched['time_out'] = 'Day Off';
            } else {
                $data_worksched['time_in'] = $result[0]->worksched_timein;
                $data_worksched['time_out'] = $result[0]->worksched_timeout;
            }


            if ($result[0]->toshift_timein == '11:59:59') {
                $data_toshift['time_in'] = 'Day Off';
                $data_toshift['time_out'] = 'Day Off';
            } else {
                $data_toshift['time_in'] = $result[0]->toshift_timein;
                $data_toshift['time_out'] = $result[0]->toshift_timeout;
            }
            if ($result[0]->noted_status != 0 || $result[0]->approved_status != 0 || $result[0]->counter_signed_status != 0) {
                $result[0]->is_updated = 1;
            } else {
                $result[0]->is_updated = 0;
            }
            $result[0]->reliever_date_status = date('m/d/Y', strtotime($result[0]->reliever_date_status));
            $result[0]->approved_date = date('m/d/Y', strtotime($result[0]->approved_date));
            $result[0]->counter_sign_date = date('m/d/Y', strtotime($result[0]->counter_sign_date));
            $result[0]->noted_date = date('m/d/Y', strtotime($result[0]->noted_date));
        }
        $res = array('info' => $result[0], 'worksched' => $data_worksched, 'toshift' => $data_toshift);
        echo json_encode($res);
    }

}
