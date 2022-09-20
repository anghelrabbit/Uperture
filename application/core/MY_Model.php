<?php

class MY_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

   

    public function get_current_date() {
        $now = new DateTime();
        return $now->format("Y-m-d h:i:s");
    }

    public function format_year() {
        $now = new DateTime();
        return $now->format('Y');
    }

    public function fetch_structure_refno($refno) {
        $this->epay_db->select('refno')
                ->from('tbl_under')
                ->where('under', $refno);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function fetch_structure_under($refno) {
        $this->epay_db->select('under')
                ->from('tbl_under')
                ->where('refno', $refno);
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function structure_query($where) {
        $whereString = " (";
        $isFirst = 0;
        $first = 0;


        foreach ($where as $val) {

            $whereclause = array_keys($val);
            if ($isFirst == 0) {
                foreach ($whereclause as $row) {
                    if ($first == 0) {
                        $whereString = $whereString . "`" . $row . "`" . '=' . "'" . $val[$row] . "' ";
                        $first = 1;
                    } else {
                        $whereString = $whereString . "AND `" . $row . "`" . '=' . "'" . $val[$row] . "' ";
                    }
                }
                $isFirst = 1;
            } else {
                $first = 0;
                foreach ($whereclause as $row) {
                    if ($first == 0) {
                        $whereString = $whereString . " OR " . " `" . $row . "`" . '=' . "'" . $val[$row] . "'";
                        $first = 1;
                    } else {
                        $whereString = $whereString . "AND `" . $row . "`" . '=' . "'" . $val[$row] . "' ";
                    }
                }
            }
        }
        $whereString = $whereString . ")";

        return $whereString;
    }

    public function fetch_recipients($refno) {
        $this->epay_db->select('*')
                ->from('vw_incharge')
                ->where('refbo', $refno)
                ->where('status', 1)
                ->where('year', date('Y'));

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function UpdatePendingForm($data, $id, $index) {
        $table = array('tableleave', 'tbl_undertime', 'tbl_change_schedule', 'tbl_overtime');
        $this->epay_db->where('id', $id);
        return $this->epay_db->update($table[$index], $data);
    }

    public function ContstructTableSql($data, $type) {
        $this->epay_db->select('*')
                ->from($data['table']);

        if (isset($data['empname']) != '') {
            $this->epay_db->like('empname', $data['empname'], 'after');
        }


        if (isset($data['datein'])) {
            if ($data['table'] == 'tableleave') {
                $this->epay_db->where($data['dates']);
            } else {
                $this->epay_db->where('worksched_in >=', $data['datein'])
                        ->where('worksched_in <=', $data['dateout']);
            }
        }


        if (isset($data['cancellation_page'])) {
            if ($data['cancellation_page'] == 0) {
                $this->epay_db->where("(`is_deleted` = 0 OR `is_deleted` IS NULL OR `hr_cancel_status` = 2)")
//                        ->where("approved_status", 0);
                        ->where('(approved_status = 0 OR noted_status = 0 OR counter_signed_status = 0)');
                
            } else {
                $this->epay_db->where("hr_cancel_status", 0)
                        ->where('is_deleted ', 1);
            }
        }

        if (count($type) > 0) {
            if (isset($type['leavetype'])) {
                if ($type['leavetype'] == 'Others') {
                    $this->epay_db->where("leavetype <> 'Vacation Leave' AND leavetype <>'Sick Leave'");
                } else {
                    $this->epay_db->where($type);
                }
            } else {
                $this->epay_db->where($type);
            }
        }
        if (isset($data['whereStructure'])) {
            if ($data['whereStructure'] != '()') {
                $this->epay_db->where($data['whereStructure']);
            }
        }

        if (isset($data['profileno'])) {
            $this->epay_db->where('profileno', $data['profileno']);
        }
        if (isset($data['page'])) {
            if ($data['page'] == 1) {
                $this->epay_db->where('profileno !=', $this->session->userdata('profileno'));
                $this->epay_db->order_by('noted_status', 'DESC');
//                $this->epay_db->order_by('empname', 'ASC');
                $this->epay_db->order_by('date_requested', 'DESC');
            } else {
                $this->epay_db->where($data['approved_forms']);
                $this->epay_db->order_by('empname', 'ASC');
            }
        }
    }

    function MyDataTable($data, $type) {
        $this->ContstructTableSql($data, $type);
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    function MyDataTableFiler($data, $type) {
        $this->ContstructTableSql($data, $type);
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    function ApprovedForms($profileno, $datein, $dateout, $table) {
        $this->epay_db->select('*')
                ->from($table);
        if ($table == 'tableleave') {
            $this->epay_db->where("(`fromdate` BETWEEN " . "'" . $datein . "'" . "AND " . "'" . $dateout . "'" . "OR "
                    . "`todate` BETWEEN " . "'" . $datein . "'" . "AND " . "'" . $dateout . "')");
            $this->epay_db->where("('" . $datein . "' BETWEEN " . "fromdate AND todate)");
        } else {
            $this->epay_db->where('worksched_in >=', $datein)
                    ->where('worksched_in <=', $dateout);
        }
        $this->epay_db->where('approved_status', 1)->where('hr_cancel_status <>', 1)->where('profileno', $profileno);
        $query = $this->epay_db->get();
        return $query->result();
    }

    function FetchReport($structure, $worksched_from, $worksched_to, $category) {

        $db_table = array('tableleave', 'tbl_undertime', 'tbl_change_schedule', 'tbl_overtime');
        $this->epay_db->select('*')->from($db_table[$category])->where($structure);
        if ($category == 0) {
            $this->epay_db->where('fromdate >=', $worksched_from)
                    ->where('fromdate <=', $worksched_to);
        } else {
            $this->epay_db->where('worksched_in >=', $worksched_from)
                    ->where('worksched_out <=', $worksched_to);
        }

        $this->epay_db->where('approved_status', 1)->where('hr_cancel_status <>', 1);
        $this->epay_db->order_by('empname', 'ASC');

        $query = $this->epay_db->get();
        return $query->result();
    }

    public function DeleteForm($id, $table) {
        $this->epay_db->where('id', $id);
        return $this->epay_db->delete($table);
    }

    public function FetchApprovedLeave($profileno, $datein, $dateout, $table) {
        $this->epay_db->select('*')
                ->from($table . ' use index (`idx_profileno_workscheds`)')
                ->where('profileno', $profileno);
        $this->epay_db->where('is_approved', 1);
        $this->epay_db->where('worksched_in >=', $datein)->where('worksched_in <=', $dateout);
        $query = $this->epay_db->get();
        return $query->result();
    }
    
    public function FetchMessageDirectory(){
        $this->hospv2->select('*')->from('textdata');
             $query = $this->hospv2->get();
        return $query->result();
    }
    
    public function SaveTextData($data){
        return $this->messaging->insert('tb_text', $data);
    }

}
