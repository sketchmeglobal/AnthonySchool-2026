<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 18-06-2019
 * Time: 15:17
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->
</head>

<body class="sticky-header">

<section>
    <!-- sidebar left start (Menu)-->
    <?php $this->load->view('components/left_sidebar'); //left side menu ?>
    <!-- sidebar left end (Menu)-->

    <!-- body content start-->
    <div class="body-content" style="min-height: 1500px;">

        <!-- header section start-->
        <?php $this->load->view('components/top_menu'); ?>
        <!-- header section end-->

        <!-- page head start-->
        <div class="page-head">
            <h3 class="m-b-less">
                <?=$menu_name;?>
            </h3>
            <!--<span class="sub-title">Welcome to Static Table</span>-->
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                    <li class="active"> <?=$menu_name;?> </li>
                </ol>
            </div>
        </div>
        <!-- page head end-->

        <!--body wrapper start-->
        <div class="wrapper">

            <?php
            if ($section == 'student_homework_details') {
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <!--question area-->
                    <section class="panel">
                        <header class="panel-heading">
                            Question / Note Details
                        </header>
                        <div class="panel-body">
                            <section class="cmxform form-horizontal tasi-form">
                                <div class="form-group">
                                    <label class="control-label col-sm-2"><h3><strong>Subject</strong></h3></label>
                                    <div class="col-sm-4">
                                        <h3><?=$homework->sub_name?></h3>
                                    </div>

                                    <label class="control-label col-sm-2"><h3><strong>Publish Date</strong></h3></label>
                                    <div class="col-sm-4">
                                        <h3><?=date('d-m-Y', strtotime($homework->release_date))?></h3>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2"><h3><strong>Homework</strong></h3></label>
                                    <div class="col-sm-10">
                                        <?=$homework->homework?>
                                    </div>
                                </div>

                                <?php
                                if($homework->doc != '') {
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2"><h3><strong>Document</strong></h3></label>
                                        <div class="col-sm-10">
                                            <h3><a href="<?=base_url('assets/admin_panel/homework_files/'.$homework->doc)?>" target="_blank">Click here to View</a></h3>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </section>
                        </div>
                    </section>

                    <!--answer area-->
                    <section class="panel">
                        <header class="panel-heading">
                            Answer Details
                        </header>
                        <div class="panel-body">
                            <section class="cmxform form-horizontal tasi-form">
                                <?php
                                $ans_date = date('Y-m-d', strtotime($homework->ans_date));
                                $today = date('Y-m-d');
                                //if ans publish date passed-by
                                if($ans_date <= $today) {
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-sm-2"><h3><strong>Answer</strong></h3></label>
                                        <div class="col-sm-10">
                                            <?= $homework->answer ?>
                                        </div>
                                    </div>

                                    <?php
                                    if ($homework->ans_doc != '') {
                                        ?>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"><h3><strong>Document</strong></h3>
                                            </label>
                                            <div class="col-sm-10">
                                                <h3>
                                                    <a href="<?= base_url('assets/admin_panel/homework_files/' . $homework->ans_doc) ?>"
                                                       target="_blank">Click here to View</a></h3>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                //if ans publish date not came yet
                                else {
                                    ?>
                                    <div class="text-center">
                                        <h3>Answer will be published on <?=date('d-m-Y', strtotime($homework->ans_date))?></h3>
                                    </div>
                                    <?php
                                }
                                ?>
                            </section>
                        </div>
                    </section>
                </div>
            </div>
            <?php
            }

            if ($section == 'student_exam') {
                $class_id = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;

                //check if any exam scheduled today
                $this->db->join('subject', 'subject.sub_id = exam_timings.sub_id', 'left');
                $this->db->where('class_id', $class_id);
                $this->db->where('DATE(start_date_time)', date('Y-m-d'));
                $this->db->where('TIME(end_date_time) >=', date('H:i:s'));
                $this->db->order_by('start_date_time');
                $exam_row = $this->db->get('exam_timings')->row();

                //if today is exam date AND all exam not completed
                if(count((array)$exam_row) > 0) {
                    //if exam is not started yet
                    if(date('H:i:s', strtotime($exam_row->start_date_time)) > date('H:i:s')) {
                        $exam_status = 'not_started';
                        echo '<h1><strong>'.$exam_row->sub_name.' exam will start at '.date('H:i:s', strtotime($exam_row->start_date_time)).'</strong></h1>';
                    }
                    //if exam is started
                    else{
                        $exam_status = 'ongoing';

                        //Descriptive Exam
                        if($exam_row->exam_type == 'Descriptive') {
                            ?>
                            <div class="row">
                                <div class="col-md-8" style="color: #0000FF">
                                    <strong>Notes:</strong>
                                    <br/>
                                    1. You can save your answer anytime during the exam, this will not end the exam.
                                    <br/>
                                    2. Exam will automatically end when timer runs out, so save your answers before
                                    timer countdown ends.
                                    <br/>
                                    3. System will auto-save your answers, when less than 30 seconds remain.
                                </div>
                                <div class="col-md-4" style="color: red">
                                    <div class="text-right">Exam will end in: <span style="font-size: 30px"
                                                                                    id="countdown"></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $this->db->where('qs_id', $exam_row->qs_id);
                                    $ques_row = $this->db->get('question_sets')->row();
                                    ?>
                                    <h1 class="text-center">Questions</h1>
                                    <br/>
                                    <?= $ques_row->questions ?>
                                </div>
                                <div class="col-md-6">
                                    <h1 class="text-center">Answers</h1>
                                    <br/>
                                    <?php
                                    //fetch answers
                                    $this->db->where('EXAM_SEQ', $exam_row->EXAM_SEQ);
                                    $this->db->where('qs_id', $exam_row->qs_id);
                                    $this->db->where('STD_SEQ', $this->session->tbl_id);
                                    $ans_row = $this->db->get('exam_answers')->row();
                                    if (count((array)$ans_row) > 0) {
                                        $saved_ans = $ans_row->answers;
                                    } else {
                                        $saved_ans = '';
                                    }
                                    ?>
                                    <form id="exam_answer_form" method="post" action="exam_answer_save">
                                        <input type="hidden" name="exam_seq" value="<?= $exam_row->EXAM_SEQ ?>">
                                        <input type="hidden" name="qs_id" value="<?= $exam_row->qs_id ?>">
                                        <textarea id="editor1" name="answers"><?= $saved_ans ?></textarea>
                                        <br/>
                                        <button type="submit" class="btn btn-success btn-block"><i
                                                    class="fa fa-save"></i> Save
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }

                        //MCQ Exam
                        elseif($exam_row->exam_type == 'MCQ') {
                            ?>
                            <div class="row">
                                <div class="col-md-8" style="color: #0000FF">
                                    <strong>Notes:</strong>
                                    <br/>
                                    1. You can save your answer anytime during the exam, this will not end the exam.
                                    <br/>
                                    2. Exam will automatically end when timer runs out, so save your answers before
                                    timer countdown ends.
                                    <br/>
                                    3. System will auto-save your answers, when less than 30 seconds remain.
                                </div>
                                <div class="col-md-4" style="color: red">
                                    <div class="text-right">Exam will end in: <span style="font-size: 30px"
                                                                                    id="countdown"></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h1 class="text-center">Questions</h1>
                                    <br/>
                                    <?php
                                    //fetch questions
                                    $this->db->where('mcq_qs_id', $exam_row->mcq_qs_id);
                                    $this->db->order_by('priority', 'ASC');
                                    $ques_rs = $this->db->get('mcq_questions')->result();

                                    //fetch answers
                                    $this->db->where('EXAM_SEQ', $exam_row->EXAM_SEQ);
                                    $this->db->where('mcq_qs_id', $exam_row->mcq_qs_id);
                                    $this->db->where('STD_SEQ', $this->session->tbl_id);
                                    $ans_rs = $this->db->get('mcq_exam_answers')->result_array();
                                    $ans_arr = array();
                                    if (count($ans_rs) > 0) {
                                        $ans_arr = array_column($ans_rs, 'option_selected', 'mcq_q_id');
                                    }
                                    ?>
                                    <form id="mcq_exam_answer_form" method="post" action="mcq_exam_answer_save">
                                        <input type="hidden" name="exam_seq" value="<?= $exam_row->EXAM_SEQ ?>">
                                        <input type="hidden" name="mcq_qs_id" value="<?= $exam_row->mcq_qs_id ?>">

                                        <?php
                                        //print all questions
                                        $no = 1;
                                        foreach ($ques_rs as $ques) {
                                            $selection = NULL;
                                            if(array_key_exists($ques->mcq_q_id, $ans_arr)) {
                                                $selection = $ans_arr[$ques->mcq_q_id];
                                            }

                                            echo '<strong>('.$no.') '.$ques->question.'</strong><br/>';
                                            ?>
                                            <input type="hidden" name="ques[]" value="<?=$ques->mcq_q_id?>">
                                            <input type="radio" name="ans[<?=$ques->mcq_q_id?>]" value="1" id="<?=$ques->mcq_q_id?>_option1" <?php if($selection=='1'){echo 'checked';} ?> >
                                            <label for="<?=$ques->mcq_q_id?>_option1"><?=$ques->option1?></label><br>
                                            <input type="radio" name="ans[<?=$ques->mcq_q_id?>]" value="2" id="<?=$ques->mcq_q_id?>_option2" <?php if($selection=='2'){echo 'checked';} ?> >
                                            <label for="<?=$ques->mcq_q_id?>_option2"><?=$ques->option2?></label><br>
                                            <?php
                                            if($ques->option3){
                                                ?>
                                                <input type="radio" name="ans[<?=$ques->mcq_q_id?>]" value="3" id="<?=$ques->mcq_q_id?>_option3" <?php if($selection=='3'){echo 'checked';} ?> >
                                                <label for="<?=$ques->mcq_q_id?>_option3"><?=$ques->option3?></label><br>
                                                <?php
                                            }
                                            if($ques->option4){
                                                ?>
                                                <input type="radio" name="ans[<?=$ques->mcq_q_id?>]" value="4" id="<?=$ques->mcq_q_id?>_option4" <?php if($selection=='4'){echo 'checked';} ?> >
                                                <label for="<?=$ques->mcq_q_id?>_option4"><?=$ques->option4?></label><br>
                                                <?php
                                            }
                                            if($ques->option5){
                                                ?>
                                                <input type="radio" name="ans[<?=$ques->mcq_q_id?>]" value="5" id="<?=$ques->mcq_q_id?>_option5" <?php if($selection=='5'){echo 'checked';} ?> >
                                                <label for="<?=$ques->mcq_q_id?>_option5"><?=$ques->option5?></label><br>
                                                <?php
                                            }
                                            ?>
                                            <br/>
                                            <?php
                                            $no++;
                                        }
                                        ?>

                                        <br/>
                                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i> Save</button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }

                    }
                }
                //if no exam today OR all exam completed
                else{
                    $exam_status = 'no_exam';
                    echo '<h1 class="text-center"><strong>No exam(s) today!</strong></h1>';
                }
            }
            ?>

        </div>
        <!--body wrapper end-->


        <!--footer section start-->
        <?php $this->load->view('components/footer'); ?>
        <!--footer section end-->

    </div>
    <!-- body content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->

<!--ckeditor-->
<script src="<?=base_url();?>assets/grocery_crud/texteditor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor1', {
        height: '300',
        toolbar :
            [
                { name: 'basicstyles', items : [ 'Bold','Italic','RemoveFormat' ] },
                { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
                { name: 'tools', items : [ 'Maximize' ] }
            ],
    });
</script>

<!--ajax form submit-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.form.min.js"></script>

<?php
//exam related scripts
if($exam_status = 'ongoing') {
    ?>
    <script>
        //submit answer
        $('#exam_answer_form').ajaxForm({
            beforeSerialize: function () {
                var data = CKEDITOR.instances.editor1.getData();
                $("#editor1").val(data);
            },

            success: function (returnData) {
                obj = JSON.parse(returnData);
                notification(obj);
            }
        });

        //mcq submit answer
        $('#mcq_exam_answer_form').ajaxForm({
            success: function (returnData) {
                obj = JSON.parse(returnData);
                notification(obj);
            }
        });

        //exam timer
        <?php
        $end_time = strtotime($exam_row->end_date_time);
        $now = strtotime(date('Y-m-d H:i:s'));
        $differenceInSeconds = $end_time - $now;
        ?>
        var seconds = <?=$differenceInSeconds?>;

        function timer() {
            var days = Math.floor(seconds / 24 / 60 / 60);
            var hoursLeft = Math.floor((seconds) - (days * 86400));
            var hours = Math.floor(hoursLeft / 3600);
            var minutesLeft = Math.floor((hoursLeft) - (hours * 3600));
            var minutes = Math.floor(minutesLeft / 60);
            var remainingSeconds = seconds % 60;

            function pad(n) {
                return (n < 10 ? "0" + n : n);
            }

            document.getElementById('countdown').innerHTML = pad(hours) + ":" + pad(minutes) + ":" + pad(remainingSeconds);

            //save answers before ending exam
            if (seconds == <?=rand(10, 30)?>) {
                <?php if($exam_row->exam_type == 'Descriptive') { ?>
                $("#exam_answer_form").submit();
                <?php } elseif($exam_row->exam_type == 'MCQ') { ?>
                $("#mcq_exam_answer_form").submit();
                <?php } ?>
            }
            if (seconds == 0) {
                // clearInterval(countdownTimer);
                // document.getElementById('countdown').innerHTML = "Completed";

                //refresh page
                location.reload();
            } else {
                seconds--;
            }
        }

        var countdownTimer = setInterval('timer()', 1000);
    </script>
    <?php
}
?>

</body>
</html>
 