<?php
Prado::using ('Application.app_code.CContextMenu');
class introduction extends TPage
{
	public function clickButton ($sender, $param)
	{
		$this->lblResult->setText('You have clicked the link with the left mouse button !');
	}

	public function clickMenu ($sender, $param)
	{
		$command=$param->getCommand();

		switch ($command)
		{
			case "Menu1":
			case "Menu2":
				$this->lblResult->setText('You have selected the command '.$param->getCommand());
				break;
			case "CommandMenu3":
				$sender->getItems()->itemAt(1)->setEnabled(!$sender->getItems()->itemAt(1)->getEnabled());
				$this->lblResult->setText($sender->getItems()->itemAt(1)->getEnabled()?"Second menu item is now enabled":"Second menu item is now disabled");
				break;
		}
		
	}
}
?>
