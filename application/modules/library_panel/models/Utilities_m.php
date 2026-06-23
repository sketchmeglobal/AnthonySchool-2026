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


    
    public function issue_books($hdr_id)
    {
        $data['book_master'] = $this->db->get_where('book_master')->result_array();
        $data['class_sec_hdr'] = $this->db->get_where('class_sec_hdr')->result_array();
        $data['student_details'] = $this->db->get_where('student_details')->result_array();
        $data['tab_title'] = 'Issued Book (Listings)';
        $data['section_heading'] = 'Issued Books (Listings) <small>(Add)</small>';
        $data['menu_name'] = 'Issued Books (Listings)';
        return array('page'=>'book_issue_page', 'data'=>$data);
    }


    public function return_books()
    {
        $data['book_master'] = $this->db->get_where('book_master')->result_array();
        $data['class_sec_hdr'] = $this->db->get_where('class_sec_hdr')->result_array();
        $data['student_details'] = $this->db->get_where('student_details')->result_array();
        $data['tab_title'] = 'Return Books (Listings)';
        $data['section_heading'] = 'Return Books (Listings) <small>(Add)</small>';
        $data['menu_name'] = 'Return Books (Listings)';
        return array('page'=>'book_return_page', 'data'=>$data);
    }


    public function ajax_book_issue_table_data() {
        //actual db table column names
        $column_orderable = array(
            0 => 'book_master.BOOK_SEQ',
            1 => 'student_details.STD_SEQ',
            2 => 'issue_date',
            3 => 'return_date',
            4 => 'returned_or_not',
            5 => 'fine_amount',
        );
        // Set searchable column fields
        $column_search = array('book_master.BOOK_SEQ','student_details.STD_SEQ','issue_date','return_date','fine_amount');
        // $column_search = array('co_no');

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        
        $order = $column_orderable[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
        $search = $this->input->post('search')['value'];

        $rs = $this->db->get('book_issued')->result();
        if($module_permission == 'show'){
                $rs = $this->db->get('book_issued')->result();
            } else {
                #module_permission contains the dept id now
                $rs = $this->db
                    ->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left')
                    ->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left')
                    ->get_where('book_issued', array('book_issued.status' => 1))->result();
            }
        $totalData = count($rs);
        $totalFiltered = $totalData;

        //if not searching for anything
        if(empty($search)) {
            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            
                $rs = $this->db->get_where('book_issued', array('book_issued.status => 1'))->result();
        }
        //if searching for something
        else {
            $this->db->start_cache();
            // loop searchable columns
            $i = 0;
            foreach($column_search as $item){
                // first loop
                if($i===0){
                    $this->db->group_start(); //open bracket
                    $this->db->like($item, $search);
                }else{
                    $this->db->or_like($item, $search);
                }
                // last loop
                if(count($column_search) - 1 == $i){
                    $this->db->group_end(); //close bracket
                }
                $i++;
            }
            $this->db->stop_cache();

            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            
                $rs = $this->db->get_where('book_issued', array('book_issued.status => 1'))->result();
            // echo $this->db->get_compiled_select('customer_order');
            // exit();
            $totalFiltered = count($rs);
            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');     
                $rs = $this->db->get_where('book_issued', array('book_issued.status => 1'))->result();
            $this->db->flush_cache();
        }
        $data = array();
        foreach ($rs as $val) {
            //Fine Calculation
            $actual_fine = 0;
            $return_date = strtotime($val->return_date);
            $current_date = strtotime(date("Y-m-d"));
            $diff = $current_date - $return_date;
            if($diff >= 0) {
                $date_values = floor($diff/(60*60*24));
            } else {
            $date_values = 0;   
            }
            $fine_amount = $this->db->get_where('library_fine_charges', array('status' => 1))->row()->fine_amount;
            $book_price = $this->db->get_where('book_master', array('BOOK_SEQ' => $val->BOOK_SEQ))->row()->Cost;
            //If fine amount exceed actual book cost then book cost will be actual fine amount
            if($book_price < $fine_amount) {
                $actual_fine = $book_price;
            } else {
                $actual_fine = $fine_amount * $date_values;
            }
            if($val->returned_or_not == 0) {
                $return_values = 'No';
            } else {
                $return_values = 'Yes';
            }
            $nestedData['Book_Name'] = $val->Book_Name;
            $nestedData['ST_FULL_NAME'] = $val->ST_FULL_NAME;
            $nestedData['issue_date'] = $val->issue_date;
            $nestedData['return_date'] = $val->return_date;
            $nestedData['returned_or_not'] = $return_values;
            $nestedData['fine_amount'] = $actual_fine;
            $nestedData['actions'] = "<button class='delete_issued_book btn btn-danger' id='$val->BOOK_ISSUED_ID'><i class='fa fa-trash-o'></i> Delete</button>";
            $data[] = $nestedData;

            // echo '<pre>', print_r($rs), '</pre>'; 
        }
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return $json_data;
    }


    public function ajax_book_return_table_data() {
        //actual db table column names
        $column_orderable = array(
            0 => 'book_master.BOOK_SEQ',
            1 => 'student_details.STD_SEQ',
            2 => 'issue_date',
            7 => 'return_date',
        );
        // Set searchable column fields
        $column_search = array('book_master.BOOK_SEQ','student_details.STD_SEQ','issue_date');
        // $column_search = array('co_no');

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        
        $order = $column_orderable[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
        $search = $this->input->post('search')['value'];


                $rs = $this->db
                    ->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left')
                    ->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left')
                    ->get_where('book_issued', array('book_issued.status' => 1, 'returned_or_not' => 0))->result();


        $totalData = count($rs);
        $totalFiltered = $totalData;

        //if not searching for anything
        if(empty($search)) {
            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            
                $rs = $this->db->get_where('book_issued', array('book_issued.status => 1', 'returned_or_not' => 0))->result();
        }
        //if searching for something
        else {
            $this->db->start_cache();
            // loop searchable columns
            $i = 0;
            foreach($column_search as $item){
                // first loop
                if($i===0){
                    $this->db->group_start(); //open bracket
                    $this->db->like($item, $search);
                }else{
                    $this->db->or_like($item, $search);
                }
                // last loop
                if(count($column_search) - 1 == $i){
                    $this->db->group_end(); //close bracket
                }
                $i++;
            }
            $this->db->stop_cache();

            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            
                $rs = $this->db->get_where('book_issued', array('book_issued.status => 1', 'returned_or_not' => 0))->result();
            // echo $this->db->get_compiled_select('customer_order');
            // exit();
        

            $totalFiltered = count($rs);

            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            
                $rs = $this->db->get_where('book_issued', array('book_issued.status => 1', 'returned_or_not' => 0))->result();

            $this->db->flush_cache();
        }

        $data = array();

        foreach ($rs as $val) {


            //Fine Calculation
            $actual_fine = 0;
            $return_date = strtotime($val->return_date);
            $current_date = strtotime(date("Y-m-d"));
            $diff = $current_date - $return_date;
            if($diff >= 0) {
                $date_values = floor($diff/(60*60*24));
            } else {
            $date_values = 0;   
            }
            $fine_amount = $this->db->get_where('library_fine_charges', array('status' => 1))->row()->fine_amount;
            $book_price = $this->db->get_where('book_master', array('BOOK_SEQ' => $val->BOOK_SEQ))->row()->Cost;
            //If fine amount exceed actual book cost then book cost will be actual fine amount
            if($book_price < $fine_amount) {
                $actual_fine = $book_price;
            } else {
                $actual_fine = $fine_amount * $date_values;
            }
            if($val->returned_or_not == 0) {
                $return_values = 'No';
            } else {
                $return_values = 'Yes';
            }


            $nestedData['Book_Name'] = $val->Book_Name;
            $nestedData['ST_FULL_NAME'] = $val->ST_FULL_NAME;
            $nestedData['issue_date'] = $val->issue_date;
            $nestedData['return_date'] = $val->return_date;
            $nestedData['returned_or_not'] = $return_values;
            $nestedData['fine_amount'] = $actual_fine;
            $nestedData['action'] = '<a del-id="'.$val->BOOK_ISSUED_ID.'" class="btn btn-success confirm"><i class="fa fa-check"></i> Confirm Return</a>';
            $data[] = $nestedData;

            // echo '<pre>', print_r($rs), '</pre>'; 
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return $json_data;
    }


    public function form_return_book_filter() {
                $filter_book_id = $this->input->post('filter_book_id');
                $filter_class_id = $this->input->post('filter_class_id');
                $filter_student_id = $this->input->post('filter_student_id');
        //actual db table column names
        $column_orderable = array(
            0 => 'book_master.BOOK_SEQ',
            1 => 'student_details.STD_SEQ',
            2 => 'issue_date',
            7 => 'return_date',
        );
        // Set searchable column fields
        $column_search = array('book_master.BOOK_SEQ','student_details.STD_SEQ','issue_date');
        // $column_search = array('co_no');

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        
        $order = $column_orderable[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
        $search = $this->input->post('search')['value'];


                $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
                $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
                if($filter_book_id != '') {
                $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
                }
                if($filter_class_id != '') {
                $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
                }
                if($filter_student_id != '') {
                $this->db->where('student_details.STD_SEQ', $filter_student_id);
                }
                $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1, 'returned_or_not' => 0))->result();


        $totalData = count($rs);
        $totalFiltered = $totalData;

        //if not searching for anything
        if(empty($search)) {
                    $this->db->limit($limit, $start);
                    $this->db->order_by($order, $dir);
                    $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
                    $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
                    if($filter_book_id != '') {
                    $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
                    }
                    if($filter_class_id != '') {
                    $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
                    }
                    if($filter_student_id != '') {
                    $this->db->where('student_details.STD_SEQ', $filter_student_id);
                    }
                    $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1, 'returned_or_not' => 0))->result();
        }
        //if searching for something
        else {
            $this->db->start_cache();
            // loop searchable columns
            $i = 0;
            foreach($column_search as $item){
                            // first loop
                            if($i===0){
                            $this->db->group_start(); //open bracket
                            $this->db->like($item, $search);
                            }else{
                            $this->db->or_like($item, $search);
                            }
                            // last loop
                            if(count($column_search) - 1 == $i){
                            $this->db->group_end(); //close bracket
                            }
                            $i++;
                            }
                            $this->db->stop_cache();

                            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
                            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
                            if($filter_book_id != '') {
                            $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
                            }
                            if($filter_class_id != '') {
                            $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
                            }
                            if($filter_student_id != '') {
                            $this->db->where('student_details.STD_SEQ', $filter_student_id);
                            }
                            $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1, 'returned_or_not' => 0))->result();
                    

            $totalFiltered = count($rs);

                    $this->db->limit($limit, $start);
                    $this->db->order_by($order, $dir);
                    $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
                    $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
                    if($filter_book_id != '') {
                    $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
                    }
                    if($filter_class_id != '') {
                    $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
                    }
                    if($filter_student_id != '') {
                    $this->db->where('student_details.STD_SEQ', $filter_student_id);
                    }
                    $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1, 'returned_or_not' => 0))->result();

            $this->db->flush_cache();
        }

        $data = array();

        foreach ($rs as $val) {


            //Fine Calculation
            $actual_fine = 0;
            $return_date = strtotime($val->return_date);
            $current_date = strtotime(date("Y-m-d"));
            $diff = $current_date - $return_date;
            if($diff >= 0) {
                $date_values = floor($diff/(60*60*24));
            } else {
            $date_values = 0;   
            }
            $fine_amount = $this->db->get_where('library_fine_charges', array('status' => 1))->row()->fine_amount;
            $book_price = $this->db->get_where('book_master', array('BOOK_SEQ' => $val->BOOK_SEQ))->row()->Cost;
            //If fine amount exceed actual book cost then book cost will be actual fine amount
            if($book_price < $fine_amount) {
                $actual_fine = $book_price;
            } else {
                $actual_fine = $fine_amount * $date_values;
            }
            if($val->returned_or_not == 0) {
                $return_values = 'No';
            } else {
                $return_values = 'Yes';
            }


            $nestedData['Book_Name'] = $val->Book_Name;
            $nestedData['ST_FULL_NAME'] = $val->ST_FULL_NAME;
            $nestedData['issue_date'] = $val->issue_date;
            $nestedData['return_date'] = $val->return_date;
            $nestedData['returned_or_not'] = $return_values;
            $nestedData['fine_amount'] = $actual_fine;
            $nestedData['action'] = '<a del-id="'.$val->BOOK_ISSUED_ID.'" class="btn btn-success confirm"><i class="fa fa-check"></i> Confirm Return</a>';
            $data[] = $nestedData;

            // echo '<pre>', print_r($rs), '</pre>'; 
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return $json_data;
    }


    public function form_book_return() {
        $book_issue_id = $this->input->post('id');


            $get_actual_book_id = $this->db->get_where('book_issued', array('BOOK_ISSUED_ID' => $book_issue_id))->row();
            $get_available_book_copies = $this->db->get_where('book_master', array('BOOK_SEQ' => $get_actual_book_id->BOOK_SEQ))->row()->Available_Copies;
            //Fine Calculation
            $actual_fine = 0;
            $return_date = strtotime($get_actual_book_id->return_date);
            $current_date = strtotime(date("Y-m-d"));
            $diff = $current_date - $return_date;
            if($diff >= 0) {
                $date_values = floor($diff/(60*60*24));
            } else {
            $date_values = 0;   
            }
            $fine_amount = $this->db->get_where('library_fine_charges', array('status' => 1))->row()->fine_amount;
            $book_price = $this->db->get_where('book_master', array('BOOK_SEQ' => $get_actual_book_id->BOOK_SEQ))->row()->Cost;
            //If fine amount exceed actual book cost then book cost will be actual fine amount
            if($book_price < $fine_amount) {
                $actual_fine = $book_price;
            } else {
                $actual_fine = $fine_amount * $date_values;
            }
            $data_update['returned_or_not'] = 1;
            $data_update['fine_amount'] = $actual_fine;
            $data_update['update_by'] = $this->session->user_id;
            $this->db->update('book_issued', $data_update, array('BOOK_ISSUED_ID' => $book_issue_id));
            unset($data_update);
            $data_update['Available_Copies'] = ($get_available_book_copies + 1);
        $this->db->update('book_master', $data_update, array('BOOK_SEQ' => $get_actual_book_id->BOOK_SEQ));
        $data['type'] = 'success';
        $data['msg'] = 'Details Updated Successfully.';
        return $data;
    }


    public function form_issue_book_filter() {
                $filter_book_id = $this->input->post('filter_book_id');
                $filter_class_id = $this->input->post('filter_class_id');
                $filter_student_id = $this->input->post('filter_student_id');
        //actual db table column names
        $column_orderable = array(
            0 => 'book_master.BOOK_SEQ',
            1 => 'student_details.STD_SEQ',
            2 => 'issue_date',
            7 => 'return_date',
        );
        // Set searchable column fields
        $column_search = array('book_master.BOOK_SEQ','student_details.STD_SEQ','issue_date');
        // $column_search = array('co_no');

        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        
        $order = $column_orderable[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
        $search = $this->input->post('search')['value'];


            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            if($filter_book_id != '') {
            $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
            }
            if($filter_class_id != '') {
            $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
            }
            if($filter_student_id != '') {
            $this->db->where('student_details.STD_SEQ', $filter_student_id);
            }
            $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1))->result();


        $totalData = count($rs);
        $totalFiltered = $totalData;

        //if not searching for anything
        if(empty($search)) {
            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            if($filter_book_id != '') {
            $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
            }
            if($filter_class_id != '') {
            $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
            }
            if($filter_student_id != '') {
            $this->db->where('student_details.STD_SEQ', $filter_student_id);
            }
            $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1))->result();
        }
        //if searching for something
        else {
            $this->db->start_cache();
            // loop searchable columns
            $i = 0;
            foreach($column_search as $item){
                // first loop
                if($i===0){
                    $this->db->group_start(); //open bracket
                    $this->db->like($item, $search);
                }else{
                    $this->db->or_like($item, $search);
                }
                // last loop
                if(count($column_search) - 1 == $i){
                    $this->db->group_end(); //close bracket
                }
                $i++;
            }
            $this->db->stop_cache();

            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            if($filter_book_id != '') {
            $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
            }
            if($filter_class_id != '') {
            $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
            }
            if($filter_student_id != '') {
            $this->db->where('student_details.STD_SEQ', $filter_student_id);
            }
            $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1))->result();
            // echo $this->db->get_compiled_select('customer_order');
            // exit();
        

            $totalFiltered = count($rs);

            $this->db->limit($limit, $start);
            $this->db->order_by($order, $dir);
            $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
            $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
            if($filter_book_id != '') {
            $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
            }
            if($filter_class_id != '') {
            $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
            }
            if($filter_student_id != '') {
            $this->db->where('student_details.STD_SEQ', $filter_student_id);
            }
            $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1))->result();

            $this->db->flush_cache();
        }

        $data = array();

        foreach ($rs as $val) {


            //Fine Calculation
            $actual_fine = 0;
            $return_date = strtotime($val->return_date);
            $current_date = strtotime(date("Y-m-d"));
            $diff = $current_date - $return_date;
            if($diff >= 0) {
                $date_values = floor($diff/(60*60*24));
            } else {
            $date_values = 0;   
            }
            $fine_amount = $this->db->get_where('library_fine_charges', array('status' => 1))->row()->fine_amount;
            $book_price = $this->db->get_where('book_master', array('BOOK_SEQ' => $val->BOOK_SEQ))->row()->Cost;
            //If fine amount exceed actual book cost then book cost will be actual fine amount
            if($book_price < $fine_amount) {
                $actual_fine = $book_price;
            } else {
                $actual_fine = $fine_amount * $date_values;
            }
            if($val->returned_or_not == 0) {
                $return_values = 'No';
            } else {
                $return_values = 'Yes';
            }


            $nestedData['Book_Name'] = $val->Book_Name;
            $nestedData['ST_FULL_NAME'] = $val->ST_FULL_NAME;
            $nestedData['issue_date'] = $val->issue_date;
            $nestedData['return_date'] = $val->return_date;
            $nestedData['returned_or_not'] = $return_values;
            $nestedData['fine_amount'] = $actual_fine;
            $nestedData['action'] = '<a del-id="'.$val->BOOK_ISSUED_ID.'" class="btn btn-success confirm"><i class="fa fa-check"></i> Confirm Return</a>';
            $data[] = $nestedData;

            // echo '<pre>', print_r($rs), '</pre>'; 
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return $json_data;
    }


    public function form_book_add_filter() {
            $filter_book_id = $this->input->post('book_select');
            $rs = $this->db->get_where('book_master', array('BOOK_SEQ' => $filter_book_id))->result();

        //if students exists
        $html = '';
        if(count($rs) > 0) {
            $i = 1;
            foreach ($rs as $val) {
                $issue_date = date('Y-m-d');
                $html .= '
                <tr class="'.$val->BOOK_SEQ.'">
                <td>' . $val->Book_Name . '</td>
                <td class="text-center">
                    <input name="book_issue_date[' . $val->BOOK_SEQ . ']" type="date" value="'.$issue_date.'" required class="form-control round-input" >
                </td>
                <td class="text-center">
                    <input name="book_return_date[' . $val->BOOK_SEQ . ']" type="date" value="" required class="form-control round-input" >
                </td>
                <td class="text-center">Add <input checked value="' . $val->BOOK_SEQ . '" name="approve[]" type="checkbox" class="iCheck approve"></td>
                <td class="hidden"><input name="book_id[]" value="' . $val->BOOK_SEQ . '" type="hidden"></td>
                </tr>
                ';
                $i++;
            }

            $data['status'] = 'ok';
        }
        //if no student found
        else{
            $data['status'] = 'error';
            $data['type'] = 'error';
            $data['msg'] = 'Sorry! No records found.';
        }
        $data['html'] = $html;

        return $data;
    }


    public function add_book_issue_detail() {
        $book_id = $this->input->post('book_id');
        $book_issue_date = $this->input->post('book_issue_date');
        $book_return_date = $this->input->post('book_return_date');
        $approve = $this->input->post('approve');
        $student_id = $this->input->post('student_id');

        
        foreach ($book_id as $val) {

            $exist_in_student_table = $this->db->get_where('book_issued', array('BOOK_SEQ' => $val, 'STD_SEQ' => $student_id, 'returned_or_not' => 0, 'return_date >=' => date('Y-m-d')))->num_rows();
            if($exist_in_student_table == 0) {
            $get_available_book_copies = $this->db->get_where('book_master', array('BOOK_SEQ' => $val))->row()->Available_Copies;
            $data_insert['BOOK_SEQ'] = $val;
            $data_insert['STD_SEQ'] = $student_id;
            $data_insert['issue_date'] = $book_issue_date[$val];
            $data_insert['return_date'] = $book_return_date[$val];
            $data_insert['update_by'] = $this->session->user_id;

            $this->db->insert('book_issued', $data_insert);
        $data_update['Available_Copies'] = ($get_available_book_copies - 1);
        if($get_available_book_copies > 0) {
        $this->db->update('book_master', $data_update, array('BOOK_SEQ' => $val));
        }
    }
        }

        $data['type'] = 'success';
        $data['msg'] = 'Details Added Successfully.';
        return $data;
    }


    public function add_book_issue_detail_for_multiple_student_lists() {
        $book_id = $this->input->post('book_id');
        $book_issue_date = $this->input->post('book_issue_date');
        $book_return_date = $this->input->post('book_return_date');
        $approve = $this->input->post('approve');
        $class_id = $this->input->post('class_id');


        $get_all_students_list = $this->db->get_where('student_details', array('STD_CS_SEQ' => $class_id, 'STD_LEFT' => 0, 'STD_STATUS' => 0))->result();

        
        foreach($get_all_students_list as $ge) {
        foreach ($book_id as $val) {

            
            $exist_in_student_table = $this->db->get_where('book_issued', array('BOOK_SEQ' => $val, 'STD_SEQ' => $ge->STD_SEQ, 'returned_or_not' => 0, 'return_date >=' => date('Y-m-d')))->num_rows();
            if($exist_in_student_table == 0) {
            $get_available_book_copies = $this->db->get_where('book_master', array('BOOK_SEQ' => $val))->row()->Available_Copies;
            $data_insert['BOOK_SEQ'] = $val;
            $data_insert['STD_SEQ'] = $ge->STD_SEQ;
            $data_insert['issue_date'] = $book_issue_date[$val];
            $data_insert['return_date'] = $book_return_date[$val];
            $data_insert['update_by'] = $this->session->user_id;

            $this->db->insert('book_issued', $data_insert);
            $data_update['Available_Copies'] = ($get_available_book_copies - 1);
        if($get_available_book_copies > 0) {
        $this->db->update('book_master', $data_update, array('BOOK_SEQ' => $val));
      }
  }
        }
    }

        $data['type'] = 'success';
        $data['msg'] = 'Details Added Successfully.';
        return $data;
    }


    public function add_issue_books($std_id) {
        $search_by_book_name = $this->input->get('b');
        $search_by_accession_no = $this->input->get('acn');

        $this->load->helper('url');
        $data['url_param1'] = 'library/add_issue_books/1';
        $data['url_param2'] = 'library/add_issue_books/2';
        $data['segment_val'] = $this->uri->segment(3);

        $std_id = 0;
        $class_id = 0;
        $class_sect = '';
        if ($this->uri->segment(3) == 1) {
            $std_id = $this->uri->segment(4);
            if ($std_id != 0) {
                $this->db->where('STD_SEQ', $std_id);
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
                $row_values = $this->db->get('student_details')->row();
                $data['section_heading'] = '<h4 style="text-align: center;">Student Name: <strong>' . $row_values->STD_FNAME . ' ' . $row_values->STD_MNAME . ' ' . $row_values->STD_LNAME . '</strong>   &nbsp;  Reg. No: <strong>' . $row_values->STD_REGNO . '</strong>   &nbsp;   Class & Sec: <strong>' . $row_values->Class_Name . ' - ' . $row_values->Sec_Name . '</strong>   &nbsp;   Roll No: <strong>' . $row_values->STD_ROLLNO . '</strong></h4>';
                $class_sect = $row_values->Class_Name . '-' . $row_values->Sec_Name;
            }
        } elseif ($this->uri->segment(3) == 2) {
            $class_id = $this->uri->segment(4);
            if ($class_id != 0) {
                $this->db->where('CS_SEQ', $class_id);
                $row_values = $this->db->get('class_sec_hdr')->row();
                $data['section_heading'] = '<h4 style="text-align: center;">Class: <strong>' . $row_values->Class_Name . '</strong>   &nbsp;  Section: <strong>' . $row_values->Sec_Name . '</strong></h4>';
                $class_sect = $row_values->Class_Name . '-' . $row_values->Sec_Name;
            }
        }
        $data['std_id'] = $std_id;
        $data['class_id'] = $class_id;
        $book_list = array();
//        $this->db->where('CS_SEQ', $row_values->CS_SEQ);
        $this->db->like('Book_Name', $search_by_book_name);
        $this->db->like('Accession_No', $search_by_accession_no);
        $book_lists_rows = $this->db->get_where('book_master', array('Available_Copies >' => 0))->result_array();
        if (count($book_lists_rows) > 0) {
            foreach ($book_lists_rows as $bk) {
                if ($this->uri->segment(3) == 1) {
                    $exist_in_student_table = $this->db->get_where('book_issued', array('BOOK_SEQ' => $bk['BOOK_SEQ'], 'STD_SEQ' => $std_id, 'returned_or_not' => 0, 'return_date >=' => date('Y-m-d')))->num_rows();
                    if ($exist_in_student_table > 0) {
                        continue;
                    }
                }
                array_push($book_list, $bk);
            }
        }
        $data['book_list'] = $book_list;
        $data['class_sect'] = $class_sect;
        if ($std_id == '') {
            $data['tab_title'] = 'Add Issue Books';
            $data['menu_name'] = 'Add Issue Books';
            return array('type' => 'load_view', 'page' => 'issue_books_add', 'data' => $data);
        }
        $this->db->where('STD_SEQ', $std_id);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        if (count((array)$row) == 0) { //if student not exists in student table
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Student does not exists.');
            return array('type' => 'redirect', 'page' => 'library/add_issue_books/1');
        }
        $data['tab_title'] = 'Issue Book';
        $data['menu_name'] = 'Issue Book';

        return array('type' => 'load_view', 'page' => 'issue_books_add', 'data' => $data);
    }

    public function ajax_delete_issued_book() {
        $id = $this->input->post('id');
        $this->db->where('BOOK_ISSUED_ID', $id);
        $this->db->delete('book_issued');

        $data['type'] = 'success';
        $data['msg'] = 'Record deleted.';
        return $data;
    }


}