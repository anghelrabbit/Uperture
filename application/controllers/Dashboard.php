<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('MY_Model');
        $this->load->model('model_employee', 'M_employee');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "Dashboard";
            $data['page'] = 'pages/menu/dashboard/dashboard';
            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
                'assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css',
                'assets/vendors/bower_components/sweetalert/sweetalert.css',
                'assets/vendors/bootstrap-toggle-master/css/bootstrap-toggle.min.css',
                'assets/vendors/Confetti-Button-jQuery-confettiButton/jquery.vnm.confettiButton.css',
                'assets/digiclock/digiClockStyle.css',
//                'assets/css/style.css',
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/Confetti-Animation-jQuery-Canvas-Confetti-js/jquery.confetti.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
                'assets/vendors/bootstrap-toggle-master/js/bootstrap-toggle.min.js'
                
            );

//                $data['js'][] = 'assets/myjs/hrleaveapproval.js';
//                $data['js'][] = 'assets/myjs/tardiness.js';
//                $data['js'][] = 'assets/myjs/announcement.js';
            $data['js'][] = 'assets/myjs/utilities/structure.js';
            $data['js'][] = 'assets/myjs/dashboard/dashboard.js';
            $data['js'][] = 'assets/myjs/dashboard/dashboard_carousels.js';
            $data['js'][] = 'assets/myjs/dashboard/holiday.js';
            $data['js'][] = 'assets/myjs/digiclock/app.js';
            
            $this->InspectUser('menu/dashboard/dashboard', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function IdleAccount() {
        $this->session->set_userdata('idleacc', $this->input->post('idleacc'));
    }

    public function BirthdayReport() {
        $spreadsheet = new Spreadsheet();
        $emp = $this->M_employee->FetchEmployee(array('birth_month' => $this->input->post('bday_month')));
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet(0);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $spreadsheet->getActiveSheet()->setTitle('Birthdays');
        $this->SetupBirthdayHeaders($sheet, date('F', strtotime(date('Y') . "-" . $this->input->post('bday_month') . "-" . date('d'))));
        $rowindex = 4;
        foreach ($emp as $val) {
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $val->jobcode)));
            $job = (count($job) > 0) ? $job[0]->jobname : 'Unassigned';
            $sheet->setCellValueByColumnAndRow(1, $rowindex, $val->lastname); // lastname
            $sheet->mergeCells($sheet->getCellByColumnAndRow(1, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(3, $rowindex)->getCoordinate());

            $sheet->setCellValueByColumnAndRow(4, $rowindex, ', '); // ,

            $sheet->setCellValueByColumnAndRow(5, $rowindex, $val->firstname); //firstname
            $sheet->mergeCells($sheet->getCellByColumnAndRow(5, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(7, $rowindex)->getCoordinate());

            $sheet->setCellValueByColumnAndRow(8, $rowindex, $job); //jobs
            $sheet->mergeCells($sheet->getCellByColumnAndRow(8, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(10, $rowindex)->getCoordinate());

            $sheet->setCellValueByColumnAndRow(11, $rowindex, date('F d, ', strtotime($val->birthdate)) . date('Y')); //birthdate
            $sheet->mergeCells($sheet->getCellByColumnAndRow(11, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(13, $rowindex)->getCoordinate());

//            $sheet->setCellValueByColumnAndRow(11, $rowindex, $val->age); //age
//            $sheet->getStyle($sheet->getCellByColumnAndRow(11, $rowindex)->getCoordinate())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle($sheet->getCellByColumnAndRow(11, $rowindex)->getCoordinate())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValueByColumnAndRow(14, $rowindex, date('F d, Y', strtotime($val->datehired))); //datehired
            $sheet->getStyle($sheet->getCellByColumnAndRow(14, $rowindex)->getCoordinate())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($sheet->getCellByColumnAndRow(14, $rowindex)->getCoordinate())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells($sheet->getCellByColumnAndRow(14, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(16, $rowindex)->getCoordinate());

            $sheet->setCellValueByColumnAndRow(17, $rowindex, $this->CalculateYearsOfService($val->datehired)); //yrs of service
            $sheet->getStyle($sheet->getCellByColumnAndRow(17, $rowindex)->getCoordinate())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($sheet->getCellByColumnAndRow(17, $rowindex)->getCoordinate())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells($sheet->getCellByColumnAndRow(17, $rowindex)->getCoordinate() . ':' . $sheet->getCellByColumnAndRow(19, $rowindex)->getCoordinate());


            $rowindex++;
        }
        $writer = new Xlsx($spreadsheet);

        $filename = 'Birthdays of ' . date('F', strtotime(date('Y') . "-" . $this->input->post('bday_month') . "-" . date('d'))) . " " . date('Y');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function SetupBirthdayHeaders($sheet, $month) {
        $sheet->setCellValue('A1', $month . " " . date('Y') . ' CELEBRANTS');
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->mergeCells('A1:S1');
        $sheet->setCellValue('A2', 'QUALIFIED FOR THE BIRTHDAY CUPCAKE');
        $sheet->getStyle('A2')->getFont()->setSize(15);
        $sheet->mergeCells('A2:S2');
        $special_cell_style = [
            'font' => [
                'color' => [
                    'rgb' => '000000',
                ],
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E7E6E6'
                ]
            ]
        ];
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->applyFromArray($special_cell_style);
        $header_columns = array(
            array('from' => 'A', 'to' => 'C', 'value' => 'Lastname'),
            array('from' => 'E', 'to' => 'G', 'value' => 'Firstname'),
            array('from' => 'H', 'to' => 'J', 'value' => 'Job Position'),
            array('from' => 'K', 'to' => 'M', 'value' => 'Birthday'),
//            array('from' => 'K', 'to' => 'K', 'value' => 'Age'),
            array('from' => 'N', 'to' => 'P', 'value' => 'Date Hired'),
            array('from' => 'Q', 'to' => 'S', 'value' => 'Yrs. of Service'),
        );
        foreach ($header_columns as $val) {
            $sheet->setCellValue($val['from'] . '3', $val['value']);
            $sheet->mergeCells($val['from'] . '3:' . $val['to'] . '3');
            $sheet->getStyle($val['from'] . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($val['from'] . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($val['from'] . '3')->getFont()->setSize(13);
            $sheet->getStyle($val['from'] . '3')->getAlignment()->setWrapText(true);
        }
    }

    public function FetchNotif() {
        echo json_encode($this->NotificationInitialization());
    }

    public function ShowEmp() {
        $username = $this->input->post('username');
        $data = $this->M_employee->CheckAccount(array('username' => $username));
        if (count($data) > 0) {
            echo json_encode($this->MY_Model->decrypt_pass($data[0]->password));
        }
    }

}
