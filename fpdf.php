<?php
class FPDF {
    protected $page;
    protected $pages = array();
    protected $font;
    protected $buffer;

    function __construct() {
        $this->AddPage();
    }

    function AddPage() {
        $this->page = '';
        $this->pages[] = &$this->page;
    }

    function SetFont($family, $style='', $size=12) {
        $this->font = "$family $style $size";
    }

    function Cell($w, $h, $txt, $border=0, $ln=0, $align='', $fill=false, $link='') {
        $this->page .= $txt . "\n";
    }

    function Output($dest='', $name='') {
        header('Content-Type: text/plain');
        echo implode("\n--- Page ---\n", $this->pages);
    }

    function Ln($h = 10) {
        $this->page .= "\\n"; 
    }
}
?>
