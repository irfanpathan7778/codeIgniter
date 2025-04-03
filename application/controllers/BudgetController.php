<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');  // Change to your required timezone  

class BudgetController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('JwtLib');

        $this->load->model('BudgetModel');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    
    public function index() {
        $this->load->view('budget');
    }

    public function get_budget() {
        $user_id = $this->authenticate_user();
        if (!$user_id) return;

        $budgets = $this->BudgetModel->get_all_budgets($user_id);
        if ($budgets) {
            echo json_encode($budgets);
        } else {
            echo json_encode(["error" => "No budgets found for this user"]);
        }
    }

    public function get_budget_by_id($id) {
        $user_id = $this->authenticate_user();
        if (!$user_id) return;

        $budget = $this->BudgetModel->get_budget_by_id($id, $user_id);
        if ($budget) {
            echo json_encode($budget);
        } else {
            echo json_encode(["error" => "Budget not found or access denied"]);
        }
    }

    public function add_budget() {
        $user_id = $this->authenticate_user();
        if (!$user_id) return;

        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['category_id'], $data['limit'], $data['month'], $data['year'])) {
            echo json_encode(["error" => "Invalid input"]);
            return;
        }

        $data['user_id'] = $user_id;

        $insert_id = $this->BudgetModel->insert_budget($data);
        if ($insert_id) {
            echo json_encode(["message" => "Budget added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add budget"]);
        }
    }

public function update_budget($id) {
    $user_id = $this->authenticate_user();
    if (!$user_id) return;

    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['category_id'], $data['limit'], $data['month'], $data['year'])) {
        echo json_encode(["error" => "Invalid input"]);
        return;
    }

    $updated = $this->BudgetModel->update_budget($id, $data, $user_id);
    if ($updated) {
        echo json_encode(["message" => "Budget updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update budget or access denied"]);
    }
}

public function delete_budget($id) {
    $user_id = $this->authenticate_user();
    if (!$user_id) return;

    $deleted = $this->BudgetModel->delete_budget($id, $user_id);
    if ($deleted) {
        echo json_encode(["message" => "Budget deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete budget or access denied"]);
    }
}

// Authenticate user using JWT
private function authenticate_user() {
    $headers = $this->input->request_headers();
    $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

    if (!$token) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Token is required']);
        exit;
    }

    $decoded_token = (array) $this->jwtlib->verify_jwt($token);

    // print_r($decoded_token);
    if (!$decoded_token) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }

    return $decoded_token['id'];
}
}
