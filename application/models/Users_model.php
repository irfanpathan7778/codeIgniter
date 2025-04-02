<?php
class Users_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); 
    }

    public function insert_user($data) {
        return $this->db->insert('users', $data);
    }
   
     public function get_user_by_email($email) {
        $query = $this->db->get_where('users', ['email' => $email]);
        return $query->row();
    }

    public function get_user_by_id($userId) {
        $query = $this->db->get_where('users', ['id' => $userId]);
        return $query->row();


        
    }


     
}
?>
