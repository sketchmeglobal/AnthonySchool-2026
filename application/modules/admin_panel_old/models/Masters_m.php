<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-02-2019
 * Time: 16:54
 */

class Masters_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function database_backup() {
        //die('Hello');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->dbutil();
        date_default_timezone_set('Asia/Kolkata');
        $datetime = date('Y-m-d_h-i-A');

        $prefs = array('format' => 'zip', 'filename' => 'stantony_database_backup_' . $datetime);
        $backup = $this->dbutil->backup($prefs);

        if (!write_file('./assets/admin_panel/database_backup/stantony_database_backup_' . $datetime . '.zip', $backup)) {
            echo "Error while creating auto database backup!";
        }
        else {
            //file path
            $file = './assets/admin_panel/database_backup/stantony_database_backup_' . $datetime . '.zip';
            //download file from directory
            force_download($file, NULL);
        }
    }
    
    public function account_group() {
        try{
            $output = new \stdClass();
            $query = "SELECT * FROM acc_group";
            $output->all_account_groups = $this->db->query($query)->result();
            $output->menu_name = 'Account Group';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function account_group_edit() {
        try{
            $crud = new grocery_CRUD();
            // $crud->set_crud_url_path(base_url('admin_panel/Masters/account_group_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Account Group');
            $crud->set_table('acc_group');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();

            $crud->columns('ACC_GROUP_NAME', 'ACC_GROUP_PLBS', 'ACC_GROUP_PLBS_PART');
            $crud->required_fields('ACC_GROUP_NAME', 'ACC_GROUP_PLBS', 'ACC_GROUP_PLBS_PART');
            //$crud->unset_fields('ACC_GROUP_NAME');
            $crud->display_as('ACC_GROUP_NAME','Account Group Name');
            $crud->display_as('ACC_GROUP_PLBS','Account Group PLBS');
            $crud->display_as('ACC_GROUP_PLBS_PART','Account Group PLBS PART');

            $crud->field_type('ACC_GROUP_PLBS', 'dropdown', array('1'=>'Profit & Loss','2'=>'Balance Sheet'));

            $crud->field_type('ACC_GROUP_PLBS_PART', 'dropdown', array('1'=>'Income', '2'=>'Expenditure','3'=>'Assets','4'=>'Liabilities'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Account Group';
            $output->section_heading = 'Account Master <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Account Group';
            $output->add_button = '';
            
            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function account_master() {
        try{
            $output = new \stdClass();
            $query = "SELECT ACC_MASTER_CODE,acc_group.ACC_GROUP_NAME, ACC_MASTER_NAME,fees_type.name FROM acc_master LEFT JOIN acc_group ON acc_master.ACC_GROUP_CODE = acc_group.ACC_GROUP_CODE LEFT JOIN fees_type ON fees_type.ft_id = Fees_Type";
            $output->all_account_masters = $this->db->query($query)->result();

            // echo $this->db->last_query(); die();
            $output->menu_name = 'Account Master';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function account_master_edit() {
        try{
            $crud = new grocery_CRUD();
            // $crud->set_crud_url_path(base_url('admin_panel/Masters/account_master'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Account Master');
            $crud->set_table('acc_master');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();

            $crud->columns('ACC_GROUP_CODE', 'ACC_MASTER_NAME', 'Fees_Type');
            $crud->required_fields('ACC_GROUP_CODE', 'ACC_MASTER_NAME');
            $crud->unset_fields('COM_CODE', 'CREATED_DATE','MODIFIED_DATE','STATUS');
            $crud->display_as('ACC_GROUP_CODE','Account Group');
            $crud->display_as('ACC_MASTER_NAME','Account Name');

            $crud->unique_fields(array('ACC_MASTER_NAME'));

            // Debit / Credit

            $crud->field_type('ACC_CLOS_BAL_DRCR', 'dropdown', array('D'=>'Debit', 'C'=>'Credit'));


            $crud->field_type('ACC_OPN_BAL_DRCR', 'dropdown', array('D'=>'Debit', 'C'=>'Credit'));


            $crud->set_relation('ACC_GROUP_CODE', 'acc_group', 'ACC_GROUP_NAME');
            $crud->set_relation('Fees_Type', 'fees_type', 'name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Account Master';
            $output->section_heading = 'Account Master <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Account Master';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }


    public function class_section() {
        try{
            $output = new \stdClass();
            $query = "SELECT class_sec_hdr.CS_SEQ ,Class_Name, Sec_Name, class_sec, name FROM `class_sec_hdr` LEFT JOIN class_type ON class_type.ct_id = class_sec_hdr.Class_Type";
            $output->all_class_sections = $this->db->query($query)->result();
            $output->menu_name = 'Class Section';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function class_section_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/class_section_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Class & Section');
            $crud->order_by('CS_SEQ', 'ASC');
            $crud->set_table('class_sec_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            // $crud->unset_edit();

            $crud->unset_columns('Next_Class','class_sec');
            $crud->unset_fields('Next_Class','class_sec');
            $crud->required_fields('Class_Name', 'Class_Type');

            $crud->set_relation('Class_Type', 'class_type', 'name');
            $crud->set_relation('class_teacher', 'teacher', 'TCH_NAME');
            
            $crud->display_as('Class_Type', 'School');
            $crud->callback_after_insert(array($this, '_callback_class_sce_data'));
            $crud->callback_after_update(array($this, '_callback_class_sce_data'));

            $crud->add_action('Fees Structure',  base_url().'assets/grocery_crud/themes/flexigrid/css/images/money.png', 'admin/edit_class_fess');
            
            $output = $crud->render();
            //rending extra value to $output
            $output->output = str_replace('title="Fees Structure"', 'title="Fees Structure" target="_blank"', $output->output);
            $output->tab_title = 'Class & Section';
            $output->section_heading = 'Class & Section <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Class & Section';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }



    public function edit_class_fess($class_id) {
        try{

            $data = array();

            $this->db->select('acc_master.ACC_MASTER_NAME as Fees_name, class_sec_dtl.Fees as fees, class_sec_dtl.ACC_MASTER_CODE as fees_id');

            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE=class_sec_dtl.ACC_MASTER_CODE', 'left'); 

            $this->db->order_by('class_sec_dtl.ACC_MASTER_CODE', 'asc'); 

            $fees_data = $this->db->get_where('class_sec_dtl', array('class_sec_dtl.CS_SEQ'=>$class_id))->result();



            // echo "<pre>"; print_r($fees_data); die();

            //rending extra value to $output


            $data['form_type'] = "clas_sec_fees";

            $data['fees_data'] = $fees_data;

            $data['class_id'] = $class_id;

            $data['tab_title'] = 'Class Fees';
            $data['section_heading'] = 'Class Fees <small>(Add / Edit / Delete)</small>';
            $data['menu_name'] = 'Class Fees';
          
            return array('page'=>'fees_v', 'data'=>$data); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }


    public function class_sec_fees_edit() {
        try{

            // echo "<pre>"; print_r($this->input->post()); die();

            $data = array();

            $edit_fees_arr = $this->input->post('edit_fees');

            $class_id = $this->input->post('class_id');

            if ($this->input->post('submit') == 'clas_sec_fees_edit') {
                
                // echo "<pre>"; print_r($edit_fees_arr); die();
                $fees_data = array();
                foreach ($edit_fees_arr as $fees_id => $fees_value) {

                    $fees_data['Fees'] = $fees_value;

                    $this->db->update('class_sec_dtl', $fees_data, array('CS_SEQ' => $class_id, 'ACC_MASTER_CODE' => $fees_id));
                }
            }
            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Fees updated successfully.');
            return redirect(base_url('admin/class_section')); // redirect
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    
    public function subjects() {
        try{
            $output = new \stdClass();
            $query = "SELECT * FROM subject";
            $output->all_subjects = $this->db->query($query)->result();
            $output->menu_name = 'Subjects';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function subjects_edit() {
        try{
            $crud = new grocery_CRUD();
            // $crud->set_crud_url_path(base_url('admin_panel/Masters/subjects'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Subjects');
            $crud->set_table('subject');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();

            $crud->columns('sub_s_name','sub_name','comb');
            $crud->unset_fields('created_date','modified_date','status');
            $crud->required_fields('sub_s_name','sub_name','comb');

            $crud->display_as('sub_s_name', 'Subject Code');
            $crud->display_as('sub_name', 'Subject Name');
            $crud->display_as('comb', 'Combination Class?');

            $crud->field_type('comb', 'true_false', array("No","Yes"));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Subjects';
            $output->section_heading = 'Subjects <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Subjects';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function cls_sub_setup() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/cls_sub_setup'));
            $crud->set_theme('datatables');
            $crud->set_subject('Class Subjects');
            $crud->order_by('CS_SEQ,Sorting', 'ASC');
            $crud->set_table('class_sub_link');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();

            // $crud->edit_fields('Sorting');
            $crud->required_fields('CS_SEQ','CS_Sub_id','Sorting');

            $crud->display_as('CS_SEQ', 'Class & Sec');
            $crud->display_as('CS_Sub_id', 'Subject');

            $crud->set_relation('CS_SEQ', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('CS_Sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Class Subjects';
            $output->section_heading = 'Class Subjects <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Class Subjects';
            $output->add_button = '<a href="'.base_url('admin/copy_subjects').'" class="btn btn-success" role="button">Copy Subjects to Other Class</a>';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }


    // public function cls_sub_setup() {
    //     try{
    //         $output = new \stdClass();
    //         $query = "SELECT class_sub_link.CS_SUB_LINK_SEQ, sub_name, CONCAT(class_sec_hdr.Class_Name, '-', class_sec_hdr.Sec_Name) AS class_sec, Sorting FROM class_sub_link LEFT JOIN subject ON subject.sub_id = class_sub_link.CS_Sub_id LEFT JOIN class_sec_hdr ON class_sec_hdr.CS_SEQ = class_sub_link.CS_SEQ";
    //         $output->all_class_sections = $this->db->query($query)->result();
    //         $output->menu_name = 'Classes-Sections';
            
    //         return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
    //     } catch(Exception $e) {
    //         show_error($e->getMessage().' --- '.$e->getTraceAsString());
    //     }
    // }
    
    public function cls_sub_setup_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/cls_sub_setup_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Class Subjects');
            $crud->order_by('CS_SEQ,Sorting', 'ASC');
            $crud->set_table('class_sub_link');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();

            // $crud->edit_fields('Sorting');
            $crud->required_fields('CS_SEQ','CS_Sub_id','Sorting');

            $crud->display_as('CS_SEQ', 'Class & Sec');
            $crud->display_as('CS_Sub_id', 'Subject');

            $crud->set_relation('CS_SEQ', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('CS_Sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Class Subjects';
            $output->section_heading = 'Class Subjects <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Class Subjects';
            $output->add_button = '<a href="'.base_url('admin/copy_subjects').'" class="btn btn-success" role="button">Copy Subjects to Other Class</a>';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function copy_subjects() {
        $this->db->distinct();
        $this->db->select('class_sub_link.CS_SEQ, Class_Name, Sec_Name');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = class_sub_link.CS_SEQ', 'left');
        $from_cls = $this->db->get('class_sub_link')->result_array();

        $this->db->select('CS_SEQ, Class_Name, Sec_Name');
        $to_cls = $this->db->get('class_sec_hdr')->result_array();

        $data['from_cls'] = $from_cls;
        $data['to_cls'] = $to_cls;
        $data['section'] = 'copy_subjects';

        $data['tab_title'] = 'Copy Subjects';
        $data['menu_name'] = 'Copy Subjects';

        return array('type' => 'load_view', 'page' => 'Masters_v', 'data' => $data);
    }

    public function form_copy_subjects() {
        //if form submitted
        if($this->input->post('submit') != 'submit_copy_subjects_form') {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/cls_sub_setup');
        }

        $from_class = $this->input->post('from_class');
        $to_class = $this->input->post('to_class');

        $this->db->where('CS_SEQ', $from_class);
        $rs_from = $this->db->get('class_sub_link')->result_array();
        //if copy_from class does not exists
        if(count($rs_from) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'From class does not exists.');
            return array('page'=>'admin/cls_sub_setup');
        }

        foreach ($to_class as $t) {
            $this->db->where('CS_SEQ', $t);
            $rs_to = $this->db->get('class_sec_hdr')->result_array();
            //if copy_to class does not exists
            if(count($rs_to) == 0) {
                continue;
            }

            foreach ($rs_from as $f) {
                $this->db->where('CS_SEQ', $t);
                $this->db->where('CS_Sub_id', $f['CS_Sub_id']);
                $rs_check = $this->db->get('class_sub_link')->result_array();
                //if subject already exists in that class
                if(count($rs_check) > 0) {
                    continue;
                }

                $data_insert['CS_SEQ'] = $t;
                $data_insert['CS_Sub_id'] = $f['CS_Sub_id'];
                $data_insert['Sorting'] = $f['Sorting'];
                //inserting data
                $this->db->insert('class_sub_link', $data_insert);
            }
        }

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('msg', 'Subject copied successfully.');
        return array('page'=>'admin/cls_sub_setup');
    }

    public function books() {
        try{
            $output = new \stdClass();
            $query = "SELECT BOOK_SEQ,Accession_No,subject.sub_name, Book_Name,Author,Total_Copies FROM `book_master` LEFT JOIN subject ON subject.sub_id = book_master.sub_id";
            $output->all_books = $this->db->query($query)->result();
            $output->menu_name = 'Books';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function books_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/books_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Books');
            $crud->set_table('book_master');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('Accession_No','sub_id','Book_Name','Author','Total_Copies');
            $crud->required_fields('Accession_No','Call_No','sub_id','Book_Name','Author','Total_Copies');

            $crud->display_as('sub_id', 'Subject');

            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Books';
            $output->section_heading = 'Books <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Books';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function exam_master() {
        try{
            $output = new \stdClass();
            $query = "SELECT * FROM exam_test";
            $output->all_exams = $this->db->query($query)->result();
            $output->menu_name = 'Examinations';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function exam_master_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/exam_master_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Examination');
            $crud->set_table('exam_test');

            //$crud->unset_edit();
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_delete();
            
            $crud->required_fields('Exam_Name','Full_Marks','Exam_Year');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Examination';
            $output->section_heading = 'Examination <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Examination';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function exam_terms() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/exam_terms'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Examination');
            $crud->set_table('exam_terms');

            $crud->unset_add();
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_delete();
            
            $crud->columns('term_title','total_working_days');
            $crud->edit_fields('total_working_days');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Examination';
            $output->section_heading = 'Examination <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Examination';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function grade_setup() {
        try{
            $output = new \stdClass();
            $query = "SELECT * FROM grades";
            $output->all_grades = $this->db->query($query)->result();
            $output->menu_name = 'Grades';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function grade_setup_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/grade_setup_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Grade');
            $crud->set_table('grades');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();

            $crud->required_fields('marks_from','marks_to','grade');
            // $crud->display_as('grd_from','Higer value');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Grade Setup';
            $output->section_heading = 'Grade Setup <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Grade Setup';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function teachers() {
        try{
            $output = new \stdClass();
            $query = "SELECT TCH_SRLNO,TCH_PICTURE,TCH_SIGN, TCH_CODE,TCH_NAME,TCH_PHONE, CONCAT(Class_Name, '-', Sec_Name) AS cto FROM `teacher` LEFT JOIN class_sec_hdr on class_sec_hdr.CS_SEQ = teacher.TCH_CS_SEQ ";
            $output->all_teachers = $this->db->query($query)->result();
            $output->menu_name = 'Teachers & Staff';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function teachers_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/teachers_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Teachers & Staff');
            $crud->order_by('CS_SEQ', 'ASC');
            $crud->set_table('teacher');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('TCH_PICTURE','TCH_CODE','TCH_NAME','TCH_PHONE','TCH_CS_SEQ');
            $crud->required_fields('DEPT_CODE','TCH_NAME','TCH_CAT','TCH_CITY','TCH_PHONE','TCH_SEX','TCH_DOB','TCH_DOJ','TCH_MRTS','STD_CAT');
            $crud->unset_fields('LAST_MOD_DT','TCH_RTN_SRNO');
            $crud->unique_fields(array('TCH_EMAIL'));

            $crud->display_as('DEPT_CODE', 'Department');
            $crud->display_as('TCH_CS_SEQ', 'Class Teacher of');
            $crud->display_as('TCH_CODE', 'Teacher & Staff Code');
            $crud->display_as('TCH_SALUTATION', 'Salutation');
            $crud->display_as('TCH_NAME', 'Teacher & Staff Name');
            $crud->display_as('TCH_CLASSES', 'Classes');
            $crud->display_as('TCH_SUBJECTS', 'Subjects');
            $crud->display_as('TCH_CAT', 'Category');
            $crud->display_as('TCH_FTH', 'Father/Husband Name');
            $crud->display_as('TCH_ADDR', 'Full Address');
            $crud->display_as('TCH_CITY', 'City');
            $crud->display_as('TCH_EMAIL', 'Email Address');
            $crud->display_as('TCH_PHONE', 'Phone No');
            $crud->display_as('TCH_SEX', 'Gender');
            $crud->display_as('TCH_DOB', 'Date of Birth');
            $crud->display_as('TCH_DOJ', 'Date of Joining');
            $crud->display_as('TCH_DOR', 'Date of Retire');
            $crud->display_as('TCH_MRTS', 'Marital Status');
            $crud->display_as('TCH_MAX_CLS', 'Max Class for a Week');
            $crud->display_as('TCH_RTN_SRNO', 'Serial No for Routine');
            $crud->display_as('TCH_PF_ACCNO', 'P.F Account No');
            $crud->display_as('TCH_BANK_ACCNO', 'Bank Account No');
            $crud->display_as('TCH_PICTURE', 'Photograph');
            $crud->display_as('TCH_SIGN', 'Signature');
             $crud->display_as('TCH_PAN', 'Pancard No');
            $crud->display_as('TCH_AADHAR', 'Aadhar Card No');

            $this->db->select('CS_SEQ, Class_Name, Sec_Name');
            $rs_cls = $this->db->get('class_sec_hdr')->result();
            $class_multiselect = array();


            if (@count($rs_cls) > 0) {
                foreach ($rs_cls as $cls) {
                $class_multiselect[$cls->CS_SEQ] = $cls->Class_Name.' - '.$cls->Sec_Name;
                }
            }else{
                $class_multiselect[0] = "No Class Found";
            }

            
            $this->db->select('sub_id, sub_name');
            $rs_sub = $this->db->get('subject')->result();
            $sub_multiselect = array();

            if (@count($rs_sub) > 0) {
                foreach ($rs_sub as $sub) {
                $sub_multiselect[$sub->sub_id] = $sub->sub_name;
                }
            } else {
                $sub_multiselect[0] = "No Subject Found";
            }
            
            
            // $crud->set_relation('TCH_CLASSES', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->field_type('TCH_CLASSES', 'multiselect', $class_multiselect);
            $crud->field_type('TCH_SUBJECTS', 'multiselect', $sub_multiselect);
            
            $crud->field_type('TCH_SEX', 'true_false', array('1' => 'Male', '0' => 'Female'));
            $crud->field_type('TCH_MRTS', 'true_false', array('1' => 'Married', '0' => 'Unmarried'));
            $crud->field_type('TCH_CAT', 'dropdown', array('None'=>'None', 'Provision'=>'Provision', 'Contractual'=>'Contractual' , 'Permanent'=>'Permanent'));
            $crud->field_type('TCH_DOR', 'hidden');

            $crud->set_relation('DEPT_CODE', 'dept', 'DEPT_NAME');
            $crud->set_relation('TCH_CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_field_upload('TCH_PICTURE','assets/img/employees');
            $crud->set_field_upload('TCH_SIGN','assets/img/tch_sign');

            $crud->callback_before_insert(array($this,'_callback_BeforeInsert_teachers'));
            $crud->callback_before_update(array($this,'_callback_BeforeUpdate_teachers'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Teachers';
            $output->section_heading = 'Teachers & Staff <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Teachers & Staff';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function teachers_delete($id){
        $this->db->where('TCH_SRLNO',$id);
        $this->db->delete('teacher');
        
    }
    
    public function _callback_BeforeInsert_teachers($post_array){
        $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'teacher'")->row()->Auto_increment;
        $pass_encrypted = hash('sha256', $post_array['TCH_PHONE']); //encrypting password with sha256 encoding

        //creating details for login
        $data_insert['usertype'] = '6'; //login type operator
        $data_insert['tbl_id'] = $get_auto_index; //primary_id of teacher table
        $data_insert['email'] = $post_array['TCH_EMAIL'];
        $data_insert['pass'] = $pass_encrypted;
        $data_insert['verified'] = '1';
        $this->db->insert('users', $data_insert);

        $date = str_replace('/','-', $post_array['TCH_DOB']);
        $date = new DateTime($date);
        $date->add(new DateInterval('P58Y')); //+58 year
        $post_array['TCH_DOR'] = $date->format('Y-m-d');

        return $post_array;
    }
    public function _callback_BeforeUpdate_teachers($post_array){
        $date = str_replace('/','-', $post_array['TCH_DOB']);
        $date = new DateTime($date);
        $date->add(new DateInterval('P58Y')); //+58 year
        $post_array['TCH_DOR'] = $date->format('Y-m-d');

        return $post_array;
    }

    
    public function employees() {
        try{
            $output = new \stdClass();
            $query = "SELECT employee.*,dept.DEPT_NAME FROM employee LEFT JOIN dept ON dept.DEPT_CODE = employee.EMP_DEPT";
            $output->all_employees = $this->db->query($query)->result();
            $output->menu_name = 'Employees';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function employees_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/employees_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Employees');
            $crud->set_table('employee');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('EMP_PICTURE','EMP_CODE','EMP_DEPT','EMP_NAME','EMP_PHONE');
            $crud->required_fields('EMP_DEPT','EMP_CAT','EMP_CODE','EMP_NAME','EMP_PHONE','EMP_DOB','EMP_DOJ','EMP_DOR');
            $crud->unset_fields('LAST_MOD_DT');

            $crud->display_as('EMP_CODE', 'Employee Code');
            $crud->display_as('EMP_DEPT', 'Department');
            $crud->display_as('EMP_CAT', 'Category');
            $crud->display_as('EMP_NAME', 'Employee Name');
            $crud->display_as('EMP_FTH', 'Father/Husband Name');
            $crud->display_as('EMP_ADDR', 'Address');
            $crud->display_as('EMP_CITY', 'City');
            $crud->display_as('EMP_PHONE', 'Phone No');
            $crud->display_as('EMP_DOB', 'Date of Birth');
            $crud->display_as('EMP_DOJ', 'Date of Joining');
            $crud->display_as('EMP_DOR', 'Date of Retire');
            $crud->display_as('EMP_SEX', 'Gender');
            $crud->display_as('EMP_MRTS', 'Marital Status');
            $crud->display_as('EMP_PF_ACCNO', 'P.F Account No');
            $crud->display_as('EMP_BANK', 'Bank');
            $crud->display_as('EMP_BANK_ACCNO', 'Bank Account No');
            $crud->display_as('EMP_PAN', 'Pan Card No');
            $crud->display_as('EMP_PF', 'P.F %');
            $crud->display_as('EMP_EPF', 'E.P.F %');
            $crud->display_as('EMP_FPF', 'F.P.F %');
            $crud->display_as('EMP_BASIC', 'Basic Salary');
            $crud->display_as('EMP_DI_BASIC', 'DI. Basic');
            $crud->display_as('EMP_DA_PERCEN', 'D.A %');
            $crud->display_as('EMP_DA', 'D.A');
            $crud->display_as('EMP_HRA', 'H.R.A %');
            $crud->display_as('EMP_DA_CT', 'Cut Off D.A %');
            $crud->display_as('EMP_DOM', 'Domestic');
            $crud->display_as('EMP_GRD', 'Grade Pay');
            $crud->display_as('EMP_BND', 'Band Pay');
            $crud->display_as('EMP_RLF_PER', 'Interim Relief %');
            $crud->display_as('EMP_RLF', 'Interim Relief');
            $crud->display_as('EMP_MED', 'Medical Allowance');
            $crud->display_as('EMP_SPL_PER', 'Special Allowance %');
            $crud->display_as('EMP_SPL', 'Special Allowance');
            $crud->display_as('EMP_WF', 'Welfare Fund');
            $crud->display_as('EMP_CMP', 'Compensatory Allowance');
            $crud->display_as('EMP_NOMINEE', 'Nominee Name');
            $crud->display_as('EMP_EMAIL', 'Email ID');
            $crud->display_as('EMP_DSGN', 'Designation');

            $crud->set_relation('EMP_DEPT', 'dept', 'DEPT_NAME');
            $crud->set_relation('EMP_BANK', 'acc_master', 'ACC_MASTER_NAME', array('ACC_GROUP_CODE' => '2'));
            $crud->field_type('EMP_MRTS', 'true_false', array('1' => 'Married', '0' => 'Unmarried'));
            $crud->field_type('EMP_DOM', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('EMP_SEX', 'dropdown', array('Male' => 'Male', 'Female' => 'Female', 'Transgender' => 'Transgender'));
            $crud->field_type('EMP_CAT', 'dropdown', array('None'=>'None', 'Provision'=>'Provision', 'Contractual'=>'Contractual' , 'Permanent'=>'Permanent'));

            $crud->set_field_upload('EMP_PICTURE','assets/img/employees');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Employees';
            $output->section_heading = 'Employees <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Employees';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function signatures() {
        try{
            $output = new \stdClass();
            $query = "SELECT * from signatures";
            $output->all_signatures = $this->db->query($query)->result();
            $output->menu_name = 'Signatures';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function signatures_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/signatures_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Signatures');
            $crud->set_table('signatures');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('signature','effective_from');
            $crud->required_fields('signature','effective_from');
           

            $crud->display_as('signature', 'Signature');
            $crud->display_as('effective_from', 'Effective From');
           

           
           

            $crud->set_field_upload('signature','assets/img/');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Signatures';
            $output->section_heading = 'Signatures <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Signatures';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    /*transfer Accounts model to Master*/
    public function class_fees() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/class_fees'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Class Fees');
            $crud->order_by('CS_SEQ, ACC_MASTER_CODE', 'ASC');
            $crud->set_table('class_sec_dtl');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            // $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('CS_SEQ', 'ACC_MASTER_CODE', 'Fees', 'CS_FEES_TYPE');
            $crud->required_fields('CS_SEQ', 'ACC_MASTER_CODE','CS_FEES_TYPE', 'Fees');
            $crud->display_as('CS_SEQ', 'Class & Section Name');
            $crud->display_as('ACC_MASTER_CODE', 'Account Master Name');
            $crud->display_as('CS_FEES_TYPE', 'Fees Type');

            $crud->set_relation('CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('ACC_MASTER_CODE', 'acc_master', 'ACC_MASTER_NAME');
            $crud->set_relation('CS_FEES_TYPE', 'fees_type', 'name');

            // $crud->callback_column('CS_FEES_TYPE', array($this, '_callback_fees_type'));

            $crud->callback_after_insert(array($this, '_callback_AfterInsert_classfees'));
            $crud->callback_before_update(array($this, '_callback_BeforeUpdate_classfees'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Class Fees';
            $output->section_heading = 'Class Fees <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Class Fees';
            $output->add_button = '<a href="'.base_url('admin/copy_fees').'" class="btn btn-success" role="button">Copy Fees to Other Class</a>';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_AfterInsert_classfees($post_array, $primary_key){
        //fetch all concession students of this class
        $this->db->query("SET sql_mode=''");
        $this->db->where('class_id', $post_array['CS_SEQ']);
        $this->db->group_by('std_id');
        $std_rs = $this->db->get('fees_concession')->result_array();

        //add this fee in concession table
        foreach ($std_rs as $val) {
            unset($data_insert);
            $data_insert['std_id'] = $val['std_id'];
            $data_insert['class_id'] = $post_array['CS_SEQ'];
            $data_insert['ACC_MASTER_CODE'] = $post_array['ACC_MASTER_CODE'];;
            $data_insert['Fees'] = $post_array['Fees'];
            //inserting data
            $this->db->insert('fees_concession', $data_insert);
        }

        return true;
    }
    public function _callback_BeforeUpdate_classfees($post_array, $primary_key){
        //fetch old fee
        $this->db->where('CS_DTL_SRLNO', $primary_key);
        $old_fee = $this->db->get('class_sec_dtl')->row()->Fees;

        //update new fee in concession table
        unset($data_update);
        $data_update['Fees'] = $post_array['Fees'];

        $this->db->where('class_id', $post_array['CS_SEQ']);
        $this->db->where('ACC_MASTER_CODE', $post_array['ACC_MASTER_CODE']);
        $this->db->where('Fees', $old_fee);
        $this->db->update('fees_concession', $data_update);

        return $post_array;
    }

    public function copy_fees() {
        $this->db->distinct();
        $this->db->select('class_sec_dtl.CS_SEQ, Class_Name, Sec_Name');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
        $from_cls = $this->db->get('class_sec_dtl')->result_array();

        $this->db->select('CS_SEQ, Class_Name, Sec_Name');
        $to_cls = $this->db->get('class_sec_hdr')->result_array();

        $data['from_cls'] = $from_cls;
        $data['to_cls'] = $to_cls;
        $data['form_type'] = 'copy_fees';

        $data['tab_title'] = 'Copy Fees';
        $data['section_heading'] = 'Copy Fees';
        $data['menu_name'] = 'Copy Fees';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function form_copy_fees() {
        //if form submitted
        if($this->input->post('submit') != 'submit_copy_fees_form') {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/copy_fees');
        }

        $from_class = $this->input->post('from_class');
        $to_class = $this->input->post('to_class');

        $this->db->where('CS_SEQ', $from_class);
        $rs_from = $this->db->get('class_sec_dtl')->result_array();
        //if copy_from class does not exists
        if(count($rs_from) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'From class does not exists.');
            return array('page'=>'admin/copy_fees');
        }

        foreach ($to_class as $t) {
            $this->db->where('CS_SEQ', $t);
            $rs_to = $this->db->get('class_sec_hdr')->result_array();
            //if copy_to class does not exists
            if(count($rs_to) == 0) {
                continue;
            }

            foreach ($rs_from as $f) {
                $this->db->where('CS_SEQ', $t);
                $this->db->where('ACC_MASTER_CODE', $f['ACC_MASTER_CODE']);
                $rs_check = $this->db->get('class_sec_dtl')->result_array();
                //if subject already exists in that class
                if(count($rs_check) > 0) {
                    continue;
                }
                $data_insert['CS_SEQ'] = $t;
                $data_insert['ACC_MASTER_CODE'] = $f['ACC_MASTER_CODE'];
                $data_insert['Fees'] = $f['Fees'];
                $data_insert['CS_FEES_TYPE'] = $f['CS_FEES_TYPE'];
                //inserting data
                $this->db->insert('class_sec_dtl', $data_insert);
            }
        }

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('msg', 'Fees copied successfully.');
        return array('page'=>'admin/copy_fees');
    }

    public function concession_fees() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Masters/concession_fees'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Concession Fees');
            // $where = "where student_details.STD_CONSC=1";
            // $crud->where('student_details.STD_CONSC', 1); //showing only concession granted students
            $crud->order_by('STD_CS_SEQ', 'ASC');
            $crud->set_table('student_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('STD_CS_SEQ','STD_ROLLNO','STD_REGNO','STD_FNAME','STD_MNAME','STD_LNAME','Actions');
            $crud->display_as('STD_CS_SEQ', 'Class & Section Name');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_FNAME', 'First Name');
            $crud->display_as('STD_MNAME', 'Middle Name');
            $crud->display_as('STD_LNAME', 'Last Name');

            $crud->set_relation('STD_CS_SEQ', 'class_sec_hdr', '{class_sec}');

            $crud->callback_column('Actions', array($this, '_callback_action_buttons'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Concession Fees';
            $output->section_heading = 'Concession Fees <small>(Add / Edit)</small>';
            $output->menu_name = 'Concession Fees';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }


    public function _callback_action_buttons($value, $row) {
        $query = $this->db->query("SELECT `fc_id` FROM `fees_concession` WHERE std_id=$row->STD_SEQ AND `class_id`=$row->STD_CS_SEQ");
        if ($query->num_rows() == 0) { //if student not added to fee concession table yet
            $button = '<a href="'.base_url('admin/add_concession_fees/'.$row->STD_SEQ).'/common" style="text-decoration: none;" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
                        <span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
                        <span class="ui-button-text">&nbsp;Add</span>
                    </a>';
        } else {
            $button = '<a href="'.base_url('admin/edit_concession_fees/'.$row->STD_SEQ).'/common" style="text-decoration: none;" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
                        <span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
                        <span class="ui-button-text">&nbsp;Edit</span>
                    </a>
                    <a href="'.base_url('admin/delete_concession_fees/'.$row->STD_SEQ).'" style="text-decoration: none;"  class="confirm delete_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
                        <span class="ui-button-icon-danger ui-icon ui-icon-trash"></span>
                        <span class="ui-button-text">&nbsp;Delete</span>
                    </a>';
        }
        return $button;
    }

    public function add_concession_fees($std_id, $val) {
        $this->db->where('STD_SEQ', $std_id);
        $this->db->where('STD_CONSC', "1");
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        
        $data_update['STD_CONSC'] = 1;
        $this->db->where('STD_SEQ', $std_id);
        $this->db->update('student_details', $data_update);
        
        $this->db->select('std_id');
        $this->db->where('std_id', $std_id);
        $this->db->where('class_id', $row->STD_CS_SEQ);
        $result = $this->db->get('fees_concession')->result_array();
        
        $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
        $result_class_sec_dtl = $this->db->get('class_sec_dtl')->result_array();

        if(count($result_class_sec_dtl) == 0) { //if actual fees for that class not added yet
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Stop!');
            $this->session->set_flashdata('msg', 'Actual fees for that class, not added yet.');
            return array('type'=>'redirect', 'page'=>'admin/concession_fees');
        }

        $data['all_fees'] = $result_class_sec_dtl;
        $data['total_row'] = count($result_class_sec_dtl);;
        $data['std_id'] = $std_id;
        $data['success_val'] = $val;
        $data['class_id'] = $row->STD_CS_SEQ;
        $data['form_type'] = 'add';

        $data['tab_title'] = 'Add Concession Fees';
        $data['section_heading'] = '<h4>Student Name: <strong>'.$row->STD_FNAME.' '.$row->STD_MNAME.' '.$row->STD_LNAME.'</strong><br> Reg. No: <strong>'.$row->STD_REGNO.'</strong><br> Class & Sec: <strong>'.$row->Class_Name.' - '.$row->Sec_Name.'</strong><br> Roll No: <strong>'.$row->STD_ROLLNO.'</strong></h4>';
        $data['menu_name'] = 'Add Concession Fees';

        return array('type'=>'load_view', 'page'=>'concession_fees_v', 'data'=>$data);
        
    }

    public function form_add_concession_fees() {
        if($this->input->post('submit') == 'submit_concession_fees') { //if form submitted
            $std_id = $this->input->post('std_id');
            $class_id = $this->input->post('class_id');
            $total_row = $this->input->post('total_row');
            // echo "<pre>"; print_r($this->input->post()); die();
            
            $val = $this->input->post('success_val');
            

            for($i=1; $i<=1; $i++) {
                $data_insert['std_id'] = $std_id;
                $data_insert['class_id'] = $class_id;
                $data_insert['ACC_MASTER_CODE'] = $this->input->post('fees_id_'.$i);
                $data_insert['Fees'] = $this->input->post('fee_'.$i);

                //inserting data
                $this->db->insert('fees_concession', $data_insert);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Concession fees added.');
            
            
            if($val == 'monthly') {
            return array('type'=>'redirect', 'page'=>"admin/add_monthly_fees/$std_id");
            } else if($val == 'yearly') {
            return array('type'=>'redirect', 'page'=>"admin/add_yearly_fees/$std_id");    
            }else if($val == 'newadms') {
            return array('type'=>'redirect', 'page'=>"admin/add_new_admission_fees/$std_id");    
            } else {
            
            
            return array('page'=>'admin/concession_fees');
            
            
            }
            
            
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/concession_fees');
        }
    }


    public function edit_concession_fees($std_id, $val='') {
        $this->db->where('STD_SEQ', $std_id);
        $this->db->where('STD_CONSC', "1");
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        if(count((array)$row) == 0) { //if student not exists in student table
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'That student is not granted for concession.');
            return array('type'=>'redirect', 'page'=>'admin/concession_fees');
        } else {
            $this->db->select('std_id');
            $this->db->where('std_id', $std_id);
            $this->db->where('class_id', $row->STD_CS_SEQ);
            $result = $this->db->get('fees_concession')->result_array();
            if(count($result) == 0) { //if student concession fees not added yet
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'Concession fees for that student, not added yet.');
                return array('type'=>'redirect', 'page'=>'admin/concession_fees');
            } else { //updating concession fees
                $this->db->select('fc_id,ACC_MASTER_NAME,fees_concession.Fees,class_sec_dtl.Fees as actual_fees');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $row->STD_CS_SEQ);
                $this->db->join('acc_master', 'fees_concession.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $result_class_sec_dtl = $this->db->get('fees_concession')->result_array();

                $data['all_fees'] = $result_class_sec_dtl;
                $data['total_row'] = count($result_class_sec_dtl);
                
                
                $data['std_id'] = $std_id;
                $data['success_val'] = $val;
                
                
                $data['form_type'] = 'edit';

                $data['tab_title'] = 'Edit Concession Fees';
                $data['section_heading'] = '<h4>Student Name: <strong>'.$row->STD_FNAME.' '.$row->STD_MNAME.' '.$row->STD_LNAME.'</strong><br> Reg. No: <strong>'.$row->STD_REGNO.'</strong><br> Class & Sec: <strong>'.$row->Class_Name.' - '.$row->Sec_Name.'</strong><br> Roll No: <strong>'.$row->STD_ROLLNO.'</strong></h4>';
                $data['menu_name'] = 'Edit Concession Fees';

                return array('type'=>'load_view', 'page'=>'concession_fees_v', 'data'=>$data);
            }
        }
    }

    public function form_edit_concession_fees() {
        if($this->input->post('submit') == 'update_concession_fees') { //if form submitted
            $total_row = $this->input->post('total_row');
            $std_id = $this->input->post('std_id_edit');
            $val = $this->input->post('success_val_edit');

            for($i=1; $i<=$total_row; $i++) {
                $data_update['Fees'] = $this->input->post('fee_'.$i);

                //updating user details
                $this->db->where('fc_id', $this->input->post('fc_id_'.$i));
                $this->db->update('fees_concession', $data_update);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Concession fees updated.');
            if($val == 'monthly') {
            return array('type'=>'redirect', 'page'=>"admin/add_monthly_fees/$std_id");
            } else if($val == 'yearly') {
            return array('type'=>'redirect', 'page'=>"admin/add_yearly_fees/$std_id");    
            }else if($val == 'newadms') {
            return array('type'=>'redirect', 'page'=>"admin/add_new_admission_fees/$std_id");    
            } else {
            return array('page'=>'admin/concession_fees');
            }
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/concession_fees');
        }
    }

    public function delete_concession_fees($std_id) {
        $this->db->where('STD_SEQ', $std_id);
        $this->db->where('STD_CONSC', "1");
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        
        if(count((array)$row) > 0) { //if student not exists in student table
        $this->db->select('std_id');
        $this->db->where('std_id', $std_id);
        $this->db->where('class_id', $row->STD_CS_SEQ);
        $result = $this->db->get('fees_concession')->result_array();
        if(count($result) > 0) { //if student concession fees added
        $this->db->delete('fees_concession', array('std_id'=> $std_id, 'class_id'=> $row->STD_CS_SEQ));
        } else {
        $this->session->set_flashdata('title', 'Zzz!');
        $this->session->set_flashdata('msg', 'Concession fees for that student, not added yet.');
        return array('type'=>'redirect', 'page'=>'admin/concession_fees');    
        }
        
        $data_update['STD_CONSC'] = 0;
        
        //updating Student details
        $this->db->where('STD_SEQ', $std_id);
        $this->db->update('student_details', $data_update);
        $this->session->set_flashdata('type', 'error');
        $this->session->set_flashdata('title', 'Deleted!');
        $this->session->set_flashdata('msg', 'Concession details have been deleted successfully.');
        return array('type'=>'redirect', 'page'=>'admin/concession_fees');
        } else {
        $this->session->set_flashdata('type', 'error');
        $this->session->set_flashdata('title', 'Oops!');
        $this->session->set_flashdata('msg', 'That student is not granted for concession.');
        return array('type'=>'redirect', 'page'=>'admin/concession_fees');    
        }
        
        
        
    }
    
    /*---------------------------------*/

    /*Transfer Student Model to Master*/
    public function student_details() {
        try{

            
            $query = "SELECT STD_SEQ,STD_STATUS, class_sec_hdr.class_sec, STD_ROLLNO, STD_REGNO, STD_SRLNO, ST_FULL_NAME, STD_PH_NO, STD_IMAGE_PATH, STD_SEX FROM student_details LEFT JOIN class_sec_hdr ON student_details.STD_CS_SEQ = class_sec_hdr.CS_SEQ  WHERE 1=1 ORDER BY STD_CS_SEQ, STD_ROLLNO";

            $output = new \stdClass();

            $output->all_stdnt_details = $this->db->query($query)->result();

            // echo $this->db->last_query();

            // echo "<pre>"; print_r($output->all_stdnt_details); die();
            $output->menu_name = 'Student Details';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function student_details_edit() {
        try{
            $crud = new grocery_CRUD();
            // $crud->set_crud_url_path(base_url('admin_panel/Masters/student_details_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Student Details');
            $crud->order_by('STD_CS_SEQ,STD_ROLLNO', 'ASC');
            $crud->set_table('student_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();


            $crud->columns('STD_CS_SEQ','STD_ROLLNO','STD_REGNO','STD_SRLNO', 'ST_FULL_NAME',  'STD_PH_NO',  'STD_IMAGE_PATH');
            $crud->required_fields('STD_REGNO','STD_CS_SEQ','STD_ROLLNO','STD_FNAME','STD_LNAME','STD_DOA','STD_EMAIL','STD_PH_NO','STD_SEX','STD_DOB','STD_STATE','STD_CONSC','STD_BLDGRP','STD_RLGN','STD_CAT');
            $crud->unset_fields('space','ST_FULL_NAME','STD_LAST_SESSION', 'STD_LAST_CLASS','STD_LAST_CLASS_NEW','filter_scl');
            $crud->unique_fields(array('STD_EMAIL'));
            $crud->add_action('Print Certificate', base_url().'assets/grocery_crud/themes/flexigrid/css/images/print.png', base_url().'admin/print_certificate/');

            $crud->display_as('STD_CS_SEQ', 'Class & Sec');

            $crud->display_as('STD_CURRENT_SESSION', 'Admission Seession');

            
            $crud->display_as('STD_FNAME', 'First Name');
            $crud->display_as('STD_MNAME', 'Middle Name');
            $crud->display_as('STD_LNAME', 'Last Name');
            $crud->display_as('STD_REGNO', 'Registration No');
            $crud->display_as('STD_SRLNO', 'Adm. No');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('STD_DOA', 'Date of Admission');
            $crud->display_as('STD_EMAIL', 'Email Address');
            $crud->display_as('STD_PH_NO', 'Phone No');
            $crud->display_as('STD_SEX', 'Gender');
            $crud->display_as('STD_DOB', 'Date of Birth');
            $crud->display_as('STD_HOUSE', 'House');
            $crud->display_as('STD_STATE', 'State');
            $crud->display_as('STD_ADDR_0', 'Street No');
            $crud->display_as('STD_ADDR_1', 'Street Address');
            $crud->display_as('STD_ADDR_2', 'City / Village');
            $crud->display_as('STD_ADDR_3', 'District');
            $crud->display_as('STD_ADDR_4', 'Post Office');
            $crud->display_as('STD_ADDR_5', 'Pin Code');
            $crud->display_as('STD_MTNG', 'Mother Tongue');
            $crud->display_as('STD_2LANG', '2nd Language');
            $crud->display_as('STD_3LANG', '3rd Language');
            $crud->display_as('STD_CONSC', 'Allow Concession');
            $crud->display_as('STD_RLGN', 'Religion');
            $crud->display_as('STD_CAT', 'Caste');
            $crud->display_as('STD_RC', 'Roman Catholic?');
            $crud->display_as('STD_PHYDSBL', 'Physical Disability');
            $crud->display_as('STD_BLDGRP', 'Blood Group');
            $crud->display_as('STD_SUB67', '6th / 7th Subject');
            $crud->display_as('STD_LAST_SCHOOL', 'Previous School Name');
            $crud->display_as('STD_LAST_CLASS', 'Last Class Attended at Previous School');
            $crud->display_as('STD_OLD_TC_NO', 'Previous School TC No');
            $crud->display_as('STD_OLD_TC_DT', 'Previous School TC Date');
            $crud->display_as('STD_OLD_DT_LV', 'Previous School Leaving Date');
            $crud->display_as('STD_LAST_CLASS_NEW', 'Last Class Attended at Our School');
            $crud->display_as('STD_TC_NO', 'TC No');
            $crud->display_as('STD_TC_DT', 'TC Date');
            $crud->display_as('STD_DT_LV', 'Leaving Date');
            $crud->display_as('STD_RSN_LEAVE', 'Reason to Leave');
            $crud->display_as('STD_PRM', 'Promotion');
            $crud->display_as('STD_UID1', 'U-ID 1');
            $crud->display_as('STD_BY1', 'Board Year-1');
            $crud->display_as('STD_UID2', 'U-ID 2');
            $crud->display_as('STD_BY2', 'Board Year-2');
            $crud->display_as('STD_LEFT', 'Left from School?');
            $crud->display_as('STD_PROMOTED', 'Promoted?');
            $crud->display_as('STD_IMAGE_PATH', 'Student Photograph');

            $crud->set_relation('STD_CS_SEQ', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('STD_HOUSE', 'house', 'name');
            $crud->set_relation('STD_STATE', 'states', 'name');
            $crud->set_relation('STD_RLGN', 'religion', 'name');
            $crud->set_relation('STD_2LANG', 'subject', 'sub_name');
            $crud->set_relation('STD_3LANG', 'subject', 'sub_name');

            $crud->field_type('STD_SEX', 'true_false', array('1' => 'Male', '0' => 'Female'));
            $crud->field_type('STD_CONSC', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_RC', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_PHYDSBL', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_PRM', 'true_false', array('1' => 'Granted', '0' => 'Not Granted'));
            $crud->field_type('STD_LEFT', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_PROMOTED', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_CAT', 'dropdown', array('General'=>'General', 'SC'=>'SC', 'ST'=>'ST' , 'OBC'=>'OBC', 'Other'=>'Other'));
            $crud->field_type('STD_BLDGRP', 'dropdown', array('A+'=>'A+', 'A-'=>'A-', 'B+'=>'B+' , 'B-'=>'B-', 'AB+'=>'AB+', 'AB-'=>'AB-', 'O+'=>'O+', 'O-'=>'O-', 'Unknown'=>'Unknown'));
//            $crud->field_type('STD_SRLNO', 'invisible');
            $crud->field_type('STD_LAST_MOD_DT', 'hidden', date("Y-m-d H:i:s"));
            $crud->set_field_upload('STD_IMAGE_PATH','assets/img/students');

            date_default_timezone_set("Asia/Kolkata");
            $cur_session = array();
            $current_year = date("Y");
            $next_year = strtotime($current_year);
            $next_year = strtotime("+1 year", $next_year);
            $next_year = date("Y", $next_year);
            $next_year = $next_year;

            $cur_session[$next_year] = $next_year;

            $current_year = $current_year;

            $cur_session[$current_year] = $current_year;

            for ($i=1; $i <=2 ; $i++) {
            $last_year = strtotime(date("Y"));
            $last_year = strtotime('-'.$i.' year', $last_year);
            $last_year = date("Y", $last_year);
            $last_year = $last_year;
            $cur_session[$last_year] = $last_year;
            }

        // echo "<pre>"; print_r($cur_session); die();

        $crud->field_type('STD_CURRENT_SESSION', 'dropdown', $cur_session);

//            $crud->callback_before_insert(array($this,'_callback_BeforeInsert_StdDtls'));
            $crud->callback_after_insert(array($this, '_callback_AfterInsert_StdDtls'));
            $crud->callback_after_update(array($this, '_callback_AfterInsert_StdDtls'));



            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Student Details';
            $output->section_heading = 'Student Details <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Student Details';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function _callback_AfterInsert_StdDtls($post_array, $primary_key){


        $count = $this->db->get_where('users', array('tbl_id'=>$primary_key, 'usertype'=>4))->num_rows();

        //die();
        if($count == 0){
            $pass_encrypted = hash('sha256', $post_array['STD_PH_NO']); //encrypting password with sha256 encoding
            //creating details for login
            $data_insert['usertype'] = '4'; //login type student
            $data_insert['tbl_id'] = $primary_key; //primary_id of student_details table
            $data_insert['email'] = $post_array['STD_EMAIL'];
            $data_insert['pass'] = $pass_encrypted;
            $data_insert['verified'] = 1;
            $this->db->insert('users', $data_insert);
        }

        //creating parent details

        $count_p = $this->db->get_where('student_parent_details', array('STD_SEQ'=>$primary_key))->num_rows();

        if($count_p == 0){
        $data_insert2['STD_SEQ'] = $primary_key; //primary_id of student_details table
        $this->db->insert('student_parent_details', $data_insert2);

        }

        if (empty($post_array['STD_MNAME'])) {
            $st_full_name = $post_array['STD_FNAME'].' '.$post_array['STD_LNAME'];
        }else{
            $st_full_name = $post_array['STD_FNAME'].' '.$post_array['STD_MNAME'].' '.$post_array['STD_LNAME'];
        }

        $data = array(
            "ST_FULL_NAME" => $st_full_name,
        );

        if ($this->db->update('student_details',$data, array('STD_SEQ'=>$primary_key))) {
            return true;
        }else{
            die('Problem');
        }
    }
    
    public function _callback_class_sce_data($post_array, $primary_key){

        $cls_sec = $post_array['Class_Name'].'-'.$post_array['Sec_Name'];
        

        $data = array(
            "class_sec" => $cls_sec,
        );

        if ($this->db->update('class_sec_hdr',$data, array('CS_SEQ'=>$primary_key))) {
            return true;
        }else{
            die('Problem');
        }
    }
    /*-------------------------------*/
    public function ajax_potpow_students(){
        $from_class = $this->input->post('class_id');
        $this->db->select('*');
        $this->db->where('STD_CS_SEQ', $from_class);
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    public function update_student_status($student_id, $status)
    {
        $data = ['PoT_PoW' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
     public function ajax_second_language_students(){
        $from_class = $this->input->post('class_id');
        $this->db->select('*');
        $this->db->where('STD_CS_SEQ', $from_class);
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    public function update_student_second_langstatus($student_id, $status)
    {
        $data = ['STD_SECOND_LANG' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
    public function update_student_housestatus($student_id, $status)
    {
        $data = ['STD_HOUSE' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
    public function ajax_third_language_students(){
        $from_class = $this->input->post('class_id');
        $this->db->select('*');
        $this->db->where('STD_CS_SEQ', $from_class);
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    public function update_student_third_langstatus($student_id, $status)
    {
        $data = ['STD_THIRD_LANG' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
    public function ajax_concession_fees_students(){  
        
       $from_class = $this->input->post('class_id');
        $reg_no = $this->input->post('reg_no');
        
        $this->db->select('
            student_details.STD_SEQ,
            student_details.ST_FULL_NAME,
            student_details.STD_REGNO,
            student_details.STD_ROLLNO,
            (SELECT Fees FROM fees_concession 
             WHERE fees_concession.std_id = student_details.STD_SEQ 
             AND fees_concession.class_id = student_details.STD_CS_SEQ 
             AND fees_concession.ACC_MASTER_CODE = 4 
             LIMIT 1) as concession_fees,
            class_sec_dtl.Fees as actual_fees
        ');
        $this->db->from('student_details');
        $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = student_details.STD_CS_SEQ AND class_sec_dtl.ACC_MASTER_CODE =4');
        
        $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        
        if($from_class !== ""){
            $this->db->where('student_details.STD_CS_SEQ', $from_class);
        }
        if (!empty($reg_no)) {
            $this->db->where_in('student_details.STD_REGNO', $reg_no);
        }
        
        $this->db->order_by('student_details.STD_ROLLNO');
        
        $result = $this->db->get()->result_array();
        
        return $result;



    }
    public function getClassByRegistration($regno){
        $this->db->where('STD_REGNO',$regno);
        $query = $this->db->get('student_details');
        return $query->row()->STD_CS_SEQ;
    }
    public function update_student_concession_fees($student_id, $status, $class)
    {
        $data = ['STD_CONSC' => 1];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
        
        $this->db->select('std_id');
        $this->db->where('std_id', $student_id);
        $this->db->where('class_id', $class);
        $this->db->where('ACC_MASTER_CODE','4');
        $result = $this->db->get('fees_concession')->row();
        
        if($status > 0){
          if(count($result) == 0){ 
            $insdata = array(
                "std_id"=>$student_id,
                "class_id"=>$class,
                "ACC_MASTER_CODE"=>4,
                "Fees"=>$status
                );
                $this->db->insert('fees_concession',$insdata);
            }else{
               $updatedata = array(
                   "Fees"=>$status
                   ); 
                $this->db->where('std_id', $student_id);
                $this->db->where('class_id', $class);
                $this->db->where('ACC_MASTER_CODE','4');
                $this->db->update('fees_concession', $updatedata);
            }  
        }else{
             if(count($result) > 0){
                $this->db->where('std_id', $student_id);
                $this->db->where('class_id', $class);
                $this->db->where('ACC_MASTER_CODE','4');
                $this->db->delete('fees_concession');
             }
        }
        
    }
    public function ajax_mobileno_students(){
        $from_class = $this->input->post('class_id');
        $reg_no = $this->input->post('reg_no');
        $this->db->select('*');
        
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        if($from_class !== ""){
            $this->db->where('STD_CS_SEQ', $from_class);
        }
        if($reg_no !==''){
            //$reg_no_array = explode(',', $reg_no);
            $this->db->where_in('STD_REGNO', $reg_no);
        }
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    public function update_student_mobile_no($student_id, $status)
    {
        $data = ['STD_PH_NO' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
    
     public function ajax_aadharno_students(){
        $from_class = $this->input->post('class_id');
        $reg_no = $this->input->post('reg_no');
        $this->db->select('*');
       
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        if($from_class !== ""){
             $this->db->where('STD_CS_SEQ', $from_class);
        }
        if($reg_no !==''){
            //$reg_no_array = explode(',', $reg_no);
            $this->db->where_in('STD_REGNO', $reg_no);
        }
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    
    public function update_student_aadhar_no($student_id, $status)
    {
       
        $data = ['aadhaar_id' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
    
    public function ajax_shiksha_students(){
        $from_class = $this->input->post('class_id');  
        $reg_no = $this->input->post('reg_no');
        $this->db->select('*');
         
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        if($from_class !== ""){
             $this->db->where('STD_CS_SEQ', $from_class);
        }
        if($reg_no !==''){
            //$reg_no_array = explode(',', $reg_no);
            $this->db->where_in('STD_REGNO', $reg_no);
        }
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    
    public function update_shiksha_no($student_id, $status)
    {
       
        $data = ['banglar_shiksha_id' => $status];
        $this->db->where('STD_SEQ', $student_id);
        $this->db->update('student_details', $data);
    }
    
    public function ajax_get_cpysession_students(){
        $from_class = $this->input->post('class_id');
        $session = $this->input->post('session');
        $this->db->select('*');
        $this->db->where('STD_CS_SEQ', $from_class);
         $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        $this->db->order_by('STD_ROLLNO');
        return $std = $this->db->get('student_details')->result_array();
    }
    
    public function submit_staff_leave(){
        $insdata=array(
            "staff_id"=>$this->input->post('staff'),
            "leave_category"=>$this->input->post('leave_category'),
            "from_date"=>$this->input->post('from_date'),
            "to_date"=>$this->input->post('to_date'),
            "created_by"=>$this->session->user_id
            );
        $this->db->insert("staff_leave",$insdata);
    }
    public function ajax_search_leave_record(){
        $staff = $this->input->post('staff');
        $leave_category = $this->input->post('leave_category');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        $this->db->select('*'); 
        $this->db->from('staff_leave'); 
        $this->db->join('teacher', 'staff_leave.staff_id = teacher.tch_srlno', 'inner');   
        if($staff !== ''){
            $this->db->where("staff_leave.staff_id",$staff);
        }
        if($leave_category !== ''){
            $this->db->where("staff_leave.leave_category",$leave_category);
        }
        if ($from_date !== '') {
            $this->db->where("staff_leave.from_date >=", $from_date);
        }
        
        if ($to_date !== '') {
            $this->db->where("staff_leave.to_date <=", $to_date);
        }
        $query = $this->db->get(); 
        
 
        return $query->result();

        
    }
   

} // /.Masters_m model