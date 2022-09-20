<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_overtime
 *
 * @author MIS
 */
class model_overtime extends MY_Model {

    public function __construct() {
        parent::__construct();

        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchOvertimeUsdingID($data) {
        $this->epay_db->select('*')
                ->from('tbl_overtime')
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchOvertimeTypes() {
        $this->epay_db->select('*')
                ->from('tbl_incentives')
                ->where('InGroup', 'OVERTIME');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchOvertimeTypeReference($data, $sql_syntax) {
        $this->epay_db->select('*')
                ->from('tbl_incentives');
        if ($sql_syntax == 'like') {
            $this->epay_db->like($data);
        } else {
            $this->epay_db->where($data);
        }
        $this->epay_db->where('InGroup', 'OVERTIME');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function SaveUpdateOvertime($data, $id) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('tbl_overtime', $data);
        } else {
            return $this->epay_db->insert('tbl_overtime', $data);
        }
    }

}
