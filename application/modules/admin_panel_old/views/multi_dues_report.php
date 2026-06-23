<!DOCTYPE html>
<html lang='en'>
    <head>
        <title>All Dues | St. Anthony's English Medium School</title>
        <meta name='description' content='Paper Css reports'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css'>
        <link href='https://fonts.googleapis.com/css2?family=Oswald:wght@400&display=swap' rel='stylesheet'>
        <style>
            section{margin: auto}
            .text-center{text-align: center;}
            .text-right{text-align: right}
            hr.style_2{border-top: 2px dashed #000;margin-top: 15px;}
            hr.style-four {padding: 0;border: none;border-top: medium double #333;color: #333;text-align: center;}
            hr.style-four:after {content: 'TEAR HERE';display: inline-block;position: relative;top: -0.7em;font-size: 1.5em;padding: 0 0.25em;background: white;}

            .sheet.padding-10mm{padding-top: 0px}
            header h3{margin-bottom: 4px;font-size: 16px;}
            header h4{margin: 0px;font-size: 15px;}
            header h5{margin-top: 4px; margin-bottom: 5px;}
            p{font-size: 14px;line-height: 1.3;margin: 3px 0;}
            table{width: 50%;margin: auto;border: 1px solid;}
        </style>
    </head>
    <body class='A4'>
            <?= $sections ?>
        <!-- Placed js at the end of the document so the pages load faster -->
        <script src='https://stanthonyschooledu.org/assets/admin_panel/js/jquery-1.10.2.min.js'></script>    
        <script></script>
    </body>
</html>