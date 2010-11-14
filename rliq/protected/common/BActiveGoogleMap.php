<?php
/**
 * BActiveGoogleMap.php
 * 
 * @author Bradley Booms <Bradley.Booms@nsighttel.com>
 * @version Creation Date: Jun 7, 2007
 */

Prado::using('System.Web.UI.WebControls.TPanel');
prado::using('System.Web.Javascripts.TJSON');

/**
 * BActiveGoogleMap.php class
 * 
 * 
 * 
 * Properties
 * -
 * 
 * @author Bradley Booms <Bradley.Booms@nsighttel.com>
 * @version Modified Date: Jun 7, 2007
 * 
 * Modifications:
 */
class BActiveGoogleMap extends TPanel implements ICallbackEventHandler, IActiveControl{
	/**
	 * @var array center latitude and longitude of the map.
	 */
	private $_center = array(44.553278, -88.108981);
	
	/**
	 * @var array of names of control types.
	 */
	private $_mapControls = array('GLargeMapControl');
	
	/**
	 * @var string markerID
	 */
	private $_markerID = null;
		
	/**
	 * @var boolean whether the scroll wheel zooms the map.
	 */
	private $_scrollWheelZoom = false;
	
	/**
	 * @var TMap of all IActiveGoogleMapMarker items that have been sent to the client side.
	 */
	private $_sentMarkers = null;
	
	/**
	 * @var boolean whether items are restored from viewstate
	 */
	private $_stateLoaded = false;
	
	/**
	 * @var integer the zoom level of the map.
	 */
	private $_zoomLevel = 11;
	
	/**
	 * @var array the actual client bounds
	 */
	private $_bounds = null;
	
	/**
	 * @var string the Google Maps API Key,
	 * default 'ABQIAAAA26Hu8WCmkPWtVuygEWwhHxQ6BiwfRMUYoz7gZiatyH05CpWT4BSo0y96ChNHjUaHszyjaWHeT9DqvQ' for localhost.
	 */
	private $_apiKey = 'ABQIAAAA26Hu8WCmkPWtVuygEWwhHxQ6BiwfRMUYoz7gZiatyH05CpWT4BSo0y96ChNHjUaHszyjaWHeT9DqvQ';
		
	/**
	 * Adds object parsed from template to the control.
	 * This method adds only {@link TListItem} objects into the {@link getMarkers Markers collection.
	 * All other objects are ignored.
	 * @param mixed object parsed from template
	 */
	public function addParsedObject($object){
		// Do not add items from template if items are loaded from viewstate
		if(!$this->_stateLoaded && ($object instanceof IActiveGoogleMapMarker)){
			parent::addParsedObject($object);
		}
	}
	
	public function onInit($param){
		parent::onInit($param);
		$this->getPage()->setAdapter(new BMapPageAdapter($this->getPage()));
	}
	
	/**
	 * Raises the callback event. This method is required by {@link
	 * ICallbackEventHandler} interface. If {@link getCausesValidation
	 * CausesValidation} is true, it will invoke the page's {@link TPage::
	 * validate validate} method first. It will raise {@link onClick
	 * OnClick} event first and then the {@link onCallback OnCallback} event. 
	 * This method is mainly used by framework and control developers.
	 * @param TCallbackEventParameter the event parameter
	 */
 	public function raiseCallbackEvent($param){
		$json = new TJSON;
		$paramObj = $json->decode($param->getCallbackParameter());
		switch ($paramObj->eventType){
		case "__BActiveGoogleMap_OnMoveEnd__":
			// raise the event
			$eventParam = new TCallbackEventParameter($this->getResponse(), $paramObj->clientSide);
			$this->onMoveEnd($eventParam);
			break;
		case "__BActiveGoogleMap_OnMarkerChanged__":
			// raise the event
			$eventParam = new TCallbackEventParameter($this->getResponse(), $paramObj->markerID);
			$this->onMarkerChanged($eventParam);
			break;
		case "__BActiveGoogleMap_OnMarkerDrag__":
			$eventParam = new TCallbackEventParameter($this->getResponse(),new BMarkerDragEventParameter($paramObj->markerID,$paramObj->clientSide,$paramObj->lat,$paramObj->lng));
			$this->onMarkerDrag($eventParam);
			$eventParam = new TCallbackEventParameter($this->getResponse(), $paramObj->clientSide);
			$this->onMoveEnd($eventParam);
			break;
		case "__BActiveGoogleMap_OnClick__":
			$eventParam = new TCallbackEventParameter($this->getResponse(),new BMapClickEventParameter($paramObj->clientSide,$paramObj->lat,$paramObj->lng));
			$this->onClick($eventParam);
			break;
		}
	}

