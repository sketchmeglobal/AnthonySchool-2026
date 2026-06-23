<?php  ?>

<?php
/**
 * Coded by: Pritam Khan
 * CI: 3.0.6
 * Date: 20-09-2022
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">

    <!-- common head -->
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/keytable/2.7.0/css/keyTable.dataTables.min.css">
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->

    <style type="text/css">
        table.dataTable td.focus {
        outline: 1px solid #ac1212;
        outline-offset: -3px;
        background-color: #f8e6e6 !important;
    }
    </style>

    <!-- Start grocerycrud JS & STYLES -->
    <?php
    if(!empty($output)){
    foreach($css_files as $file):
        ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
    <?php
    endforeach;
    foreach($js_files as $file):
    ?>
        <script src="<?php echo $file; ?>"></script>
        <?php
    endforeach;
    }
    ?>
    <!--  End grocerycrud JS & STYLES  -->
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
        <div class="wrapper" style="padding:0px !important;">

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <?php if (!empty($section_heading)) {?>
                        <header class="panel-heading">
                            <?=$section_heading;?>
                        </header>
                        <?php } ?>
                        <div class="panel-body">
                            <?php echo $add_button; ?>
                            <?php echo $output; ?>                            
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
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-1.10.2.min.js"></script>-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->
<?php
//open print page
if(isset($print)){
    ?>
    <script>window.open("<?=base_url($print);?>");</script>
    <?php
}
?>
<!-- common js -->
<!-- <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script> -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->



<div id="myModal" class="modal fade" data-backdrop="static" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Student List</h4>
      </div>
      <div class="modal-body">
          <table class="table table-bordered" id="dttbl">
    <thead>
    	
      <tr>
        <th>Student Name</th>
        <th>Class / Sec</th>
        <th>Reg.no</th>
        <th>Action</th>
      </tr>

    </thead>
    <tbody>
    	<?php foreach ($st_array as $index => $st_value) {  
    		$st_name = $st_value->STD_FNAME.' '.$st_value->STD_MNAME.' '.$st_value->STD_LNAME;
    		$st_class_sec =  $st_value->Class_Name.' - '.$st_value->Sec_Name;
    		?>
      
        <tr>
            
            <td><?=$st_name?></td>
            <td><?=$st_class_sec?></td>
            <td><?=$st_value->STD_REGNO?></td>
            <td>
            	<a href="<?=$url.'/'.$st_value->STD_SEQ?>" target="_blank" id="ss"><img src="<?=base_url('assets/grocery_crud/themes/flexigrid/css/images/next.gif')?>" alt="Proceed"></a>
            </td>
        </tr>
     
      <?php } ?>
    </tbody>
  </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/keytable/2.7.0/js/dataTables.keyTable.min.js"></script>

<script type="text/javascript">
    window.onload = function() {
        document.getElementById('search_text').value = '';
        document.getElementById('search_collection_date').value = '';
        document.getElementById('search_field').value = '';
    };
</script>

<script>

    //making required fields label color red
    $("span.required").parents('div.form-display-as-box').css("color", "red");

    $(document).ready(function() {


        jQuery('#dttbl').DataTable({
        "keys": {
           "columns": [3] ,
            "keys": [ "\t".charCodeAt(0) ],
            "editorKeys": "tab-only"
         },
        "ordering": false,
   }).on( 'key-focus', function ( e, datatable, cell, originalEvent ) {

    //alert(cell.node());
      
       $('#ss', cell.node()).focus();
 
   } )

    	/*var table = $('#dttbl').DataTable({
            //keys: true

            "ordering": false,

            "keys": {
           "columns": [3] ,
            "keys": [ "\t".charCodeAt(0) ],
            "editorKeys": "tab-only"
         }
        });

        table.cell( ':eq(0)' ).focus();*/

        $("#all_selected_rcpt_form").submit(function(){

            // $("#search_collection_date").val()
            $rcpt ='';
            $('table tbody tr').each(function(){

                // Monthly payment
                $rcpt_cat = $.trim($(this).find('td:eq(0)').text()).substr(0, 2);
                $rcpt_cat2 = $.trim($(this).find('td:eq(0)').text()).substr(0, 4);

                if($rcpt_cat == 'RM' || $rcpt_cat2 == 'RCPM' || $rcpt_cat == 'RY' || $rcpt_cat2 == 'RCPY' || $rcpt_cat == 'RN' || $rcpt_cat2 == 'RCPN'){
                    $fullrcpt = $.trim($(this).find('td:eq(0)').text())
                    $rcpt += $fullrcpt + ','
                }

            })

            $rcpt = $rcpt.replace(/,\s*$/, "");
            // console.log($rcpt)
            $("#all_selected_rcpts").val($rcpt)

            // return false

        })

        
        if($('#field-Full_Marks').length){
                $('#field-Exam_Year').attr("onkeypress", "return event.charCode >= 48 && event.charCode <= 57");
                $('#field-Full_Marks').attr("onkeypress", "return event.charCode >= 48 && event.charCode <= 57");
        }
        
    });
</script>

</body>
</html>