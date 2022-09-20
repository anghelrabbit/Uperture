<?php

class model_schedule_management extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchEmployees($structure, $where) {

        $this->epay_db->select('*')
                ->from('tbl_masterlist');
        if (!isset($where['status'])) {
            $this->epay_db->where('status', 1);
        }
        if (count($where) > 0) {
            if (isset($where['lastname']) != '') {
                $this->epay_db->like('lastname', $where['lastname'], 'after');
            }
            if (isset($where['firstname']) != '') {
                $this->epay_db->like('firstname', $where['firstname'], 'after');
            }
            if (isset($where['sex'])) {
                $this->epay_db->where('sex', $where['sex']);
            }
            if (isset($where['start_service'])) {
                $this->epay_db->where('datehired >=', $where['start_service']);
                if ($where['end_service'] != 'less than 1 month') {
                    $this->epay_db->where('datehired <=', $where['end_service']);
                }
            }
            if (isset($where['for_regular'])) {
                $this->epay_db->where('regularization <>', '1901-01-1');
            }
            if (isset($where['expiry'])) {
                $this->epay_db->where('MONTH(expirydate) = ' . "'" . intval($where['expiry']) . "'");
            }
            if (isset($where['bday_month'])) {
                $this->epay_db->where('MONTH(birthdate) = ' . "'" . intval($where['bday_month']) . "'");
            }
        }

        if ($structure != '()') {
            $this->epay_db->where($structure);
        }
        $this->epay_db->order_by('lastname', 'ASC');
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchEmployeeSched($where,$sched_in,$sched_out) {

        $this->epay_db->select('COUNT(profileno) as count_sched,profileno')
                ->from('tbl_masterlist_sched');
        if ($where != '') {
            $this->epay_db->where($where);
        }
        $this->epay_db->where('timein >=',$sched_in)
        ->where('timein <=',$sched_out);
        $this->epay_db->group_by('profileno');
    }

    function FetchSpecificSched($where) {
        $this->epay_db->select('tbl_masterlist_sched.*,tbl_masterlist.lastname,tbl_masterlist.firstname,tbl_masterlist.midname')
                ->from('tbl_masterlist_sched');
        if (isset($where['datein'])) {
            $this->epay_db->where('tbl_masterlist_sched.timein >=', $where['datein']);
            $this->epay_db->where('tbl_masterlist_sched.timein <=', $where['dateout']);
        } else {
            $this->epay_db->where($where);
        }
         $this->epay_db->join('tbl_masterlist', 'tbl_masterlist_sched.profileno = tbl_masterlist.profileno', 'inner');
        $query = $this->epay_db->get();
        return $query->result();
    }

    function UpdateSchedRefIndex($data, $where) {
        $this->epay_db->where($where)
                ->update('tbl_masterlist_sched', $data);
    }

    function FetchEmployeeSchedTable($where,$sched_in,$sched_out) {
        $this->FetchEmployeeSched($where,$sched_in,$sched_out);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function EmployeeSchedTableFilter($where,$sched_in,$sched_out) {
        $this->FetchEmployeeSched($where,$sched_in,$sched_out);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    function BatchInsertSchedule($data) {
        return $this->epay_db->insert_batch('tbl_masterlist_sched', $data);
    }

    function BatchDeleteSchedule($data) {
        return $this->epay_db->query($data);
    }

    function RemoveSchedule($data) {
        $this->db->where($data);
        return $this->db->delete('tbl_masterlist_sched');
    }

}
