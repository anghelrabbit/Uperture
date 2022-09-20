<?php


class model_undertime extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    
     public function FetchApprovedUndertime($data, $datein, $dateout) {
        $this->epay_db->select('*')
                ->from('tbl_undertime use index (`idx_profileno_workscheds`)')
                ->where('worksched_in >=', $datein)->where('worksched_in <=', $dateout)
                ->where($data);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchPendingUndertime($where, $status, $empname, $datein, $dateout, $tab_category) {
        $whereString = $this->structure_query($where);
        $this->epay_db->select('*')
                ->from('tbl_undertime');
        if ($status == 0) {
            $this->epay_db->where('profileno !=', $this->session->userdata('profileno'));
        }
        if ($datein != '' && $datein != NULL) {
            $this->epay_db->where('date_requested >=', date('Y-m-d H:i:s', strtotime($datein . " 00:00:00")));
            $this->epay_db->where('date_requested <=', date('Y-m-d H:i:s', strtotime($dateout . " 23:59:59")));
        }

        if ($empname != '') {
            $this->epay_db->like('empname', $empname, 'after');
        }
        if ($tab_category == 0) {
            $this->epay_db->where('approved_status', $status);
            $this->epay_db->where("(`is_deleted` = 0 OR `is_deleted` IS NULL)");
        } else {
            $this->epay_db->where("approved_status <", 3)
                    ->where('is_deleted ', 1);
        }
        $this->epay_db->where($whereString);
    }

    function UndertimeTable($where, $status, $profileno, $datein, $dateout, $tab_category) {
        $this->FetchPendingUndertime($where, $status, $profileno, $datein, $dateout, $tab_category);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function UndertimeTableFilter($where, $status, $profileno, $datein, $dateout, $tab_category) {
        $this->FetchPendingUndertime($where, $status, $profileno, $datein, $dateout, $tab_category);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    public function FetchUndertime($data) {
        $this->epay_db->select('*')
                ->from('tbl_undertime')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function CheckExistingUndertime($worksched) {
        $this->epay_db->select('*')
                ->like('worksched_in', $worksched, 'after')
                ->from('tbl_undertime')
                ->where('profileno', $this->session->userdata('profileno'))
                ->where('approved_status !=', 2)
                ->where('hr_cancel_status !=', 1);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function SaveUpdateUndertime($data, $id) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('tbl_undertime', $data);
        } else {
            return $this->epay_db->insert('tbl_undertime', $data);
        }
    }

 

}
