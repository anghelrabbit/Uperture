<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestForms
 *
 * @author MIS
 */
class RequestOvertime extends MY_Controller {

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
            $data["page_title"] = "Request Overtime";
            $data['page'] = 'pages/menu/my_account/request_forms/request_overtime';


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

    public function FetchWorkschedule() {

        $date = date('Y-m-d', strtotime($this->input->post('date')));
        $id = $this->input->post('id');
        $form = $this->input->post('form');
        $result = $this->M_workschedule->FetchSpecificSchedule($date);
        $data = array();
        $data['date_in'] = $date;
        $data['date_out'] = $date;
        $data['time_in'] = 'Day Off';
        $data['time_out'] = 'Day Off';
        $data['has_schedule'] = false;
        $data['has_undertime'] = false;
        if (count($result) > 0) {
            if ($form == 1) {
                $res = $this->M_undertime->CheckExistingUndertime($result[0]->timein);
                if (count($res) > 0 && $res[0]->id != $id) {
                    $data['has_undertime'] = true;
                }
            }
            $data['date_in'] = date('Y-m-d', strtotime($result[0]->timein));
            $data['date_out'] = date('Y-m-d', strtotime($result[0]->timeout));
            $data['time_in'] = $this->ConvertTo24Format($result[0]->timein);
            $data['time_out'] = $this->ConvertTo24Format($result[0]->timeout);
            $data['has_schedule'] = true;
        }
        echo json_encode($data);
    }

    public function RemoveForm() {
        $id = $this->input->post('id');
        $form = array(1 => 'tbl_undertime', 2 => 'tbl_change_schedule', 3 => 'tbl_overtime');
        echo json_encode($this->MY_Model->DeleteForm($id, $form[$this->input->post('category')]));
    }

    public function RequestCancellation() {
        $id = $this->input->post('id');
        $category = $this->input->post('category');
        $data = array();
        $data['is_deleted'] = 1;
        $data['request_cancel_date'] = date('Y-m-d H:i:s');
        if ($category == 0) {
            echo json_encode($this->M_leave->SaveUpdateLeave($id, $this->CleanArray($data)));
        } else if ($category == 1) {
            echo json_encode($this->M_undertime->SaveUpdateUndertime($this->CleanArray($data), $id));
        } else if ($category == 2) {
            echo json_encode($this->M_changeschedule->SaveUpdateChangeSchedule($id, $this->CleanArray($data)));
        } else if ($category == 3) {
            echo json_encode($this->M_overtime->SaveUpdateOvertime($this->CleanArray($data), $id));
        }
    }

}
