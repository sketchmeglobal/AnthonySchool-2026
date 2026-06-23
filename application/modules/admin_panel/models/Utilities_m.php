<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 14-01-2019
 * Time: 23:07
 */

class Utilities_m extends CI_Model {

    public function __construct() {
        parent::__construct();

        // echo
        $this->db->query("SET sql_mode = ' ' ");
        error_reporting(0);
        @ini_set('display_errors', 0);
    }


    public function fees_related_transfer() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $class_type = $this->db->get('class_type')->result_array();


        $data['class'] = $cls;
        $data['class_type'] = $class_type;
        $data['form_type'] = 'fees_related_transfer';

        $data['tab_title'] = 'Fees Related Transfer';
        $data['section_heading'] = 'Fees Related Transfer<small>(Edit)</small>';
        $data['menu_name'] = 'Fees Related Transfer';

        return array('type' => 'load_view', 'page' => 'utilities_v', 'data' => $data);
    }

    public function get_fees_data() {
        
        extract($this->input->post());

        /*$form_date;
        $to_date;
        $fees_type; */
        $this->db->start_cache();
        $this->db->select('FM_HDR_SRLNO,FM_HDR_RCPT_NO,FM_HDR_TOT_FEES, student_details.STD_REGNO, student_details.ST_FULL_NAME as full_name, class_sec_hdr.class_sec as cls');
        $this->db->join('student_details', 'student_details.STD_SEQ = FM_HDR_STD_SEQ', 'left');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FM_HDR_STD_CS_SEQ', 'left');
        if ($school != 'all') {
            $this->db->where('class_sec_hdr.Class_Type', $school);
        }
        if($form_date != null && $to_date != null) {

            $form_date = str_replace('/', '-', $form_date);
            $form_date = date("Y-m-d", strtotime($form_date));

            $to_date = str_replace('/', '-', $to_date);
            $to_date = date("Y-m-d", strtotime($to_date));
            $this->db->where('FM_HDR_COL_DATE >=', $form_date);
            $this->db->where('FM_HDR_COL_DATE <=', $to_date);
        }
        $this->db->order_by("FM_HDR_COL_DATE,FM_HDR_RCPT_NO");
        $this->db->stop_cache();
        if ($fees_type == 'M') {
            $fees_hdr = $this->db->get('fees_monthly_hdr')->result();
        }
        if ($fees_type == 'Y') {
            $fees_hdr = $this->db->get('fees_yearly_hdr')->result();
        }
        if ($fees_type == 'N') {
            $fees_hdr = $this->db->get('fees_newadm_hdr')->result();
        }
        $this->db->flush_cache();

        $array = array();
         $html_fees = '';
        //creating individual student table row
        foreach($fees_hdr as $key=>$fees_val) {
            $html_fees .= <<<EOD
<option value="$fees_val->FM_HDR_SRLNO">$fees_val->FM_HDR_RCPT_NO ₹ $fees_val->FM_HDR_TOT_FEES ($fees_val->STD_REGNO - $fees_val->full_name - $fees_val->cls)</option>
EOD;
        }

        $array['html_fees'] = $html_fees;

        // echo "<pre>"; print_r($array['html_fees']); die();



        return $array;
    }

    public function form_fees_related_transfer() {

        extract($this->input->post());
        /*$form_date;
        $to_date;
        $fees_type;
        $bank;
        $new_date;
        $fees_list;
        */
        $this->session->set_userdata('fees_related_transfer_form_date', $from_date);
        $this->session->set_userdata('fees_related_transfer_to_date', $to_date);
        if ($form_fees_related_transfer == 'form_fees_related_transfer') {

            if (empty($fees_list)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'No transaction found');
                return array('type' => 'redirect', 'page'=>'admin/fees_related_transfer');
            }

            if (empty($bank) and empty($new_date)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Please select bank or new date');
                return array('type' => 'redirect', 'page'=>'admin/fees_related_transfer');
            }
            if (!empty($new_date)) {
                $new_date = str_replace('/', '-', $new_date);
                $new_date = date("Y-m-d", strtotime($new_date));
            }
            
            foreach ($fees_list as $key => $value) {

                // echo "<pre>"; print_r($value); die();
                $updateArray = array();
                if (!empty($bank)) {
                    $updateArray['FM_HDR_B_NAME'] = $bank;
                }
                if (!empty($new_date)) {
                    $updateArray['FM_HDR_COL_DATE'] = $new_date;
                }
                if ($fees_type == 'M') {
                    $this->db->where('FM_HDR_SRLNO', $value);
                    $this->db->update('fees_monthly_hdr', $updateArray);
                    if (!empty($new_date)) {
                        $this->db->update('fees_monthly_dtl', array('FEES_DTL_COL_DATE' => $new_date), array('FEES_DTL_HDR_SRLNO' => $value));
                    }
                }
                if ($fees_type == 'Y') {
                    $this->db->where('FM_HDR_SRLNO', $value);
                    $this->db->update('fees_yearly_hdr', $updateArray);
                    if (!empty($new_date)) {
                        $this->db->update('fees_yearly_dtl', array('FEES_DTL_COL_DATE' => $new_date), array('FEES_DTL_HDR_SRLNO' => $value));
                    }
                }
                if ($fees_type == 'N') {
                    $this->db->where('FM_HDR_SRLNO', $value);
                    $this->db->update('fees_newadm_hdr', $updateArray);
                    if (!empty($new_date)) {
                        $this->db->update('fees_newadm_dtl', array('FEES_DTL_COL_DATE' => $new_date), array('FEES_DTL_HDR_SRLNO' => $value));
                    }
                }
            }
            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Data updated successfully');
            return array('type' => 'redirect', 'page'=>'admin/fees_related_transfer');
        }else{
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Oops! something went wrong. Please try again');
            return array('type' => 'redirect', 'page'=>'admin/fees_related_transfer');
        }

        return array('type' => 'load_view', 'page' => 'utilities_v', 'data' => $data);
    }

    public function activity_locks() {
        try{
            $user_id = $this->session->user_id;

            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Utilities/activity_locks'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Activity Locks');
            $crud->set_table('activity_locks');

            $crud->unset_add();
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_delete();

            $crud->columns('activity_id', 'exam_term', 'lock_from', 'status');
            $crud->add_fields('activity_id', 'exam_term', 'lock_from'); 
            $crud->edit_fields('lock_from'); 
            $crud->required_fields('activity_id', 'exam_term', 'lock_from');

            $crud->display_as('activity_id','Select Activity');
            
            $crud->set_relation('exam_term','exam_terms','term_title');

            $crud->add_action('Exception', base_url().'assets/grocery_crud/themes/flexigrid/css/images/user.png', 'admin/activity-exception-users');

            $crud->field_type('user_id','hidden',$user_id);

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Activity Locks';
            $output->section_heading = 'Activity Locks <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Activity Locks';
            $output->add_button = '';
            
            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function activity_exception_users($al_id) {
        try{
            $user_id = $this->session->user_id;

            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Utilities/activity_exception_users/'.$al_id));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Excluded Users');
            $crud->set_table('activity_lock_exceptions');
            $exam_term = $this->db->get_where('activity_locks',array('al_id' => $al_id))->row()->exam_term;
            $crud->where('exam_term',$exam_term);

            $crud->unset_clone();
            $crud->unset_read();

            $crud->columns('exam_term', 'teacher'); //'class', 'subject', 
            $crud->fields('al_id','exam_term', 'teacher','user_id','status'); //'class', 'subject',
            $crud->required_fields('exam_term', 'teacher'); //'class', 'subject', 

            // $crud->display_as('activity_id','Select Activity');

            $crud->set_relation('exam_term','exam_terms','term_title');
            // $crud->set_relation('class','class_sec_hdr','class_sec');
            // $crud->set_relation('subject','subject','sub_name');
            $crud->set_relation('teacher','teacher','TCH_NAME');

            $crud->field_type('al_id','hidden',$al_id);
            $crud->field_type('user_id','hidden',$user_id);

            $output = $crud->render();
            //rending extra value to $output
            $activity_header1 = $this->db->get_where('activity_locks', array('al_id' => $al_id))->row()->activity_id;
            $activity_header2 = $this->db->join('exam_terms','exam_terms.et_id=exam_term','left')->get_where('activity_locks', array('al_id' => $al_id))->row()->term_title;
            if($activity_header1 == 'marks_entry'){
                $appnd = "Excluded Users For <span><b>Marks Entry</b> For <b>".$activity_header2."</b></span>";
            }
            $output->tab_title = 'Excluded Users';
            $output->section_heading = ' '.$appnd;
            $output->menu_name = 'Excluded Users';
            $output->add_button = '';
            
            
            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

} // /.Utilities_m model