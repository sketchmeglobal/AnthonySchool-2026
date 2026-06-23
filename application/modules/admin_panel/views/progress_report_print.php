<?php
// echo $form_type;
// echo '<pre>',print_r($student_details),'<pre>'; 

function fetch_marks($std_seq, $cs_seq, $test_seq, $term_seq, $sub_seq) {
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('marks_dtl',
            array(
                'MD_CLASS_SEQ' => $cs_seq,
                'MD_TEST_SEQ' => $test_seq,
                'MD_SUB_SEQ' => $sub_seq,
                'MD_TERM_SEQ' => $term_seq,
                'MD_STD_SEQ' => $std_seq)
        )->result();
    if(count($rv) == 0){
        return '-';
    } else {
        if(($rv[0]->MD_MARKS == NULL or $rv[0]->MD_MARKS == '') and ($rv[0]->MD_GRADE == NULL or $rv[0]->MD_GRADE == '')){
            return 'Ab';
        }else{

            $mt = $CI->db->get_where('subject', array('sub_id' => $sub_seq))->row()->marks_type;
            if($mt == 'Grade'){

                $rval=$rv[0]->MD_GRADE;

            }else{

                if($rv[0]->MD_MARKS < 10){
                    $rval='0'.$rv[0]->MD_MARKS;
                }else{
                    $rval=$rv[0]->MD_MARKS;
                }
            }

            return $rval;
        }
    }
}

function fetch_grade($marks_obtained, $total_marks,$class){
    if($class == 'IX' || $class == 'X'){
        if($marks_obtained >= 90){
           return"AA";
        }else if($marks_obtained >= 80 and $marks_obtained <= 89){
           return"A+";
        }else if($marks_obtained >= 60 and $marks_obtained <= 79){
           return"A";
        }else if($marks_obtained >= 45 and $marks_obtained <= 59){
           return"B+";
        }else if($marks_obtained >= 35 and $marks_obtained <= 44){
           return"B";
        }else if($marks_obtained >= 25 and $marks_obtained <= 34){
           return"C";
        }else if($marks_obtained >= 0 and $marks_obtained <= 24){
           return"D";
        }
    }else{
        $marks = floor($marks_obtained);
      $CI = & get_instance();
        $query = "SELECT * FROM `grades` WHERE `marks_from` >= ".$marks." AND `marks_to` <=".$marks;
        //echo $query;
        $grade = $CI->db->query($query)->row()->grade;
    
        return $grade;  
    }
    
}

function fetch_attendance($term_seq, $std_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('progress_report_entry', array('EXAM_TERM' => $term_seq,'STUDENT' => $std_seq))
        ->result();
    if(count($rv) == 0){
        return '-';
    } else {
        $total_working_days = $CI->db->get_where('exam_terms',array('et_id' => $term_seq))->row()->total_working_days;
        return $rv[0]->TOTAL_ATTENDANCE . '/' . $total_working_days;
    }
}

function fetch_general_remarks($term_seq, $std_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('progress_report_entry', array('EXAM_TERM' => $term_seq,'STUDENT' => $std_seq))
        ->result();
    if(count($rv) == 0){
        return '-';
    } else {
        return $rv[0]->GENERAL_REMARKS;
    }
}

function fetch_charcter_grade($term_seq, $std_seq, $title){
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('progress_report_entry', array('EXAM_TERM' => $term_seq,'STUDENT' => $std_seq))
        ->result();
    if(count($rv) == 0){
        return '-';
    } else {
        $field = 'GRADE_'.$title;
        return $rv[0]->$field;
    }
}

function fetch_marks_total($std_seq, $test_seq, $term_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->select('SUM(MD_MARKS) AS total_marks')
        ->group_by('MD_TERM_SEQ,MD_TEST_SEQ,MD_STD_SEQ')
        ->get_where('marks_dtl',
            array(
                'MD_TEST_SEQ' => $test_seq,
                'MD_TERM_SEQ' => $term_seq,
                'MD_STD_SEQ' => $std_seq)
        )
        ->row();
        //echo $CI->db->last_query();
    if(count($rv) == 0){
        return '-';
    } else{
        return ($rv->total_marks < 10) ? '0'. $rv->total_marks : $rv->total_marks;
    }
}

