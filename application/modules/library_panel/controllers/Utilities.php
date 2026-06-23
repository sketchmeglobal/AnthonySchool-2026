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
//    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }


    public function issue_books($hdr_id=null) {
        if($this->check_permission(array(1,5),null) == true) {
        
        //die();
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->issue_books($hdr_id);
            $this->load->view($data['page'], $data['data']);
        }
    }


    public function return_books() {
        if($this->check_permission(array(1,5),null) == true) {
        
        //die();
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->return_books();
            $this->load->view($data['page'], $data['data']);
        }
    }


    public function ajax_book_issue_table_data() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->ajax_book_issue_table_data();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function ajax_book_return_table_data() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->ajax_book_return_table_data();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function form_return_book_filter() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->form_return_book_filter();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function form_issue_book_filter()
    {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->form_issue_book_filter();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


   public function form_book_add_filter()
    {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->form_book_add_filter();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function add_book_issue_detail()
    {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->add_book_issue_detail();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function form_book_return()
    {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->form_book_return();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function add_book_issue_detail_for_multiple_student_lists()
    {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->add_book_issue_detail_for_multiple_student_lists();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


    public function add_issue_books($std_id='') {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->add_issue_books($std_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                
              
                redirect(base_url($data['page']));
            }
        }
    }


    public function get_fees_data() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->get_fees_data();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function form_fees_related_transfer() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->form_fees_related_transfer();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function ajax_delete_issued_book() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Utilities_m');
            $data = $this->Utilities_m->ajax_delete_issued_book();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }


}