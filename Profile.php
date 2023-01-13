<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Profile
 *
 * @author MIS
 */
class Profile extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_employee', 'M_employee');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $profileno = $this->input->post('profileno');
            $company = $this->input->post('company');
            if ($profileno == '') {
                $profileno = $this->session->userdata('profileno');
            }
            if ($company == '') {
                $company = $this->session->userdata('company');
            }
            $data["page_title"] = "Profile";
            $data['page'] = 'pages/menu/my_account/profile/profile';
            $data['profileno'] = $profileno;
            $data['company'] = $company;
            $data['employeeprofile'] = $this->M_employee->FetchEmployee($this->CleanArray(array('profileno' => $profileno)));
            $data['profile_image'] = $this->ConvertImage($this->M_employee->FetchProfilePic(array('refno' => $profileno)));
            $data['wifipass'] = $this->FetchWifiPassword($data['employeeprofile'][0]->profileno);
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $data['employeeprofile'][0]->jobcode)));
            $data['jobposition'] = (count($job) > 0) ? $job[0]->jobname : 'Unassigned';
            $data['isadmin'] = 0;
            $data["css"] = array(
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/sweetalert/sweetalert.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
                
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/bower_components/raphael/raphael.min.js',
                'assets/vendors/bower_components/morris.js/morris.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/vendors/sweetalert/sweetalert.min.js',
                'assets/myjs/utilities/payperiod.js',
                'assets/myjs/my_account/profile/payslip.js',
                'assets/myjs/my_account/profile/workschedule.js',
                'assets/myjs/my_account/profile/profile.js',
                'assets/vendors/momentjs/moment.js',
            );
            $this->InspectUser('menu/my_account/profile', $data);
        } else {
            $this->index();
        }
    }

}
