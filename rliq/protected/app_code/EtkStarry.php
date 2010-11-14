<?php
/**
 * EtkStarry class file.
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

/**
 * EtkStarry class
 *
 * EtkStarry display a bar of stars.  Users can select a star rating.
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
 * A common usage of EtkStarry is included in the following sequence:
 * <code>
 * <com:EtkStarry
 *  MaxStars="8"
 *  StarRating="4"
 *  AutoPostBack="true"
 *  OnStarRatingChanged="onStarRatingChanged"/>
 * </code>
 * @author Bradley Booms <bradley.booms@gmail.com>
 * @author Edward Stow <edward@etk.com.au>
 */
class EtkStarry extends TPanel implements IDataRenderer, IValidatable, INamingContainer{
	/**
	 * Adds attribute name-value pairs to renderer.
	 * This method overrides the parent implementation with additional textbox specific attributes.
	 * @param THtmlWriter the writer used for the rendering purpose
	 */
	protected function addAttributesToRender($writer){
		parent::addAttributesToRender($writer);
		$writer->addAttribute('id',$this->getClientID());
		$enabledCssClass = ($this->getEnabled()) ? 'enabled' : 'disabled';
		$writer->addAttribute('class', "starry $enabledCssClass");
	}
	
	/**
	 * This method registers the appropriate javascript and css files for
	 * the component. Also calls {@link registerPostBack},
	 * which allows the object to respond to post events.
	 * 
	 * This method is invoked when the control enters 'OnPreRender' stage.
	 * The method raises 'OnPreRender' event.
	 * If you override this method, be sure to call the parent implementation
	 * so that the event handlers can be invoked.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onPreRender($param){
		parent::onPreRender($param);
		$this->setHeight("30px");
		$this->getPage()->getClientScript()->registerPradoScript('prado');
		
		$this->getPage()->getClientScript()->registerStyleSheetFile('EtkStarryCss',$this->publishAsset("/stars.css", __CLASS__));
		$this->getPage()->getClientScript()->registerScriptFile('EtkStarryJs',$this->publishAsset("/stars.js", __CLASS__));
		$this->registerPostBack();
	}
	
	/**
	 * Registers the objects to allow it to respond to post events.
	 * Seperated to allow for subclassing by EtkActiveStarry.
	 */
	protected function registerPostBack(){
		$this->getPage()->getClientScript()->registerPostBackControl($this->getClientClassName(),$this->getPostBackOptions());
	}
	
	/**
	 * @return array the list of options that get returned to the client side.
	 */
	protected function getPostBackOptions(){
		// Standard  Options :
		$options['ID'] = $this->getClientID();
		$options['EventTarget'] = $this->getUniqueID();
		$options['AutoPostBack'] = $this->getAutoPostBack();
		$options['Enabled'] = $this->getEnabled();
		
		// Control options
		
		$options['MaxStars'] = $this->getMaxStars();
		$options['DefaultStars'] = $this->getDefaultStars();
		$options['ShowNullStar'] = $this->getShowNullStar();
		$options['ShowHalfStar'] = $this->getShowHalfStar();
		$options['StarRating']= $this->getStarRating();
		return $options;
	}
	
	/**
	 * @return string the name of the client (javascript) class name.
	 */
	protected function getClientClassName(){
		return 'EtkStarry';
	}
	
	/**
	 * Creates a {@link THiddenField} object and all of the {@link EtkStar} objects.
	 * 
	 * Creates child controls.
	 * This method can be overriden for controls who want to have their controls.
	 * Do not call this method directly. Instead, call {@link ensureChildControls}
	 * to ensure child controls are created only once.
	 */
	public function createChildControls(){
		$this->createHidden();
		$this->createNullStar();
		$this->createStandardStars();
		$this->createHalfStandardStar();
		$this->createDotStars();
	}
	
	/**
	 * Creates a {@link THiddenField} object and registers an event handler.
	 */
	protected function createHidden(){
		$hidden = prado::createComponent('THiddenField');
		$hidden->setID($this->getClientID().'_hidden');
		$hidden->OnValueChanged[] = array($this, "hiddenFieldChanged");
		$this->getControls()->add($hidden);
	}
	
	/**
	 * Creates a {@link EtkStar} object with the 'outline' {@link EtkStar::getStarType StarType}.
	 */
	protected function createNullStar(){	
		if($this->getShowNullStar())
			$this->createStar('outline', 0);
	}
	
	/**
	 * Creates a {@link EtkStar} object with the 'standard' {@link EtkStar::getStarType StarType}.
	 */
	protected function createStandardStars(){
		for ($i = 1 ; $i <= $this->getStarRating() ; $i++){
			$this->createStar('standard', $i);
		}		
	}
	
