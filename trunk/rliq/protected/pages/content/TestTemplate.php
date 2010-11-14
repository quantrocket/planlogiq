<?php

class TestTemplate extends TTemplateControl
{
    
    public function dataBind()
    {
        $this->grid1->DataSource = array(0 => array('id' => 0));
        $this->grid1->DataBind();

        $this->grid2->DataSource = array(0 => array('id' => 0));
        $this->grid2->DataBind();
    }
    
    
    public function toggleGrid($sender, $param)
    {
        if($this->grid1->Visible){
            $this->grid1->Visible = false;
            $this->grid2->Visible = true;
        }else{
            $this->grid1->Visible = true;
            $this->grid2->Visible = false;
        }
            
    }

}


?>
