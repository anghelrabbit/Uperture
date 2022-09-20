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
            
            
            
            
            
            $sub_array[] = '<button class="btn" onclick="activateAccount(2, '.$val->indx.')"  style="background-color:#FF392E;color:white"><i class="fa fa-trash" style="font-size:18px"></i></button> '
                    . '<button class="btn" onclick="activateAccount(1, '.$val->indx.')"  style="background-color:#3ED03E;color:white"><i class="fa fa-check" style="font-size:18px"></i></button>';
            $sub_array[] = $val->lastname . " " . $val->firstname;
            $sub_array[] = $val->address;
            $sub_array[] = $val->contact;
            $sub_array[] = $val->email;
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

    public function ApproveAccount() {
        $action = $this->input->post('action');
        $id = $this->input->post('id');
        echo json_encode($this->M_acc_approval->ApproveAccount(array('is_approve' => $action), $id));
    }

}
