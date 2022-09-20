<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_leavecredits
 *
 * @author MIS
 */
class model_leavecredits extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchEmployeeLeaveCredits($where) {
//                ->join('tbl_employee_leavecredits', 'tbl_masterlist.profileno = tbl_employee_leavecredits.profileno WHERE tbl_employee_leavecredits.year =' . "'" . date('Y') . "'");
        $this->epay_db->select('*')->from('tbl_employee_leavecredits')
                ->where($where);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchSpecificCredits($data) {
        $this->epay_db->select('*')
                ->where($data)
                ->from('tbl_employee_leavecredits');
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchLeaveTypeTable() {
        $this->epay_db->select('*')
                ->from('tbl_leave_types');
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $this->epay_db->order_by('updated_date', "ASC");
        $query = $this->epay_db->get();
        return $query->result();
    }

    function LeaveTypeTableFilter() {
        $this->epay_db->select('*')
                ->from('tbl_leave_types');
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    function SaveLeaveType($data, $id) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('tbl_leave_types', $data);
        } else {
            return $this->epay_db->insert('tbl_leave_types', $data);
        }
    }

    function FetchSpecificLeaveType($data) {
        $this->epay_db->select('')
                ->from('tbl_leave_types')->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    function UpdateLeaveCredits($where, $data) {
        if (count($where) > 0) {
            $this->epay_db->where($where);
            return $this->epay_db->update('tbl_employee_leavecredits', $data);
        } else {
            return $this->epay_db->insert('tbl_employee_leavecredits', $data);
        }
    }

    function RemoveLeaveType($id) {
        $this->epay_db->where('leavetype', $id)->delete('tbl_employee_leavecredits');
        return $this->epay_db->where('id', $id)->delete('tbl_leave_types');
    }

}
