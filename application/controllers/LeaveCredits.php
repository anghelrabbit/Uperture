<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Leave_Credits
 *
 * @author MIS
 */
class LeaveCredits extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_leave', 'M_leave');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('model_structure', 'M_structure');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Leave Credits";
            $data['page'] = 'pages/menu/leave_credits/leave_credits';
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
                'assets/myjs/utilities/selecting_employees.js',
                'assets/myjs/leave_credits/leave_credits.js',
//                'assets/myjs/leave_credits/give_credits.js',
                'assets/myjs/leave_credits/give_credits2.js',
            );

            $this->InspectUser('menu/leave_credits/leave_credits', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FetchTypeOfLeave() {
        $leave_types = $this->M_leavecredits->FetchLeaveTypeTable();
        echo json_encode($leave_types);
    }

    public function FetchLeaveTypes() {
        $leave_types = $this->M_leavecredits->FetchLeaveTypeTable();

        $category = $this->input->post('category');
        $profileno = $this->input->post('profileno');
        $from_add_leavetype = $this->input->post('from_leavetype');
        if ($category == 0) {
            $data = array();
            $counter = 0;
            $length = 0;
            $sub_array = array();
            foreach ($leave_types as $val) {
                $table_data = $val->name;
                if ($from_add_leavetype == 1) {
                    $table_data = '<input name="' . $val->id . '_input" style="width:100%" class="form-control hidden" value="' . $val->name . '"/>'
                            . '<span name="' . $val->id . '_text">' . $val->name . '</span>' . '<br><br>' .
                            '<label style="color:red"  name="' . $val->id . '_error"></label>';
                    if ($val->name != 'Others') {
                        $table_data = $table_data . '<div class="pull-right">' .
                                '<span name="' . $val->id . '_edit" onclick="updateLeaveType(' . $val->id . ',' . '1' . ')" class="btn" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #FFB347; width: 25px;height: 25px;margin:0;color:white"><i class="glyphicon glyphicon-pencil" ></i></span>&nbsp;&nbsp;' .
                                '<span name="' . $val->id . '_delete" onclick="updateLeaveType(' . $val->id . ',' . '2' . ')" class="btn" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #FF392E; width: 25px;height: 25px;margin:0;color:white"><i class="fa fa-trash"></i></span>&nbsp;&nbsp;' .
                                '<span name="' . $val->id . '_update" onclick="updateLeaveType(' . $val->id . ',' . '3' . ')" class="btn hidden" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #3ED03E; width: 25px;height: 25px;margin:0;color:white"><i class="glyphicon glyphicon-ok"></i></span>&nbsp;&nbsp;' .
                                '<span name="' . $val->id . '_cancel" onclick="updateLeaveType(' . $val->id . ',' . '4' . ')" class="btn hidden" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #FF392E; width: 25px;height: 25px;margin:0;color:white"><i class="glyphicon glyphicon-remove"></i></span>&nbsp;&nbsp;' .
                                '</div>';
                    }
                } else if ($from_add_leavetype == 2) {
                    $credit_where = array('profileno' => $profileno, 'year' => date('Y'), 'leavetype' => $val->id);
                    $result = $this->M_leavecredits->FetchEmployeeLeaveCredits($credit_where);
                    $days_remaining = 0;
                    if (count($result) > 0) {
                        $days_remaining = $result[0]->remaining_days;
                    }
                    $table_data = '<span >' . $val->name . '</span>' . '<br><br>' .
                            '<input type="number" name="' . $val->id . '_credit" class="form-control" style="width:80px" readonly value="' . $days_remaining . '">' .
                            '<div class="pull-right">' .
                            '<span name="' . $val->id . '_edit" onclick="updateEmployeeCredit(' . "'" . $profileno . "'," . $val->id . ',' . '1' . ')" class="btn" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #FFB347; width: 25px;height: 25px;margin:0;color:white"><i class="glyphicon glyphicon-pencil" ></i></span>&nbsp;&nbsp;' .
                            '<span name="' . $val->id . '_update" onclick="updateEmployeeCredit(' . "'" . $profileno . "'," . $val->id . ',' . '3' . ')" class="btn hidden" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #3ED03E; width: 25px;height: 25px;margin:0;color:white"><i class="glyphicon glyphicon-ok"></i></span>&nbsp;&nbsp;' .
                            '<span name="' . $val->id . '_cancel" onclick="updateEmployeeCredit(' . "'" . $profileno . "'," . $val->id . ',' . '4' . ')" class="btn hidden" style="padding:0;padding-top:2px;border-radius: 50%; background-color: #FF392E; width: 25px;height: 25px;margin:0;color:white"><i class="glyphicon glyphicon-remove"></i></span>&nbsp;&nbsp;' .
                            '</div>';
                } else {
                    $table_data = $table_data . '<br><input type="number"style="width:100%" class="form-control" onchange="leaveTypeCount(' . "'" . $val->id . "'," . "this" . ')">';
                }
                $length++;
                if ($counter < 3) {
                    $sub_array[] = $table_data;
                    if ($length == count($leave_types)) {
                        while ($counter < 3) {
                            $sub_array[] = '';
                            $counter++;
                        }
                        $data[] = $sub_array;
                    }
                    $counter++;
                } else {
                    $data[] = $sub_array;
                    $sub_array = array();
                    $sub_array[] = $table_data;
                    $counter = 1;
                    if ($length == count($leave_types)) {
                        while ($counter < 3) {
                            $sub_array[] = '';
                            $counter++;
                        }
                        $data[] = $sub_array;
                    }
                }
            }
            $output = array
                (
                "draw" => intval($this->input->post("draw")),
                "recordsTotal" => count($leave_types),
                "recordsFiltered" => $this->M_leavecredits->LeaveTypeTableFilter(),
                "data" => $data
            );
            echo json_encode($output);
        } else {
            echo json_encode($leave_types);
        }
    }

    public function SaveUpdateLeaveType() {
        $this->FormRestrictions('leavetype');
        $result = $this->ValidateErrorsSample($_POST);
        if ($result["success"] == true) {
            $id = $this->input->post('id');
            $data = array();
            $data['name'] = $this->input->post('leavetype_name');
            $data['update_by'] = $this->session->userdata('profileno');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $this->M_leavecredits->SaveLeaveType($data, $id);
        }
        echo json_encode($result);
    }

    public function FetchEmployeeCredits() {
        $data = array();
        $struct = (array) json_decode($this->input->post('structure'));
        $leavetypes = (array) json_decode($this->input->post('leavetypes'));
        $structure_string = $this->StructureChecker($struct, "=");
        $column_array = array('lastname', 'ASC');
        $where = array();
        $emp = $this->M_employee->FetchEmployeeTable(0, $structure_string, $column_array, $this->CleanArray($where));
        foreach ($emp as $row) {
            $counter = 0;
            $sub_array = array();
            $sub_array[] = '<span class="btn btn-success" style="background-color: #3ED03E;" onclick="openUpdateCredits(' . "'" . $row->profileno . "'" . ')">Edit Credits</span>';
            $sub_array[] = $row->lastname . ", " . $row->firstname;
            foreach ($leavetypes as $val) {
                $credit_where = array('profileno' => $row->profileno, 'year' => date('Y'), 'leavetype' => $val->id);
                $result = $this->M_leavecredits->FetchEmployeeLeaveCredits($credit_where);
                if (count($result) > 0) {
                    $sub_array[] = $result[0]->remaining_days . ' (' . $result[0]->taken_days . ')';
                    $counter++;
                } else {
                    $sub_array[] = 0;
                }
            }
            if ($counter > 0) {
                $data[] = $sub_array;
            }
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function UpdateEmployeeLeaveCredits() {
        $selected_departments = (array) json_decode($this->input->post('selected_departments'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_departments'));
        $selectd_profileno = (array) json_decode($this->input->post('selectd_profileno'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselectd_profileno'));
        $leavetypes = (array) json_decode($this->input->post('leavetypes'));
        $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);

        $emp = $this->M_employee->FetchEmployeeTable(0, $where_selected, '', array());
        $res = false;
        foreach ($emp as $row) {
            foreach ($leavetypes as $key => $val) {
                $data = array();
                $data['profileno'] = $row->profileno;
                $data['year'] = date('Y');
                $data['writeoffdays'] = 0;
                $data['taken_days'] = 0;
                $data['iswriteoff'] = 0;
                $data['update_by'] = $this->session->userdata('profileno');
                $data['updated_date'] = date('Y-m-d H:i:s');
                $data['leavetype'] = $key;
                $data['total_days'] = $val;
                $data['remaining_days'] = $val;
                $res = $this->M_leavecredits->UpdateLeaveCredits(array(), $this->CleanArray($data));
                if ($res == false) {
                    break;
                }
            }
        }
        echo json_encode($res);
    }

    public function FetchSpecificLeaveCredits() {
        $profileno = $this->input->post('profileno');
        if ($profileno == 0) {
            $profileno = $this->session->userdata('profileno');
        }
        $result = $this->M_leavecredits->FetchEmployeeLeaveCredits(array('profileno' => $profileno));
        $data = array();
        foreach ($result as $val) {
            $leavetype = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $val->leavetype));
            $sub_array = array();
            $sub_array['name'] = $leavetype[0]->name;
            $sub_array['remaining_days'] = intval($val->remaining_days);
            $sub_array['total'] = $val->total_days;
            $data[$leavetype[0]->name] = $sub_array;
        }

        echo json_encode($data);
    }

    public function RemoveSpecificLeaveType() {
        $id = $this->input->post('id');
        echo json_encode($this->M_leavecredits->RemoveLeaveType($id));
    }

    public function UpdateEmployeeCredit() {
        $where = array('profileno' => $this->input->post('profileno'), 'leavetype' => $this->input->post('leavetype'));
        $data = array('total_days' => $this->input->post('credit_count'),
            'remaining_days' => $this->input->post('credit_count'),
            'update_by' => $this->session->userdata('profileno'),
            'updated_date' => date('Y-m-d H:i:s')
        );
        $has_credit = $this->M_leavecredits->FetchEmployeeLeaveCredits($where);
        if (count($has_credit) > 0) {
            echo json_encode($this->M_leavecredits->UpdateLeaveCredits($where, $data));
        } else {
            $data['profileno'] = $this->input->post('profileno');
            $data['year'] = date('Y');
            $data['taken_days'] = 0;
            $data['writeoffdays'] = 0;
            $data['iswriteoff'] = 0;
            $data['update_by'] = $this->session->userdata('profileno');
            $data['updated_date'] = date('Y-m-d H:i:s');
            $data['leavetype'] = $this->input->post('leavetype');
            echo json_encode($this->M_leavecredits->UpdateLeaveCredits(array(), $data));
        }
    }

    public function FetchLeaveCreditCount() {
        $leavetype = $this->input->post('leavetype');
        $explode_leavetype = explode('/', $leavetype);

        $res = $this->M_leavecredits->FetchSpecificCredits(array('profileno' => $this->session->userdata('profileno'), 'leavetype' => $explode_leavetype[1]));
        $credit_count = 0;
        if (count($res) > 0) {
            $credit_count = $res[0]->remaining_days;
        }
        $data['credit_name'] = $explode_leavetype[0];
        $data['count'] = $credit_count;
        
        echo json_encode($data);
    }

}
