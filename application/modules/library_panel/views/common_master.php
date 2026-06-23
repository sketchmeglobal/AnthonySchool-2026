<?php // echo '<pre>', print_r($all_account_groups), '</pre>'; die; ?>
 
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=$menu_name?></title>
   <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->
    
  <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">-->
  <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="sticky-header">

    <section>
        <!-- sidebar left start (Menu)-->
        <?php $this->load->view('components/left_sidebar'); //left side menu ?>
        <!-- sidebar left end (Menu)-->
    
        <!-- body content start-->
        <div class="body-content" style="min-height: 1500px;">
            
             <!-- page head start-->
            <div class="page-head">
                <h3 class="m-b-less">
                    <?=$menu_name?>
                </h3>
                <!--<span class="sub-title">Welcome to Static Table</span>-->
                <div class="state-information">
                    <ol class="breadcrumb m-b-less bg-less">
                        <li><a href="<?=base_url('library/dashboard');?>">Home</a></li>
                        <li class="active"> <?=$menu_name?> </li>
                    </ol>
                </div>
            </div>
            <!-- page head end-->
    
            <!-- header section start-->
            <?php $this->load->view('components/top_menu'); ?>
            
            <!--body wrapper start-->
            <div class="wrapper" style="padding:0px;">
                <div class="container-fluid">
                    <div class="col-lg-12">
                        <section class="panel">
                                <?php
                            if($menu_name == 'Books'){
                                ?>
                                <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>library_panel/Masters/books_edit/add" class="btn btn-primary">Add</a>
                                <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Accession No.</th>
                                        <th>Subject Name</th>
                                        <th>Book Year</th>
                                        <th>Author</th>
                                        <th>Total Copies</th>
                                        <th>Available Copies</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $iter=1;
                                    foreach($all_books as $op){
                                    ?>
                                        <tr>
                                            <td><?=$iter++?></td>
                                            <td><?= $op->Accession_No ?></td>
                                            <td><?= $op->sub_name ?></td>
                                            <td><?= $op->Book_Name ?></td>
                                            <td><?= $op->Author ?></td>
                                            <td><?= $op->Total_Copies ?></td>
                                            <td><?= $op->Available_Copies ?></td>
                                            <td nowrap>
                                                <a href="<?=base_url()?>library_panel/Masters/books_edit/edit/<?=$op->BOOK_SEQ?>" class="btn btn-warning">Edit</a>
                                                <!--<a href="" class="btn btn-danger">Delete</a>-->
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                            }
                            else if($menu_name == 'class_subject'){
                                ?>
                                <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>library/class_subject/add" class="btn btn-primary">Add</a>
                                <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Class </th>
                                        <th>Subject</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $iter=1;
                                    foreach($all_subjects as $op){
                                    ?>
                                        <tr>
                                            <td><?=$iter++?></td>
                                            <td>(<?= $op->Class_Name ?> - <?= $op->Sec_Name ?>)</td>
                                            <td><?= $op->sub_name ?></td>
                                            <td nowrap>
                                                <a href="<?=base_url('library_panel/Masters/class_subject_edit/'.$op->CS_SUB_LINK_SEQ)?>" class="btn btn-info" role="button"><span class="ui-button-text">&nbsp;Edit</span></a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                                <?php
                            }
                            else if($menu_name == 'Library'){
                                ?>
                                <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>library/add_library_tran" class="btn btn-primary">Add</a>
                                <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Class-section </th>
                                        <th>Issue date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $iter=1;
                                    foreach($all_library as $op){
                                    ?>
                                        <tr>
                                            <td><?=$iter++?></td>
                                            <td><?= ($op->Class_Name . '-' . $op->Sec_Name) ?></td>
                                            <td><?= $op->date_issue ?></td>
                                            <td nowrap>
                                                <a href="<?=base_url('library/edit_library_tran/'.$op->lb_hdr_id)?>" class="btn btn-info" role="button"><span class="ui-button-text">&nbsp;Edit</span></a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                                <?php
                            }
                            ?>
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
      
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<!--common scripts for all pages-->
<script src="<?=base_url();?>assets/admin_panel/js/bootstrap.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/modernizr.min.js"></script>

<script src="<?=base_url();?>assets/admin_panel/js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/toastr-master/toastr.js"></script>
<script>

    $(document).ready(function () {
        $('table').DataTable();
    });
</script>

<?php
//notification
if($this->session->flashdata('msg')) {
    $notification_type = $this->session->flashdata('type') ? $this->session->flashdata('type') : "warning";
    ?>
    <script>
        toastr["<?=$notification_type;?>"]("<?=$this->session->flashdata('msg');?>", "<?=$this->session->flashdata('title');?>", {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "15000",
            "extendedTimeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        })
    </script>
    <?php
}
?>
</body>
</html>
 
