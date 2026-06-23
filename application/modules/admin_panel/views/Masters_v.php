<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 16-03-2019
 * Time: 18:16
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
            if ($section == 'copy_subjects') { //copy subjects form
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                Copy Subjects
                            </header>
                            <div class="panel-body">
                                <form action="<?=base_url();?>admin/form_copy_subjects" method="post" class="cmxform form-horizontal tasi-form" >
                                        <div class="form-group">
                                            <label for="from_class" class="control-label col-lg-2 text-danger">Copy From *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="from_class" name="from_class" required >
                                                    <option value="">Select a class</option>
                                                    <?php
                                                    foreach($from_cls as $val){
                                                        ?>
                                                        <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="to_class" class="control-label col-lg-2 text-danger">Copy To *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select multiple class="form-control" id="to_class" name="to_class[]" required >
                                                    <?php
                                                    foreach($to_cls as $val){
                                                        ?>
                                                        <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="submit_copy_subjects_form">Copy <i class="fa fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </section>
                    </div>
                </div>
                <?php
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

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->

</body>
</html>