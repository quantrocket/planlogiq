<?php

class translationtable extends TPage{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param){
        parent::onLoad($param);
        if(!$this->IsPostBack){
            $this->bind_lstTranslations();
        }
    }

    public function bind_lstTranslations(){
        $this->lstTranslations->dataSource=TransUnitRecord::finder()->findAll();
        $this->lstTranslations->dataBind();
    }

    public function lstTranslationsEdit($sender,$param){
        $this->lstTranslations->EditItemIndex=$param->Item->ItemIndex;
        $this->lstTranslations->DataSource=TransUnitRecord::finder()->findAll();
        $this->lstTranslations->dataBind();
    }

    public function lstTranslations_pageIndexChanged($sender,$param){
        $this->lstTranslations->CurrentPageIndex = $param->NewPageIndex;
        $this->bind_lstTranslations();
    }

    public function lstTranslationsCancel($sender,$param)
    {
        $this->lstTranslations->EditItemIndex=-1;
        $this->lstTranslations->DataSource=TransUnitRecord::finder()->findAll();
        $this->lstTranslations->dataBind();
    }

    public function lstTranslationsSave($sender,$param){
        $item=$param->Item;

        $Record = TransUnitRecord::finder()->findByPK($this->lstTranslations->DataKeys[$item->ItemIndex]);
        $Record->target = $item->lst_target->TextBox->Text;
        $Record->save();

        $this->lstTranslations->EditItemIndex=-1;
        $this->lstTranslations->DataSource=TransUnitRecord::finder()->findAll();
        $this->lstTranslations->dataBind();
    }

			
}

?>