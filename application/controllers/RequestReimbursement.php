<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestReimbursement
 *
 * @author angel bunny
 */
class RequestReimbursement extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('sendsms_helper');

        $this->load->model('model_reimbursement', 'M_reimbursement');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Request Reimbursement";
            $data['page'] = 'pages/menu/my_account/request_forms/request_reimbursement';


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
                'assets/myjs/my_account/request_forms/reimbursements.js',
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

    public function SaveReimbursementRequest() {


        $currentdate = date("Y-m-d h:i:s");
        $splitcurrentdate = explode(" ", $currentdate);
        $curdate = explode("-", $splitcurrentdate[0]);
        $curtime = explode(":", $splitcurrentdate[1]);
        $datecode = $curdate[1] . $curdate[2] . $curdate[0];
        $timecode = $curtime[0] . $curtime[1] . $curtime[2];
        $reimbursement_id = $datecode . $timecode . "REIM";

        $data = array(
            'profileno' => $this->session->userdata('profileno'),
            'reimbursed_item' => $this->input->post('reimbursement_for'),
            'full_amount' => $this->input->post('reimbursement_amount'),
            'payment_mode' => $this->input->post('reimbursement_payment_mode'),
            'regularity' => $this->input->post('reimbursement_regularity'),
            //0=request,1=approved,2=payed
            'status' => 0,
            'reimbursement_id' => $reimbursement_id,
            'request_date' => date("Y-m-d h:i:s"),
        );

        echo json_encode($this->M_reimbursement->SaveReimbursementRequest($data));
    }

}
