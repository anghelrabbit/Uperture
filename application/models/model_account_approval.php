<?php

class model_account_approval extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchAccounts() {
        $this->epay_db->select('*');
        $this->epay_db->where('is_approve', 0)
                ->from('tbl_masterlist');
    }

    public function FetchAllEmployess() {
        $this->epay_db->select('firstname, lastname, profileno')
                ->where('is_approve', 1)
                ->where('is_Payroll', 0)
                ->from('tbl_masterlist');
        $query = $this->epay_db->get();
        return $query->result_array();
    }

    function FetchAccountsTable() {
        $this->FetchAccounts();
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchAccountsFilter() {
        $this->FetchAccounts();
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    
    function ApproveAccount($data, $id) {
        $this->epay_db->where('profileno', $id);
        return $this->epay_db->update('tbl_masterlist', $data);
    }

    public function FetchProfileNoDetails($profileno) {

        $this->epay_db->select('firstname, lastname, email')
                ->from('tbl_masterlist')
                ->where('profileno', $profileno)
                ->where('served', 1);
        $query = $this->epay_db->get();
        return $query->result_array();
    }

}
