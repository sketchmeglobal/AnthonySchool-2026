<?php
/**
 * Coded by: Pran Krishna Das
 * Social: https://sketchmeglobal.com
 * CI: 3.0.6
 * Date: 29-01-2019
 * Time: 10:49
 */

class Teachers_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function attendance() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1))->result_array();
        $data['form_type'] = 'attendance';

        $data['tab_title'] = 'Class Attendance';
        $data['section_heading'] = 'Class Attendance <small>(Add)</small>';
        $data['menu_name'] = 'Class Attendance';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }
    public function view_attendance() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1))->result_array();
        $data['form_type'] = 'view_attendance';

        $data['tab_title'] = 'View Class Attendance';
        $data['section_heading'] = 'Class Attendance <small>(View)</small>';
        $data['menu_name'] = 'View Class Attendance';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }
    
    public function ajax_search_attendance(){
        $class_id = $this->input->post('class_id');
        $att_date = $this->input->post('att_date');
        
        $this->db->select('ah.att_hdr_id,csh.Class_Name, ah.date,COUNT(ad.STD_SEQ) as student_count');
        $this->db->from('attendance_hdr ah');
        $this->db->join('class_sec_hdr csh', 'ah.CS_SEQ = csh.CS_SEQ');
        $this->db->join('attendance_dtl ad', 'ah.att_hdr_id = ad.att_hdr_id');
        $this->db->where('ah.CS_SEQ', $class_id);
        if($att_date !== ''){
            $this->db->where('DATE(ah.date)', $att_date);
        }
        $this->db->group_by('csh.Class_Name,ah.att_hdr_id,ah.date');
    
        $query = $this->db->get();
        return $query->result();
    }
    
    public function edit_attendance($id){
        $this->db->where('att_hdr_id',$id);
        $hdrqry = $this->db->get('attendance_hdr');
        $hdrdata = $hdrqry->row();
        $class = $hdrdata->CS_SEQ;
        
        $this->db->where('att_hdr_id',$id);
        $query = $this->db->get('attendance_dtl');
        $attdata = $query->result();
        
        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where("STD_LEFT", 0);
        $this->db->where("STD_STATUS", 0);
        $this->db->where('STD_CS_SEQ', $class);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();
        
        $data['attdata'] = $attdata;
        $data['std'] = $std;
        $data['hdr_id'] = $id;
        $data['form_type'] = 'edit_attendance';

        $data['tab_title'] = 'Edit Class Attendance';
        $data['section_heading'] = 'Class Attendance <small>(Edit)</small>';
        $data['menu_name'] = 'Edit Class Attendance';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }

    public function ajax_update_std_attendance() {
        $class_id = $this->input->post('class_id');

        $this->db->where('CS_SEQ', $class_id);
        $this->db->where("date(`date`)", date('Y-m-d'));
        $row_attnd = $this->db->get('attendance_hdr')->row();
        //if attendance already taken
        if(count((array)$row_attnd) > 0) {
            $array['html_std'] = "<h1 class='text-center text-primary'><strong>Attendance for that class already taken!</strong></h1>";
            return $array;
        }

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where("STD_LEFT", 0);
        $this->db->where("STD_STATUS", 0);
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html_std = '';
        //creating individual student table row
        foreach($std as $s) {
            $html_std .= <<<EOD
<label class="checkbox-custom check-success col-lg-4">
    <input checked value="{$s['STD_SEQ']}" name="std[]" id="std_{$s['STD_SEQ']}" type="checkbox" class="selected_std_attendance">
    <label for="std_{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</label>
</label>
EOD;
        }

        $array['html_std'] = $html_std;
        return $array;
    }

    public function getClassWiseStudent($class){
        $this->db->select('STD_SEQ');
        $this->db->where("STD_CS_SEQ", $class);
        $this->db->where('STD_LEFT ', 0);
        $this->db->where("STD_STATUS", 0);
        return $this->db->get('student_details')->result_array();
    }
    public function update_attendance(){
        $std_present = $this->input->post('std[]');
        $att_hdr_id = $this->input->post('header_id');
        // echo $att_hdr_id;
        // die;
        $this->db->where('att_hdr_id',$att_hdr_id);
        $query = $this->db->get('attendance_hdr');
        $hdr_row = $query->row();
        $class = $hdr_row->CS_SEQ;
        
        $this->db->select('STD_SEQ');
        $this->db->where("STD_CS_SEQ", $class);
        $this->db->where('STD_LEFT ', 0);
        $this->db->where("STD_STATUS", 0);
        $std_all = $this->db->get('student_details')->result_array(); 
        $std_all = array_column($std_all, 'STD_SEQ');
        
        $this->db->where('att_hdr_id', $att_hdr_id);
        $this->db->delete('attendance_dtl');
        
        $std_absent = array_diff($std_all, $std_present);
        // echo "<pre>";
        // print_r($std_absent);
        // die;
    
        foreach($std_absent as $val) {
            $data_insert_dtl = array(
                "att_hdr_id" => $att_hdr_id,
                "STD_SEQ" => $val,
            );
            $this->db->insert('attendance_dtl', $data_insert_dtl);
        }
        return;
    }
    public function form_attendance() {
        //if form not submitted
        if($this->input->post('submit') != 'submit_attendance_form') {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/attendance');
        }

        $class = $this->input->post('class');
        $std_present = $this->input->post('std[]');

        $this->db->where('CS_SEQ', $class);
        $cls = $this->db->get('class_sec_hdr')->row();
        //if class does not exists
        if (count((array)$cls) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Class not found.');
            return array('page' => 'admin/attendance');
        }
        //if no student selected
        if (count($std_present) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Zzz!');
            $this->session->set_flashdata('msg', 'No Student Selected.');
            return array('page' => 'admin/attendance');
        }

        $this->db->select('STD_SEQ');
        $this->db->where("STD_CS_SEQ", $class);
        $this->db->where('STD_LEFT ', 0);
        $this->db->where("STD_STATUS", 0);
        $std_all = $this->db->get('student_details')->result_array();
        $std_all = array_column($std_all, 'STD_SEQ');
        // echo "<pre>";
        // print_r($std_all);
        // die;

        $std_absent = array_diff($std_all, $std_present);

        $data_insert_hdr = array(
            "CS_SEQ" => $class,
            "user_id" => $this->session->user_id,
            "date" => $this->input->post('attendance_date'),
        );
        $this->db->insert('attendance_hdr', $data_insert_hdr);
        $att_hdr_id = $this->db->insert_id();

        foreach($std_absent as $val) {
            $data_insert_dtl = array(
                "att_hdr_id" => $att_hdr_id,
                "STD_SEQ" => $val,
            );
            $this->db->insert('attendance_dtl', $data_insert_dtl);
        }

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('title', 'Great!');
        $this->session->set_flashdata('msg', 'Attendance saved successfully.');
        return array('page' => 'admin/attendance');
    }

    // public function marks_entry() {
    //     try{

    //         $session_user = $this->session->user_id;
    //         $session_user_type = $this->session->usertype;
    //         //echo $session_user;
    //         $crud = new grocery_CRUD();
    //         $crud->set_crud_url_path(base_url('admin_panel/Teachers/marks_entry'));
    //         $crud->set_theme('flexigrid');
    //         $crud->set_subject('Marks');
    //         $crud->order_by('MH_CLASS_SEQ', 'ASC');
    //         $crud->set_table('marks_hdr');

    //         $crud->unset_export();
    //         $crud->unset_print();
    //         $crud->unset_clone();
    //         $crud->unset_read();
    //         $crud->unset_add();
    //         $crud->unset_edit();

    //         if($session_user_type != 1){
    //             $crud->unset_delete();
    //         }
            
    //         if ($session_user_type == 6) {
                
    //             $crud->where('MH_USER_ID', $session_user);
    //         }
            
    //         $crud->callback_before_delete(array($this,'marks_dtl_delete'));

    //         $crud->columns('MH_USER_ID','MH_TERM_SEQ','MH_TEST_SEQ','MH_CLASS_SEQ','MH_SUB_SEQ');

    //         $crud->display_as('MH_TERM_SEQ', 'Exam Term');
    //         $crud->display_as('MH_CLASS_SEQ', 'Class & Section');
    //         $crud->display_as('MH_TEST_SEQ', 'Exam Name');
    //         $crud->display_as('MH_SUB_SEQ', 'Subject Name');

    //         $crud->set_relation('MH_TERM_SEQ', 'exam_terms', 'term_title');
    //         $crud->set_relation('MH_CLASS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
    //         $crud->set_relation('MH_TEST_SEQ', 'exam_test', 'Exam_Name');
    //         $crud->set_relation('MH_SUB_SEQ', 'subject', 'sub_name');

    //         $crud->add_action('Edit', base_url().'assets/grocery_crud/themes/flexigrid/css/images/edit.png', 'admin/edit_marks');
            
    //         $output = $crud->render();
    //         //rending extra value to $output
    //         $output->tab_title = 'Marks Entry';
    //         $output->section_heading = 'Marks Entry <small>(Add / Edit)</small>';
    //         $output->menu_name = 'Marks Entry';
    //         $output->add_button = '<a href="'.base_url().'admin/add_marks" class="btn btn-success" role="button"><span class="fa fa-percent"></span> Entry Marks</a>';

    //         return array('page'=>'common_v', 'data'=>$output); //loading common view page
    //     } catch(Exception $e) {
    //         show_error($e->getMessage().' --- '.$e->getTraceAsString());
    //     }
    // }
    
    