	/**
	 * Saves items into viewstate.
	 * This method is invoked right before control state is to be saved.
	 */
	public function saveState(){
		parent::saveState();
		$this->setViewState('ApiKey',$this->getApiKey(), 'ABQIAAAA26Hu8WCmkPWtVuygEWwhHxQ6BiwfRMUYoz7gZiatyH05CpWT4BSo0y96ChNHjUaHszyjaWHeT9DqvQ');
		$this->setViewState('Center',$this->getCenter(),array(44.553278, -88.108981));
		$this->setViewState('MapControls',$this->getMapControls(),array('GSmallMapControl'));
//		echo 'saveState:',$this->getMarker();
		$this->setViewState('MarkerID',$this->getMarkerID(),null);
		$this->setViewState('ScrollWheelZoom',$this->getScrollWheelZoom(),false);
		$this->setViewState('SentMarkers',$this->getSentMarkers(),null);
		$this->setViewState('ZoomLevel',$this->getZoomLevel(),11);
		$this->setViewState('Bounds', $this->getBounds(), array());
	}
	
	/**
	 * Loads items from viewstate.
	 * This method is invoked right after control state is loaded.
	 */
	public function loadState(){
		parent::loadState();
		if (($this->getPage()->getisCallback()) || ($this->getPage()->getIsPostBack())){
			$this->_apiKey = $this->setViewState('ApiKey', 'ABQIAAAA26Hu8WCmkPWtVuygEWwhHxQ6BiwfRMUYoz7gZiatyH05CpWT4BSo0y96ChNHjUaHszyjaWHeT9DqvQ');
			$this->_center = $this->getViewState('Center',array(44.553278, -88.108981));
			$this->_mapControls = $this->getViewState('MapControls',array('GSmallMapControl'));
			echo 'loadState:',$this->_markerID;
			$this->_markerID = $this->getViewState('MarkerID',null);
			echo 'after:',$this->_markerID;
			$this->_scrollWheelZoom = $this->getViewState('ScrollWheelZoom',false);
			$this->_sentMarkers = $this->getViewState('SentMarkers',null);
			$this->_zoomLevel = $this->getViewState('ZoomLevel',11);
			$this->_bounds = $this->getViewState('Bounds', array());
			$this->_stateLoaded = true;
		}
		$this->clearState();
	}
	
	/**
	 * Clears items from viewstate.
	 * This method is invoked right after control state is loaded.
	 */
	protected function clearState(){
		$this->clearViewState('ApiKey');
		$this->clearViewState('Center');
		$this->clearViewState('MapControls');
		$this->clearViewState('MarkerID');
		$this->clearViewState('ScrollWheelZoom');
		$this->clearViewState('SentMarkers');
		$this->clearViewState('ZoomLevel');
		$this->clearViewState('Bounds');
	}

	/**
	 * @return TList the marker list
	 */
	public function getMarkers(){
		return $this->getControls();
	}
	
	public function getMarker($markerID){
		// find the right control
		$marker = $this->findControlsByID($markerID);
		if (count($marker) == 1){
			$marker = $marker[0];
		} else {
			$marker = null;
		}
		return $marker;
	}
	
	/**
	 *@return TMap of all towers that have been sent to the client side, default empty TMap. 
	 */
	public function getSentMarkers(){
		if (!$this->_sentMarkers){
			$this->_sentMarkers = new TMap;
		}
		return $this->_sentMarkers;
	}
	
	/**
	 * @return string Google Maps API Key.
	 * Visit http://www.google.com/apis/maps/signup.html to get an API Key for your domain.
	 */
	public function getApiKey(){
		return $this->_apiKey;
	}
	
	/**
	 * @param string Google Maps API Key.
	 * Visit http://www.google.com/apis/maps/signup.html to get an API Key for your domain.
	 */
	public function setApiKey($value){
		$this->_apiKey = TPropertyValue::ensureString($value);
	}
	
