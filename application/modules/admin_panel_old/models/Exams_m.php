<?php
/**
 * Coded by: Pran Krishna Das
 * Website: https://pran.dev
 * CI: 3.0.6
 * Date: 23-03-2021
 * Time: 12:25
 */

class Exams_m extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->db->query("SET SQL_MODE=''");
    }

    public function exam_time_setup() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Exams/exam_time_setup'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Exam Timing');
            $crud->order_by('class_id', 'ASC');
            $crud->set_table('exam_timings');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();

            $crud->columns('EXAM_SEQ','class_id','sub_id','full_marks');
            $crud->required_fields('EXAM_SEQ','class_id','sub_id','full_marks');

            $crud->display_as('EXAM_SEQ','Exam Name');
            $crud->display_as('class_id','Class & Section');
            $crud->display_as('sub_id','Subject Name');
            $crud->display_as('full_marks','Full Marks');
            

            $crud->set_relation('EXAM_SEQ', 'exam_test', '{Exam_Name} - {Exam_Year}');
            $crud->set_relation('class_id', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Exam Details';
            $output->section_heading = 'Exam Details <small>(Add / Edit / Delete)</small>';
            $output->menu_name = 'Exam Details';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function ques_setup() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Exams/ques_setup'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Question Set');
            $crud->order_by('class_id', 'ASC');
            $crud->set_table('question_sets');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_delete();
            $crud->unset_clone();

            $crud->columns('class_id','sub_id','title','questions');
            $crud->required_fields('class_id','sub_id','title','questions');

            $crud->display_as('class_id','Class & Section');
            $crud->display_as('sub_id','Subject Name');

            $crud->set_relation('class_id', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Question Set';
            $output->section_heading = 'Question Set <small>(Add / Edit)</small>';
            $output->menu_name = 'Question Set';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function exam_answers() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Exams/exam_answers'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Answers');
            $crud->set_table('exam_answers');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_delete();

            $crud->columns('EXAM_SEQ','qs_id','STD_SEQ','marks');
            $crud->required_fields('marks_details','marks');

            $crud->display_as('EXAM_SEQ','Exam Name');
            $crud->display_as('qs_id','Question Set Title');
            $crud->display_as('STD_SEQ','Student Name');

            $crud->set_relation('EXAM_SEQ', 'exam_test', '{Exam_Name} - {Exam_Year}');
            $crud->set_relation('qs_id', 'question_sets', 'title');
            $crud->set_relation('STD_SEQ', 'student_details', '{STD_FNAME} {STD_MNAME} {STD_LNAME}');

            $crud->field_type('EXAM_SEQ', 'readonly');
            $crud->field_type('qs_id', 'readonly');
            $crud->field_type('STD_SEQ', 'readonly');
            $crud->field_type('answers', 'readonly');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Answers';
            $output->section_heading = 'Answers <small></small>';
            $output->menu_name = 'Answers';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function mcq_ques_setup() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Exams/mcq_ques_setup'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('MCQ Question Set');
            $crud->order_by('class_id', 'ASC');
            $crud->set_table('mcq_ques_set');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_delete();
            $crud->unset_clone();

            $crud->columns('class_id','sub_id','title');
            $crud->required_fields('class_id','sub_id','title');

            $crud->display_as('class_id','Class & Section');
            $crud->display_as('sub_id','Subject Name');

            $crud->set_relation('class_id', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $crud->add_action('Questions', base_url().'assets/img/list.png', base_url().'admin/mcq_questions/');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'MCQ Question Set';
            $output->section_heading = 'MCQ Question Set <small>(Add / Edit)</small>';
            $output->menu_name = 'MCQ Question Set';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function mcq_questions($mcq_qs_id) {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Exams/mcq_questions/'.$mcq_qs_id));
            $crud->set_theme('flexigrid');
            $crud->set_subject('MCQ Question');
            $crud->where('mcq_qs_id', $mcq_qs_id);
            $crud->order_by('priority', 'ASC');
            $crud->set_table('mcq_questions');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_delete();
            $crud->unset_clone();

            $crud->columns('priority','question','marks');
            $crud->required_fields('priority','question','option1','option2','answer','marks');

            $crud->display_as('priority','Ques. Priority');
            $crud->display_as('answer','Correct Option No (eg: 2)');

            $crud->field_type('mcq_qs_id', 'hidden', $mcq_qs_id);

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'MCQ Question';
            $output->section_heading = 'MCQ Question <small>(Add / Edit)</small>';
            $output->menu_name = 'MCQ Question';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function mcq_exam_answers() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Exams/mcq_exam_answers'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Answers');
            $crud->set_model('grocery_crud_custom_models/Mcq_exam_answers');
            $crud->set_table('mcq_exam_answers');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('EXAM_SEQ','mcq_qs_id','STD_SEQ','total_marks');

            $crud->display_as('EXAM_SEQ','Exam Name');
            $crud->display_as('mcq_qs_id','Question Set Title');
            $crud->display_as('STD_SEQ','Student Name');
            $crud->display_as('total_marks','Total Marks');

            $crud->set_relation('EXAM_SEQ', 'exam_test', '{Exam_Name} - {Exam_Year}');
            $crud->set_relation('mcq_qs_id', 'mcq_ques_set', 'title');
            $crud->set_relation('STD_SEQ', 'student_details', '{STD_FNAME} {STD_MNAME} {STD_LNAME}');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Answers';
            $output->section_heading = 'Answers <small></small>';
            $output->menu_name = 'Answers';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }


}