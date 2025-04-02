<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {


    public function __construct() {
        parent::__construct();
		$this->load->helper('url');
        $this->load->library('JwtLib');


    }
    protected function authenticate() {

    $headers = $this->input->get_request_header('Authorization');
    // $headers = $this->session->userdata('jwt_token');
    if (!$headers) {
        echo json_encode(['status' => false, 'message' => 'Token is required']);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers);

    $decoded = $this->jwtlib->verify_jwt($token);

    if (!$decoded) {
        echo json_encode(['status' => false, 'message' => 'Invalid or expired token']);
        exit;
    }

    return;
}
}

?>
