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
class accountportlet extends portlet
{
	public function logout($sender,$param)
	{
            $this->Application->getModule('auth')->logout();
            $url=$this->getRequest()->constructUrl('page',$this->Service->DefaultPage);
            $this->Response->redirect($url);
	}

        public function generateStructureOrganisation($sender,$param){
            $command=$param->getCommand();
            if($this->User->isInRole('Administrator')){
                if($command = "createOrganisationStructure"){
                    PFDBTools::initOrganisationStruktur();
                }
            }
        }
}

?>