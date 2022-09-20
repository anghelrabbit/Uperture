<?php

class model_payslip extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchPayslips($data) {
        $this->epay_db->select('*')
                ->from('tbl_payroll')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchBatchcode($data) {
        $this->epay_db->select('*')
                ->from('tbl_payroll_batches')
                ->where($data)
                ->where('recover', 0)
                ->or_where('category', 'ALL')
                ->order_by('payschedfrom', 'DESC');
    }
    
    public function SpecificBatchcode($data){
          $this->epay_db->select('*')
                ->from('tbl_payroll_batches')
                ->where($data)
                ->where('recover', 0);
           $query = $this->epay_db->get();
        return $query->result();
    }

    function PayslipDataTable($data) {
        $this->FetchBatchcode($data);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function PayslipDataTableFiler($data) {
        $this->FetchBatchcode($data);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

  public function FetchPayrollAdjustments( $data, $index) {
        $this->epay_db->select('*')
                ->from('tbl_payroll_adjustments use index (`'.$index.'`)')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchPayroll($data) {
        $this->epay_db->select('*')
                ->from('tbl_payroll')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function SaveUpdatePayslip($data, $where) {
        $this->epay_db->where($where);
        return $this->epay_db->update('tbl_payroll', $data);
    }

}
