<?php

class Registry extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
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
//             'assets/vendors/bower_components/sweetalert/sweetalert.css',
//            'assets/prop/css/propeller.min.css',
//            'assets/mycss/my_background.css',
//        );
//
//        $data["js"] = array
//            (
//            'assets/vendors/bower_components/jquery/dist/jquery.min.js',
//            'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
//            'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
//             'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
//            'assets/myjs/login/login.js',
//            'assets/myjs/utilities/form_checker.js'
//        );
//        
        
        $data["css"] = array
            (
            //            'assets/css/style.css',
//            'assets/css/mystyle.css',
//            'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
//            'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
//            'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
             'assets/vendors/bower_components/sweetalert/sweetalert.css',
//            'assets/prop/css/propeller.min.css',
//            'assets/mycss/my_background.css',
            'assets/logins/css/style.css',
           
        );

        $data["js"] = array
            (
            'assets/logins/js/jquery.min.js',
            'assets/logins/js/popper.js',
            'assets/logins/js/bootstrap.min.js',
             'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
            'assets/logins/js/main.js',
//            'assets/myjs/login/login.js',
            'assets/myjs/login/register.js',
            'assets/myjs/utilities/form_checker.js'
        );
        
        $this->load->view('pages/registry/registry', $data);
    }

    public function SaveUpdateRegistry() {
        $data = array(
            'lastname' => $this->input->post('register_lastname'),
            'firstname' => $this->input->post('register_firstname'),
            'midname' => $this->input->post('register_midname'),
            'birthdate' => $this->input->post('register_dob'),
            'email' => $this->input->post('register_email'),
            'contact' => $this->input->post('register_contact_num'),
            'username' => $this->input->post('register_username'),
            'password' => $this->encrypt_pass($this->input->post('register_password')),
            'is_approve' => 0,
            'status'=>1
        );

        echo json_encode($this->M_employee->RegisterAccount($data));
    }

}
