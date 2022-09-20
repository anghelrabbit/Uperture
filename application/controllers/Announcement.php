<?php


class Announcement extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_announcement', 'M_announcement');
        $this->load->model('model_employee', 'M_employee');
    }

    public function index() {
        if ($this->has_logging_in()) {
            $id = $this->input->post('idx');
            if ($id == null && $id == '') {
                $id = 0;
            }

            $data["page_title"] = "Announcement";
            $data['extra_data'] = $id;
            $data['page'] = 'pages/menu/announcement/announcement';
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
            );

            $data["js"] = array
                (
                'assets/vendors/bower_components/jquery/dist/jquery.min.js',
                'assets/vendors/bower_components/jquery-ui/jquery-ui.min.js',
                'assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js',
                'assets/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js',
                'assets/vendors/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
                'assets/vendors/dist/js/adminlte.min.js',
                'assets/vendors/bower_components/sweetalert/sweetalert.min.js',
                'assets/vendors/bootstrap-toggle-master/js/bootstrap-toggle.min.js',
                'assets/myjs/utilities/structure.js',
                'assets/myjs/utilities/selecting_employees.js',
                'assets/myjs/announcement/announcement.js',
                'assets/myjs/announcement/compose_announcement.js',
            );

            $this->InspectUser('menu/announcement/announcement', $data);
        } else {
            redirect('', 'refresh');
        }
    }

    public function SaveAnnouncementImage() {
        $this->FormRestrictions('announcement_img');
        $result = $this->ValidateErrorsSample($_POST);
        if (strtotime($this->input->post('displayed_from')) == false) {
            $result['success'] = false;
            $result['messages']['displayed_from'] = 'Invalid Date';
        }
        if (strtotime($this->input->post('displayed_to')) == false) {
            $result['success'] = false;
            $result['messages']['displayed_to'] = 'Invalid Date';
        }
        if ($result['success'] == true) {
            $filename = basename(date('HYimsd') . '.jpg');
            $data = array();
            $data['displayed_from'] = $this->input->post('displayed_from');
            $data['displayed_to'] = $this->input->post('displayed_to');
            $data['name'] = $filename;
            $data['updated_by'] = $this->session->userdata('profileno');
            $data['updated_date'] = date('Y-m-d H:i:s');
			  $data['pop_up'] = $this->input->post('popup');
            $this->M_announcement->SaveUpdateAnnouncementImage($data, 0);
            if (!move_uploaded_file($_FILES['file']['tmp_name'], 'assets/uploads/announcement/' . $filename)) {
                $result['success'] = false;
                $result['image_error'] = 'No uploaded Image';
            }
        }
        echo json_encode($result);
    }

    public function FetchAnnouncement() {
        $announcement_images = $this->M_announcement->FetchAnnouncementImages(date('Y-m-d'));
        $announcement = $this->M_announcement->FetchDashboardAnnouncements(date('Y-m-d'));
        $announcement_data = array();
        $popup_announcemnt = array();
        foreach ($announcement as $val) {
            $result = $this->M_announcement->FetchSpecificAnnouncement($val->announcement_id);
            $sub_array = array();
            if (count($result) > 0) {
                $dates = $this->M_announcement->FetchAnnouncementDateTime($val->announcement_id);
                $datein = $dates[0]->start_date;
                $dateout = $dates[(count($dates) - 1)]->end_date;
                if (strtotime($datein) == strtotime($dateout)) {
                    $datein = date('F d, Y', strtotime($datein));
                } else {
                    $datein = date('F d', strtotime($datein)) . ' - ' . date('F d, Y', strtotime($dateout));
                }
                $sub_array['topic'] = $result[0]->topic;
                $sub_array['venue'] = $result[0]->venue;
                $sub_array['dates'] = $datein;
                $sub_array['announcement_id'] = $val->announcement_id;
                $sub_array['pop_up'] = $result[0]->pop_up;
                $announcement_data[] = $sub_array;
                if ($result[0]->pop_up == 1) {
                    $popup_announcemnt[] = $sub_array;
                }
            }
        }
		 foreach ($announcement_images as $val) {
            if ($val->pop_up == 1) {
                $popup_announcemnt[] = 1;
            }
        }
        $output = array('images' => $announcement_images, 'announcement' => $announcement_data, 'popup_announcement' => $popup_announcemnt, 'hr' => $this->session->userdata('hr'));
        echo json_encode($output);
    }

    public function FetchAnnouncementForms() {
        $announcements = $this->M_announcement->FetchAnnouncementTable();

        $data = array();
        foreach ($announcements as $val) {
            $dates = $this->M_announcement->FetchAnnouncementDateTime($val->id);
            $prepared_by = $this->M_employee->FetchEmployee(array('profileno' => $val->updated_by));
            $datein = '';
            $dateout = '';
            if (count($dates) > 0) {
                $datein = $dates[0]->start_date;
                $dateout = $dates[(count($dates) - 1)]->end_date;
            }

            if (strtotime($datein) == strtotime($dateout)) {
                $datein = date('F d, Y', strtotime($datein));
            } else {
                $datein = date('F d', strtotime($datein)) . ' - ' . date('F d, Y', strtotime($dateout));
            }
			 $checked = ($val->pop_up == 1) ?'checked': '';
            $sub_array = array();
            $sub_array[] = '<span class="btn " style="background-color:#3ED03E;color:white" onclick="showAnnouncementDetails(' . $val->id . ')">Show Details</span>';
            $sub_array[] = $prepared_by[0]->lastname . " " . $prepared_by[0]->firstname;
            $sub_array[] = $val->topic;
            $sub_array[] = $datein;
            $sub_array[] = $val->venue;
			 $sub_array[] = '  <div class="col-lg-1 col-md-2 col-sm-2  col-xs-6">
                    <div class="form-group">
                        <input class="announce_toggle" '.$checked.'  type="checkbox" onchange="popupAnnouncement(' . $val->id . ',this)" >
                    </div>
                </div>';
            $data[] = $sub_array;
        }


        $output = array
            (
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => count($announcements),
            "recordsFiltered" => $this->M_announcement->FetchAnnouncementFilter(),
            "data" => $data
        );

        echo json_encode($output);
    }

    public function SetupAnnouncementDates() {
        $datein = $this->input->post('datein');
        $dateout = $this->input->post('dateout');
        $data = (array) json_decode($this->input->post('datetime'));
        $is_same_time = $this->input->post('same_time');
        $updated_dates = array();
        if ($is_same_time == 1) {
            $datestring = (strtotime($datein) == strtotime($dateout)) ? date('F d, Y', strtotime($datein)) : date('F d', strtotime($datein)) . " - " . date('F d, Y', strtotime($dateout));
            $id = 0;
            if (count($data) > 0) {
                foreach ($data as $val) {
                    $id = $val->id;
                }
            }
            $updated_dates[$datein] = array('timein' => null, 'timeout' => null, 'id' => $id, 'date_string' => $datestring);
        } else {
            while ($datein <= $dateout) {
                if (!isset($data[$datein])) {
                    $updated_dates[$datein] = date('F d, Y', strtotime($datein));
                    $updated_dates[$datein] = array('timein' => null, 'timeout' => null, 'id' => 0, 'date_string' => date('F d, Y', strtotime($datein)));
                } else {
                    $data[$datein]->date_string = date('F d, Y', strtotime($datein));
                    $updated_dates[$datein] = $data[$datein];
                }
                $datein = date('Y-m-d', strtotime($datein . '+1 days'));
            }
        }
        echo json_encode($updated_dates);
    }

    public function SaveUpdateAnnouncement() {
        $this->FormRestrictions('announcement');
        $result = $this->ValidateErrorsSample($_POST);
        $result['messages']['announcement_datetime'] = '';
        $result['messages']['announcement_participants'] = '';
        $same_time = $this->input->post('same_time');
        $announcement_id = $this->input->post('id');
        $announce_datein = $this->input->post('announce_datein');
        $announce_dateout = $this->input->post('announce_dateout');
        $prev_date_times = (array) json_decode($this->input->post('prev_date_times'));
        $dates_with_time = (array) json_decode($this->input->post('dates_with_time'));
        $selected_departments = (array) json_decode($this->input->post('selected_departments'));
        $unselectd_departments = (array) json_decode($this->input->post('unselected_departments'));
        $selectd_profileno = (array) json_decode($this->input->post('selectd_profileno'));
        $unselectd_profileno = (array) json_decode($this->input->post('unselectd_profileno'));
        $where_selected = $this->WhereSelectedEmployees($selected_departments, $unselectd_departments, $selectd_profileno, $unselectd_profileno);
        $emp = array();
        if ($where_selected == '') {
            $result['success'] = false;
            $result['messages']['announcement_participants'] = '(Please Select Employees)';
        }
        if (strtotime($announce_datein) == false) {
            $result['success'] = false;
            $result['messages']['announce_datein'] = 'Invalid Date';
        }
        if (strtotime($announce_dateout) == false) {
            $result['success'] = false;
            $result['messages']['announce_dateout'] = 'Invalid Date';
        }
        $announcement_datetime = array();
        foreach ($dates_with_time as $key => $value) {
            if (($value->timein != null && $value->timeout != null) && ($value->timein != '' && $value->timeout != '')) {
                $start_date = $key;
                $end_date = $key;
                if ($same_time == 1) {
                    $start_date = $announce_datein;
                    $end_date = $announce_dateout;
                }
                unset($prev_date_times[$key]);
                $announcement_datetime[] = array(
                    'announcement_id' => $announcement_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'id' => $value->id,
                    'start_time' => $dates_with_time[$key]->timein,
                    'end_time' => $dates_with_time[$key]->timeout
                );
            } else {
                $result['success'] = false;
                $result['messages']['announcement_datetime'] = 'Fill-up all Time-in/Time-out';
                break;
            }
        }

        if ($result['success']) {

            $emp = $this->M_employee->FetchEmployeeTable(0, $where_selected, '', array());
            $announcement_data = array(
                'optional_id' => $this->input->post('announcement_optional_id'),
                'topic' => $this->input->post('announcement_topic'),
                'venue' => $this->input->post('announcement_venue'),
                'announcement_type' => $this->input->post('announcement_category'),
                'description' => $this->input->post('announcement_description'),
                'updated_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('profileno'),
            );
            $announcement_id = $this->M_announcement->SaveUpdateAnnouncement($announcement_data, $announcement_id);
            $participants = array();
            foreach ($announcement_datetime as $key => $val) {
                $announcement_datetime[$key]['announcement_id'] = $announcement_id;
                $this->M_announcement->SaveUpdateAnnouncemenTime($announcement_datetime[$key], $announcement_datetime[$key]['id']);
            }

            foreach ($prev_date_times as $val) {
                $this->M_announcement->RemoveAnnouncementDateTime($val->id);
            }
            foreach ($emp as $val) {
                $participants[] = array(
                    'announcement_id' => $announcement_id,
                    'profileno' => $val->profileno,
                    'comID' => $val->comID,
                    'locID' => $val->locID,
                    'divID' => $val->divID,
                    'depID' => $val->depID,
                    'secID' => $val->secID,
                    'areID' => $val->areID,
                );
            }
            $this->M_announcement->SaveUpdateParticipants($participants, $announcement_id);
        }
        echo json_encode($result);
    }

    public function FetchSpecificAnnouncement() {
        $id = $this->input->post('id');
        $announcement = $this->M_announcement->FetchSpecificAnnouncement($id);

        $datein = '';
        $dateout = '';
        $same_time = 0;
        $participants = array();
        $structure_profileno = array();
        if (count($announcement) > 0) {
            $datetime = $this->M_announcement->FetchAnnouncementDateTime($announcement[0]->id);
            $participants_result = $this->M_announcement->FetchAnnouncementParticipants($announcement[0]->id);
            $data = array();
            $announcement_datetime = array();
            if (count($datetime) == 1) {
                $datein = $datetime[0]->start_date;
                $dateout = $datetime[0]->end_date;
                $same_time = 1;
                $announcement_datetime[$datein] = array('timein' => $datetime[0]->start_time, 'timeout' => $datetime[0]->end_time, 'id' => $datetime[0]->id,
                    'date_string' => date('F d', strtotime($datetime[0]->start_date)) . " - " . date('F d, Y', strtotime($datetime[0]->end_date)));
            } else if (count($datetime) > 1) {
                foreach ($datetime as $val) {
                    if ($datein == '') {
                        $datein = $val->start_date;
                    }
                    $dateout = $val->end_date;
                    $announcement_datetime[$val->start_date] = array('timein' => $val->start_time, 'timeout' => $val->end_time, 'id' => $val->id, 'date_string' => date('F d, Y', strtotime($val->start_date)));
                }
            }
            foreach ($participants_result as $val) {
                $structure = '';
                $participants[$val->profileno] = $val->profileno;
                if ($val->comID != null && $val->comID != '') {
                    $structure = $val->comID;
                }
                if ($val->locID != null && $val->locID != '') {
                    $structure = $structure . "@" . $val->locID;
                }
                if ($val->divID != null && $val->divID != '') {
                    $structure = $structure . "@" . $val->divID;
                }
                if ($val->depID != null && $val->depID != '') {
                    $structure = $structure . "@" . $val->depID;
                }
                if ($val->secID != null && $val->secID != '') {
                    $structure = $structure . "@" . $val->secID;
                }
                if ($val->areID != null && $val->areID != '') {
                    $structure = $structure . "@" . $val->areID;
                }
                $structure_profileno[$structure][] = $val->profileno;
            }
            $data['announcement_info'] = $announcement[0];
            $data['datein'] = $datein;
            $data['dateout'] = $dateout;
            $data['same_time'] = $same_time;
            $data['participants'] = $participants;
            $data['structure_profileno'] = $structure_profileno;
            $data['datetime'] = $announcement_datetime;
            echo json_encode($data);
        }
    }

    public function RemoveAnnouncementImage() {
        $id = $this->input->post('announcement_id');
        $img_name = $this->input->post('img_name');

        $file = 'assets/uploads/announcement/' . $img_name;
        if (unlink($file)) {
            echo json_encode($this->M_announcement->RemoveAnnouncementImage(array('id' => $id)));
        }
    }
	 public function AnnouncementPopup() {
        $id = $this->input->post('id');
        $is_checked = $this->input->post('is_checked');
      echo json_encode($this->M_announcement->SaveUpdateAnnouncement(array('pop_up'=>$is_checked),$id));
    }

}
