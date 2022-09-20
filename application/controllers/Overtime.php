<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Overtime
 *
 * @author MIS
 */
class Overtime extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('sendsms_helper');
        $this->load->model('model_overtime', 'M_overtime');
    }

    public function FetchRequestedOvertime() {
        $struct = (array) json_decode($this->input->post('structure'));
        $where = $this->StructureChecker($struct, "=");
        $page = $this->input->post('page');
        $column = $this->input->post('columns');
        $empname = $column[4]['search']['value'];
        $cancellation_page = $this->input->post('tab_category');
        $datein = $this->input->post('datefiledin');
        $dateout = $this->input->post('datefiledout');
        $type = array();
        if ($column[5]['search']['value'] != '') {
            if ($column[5]['search']['value'] != 'REST DAY OVERTIME') {
                $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => $column[5]['search']['value']), 'like');
                if (count($overtime_type) > 0) {
                    $type['overtime_type'] = $overtime_type[0]->refno;
                }
            } else {
                $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => 'REST DAY OVERTIME(130%)'), 'where');
                $type['overtime_type'] = $overtime_type[0]->refno;
            }
        }

        $data = array();
        $result = $this->MY_Model->MyDataTable($this->FormTable('tbl_overtime', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type);
        foreach ($result as $val) {
            $sub_array = array();
            $worksched_in_date = date('m/d/Y', strtotime($val->worksched_in));
            $worksched_in_time = $this->ConvertTo12Format($val->worksched_in);
            $worksched_out_time = $this->ConvertTo12Format($val->worksched_out);

            $actual_in_date = date('m/d/Y', strtotime($val->ot_timein));
            $actual_in_time = $this->ConvertTo12Format($val->ot_timein);
            $actual_out_time = $this->ConvertTo12Format($val->ot_timeout);


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
                $sub_array[] = '<button class="btn btn-success" style="background-color:#3ED03E" onclick="overtimeModal(' . $is_supervisor . "," . $is_head . "," . $is_hr . "," . $val->id . ')">Check Details</button>';
                $sub_array[] = $val->noted_status;
            } else {
                $sub_array[] = '<label>' . date('m/d/Y', strtotime($val->approved_date)) . '</label><br> <p style="font-size:12px">by ' . $val->approved_by . '</p>';
            }
            $department = $this->FetchDepartmentAssigned($val);
            $sub_array[] = $department[0]->name;
            $sub_array[] = date('F d, Y', strtotime($val->date_requested));
            $sub_array[] = $val->empname;
            $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('refno' => $val->overtime_type), 'where');
            $explode_type = array(0 => '');
            if (count($overtime_type) > 0) {
                $explode_type = explode('(', $overtime_type[0]->incentive);
            }
            $sub_array[] = $explode_type[0];
            $sub_array[] = $worksched_in_date . " " . $worksched_in_time . " - " . $worksched_out_time;
            $sub_array[] = $actual_in_date . " " . $actual_in_time . " - " . $actual_out_time;
            if ($val->excess_rdot_timein != null) {
                $sub_array[] = abs(strtotime($result[0]->excess_rdot_timein) - strtotime($result[0]->excess_rdot_timeout)) / (60 * 60);
            } else {
                $sub_array[] = 'None';
            }

            $data[] = $sub_array;
        }


        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tbl_overtime', $empname, '', $datein, $dateout, $page, $cancellation_page, $where), $type),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchMyOvertime() {
        $datein = date('Y-m-d H:i:s', strtotime($this->input->post('datein') . " 00:00:00"));
        $dateout = date('Y-m-d H:i:s', strtotime($this->input->post('dateout') . " 23:59:59"));
        $result = $this->MY_Model->MyDataTable($this->FormTable('tbl_overtime', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array());
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
            $sub_array[] = '<button class="btn" style="background-color:#3ED03E;color:white" onclick="fetchSelectedOvertime(' . $val->id . ')">Check Details</button><br><div style="text-align: center"><span style="color:white;letter-spacing: 0.4px">' . $cancel_string . '</span></div>';
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
            $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('refno' => $val->overtime_type), 'where');
            $explode_type = array(0 => '');
            if (count($overtime_type) > 0) {
                $explode_type = explode('(', $overtime_type[0]->incentive);
            }

            $sub_array[] = $explode_type[0];
            $sub_array[] = date('m/d/Y', strtotime($val->worksched_in)) . " " . $this->ConvertTo12Format($val->worksched_in) . " - " . $this->ConvertTo12Format($val->worksched_out);
            $sub_array[] = date('m/d/Y', strtotime($val->ot_timein)) . " " . $this->ConvertTo12Format($val->ot_timein) . " - " . $this->ConvertTo12Format($val->ot_timeout);

            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->MY_Model->MyDataTableFiler($this->FormTable('tbl_overtime', '', $this->session->userdata('profileno'), $datein, $dateout, '', '', array()), array()),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchSpecificOvertime() {
        $id = $this->input->post('id');
        $result = $this->M_overtime->FetchOvertimeUsdingID($this->CleanArray(array('id' => $id)));
        $company = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $result[0]->comID)), 'tbl_company');
        $result[0]->compname = $company[0]->name;
        $result[0]->date_requested = date('m/d/Y', strtotime($result[0]->date_requested));
        $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('refno' => $result[0]->overtime_type), 'where');
        if (count($overtime_type) > 0) {
            $explode_type = explode('(', $overtime_type[0]->incentive);
            $result[0]->ot_refno = $explode_type[0];
        }

        $result[0]->workshed_datein = date('Y-m-d', strtotime($result[0]->worksched_in));
        $result[0]->workshed_timein = $this->ConvertTo12Format($result[0]->worksched_in);
        $result[0]->worksched_dateout = date('Y-m-d', strtotime($result[0]->worksched_out));
        $result[0]->worksched_timeout = $this->ConvertTo12Format($result[0]->worksched_out);

        $result[0]->actual_datein = date('Y-m-d', strtotime($result[0]->ot_timein));
        $result[0]->actual_timein = $this->ConvertTo24Format($result[0]->ot_timein);
        $result[0]->actual_dateout = date('Y-m-d', strtotime($result[0]->ot_timeout));
        $result[0]->actual_timeout = $this->ConvertTo24Format($result[0]->ot_timeout);

        $result[0]->excess_rdot_timein = date('F d, Y g:i A', strtotime($result[0]->excess_rdot_timein));
        $result[0]->excess_rdot_timeout = date('F d, Y g:i A', strtotime($result[0]->excess_rdot_timeout));
        $result[0]->total_excess = abs(strtotime($result[0]->excess_rdot_timein) - strtotime($result[0]->excess_rdot_timeout)) / (60 * 60);

        $result[0]->noted_date = date('F d, Y', strtotime($result[0]->noted_date));
        $result[0]->approved_date = date('F d, Y', strtotime($result[0]->approved_date));
        $result[0]->counter_sign_date = date('F d, Y', strtotime($result[0]->counter_sign_date));

        if ($result[0]->noted_status != 0 || $result[0]->approved_status != 0 || $result[0]->counter_signed_status != 0) {
            $result[0]->is_updated = 1;
        } else {
            $result[0]->is_updated = 0;
        }
        echo json_encode($result[0]);
    }

    public function SaveUpdateOvertime() {
        $this->FormRestrictions('overtime');
        $result = $this->ValidateErrorsSample($_POST);
        if (strtotime($this->input->post('overtime_actual_datein')) == false) {
            $result['success'] = false;
            $result['messages']['overtime_actual_datein'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('overtime_actual_dateout')) == false) {
            $result['success'] = false;
            $result['messages']['overtime_actual_dateout'] = 'Invalid Date';
        }
        if ($this->input->post('overtime_actualin') == '') {
            $result['success'] = false;
            $result['messages']['overtime_actualin'] = 'Invalid Time';
        }
        if ($this->input->post('overtime_actualout') == '') {
            $result['messages']['overtime_actualout'] = 'Invalid Time';
            $result['success'] = false;
        }
        if ($this->input->post('overtime_type') == '') {
            $result['messages']['overtime_type'] = 'Please choose a category';
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
            if ($this->input->post('overtime_type') != 'REST DAY OVERTIME') {
                $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => $this->input->post('overtime_type')), 'like');
                if (count($overtime_type) > 0) {
                    $data['overtime_type'] = $overtime_type[0]->refno;
                }
            } else {
                $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => 'REST DAY OVERTIME(130%)'), 'where');
                $data['overtime_type'] = $overtime_type[0]->refno;
                if ($this->input->post('overtime_excess_total') != '') {
                    $overtime_excess = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => 'REST DAY OVERTIME(169%)'), 'where');
                    $data['excess_rdot_refno'] = $overtime_excess[0]->refno;
                    $data['excess_rdot_timein'] = date('Y-m-d H:i:s', strtotime($this->input->post('overtime_excess_from')));
                    $data['excess_rdot_timeout'] = date('Y-m-d H:i:s', strtotime($this->input->post('overtime_excess_to')));
                }
            }
            ($this->input->post('overtime_worksched_timein') == 'Day Off') ? $data['worksched_in'] = $this->input->post('overtime_actual_datein') . " " . '11:59:59' : $data['worksched_in'] = $this->input->post('overtime_actual_datein') . " " . $this->ConvertTo24Format($this->input->post('overtime_worksched_timein'));
            ($this->input->post('overtime_worksched_timein') == 'Day Off') ? $data['worksched_out'] = $this->input->post('overtime_actual_datein') . " " . '11:59:59' : $data['worksched_out'] = $this->input->post('overtime_actual_datein') . " " . $this->ConvertTo24Format($this->input->post('overtime_worksched_out'));
            $data['ot_timein'] = date('Y-m-d', strtotime($this->input->post('overtime_actual_datein'))) . " " . $this->ConvertTo24Format($this->input->post('overtime_actualin'));
            $data['ot_timeout'] = date('Y-m-d', strtotime($this->input->post('overtime_actual_dateout'))) . " " . $this->ConvertTo24Format($this->input->post('overtime_actualout'));
            $data['ot_reason'] = $this->input->post('overtime_reason');

            $this->M_overtime->SaveUpdateOvertime($this->CleanArray($data), $this->input->post('id'));
        }
        echo json_encode($result);
    }

    public function SetupOvertimeTypes() {
        $result = $this->M_overtime->FetchOvertimeTypes();
        $data = array();
        foreach ($result as $val) {
            $val_explode = explode('(', $val->incentive);
            if ($val_explode[0] == 'REST DAY OVERTIME') {
                if ($val->incentive == 'REST DAY OVERTIME(130%)') {
                    $data[$val_explode[0]] = $val_explode[0];
                }
            } else {
                $data[$val_explode[0]] = $val_explode[0];
            }
        }

        echo json_encode($data);
    }

    public function OvertimeTypeRestriction() {
        $data = array();
        $excess_rdot = 8;
        $overtime_type = $this->input->post('overtime_type');
        $worksched_date = date('Y-m-d', strtotime($this->input->post('worksched_date')));
        $worksched_time = $this->input->post('worksched_time');
        $actual_date = date('Y-m-d', strtotime($this->input->post('actual_date')));
        $actual_time = $this->input->post('actual_time');
        if ($overtime_type == 'BEFORE SHIFT OVERTIME') {
            $data = $this->CheckOvertimeDateTime(0, $data, $worksched_date, $worksched_time, $actual_date, $actual_time);
        } else if ($overtime_type == 'AFTER SHIFT OVERTIME') {
            $data = $this->CheckOvertimeDateTime(1, $data, $worksched_date, $worksched_time, $actual_date, $actual_time);
        } else if ($overtime_type == 'REST DAY OVERTIME') {
            if (strtotime($this->input->post('actual_date')) != false && strtotime($actual_time) != false &&
                    strtotime($this->input->post('worksched_date')) != false && strtotime($worksched_time) != false) {
                $total_hours = abs(strtotime($actual_date . " " . $actual_time) - strtotime($worksched_date . " " . $worksched_time)) / (60 * 60);
                if ($total_hours > $excess_rdot) {
                    $timestamp = strtotime($worksched_date . " " . $worksched_time) + 60 * 60 * $excess_rdot;
                    $total_excess = abs(strtotime($actual_date . " " . $actual_time) - $timestamp) / (60 * 60);
                    $data['rdot_start_date'] = date('F d, Y', $timestamp);
                    $data['rdot_start_time'] = date('g:i A', $timestamp);
                    $data['rdot_end_date'] = date('F d, Y', strtotime($actual_date));
                    $data['rdot_end_time'] = date('g:i A', strtotime($actual_time));
                    $data['total_excess'] = $total_excess;
                }
            }
        }
        echo json_encode($data);
    }

    function CheckOvertimeDateTime($category, $data, $worksched_date, $worksched_time, $actual_date, $actual_time) {
        if ($category == 0) {
            if (strtotime($actual_date) != false && strtotime($actual_time) != false) {
                if (strtotime(date('Y-m-d H:i:s', strtotime($worksched_date . " " . $worksched_time))) <= strtotime(date('Y-m-d H:i:s', strtotime($actual_date . " " . $actual_time)))) {
                    $data['actual_date'] = $worksched_date;
                    $data['actual_time'] = date('H:i:s', strtotime($worksched_time));
                } else {
                    $data['actual_date'] = $actual_date;
                    $data['actual_time'] = date('H:i:s', strtotime($actual_time));
                }
            } else if (strtotime($actual_date) != false) {
                if (strtotime($worksched_date) <= strtotime($actual_date)) {
                    $data['actual_date'] = $worksched_date;
                } else {
                    $data['actual_date'] = $actual_date;
                }
            }
        } else {
            if (strtotime($actual_date) != false && strtotime($actual_time) != false) {
                if (strtotime(date('Y-m-d H:i:s', strtotime($worksched_date . " " . $worksched_time))) >= strtotime(date('Y-m-d H:i:s', strtotime($actual_date . " " . $actual_time)))) {
                    $data['actual_date'] = $worksched_date;
                    $data['actual_time'] = date('H:i:s', strtotime($worksched_time));
                } else {
                    $data['actual_date'] = $actual_date;
                    $data['actual_time'] = date('H:i:s', strtotime($actual_time));
                }
            } else if (strtotime($actual_date) != false) {
                if (strtotime($worksched_date) >= strtotime($actual_date)) {
                    $data['actual_date'] = $worksched_date;
                } else {
                    $data['actual_date'] = $actual_date;
                }
            }
        }
        return $data;
    }

    public function RemoveOvertime() {
        $id = $this->input->post('id');
        echo json_encode($this->M_undertime->DeleteUndertime($id), 'tbl_overtime');
    }
    
     public function SaveUpdateOvertimeNotif() {
        $this->FormRestrictions('overtime_notif');
        $result = $this->ValidateErrorsSample($_POST);
        if (strtotime($this->input->post('overtime_actual_datein_notif')) == false) {
            $result['success'] = false;
            $result['messages']['overtime_actual_datein_notif'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('overtime_actual_dateout_notif')) == false) {
            $result['success'] = false;
            $result['messages']['overtime_actual_dateout_notif'] = 'Invalid Date';
        }
        if ($this->input->post('overtime_actualin_notif') == '') {
            $result['success'] = false;
            $result['messages']['overtime_actualin_notif'] = 'Invalid Time';
        }
        if ($this->input->post('overtime_actualout_notif') == '') {
            $result['messages']['overtime_actualout_notif'] = 'Invalid Time';
            $result['success'] = false;
        }
        if ($this->input->post('overtime_type_notif') == '') {
            $result['messages']['overtime_type_notif'] = 'Please choose a category';
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
            if ($this->input->post('overtime_type_notif') != 'REST DAY OVERTIME') {
                $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => $this->input->post('overtime_type_notif')), 'like');
                if (count($overtime_type) > 0) {
                    $data['overtime_type'] = $overtime_type[0]->refno;
                }
            } else {
                $overtime_type = $this->M_overtime->FetchOvertimeTypeReference(array('incentive' => 'REST DAY OVERTIME(130%)'), 'where');
                $data['overtime_type'] = $overtime_type[0]->refno;
               
            }
            ($this->input->post('overtime_worksched_timein_notif') == 'Day Off') ? $data['worksched_in'] = $this->input->post('overtime_actual_datein_notif') . " " . '11:59:59' : $data['worksched_in'] = $this->input->post('overtime_actual_datein_notif') . " " . $this->ConvertTo24Format($this->input->post('overtime_worksched_timein_notif'));
            ($this->input->post('overtime_worksched_timein_notif') == 'Day Off') ? $data['worksched_out'] = $this->input->post('overtime_actual_datein_notif') . " " . '11:59:59' : $data['worksched_out'] = $this->input->post('overtime_actual_datein_notif') . " " . $this->ConvertTo24Format($this->input->post('overtime_worksched_out_notif'));
            $data['ot_timein'] = date('Y-m-d', strtotime($this->input->post('overtime_actual_datein_notif'))) . " " . $this->ConvertTo24Format($this->input->post('overtime_actualin_notif'));
            $data['ot_timeout'] = date('Y-m-d', strtotime($this->input->post('overtime_actual_dateout_notif'))) . " " . $this->ConvertTo24Format($this->input->post('overtime_actualout_notif'));
            $data['ot_reason'] = $this->input->post('overtime_reason_notif');

            $this->M_overtime->SaveUpdateOvertime($this->CleanArray($data), $this->input->post('id'));
        }
        echo json_encode($result);
    }

}