function fetch_sig_status($std_seq, $test_seq, $term_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->where_in('MD_TEST_SEQ', $test_seq)
        ->get_where('marks_dtl',
            array(
                'MD_TERM_SEQ' => $term_seq,
                'MD_STD_SEQ' => $std_seq
            )
        )->result();
    if(count($rv) == 0){
        return false;
    } else{
        return true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="Paper Css reports">
    <link href="https://stanthonyschooledu.org/2025-26/assets/img/favicon.ico" rel="shortcut icon" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400&display=swap" rel="stylesheet">
    <style>
        input[type="text"],input[type="password"],textarea {width: 100%;border: none;outline: none;border-bottom: 2px dashed #b21414;font-size: 14px;font-weight: bold;padding-left: 5px;color: #b21414;text-align: center;}
        section{margin:auto!important}
        label{color: #b21414;}
        table.table { font-family: 'Oswald', sans-serif;border-collapse: collapse;font-size: 12px;}

        .table td, .table th { border: 1px solid #000; text-align: left; padding: 0.2px;}
        .table th{font-size: 11px;text-align: center;font-weight: lighter}
        .table td{font-family: 'Open Sans', sans-serif;text-align: center;}
        .table tr td:first-child{font-weight:bold;text-align: left;padding-left: 5px}

        .left_table{width:128mm; float:left}
        .left_table tr th:first-child{width:125px}
        .right_table{width:128mm; float:right}
        .right_table tr td:first-child{padding: 0px 5px}
        .right_table tr td strong{font-size: 11px;}

        .padding-5mm{padding:5mm}
        .gradation{font-weight: bold;text-align: center!important;font-size:11px}
        .text-center {text-align: center;}
        .text-justify {text-align: justify;}
        .heading-text-style {color: #b21414;}
        .border-bottom {border-bottom: 2px solid;}
        .para-style {color: #f83030d4;font-size: 1.1rem;line-height: 1.25rem;}
        .main-div {border: 2px solid;border-radius: 10px;}
        .parent-p {padding: 0px 10px;}
        .font-weight-bld {font-weight: bolder;font-size: 1.1rem;}
        .img-fluid {max-width: 100%;display: block;}
        .d-flex {display: flex;justify-content: center;}
        .k2-style {color: #aeae80;font-size: 1.4rem;padding: 0px;margin: 0px;}
        .parent-form {border: 3px solid #b21414;padding: 0px 5px;}
        .signature{width: 100%;border: none;outline: none;border: 2px solid #b21414 !important;}
        .flex-display{display: flex;}
        .m-0{margin: 0}
        .my-3{margin: 12px 0px;}
        .mt-3{margin-top: 20px}
        .mb-3{margin-bottom: 20px}
        .mb-2{margin-bottom: 10px}
        .form-side-padding{padding: 0px 5px;margin-bottom: 11px;}
        body.A4.landscape .sheet{height:216mm !important;}
        
        @media print {
          .noPrint {
              display:none;
          }
        }
    </style>
</head>
<?php
if($form_type == 'progress_report_type_2'){
    ?>
    <body class="A4 landscape">
        <button class="noPrint btn btn-warning btn-sm btn-small" style="display:block;margin:auto" onclick="window.print()">Print</button>
    <?php foreach($student_details as $sd){ ?>

        <section class="sheet padding-15mm" style="height: auto!important">
            <div class="body-content">
                <div class="left_table">
                    <div class="main-div">
                        <div class="border-bottom">
                            <h2 class="text-center heading-text-style">IMPORTANT INFORMATION</h2>
                        </div>
                        <div class="parent-p">
                            <p class="text-justify para-style">This report enables us to share with parents/guardians our
                                observation on
                                the progress of their
                                sons. It should be duly signed and returned within one week. After the Annual Examination,
                                it
                                may be retained.</p>
                            <p class="text-justify para-style">
                                The school reserves the right to grant promotion to the student who has demonstrated the
                                ability
                                to cope with the next step in his educational progress. The decision of the school
                                authorities
                                with regard to promotion is final and indisputable.
                            </p>
                            <p class="text-justify para-style">
                                As academics is only one aspect of a holistic education, maturity, co-curricular and
                                extra-curricular development are also taken into account in judging the student's ability to
                                deal with the next class.

                            </p>
                            <p class="text-justify para-style">
                                An ailing student should never be sent to appear for an examination/ test under any
                                circumstances. However, the school authorities should be intimated about such illness.
                            </p>
                            <p class="text-justify para-style">
                                The minimum required attendance is 80% annually. Students from classes 1 to 8 should secure
                                40%
                                pass marks in all subjects for promotion to the next class. Students of Classes 8 and 9
                                follow
                                the criterion set down by the WBBSE
                            </p>
                            <p class="text-center para-style">
                                Students who fail to secure promotion for two consecutive years cannot be retained in the
                                school.
                            </p>
                            <p class="text-center para-style">
                                A fine of Rs. 50/- will be charged for a replacement of this report card.
                            </p>
                        </div>

                    </div>
                </div>
                <div class="table right_table">
                    <div class="main-div">
                        <div class="">
                            <h3 class="text-center heading-text-style" style="margin-bottom:0;font-size:20px;letter-spacing: 1px;">ST. ANTHONY'S HIGH SCHOOL</h3>
                            <h4 class="text-center heading-text-style font-weight-bld" style="margin-top:0;font-size: 0.95rem;">
                                (HIGHER SECONDARY)
                                <br>
                                19, MARKET STREET, KOLKATA-700 087
                            </h4>
                        </div>
                        <div class="d-flex" style="margin: 20px 0;">
                            <img style="height:132px" class="img-fluid" src="<?=base_url()?>/assets/img/favicon.ico" alt=""><!--width="100"-->
                        </div>
                        <div>
                            <h3 class="text-center m-0 heading-text-style">PROGRESS REPORT</h3>
                            <h3 class="text-center k2-style"><?=CURRENT_YEAR?></h3>
                            <h3 class="text-center m-0 heading-text-style">CLASS <?=$class . ' - ' . $sec?></h3>
                        </div>
                        <div class="form-side-padding">
                            <div class="parent-form">
                                <form action="#" method="post">
                                    <div class="flex-display my-3">
                                        <label for="name1">Name</label>
                                        <input type="text" id="name1" value="<?=$sd['STD_FNAME'] . ' ' . $sd['STD_MNAME'] . ' ' . $sd['STD_LNAME']?>">
                                    </div>
                                    <div class="flex-display my-3">

                                        <label for="name2">Class</label>
                                        <input type="text" id="name2" value="<?=$class?>">
                                        <label for="name3">Sec</label>
                                        <input type="text" id="name3" value="<?=$sec?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name4">Roll</label>
                                        <input type="text" id="name4" value="<?=$sd['STD_ROLLNO']?>">
                                        <label for="name5">Reg</label>
                                        <input type="text" id="name5" value="<?=$sd['STD_REGNO']?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name6">Parent/Guardian's Name</label>
                                        <input type="text" id="name6" value="<?=$sd['STD_FTH_NAME']?>">
                                    </div>
                                    <!-- <div class="flex-display my-3">
                                        <label for="name7">Address</label>
                                        <input type="text" id="name7">
                                    </div> -->
                                    <div class="flex-display my-3">
                                        <label for="name8">Phone</label>
                                        <input type="text" id="name8" value="<?=$sd['STD_PH_NO']?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name9">Specimen Signature of Parent/Guardian</label>
                                        <input style="height: 35px" class="signature" type="text" id="name9">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="main-div" style="margin-top: 8px;text-align: center;">
                        <label>Class Teacher For <?=$class . '-' . $sec?></label><br>
                        <div style="height: 40px;"></div>
                        <label class="text-center"><b><?= $class_teacher ?></b></label>
                    </div>
                </div>
            </div>
            
            <div style="display:block;clear:both;min-width:100%;height:50px"></div>
            
            <div class="body-content">
                <div class="left_table">
                    <div style="padding:5px;border: 2px solid; border-radius: 10px;margin-bottom:5px;margin-top: 0;text-align: center;">
                        <h4 style="margin:0">ACADEMIC PROGRESS</h4>
                        <h5 style="margin:0">Name:<?=$sd['STD_FNAME'] . ' ' . $sd['STD_MNAME'] . ' ' . $sd['STD_LNAME']?></h5>
                        <h5 style="margin:0">
                            <?='Class: '. $class . '-' . $sec . ' <b>|</b> Roll No.: ' . $sd['STD_ROLLNO'] . ' <b>|</b> Reg. No.: ' . $sd['STD_REGNO'] ?>
                        </h5>
                    </div>
                    <table class="table" style="width:100%">
                        <thead>
                        <tr>
                            <th style="font-size:11px">SCHOLASTIC AREA</th>
                            <th style="font-size:11px" colspan="4">FIRST TERM</th>
                            <th style="font-size:11px" colspan="4">SECOND TERM</th>
                            <th style="font-size:11px" colspan="4">FINAL TERM</th>
                        </tr>
                        <tr>
                            <th>SUBJECTS</th>

                            <th nowrap>&nbsp;FA 1&nbsp;<br> (10)</th>
                            <th nowrap>&nbsp;SA 1&nbsp;<br> (90)</th>
                            <th>TOTAL</th>
                            <th>GRADE</th>

                            <th nowrap>&nbsp;FA 2&nbsp;<br> (10)</th>
                            <th nowrap>&nbsp;SA 2&nbsp;<br> (90)</th>
                            <th>TOTAL</th>
                            <th>GRADE</th>

                            <th nowrap>&nbsp;FA 3&nbsp;<br> (10)</th>
                            <th nowrap>&nbsp;SA 3&nbsp;<br> (90)</th>
                            <th>TOTAL</th>
                            <th>GRADE</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $default_row = 10;
                        $subject_row = count($subjects);
                        $term_iter = 1; $entry_status_1 = $entry_status_2 = $entry_status_3 = 0;
                        
                        foreach($subjects as $sub){
                          
                            ?>

                            <tr>
                                <td><?=$sub->sub_name?></td> 
                                <?php 
                                
                                foreach($exam_terms as $et){ 
                                  $res1=fetch_marks($sd['STD_SEQ'], $cs_seq, $test_seq = 1, $et->et_id, $sub->CS_Sub_id);
                                  $res2=fetch_marks($sd['STD_SEQ'], $cs_seq, $test_seq = 4, $et->et_id, $sub->CS_Sub_id);
                                  if($res1 > 0 || $res2 > 0){
                                      $total = $res1+$res2;
                                  }else{
                                      $total = 0;
                                  }
                                  
                                ?>
                                    <td><?=$res1?></td>
                                    <td><?=$res2?></td>
                                    <td style="font-weight:bold">
                                       <?php 
                                       if(is_numeric($res1) || is_numeric($res2) ){
                                           $sum = ($res1 == '-' || $res2 == '-') ? '-' : $res1 + $res2;
                                            echo (is_numeric($sum)) ? (($sum < 0) ? '-'  :  str_pad($sum, 2, '0', STR_PAD_LEFT)) : '-'; 
                                       }else{
                                           echo '-';
                                       }
                                           
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if(is_numeric($res1) || is_numeric($res2) ){
                                          $total = ($res1 == '-' || $res2 == '-') ? '-' : ($res1 + $res2);
                                          echo is_numeric($total) ? fetch_grade($total, 100, $class) : $res2;  
                                        }else{
                                            echo $res2;
                                        }
                                            
                                        ?>
                                    </td>
                                <?php
                                } ?>
                            </tr>

                            <?php
                        }
                        
                        // add new row to look good
                        if($subject_row < $default_row){
                            $add_row = $default_row - $subject_row;
                            for($iter=1;$iter<=$add_row;$iter++){
                                ?>
                                <tr>
                                    <td style="text-align:center" colspan="13">-</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                        <!-- Grade -->
                        <tr>
                            <td>Grand Total Grade</td>

                            <?php 
                           
                            $percentageArray = []; 
                            
                            foreach ($exam_terms as $et) {
                                 $alltotal = 0;
                                $subcount = 0;
                                foreach($subjects as $sub){
                                   $res1=fetch_marks($sd['STD_SEQ'], $cs_seq, $test_seq = 1, $et->et_id, $sub->CS_Sub_id);
                                   $res2=fetch_marks($sd['STD_SEQ'], $cs_seq, $test_seq = 4, $et->et_id, $sub->CS_Sub_id);
                                
                                    $alltotal += ($res1 + $res2);
                                    if (($res1 + $res2) > 0) {
                                        $subcount += 1;
                                    } 
                                }
                                
                               
                                $percentage = $alltotal > 0 ? number_format(($alltotal / ($subcount * 100)) * 100, 2) : 0;
                            
                                $percentageArray[$et->et_id] = $percentage;//fetch_grade($percentage, 100, $class);
                            }
                       
                           
                            

                            foreach($exam_terms as $et2){
                              
                                
                            ?>
                                <td><?=$gt_res1=fetch_marks_total($sd['STD_SEQ'], $test_seq = 1, $et2->et_id)?></td>
                                <td><?=$gt_res2=fetch_marks_total($sd['STD_SEQ'], $test_seq = 4, $et2->et_id)?></td>
                                <td style="font-weight:bold"><?php echo ($gt_res1 == '-' and $gt_res2 == '-') ? '-' : (($gt_res1+$gt_res2 < 10) ? '0' . ($gt_res1+$gt_res2) : ($gt_res1+$gt_res2)); ?></td>
                                <td style="font-weight:bold"><?php echo $percentageArray[$et2->et_id];?></td>
                            <?php 
                            } 
                            
                            ?>
                        </tr>
                        <tr>
                            <td>Attendance</td>
                            <?php foreach($exam_terms as $et){ ?>
                                <td colspan="3"><?=fetch_attendance($et->et_id,$std_seq=$sd['STD_SEQ'])?></td>
                                <!--<td>< ?php echo fetch_grade($percentageArray[$et->et_id], 100, $class);?></td>-->
                                <td><?php echo fetch_grade($percentageArray[$et->et_id], 100, $class); ?></td>

                            <?php } ?>
                        </tr>
                       
                        <!-- signature -->
                        <tr>
                            <td>Class Teacher's <br> Signature</td>
                            <?php
                            foreach($exam_terms as $et){
                                if(fetch_sig_status($sd['STD_SEQ'], $test_seq = [1,4], $et->et_id)){
                                    ?>
                                    <td colspan="4"><img height="35" src="<?=base_url('assets/img/tch_sign/'.$class_teacher_sign)?>" /></td>
                                    <?php
                                }
                                else {
                                    echo '<td colspan="4"></td>';
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <td>Head master's <br> Signature</td>
                            
                            <?php foreach($exam_terms as $et){
                                  if(fetch_sig_status($sd['STD_SEQ'], $test_seq = [1,4], $et->et_id)){
                                    ?>
                            <td colspan="4"><img width="70"
                                    src="<?=base_url('assets/img') . '/' . $company->HEADMASTER_SIGN?>" /></td>
                            <?php } else{
                                     echo '<td colspan="4"></td>';
                                }
                             } ?>
                           <!-- < ?php
                            foreach($exam_terms as $et){
                                if(fetch_sig_status($sd['STD_SEQ'], $test_seq = [1,4], $et->et_id)){
                                    ?>
                                    <td colspan="4">
                                        < ?php  if($percentageArray[$et2->et_id]==0){ ?>
                                            <img height="35" src="" />
                                        < ?php } else{ ?>
                                            <img height="35" src="< ?=base_url('assets/img/'.$company->HEADMASTER_SIGN)?>" />
                                        < ?php } ?>
                                        
                                        
                                    </td>
                                    < ?php
                                }
                                else {
                                    echo '<td colspan="4"></td>';
                                }
                            }
                            ?> -->

                        </tr>
                        <tr>
                            <td>Parent's / Guardian's <br> Signature</td>
                            <?php foreach($exam_terms as $et){ ?>
                                <td colspan="4"></td>
                            <?php } ?>
                        </tr>

                        <!-- \remraks section -->
                        <?php foreach($exam_terms as $et){ ?>
                            <tr>
                                <td rowspan="1">General Remarks <br> (<?=$et->term_title?>)</td>
                                <td colspan="12" style="height:40px"><?=fetch_general_remarks($et->et_id,$std_seq=$sd['STD_SEQ'])?></td>
                            </tr>
                        <?php } ?>
                        
                       <tr>
                           
                            <?php

                                $roman_to_int = ['i'=>1, 'ii'=>2, 'iii'=>3, 'iv'=>4, 'v'=>5, 'vi'=>6, 'vii'=>7, 'viii'=>8, 'ix'=>9, 'x'=>10, 'xi'=>11, 'xii'=>12];
                                $int_to_roman = array_flip($roman_to_int);

                                $class_lower = strtolower($class);
                                $next_class = isset($roman_to_int[$class_lower]) ? $int_to_roman[$roman_to_int[$class_lower] + 1] : $class;

                                if ($percentage > 40) {
                                    echo '<td colspan="14" style="text-align: center; font-weight: bold; padding: 7px 0px; font-size: .9rem;">Promoted to class ' . strtoupper($next_class) . '</td>';
                                } elseif ($percentage >= 20 && $percentage <= 39) {
                                    echo '<td colspan="14" style="text-align: center; font-weight: bold; padding: 7px 0px; font-size: .9rem;">Promoted on trial</td>';
                                } elseif ($percentage >= 2 && $percentage <= 19) {
                                    echo '<td colspan="14" style="text-align: center; font-weight: bold; padding: 7px 0px; font-size: .9rem;">Promoted on warning</td>';
                                }
                                ?>





                        </tr>
                        <!-- GRADATION DETAILS -->
                        <?php
                        if($class == 'IX' || $class == 'X'){
                            ?>
                            <tr>
                                <td class="gradation" rowspan="7">Gradation Criterion</td>
                                <td class="gradation" colspan="4">AA</td>
                                <td class="gradation" colspan="4">100% - 90%</td>
                                <td class="gradation" colspan="4">OUTSTANDING</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">A+</td>
                                <td class="gradation" colspan="4">89% - 80%</td>
                                <td class="gradation" colspan="4">EXCELLENT</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">A</td>
                                <td class="gradation" colspan="4">79% - 60%</td>
                                <td class="gradation" colspan="4">VERY GOOD</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">B+</td>
                                <td class="gradation" colspan="4">59% - 45%</td>
                                <td class="gradation" colspan="4">GOOD</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">B</td>
                                <td class="gradation" colspan="4">44% - 35%</td>
                                <td class="gradation" colspan="4">SATISFACTORY</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">C</td>
                                <td class="gradation" colspan="4">34% - 25%</td>
                                <td class="gradation" colspan="4">MARGINAL</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">D</td>
                                <td class="gradation" colspan="4">24% - 0%</td>
                                <td class="gradation" colspan="4">DISQUALIFIED</td>
                            </tr>
                           <?php 
                        }else{
                           ?>
                            <tr>
                                <td class="gradation" rowspan="7">Gradation Criterion</td>
                                <td class="gradation" colspan="4">AA</td>
                                <td class="gradation" colspan="4">100% - 90%</td>
                                <td class="gradation" colspan="4">OUTSTANDING</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">A+</td>
                                <td class="gradation" colspan="4">89% - 80%</td>
                                <td class="gradation" colspan="4">EXCELLENT</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">A</td>
                                <td class="gradation" colspan="4">79% - 70%</td>
                                <td class="gradation" colspan="4">VERY GOOD</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">B+</td>
                                <td class="gradation" colspan="4">69% - 60%</td>
                                <td class="gradation" colspan="4">GOOD</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">B</td>
                                <td class="gradation" colspan="4">59% - 50%</td>
                                <td class="gradation" colspan="4">SATISFACTORY</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">C</td>
                                <td class="gradation" colspan="4">49% - 40%</td>
                                <td class="gradation" colspan="4">MARGINAL</td>
                            </tr>
                            <tr>
                                <td class="gradation" colspan="4">D</td>
                                <td class="gradation" colspan="4">39% - 0%</td>
                                <td class="gradation" colspan="4">DISQUALIFIED</td>
                            </tr>
                           <?php 
                        }
                        ?>
                       
                        </tbody>
                    </table>
                </div>


                <table class="table right_table">
                    <tr>
                        <th style="font-size:14.5px" colspan="5">CHARACTER DEVELOPMENT</th>
                    </tr>
                    <tr>
                        <td style="text-align: left;padding-left:10px">
                            <strong>GRADES</strong><br>
                            <span>A - Advanced Development </span><br>
                            <span>B - Good Development</span><br>
                            <span>C - Average Development </span><br>
                            <span>D - Below Average Development</span>
                        </td>
                        <td class="gradation">First Term</td>
                        <td class="gradation">Second Term</td>
                        <td class="gradation">Final Term</td>

                    </tr>
                    <tr>

                        <td><strong>INITIATIVE</strong><br>(can respond to situations properly)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'INITIATIVE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'INITIATIVE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'INITIATIVE')?></td>
                    </tr>
                    <tr>
                        <td><strong>PERSEVERANCE</strong> <br>continues with the task in hand till completed</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'PERSEVERANCE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'PERSEVERANCE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'PERSEVERANCE')?></td>
                    </tr>
                    <tr>
                        <td><strong>ORIGINALITY</strong><br>(has ideas of his own, does not try to copy others)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'ORIGINALITY')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'ORIGINALITY')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'ORIGINALITY')?></td>
                    </tr>
                    <tr>
                        <td><strong>CONCENTRATION</strong><br>(can apply himself to the work in hand without being
                            distracted by what is going on around him)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'CONCENTRATION')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'CONCENTRATION')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'CONCENTRATION')?></td>
                    </tr>
                    <tr>
                        <td><strong>OBSERVATION</strong><br>(is alert and aware of surroundings, remembers
                            what he sees)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'OBSERVATION')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'OBSERVATION')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'OBSERVATION')?></td>
                    </tr>
                    <tr>
                        <td><strong>CURIOSITY</strong><br>(asks questions, seeks answers, is interested in finding
                            out more)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'CURIOSITY')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'CURIOSITY')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'CURIOSITY')?></td>
                    </tr>
                    <tr>
                        <td><strong>CONFIDENCE</strong><br>(is not afraid to try out new things, to work alone, to ask
                            questions )</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'CONFIDENCE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'CONFIDENCE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'CONFIDENCE')?></td>
                    </tr>
                    <tr>
                        <td><strong>RESPONSIBILITY</strong><br>(can be trusted to carry out tasks, to look after
                            his belongings and to respect the property of others)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'RESPONSIBILITY')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'RESPONSIBILITY')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'RESPONSIBILITY')?></td>
                    </tr>
                    <tr>
                        <td><strong>RELATIONSHIPS</strong><br>(is friendly, helpful and caring)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'RELATIONSHIPS')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'RELATIONSHIPS')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'RELATIONSHIPS')?></td>
                    </tr>
                    <tr>
                        <td><strong>PARTICIPATION IN GROUP WORK</strong><br>(contributes readily, works well with others,
                            respects team spirit)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'PARTICIPATION')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'PARTICIPATION')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'PARTICIPATION')?></td>
                    </tr>
                    <tr>
                        <td><strong>NEATNESS</strong><br>(is tidy and orderly in his work and appearance)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'NEATNESS')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'NEATNESS')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'NEATNESS')?></td>
                    </tr>
                    <tr>
                        <td><strong>SPIRIT OF SERVICE</strong><br>(is prepared to sacrifice time and energy for those less
                            privileged than himself)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'SPIRIT_OF_SERVICE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'SPIRIT_OF_SERVICE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'SPIRIT_OF_SERVICE')?></td>
                    </tr>
                    <tr>
                        <td><strong>SOCIAL AWARENESS</strong><br>(is aware of society and how it functions and is
                            concerned for those around him)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'SOCIAL_AWARENESS')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'SOCIAL_AWARENESS')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'SOCIAL_AWARENESS')?></td>
                    </tr>
                    <tr>
                        <td><strong>TIME MANAGEMENT</strong><br>(uses time fruitfully, hands in assignments on time,
                            is punctual)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'TIME_MANAGEMENT')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'TIME_MANAGEMENT')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'TIME_MANAGEMENT')?></td>
                    </tr>
                    <tr>

                    <?php if ($percentage > 0) { ?>
                    <td colspan="4">
                        <img style="padding:20px 0px; margin: 0 auto;display: block; width: 60px;"
                            src="<?=base_url('assets/img') . '/' . $company->HEADMASTER_SIGN?>" />
                    </td>
                    <?php } ?>

                </tr>
                </table>

            </div>
        </section>


    <?php } ?>

    <!-- Placed js at the end of the document so the pages load faster -->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
    <script></script>
    </body>
    <?php
    }