	/**
	 * @return array(latitude, longitude), default array(44.553278, -88.108981) (Green Bay, WI)
	 */
	public function getCenter(){
		return $this->_center;
	}
	
	/**
	 * @param array/string/float 
	 * 		array format (latitude, longitude),
	 * 		string format "(latitude, longitude)",
	 * 		float latitude (must specify longitude parameter as well)
	 * @param float longitude (ignored unless both parameters are float values)
	 * 	default array(44.553278, -88.108981) (Green Bay, WI)
	 */
	public function setCenter($value, $lon=null){
		if ((is_float($value) && is_float($lon))){
			$value = array($value, $lon);
		} else {
			$value = TPropertyValue::ensureArray($value);
		}
		if (count($value) == 2){
			$this->_center = $value;
			if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
				$this->getClient()->setCenter($this, $this->getCenter());
			}
		}
	}
	
	public function getClient(){
		return $this->getPage()->getCallbackClient();
	}
	
	/**
	 * @return boolean whether the map should zoom with the scroll wheel.
	 */
	public function getScrollWheelZoom(){
		return $this->_scrollWheelZoom;
	}
	
	/**
	 * @param boolean whether the map should zoom with the scroll wheel.
	 */
	public function setScrollWheelZoom($value){
		$this->_scrollWheelZoom = TPropertyValue::ensureBoolean($value);
		if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
			$this->getClient()->setScrollWheelZoom($this, $this->getScrollWheelZoom());
		}
	}
	
	/**
	 * @return integer the current zoom level of the map.
	 */
	public function getZoomLevel(){
		return $this->_zoomLevel;
	}
	
	/**
	 * @param integer the current zoom level of the map.
	 */
	public function setZoomLevel($value){
		$this->_zoomLevel = TPropertyValue::ensureInteger($value);
		if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
			$this->getClient()->setZoomLevel($this, $this->getZoomLevel());
		}
	}
	
	/**
	 * @return array the current boundaries of the map.
	 */
	public function getBounds(){
		return $this->_bounds;
	}
	
	/**
	 * @param array the current boundaries of the map.
	 */
	public function setBounds($value){
		$this->_bounds = TPropertyValue::ensureArray($value);
		if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
			$this->getClient()->setBounds($this, $this->getBounds());
		}
	}
	
	/**
	 * @return array the controls to be displayed on the map.
	 */
	public function getMapControls(){
		return $this->_mapControls;
	}
	
	/**
	 * @param array/string of the form "(control1,control2,control3)".
	 * Currently supported controls
	 *  * GLargeMapControl
	 *  * GMapTypeControl
	 *  * GOverviewMapControl
	 *  * GScaleControl
	 *  * GSmallMapControl
	 *  * GSmallZoomControl
	 */
	public function setMapControls($value){
		$this->_mapControls = TPropertyValue::ensureArray($value);
		if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
			$this->getClient()->setMapControls($this, $this->getMapControls());
		}
	}
	
	/**
	 * @return integer the id of the currently selected Marker object, default 0.
	 */
	public function getMarkerID(){
		return $this->_markerID;
	}
	
	/**
	 * @param integer the id of the currently selected Marker object.
	 */
	protected function setMarkerID($value){
		if ($this->_markerID !== $value){
			$this->_markerID = $value;
			if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
				$this->getClient()->setMarkerID($this,$this->getMarkerID());
			}
		}
	}
	
	/**
	 * Raises <b>onMarkerClicked</b> event.
	 * This method is invoked when a new marker is selected by the user.
	 * If you override this method, be sure to call the parent implementation to ensure
	 * the invocation of the attached event handlers.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onMarkerChanged($param){
		$this->openInfoWindow($param->getCallbackParameter());
		$this->raiseEvent('OnMarkerChanged', $this, $param);
	}
	
	/**
	 * Opens the info window of the currently selected marker.
	 * @param markerID optional
	 */
	public function openInfoWindow($markerID = null){
		if ($markerID === null){
			$markerID = $this->getMarkerID();
		} else {
			$this->setMarkerID($markerID);
		}
		
		$marker = $this->getMarker($markerID);

		// if it hasn't been sent yet, add it.
		$this->addMarker($marker);
		$marker->openInfoWindow($this->getPage()->getResponse()->createHtmlWriter());
	}
	
	/**
	 * Closes the info window of the currently selected marker.
	 * @param markerID optional
	 */
	public function closeInfoWindow($markerID = null){
		if($this->getActiveControl()->canUpdateClientSide()&& $this->getHasLoadedPostData()){
			$this->getClient()->closeInfoWindow($this);
		}
	}
	
	/**
	 * Raises <b>OnMoveEnd</b> event.
	 * This method is invoked when the map has been repositioned.
	 * If you override this method, be sure to call the parent implementation to ensure
	 * the invocation of the attached event handlers.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onMoveEnd($param){
		// get markers to return
		$clientSide = $param->getCallbackParameter();
		$this->_center = $clientSide->center;
		$this->_zoomLevel = $clientSide->zoomLevel;
		$this->_bounds = $clientSide->bounds;
		
		$markers = $this->getMarkersInBounds($clientSide->bounds);
		
		if($this->getActiveControl()->canUpdateClientSide()&& $this->getHasLoadedPostData()){
			foreach ($markers as $marker){
				$this->addMarker($marker);
			}
		}
		$this->raiseEvent('OnMoveEnd', $this, $param);
	}
	
	public function addMarker($marker){
		if (($marker instanceof BActiveGoogleMapMarker) && (!$this->getSentMarkers()->itemAt($marker->getID()))){
			$this->getSentMarkers()->add($marker->getID(), true);
			$options = $marker->getOptions();
			$this->getClient()->addMarker($this, $options);
		}
	}
	
	/**
	 * Called during <b>OnMoveEnd</b> event to find only the sites that are in the current 
	 * boundaries of the map.
	 * @param  array, boundaries as returned from client side.
	 * 					(min latitude,
	 * 					max latitude,
	 * 					min longitude,
	 * 					max longitude)
	 * @return TMap of IActiveGoogleMapMarker objects
	 */
	protected function getMarkersInBounds($bounds){
		
		$data = new TMap;
		foreach ($this->getMarkers() as $key => $item){
			if (($item instanceof BActiveGoogleMapMarker)) {
				if ( $item->getIsInBounds($bounds) ){
					$data[$key] = $item;
				}
			}
		}
		return $data;
	}
	
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
	 */
	public function getActiveControl(){
		return $this->getAdapter()->getBaseActiveControl();
	}

	/**
	 * Renders the javascript for div tag.
	 */
	protected function addAttributesToRender($writer){
		parent::addAttributesToRender($writer);
		$writer->addAttribute('id',$this->getClientID());
		
		$this->getPage()->getClientScript()->registerPradoScript('prado');
		$this->getPage()->getClientScript()->registerPradoScript('ajax');
		$this->getPage()->getClientScript()->registerScriptFile('GoogleAjaxApi','http://www.google.com/jsapi?key='.$this->getApiKey());
		$this->getPage()->getClientScript()->registerScriptFile('B',$this->publishAsset("assets/B.js", __CLASS__));
   		$this->getPage()->getClientScript()->registerScriptFile('BActiveGoogleMap',$this->publishAsset("assets/BActiveGoogleMap.js", __CLASS__));
		//$this->getPage()->getClientScript()->registerScriptFile('GMap.js','http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$this->getApiKey());
		//$url = $this->getPage()->getClientScript()->registerJavascriptPackages(dirname(__FILE__) . "/assets",array('activegooglemap'));
		//$this->getPage()->getClientScript()->registerScriptFile($url, $url);
		
		$this->getActiveControl()->registerCallbackClientScript($this->getClientClassName(),$this->getClientOptions());
	}

	/**
	 * Gets the client side options for this control.
	 * @return array (	ID => client ID,
	 * 					EventTarget => unique ID,
	 * 					onLoading => loading javascriupt function,
	 * 					onComplete => complete javascript function,
	 * 					purpleMarkerURL => url of published asset)
	 */
	protected function getClientOptions(){
		$options['ID'] = $this->getClientID();
		$options['EventTarget'] = $this->getUniqueID();
		$options['Center'] = $this->getCenter();
		$options['ZoomLevel'] = $this->getZoomLevel();
		$options['Controls'] = $this->getMapControls();
		$options['ScrollWheelZoom'] = $this->getScrollWheelZoom();
		return $options;
	}

	/**
	 * @return string corresponding javascript class name for this control.
	 */
	protected function getClientClassName(){
		return 'B.ActiveGoogleMap';
	}
	
	public function getClientMarkers() {
		return $this->getMarkersInBounds($this->_bounds);
	}
	
	/**
	 * Raises <b>onMarkerDrag</b> event.
	 * This method is invoked when a marker is drag by the user.
	 * If you override this method, be sure to call the parent implementation to ensure
	 * the invocation of the attached event handlers.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onMarkerDrag($param){
		$this->raiseEvent('OnMarkerDrag', $this, $param->getCallbackParameter());
	}
	
	/**
	 * Raises <b>onClick</b> event.
	 * This method is invoked when the map is clicked by the user.
	 * If you override this method, be sure to call the parent implementation to ensure
	 * the invocation of the attached event handlers.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onClick($param){
		$this->raiseEvent('OnClick', $this, $param->getCallbackParameter());
	}
}

/**
 * IActiveGoogleMapMarker interface
 * 
 * extends TPanel class.
 * 
 * Properties
 * -
 * 
 * @author Bradley Booms <Bradley.Booms@nsighttel.com>
 * @version Modified Date: Jun 7, 2007
 */
