<?php
class CategoryModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); 
    }


    public function get_all_categories($user_id) {
        return $this->db->get_where('categories', ['user_id' => $user_id])->result_array();
    }

    public function insert_category($user_id, $category_name) {
        $data = [
            'user_id' => $user_id,
            'category_name' => $category_name
        ];
    
        $this->db->insert('categories', $data);
        return $this->db->insert_id();
    }


    public function update_category($id, $user_id, $category_name) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->update('categories', [
            'category_name' => $category_name,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    

    public function delete_category($id, $user_id) {
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        return $this->db->delete('categories');
    }

}
?>
