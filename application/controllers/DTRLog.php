<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DTRLog
 *
 * @author MIS
 */
class DTRLog extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('MY_Model');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_dtr', 'M_dtr');
        $this->load->model('model_workschedule', 'M_workschedule');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "DTR Log";
            $data['page'] = 'pages/menu/dtr_log/dtr_log';
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
                'assets/vendors/bootstrap-toggle-master/css/bootstrap-toggle.min.css'
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
                'assets/vendors/bootstrap-toggle-master/js/bootstrap-toggle.min.js',
                'assets/myjs/utilities/structure.js',
                'assets/myjs/dtrlog/dtr_log.js',
            );

            $this->InspectUser('menu/dtr_log/dtr_log', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FetchDTRLog() {
           $struct = (array) json_decode($this->input->post('structure'));
        $structure_string = $this->StructureChecker($struct, "=");
         $emp = $this->M_employee->FetchEmployeeTable(0, $structure_string, '', array());
         
         $profileno_query = $this->QueryProfileno($emp);
        $result = $this->M_dtr->FetchDTRLog(date('Y-m-d', strtotime($this->input->post('schedule'))),$profileno_query);
        $data = array();
        foreach ($result as $val) {
            $sched = $this->M_workschedule->FetchEmployeeCurrentSchedule($val->profileno, date('Y-m-d', strtotime($this->input->post('schedule'))));

            if (count($sched) > 0) {
                $data_out = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($sched[0]->timeout) - 60 * 60 * 7),
                    'time_end' => date('Y-m-d H:i:s', strtotime($sched[0]->timeout) + 60 * 60 * 11));
                $timeout = $this->M_dtr->FetchUserDTR($val->profileno, $data_out, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
            } else {
                $data_out = array(
                    'time_start' => date('Y-m-d H:i:s', strtotime($val->timein) - 60 * 60 * 9),
                    'time_end' => date('Y-m-d H:i:s', strtotime($val->timein) + 60 * 60 * 11));
                $timeout = $this->M_dtr->FetchUserDTR($val->profileno, $data_out, 'timeout', 'tbl_biometric_time_out', 'profileno_timeout');
            }
            $sub_array = array();
            $sub_array[] = $val->fullname;
            if (count($sched) > 0) {
                $sub_array[] = date('F d,Y', strtotime($sched[0]->timein)) . " " . $this->ConvertTo12Format($sched[0]->timein);
                $sub_array[] = $this->ConvertTo12Format($val->timein);
                $sub_array[] = date('F d,Y', strtotime($sched[0]->timeout)) . " " . $this->ConvertTo12Format($sched[0]->timeout);
                if (count($timeout) > 0) {
                    $sub_array[] = $this->ConvertTo12Format($timeout[0]->timeout);
                } else {
                    $sub_array[] = 'Missing Out';
                }
            } else {
                $sub_array[] = date('F d, Y', strtotime($this->input->post('schedule')));
                $sub_array[] ='<label >'. $this->ConvertTo12Format($val->timein).'</label>' . '<br><span>(Day Off)</span>';
                $sub_array[] = date('F d, Y', strtotime($this->input->post('schedule')));
                if (count($timeout) > 0) {
                    $sub_array[] = '<label>'.$this->ConvertTo12Format($timeout[0]->timeout).'</label>'. '<br> <span>(Day Off)</span>';
                } else {
                    $sub_array[] = 'Missing Out';
                }
            }
            $data[] = $sub_array;
//            if ($emp != $val->profileno) {
//                $emp = $val->profileno;
//            }
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "data" => $data
        );

        echo json_encode($output);
    }

}
