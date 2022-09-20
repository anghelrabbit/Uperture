<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_holiday
 *
 * @author MIS
 */
class model_holiday extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    function FetchHolidays($where) {
        $this->epay_db->select('*')->from('holiday');
        if (isset($where['holiday_from'])) {
            $this->epay_db->where('datex >=', $where['holiday_from'])->where('datex <=', $where['holiday_to']);
        }
        if (isset($where['year'])) {
            $this->epay_db->where($where);
        }
        $this->epay_db->group_by('datex', 'ASC');
    }

    function HolidayTable($where) {
        $this->FetchHolidays($where);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function HolidayTableFilter($where) {
        $this->FetchHolidays($where);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    function FetchHoliday($data) {
        $this->epay_db->select('*')->from('holiday')->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchHolidayTypes() {
        $this->epay_db->select('refno, incentive')
                ->from('tbl_incentives')
                ->where('InGroup', 'HOLIDAY');
        $query = $this->epay_db->get();
        return $query->result();
    }

    function InsertUpdateHoliday($data, $id) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('holiday', $data);
        } else {
            return $this->epay_db->insert('holiday', $data);
        }
    }

    function RemoveHoliday($data) {
        return $this->epay_db->where($data)->delete('holiday');
    }

    function MergeHolidays($data) {
      return  $this->db->insert_batch('holiday', $data);
    }

}
