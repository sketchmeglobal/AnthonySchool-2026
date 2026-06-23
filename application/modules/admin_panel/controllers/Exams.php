<?php
/**
 * Coded by: Pran Krishna Das
 * Website: https://pran.dev
 * CI: 3.0.6
 * Date: 23-03-2021
 * Time: 12:10
 */

class Exams extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function exam_time_setup() {
        if($this->check_permission(array(1,3,6),53) == true) {
            $this->load->model('Exams_m');
            $data = $this->Exams_m->exam_time_setup();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function ques_setup() {
        if($this->check_permission(array(1,3,6),54) == true) {
            $this->load->model('Exams_m');
            $data = $this->Exams_m->ques_setup();
            $this->load->view($data['page'], $data['data']);
        }
    }

    Public function exam_answers() {
        if($this->check_permission(array(1,3,6),55) == true) {
            $this->load->model('Exams_m');
            $data = $this->Exams_m->exam_answers();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function mcq_ques_setup() {
        if($this->check_permission(array(1,3,6),56) == true) {
            $this->load->model('Exams_m');
            $data = $this->Exams_m->mcq_ques_setup();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function mcq_questions($mcq_qs_id) {
        if($this->check_permission(array(1,3,6),56) == true) {
            $this->load->model('Exams_m');
            $data = $this->Exams_m->mcq_questions($mcq_qs_id);
            $this->load->view($data['page'], $data['data']);
        }
    }

    Public function mcq_exam_answers() {
        if($this->check_permission(array(1,3,6),57) == true) {
            $this->load->model('Exams_m');
            $data = $this->Exams_m->mcq_exam_answers();
            $this->load->view($data['page'], $data['data']);
        }
    }

}