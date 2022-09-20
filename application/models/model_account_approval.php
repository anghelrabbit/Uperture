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
        $this->epay_db->where('indx', $id);
        return $this->epay_db->update('tbl_masterlist', $data);
    }

}
