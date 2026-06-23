<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 14-01-2019
 * Time: 23:07
 */

class Utilities extends My_Controller {

    private $user_type = null;
    
    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function fees_related_transfer() {
        if($this->check_permission(array(1,6),64) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->fees_related_transfer();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function get_fees_data() {
        if($this->check_permission(array(1,6),64) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->get_fees_data();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function form_fees_related_transfer() {
        if($this->check_permission(array(1,6),64) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->form_fees_related_transfer();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function activity_locks() {
        if($this->check_permission(array(1,6),64) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->activity_locks();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function activity_exception_users($lock_activity) {
        if($this->check_permission(array(1,6),64) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->activity_exception_users($lock_activity);
            $this->load->view($data['page'], $data['data']);
        }
    }

}