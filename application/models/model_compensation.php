<?php

class model_compensation extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchPayrollBatches($data) {
        $this->epay_db->select('payschedyear,payschedtype,payschedmonth, batchcode,payschedfrom,payschedto')
                ->from('tbl_payroll_batches use index (`idx_payschedyear`)')
                ->where($data)
                ->order_by('payschedfrom', 'asc');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchEmployeeAdjustments($data) {
        return $this->epay_db->query($data)->result();
    }

    public function FetchEmployeesInPayroll($where) {
        $this->epay_db->select('*')
                ->from('tbl_payroll');
        if ($where != '()') {
            $this->epay_db->where($where);
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchBatchPayroll($data) {
        return $this->epay_db->query($data)->result();
    }

}
