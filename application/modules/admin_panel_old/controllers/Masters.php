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

    public function database_backup() {
        if($this->check_permission(array(1),null) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->database_backup();
        }
    }

    public function account_group() {
        if($this->check_permission(array(1,2,6),22) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->account_group();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function account_group_edit() {
        if($this->check_permission(array(1,2,6),22) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->account_group_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function account_master() {
        if($this->check_permission(array(1,2,6),23) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->account_master();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function account_master_edit() {
        if($this->check_permission(array(1,2,6),23) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->account_master_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function class_section() {
        if($this->check_permission(array(1,2,6),24) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->class_section();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function class_section_edit() {
        if($this->check_permission(array(1,2,6),24) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->class_section_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function edit_class_fess($class_id) {
        if($this->check_permission(array(1,2,6),24) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->edit_class_fess($class_id);
            $this->load->view($data['page'], $data['data']);
        }
    }


    public function class_sec_fees_edit() {
        if($this->check_permission(array(1,2,6),24) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->class_sec_fees_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function subjects() {
        if($this->check_permission(array(1,2,6),31) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->subjects();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function subjects_edit() {
        if($this->check_permission(array(1,2,6),31) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->subjects_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function cls_sub_setup() {
        if($this->check_permission(array(1,2,6),27) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->cls_sub_setup();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function cls_sub_setup_edit() {
        if($this->check_permission(array(1,2,6),27) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->cls_sub_setup_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function copy_subjects() {
        if($this->check_permission(array(1,2,6),27) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->copy_subjects();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function form_copy_subjects() {
        if($this->check_permission(array(1,2,6),27) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->form_copy_subjects();
            redirect(base_url($data['page']));
        }
    }

    public function books() {
        if($this->check_permission(array(1,2,6),33) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->books();
            $this->load->view($data['page'], $data['data']);
        }
    }
    public function books_edit() {
        if($this->check_permission(array(1,2,6),33) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->books_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function exam_master() {
        if($this->check_permission(array(1,2,6),32) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->exam_master();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function exam_master_edit() {
        if($this->check_permission(array(1,2,6),32) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->exam_master_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function exam_terms() {
        if($this->check_permission(array(1,2,6),32) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->exam_terms();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function grade_setup() {
        if($this->check_permission(array(1,2,6),34) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->grade_setup();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function grade_setup_edit() {
        if($this->check_permission(array(1,2,6),34) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->grade_setup_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function teachers() {
        if($this->check_permission(array(1,2,6),30) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->teachers();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function teachers_edit() {
        if($this->check_permission(array(1,2,6),30) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->teachers_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function teachers_delete($id){
       if($this->check_permission(array(1,2,6),30) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->teachers_delete($id);
            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Teacher deleted successfully.');
            redirect(base_url('admin/teachers'));
        } 
    }

    public function employees() {
        if($this->check_permission(array(1,2,6),35) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->employees();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function employees_edit() {
        if($this->check_permission(array(1,2,6),35) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->employees_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function signatures() {
        if($this->check_permission(array(1,2,6),35) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->signatures();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function signatures_edit() {
        if($this->check_permission(array(1,2,6),35) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->signatures_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    /* Transfar Function From Accounts Controller to Master */
    public function class_fees() {
        if($this->check_permission(array(1,2,6),25) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->class_fees();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function copy_fees() {
        if($this->check_permission(array(1,2,6),26) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->copy_fees();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function form_copy_fees() {
        if($this->check_permission(array(1,2,6),26) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->form_copy_fees();
            redirect(base_url($data['page']));
        }
    }

    public function concession_fees() {
        if($this->check_permission(array(1,2,6),29) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->concession_fees();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_concession_fees($std_id, $val) {
        if($this->check_permission(array(1,2,6),29) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->add_concession_fees($std_id, $val);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_add_concession_fees() {
        if($this->check_permission(array(1,2,6),29) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->form_add_concession_fees();
            redirect(base_url($data['page']));
        }
    }

    public function edit_concession_fees($std_id, $val='') {
        if($this->check_permission(array(1,2,6),29) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->edit_concession_fees($std_id, $val);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_edit_concession_fees() {
        if($this->check_permission(array(1,2,6),29) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->form_edit_concession_fees();
            redirect(base_url($data['page']));
        }
    }

    public function delete_concession_fees($std_id) {
        if($this->check_permission(array(1,2,6),29) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->delete_concession_fees($std_id);
            redirect(base_url($data['page']));
        }
    }
    /*------------------------------------------------------*/


    /**/
    public function student_details() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->student_details();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function student_details_edit() {

        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $data = $this->Masters_m->student_details_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }
    /*-----------------------------------------------*/
    
    public function pot_pow_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"PoT & PoW Update",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function ajax_get_potpow_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_potpow_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="PoT" ' . ($std['PoT_PoW'] == 'PoT' ? 'checked' : '') . '> PoT
                            </label>
                            <label>
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="PoW" ' . ($std['PoT_PoW'] == 'PoW' ? 'checked' : '') . '> PoW
                            </label>
                            <label>
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="null" > NA
                            </label>
                        </div>
                    </div>
                ';
             }
         }
         echo json_encode($response);
    }
    public function update_pot_pow_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('pot_pow_status_' . $student_id);
                
                if ($status) {
                    $response_status[] = $status;
                    $this->Masters_m->update_student_status($student_id, $status);
                }
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    
    public function second_language_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"2nd Language Update",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
     public function ajax_second_language_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_second_language_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        ';
                        if($this->input->post('class_id') == 22 || $this->input->post('class_id') == 23 || $this->input->post('class_id') == 24 || $this->input->post('class_id') == 25){
                          $response['html'] .= '<div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="second_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="HINDI, ENG" ' . ($std['STD_SECOND_LANG'] == 'HINDI, ENG' ? 'checked' : '') . '> HINDI, ENG
                            </label>
                            <label>
                                <input type="radio" name="second_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="ENG, BENG" ' . ($std['STD_SECOND_LANG'] == 'ENG, BENG' ? 'checked' : '') . '> ENG, BENG
                            </label>
                            <label>
                                <input type="radio" name="second_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="null" > NA
                            </label>
                        </div>';    
                        }else{
                           $response['html'] .= '<div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="second_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Bengali" ' . ($std['STD_SECOND_LANG'] == 'Bengali' ? 'checked' : '') . '> Bengali
                            </label>
                            <label>
                                <input type="radio" name="second_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Hindi" ' . ($std['STD_SECOND_LANG'] == 'Hindi' ? 'checked' : '') . '> Hindi
                            </label>
                            <label>
                                <input type="radio" name="second_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="null" > NA
                            </label>
                        </div>'; 
                        }
                      $response['html'] .= '  
                    </div>
                ';
             }
         }
         echo json_encode($response);
    }
     public function update_second_language_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('second_language_status_' . $student_id);
                
                if ($status) {
                    $response_status[] = $status;
                    $this->Masters_m->update_student_second_langstatus($student_id, $status);
                }
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    
      public function third_language_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"3rd Language Update",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
     public function ajax_third_language_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_third_language_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="third_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Bengali" ' . ($std['STD_THIRD_LANG'] == 'Bengali' ? 'checked' : '') . '> Bengali
                            </label>
                            <label>
                                <input type="radio" name="third_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Hindi" ' . ($std['STD_THIRD_LANG'] == 'Hindi' ? 'checked' : '') . '> Hindi
                            </label>
                            <label>
                                <input type="radio" name="third_language_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="null" > NA
                            </label>
                        </div>
                    </div>
                ';
             }
         }
         echo json_encode($response);
    }
     public function update_third_language_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('third_language_status_' . $student_id);
                
                if ($status) {
                    $response_status[] = $status;
                    $this->Masters_m->update_student_third_langstatus($student_id, $status);
                }
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    public function concession_fees_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Concession Fees Update",
                "class"=>$cls
                ); 
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function ajax_concession_fees_students(){ 
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_concession_fees_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){ 
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div style="width:400px">
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <input type="text" name="actual_fees_' . htmlspecialchars($std['STD_SEQ']) . '" value="'.$std['actual_fees'].'" disabled />
                            
                        </div>
                        <div>
                            <input type="number" name="concession_fees_' . htmlspecialchars($std['STD_SEQ']) . '" value="'.$std['concession_fees'].'" max="'.$std['actual_fees'].'" />
                            
                        </div>
                    </div>
                ';
             }
         }
         if($this->input->post('class_id') == ''){
             $response['class'] = $this->Masters_m->getClassByRegistration($this->input->post('reg_no'));
         }else{
             $response['class'] = $this->input->post('class_id');
         }
         echo json_encode($response);
    }
    public function update_concession_fees_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $class = $this->input->post('from_class');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('concession_fees_' . $student_id);
                
                //if ($status) {
                    $response_status[] = $status;
                    $this->Masters_m->update_student_concession_fees($student_id, $status, $class); 
                //}
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    public function mobile_no_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Mobile Number Update",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function ajax_mobileno_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_mobileno_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <input type="text" name="mobile_no_' . htmlspecialchars($std['STD_SEQ']) . '" value="'.$std['STD_PH_NO'].'" />
                            
                        </div>
                    </div>
                ';
             }
         }
         if($this->input->post('class_id') == ''){
             $response['class'] = $this->Masters_m->getClassByRegistration($this->input->post('reg_no'));
         }else{
             $response['class'] = $this->input->post('class_id');
         }
         echo json_encode($response);
    }
    
     public function update_mobileno_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('mobile_no_' . $student_id);
                
                if ($status) {
                    $response_status[] = $status;
                    $this->Masters_m->update_student_mobile_no($student_id, $status);
                }
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    
    public function aadhar_id_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Aadhar ID Update",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function ajax_aadharno_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_aadharno_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <input type="text" name="aadhar_no_' . htmlspecialchars($std['STD_SEQ']) . '" value="'.$std['aadhaar_id'].'" />
                            
                        </div>
                    </div>
                ';
             }
         }
         if($this->input->post('class_id') == ''){
             $response['class'] = $this->Masters_m->getClassByRegistration($this->input->post('reg_no'));
         }else{
             $response['class'] = $this->input->post('class_id');
         }
         echo json_encode($response);
    }
    public function update_aadharno_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('aadhar_no_' . $student_id);
                
               
                    $response_status[] = $status;
                    $this->Masters_m->update_student_aadhar_no($student_id, $status);
              
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    
    public function shiksha_id_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Bangla Shiksha ID Update",  
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function ajax_shiksha_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_shiksha_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <input type="text" name="shiksha_id_' . htmlspecialchars($std['STD_SEQ']) . '" value="'.$std['banglar_shiksha_id'].'" />
                            
                        </div>
                    </div>
                ';
             }
         }
         if($this->input->post('class_id') == ''){
             $response['class'] = $this->Masters_m->getClassByRegistration($this->input->post('reg_no'));
         }else{
             $response['class'] = $this->input->post('class_id');
         }
         echo json_encode($response);
    }
    public function update_shiksha_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('shiksha_id_' . $student_id);
                
               
                    $response_status[] = $status;
                    $this->Masters_m->update_shiksha_no($student_id, $status);
              
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    
     public function house_update() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $this->db->order_by('class_order', 'ASC');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"House Update",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
     public function ajax_house_update_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_second_language_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        ';
                     
                           $response['html'] .= '<div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="house_update_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Red" ' . ($std['STD_HOUSE'] == 'Red' ? 'checked' : '') . '> Red
                            </label>
                            <label>
                                <input type="radio" name="house_update_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Green" ' . ($std['STD_HOUSE'] == 'Green' ? 'checked' : '') . '> Green
                            </label>
                            <label>
                                <input type="radio" name="house_update_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Yellow" ' . ($std['STD_HOUSE'] == 'Yellow' ? 'checked' : '') . '> Yellow
                            </label>
                             <label>
                                <input type="radio" name="house_update_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="Blue" ' . ($std['STD_HOUSE'] == 'Blue' ? 'checked' : '') . '> Blue
                            </label>
                             <label>
                                <input type="radio" name="house_update_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="NULL" > NA
                            </label>
                        </div>'; 
                        
                      $response['html'] .= '  
                    </div>
                ';
             }
         }
         echo json_encode($response);
    }
     public function update_house_update_form(){
        $this->load->model('Masters_m');
        $student_ids = $this->input->post('student_id');
        $response_status = [];
        
        if (!empty($student_ids)) {
            foreach ($student_ids as $index => $student_id) {
                $status = $this->input->post('house_update_status_' . $student_id);
                
                if ($status) {
                    $response_status[] = $status;
                    $this->Masters_m->update_student_housestatus($student_id, $status);
                }
            }
            
            $previous_url = $_SERVER['HTTP_REFERER'];
            redirect($previous_url);
        } 
    }
    
    public function copy_student() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Copy Student",
                "class"=>$cls
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function ajax_get_cpy_std_class(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_get_cpy_std_class();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="PoT" ' . ($std['PoT_PoW'] == 'PoT' ? 'checked' : '') . '> PoT
                            </label>
                            <label>
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="PoW" ' . ($std['PoT_PoW'] == 'PoW' ? 'checked' : '') . '> PoW
                            </label>
                            
                        </div>
                    </div>
                ';
             }
         }
         echo json_encode($response);
    }
     public function ajax_get_cpysession_students(){
         $this->load->model('Masters_m');
         $students = $this->Masters_m->ajax_get_cpysession_students();
         $response['html'] = '';
         if(!empty($students)){
             foreach($students as $std){
                  $response['html'] .= '
                    <input type="hidden" name="student_id[]" value="'.$std['STD_SEQ'].'" />
                     <div class="student-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;">
                        <div>
                            <span><strong>Roll No:</strong> ' . htmlspecialchars($std['STD_ROLLNO']) . '</span>
                            <span style="margin-left: 20px;">' . htmlspecialchars($std['ST_FULL_NAME']) . '</span>
                        </div>
                        <div>
                            <label style="margin-right: 10px;">
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="PoT" ' . ($std['PoT_PoW'] == 'PoT' ? 'checked' : '') . '> PoT
                            </label>
                            <label>
                                <input type="radio" name="pot_pow_status_' . htmlspecialchars($std['STD_SEQ']) . '" value="PoW" ' . ($std['PoT_PoW'] == 'PoW' ? 'checked' : '') . '> PoW
                            </label>
                        </div>
                    </div>
                ';
             }
         }
         echo json_encode($response);
    }
    
    public function staff_leave_record() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $staff = $this->db->get('teacher')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Staff Leave Record",
                "staff"=>$staff
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    public function add_staff_leave_record() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Masters_m');
            $staff = $this->db->get('teacher')->result_array();
            $data['page'] = 'common_master';
            $data['data'] = array(
                "menu_name"=>"Add Staff Leave Record",
                "staff"=>$staff
                );
            $this->load->view($data['page'],$data['data']);
        }
    }
    
    public function submit_staff_leave(){
        $this->load->model('Masters_m');
        $this->Masters_m->submit_staff_leave();
            
        $previous_url = $_SERVER['HTTP_REFERER'];
        redirect('admin/staff_leave_record');
    
    }
    
    public function ajax_search_leave_record(){
       $this->load->model('Masters_m');
       $records = $this->Masters_m->ajax_search_leave_record();
       $response['html'] = '';
       if(!empty($records)){
           $i=0;
           foreach($records as $row){
               $i++;
               $response['html'] .='<tr>
                <td>'.$i.'</td>
                <td>'.$row->TCH_NAME.'</td>
                <td>'.$row->leave_category.'</td>
                <td>'.date('d-M-Y',strtotime($row->from_date)).'</td>
                <td>'.date('d-M-Y',strtotime($row->to_date)).'</td>
                <td><a type="button" class="btn btn-danger" href="'.base_url().'admin/delete_staff_leave_record/'.$row->id.'">Delete</a></td>  
               </tr>';
           }
       }
       echo json_encode($response);
    }
    public function delete_staff_leave_record($id){
        $this->db->where('id',$id);
        $this->db->delete('staff_leave');
        $previous_url = $_SERVER['HTTP_REFERER'];
        redirect($previous_url);
    }
    

}