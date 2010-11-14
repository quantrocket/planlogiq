<?php

class risikonavigator extends TPage {

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {
            $this->bindRepeater();
        }

    }

    public function bindRepeater() {

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idta_risiko_type = :suchtext1";
        $criteria->Parameters[':suchtext1'] = 1;

        $this->Repeater->VirtualItemCount = count(RisikoRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->Repeater->PageSize);
        $criteria->setOffset($this->Repeater->PageSize * $this->Repeater->CurrentPageIndex);

        $this->Repeater->VirtualItemCount = count(RisikoRecord::finder()->findAll());
        $this->Repeater->DataSource=RisikoRecord::finder()->findAll($criteria);
        $this->Repeater->dataBind();
    }

    public function bindRepeater2($sender,$param) {

        $item=$param->Item;

        $this->bindRepeater4($sender,$param);

        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="parent_idtm_risiko = :suchtext1 AND idta_risiko_type = :suchtext2";
            $criteria->Parameters[':suchtext1'] = $item->Data->idtm_risiko;
            $criteria->Parameters[':suchtext2'] = 2;

            $item->Repeater2->DataSource=RisikoRecord::finder()->findAll($criteria);
            $item->Repeater2->dataBind();
        }
    }

    public function bindRepeater3($sender,$param) {

        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="idtm_risiko = :suchtext1";
            $criteria->Parameters[':suchtext1'] = $item->Data->idtm_risiko;

            $item->Repeater3->DataSource=RCValueRecord::finder()->findAll($criteria);
            $item->Repeater3->dataBind();
        }
    }

    public function bindRepeater4($sender,$param) {

        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="idtm_risiko = :suchtext1";
            $criteria->Parameters[':suchtext1'] = $item->Data->idtm_risiko;

            $item->Repeater4->DataSource=RCValueRecord::finder()->findAll($criteria);
            $item->Repeater4->dataBind();
        }
    }

}
?>