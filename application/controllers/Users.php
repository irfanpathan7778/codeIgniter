<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Users_model');
		$this->load->helper('url');
        $this->load->library(['JwtLib','session']);


    }

    public function save() {
        header('Content-Type: application/json');
    
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');
    
        if ($password !== $confirm_password) {
            echo json_encode(['status' => false, 'message' => 'Passwords do not match!']);
            return;
        }
    
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
        $this->load->model('Users_model');
        $existing_user = $this->Users_model->get_user_by_email($email);
        if ($existing_user) {
            echo json_encode(['status' => false, 'message' => 'Email already exists!']);
            return;
        }
    
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashed_password
        ];
    
        
        if ($this->Users_model->insert_user($data)) {

            $this->load->library('JwtLib');
            $payload = [
                'id' => $this->db->insert_id(),
                'email' => $email,
                'exp' => time() + 86400
            ];
            $token = $this->jwtlib->encode($payload);
            $data = (array) $this->jwtlib->verify_jwt($token);

    
            echo json_encode(['status' => true, 'message' => 'User registered successfully!', 'token' => $token, 'user_id' => $data['id']]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Registration failed!']);
        }
    }
    

    public function login_form() {
        $this->load->view('login'); 
    }

    public function login() {
        header('Content-Type: application/json');
    
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        
        $this->load->model('Users_model');
        
        $user = $this->Users_model->get_user_by_email($email);
        if (!$user) {
            echo json_encode(['status' => false, 'message' => 'Invalid email or password!']);
            return;
        }
    
        if (!password_verify($password, $user->password)) {
            echo json_encode(['status' => false, 'message' => 'Invalid email or password!']);
            return;
        }
    
        $this->load->library('JwtLib');
        
        $payload = [
            'id' => $user->id,
            'email' => $user->email,
            'exp' => time() + 86400
        ];
    
        $token = $this->jwtlib->encode($payload);
        $this->session->set_userdata('jwt_token', $token);

    
        echo json_encode([
            'status' => true,
            'message' => 'Login successful!',
            'token' => $token,
        ]);

        // echo json_encode(['status' => true, 'message' => 'Login successful!', 'token' => $token,'userId' => $user->id]);
        // redirect('dashboard');
    }
    
    public function logout() {
        $this->session->unset_userdata('jwt_token');
        echo json_encode(['status' => true, 'message' => 'User Logged out successfully!']);
    }
    
}
?>
