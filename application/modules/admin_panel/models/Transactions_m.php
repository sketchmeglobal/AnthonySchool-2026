<?php
/**
 * Coded by: Pran Krishna Das
 * Social: https://sketchmeglobal.com
 * CI: 3.0.6
 * Date: 16-01-2019
 * Time: 22:00
 */
/*error_reporting(0);
@ini_set('display_errors', 0);*/
class Transactions_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        
        $this->db->query("SET sql_mode = ' ' ");
    }

    private function company_name($class_id)
    {
        $company = '';
        if(in_array("all", $class_id)){
            $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
            }else{
                if(@count($class_id) == 1){
                              $this->db->where_in('CS_SEQ', $class_id);
                $class_type = $this->db->get('class_sec_hdr')->row();
                $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
                }else{
                    if(@count($class_id) > 1){
                $this->db->where_in('CS_SEQ', $class_id);
                $this->db->group_by("Class_Type");
                $class_type = $this->db->get('class_sec_hdr')->result();
                // echo "<pre>"; print_r($class_type); die();
                if(count($class_type) > 1){
                    $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
                }else{
                              $this->db->where_in('CS_SEQ', $class_id);
                $class_type = $this->db->get('class_sec_hdr')->row();
                $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
                }
            }else{
                          $this->db->where_in('CS_SEQ', $class_id);
            $class_type = $this->db->get('class_sec_hdr')->row();
            $company    = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
                
                    }
                }
            }

            return $company;
    }

    public function monthly_fees_report() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Transactions/monthly_fees_report'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Monthly Fees Report');
            $crud->order_by('FM_HDR_SRLNO', 'DESC');
            $crud->set_table('fees_monthly_hdr');
            $crud->unset_export();
            // $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_read();
//            $crud->unset_edit();

            $crud->columns('FM_HDR_RCPT_NO','STD_REGNO','FM_HDR_STD_CS_SEQ', 'STD_ROLLNO','FM_HDR_STD_SEQ','FM_HDR_LATE_FEES','FM_HDR_TOT_FEES','FM_HDR_P_TYP','FM_HDR_COL_DATE','FM_HDR_B_NAME');
            $crud->fields('encash_date', 'FM_HDR_COL_DATE');
            $crud->field_type('FM_HDR_COL_DATE', 'hidden');

            $crud->display_as('FM_HDR_RCPT_NO', 'Recp. No');
            $crud->display_as('FM_HDR_STD_SEQ', 'Student Name');
            $crud->display_as('FM_HDR_STD_CS_SEQ', 'Class & Sec');
            $crud->display_as('FM_HDR_LATE_FEES', 'Late Fine');
            $crud->display_as('FM_HDR_TOT_FEES', 'Total Fees');
            $crud->display_as('FM_HDR_COL_DATE', 'Collection Date');
            $crud->display_as('encash_date', 'Cheque Encashment Date <small style="color: red">Cution: If this date modified, <u>Collection Date</u> will be changed to this date!</small>');
            $crud->display_as('FM_HDR_B_NAME', 'Bank Name');
            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('FM_HDR_P_TYP', 'Payment Type');

            $crud->set_relation('FM_HDR_STD_SEQ', 'student_details', 'ST_FULL_NAME');
            $crud->set_relation('FM_HDR_STD_CS_SEQ', 'class_sec_hdr', 'class_sec');

            $crud->add_action('Print',  base_url().'assets/grocery_crud/themes/flexigrid/css/images/print.png', 'admin/print_monthly_fess');

            $crud->callback_column('STD_REGNO',array($this,'_callback_reg_no'));

            if(isset($_POST['search_text']) && !empty($_POST['search_text']))  {
                    $crud->or_like('jfff7ee35.STD_REGNO', $_POST['search_text']);
                    $crud->or_like('jfff7ee35.STD_ROLLNO', $_POST['search_text']);
            }

            if(isset($_POST['search_collection_date']) && !empty($_POST['search_collection_date']))  {
                // $search_collection_date = str_replace('/', '-', );
                $search_collection_date = date("Y-m-d", strtotime($_POST['search_collection_date']));
                // echo $search_collection_date; die();
                $crud->where('FM_HDR_COL_DATE', $search_collection_date);
                // echo $search_collection_date; die();
            }

            if(isset($_POST['school_list']) && !empty($_POST['school_list']))  {
                // $search_collection_date = str_replace('/', '-', );
                $school_list = $_POST['school_list'];
                // echo $search_collection_date; die();
                $this->db->select('CS_SEQ');
                $school_list_data = $this->db->get_where('class_sec_hdr', array('Class_Type'=>$school_list))->result_array();
                $scl_list_array = array();
                foreach ($school_list_data as $key => $school_list_data) {
                  $scl_list_array[] =  $school_list_data['CS_SEQ'];
                }
                // echo "<pre>"; print_r($scl_list_array); die();
                $scl_list_array = implode(',', $scl_list_array);
                $where = "FM_HDR_STD_CS_SEQ IN ($scl_list_array)";
                $crud->where($where);
                // $crud->where('FM_HDR_STD_CS_SEQ', $where);
                // echo $search_collection_date; die();
            }
            $crud->callback_column('STD_ROLLNO',array($this,'_callback_roll_no'));
            
            $crud->callback_before_delete(array($this,'log_user_before_monthly_delete'));
            $crud->callback_before_update(array($this, '_callback_BeforeUpdate_monthly_fees_report'));

            $output = $crud->render();            

            $output->output = str_replace('title="Print"', 'title="Print" target="_blank"', $output->output); //additional line
            //rending extra value to $output
            $output->tab_title = 'Monthly Fees Report';
            $output->section_heading = '';
            $output->menu_name = 'Monthly Fees Report';

            $form = '
            <form method="post" id="all_selected_rcpt_form" style="display:inline" target="_blank" action="'.base_url('admin/print_all_students_fee/monthly').'">
                <input type="hidden" name="all_selected_rcpts" id="all_selected_rcpts" class="hidden" value=""/>
                <input type="submit" name="all_selected_rcpt_submit" class="btn btn-warning btn-sm" value="Print Selected"/>
            </form>
            ';

            $output->add_button = '<a class="btn btn-success btn-sm" href="'.base_url("admin/add_monthly_fees").'"> Insert </a>  | <a class="btn btn-primary btn-sm" href="'.base_url("admin/yearly_fees_report").'"> Yearly Fees </a> | <a class="btn btn-info btn-sm" href="'.base_url("admin/new_admission_fees_report").'"> New Adm. Fees </a> | ' . $form;
//            $output->add_button = '<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">Insert</button> | <a class="btn btn-primary btn-sm" href="'.base_url("admin/yearly_fees_report").'"> Yearly Fees </a> | <a class="btn btn-info btn-sm" href="'.base_url("admin/new_admission_fees_report").'"> New Adm. Fees </a>';
                                $this->db->select('class_sec_hdr.Class_Name, class_sec_hdr.Sec_Name, student_details.STD_FNAME,
                                student_details.STD_MNAME,student_details.STD_LNAME, student_details.STD_REGNO,student_details.STD_SEQ');
                                $this->db->join('class_sec_hdr','class_sec_hdr.CS_SEQ=student_details.STD_CS_SEQ');
                                $this->db->order_by('STD_CS_SEQ,STD_ROLLNO', 'asc');
            $output->st_array = $this->db->get_where('student_details',array('STD_LEFT' => 0))->result();
            $output->url = base_url('admin/add_monthly_fees/');
            return array('page'=>'common_transaction_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_reg_no($value, $row){

        if ($row->FM_HDR_STD_SEQ != 0) {
            $this->db->select('STD_REGNO');
            $this->db->where('STD_SEQ', $row->FM_HDR_STD_SEQ);
            $reg_no = $this->db->get("student_details")->row();
            if (!empty($reg_no)) {
                return $reg_no->STD_REGNO;
            }else{
                return '';
            }
            
        }
    }
    public function _callback_roll_no($value, $row){
        if ($row->FM_HDR_STD_SEQ != 0) {
            $this->db->select('STD_ROLLNO');
            $this->db->where('STD_SEQ', $row->FM_HDR_STD_SEQ);
            $roll_no = $this->db->get("student_details")->row();

            //echo "<pre>"; print_r($roll_no);
            if (!empty($roll_no)) {
                return $roll_no->STD_ROLLNO;
            }else{
                return '';
            }
        }
    }
    public function _callback_BeforeUpdate_monthly_fees_report($post_array, $primary_key){
        $encash_date = date('Y-m-d', strtotime(str_replace('/','-',$post_array['encash_date'])));
        //fetch old details
        $this->db->where('FM_HDR_SRLNO', $primary_key);
        $rs = $this->db->get('fees_monthly_hdr')->row();

        //if encashment date modified
        if($post_array['encash_date'] != NULL && $encash_date != $rs->encash_date) {
            $post_array['FM_HDR_COL_DATE'] = $encash_date;

            //update dtl table
            unset($data_update);
            $data_update['FEES_DTL_COL_DATE'] = $encash_date;
            $this->db->where('FEES_DTL_HDR_SRLNO', $primary_key);
            $this->db->update('fees_monthly_dtl', $data_update);
        }

        return $post_array;
    }
    
    public function log_user_before_monthly_delete($primary_key)
{
    $this->db->where('FEES_DTL_HDR_SRLNO',$primary_key)->delete('fees_monthly_dtl');
    return true;
}
    

    public function yearly_fees_report() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Transactions/yearly_fees_report'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Yearly Fees Report');
            $crud->order_by('FM_HDR_SRLNO', 'DESC');
            $crud->set_table('fees_yearly_hdr');
            $crud->unset_export();
            // $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_read();
