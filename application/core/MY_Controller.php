<?php

use phpDocumentor\Reflection\Types\Object_;

defined('BASEPATH') or exit('No direct script access allowed');

ini_set('max_execution_time', 0);
ini_set('memory_limit', '2048M');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('file');
        $this->load->model('model_userwifi', 'M_wifi');
        $this->load->model('model_structure', 'M_structure');
        $this->load->model('model_undertime', 'M_undertime');
        $this->load->model('model_changeschedule', 'M_changeschedule');
        $this->load->model('model_leave', 'M_leave');
        $this->load->model('model_workschedule', 'M_worksched');
        $this->load->model('model_announcement', 'M_announcement');
        $this->load->model('model_dtr', 'M_dtr');
        $this->load->model('model_leavecredits', 'M_leavecredits');
        $this->load->model('MY_Model');
    }

    public function UserSession($user_data, $data, $jobposition, $comp, $company_logo, $profile_pic) {
        $emp = $user_data[0];
        $explode_comp = explode(' ', $comp);
        $is_oncall = 0;
        (count($explode_comp) > 1) ? $is_oncall = ($explode_comp[0] == 'Oncall') ? 1 : 0 : '';
        $session_data = array(
            'empname' => $emp->lastname . ", " . $emp->firstname . " " . $emp->midname,
            'biometric' => $emp->biometric,
            'date_hired' => $emp->datehired,
            'lastname' => $emp->lastname,
            'firstname' => $emp->firstname,
            'profileno' => $emp->profileno,
            'empid' => $emp->empid,
            'username' => $emp->username,
            'bday' => $emp->birthdate,
            'sex' => $emp->sex,
            'nationality' => $emp->nationality,
            'address' => $emp->address,
            'cellphone' => $emp->contact,
            'civilstatus' => $emp->civilstatus,
            'dependents' => $emp->dependants,
            'sssno' => $emp->sssno,
            'hdmfno' => $emp->hdmfno,
            'phicno' => $emp->phicno,
            'tin' => $emp->taxno,
            'datehired' => $emp->datehired,
            'jobstatus' => $emp->empstatus,
            'company' => $emp->comID,
            'location' => $emp->locID,
            'division' => $emp->divID,
            'department' => $emp->depID,
            'section' => $emp->secID,
            'area' => $emp->areID,
            'flex' => $emp->timein,
            'monthly' => $emp->ratecom,
            'expirydate' => $emp->expirydate,
            'user' => 0,
            'compname' => $comp,
            'complogo' => $company_logo,
            'jobposition' => $jobposition,
            'profilepic' => $profile_pic,
            'oncall' => $is_oncall,
            'payroll' => 0,
            'wifipass' => $this->FetchWifiPassword($emp->profileno),
            'server_address' => '',
            'path' => '',
            'logged_in' => TRUE,
        );
//        if (count($text_directory)) {
//            $session_data['server_address'] = $text_directory[0]->serveraddress;
//            $session_data['path'] = $text_directory[0]->serversubfolder;
//        }


        $structure = array();
        $structure_log = array('COM' => array(), 'LOC' => array(), 'DIV' => array(), 'DEP' => array(), 'SEC' => array(), 'ARE' => array());
        $roles = array();
        if (count($data) > 0) {
            foreach ($data as $val) {
                $reference = $this->TrackReferenece($val->refno, $val->tag, $structure, $structure_log);
                $structure = $reference[0];
                $structure_log = $reference[1];
                $roles[$val->refno][] = $val->role;

                if ($val->role == 'Admin') {
                    $session_data['admin'] = 1;
                }
                if ($val->role == 'HR') {
                    $session_data['hr'] = 1;
                }
                if ($val->role == 'Team Leader') {
                    $session_data['head'] = 1;
                }
                if ($val->role == 'Supervisor') {
                    $session_data['supervisor'] = 1;
                }
                if ($val->role == 'Scheduler') {
                    $session_data['scheduler'] = 1;
                }
                if ($val->role == 'Payroll') {
                    $session_data['payroll'] = 1;
                }
            }
        } else {
            $session_data['user'] = 1;
        }
        $session_data['structure'] = $structure;
        $session_data['departments'] = $structure_log;
        $session_data['roles'] = $roles;

        $this->session->set_userdata($session_data);
    }

    public function CleanStructureReference($structure) {
        $struct = array();
        foreach ($structure as $row) {
            $struct[$row['holder']] = $row['holder'];
        }
        return $struct;
    }

    public function TrackReferenece($department, $tag, $structure, $structure_log) {

        $searcher = array();
        $structure[$department][$department] = $department;
        $explode_dept = explode('-', $department);
        $structure_log[$explode_dept[0]][$department] = $department;
        $searcher[$department] = array('searcher' => array($department), 'link' => array($department));
        $new_search = array();
        while ($tag < 6) {
            foreach ($searcher as $val) {
                foreach ($val['searcher'] as $key => $data) {
                    $under = $this->MY_Model->fetch_structure_under($data);
                    foreach ($under as $row) {
                        $new_search[$row->under]['searcher'][] = $row->under;
                        $new_search[$row->under]['link'][] = $val['link'][$key] . "@" . $row->under;
                        $structure[$row->under][$val['link'][$key] . "@" . $row->under] = $val['link'][$key] . "@" . $row->under;

                        $explode_row = explode('-', $row->under);
                        $structure_log[$explode_row[0]][$row->under] = $row->under;
                    }
                }
            }
            $searcher = $new_search;
            $new_search = array();
            $tag++;
        }
        return $this->TrackUnder($department, $tag, $structure, $structure_log);
    }

    public function TrackUnder($department, $tag, $structure, $restrictions) {

        $searcher = array();
        $structure[$department][$department] = $department;

        $searcher[$department] = array('searcher' => $department, 'link' => $department);
        $new_search = array();
        while ($tag > 0) {
            foreach ($searcher as $val) {
                $under = $this->MY_Model->fetch_structure_refno($val['searcher']);
                foreach ($under as $row) {
                    $new_search[$row->refno]['searcher'] = $row->refno;
                    $new_search[$row->refno]['link'] = $row->refno . "@" . $val['link'];
                    $structure[$row->refno][$row->refno . "@" . $val['link']] = $row->refno . "@" . $val['link'];

                    $explode_row = explode('-', $row->refno);
                    $restrictions[$explode_row[0]][$row->refno] = $row->refno;
                }
            }
            $searcher = $new_search;
            $new_search = array();
            $tag--;
        }
        $return_value = array($structure, $restrictions);
        return $return_value;
    }

    public function has_logging_in() {
        if ($this->session->userdata('logged_in')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function InspectUser($page = NULL, $data = NULL) {
        if ($page === NULL) {
            if ($this->has_logging_in()) {
                $this->session->set_userdata('dont_trigger', 0);
                redirect('Homepage', 'refresh');
                $this->load->view('templates/page_template/header', $data);
                $this->load->view('templates/page_template/navbar', $data);
                $this->load->view('templates/page_template/page', $data);
                $this->load->view('templates/page_template/sidebar', $data);
            } else {
                $this->session->set_userdata('footer', 0);
                $this->session->set_userdata('idleacc', 0);
                $this->load->view('pages/landingpage/login', $data);
            }
        } else {
            $this->load->view('templates/page_template/header', $data);
            $this->load->view('templates/page_template/navbar', $data);
            $this->load->view('templates/page_template/page', $data);
            $this->load->view('templates/page_template/sidebar', $data);
            $this->load->view('templates/page_template/footer', $data);
        }
    }

    public function FormRestrictions($form) {

        $forms = array(
            'undertime' => array(
                0 => array('field' => 'undertime_actual_datein', 'label' => 'Actual Date-in', 'rules' => 'required'),
                1 => array('field' => 'undertime_actualin', 'label' => 'Actual Time-out', 'rules' => 'required'),
                2 => array('field' => 'undertime_actual_dateout', 'label' => 'Actual Date-out', 'rules' => 'required'),
                3 => array('field' => 'undertime_actualout', 'label' => 'Actual Time-out', 'rules' => 'required')
            ),
            'holiday' => array(
                0 => array('field' => 'holiday_date', 'label' => 'Date', 'rules' => 'required'),
                1 => array('field' => 'holiday_name', 'label' => 'Name', 'rules' => 'required')
            ),
            'overtime' => array(
                0 => array('field' => 'overtime_actual_datein', 'label' => 'Actual Date-in', 'rules' => 'required'),
                1 => array('field' => 'overtime_actualin', 'label' => 'Actual Time-out', 'rules' => 'required'),
                2 => array('field' => 'overtime_actual_dateout', 'label' => 'Actual Date-out', 'rules' => 'required'),
                3 => array('field' => 'overtime_actualout', 'label' => 'Actual Time-out', 'rules' => 'required'),
            ),
            'leavetype' => array(
                0 => array('field' => 'leavetype_name', 'label' => 'Leave type', 'rules' => 'required'),
            ),
            'announcement_img' => array(
                0 => array('field' => 'displayed_from', 'label' => 'Date', 'rules' => 'required'),
                1 => array('field' => 'displayed_to', 'label' => 'Date', 'rules' => 'required'),
            ),
            'announcement' => array(
                0 => array('field' => 'announcement_topic', 'label' => 'Topic', 'rules' => 'required'),
                1 => array('field' => 'announcement_optional_id', 'label' => 'Announcement ID', 'rules' => 'required'),
                2 => array('field' => 'announcement_venue', 'label' => 'Venue', 'rules' => 'required'),
                3 => array('field' => 'announce_datein', 'label' => 'Date', 'rules' => 'required'),
                4 => array('field' => 'announce_dateout', 'label' => 'Date', 'rules' => 'required'),
            ),
            'leave' => array(
                0 => array('field' => 'leave_datefrom', 'label' => 'Date', 'rules' => 'required'),
                1 => array('field' => 'leave_dateto', 'label' => 'Date', 'rules' => 'required'),
            ),
            'policy' => array(
                0 => array('field' => 'policy_number', 'label' => 'Number', 'rules' => 'required'),
                1 => array('field' => 'policy_title', 'label' => 'Title', 'rules' => 'required'),
                2 => array('field' => 'select_action_taken', 'label' => 'Action', 'rules' => 'required'),
                3 => array('field' => 'policy_effective', 'label' => 'Time Effective', 'rules' => 'required'),
                4 => array('field' => 'policy_desc', 'label' => 'Description', 'rules' => 'required'),
                5 => array('field' => 'select_final_action', 'label' => 'Final action', 'rules' => 'required'),
            ),
            'supplemental' => array(
                0 => array('field' => 'supp_policy_number', 'label' => 'Number', 'rules' => 'required'),
                1 => array('field' => 'supp_policy_title', 'label' => 'Title', 'rules' => 'required'),
                2 => array('field' => 'supp_policy_desc', 'label' => 'Description', 'rules' => 'required'),
            ),
            'reprimand_letter' => array(
                0 => array('field' => 'reprimand_title', 'label' => 'Title', 'rules' => 'required'),
                1 => array('field' => 'reprimand_date', 'label' => 'Date', 'rules' => 'required'),
                2 => array('field' => 'reprimand_employee', 'label' => 'Employee', 'rules' => 'required'),
                3 => array('field' => 'reprimand_email', 'label' => 'Email', 'rules' => 'required'),
                4 => array('field' => 'reprimand_policy', 'label' => 'Policy', 'rules' => 'required'),
            ),
            'certificate' => array(
                0 => array('field' => 'certificate_title', 'label' => 'Title', 'rules' => 'required'),
                1 => array('field' => 'certificate_type', 'label' => 'Type', 'rules' => 'required'),
                2 => array('field' => 'with_budget', 'label' => 'Budget', 'rules' => 'required'),
                3 => array('field' => 'certificate_from', 'label' => 'Date', 'rules' => 'required'),
                4 => array('field' => 'certificate_to', 'label' => 'Date', 'rules' => 'required'),
            ),
            'form_applicant' => array(
                0 => array('field' => 'application_date', 'label' => 'Date of application', 'rules' => 'required'),
                1 => array('field' => 'applied_postion', 'label' => 'Applied position', 'rules' => 'required'),
                2 => array('field' => 'firstname', 'label' => 'Firstname', 'rules' => 'required'),
                3 => array('field' => 'lastname', 'label' => 'Lastname', 'rules' => 'required'),
                4 => array('field' => 'contact_no', 'label' => 'Contact No.', 'rules' => 'required'),
                5 => array('field' => 'highest_educational_attainment', 'label' => 'Highest Educational Attainment', 'rules' => 'required'),
                6 => array('field' => 'current_emp_background', 'label' => 'Current Employment/Background', 'rules' => 'required'),
                7 => array('field' => 'general_information', 'label' => 'General Information', 'rules' => 'required'),
            ),
            'classification' => array(
                0 => array('field' => 'classification_name', 'label' => 'Name', 'rules' => 'required'),
            ),
            'asset_details' => array(
                0 => array('field' => 'control_number', 'label' => 'Control number', 'rules' => 'required'),
                1 => array('field' => 'asset_name', 'label' => 'Asset name', 'rules' => 'required'),
                2 => array('field' => 'asset_category', 'label' => 'Category', 'rules' => 'required'),
                3 => array('field' => 'asset_type', 'label' => 'Type', 'rules' => 'required'),
            ),
            'pms_template' => array(
                0 => array('field' => 'template_name', 'label' => 'Template name', 'rules' => 'required'),
                1 => array('field' => 'template_total_points', 'label' => 'Total grade', 'rules' => 'required'),
                2 => array('field' => 'template_passing_grade', 'label' => 'Passing grade', 'rules' => 'required'),
                3 => array('field' => 'template_min_grade', 'label' => 'Minimum grade', 'rules' => 'required'),
            ),
            'status_category' => array(
                0 => array('field' => 'grade_from', 'label' => 'Grade from', 'rules' => 'required'),
                1 => array('field' => 'grade_to', 'label' => 'Grade to', 'rules' => 'required'),
                2 => array('field' => 'category_name', 'label' => 'Name', 'rules' => 'required'),
                3 => array('field' => 'status_category_desc', 'label' => 'Description', 'rules' => 'required'),
            ),
            'eval_category' => array(
                0 => array('field' => 'eval_category_name', 'label' => 'Name', 'rules' => 'required'),
            ),
            'eval_type' => array(
                0 => array('field' => 'eval_type_name', 'label' => 'Name', 'rules' => 'required'),
            ),
            'questions' => array(
                0 => array('field' => 'template_question_name', 'label' => 'Name', 'rules' => 'required'),
                1 => array('field' => 'template_question_points', 'label' => 'Points', 'rules' => 'required'),
            ),
            'form_interview_details' => array(
                0 => array('field' => 'date', 'label' => 'Date', 'rules' => 'required'),
                1 => array('field' => 'time', 'label' => 'Time', 'rules' => 'required'),
                2 => array('field' => 'location', 'label' => 'Location', 'rules' => 'required'),
                3 => array('field' => 'contact_person', 'label' => 'Contact Person', 'rules' => 'required'),
                4 => array('field' => 'contact_address', 'label' => 'Contact Address', 'rules' => 'required'),
                5 => array('field' => 'interviewer', 'label' => 'Interviewer', 'rules' => 'required'),
                6 => array('field' => 'purpose', 'label' => 'Purpose', 'rules' => 'required'),
            ),
            'form_applicant_evaluation' => array(
                0 => array('field' => 'template', 'label' => 'Template', 'rules' => 'required'),
                1 => array('field' => 'passing_grade', 'label' => 'Passing Grade', 'rules' => 'required'),
                2 => array('field' => 'description', 'label' => 'Description', 'rules' => 'required'),
            ),
            'form_applicant_evaluation_add_question' => array(
                0 => array('field' => 'question', 'label' => 'Question', 'rules' => 'required'),
                1 => array('field' => 'points', 'label' => 'Points', 'rules' => 'required'),
            ),
            'appraisal' => array(
                0 => array('field' => 'grade_from', 'label' => 'Date', 'rules' => 'required'),
                1 => array('field' => 'grade_to', 'label' => 'Date', 'rules' => 'required'),
                2 => array('field' => 'evaluation_date', 'label' => 'Date', 'rules' => 'required'),
                3 => array('field' => 'eval_category', 'label' => 'Category', 'rules' => 'required'),
                4 => array('field' => 'eval_template', 'label' => 'Template', 'rules' => 'required'),
                5 => array('field' => 'eval_type', 'label' => 'Type', 'rules' => 'required'),
            ),
            'profile' => array(
                0 => array('field' => 'profile_biometric', 'label' => 'Biometric', 'rules' => 'required'),
                1 => array('field' => 'profile_date_hired', 'label' => 'Date Hired', 'rules' => 'required'),
                2 => array('field' => 'profile_birthdate', 'label' => 'Birthdate', 'rules' => 'required'),
                3 => array('field' => 'select_gender', 'label' => 'Gender', 'rules' => 'required'),
                4 => array('field' => 'profile_civil_status', 'label' => 'Civil Status', 'rules' => 'required'),
                5 => array('field' => 'profile_nationality', 'label' => 'Nationality', 'rules' => 'required'),
                6 => array('field' => 'profile_blood_type', 'label' => 'Blood Type', 'rules' => 'required'),
                7 => array('field' => 'profile_religion', 'label' => 'Religion', 'rules' => 'required'),
                8 => array('field' => 'profile_contact_num', 'label' => 'Contact Number', 'rules' => 'required'),
                9 => array('field' => 'profile_email', 'label' => 'Email', 'rules' => 'required'),
                10 => array('field' => 'profile_address', 'label' => 'Address', 'rules' => 'required'),
            )
        );
      
        $this->form_validation->set_rules($forms[$form]);
    }

    public function ValidateErrors($error_field, $input_id, $form_error, $error_message = '') {
        $validation = array();
        $validation['error_field'] = $error_field;
        $validation['input_id'] = $input_id;

        if ($error_message != '') {
            $validation['error'] = $error_message;
        } else {
            $validation['error'] = $form_error;
        }
        return $validation;
    }

    public function ValidateErrorsSample($data) {
        $result = array('success' => false, 'messages' => array());
        if ($this->form_validation->run()) {
            $result['success'] = true;
        } else {
            foreach ($data as $key => $value) {
                $result['messages'][$key] = form_error($key);
            }
        }
        return $result;
    }

    public function CleanArray($data) {
        foreach ($data as $key => $item) {
            $data[$key] = $this->security->xss_clean($item);
        }
        return $data;
    }

    public function Convert24FormatNoSeconds($time) {
        if (date('H:i:s', strtotime($time)) == '11:59:59') {
            return 'Day Off';
        } else {
            return date('H:i', strtotime($time));
        }
    }

    public function ConvertTo24Format($time) {
        if (date('H:i:s', strtotime($time)) == '11:59:59') {
            return 'Day Off';
        } else {
            return date('H:i:s', strtotime($time));
        }
    }

    public function ConvertTo12Format($time) {
        if (date('H:i:s', strtotime($time)) == '11:59:59') {
            return 'Day Off';
        } else {
            return date("g:i A", strtotime($time));
        }
    }

    function ValidateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public function CheckRole($structure) {
        $roles = array();
        $role = $this->session->userdata('roles');
        if (isset($role[$structure->comID])) {
            foreach ($role[$structure->comID] as $row) {
                $roles[$row] = $row;
            }
        }
        if (isset($role[$structure->locID])) {
            foreach ($role[$structure->locID] as $row) {
                $roles[$row] = $row;
            }
        }
        if (isset($role[$structure->divID])) {
            foreach ($role[$structure->divID] as $row) {
                $roles[$row] = $row;
            }
        }
        if (isset($role[$structure->depID])) {
            foreach ($role[$structure->depID] as $row) {
                $roles[$row] = $row;
            }
        }
        if (isset($role[$structure->secID])) {
            foreach ($role[$structure->secID] as $row) {
                $roles[$row] = $row;
            }
        }
        if (isset($role[$structure->areID])) {
            foreach ($role[$structure->areID] as $row) {
                $roles[$row] = $row;
            }
        }


        return $roles;
    }

    public function FetchDepartmentAssigned($data) {
        $department = array();
        if ($data->areID != '') {
            $department = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $data->areID)), 'tbl_company');
        } else if ($data->secID != '') {
            $department = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $data->secID)), 'tbl_sections');
        } else if ($data->depID != '') {
            $department = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $data->depID)), 'tbl_departments');
        } else if ($data->divID != '') {
            $department = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $data->divID)), 'tbl_division');
        }
        return $department;
    }

    public function OrganizeStructure($under, $struct) {
        $split = explode('@', $under);
        $where = array();
        if ($split[0] == 'All' && count($struct) == 0) {
            foreach ($this->session->userdata('departments') as $depts) {
                $dept = explode('+', $depts);
                $where = $this->OrganizeDepartments($where, $this->ReturnStructure($struct, $dept[1], $this->input->post('company')));
            }
        } else {
            $where[] = $this->returnStructure($struct, $under, $this->input->post('company'));
        }
        return $where;
    }

    public function ReturnStructure($data, $structure, $company) {
        $struct = ['locID', 'divID', 'depID', 'secID', 'areID'];

        $split = explode('@', $structure);
        $com_split = explode('-', $split[0]);
        if ($com_split[0] == 'COM') {
            $data['comID'] = $split[0];
        } else {
            for ($cv = 0; $cv < count($split); $cv++) {
                $data[$struct[$cv]] = $split[$cv];
            }
        }
        if ($company != '') {
            $data['comID'] = $this->input->post('company');
        }
        return $data;
    }

    public function OrganizeDepartments($where, $structure) {
        $struct = array('comID', 'locID', 'divID', 'depID', 'secID', 'areID');
        $count = 0;
        $ishere = false;
        $newStruct = array();
        $flag = false;
        if (count($where) > 0) {
            while ($count < count($where)) {
                foreach ($struct as $val) {
                    if (isset($where[$count][$val]) && isset($structure[$val])) {
                        $ishere = true;
                        $newStruct[$val] = $structure[$val];
                        if ($where[$count][$val] == $structure[$val]) {
                            $flag = false;
                        } else {
                            $flag = true;
                        }
                    }
                }
                if ($flag == false) {
                    if ($ishere == false) {
                        $where[] = $structure;
                    } else {

                        $where[$count] = $newStruct;
                    }
                }
                $count++;
            }
        } else {
            $where[] = $structure;
        }
        if ($flag == true) {
            $where[] = $newStruct;
        }

        return $where;
    }

    public function FetchWifiPassword($profileno) {
        $fetch_wifi = $this->M_wifi->HasWifiPassword($this->CleanArray($data = array('profileno' => $profileno)));
        if (count($fetch_wifi) > 0) {
            return $this->encrypt_pass($fetch_wifi[0]->wifi_pass);
        } else {
            $available = $this->M_wifi->get_available_password();
            if (count($available) > 0) {
                $data = array();
                $data['profileno'] = $profileno;
                $data['wifi_pass'] = $available[0]->wifi_pass;
                $this->M_wifi->update_emp_wifi($available[0]->id, $this->CleanArray($data));
                return $this->encrypt_pass($available[0]->wifi_pass);
            } else {
                return 'No available password';
            }
        }
    }

    public function encrypt_pass($pass) {
        $password = "";
        for ($i = 0; $i < strlen($pass); $i++) {
            $password .= ($i % 2 === 0) ? chr(ord($pass[$i]) + 2) : chr(ord($pass[$i]) + 3);
        }

        return $password;
    }

    public function decrypt_pass($pass) {
        $password = "";
        for ($i = 0; $i < strlen($pass); $i++) {
            $password .= ($i % 2 === 0) ? chr(ord($pass[$i]) - 2) : chr(ord($pass[$i]) - 3);
        }
        return $password;
    }

    public function CalculateYearsOfService($datehired) {
        $yearsofservice = explode("-", $datehired);
        $total = (date("md", date("U", mktime(0, 0, 0, $yearsofservice[1], $yearsofservice[2], $yearsofservice[0]))) > date("md") ? ((date("Y") - $yearsofservice[0]) - 1) : (date("Y") - $yearsofservice[0]));

        $serviceMonths = date('m', strtotime(date('Y-m-d'))) - date('m', strtotime($datehired));
        $serviceDays = date('d', strtotime(date('Y-m-d'))) - date('d', strtotime($datehired));
        $days = strtotime(date('Y-m-d')) - strtotime($datehired);
        if ($serviceDays < 0) {
            $serviceMonths--;
        }

        if ($total <= 0) {
            $date1 = $datehired;
            $date2 = date('Y-m-d');

            $ts1 = strtotime($date1);
            $ts2 = strtotime($date2);

            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);

            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);

            $day1 = date('d', $ts1);
            $day2 = date('d', $ts2);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
            if ($day1 > $day2) {
                if ($diff > 0) {
                    $diff--;
                } else {
                    $days = $day2 - $day1;
                }
            } else {
                $days = -1 * ($day1 - $day2);
            }
            $serviceMonths = $diff;
        }
        $service = '';
        if ($total > 0) {

            $service = $total;

            if ($total == 1) {
                $service = $service . " year";
            } else {
                $service = $service . " yrs";
            }
        }
        if ($serviceMonths > 0) {
            if ($service != '') {
                $service = $service . " & ";
            }
            $service = $service . $serviceMonths;
            if ($serviceMonths == 1) {
                $service = $service . " month";
            } else {
                $service = $service . " months";
            }
        }

        if ($service == '') {
            $service = abs(round($days / 86400));
            if ($days == 1) {
                $service = $service . " day";
            } else if ($days > 1) {
                $service = $service . " days";
            }
        }


        return $service;
    }

    public function StructureChecker($struct, $operation) {
        $structure = $this->session->userdata('structure');
        $index = 0;
        $department = '';
        $last_search = '';
        $multiple_filter = array('COM' => '', 'LOC' => '', 'DIV' => '', 'DEP' => '', 'SEC' => '', 'ARE' => '');
        foreach ($struct as $val) {
            if ($val != 'All') {
                $last_search = $val;
                $explode_val = explode('-', $val);
                $multiple_filter[$explode_val[0]] = $val;
                if (isset($structure[$val])) {
                    if ($index == 0) {
                        $department = $val;
                    }
                    $index++;
                }
            }
        }
        //        unset($multiple_filter[$last_search]);
        return $this->WhereQuery($index, $structure, $operation, $last_search, $department, $multiple_filter);
    }

    public function WhereQuery($index, $structure, $operation, $last_search, $department, $multiple_filter) {
        $where = '(';
        $is_first = 0;
        $samplee = array();
        $where_struct = array('COM' => 'comID', 'LOC' => 'locID', 'DIV' => 'divID', 'DEP' => 'depID', 'SEC' => 'secID', 'ARE' => 'areID');
        if ($index > 1) {
            $added_structure = '';
            foreach ($structure[$last_search] as $row) {
                $explode_row = explode('@', $row);
                $sampol = 0;
                foreach ($explode_row as $val) {
                    $explode_val = explode('-', $val);
                    if ($multiple_filter[$explode_val[0]] == $val) {
                        $sampol++;
                    }
                }
                if ($sampol == $index) {
                    $samplee[] = $row;
                }
            }


            foreach ($samplee as $row) {
                $explode_row = explode('@', $row);
                foreach ($explode_row as $val) {
                    $explode_val = explode('-', $val);
                    if ($is_first == 0) {
                        if ($where == '(') {
                            $where = $where . "(" . $added_structure . "`" . $where_struct[$explode_val[0]] . "`" . $operation . "'" . $val . "'";
                        } else {
                            $where = $where . " OR (" . $added_structure . "`" . $where_struct[$explode_val[0]] . "`" . $operation . "'" . $val . "'";
                        }
                        $is_first++;
                    } else {
                        $where = $where . " AND " . "`" . $where_struct[$explode_val[0]] . "`" . $operation . "'" . $val . "'";
                    }
                }
                $where = $where . ")";
                $is_first = 0;
            }
            $where = $where . ")";
        } else if ($index == 1) {
            foreach ($structure[$department] as $row) {
                $explode_row = explode('@', $row);
                foreach ($explode_row as $val) {
                    $explode_val = explode('-', $val);
                    if ($is_first == 0) {
                        if ($where == '(') {
                            $where = $where . "(" . "`" . $where_struct[$explode_val[0]] . "`" . $operation . "'" . $val . "'";
                        } else {
                            $where = $where . " OR (" . "`" . $where_struct[$explode_val[0]] . "`" . $operation . "'" . $val . "'";
                        }
                        $is_first++;
                    } else {
                        $where = $where . " AND " . "`" . $where_struct[$explode_val[0]] . "`" . $operation . "'" . $val . "'";
                    }
                }
                $where = $where . ")";
                $is_first = 0;
            }
            $where = $where . ")";
        } else {
            $roles = $this->session->userdata('roles');
            foreach ($roles as $key => $value) {
                $explode_key = explode('-', $key);
                if ($is_first == 0) {
                    $where = $where . "`" . $where_struct[$explode_key[0]] . "`" . $operation . "'" . $key . "'";
                    $is_first++;
                } else {
                    $where = $where . " OR " . "`" . $where_struct[$explode_key[0]] . "`" . $operation . "'" . $key . "'";
                }
            }
            $where = $where . ")";
        }
        return $where;
    }

    public function CheckedEmployees($employee, $selected_department, $unselected_departments, $profileno_selected, $profileno_unselected) {

        $value = false;
        foreach ($selected_department as $row) {
            $explode_row = explode('/', $row);
            $index = 0;
            if ($explode_row[5] == $employee->areID || $explode_row[5] == 'All') {
                $index++;
            }
            if ($explode_row[4] == $employee->secID || $explode_row[4] == 'All') {
                $index++;
            }
            if ($explode_row[3] == $employee->depID || $explode_row[3] == 'All') {
                $index++;
            }
            if ($explode_row[2] == $employee->divID || $explode_row[2] == 'All') {
                $index++;
            }
            if ($explode_row[1] == $employee->locID || $explode_row[1] == 'All') {
                $index++;
            }
            if ($explode_row[0] == $employee->comID || $explode_row[0] == 'All') {
                $index++;
            }
            if ($index == 6) {
                $value = $this->UncheckedEmployees($employee, $unselected_departments, $profileno_unselected);
                break;
            }
        }
        if (isset($profileno_selected[$employee->profileno])) {
            $value = true;
        }
        if (isset($profileno_unselected[$employee->profileno])) {
            $value = false;
        }
        return $value;
    }

    public function UncheckedEmployees($employee, $unselected_departments) {
        $value = true;
        foreach ($unselected_departments as $row) {
            $explode_row = explode('/', $row);
            $index = 0;
            if ($explode_row[5] == $employee->areID || $explode_row[5] == 'All') {
                $index++;
            }
            if ($explode_row[4] == $employee->secID || $explode_row[4] == 'All') {
                $index++;
            }
            if ($explode_row[3] == $employee->depID || $explode_row[3] == 'All') {
                $index++;
            }
            if ($explode_row[2] == $employee->divID || $explode_row[2] == 'All') {
                $index++;
            }
            if ($explode_row[1] == $employee->locID || $explode_row[1] == 'All') {
                $index++;
            }
            if ($explode_row[0] == $employee->comID || $explode_row[0] == 'All') {
                $index++;
            }

            if ($index == 6) {
                $value = false;
                break;
            }
        }
        return $value;
    }

    public function CategoriesEmployees($employees) {
        $structure = array();
        foreach ($employees as $row) {
            $structure[$row->locID . $row->comID]['company'] = $row->comID;
            if ($row->locID == '' || $row->locID == NULL) {
                $structure[$row->locID . $row->comID]['location'] = 'No assigned location';
            } else {
                $structure[$row->locID . $row->comID]['location'] = $row->locID;
            }
            $structure[$row->locID . $row->comID]['employees'][] = $row;
        }
        return $structure;
    }

    public function ReinitializeTime($time, $time2) {
        $explode_time = explode(':', $time);
        $explode_time2 = explode(':', $time2);
        $explode_hour = explode(' ', $explode_time[0]);
        $explode_hour2 = explode(' ', $explode_time2[0]);
        $excess = 0;
        if ($explode_time[1] > 0) {
            $excess = 60 - $explode_time[1];
            $explode_hour[1] = $explode_hour[1] + 1;
        } else {
            $excess = 0;
        }
        $total = ($excess + $explode_time2[1]) / 60;
        return abs(($explode_hour2[1] - $explode_hour[1]) + $total);
    }

    public function ReportDepartment($structure) {
        $department = 'All Departments';
        $struct = array('DIV' => 'tbl_division', 'DEP' => 'tbl_departments', 'SEC' => 'tbl_sections', 'ARE' => 'tbl_areas');
        foreach ($structure as $value) {
            if ($value != 'All') {
                $explode_value = explode('-', $value);
                if ($explode_value[0] != 'COM' && $explode_value[0] != 'LOC') {
                    $department = $value;
                }
            }
        }
        if ($department != 'All Departments') {
            $explode_department = explode('-', $department);
            $dept = $this->M_structure->FetchStructureName(array('refno' => $department), $struct[$explode_department[0]]);
            $department = $dept[0]->name;
        }
        return $department;
    }

    public function SetDates($datein, $dateout) {
        $stringDates = '';
        $datesArray = array();
        $datesforPDF = '';
        while ($datein <= $dateout) {

            $day_of_week = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
            $stringDates = $stringDates . $datein . ' ';
            $datesArray[$datein] = $datein;
            $datesforPDF = $datesforPDF . '<th style="border:solid;border-width: thin;font-size:10px" colspan="2">' . date('M d', strtotime($datein)) . ' (' . $day_of_week[date('w', strtotime($datein))] . ')' . '</th>';
            $datein = date('Y-m-d', strtotime($datein . '+1 days'));
        }
        $sub = array();
        $sub[] = $stringDates;
        $sub[] = $datesArray;
        $sub[] = $datesforPDF;

        return $sub;
    }

    public function WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno) {
        $where_selected = '';
        $profileno_selected = '';
        foreach ($selected_departments as $row) {
            $explode_row = explode('/', $row);
            $struct = array('comID' => $explode_row[0], 'locID' => $explode_row[1], 'divID' => $explode_row[2], 'depID' => $explode_row[3], 'secID' => $explode_row[4], 'areID' => $explode_row[5]);
            $selected = $this->StructureChecker($struct, "=");
            if ($where_selected == '') {
                $where_selected = $selected;
            } else {
                $where_selected = $where_selected . " OR" . $selected;
            }
        }
        foreach ($selectd_profileno as $val) {
            if ($profileno_selected == '') {
                $profileno_selected = "(" . "profileno  =" . "'" . $val . "'";
            } else {
                $profileno_selected = $profileno_selected . " OR " . "profileno=" . "'" . $val . "'";
            }
        }
        if (count($selected_departments) > 0 || count($selectd_profileno) > 0) {
            foreach ($unselectd_departments as $row) {
                $explode_row = explode('/', $row);
                $struct = array('comID' => $explode_row[0], 'locID' => $explode_row[1], 'divID' => $explode_row[2], 'depID' => $explode_row[3], 'secID' => $explode_row[4], 'areID' => $explode_row[5]);
                $unselected = $this->StructureChecker($struct, "=");
                if ($where_selected == '') {
                    $where_selected = $unselected;
                } else {
                    $where_selected = $where_selected . " AND NOT " . $unselected;
                }
            }
            foreach ($unselectd_profileno as $val) {
                if ($profileno_selected == '') {
                    $profileno_selected = "profileno !=" . "'" . $val . "'";
                } else {

                    $profileno_selected = $profileno_selected . " AND " . "profileno !=" . "'" . $val . "'";
                }
            }
        }

        ($profileno_selected != '') ? $profileno_selected = $profileno_selected . ")" : "";

        if ($where_selected != '' && $profileno_selected != '') {
            $where_selected = '(' . $where_selected . " OR " . $profileno_selected . ")";
        } else if ($profileno_selected != '') {
            $where_selected = $profileno_selected;
        } else if ($where_selected != '') {
            $where_selected = '(' . $where_selected . ")";
        }

        return $where_selected;
    }

    public function SetupDates($datein, $dateout) {
        $dates = array();
        while ($datein <= $dateout) {
            $dates[date('Y-m-d', strtotime($datein))]['scheds'] = array();
            $dates[date('Y-m-d', strtotime($datein))]['bioms'] = array();
            $datein = date('Y-m-d', strtotime($datein . '+1 days'));
        }
        return $dates;
    }

    public function OrganizeScheds($dates, $sched) {
        foreach ($sched as $val) {
            $count = 1;
            foreach ($val['biom'] as $row) {
                $range_in = 0;
                $range_out = 0;
                if (count($val['biom']) > 1) {
                    $range_in = 1;
                    $range_out = 1;
                }
                if ($count == 1) {
                    $range_in = 8;
                }
                if ($count == count($val['biom'])) {
                    $range_out = 8;
                }
                $dates[$val['date_index']]['bioms'][$row['type']] = array(
                    'worksched_in' => $row['timein'],
                    'worksched_out' => $row['timeout'],
                    'undertime_in' => $row['timein'],
                    'undertime_out' => $row['timeout'],
                    'raw_quarterin' => $row['raw_quarterin'],
                    'raw_quarterout' => $row['raw_quarterout'],
                    'punch_in' => '',
                    'punch_out' => '',
                    'autofill_in' => 0,
                    'autofill_out' => 0,
                    'range_in' => $range_in,
                    'range_out' => $range_out,
                    'late_in' => false,
                    'late_out' => false,
                    'leave_id' => $row['leave_id'],
                    'has_undertime' => $row['has_undertime'],
                    'has_leave' => $row['has_leave'],
                    'leave_category' => $row['leave_category'],
                    'has_late' => false,
                    'total_late' => 0,
                    'total_ut' => '',
                    'type' => $row['type']
                );
                $count++;
            }
            foreach ($val['scheds'] as $row) {
                $dates[$val['date_index']]['scheds'][$row['type']] = $row;
            }
        }
        return $dates;
    }

    public function ReviewSched($dates, $scheds, $profileno, $datein, $dateout) {
        $has_leave = $this->MY_Model->FetchApprovedLeave($profileno, date('Y-m-d', strtotime($datein)) . ' 23:00:00', date('Y-m-d', strtotime($dateout)) . ' 23:59:59', 'tbl_leave_days');
        $has_undertime = $this->M_undertime->FetchApprovedUndertime(
                $this->CleanArray(array(
                    'profileno' => $profileno,
                    'approved_status' => 1,
                    'hr_cancel_status' => 0
                )),
                $datein,
                $dateout
        );
        $data = array();
        $primary_type = '';

        foreach ($scheds as $val) {

            $timein = $val->timein;
            $timeout = $val->timeout;
            $raw_quarterin = '';
            $raw_quarterout = '';
            $leave_ut = (array) $this->CheckLeaveAndUndertime($has_leave, $has_undertime, $val->timein);
            $last_schedref = '';
            if ($leave_ut['has_leave'] == true) {
                if ($leave_ut['data']->category == 0) {
                    $timein = $leave_ut['data']->quarter_schedout;
                } else if ($leave_ut['data']->category == 1) {
                    $timeout = $leave_ut['data']->quarter_schedin;
                } else if ($leave_ut['data']->category == 2) {
                    $timein = '0000-00-00 00:00:00';
                }
                $raw_quarterin = $leave_ut['data']->quarter_schedin;
                $raw_quarterout = $leave_ut['data']->quarter_schedout;
            } else if ($leave_ut['has_undertime'] == true) {
                $timein = $leave_ut['data']->actual_in;
                $timeout = $leave_ut['data']->actual_out;
            }
            if (isset($data[$val->schedrefindx])) {
                $ref_type = $val->type;
                $last_schedref = $val->schedrefindx;

                $total = abs(strtotime($timein) - strtotime($data[$last_schedref]['biom'][$data[$last_schedref]['last_reftype']]['timeout'])) / (60 * 60);

                if ($total == 0 && $timein != '0000-00-00 00:00:00') {
                    $data[$last_schedref]['biom'][$data[$val->schedrefindx]['primary_type']]['timeout'] = $timeout;
                    $ref_type = $data[$last_schedref]['scheds'][$data[$val->schedrefindx]['primary_type']]['ref_type'];
                } else {
                    $primary_type = $val->type;
                    $data[$val->schedrefindx]['primary_type'] = $val->type;
                    $data[$val->schedrefindx]['biom'][$val->type] = array(
                        'timein' => $timein, 'timeout' => $timeout, 'type' => $val->type, 'has_leave' => $leave_ut['has_leave'],
                        'raw_quarterin' => $raw_quarterin,
                        'raw_quarterout' => $raw_quarterout, 'leave_id' => $leave_ut['leaveform_id'], 'raw_quarterin', 'leave_category' => $leave_ut['leave_category'], 'has_undertime' => $leave_ut['has_undertime']
                    );
                    $data[$val->schedrefindx]['last_reftype'] = $val->type;
                }
                $data[$val->schedrefindx]['scheds'][$val->type] = array(
                    'worksched_in' => $val->timein,
                    'worksched_out' => $val->timeout,
                    'ref_type' => $ref_type,
                    'schedrefindex' => $val->schedrefindx,
                    'has_leave' => $leave_ut['has_leave'],
                    'leave_category' => $leave_ut['leave_category'],
                    'quarter_in' => $timein,
                    'quarter_out' => $timeout,
                    'raw_quarterin' => $raw_quarterin,
                    'raw_quarterout' => $raw_quarterout,
                    'type' => $val->type
                );
            } else {
                $last_schedref = $val->indx;
                $primary_type = $val->type;

                $data[$val->indx]['biom'][$val->type] = array(
                    'timein' => $timein,
                    'timeout' => $timeout,
                    'type' => $val->type,
                    'has_leave' => $leave_ut['has_leave'],
                    'leave_category' => $leave_ut['leave_category'],
                    'leave_id' => $leave_ut['leaveform_id'],
                    'raw_quarterin' => $raw_quarterin,
                    'raw_quarterout' => $raw_quarterout,
                    'has_undertime' => $leave_ut['has_undertime']
                );
                $data[$val->indx]['scheds'][$val->type] = array(
                    'worksched_in' => $val->timein,
                    'worksched_out' => $val->timeout,
                    'schedrefindex' => $val->indx,
                    'has_leave' => $leave_ut['has_leave'],
                    'leave_category' => $leave_ut['leave_category'],
                    'quarter_in' => $timein,
                    'quarter_out' => $timeout,
                    'raw_quarterin' => $raw_quarterin,
                    'raw_quarterout' => $raw_quarterout,
                    'ref_type' => $val->type,
                    'type' => $val->type
                );
                $data[$val->indx]['date_index'] = date('Y-m-d', strtotime($val->timein));
                $data[$val->indx]['last_reftype'] = $val->type;
                $data[$val->indx]['primary_type'] = $val->type;
            }
        }
        return $this->OrganizeScheds($dates, $data);
    }

    public function CheckLeaveAndUndertime($leave_array, $undertime_array, $schedin) {
        $data = array('has_leave' => false, 'leaveform_id' => 0, 'leave_category' => 3, 'has_undertime' => false, 'data' => null);
        foreach ($leave_array as $key => $val) {
            if ($val->worksched_in == $schedin) {

                $data['has_leave'] = true;
                $data['leave_category'] = $val->category;
                $data['leaveform_id'] = $val->leave_id;
                $data['data'] = $val;
                break;
            }
        }
        if ($data['has_leave'] == false) {
            foreach ($undertime_array as $key => $val) {
                if ($val->worksched_in == $schedin && $val->approved_status == 1) {
                    $data['has_undertime'] = true;
                    $data['data'] = $val;
                    break;
                }
            }
        }
        return $data;
    }

    public function SetupEmployeeSchedule($dates, $profileno, $datein, $dateout, $deduct_in) {
        $raw_sched = $this->M_worksched->UserWorkSchedule($datein, $dateout, $profileno);
        $sched = $this->ReviewSched($dates, $raw_sched, $profileno, $datein, $dateout);
        foreach ($sched as $date => $row) {
            foreach ($row['bioms'] as $type => $val) {
                if ($val['leave_category'] != 2) {
                    $in = date('Y-m-d H:i', strtotime($val['worksched_in'] . ' ' . $deduct_in . ' minutes'));

                    $timein = $this->M_dtr->FetchUserDTR($profileno, array('time_start' => date('Y-m-d H:i:s', strtotime($in) - 60 * 60 * $sched[$date]['bioms'][$type]['range_in']), 'time_end' => date('Y-m-d H:i:s', strtotime($in) + 60 * 60 * $sched[$date]['bioms'][$type]['range_in'])), 'timein', 'tbl_biometric_time_in', 'profileno_timein');
                    $timeout = $this->M_dtr->FetchUserDTR($profileno, array('time_start' => date('Y-m-d H:i:s', strtotime($val['worksched_out']) - 60 * 60 * $sched[$date]['bioms'][$type]['range_out']), 'time_end' => date('Y-m-d H:i:s', strtotime($val['worksched_out']) + 60 * 60 * $sched[$date]['bioms'][$type]['range_out'])), 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');

                    if (count($timein) <= 0 && count($timeout) <= 0) {
                        $sched[$date]['bioms'][$type]['punch_in'] = (date('Y-m-d', strtotime($val['worksched_in'])) < date('Y-m-d')) ? 'Absent' : 'Pending';
                    } else {
                        $late_in = 0;
                        $early_out = 0;
                        if (count($timein) > 0) {
                            $sched[$date]['bioms'][$type]['punch_in'] = date('g:i A', strtotime($timein[0]->timein));
                            $sched[$date]['bioms'][$type]['autofill_in'] = $timein[0]->autofill;
                            $late_in = $this->ComputeLate($in, $timein, 0);
                            $sched[$date]['bioms'][$type]['late_in'] = ($late_in > 0) ? true : false;
                        } else {
                            $sched[$date]['bioms'][$type]['punch_in'] = "Missing";
                        }
                        if (count($timeout) > 0) {
                            $sched[$date]['bioms'][$type]['punch_out'] = date('g:i A', strtotime($timeout[0]->timeout));
                            $sched[$date]['bioms'][$type]['autofill_out'] = $timeout[0]->autofill;
                            $early_out = $this->ComputeLate($val['worksched_out'], $timeout, 1);
                            $sched[$date]['bioms'][$type]['late_out'] = ($early_out > 0) ? true : false;
                        } else {
                            $sched[$date]['bioms'][$type]['punch_out'] = "Missing";
                        }
                        if ($late_in > 0 || $early_out > 0) {
                            $sched[$date]['bioms'][$type]['total_late'] = $late_in + $early_out;
                            $sched[$date]['bioms'][$type]['has_late'] = true;
                        }
                    }
                } else {
                    $leave_form = $this->M_leave->FetchLeaveUsdingID(array('ID' => $sched[$date]['bioms'][$type]['leave_id']));
                    $leave_type = $this->M_leavecredits->FetchSpecificLeaveType(array('id' => $leave_form[0]->leavetype));
                    $sched[$date]['bioms'][$type]['punch_in'] = $leave_type[0]->name;
                    $sched[$date]['bioms'][$type]['punch_out'] = $leave_type[0]->name;
                }
            }
        }

        return $sched;
    }

    public function ComputeLate($workschedule, $biometric, $category) {
        $actminutes = 0;
        if (count($biometric) > 0) {
            if ($category == 0) {
                if (strtotime($workschedule) < strtotime($biometric[0]->timein)) {
                    $diff = abs(strtotime(date('Y-m-d H:i', strtotime($biometric[0]->timein))) - strtotime($workschedule));
                    $actminutes = round($diff / 60);
                }
            } else {
                if (strtotime($workschedule) > strtotime($biometric[0]->timeout)) {
                    $diff = abs(strtotime($workschedule) - strtotime(date('Y-m-d H:i', strtotime($biometric[0]->timeout))));
                    $actminutes = round($diff / 60);
                }
            }
        }
        return $actminutes;
    }

    public function ComputeTotalUndertime($undertime) {
        $total = 0;
        if ($undertime->undertime_type == 0) {
            $total = $this->ReinitializeTime($undertime->worksched_in, $undertime->actual_in);
        } else {
            $total = $this->ReinitializeTime($undertime->actual_out, $undertime->worksched_out);
        }

        return $total;
    }

    public function SetupLeaveSql($dates) {
        $leave_sql = '(';
        foreach ($dates as $key => $val) {
            ($leave_sql != '(') ? $leave_sql = $leave_sql . " OR " : '';
            $leave_sql = $leave_sql . "('" . $key . "' BETWEEN " . "fromdate AND todate)";
        }
        $leave_sql = $leave_sql . ")";
        return $leave_sql;
    }

    public function NotificationInitialization() {
        $struct = array("comID" => "All", "locID" => "All", "divID" => "All", "depID" => "All", "secID" => "All", "areID" => "All");
        $where = $this->StructureChecker($struct, "=");
        $all_pending_leave = $this->MY_Model->MyDataTable($this->FormTable('tableleave', '', '', '', '', 1, 0, $where), array());
        $all_pending_undertime = $this->MY_Model->MyDataTable($this->FormTable('tbl_undertime', '', '', '', '', 1, 0, $where), array());
        $all_pending_cs = $this->MY_Model->MyDataTable($this->FormTable('tbl_change_schedule', '', '', '', '', 1, 0, $where), array());
        $all_pending_overtime = $this->MY_Model->MyDataTable($this->FormTable('tbl_overtime', '', '', '', '', 1, 0, $where), array());

        $cancel_pending_leave = $this->MY_Model->MyDataTable($this->FormTable('tableleave', '', '', '', '', 1, 1, $where), array());
        $cancel_pending_undertime = $this->MY_Model->MyDataTable($this->FormTable('tbl_undertime', '', '', '', '', 1, 1, $where), array());
        $cancel_pending_overtime = $this->MY_Model->MyDataTable($this->FormTable('tbl_overtime', '', '', '', '', 1, 1, $where), array());

        $announcement_images = $this->M_announcement->FetchAnnouncementImages(date('Y-m-d'));
        $announcement = $this->M_announcement->FetchDashboardAnnouncements(date('Y-m-d'));

        $approve_as_reliever = $this->M_changeschedule->RequesterTable($this->session->userdata('profileno'), '', '');
        $data = array(
            'leave' => count($all_pending_leave),
            'undertime' => count($all_pending_undertime),
            'cs' => count($all_pending_cs),
            'ot' => count($all_pending_overtime),
            'cancel_leave' => count($cancel_pending_leave),
            'cancel_undertime' => count($cancel_pending_undertime),
            'cancel_ot' => count($cancel_pending_overtime),
            'announcement_images' => count($announcement_images),
            'announcement' => count($announcement),
            'approve_as_reliever' => count($approve_as_reliever)
        );

        return $data;
    }

    public function FormTable($table, $empname, $profileno, $datein, $dateout, $page, $cancellation_page, $where) {
        $data = array();
        $data['table'] = $table;
        if ($empname != '') {
            $data['empname'] = $empname;
        }
        if ($table == 'tableleave') {
            if ($datein != '') {
                $data['dates'] = $this->SetupLeaveSql($this->SetupDates($datein, $dateout));
            }
        }
        if ($datein != '' && $datein != NULL) {
            $data['datein'] = date('Y-m-d', strtotime($datein)) . " " . '00:00:00';
            $data['dateout'] = date('Y-m-d', strtotime($dateout)) . " " . '23:59:59';
        }
        if ($page != '') {
            $data['page'] = $page;
            if ($page == 0) {
                $data['approved_forms'] = array('approved_status' => 1, 'hr_cancel_status' => 0);
            }
        }

        if ($cancellation_page !== '') {
            $data['cancellation_page'] = $cancellation_page;
        }
        if (count($where) > 0) {
            $data['whereStructure'] = $where;
        }
        if ($profileno != '') {
            $data['profileno'] = $profileno;
        }

        return $this->CleanArray($data);
    }

    public function ConvertImage($image) {
        if (count($image) > 0) {
            $image = base64_encode($image[0]->blobimg);
        } else {
            $tmp_name = 'assets/images/profile.png';
            $file_content = file_get_contents($tmp_name);
            $image = base64_encode($file_content);
        }

        return $image;
    }

    public function QueryProfileno($emp) {
        $profileno_query = '';
        foreach ($emp as $val) {
            if ($profileno_query == '') {
                $profileno_query = '( profileno =' . "'" . $val->profileno . "'";
            } else {
                $profileno_query = $profileno_query . ' OR ' . ' profileno =' . "'" . $val->profileno . "'";
            }
        }
        if ($profileno_query != '') {
            $profileno_query = $profileno_query . ')';
        }
        return $profileno_query;
    }

    public function CheckUpongReachingRestriction($regular_date, $credit_updated, $day_category, $day_number) {
        //       var_dump('credit_updated: '. $credit_updated);
        //       var_dump('regular_updated: '. $regular_date);
        $category = ($day_category == 0) ? ' months' : ' years';
        $date = date('Y-m-d', strtotime($regular_date . " +" . $day_number . $category));
        if ($credit_updated != null) {
            if ($date > $credit_updated && $date <= date('Y-m-d')) {
                return true;
            }
        } else if ($date <= date('Y-m-d')) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckPrerequisiteRestriction($regular_date, $day_category, $day_number) {
        $category = ($day_category == 0) ? ' months' : ' years';
        $date = date('Y-m-d', strtotime($regular_date . " +" . $day_number . $category));
        if ($date <= date('Y-m-d')) {
            return true;
        } else {
            return false;
        }
    }

    public function OrganizeNumberFormat($value) {
        $temp = explode('.', $value);
        $rounded = number_format($temp[0]);
        if (count($temp) > 1) {
            $rounded .= "." . $temp[1];
        }
        return $rounded;
    }

    function CalculateRemainingCredits($id, $emp_credits, $leave_payment_type, $previous_total, $total_days, $prev_payment_type) {
        $data = array('remaining' => -1, 'taken' => 0);
        if (count($emp_credits) > 0) {
            if ($leave_payment_type == 1) {
                if ($id > 0 && $prev_payment_type == 1) {
                    $data['remaining'] = ($emp_credits[0]->remaining_days + $previous_total) - $total_days;
                    $data['taken'] = ($emp_credits[0]->taken_days - $previous_total) + $total_days;
                } else {
                    $data['remaining'] = $emp_credits[0]->remaining_days - $total_days;
                    $data['taken'] = $emp_credits[0]->taken_days + $total_days;
                }
            } else {
                if ($id > 0 && $prev_payment_type == 1) {
                    $data['remaining'] = $emp_credits[0]->remaining_days + $previous_total;
                    $data['taken'] = $emp_credits[0]->taken_days - $previous_total;
                } else {
                    $data['remaining'] = $emp_credits[0]->remaining_days;
                    $data['taken'] = $emp_credits[0]->taken_days;
                }
            }
        }
        return $data;
    }

    public function OrganizeMoneyFormat($value) {
        $temp = explode('.', $value);
        $rounded = number_format($temp[0]);
        if (count($temp) > 1) {
            $rounded .= "." . $temp[1];
        }
        return $rounded;
    }

    public function SendTextMessage($text, $contact_number, $file_tite, $transaction) {
        $data = array(
            'systemlog' => 'HRIS',
            'userid' => $this->session->userdata('empid'),
            'station' => '',
            'name' => $this->session->userdata('lastname') . ', ' . $this->session->userdata('firstname'),
            'Number' => $contact_number,
            'Message' => $text,
            'Datelog' => date('Y-m-d H:i:s'),
            'Status' => 0,
            'transaction' => $transaction
        );
        $this->MY_Model->SaveTextData($data);

        $path = $this->session->userdata('server_address') . "/" . $this->session->userdata('path') . "/";

        if (write_file($path . $file_tite . '.txt', $text . "\n" . $contact_number . "\n" . "~")) {
            
        }
    }

    //    public function CategorizeEmpByDept($employees){
    //           $structure = array();
    //        foreach ($employees as $row) {
    //            $structure[$row->locID . $row->comID]['company'] = $row->comID;
    //            if ($row->locID == '' || $row->locID == NULL) {
    //                $structure[$row->locID . $row->comID]['location'] = 'No assigned location';
    //            } else {
    //                $structure[$row->locID . $row->comID]['location'] = $row->locID;
    //            }
    //            $structure[$row->locID . $row->comID]['employees'][$row->depID][] = $row;
    //        }
    //        return $structure;
    //    }
}
