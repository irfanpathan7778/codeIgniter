<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->library('JwtLib');
        $this->load->model('Users_model');
        $this->load->model('ExpenseModel');
        $this->load->model('BudgetModel');
        // $this->authenticate();
        // $headers = $this->input->get_request_header('Authorization');\

        // // Check for JWT token
        // $token = $this->input->get_request_header('Authorization');

        // print_r($token);exit;

        // if (!$token) {
        //     print_r("token"); exit;
        //     redirect('login_user');
        // }

        // // Decode JWT token
        // $decoded = $this->jwtlib->decode(str_replace("Bearer ", "", $token));

        // if (!$decoded) {
        //     print_r("decoded"); exit;

        //     redirect('login_user');
        // }
    }

    public function index() {
        $user_data = $this->session->userdata('jwt_token');
        $data = (array) $this->jwtlib->verify_jwt($user_data);
    
        $user = $this->Users_model->get_user_by_id($data['id']);
        if (!$user) {
            show_404();
        }
    
        $user_id = $data['id'];
        $total_spending = $this->ExpenseModel->get_total_spending($user_id);
        $budget = $this->BudgetModel->get_user_budget($user_id);
        $remaining_budget = $budget ? $budget['limit'] - $total_spending : 0;
        $top_categories = $this->ExpenseModel->get_top_spending_categories($user_id);
    
        $data['user'] = $user;
        $data['total_spending'] = $total_spending;
        $data['remaining_budget'] = $remaining_budget;
        $data['top_categories'] = $top_categories;
    
        $this->load->view('dashboard', $data);
    }
    

    public function postman_dashbaord(){

        $token = $this->session->userdata('jwt_token');
        $user_data = (array) $this->jwtlib->verify_jwt($token);
    
        $user = $this->Users_model->get_user_by_id($user_data['id']);
        if (!$user) {
            show_404();
        }
    
        $user_id = $user_data['id'];
        $total_spending = $this->ExpenseModel->get_total_spending($user_id);
        $budget = $this->BudgetModel->get_user_budget($user_id);
        $remaining_budget = $budget ? $budget['limit'] - $total_spending : 0;
        $top_categories = $this->ExpenseModel->get_top_spending_categories($user_id);
    
        // $data['user'] = $user;
        $data['total_spending'] = $total_spending;
        $data['remaining_budget'] = $remaining_budget;
        $data['top_categories'] = $top_categories;


        echo json_encode($data);
    }
}

?>
