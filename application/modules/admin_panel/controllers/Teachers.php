<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 29-01-2019
 * Time: 10:48
 */

class Teachers extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function attendance() {
        if($this->check_permission(array(1,3,6),46) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->attendance();
            $this->load->view($data['page'], $data['data']);
        }
    }
     public function view_attendance() {
        if($this->check_permission(array(1,3,6),46) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->view_attendance();
            $this->load->view($data['page'], $data['data']);
        }
    }
    public function ajax_search_attendance() {
        if($this->check_permission(array(1,3,6),46) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->ajax_search_attendance();
            $allstudent = $this->Teachers_m->getClassWiseStudent($this->input->post('class_id'));
          
            
            
            $response['html'] = '';
            if(!empty($data)){
                $i=0;
                foreach($data as $row){
                    $i++;
                    $attend_student = (count($allstudent) - $row->student_count);
                    $response['html'] .='<tr>
                        <td>'.$i.'</td>
                        <td>'.$row->Class_Name.'</td>
                        <td>'.$row->date.'</td>
                        <td>'.$attend_student.'</td>
                        <td><a class="btn btn-primary" type="btn btn-primary" href="'.base_url().'admin/edit_attendance/'.$row->att_hdr_id.'">Edit</a></td>
                    </tr>';
                }
            }
            echo json_encode($response);
        }
    }
    public function edit_attendance($id){
        if($this->check_permission(array(1,3,6),46) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->edit_attendance($id);
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function attendance_update(){
        $std_selected = $this->input->post('std');
        $att_hdr_id = $this->input->post('header_id'); 
        $this->load->model('Teachers_m');
        $this->Teachers_m->update_attendance();
    
       
    
        $this->session->set_flashdata('success', 'Attendance updated successfully.');
        redirect('admin/view_attendance');
    }

    public function ajax_update_std_attendance() {
        if($this->check_permission(array(1,2,3,6),null) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->ajax_update_std_attendance();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function form_attendance() {
        if($this->check_permission(array(1,3,6),46) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->form_attendance();
            redirect(base_url($data['page']));
        }
    }

    public function marks_entry() {
        if($this->check_permission(array(1,3,6),48) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->marks_entry();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_marks() {
        if($this->check_permission(array(1,3,6),48) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->add_marks();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function ajax_update_std_marks_table() {
        if($this->check_permission(array(1,3,6),null) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->ajax_update_std_marks_table();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function ajax_mark_type_on_subject(){
        if($this->check_permission(array(1,3,6),null) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->ajax_mark_type_on_subject();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function ajax_update_std_class_table() {
        if($this->check_permission(array(1,3,6),null) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->ajax_update_std_class_table();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function form_add_marks() {
        if($this->check_permission(array(1,3,6),48) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->form_add_marks();
            redirect(base_url($data['page']));
        }
    }

    public function edit_marks($hdr_id) {
        if($this->check_permission(array(1,3,6),48) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->edit_marks($hdr_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_edit_marks() {
        if($this->check_permission(array(1,3,6),48) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->form_edit_marks();
            redirect(base_url($data['page']));
        }
    }

    public function ajax_fetch_std_on_marks_entry() {
        if($this->check_permission(array(1,3,6),49) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->ajax_fetch_std_on_marks_entry();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
        }
    }

    public function progress_report_entry() {
        if($this->check_permission(array(1,3,6),49) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->progress_report_entry();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function fetch_student_on_class_sec() {
        if($this->check_permission(array(1,3,6),49) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->fetch_student_on_class_sec();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
        }
    }

    public function progress_report() {
        if($this->check_permission(array(1,3,6),49) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->progress_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_progress_report() {
        if($this->check_permission(array(1,3,6),49) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->print_progress_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function marksheet() {
        if($this->check_permission(array(1,3,6),50) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->marksheet();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function student_class_update() {
        if($this->check_permission(array(1,3,6),47) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->student_class_update();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_update_student_class() {
        if($this->check_permission(array(1,3,6),47) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->form_update_student_class();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_marksheet() {
        if($this->check_permission(array(1,3,6),50) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->print_marksheet();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function homework() {
        if($this->check_permission(array(1,3,6),51) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->homework();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function homework_edit() {
        if($this->check_permission(array(1,3,6),51) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->homework_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function notices() {
        if($this->check_permission(array(1,3,6),51) == true) {
            $this->load->model('Teachers_m');
            $data = $this->Teachers_m->notices();
            $this->load->view($data['page'], $data['data']);
        }
    }

}