interface IActiveGoogleMapMarker{
	public function getID();
	public function getClientID();
	
	public function getName();
	public function getPoint();
	/**
	 * @param  array, boundaries as returned from client side.
	 * 					(min latitude,
	 * 					max latitude,
	 * 					min longitude,
	 * 					max longitude)
	 */
	public function getIsInBounds($bounds);
	public function getLatitude();
	public function getLongitude();
	
}


/**
 * BActiveGoogleMapMarker class
 * 
 * extends TPanel class.
 * 
 * Properties
 * -
 * 
 * @author Bradley Booms <Bradley.Booms@nsighttel.com>
 * @version Modified Date: Jun 7, 2007
 */
class BActiveGoogleMapMarker extends TActivePanel implements IActiveGoogleMapMarker{
	public function getName(){
		return $this->getViewState('Name', $this->getID());
	}
	
	public function setName($value){
		$this->setViewState('Name', $value, $this->getID());
	}
	
	public function getTitle(){
		return $this->getViewState('Title', $this->getName());
	}
	
	public function setTitle($value){
		$this->setViewState('Title', $value, '');
	}
	
	public function getPoint(){
		return $this->getViewState('Point', array(0,0));
	}
	
	public function setPoint($value){
		$value = TPropertyValue::ensureArray($value);
		if ( count($value) == 2 ){
			$this->setViewState('Point', $value, array(0,0));
		}
		else
			throw new TInvalidDataValueException('bactivegooglemapmarker_point_invalid',get_class($this));
	}
	
