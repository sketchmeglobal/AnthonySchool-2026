<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 13-02-2019
 * Time: 12:09
 */

class Student_Single extends My_Controller {

    private $user_type = null;
//    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function index() {
        redirect(base_url('admin/student_homework'));
    }

    public function student_exam() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else {
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->student_exam();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function exam_answer_save() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else {
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->exam_answer_save();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function mcq_exam_answer_save() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else {
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->mcq_exam_answer_save();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function student_homework() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->student_homework();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function student_homework_details($hw_id) {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->student_homework_details($hw_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function student_library() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->student_library();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function my_routine() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->my_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function my_details() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->my_details();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function parent_details() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->parent_details();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function my_dues() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->my_dues();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function monthly_pay_hist() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->monthly_pay_hist();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function yearly_pay_hist() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->yearly_pay_hist();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function admission_pay_hist() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->admission_pay_hist();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function my_progress() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->my_progress();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function my_progress_report() {
        if($this->user_type != 4) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Student_Single_m');
            $data = $this->Student_Single_m->my_progress_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

}