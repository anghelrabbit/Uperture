<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Emp201File extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_employee', 'M_employee');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $data["page_title"] = "201 File";
            $data['page'] = 'pages/menu/reports/201_file/201_file';

            $data["css"] = array
                (
                'assets/vendors/bower_components/bootstrap/dist/css/bootstrap.min.css',
                'assets/vendors/bower_components/font-awesome/css/font-awesome.min.css',
                'assets/vendors/bower_components/Ionicons/css/ionicons.min.css',
                'assets/vendors/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
                'assets/vendors/dist/css/AdminLTE.min.css',
                'assets/vendors/dist/css/skins/_all-skins.min.css',
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/myjs/utilities/structure.js',
                'assets/myjs/reports/201file/201_file.js'
            );

            $this->InspectUser('menu/pages/reports/201_file', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function EmployeeRecord() {

        $data = array();
        $where = array();
        $struct = (array) json_decode($this->input->post('structure'));
        $structure_string = $this->StructureChecker($struct, "=");

        $order = $this->input->post('order');
        $column_index = $order[0]['column'];
        $column_name = array();
        $column_name[0] = 'lastname';
        $column_name[2] = 'biometric';
        $column_name[3] = 'empid';
        $column_name[4] = 'lastname';
        $column_name[5] = 'datehired';
        $column_name[6] = 'birthdate';

        $column = $this->input->post('columns');
        if ($column[7]['search']['value'] != '') {
            $where['sex'] = $column[7]['search']['value'];
        }
        if ($column[4]['search']['value'] != '') {
            $empname = $column[4]['search']['value'];
            $explode_name = explode('-', $empname);
            if (count($explode_name) > 2) {
                $where[$explode_name[0]] = $explode_name[1];
                $where[$explode_name[2]] = $explode_name[3];
            } else {
                $where[$explode_name[0]] = $explode_name[1];
            }
        }
        if ($column[6]['search']['value'] != '' && $column[6]['search']['value'] != '0-0') {
            $service = $column[6]['search']['value'];
            $explode_service = explode('-', $service);
            if ($explode_service[0] != 0 && $explode_service[1] != 0 && $explode_service[1] < (int) date('m')) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . (intval($explode_service[1]) + 1) . " months"));
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . $explode_service[1] . " months"));

                //2018-02-06
            } else if ($explode_service[0] != 0) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-01-01";
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d');
            } else if ($explode_service[1] != 0) {
                $where['start_service'] = date('Y-m', strtotime("-" . (1 + intval($explode_service[1])) . " months")) . "-" . date('d', strtotime('+1 days'));
                $where['end_service'] = date('Y-m', strtotime("-" . $explode_service[1] . " months")) . "-" . date('d');
            }
        }
        $column_array = array($column_name[intval($column_index)], $order[0]['dir']);
        $emp = $this->M_employee->FetchEmployeeTable(1, $structure_string, $column_array, $this->CleanArray($where));
        foreach ($emp as $row) {
            $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $row->jobcode)));

            $dept = $this->M_employee->FetchEmployeeDept($row->depID);
            $refperson = $this->M_employee->FetchEmployeeReferalPerson($row->referral_person);



            $jobnem = (count($job) > 0) ? $job[0]->jobname : '';

            $sub_array = array();


