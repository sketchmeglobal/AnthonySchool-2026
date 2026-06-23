<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-02-2019
 * Time: 16:53
 */

class Masters extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function books() {
        if($this->check_permission(array(1,5),1) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->books();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function fine() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->fine();
            $this->load->view($data['page'], $data['data']);
        }
    }
    public function books_edit() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->books_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function class_subject() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->class_subject();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function class_subject_edit() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->class_subject_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

}