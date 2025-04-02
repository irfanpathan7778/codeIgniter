<?php
class ExpenseModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); 
    }
    public function get_expenses_by_user($user_id) {
        $this->db->select('expenses.id, expenses.amount, categories.category_name as category, expenses.description, expenses.created_at as date');
        $this->db->from('expenses');
        $this->db->join('categories', 'categories.id = expenses.category_id', 'left');
        $this->db->where('expenses.user_id', $user_id);
  
        return $this->db->get()->result_array();
    }

    public function add_expense($data) {
        if ($this->db->insert('expenses', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }


    public function update_expense($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('expenses', $data);
    }
    



    public function delete_expense($id) {
        $this->db->where('id', $id);
        return $this->db->delete('expenses');
    }
    



    public function get_expense_by_id($id) {
        return $this->db->get_where('expenses', ['id' => $id])->row_array();
    }
    

        public function get_expenses_by_month($year, $month) {
            $this->db->select("
                expenses.id AS expense_id, 
                expenses.amount, 
                expenses.category_id, 
                categories.category_name, 
                expenses.description, 
                expenses.created_at
            ");
            $this->db->from("expenses");
            $this->db->join("categories", "categories.id = expenses.category_id", "left");
            $this->db->where("YEAR(expenses.created_at)", $year);
            $this->db->where("MONTH(expenses.created_at)", $month);
        
            $query = $this->db->get();
            return $query->result_array();
        }        
        

        public function get_or_create_category_id($user_id, $category_name) {
            $this->db->select('id');
            $this->db->from('categories');
            $this->db->where('category_name', $category_name);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get();
        
            if ($query->num_rows() > 0) {
                return $query->row()->id;
            }
        
            $data = [
                'user_id' => $user_id,
                'category_name' => $category_name
            ];
            $this->db->insert('categories', $data);
            return $this->db->insert_id();
        }
        
        public function insert_expense($expense_data) {
            return $this->db->insert('expenses', $expense_data);
        }


   
         public function get_total_spending($user_id) {
            $this->db->select_sum('amount');
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('expenses');
            return $query->row()->amount ?? 0;
        }

        public function get_top_spending_categories($user_id) {
            $this->db->select('categories.category_name, SUM(expenses.amount) as total_spent');
            $this->db->from('expenses');
            $this->db->join('categories', 'categories.id = expenses.category_id', 'left');
            $this->db->where('expenses.user_id', $user_id);
            $this->db->group_by('categories.category_name');
            $this->db->order_by('total_spent', 'DESC');
            $this->db->limit(3);
            return $this->db->get()->result_array();
        }




        public function get_monthly_report($user_id, $year, $month) {

            $this->db->select_sum('amount');
            $this->db->where('user_id', $user_id);
            $this->db->where('YEAR(created_at)', $year);
            $this->db->where('MONTH(created_at)', $month);
            $total_spent = $this->db->get('expenses')->row()->amount ?? 0;
        
            $this->db->select('categories.category_name as category, SUM(expenses.amount) as amount');
            $this->db->from('expenses');
            $this->db->join('categories', 'categories.id = expenses.category_id', 'left');
            $this->db->where('expenses.user_id', $user_id);
            $this->db->where('YEAR(expenses.created_at)', $year);
            $this->db->where('MONTH(expenses.created_at)', $month);
            $this->db->group_by('categories.category_name');
            $query = $this->db->get();
        
            return [
                "total_spent" => $total_spent,
                "category_breakdown" => $query->result_array()
            ];
        }


        
        public function get_total_spent_by_category($user_id, $category_id) {
            $this->db->select_sum('amount');
            $this->db->from('expenses');
            $this->db->where('user_id', $user_id);
            $this->db->where('category_id', $category_id);
            $query = $this->db->get();
            
            $result = $query->row();
            return $result ? $result->amount : 0;
        }
        
}
?>
