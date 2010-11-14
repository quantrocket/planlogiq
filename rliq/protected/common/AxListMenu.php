<?php

/**
 * AxListMenu, AxListMenuItem, AxListMenuColl class file
 *
 * @author Axelerator inspired by XListMenu by Qiang Xue <qiang.xue@gmail.com>
 */

class AxListMenu extends TControl{

    public function getCssClass() { return $this->getViewState('CssClass',''); }
    public function setCssClass($value) { $this->setViewState('CssClass',TPropertyValue::ensureString($value),''); }

    public function getActCss() { return $this->getViewState('ActCss',''); }
    public function setActCss($value) { $this->setViewState('ActCss',TPropertyValue::ensureString($value),''); }

    public function getIActCss() { return $this->getViewState('IActCss',''); }
    public function setIActCss($value) { $this->setViewState('IActCss',TPropertyValue::ensureString($value),''); }

    public function getColapse() { return $this->getViewState('Colapse',false); }
    public function setColapse($value) { $this->setViewState('Colapse',TPropertyValue::ensureBoolean($value),false); }

    public function render($writer) {
        if ($this->getVisible()){
            $writer->writeLine("<table class='".$this->getCssClass()."' width='100%'>");
            foreach($this->getControls() as $item){
                if (($item instanceof AxListMenuItem)||($item instanceof AxListMenuColl)){
                    $item->render($writer);
                }
            }
            $writer->writeLine("</table>");
        }
    }
    
}

class AxListMenuItem extends TControl{

    public function getText(){ return $this->getViewState('Text',''); }
    public function setText($value){ $this->setViewState('Text',TPropertyValue::ensureString($value),''); }

    public function getNavigateUrl() { return $this->getViewState('NavigateUrl',''); }
    public function setNavigateUrl($value) { $this->setViewState('NavigateUrl',TPropertyValue::ensureString($value),''); }

    public function getPagePath(){ return $this->getViewState('PagePath',array()); }
    public function setPagePath($value){ $this->setViewState('PagePath',TPropertyValue::ensureArray($value),array());}

    public function getURL(){
        if($this->getNavigateUrl() === "")
            if($this->getPagePath() !== array())
                return $url=$this->getService()->constructUrl($this->PagePath[0]);
            else
                return "";
        else
            return $this->getNavigateUrl();
    }

    public function getActCss() { return $this->getViewState('ActCss',$this->getParent()->getActCss()); }
    public function setActCss($value) { $this->setViewState('ActCss',TPropertyValue::ensureString($value),''); }

    public function getIActCss() { return $this->getViewState('IActCss',$this->getParent()->getIActCss()); }
    public function setIActCss($value) { $this->setViewState('IActCss',TPropertyValue::ensureString($value),''); }

    public function isInPage(){
        if(in_array($this->getPage()->getPagePath(), $this->getPagePath()))
            return true;
        else
            return false;
    }

    public function render($writer) {
        $hplnk = new THyperLink();
        $hplnk->setNavigateUrl($this->getURL());
        $hplnk->setText($this->getText());
        if(in_array($this->getPage()->getPagePath(), $this->getPagePath())){
            if ($this->getActCss()!=='')
                $hplnk->setCssClass($this->getActCss());
        }
        else{
            if ($this->getIActCss()!=='')
                $hplnk->setCssClass($this->getIActCss());
        }
        if($this->getVisible()){
            $writer->writeLine("<tr><td class='".$this->getIActCss()."'>");
            $hplnk->render($writer);
            $writer->writeLine("</td></tr>");
        }
    }
}

class AxListMenuColl extends TControl{

    public function getText(){ return $this->getViewState('Text',''); }
    public function setText($value){ $this->setViewState('Text',TPropertyValue::ensureString($value),''); }

    public function getNavigateUrl() { return $this->getViewState('NavigateUrl',''); }
    public function setNavigateUrl($value) { $this->setViewState('NavigateUrl',TPropertyValue::ensureString($value),''); }

    public function getPagePath(){ return $this->getViewState('PagePath',array()); }
    public function setPagePath($value){ $this->setViewState('PagePath',TPropertyValue::ensureArray($value),array());}

    public function getURL(){
        if($this->getNavigateUrl() === "")
            if($this->getPagePath() !== array())
                return $url=$this->getService()->constructUrl($this->PagePath[0]);
            else
                return "";
        else
            return $this->getNavigateUrl();
    }

    public function getColapse() { return $this->getViewState('Colapse',$this->getParent()->getColapse()); }
    public function setColapse($value) { $this->setViewState('Colapse',TPropertyValue::ensureBoolean($value),false); }

    public function getActCss() { return $this->getViewState('ActCss',$this->getParent()->getActCss()); }
    public function setActCss($value) { $this->setViewState('ActCss',TPropertyValue::ensureString($value),''); }

    public function getIActCss() { return $this->getViewState('IActCss',$this->getParent()->getIActCss()); }
    public function setIActCss($value) { $this->setViewState('IActCss',TPropertyValue::ensureString($value),''); }

    public function isInPage(){
        if(in_array($this->getPage()->getPagePath(), $this->getPagePath()))
            return true;
        else{
            foreach($this->getControls() as $item)
                if (($item instanceof AxListMenuItem)||($item instanceof AxListMenuColl))
                    if ($item->isInPage())
                        return true;
            return false;
        }
    }

    public function getFirstSubLnk(){
        $lstr = $this->getNavigateUrl();
        if ($lstr === ""){
            foreach($this->getControls() as $item){
                if ($item instanceof AxListMenuItem){
                    $lstr = $item->getURL();
                }
                elseif($item instanceof AxListMenuColl){
                    $lstr=$item->getFirstSubLnk();
                }
                if($lstr !== "")
                    return $lstr;
            }
        }
    }

    public function render($writer){
        if ($this->getVisible()){
            $writer->writeLine("<table><tr><td>");
            $hplnk = new THyperLink;
            if ($this->getNavigateUrl() !== ''){
                $hplnk->setNavigateUrl($this->getURl());
            }
            else{
                $hplnk->setNavigateUrl($this->getFirstSubLnk());
            }
            $hplnk->setText($this->getText());
            if($this->isInPage())
                $hplnk->setCssClass($this->getActCss());
            else
                $hplnk->setCssClass($this->getIActCss());
            $hplnk->render($writer);
            if((!$this->getColapse())||($this->isInPage())){
                $writer->writeLine("<table>");
                foreach($this->getControls() as $item)
                    if (($item instanceof AxListMenuItem)||($item instanceof AxListMenuColl))
                        $item->render($writer);
                $writer->writeLine("</table>");
                $writer->writeLine("</td></tr></table>");
            }
        }
    }

}

?>
