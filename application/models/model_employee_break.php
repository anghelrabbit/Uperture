<?php

class model_employee_break extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function InsertUpdateBreak($id, $data) {
        if ($id > 0) {
            return $this->epay_db->where('id', $id)
                            ->update('tbl_employee_break', $data);
        } else {
            $this->db->insert('tbl_employee_break', $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
    }


    public function FetchExistingBreak($schedref) {
        $this->epay_db->select('*')
                ->from('tbl_employee_break')
                ->where('schedule_reference', $schedref)
                ->where('is_on', 1);
        $query = $this->epay_db->get();
        return $query->result();
    }
    
    public function FetchLastBreak($schedref){
          $this->epay_db->select('*')
                ->from('tbl_employee_break')
                ->where('schedule_reference', $schedref)
                  ->order_by('break_in','DESC');
                   $query = $this->epay_db->get();
        return $query->result();
    }
    

}
