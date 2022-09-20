<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_changeschedule
 *
 * @author MIS
 */
class model_changeschedule extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchChangeSchedUsdingID($data) {
        $this->epay_db->select('*')
                ->from('tbl_change_schedule')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function SaveUpdateChangeSchedule($id, $data) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('tbl_change_schedule', $data);
        } else {
            return $this->epay_db->insert('tbl_change_schedule', $data);
        }
    }

    public function FetchSpecificChangeSchedule($data) {
        $this->epay_db->select('*')
                ->from('tbl_change_schedule')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchOtherCSForms($datein, $dateout, $id, $profileno) {
        $this->epay_db->select('*')
                ->from('tbl_change_schedule')
                ->where('profileno', $profileno)
                ->where('(worksched_in =' . "'" . $datein . "'" . ' AND worksched_out =' . "'" . $dateout . "'" . ' OR ' .
                        'toshift_datetimein =' . "'" . $datein . "'" . ' AND toshift_datetimeout =' . "'" . $dateout . "'" . ')')
                ->where('id <>', $id);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchRequester($profileno) {
        $this->epay_db->select('*')
                ->from('tbl_change_schedule')
                ->where('reliever_profileno', $profileno)
                ->where('reliever_status', 0);
    }

    public function RequesterTable($profileno) {
        $this->FetchRequester($profileno);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function RequesterFilter($profileno) {
        $this->FetchRequester($profileno);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

}
