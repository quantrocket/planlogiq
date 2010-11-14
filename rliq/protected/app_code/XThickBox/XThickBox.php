<?php
/* 
 * XThickBox component for Prado, based on jQuery's ThickBox
 *
 * @author Yery Tannus <ytannus@gmail.com>
 * @link http://jquery.com/demo/thickbox/
 *
 */


/**
 * Class XTickBox
 *
 * Based on: http://jquery.com/demo/thickbox/
 * 
 * ThickBox is a webpage UI dialog widget written in JavaScript
 * on top of the jQuery library. Its function is to show a single
 * image, multiple images, inline content, iframed content, or
 * content served through AJAX in a hybrid modal.
 * 
 * Configurable Properties:
 * Width: width of box
 * Height: height of box
 * Text: link text to show
 * Modal: http://en.wikipedia.org/wiki/Modal_window
 * 
 * 
 * 
 * Example:
 *
   <com:XThickBox
       ID="ticktest"
       Width="400"
       Height="300"
       Text="click me!!"
       Modal="false">

        Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
        Donec urna ipsum, feugiat ultrices, commodo sit amet,
        pellentesque at, augue. Vestibulum sed justo sed mi mattis mattis.
        Aenean tortor quam, aliquet vitae, ullamcorper at, lacinia vitae, enim.
        Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.

   </com:XThickBox>
 
 * 
 */

class XThickBox extends TPanel
{
    //TODO: Set default values...   
    private $width=400;
    private $height=400;
    private $text="thickbox link";
    private $modal="false";

    
    public function onInit($param) {
        
        parent::onInit($param);
        
        $this->getPage()->getClientScript()->registerScriptFile('JQuery',$this->publishAsset("js/jquery.js"));
        $this->getPage()->getClientScript()->registerScriptFile('XThickBox',$this->publishAsset("js/thickbox.js"));
        $this->getPage()->getClientScript()->registerStyleSheetFile('XThickBoxCss',$this->publishAsset('css/thickbox.css'));
       
    }
    
    public function onLoad($param) {
        
        parent::onLoad($param);

         // initially hides the panel
        $this->Attributes->style = "display:none;";


    }
    
    public function renderContents($writer) {
        //Fixing render problem, content needs to be enclosed between <p>'s
        $writer->write('<p>');
        parent::renderContents($writer);
        $writer->write('</p>');     
    }

    
    public function renderEndTag($writer) {
        
        parent::renderEndTag($writer);
        
        $width=$this->getWidth();
        $height=$this->getHeight();
        $text=$this->getText();

        $target=$this->getClientID();
        $modal=$this->getModal();

        $link="<a href='#TB_inline?height=$height&width=$width&inlineId=$target&modal=$modal' class='thickbox'>$text</a>";
        $writer->write($link);
    }
    

    public function getText() {
        return $this->getViewState('Text','');
    }
    public function setText($value) {
        $this->setViewState('Text',$value,'');
    }
    
    public function getWidth() {
        return $this->getViewState('Width','');
    }
    public function setWidth($value) {
        $this->setViewState('Width',$value,'');
    }
    public function getHeight() {
        return $this->getViewState('Height','');
    }
    public function setHeight($value) {
        $this->setViewState('Height',$value,'');
    }

    public function getModal() {
        return $this->getViewState('Modal','');
    }
    public function setModal($value) {
        $this->setViewState('Modal',$value,'');
    }
}

?>