//            $sub_array[] = '<button type="button" class="btn btn-success" onclick="retrieveAccount(' . "'" . $row->profileno . "'" . ')">View 201 File</button>';

            $sub_array[] = '';


            if ($jobnem != 'HR') {
                $sub_array[] = "<button class='btn btn-primary' onclick='retrieveAccount(" . '"' . $row->profileno . '"' . ")' ><i class='fas fa-user' style='font-size:18px'></i></button> " .
                        "<button class='btn' onclick='directToUserProfile(2, " . '"' . $row->profileno . '"' . ")'  style='background-color:#FF392E;color:white'><i class='fas fa-lock-open' style='font-size:18px'></i></button> ";
            } else {
                $sub_array[] = "<button class='btn btn-primary' onclick='retrieveAccount(2, " . '"' . $row->profileno . '"' . ")' ><i class='fas fa-user' style='font-size:18px'></i></button> ";
            }



            $sub_array[] = $row->lastname . ", " . $row->firstname . " " . $row->midname . ' <label style="font-size:13px"> (' . $jobnem . ')</label>';
            $sub_array[] = $row->username;
            $sub_array[] = $this->CalculateYearsOfService($row->datehired);
            $sub_array[] = (count($dept) > 0) ? $dept[0]->deptname : '';
            $sub_array[] = ($row->position_stat == 1) ? 'Team Lead' : 'Team Member';
            $sub_array[] = ($row->empstatus == 1) ? 'Full Time' : 'Part Time';
            $sub_array[] = date('m/d/Y', strtotime($row->datehired));
            $sub_array[] = (count($refperson) > 0) ? $refperson[0]->lastname . ", " . $refperson[0]->firstname : 'NONE';

            $sub_array[] = $row->contact;
            $sub_array[] = '';
            $data[] = $sub_array;
        }

        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($emp),
            "recordsFiltered" => $this->M_employee->EmployeeTableFilter($structure_string, $column_array, $this->CleanArray($where)),
            "data" => $data
        );
        echo json_encode($output);
    }

    public function FetchSpecificEmployee() {
        $emp = $this->M_employee->FetchEmployee(array('profileno' => $this->input->post('profileno')));
        $department = $this->FetchDepartmentAssigned($emp[0]);
        $emp[0]->password = $this->decrypt_pass($emp[0]->password);
        $emp[0]->department = (count($department) > 0) ? $department : array(0 => array('name' => 'Not Assigned'));
        $emp[0]->profile_pic = $this->ConvertImage($this->M_employee->FetchProfilePic(array('refno' => $emp[0]->profileno)));
        echo json_encode($emp[0]);
    }

    public function Employee201ExcelReport() {
        $spreadsheet = new Spreadsheet();
        $tab = 0;
        $where = array();
        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        if ($this->input->post('lastname') != '') {
            $where['lastname'] = $this->input->post('lastname');
        }
        if ($this->input->post('firstname') != '') {
            $where['firstname'] = $this->input->post('firstname');
        }
        if ($this->input->post('sex') != '') {
            $where['sex'] = $this->input->post('sex');
        }
        if ($this->input->post('years_service') != '') {
            $explode_service = explode('-', $this->input->post('years_service'));
            if ($explode_service[0] != 0 && $explode_service[1] != 0 && $explode_service[1] < (int) date('m')) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . (intval($explode_service[1]) + 1) . " months"));
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . $explode_service[1] . " months"));

                //2018-02-06
            } else if ($explode_service[0] != 0) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-01-01";
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d');
            } else if ($explode_service[1] != 0) {
                $where['start_service'] = date('Y-m', strtotime("-" . (1 + intval($explode_service[1])) . " months")) . "-" . date('d', strtotime('+1 days'));
                $where['end_service'] = date('Y-m', strtotime("-" . $explode_service[1] . " months")) . "-" . date('d');
            }
        }
        $employees = $this->CategoriesEmployees($this->M_employee->FetchEmployeesReport($where, $this->StructureChecker((array) json_decode($this->input->post('structure')), "=")));

        $rowindex = 9;
        foreach ($employees as $val) {
            $company = $this->M_structure->FetchStructureName(array('refno' => $val['company']), 'tbl_company');
            $company_first_word = explode(' ', $company[0]->name);
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($tab);
            $sheet = $spreadsheet->getActiveSheet($tab);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
            $spreadsheet->getActiveSheet()->setTitle($company_first_word[0]);
            $this->SetupExcelTable($sheet);
            $tab++;
            foreach ($val['employees'] as $row) {
                $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $row->jobcode)));
                $designation = array();
                if ($row->areID != null && $row->areID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->areID), 'tbl_areas');
                }
                if ($row->secID != null && $row->secID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->secID), 'tbl_sections');
                }
                if ($row->depID != null && $row->depID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->depID), 'tbl_departments');
                }
                if ($row->divID != null && $row->divID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->divID), 'tbl_division');
                }
                $designation = (count($designation) > 0) ? $designation[0]->name : 'Not Assigned';
                $job_name = (count($job) > 0) ? $job[0]->jobname : 'Not Assigned';
                $sheet->setCellValueByColumnAndRow(1, $rowindex, $row->empid); //empid
                $sheet->setCellValueByColumnAndRow(2, $rowindex, date('m/d/Y', strtotime($row->datehired))); //datehired
                $sheet->setCellValueByColumnAndRow(3, $rowindex, $row->lastname); //lastname
                $sheet->setCellValueByColumnAndRow(4, $rowindex, ','); //comma
                $sheet->setCellValueByColumnAndRow(5, $rowindex, $row->firstname . ' ' . $row->suffix); //given name
                $sheet->setCellValueByColumnAndRow(6, $rowindex, $row->midname); //middle name
                $sheet->setCellValueByColumnAndRow(7, $rowindex, $this->CalculateYearsOfService($row->datehired)); //tenureship
                $sheet->setCellValueByColumnAndRow(8, $rowindex, $row->empstatus); //empstatus
                $sheet->setCellValueByColumnAndRow(9, $rowindex, $job_name); //position
                $sheet->setCellValueByColumnAndRow(10, $rowindex, $designation); //designation
                $sheet->setCellValueByColumnAndRow(11, $rowindex, date('m/d/Y', strtotime($row->birthdate))); //bbirthdate
                $sheet->setCellValueByColumnAndRow(12, $rowindex, $row->age); //age
                $sheet->setCellValueByColumnAndRow(13, $rowindex, $row->sex); //gender
                $sheet->setCellValueByColumnAndRow(14, $rowindex, $row->address); //address
                $sheet->setCellValueByColumnAndRow(15, $rowindex, $row->contact); //contact
                $sheet->setCellValueByColumnAndRow(16, $rowindex, '');
                $sheet->setCellValueByColumnAndRow(17, $rowindex, '');
                $sheet->setCellValueByColumnAndRow(25, $rowindex, $row->sssno); //sss
                $sheet->setCellValueByColumnAndRow(26, $rowindex, $row->phicno); //phic
                $sheet->setCellValueByColumnAndRow(27, $rowindex, $row->hdmfno); //pagibig
                $sheet->setCellValueByColumnAndRow(28, $rowindex, $row->taxno); //tin

                $rowindex++;
            }
            $sheet->getStyle(
                    'A7:' . $sheet->getHighestColumn() . $sheet->getHighestRow()
            )->applyFromArray($border);
            $rowindex = 9;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'Employee 201';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file 
    }

    function SetupExcelTable($sheet) {

        $header_style = [
            'font' => [
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '2692D0'
                ]
            ],
        ];
        $sheet->getStyle('A7:AF8')->applyFromArray($header_style);
        $column_widths = array('A' => 15, 'B' => 15, 'C' => '15', 'D' => 1, 'E' => 15, 'F' => 15, 'G' => 22, 'H' => 24, 'I' => 30, 'J' => 40, 'K' => 15, 'L' => 10, 'M' => 15, 'N' => 40, 'O' => 25, 'P' => 25, 'Q' => 25, 'R' => 25);
        $column_widths2 = array('S' => 20, 'T' => 20, 'U' => 20, 'V' => 20, 'W' => 20, 'X' => 20, 'Y' => 20, 'Z' => 20, 'AA' => 20, 'AB' => 20, 'AC' => 20, 'AD' => 20, 'AE' => 20, 'AF' => 20);
        $header = array('EMP.NO.', 'DATE HIRED', 'LAST NAME', ',', 'GIVEN NAME', 'MIDDLE NAME',
            'TENURESHIP', 'EMPLOYMENT STATUS', 'POSITION', 'DESIGNATION', 'BIRTHDATE', 'AGE', 'GENDER', 'ADDRESS', 'CONTACT NO.',
            'CONTACT PERSON DURING EMERGENCY AND THEIR  CONTACT NO.', 'TRAININGS COMPLETED', 'LICENSE', 'INCENTIVE SET UP',
            'FREE MEAL', 'DTR SET UP', 'PRINCIPAL INSURANCE', 'DEPENDENT INSURANCE', 'OTHERS', 'SSS NO.', 'PHIC NO.', 'PAGIBIG NO.', 'TIN NO.', 'BONDS', 'EQUIPMENTS', 'DEVICES', 'OTHERS');
        $index = 0;
        foreach ($column_widths as $key => $val) {

            $sheet->setCellValue($key . '7', $header[$index]);
            $sheet->mergeCells($key . '7:' . $key . '8');
            $sheet->getStyle($key . '7')->getFont()->setSize(11);
            $sheet->getStyle($key . '7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($key . '7')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($key . '7')->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($key)->setWidth($val);
            $index++;
        }
        foreach ($column_widths2 as $key => $val) {
            $sheet->setCellValue($key . '8', $header[$index]);
            $sheet->getStyle($key . '8')->getFont()->setSize(11);
            $sheet->getStyle($key . '8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($key . '8')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($key . '8')->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($key)->setWidth($val);
            $index++;
        }
        $special_cell_style = [
            'font' => [
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '3ED03E'
                ]
            ]
        ];
        $special_cell = array('S7' => 'Other Benefits', 'Y7' => 'GOVERNMENT BENEFITS', 'AC7' => 'ACCOUNTABILITIES');
        foreach ($special_cell as $key => $val) {
            $sheet->setCellValue($key, $val);
            $sheet->getStyle($key)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($key)->applyFromArray($special_cell_style);
        }


        $sheet->mergeCells('S7:X7');
        $sheet->mergeCells('Y7:AB7');
        $sheet->mergeCells('AC7:AF7');

        $sheet->getStyle('S8:AF8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('S8:AF8')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S8:AF8')->getAlignment()->setWrapText(true);
    }

    public function Generate201PDF() {
        $this->load->library('pdf');
        $where = array();
        if ($this->input->post('lastname_pdf') != '') {
            $where['lastname'] = $this->input->post('lastname_pdf');
        }
        if ($this->input->post('firstname_pdf') != '') {
            $where['firstname'] = $this->input->post('firstname_pdf');
        }
        if ($this->input->post('sex_pdf') != '') {
            $where['sex'] = $this->input->post('sex_pdf');
        }
        if ($this->input->post('years_service_pdf') != '') {
            $explode_service = explode('-', $this->input->post('years_service_pdf'));
            if ($explode_service[0] != 0 && $explode_service[1] != 0 && $explode_service[1] < (int) date('m')) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . (intval($explode_service[1]) + 1) . " months"));
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d', strtotime("-" . $explode_service[1] . " months"));

                //2018-02-06
            } else if ($explode_service[0] != 0) {
                $where['start_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-01-01";
                $where['end_service'] = date('Y', strtotime('-' . $explode_service[0] . ' year')) . "-" . date('m-d');
            } else if ($explode_service[1] != 0) {
                $where['start_service'] = date('Y-m', strtotime("-" . (1 + intval($explode_service[1])) . " months")) . "-" . date('d', strtotime('+1 days'));
                $where['end_service'] = date('Y-m', strtotime("-" . $explode_service[1] . " months")) . "-" . date('d');
            }
        }
        $employees = $this->CategoriesEmployees($this->M_employee->FetchEmployeesReport($where, $this->StructureChecker((array) json_decode($this->input->post('structure_pdf')), "=")));

        $table_report = '';
        $thead = '<tr>
                        <td colspan="1" rowspan="2" style="border:solid; text-align: center;border-width: thin ">
                            <b>EMP.NO.</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid; text-align: center;border-width: thin ">
                            <b>DATE HIRED</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid; text-align: center;border-width: thin ">
                            <b>FULL NAME</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid; text-align: center;border-width: thin">
                            <b>TENURESHIP</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin;width: 70px">
                            <b>EMPLOYMENT STATUS</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin; width: 30px">
                            <b>POSITION</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin">
                            <b>DESIGNATION</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin">
                            <b>BIRTHDATE</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin">
                            <b>GENDER</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin">
                            <b>ADDRESS</b>
                        </td>
                        <td colspan="1" rowspan="2" style="border:solid;  text-align: center;border-width: thin">
                            <b>CONTACT NO.</b>
                        </td>
                   
                        <td colspan="4" style="border:solid;  text-align: center;border-width: thin">
                            <b>GOVERNMENT BENEFITS</b>
                        </td>
                    </tr>
                    <tr>
                     
                      <td colspan="1" style="border:solid;  text-align: center;border-width: thin">
                            <b>SSS NO.</b>
                        </td>
                      <td colspan="1" style="border:solid;  text-align: center;border-width: thin">
                            <b>PHIC NO.</b>
                        </td>
                      <td colspan="1" style="border:solid;  text-align: center;border-width: thin">
                            <b>PAGIBIG NO.</b>
                        </td>
                      <td colspan="1" style="border:solid;  text-align: center;border-width: thin">
                            <b>TIN NO.</b>
                        </td>
                     
