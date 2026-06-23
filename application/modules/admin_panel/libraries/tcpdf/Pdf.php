<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 15-01-2019
 * Time: 16:07
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf.php';

class Pdf extends TCPDF {

    public function __construct() {
        parent::__construct();
    }

    var $htmlHeader;
    var $need_bg_img = false;

    public function setHtmlHeader($htmlHeader, $need_bg_img) {
        $this->htmlHeader = $htmlHeader;
        $this->need_bg_img = $need_bg_img;
    }

    public function Header() {
        $this->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $this->htmlHeader, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);

        if($this->need_bg_img) {
            // -- set new background ---
            // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->getAutoPageBreak();
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);
            
            
           // set default header data
            //$logo_file = base_url() . 'assets/img/favicon.ico';
            //$this->SetHeaderData($logo_file, '99', 'TITLE'.' 008', 'TI');
            // set bacground image
            $img_file = base_url() . 'assets/img/logo_stan1.jpg';
            $this->Image($img_file, 55, 50, 90, 75, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();
        }
    }

}