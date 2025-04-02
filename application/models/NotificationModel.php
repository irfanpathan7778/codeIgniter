<?php

class NotificationModel extends CI_Model {
    public function store_notification($data) {
        return $this->db->insert('notifications', $data);
    }

    public function get_notifications() {

        $query = $this->db->get('notifications');
        return $query->result_array();
    }


    public function mark_notifications_read() {

        return $this->db->update('notifications',[
         'mark_status' => 1
        ]);
    }
}

?>