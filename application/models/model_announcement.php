<?php


class model_announcement extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->epay_db = $this->load->database('default', true);
    }

    public function SaveUpdateAnnouncementImage($data, $id) {
        if ($id > 0) {
            $this->epay_db->where('id', $id);
            return $this->epay_db->update('tbl_announcement_images', $data);
        } else {
            return $this->epay_db->insert('tbl_announcement_images', $data);
        }
    }

    public function FetchDashboardAnnouncements($data) {
        $this->epay_db->select('*')
                ->from('tbl_announcement_datetime')
                ->where("'" . $data . "'" . ' BETWEEN start_date AND end_date')
                ->or_where('start_date', $data)
                ->group_by('announcement_id');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchAnnouncementImages($data) {
        $this->epay_db->select('*')
                ->from('tbl_announcement_images')
                ->where("'" . $data . "'" . ' BETWEEN tbl_announcement_images.displayed_from AND tbl_announcement_images.displayed_to')
                ->order_by('displayed_from', 'DESC');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchAnnouncement() {
        $this->epay_db->select('*')
                ->from('tbl_announcement');
    }

    public function FetchAnnouncementTable() {
        $this->FetchAnnouncement();
        if ($this->input->post("length") != -1) {
            $this->epay_db->limit($this->input->post('length'), $this->input->post('start'));
        }
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchAnnouncementFilter() {
        $this->FetchAnnouncement();
        $query = $this->epay_db->get();
        return $query->num_rows();
    }

    public function FetchSpecificAnnouncement($id) {
        $this->epay_db->select('*')
                ->where('id', $id)
                ->from('tbl_announcement');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchAnnouncementDateTime($announcement_id) {
        $this->epay_db->select('*')
                ->where('announcement_id', $announcement_id)
                ->from('tbl_announcement_datetime')
                ->order_by('start_date', 'ASC');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function FetchAnnouncementParticipants($announcement_id) {
        $this->epay_db->select('*')
                ->where('announcement_id', $announcement_id)
                ->from('tbl_announcement_participants');
        $query = $this->epay_db->get();
        return $query->result();
    }

    public function SaveUpdateAnnouncement($data, $id) {

        if ($id > 0) {
            $this->epay_db->where('id', $id);
            $this->epay_db->update('tbl_announcement', $data);
        } else {
            $this->epay_db->insert('tbl_announcement', $data);
            $id = $this->epay_db->insert_id();
        }
        return $id;
    }

    public function SaveUpdateAnnouncemenTime($data, $id) {
        if ($id > 0) {

            $this->epay_db->where('id', $id);
            $this->epay_db->update('tbl_announcement_datetime', $data);
        } else {
            return $this->epay_db->insert('tbl_announcement_datetime', $data);
        }
    }

    public function SaveUpdateParticipants($data, $announcement_id) {
        $this->epay_db->where('announcement_id', $announcement_id);
        $this->epay_db->delete('tbl_announcement_participants');
        return $this->epay_db->insert_batch('tbl_announcement_participants', $data);
    }

    public function RemoveAnnouncementDateTime($announcement_id) {
        $this->epay_db->where('id', $announcement_id);
        $this->epay_db->delete('tbl_announcement_datetime');
    }

    public function RemoveAnnouncementImage($data) {
        $this->epay_db->where($data);
        return $this->epay_db->delete('tbl_announcement_images');
    }

}
