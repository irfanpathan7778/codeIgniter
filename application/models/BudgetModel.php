<?php
class BudgetModel extends CI_Model {
public function get_all_budgets($user_id) {
    $this->db->select('budget.id, budget.category_id, categories.category_name, budget.limit, budget.month, budget.year');
    $this->db->from('budget');
    $this->db->join('categories', 'categories.id = budget.category_id', 'left');
    $this->db->where('budget.user_id', $user_id);

    $query = $this->db->get();
    return $query->result();
}


public function get_budgets_by_user_id($user_id) {
    $this->db->select('id, category_id, limit, month, year');
    $this->db->from('budget');
    $this->db->where('user_id', $user_id);
    $query = $this->db->get();
    return $query->result();
}


public function get_budget_by_id($id) {
    $this->db->select('*');
    $this->db->from('budget');
    $this->db->where('id', $id);
    $query = $this->db->get();
    return $query->row();
}

public function insert_budget($data) {
    return $this->db->insert('budget', $data) ? $this->db->insert_id() : false;
}

public function update_budget($id, $data) {
    $this->db->where('id', $id);
    return $this->db->update('budget', $data);
}

public function delete_budget($id) {
    $this->db->where('id', $id);
    return $this->db->delete('budget');
}

   public function get_user_budget($user_id) {
    $this->db->select('limit');
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('budget');
    return $query->row_array();
}



    public function get_budget_by_category($user_id, $category_id) {
        $this->db->select('limit');
        $this->db->from('budget');
        $this->db->where('user_id', $user_id);
        $this->db->where('category_id', $category_id);
        $query = $this->db->get();
        
        $result = $query->row();
        return $result ? $result->limit : 0;
    }
}
?>