</tr>';

        foreach ($employees as $val) {
            $company = $this->M_structure->FetchStructureName(array('refno' => $val['company']), 'tbl_company');
            $location = $this->M_structure->FetchStructureName(array('refno' => $val['location']), 'tbl_location');
            $total = count($val['employees']);
            $index = 0;
            $empoyee_desc = '';
            foreach ($val['employees'] as $row) {
                $emp_string = '';
                $job = $this->M_employee->FetchJobposition($this->CleanArray(array('jobcode' => $row->jobcode)));
                $designation = array();
                if ($row->areID != null && $row->areID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->areID), 'tbl_areas');
                }
                if ($row->secID != null && $row->secID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->secID), 'tbl_sections');
                }
                if ($row->depID != null && $row->depID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->depID), 'tbl_departments');
                }
                if ($row->divID != null && $row->divID != '') {
                    $designation = $this->M_structure->FetchStructureName(array('refno' => $row->divID), 'tbl_division');
                }
                $designation = (count($designation) > 0) ? $designation[0]->name : 'Not Assigned';

                $emp_string = $emp_string . '<tr>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px ">' . $row->empid . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px ">' . date('m/d/Y', strtotime($row->datehired)) . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px ">' . $row->lastname . ", " . $row->firstname . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $this->CalculateYearsOfService($row->datehired) . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->empstatus . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px;text-align:center">' . $job[0]->jobname . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $designation . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px">' . date('m/d/Y', strtotime($row->birthdate)) . '</td>';
                $emp_string = $emp_string . ' <td colspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->sex . '</td>';
                $emp_string = $emp_string . ' <td coslspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->address . '</td>';
                $emp_string = $emp_string . ' <td coslspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->contact . '</td>';
                $emp_string = $emp_string . ' <td coslspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->sssno . '</td>';
                $emp_string = $emp_string . ' <td coslspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->phicno . '</td>';
                $emp_string = $emp_string . ' <td coslspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->hdmfno . '</td>';
                $emp_string = $emp_string . ' <td coslspan="1" style="border:solid;border-width: thin;font-size: 11px">' . $row->taxno . '</td>';
                $emp_string = $emp_string . '</tr>';
                $empoyee_desc = $empoyee_desc . $emp_string;
                $index++;
                $total--;
                if ($index == 14 || $total == 0) {
                    $table_report = $table_report . $this->PDFReport($company[0]->name, $location[0]->name, 'All Departments', $empoyee_desc, $thead, 15);
                    $empoyee_desc = '';
                    $index = 0;
                }
            }
        }
        $mypdf = array();
        $mypdf['table'] = $table_report;
        $mypdf['title'] = "201 File Report";