	public function getDraggable(){
		return $this->getViewState('Draggable',false);
	}
	
	public function setDraggable($value){
		$value = TPropertyValue::ensureBoolean($value);
		$this->setViewState('Draggable',$value);
		if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
			$this->getParent()->getClient()->setMarkerDragging($this, $value);
		}
		
	}
	
	/**
	 * @param  array, boundaries as returned from client side.
	 * 					(min latitude,
	 * 					max latitude,
	 * 					min longitude,
	 * 					max longitude)
	 */
	 public function getIsInBounds($bounds){
	 	$ret = false;
		if ( ($this->getLatitude() >= $bounds[0]) &&
			($this->getLatitude() <= $bounds[1]) && 
			($this->getLongitude() >= $bounds[2]) && 
			($this->getLongitude() <= $bounds[3]) ){
			$ret = true;
		}
		return $ret;
	}
	
	public function getLatitude(){
		$point = $this->getPoint();
		return $point[0];
	}
	
	public function getLongitude(){
		$point = $this->getPoint();
		return $point[1];
	}
	
	public function getOptions(){
		return array($this->getID(), $this->getName(), 
				$this->getPoint(), $this->getClientID(),
				$this->getTitle(),$this->getDraggable());
	}
	
	/**
	 * Renders and replaces the panel's content on the client-side.
	 * When render() is called before the OnPreRender event, such as when render()
	 * is called during a callback event handler, the rendering
	 * is defered until OnPreRender event is raised.
	 * @param THtmlWriter html writer
	 */
	public function openInfoWindow($writer){
		$this->render($writer);
		if($this->getActiveControl()->canUpdateClientSide()){
			$this->getPage()->getCallbackClient()->openInfoWindow($this, $writer);
		}
	}

	/**
	 * Renders marker only on callback process
	 */	
	public function render ($writer) {
		if (($this->getPage()->getisCallback()) || ($this->getPage()->getIsPostBack())){
			parent::render($writer);
		}
	}
	
	public function getVisible(){
		return $this->getViewState('BVisible',true);
	}
	
	public function setVisible($value){
		$value = TPropertyValue::ensureBoolean($value);
		$this->setViewState('BVisible', $value);
		if($this->getActiveControl()->canUpdateClientSide() && $this->getHasLoadedPostData()){
			$this->getParent()->getClient()->setMarkerVisible($this, $value);
		}
	}
	
}

