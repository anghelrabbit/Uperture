<?php

class Employee extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('model_member_schedule', 'M_emp_sched');
    }

    public function TotalEmployees() {
        $struct = (array) json_decode($this->input->post('structure'));
        $where = $this->StructureChecker($struct, "=");

        $result = $this->M_employee->FetchEmployeeTable(0, $where, '', array());
        $male_count = 0;
        $female_count = 0;
        $new_hired_count = 0;
        $data = array();

        foreach ($result as $row) {
            if ($row->sex == 'Male') {
                $male_count++;
            }
            if ($row->sex == 'Female') {
                $female_count++;
            }

            if (date('Y') == date('Y', strtotime($row->datehired)) && (date('m') - date('m', strtotime($row->datehired))) < 3 && (date('m') - date('m', strtotime($row->datehired))) >= 0) {
                $new_hired_count++;
            }
        }
        $data['total_emp'] = count($result);
        $data['male_emp'] = $male_count;
        $data['female_emp'] = $female_count;
        $data['new_hired'] = $new_hired_count;
        echo json_encode($data);
    }

    public function FetchEmployeeServiceOrBirthdate() {
        $index = $this->input->post('index');
        $emp = array();
        $where = array();
        if ($index == 0) {
            $where['birth_month'] = $this->input->post('birthdate');
            $where['depID'] = $this->session->userdata('department');
            $emp = $this->M_employee->FetchEmployee($where);
        } else if ($index == 2) {
            $where['birthdate'] = date('m-d');
            $emp = $this->M_employee->FetchEmployee($where);
        } else {
            $emp = $this->M_employee->FetchEmployee(array('month' => date('m', strtotime("-1 months")), 'year' => date('Y')));
        }
        $data = array();
        foreach ($emp as $val) {
            $sub_array = array();
            $sub_array['empname'] = $val->lastname . ", " . $val->firstname;
            if ($index == 1) {
                $sub_array['service'] = $this->CalculateYearsOfService($val->datehired);
//                if($val->profileno == '09242019094540562PWD'){
//                      $explode_service = explode(' ', $sub_array['service']);
//                      var_dump($explode_service);
//                }
                if (date('d') < date('d', strtotime($val->datehired))) {
                    $sub_array['service'] = $this->AdjustYearsOfService($sub_array['service']) . ' on ' . date('F') . " " . date('d', strtotime($val->datehired));
                }
            } else if ($index == 0) {
                $sub_array['birthday'] = date('F d', strtotime($val->birthdate));
            }
            $sub_array['profile_image'] = $this->ConvertImage($this->M_employee->FetchProfilePic(array('refno' => $val->profileno)));
            $data[] = $sub_array;
        }

        $output = array('data' => $data, 'date' => date('F', strtotime('1997-' . $this->input->post('birthdate') . '-01')));
        echo json_encode($output);
    }

    public function UpdatePassword() {
        $newpass = $this->input->post('newpass');
        $result = $this->M_employee->UpdateAccountPassword($this->CleanArray(array('password' => $this->encrypt_pass($newpass))));
        echo json_encode($result);
    }

    public function FetchEmployeesToSelect() {
        $where = array();
        $struct = (array) json_decode($this->input->post('structure'));
        $structure_string = $this->StructureChecker($struct, "=");
        $category = $this->input->post('category');
        $order = $this->input->post('order');
        $column_index = $order[0]['column'];
        $column_name = array();
        $column_name[0] = 'lastname';
        $column_name[3] = 'lastname';
        $column_name[4] = 'datehired';
        $column = $this->input->post('columns');
        $departments_selected = (array) json_decode($this->input->post('selected_departments'));
        $departments_unselected = (array) json_decode($this->input->post('unselected_department'));
        $profileno_selected = (array) json_decode($this->input->post('selected_profileno'));
        $profileno_unselected = (array) json_decode($this->input->post('unselected_profileno'));
        if ($column[0]['search']['value'] != '') {
            $explode_departments = explode("+", $column[0]['search']['value']);
            if ($explode_departments[0] != '{}') {
                $departments_selected = (array) json_decode($explode_departments[0]);
            } else {
                $departments_selected = array();
            }
            if ($explode_departments[1] != '{}') {
                $departments_unselected = (array) json_decode($explode_departments[1]);
            } else {
                $departments_unselected = array();
            }
            if ($explode_departments[2] != '{}') {
                $profileno_selected = (array) json_decode($explode_departments[2]);
            } else {
                $profileno_selected = array();
            }
            if ($explode_departments[3] != '{}') {
                $profileno_unselected = (array) json_decode($explode_departments[3]);
            } else {
                $profileno_unselected = array();
            }
        }
        if ($column[1]['search']['value'] != '') {
            $empname = $column[1]['search']['value'];
            $explode_name = explode('/', $empname);
            if (count($explode_name) > 2) {
                $where[$explode_name[0]] = $explode_name[1];
                $where[$explode_name[2]] = $explode_name[3];
            } else {
                $where[$explode_name[0]] = $explode_name[1];
            }
        }
        if ($column[3]['search']['value'] != '' && $column[3]['search']['value'] != '0-0') {
            $service = $column[3]['search']['value'];
            $explode_service = explode('-', $service);
            if ($explode_service[0] != 0 && $explode_service[1] != 0 && $explode_service[1] < (int) date('m')) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . (intval($explode_service[1]) + 1) . " months"));
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . $explode_service[1] . " months"));
            } else if ($explode_service[0] != 0) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-01-01";
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d');
            } else if ($explode_service[1] != 0) {
                $where['start_service'] = date('Y-m', strtotime("-" . (1 + intval($explode_service[1])) . " months")) . "-" . date('d', strtotime('+1 days'));
                $where['end_service'] = date('Y-m', strtotime("-" . $explode_service[1] . " months")) . "-" . date('d');
            }
        }
        $column_array = array($column_name[intval($column_index)], $order[0]['dir']);
        $emp = $this->M_employee->FetchEmployeeTable(1, $structure_string, $column_array, $this->CleanArray($where));
        $datax = array();
        foreach ($emp as $row) {
            $credit_where = array('profileno' => $row->profileno, 'year' => date('Y'));
            $count = 0;
            if ($category == 0) {
                $result = $this->M_leavecredits->FetchEmployeeLeaveCredits($credit_where);
                $count = count($result);
            }
            $sub_array = array();
            $service = $this->CalculateYearsOfService($row->datehired);
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $row->jobcode)));
            $dept_string = '';
            if ($row->comID != null && $row->comID != '') {
                $dept_string = $row->comID;
            }
            if ($row->locID != null && $row->locID != '') {
                $dept_string = $dept_string . "@" . $row->locID;
            }
            if ($row->divID != null && $row->divID != '') {
                $dept_string = $dept_string . "@" . $row->divID;
            }
            if ($row->depID != null && $row->depID != '') {
                $dept_string = $dept_string . "@" . $row->depID;
            }
            if ($row->secID != null && $row->secID != '') {
                $dept_string = $dept_string . "@" . $row->secID;
            }
            if ($row->areID != null && $row->areID != '') {
                $dept_string = $dept_string . "@" . $row->areID;
            }
            $sub_array[] = $dept_string . "+" . $row->profileno;

            if ($count == 0) {
                if ($this->CheckedEmployees($row, $departments_selected, $departments_unselected, $profileno_selected, $profileno_unselected)) {
                    $sub_array[] = 1;
                    $sub_array[] = '<input name="' . $row->profileno . '" type="checkbox" checked style="width:20px;height:20px;" >';
                } else {
                    $sub_array[] = 0;
                    $sub_array[] = '<input name="' . $row->profileno . '" type="checkbox" style="width:20px;height:20px;" >';
                }
            } else {
                $sub_array[] = 2;
                $sub_array[] = '<span><b>-</b></span>';
            }
            $sub_array[] = $row->lastname . ", " . $row->firstname;
            if ($category == 2) {
                $sub_array[] = $row->empid;
                $sub_array[] = $row->biometric;
                $sub_array[] = $job[0]->jobname;
                $sub_array[] = $row->sex;
                $sub_array[] = $row->contact;
            } else {

                $sub_array[] = $service;
                if (count($job) > 0) {
                    $sub_array[] = $job[0]->jobname;
                } else {
                    $sub_array[] = '';
                }
            }
            $datax[] = $sub_array;
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($emp),
            "recordsFiltered" => $this->M_employee->EmployeeTableFilter($structure_string, $column_array, $this->CleanArray($where)),
            "data" => $datax,
        );
        echo json_encode($output);
    }

    public function FetchEmployee() {
        $emp = $this->M_employee->FetchEmployee(array('profileno' => $this->input->post('profileno')));
        $data = array('name' => '', 'job' => 'Unassigned');
        if (count($emp) > 0) {
            $data['name'] = $emp[0]->lastname . ", " . $emp[0]->firstname;
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $emp[0]->jobcode)));
            if (count($job) > 0) {
                $data['job'] = $job[0]->jobname;
            }
        }
        echo json_encode($data);
    }

    public function AdjustYearsOfService($serv) {
        $explode_service = explode(' ', $serv);
        $service = '';
        if (count($explode_service) == 2) {
            if ($explode_service[1] == 'month' || $explode_service[1] == 'months') {
                $service = intval($explode_service[0]) + 1;
                if ($service <= 11) {
                    $service = $service . " months";
                } else if ($service > 12) {
                    $service = $service - 12;
                    $service = '1 yr and ' . $service . ' months';
                } else {
                    $service = '1 yr';
                }
            } else if ($explode_service[1] == 'year' || $explode_service[1] == 'yrs') {
                $service = $explode_service[0] . " " . $explode_service[1] . "  1 month";
            }
        } else if (count($explode_service) == 5) {
            $service = intval($explode_service[3]) + 1;
            if ($service <= 11) {
                $service = $explode_service[0] . 'yrs & ' . $service . " months";
            } else if ($service > 12) {
                $service = $service - 12;
                $service = (intval($explode_service[0]) + 1) . ' yr and ' . $service . ' months';
            } else {
                $service = (intval($explode_service[0]) + 1) . ' yr';
            }
        }
        return $service;
    }

    public function SelectMember() {
        $where = array('depID' => $this->session->userdata('department'));
        $result = $this->M_emp_sched->FetchMemberTable($where);
        $data = array();
        foreach ($result as $val) {
            $sub_array = array();
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $val->jobcode)));
            $service = $this->CalculateYearsOfService($val->datehired);
            $sub_array[] = $val->profileno;
            $sub_array[] = $val->lastname . ", " . $val->firstname . " " . $val->midname . " " . $val->suffix;
            $sub_array[] = (count($job) > 0) ? $job[0]->jobname : 'None';
            $sub_array[] = $service;
            $data[] = $sub_array;
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->M_emp_sched->FetchMemberFilter($where),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function SaveMemberSchedule() {
        $profileno = $this->input->post('profileno');
        $datein = $this->input->post('member_timein_date');
        $timein = $this->input->post('member_timein_time');
        $dateout = $this->input->post('member_timeout_date');
        $timeout = $this->input->post('member_timeout_time');
        $id = $this->input->post('id');

        $data = array(
            'profileno' => $profileno,
            'timein' => $datein . " " . $timein,
            'timeout' => $dateout . " " . $timeout
        );
        $this->M_emp_sched->SaveUpdateMemberSchedule($data, $id);

        echo json_encode(true);
    }

    public function FetchMemberSchedules() {
        $result = $this->M_employee->FetchMemberSchedules();
        $data = array();
        foreach ($result as $val) {
            $emp = $this->M_employee->CheckAccount(array('profileno' => $val->profileno));
            $sub_array = array();
            $sub_array[] = '<span class="btn btn-warning" onclick="editSchedule(' . $val->indx . ",'" . $val->profileno . "','" . $emp[0]->lastname . ", " . $emp[0]->firstname . "'" . ')">Edit</span>&nbsp;<span class="btn btn-danger">Remove</span>';
            $sub_array[] = $emp[0]->lastname . ", " . $emp[0]->firstname;
            $sub_array[] = date('M d,Y g:i A', strtotime($val->timein));
            $sub_array[] = date('M d,Y g:i A', strtotime($val->timeout));
            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function FetchEmployeeSched() {
        $sched_id = $this->input->post('sched_id');
        $res = $this->M_employee->FetchEmployeeSchedule($sched_id);
        $data = array();
        if (count($res) > 0) {
            $data['sched_datein'] = date('Y-m-d', strtotime($res[0]->timein));
            $data['sched_timein'] = date('H:i:s', strtotime($res[0]->timein));
            $data['sched_dateout'] = date('Y-m-d', strtotime($res[0]->timeout));
            $data['sched_timeout'] = date('H:i:s', strtotime($res[0]->timeout));
            
        }
        echo json_encode($data);
    }

}