//            $crud->unset_edit();

            $crud->columns('FM_HDR_RCPT_NO','STD_REGNO','FM_HDR_STD_CS_SEQ','STD_ROLLNO','FM_HDR_STD_SEQ','FM_HDR_LATE_FEES','FM_HDR_TOT_FEES','FM_HDR_P_TYP','FM_HDR_COL_DATE','FM_HDR_B_NAME');
            $crud->fields('encash_date', 'FM_HDR_COL_DATE');
            $crud->field_type('FM_HDR_COL_DATE', 'hidden');

            $crud->display_as('FM_HDR_RCPT_NO', 'Recp. No');
            $crud->display_as('FM_HDR_STD_SEQ', 'Student Name');
            $crud->display_as('FM_HDR_STD_CS_SEQ', 'Class & Sec');
            $crud->display_as('FM_HDR_LATE_FEES', 'Late Fine');
            $crud->display_as('FM_HDR_TOT_FEES', 'Total Fees');
            $crud->display_as('FM_HDR_COL_DATE', 'Collection Date');
            $crud->display_as('encash_date', 'Cheque Encashment Date <small style="color: red">Cution: If this date modified, <u>Collection Date</u> will be changed to this date!</small>');
            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('FM_HDR_P_TYP', 'Payment Type');
            $crud->display_as('FM_HDR_B_NAME', 'Bank Name');

            $crud->set_relation('FM_HDR_STD_SEQ', 'student_details', 'ST_FULL_NAME');
            $crud->set_relation('FM_HDR_STD_CS_SEQ', 'class_sec_hdr', 'class_sec');

            $crud->add_action('Print', base_url().'assets/grocery_crud/themes/flexigrid/css/images/print.png', 'admin/print_yearly_fess');

            if(isset($_POST['search_text']) && !empty($_POST['search_text']))  {
                    $crud->or_like('jfff7ee35.STD_REGNO', $_POST['search_text']);
                    $crud->or_like('jfff7ee35.STD_ROLLNO', $_POST['search_text']);
            }
            if(isset($_POST['search_collection_date']) && !empty($_POST['search_collection_date']))  {
                    
                    // $search_collection_date = str_replace('/', '-', );
                    $search_collection_date = date("Y-m-d", strtotime($_POST['search_collection_date']));
                    // echo $search_collection_date; die();
                    $crud->where('FM_HDR_COL_DATE', $search_collection_date);

                    // echo $search_collection_date; die();
            }

            if(isset($_POST['school_list']) && !empty($_POST['school_list']))  {
                // $search_collection_date = str_replace('/', '-', );
                $school_list = $_POST['school_list'];
                // echo $search_collection_date; die();
                                    $this->db->select('CS_SEQ');
                $school_list_data = $this->db->get_where('class_sec_hdr', array('Class_Type'=>$school_list))->result_array();
                $scl_list_array = array();
                foreach ($school_list_data as $key => $school_list_data) {
                  $scl_list_array[] =  $school_list_data['CS_SEQ'];
                }
                // echo "<pre>"; print_r($scl_list_array); die();
                $scl_list_array = implode(',', $scl_list_array);
                $where = "FM_HDR_STD_CS_SEQ IN ($scl_list_array)";
                $crud->where($where);
                // $crud->where('FM_HDR_STD_CS_SEQ', $where);
                // echo $search_collection_date; die();
            }
            // echo $this->db->query();

            $crud->callback_column('STD_REGNO',array($this,'_callback_reg_no'));
            $crud->callback_column('STD_ROLLNO',array($this,'_callback_roll_no'));
            
            
            $crud->callback_before_delete(array($this,'log_user_before_yearly_delete'));
            $crud->callback_before_update(array($this, '_callback_BeforeUpdate_yearly_fees_report'));

            $output = $crud->render();

            $output->output = str_replace('title="Print"', 'title="Print" target="_blank"', $output->output); //additional line
            //rending extra value to $output
            $output->tab_title = 'Yearly Fees Report';
            $output->section_heading = '';
            $output->menu_name = 'Yearly Fees Report';
            $form = '
            <form method="post" id="all_selected_rcpt_form" style="display:inline" target="_blank" action="'.base_url('admin/print_all_students_fee/yearly').'">
                <input type="hidden" name="all_selected_rcpts" id="all_selected_rcpts" class="hidden" value=""/>
                <input type="submit" name="all_selected_rcpt_submit" class="btn btn-warning btn-sm" value="Print Selected"/>
            </form>
            ';
            $output->add_button = '<a class="btn btn-success btn-sm" href="'.base_url("admin/add_yearly_fees").'"> Insert </a> | <a class="btn btn-primary btn-sm" href="'.base_url("admin/monthly_fees_report").'"> Monthly Fees </a> | <a class="btn btn-info btn-sm" href="'.base_url("admin/new_admission_fees_report").'"> New Adm. Fees </a> | ' . $form;
                                $this->db->select('class_sec_hdr.Class_Name, class_sec_hdr.Sec_Name, student_details.STD_FNAME,
                                student_details.STD_MNAME,student_details.STD_LNAME, student_details.STD_REGNO,student_details.STD_SEQ');
                                $this->db->join('class_sec_hdr','class_sec_hdr.CS_SEQ=student_details.STD_CS_SEQ');
                                $this->db->order_by('STD_CS_SEQ,STD_ROLLNO', 'asc');
            $output->st_array = $this->db->get_where('student_details',array('STD_LEFT' => 0))->result();

            $output->url = base_url('admin/add_yearly_fees/');

            return array('page'=>'common_transaction_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_BeforeUpdate_yearly_fees_report($post_array, $primary_key){
        $encash_date = date('Y-m-d', strtotime(str_replace('/','-',$post_array['encash_date'])));
        //fetch old details
        $this->db->where('FM_HDR_SRLNO', $primary_key);
        $rs = $this->db->get('fees_yearly_hdr')->row();

        //if encashment date modified
        if($post_array['encash_date'] != NULL && $encash_date != $rs->encash_date) {
            $post_array['FM_HDR_COL_DATE'] = $encash_date;

            //update dtl table
            unset($data_update);
            $data_update['FEES_DTL_COL_DATE'] = $encash_date;
            $this->db->where('FEES_DTL_HDR_SRLNO', $primary_key);
            $this->db->update('fees_yearly_dtl', $data_update);
        }

        return $post_array;
    }

    public function log_user_before_yearly_delete($primary_key)
{
    $this->db->where('FEES_DTL_HDR_SRLNO',$primary_key)->delete('fees_yearly_dtl');
    return true;
}
    

    public function new_admission_fees_report() {
        try{
            $this->url = 'admin/print_new_admission_fess';
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Transactions/new_admission_fees_report'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('New Admission Fees Report');
            $crud->order_by('FM_HDR_SRLNO', 'DESC');
            $crud->set_table('fees_newadm_hdr');
            $crud->unset_export();
            // $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_read();
//            $crud->unset_edit();

            $crud->columns('FM_HDR_RCPT_NO','STD_REGNO','FM_HDR_STD_CS_SEQ','STD_ROLLNO','FM_HDR_STD_SEQ','FM_HDR_LATE_FEES','FM_HDR_TOT_FEES','FM_HDR_P_TYP','FM_HDR_COL_DATE','FM_HDR_B_NAME');
            $crud->fields('encash_date', 'FM_HDR_COL_DATE');
            $crud->field_type('FM_HDR_COL_DATE', 'hidden');

            $crud->display_as('FM_HDR_RCPT_NO', 'Recp. No');
            $crud->display_as('FM_HDR_STD_SEQ', 'Student Name');
            $crud->display_as('FM_HDR_STD_CS_SEQ', 'Class & Sec');
            $crud->display_as('FM_HDR_LATE_FEES', 'Late Fine');
            $crud->display_as('FM_HDR_TOT_FEES', 'Total Fees');
            $crud->display_as('encash_date', 'Cheque Encashment Date <small style="color: red">Cution: If this date modified, <u>Collection Date</u> will be changed to this date!</small>');
            $crud->display_as('FM_HDR_COL_DATE', 'Collection Date');
            $crud->display_as('FM_HDR_P_TYP', 'Payment Type');
            $crud->display_as('FM_HDR_B_NAME', 'Bank Name');

            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_ROLLNO', 'Roll No');

            $crud->set_relation('FM_HDR_STD_SEQ', 'student_details', 'ST_FULL_NAME');
            $crud->set_relation('FM_HDR_STD_CS_SEQ', 'class_sec_hdr', 'class_sec');

            $crud->add_action('Print', base_url().'assets/grocery_crud/themes/flexigrid/css/images/print.png', 'admin/print_new_admission_fess');

            // $crud->add_action('Print', base_url().'assets/grocery_crud/themes/flexigrid/css/images/print.png', '','',array($this,'get_student_id'));

            // $crud->add_action('Photos', '', '','fa fa-print',array($this,'get_student_id'));

            if(isset($_POST['search_text']) && !empty($_POST['search_text']))  {
                    $crud->or_like('jfff7ee35.STD_REGNO', $_POST['search_text']);
                    $crud->or_like('jfff7ee35.STD_ROLLNO', $_POST['search_text']);
            }

            if(isset($_POST['search_collection_date']) && !empty($_POST['search_collection_date']))  {
                // $search_collection_date = str_replace('/', '-', );
                $search_collection_date = date("Y-m-d", strtotime($_POST['search_collection_date']));
                // echo $search_collection_date; die();
                $crud->where('FM_HDR_COL_DATE', $search_collection_date);
                // echo $search_collection_date; die();
            }
            if(isset($_POST['school_list']) && !empty($_POST['school_list']))  {
                // $search_collection_date = str_replace('/', '-', );
                $school_list = $_POST['school_list'];
                // echo $search_collection_date; die();
                                    $this->db->select('CS_SEQ');
                $school_list_data = $this->db->get_where('class_sec_hdr', array('Class_Type'=>$school_list))->result_array();
                $scl_list_array = array();
                foreach ($school_list_data as $key => $school_list_data) {
                  $scl_list_array[] =  $school_list_data['CS_SEQ'];
                }
                // echo "<pre>"; print_r($scl_list_array); die();
                $scl_list_array = implode(',', $scl_list_array);
                $where = "FM_HDR_STD_CS_SEQ IN ($scl_list_array)";
                $crud->where($where);
                // $crud->where('FM_HDR_STD_CS_SEQ', $where);
                // echo $search_collection_date; die();
            }

            $crud->callback_column('STD_REGNO',array($this,'_callback_reg_no'));
            $crud->callback_column('STD_ROLLNO',array($this,'_callback_roll_no'));
            
            
            $crud->callback_before_delete(array($this,'log_user_before_newadms_delete'));
            $crud->callback_before_update(array($this, '_callback_BeforeUpdate_new_admission_fees_report'));

            $output = $crud->render();

            $output->output = str_replace('title="Print"', 'title="Print" target="_blank"', $output->output); //additional line
            //rending extra value to $output
            $output->tab_title = 'New Admission Fees Report';
            $output->section_heading = '';
            $output->menu_name = 'New Admission Fees Report';
            $form = '
            <form method="post" id="all_selected_rcpt_form" style="display:inline" target="_blank" action="'.base_url('admin/print_all_students_fee/new_admission').'">
                <input type="hidden" name="all_selected_rcpts" id="all_selected_rcpts" class="hidden" value=""/>
                <input type="submit" name="all_selected_rcpt_submit" class="btn btn-warning btn-sm" value="Print Selected"/>
            </form>
            ';
            $output->add_button = '<a class="btn btn-success btn-sm" href="'.base_url("admin/add_new_admission_fees").'"> Insert </a> | <a class="btn btn-primary btn-sm" href="'.base_url("admin/monthly_fees_report").'"> Monthly Fees </a> | <a class="btn btn-info btn-sm" href="'.base_url("admin/yearly_fees_report").'"> Yearly Fees </a> | ' . $form;

//            $output->add_button = '<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">Insert</button> | <a class="btn btn-primary btn-sm" href="'.base_url("admin/monthly_fees_report").'"> Monthly Fees </a> | <a class="btn btn-info btn-sm" href="'.base_url("admin/yearly_fees_report").'"> Yearly Fees </a>';

            $this->db->select('class_sec_hdr.Class_Name, class_sec_hdr.Sec_Name, student_details.STD_FNAME,
            student_details.STD_MNAME,student_details.STD_LNAME, student_details.STD_REGNO,student_details.STD_SEQ');
            $this->db->join('class_sec_hdr','class_sec_hdr.CS_SEQ=student_details.STD_CS_SEQ');
            $this->db->order_by('STD_CS_SEQ,STD_ROLLNO', 'asc');
            $output->st_array = $this->db->get_where('student_details',array('STD_LEFT' => 0))->result();
            $output->url = base_url('admin/add_new_admission_fees/');

            return array('page'=>'common_transaction_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_BeforeUpdate_new_admission_fees_report($post_array, $primary_key){
        $encash_date = date('Y-m-d', strtotime(str_replace('/','-',$post_array['encash_date'])));
        //fetch old details
        $this->db->where('FM_HDR_SRLNO', $primary_key);
        $rs = $this->db->get('fees_newadm_hdr')->row();

        //if encashment date modified
        if($post_array['encash_date'] != NULL && $encash_date != $rs->encash_date) {
            $post_array['FM_HDR_COL_DATE'] = $encash_date;

            //update dtl table
            unset($data_update);
            $data_update['FEES_DTL_COL_DATE'] = $encash_date;
            $this->db->where('FEES_DTL_HDR_SRLNO', $primary_key);
            $this->db->update('fees_newadm_dtl', $data_update);
        }

        return $post_array;
    }
    
    public function log_user_before_newadms_delete($primary_key)
{
    $this->db->where('FEES_DTL_HDR_SRLNO',$primary_key)->delete('fees_newadm_dtl');
    return true;
}
    

    public function getIndianCurrency($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal) ? " and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees' : '') . $paise.' Only';
    }



    
    public function print_yearly_fess($hdr_id) {
        $this->db->where('FM_HDR_SRLNO', $hdr_id);
        $hdr = $this->db->get('fees_yearly_hdr')->row();
        if(count((array)$hdr) == 0) { //if transaction does not exists
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Transaction not found.');
            return array('type'=>'redirect', 'page'=>'admin/yearly_fees_report');
        }

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_m = $this->db->get('fees_monthly_hdr')->result();

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_y = $this->db->get('fees_yearly_hdr')->result();

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_n = $this->db->get('fees_newadm_hdr')->result();
        $this->print_fees($hdr, 'year');
        if (@count($hdr_m) > 0 or @count($hdr_n) > 0) {
            $this->print_fees($hdr, 'year');
        }else{
        $this->db->where('FEES_DTL_HDR_SRLNO', $hdr_id);
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_yearly_dtl.FEES_DTL_ACC_SEQ', 'left');
        $dtl = $this->db->get('fees_yearly_dtl')->result_array();
        $this->db->where('STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $std = $this->db->get('student_details')->row();

        // $company = $this->db->get_where('company', array('SCHOOL_TYPE' => $std->Class_Type))->row();
        $company = $this->company_name((array)$std->STD_CS_SEQ);

        $amount_in_word = ucwords($this->getIndianCurrency($hdr->FM_HDR_TOT_FEES));

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = $hdr->FM_HDR_RCPT_NO.' | '.$std->STD_FNAME.' '.$std->STD_MNAME.' '.$std->STD_LNAME.' (Reg. No: '.$std->STD_REGNO.') | '.$std->Class_Name.' - '.$std->Sec_Name;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Yearly Fees Receipt');
        $pdf->SetKeywords('yearly fees, smg, developed by: https://sketchmeglobal.com');

        // set default header data
        $col_dt = date('d-m-Y', strtotime($hdr->FM_HDR_COL_DATE));
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>Money Receipt (Yearly): <span style="background-color: black;color: white;"> $hdr->FM_HDR_RCPT_NO </span></strong>
        <br>
        Name: <strong>$std->STD_FNAME $std->STD_MNAME $std->STD_LNAME</strong> • Class & Sec: <strong>$std->Class_Name - $std->Sec_Name</strong> • Roll: <strong>$std->STD_ROLLNO</strong> • Reg. No: <strong>$std->STD_REGNO-$std->STD_SRLNO</strong> • Date: <strong>$col_dt</strong>
        </div>
        <hr>
EOD;
        $pdf->setHtmlHeader($html_header, true);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 35, 10);
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
        $pdf->SetFont('times', '', 14, '', true);

        // Add a page
        $pdf->AddPage('P', 'A4');

        // Set some content to print
        $html = <<<EOD
<div>
Session: <strong>$hdr->FM_HDR_FIN_YEAR</strong>
</div>
<br>
<table>
    <thead>
    <tr>
        <th><strong>Fees Name</strong></th>
        <th style="text-align: right"><strong>Amount</strong></th>
    </tr>
    </thead>
    <tbody>
EOD;
        foreach($dtl as $d) {
            $html .= '<tr>
                        <td>'.$d['ACC_MASTER_NAME'].'</td>
                        <td style="text-align: right">'.$d['FEES_DTL_AMOUNT'].'</td>
                    </tr>';
        }

        $html .= <<<EOD
        <tr>
            <td>Late Fine</td>
            <td style="text-align: right">$hdr->FM_HDR_LATE_FEES</td>
        </tr>
        <tr>
            <td>Concession/Discount</td>
            <td align="right">$hdr->FM_HDR_CONC_FEES</td>
        </tr>
        <tr>
            <th><strong>Total Fees</strong></th>
            <th style="text-align: right;"><strong style="background-color: black;color: white;"> $hdr->FM_HDR_TOT_FEES </strong></th>
        </tr>
        <tr>
            <th><strong>Total Fees in Words</strong></th>
            <th style="text-align: right"><strong>$amount_in_word</strong></th>
        </tr>
    </tbody>
</table>

<div>
<br>
<span style="text-align: right"><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><span>
<br>
<span style="text-align: right">Collector's Signature<span>
</div>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name.'.pdf', 'I');
        }


        

    }


    public function print_new_admission_fess($hdr_id) {
        $this->db->where('FM_HDR_SRLNO', $hdr_id);
        $hdr = $this->db->get('fees_newadm_hdr')->row();
        if(count((array)$hdr) == 0) { //if transaction does not exists
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Transaction not found.');
            return array('type'=>'redirect', 'page'=>'admin/new_admission_fees_report');
        }

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_m = $this->db->get('fees_monthly_hdr')->result();

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_y = $this->db->get('fees_yearly_hdr')->result();

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_n = $this->db->get('fees_newadm_hdr')->result();
        $this->print_fees($hdr, 'new');
        if (@count($hdr_m) > 0 or @count($hdr_y) > 0) {
            $this->print_fees($hdr, 'new');
        }else{
            $this->db->where('FEES_DTL_HDR_SRLNO', $hdr_id);
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_newadm_dtl.FEES_DTL_ACC_SEQ', 'left');
        $dtl = $this->db->get('fees_newadm_dtl')->result_array();
        $this->db->where('STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $std = $this->db->get('student_details')->row();


        $class_type = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $std->STD_CS_SEQ))->row()->Class_Type;
        $company = $this->company_name((array)$std->STD_CS_SEQ);
        // $company = $this->db->get_where('company', array('SCHOOL_TYPE' => $class_type))->row();
        $amount_in_word = ucwords($this->getIndianCurrency($hdr->FM_HDR_TOT_FEES));

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = $hdr->FM_HDR_RCPT_NO.' | '.$std->ST_FULL_NAME.' (Reg. No: '.$std->STD_REGNO.') | '.$std->Class_Name.' - '.$std->Sec_Name;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('New Admission Fees Receipt');
        $pdf->SetKeywords('new admission fees, smg, developed by: https://sketchmeglobal.com');

        // set default header data
        $col_dt = date('d-m-Y', strtotime($hdr->FM_HDR_COL_DATE));
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>Money Receipt (New Admission): <span style="background-color: black;color: white;"> $hdr->FM_HDR_RCPT_NO </span></strong>
        <br>
        Name: <strong>$std->ST_FULL_NAME</strong> • Class & Sec: <strong>$std->Class_Name - $std->Sec_Name</strong> • Roll: <strong>$std->STD_ROLLNO</strong> • Reg. No: <strong>$std->STD_REGNO-$std->STD_SRLNO</strong> • Date: <strong>$col_dt</strong>
        </div>
        <hr>
EOD;
        $pdf->setHtmlHeader($html_header, true);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 35, 10);
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
        $pdf->SetFont('times', '', 14, '', true);

        // Add a page
        $pdf->AddPage('P', 'A4');

        // Set some content to print
        $html = <<<EOD
<div>
Session: <strong>$hdr->FM_HDR_FIN_YEAR</strong>
</div>
<br>
<table>
    <thead>
    <tr>
        <th><strong>Fees Name</strong></th>
        <th style="text-align: right"><strong>Amount</strong></th>
    </tr>
    </thead>
    <tbody>
EOD;
        foreach($dtl as $d) {
            $html .= '<tr>
                        <td>'.$d['ACC_MASTER_NAME'].'</td>
                        <td style="text-align: right">'.$d['FEES_DTL_AMOUNT'].'</td>
                    </tr>';
        }

        $html .= <<<EOD
        <tr>
            <td>Late Fine</td>
            <td style="text-align: right">$hdr->FM_HDR_LATE_FEES</td>
        </tr>
        <tr>
            <td>Concession/Discount</td>
            <td align="right">$hdr->FM_HDR_CONC_FEES</td>
        </tr>
        <tr>
            <th><strong>Total Fees</strong></th>
            <th style="text-align: right;"><strong style="background-color: black;color: white;"> $hdr->FM_HDR_TOT_FEES </strong></th>
        </tr>
        <tr>
            <th><strong>Total Fees in Words</strong></th>
            <th style="text-align: right"><strong>$amount_in_word</strong></th>
        </tr>
    </tbody>
</table>

<div>
<br>
<span style="text-align: right"><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><span>
<br>
<span style="text-align: right">Collector's Signature<span>
</div>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name.'.pdf', 'I');
        }


        
    }


    public function voucher_entry() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Transactions/voucher_entry'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Voucher Entry');
            $crud->order_by('date', 'DESC');
            $crud->set_table('voucher_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('ref_no', 'remark', 'total_cr', 'total_dr', 'date');
            $crud->display_as('ref_no', 'Ref. No');
            $crud->display_as('total_cr', 'Total Credit');
            $crud->display_as('total_dr', 'Total Debit');

            $crud->add_action('Edit', base_url().'assets/grocery_crud/themes/flexigrid/css/images/edit.png', 'admin/edit_voucher_entry');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Voucher Entry';
            $output->section_heading = 'Voucher Entry <small>(Add / Edit)</small>';
            $output->menu_name = 'Voucher Entry';
            $output->add_button = '<a href="'.base_url('admin/add_voucher_entry').'" class="btn btn-success" role="button">New Voucher Entry</a><br><br>';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function add_voucher_entry() {
        $this->db->select('ACC_MASTER_CODE, ACC_MASTER_NAME');
        $rs_acc_master = $this->db->get('acc_master')->result_array();

        $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'voucher_hdr'")->row()->Auto_increment;
        $string = 'V'.$get_auto_index.strtoupper(uniqid());
        $ref_no = substr($string, 0, 15);

        $this->session->set_userdata('voucher_ref_no', $ref_no);

        $data['rs_acc_master'] = $rs_acc_master;
        $data['ref_no'] = $ref_no;
        $data['form_type'] = 'add_voucher_entry';

        $data['tab_title'] = 'Add Voucher Entry';
        $data['section_heading'] = 'Add Voucher Entry';
        $data['menu_name'] = 'Add Voucher Entry';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function form_add_voucher_entry() {
        //if form not submitted
        if($this->input->post('submit') != 'submit_add_voucher_entry_form') {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/voucher_entry');
        }

        $remark = $this->input->post('remark');
        $date = $this->input->post('date');
        $acc_master = $this->input->post('acc_master');
        $cr_dr = $this->input->post('cr_dr');
        $amount = $this->input->post('amount');

        $voucher_ref_no = $this->session->userdata('voucher_ref_no');

        //Check if credit amount = debit amount
        $tot_cr = 0; $tot_dr = 0;
        foreach($amount as $key=>$val) {
            if($cr_dr[$key] == 1) { //credit amount
                $tot_cr += $val;
            } else { //debit amount
                $tot_dr += $val;
            }
        }
        //if total credit amount and total debit amount is not equal
        if($tot_cr != $tot_dr) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Credit & Debit Is Not Equal!');
            $this->session->set_flashdata('msg', 'Total Credit Amount (cr: '.$tot_cr.') must be equals to Total Debit Amount (dr: '.$tot_dr.').');
            return array('page'=>'admin/add_voucher_entry');
        }

        $data_insert_hdr['ref_no'] = $voucher_ref_no;
        $data_insert_hdr['remark'] = $remark;
        $data_insert_hdr['total_cr'] = $tot_cr;
        $data_insert_hdr['total_dr'] = $tot_dr;
        $data_insert_hdr['date'] = $date;
        //inserting data
        $this->db->insert('voucher_hdr', $data_insert_hdr);
        $vchr_hdr_id= $this->db->insert_id();

        foreach ($acc_master as $key=>$val) {
            $data_insert_dtl['vchr_hdr_id'] = $vchr_hdr_id;
            $data_insert_dtl['ACC_MASTER_CODE'] = $val;
            $data_insert_dtl['cr_dr'] = $cr_dr[$key];
            $data_insert_dtl['amount'] = $amount[$key];
            //inserting data
            $this->db->insert('voucher_dtl', $data_insert_dtl);
        }

        $this->session->unset_userdata(array('voucher_ref_no'));

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('msg', 'Details saved successfully.');
        return array('page'=>'admin/voucher_entry');
    }

    public function edit_voucher_entry($vchr_hdr_id) {
        $this->db->where('vchr_hdr_id', $vchr_hdr_id);
        $row_vchr_hdr = $this->db->get('voucher_hdr')->row();
        //if that voucher id does not exists
        if(count((array)$row_vchr_hdr) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'No such transaction found.');
            return array('type'=>'redirect', 'page'=>'admin/voucher_entry');
        }

        $this->db->where('vchr_hdr_id', $vchr_hdr_id);
        $rs_vchr_dtl = $this->db->get('voucher_dtl')->result_array();

        $this->db->select('ACC_MASTER_CODE, ACC_MASTER_NAME');
        $rs_acc_master = $this->db->get('acc_master')->result_array();

        $data['row_vchr_hdr'] = $row_vchr_hdr;
        $data['rs_vchr_dtl'] = $rs_vchr_dtl;
        $data['rs_acc_master'] = $rs_acc_master;
        $data['form_type'] = 'edit_voucher_entry';

        $data['tab_title'] = 'Edit Voucher Entry';
        $data['section_heading'] = 'Edit Voucher Entry';
        $data['menu_name'] = 'Edit Voucher Entry';

        return array('type'=>'load_view', 'page'=>'accounts_v', 'data'=>$data);
    }

    public function form_edit_voucher_entry() {
        //if form not submitted
        if($this->input->post('submit') != 'submit_edit_voucher_entry_form') {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page' => 'admin/voucher_entry');
        }

        $vchr_hdr_id = $this->input->post('vchr_hdr');
        $remark = $this->input->post('remark');
        $date = $this->input->post('date');
        $vchr_dtl_id = $this->input->post('vchr_dtl');
        $cr_dr = $this->input->post('cr_dr');
        $amount = $this->input->post('amount');

        //Check if credit amount = debit amount
        $tot_cr = 0; $tot_dr = 0;
        foreach($amount as $key=>$val) {
            if($cr_dr[$key] == 1) { //credit amount
                $tot_cr += $val;
            } else { //debit amount
                $tot_dr += $val;
            }
        }
        //if total credit amount and total debit amount is not equal
        if($tot_cr != $tot_dr) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Credit & Debit Is Not Equal!');
            $this->session->set_flashdata('msg', 'Total Credit Amount (cr: '.$tot_cr.') must be equals to Total Debit Amount (dr: '.$tot_dr.').');
            return array('page'=>'admin/edit_voucher_entry/'.$vchr_hdr_id);
        }

        $data_update_hdr['remark'] = $remark;
        $data_update_hdr['total_cr'] = $tot_cr;
        $data_update_hdr['total_dr'] = $tot_dr;
        $data_update_hdr['date'] = $date;
        //updating details
        $this->db->where('vchr_hdr_id', $vchr_hdr_id);
        $this->db->update('voucher_hdr', $data_update_hdr);

        foreach ($vchr_dtl_id as $key=>$val) {
            $data_update_dtl['cr_dr'] = $cr_dr[$key];
            $data_update_dtl['amount'] = $amount[$key];
            //updating details
            $this->db->where('vchr_dtl_id', $val);
            $this->db->update('voucher_dtl', $data_update_dtl);
        }

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('msg', 'Details updated.');
        return array('page'=>'admin/voucher_entry');
    }

    public function print_fees($hdr, $type){

        // echo $hdr->FM_HDR_STD_SEQ; die();

        $this->db->where('STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $std = $this->db->get('student_details')->row();        
        $company = $this->company_name((array)$std->STD_CS_SEQ);
        // echo "<pre>"; print_r($company); die();
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_newadm_dtl.FEES_DTL_ACC_SEQ', 'left');
        $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        // $this->db->group_by('FEES_DTL_COL_DATE');
        $tbl_newadm_dtl = $this->db->get('fees_newadm_dtl')->result_array();


        // echo "<pre>"; print_r($tbl_newadm_dtl); die();
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_yearly_dtl.FEES_DTL_ACC_SEQ', 'left');
        $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        // $this->db->group_by('FEES_DTL_COL_DATE');
        $tbl_yearly_dtl = $this->db->get('fees_yearly_dtl')->result_array();

        // echo "<pre>"; print_r($tbl_yearly_dtl); die();

        $this->db->select('acc_master.*, fees_monthly_dtl.*,  SUM(FEES_DTL_AMOUNT) as FEES_DTL_AMOUNT');
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_monthly_dtl.FEES_DTL_ACC_SEQ', 'left');
        $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->group_by('FEES_DTL_ACC_SEQ');
        $tbl_monthly_dtl = $this->db->get('fees_monthly_dtl')->result_array();



         // echo "<pre>"; print_r($tbl_monthly_dtl); die();

        /*Total Fees And Late Fine*/
        $total_fs = 0.00;
        $total_late_fs = 0.00;

        $this->db->select('SUM(FM_HDR_TOT_FEES) as total_fees, SUM(FM_HDR_LATE_FEES) as total_late_fees, FM_HDR_RCPT_NO');
        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_m = $this->db->get('fees_monthly_hdr')->result();
        if(@count($hdr_m) > 0){

            foreach ($hdr_m as $hdr_m_r) {
                $total_fs += $hdr_m_r->total_fees;
                $total_late_fs += $hdr_m_r->total_late_fees;
            }
        }
        $this->db->select('SUM(FM_HDR_TOT_FEES) as total_fees, SUM(FM_HDR_LATE_FEES) as total_late_fees, FM_HDR_RCPT_NO');
        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_y = $this->db->get('fees_yearly_hdr')->result();

        if(@count($hdr_y) > 0){

            foreach ($hdr_y as $hdr_y_r) {
                $total_fs += $hdr_y_r->total_fees;
                $total_late_fs += $hdr_y_r->total_late_fees;
            }
        }
        $this->db->select('SUM(FEES_DTL_AMOUNT) as total_fees');
        $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_n = $this->db->get('fees_newadm_dtl')->result();

        if(@count($hdr_n) > 0){

            foreach ($hdr_n as $hdr_n_r) {
                $total_fs += $hdr_n_r->total_fees;
                //$total_late_fs += $hdr_n_r->total_late_fees;
            }

            
        }

        // echo $total_late_fs; die();

        /*------------------------*/

        /*Receipt no*/
        $this->db->select('FM_HDR_RCPT_NO, FM_HDR_CARD_NO, encash_date');
        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_m_recpt = $this->db->get('fees_monthly_hdr')->result();

        $this->db->select('FM_HDR_RCPT_NO, FM_HDR_CARD_NO, encash_date');
        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_y_recpt = $this->db->get('fees_yearly_hdr')->result();

        $this->db->select('FM_HDR_RCPT_NO, FM_HDR_CARD_NO, encash_date');
        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_n_recpt = $this->db->get('fees_newadm_hdr')->result();

        /*----------*/
        if(count((array)$std) == 0) { //if transaction does not exists
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Transaction not found.');
            return array('type'=>'redirect', 'page'=>'admin/monthly_fees_report');
        }

        $tbl_row =  max( @count($tbl_newadm_dtl), @count($tbl_yearly_dtl), @count($tbl_monthly_dtl) );


        // echo "<pre>"; print_r($tbl_row); die();

        

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = $std->ST_FULL_NAME.' (Reg. No: '.$std->STD_REGNO.') | '.$std->Class_Name.' - '.$std->Sec_Name;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('All Fees Receipt');
        $pdf->SetKeywords('all  fees, smg, developed by: sketchmeglobal.com');

        // set default header data
        $col_dt = date('d-m-Y', strtotime($hdr->FM_HDR_COL_DATE));
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 17px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong><span style="background-color: black;color: white; font-size: 10px;"> Money Receipt </span></strong>
        <br>
        <span style="font-family:Times New Roman;">
        Name: <strong >$std->ST_FULL_NAME</strong> • Class & Sec: <strong>$std->Class_Name - $std->Sec_Name</strong> • Roll: <strong>$std->STD_ROLLNO</strong> • Reg. No: <strong>$std->STD_REGNO-$std->STD_SRLNO</strong> • Date: <strong>$col_dt</strong>
        </span>
        </div>
        <hr>
EOD;
        $pdf->SetPrintFooter(false);
        $pdf->SetAlpha(0);
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(8, 35, 5);
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
        $pdf->SetFont('helveticaB', '', 8, '', true);

        //$pdf->addTTFfont('/path-to-font/verdana.ttf', 'TrueTypeUnicode', '', 32);

        // Add a page
        $pdf->AddPage('P', 'A4');

        // Set some content to print

        // echo max( count($arr1), count($arr2), count($arr3) )
        $mnt_fees = 0.00;
        //$tot_mon = 1;

        $this->db->select('COUNT(FEES_DTL_ACC_SEQ) as total_rows');
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->group_by('FEES_DTL_ACC_SEQ');
        $total_rows = $this->db->get('fees_monthly_dtl')->row();

        $amount_in_word = 'RUPEES '.strtoupper($this->getIndianCurrency((int)$total_fs));

        //echo "<pre>"; print_r($total_rows->total_rows); die();

        if(empty($total_rows->total_rows)){
            $tot_mon = 0.00;
        }else{
            $tot_mon = $total_rows->total_rows;
        }

        

        $yer_fees = 0.00;

        // $tot_mon = 0.00;

        $nwadm_fees = 0.00;

        $this->db->select('FEES_DTL_MONTH');
//        $this->db->where('FEES_DTL_HDR_SRLNO', $hdr->FM_HDR_SRLNO);
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->group_by('FEES_DTL_MONTH');
        $paid_months_id = $this->db->get('fees_monthly_dtl')->result_array();
        $paid_months_id = array_unique(array_map(function($value){return $value['FEES_DTL_MONTH'];} , $paid_months_id));

        $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");

        $paid_months = array_intersect($months_arr, $paid_months_id);
//        echo "<pre>"; print_r($paid_months); die();

        $html = '';
        $hhtml = '';
        $hhtml .= <<<EOD
<table>
<tr>
<td width="100">
Fees of Month: 
</td>
<td width="45">
Session
</td>

<td width="1000">
Receipt No
</td>


</tr>
<tr>
<td width="100">
<strong>
EOD;
foreach ($paid_months as $key => $m){
            $hhtml .= $key.' ';
        }
$hhtml .= <<<EOD
</strong>
</td>
<td width="45">
<strong>$company->COM_FIN_YEAR</strong>
</td>

<td width="1000">

EOD;

if(@count($hdr_m_recpt) > 0){
    $hhtml .= <<<EOD
 Monthly 
EOD;
    foreach ($hdr_m_recpt as $value) {

$hhtml .= <<<EOD
  <strong><span style="background-color: black;color: white; font-size: 10px;"> $value->FM_HDR_RCPT_NO </span></strong> 
EOD;
}
}


if(@count($hdr_y_recpt) > 0){
    $hhtml .= <<<EOD
 Annual 
EOD;
    foreach ($hdr_y_recpt as $value) {
$hhtml .= <<<EOD
<strong><span style="background-color: black;color: white; font-size: 10px;"> $value->FM_HDR_RCPT_NO </span></strong>  
EOD;
}
}



if(@count($hdr_n_recpt) > 0){
    $hhtml .= <<<EOD
 Admission 
EOD;
    foreach ($hdr_n_recpt as $value) {
$hhtml .= <<<EOD
<strong><span style="background-color: black;color: white; font-size: 10px;"> $value->FM_HDR_RCPT_NO </span></strong>
EOD;
}
}


$hhtml .= <<<EOD
</td>
</tr>

<tr>
    <td width="210">
Cheque/Card Details (Encashment Date): 
    </td>
    <td width="935">
<strong>
EOD;
        if(@count($hdr_m_recpt) > 0){
            foreach ($hdr_m_recpt as $value) {
                if($value->FM_HDR_CARD_NO != ''){
                    $hhtml .= $value->FM_HDR_CARD_NO.' ';
                }
                if($value->encash_date != NULL && $value->encash_date != '0000-00-00'){
                    $hhtml .= '('.date('d-m-Y', strtotime($value->encash_date)).') ';
                }
            }
        }

        if(@count($hdr_y_recpt) > 0){
            foreach ($hdr_y_recpt as $value) {
                if($value->FM_HDR_CARD_NO != ''){
                    $hhtml .= $value->FM_HDR_CARD_NO.' ';
                }
                if($value->encash_date != NULL && $value->encash_date != '0000-00-00'){
                    $hhtml .= '('.date('d-m-Y', strtotime($value->encash_date)).') ';
                }
            }
        }

        if(@count($hdr_n_recpt) > 0){
            foreach ($hdr_n_recpt as $value) {
                if($value->FM_HDR_CARD_NO != ''){
                    $hhtml .= $value->FM_HDR_CARD_NO.' ';
                }
                if($value->encash_date != NULL && $value->encash_date != '0000-00-00'){
                    $hhtml .= '('.date('d-m-Y', strtotime($value->encash_date)).') ';
                }
            }
        }
        $hhtml .= <<<EOD
</strong>
    </td>
</tr>
</table>

EOD;

// $tbl_newadm_dtl
        $html .= "<br><hr /> <table border='0' cellpadding='6'>
    <thead style='border-bottom: 50px;'>
        <tr>";
        //if(@count($tbl_monthly_dtl) > 0){
        $html.="<th align='center' ><strong>Monthly Fees</strong></th>
        <th align='center'>Amount</th>";
        //}
        //if(@count($tbl_yearly_dtl) > 0){
        $html.="<th align='center'  ><strong>Annual Fees</strong></th>
        <th align='center'>Amount</th>";
        //}

        //if(@count($tbl_newadm_dtl) > 0){
        $html.="<th align='center' ><strong>Other Fees</strong></th>
        <th align='center'>Amount</th>";
       //}

    $html.="</tr>    
    </thead>

    <tbody>";

for ($i=0; $i < $tbl_row; $i++) {
    
   $html .= <<<EOD
<tr>
EOD;

        //if(@count($tbl_monthly_dtl) > 0){
        $html .= <<<EOD
<td >
EOD;
        if (array_key_exists($i,$tbl_monthly_dtl)) {
            $html .= $tbl_monthly_dtl[$i]['ACC_MASTER_NAME'];
        }
        $html .= <<<EOD
</td><td align='center' style='width:20px;'>
EOD;
        if (array_key_exists($i,$tbl_monthly_dtl)) {
            $html .= $tbl_monthly_dtl[$i]['FEES_DTL_AMOUNT'];
            $mnt_fees += $tbl_monthly_dtl[$i]['FEES_DTL_AMOUNT'];
        }
         $html .= <<<EOD
</td>
EOD;
        //}





        //if(@count($tbl_yearly_dtl) > 0){
        $html .= <<<EOD
<td style='width:200px;'>
EOD;
        if (array_key_exists($i,$tbl_yearly_dtl)) {
            $html .= $tbl_yearly_dtl[$i]['ACC_MASTER_NAME'];
        }
        $html .= <<<EOD
</td><td align='center' style='width:20px;'>
EOD;
        if (array_key_exists($i,$tbl_yearly_dtl)) {
            $html .= $tbl_yearly_dtl[$i]['FEES_DTL_AMOUNT'];
            $yer_fees += $tbl_yearly_dtl[$i]['FEES_DTL_AMOUNT'];
        }
         $html .= <<<EOD
</td>
EOD;
        //}
        //if(@count($tbl_newadm_dtl) > 0){
        $html .= <<<EOD
<td style='width:20px;'>
EOD;
        if (array_key_exists($i,$tbl_newadm_dtl)) {
            $html .= $tbl_newadm_dtl[$i]['ACC_MASTER_NAME'];
        }
        $html .= <<<EOD
</td><td>
EOD;
        if (array_key_exists($i,$tbl_newadm_dtl)) {
            $html .= $tbl_newadm_dtl[$i]['FEES_DTL_AMOUNT'];

            $nwadm_fees += $tbl_newadm_dtl[$i]['FEES_DTL_AMOUNT'];   
        }
        $html .= <<<EOD
</td>
EOD;
//}
    $html .= <<<EOD
</tr>
EOD;
}

$totmthfees = number_format($mnt_fees * $tot_mon, 2);

$yer_fees = number_format($yer_fees, 2);

$mnt_fees = number_format($mnt_fees,2);

$nwadm_fees = number_format($nwadm_fees, 2);

$total_fs = number_format($total_fs, 2);

$total_late_fs = number_format($total_late_fs, 2);

    $html .= "
    <hr>
    
    </tbody>
    <tfoot>
        <tr>";

        //if(@count($tbl_monthly_dtl) > 0){
        $html.="<th>Total</th>
        <th align='left'><strong>$mnt_fees</strong></th>";
        //}

        //if (@count($tbl_yearly_dtl) > 0) {
         
        $html.="<th>Total Annual Fees</th>
        <th><strong>$yer_fees</strong></th>";
        //}

        //if (@count($tbl_newadm_dtl) > 0) {

        $html.="<th>Admission Fees</th>
        <th><strong>$nwadm_fees</strong></th>"; 
        //}



        $html .= <<<EDO
    </tr>   
    </tfoot>
    <br>
    <strong><hr></strong>
</table>



<span >
<h4><strong>Late Fees : &nbsp;&nbsp;&nbsp; $total_late_fs  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TOTAL FEES RECEIVED $total_fs</strong></h4>
<span>

<span> <h4><strong>$amount_in_word </strong></h4><span>

<div >
<span style="text-align: right"><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><span>
<br>
<span style="text-align: right">Collector's Signature<span>
</div>
<span style="text-align:center;"> <h4><strong>THIS IS COMPUTER GENERATED MONEY RECEIPT NO SIGNATURE REQUIRED</strong></h4><span>
EDO;


//echo $html; die();
/*<strong></strong>*/
        // Print text using writeHTMLCell()
        // $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
         $pdf->writeHTMLCell(0, 0, '', 30, $hhtml, 0, 1, 0, true, '', true);
         $pdf->writeHTMLCell(0, 0, '', 42, $html, 0, 1, 0, true, '', true);
        //$pdf->writeHTML($html, true, false, false, false, '');
        // Close and output PDF document
        $pdf->Output($doc_name.'.pdf', 'I');
    }
    public function print_monthly_fess($hdr_id) {  
        // echo $this->db->last_query(); die();
      
       //$this->db->query("SET sql_mode=''");
        $this->db->where('FM_HDR_SRLNO', $hdr_id);
        $hdr = $this->db->get('fees_monthly_hdr')->row();
        if(count((array)$hdr) == 0) { //if transaction does not exists
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Transaction not found.');
            return array('type'=>'redirect', 'page'=>'admin/monthly_fees_report');
        }

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_m = $this->db->get('fees_monthly_hdr')->result();

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_y = $this->db->get('fees_yearly_hdr')->result();

        $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $hdr_n = $this->db->get('fees_newadm_hdr')->result();

        $this->print_fees($hdr, 'month');
        if(@count($hdr_y) > 0 or @count($hdr_n) > 0){
            $this->print_fees($hdr, 'month');

        }
        else{
        $this->db->select('fees_monthly_dtl.*, acc_master.ACC_MASTER_NAME, COUNT(fees_monthly_dtl.FEES_DTL_ACC_SEQ) as total_rows');
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_monthly_dtl.FEES_DTL_ACC_SEQ', 'left');
        $this->db->where('fees_monthly_dtl.FEES_DTL_HDR_SRLNO', $hdr_id);
        $this->db->group_by('fees_monthly_dtl.FEES_DTL_ACC_SEQ');
        $dtl = $this->db->get('fees_monthly_dtl')->result_array();
                
        //return $this->db->last_query();

        $this->db->select('FEES_DTL_MONTH');
        $this->db->where('FEES_DTL_HDR_SRLNO', $hdr_id);
        $this->db->group_by('FEES_DTL_MONTH');
        $paid_months_id = $this->db->get('fees_monthly_dtl')->result_array();

        $this->db->where('STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $std = $this->db->get('student_details')->row(); //echo "<pre>"; print_r($std); die();

        $company = $this->company_name((array)$std->STD_CS_SEQ);

//         echo "<pre>"; print_r($company); die();

        $paid_months_id = array_unique(array_map(function($value){return $value['FEES_DTL_MONTH'];} , $paid_months_id));
        $months_arr = array("January"=>"1","February"=>"2","March"=>"3","April"=>"4","May"=>"5","June"=>"6","July"=>"7","August"=>"8","September"=>"9","October"=>"10","November"=>"11","December"=>"12");
        $paid_months = array_intersect($months_arr, $paid_months_id);
        $amount_in_word = ucwords($this->getIndianCurrency($hdr->FM_HDR_TOT_FEES));

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = $hdr->FM_HDR_RCPT_NO.' | '.$std->STD_FNAME.' '.$std->STD_MNAME.' '.$std->STD_LNAME.' (Reg. No: '.$std->STD_REGNO.') | '.$std->Class_Name.' - '.$std->Sec_Name;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Monthly Fees Receipt');
        $pdf->SetKeywords('monthly fees, smg, developed by: https://sketchmeglobal.com');

        // set default header data
        $col_dt = date('d-m-Y', strtotime($hdr->FM_HDR_COL_DATE));
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2
        <br>
        <strong>Money Receipt (Monthly): <span style="background-color: black;color: white;"> $hdr->FM_HDR_RCPT_NO </span></strong>
        <br>
        Name: <strong>$std->STD_FNAME $std->STD_MNAME $std->STD_LNAME</strong> • Class & Sec: <strong>$std->Class_Name - $std->Sec_Name</strong> • Roll: <strong>$std->STD_ROLLNO</strong> • Reg. No: <strong>$std->STD_REGNO-$std->STD_SRLNO</strong> • Date: <strong>$col_dt</strong>
        </div>
        <hr>
EOD;
        $pdf->setHtmlHeader($html_header, true);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 35, 10);
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
        $pdf->SetFont('times', '', 14, '', true);

        // Add a page
        $pdf->AddPage('P', 'A4');

        // Set some content to print
        $html = <<<EOD
<div>
Fees of Month: <strong>
EOD;
        foreach ($paid_months as $key => $m){
            $html .= $key.' ';
        }
        $html .= <<<EOD
</strong>
<br>
Session: <strong>$hdr->FM_HDR_FIN_YEAR</strong>
</div>
<br>
<table>
    <thead>
    <tr>
        <th><strong>Fees Name</strong></th>
        <th align="right"><strong>Amount</strong></th>
    </tr>
    </thead>
    <tbody>
EOD;
        foreach($dtl as $d) {
            $cal_amount = $d['FEES_DTL_AMOUNT']*$d['total_rows'];
            if($cal_amount == 0) { //excluding rows whose amount is zero
                continue;
            }
            $html .= '<tr>
                        <td>'.$d['ACC_MASTER_NAME'].'</td>
                        <td align="right">'.$d['FEES_DTL_AMOUNT'].' x '.$d['total_rows'].' = '.number_format($cal_amount,2).'</td>
                    </tr>';
        }

        $html .= <<<EOD
        <tr>
            <td>Late Fine</td>
            <td align="right">$hdr->FM_HDR_LATE_FEES</td>
        </tr>
        <tr>
            <td>Concession/Discount</td>
            <td align="right">$hdr->FM_HDR_CONC_FEES</td>
        </tr>
        <tr>
            <th><strong>Total Fees</strong></th>
            <th align="right"><strong style="background-color: black;color: white;"> $hdr->FM_HDR_TOT_FEES </strong></th>
        </tr>
        <tr>
            <th><strong>Total Fees in Words</strong></th>
            <th align="right"><strong>$amount_in_word</strong></th>
        </tr>
    </tbody>
</table>

<div>
<br>
<span style="text-align: right"><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><span>
<br>
<span style="text-align: right">Collector's Signature<span>
</div>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        // Close and output PDF document
        $pdf->Output($doc_name.'.pdf', 'I');
        }       
    }


    public function print_all_students_fee($hdr_id) {  

        $fee_category = $hdr_id; // not used so far
        $asr = $this->input->post('all_selected_rcpts');
        $asr_arr = (explode(",",$asr));

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        foreach($asr_arr as $aa){

            if($fee_category == 'monthly'){
                
                $hdr_id = $this->db->get_where('fees_monthly_hdr', array('FM_HDR_RCPT_NO' => $aa))->row()->FM_HDR_SRLNO;
                $this->db->where('FM_HDR_SRLNO', $hdr_id);
                $hdr = $this->db->get('fees_monthly_hdr')->row();

            }else if($fee_category == 'yearly'){

                $hdr_id = $this->db->get_where('fees_yearly_hdr', array('FM_HDR_RCPT_NO' => $aa))->row()->FM_HDR_SRLNO;
                $this->db->where('FM_HDR_SRLNO', $hdr_id);
                $hdr = $this->db->get('fees_yearly_hdr')->row();

            }else{

                $hdr_id = $this->db->get_where('fees_newadm_hdr', array('FM_HDR_RCPT_NO' => $aa))->row()->FM_HDR_SRLNO;
                $this->db->where('FM_HDR_SRLNO', $hdr_id);
                $hdr = $this->db->get('fees_newadm_hdr')->row();

            }    
            
                
        
            // $this->print_fees($hdr, 'month');

            $this->db->where('STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $std = $this->db->get('student_details')->row();        
            $company = $this->company_name((array)$std->STD_CS_SEQ);
            // echo "<pre>"; print_r($company); die();
            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_newadm_dtl.FEES_DTL_ACC_SEQ', 'left');
            $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            // $this->db->group_by('FEES_DTL_COL_DATE');
            $tbl_newadm_dtl = $this->db->get('fees_newadm_dtl')->result_array();


            // echo "<pre>"; print_r($tbl_newadm_dtl); die();
            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_yearly_dtl.FEES_DTL_ACC_SEQ', 'left');
            $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            // $this->db->group_by('FEES_DTL_COL_DATE');
            $tbl_yearly_dtl = $this->db->get('fees_yearly_dtl')->result_array();

            // echo "<pre>"; print_r($tbl_yearly_dtl); die();

            $this->db->select('acc_master.*, fees_monthly_dtl.*,  SUM(FEES_DTL_AMOUNT) as FEES_DTL_AMOUNT');
            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_monthly_dtl.FEES_DTL_ACC_SEQ', 'left');
            $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $this->db->group_by('FEES_DTL_ACC_SEQ');
            $tbl_monthly_dtl = $this->db->get('fees_monthly_dtl')->result_array();

            /*Total Fees And Late Fine*/
            $total_fs = 0.00;
            $total_late_fs = 0.00;

            $this->db->select('SUM(FM_HDR_TOT_FEES) as total_fees, SUM(FM_HDR_LATE_FEES) as total_late_fees, FM_HDR_RCPT_NO');
            $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $hdr_m = $this->db->get('fees_monthly_hdr')->result();
            if(@count($hdr_m) > 0){

                foreach ($hdr_m as $hdr_m_r) {
                    $total_fs += $hdr_m_r->total_fees;
                    $total_late_fs += $hdr_m_r->total_late_fees;
                }
            }
            $this->db->select('SUM(FM_HDR_TOT_FEES) as total_fees, SUM(FM_HDR_LATE_FEES) as total_late_fees, FM_HDR_RCPT_NO');
            $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $hdr_y = $this->db->get('fees_yearly_hdr')->result();

            if(@count($hdr_y) > 0){

                foreach ($hdr_y as $hdr_y_r) {
                    $total_fs += $hdr_y_r->total_fees;
                    $total_late_fs += $hdr_y_r->total_late_fees;
                }
            }
            $this->db->select('SUM(FEES_DTL_AMOUNT) as total_fees');
            $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $hdr_n = $this->db->get('fees_newadm_dtl')->result();

            if(@count($hdr_n) > 0){

                foreach ($hdr_n as $hdr_n_r) {
                    $total_fs += $hdr_n_r->total_fees;
                    //$total_late_fs += $hdr_n_r->total_late_fees;
                }

            }
        
            /*Receipt no*/
            $this->db->select('FM_HDR_RCPT_NO, FM_HDR_CARD_NO, encash_date');
            $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $hdr_m_recpt = $this->db->get('fees_monthly_hdr')->result();

            $this->db->select('FM_HDR_RCPT_NO, FM_HDR_CARD_NO, encash_date');
            $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $hdr_y_recpt = $this->db->get('fees_yearly_hdr')->result();

            $this->db->select('FM_HDR_RCPT_NO, FM_HDR_CARD_NO, encash_date');
            $this->db->where('FM_HDR_COL_DATE', $hdr->FM_HDR_COL_DATE);
            $this->db->where('FM_HDR_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
            $hdr_n_recpt = $this->db->get('fees_newadm_hdr')->result();

            /*----------*/
            if(count((array)$std) == 0) { //if transaction does not exists
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Oops!');
                $this->session->set_flashdata('msg', 'Transaction not found.');
                return array('type'=>'redirect', 'page'=>'admin/monthly_fees_report');
            }

            $tbl_row =  max( @count($tbl_newadm_dtl), @count($tbl_yearly_dtl), @count($tbl_monthly_dtl) );

            // set document information
            $doc_name = $std->ST_FULL_NAME.' (Reg. No: '.$std->STD_REGNO.') | '.$std->Class_Name.' - '.$std->Sec_Name;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('All Fees Receipt');
            $pdf->SetKeywords('all  fees, smg, developed by: sketchmeglobal.com');

            // set default header data
            $col_dt = date('d-m-Y', strtotime($hdr->FM_HDR_COL_DATE));
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 17px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white; font-size: 10px;"> Money Receipt </span></strong>
            <br>
            <span style="font-family:Times New Roman;">
            Name: <strong >$std->ST_FULL_NAME</strong> • Class & Sec: <strong>$std->Class_Name - $std->Sec_Name</strong> • Roll: <strong>$std->STD_ROLLNO</strong> • Reg. No: <strong>$std->STD_REGNO-$std->STD_SRLNO</strong> • Date: <strong>$col_dt</strong>
            </span>
            </div>
            <hr>
EOD;
        $pdf->SetPrintFooter(false);
        $pdf->SetAlpha(0);
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(8, 35, 5);
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
        $pdf->SetFont('helveticaB', '', 8, '', true);

        //$pdf->addTTFfont('/path-to-font/verdana.ttf', 'TrueTypeUnicode', '', 32);

        // Add a page
        $pdf->AddPage('P', 'A4');

        // Set some content to print

        // echo max( count($arr1), count($arr2), count($arr3) )
        $mnt_fees = 0.00;
        //$tot_mon = 1;

        $this->db->select('COUNT(FEES_DTL_ACC_SEQ) as total_rows');
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->group_by('FEES_DTL_ACC_SEQ');
        $total_rows = $this->db->get('fees_monthly_dtl')->row();

        $amount_in_word = 'RUPEES '.strtoupper($this->getIndianCurrency((int)$total_fs));

        //echo "<pre>"; print_r($total_rows->total_rows); die();

        if(empty($total_rows->total_rows)){
            $tot_mon = 0.00;
        }else{
            $tot_mon = $total_rows->total_rows;
        }

        

        $yer_fees = 0.00;

        // $tot_mon = 0.00;

        $nwadm_fees = 0.00;

        $this->db->select('FEES_DTL_MONTH');
//        $this->db->where('FEES_DTL_HDR_SRLNO', $hdr->FM_HDR_SRLNO);
        $this->db->where('FEES_DTL_STD_SEQ', $hdr->FM_HDR_STD_SEQ);
        $this->db->where('FEES_DTL_COL_DATE', $hdr->FM_HDR_COL_DATE);
        $this->db->group_by('FEES_DTL_MONTH');
        $paid_months_id = $this->db->get('fees_monthly_dtl')->result_array();
        $paid_months_id = array_unique(array_map(function($value){return $value['FEES_DTL_MONTH'];} , $paid_months_id));

        $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");

        $paid_months = array_intersect($months_arr, $paid_months_id);
//        echo "<pre>"; print_r($paid_months); die();

        $html = '';
        $hhtml = '';
        $hhtml .= <<<EOD
<table>
<tr>
<td width="100">
Fees of Month: 
</td>
<td width="45">
Session
</td>

<td width="1000">
Receipt No
</td>


</tr>
<tr>
<td width="100">
<strong>
EOD;
foreach ($paid_months as $key => $m){
            $hhtml .= $key.' ';
        }
$hhtml .= <<<EOD
</strong>
</td>
<td width="45">
<strong>$company->COM_FIN_YEAR</strong>
</td>

<td width="1000">

EOD;

if(@count($hdr_m_recpt) > 0){
    $hhtml .= <<<EOD
 Monthly 
EOD;
    foreach ($hdr_m_recpt as $value) {

$hhtml .= <<<EOD
  <strong><span style="background-color: black;color: white; font-size: 10px;"> $value->FM_HDR_RCPT_NO </span></strong> 
EOD;
}
}


if(@count($hdr_y_recpt) > 0){
    $hhtml .= <<<EOD
 Annual 
EOD;
    foreach ($hdr_y_recpt as $value) {
$hhtml .= <<<EOD
<strong><span style="background-color: black;color: white; font-size: 10px;"> $value->FM_HDR_RCPT_NO </span></strong>  
EOD;
}
}



if(@count($hdr_n_recpt) > 0){
    $hhtml .= <<<EOD
 Admission 
EOD;
    foreach ($hdr_n_recpt as $value) {
$hhtml .= <<<EOD
<strong><span style="background-color: black;color: white; font-size: 10px;"> $value->FM_HDR_RCPT_NO </span></strong>
EOD;
}
}


$hhtml .= <<<EOD
</td>
</tr>

<tr>
    <td width="210">
Cheque/Card Details (Encashment Date): 
    </td>
    <td width="935">
<strong>
EOD;
        if(@count($hdr_m_recpt) > 0){
            foreach ($hdr_m_recpt as $value) {
                if($value->FM_HDR_CARD_NO != ''){
                    $hhtml .= $value->FM_HDR_CARD_NO.' ';
                }
                if($value->encash_date != NULL && $value->encash_date != '0000-00-00'){
                    $hhtml .= '('.date('d-m-Y', strtotime($value->encash_date)).') ';
                }
            }
        }

        if(@count($hdr_y_recpt) > 0){
            foreach ($hdr_y_recpt as $value) {
                if($value->FM_HDR_CARD_NO != ''){
                    $hhtml .= $value->FM_HDR_CARD_NO.' ';
                }
                if($value->encash_date != NULL && $value->encash_date != '0000-00-00'){
                    $hhtml .= '('.date('d-m-Y', strtotime($value->encash_date)).') ';
                }
            }
        }

        if(@count($hdr_n_recpt) > 0){
            foreach ($hdr_n_recpt as $value) {
                if($value->FM_HDR_CARD_NO != ''){
                    $hhtml .= $value->FM_HDR_CARD_NO.' ';
                }
                if($value->encash_date != NULL && $value->encash_date != '0000-00-00'){
                    $hhtml .= '('.date('d-m-Y', strtotime($value->encash_date)).') ';
                }
            }
        }
        $hhtml .= <<<EOD
</strong>
    </td>
</tr>
</table>

EOD;

// $tbl_newadm_dtl
        $html .= "<hr /> <table border='0' cellpadding='6'>
    <thead style='border-bottom: 50px;'>
        <tr>";
        //if(@count($tbl_monthly_dtl) > 0){
        $html.="<th align='center' ><strong>Monthly Fees</strong></th>
        <th align='center'>Amount</th>";
        //}
        //if(@count($tbl_yearly_dtl) > 0){
        $html.="<th align='center'  ><strong>Annual Fees</strong></th>
        <th align='center'>Amount</th>";
        //}

        //if(@count($tbl_newadm_dtl) > 0){
        $html.="<th align='center' ><strong>Other Fees</strong></th>
        <th align='center'>Amount</th>";
       //}

    $html.="</tr>    
    </thead>

    <tbody>";

for ($i=0; $i < $tbl_row; $i++) {
    
   $html .= <<<EOD
<tr>
EOD;

        //if(@count($tbl_monthly_dtl) > 0){
        $html .= <<<EOD
<td >
EOD;
        if (array_key_exists($i,$tbl_monthly_dtl)) {
            $html .= $tbl_monthly_dtl[$i]['ACC_MASTER_NAME'];
        }
        $html .= <<<EOD
</td><td align='center' style='width:20px;'>
EOD;
        if (array_key_exists($i,$tbl_monthly_dtl)) {
            $html .= $tbl_monthly_dtl[$i]['FEES_DTL_AMOUNT'];
            $mnt_fees += $tbl_monthly_dtl[$i]['FEES_DTL_AMOUNT'];
        }
         $html .= <<<EOD
</td>
EOD;
        //}





        //if(@count($tbl_yearly_dtl) > 0){
        $html .= <<<EOD
<td style='width:200px;'>
EOD;
        if (array_key_exists($i,$tbl_yearly_dtl)) {
            $html .= $tbl_yearly_dtl[$i]['ACC_MASTER_NAME'];
        }
        $html .= <<<EOD
</td><td align='center' style='width:20px;'>
EOD;
        if (array_key_exists($i,$tbl_yearly_dtl)) {
            $html .= $tbl_yearly_dtl[$i]['FEES_DTL_AMOUNT'];
            $yer_fees += $tbl_yearly_dtl[$i]['FEES_DTL_AMOUNT'];
        }
         $html .= <<<EOD
</td>
EOD;
        //}
        //if(@count($tbl_newadm_dtl) > 0){
        $html .= <<<EOD
<td style='width:20px;'>
EOD;
        if (array_key_exists($i,$tbl_newadm_dtl)) {
            $html .= $tbl_newadm_dtl[$i]['ACC_MASTER_NAME'];
        }
        $html .= <<<EOD
</td><td>
EOD;
        if (array_key_exists($i,$tbl_newadm_dtl)) {
            $html .= $tbl_newadm_dtl[$i]['FEES_DTL_AMOUNT'];

            $nwadm_fees += $tbl_newadm_dtl[$i]['FEES_DTL_AMOUNT'];   
        }
        $html .= <<<EOD
</td>
EOD;
//}
    $html .= <<<EOD
</tr>
EOD;
}

$totmthfees = number_format($mnt_fees * $tot_mon, 2);

$yer_fees = number_format($yer_fees, 2);

$mnt_fees = number_format($mnt_fees,2);

$nwadm_fees = number_format($nwadm_fees, 2);

$total_fs = number_format($total_fs, 2);

$total_late_fs = number_format($total_late_fs, 2);

    $html .= "
    <hr>
    
    </tbody>
    <tfoot>
        <tr>";

        //if(@count($tbl_monthly_dtl) > 0){
        $html.="<th>Total</th>
        <th align='left'><strong>$mnt_fees</strong></th>";
        //}

        //if (@count($tbl_yearly_dtl) > 0) {
         
        $html.="<th>Total Annual Fees</th>
        <th><strong>$yer_fees</strong></th>";
        //}

        //if (@count($tbl_newadm_dtl) > 0) {

        $html.="<th>Admission Fees</th>
        <th><strong>$nwadm_fees</strong></th>"; 
        //}



        $html .= <<<EDO
    </tr>   
    </tfoot>
    <br>
    <strong><hr></strong>
</table>



<span >
<h4><strong>Late Fees : &nbsp;&nbsp;&nbsp; $total_late_fs  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TOTAL FEES RECEIVED $total_fs</strong></h4>
<span>

<span> <h4><strong>$amount_in_word </strong></h4><span>

<div >
<span style="text-align: right"><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><span>
<br>
<span style="text-align: right">Collector's Signature<span>
</div>
<span style="text-align:center;"> <h4><strong>THIS IS COMPUTER GENERATED MONEY RECEIPT NO SIGNATURE REQUIRED</strong></h4><span>
EDO;


        $pdf->writeHTMLCell(0, 0, '', 30, $hhtml, 0, 1, 0, true, '', true);
        $pdf->writeHTMLCell(0, 0, '', 42, $html, 0, 1, 0, true, '', true);

        } // loop ends
        
        $pdf->Output($doc_name.'.pdf', 'I');
    }
        
} // /.Transactions_m model
