<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_leave
 *
 * @author MIS
 */
class model_leave extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchLeaveUsdingID($data) {
        $this->epay_db->select('*')
                ->from('tableleave')
                ->where($data);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function GetWorkDays($datein, $dateto, $profileno) {
        $datetoAdded = date('Y-m-d', strtotime($dateto . ' +1 day'));
        if ($profileno == '') {
            $profileno = $this->security->xss_clean($this->session->userdata('profileno'));
        }
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where('profileno', $profileno)
                ->where('(timein >= "' . $datein . '" AND timein <= "' . $datetoAdded . '")');
        $query = $this->epay_db->get();
        return $query->result();
    }

 

    public function SaveUpdateLeave($id, $data) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('tableleave', $data);
        } else {
            return $this->epay_db->insert('tableleave', $data);
        }
    }

    public function SaveLeaveExclusion($data) {
        return $this->epay_db->insert('tbl_leave_exclusion', $data);
    }

    public function RemoveSpecificLeave($id) {
        $this->epay_db->where('ID', $id);
        return $this->epay_db->delete('tableleave');
    }

    public function RemoveLeaveCredits($where) {
        $query = $this->epay_db->where($where)
                ->delete('tbl_employee_leavecredits');
        return $query;
    }

    public function CheckApprovedLeave($profileno, $leavedays) {
        $this->epay_db->select('*')
                ->from('tableleave')
                ->where('profileno', $profileno)
                ->where($leavedays)
                ->where('hr_cancel_status <>', 1)
                ->where('approved_status', 1);
        $query = $this->epay_db->get();
        return $query->result();
    }
       public function CheckExistingLeave($worksched) {
        $this->epay_db->select('*')
                ->like('worksched_in', $worksched, 'after')
                ->from('tbl_undertime')
                ->where('profileno', $this->session->userdata('profileno'))
                ->where('approved_status !=', 2)
                ->where('hr_cancel_status !=', 1);

        $query = $this->epay_db->get();
        return $query->result();
    }
   public function FiledLeaveForms($profileno, $leavedays, $id) {
        $this->epay_db->select('*')
                ->from('tableleave')
                ->where('profileno', $profileno)
                ->where('approved_status <', 2)
                ->where('hr_cancel_status !=', 1)
                ->where('ID !=', $id)
                ->where($leavedays)
                ->order_by('date_requested', 'asc');

        $query = $this->epay_db->get();
        return $query->result();
    }
}