else if($form_type == 'progress_report_type_3'){
    ?>
    <body class="A4">

    <?php foreach($student_details as $sd){ ?>
        <section class="sheet padding-15mm">
            <div class="body-content">
                <div class="left_table">
                    <div class="main-div">
                        <div class="border-bottom">
                            <h2 class="text-center heading-text-style">IMPORTANT INFORMATION</h2>
                        </div>
                        <div class="parent-p">
                            <p class="text-justify para-style">This report enables us to share with parents/guardians our
                                observation on
                                the progress of their
                                sons. It should be duly signed and returned within one week. After the Annual Examination,
                                it
                                may be retained.</p>
                            <p class="text-justify para-style">
                                The school reserves the right to grant promotion to the student who has demonstrated the
                                ability
                                to cope with the next step in his educational progress. The decision of the school
                                authorities
                                with regard to promotion is final and indisputable.
                            </p>
                            <p class="text-justify para-style">
                                As academics is only one aspect of a holistic education, maturity, co-curricular and
                                extra-curricular development are also taken into account in judging the student's ability to
                                deal with the next class.

                            </p>
                            <p class="text-justify para-style">
                                An ailing student should never be sent to appear for an examination/ test under any
                                circumstances. However, the school authorities should be intimated about such illness.
                            </p>
                            <p class="text-justify para-style">
                                The minimum required attendance is 80% annually. Students from classes 1 to 8 should secure
                                40%
                                pass marks in all subjects for promotion to the next class. Students of Classes 8 and 9
                                follow
                                the criterion set down by the WBBSE
                            </p>
                            <p class="text-center para-style">
                                Students who fail to secure promotion for two consecutive years cannot be retained in the
                                school.
                            </p>
                            <p class="text-center para-style">
                                A fine of Rs. 50/- will be charged for a replacement of this report card.
                            </p>
                        </div>

                    </div>
                </div>
                <div class="table right_table">
                    <div class="main-div">
                        <div class="">
                            <h3 class="text-center heading-text-style" style="margin-bottom:0;font-size:20px;letter-spacing: 1px;">ST. ANTHONY'S HIGH SCHOOL</h3>
                            <h4 class="text-center heading-text-style font-weight-bld" style="margin-top:0;font-size: 0.95rem;">
                                (HIGHER SECONDARY)
                                <br>
                                19, MARKET STREET, KOLKATA-700 087
                            </h4>
                        </div>
                        <div class="d-flex" style="margin: 20px 0;">
                            <img style="height:132px" class="img-fluid" src="<?=base_url()?>/assets/img/favicon.ico" alt=""><!--width="100"-->
                        </div>
                        <div>
                            <h3 class="text-center m-0 heading-text-style">PROGRESS REPORT</h3>
                            <h3 class="text-center k2-style"><?=CURRENT_YEAR?></h3>
                            <h3 class="text-center m-0 heading-text-style">CLASS <?=$class . ' - ' . $sec?></h3>
                        </div>
                        <div class="form-side-padding">
                            <div class="parent-form">
                                <form action="#" method="post">
                                    <div class="flex-display my-3">
                                        <label for="name1">Name</label>
                                        <input type="text" id="name1" value="<?=$sd['STD_FNAME'] . ' ' . $sd['STD_MNAME'] . ' ' . $sd['STD_LNAME']?>">
                                    </div>
                                    <div class="flex-display my-3">

                                        <label for="name2">Class</label>
                                        <input type="text" id="name2" value="<?=$class?>">
                                        <label for="name3">Sec</label>
                                        <input type="text" id="name3" value="<?=$sec?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name4">Roll</label>
                                        <input type="text" id="name4" value="<?=$sd['STD_ROLLNO']?>">
                                        <label for="name5">Reg</label>
                                        <input type="text" id="name5" value="<?=$sd['STD_REGNO']?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name6">Parent/Guardian's Name</label>
                                        <input type="text" id="name6" value="<?=$sd['STD_FTH_NAME']?>">
                                    </div>
                                    <!-- <div class="flex-display my-3">
                                        <label for="name7">Address</label>
                                        <input type="text" id="name7">
                                    </div> -->
                                    <div class="flex-display my-3">
                                        <label for="name8">Phone</label>
                                        <input type="text" id="name8" value="<?=$sd['STD_PH_NO']?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name9">Specimen Signature of Parent/Guardian</label>
                                        <input style="height: 35px" class="signature" type="text" id="name9">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="main-div" style="margin-top: 8px;text-align: center;">
                        <label>Class Teacher For <?=$class . '-' . $sec?></label><br>
                        <div style="height: 40px;"></div>
                        <label class="text-center"><b><?= $class_teacher ?></b></label>
                    </div>
                </div>
            </div>
        </section>

        <section class="sheet padding-5mm">

            <!-- body content start-->
            <div class="body-content" style="margin-top 80px;">
                <div class="left_table">
                    <div style="padding:15px;border: 2px solid; border-radius: 10px;margin-bottom:10px;margin-top: 0;text-align: center;">
                        <h4 style="margin:0">ACADEMIC PROGRESS</h4>
                        <h5 style="margin:0">Name:<?=$sd['STD_FNAME'] . ' ' . $sd['STD_MNAME'] . ' ' . $sd['STD_LNAME']?></h5>
                        <h5 style="margin:0"><?='Class: '. $class . '-' . $sec . ' <b>|</b> Roll No.: ' . $sd['STD_ROLLNO'] . ' <b>|</b> Reg. No.: ' . $sd['STD_REGNO'] ?></h5>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>SCHOLASTIC AREA</th>
                            <th colspan="4">FIRST TERM</th>
                            <th colspan="4">SECOND TERM</th>
                            <th colspan="4">FINAL TERM</th>
                        </tr>
                        <tr>
                            <th>SUBJECTS</th>

                            <th>FA 1 <br> (10)</th>
                            <th>SA 1 <br> (90)</th>
                            <th>TOTAL</th>
                            <th>GRADE</th>

                            <th>FA 2 <br> (10)</th>
                            <th>SA 2 <br> (90)</th>
                            <th>TOTAL</th>
                            <th>GRADE</th>

                            <th>FA 3 <br> (10)</th>
                            <th>SA 3 <br> (90)</th>
                            <th>TOTAL</th>
                            <th>GRADE</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach($subjects as $sub){
                            ?>

                            <tr>
                                <td><?=$sub->sub_name?></td>
                                <?php foreach($exam_terms as $et){ ?>
                                    <td><?=$res1=fetch_marks($sd['STD_SEQ'], $cs_seq, $test_seq = 2, $et->et_id, $sub->CS_Sub_id)?></td>
                                    <td><?=$res2=fetch_marks($sd['STD_SEQ'], $cs_seq, $test_seq = 3, $et->et_id, $sub->CS_Sub_id)?></td>
                                    <td>
                                        <?php

                                        // show pinci sign if any data is entered in any of the terms
                                        if($term_iter == 1 and ($res1+$res2) > 0){
                                            $entry_status_1 = 1;
                                        }
                                        if($term_iter == 2 and ($res1+$res2) > 0){
                                            $entry_status_2 = 1;
                                        }if($term_iter == 3 and ($res1+$res2) > 0){
                                            $entry_status_3 = 1;
                                        }
                                        $term_iter++;

                                        echo ($res1+$res2 < 10) ? '0' . ($res1+$res2) : ($res1+$res2);
                                        ?>
                                    </td>
                                    <td><?=fetch_grade(($res1+$res2),100,$class)?></td>
                                <?php } ?>
                            </tr>

                            <?php
                        } ?>

                        <!-- Grade -->
                        <tr>
                            <td>Grand Total Grade</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Attendance</td>
                            <?php foreach($exam_terms as $et){ ?>
                                <td colspan="4"><?=fetch_attendance($et->et_id,$std_seq=$sd['STD_SEQ'])?></td>
                            <?php } ?>
                        </tr>
                        <!-- signature -->
                        <tr>
                            <td>Class Teacher's Signature</td>
                            <?php foreach($exam_terms as $et){ ?>
                                <td colspan="4"></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td>Head master's <br> Signature</td>
                            <?php
                            $sign_iter = 1;
                            foreach($exam_terms as $et){
                                if(($sign_iter == 1 and $entry_status_1 == 1) or ($sign_iter == 2 and $entry_status_2 == 2) or ($sign_iter == 3 and $entry_status_3 == 3)){
                                    ?>
                                    <td colspan="4"><?=$sign_iter . ' .. ' . $entry_status_1;?><img height="35" src="<?=base_url('assets/img') . '/' . $company->HEADMASTER_SIGN?>" /></td>
                                    <?php
                                }
                                $sign_iter++;
                            }
                            ?>
                        </tr>
                        <tr>
                            <td>Parent's / Guardian's Signature</td>
                            <?php foreach($exam_terms as $et){ ?>
                                <td colspan="4"></td>
                            <?php } ?>
                        </tr>

                        <!-- \remraks section -->
                        <?php foreach($exam_terms as $et){ ?>
                            <tr>
                                <td rowspan="1">General Remarks <br> (<?=$et->term_title?>)</td>
                                <td colspan="12" style="height:72px"><?=fetch_general_remarks($et->et_id,$std_seq=$sd['STD_SEQ'])?></td>
                            </tr>
                        <?php } ?>

                        <!-- GRADATION DETAILS -->
                      
                        <tr>
                            <td class="gradation" rowspan="7">Gradation Criterion</td>
                            <td class="gradation" colspan="4">AA</td>
                            <td class="gradation" colspan="4">100% - 90%</td>
                            <td class="gradation" colspan="4">OUTSTANDING</td>
                        </tr>
                        <tr>
                            <td class="gradation" colspan="4">A+</td>
                            <td class="gradation" colspan="4">89% - 80%</td>
                            <td class="gradation" colspan="4">EXCELLENT</td>
                        </tr>
                        <tr>
                            <td class="gradation" colspan="4">A</td>
                            <td class="gradation" colspan="4">79% - 70%</td>
                            <td class="gradation" colspan="4">VERY GOOD</td>
                        </tr>
                        <tr>
                            <td class="gradation" colspan="4">B+</td>
                            <td class="gradation" colspan="4">69% - 60%</td>
                            <td class="gradation" colspan="4">GOOD</td>
                        </tr>
                        <tr>
                            <td class="gradation" colspan="4">B</td>
                            <td class="gradation" colspan="4">59% - 50%</td>
                            <td class="gradation" colspan="4">SATISFACTORY</td>
                        </tr>
                        <tr>
                            <td class="gradation" colspan="4">C</td>
                            <td class="gradation" colspan="4">49% - 40%</td>
                            <td class="gradation" colspan="4">MARGINAL</td>
                        </tr>
                        <tr>
                            <td class="gradation" colspan="4">D</td>
                            <td class="gradation" colspan="4">39% - 0%</td>
                            <td class="gradation" colspan="4">DISQUALIFIED</td>
                        </tr>
                        </tbody>
                    </table>
                </div>


                <table class="table right_table">
                    <tr>
                        <th colspan="5">CHARACTER DEVELOPMENT</th>

                    </tr>
                    <tr>
                        <td style="text-align: left;padding-left:10px">
                            <strong>GRADES</strong><br>
                            <span>A - Advanced Development </span><br>
                            <span>B - Good Development</span><br>
                            <span>C - Average Development </span><br>
                            <span>D - Below Average Development</span>
                        </td>
                        <td class="gradation">First Term</td>
                        <td class="gradation">Second Term</td>
                        <td class="gradation">Final Term</td>

                    </tr>
                    <tr>

                        <td><strong>INITIATIVE</strong><br>(can respond to situations properly)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'INITIATIVE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'INITIATIVE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'INITIATIVE')?></td>
                    </tr>
                    <tr>
                        <td><strong>PERSEVERANCE</strong> <br>continues with the task in hand till completed</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'PERSEVERANCE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'PERSEVERANCE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'PERSEVERANCE')?></td>
                    </tr>
                    <tr>
                        <td><strong>ORIGINALITY</strong><br>(has ideas of his own, does not try to copy others)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'ORIGINALITY')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'ORIGINALITY')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'ORIGINALITY')?></td>
                    </tr>
                    <tr>
                        <td><strong>CONCENTRATION</strong><br>(can apply himself to the work in hand without being
                            distracted by what is going on around him)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'CONCENTRATION')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'CONCENTRATION')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'CONCENTRATION')?></td>
                    </tr>
                    <tr>
                        <td><strong>OBSERVATION</strong><br>(is alert and aware of surroundings, remembers
                            what he sees)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'OBSERVATION')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'OBSERVATION')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'OBSERVATION')?></td>
                    </tr>
                    <tr>
                        <td><strong>CURIOSITY</strong><br>(asks questions, seeks answers, is interested in finding
                            out more)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'CURIOSITY')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'CURIOSITY')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'CURIOSITY')?></td>
                    </tr>
                    <tr>
                        <td><strong>CONFIDENCE</strong><br>(is not afraid to try out new things, to work alone, to ask
                            questions )</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'CONFIDENCE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'CONFIDENCE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'CONFIDENCE')?></td>
                    </tr>
                    <tr>
                        <td><strong>RESPONSIBILITY</strong><br>(can be trusted to carry out tasks, to look after
                            his belongings and to respect the property of others)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'RESPONSIBILITY')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'RESPONSIBILITY')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'RESPONSIBILITY')?></td>
                    </tr>
                    <tr>
                        <td><strong>RELATIONSHIPS</strong><br>(is friendly, helpful and caring)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'RELATIONSHIPS')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'RELATIONSHIPS')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'RELATIONSHIPS')?></td>
                    </tr>
                    <tr>
                        <td><strong>PARTICIPATION IN GROUP WORK</strong><br>(contributes readily, works well with others,
                            respects team spirit)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'PARTICIPATION')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'PARTICIPATION')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'PARTICIPATION')?></td>
                    </tr>
                    <tr>
                        <td><strong>NEATNESS</strong><br>(is tidy and orderly in his work and appearance)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'NEATNESS')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'NEATNESS')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'NEATNESS')?></td>
                    </tr>
                    <tr>
                        <td><strong>SPIRIT OF SERVICE</strong><br>(is prepared to sacrifice time and energy for those less
                            privileged than himself)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'SPIRIT_OF_SERVICE')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'SPIRIT_OF_SERVICE')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'SPIRIT_OF_SERVICE')?></td>
                    </tr>
                    <tr>
                        <td><strong>SOCIAL AWARENESS</strong><br>(is aware of society and how it functions and is
                            concerned for those around him)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'SOCIAL_AWARENESS')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'SOCIAL_AWARENESS')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'SOCIAL_AWARENESS')?></td>
                    </tr>
                    <tr>
                        <td><strong>TIME MANAGEMENT</strong><br>(uses time fruitfully, hands in assignments on time,
                            is punctual)</td>
                        <td><?=fetch_charcter_grade($term=1,$std_seq=$sd['STD_SEQ'],'TIME_MANAGEMENT')?></td>
                        <td><?=fetch_charcter_grade($term=2,$std_seq=$sd['STD_SEQ'],'TIME_MANAGEMENT')?></td>
                        <td><?=fetch_charcter_grade($term=3,$std_seq=$sd['STD_SEQ'],'TIME_MANAGEMENT')?></td>
                    </tr>
                </table>

            </div>

        </section>
    <?php } ?>

    <!-- Placed js at the end of the document so the pages load faster -->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
    <script></script>
    </body>
    <?php
    }