public function marks_entry() {
    try{
        $session_user = $this->session->user_id;
        $session_user_type = $this->session->usertype;
        //echo $session_user;
        $crud = new grocery_CRUD();
        $crud->set_crud_url_path(base_url('admin_panel/Teachers/marks_entry'));
        $crud->set_theme('datatables'); // Changed from 'flexigrid' to 'datatables'
        $crud->set_subject('Marks');
        $crud->order_by('MH_CLASS_SEQ', 'ASC');
        $crud->set_table('marks_hdr');
        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_clone();
        $crud->unset_read();
        $crud->unset_add();
        $crud->unset_edit();
        if($session_user_type != 1){
            $crud->unset_delete();
        }
        
        if ($session_user_type == 6) {
            $crud->where('MH_USER_ID', $session_user);
        }
        
        $crud->callback_before_delete(array($this,'marks_dtl_delete'));
        $crud->columns('MH_USER_ID','MH_TERM_SEQ','MH_TEST_SEQ','MH_CLASS_SEQ','MH_SUB_SEQ');
        $crud->display_as('MH_TERM_SEQ', 'Exam Term');
        $crud->display_as('MH_CLASS_SEQ', 'Class & Section');
        $crud->display_as('MH_TEST_SEQ', 'Exam Name');
        $crud->display_as('MH_SUB_SEQ', 'Subject Name');
        $crud->set_relation('MH_TERM_SEQ', 'exam_terms', 'term_title');
        $crud->set_relation('MH_CLASS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
        $crud->set_relation('MH_TEST_SEQ', 'exam_test', 'Exam_Name');
        $crud->set_relation('MH_SUB_SEQ', 'subject', 'sub_name');
        $crud->add_action('Edit', base_url().'assets/grocery_crud/themes/datatables/css/images/edit.png', 'admin/edit_marks'); // Updated path for datatables theme
        
        $output = $crud->render();
        //rending extra value to $output
        $output->tab_title = 'Marks Entry';
        $output->section_heading = 'Marks Entry <small>(Add / Edit)</small>';
        $output->menu_name = 'Marks Entry';
        $output->add_button = '<a href="'.base_url().'admin/add_marks" class="btn btn-success" role="button"><span class="fa fa-percent"></span> Entry Marks</a>';
        return array('page'=>'common_v', 'data'=>$output); //loading common view page
    } catch(Exception $e) {
        show_error($e->getMessage().' --- '.$e->getTraceAsString());
    }
}


    public function marks_dtl_delete($primary_key) {
        $this->db->where('MH_HDR_SRLNO',$primary_key)->delete('marks_dtl');
        return true;
    }

    public function add_marks() {

        $session_id = $this->session->user_id;
        $usertype = $this->db->get_where('users', array('user_id' => $session_id))->row()->usertype;
        if($usertype == 6){ // operators, mainly teachers
            $actual_userid = $this->db->get_where('users', array('user_id' => $session_id))->row()->actual_userid;
        }else{
            $actual_userid = $session_id;
        }

        $exam = $this->db->get('exam_test')->result_array();

        if($session_id == 1){
            $cls = $this->db->get('class_sec_hdr')->result_array();
            $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1, 'term_year' => FINANCIAL_YEAR))->result_array();
        }else{

            $cls_teach = $this->db
                ->join('teacher','TCH_SRLNO=actual_userid','left')
                ->get_where('users', array('user_id' => $session_id))->row()->TCH_CLASSES;
            $cls_teach = "'" .str_replace(",","','",$cls_teach) . "'";
            $query = "SELECT * FROM `class_sec_hdr` WHERE `CS_SEQ` IN (".$cls_teach.")";
            $cls = $this->db->query($query)->result_array();

            $excluded_nr = $this->db->get_where('activity_lock_exceptions', array('status' => 1, 'teacher' => $actual_userid))->num_rows();
            if($excluded_nr > 0){

                $excluded_exam_term = $this->db->select("GROUP_CONCAT(exam_term) AS exam_term")->get_where('activity_lock_exceptions', array('status' => 1, 'teacher' => $actual_userid))->result()[0]->exam_term;

                $query = "SELECT et_id,term_title FROM `exam_terms` 
                    WHERE `exam_terms`.status = 1 AND `term_year`='". FINANCIAL_YEAR ."'
                    AND et_id NOT IN (
                        SELECT exam_term FROM activity_locks 
                        WHERE activity_id = 'marks_entry' 
                        AND DATE(lock_from) <= DATE(NOW())
                        AND exam_term NOT IN (".$excluded_exam_term.")
                    )";

                $data['exam_terms'] = $this->db->query($query)->result_array();

                // echo '<pre>'; print_r($data['exam_terms']); die;

            }else{

                $query = "SELECT et_id,term_title FROM `exam_terms` 
                    WHERE `exam_terms`.status = 1 AND `term_year`='". FINANCIAL_YEAR ."'
                    AND et_id NOT IN (SELECT exam_term FROM activity_locks WHERE activity_id = 'marks_entry' AND DATE(lock_from) <= DATE(NOW()))";

                $data['exam_terms'] = $this->db->query($query)->result_array();

            }

        }


        $data['class'] = $cls;
        $data['exam'] = $exam;
        $data['form_type'] = 'add_marks';

        $data['tab_title'] = 'Add Marks';
        $data['section_heading'] = 'Add Marks';
        $data['menu_name'] = 'Add Marks';

        return array('page' => 'teachers_v', 'data' => $data);
    }

    public function ajax_update_std_marks_table() {
        $class_id = $this->input->post('class_id');
        $exam_id = $this->input->post('exam_id');

        $max_mark = 0;
        if($exam_id != '') {
            $this->db->where('EXAM_SEQ', $exam_id);
            $max_mark = $this->db->get('exam_test')->row()->Full_Marks;
        }

        $this->db->select('CS_Sub_id,sub_name');
        $this->db->join('subject', 'subject.sub_id = class_sub_link.CS_Sub_id', 'left');
        $this->db->where('CS_SEQ', $class_id);
        $this->db->order_by('Sorting');
        $sub = $this->db->get('class_sub_link')->result_array();

        $session_id = $this->session->user_id;
        $teacher_nr = $this->db->get_where('users', array('user_id' => $session_id))->num_rows();
        if($teacher_nr == 0){
            $html_sub = <<<EOD
                <select id="class_subjects" name="" class="form-control round-input" required >
EOD;
            $html_sub .='<option disabled>Teacher not assigned</option>';
            $html_sub .= '</select>';
            $teacher_id = '0';
        } else{
            $teacher_id = $this->db->get_where('users', array('user_id' => $session_id))->row()->actual_userid;
        }

        if($session_id != 1){
            foreach($sub as $key=>$s){
                $sub_id = $s['CS_Sub_id'];
                $nr = $this->db->get_where('routine',array('class_id' => $class_id, 'sub_id' => $sub_id, 'tch_id' => $teacher_id))->num_rows();
                if($nr == 0){
                    unset($sub[$key]);
                }
            }
        }
        // print_r($sub); die('dead');

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->where('STD_STATUS', 0);
        $this->db->where('STD_LEFT', 0);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html_sub = <<<EOD
                <select id="class_subjects" name="sub_id" class="form-control round-input" required >
                    <option value="">Select subject</option>
EOD;
        foreach($sub as $v) {
            $html_sub .='<option value="'.$v['CS_Sub_id'].'">'.$v['sub_name'].'</option>';
        }
        $html_sub .= '</select>';

        $html_std = '';
        //creating individual student table row
        foreach($std as $s) {
            $html_std .= <<<EOD
<tr>
<td>{$s['STD_ROLLNO']}</td>
<td>{$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</td>
<td><input name="marks[]" class="marks form-control round-input" value="" min="0" max="$max_mark" placeholder="Max marks: $max_mark" type="number" /></td>
<td>
    <select class="grade form-control" name="grade[]">
        <option value="AA">AA</option>
        <option value="A+">A+</option>
        <option value="A">A</option>
        <option value="B+">B+</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
    </select>
</td>
<input name="std_id[]" value="{$s['STD_SEQ']}" type="hidden" />
</tr>
EOD;
        }

        $array['html_sub'] = $html_sub;
        $array['html_std'] = $html_std;
        return $array;
    }

    public function ajax_mark_type_on_subject(){
        $subid = $this->input->post('subid');
        $marks_type = $this->db->get_where('subject',array('sub_id' => $subid))->row()->marks_type;
        return $marks_type;
    }

    public function ajax_update_std_class_table() {
        $from_class = $this->input->post('from_class');

        $from_class_session = $this->input->post('from_class_session');

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where('STD_CS_SEQ', $from_class);
        $this->db->where('STD_STATUS ', 0);
        /*if($from_class != 24 || $from_class != 25){
            $this->db->where('STD_LEFT', 0);
        }*/
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        //echo $this->db->last_query(); die();

        $html_std = '';
        $html_std .= <<<EOD
<label class="checkbox-custom check-warning col-lg-12">
    <input checked class="" value="" type="checkbox" id="checkbox_select_all" name="" >
    <label for="checkbox_select_all">Select All</label>
</label>
EOD;
        foreach($std as $s) {
            $html_std .= <<<EOD
<label class="checkbox-custom check-success col-lg-4">
    <input checked value="{$s['STD_SEQ']}" name="std_class[]" id="std_class_{$s['STD_SEQ']}" type="checkbox">
    <label for="std_class_{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</label>
</label>
EOD;
        }
        $array['html_std'] = $html_std;
        return $array;
    }

    public function form_add_marks() {
        if($this->input->post('submit') == 'submit_add_marks') { //if form submitted
            $class_id = $this->input->post('class');
            $term_id = $this->input->post('term');
            $exam_id = $this->input->post('exam');
            $sub_id = $this->input->post('sub_id');
            $std_id_arr = $this->input->post('std_id[]');
            $marks_arr = $this->input->post('marks[]');
            $grade_arr = $this->input->post('grade[]');
            $user_id = $this->session->user_id;
            $total_row = count($std_id_arr);

            $this->db->where('CS_SEQ', $class_id);
            $cls_rs = $this->db->get('class_sec_hdr')->result_array();
            //if class does not exists
            if(count($cls_rs) < 1){
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class does not exists.');
                return array('page'=>'admin/marks_entry');
            }
            //if no student in that class
            if($total_row < 1){
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'No student found for that class.');
                return array('page'=>'admin/marks_entry');
            }

            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'marks_hdr'")->row()->Auto_increment;
            $fin_year = $this->db->get("company")->row()->COM_FIN_YEAR;

            $data_insert_hdr['MH_TERM_SEQ'] = $term_id;
            $data_insert_hdr['MH_CLASS_SEQ'] = $class_id;
            $data_insert_hdr['MH_TEST_SEQ'] = $exam_id;
            $data_insert_hdr['MH_SUB_SEQ'] = $sub_id;
            $data_insert_hdr['MH_USER_ID'] = $user_id;
            $data_insert_hdr['MH_FIN_YEAR'] = $fin_year;
            //inserting data
            $this->db->insert('marks_hdr', $data_insert_hdr);

            for($i=0; $i < $total_row; $i++) {
                $data_insert_dtl['MH_HDR_SRLNO'] = $get_auto_index;
                $data_insert_dtl['MD_CLASS_SEQ'] = $class_id;
                $data_insert_dtl['MD_TERM_SEQ'] = $term_id;
                $data_insert_dtl['MD_TEST_SEQ'] = $exam_id;
                $data_insert_dtl['MD_SUB_SEQ'] = $sub_id;
                $data_insert_dtl['MD_STD_SEQ'] = $std_id_arr[$i];
                $data_insert_dtl['MD_MARKS'] = $marks_arr[$i];
                $data_insert_dtl['MD_GRADE'] = $grade_arr[$i];
                $data_insert_dtl['MD_USER_ID'] = $user_id;
                $data_insert_dtl['MD_FIN_YEAR'] = $fin_year;
                //inserting data
                $this->db->insert('marks_dtl', $data_insert_dtl);
                // echo $this->db->last_query(); die;
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Marks successfully added.');
            return array('page'=>'admin/marks_entry');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/marks_entry');
        }
    }

    public function edit_marks($hdr_id) {
        $this->db->where('MH_HDR_SRLNO', $hdr_id);
        $marks_hdr_rs = $this->db->get('marks_hdr')->row();
        //if library header does not exists
        if(count((array)$marks_hdr_rs) < 1){
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'No record found.');
            return array('page'=>'admin/marks_entry');
        }

        $this->db->where('CS_SEQ', $marks_hdr_rs->MH_CLASS_SEQ);
        $cls = $this->db->get('class_sec_hdr')->row();
        $this->db->where('EXAM_SEQ', $marks_hdr_rs->MH_TEST_SEQ);
        $exam = $this->db->get('exam_test')->row();
        $max_mark = $exam->Full_Marks;
        $this->db->where('sub_id', $marks_hdr_rs->MH_SUB_SEQ);
        $sub = $this->db->get('subject')->row();

        $this->db->select('MD_DTL_SRLNO,MD_MARKS,MD_GRADE,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->join('student_details', 'student_details.STD_SEQ = marks_dtl.MD_STD_SEQ', 'left');
        $this->db->where('MH_HDR_SRLNO', $hdr_id);
        $marks_dtl_rs = $this->db->get('marks_dtl')->result_array();

        $data['marks_dtl'] = $marks_dtl_rs;
        $data['max_mark'] = $max_mark;
        $data['form_type'] = 'edit_marks';

        $data['tab_title'] = 'Edit Marks';
        $data['section_heading'] = <<<EOD
Class & Sec : <strong>$cls->Class_Name - $cls->Sec_Name</strong><br>
Exam Name: <strong>$exam->Exam_Name</strong> (Exam of $exam->Full_Marks Marks)<br>
Subject Name: <strong>$sub->sub_name</strong><br>
EOD;
        $data['menu_name'] = 'Edit Marks';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }

    public function form_edit_marks() {
        if($this->input->post('submit') == 'submit_edit_marks') { //if form submitted

            $user_id = $this->session->user_id;

            $marks_arr = $this->input->post('marks[]');
            $marks_dtl_arr = $this->input->post('marks_dtl[]');

            $grade_arr = $this->input->post('grade[]');
            $grade_dtl_arr = $this->input->post('grade_dtl[]');

            $mt = $this->db
                ->join('subject','sub_id = MD_SUB_SEQ','left')
                ->get_where('marks_dtl', array('MD_DTL_SRLNO' =>$grade_dtl_arr[0]))->row()->marks_type;

            if($mt == 'Marks'){
                foreach($marks_dtl_arr as $marks_dtl_id) {
                    $data_update_dtl['MD_MARKS'] = $marks_arr[$marks_dtl_id];
                    $data_update_dtl['MD_USER_ID'] = $user_id;
                    //updating data
                    $this->db->where('MD_DTL_SRLNO', $marks_dtl_id);
                    $this->db->update('marks_dtl', $data_update_dtl);
                }
            } else {

                foreach($grade_arr as $key=>$gda){

                    $data_update_dtl['MD_GRADE'] = $gda;
                    $data_update_dtl['MD_USER_ID'] = $user_id;
                    //updating data
                    $this->db->where('MD_DTL_SRLNO', $key);
                    $this->db->update('marks_dtl', $data_update_dtl);

                }

            }


            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Marks successfully updated.');
            return array('page'=>'admin/marks_entry');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/marks_entry');
        }
    }

    public function ajax_fetch_std_on_marks_entry(){

        $class = $this->input->post('class');
        $term = $this->input->post('term');
        $exam = $this->input->post('exam');
        $subject = $this->input->post('subject');

        $nr = $this->db->get_where('marks_dtl',
            array(
                'MD_CLASS_SEQ' => $class,
                'MD_TERM_SEQ' => $term,
                'MD_TEST_SEQ' => $exam,
                'MD_SUB_SEQ' => $subject
            )
        )->num_rows();

        if($nr > 0){
            return 'entered';
        }else{
            return 'blank';
        }
    }
    public function progress_report_entry(){
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Teachers/progress_report_entry'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Progress Report');
            $crud->order_by('EXAM_TERM,CLASS_SEC', 'ASC');
            $crud->set_table('progress_report_entry');

            $crud->unset_clone();
            // $crud->unset_edit();

            $crud->columns('EXAM_TERM','CLASS_SEC','STUDENT','TOTAL_ATTENDANCE');
            $crud->callback_column('STUDENT',array($this,'_callback_student_name'));

            $crud->unset_fields('CREATED_DATE','MODIFIED_DATE','STATUS');
            $crud->edit_fields('TOTAL_ATTENDANCE','GENERAL_REMARKS_FILLED','GENERAL_REMARKS','GRADE_INITIATIVE','GRADE_PERSEVERANCE','GRADE_ORIGINALITY','GRADE_CONCENTRATION','GRADE_OBSERVATION','GRADE_CURIOSITY','GRADE_CONFIDENCE','GRADE_RESPONSIBILITY','GRADE_RELATIONSHIPS','GRADE_PARTICIPATION','GRADE_NEATNESS','GRADE_SPIRIT_OF_SERVICE','GRADE_SOCIAL_AWARENESS','GRADE_TIME_MANAGEMENT');

            $crud->display_as('GENERAL_REMARKS_FILLED', 'Select Remark Template');

            $crud->required_fields('EXAM_TERM','CLASS_SEC','STUDENT','TOTAL_ATTENDANCE');
            $crud->field_type('STUDENT','dropdown',array('n/a'=>'Select Class/Sec.'));

            $crud->set_relation('EXAM_TERM', 'exam_terms', 'term_title');
            $crud->set_relation('CLASS_SEC', 'class_sec_hdr', 'class_sec');

            // $crud->add_action('Edit', base_url().'assets/grocery_crud/themes/flexigrid/css/images/edit.png', 'admin/edit_marks');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Progress Report';
            $output->section_heading = 'Progress Report <small>(Add / Edit)</small>';
            $output->menu_name = 'Progress Report';
            $output->add_button = '';

            return array('page'=>'progress_report_entry', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function _callback_student_name($value, $row){
        $std_rs = $this->db
            ->select('ST_FULL_NAME, STD_ROLLNO')
            ->get_where('student_details', array('STD_SEQ' => $row->STUDENT))->row();
        return $std_rs->STD_ROLLNO.' - '.$std_rs->ST_FULL_NAME;
    }

    public function fetch_student_on_class_sec(){

        $cs = $this->input->post('cs');
        $term = $this->input->post('term');

        $query = "SELECT STD_SEQ, ST_FULL_NAME, STD_ROLLNO FROM `student_details` 
        WHERE `STD_CS_SEQ` = ".$this->db->escape_str($cs)." AND `STD_LEFT` = 0 AND `STD_STATUS`= 0 
        AND STD_SEQ NOT IN (SELECT STUDENT FROM progress_report_entry WHERE EXAM_TERM = ".$this->db->escape_str($term).")
        ORDER BY ST_FULL_NAME";
        $rval = $this->db->query($query)->result();
        // echo $this->db->last_query();
        return $rval;

    }

    public function progress_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'progress_report';

        $data['tab_title'] = 'Print Progress Report';
        $data['section_heading'] = 'Print Progress Report <small>(Print)</small>';
        $data['menu_name'] = 'Print Progress Report';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }

    public function print_progress_report() {
        
        if($this->input->post('submit') == 'print_progress_report_1') { //if form submitted
            $class = $this->input->post('class');
            $std_class = $this->input->post('std_class');

            if (!is_array($std_class)) {
                $std_class = [$std_class];
            }
            
            $std_id = "IN('" . implode("','", $std_class) . "')";
            
          

            $company = $this->db->get('company')->row();
            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();

            //if class does not exists
            if (count((array)$cls) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('type' =>'redirect', 'page' => 'admin/progress_report');
            }

            $this->db->select('EXAM_SEQ,Exam_Name');
            $this->db->join("exam_test", 'exam_test.EXAM_SEQ = MH_TEST_SEQ', 'left');
            $this->db->where("MH_CLASS_SEQ", $class);
            $this->db->group_by("MH_TEST_SEQ");
            $exam_name = $this->db->get('marks_hdr')->result_array();
            $total_exam = count($exam_name);

            //if no exam marks entry for that class
            if ($total_exam == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'No exam marks entry found.');
                return array('type' =>'redirect', 'page' => 'admin/progress_report');
            }

            $this->db->select('STD_SEQ,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME');
            $this->db->where("STD_LEFT", 0);
            $this->db->where('STD_CS_SEQ', $class);
            $this->db->where("STD_SEQ $std_id");
            $std = $this->db->get('student_details')->result_array();
  
         



            $this->db->select('sub_id,sub_s_name');
            $this->db->join('subject', 'subject.sub_id = CS_Sub_id', 'left');
            $this->db->where('CS_SEQ', $class);
            $this->db->order_by('Sorting');
            $sub = $this->db->get('class_sub_link')->result_array();
            $total_sub = count($sub);

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Progress Report of Class ' . $cls->Class_Name . ' - ' . $cls->Sec_Name;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name . ', smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
<div style="text-align:center;">
<span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
<br>
$company->COM_ADD2 , $company->COM_CITY
<br>
<strong style="font-size: 13px">Progress Report of <span style="background-color: black;color: white;"> $cls->Class_Name - $cls->Sec_Name </span></strong>
<hr align="left">
</div>
EOD;
            $pdf->setHtmlHeader($html_header, false);

            // set header and footer fonts and size
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(10, 20, 10);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 15);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // -----------------------------------------------------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            // Add a page
            $pdf->AddPage('L', 'A4');

            // Set some content to print
            $html = '';

            foreach ($std as $s) { //this loop is for all students of selected class

                $html .= <<<EOD
<span style="font-size: 17px">Name: <strong>{$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</strong> • Roll: <strong>{$s['STD_ROLLNO']}</strong> • Reg. No: <strong>{$s['STD_REGNO']}</strong></span>
<br>
<table border="1">
<thead>
<tr>
    <th width="80"><strong>Exam Name</strong></th>
EOD;
                foreach ($sub as $subject) { //sub names
                    $html .= <<<EOD
    <th align="center"><strong>{$subject['sub_s_name']}</strong></th>
EOD;
                }
                $html .= <<<EOD
    <th width="30" align="center"><strong>Total</strong></th>
    <th width="30" align="center"><strong>Avg</strong></th>
    <th width="60" align="center"><strong>Remarks</strong></th>
</tr>
</thead>
<tbody>
EOD;

                $sub_total = array_fill(0, $total_sub, 0);
                foreach ($exam_name as $ex) { //exam names
                    $html .= <<<EOD
<tr>
    <td width="80">{$ex['Exam_Name']}</td>
EOD;
                    $exam_total = 0;
                    $sub_total_index = 0;
                    foreach ($sub as $subject) { //sub names
                        $this->db->select('MD_MARKS');
                        $this->db->where('MD_CLASS_SEQ', $class);
                        $this->db->where('MD_TEST_SEQ', $ex['EXAM_SEQ']);
                        $this->db->where('MD_SUB_SEQ', $subject['sub_id']);
                        $this->db->where('MD_STD_SEQ', $s['STD_SEQ']);
                        $marks_rs = $this->db->get('marks_dtl')->result_array();
                        $marks_arr = array_column($marks_rs, 'MD_MARKS');
                        if($marks_arr){$marks = $marks_arr[0]; $exam_total+=$marks;} else {$marks = '';}

                        $html .= <<<EOD
    <td align="center">$marks</td>
EOD;
                        $sub_total[$sub_total_index] += $marks;
                        $sub_total_index++;
                    }

                    $exam_avg = round($exam_total/$total_sub);
                    $html .= <<<EOD
    <td width="30" align="center"><strong>$exam_total</strong></td>
    <td width="30" align="center"><strong>$exam_avg</strong></td>
    <td width="60" align="center"></td>
</tr>
EOD;
                }

                $html .= <<<EOD
<tr>
    <td width="80"><strong>Average</strong></td>
EOD;
                foreach ($sub_total as $sub_tot){
                    $sub_avg = round($sub_tot/$total_exam);
                    $html .= '<td align="center"><strong>'.$sub_avg.'</strong></td>';
                }
                $html .= <<<EOD
    <td width="30" align="center"></td>
    <td width="30" align="center"></td>
    <td width="60" align="center"></td>
</tr>
</tbody>
</table>
<br>
<br>
EOD;
            }

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        }
        else if($this->input->post('submit') == 'print_progress_report_2'){

            $data = array();
            $class = $this->input->post('class');
            $std_class = $this->input->post('std_class');

            if (!is_array($std_class)) {
                $std_class = [$std_class];
            }
            
            $std_id = "IN('" . implode("','", $std_class) . "')";
            $data['cs_seq'] =$class;
            $company = $this->db->get('company')->row();
            $data['company'] = $company;

            $this->db->select('student_details.STD_SEQ,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,STD_PH_NO,STD_FTH_NAME,STD_MTH_NAME');
            $this->db->join('student_parent_details','student_parent_details.STD_SEQ=student_details.STD_SEQ','left');
            $this->db->where("STD_LEFT", 0);
            $this->db->where('STD_CS_SEQ', $class);
            $this->db->where("student_details.STD_SEQ $std_id");
            $std = $this->db->order_by('STD_ROLLNO')->get('student_details')->result_array();
            $data['student_details'] = $std;

            $data['class'] = $this->db->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->Class_Name;
            $data['sec'] = $this->db->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->Sec_Name;

            // $class_tch_rs = $this->db->join('teacher','TCH_SRLNO=class_teacher','left')->where('CS_SEQ', $class)->get('class_sec_hdr')->row();
            // $data['class_teacher'] = $class_tch_rs->TCH_NAME;
            $class_tch_rs = $this->db->join('teacher','TCH_CS_SEQ=CS_SEQ','left')->where('CS_SEQ', $class)->get('class_sec_hdr')->row();
            $data['class_teacher'] = $class_tch_rs->TCH_NAME;
            $data['class_teacher_sign'] = $class_tch_rs->TCH_SIGN;
            $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1))->result();
            $data['subjects'] = $this->db
                ->select('class_sub_link.*,sub_name')
                ->join('subject','sub_id=CS_Sub_id','left')
                ->where('CS_SEQ', $class)
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();
            $data['all_grades'] = $this->db->get('grades')->result();

            $data['tab_title'] = 'Progress Report';
            $data['form_type'] = 'progress_report_type_2';

            return array('type' => 'load_view', 'page' => 'progress_report_print', 'data' => $data);

        }
        else if($this->input->post('submit') == 'print_progress_report_3'){

            $data = array();
            $class = $this->input->post('class');
            $std_class = $this->input->post('std_class');

            if (!is_array($std_class)) {
                $std_class = [$std_class];
            }
            
            $std_id = "IN('" . implode("','", $std_class) . "')";
            $data['cs_seq'] =$class;
            $company = $this->db->get('company')->row();
            $data['company'] = $company;

            $this->db->select('student_details.STD_SEQ,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,STD_PH_NO,STD_FTH_NAME,STD_MTH_NAME');
            $this->db->join('student_parent_details','student_parent_details.STD_SEQ=student_details.STD_SEQ','left');
            $this->db->where("STD_LEFT", 0);
            $this->db->where('STD_CS_SEQ', $class);
            $this->db->where("student_details.STD_SEQ $std_id");
            $std = $this->db->order_by('STD_FNAME')->get('student_details')->result_array();
            $data['student_details'] = $std;
            $data['class'] = $this->db->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->Class_Name;
            $data['sec'] = $this->db->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->Sec_Name;
            $data['class_teacher'] = $this->db->join('teacher','TCH_SRLNO=class_teacher','left')->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->TCH_NAME;
            $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1))->result();
            $data['subjects'] = $this->db
                ->select('class_sub_link.*,sub_name')
                ->join('subject','sub_id=CS_Sub_id','left')
                ->where('CS_SEQ', $class)
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();
            $data['tab_title'] = 'Progress Report';
            $data['form_type'] = 'progress_report_type_3';

            return array('type' => 'load_view', 'page' => 'student_report_print', 'data' => $data);

        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/progress_report');
        }
    }

    public function marksheet() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'marksheet';

        $data['tab_title'] = 'Mark Sheet';
        $data['section_heading'] = 'Mark Sheet <small>(Print)</small>';
        $data['menu_name'] = 'Mark Sheet';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }


    public function print_marksheet() {
        if($this->input->post('submit') == 'print_marksheet') { //if form submitted
            $class = $this->input->post('class');
            $std_id_arr = $this->input->post('std[]');

            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();

            //if class does not exists
            if (count((array)$cls) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('page' => 'admin/marksheet');
            }
            //if no student selected
            if (count($std_id_arr) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No Student Selected.');
                return array('page' => 'admin/marksheet');
            }

            $this->db->select('STD_SEQ,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME');
            $this->db->where_in("STD_SEQ", $std_id_arr);
            $this->db->order_by("STD_ROLLNO");
            $std_arr = $this->db->get('student_details')->result_array();

            $this->db->select('sub_id,sub_name');
            $this->db->join("subject", 'subject.sub_id = class_sub_link.CS_Sub_id', 'left');
            $this->db->where("CS_SEQ", $class);
            $this->db->order_by("Sorting");
            $sub = $this->db->get('class_sub_link')->result_array();

            $this->db->select('MD_TEST_SEQ,MD_SUB_SEQ,MD_STD_SEQ,MD_MARKS');
            $this->db->where("MD_CLASS_SEQ", $class);
            $this->db->where_in("MD_STD_SEQ", $std_id_arr);
            $marks = $this->db->get('marks_dtl')->result_array();

            $this->db->order_by("grd_from", "DESC");
            $grades_rs = $this->db->get('grades')->result_array();
            $grades = array_column($grades_rs, 'grd_from', 'grade');

            $data['class'] = $cls;
            $data['students'] = $std_arr;
            $data['subjects'] = $sub;
            $data['marks'] = $marks;
            $data['grades'] = $grades;

            return array('type' => 'load_view', 'page' => 'marksheet_v', 'data' => $data);

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/marksheet');
        }
    }

    public function student_class_update() {
        // $cls = $this->db->get('class_sec_hdr')->result_array();
        $cls = $this->db->order_by('class_order', 'ASC')->get('class_sec_hdr')->result_array();


        $data['class'] = $cls;
        $data['form_type'] = 'student_class_update';

        $data['tab_title'] = 'Update Student Class';
        $data['section_heading'] = '';
        $data['menu_name'] = 'Update Student Class';

        return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }


    public function form_update_student_class() {

        if ($this->input->post('submit') == 'class_update') {

            $students = $this->input->post("std_class[]");

            $from_class = $this->input->post('from_class');

            $from_class_session = $this->input->post('from_class_session');

            $to_class = $this->input->post('to_class');

            $to_class_session = $this->input->post('to_class_session');


            if (empty($students)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('msg', 'No student found.');
                return array('type' => 'redirect','page'=>'admin/student_class_update');
            }


            // echo "<pre>"; print_r($this->input->post()); die();
            foreach ($students as $index => $student) {

                $update_data = array(
                    'STD_CS_SEQ' => $to_class,
                    'STD_LAST_CLASS' => $from_class,
                    'STD_LAST_SESSION' => $from_class_session,
                    'STD_CURRENT_SESSION' => $to_class_session
                );
                $this->db->where('STD_SEQ', $student);
                $this->db->update('student_details', $update_data);

                // update_class

                $update_class_data = array(
                    'st_id'=>$student,
                    'last_class_id'=>$from_class,
                    'promote_class_id'=>$to_class,
                    'last_session'=>$from_class_session,
                    'promot_session'=>$to_class_session,
                );

                $this->db->insert('update_class',$update_class_data);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Class promotion successfully.');
            return array('type' => 'redirect','page'=>'admin/student_class_update');

        }else{
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect','page'=>'admin/student_class_update');
        }

        die('COMPLETED TEACHERS MODEL');

        /*else{

        }*/


        // return array('type' => 'load_view', 'page' => 'teachers_v', 'data' => $data);
    }

    public function homework() {
        try{
            $output = new \stdClass();
            $query = "SELECT hw_id,release_date,class_sec,sub_name FROM homeworks LEFT JOIN class_sec_hdr ON class_sec_hdr.CS_SEQ=homeworks.class_id LEFT JOIN subject ON subject.sub_id=homeworks.sub_id";

            $output->menu_name = 'Homework';
            $output->all_homework  = $this->db->query($query)->result();
            //  print_r($data);
            return array('page'=>'common_master', 'data'=>$output); //loading common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function homework_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Teachers/homework_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Home Work');
            $crud->order_by('class_id', 'ASC');
            $crud->set_table('homeworks');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('class_id','sub_id','homework','release_date');
            $crud->required_fields('class_id','sub_id','release_date','homework');

            $crud->display_as('class_id','Class & Section');
            $crud->display_as('sub_id','Subject Name');
            $crud->display_as('release_date','Question Release Date');
            $crud->display_as('homework','Home Work');
            $crud->display_as('doc','Question Document');
            $crud->display_as('ans_date','Answer Release Date');
            $crud->display_as('answer','Answer');
            $crud->display_as('ans_doc','Answer Document');

            $crud->set_field_upload('doc','assets/admin_panel/homework_files');
            $crud->set_field_upload('ans_doc','assets/admin_panel/homework_files');

            $crud->set_relation('class_id', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Home Work';
            $output->section_heading = 'Home Work <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Home Work';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function notices() {
        try{

            $session_user = $this->session->user_id;
            $session_user_type = $this->session->user_type;

            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Teachers/notices'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Notice');
            $crud->set_table('notices');

            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_delete();

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Notice Board';
            $output->section_heading = 'Notice Board <small>(Edit)</small>';
            $output->menu_name = 'Notice Board';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

} // /.Teachers_m model