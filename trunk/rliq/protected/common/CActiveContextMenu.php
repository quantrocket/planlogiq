<?php

if (!prado::getPathOfAlias('CContextMenu')) prado::setPathOfAlias('CContextMenu', dirname(__FILE__));
prado::using ('CContextMenu.CContextMenu');
prado::using ('System.Web.UI.ActiveControls.TActiveControlAdapter');

class CActiveContextMenu extends CContextMenu implements IActiveControl, ICallbackEventHandler
{
    /**
	 * Creates a new callback control, sets the adapter to
	 * TActiveControlAdapter. If you override this class, be sure to set the
	 * adapter appropriately by, for example, by calling this constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setAdapter(new TActiveControlAdapter($this));
	}

	/**
	 * @return TBaseActiveControl basic active control options.
	 */
	public function getActiveControl()
	{
		return $this->getAdapter()->getBaseActiveControl();
	}

	/**
	 *
	 * Raises the callback event. This method is required by {@link
	 * ICallbackEventHandler} interface. It will raise {@link OnMenuItemSelected
	 * OnMenuItemSelected} event first and then the {@link onCallback OnCallback} event.
	 * This method is mainly used by framework and control developers.
	 * @param TCallbackEventParameter the event parameter
	 */
	public function raiseCallbackEvent($param)
	{
		$this->raisePostBackEvent(implode(',',$param->getCallbackParameter()));
		$this->onCallback($param);
	}

	/**
	 * This method is invoked when a callback is requested. The method raises
	 * 'OnCallback' event to fire up the event handlers. If you override this
	 * method, be sure to call the parent implementation so that the event
	 * handler can be invoked.
	 * @param TCallbackEventParameter event parameter to be passed to the event handlers
	 */
	public function onCallback($param)
	{
		$this->raiseEvent('OnCallback', $this, $param);
	}

	/**
	 * Renders the client-script code.
	 */
	protected function renderClientControlScript()
	{
		$this->getActiveControl()->registerCallbackClientScript(
			$this->getClientClassName(), $this->getPostBackOptions());
	}

	/**
	 * @return string corresponding javascript class name for this CActiveContextMenu.
	 */
	protected function getClientClassName()
	{
		return 'CActiveContextMenu';
	}
}
?>
