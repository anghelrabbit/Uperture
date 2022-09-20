<?php


class model_structure extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function FetchCompanyLogo($comID) {
        $this->epay_db->select('*')
                ->from('tbl_company_logo')
                ->where($comID);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchStructureName($data, $table) {
        $this->epay_db->select('name')
                ->from($table)
                ->where($data);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchStructureUnder($category) {
        $this->epay_db->select('*')
                ->from('vw_under')
                ->where('refno', $category);
        $this->epay_db->group_by('undername');

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchStructure($table,$refno) {
        $this->epay_db->select('*')
                ->from($table)->where('status', 1)->where('refno',$refno);
        $query = $this->epay_db->get();
        return $query->result();
    }

}
