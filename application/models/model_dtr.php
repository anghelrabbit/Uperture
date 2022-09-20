<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_dtr
 *
 * @author MIS
 */
class model_dtr extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchUserDTR($profileno, $data, $time, $table, $index) {
//          $where = '(' . $time . '>=' . $data['time_start'] . ' AND ' . $time . ' <=' . $data['time_end'] . ')';

        $this->epay_db->select('*')
                ->from($table . ' use index (`' . $index . '`)');
        if ($profileno != '') {
            $this->epay_db->where('profileno', $profileno);
        } else {
            $this->epay_db->where('profileno', $this->session->userdata('profileno'));
        }
        $this->epay_db->order_by($time, "asc");
        $this->epay_db->where($time . ' >=', $data['time_start'])
                ->where($time . ' <=', $data['time_end']);
        $query = $this->epay_db->get();
        return $query->result();
    }

    function DTR_TableFilter($profileno, $batchcode, $data) {
        $this->FetchUserDTR($profileno, $batchcode, $data);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    function DTR_Table($profileno, $batchcode) {
        $this->FetchUserDTR($profileno, $batchcode);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchTimeOut($profileno, $batchcode) {
        $this->epay_db->select('*')
                ->from('tbl_payroll_sched_out')
                ->order_by("timeout", "asc")
                ->where('batchcode', $batchcode)
                ->where('profileno', $profileno);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function fetch_batchcodes() {
        $this->epay_db->select('*')
                ->from('tbl_payroll_batches')
                ->where('recover', 0);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function UserWorkSchedule($datein, $dateout) {
        $datetoAdded = date('Y-m-d', strtotime($dateout . ' +1 day'));
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where('(timein >= "' . $datein . '" AND timein <= "' . $datetoAdded . '")')
                ->order_by('timein', 'asc')
                ->where('profileno', $this->session->userdata('profileno'));
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchDTRLog($date, $profileno) {
        $this->epay_db->select('*')
                ->where('timein >=', $date . " " . "00:00:00")
                ->where('timein <=', $date . " " . "23:59:59")
                ->from('tbl_biometric_time_in')
                ->group_by('profileno')
                ->order_by('timein', 'ASC');
        if ($profileno != '') {
            $this->epay_db->where($profileno);
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function EmployeeHasSched($profileno, $range_in, $range_out, $is_timein) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where('profileno', $profileno);
        if ($is_timein == 1) {
            $this->epay_db->where('timein >=', $range_in)
                    ->where('timein <=', $range_out);
        } else {
            $this->epay_db->where('timeout >=', $range_in)
                    ->where('timeout <=', $range_out);
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function InsertBiometric($data, $is_timein) {
        if ($is_timein == 1) {
            return $this->epay_db->insert('tbl_biometric_time_in', $data);
        } else {
            return $this->epay_db->insert('tbl_biometric_time_out', $data);
        }
    }

    public function FetchEmpPunch($profileno, $range_in, $range_out, $is_timein) {
        $this->epay_db->select('*');
        if ($is_timein == 1) {
            $this->epay_db->from('tbl_biometric_time_in')
                    ->where('profileno', $profileno);
            $this->epay_db->where('timein >=', $range_in)
                    ->where('timein <=', $range_out);
        } else {
            $this->epay_db->from('tbl_biometric_time_outs')
                    ->where('profileno', $profileno);
            $this->epay_db->where('timeout >=', $range_in)
                    ->where('timeout <=', $range_out);
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

}
