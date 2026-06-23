<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-02-2019
 * Time: 19:39
 */

class Administrations extends My_Controller {

    private $user_type = null;
//    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function index() {
        redirect(base_url('admin/create_account'));
    }

    public function create_account() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->create_account();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_create_account() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->add_create_account();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function add_create_account_operator() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->add_create_account_operator();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } else {
                redirect(base_url($data['page']));
            }
        }
    }

    public function manage_users() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->manage_users();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function set_user_permissions($user_id) {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else {
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->set_user_permissions($user_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } else {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_set_user_permissions() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else {
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->form_set_user_permissions();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function student_control() {
        if($this->user_type != 1) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else {
            $this->load->model('Administrations_m');
            $data = $this->Administrations_m->student_control();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function permission_student_for_result()
    {
        if ($this->user_type != 1) {
            if ($this->input->is_ajax_request()) {
                echo "Unauthorized access.";
            } else {
                $this->session->set_flashdata('title', 'Log-in!');
                $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
                redirect(base_url('admin'));
            }
            return;
        }

        if ($this->input->post('search')) {
            $std_seq = $this->input->post('search');
            $student = $this->db->get_where('student_details', ['STD_SEQ' => $std_seq])->row();

            if ($student) {
                $formattedDob = date('dmY', strtotime($student->STD_DOB));
                $hashedPassword = hash('sha256', $formattedDob);

                $data = [
                    'usertype' => 4,
                    'tbl_id' => $student->STD_SEQ,
                    'username' => $student->STD_REGNO,
                    'pass' => $hashedPassword,
                    'verified' => 1,
                    'registration_date' => date('Y-m-d H:i:s')
                ];
                $exist = $this->db->get_where('users', ['tbl_id' => $student->STD_SEQ, 'usertype' => 4])->row();
                if (!$exist) {
                    $this->db->insert('users', $data);
                    echo "Student added successfully!";
                } else {
                    echo "Student already exists.";
                }
            } else {
                echo "Student not found.";
            }
            return;
        }

        $this->load->model('Administrations_m');
        $data = $this->Administrations_m->student_control();
        $this->load->view($data['page'], $data['data']);
    }

    public function ajax_fetch_students_by_class() {
        $this->load->model('Administrations_m');
        $data = $this->Administrations_m->ajax_fetch_students_by_class();
        echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
        exit();
    }

    public function form_student_control() {
        $this->load->model('Administrations_m');
        $data = $this->Administrations_m->form_student_control();
        redirect(base_url($data['page']));
    }
    public function update_fees_month(){
        //update fees month
        if($this->input->post('submit') == 'submit_months'){
            $month = $this->input->post('month');
            $this->db->where('id',1);
            $this->db->update('settings',array("student_due_fees_month"=>$month));
             $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Month update successfully');
            redirect(base_url('admin/student_control'));
        }
    }

}