class BMapPageAdapter extends TActivePageAdapter{
	/**
	 * @var TCallbackClientScript callback client script handler
	 */
	private $_callbackClient;
	
	/**
	 * Gets the callback client script handler. It handlers the javascript functions
	 * to be executed during the callback response.
	 * @return TCallbackClientScript callback client handler.
	 */
	public function getCallbackClientHandler()
	{
		if(is_null($this->_callbackClient))
			$this->_callbackClient = new BMapCallbackClientScript;
		return $this->_callbackClient;
	}
}

class BMapCallbackClientScript extends TCallbackClientScript{
	/**
	 * Open the info window of the current marker with new content 
	 * contained in writer.
	 * @param TControl control element or HTML element id.
	 * @param string HTML fragement or the control to be rendered
	 */
	public function openInfoWindow($element, $content){
		if ($element instanceof IActiveGoogleMapMarker){
			$element = $element->getParent();
		}
		$this->replace($element, $content, 'B.ActiveGoogleMaps.openInfoWindow');
	}
	
	public function closeInfoWindow($element){
		if ($element instanceof IActiveGoogleMapMarker){
			$element = $element->getParent();
		}
		$this->callClientFunction('B.ActiveGoogleMaps.closeInfoWindow', array($element));
	}
	
	public function setMarkerID($element,$value){
		$this->callClientFunction('B.ActiveGoogleMaps.setMarkerID', array($element, $value));
	}
	
	public function addMarker($element,$value){
		$this->callClientFunction('B.ActiveGoogleMaps.addMarker', array($element, $value));
	}
	
	public function setZoomLevel($element, $value){
		$this->callClientFunction('B.ActiveGoogleMaps.setZoom', array($element, $value));
	}
	
	public function setBounds($element, $value){
		$this->callClientFunction('B.ActiveGoogleMaps.setBounds', array($element, $value));
	}
	
	public function setScrollWheelZoom($element, $value){
		$this->callClientFunction('B.ActiveGoogleMaps.setScrollWheelZoom', array($element, $value));
	}
	
	public function setMapControls($element, $value){
		$this->callClientFunction('B.ActiveGoogleMaps.setControls', array($element, $value));
	}
	
	public function setCenter($element, $value){
		$this->callClientFunction('B.ActiveGoogleMaps.setCenter', array($element, $value));
	}
	
	public function setMarkerVisible($element, $value) {
		$map = $element->getParent();
		$this->callClientFunction('B.ActiveGoogleMaps.setMarkerVisible',array($map,$element->getID(),$value));
	}
	public function setMarkerDragging($element, $value) {
		$map = $element->getParent();
		$this->callClientFunction('B.ActiveGoogleMaps.setMarkerDragging',array($map,$element->getID(),$value));
	}
}

class BMarkerDragEventParameter extends TEventParameter {
	var $markerID;
	var $point;
	var $clientSide;
	
	public function __construct($markerID,$clientSide,$lat,$lng){
		$this->markerID = $markerID;
		$this->point = array($lat,$lng);
		$this->clientSide = $clientSide;
	}
	
}

class BMapClickEventParameter extends TEventParameter {
	var $point;
	var $clientSide;
	
	public function __construct($clientSide,$lat,$lng){
		$this->point = array($lat,$lng);
		$this->clientSide = $clientSide;
	}
}
?>