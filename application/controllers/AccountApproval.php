<?php

class AccountApproval extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_account_approval', 'M_acc_approval');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Account Approval";
            $data['page'] = 'pages/menu/account_approval/account_approval';

            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
                'assets/vendors/css/ui-lightness.css',
                'assets/vendors/assets/owlcarousel/assets/owl.carousel.min.css',
                'assets/vendors/assets/owlcarousel/assets/owl.theme.default.min.css',
                'assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css',
                'assets/vendors/bower_components/sweetalert/sweetalert.css',
                'assets/vendors/plugins/timepicker/bootstrap-timepicker.min.css',
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/vendors/assets/owlcarousel/owl.carousel.js',
                'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
                'assets/vendors/plugins/timepicker/bootstrap-timepicker.min.js',
                'assets/vendors/momentjs/moment.js',
                'assets/myjs/account_approval/approve_accounts.js'
            );

            $this->InspectUser('menu/account_approval/account_approval', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function FetchApproval() {
        $result = $this->M_acc_approval->FetchAccountsTable();
        $data = array();
        foreach ($result as $val) {
            $sub_array = array();
//            $approve = ($val->is_approve == 1) ? 'selected' : '';
//            $disapprove = ($val->is_approve == 2) ? 'selected' : '';
//
//            $sub_array[] = '<select name="' . 'activate_' . $val->indx . '" onchange="activateAccount(this,' . $val->indx . ')" class="form-control"><option value="">Peding</option><option value="1" ' . $approve . '>Yes</option><option value="2" ' . $disapprove . '>No</option></select>';



            $fullname = $val->lastname . " " . $val->firstname;

            $sub_array[] = "<button class='btn' onclick='activateAccount(2, " . '"' . $val->profileno . '"' . ")'  style='background-color:#FF392E;color:white'><i class='fa fa-trash' style='font-size:18px'></i></button> "
                    . "<button class='btn' onclick='activateAccount(1, " . '"' . $val->profileno . '","' . $fullname . '","' . $val->email . '"' . ")'  style='background-color:#3ED03E;color:white'><i class='fa fa-check' style='font-size:18px'></i></button>";
            $sub_array[] = $fullname;
//            $sub_array[] = $val->address;
            $sub_array[] = $val->email;
            $sub_array[] = $val->contact;
            $data[] = $sub_array;
        }

        $output = array(
            "draw" => intval($this->input->post("draw")),
            "recordsFiltered" => $this->M_acc_approval->FetchAccountsFilter(),
            "recordsTotal" => count($result),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function FetchAllEmployees() {
        $result = array();
        $result = $this->M_acc_approval->FetchAllEmployess();
        echo json_encode($result);
    }

    public function DenyAccount() {
        $action = $this->input->post('action');
        $id = $this->input->post('id');
        echo json_encode($this->M_acc_approval->ApproveAccount(
                        array(
                            'is_approve' => $action,
                            'approved_by' => $this->session->userdata('profileno'),
                            'approve_date' => date("Y-m-d h:i:s"),
                        ), $id));
    }

    public function ApproveAccount() {
        $action = $this->input->post('action');
      
            $result = $this->M_acc_approval->ApproveAccount(
                    array(
                        'is_approve' => $action,
                        'datehired' => $this->input->post('acc_doh'),
                        'depID' => $this->input->post('acc_department'),
                        'position_stat' => $this->input->post('acc_position_stat'),
                        'empstatus' => $this->input->post('acc_job_status'),
                        'paysched' => $this->input->post('acc_pay_period'),
                        'referral_person' => $this->input->post('acc_referall_person'),
                        'resume' => $this->input->post('acc_resume'),
                        'approved_by' => $this->session->userdata('profileno'),
                        'approve_date' => date("Y-m-d h:i:s"),
                             
                    ), $this->input->post('txtprofileno'));
        
        echo json_encode($result);
    }

    public function FetchProfileNoDetails() {

        $result = array('status' => false);
        $profileno = $this->input->post('profileno');
        $result = $this->M_acc_approval->FetchProfileNoDetails($profileno);

        echo json_encode($result);
    }

}
