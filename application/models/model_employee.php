<?php

class model_employee extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    public function SaveAccount($data) {
        return $this->epay_db->insert('tbl_masterlist', $data);
    }

    public function CheckAccount($data) {

        $this->epay_db->select('*')
                ->from('tbl_masterlist')
                ->where('status', 1)
                ->where($data);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function CheckUserRole($data) {
        $this->epay_db->select("*")
                ->from('vw_incharge')
                ->where($data);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchJobposition($data) {
        $this->epay_db->select("*")
                ->from('tbl_jobs')
                ->where($data);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchEmployeesReport($where, $structure) {
        $this->epay_db->select('*')
                ->where('status', 1)
                ->from('tbl_masterlist');
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
                $this->epay_db->where('datehired <=', $where['end_service']);
            }
        }
        if ($structure != '()') {
            $this->epay_db->where($structure);
        }
        $this->epay_db->order_by('lastname', 'ASC');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchEmployees($structure, $column, $where) {
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
                $this->epay_db->where('datehired <=', $where['end_service']);
            }
        }

        if ($column != '') {
            $this->epay_db->order_by($column[0], $column[1]);
        }
        if ($structure != '()') {
            $this->epay_db->where($structure);
        }
    }

    function FetchEmployeeTable($category, $structure, $column, $where) {
        $this->FetchEmployees($structure, $column, $where);
        if ($category == 1) {
            if ($this->input->post("length") != -1) {
                $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
            }
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function EmployeeTableFilter($structure, $column, $where) {
        $this->FetchEmployees($structure, $column, $where);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    public function FetchEmployeeService($data) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist')
                ->where('status', 1);
        if ($data != '') {
            $this->epay_db->like('datehired', $data)
                    ->order_by('datehired', 'ASC');
        }
        $this->epay_db->order_by('lastname', 'ASC');

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchEmployee($data) {
        $this->epay_db->select('*')
                ->where('status', 1);
        if (isset($data['birth_month'])) {
            $this->epay_db->where('MONTH(birthdate) =', $data['birth_month']);
            $this->epay_db->order_by('DAY(birthdate) ', 'ASC');
        } else if (isset($data['birthdate'])) {
            $this->epay_db->like('birthdate', $data['birthdate'])->order_by('birthdate', 'ASC');
        } else if (isset($data['month'])) {
            $this->epay_db->where('MONTH(datehired) <=', $data['month']);
            $this->epay_db->where('YEAR(datehired) <=', $data['year']);
            $this->epay_db->order_by('datehired', 'DESC');
        } else {
            $this->epay_db->where($data);
        }
        $this->epay_db->from('tbl_masterlist');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function UpdateAccountPassword($data) {
        $this->epay_db->where('profileno', $this->security->xss_clean($this->session->userdata('profileno')));
        return $this->epay_db->update('tbl_masterlist', $data);
    }

    public function FetchProfilePic($data) {
        $this->epay_db->from('tbl_masterlist_pic');
        $this->epay_db->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchMemberSchedules() {
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where('profileno ', '07102019220608562PWD')
                ->or_where('profileno ', '07102019221344562PWD')
                ->order_by('timein', 'DESC');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function RegisterAccount($data) {
        return $this->epay_db->insert('tbl_masterlist', $data);
    }

    public function FetchEmployeeSchedule($id) {
        $this->epay_db->select('*')->from('tbl_masterlist_sched');
        $this->epay_db->where('indx', $id);
        $query = $this->epay_db->get();
        return $query->result();
    }

}
