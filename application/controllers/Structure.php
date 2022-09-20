<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Structure
 *
 * @author MIS
 */
class Structure extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_structure', 'M_structure');
    }

    
public function StructureDepartments(){
    echo json_encode($this->session->userdata('structure'));
}
    public function SetupStructure() {
        $departments = $this->session->userdata('departments');
        $structure = array('tbl_company', 'tbl_location', 'tbl_division', 'tbl_departments', 'tbl_sections', 'tbl_areas');
        $options = array();
        $index = 0;
        $string_option = '';
        foreach ($departments as $row) {
            foreach ($row as $val) {
                $result = $this->M_structure->FetchStructure($structure[$index], $val);
                if (count($result) > 0) {
                    $string_option = $string_option . '<option value="' . $val . '">' . $result[0]->name . '</option>';
                }
            }
            $options[] = $string_option;
            $string_option= '';
            $index++;
        }
        echo json_encode($options);
    }

  

}
