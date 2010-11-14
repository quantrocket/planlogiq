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
class pspportlet extends portlet
{

    public function callback_MyCallback($sender,$param){
        if($this->page->isCallback && $this->page->isPostBack){
            $theObjectContainingParameters = $param->CallbackParameter;
            $this->page->view_Activity($theObjectContainingParameters->idtm_activity);
        }
    }

    public function callback_MyCallbackMenuClick($sender,$param){
        if($this->page->isCallback && $this->page->isPostBack){
            $theObjectContainingParameters = $param->CallbackParameter;
            $this->page->add_context_Activity($theObjectContainingParameters->idtm_activity,$theObjectContainingParameters->idta_activity_type);
        }
    }

    public function callback_MyCallbackDrop($sender,$param) {
        if($this->page->isCallback && $this->page->isPostBack) {
            $theObjectContainingParameters = $param->CallbackParameter;
            $Record = ActivityRecord::finder()->findBy_idtm_activity($theObjectContainingParameters->idtm_activity);
            $Record->parent_idtm_activity = $theObjectContainingParameters->parent_idtm_activity;
            $Record->save();
        }
    }
	
}

?>