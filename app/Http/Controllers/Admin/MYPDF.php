<?php

namespace App\Http\Controllers\Admin;

use PDF;

class MYPDF extends PDF
{
    protected $footer_data;
    protected $footertext;
    // public function Header()
    // {
    //     // $image_file = 'st_logo.png';
    //     $this->SetFont(Arial, 'B', 9);
    //     $this->SetTextColor(167, 147, 68);
    //     // $this->Image($image_file, 11, 3, 50, 15);
    // }
    // public function setFootertext($string)
    // {
    //     $this->footertext = $string;
    // }
    // public function Footer()
    // {
    //     $this->SetY(-15);
    //     if ($this->PrintCoverPageFooter && $this->page == 1) {
    //         // $this->Cell(0, 10, 'Cover Page Footer '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    //     } elseif ($this->PrintCoverPageFooter && $this->page == 2) {
    //         $this->Cell(0, 10, 'Cover Page Overflow Footer ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    //     } else {
    //         $this->Cell(0, 10, 'Other Page Footer' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    //     }
    // }
}
