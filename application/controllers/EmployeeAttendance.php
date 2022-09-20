<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmployeeAttendance
 *
 * @author Angel Bunny
 */
class EmployeeAttendance extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('sendsms_helper');

        $this->load->model('model_workschedule', 'M_workschedule');
        $this->load->model('model_leave', 'M_leave');
        $this->load->model('model_undertime', 'M_undertime');
        $this->load->model('model_overtime', 'M_overtime');
        $this->load->model('model_changeschedule', 'M_changeschedule');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Employee Attendance";
            $data['page'] = 'pages/menu/employee_attendance/employee_attendance';


            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
                'assets/vendors/css/ui-lightness.css',
                'assets/vendors/assets/owlcarousel/assets/owl.carousel.min.css',
                'assets/vendors/assets/owlcarousel/assets/owl.theme.default.min.css',
                'assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css',
                'assets/vendors/bower_components/sweetalert/sweetalert.css',
                'assets/vendors/plugins/timepicker/bootstrap-timepicker.min.css',
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/vendors/assets/owlcarousel/owl.carousel.js',
                'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
                'assets/vendors/plugins/timepicker/bootstrap-timepicker.min.js',
                'assets/vendors/momentjs/moment.js',
                'assets/myjs/utilities/structure.js',
                'assets/myjs/utilities/payperiod.js',
                'assets/myjs/utilities/form_checker.js',
                'assets/myjs/my_account/request_forms/helpdesk.js',
                'assets/myjs/my_account/request_forms/leave.js',
                'assets/myjs/my_account/request_forms/undertime.js',
                'assets/myjs/my_account/request_forms/overtime.js',
                'assets/myjs/my_account/request_forms/change_schedule.js',
                'assets/myjs/my_account/request_forms/reliever.js',
                'assets/myjs/utilities/approving_buttons.js',
            );


            $this->InspectUser('menu/my_account/request_forms/request_forms', $data);
        } else {
            redirect('', 'refresh');
        }
    }

   

}
