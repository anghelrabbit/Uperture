<?php


class Holiday extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('model_holiday', 'M_holiday');
        $this->load->model('MY_Model');
    }

    function FetchHolidays() {
        $data = array();
        $where = array();
        $holiday_year = $this->input->post('holiday_year');
        $holiday_month = $this->input->post('holiday_month');
        $column = $this->input->post('columns');
        $holiday_search = $column[0]['search']['value'];
        $explode_search = explode('/', $holiday_search);
        if (count($explode_search) > 1) {
            if ($explode_search[0] != '') {
                $holiday_month = $explode_search[0];
            }
            if ($explode_search[1] != '') {
                if ($explode_search[1] > 999) {
                    $holiday_year = $explode_search[1];
                }
            }
        }

        if ($holiday_year != '' && $holiday_month != '') {
            $where['holiday_from'] = $holiday_year . '-' . $holiday_month . '-01';
            $where['holiday_to'] = $holiday_year . '-' . $holiday_month . '-' . date("t", strtotime($where['holiday_from']));
        } else if ($holiday_year != '') {
            $where['year'] = $holiday_year;
        }

        $holidays = $this->M_holiday->HolidayTable($where);
        foreach ($holidays as $val) {
            $sub_array = array();
            $addon_btn = '';
            if ($this->session->userdata('user') == 0 && $this->session->userdata('hr') == 1) {
                $addon_btn = '<span onclick="editHoliday(' . $val->ID . ')" class="btn" style="background-color:#FFB347;color:white"><i class="glyphicon glyphicon-pencil"></i></span>&nbsp;'
                        . '<span onclick="removeHoliday(' . $val->ID . ')"  class="btn" style="background-color:#FF392E;color:white"><i class="glyphicon glyphicon-trash"></i></span>';
            }
            $sub_array[] = '<span style="font-weight:bold">' . $val->description . '</span>'
                    . '<span class="pull-right" style="font-size:12px;letter-spacing:0.5px;font-weight:bold">' . date('F d, Y', strtotime($val->datex)) . " (" . date('l', strtotime($val->datex)) . ")" . '</span>'
                    . '<br><span style="font-size:12px">' . $val->type . '</span>'
                    . '<div class="pull-right">'
                    . $addon_btn
                    . '</div>';
            $data[] = $sub_array;
        }

        $output = array(
            "draw" => intval($this->input->post("draw")),
            "recordsFiltered" => $this->M_holiday->HolidayTableFilter($where),
            "recordsTotal" => count($holidays),
            "data" => $data
        );

        echo json_encode($output);
    }

    function FetchHolidayTypes() {
        $holidays = $this->M_holiday->FetchHolidayTypes();
        echo json_encode($holidays);
    }

    function AddHoliday() {
        $week_name = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Friday');
        $this->FormRestrictions('holiday');

        $result = $this->ValidateErrorsSample($_POST);
        if (strtotime($this->input->post('holiday_date')) == false) {
            $result['success'] = false;
            $result['messages']['holiday_date'] = 'Invalid Date';
        }
        if ($result['success'] == true) {
            $data = array();
            $id = $this->input->post('id');
            $split_type = explode(',', $this->input->post('holiday_type'));
            $data['datex'] = date('Y-m-d', strtotime($this->input->post('holiday_date')));
            $data['week'] = $week_name[date('w', strtotime($data['datex']))];
            $data['description'] = $this->input->post('holiday_name');
            $data['refno'] = $split_type[0];
            $data['type'] = $split_type[1];
            $data['year'] = date('Y', strtotime($data['datex']));
            $data['status'] = 'ACTIVE';
            $data['updated'] = date('Y-m-d H:i:s');
            $data['updatedby'] = $this->session->userdata('profileno');

            $this->M_holiday->InsertUpdateHoliday($data, $id);
        }
        echo json_encode($result);
    }

    public function FetchHoliday() {
        $holiday = $this->M_holiday->FetchHoliday(array('ID' => $this->input->post('id')));
        if (count($holiday) > 0) {
            echo json_encode($holiday[0]);
        } else {
            echo json_encode(false);
        }
    }

    public function RemoveHoliday() {
        echo json_encode($this->M_holiday->RemoveHoliday(array('id' => $this->input->post('id'))));
    }

    public function MergeHolidays() {
        $yearin = $this->input->post('yearin');
        $yearof = $this->input->post('yearof');

        $this->M_holiday->RemoveHoliday(array('year' => $yearof));
        $result = $this->M_holiday->FetchHoliday(array('year' => $yearin));
        $data = array();
        foreach ($result as $val) {
            $sub_array = array(
                'datex' => date('Y-m-d', strtotime($yearof . "-" . date('m-d', strtotime($val->datex)))),
                'description' => $val->description,
                'type' => $val->type,
                'year' => $yearof,
                'status' => 'ACTIVE',
                'updatedby' => $this->session->userdata('profileno'),
                'updated' => date('Y-m-d H:i:s'),
                'refno' => $val->refno
            );
            $data[] = $sub_array;
        }
        echo json_encode($this->M_holiday->MergeHolidays($data));
    }

}
