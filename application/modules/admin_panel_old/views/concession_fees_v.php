<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-01-2019
 * Time: 12:52
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

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <?=$section_heading;?>
                        </header>
                        <div class="panel-body">
                            <?php
                            if ($form_type == 'add') { //add form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="" method="post"
                                      action="<?= base_url(); ?>admin/form_add_concession_fees">

                                    <?php
                                    $count = 1;
                                    foreach ($all_fees as $fee) {
                                        if(!$fee['SHOW_ON_CONCESSION']){
                                            continue;
                                        }
                                        ?>
                                        <div class="form-group">
                                            <input value="<?= $fee['ACC_MASTER_CODE']; ?>" id="fees_id_<?= $count; ?>"
                                                   name="fees_id_<?= $count; ?>" type="hidden"/>

                                            <label for="fee_<?= $count; ?>"
                                                   class="control-label col-lg-2 <?=($fee['ACC_MASTER_NAME'] === "FINE")?'':'text-danger'?> "><?= $fee['ACC_MASTER_NAME']; ?>
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="" class="form-control round-input" id="fee_<?= $count; ?>"
                                                       name="fee_<?= $count; ?>" type="number"
                                                       placeholder="Actual fee is: <?= $fee['Fees']; ?>" <?=($fee['ACC_MASTER_NAME'] === "FINE")?'':'required'?>
                                                       min="0" />
                                            </div>
                                        </div>
                                        <?php
                                        $count++;
                                    }
                                    ?>

                                    <input value="<?= $std_id; ?>" id="std_id" name="std_id" type="hidden"/>
                                    <input value="<?= $class_id; ?>" id="class_id" name="class_id" type="hidden"/>
                                    <input value="<?= $total_row; ?>" id="total_row" name="total_row" type="hidden"/>
                                    <input value="<?= $success_val; ?>" id="success_val" name="success_val" type="hidden"/>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_concession_fees">Add <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }

                            elseif ($form_type == 'edit') { //edit form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="" method="post" action="<?= base_url(); ?>admin/form_edit_concession_fees">

                                    <?php
                                    $count = 1;
                                    foreach ($all_fees as $fee) {
                                        ?>
                                        <div class="form-group <?=($fee['ACC_MASTER_NAME'] == "Tution Fees")?'':'hidden'?>">
                                            <input value="<?= $fee['fc_id']; ?>" id="fc_id_<?= $count; ?>" name="fc_id_<?= $count; ?>" type="hidden"/>

                                            <label for="fee_<?= $count; ?>" class="control-label col-lg-2 <?=($fee['ACC_MASTER_NAME'] === "FINE")?'':'text-danger'?> "><?= $fee['ACC_MASTER_NAME']; ?> *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="<?= $fee['Fees']; ?>" class="form-control round-input" id="fee_<?= $count; ?>"
                                                       name="fee_<?= $count; ?>" type="number" placeholder="Actual fee is: <?= $fee['actual_fees']; ?>" <?=($fee['ACC_MASTER_NAME'] === "FINE")?'':'required'?>  min="0"/>
                                            </div>
                                        </div>
                                        <?php
                                        $count++;
                                    }
                                    ?>

                                    <input value="<?= $total_row; ?>" id="total_row" name="total_row" type="hidden"/>
                                    <input value="<?= $std_id; ?>" id="std_id_edit" name="std_id_edit" type="hidden"/>
                                    <input value="<?= $success_val; ?>" id="success_val_edit" name="success_val_edit" type="hidden"/>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="update_concession_fees">Update <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            ?>
                        </div>
                    </section>
                </div>
            </div>

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

</body>
</html>
