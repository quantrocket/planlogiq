<?php
/**
 * PWCWindow class.
 *
 * PWCWindow component is a wrapper for JavaScript "Prototype Window Class"
 * by Sebastien Gruhier.
 * See the project site at http://prototype-window.xilinus.com/.
 * It allows you to display visually appealing windows on top of your page
 * without worrying to include all the js and CSS files.
 * 
 * It does not cover all the options that PWC provides. It is meant to make 
 * creating a basic window as simple as possible.
 *
 * Usage:
 *
 * Provided that you have loaded the class file either by entering the path to PWCWindow
 * class file in your paths configuration or by calling Prado::using('Path.to.PWCWindowClassFile')
 * you can create PWC Window using syntax:
 *
 * <com:PWCWindow ID="Window1">
 * </com:PWCWindow>
 * Available options are:
 * Title - text displayed in title bar
 * Theme - graphical theme to be used; bundled are:
 * 		dialog (default)
 *		alphacube
 *		bluelighting
 *		darkbluelighting
 *		greenligthing
 *		greylighting
 *		darkX
 *		mac_os_x
 *		nuncio
 *		spread
 * Content - the window content interpreted according to Mode
 * Mode - can be one of:
 *		HTML - the Content property value is pasted directly into the window
 *		Url - the window displays a page with url defined in content (using iframe)
 *		Existing - DOM element with ID specified by Content is displayed in window
 *		'' (empty string or not set) - no content is displayed in window
 *					this is useful if you want to set the window's content by yourself using javascript
 * Width, Height, Top, Bottom, Left, Right - dimensions and position
 * DestroyOnClose - whether the window should be destroyed on closing, useful when the window contains
 *		a movie or music
 * AutoResize, AutoPosition - resize,recenter window when browser size has changed
 *
 * To show/hide PWCWindow on clientside use Windows.show(windowId,modal),
 * Windows.showCenter(windowId,modal) and Windows.hide() functions.
 *
 * Since PWCWindow uses Prado.Registry on the Clientside, it works with Prado 3.1.5 and above
 * The window.js file is slightly modified, so don't replace the version bundled with this component.
 *
 * @author mp (find_me@pradosoft.com_forum)
 */
	class PWCWindow extends TWebControl
	{
		
		function getTop(){
			return $this->getViewState('Top');
		}
		function setTop($value){
			$this->setViewState('Top',$value,'');
		}
		function getBottom(){
			return $this->getViewState('Bottom');
		}
		function setBottom($value){
			$this->setViewState('Bottom',$value,'');
		}
		function getLeft(){
			return $this->getViewState('Left');
		}
		function setLeft($value){
			$this->setViewState('Left',$value,'');
		}
		function getRight(){
			return $this->getViewState('Right');
		}
		function setRight($value){
			$this->setViewState('Right',$value,'');
		}
		function getTitle(){
			return $this->getViewState('Title');
		}
		function setTitle($value){
			$this->setViewState('Title',$value,'');
		}
		function getTheme(){
			return $this->getViewState('Theme','dialog');
		}
		function setTheme($value){
			$this->setViewState('Theme',$value);
		}
		function getWidth(){
			return $this->getViewState('Width',400);
		}
		function setWidth($value){
			$this->setViewState('Width',$value);
		}
		function getHeight(){
			return $this->getViewState('Height',300);
		}
		function setHeight($value){
			$this->setViewState('Height',$value);
		}
		function getText(){
			return $this->getViewState('Text',300);
		}
		function setText($value){
			$this->setViewState('Text',$value);
		}
		function getMode(){
			return $this->getViewState('Mode','Text');
		}
		function setMode($value){
			$this->setViewState('Mode',$value);
		}
		function getContent(){
			return $this->getViewState('Content','');
		}
		function setContent($value){
			$this->setViewState('Content',$value);
		}
		function getAutoResize(){
			return $this->getViewState('AutoResize','false');
		}
		function setAutoResize($value){
			$this->setViewState('AutoResize',$value,'false');
		}
		function getAutoPosition(){
			return $this->getViewState('AutoPosition','false');
		}
		function setAutoPosition($value){
			$this->setViewState('AutoPosition',$value,'false');
		}
		function getDestroyOnClose(){
			//echo '|viewstate:'.$this->getViewState('DestroyOnClose','false');
			return $this->getViewState('DestroyOnClose','false');
		}
		function setDestroyOnClose($value){
			$this->setViewState('DestroyOnClose',$value,'false');
		}
		public function onPreRender($param)
		{
			parent::onPreRender($param);
			if (!$this->getPage()->getIsCallback())
			{
				$this->registerStyleSheet();
				$this->registerClientScript();
			}
		}
		
		protected function getClientOptions()
		{
			//$options['id']=$this->getClientId();
			$options['className']=$this->getTheme();
			$options['title']=$this->getTitle();
			$options['width']=$this->getWidth();
			$options['height']=$this->getHeight();
			if ($this->getDestroyOnClose()==='True') $options['destroyOnClose']='true';
			if (($d=$this->getLeft())!==null) $options['left']=$d;
			if (($d=$this->getRight())!==null) $options['right']=$d;
			if (($d=$this->getTop())!==null) $options['top']=$d;
			if (($d=$this->getBottom())!==null) $options['bottom']=$d;
			//$options['parent']='test';
			return $options;
		}
		protected function registerStyleSheet()
		{
			$path=dirname(__FILE__).DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;
			$am=$this->getApplication()->getAssetManager();
			$assetspath=$am->getBasePath().DIRECTORY_SEPARATOR.'PWC'.DIRECTORY_SEPARATOR;
			$assets=$am->getBaseUrl().'/PWC/';
			$am->copyDirectory($path,$assetspath);
			$defcss=$assets.'default.css';
			$cs=$this->getPage()->getClientScript();
			$cs->registerStyleSheetFile($defcss,$defcss);
			if ($this->getTheme()!=='dialog')
			{
				$themecss=$assets.$this->getTheme().'.css';
				$cs->registerStyleSheetFile($themecss,$themecss);
			}
		}
		protected function registerClientScript()
		{
			$path='javascripts'.DIRECTORY_SEPARATOR;
			$cs=$this->getPage()->getClientScript();
			//$cs->registerBeginScript('PWCInitJS','var PWCInitJS="";');
			if(!$cs->isScriptFileRegistered('PWCWindow'))
			{
				$cs->registerPradoScript('effects');
				$cs->registerScriptFile('PWCWindow',$this->publishAsset($path.'window.js'));
				//$cs->registerScriptFile('PWCPrado',$this->publishAsset($path.'pwc-prado.js'));
			}
			$options=$this->getClientOptions();
			switch ($this->getMode())
			{
				case 'Existing':
					$endJS2="Prado.Registry.get('$this->ClientID').setContent('$this->Content',$this->AutoResize,$this->AutoPosition);\";";
				break;
				case 'Url':
					$options['url']=$this->getContent();
					$endJS2='"';
				break;
				case 'HTML':
					$escaped=str_replace(array('"',"\n","\r","\t"),array('\"'),$this->Content);
					$endJS2="Prado.Registry.get('$this->ClientID').setHTMLContent('$escaped');\";";
				break;
				default:
					$endJS2='"';
				break;
			}
			$options=TJavaScript::encode($options);
			$endJS="Windows.codes['$this->ClientID']=\"Prado.Registry.set('$this->ClientID', new Window('$this->ClientID',{$options}));".$endJS2;
			$cs->registerEndScript('PWCCode:'.$this->getClientID(),$endJS);
		}
		public function render($writer)
		{
		}
	}
?>