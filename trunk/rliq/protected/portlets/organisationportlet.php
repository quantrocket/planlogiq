<?php
/**
 * AccountPortlet class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: AccountPortlet.php 1398 2006-09-08 19:31:03Z xue $
 */

Prado::using('Application.portlets.portlet');

/**
 * AccountPortlet class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 */
class organisationportlet extends portlet{

    public function callback_MyCallback($sender,$param){
        if($this->page->isCallback){
            $theObjectContainingParameters = $param->CallbackParameter;
            $this->page->view_Organisation($theObjectContainingParameters->idtm_organisation);
        }
    }

    public function callback_MyCallbackMenuClick($sender,$param){
        if($this->page->isCallback){
            $theObjectContainingParameters = $param->CallbackParameter;
            $this->page->add_context_Organisation($theObjectContainingParameters->idtm_organisation,$theObjectContainingParameters->idta_organisation_type);
        }
    }

    public function callback_MyCallbackPreview($sender,$param){
        if($this->page->isCallback){
            $theObjectContainingParameters = $param->CallbackParameter;
            if(substr($theObjectContainingParameters->idtm_aufgaben,0,3)=='DAT'){
                $this->page->applyDateFilter($theObjectContainingParameters->idtm_aufgaben);
            }else{                
                $this->page->previewOrganisation($theObjectContainingParameters->idtm_aufgaben);
            }
        }
    }

    public function callback_MyCallbackDrop($sender,$param) {
        if($this->page->isCallback) {
            $theObjectContainingParameters = $param->CallbackParameter;
            $Record = OrganisationRecord::finder()->findBy_idtm_organisation($theObjectContainingParameters->idtm_organisation);
            $Record->parent_idtm_organisation = $theObjectContainingParameters->parent_idtm_organisation;
            $Record->save();
        }
    }
	
}

?>