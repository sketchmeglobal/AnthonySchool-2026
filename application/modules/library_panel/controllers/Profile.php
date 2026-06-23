<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

class Profile extends My_Controller {

    private $user_type = null;
//    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        if(isset($this->session->userdata['libr']['user_id'])) { //if logged-in
            $this->user_type = $this->session->userdata['libr']['usertype'];
        }
    }

    public function index() {
        redirect(base_url('library/profile'));
    }

    public function profile() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('library'));
        } else { //if library already logged-in
            $this->load->model('Profile_m');
            $data = $this->Profile_m->profile();
            $this->load->view($data['page'], $data['data']);
        }
    } // /.profile

    public function form_basic_info() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('library'));
        } else { //if library already logged-in
            $this->load->model('Profile_m');
            $this->Profile_m->form_basic_info();
        }
    } // /.form_basic_info

    public function form_change_pass() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('library'));
        } else { //if library already logged-in
            $this->load->model('Profile_m');
            $this->Profile_m->form_change_pass();
        }
    } // /.form_change_pass

    public function form_change_email() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('library'));
        } else { //if library already logged-in
            $this->load->model('Profile_m');
            $this->Profile_m->form_change_email();
        }
    } // /.form_change_email

    public function change_email($verification_key) {
        $this->load->model('Profile_m');
        $this->Profile_m->change_email($verification_key);
    } // /.change_email

    public function ajax_username_check() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('library'));
        } else { //if library already logged-in
            $this->load->model('Profile_m');
            $this->Profile_m->ajax_username_check();
        }
    } // /.ajax_username_check

    public function form_change_username() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('library'));
        } else { //if library already logged-in
            $this->load->model('Profile_m');
            $this->Profile_m->form_change_username();
        }
    } // /.form_change_username

} // /.Profile() controller