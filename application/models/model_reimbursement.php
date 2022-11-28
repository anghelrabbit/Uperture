<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_reimbursement
 *
 * @author angel bunny
 */
class model_reimbursement extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function SaveReimbursementRequest($data) {
        return $this->epay_db->insert('tbl_reimbursement', $data);
    }

    public function SaveReimbursementInstallmentRequest($data) {
        return $this->epay_db->insert('tbl_reimbursed_installment', $data);
    }

    public function FetchReimbursements($payment_mode) {
        $this->epay_db->select('*')
                ->from('tbl_reimbursement');
        $this->epay_db->where('status', 0)
                ->where('profileno', $this->session->userdata('profileno'));

        if ($payment_mode != 0) {
            $this->epay_db->where('payment_mode', $payment_mode);
        }
    }

    function FetchPendingReimbursementRequest($payment_mode) {
        $this->FetchReimbursements($payment_mode);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchPendingReimbursementRequestFilter($payment_mode) {
        $this->FetchReimbursements($payment_mode);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

}
