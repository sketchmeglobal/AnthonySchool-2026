<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 14-01-2019
 * Time: 23:07
 */

class Reports extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function student_related_report() {
        if($this->check_permission(array(1,2,6),null) == true) {
            $data = array();

            $data['tab_title'] = 'Student Related Reports';
            $data['section_heading'] = 'Student Related Reports <small>(Print)</small>';
            $data['menu_name'] = 'Student Related Reports';

            $data['form_type'] = 'student_related_report';
            $this->load->view('Reports_v', $data);
        }
    }

    public function std_reg_report() {
        if($this->check_permission(array(1,2,6),6) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_reg_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_std_reg_report() {
        if($this->check_permission(array(1,2,6),6) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_reg_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function std_consc_report() {
        if($this->check_permission(array(1,2,6),7) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_consc_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_std_consc_report() {
        if($this->check_permission(array(1,2,6),7) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_consc_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function print_std_consc_report_2() {
        if($this->check_permission(array(1,2,6),7) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_consc_report_2();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function print_std_consc_report_3() {
        if($this->check_permission(array(1,2,6),7) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_consc_report_3();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_session_consc_report() {
        if($this->check_permission(array(1,2,6),7) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_session_consc_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_tran_report() {
        if($this->check_permission(array(1,2,6),11) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_tran_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_tran_report() {
        if($this->check_permission(array(1,2,6),11) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_tran_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_fees_type1_report() {
        if($this->check_permission(array(1,2,6),12) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_fees_type1_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_fees_type1_report() {
        if($this->check_permission(array(1,2,6),12) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_fees_type1_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_fees_type2_report() {
        if($this->check_permission(array(1,2,6),13) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_fees_type2_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_fees_type2_report() {
        if($this->check_permission(array(1,2,6),13) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_fees_type2_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function student_strength() {
        if($this->check_permission(array(1,2,6),9) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->student_strength();
            $this->load->view($data['page'], $data['data']);
        }
    }


    public function print_student_strength_report() {
        if($this->check_permission(array(1,2,6),9) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_student_strength_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function std_fees_ledger_report() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_fees_ledger_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_std_fees_ledger_report() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_fees_ledger_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function single_month_dues_report() {
        if($this->check_permission(array(1,2,6),14) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->single_month_dues_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_single_month_dues_report() {
        if($this->check_permission(array(1,2,6),14) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_single_month_dues_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_dues_report() {
        if($this->check_permission(array(1,2,6),15) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_dues_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_dues_report() {
        if($this->check_permission(array(1,2,6),15) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_dues_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function outstanding_total_report() {
        if($this->check_permission(array(1,2,6),15) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->outstanding_total_report();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
     public function print_outstanding_total_report() {
        if($this->check_permission(array(1,2,6),15) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_outstanding_total_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function payment_type_report() {
        if($this->check_permission(array(1,2,6),16) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->payment_type_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_payment_type_report() {
        if($this->check_permission(array(1,2,6),16) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_payment_type_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function library_register() {
        if($this->check_permission(array(1,2,6),17) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->library_register();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_library_register() {
        if($this->check_permission(array(1,2,6),17) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_library_register();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function books_register() {
        if($this->check_permission(array(1,2,6),18) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->books_register();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_books_register() {
        if($this->check_permission(array(1,2,6),18) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_books_register();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function class_routine() {
        if($this->check_permission(array(1,2,6),19) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->class_routine();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_class_routine() {
        if($this->check_permission(array(1,2,6),19) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_class_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function teacher_routine() {
        if($this->check_permission(array(1,2,6),20) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->teacher_routine();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_teacher_routine() {
        if($this->check_permission(array(1,2,6),20) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_teacher_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
     public function master_routine() {
        if($this->check_permission(array(1,2,6),20) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->master_routine();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_master_routine() {
        if($this->check_permission(array(1,2,6),21) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_master_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function student_list() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->student_list();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_student_list_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_student_list_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function print_student_category_list_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_student_category_list_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function ajax_fetch_classes_by_class_type() {
        if($this->check_permission() == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->ajax_fetch_classes_by_class_type(); 
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }
    
    public function student_rank_list() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_ranklist_report();
            $this->load->view($data['page'], $data['data']);
        }
    }
    public function class_subject_topper() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->class_subject_topper_report();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function due_undertaking_report() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_due_undertaking_report();
            $this->load->view($data['page'], $data['data']);
        }
    }
    public function print_due_undertaking_report() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_due_undertaking_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function print_ranklist_report() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_ranklist_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function print_class_subject_topper_report() {
        if($this->check_permission(array(1,2,6),8) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_class_subject_topper_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function get_studentlist_ajax(){
        $class = $this->input->post('undertaking_class');
        $this->load->model('Reports_m');
        $data = $this->Reports_m->get_studentlist_ajax($class);
        $response['html'] = '';
        if(!empty($data)){
            foreach($data as $row){
                $response['html'] .= '<option value="' . $row->STD_SEQ . '">' . $row->ST_FULL_NAME . '</option>';
            }
        }
        echo json_encode($response);
    }
    public function get_subjectlist_ajax(){
        $class = $this->input->post('rank_class');
        $this->load->model('Reports_m');
        $data = $this->Reports_m->get_subjectlist_ajax($class);
       $response['html'] = '<option value="All">All</option>';
        if(!empty($data)){
            foreach($data as $row){
                $response['html'] .= '<option value="' . $row->sub_id . '">' . $row->sub_name . '</option>';
            }
        }
        echo json_encode($response);
    }
    
    public function notice_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->notice_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function add_notice_report(){
        if($this->check_permission(array(1,2,6),10) == true) { 
            $this->load->model('Reports_m');
            $data = $this->Reports_m->add_notice_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function edit_notice_report($id){
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->edit_notice_report($id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
     public function delete_notice_report($id){
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->delete_notice_report($id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function submit_notice_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->submit_notice_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
     public function update_notice_report() {
        if($this->check_permission(array(1,2,6),10) == true) { 
            $this->load->model('Reports_m');
            $data = $this->Reports_m->update_notice_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
     public function print_notice_report($id) {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_notice_report($id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
     public function teacher_related_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->teacher_related_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
      public function print_teacher_related_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_teacher_related_report(); 
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    public function staff_leave_report() {
        if($this->check_permission(array(1,2,6),null) == true) {
            $data = array();

            $data['tab_title'] = 'Staff Leave Report';
            $data['section_heading'] = 'Staff Leave Report <small>(Print)</small>';
            $data['menu_name'] = 'Staff Leave Report';

            $data['form_type'] = 'staff_leave_report';
            $this->load->view('Reports_v', $data);
        }
    }
    public function print_staff_leave_report() {
        if($this->check_permission(array(1,2,6),10) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_staff_leave_report(); 
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    

}