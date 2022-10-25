<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model_reimbursement
 *
 * @author angel bunny
 */
class model_reimbursement extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }  
    
     public function SaveReimbursementRequest($data) {
        return $this->epay_db->insert('tbl_reimbursement', $data);
    }
    
    
}
