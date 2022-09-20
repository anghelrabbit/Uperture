<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LandingPage
 * @copyright (c) 2019-2020, Drainwiz
 * @version 2.0
 * @author Drainwiz
 */
class LandingPage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->load->model('model_structure', 'M_structure');
        $this->load->model('model_employee', 'M_employee');
    }

    public function index() {
        $data["page_title"] = "UPERTURE";

//        $data["css"] = array
//            (
//            'assets/css/style.css',
//            'assets/css/mystyle.css',
//            'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
//            'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
//            'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
//            'assets/prop/css/propeller.min.css',
//            'assets/vendors/bower_components/sweetalert/sweetalert.css',
//            'assets/mycss/my_background.css',
//        );
//
//        $data["js"] = array
//            (
//            'assets/vendors/bower_components/jquery/dist/jquery.min.js',
//            'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
//            'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
//            'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
//            'assets/myjs/login/login.js',
//            'assets/myjs/utilities/form_checker.js'
//        );
//        $this->InspectUser(NULL, $data);
//    }
    
    
    $data["css"] = array
            (
            'assets/logins/css/style.css',
           
        );

        $data["js"] = array
            (
            'assets/logins/js/jquery.min.js',
            'assets/logins/js/popper.js',
            'assets/logins/js/bootstrap.min.js',
            'assets/logins/js/main.js',
            'assets/myjs/login/login.js',
            'assets/myjs/utilities/form_checker.js'
        );
        $this->InspectUser(NULL, $data);
    }
    

    public function LoginAccount() {
        $validation = array();
        $result = false;
        $approved = 1;
        if ($this->form_validation->run() == FALSE) {
            $validation[] = $this->ValidateErrors('span[name=invalid_input]', 'username', form_error('username'), 'Username / Password required');
            $validation[] = $this->ValidateErrors('span[name=invalid_input]', 'password', form_error('password'), 'Username / Password required');
        } else {
            $data = array();
            $data['username'] = $this->input->post('username');
            $data['password'] = $this->encrypt_pass($this->input->post('password'));
            $valid = $this->M_employee->CheckAccount($data);
            if (count($valid) > 0) {
                if ($valid[0]->is_approve == 1) {
                    $role = $this->M_employee->CheckUserRole($this->CleanArray($data = array('profileno' => $valid[0]->profileno)));
                    $profile_pic = $this->ConvertImage($this->M_employee->FetchProfilePic(array('refno' => $valid[0]->profileno)));
                    $company = $this->M_structure->FetchStructureName($this->CleanArray($data = array('refno' => $valid[0]->comID)), 'tbl_company');
                    $jobposition = $this->M_employee->FetchJobposition($this->CleanArray($data = array('jobcode' => $valid[0]->jobcode)));
                    $logo = $this->M_structure->FetchCompanyLogo($this->CleanArray($data = array('refno' => $valid[0]->comID)));
                    if (count($logo) > 0) {
                        $logo = base64_encode($logo[0]->blobimg);
                    } else {
                        $logo = '';
                    }
                    if (count($company) > 0) {
                        $company = $company[0]->name;
                    } else {
                        $company = '';
                    }
                    if (count($jobposition) > 0) {
                        $jobposition = $jobposition[0]->jobname;
                    } else {
                        $jobposition = '';
                    }
                    $this->UserSession($valid, $role, $jobposition, $company, $logo, $profile_pic);
                    $result = true;
                } else {
                    $result = false;
                    $approved = $valid[0]->is_approve;
                }
            } else {
                $result = false;
                $validation[] = $this->ValidateErrors('span[name=invalid_input]', 'username', form_error('username'), 'Invalid Username / Password');
                $validation[] = $this->ValidateErrors('span[name=invalid_input]', 'password', form_error('password'), 'Invalid Username / Password');
            }
        }
        echo json_encode(array('result' => $result, 'data' => $validation, 'approved' => $approved));
    }

    public function checkPassword() {
        $result = false;
        $data['username'] = $this->session->userdata('username');
        $data['password'] = $this->encrypt_pass($this->input->post('password'));
        $valid = $this->M_employee->CheckAccount($this->CleanArray($data));
        if (count($valid) > 0) {
            $this->session->set_userdata('idleacc', 0);
            $result = true;
        }
        echo json_encode($result);
    }

    public function Signout() {
        $this->session->sess_destroy();
        redirect('http://localhost/SilverSummit', 'refresh');
    }

}
