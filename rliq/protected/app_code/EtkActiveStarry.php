<?php
/**
 * EtkActiveStarry class file.
 *
 * @author Edward Stow <edward@etk.com.au>
 * @author Bradley Booms <bradley.booms@gmail.com>
 *
 *  Javascript component based upon starry control version 1.1 (April 27, 2007)
 *  (c) 2007 Chris Iufer <chris@duarte.com>
 *  Starry is freely distributable under the terms of an MIT-style license.
 *  See the Duarte Design web site: http://www.duarte.com/starry/
 *
 * This component is released under a BSD licence compatible with the
 * Prado license at http://pradosoft.com/license/
 *
 * Eternity Technologies Pty Ltd (www.etk.com.au) retain copyright.
 *  
 */

Prado::using('System.Web.UI.ActiveControls.TActiveControlAdapter');

/**
 * EtkActiveStarry class
 *
 * EtkActiveStarry display a bar of stars.  Users can select a star rating.
 * The number of stars displayed is determined by the {@link setMaxStars MaxStars}
 * property. A zero/null value star can be displayed by setting the 
 * {@link setShowNullStar ShowNullStar} property. 
 * 
 * If {@link setAutoPostBack AutoPostBack} is set true, clicking a star 
 * value will cause postback action.
 * 
 * Setting the value is done using the {@link setStarRating StarRating}, and 
 * a default value can be set using the {@link setDefaultStars DefaultStars} 
 * property. 
 * 
 *
 * A common usage of EtkActiveStarry is included in the following sequence:
 * <code>
 * <com:EtkActiveStarry
 *  MaxStars="8"
 *  StarRating="4"
 *  AutoPostBack="true"
 *  OnStarRatingChanged="onStarRatingChanged"/>
 * </code>
 * @author Bradley Booms <bradley.booms@gmail.com>
 * @author Edward Stow <edward@etk.com.au>
 */
class EtkActiveStarry extends EtkStarry implements ICallbackEventHandler, IActiveControl{
	/**
	 * Creates a new callback control, sets the adapter to
	 * TActiveControlAdapter. If you override this class, be sure to set the
	 * adapter appropriately by, for example, by calling this constructor.
	 */
	public function __construct(){
		parent::__construct();
		$this->setAdapter(new TActiveControlAdapter($this));
	}
	
	/**
	 * @return TBaseActiveCallbackControl standard callback control options.
	 * @see {@link IActiveControl::getActiveControl}
	 */
	public function getActiveControl(){
		return $this->getAdapter()->getBaseActiveControl();
	}
	/**
	 * /**
	 * Raises the callback event. 
	 * @see {@link ICallbackEventHandler::raiseCallbackEvent}
	 * @param TCallbackEventParameter the event parameter
	 */
 	public function raiseCallbackEvent($param){	}

	/**
	 * @return string the name of the client (javascript) class name.
	 */
	protected function getClientClassName(){
		return 'EtkActiveStarry';
	}
	
	/**
	 * Registers the objects to allow it to respond to post events.
	 * Seperated to allow for subclassing by EtkActiveStarry.
	 */
	protected function registerPostBack(){
		if (!$this->getPage()->getIsPostBack() || !$this->getPage()->getisCallback()){
			$this->getPage()->getClientScript()->registerPradoScript('ajax');
			$this->getPage()->getClientScript()->registerCallbackControl($this->getClientClassName(),$this->getPostBackOptions());
		}		
	}

}

?>