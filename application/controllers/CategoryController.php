<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');  // Change to your required timezone  

class CategoryController extends MY_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->library('JwtLib');
        $this->load->model('Users_model');
        $this->load->model('CategoryModel');
        // $this->authenticate();
    }

    public function index() {
        $this->load->view('categories');
    }


    public function get_categories() {
        $user_id = $this->authenticate_user();
    
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $this->load->model('CategoryModel');
        $categories = $this->CategoryModel->get_all_categories($user_id);
    
        if (!$categories) {
            $categories = [];
        }
    
        header('Content-Type: application/json');
        echo json_encode($categories);
        exit;
    }
    



    public function add_category() {
        $user_id = $this->authenticate_user();
        
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($input['category_name']) || empty(trim($input['category_name']))) {
            echo json_encode(["error" => "Category name is required"]);
            return;
        }
    
        $this->load->model('CategoryModel');
        $category_id = $this->CategoryModel->insert_category($user_id, trim($input['category_name']));
    
        if ($category_id) {
            echo json_encode(["message" => "Category added successfully", "category_id" => $category_id]);
        } else {
            echo json_encode(["error" => "Failed to add category"]);
        }
    }
    


    public function update_category($id) {
        $user_id = $this->authenticate_user();
    
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $this->load->model('CategoryModel');
    
        $input_data = json_decode(file_get_contents("php://input"), true);
        $new_category_name = $input_data['category_name'] ?? '';
    
        if (empty($new_category_name)) {
            echo json_encode(["error" => "Category name is required"]);
            return;
        }
    
        $update_status = $this->CategoryModel->update_category($id, $user_id, $new_category_name);
    
        if ($update_status) {
            echo json_encode(["message" => "Category updated successfully"]);
        } else {
            echo json_encode(["error" => "Failed to update category"]);
        }
    }

    

    public function delete_category($id) {
        $user_id = $this->authenticate_user();
    
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $this->load->model('CategoryModel');
    
        $delete_status = $this->CategoryModel->delete_category($id, $user_id);
    
        if ($delete_status) {
            echo json_encode(["message" => "Category deleted successfully"]);
        } else {
            echo json_encode(["error" => "Failed to delete category"]);
        }
    }

    

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

?>
