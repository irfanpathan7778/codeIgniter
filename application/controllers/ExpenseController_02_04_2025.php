<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExpenseController extends MY_Controller {
    public function __construct() {
        parent::__construct();
        // $this->load->library('JwtLib');
        $this->load->model('Users_model');
        $this->load->model('ExpenseModel');
        $this->load->library('Pdf'); 

        // $this->authenticate();
    }

    public function index() {
        $this->load->view('expenses'); 

    }


       public function expenses_get() {
        $user_id = $this->authenticate_user();


        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $this->load->model('ExpenseModel');
        $expenses = $this->ExpenseModel->get_expenses_by_user($user_id);
    

        if (!$expenses) {
            // print_r('in');
            // exit;
            $expenses = [];
        }

    
        header('Content-Type: application/json');
        echo json_encode($expenses);
        exit;
    }
    


    public function add_expense() {
        $user_id = $this->authenticate_user();
    
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $input_data = json_decode(file_get_contents("php://input"), true);
        
        $data = [
            'user_id' => $user_id,
            'amount' => $input_data['amount'] ?? '',
            'category_id' => $input_data['category_id'] ?? '',
            'description' => $input_data['description'] ?? '',
            'date' => $input_data['date'] ?? ''
        ];
    
        $this->load->model('ExpenseModel');
        $expense_id = $this->ExpenseModel->add_expense($data);
    
        if ($expense_id) {
            echo json_encode(["message" => "Expense added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add expense"]);
        }
    }

    public function update_expense($id) {
        $user_id = $this->authenticate_user();
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        print_r($data);exit;
        if (!isset($data['amount'], $data['category_id'], $data['description'], $data['date'])) {
            echo json_encode(["error" => "All fields (amount, category_id, description, date) are required"]);
            return;
        }
    
        $this->load->model('ExpenseModel');
    
        $expense = $this->ExpenseModel->get_expense_by_id($id);
        if (!$expense || $expense['user_id'] != $user_id) {
            echo json_encode(["error" => "Expense not found or unauthorized"]);
            return;
        }
    
        $updated = $this->ExpenseModel->update_expense($id, $data);
        if ($updated) {
            echo json_encode(["message" => "Expense updated successfully"]);
        } else {
            echo json_encode(["error" => "Failed to update expense"]);
        }
    }
    

    public function delete_expense($id) {
        $user_id = $this->authenticate_user();
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $this->load->model('ExpenseModel');
    
        $expense = $this->ExpenseModel->get_expense_by_id($id);
        if (!$expense || $expense['user_id'] != $user_id) {
            echo json_encode(["error" => "Expense not found or unauthorized"]);
            return;
        }
    
        $deleted = $this->ExpenseModel->delete_expense($id);
        if ($deleted) {
            echo json_encode(["message" => "Expense deleted successfully"]);
        } else {
            echo json_encode(["error" => "Failed to delete expense"]);
        }
    }

    

    public function get_expense_by_id($id) {
        $user_id = $this->authenticate_user();
        if (!$user_id) {
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        $this->load->model('ExpenseModel');
    
        $expense = $this->ExpenseModel->get_expense_by_id($id);
    
        if (!$expense || $expense['user_id'] != $user_id) {
            echo json_encode(["error" => "Expense not found or unauthorized"]);
            return;
        }
    
        header('Content-Type: application/json');
        echo json_encode($expense);
        exit;
    }
    

    public function export_csv() {
        $user_id = $this->authenticate_user();
        if (!$user_id) return;
    
        $year = $this->input->get('year');
        $month = $this->input->get('month');
    
        $expenses = $this->ExpenseModel->get_expenses_by_user($user_id, $year, $month);
    
        if (!$expenses) {
            echo json_encode(["error" => "No expenses found for the given period"]);
            return;
        }
    
        $filename = "expenses_{$year}_{$month}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ["Date", "Amount", "Category", "Description"]);
    
        foreach ($expenses as $expense) {
            fputcsv($output, [$expense['date'], $expense['amount'], $expense['category'], $expense['description']]);
        }
        fclose($output);
    }
    


        public function export_pdf() {
            $year = $this->input->get('year');
            $month = $this->input->get('month');
    
            $data['expenses'] = $this->ExpenseModel->get_expenses_by_month($year, $month);
            $data['year'] = $year;
            $data['month'] = $month;
    


            $html = $this->load->view('expenses_pdf', $data, true);
    
            $this->pdf->createPDF($html, "expenses_{$year}_{$month}", true);
        }



        public function import_csv() {
            $user_id = $this->authenticate_user();
            if (!$user_id) return;
        
            if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
                echo json_encode(["error" => "No file uploaded or file error"]);
                return;
            }
        
            $file = $_FILES['file']['tmp_name'];
            $handle = fopen($file, "r");

            if (!$handle) {
                echo json_encode(["error" => "Error opening file"]);
                return;
            }
        
            $imported_count = 0;
            fgetcsv($handle);
        
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 4) continue;
        
                $date = $data[0];
                $amount = $data[1];
                $category = $data[2];
                $description = $data[3];
        
                $category_id = $this->ExpenseModel->get_or_create_category_id($user_id, $category);
        
                if ($category_id) {
                    $expense_data = [
                        'user_id' => $user_id,
                        'amount' => $amount,
                        'category_id' => $category_id,
                        'description' => $description,
                        'created_at' => $date
                    ];
                    $this->ExpenseModel->insert_expense($expense_data);
                    $imported_count++;
                }
            }
            fclose($handle);
        
            echo json_encode(["message" => "Expenses imported successfully", "imported_count" => $imported_count]);
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
