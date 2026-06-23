<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

class Dashboard_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function dashboard() {
        //admin
        if($this->session->usertype == 1) {
            try{
                $crud = new grocery_CRUD();
                $crud->set_crud_url_path(base_url('library/Dashboard/dashboard'));
                $crud->set_theme('flexigrid');
                $crud->set_subject('School Name');
                $crud->set_table('company');
                $crud->unset_export();
                $crud->unset_print();
                $crud->unset_read();
                $crud->unset_delete();
                
                $crud->unset_add();
                $crud->unset_clone();
                $crud->unset_fields('COM_FIN_YEAR','SCHOOL_TYPE');

                $crud->columns('COM_NAME','SCHOOL_TYPE', 'COM_CITY', 'COM_PHONE','COM_FIN_YEAR','COM_FAX','COM_VATNO');
                
                // $crud->required_fields('COM_NAME', 'COM_ADD1', 'COM_CITY', 'COM_PIN', 'COM_PHONE');

                $crud->display_as('COM_NAME','School Name');
                $crud->display_as('COM_CITY','City');
                $crud->display_as('SCHOOL_TYPE','School');
                $crud->display_as('COM_PHONE','Phone');
                $crud->display_as('COM_ADD1','Address  1');
                $crud->display_as('COM_ADD2','Address 2');
                $crud->display_as('COM_PIN','Pincode');
                $crud->display_as('COM_FAX','FAX No.');
                $crud->display_as('COM_EMAIL','Email Address');
                $crud->display_as('IMAGE_PATH','School Picture');
                $crud->display_as('COM_VATNO','VAT No.');
                $crud->display_as('ESTD_CODE_NO','ESTD Code ');
                $crud->display_as('BANK_NAME','Bank Name');
                $crud->display_as('BRANCH_NAME','Branch Name');
                $crud->display_as('BANK_ADDR','Bank Address');
                $crud->display_as('COM_FIN_YEAR','Financial Year');

                $crud->set_relation('SCHOOL_TYPE', 'class_type', 'name');

                $output = $crud->render();
                //rending extra value to $output
                $output->tab_title = 'School Details';
                $output->section_heading = 'School Details <small>(Edit)</small>';
                $output->menu_name = 'Dashboard';
                $output->add_button = '';

                return array('page'=>'dashboard_v', 'data'=>$output); //loading dashboard view page
            } catch(Exception $e) {
                show_error($e->getMessage().' --- '.$e->getTraceAsString());
            }
        }
        //other
        else {
            $output['section_heading'] = '';
            $output['add_button'] = '';
            $output['output'] = '';
            return array('page'=>'dashboard_v', 'data'=>$output); //loading dashboard view page
        }

    }

} // /.Dashboard_m model