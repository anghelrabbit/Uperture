<?php

class model_userwifi extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function insert_wifi($data) {
        return $this->epay_db->insert('tbl_wifipass', $data);
    }

    public function clear_wifipass() {
        $this->db->empty_table('tbl_wifipass');
    }

    public function HasWifiPassword($data) {
        $this->epay_db->select('*')
                ->where($data)
                ->from('tbl_wifipass');

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function get_available_password() {
        $this->epay_db->select('*')
                ->where('profileno', '')
                ->or_where('profileno IS NULL')
                ->from('tbl_wifipass');

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function update_emp_wifi($id, $data) {
        $this->epay_db->where('id', $id);
        return $this->epay_db->update('tbl_wifipass', $data);
    }

}