	/**
	 * Creates a {@link EtkStar} object with the 'half-standard' {@link EtkStar::getStarType StarType}.
	 */
	protected function createHalfStandardStar(){
		$rating = $this->getStarRating();
		if ($this->isStarRatingSomethingAndHalf($rating))
		{
			$this->createStar('half-standard', floor($rating) + 1);
		}
	}

	/**
	 * @return true if the star rating ends in .5, 3.5 - true, 3.0 - false.
	 */	
	protected function isStarRatingSomethingAndHalf($rating)
	{
		return (($rating * 10) % 10) > 0;
	}

	/**
	 * Creates a {@link EtkStar} object with the 'dot' {@link EtkStar::getStarType StarType}.
	 */
	protected function createDotStars(){
		$rating = $this->getStarRating();
		$rating = round($rating); // round(3.5) is 4  round(3.0) is 3
		for ($i = $rating + 1 ; $i <= $this->getMaxStars() ; $i++){
			$this->createStar('dot', $i);
		}		
	}

	/**
	 * Creates a {@link EtkStar} object, sets {@link TControl::getID ID} property,
	 * and the {@link EtkStar::getStarType StarType} property.
	 */
	protected function createStar($starType, $index){
		$star = prado::createComponent('EtkStar');
		$star->setID($this->getClientID().'_'.$index);
		$star->setStarType($starType);
		$this->getControls()->add($star);
		
	}

	/**
	 * Event handler for the hidden field {@link THiddenField::onValueChanged} event.
	 * This event notifies us that the client side made a change to the 
	 * {@link getStarRating StarRating} property value.
	 * Calls {@link onStarRatingChanged} function.
	 */
	public function hiddenFieldChanged($sender, $param){
		$value = TPropertyValue::ensureFloat($sender->getValue());
		if($this->getStarRating() !== $value)
			$this->setStarRating($value);
		$this->onStarRatingChanged($param);
	}
	
	/**
	 * Raises <b>OnStarRatingChanged</b> event.
	 * This method is invoked when the value of the {@link getStarRating StarRating}
	 * property changes on postback.
	 * If you override this method, be sure to call the parent implementation to ensure
	 * the invocation of the attached event handlers.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onStarRatingChanged($param){
		$this->raiseEvent('OnStarRatingChanged',$this,$param);
	}
	
	/**
	 * @return integer the total number of stars displayed, default 5.
	 */
	public function getMaxStars(){
		return $this->getViewState('MaxStars', 5);
	}
	
	/**
	 * @param integer the total number of stars displayed.
	 */
	public function setMaxStars($value){
		$value = TPropertyValue::ensureInteger($value);
		if($value > 0)
			$this->setViewState('MaxStars', $value, 5);
		else
			throw new TApplicationException('MaxStars must be more than 0');
	}
	
	/**
	 * @return integer the default number of stars selected, default 2.
	 */
	public function getDefaultStars(){
		return $this->getViewState('DefaultStars', 2);		
	}
	
	/**
	 * @param integer the default number of stars selected.
	 */
	public function setDefaultStars($value){
		$value = TPropertyValue::ensureInteger($value);
		if($value >= 0)
			$this->setViewState('Default', $value, 2);
		else
			throw new TApplicationException('DefaultStars must be more than 0');
	}
	
	/**
	 * @return boolean whether to show the zero/null star, default true.
	 */
	public function getShowNullStar(){
		return $this->getViewState('ShowNullStar', true);
	}

	/**
	 * @return boolean whether to show the half star, default false.
	 */
	public function getShowHalfStar(){
		return $this->getViewState('ShowHalfStar', false);
	}

	/**
	 * @param boolean whether to show the half star.
	 */
	public function setShowHalfStar($value){
		$value = TPropertyValue::ensureBoolean($value);	
		$this->setViewState('ShowHalfStar', $value, true);
	}

	/**
	 * @param boolean whether to show the zero/null star.
	 */
	public function setShowNullStar($value){
		$value = TPropertyValue::ensureBoolean($value);	
		$this->setViewState('ShowNullStar', $value, true);
	}
	
	/**
	 * @return integer the current number of stars selected, 
	 * default {@link setDefaultStars DefaultStars} property.
	 */
	public function getStarRating(){
		$default = $this->getDefaultStars();
		return $this->getViewState('StarRating', $default);
	}
		
