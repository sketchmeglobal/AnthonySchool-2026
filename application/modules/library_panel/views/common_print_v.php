<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 30-09-2020
 * Time: 11:53
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Pran Krishna Das">
    <link href="<?=base_url();?>assets/img/favicon.ico" rel="shortcut icon" type="image/png">

    <title><?=$tab_title.' | '.WEBSITE_NAME;?></title>
    <!-- Normalize or reset CSS with your favorite library -->
    <link href="<?=base_url();?>assets/admin_panel/css/normalize.min.css" rel="stylesheet">
    <!-- Load paper.css for happy printing -->
    <link href="<?=base_url();?>assets/admin_panel/css/paper.css" rel="stylesheet">
    <link href="<?=base_url();?>assets/admin_panel/css/fonts.googleapis.css" rel="stylesheet">

    <link href="https://fonts.cdnfonts.com/css/coronet" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/monotype-corsiva" rel="stylesheet">
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300&family=Pacifico&display=swap');
        @font-face {
            font-family: 'Mistral';
            font-style: normal;
            font-weight: normal;
            src: local('Mistral'), url(<?=FCPATH?>'assets/admin_panel/fonts/MISTRAL.woff') format('woff');
        }
    </style>

    <!-- common head -->
    <!-- /common head -->
    <style>
        .input_ruler {
            border-bottom: 1px solid darkgrey;
            padding-left: 0px;
        }
        .custom-col-5{width: 20%;width: 20%;float: left;padding-left: 15px;padding-right: 15px;}
        hr {
    margin-top: 20px;
    margin-bottom: 20px;
    border: 0;
    border-top: 1px solid #262222;
}
        .mb-1{margin-bottom: 1rem;}

        @media print{
            .col-sm-1 { width: 8.33333333%!important; float:left!important;}
            .col-sm-2 { width: 16.66666667%!important; float:left!important; }
            .col-sm-3 { width: 25%!important; float:left;!important; }
            .col-sm-4 { width: 33.33333333%%;!important; float:left;!important; }
            .col-sm-5 { width: 41.66666667%!important; float:left;!important; }
            .col-sm-6 { width: 50%!important; float:left!important; }
            .col-sm-7 { width: 58.33333333%!important; float:left;!important; }
            .col-sm-8 { width: 66.66666667%;!important; float:left;!important; }
            .col-sm-9 { width: 75%!important; float:left;!important; }
            .col-sm-10 { width: 83.33333333%;!important; float:left;!important; }
            .col-sm-11 { width: 91.66666667%;!important; float:left;!important; }
            .col-sm-12 { width: 100%!important; float:left;!important; }

            .col-sm-offset-2 { margin-left: 16.66666667%;!important; }
            .col-sm-offset-8 { margin-left: 66.66666667%;!important; }
            .custom-col-5{width: 20%;width: 20%;float: left;padding-left: 15px;padding-right: 15px;}
            .mb-1{margin-bottom: 1rem;}
            .text-red {
            color: red!important;
        }
#red_td {
    background-color: #fbb6b6!important;
    color: red!important;
        }
        }
    </style>

    <!--table to excel-->
    <style type="text/css">
        @media print{
            .print-me{display: none}
        }
    </style>
</head>


<?php
//if $print_section not set
if(!isset($print_section)) {
    ?>
    <body>
    <div class="col-sm-12">
        <div class="row">
            <h1 class="text-warning text-center" style="padding-top: 50px;">
                <strong>No Data!</strong>
            </h1>
        </div>
    </div>
    </body>
    <?php
}

