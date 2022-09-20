<?php

class EmployeeBreak extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_employee_break', 'M_emp_break');
        $this->load->model('model_dtr', 'M_dtr');
    }

    public function FetchExistingBreak() {
        $range_in = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 7);
        $range_out = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) + 60 * 60 * 11);
        $has_sched = $this->M_dtr->EmployeeHasSched(
                $this->session->userdata('profileno'),
                $range_in,
                $range_out,
                1
        );
        $sched_reference = 0;
        if (count($has_sched) > 0) {
            $sched_reference = $has_sched[0]->indx;
        }
        echo json_encode($sched_reference);
    }

    public function breakIn() {
        $timeleft = $this->input->post('timeleft');
        $range_in = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 7);
        $range_out = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) + 60 * 60 * 11);
        $sched_reference = 0;
        $has_sched = $this->M_dtr->EmployeeHasSched(
                $this->session->userdata('profileno'),
                $range_in,
                $range_out,
                1
        );
        $has_schedule = false;
        $has_punch_in = false;
        if (count($has_sched) > 0) {
            $has_schedule = true;
            $timein = array(
                'time_start' => date('Y-m-d H:i:s', strtotime($has_sched[0]->timein) - 60 * 60 * 7),
                'time_end' => date('Y-m-d H:i:s', strtotime($has_sched[0]->timeout) + 60 * 60 * 8));
            $has_punched = $this->M_dtr->FetchUserDTR('', $timein, 'timein', 'tbl_biometric_time_in', 'profileno_timein');
            if (count($has_punched) > 0) {
                $has_punch_in = true;
                $sched_reference = $has_sched[0]->indx;
                $data = array(
                    'profileno' => $this->session->userdata('profileno'),
                    'break_in' => date('Y-m-d H:i:s'),
                    'time_left' => $timeleft,
                    'schedule_reference' => $sched_reference,
                    'is_on' => 1
                );
                $this->M_emp_break->InsertUpdateBreak(0, $data);
            }
        }
        echo json_encode(array('sched_ref' => $sched_reference, 'has_schedule' => $has_schedule, 'has_punched' => $has_punch_in));
    }

    public function HasBreakIn() {
        $sched_ref = $this->input->post('sched_ref');
        $res = $this->M_emp_break->FetchExistingBreak($sched_ref);
        $mins = 0;
        $secs = 0;
        $has_break = false;
        if (count($res) > 0) {
            if ($res[0]->break_out == null && $res[0]->break_out == '') {
                $start_datetime = new DateTime(date('Y-m-d H:i:s'));
                $diff = $start_datetime->diff(new DateTime($res[0]->break_in));
                $converted = gmdate('i:s', ($diff->i * 60) + $diff->s);
                $explode_time = explode(':', $converted);
                $mins = $explode_time[0];
                $secs = $explode_time[1];
                $has_break = true;
            }
        }
        echo json_encode(array('mins' => $mins, 'secs' => $secs, 'has_break' => $has_break));
    }

    public function breakOut() {
        $sched_ref = $this->input->post('sched_ref');
        $timeleft = $this->input->post('timeleft');
        $res = $this->M_emp_break->FetchExistingBreak($sched_ref);
        if (count($res) > 0) {
            $data = array(
                'profileno' => $this->session->userdata('profileno'),
                'break_out' => date('Y-m-d H:i:s'),
                'time_left' => $timeleft,
                'is_on' => 2
            );
            $this->M_emp_break->InsertUpdateBreak($res[0]->id, $data);
        }
    }

    public function FetchLastBreak() {
        $sched_ref = $this->input->post('sched_ref');
        $res = $this->M_emp_break->FetchLastBreak($sched_ref);
        $mins = 0;
        $secs = 0;
        if (count($res) > 0) {
            $explode_time = explode(':', $res[0]->time_left);
            $mins = $explode_time[1];
            $secs = $explode_time[2];
        }
        echo json_encode(array('mins' => $mins, 'secs' => $secs, 'last' => json_encode($res)));
    }

}