	/**
	 * @param integer the current number of stars selected.
	 */
	public function setStarRating($value){
		$value = TPropertyValue::ensureFloat($value);

		
		$max = $this->getMaxStars();
		if($value >= 0 && $value <= $max){
			$default = $this->getDefaultStars();
			$this->setViewState('StarRating',$value, $default);
		}
		else
			throw new TApplicationException("StarRating must be between 0 and MaxStars inclusive.");

	}
	
	/**
	 * @return boolean a value indicating whether an automatic postback to the server
	 * will occur whenever the user modifies the slider value. Defaults to false.
	 */
	public function getAutoPostBack(){
		return $this->getViewState('AutoPostBack',false);
	}
	
	/**
	 * Sets the value indicating if postback automatically.
	 * An automatic postback to the server will occur whenever the user
	 * modifies the slider value.
	 * @param boolean the value indicating if postback automatically
	 */
	public function setAutoPostBack($value){
		$this->setViewState('AutoPostBack',TPropertyValue::ensureBoolean($value),false);
	}
	
	/**
	 * Returns the value of the EtkStarry control.
	 * This method is required by {@link IDataRenderer}.
	 * It is the same as {@link getStarRating}.
	 * @return string the value of the EtkStarry control.
	 * @see getStarRating
	 */
	public function getData(){
		return $this->getStarRating();
	}

	/**
	 * Sets the value of the EtkStarry control.
	 * This method is required by {@link IDataRenderer}.
	 * It is the same as {@link setStarRating}.
	 * @param string the value of the EtkStarry control.
	 * @see setStarRating
	 */
	public function setData($value){
		$this->setStarRating($value);
	}

	/**
	 * Returns the value to be validated.
	 * This methid is required by {@link IValidatable} interface.
	 * It is the same as {@link setStarRating}.
	 * @return mixed the value of the property to be validated.
	 */
	public function getValidationPropertyValue(){
		return $this->getStarRating();
	}

        public function getIsValid(){
            parent::getIsValid();
        }

        public function setIsValid($value){
            parent::setIsValid($value);
        }
}

/**
 * EtkStar class
 *
 * EtkStar display a star, and is used by {@link EtkStarry}.
 * 
 * All you need do is set the {@link getStarType StarType} property,
 * and this class takes care of the rest.
 * @author Bradley Booms <bradley.booms@gmail.com>
 * @author Edward Stow <edward@etk.com.au>
 */
class EtkStar extends TPanel{
	/**
	 * @var integer offsets of the image file.
	 * The actual pixel offset of the image is BackgroudOffset * 30px 
	 * which is calculated by {@link getBackgroudOffset} function.
	 */
	private $backgroundOffsets = array('dot' => 0, 'grey' => 1,
									   'standard' => 2, 'outline' => 3,
									   'half-grey' => 4, 'half-standard' => 5, ) ;
	
	/**
	 * Sets the default 
	 * 
	 * This method is invoked when the control enters 'OnInit' stage.
	 * The method raises 'OnInit' event.
	 * If you override this method, be sure to call the parent implementation
	 * so that the event handlers can be invoked.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onInit($param){
		if (!$this->getPage()->getIsPostBack() && !$this->getPage()->getIsCallback()){
			$this->setCssClass('standard-star');
			$this->setBackImageUrl($this->publishAsset("stars.gif"));
			
			$this->setWidth("30px");
			$this->setHeight("30px");
		}
	}
	
	/**
	 * Adds background-position and background-image style attributes.
	 * 
	 * Adds attribute name-value pairs to renderer.
	 * This method overrides the parent implementation with additional textbox specific attributes.
	 * @param THtmlWriter the writer used for the rendering purpose
	 */
	protected function addAttributesToRender($writer){
		parent::addAttributesToRender($writer);
		$offset = $this->getBackOffset();
		$writer->addStyleAttribute('background-position', "0 -{$offset}px" );
		$url=trim($this->getBackImageUrl());
		$writer->addStyleAttribute('background-image','url('.$url.')');
	}
	
	/**
	 * @return integer the pixel offset of the different 'stars' based on 
	 * the {@link getStarType StarType} property.
	 */
	protected function getBackOffset(){
		return $this->backgroundOffsets[$this->getStarType()]*30;
	}
	
	/**
	 * @return string the type of star to display, default 'dot',
	 * possible values ('dot', 'grey', 'standard', 'outline')
	 */
	public function getStarType(){
		return $this->getViewState('StarType', 'dot');
	}
	
	/**
	 * @param string the type of star to display,
	 * possible values ('dot', 'grey', 'standard', 'outline')
	 */
	public function setStarType($value){
		if (isset($this->backgroundOffsets[$value]))
			$this->setViewState('StarType', $value, 'dot');
	}
}

?>