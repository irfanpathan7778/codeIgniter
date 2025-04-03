<?php 
date_default_timezone_set('Asia/Kolkata');  // Change to your required timezone  

class ReportController extends CI_Controller {



    public function __construct() {
        parent::__construct();
        $this->load->library('JwtLib');
        $this->load->model('ExpenseModel');
        $this->load->library('Pdf');

        // $this->authenticate();
    }


    public function monthly_report() {
        $this->load->view('monthly_report');

    }

    public function get_monthly_report() {
        $user_id = $this->authenticate_user();

        $year = $this->input->get('year');
        $month = $this->input->get('month');
        // $user_id = $this->session->userdata('user_id');
    
        if (!$year || !$month) {
            echo json_encode(["error" => "Year and month are required."]);
            return;
        }
    
        $this->load->model('ExpenseModel');
        $report = $this->ExpenseModel->get_monthly_report($user_id, $year, $month);
    
        header('Content-Type: application/json');
        echo json_encode($report);
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