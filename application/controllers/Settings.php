<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author MIS
 */
class Settings extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->Model('MY_Model');
    }

    public function UploadTextFile() {
        $myfile = fopen($_FILES['file']['tmp_name'], 'r');
        $result = false;
        while (!feof($myfile)) {
            fgets($myfile);
            $data = array();
            $data['profileno'] = '';
            $data['wifi_pass'] = fgets($myfile);
            $data['date_updated'] = date('Y-m-d H:i:s');
            $data['updator'] = $this->session->userdata('profileno');
            $this->wifi->insert_wifi($this->CleanArray($data));
            $result = true;
        }
        echo json_encode($result);
    }
    
    public function ClearWifi(){
        $this->wifi->clear_wifipass();
    }
    
    
    public function DecryptWifiPass(){
       echo json_encode($this->MY_Model->decrypt_pass($this->input->post('wifipass')));
    }
}
