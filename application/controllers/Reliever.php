<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reliever
 *
 * @author MIS
 */
class Reliever extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_announcement', 'M_announcement');
        $this->load->model('model_employee', 'M_employee');
        $this->load->model('model_changeschedule', 'M_changeschedule');
        $this->load->model('model_workschedule', 'M_workschedule');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Approve as Reliever";
            $data['page'] = 'pages/menu/approve_as_reliever/reliever';
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
                'assets/myjs/utilities/payperiod.js',
                'assets/myjs/utilities/approving_buttons.js',
                'assets/myjs/approve_as_reliever/approve_as_reliever.js'
            );

            $this->InspectUser('menu/approve_as_reliever/reliever', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FetchMyRequester() {

        $result = $this->M_changeschedule->RequesterTable($this->session->userdata('profileno'));
        $data = array();

        foreach ($result as $val) {
            $sub_array = array();
            $category = '';
            if ($val->shiftchange == 1) {
                $category = 'Shift Change';
            }
            if ($val->straightduty == 1) {
                if ($category != '') {
                    $category = $category . ' / Straight Duty';
                } else {
                    $category = 'Straight Duty';
                }
            }
            if ($val->canceldayoff == 1) {
                if ($category != '') {
                    $category = $category . ' / Cancel Day-off';
                } else {
                    $category = 'Cancel Day-off';
                }
            }
            if ($val->changedayoff == 1) {
                if ($category != '') {
                    $category = $category . ' / Change Day-off';
                } else {
                    $category = 'Change Day-off';
                }
            }
            $sub_array[] = '<button class="btn " style="background-color:#3ED03E;color:white" onclick="changescheduleModal(' . "0,0,0" . ",1," . $val->id . ')">Check Details</button>';
            $sub_array[] = $category;
            $sub_array[] = $val->empname;
            $sub_array[] = date('F d, Y g:i A', strtotime($val->worksched_in)) . " - " . date('g:i A', strtotime($val->worksched_out));
            $sub_array[] = date('F d, Y g:i A', strtotime($val->toshift_datetimein)) . " - " . date('g:i A', strtotime($val->toshift_datetimeout));
            $sub_array[] = '';

            $data[] = $sub_array;
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($result),
            "recordsFiltered" => $this->M_changeschedule->RequesterFilter($this->session->userdata('profileno')),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchRelievers() {
        $structure = (array) json_decode($this->input->post('structure'));
        $column = $this->input->post('columns');
        $structure_string = $this->StructureChecker($structure, "=");
        $order = $this->input->post('order');
        $column_name = array(0 => 'lastname', 1 => 'lastname', 2 => 'lastname');
        if ($structure_string != '()') {
            $structure_string = "`profileno` != " . "'" . $this->session->userdata('profileno') . "'" . ' AND ' . $structure_string;
        } else {
            $structure_string = "`profileno` != " . "'" . $this->session->userdata('profileno') . "'";
        }
        $where = array();
        if ($column[2]['search']['value'] != '') {
            $empname = $column[2]['search']['value'];
            $explode_name = explode('/', $empname);
            if (count($explode_name) > 2) {
                $where[$explode_name[0]] = $explode_name[1];
                $where[$explode_name[2]] = $explode_name[3];
            } else {
                $where[$explode_name[0]] = $explode_name[1];
            }
        }
        $schedule = $this->input->post('sched');
        if (strtotime($column[3]['search']['value']) == true) {
            $schedule = date('Y-m-d', strtotime($column[3]['search']['value']));
        }
        $column_array = array($column_name[intval($order[0]['column'])], $order[0]['dir']);
        $emp = $this->M_employee->FetchEmployeeTable(1, $structure_string, $column_array, $this->CleanArray($where));
        $data = array();
        foreach ($emp as $val) {
            $is_checked = ($val->profileno == $column[0]['search']['value']) ? 'checked' : '';
            $sched_data = array('profileno' => $val->profileno, 'schedin' => $schedule . ' 00:00:00', 'schedout' => $schedule . ' 23:59:59');
            $sched = $this->M_workschedule->FetchRelieverSchedule($sched_data);
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $val->jobcode)));
            $jobname = '';
            if (count($job) > 0) {
                $jobname = '<label>(' . $job[0]->jobname . ')</label>';
            }
            $sub_array = array();
            $sub_array[] = json_encode(array(
                'name' => $val->lastname . ", " . $val->firstname,
                'prof' => $val->profileno,
                'datein' => (count($sched) > 0) ? date('Y-m-d', strtotime($sched[0]->timein)) : $schedule,
                'dateout' => (count($sched) > 0) ? date('Y-m-d', strtotime($sched[0]->timeout)) : $schedule,
                'timein' => (count($sched) > 0) ? date('H:i:s', strtotime($sched[0]->timein)) : 'Day Off',
                'timeout' => (count($sched) > 0) ? date('H:i:s', strtotime($sched[0]->timeout)) : 'Day Off',
                'is_checked' => $is_checked
            ));
            $sub_array[] = '<input type="checkbox" style="width:20px;height:20px;" name="' . $val->profileno . '"' . $is_checked . ' >';
            $sub_array[] = $val->lastname . ", " . $val->firstname . " " . $jobname;
            if (count($sched) > 0) {
                $sub_array[] = date('F d, Y', strtotime($sched[0]->timein)) . " "
                        . '<label style="letter-spacing:0.5px">(' . date('g:i A', strtotime($sched[0]->timein)) . " - " . date('g:i A', strtotime($sched[0]->timeout)) . ')</label>';
            } else {
                $sub_array[] = 'Day off';
            }
            $data[] = $sub_array;
        }
        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($emp),
            "recordsFiltered" => $this->M_employee->EmployeeTableFilter($structure_string, $column_array, $this->CleanArray($where)),
            "data" => $data,
        );
        echo json_encode($output);
    }

}
