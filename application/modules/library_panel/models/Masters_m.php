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

    public function books() {
        try{
            $output = new \stdClass();
            $query = "SELECT BOOK_SEQ,Accession_No,subject.sub_name, Book_Name,Author,Total_Copies,Available_Copies FROM `book_master` LEFT JOIN subject ON subject.sub_id = book_master.sub_id";
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
            $crud->set_crud_url_path(base_url('library_panel/Masters/books_edit'));
            $crud->set_theme('datatables');
            $crud->set_subject('Books');
            $crud->set_table('book_master');

            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            if($crud->getState() == 'add' or $crud->getState() == 'insert'){
                $crud->columns('Accession_No','CS_SEQ','sub_id','Book_Name','Author','Total_Copies','Available_Copies');
                $crud->fields('Accession_No','CS_SEQ','sub_id','Call_No','Book_Name','Author','Publisher','Source','Available_Copies','Total_Copies','Cost','img', 'Date_of_Purchase');
                $crud->required_fields('Book_Name','Author','Available_Copies','Total_Copies', 'Cost','Accession_No');
            }else{
                $crud->columns('Accession_No','CS_SEQ','sub_id','Book_Name','Author','Total_Copies','Available_Copies');
                $crud->fields('Accession_No','CS_SEQ','sub_id','Call_No','Book_Name','Author','Publisher','Source','Available_Copies','Total_Copies','Cost','img', 'Date_of_Purchase');
                $crud->required_fields('Book_Name','Author','Available_Copies','Total_Copies', 'Cost');
                $crud->field_type('Accession_No', 'readonly');                
            }
            
            $crud->display_as('CS_SEQ', 'class');
            $crud->display_as('sub_id', 'Subject');
            $crud->set_field_upload('img','assets/img/book_images');

            $crud->set_relation('CS_SEQ', 'class_sec_hdr', 'class_sec');
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

    public function fine() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('library_panel/Masters/fine'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Fine');
            $crud->set_table('library_fine_charges');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('fine_amount');
            $crud->fields('fine_amount');
            $crud->required_fields('fine_amount');

            $crud->display_as('fine_amount', 'Fine Charges On Daily Basis');
            $crud->unset_add();
            $crud->unset_delete();

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Fine Charges';
            $output->section_heading = 'Fine Charges <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Fine Charges';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function class_subject() {
        try{
            $output = new \stdClass();
            $query = "SELECT CS_SUB_LINK_SEQ,class_sec_hdr.Class_Name,class_sec_hdr.Sec_Name,subject.sub_name FROM `class_sub_link` LEFT JOIN subject ON subject.sub_id = class_sub_link.CS_Sub_id LEFT JOIN class_sec_hdr ON class_sec_hdr.CS_SEQ = class_sub_link.CS_SEQ";
            $output->all_subjects = $this->db->query($query)->result();
            $output->menu_name = 'class_subject';
            
            return array('page'=>'common_master', 'data'=>$output); //loading master common view page
            
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    
    public function class_subject_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('library_panel/Masters/class_subject_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('class-subject');
            $crud->set_table('class_sub_link');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('CS_SEQ','CS_Sub_id');
            $crud->required_fields('CS_SEQ','CS_Sub_id');

            $crud->display_as('CS_SEQ', 'class');
            $crud->display_as('CS_Sub_id', 'subject');

            $crud->set_relation('CS_SEQ', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('CS_Sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'class-section';
            $output->section_heading = 'class-section <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'class-section';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

} // /.Masters_m model