else if($form_type == 'progress_report_type_4'){
    // Determine section based on class
    $section_name = '';
    $class_upper = strtoupper($class);
    
    if(in_array($class_upper, array('NUR', 'NURSERY', 'KG', 'LKG', 'UKG'))){
        $section_name = 'Nursery & KG';
    } else if(in_array($class_upper, array('I', 'II', 'III', 'IV', '1', '2', '3', '4'))){
        $section_name = 'Primary Section';
    } else if(in_array($class_upper, array('V', 'VI', 'VII', 'VIII', 'IX', 'X', '5', '6', '7', '8', '9', '10'))){
        $section_name = 'Secondary Section';
    } else if(in_array($class_upper, array('XI', 'XII', '11', '12'))){
        $section_name = 'Higher Secondary';
    } else {
        $section_name = 'Higher Secondary'; // Default fallback
    }
    ?>
    <style>
        @page{size:A4;margin:0;}
        section{margin: 20px auto !important;}
        .print-button{position:fixed;top:20px;right:20px;padding:12px 24px;background:#8B4513;color:#fff;border:none;border-radius:5px;font-size:14px;font-weight:bold;cursor:pointer;box-shadow:0 2px 5px rgba(0,0,0,0.2);z-index:9999;}
        .print-button:hover{background:#6B3410;}
        .control-panel{position:fixed;top:70px;right:20px;z-index:9998;max-width:320px;max-height:80vh;overflow-y:auto;}
        .toggle-controls{width:100%;padding:10px;background:#8B4513;color:#fff;border:none;border-radius:5px;font-size:14px;font-weight:bold;cursor:pointer;margin-bottom:10px;}
        .toggle-controls:hover{background:#6B3410;}
        .controls-content{font-size:13px;color:#333;}
        .control-group{margin-bottom:12px;display:flex;align-items:center;gap:10px;}
        .control-group label{flex:0 0 140px;font-weight:bold;font-size:11px;color:#8B4513;}
        .control-group input[type=range]{flex:1;cursor:pointer;}
        .control-group span{flex:0 0 50px;text-align:right;font-size:11px;font-weight:bold;color:#666;}
        /*@media print{.print-button,.control-panel{display:none;}body{margin:0;padding:0;}}*/
        @media print{
            .print-button,.control-panel{display:none;}
            body{margin:0;padding:0;}
            @page{margin:5mm 10mm;}
            .sheet{margin:0 !important;}
            .certificate-container{margin-top:0;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .watermark{-webkit-print-color-adjust:exact;print-color-adjust:exact;opacity:1 !important;}
            .watermark-logo{-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .marks-table th{background:#8B4513 !important;color:#fff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .marks-table .term-header{background:#8B4513 !important;color:#fff !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .info-label{background:#F5DEB3 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .marks-table .subject-name{background:#FFFAF0 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
            .marks-table .total-row{background:#F5DEB3 !important;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
        }
        body.A4{background:#f5f5f5;}
        body.A4 .sheet{margin:10mm auto;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Times New Roman',serif;background:#f5f5f5;padding:10px;}
        .watermark-logo{position:absolute;inset:0;z-index:0;pointer-events:none;display:flex;justify-content:center;align-items:center;opacity:0.08;}
        .watermark-logo img{width:400px;height:400px;object-fit:contain;}
        .certificate-container{background-image:repeating-linear-gradient(0deg,rgba(139,69,19,0.03) 0 1px,transparent 1px 25px),repeating-linear-gradient(90deg,rgba(139,69,19,0.03) 0 1px,transparent 1px 100px);background-size:auto;position:relative;border:5px solid #8a4513;padding:8mm;}
        .watermark{position:absolute;inset:0;z-index:0;pointer-events:none;background-repeat:repeat;background-size:60px 30px;background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="30"><text x="2" y="20" font-size="16" fill="rgba(139,69,19,0.06)" font-weight="bold" font-family="Arial">SAHS</text></svg>');opacity:1;}
        .content-wrapper{position:relative;z-index:1;}
        .header{text-align:center;border-bottom:3px solid #8B4513;padding-bottom:5px;margin-bottom:10px;}
        .school-logo{width:80px;height:80px;margin:0 auto 5px;border:3px solid transparent;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#fff;font-size:10px;font-weight:bold;color:#8B4513;}
        .school-name{font-size:24px;font-weight:bold;color:#8B4513;margin:3px 0 0 0;text-transform:uppercase;position:relative;height:70px;}
        .curved-text-container{width:100%;height:70px;display:flex;justify-content:center;align-items:center;}
        .curved-text-container svg{overflow:visible;}
        .curved-text-container text{font-family:'Times New Roman',serif;font-size:24px;font-weight:bold;fill:#8B4513;letter-spacing:2px;}
        .school-type{font-size:12px;color:#333;margin:0;font-weight:bold;margin-bottom:10px}
        .school-address{font-size:12px;color:#333;margin:0;font-weight:bold;margin-bottom:10px}
        .report-title{font-size:26px;font-weight:bold;color:#B8860B;margin:10px 0 6px;letter-spacing:1px;}
        .session-year{font-size:16px;font-weight:bold;color:#2E8B57;margin-bottom:10px;}
        .info-table{width:100%;border-collapse:collapse;margin-bottom:10px;font-size:13px;}
        .info-table td{border:2px solid #8B4513;padding:6px;}
        .info-label{background:#F5DEB3;font-weight:bold;width:35%;color:#333;}
        .info-value{background:#FFFAF0;width:65%;}
        .info-table-double td{width:25%;}
        .marks-table{width:100%;border-collapse:collapse;margin-bottom:0;font-size:10px;}
        .marks-table th{background:#8B4513;color:#fff;border:1px solid #000;padding:4px;font-weight:bold;text-align:center;}
        .marks-table td{border:2px solid #8B4513;padding:3px 2px;text-align:center;}
        .marks-table .subject-name{text-align:left;background:#FFFAF0;font-weight:bold;padding-left:5px;}
        .marks-table .marks-cell{background:transparent;font-weight:normal;}
        .marks-table .total-cell{font-size:12px;font-weight:bold;}
        .marks-table .total-row{background:#F5DEB3;font-weight:bold;}
        .marks-table .header-main{background:#8B4513;color:#fff;font-weight:bold;font-size:10px;}
        .marks-table .term-header{background:#8B4513;color:#fff;font-weight:bold;}
        .marks-table tbody tr td{background:transparent;}
        .marks-table tbody tr td.subject-name{background:#FFFAF0;}
        .marks-table tbody tr.total-row td{background:#F5DEB3;}
        .info-table td.info-value{background:transparent;}
        .section-break{margin-bottom:20mm;page-break-after:always;}
        .section{margin:20px auto !important;}
        .summary-table{width:100%;border-collapse:collapse;margin-bottom:12px;font-size:11px;}
        .summary-table td{border:2px solid #8B4513;padding:6px;text-align:center;font-weight:bold;}
        .summary-label{background:#F5DEB3;width:30%;}
        .summary-value{background:#FFFAF0;width:20%;}
        .remarks-section{margin-bottom:12px;}
        .remarks-box{border:2px solid #8B4513;padding:8px;background:#FFFAF0;min-height:50px;font-size:11px;line-height:1.4;}
        .footer{display:flex;justify-content:space-between;margin-top:15px;font-size:11px;}
        .footer-item{text-align:center;}
        .footer-label{font-weight:bold;margin-bottom:30px;color:#8B4513;}
        .footer-line{border-top:1px solid #333;padding-top:3px;font-weight:bold;}
        .note-section{font-size:9px;font-style:italic;text-align:center;color:#666;margin-top:10px;}
        @media print{body{background:#fff;padding:0;}.certificate-container{box-shadow:none;margin:0;width:100%;height:100%;}}
    </style>
    <body class="A4">
        <!-- Print Button -->
        <button class="print-button noPrint" onclick="window.print()">🖨️ Print Report</button>
        
        <!-- Control Panel -->
        <div class="control-panel noPrint">
            <button class="toggle-controls" onclick="toggleControls()">⚙️ Layout Controls</button>
            <div class="controls-content" id="controlsContent" style="display: none;">
                <h3 style="margin: 0 0 10px 0; color: #8B4513;">Adjust Layout Settings</h3>
                
                <!-- Font Size Controls -->
                <div class="control-group">
                    <label>School Name Font:</label>
                    <input type="range" id="schoolNameFont" min="16" max="32" value="24" step="1" oninput="updateSchoolNameFont(this.value)">
                    <span id="schoolNameValue">24px</span>
                </div>
                
                <div class="control-group">
                    <label>Section Name Font:</label>
                    <input type="range" id="sectionNameFont" min="8" max="16" value="12" step="1" oninput="updateSectionNameFont(this.value)">
                    <span id="sectionNameValue">12px</span>
                </div>
                
                <div class="control-group">
                    <label>Report Title Font:</label>
                    <input type="range" id="reportTitleFont" min="18" max="32" value="26" step="1" oninput="updateReportTitleFont(this.value)">
                    <span id="reportTitleValue">26px</span>
                </div>
                
                <div class="control-group">
                    <label>Session Year Font:</label>
                    <input type="range" id="sessionYearFont" min="12" max="20" value="16" step="1" oninput="updateSessionYearFont(this.value)">
                    <span id="sessionYearValue">16px</span>
                </div>
                
                <div class="control-group">
                    <label>Student Info Font:</label>
                    <input type="range" id="infoFontSize" min="8" max="16" value="13" step="1" oninput="updateInfoFont(this.value)">
                    <span id="infoFontValue">13px</span>
                </div>
                
                <div class="control-group">
                    <label>Marks Table Font:</label>
                    <input type="range" id="marksFontSize" min="6" max="14" value="10" step="1" oninput="updateMarksFont(this.value)">
                    <span id="marksFontValue">10px</span>
                </div>
                
                <div class="control-group">
                    <label>Remarks Font:</label>
                    <input type="range" id="remarksFontSize" min="6" max="14" value="10" step="1" oninput="updateRemarksFont(this.value)">
                    <span id="remarksFontValue">10px</span>
                </div>
                
                <div class="control-group">
                    <label>Attendance/Promotion Font:</label>
                    <input type="range" id="footerFontSize" min="8" max="14" value="10" step="1" oninput="updateFooterFont(this.value)">
                    <span id="footerFontValue">10px</span>
                </div>
                
                <!-- Spacing Controls -->
                <hr style="margin: 10px 0; border-color: #8B4513;">
                
                <div class="control-group">
                    <label>Table Cell Padding:</label>
                    <input type="range" id="cellPadding" min="1" max="8" value="3" step="1" oninput="updateCellPadding(this.value)">
                    <span id="cellPaddingValue">3px</span>
                </div>
                
                <div class="control-group">
                    <label>Logo Size:</label>
                    <input type="range" id="logoSize" min="60" max="120" value="80" step="5" oninput="updateLogoSize(this.value)">
                    <span id="logoSizeValue">80px</span>
                </div>
                
                <div class="control-group">
                    <label>Page Padding:</label>
                    <input type="range" id="pagePadding" min="3" max="12" value="8" step="1" oninput="updatePagePadding(this.value)">
                    <span id="pagePaddingValue">8mm</span>
                </div>
                
                <div class="control-group">
                    <label>Header Spacing:</label>
                    <input type="range" id="headerSpacing" min="3" max="15" value="10" step="1" oninput="updateHeaderSpacing(this.value)">
                    <span id="headerSpacingValue">10px</span>
                </div>
                
                <!-- Action Buttons -->
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button onclick="resetDefaults()" style="flex: 1; padding: 8px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;">Reset to Default</button>
                    <button onclick="saveSettings()" style="flex: 1; padding: 8px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">Save Settings</button>
                </div>
            </div>
        </div>

        <?php foreach($student_details as $sd){ 
            // Initialize entry status for signature display
            $entry_status_1 = 0;
            $entry_status_2 = 0;
            $entry_status_3 = 0;
            
            // Calculate totals for each term
            $term_1_fa_total = 0;
            $term_1_sa_total = 0;
            $term_2_fa_total = 0;
            $term_2_sa_total = 0;
            $term_3_fa_total = 0;
            $term_3_sa_total = 0;
            
            // Count subjects that use marks (not grades) AND are considered for progress report
            $marks_based_subject_count = 0;
            $CI = & get_instance();
            foreach($subjects as $sub){
                $subject_data = $CI->db->get_where('subject', array('sub_id' => $sub->CS_Sub_id))->row();
                if($subject_data && $subject_data->marks_type == 'Marks' && $subject_data->report_status == 'consider'){
                    $marks_based_subject_count++;
                }
            }
        ?>
        
        <section class="sheet padding-5mm section-break">
            <div class="certificate-container">
                <!-- Watermark -->
                <div class="watermark"></div>
                <div class="watermark-logo">
                    <img src="<?=base_url()?>assets/img/favicon.ico" alt="Logo Watermark" />
                </div>
                
                <div class="content-wrapper">
                    <!-- Header -->
                    <div class="header">
                        <div class="school-logo">
                            <img height="70" src="<?=base_url()?>assets/img/favicon.ico" alt="School Logo" />
                        </div>
                        <div class="school-name">
                            <div class="curved-text-container">
                                <svg width="700" height="70" viewBox="0 0 700 70">
                                    <defs>
                                        <path id="curve" d="M 20,60 Q 350,10 680,60" fill="transparent"/>
                                    </defs>
                                    <text>
                                        <textPath href="#curve" startOffset="53%" text-anchor="middle">
                                            ST. ANTHONY'S HIGH SCHOOL
                                        </textPath>
                                    </text>
                                </svg>
                            </div>
                        </div>
                        <div class="school-address" style="color: #8B4513; font-weight:bold">(<?=strtoupper($section_name)?>)</div>
                        <div class="school-type"><?=$company->COM_ADD1 . ', ' . $company->COM_ADD2?></div>
                        <div class="report-title">PROGRESS  REPORT - <?=CURRENT_YEAR?></div>
                        <div class="session-year">CLASS <?=$class?> - <?=$sec?></div>
                    </div>
        
                    <!-- Student Information -->
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Name of the Student</td>
                            <td class="info-value" colspan="3"><strong><?=strtoupper($sd['STD_FNAME'] . ' ' . $sd['STD_MNAME'] . ' ' . $sd['STD_LNAME'])?></strong></td>
                        </tr>
                    </table>
        
                    <table class="info-table info-table-double">
                        <tr>
                            <td class="info-label">Class</td>
                            <td class="info-value"><strong><?=$class?></strong></td>
                            <td class="info-label">Section</td>
                            <td class="info-value"><strong><?=$sec?></strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Roll No.</td>
                            <td class="info-value"><strong><?=$sd['STD_ROLLNO']?></strong></td>
                            <td class="info-label">Reg. No.</td>
                            <td class="info-value"><strong><?=$sd['STD_REGNO']?></strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Parent/Guardian's Name</td>
                            <td class="info-value" colspan="3"><strong><?=strtoupper($sd['STD_FTH_NAME'])?></strong></td>
                        </tr>
                    </table>
        
                    <!-- Academic Progress with Signatures and Remarks - Single Continuous Table -->
                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th rowspan="3" style="width: 15%;">SCHOLASTIC AREA</th>
                                <?php foreach($exam_terms as $et){ ?>
                                    <th colspan="4" class="term-header"><?=strtoupper($et->term_title)?></th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php foreach($exam_terms as $et){ ?>
                                    <th colspan="4" style="font-size: 8px;">SUBJECTS</th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php foreach($exam_terms as $et){ ?>
                                    <th style="width: 3%;">FA<br>(10)</th>
                                    <th style="width: 3%;">SA<br>(90)</th>
                                    <th style="width: 4%;">TOTAL</th>
                                    <th style="width: 4%;">GRADE</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($subjects as $sub){
                                echo '<tr>';
                                echo '<td class="subject-name">'.strtoupper($sub->sub_name).'</td>';
                                
                                $term_iter = 1;
                                foreach($exam_terms as $et){
                                    // Fetch marks_type from database
                                    $CI = & get_instance();
                                    $subject_data = $CI->db->get_where('subject', array('sub_id' => $sub->CS_Sub_id))->row();
                                    $marks_type = $subject_data ? $subject_data->marks_type : 'Marks';
                                    
                                    if($marks_type == 'Grade'){
                                        // For grade-based subjects, show dashes in FA/SA and grade in GRADE column
                                        echo '<td class="marks-cell">-</td>'; // FA
                                        $grade_value = fetch_marks($sd['STD_SEQ'], $cs_seq, 4, $et->et_id, $sub->CS_Sub_id);
                                        echo '<td class="marks-cell">-</td>'; // SA
                                        echo '<td class="marks-cell total-cell"><strong>-</strong></td>'; // TOTAL
                                        echo '<td class="marks-cell"><strong>'.$grade_value.'</strong></td>'; // GRADE
                                    } else {
                                        // For marks-based subjects, show normal marks
                                        $fa_marks = fetch_marks($sd['STD_SEQ'], $cs_seq, 1, $et->et_id, $sub->CS_Sub_id);
                                        $sa_marks = fetch_marks($sd['STD_SEQ'], $cs_seq, 4, $et->et_id, $sub->CS_Sub_id);
                                        
                                        // Display FA marks as-is
                                        echo '<td class="marks-cell">'.$fa_marks.'</td>';
                                        
                                        // Display SA marks as-is
                                        echo '<td class="marks-cell">'.$sa_marks.'</td>';
                                        
                                        // Convert to numeric for calculation
                                        $fa_numeric = ($fa_marks == '-' || $fa_marks == 'Ab') ? 0 : intval($fa_marks);
                                        $sa_numeric = ($sa_marks == '-' || $sa_marks == 'Ab') ? 0 : intval($sa_marks);
                                        $total = $fa_numeric + $sa_numeric;
                                        
                                        // Add to term totals (only for marks-based subjects with report_status='consider')
                                        if($subject_data->report_status == 'consider'){
                                            if($term_iter == 1){
                                                $term_1_fa_total += $fa_numeric;
                                                $term_1_sa_total += $sa_numeric;
                                                if($total > 0) $entry_status_1 = 1;
                                            } else if($term_iter == 2){
                                                $term_2_fa_total += $fa_numeric;
                                                $term_2_sa_total += $sa_numeric;
                                                if($total > 0) $entry_status_2 = 1;
                                            } else if($term_iter == 3){
                                                $term_3_fa_total += $fa_numeric;
                                                $term_3_sa_total += $sa_numeric;
                                                if($total > 0) $entry_status_3 = 1;
                                            }
                                        }
                                        
                                        // Display Total
                                        $total_display = ($total < 10) ? '0'.$total : $total;
                                        echo '<td class="marks-cell total-cell"><strong>'.$total_display.'</strong></td>';
                                        
                                        // Display Grade (even for 0 marks, as 0% = Grade D)
                                        $grade = fetch_grade($total, 100, $class);
                                        echo '<td class="marks-cell"><strong>'.$grade.'</strong></td>';
                                    }
                                    
                                    $term_iter++;
                                }
                                
                                echo '</tr>';
                            }
                            
                            // Calculate grand totals and percentages
                            $term_1_grand_total = $term_1_fa_total + $term_1_sa_total;
                            $term_2_grand_total = $term_2_fa_total + $term_2_sa_total;
                            $term_3_grand_total = $term_3_fa_total + $term_3_sa_total;
                            
                            // Count subjects with marks entered for each term (matching type_2 logic)
                            $term_1_subject_count = 0;
                            $term_2_subject_count = 0;
                            $term_3_subject_count = 0;
                            
                            foreach($subjects as $sub){
                                $subject_data = $CI->db->get_where('subject', array('sub_id' => $sub->CS_Sub_id))->row();
                                if($subject_data && $subject_data->marks_type == 'Marks' && $subject_data->report_status == 'consider'){
                                    // Check if marks entered for term 1
                                    $t1_fa = fetch_marks($sd['STD_SEQ'], $cs_seq, 1, $exam_terms[0]->et_id, $sub->CS_Sub_id);
                                    $t1_sa = fetch_marks($sd['STD_SEQ'], $cs_seq, 4, $exam_terms[0]->et_id, $sub->CS_Sub_id);
                                    $t1_total = (($t1_fa == '-' || $t1_fa == 'Ab') ? 0 : intval($t1_fa)) + (($t1_sa == '-' || $t1_sa == 'Ab') ? 0 : intval($t1_sa));
                                    if($t1_total > 0) $term_1_subject_count++;
                                    
                                    // Check if marks entered for term 2
                                    $t2_fa = fetch_marks($sd['STD_SEQ'], $cs_seq, 1, $exam_terms[1]->et_id, $sub->CS_Sub_id);
                                    $t2_sa = fetch_marks($sd['STD_SEQ'], $cs_seq, 4, $exam_terms[1]->et_id, $sub->CS_Sub_id);
                                    $t2_total = (($t2_fa == '-' || $t2_fa == 'Ab') ? 0 : intval($t2_fa)) + (($t2_sa == '-' || $t2_sa == 'Ab') ? 0 : intval($t2_sa));
                                    if($t2_total > 0) $term_2_subject_count++;
                                    
                                    // Check if marks entered for term 3
                                    $t3_fa = fetch_marks($sd['STD_SEQ'], $cs_seq, 1, $exam_terms[2]->et_id, $sub->CS_Sub_id);
                                    $t3_sa = fetch_marks($sd['STD_SEQ'], $cs_seq, 4, $exam_terms[2]->et_id, $sub->CS_Sub_id);
                                    $t3_total = (($t3_fa == '-' || $t3_fa == 'Ab') ? 0 : intval($t3_fa)) + (($t3_sa == '-' || $t3_sa == 'Ab') ? 0 : intval($t3_sa));
                                    if($t3_total > 0) $term_3_subject_count++;
                                }
                            }
                            
                            $term_1_percentage = ($term_1_subject_count > 0) ? number_format(($term_1_grand_total / ($term_1_subject_count * 100)) * 100, 2) : '0.00';
                            $term_2_percentage = ($term_2_subject_count > 0) ? number_format(($term_2_grand_total / ($term_2_subject_count * 100)) * 100, 2) : '0.00';
                            $term_3_percentage = ($term_3_subject_count > 0) ? number_format(($term_3_grand_total / ($term_3_subject_count * 100)) * 100, 2) : '0.00';
                            ?>
                            
                            <!-- Grand Total Row -->
                            <tr class="total-row">
                                <td class="subject-name">Grand Total Grade</td>
                                <td><?=($term_1_fa_total < 10) ? '0'.$term_1_fa_total : $term_1_fa_total?></td>
                                <td><?=($term_1_sa_total < 100) ? (($term_1_sa_total < 10) ? '0'.$term_1_sa_total : $term_1_sa_total) : $term_1_sa_total?></td>
                                <td class="total-cell"><strong><?=($term_1_grand_total < 100) ? (($term_1_grand_total < 10) ? '0'.$term_1_grand_total : $term_1_grand_total) : $term_1_grand_total?></strong></td>
                                <td><strong><?=$term_1_percentage?></strong></td>
                                
                                <td><?=($term_2_fa_total < 10) ? '0'.$term_2_fa_total : $term_2_fa_total?></td>
                                <td><?=($term_2_sa_total < 100) ? (($term_2_sa_total < 10) ? '0'.$term_2_sa_total : $term_2_sa_total) : $term_2_sa_total?></td>
                                <td class="total-cell"><strong><?=($term_2_grand_total < 100) ? (($term_2_grand_total < 10) ? '0'.$term_2_grand_total : $term_2_grand_total) : $term_2_grand_total?></strong></td>
                                <td><strong><?=$term_2_percentage?></strong></td>
                                
                                <td><?=($term_3_fa_total < 10) ? '0'.$term_3_fa_total : $term_3_fa_total?></td>
                                <td><?=($term_3_sa_total < 100) ? (($term_3_sa_total < 10) ? '0'.$term_3_sa_total : $term_3_sa_total) : $term_3_sa_total?></td>
                                <td class="total-cell"><strong><?=($term_3_grand_total < 100) ? (($term_3_grand_total < 10) ? '0'.$term_3_grand_total : $term_3_grand_total) : $term_3_grand_total?></strong></td>
                                <td><strong><?=$term_3_percentage?></strong></td>
                            </tr>
                            
                            <!-- Attendance Row -->
                            <tr>
                                <td class="subject-name"><strong>Attendance</strong></td>
                                <?php 
                                $term_iter = 1;
                                foreach($exam_terms as $et){ 
                                    $attendance = fetch_attendance($et->et_id, $sd['STD_SEQ']);
                                ?>
                                    <td colspan="3" style="background:transparent;"><strong><?=$attendance?></strong></td>
                                    <td style="background:transparent;"><strong>-</strong></td>
                                <?php 
                                    $term_iter++;
                                } 
                                ?>
                            </tr>
                            
                            <!-- Signatures Section Integrated -->
                            <tr>
                                <td style="text-align: left; padding: 4px; font-weight: bold; background: #FFFAF0; font-size: 10px;">Class Teacher's<br>Signature</td>
                                <?php 
                                $sign_iter = 1;
                                foreach($exam_terms as $et){ 
                                    if(($sign_iter == 1 && $entry_status_1 == 1) || ($sign_iter == 2 && $entry_status_2 == 1) || ($sign_iter == 3 && $entry_status_3 == 1)){
                                        ?>
                                        <td colspan="4" style="text-align: center; padding: 4px; background: transparent; font-size: 10px;">
                                            <img height="35" src="<?=base_url('assets/img/tch_sign/'.$class_teacher_sign)?>" />
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td colspan="4" style="text-align: center; padding: 4px; background: transparent; font-size: 10px;"></td>
                                        <?php
                                    }
                                    $sign_iter++;
                                } 
                                ?>
                            </tr>
                            <tr>
                                <td style="text-align: left; padding: 4px; font-weight: bold; background: #FFFAF0; font-size: 10px;">Head master's<br>Signature</td>
                                <td colspan="12" style="text-align: center; padding: 4px; background: transparent; font-size: 10px;">
                                    <?php if($company->HEADMASTER_SIGN){ ?>
                                        <img height="35" src="<?=base_url('assets/img/'.$company->HEADMASTER_SIGN)?>" alt="Headmaster Signature" />
                                    <?php } else { ?>
                                        <div style="font-style: italic; font-family: 'Brush Script MT', cursive;">Signature</div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left; padding: 4px; font-weight: bold; background: #FFFAF0; font-size: 10px;">Parent's / Guardian's<br>Signature</td>
                                <td colspan="12" style="text-align: center; padding: 4px; height: 25px; background: transparent;"></td>
                            </tr>
                            
                            <!-- Remarks Section Integrated -->
                            <?php foreach($exam_terms as $et){ ?>
                                <tr>
                                    <td style="text-align: left; padding: 4px; font-weight: bold; background: #FFFAF0; font-size: 10px;">General Remarks<br>(<?=$et->term_title?>)</td>
                                    <td colspan="12" style="text-align: left; padding: 5px; line-height: 1.3; background: transparent; font-size: 10px;">
                                        <?=fetch_general_remarks($et->et_id, $sd['STD_SEQ'])?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
        
                    <!-- Promoted Section -->
                    <div style="text-align: center; font-weight: bold; font-size: 11px; margin: 5px 0; padding: 5px; border: 2px solid #8B4513; background: #F5DEB3;">
                        <?php
                        // Calculate overall percentage from final term
                        $overall_percentage = $term_3_percentage;
                        
                        // Roman numeral conversion
                        $roman_to_int = ['i'=>1, 'ii'=>2, 'iii'=>3, 'iv'=>4, 'v'=>5, 'vi'=>6, 'vii'=>7, 'viii'=>8, 'ix'=>9, 'x'=>10, 'xi'=>11, 'xii'=>12];
                        $int_to_roman = array_flip($roman_to_int);
                        $class_lower = strtolower($class);
                        $next_class = isset($roman_to_int[$class_lower]) ? $int_to_roman[$roman_to_int[$class_lower] + 1] : $class;
                        
                        if ($overall_percentage > 40) {
                            echo 'Promoted to class ' . strtoupper($next_class);
                        } elseif ($overall_percentage >= 20 && $overall_percentage <= 39) {
                            echo 'Promoted on trial';
                        } elseif ($overall_percentage >= 2 && $overall_percentage <= 19) {
                            echo 'Promoted on warning';
                        } else {
                            echo 'Not Promoted';
                        }
                        ?>
                    </div>                    
                    
                    <!-- Note -->
                    <div class="note-section" style="font-size: 9px; margin-top: 5px;">
                        <!--Date: <?=date('d/m/Y')?>-->
                    </div>
                </div>
            </div>
        </section>
        
        <?php } ?>
        
        <script>
        // Toggle control panel visibility
        function toggleControls(){const content=document.getElementById('controlsContent');content.style.display=content.style.display==='none'?'block':'none';}
        
        // Update School Name Font Size
        function updateSchoolNameFont(value){
            document.getElementById('schoolNameValue').textContent=value+'px';
            document.querySelectorAll('.curved-text-container text').forEach(el=>{
                el.style.fontSize=value+'px';
            });
        }
        
        // Update Section Name Font Size
        function updateSectionNameFont(value){
            document.getElementById('sectionNameValue').textContent=value+'px';
            document.querySelectorAll('.school-address').forEach(el=>{
                el.style.fontSize=value+'px';
            });
        }
        
        // Update Report Title Font Size
        function updateReportTitleFont(value){
            document.getElementById('reportTitleValue').textContent=value+'px';
            document.querySelectorAll('.report-title').forEach(el=>{
                el.style.fontSize=value+'px';
            });
        }
        
        // Update Session Year Font Size
        function updateSessionYearFont(value){
            document.getElementById('sessionYearValue').textContent=value+'px';
            document.querySelectorAll('.session-year').forEach(el=>{
                el.style.fontSize=value+'px';
            });
        }
        
        // Update Student Info Font
        function updateInfoFont(value){document.getElementById('infoFontValue').textContent=value+'px';document.querySelectorAll('.info-table').forEach(table=>{table.style.fontSize=value+'px';});}
        
        // Update Marks Table Font
        function updateMarksFont(value){document.getElementById('marksFontValue').textContent=value+'px';document.querySelectorAll('.marks-table').forEach(table=>{table.style.fontSize=value+'px';});}
        
        // Update Remarks Font
        function updateRemarksFont(value){document.getElementById('remarksFontValue').textContent=value+'px';document.querySelectorAll('.marks-table tbody tr').forEach((row)=>{const cells=row.querySelectorAll('td');if(cells.length>0){const firstCell=cells[0];if(firstCell.textContent.includes('Class Teacher')||firstCell.textContent.includes('Head master')||firstCell.textContent.includes('Parent')||firstCell.textContent.includes('General Remarks')){cells.forEach(td=>{td.style.fontSize=value+'px';});}}});}
        
        // Update Footer Font (Attendance/Promotion)
        function updateFooterFont(value){document.getElementById('footerFontValue').textContent=value+'px';document.querySelectorAll('.marks-table tbody tr').forEach((row)=>{const cells=row.querySelectorAll('td');if(cells.length>0){const firstCell=cells[0];if(firstCell.textContent.includes('Attendance')){cells.forEach(td=>{td.style.fontSize=value+'px';});}}});document.querySelectorAll('.certificate-container>div>div').forEach(el=>{if(el.textContent.includes('Promoted')||el.textContent.includes('Date:')){el.style.fontSize=value+'px';}});}
        
        // Update Cell Padding
        function updateCellPadding(value){document.getElementById('cellPaddingValue').textContent=value+'px';document.querySelectorAll('.marks-table td, .marks-table th').forEach(cell=>{cell.style.padding=value+'px';});document.querySelectorAll('.info-table td').forEach(cell=>{cell.style.padding=value+'px';});}
        
        // Update Logo Size
        function updateLogoSize(value){document.getElementById('logoSizeValue').textContent=value+'px';const logos=document.querySelectorAll('.school-logo');logos.forEach(logo=>{logo.style.width=value+'px';logo.style.height=value+'px';logo.style.fontSize=(value*0.125)+'px';});}
        
        // Update Page Padding
        function updatePagePadding(value){document.getElementById('pagePaddingValue').textContent=value+'mm';document.querySelectorAll('.certificate-container').forEach(container=>{container.style.padding=value+'mm';});}
        
        // Update Header Spacing
        function updateHeaderSpacing(value){document.getElementById('headerSpacingValue').textContent=value+'px';document.querySelectorAll('.header').forEach(header=>{header.style.marginBottom=value+'px';});document.querySelectorAll('.info-table').forEach(table=>{table.style.marginBottom=value+'px';});}
        
        // Reset to Default Values
        function resetDefaults(){
            document.getElementById('schoolNameFont').value=24;updateSchoolNameFont(24);
            document.getElementById('sectionNameFont').value=12;updateSectionNameFont(12);
            document.getElementById('reportTitleFont').value=26;updateReportTitleFont(26);
            document.getElementById('sessionYearFont').value=16;updateSessionYearFont(16);
            document.getElementById('infoFontSize').value=13;updateInfoFont(13);
            document.getElementById('marksFontSize').value=10;updateMarksFont(10);
            document.getElementById('remarksFontSize').value=10;updateRemarksFont(10);
            document.getElementById('footerFontSize').value=10;updateFooterFont(10);
            document.getElementById('cellPadding').value=3;updateCellPadding(3);
            document.getElementById('logoSize').value=80;updateLogoSize(80);
            document.getElementById('pagePadding').value=8;updatePagePadding(8);
            document.getElementById('headerSpacing').value=10;updateHeaderSpacing(10);
            localStorage.removeItem('progressReportSettings');
            alert('Settings reset to default values!');
        }
        
        // Save Settings to LocalStorage
        function saveSettings(){
            const settings={
                schoolNameFont:document.getElementById('schoolNameFont').value,
                sectionNameFont:document.getElementById('sectionNameFont').value,
                reportTitleFont:document.getElementById('reportTitleFont').value,
                sessionYearFont:document.getElementById('sessionYearFont').value,
                infoFontSize:document.getElementById('infoFontSize').value,
                marksFontSize:document.getElementById('marksFontSize').value,
                remarksFontSize:document.getElementById('remarksFontSize').value,
                footerFontSize:document.getElementById('footerFontSize').value,
                cellPadding:document.getElementById('cellPadding').value,
                logoSize:document.getElementById('logoSize').value,
                pagePadding:document.getElementById('pagePadding').value,
                headerSpacing:document.getElementById('headerSpacing').value
            };
            localStorage.setItem('progressReportSettings',JSON.stringify(settings));
            alert('Settings saved successfully!');
        }
        
        // Load Settings from LocalStorage
        function loadSettings(){
            const saved=localStorage.getItem('progressReportSettings');
            if(saved){
                const settings=JSON.parse(saved);
                document.getElementById('schoolNameFont').value=settings.schoolNameFont;
                updateSchoolNameFont(settings.schoolNameFont);
                document.getElementById('sectionNameFont').value=settings.sectionNameFont;
                updateSectionNameFont(settings.sectionNameFont);
                document.getElementById('reportTitleFont').value=settings.reportTitleFont;
                updateReportTitleFont(settings.reportTitleFont);
                document.getElementById('sessionYearFont').value=settings.sessionYearFont;
                updateSessionYearFont(settings.sessionYearFont);
                document.getElementById('infoFontSize').value=settings.infoFontSize;
                updateInfoFont(settings.infoFontSize);
                document.getElementById('marksFontSize').value=settings.marksFontSize;
                updateMarksFont(settings.marksFontSize);
                document.getElementById('remarksFontSize').value=settings.remarksFontSize;
                updateRemarksFont(settings.remarksFontSize);
                if(settings.footerFontSize){
                    document.getElementById('footerFontSize').value=settings.footerFontSize;
                    updateFooterFont(settings.footerFontSize);
                }
                document.getElementById('cellPadding').value=settings.cellPadding;
                updateCellPadding(settings.cellPadding);
                document.getElementById('logoSize').value=settings.logoSize;
                updateLogoSize(settings.logoSize);
                document.getElementById('pagePadding').value=settings.pagePadding;
                updatePagePadding(settings.pagePadding);
                document.getElementById('headerSpacing').value=settings.headerSpacing;
                updateHeaderSpacing(settings.headerSpacing);
            }
        }
        
        // Load saved settings on page load
        window.addEventListener('DOMContentLoaded',loadSettings);
        </script>
    </body>
    <?php
}
?>

</html>