elseif($print_section == 'details_report') {
    ?>
    <body class="A4" onload="window.print()">
    <style>
        .height40{height:40px}
        .border-all{border: 1px solid #000}.border-bottom{border-bottom: 1px solid #000}
        .m-0{margin:0}

        @media print 
        {
        @page
        {
        size: 210mm 297mm;
        }
        }

    </style>

    <?php
    if(count($result) > 0) {
        ?>
        <!-- Each sheet element should have the class "sheet" -->
        <section class="sheet padding-10mm">
            <div class="row">
                <div class="col-sm-2 text-left">
                    <img src="<?=base_url('assets/img/SCHOOL_LOGO.jpg')?>" class="img-circle" alt="Logo" width="100px" height="100px" >
                </div>

                <div class="col-sm-10 text-center">
                    <p style="font-size: 24px"><strong>ST. ANTHONY'S SCHOOL</strong></p>
                    <p class="text-center" style="font-size: 15px">
                        19, MARKET STREET <br/>
                        <span style="float: right;">SCHOOL INDEX No.AI-059 <br/>
                        Date: <?= date ('d-m-Y'); ?> </span>
                    </p>
                </div>
            </div>

            <br/>
            <div class="row" style="font-family: 'Lato', sans-serif;">
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                            <th>Student Name</th>
                            <th>Class-Sec</th>
                            <th>Book Name</th>
                            <th>Book Issue Date</th>
                            <th>Book Return Date</th>
                            <th>Fine Charges</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($result as $std) {
                                    //Fine Calculation
                                    $actual_fine = 0;
                                    $return_date = strtotime($std->return_date);
                                    $current_date = strtotime(date("Y-m-d"));
                                    $diff = $current_date - $return_date;
                                    if($diff >= 0) {
                                    $date_values = floor($diff/(60*60*24));
                                    } else {
                                    $date_values = 0;   
                                    }
                                    $fine_amount = $this->db->get_where('library_fine_charges', array('status' => 1))->row()->fine_amount;
                                    $book_price = $this->db->get_where('book_master', array('BOOK_SEQ' => $std->BOOK_SEQ))->row()->Cost;
                                    //If fine amount exceed actual book cost then book cost will be actual fine amount
                                    if($book_price < $fine_amount) {
                                    $actual_fine = $book_price;
                                    } else {
                                    $actual_fine = $fine_amount * $date_values;
                                    } 
                             if($with_fine == 1 && $actual_fine = 0) {
                               continue;
                             } elseif($with_fine == 1 && $actual_fine > 0) {
                               continue;
                             }
                            ?>
                            <tr>
                            <td><?= $std->ST_FULL_NAME ?></td>
                            <td><?= $std->class_sec ?></td>
                            <td><?= $std->Book_Name ?></td>
                            <td><?= date("d-m-Y", strtotime($std->issue_date)) ?></td>
                            <td><?= date("d-m-Y", strtotime($std->return_date)) ?></td>
                            <?php 
                             ?>
                            <td><?= $actual_fine ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br/><br/><br/>
        </section>
        <?php
    }
    ?> 
    </body>
    <?php
}
?>

<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/bootstrap.min.js"></script>
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->

<!--form validation-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery.validate.min.js" type="text/javascript"></script>-->
<!--form validation init-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/form-validation-init.js"></script>-->

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>

<script type="text/javascript">
    //table to excel (single table)
    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
            , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
        return function (table, name, filename) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }

            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();

        }
    })();


    //table to excel (multiple table)
    var array1 = new Array();
    var n = <?php if(isset($table_no)){echo $table_no;}else{echo 0;} ?>; //Total table
    for ( var x=1; x<=n; x++ ) {
        array1[x-1] = 'export_table_to_excel' + x;
    }
    var tablesToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>'
            , templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>'
            , body = '<body>'
            , tablevar = '<table>{table'
            , tablevarend = '}</table>'
            , bodyend = '</body></html>'
            , worksheet = '<x:ExcelWorksheet><x:Name>'
            , worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>'
            , worksheetvar = '{worksheet'
            , worksheetvarend = '}'
            , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
            , wstemplate = ''
            , tabletemplate = '';

        return function (table, name, filename) {
            var tables = table;
            var wstemplate = '';
            var tabletemplate = '';

            wstemplate = worksheet + worksheetvar + '0' + worksheetvarend + worksheetend;
            for (var i = 0; i < tables.length; ++i) {
                tabletemplate += tablevar + i + tablevarend;
            }

            var allTemplate = template + wstemplate + templateend;
            var allWorksheet = body + tabletemplate + bodyend;
            var allOfIt = allTemplate + allWorksheet;

            var ctx = {};
            ctx['worksheet0'] = name;
            for (var k = 0; k < tables.length; ++k) {
                var exceltable;
                if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                ctx['table' + k] = exceltable.innerHTML;
            }

            // window.location.href = uri + base64(format(allOfIt, ctx));

            document.getElementById("dlink").href = uri + base64(format(allOfIt, ctx));;
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();
        }
    })();
</script>

</html>
