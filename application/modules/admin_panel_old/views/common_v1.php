 
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>New Table Structure</title>
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
                        <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                        <li class="active"> <?=$menu_name?> </li>
                    </ol>
                </div>
            </div>
            <!-- page head end-->
    
            <!-- header section start-->
            <?php $this->load->view('components/top_menu'); ?>
            
            <!--body wrapper start-->
            <div class="wrapper">
                <div class="container-fluid">
                    <div class="col-lg-12">
                        <section class="panel">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Class-Section</th>
                                        <th>Student Name</th>
                                        <th>Father Name</th>
                                        <th>Mother Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $iter=1;
                                    foreach($outputs as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->myclass?></td>
                                        <td><?=$op->ST_FULL_NAME?></td>
                                        <td><?=$op->STD_FTH_NAME?></td>
                                        <td><?=$op->STD_MTH_NAME?></td>
                                        <td>
                                            <a href="<?=base_url()?>admin/student_parent_details/edit/<?=$op->STD_P_SEQ?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
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
    
</body>            
            
            
            
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<!--common scripts for all pages-->
<script src="<?=base_url();?>assets/admin_panel/js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('table').DataTable();
    });
</script>
</body>
</html>
 
