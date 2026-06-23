<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 07-02-2019
 * Time: 13:33
 */
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Pran Krishna Das">
    <title>Mark Sheet of <?=$class->Class_Name?> - <?=$class->Sec_Name?></title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link href="<?=base_url();?>assets/admin_panel/css/marksheet/normalize_7.0.0.min.css" rel="stylesheet">

    <!-- Load paper.css for happy printing -->
    <link href="<?=base_url();?>assets/admin_panel/css/marksheet/paper_0.4.1.css" rel="stylesheet">

    <link href="<?=base_url();?>assets/admin_panel/css/marksheet/fonts.css" rel="stylesheet">

    <style>
        body{
            font-family: 'Lato', sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%
        }
        td, th {
            border: 1px solid gray;
            text-align: center;
            padding: 8px;
        }
        .text-left{
            text-align: left
        }
        .actual_table td{
            padding:5px
        }

        .left{
            float: left;width:48%;
        }
        .right{
            float: right;width:48%; outline: 1px solid #333
        }
        .col-3{width: 33.33%;float:left}

        .outline{outline: 1px solid #333}
        .clearfix{clear:both}

        .pad-2{padding:2%}
        .pad_0{padding: 0}
        .mar_0{margin: 0}
        @page { size: A4 landscape }
        @media print{
            #print{display:none}
        }
    </style>
</head>

<body class="A4 landscape">
<button id="print" style="float: right">Print</button>

<?php
//students loop
foreach ($students as $s) {
    ?>
    <section class="sheet padding-10mm" id="content">
        <!--LEFT PORTION-->
        <div class="left">

            <div class="col-9">
                Name: <strong><?=$s['STD_FNAME']?> <?=$s['STD_MNAME']?> <?=$s['STD_LNAME']?></strong>
            </div>
            <div class="col-3">
                Class: <strong><?=$class->Class_Name?></strong>
            </div>
            <div class="col-3">
                Section: <strong><?=$class->Sec_Name?></strong>
            </div>
            <div class="col-3">
                Roll: <strong><?=$s['STD_ROLLNO']?></strong>
            </div>
            <br><hr/>

            <table class="actual_table">
                <tr bgcolor="#d3d3d3">
                    <th>Subject</th>
                    <th>Half Yearly</th>
                    <th>Annual</th>
                </tr>
                <?php
                //subject loop
                foreach ($subjects as $sub) {
                    $mrk1 = 0; $mrk2 = 0; $count1 = 0; $count2 = 0; $h_grade = 'Fail'; $a_grade = 'Fail';
                    //fetching half-yearly marks
                    foreach ($marks as $val) {
                        if ($val['MD_STD_SEQ'] == $s['STD_SEQ'] && $val['MD_SUB_SEQ'] == $sub['sub_id'] && ($val['MD_TEST_SEQ'] == '1' || $val['MD_TEST_SEQ'] == '2' || $val['MD_TEST_SEQ'] == '3')) {
                            $mrk1 += $val['MD_MARKS'];
                            $count1++;
                            if($count1 == 3){break;}
                        }
                    }
                    //fetching half-yearly grade
                    foreach ($grades as $key => $g) {
                        if($mrk1 > $g){
                            $h_grade = $key;
                            break;
                        }
                    }
                    //fetching annual marks
                    foreach ($marks as $val) {
                        if ($val['MD_STD_SEQ'] == $s['STD_SEQ'] && $val['MD_SUB_SEQ'] == $sub['sub_id'] && ($val['MD_TEST_SEQ'] == '4' || $val['MD_TEST_SEQ'] == '5' || $val['MD_TEST_SEQ'] == '6')) {
                            $mrk2 += $val['MD_MARKS'];
                            $count2++;
                            if($count2 == 3){break;}
                        }
                    }
                    //fetching annual grade
                    foreach ($grades as $key => $g) {
                        if($mrk2 > $g){
                            $a_grade = $key;
                            break;
                        }
                    }
                    ?>
                    <tr>
                        <td class="text-left"><?=$sub['sub_name'];?></td>
                        <td><?=$h_grade;?></td>
                        <td><?=$a_grade;?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td class="text-left">Attendance</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">Working Days</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            <hr/>
            <p>Principal's Signature: ...............................................................</p>
        </div>

        <!--RIGHT PORTION-->
        <div class="right" style="outline: none">
            <div class="outline">
                <h3 align=center class="pad_0 mar_0" >Half Yearly</h3>
                <br /><br /><br /><br /><br />
                <div class="left" style="outline: none">Class Teacher</div>
                <div class="right" style="outline: none; text-align: right;">Guardian/Parent</div>
                <div class="clearfix"></div>
            </div>
            <br />
            <div class="outline">
                <h3 align=center class="pad_0 mar_0" >Final</h3>
                <br /><br /><br /><br /><br />
                <div class="left" style="outline: none">Class Teacher</div>
                <div class="right" style="outline: none; text-align: right">Guardian/Parent</div>
                <div class="clearfix"></div>
            </div>

            <h2 align="center" style="color: silver;">PROMOTION GRANTED / REFUSED</h2>
            <h3 align="center">New Academic Year Begins on ...........................</h3>

            <div class="left" style="outline: none">Class Teacher</div>
            <div class="right" style="outline: none; text-align: right">Guardian/Parent</div>
            <br><br>

            <table>
                <tr bgcolor="#d3d3d3">
                    <th>Activity / Personality</th>
                    <th>Half Year</th>
                    <th>Final</th>
                </tr>
                <tr>
                    <td class="text-left">1. CONDUCT</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">2. ATTENDANCE & PUNCTUALITY</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">3. ORDER & NEATNESS</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">4. CLASS WORK & HOME WORK</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">5. CO-CURRICULAR ACTIVITIES</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">6. RESPONSIBILITY</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">7. INITIATIVE</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-left">8. DISCIPLINE</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </section>
    <?php
}
?>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>

<script>
    $("#print").click(function () {
        window.print();
    });
</script>

</body>
</html>