//        $customPaper = array(0, 0, 1300, 850);
        $this->pdf->set_paper('Legal', 'landscape');
        $this->pdf->load_view('reports/201_file/201_file', $mypdf);
        $this->pdf->render();

        $canvas = $this->pdf->get_canvas();
        $font = Font_Metrics::get_font("helvetica", "bold");

        $this->pdf->stream('"' . $mypdf['title'] . ".pdf", array('Attachment' => 0));
    }

    public function PDFReport($company, $location, $department, $tbody, $thead, $colspan) {
        $table = '<table  frame="box" rules="cols" width="100%">' .
                '<caption style="font-size:12px">' . $company . '</caption>' .
                '<caption style="font-size:12px">' . $location . '</caption>' .
                '<tr><th style="border:solid;border-width: thin;" colspan="' . $colspan . '">' . $department . '</th></tr>' .
                $thead . $tbody .
                '</table><p style="page-break-after: always;">&nbsp;</p>';
        return $table;
    }

    public function RegisterAccount() {
        $data = array(
            'firstname' => $this->input->post('register_firstname'),
            'lastname' => $this->input->post('register_lastname'),
            'midname' => $this->input->post('register_midname'),
            'username' => $this->input->post('register_username'),
            'password' => $this->input->post('register_password'),
            'address' => $this->input->post('register_address'),
            'email' => $this->input->post('register_email'),
            'contact' => $this->input->post('register_contact_num'),
            'is_approve' => 0
        );

        echo json_encode($this->M_employee->SaveAccount($data));
    }

}
