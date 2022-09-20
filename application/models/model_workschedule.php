<?php


class model_workschedule extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    function FetchWorkSchedule($data) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where('profileno', $data['profileno'])
                ->where('timein >=', $data['schedin'])
                ->where('timein <=', $data['schedout']);
    }

    public function FetchSpecificSchedule($date) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->like('timein', $date)
                ->where('profileno', $this->session->userdata('profileno'));
        $query = $this->epay_db->get();
        return $query->result();
    }

    function WorkScheduleTable($data) {
        $this->FetchWorkSchedule($data);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function WorkScheduleTableFiler($data) {
        $this->FetchWorkSchedule($data);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    public function UserWorkSchedule($datein, $dateout, $profileno) {
//        $datetoAdded = date('Y-m-d', strtotime($dateout . ' +1 day'));
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched use index (`profileno_timein_timeout_idx`)')
                ->where('profileno', $profileno)
                ->where('(timein >= "' . $datein." 00:00:00" . '" AND timein <= "' . $dateout." 23:59:59" . '")')
                ->order_by('timein', 'asc');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchRelieverSchedule($data) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where('profileno', $data['profileno'])
                ->where('timein >=', $data['schedin'])
                ->where('timein <=', $data['schedout']);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchSpecificUserSchedule($data) {
        $this->epay_db->select('*')
                ->from('tbl_masterlist_sched')
                ->where($data);

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function UpdateUserWorkSchedule($id, $data) {
        if ($id > 0) {
            $this->epay_db->where('indx', $id);
            return $this->epay_db->update('tbl_masterlist_sched', $data);
        } else {
            return $this->epay_db->insert('tbl_masterlist_sched', $data);
        }
    }
    
    public function RemoveSchedule($data){
           $this->epay_db->where($data);
        return $this->epay_db->delete('tbl_masterlist_sched');
    }

   

    public function FetchScheduleCode($data) {
        $this->epay_db->select('*')
                ->from('tbl_schedecode')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

     public function FetchEmployeeCurrentSchedule($profileno, $date){
        $this->epay_db->select('*')
                ->where('timein >=', $date . " " . "00:00:00")
                ->where('timein <=', $date . " " . "23:59:59")
                ->where('profileno', $profileno)
                ->from('tbl_masterlist_sched');

        $query = $this->epay_db->get();
        return $query->result();
    }
}
