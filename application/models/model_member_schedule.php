<?php

class model_member_schedule extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    function FetchMember($where) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist')
                ->where('status', 1)
                 ->where('is_payroll', 0)
                ->where('profileno <> ' . "'" . $this->session->userdata('profileno') . "'")
                ->where($where);
    }

    function FetchMemberTable($where) {
        $this->FetchMember($where);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchMemberFilter($where) {
        $this->FetchMember($where);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    function SaveUpdateMemberSchedule($data, $id) {
        if ($id > 0) {
            $this->epay_db->where('indx', $id);
            return $this->epay_db->update('tbl_masterlist_sched', $data);
        } else {
            return $this->epay_db->insert('tbl_masterlist_sched', $data);
        }
    }

}
