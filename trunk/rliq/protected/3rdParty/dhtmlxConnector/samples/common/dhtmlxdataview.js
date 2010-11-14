/*
2010 May 24
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms please contact us at sales@dhtmlx.com
*/




/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//dhtmlx.js'*/


/*
	Common helpers
*/

if (!window.dhtmlx) 
	dhtmlx={};
dhtmlx.version="3.0";
dhtmlx.codebase="./";

//coding helpers

//copies methods and properties from source to the target
dhtmlx.extend = function(target, source){
	for (var method in source)
		target[method] = source[method];
	//if source object has init code - call init against target
	if (source._init)	
		target._init();
	return target;	
};
//creates function with specified "this" pointer
dhtmlx.bind=function(functor, object){ 
	return function(){ return functor.apply(object,arguments); };  
};

//loads module from external js file
dhtmlx.require=function(module){
	if (!dhtmlx._modules[module]){
		dhtmlx.assert(dhtmlx.ajax,"load module is required");
		
		//load and exec the required module
		dhtmlx.exec( dhtmlx.ajax().sync().get(dhtmlx.codebase+module).responseText );
		dhtmlx._modules[module]=true;	
	}
};
dhtmlx._modules = {};	//hash of already loaded modules

//evaluate javascript code in the global scoope
dhtmlx.exec=function(code){
	if (window.execScript)	//special handling for IE
		window.execScript(code);
	else window.eval(code);
};

/*
	creates method in the target object which will transfer call to the source object
	if event parameter was provided , each call of method will generate onBefore and onAfter events
*/
dhtmlx.methodPush=function(object,method,event){
	return function(){
		var res = false;
		if (!event || this.callEvent("onBefore"+event,arguments)){
			res=object[method].apply(object,arguments);
			if (event) this.callEvent("onAfter"+event,arguments);
		}
		return res;	//result of wrapped method
	};
};
//check === undefined
dhtmlx.isNotDefined=function(a){
	return typeof a == "undefined";
};
//delay call to after-render time
dhtmlx.delay=function(method, obj, params){
	setTimeout(function(){
		var ret = method.apply(obj,params);
		method = obj = params = null;
		return ret;
	},1);
};

//common helpers

//generates unique ID (unique per window, nog GUID)
dhtmlx.uid = function(){
	if (!this._seed) this._seed=(new Date).valueOf();	//init seed with timestemp
	return this._seed++;
};
//resolve ID as html object
dhtmlx.toNode = function(node){
	if (typeof node == "string") return document.getElementById(node);
	return node;
};
//adds extra methods for the array
dhtmlx.toArray = function(array){ 
	return dhtmlx.extend((array||[]),dhtmlx.PowerArray);
};
//resolve function name
dhtmlx.toFunctor=function(str){ 
	return (typeof(str)=="string") ? eval(str) : str; 
};

//dom helpers

//hash of attached events
dhtmlx._events = {};
//attach event to the DOM element
dhtmlx.event=function(node,event,handler,master){
	node = dhtmlx.toNode(node);
	
	var id = dhtmlx.uid();
	dhtmlx._events[id]=[node,event,handler];	//store event info, for detaching
	
	if (master) 
		handler=dhtmlx.bind(handler,master);	
		
	//use IE's of FF's way of event's attaching
	if (node.addEventListener)
		node.addEventListener(event, handler, false);
	else if (node.attachEvent)
		node.attachEvent("on"+event, handler);

	return id;	//return id of newly created event, can be used in eventRemove
};

//remove previously attached event
dhtmlx.eventRemove=function(id){
	
	if (!id) return;
	dhtmlx.assert(this._events[id],"Removing non-existing event");
		
	var ev = dhtmlx._events[id];
	//browser specific event removing
	if (ev[0].removeEventListener)
		ev[0].removeEventListener(ev[1],ev[2],false);
	else if (ev[0].detachEvent)
		ev[0].detachEvent("on"+ev[1],ev[2]);
		
	delete this._events[id];	//delete all traces
};


//debugger helpers
//anything starting from error or log will be removed during code compression

//add message in the log
dhtmlx.log = function(type,message,details){
	if (window.console && console.log){
		type=type.toLowerCase();
		if (window.console[type])
			window.console[type](message);
		else
			window.console.log(type +": "+message);
		if (details) 
			window.console.log(details);
	}	
};
//register rendering time from call point 
dhtmlx.log_full_time = function(name){
	dhtmlx._start_time_log = new Date();
	dhtmlx.log("Info","Timing start ["+name+"]");
	window.setTimeout(function(){
		var time = new Date();
		dhtmlx.log("Info","Timing end ["+name+"]:"+(time.valueOf()-dhtmlx._start_time_log.valueOf())/1000+"s");
	},1);
};
//register execution time from call point
dhtmlx.log_time = function(name){
	var fname = "_start_time_log"+name;
	if (!dhtmlx[fname]){
		dhtmlx[fname] = new Date();
		dhtmlx.log("Info","Timing start ["+name+"]");
	} else {
		var time = new Date();
		dhtmlx.log("Info","Timing end ["+name+"]:"+(time.valueOf()-dhtmlx[fname].valueOf())/1000+"s");
		dhtmlx[fname] = null;
	}
};
//log message with type=error
dhtmlx.error = function(message,details){
	dhtmlx.log("Error",message,details);
};
//check some rule, show message as error if rule is not correct
dhtmlx.assert = function(test, message){
	if (!test)	dhtmlx.error(message);
};
//register names of event, which can be triggered by the object
dhtmlx.assert_event = function(obj, evs){
	if (obj._event_check)
		for (var a in evs)
			obj._event_check[evs[a]]=true;
};
//register names of properties, which can be used in object's configuration
dhtmlx.assert_property = function(obj, evs){
	if (!obj._settings_check)
		obj._settings_check={};
	dhtmlx.extend(obj._settings_check, evs);		
};
//check all options in collection, against list of allowed properties
dhtmlx.assert_check = function(data,coll){
	if (typeof data == "object"){
		for (var key in data){
			dhtmlx.assert_settings(key,data[key],coll);
		}
	}
};
//check if type and value of property is the same as in scheme
dhtmlx.assert_settings = function(mode,value,coll){
	coll = coll || this._settings_check;
	//if value is not in collection of defined ones
	if (coll && (!coll[mode] || !coll[mode][value])){
		if (!coll[mode])	//not registered property
			return dhtmlx.error("Unknown propery: "+mode);
		if (!coll[mode].__type)	//property limited by type only
			dhtmlx.error("Invalid setting: "+mode+"="+value);
		else {
			types =coll[mode].__type;
			if (typeof types == "string")	//set of types can be defined as array or comma separated string
				types = types.split(",");
				
			var valid = false;				
			for (var i=0; i < types.length; i++) {
				//check if value is of registered type
				switch(types[i]){
					case "integer":
						if (parseInt(value,10)==value) valid = true;
						break;
					case "node":
						if (typeof value == "object" && value.tagName) valid = true;
						break;
					default:	//string, function, number, object
						if (typeof value == types[i])  valid = true;
						break;
				}
			}
			if (!valid)
				dhtmlx.error("Invalid setting: "+mode+"="+value);
			
		}
	}
};

//event system
dhtmlx.EventSystem={
	_init:function(){
		this._events = {};		//hash of event handlers, name => handler
		this._handlers = {};	//hash of event handlers, ID => handler
	},
	//temporary block event triggering
	block : function(){
		this._events._block = true;
	},
	//re-enable event triggering
	unblock : function(){
		this._events._block = false;
	},
	//trigger event
	callEvent:function(type,params){
		if (this._events._block) return true;
		
		var event_stack =this._events[type.toLowerCase()];	//all events for provided name
		var return_value = true;

		if (dhtmlx.debug)	//can slowdown a lot
			dhtmlx.log("Info","["+this.name+"] event:"+type,params);
		
		if (event_stack)
			for(var i=0; i<event_stack.length; i++)
				/*
					Call events one by one
					If any event return false - result of whole event will be false
					Handlers which are not returning anything - counted as positive
				*/
				if (event_stack[i].apply(this,(params||[]))===false) return_value=false;
				
		return return_value;
	},
	//assign handler for some named event
	attachEvent:function(type,functor,id){
		type=type.toLowerCase();
		
		if (this._event_check && !this._event_check[type]) 
			dhtmlx.error("Incorrect event name: "+type);
		
		id=id||dhtmlx.uid(); //ID can be used for detachEvent
		functor = dhtmlx.toFunctor(functor);	//functor can be a name of method

		var event_stack=this._events[type]||dhtmlx.toArray();
		//save new event handler
		event_stack.push(functor);
		this._events[type]=event_stack;
		this._handlers[id]={ f:functor,t:type };
		
		return id;
	},
	//remove event handler
	detachEvent:function(id){
		var type=this._handlers[id].t;
		var functor=this._handlers[id].f;
		
		//remove from all collections
		var event_stack=this._events[type];
		event_stack.remove(functor);
		delete this._handlers[id];
	} 
};

//array helper
//can be used by dhtmlx.toArray()
dhtmlx.PowerArray={
	//remove element at specified position
	removeAt:function(pos,len){
		if (pos>=0) this.splice(pos,(len||1));
	},
	//find element in collection and remove it 
	remove:function(value){
		this.removeAt(this.find(value));
	},	
	//add element to collection at specific position
	insertAt:function(data,pos){
		if (!pos && pos!=0) 	//add to the end by default
			this.push(data);
		else {	
			var b = this.splice(pos,(this.length-pos));
  			this[pos] = data;
  			this.push.apply(this,b); //reconstruct array without loosing this pointer
  		}
  	},  	
  	//return index of element, -1 if it doesn't exists
  	find:function(data){ 
  		for (i=0; i<this.length; i++) 
  			if (data==this[i]) return i; 	
  		return -1; 
  	},
  	//execute some method for each element of array
  	each:function(functor,master){
		for (var i=0; i < this.length; i++)
			functor.call((master||this),this[i]);
	},
	//create new array from source, by using results of functor 
	map:function(functor,master){
		for (var i=0; i < this.length; i++)
			this[i]=functor.call((master||this),this[i]);
		return this;
	}
};

//environment detection
if (navigator.userAgent.indexOf('Opera') != -1)
	dhtmlx._isOpera=true;
else{
	//very rough detection, but it is enough for current goals
	dhtmlx._isIE=!!document.all;
	dhtmlx._isFF=!document.all;
	dhtmlx._isWebKit=(navigator.userAgent.indexOf("KHTML")!=-1);
	if (navigator.appVersion.indexOf("MSIE 8.0")!= -1 && document.compatMode != "BackCompat") 
		dhtmlx._isIE=8;
}


//store maximum used z-index
dhtmlx.zIndex={ drag : 10000 };

//html helpers
dhtmlx.html={
	create:function(name,attrs,html){
		attrs = attrs || {};
		var node = document.createElement(name);
		for (var name in attrs)
			node.setAttribute(name, attrs[name]);
		if (attrs.style)
			node.style.cssText = attrs.style;
		if (attrs["class"])
			node.className = attrs["class"];
		if (html)
			node.innerHTML=html;
		return node;
	},
	//return node value, different logic for different html elements
	getValue:function(node){
		node = dhtmlx.toNode(node);
		if (!node) return "";
		return dhtmlx.isNotDefined(node.value)?node.innerHTML:node.value;
	},
	//remove html node, can process an array of nodes at once
	remove:function(node){
		if (node instanceof Array)
			for (var i=0; i < node.length; i++)
				this.remove(node[i]);
		else
			if (node && node.parentNode)
				node.parentNode.removeChild(node);
	},
	//insert new node before sibling, or at the end if sibling doesn't exist
	insertBefore: function(node,before,rescue){
		if (!node) return;
		if (before)
			before.parentNode.insertBefore(node, before);
		else
			rescue.appendChild(node);
	},
	//return custom ID from html element 
	//will check all parents starting from event's target
	locate:function(e,id){
		e=e||event;
		var trg=e.target||e.srcElement;
	
		while (trg && trg.getAttribute){
				var test = trg.getAttribute(id);
				if (test) return test;
				trg=trg.parentNode;
		}	
		return null;
	},
	//returns position of html element on the page
	offset:function(elem) {
		if (elem.getBoundingClientRect) { //HTML5 method
			var box = elem.getBoundingClientRect();
			var body = document.body;
			var docElem = document.documentElement;
			var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop;
			var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
			var clientTop = docElem.clientTop || body.clientTop || 0;
			var clientLeft = docElem.clientLeft || body.clientLeft || 0;
			var top  = box.top +  scrollTop - clientTop;
			var left = box.left + scrollLeft - clientLeft;
			return { y: Math.round(top), x: Math.round(left) };
		} else { //fallback to naive approach
			var top=0, left=0;
			while(elem) {
				top = top + parseInt(elem.offsetTop,10);
				left = left + parseInt(elem.offsetLeft,10);
				elem = elem.offsetParent;
			}
			return {y: top, x: left};
		}
	},
	//returns position of event
	pos:function(ev){
		ev = ev || event;
        if(ev.pageX || ev.pageY)	//FF, KHTML
            return {x:ev.pageX, y:ev.pageY};
        //IE
        var d  =  ((dhtmlx._isIE)&&(document.compatMode != "BackCompat"))?document.documentElement:document.body;
        return {
                x:ev.clientX + d.scrollLeft - d.clientLeft,
                y:ev.clientY + d.scrollTop  - d.clientTop
        };
	},
	//stop event bubbling
	stopEvent:function(e){
		(e||event).cancelBubble=true;
		return false;
	},
	//add css class to the node
	addCss:function(node,name){
        node.className+=" "+name;
    },
    //remove css class from the node
    removeCss:function(node,name){
        node.className=node.className.replace(RegExp(name,"g"),"");
    }
};

//autodetect codebase folder
(function(){
	var temp = document.getElementsByTagName("SCRIPT");	//current script, most probably
	dhtmlx.assert(temp.length,"Can't locate codebase");
	if (temp.length){
		//full path to script
		temp = (temp[temp.length-1].getAttribute("src")||"").split("/");
		//get folder name
		temp.splice(temp.length-1, 1);
		dhtmlx.codebase = temp.slice(0, temp.length).join("/")+"/";
	}
})();

dhtmlx.ui={};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//template.js'*/


/*
	Template - handles html templates
*/

/*DHX:Depend dhtmlx.js*/

dhtmlx.Template={
	_cache:{
	},
	setter:function(name, value){
		if (typeof value == "function") return value;
		return dhtmlx.Template.fromHTML(value);
	},
	obj_setter:function(name,value){
		var f = dhtmlx.Template.setter(name,value);
		var obj = this;
		return function(){
			return f.apply(obj, arguments);
		};
	},
	fromHTML:function(str){
		if (this._cache[str])
			return this._cache[str];
			
	//supported idioms
	// {obj} => value
	// {obj.attr} => named attribute or value of sub-tag in case of xml
	// {obj.attr?some:other} conditional output
	// {-obj => sub-template
		str=str||"";		
		str=str.replace(/[\r\n]+/g,"\\n");
		str=str.replace(/\{obj\.([^}?]+)\?([^:]*):([^}]*)\}/g,"\"+(obj.$1?\"$2\":\"$3\")+\"");
		str=str.replace(/\{common\.([^}\(]*)\}/g,"\"+common.$1+\"");
		str=str.replace(/\{common\.([^\}\(]*)\(\)\}/g,"\"+(common.$1?common.$1(obj):\"\")+\"");
		str=str.replace(/\{obj\.([^}]*)\}/g,"\"+obj.$1+\"");
		str=str.replace(/#([a-z0-9_]+)#/gi,"\"+obj.$1+\"");
		str=str.replace(/\{obj\}/g,"\"+obj+\"");
		str=str.replace(/\{-obj/g,"{obj");
		str=str.replace(/\{-common/g,"{common");
		str="return \""+str+"\";";
		return this._cache[str]= Function("obj","common",str);
	}
};

dhtmlx.Type={
	/*
		adds new template-type
		obj - object to which template will be added
		data - properties of template
	*/
	add:function(obj, data){ 
		//auto switch to prototype, if name of class was provided
		if (!obj.types && obj.prototype.types)
			obj = obj.prototype;
			
		dhtmlx.assert_check(data,{
			name:{ __type:"string" },
			template:{ __type:["string","function"] },
			template_edit:{ __type:["string","function"] },
			template_loading:{ __type:["string","function"] },
			
			css:{ __type:"string" },
			width:{ __type:"integer" },
			height:{ __type:"integer" },
			margin:{ __type:"integer" },
			padding:{ __type:"integer" },
			drag_marker:{ __type:"string" }
		});
		
		
		var name = data.name||"default";
		
		//predefined templates - autoprocessing
		this._template(data);
		this._template(data,"edit");
		this._template(data,"loading");
		
		obj.types[name]=dhtmlx.extend(dhtmlx.extend({},(obj.types[name]||this._default)),data);	
		return name;
	},
	//default template value - basically empty box with 5px margin
	_default:{
		css:"default",
		template:function(){ return ""; },
		template_edit:function(){ return ""; },
		template_loading:function(){ return "..."; },
		width:150,
		height:80,
		margin:5,
		padding:0
	},
	//template creation helper
	_template:function(obj,name){ 
		var name = "template"+(name?("_"+name):"");
		var data = obj[name];
		//if template is a string - check is it plain string or reference to external content
		if (data && (typeof data == "string")){
			if (data.indexOf("#")!=-1){
				data = data.split("#");
				switch(data[0]){
					case "html": 	//load from some container on the page
						data = dhtmlx.html.getValue(data[1]).replace(/\"/g,"\\\"");
						break;
					case "http": 	//load from external file
						var loader = new dhtmlx.ajax();
						loader.sync = true;
						data = loader.get(data[1],{uid:(new Date()).valueOf()}).responseText;
						break;
				}
			}
			obj[name] = dhtmlx.Template.fromHTML(data);
		}
	}
};



/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//config.js'*/


/*
	Behavior:Settings
	
	@export
		customize
		config
*/

/*DHX:Depend template.js*/
/*DHX:Depend dhtmlx.js*/

dhtmlx.Settings={
	_init:function(){
		/* 
			property can be accessed as this.config.some
			in same time for inner call it have sense to use _settings
			because it will be minified in final version
		*/
		this._settings = this.config=[]; 
		
		dhtmlx.assert_property(this,{
			type:{ __type:["object","string"] },	//template-type
			template:{ __type:["string"] },			//alias for type
			css:{ __type:"string" },				//class name for container
			container:{ "__type":["string","node"] }	//container element, can be object or its ID
		});
	},
	//sets value for property
	define:function(mode,value){
		//default value is true
		if (arguments.length==1) value=true;
		
		dhtmlx.assert_settings.call(this,mode,value);
		
		//method with name {prop}_setter will be used as propery setter
		//setter is optional
		var setter = this[mode+"_setter"];
		return this._settings[mode]=setter?setter.call(this,mode,value):value;
	},
	//process configuration object
	_parseSeetingColl:function(coll){
		if (coll){
			dhtmlx.assert_check(coll);
			for (var a in coll)				//for each setting
				this.define(a,coll[a]);		//set value through config
		}
	},
	//helper for object initialization
	_parseSettings:function(obj,initial){
		//initial - set of default values
		var settings = dhtmlx.extend({},initial);
		//code below will copy all properties over default one
		if (typeof obj == "object" && !obj.tagName)
			dhtmlx.extend(settings,obj);	
		//call config for each setting
		this._parseSeetingColl(settings);
	},
	_mergeSettings:function(config, defaults){
		for (var key in defaults)
			switch(typeof config[key]){
				case "object": 
					config[key] = this._mergeSettings((config[key]||{}), defaults[key]);
					break;
				case "undefined":
					config[key] = defaults[key];
					break;
			};
		return config;
	},
	//helper for html container init
	_parseContainer:function(obj,name,fallback){
		/*
			parameter can be a config object, in such case real container will be obj.container
			or it can be html object or ID of html object
		*/
		if (typeof obj == "object" && !obj.tagName) 
			obj=obj.container;
		this._obj = dhtmlx.toNode(obj);
		if (!this._obj && fallback)
			this._obj = fallback(obj);
			
		dhtmlx.assert(this._obj, "Incorrect html container");
		
		this._obj.className+=" "+name;
		this._obj.onselectstart=function(){return false;};	//block selection by default
		this._dataobj = this._obj;//separate reference for rendering modules
	},
	//apply template-type
	_set_type:function(name){
		//parameter can be a hash of settings
		if (typeof name == "object")
			return this.type_setter("type",name);
		
		dhtmlx.assert(this.types, "RenderStack :: Types are not defined");
		dhtmlx.assert(this.types[name],"RenderStack :: Inccorect type name",name);
		//or parameter can be a name of existing template-type	
		this.type=dhtmlx.extend({},this.types[name]);
		this.customize();	//init configs
	},
	//change some property and init configs
	customize:function(obj){
		//apply new properties
		if (obj) dhtmlx.extend(this.type,obj);
		
		//init tempaltes for item start and item end
		this.type._item_start = dhtmlx.Template.fromHTML(this.template_item_start(this.type));
		this.type._item_end = this.template_item_end(this.type);
		
		//repaint self
		this.render();
	},
	//config.type - creates new template-type, based on configuration object
	type_setter:function(mode,value){
		this._set_type(typeof value == "object"?dhtmlx.Type.add(this,value):value);
		return value;
	},
	//config.template - creates new template-type with defined template string
	template_setter:function(mode,value){
		return this.type_setter("type",{template:value});
	},
	//config.css - css name for top level container
	css_setter:function(mode,value){
		this._obj.className += " "+value;
		return value;
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//destructor.js'*/


/*
	Behavior:Destruction
	
	@export
		destructor
*/

/*DHX:Depend dhtmlx.js*/

dhtmlx.Destruction = {
	_init:function(){
		//register self in global list of destructors
		dhtmlx.destructors.push(this);
	},
	//will be called automatically on unload, can be called manually
	//simplifies job of GC
	destructor:function(){
		this.destructor=function(){}; //destructor can be called only once
		
		//html collection
		this._htmlmap  = null;
		this._htmlrows = null;
		
		//temp html element, used by toHTML
		if (this._html)
			document.body.appendChild(this._html);	//need to attach, for IE's GC

		this._html = null;
		this._obj = this._dataobj=null;
		this.data = null;
		this._events = this._handlers = null;
	}
};
//global list of destructors
dhtmlx.destructors = [];
dhtmlx.event(window,"unload",function(){
	//call all registered destructors
	for (var i=0; i<dhtmlx.destructors.length; i++)
		dhtmlx.destructors[i].destructor();
	dhtmlx.destructors = null;
	
	//detach all known DOM events
	for (var a in dhtmlx._events){
		var ev = dhtmlx._events[a];
		if (ev[0].removeEventListener)
			ev[0].removeEventListener(ev[1],ev[2],false);
		else if (ev[0].detachEvent)
			ev[0].detachEvent("on"+ev[1],ev[2]);
		delete dhtmlx._events[a];
	}
})


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//pager.js'*/


/*
	UI:paging control
*/

/*DHX:Depend template.js*/

dhtmlx.ui.pager=function(container){
	this.name = "Pager";
	
	dhtmlx.assert_property(this,{
		size:  { __type: "integer" },	//items on page
		count: { __type: "integer" },	//total count of items
		page:  { __type: "integer" },	//current page
		group: { __type: "integer" },	//pages in group
		type:  { __type: "string,object" }
	});
	dhtmlx.assert_event(this,{
		/*
			before page changed, will not be triggered for direct config.page updates
			@param old page num
			@param new page num
		*/
		onbeforepagechange:true,
		/*
			after page changed, will not be triggered for direct config.page updates
			@param new page num
		*/
		onafterpagechange:true,
		/*
			each time when page or limit value was changed
		*/
		onrefresh:true
	});
		
	dhtmlx.extend(this, dhtmlx.Settings);
	this._parseContainer(container,"dhx_pager");
	
	dhtmlx.extend(this, dhtmlx.EventSystem);
	dhtmlx.extend(this, dhtmlx.SingleRender);
	dhtmlx.extend(this, dhtmlx.MouseEvents);
	
	this._parseSettings(container,{
		size:10,	//items on page
		page:-1,	//current page
		group:5,	//pages in group
		count:0,	//total count of items
		type:"default"
	});
	
	this.data = this._settings;
	this.refresh();
};

dhtmlx.ui.pager.prototype={
	_id:"dhx_p_id",
	_click:{
		//on paging button click
		"dhx_pager_item":function(e,id){
			this.select(id);
		}
	},
	select:function(id){
		//id - id of button, number for page buttons
		switch(id){
			case "next":
				id = this._settings.page+1;
				break;
			case "prev":
				id = this._settings.page-1;
				break;
			case "first":
				id = 0;
				break;
			case "last":
				id = this._settings.limit-1;
				break;
		}
		if (this.callEvent("onBeforePageChange",[this._settings.page,id])){
			this.data.page = id*1; //must be int
			this.refresh();
			this.callEvent("onAfterPageChange",[id]);	
		}
	},
	types:{
		"default":{ 
			template:dhtmlx.Template.fromHTML("{common.pages()}"),
			//list of page numbers
			pages:function(obj){
				var html="";
				//skip rendering if paging is not fully initialized
				if (obj.page == -1) return "";
				//current page taken as center of view, calculate bounds of group
				obj.min = obj.page-Math.round((obj.group-1)/2);
				obj.max = obj.min + obj.group-1;
				if (obj.min<0){
					obj.max+=obj.min*(-1);
					obj.min=0;
				}
				if (obj.max>=obj.limit){
					obj.min -= Math.min(obj.min,obj.max-obj.limit+1);
					obj.max = obj.limit-1;
				}
				//generate HTML code of buttons
				for (var i=(obj.min||0); i<=(obj.max||obj.limit); i++)
					html+=this.button({id:i, index:(i+1), selected:(i == obj.page ?"_selected":"")});
				return html;
			},
			//go-to-first page button
			first:function(){
				return this.button({ id:"first", index:" &lt;&lt; ", selected:""});
			},
			//go-to-last page button
			last:function(){
				return this.button({ id:"last", index:" &gt;&gt; ", selected:""});
			},
			//go-to-prev page button
			prev:function(){
				return this.button({ id:"prev", index:"&lt;", selected:""});
			},
			//go-to-next page button
			next:function(){
				return this.button({ id:"next", index:"&gt;", selected:""});
			},
			button:dhtmlx.Template.fromHTML("<div dhx_p_id='{obj.id}' class='dhx_pager_item{obj.selected}'>{obj.index}</div>")
			
		}
	},
	//update settings and repaint self
	refresh:function(){
		var s = this._settings;
		//max page number
		s.limit = Math.ceil(s.count/s.size);
		
		//correct page if it is out of limits
		if (s.limit && s.limit != s.old_limit)
			s.page = Math.min(s.limit-1, s.page);
		
		var id = s.page;
		if (id!=-1 && (id!=s.old_page) || (s.limit != s.old_limit)){ 
			//refresh self only if current page or total limit was changed
			this.render();
			this.callEvent("onRefresh",[]);
			s.old_limit = s.limit;	//save for onchange check in next iteration
			s.old_page = s.page;
		}
	},
	template_item_start:dhtmlx.Template.fromHTML("<div>"),
	template_item_end:dhtmlx.Template.fromHTML("</div>")
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//single_render.js'*/


/*
	REnders single item. 
	Can be used for elements without datastore, or with complex custom rendering logic
	
	@export
		render
*/

/*DHX:Depend template.js*/

dhtmlx.SingleRender={
	_init:function(){
		dhtmlx.assert_event(this,{
			/*
				@param	data - data object
			*/
			onbeforerender:true,	
			
			/*
			*/
			onafterrender:true
		});
	},
	//convert item to the HTML text
	_toHTML:function(obj){
			/*
				this one doesn't support per-item-$template
				it has not sense, because we have only single item per object
			*/
			return this.type._item_start(obj,this.type)+this.type.template(obj,this.type)+this.type._item_end;
	},
	//render self, by templating data object
	render:function(){
		if (!this.callEvent || this.callEvent("onBeforeRender",[this.data])){
			if (this.data)
				this._dataobj.innerHTML = this._toHTML(this.data);
			if (this.callEvent) this.callEvent("onAfterRender",[]);
		}
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//tooltip.js'*/


/*
	UI: Tooltip
	
	@export
		show
		hide
*/

/*DHX:Depend tooltip.css*/
/*DHX:Depend template.js*/
/*DHX:Depend single_render.js*/

dhtmlx.ui.Tooltip=function(container){
	this.name = "Tooltip";
	this.version = "3.0";
	
	dhtmlx.extend(this, dhtmlx.Settings);
	dhtmlx.extend(this, dhtmlx.SingleRender);

	dhtmlx.assert_property(this,{
		dx:{ __type:"integer"}, //x correction for tooltip position
		dy:{ __type:"integer"}  //y correction for tooltip position
	});
	this._parseSettings(container,{
		type:"default",
		dy:0,
		dx:20
	});	
	//create  container for future tooltip
	this._dataobj = this._obj = document.createElement("DIV");
	this._obj.className="dhx_tooltip";
	dhtmlx.html.insertBefore(this._obj,document.body.firstChild);
};
dhtmlx.ui.Tooltip.prototype = {
	//show tooptip
	//pos - object, pos.x - left, pox.y - top
	show:function(data,pos){
		//render sefl only if new data was provided
		if (this.data!=data){
			this.data=data;
			this.render(data);
		}
		//show at specified position
		this._obj.style.top = pos.y+this._settings.dy+"px";
		this._obj.style.left = pos.x+this._settings.dx+"px";
		this._obj.style.display="block";
	},
	//hide tooltip
	hide:function(){
		this.data=null; //nulify, to be sure that on next show it will be fresh-rendered
		this._obj.style.display="none";
	},
	types:{
		"default":dhtmlx.Template.fromHTML("{obj.id}")
	},
	template_item_start:function(){ return ""; },
	template_item_end:function(){ return ""; }
};



/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//autotooltip.js'*/


/*
	Behavior: AutoTooltip - links tooltip to data driven item
*/

/*DHX:Depend tooltip.js*/

dhtmlx.AutoTooltip = {
	_init:function(){
		dhtmlx.assert_property(this,{
			tooltip:{__type:["object"]}	//tooltip configuration
    	});
	},
	tooltip_setter:function(mode,value){
		var t = new dhtmlx.ui.Tooltip(value);
		this.attachEvent("onMouseMove",function(id,e){	//show tooltip on mousemove
			t.show(this.get(id),dhtmlx.html.pos(e));
		});
		this.attachEvent("onMouseOut",function(id,e){	//hide tooltip on mouseout
			t.hide();
		});
		this.attachEvent("onMouseMoving",function(id,e){	//hide tooltip just after moving start
			t.hide();
		});
		return t;
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//compatibility.js'*/


/*
	Collection of compatibility hacks
*/

/*DHX:Depend dhtmlx.js*/

dhtmlx.compat=function(name, obj){
	//check if name hach present, and applies it when necessary
	if (dhtmlx.compat[name])
		dhtmlx.compat[name](obj);
};


(function(){
	if (!window.dhtmlxError){
		//dhtmlxcommon is not included
		
		//create fake error tracker for connectors
		var dummy = function(){};
		window.dhtmlxError={ catchError:dummy, throwError:dummy };
		//helpers instead of ones from dhtmlxcommon
		window.convertStringToBoolean=function(value){
			return !!value;
		};
		window.dhtmlxEventable = function(node){
			dhtmlx.extend(node,dhtmlx.EventSystem);
		};
		//imitate ajax layer of dhtmlxcommon
		var loader = {
			getXMLTopNode:function(name){
				
			},
			doXPath:function(path){
				return dhtmlx.DataDriver.xml.xpath(this.xml,path);
			},
			xmlDoc:{
				responseXML:true
			}
		};
		//wrap ajax methods of dataprocessor
		dhtmlx.compat.dataProcessor=function(obj){
			//FIXME
			//this is pretty ugly solution - we replace whole method , so changes in dataprocessor need to be reflected here
			
			var sendData = "_sendData";
			var in_progress = "_in_progress";
			var tMode = "_tMode";
			var waitMode = "_waitMode";
			
			obj[sendData]=function(a1,rowId){
		    	if (!a1) return; //nothing to send
		    	if (rowId)
					this[in_progress][rowId]=(new Date()).valueOf();
			    
				if (!this.callEvent("onBeforeDataSending",rowId?[rowId,this.getState(rowId)]:[])) return false;				
				
				var a2 = this;
		        var a3=this.serverProcessor;
				if (this[tMode]!="POST")
					//use dhtmlx.ajax instead of old ajax layer
					dhtmlx.ajax().get(a3+((a3.indexOf("?")!=-1)?"&":"?")+a1,"",function(t,x,xml){
						loader.xml = dhtmlx.DataDriver.xml.checkResponse(t,x);
						a2.afterUpdate(a2, null, null, null, loader);
					});
				else
		        	dhtmlx.ajax().post(a3,a1,function(t,x,xml){
		        		loader.xml = dhtmlx.DataDriver.xml.checkResponse(t,x);
		        		a2.afterUpdate(a2, null, null, null, loader);
		    		});
		
				this[waitMode]++;
		    };
		};
	}
	
})();


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//dataprocessor_hook.js'*/


/*
	Behaviour:DataProcessor - translates inner events in dataprocessor calls
	
	@export
		changeId
		setItemStyle
		setUserData
		getUserData
*/

/*DHX:Depend compatibility.js*/
/*DHX:Depend dhtmlx.js*/

dhtmlx.DataProcessor={
	//called from DP as part of dp.init
	_dp_init:function(dp){
		//map methods
		var varname = "_methods";
		dp[varname]=["setItemStyle","","changeId","remove"];
		//after item adding - trigger DP
		this.attachEvent("onAfterAdd",function(obj){
			dp.setUpdated(obj.id,true,"inserted");
		});
		this.data.attachEvent("onStoreLoad",dhtmlx.bind(function(driver, data){
			if (driver.getUserData)
				driver.getUserData(data,this._userdata);
		},this));
		
		//after item deleting - trigger DP
		this.attachEvent("onBeforeDelete",function(id){
	        var z=dp.getState(id);
			if (z=="inserted") {  dp.setUpdated(id,false);		return true; }
			if (z=="deleted")  return false;
	    	if (z=="true_deleted")  return true;
	    	
			dp.setUpdated(id,true,"deleted");
	      	return false;
		});
		//after editing - trigger DP
		this.attachEvent("onAfterEditStop",function(id){
			dp.setUpdated(id,true,"updated");
		});
		
		varname = "_getRowData";
		//serialize item's data in URL
		dp[varname]=function(id,pref){
			pref=pref||"";
			var ev=this.obj.data.get(id);
			
			var str=[];
			for (var a in ev){
				if (a.indexOf("_")==0) continue;
					str.push(a+"="+this.escape(ev[a]));
			}
			
			return pref+str.join("&"+pref);
		};
		varname = "_clearUpdateFlag";
		dp[varname]=function(){};
		this._userdata = {};
		
		dp.attachEvent("insertCallback", this._dp_callback);
		dp.attachEvent("updateCallback", this._dp_callback);
		dp.attachEvent("deleteCallback", function(upd, id) {
			this.obj.setUserData(id, this.action_param, "true_deleted");
			this.obj.remove(id);
		});
				
		//enable compatibility layer - it will allow to use DP without dhtmlxcommon
		dhtmlx.compat("dataProcessor",dp);
	},
	_dp_callback:function(upd,id){
		this.obj.data.set(id,dhtmlx.DataDriver.xml.getDetails(upd.firstChild));
		this.obj.data.refresh(id);
	},
	//marks item in question with specific styles, not purposed for public usage
	setItemStyle:function(id,style){
		var node = this._locateHTML(id);
		if (node) node.style.cssText+=";"+style; //style is not persistent
	},
	//change ID of item
	//FIXME - probably need to be moved in DataStore
	changeId:function(oldid, newid){
		this.data.order[this.data.indexById(oldid)]=newid;
		this.data.pull[newid] = this.data.pull[oldid];
		delete this.data.pull[oldid];
		//FIXME slow, but there is no other way without breaking render isolation
		this.data.refresh();
	},
	//sets property value, not purposed for public usage
	setUserData:function(id,name,value){
		if (id)
			this.data.get(id)[name]=value;
		else
			this._userdata[name]=value;
	},
	//gets property value, not purposed for public usage
	getUserData:function(id,name){
		return id?this.data.get(id)[name]:this._userdata[name];
	}
};
(function(){
	var temp = "_dp_init";
	dhtmlx.DataProcessor[temp]=dhtmlx.DataProcessor._dp_init;
})();



/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//compatibility_drag.js'*/


/*
	Compatibility hack for DND
	Allows dnd between dhtmlx.dnd and dhtmlxcommon based dnd
	When dnd items - related events will be correctly triggered. 
	onDrag event must define final moving logic, if it is absent - item will NOT be moved automatically
	
	to activate this functionality , next command need to be called
		dhtmlx.compat("dnd");
*/

/*DHX:Depend compatibility.js*/

dhtmlx.compat.dnd = function(){
	//if dhtmlxcommon.js included on the page
	if (window.dhtmlDragAndDropObject){
		var _dragged = "_dragged"; //fake for code compression utility, do not change!
		
		//wrap methods of dhtmlxcommon to inform dhtmlx.dnd logic
		var old_ocl = dhtmlDragAndDropObject.prototype.checkLanding;
		dhtmlDragAndDropObject.prototype.checkLanding=function(node,e,skip){
			old_ocl.apply(this,arguments);
			if (!skip){
				dhtmlx.DragControl._drag_context = {};
				dhtmlx.DragControl._checkLand(node,e,true);
			}
		};
		
		var old_odp = dhtmlDragAndDropObject.prototype.stopDrag;
		dhtmlDragAndDropObject.prototype.stopDrag=function(e,dot,skip){
			if (!skip){
				if (dhtmlx.DragControl._last){
					dhtmlx.DragControl._active = dragger.dragStartNode;
					dhtmlx.DragControl._stopDrag(e,true);
				}
			}
			old_odp.apply(this,arguments);
		};
		
		
		//pre-create dnd object from dhtmlxcommon
		var dragger = new dhtmlDragAndDropObject();
		
		//wrap drag start process
		var old_start = dhtmlx.DragControl._startDrag;
		dhtmlx.DragControl._startDrag=function(){
			old_start.apply(this,arguments);	
			//build list of IDs and fake objects for dhtlmxcommon support
			var c = dhtmlx.DragControl._drag_context;
			if (!c) return;
			var source = [];
			var tsource = [];
			for (var i=0; i < c.source.length; i++){
				source[i]={idd:c.source[i]};
				tsource.push(c.source[i]);
			}
			
			dragger.dragStartNode = {	
				parentNode:{}, 
				parentObject:{ 
					idd:source, 
					id:(tsource.length == 1?tsource[0]:tsource),
					treeNod:{
						object:c.from
					}
				}
			};
			
			//prevent code compression of "_dragged"
			dragger.dragStartNode.parentObject.treeNod[_dragged]=source;
			dragger.dragStartObject = c.from;
		};
		//wrap drop landing checker
		var old_check = dhtmlx.DragControl._checkLand;
		dhtmlx.DragControl._checkLand = function(node,e,skip){
			old_check.apply(this,arguments);
			
			if (!this._last && !skip){
				//we are in middle of nowhere, check old drop landings
				var node = dragger.checkLanding(node,e,true)
			}
		};
		
		//wrap drop routine
		var old_drop = dhtmlx.DragControl._stopDrag;
		dhtmlx.DragControl._stopDrag=function(e,skip){
			old_drop.apply(this,arguments);
			if (dragger.lastLanding && !skip)
				dragger.stopDrag(e,false,true);
		}
		//extend getMaster, so they will be able to recognize master objects from dhtmlxcommon.js
		var old_mst = 	dhtmlx.DragControl.getMaster;
		dhtmlx.DragControl.getMaster = function(t){
			var master = null;
			if (t)
				master = old_mst.apply(this,arguments);
			if (!master){
				master = dragger.dragStartObject;
				var src = [];
				var from = master[_dragged];
				for (var i=0; i < from.length; i++) {
					src.push(from[i].idd||from[i].id);
				}
				dhtmlx.DragControl._drag_context.source = src;
			}
			return master;
		};
		
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//move.js'*/


/*
	Behavior:DataMove - allows to move and copy elements, heavily relays on DataStore.move
	@export
		copy
		move
*/
dhtmlx.DataMove={
	_init:function(){
		dhtmlx.assert(this.data, "DataMove :: Component doesn't have DataStore");
	},
	//creates a copy of the item
	copy:function(sid,tindex,tobj,tid){
		var data = this.get(sid);
		if (!data){
			dhtmlx.log("Warning","Incorrect ID in DataMove::copy");
			return;
		}
		
		//make data conversion between objects
		if (tobj){
			dhtmlx.assert(tobj.externalData,"DataMove :: External object doesn't support operation");	
			data = tobj.externalData(data);
		}
		tobj = tobj||this;
		//adds new element same as original
		return tobj.add(tobj.externalData(data,tid),tindex);
	},
	//move item to the new position
	move:function(sid,tindex,tobj,tid){
		//can process an arrya - it allows to use it from onDrag 
		if (sid instanceof Array){
			for (var i=0; i < sid.length; i++) {
				//increase index for each next item in the set, so order of insertion will be equal to order in the array
				tindex = (tobj||this).indexById(this.move(sid[i], tindex, tobj, dhtmlx.uid()))+1;
			}
			return;
		}
		
		nid = sid; //id after moving
		if (tindex<0){
			dhtmlx.log("Info","DataMove::move - moving outside of bounds is ignored");
			return;
		}
		
		var data = this.get(sid);
		if (!data){
			dhtmlx.log("Warning","Incorrect ID in DataMove::move");
			return;
		}
		
		if (!tobj || tobj == this)
			this.data.move(this.indexById(sid),tindex);	//move inside the same object
		else {
			dhtmlx.assert(tobj.externalData, "DataMove :: External object doesn't support operation");
			//copy to the new object
			nid=tobj.add(tobj.externalData(data,tid),tindex);
			this.remove(sid);//delete in old object
		}
		return nid;	//return ID of item after moving
	},
	//move item on one position up
	moveUp:function(id,step){
		return this.move(id,this.indexById(id)-(step||1));
	},
	//move item on one position down
	moveDown:function(id,step){
		return this.moveUp(id, (step||1)*-1);
	},
	//move item to the first position
	moveTop:function(id){
		return this.move(id,0);
	},
	//move item to the last position
	moveBottom:function(id){
		return this.move(id,this.data.dataCount()-1);
	},
	/*
		this is a stub for future functionality
		currently it just makes a copy of data object, which is enough for current situation
	*/
	externalData:function(data,id){
		//FIXME - will not work for multi-level data
		var newdata = dhtmlx.extend({},data);
		newdata.id = id||dhtmlx.uid();
		
		newdata.$selected=newdata.$template=null;
		return newdata;
	}
}


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//dnd.js'*/


/*
	Behavior:DND - low-level dnd handling
	@export
		getContext
		addDrop
		addDrag
		
	DND master can define next handlers
		onCreateDrag
		onDragIng
		onDragOut
		onDrag
		onDrop
	all are optional
*/

/*DHX:Depend dhtmlx.js*/

dhtmlx.DragControl={
	//has of known dnd masters
	_drag_masters : dhtmlx.toArray(["dummy"]),
	/*
		register drop area
		@param node 			html node or ID
		@param ctrl 			options dnd master
		@param master_mode 		true if you have complex drag-area rules
	*/
	addDrop:function(node,ctrl,master_mode){
		node = dhtmlx.toNode(node);
		node.dhx_drop=this._getCtrl(ctrl);
		if (master_mode) node.dhx_master=true;
	},
	//return index of master in collection
	//it done in such way to prevent dnd master duplication
	//probably useless, used only by addDrop and addDrag methods
	_getCtrl:function(ctrl){
		ctrl = ctrl||dhtmlx.DragControl;
		var index = this._drag_masters.find(ctrl);
		if (index<0){
			index = this._drag_masters.length;
			this._drag_masters.push(ctrl);
		}
		return index	
	},
	/*
		register drag area
		@param node 	html node or ID
		@param ctrl 	options dnd master
	*/
	addDrag:function(node,ctrl){
	    node = dhtmlx.toNode(node);
	    node.dhx_drag=this._getCtrl(ctrl);
		dhtmlx.event(node,"mousedown",this._preStart,node);
	},
	//logic of drag - start, we are not creating drag immediately, instead of that we hears mouse moving
	_preStart:function(e){
		dhtmlx.DragControl._active=this;
		dhtmlx.DragControl._dhx_drag_mm = dhtmlx.event(document.body,"mousemove",dhtmlx.DragControl._startDrag);
		dhtmlx.DragControl._dhx_drag_mu = dhtmlx.event(document.body,"mouseup",dhtmlx.DragControl._preStartFalse);
		
		e.cancelBubble=true;
		return false;
	},
	//if mouse was released before moving - this is not a dnd, remove event handlers
	_preStartFalse:function(e){
		dhtmlx.DragControl._dhx_drag_mm = dhtmlx.eventRemove(dhtmlx.DragControl._dhx_drag_mm);
		dhtmlx.DragControl._dhx_drag_mu = dhtmlx.eventRemove(dhtmlx.DragControl._dhx_drag_mu);
	},
	//mouse was moved without button released - dnd started, update event handlers
	_startDrag:function(e){
		dhtmlx.DragControl._preStartFalse();
		if (!dhtmlx.DragControl.createDrag(e)) return;
		dhtmlx.DragControl.sendSignal("start"); //useless for now
		dhtmlx.DragControl._dhx_drag_mm = dhtmlx.event(document.body,"mousemove",dhtmlx.DragControl._moveDrag);
		dhtmlx.DragControl._dhx_drag_mu = dhtmlx.event(document.body,"mouseup",dhtmlx.DragControl._stopDrag);
		dhtmlx.DragControl._moveDrag(e);
	},
	//mouse was released while dnd is active - process target
	_stopDrag:function(e){
		dhtmlx.DragControl._dhx_drag_mm = dhtmlx.eventRemove(dhtmlx.DragControl._dhx_drag_mm);
		dhtmlx.DragControl._dhx_drag_mu = dhtmlx.eventRemove(dhtmlx.DragControl._dhx_drag_mu);
		if (dhtmlx.DragControl._last){	//if some drop target was confirmed
			dhtmlx.DragControl.onDrop(dhtmlx.DragControl._active,dhtmlx.DragControl._last,this._landing,e);
			dhtmlx.DragControl.onDragOut(dhtmlx.DragControl._active,dhtmlx.DragControl._last,null,e);
		}
		dhtmlx.DragControl.destroyDrag();
		dhtmlx.DragControl.sendSignal("stop");	//useless for now
	},
	//dnd is active and mouse position was changed
	_moveDrag:function(e){
		var pos = dhtmlx.html.pos(e);
		//adjust drag marker position
		dhtmlx.DragControl._html.style.top=pos.y+dhtmlx.DragControl.top +"px";
		dhtmlx.DragControl._html.style.left=pos.x+dhtmlx.DragControl.left+"px";
		
		if (dhtmlx.DragControl._skip)
			dhtmlx.DragControl._skip=false;
		else
			dhtmlx.DragControl._checkLand((e.srcElement||e.target),e);
		
		e.cancelBubble=true;
		return false;		
	},
	//check if item under mouse can be used as drop landing
	_checkLand:function(node,e){ 
		while (node && node.tagName!="BODY"){
			if (node.dhx_drop){	//if drop area registered
				if (this._last && (this._last!=node || node.dhx_master))	//if this area with complex dnd master
					this.onDragOut(this._active,this._last,node,e);			//inform master about possible mouse-out
				if (!this._last || this._last!=node || node.dhx_master){	//if this is new are or area with complex dnd master
				    this._last=null;										//inform master about possible mouse-in
					this._landing=this.onDragIn(dhtmlx.DragControl._active,node,e);
					if (this._landing)	//landing was rejected
						this._last=node;
					return;				
				} 
				return;
			}
			node=node.parentNode;
		}
		if (this._last)	//mouse was moved out of previous landing, and without finding new one 
			this._last = this._landing = this.onDragOut(this._active,this._last,null,e);
	},
	//mostly useless for now, can be used to add cross-frame dnd
	sendSignal:function(signal){
		dhtmlx.DragControl.active=(signal=="start");
	},
	
	//return master for html area
	getMaster:function(t){
		return this._drag_masters[t.dhx_drag||t.dhx_drop];
	},
	//return dhd-context object
	getContext:function(t){
		return this._drag_context;
	},
	//called when dnd is initiated, must create drag representation
	createDrag:function(e){ 
		var z=document.createElement("DIV");
		var a=dhtmlx.DragControl._active;
		//if custom method is defined - use it
		if (a.dhx_drag && a.dhx_drag.onCreateDrag){
			var t = dhtmlx.DragControl._html;
			t=a.dhx_drag.onCreateDrag(a,e);
			t.style.position='absolute';
			t.style.zIndex=dhtmlx.zIndex.drag;
			t.onmousemove=dhtmlx.DragControl._skip_mark;
			return true;
		}
		//overvise use default one
		var t=dhtmlx.DragControl.onDrag(a,e)
		if (!t) return false;
		z.innerHTML=t;
		z.className="dhx_drag_zone";
		z.onmousemove=dhtmlx.DragControl._skip_mark;
		document.body.appendChild(z);
		dhtmlx.DragControl._html=z;
		return true;
	},
	//helper, prevents unwanted mouse-out events
	_skip_mark:function(){
		dhtmlx.DragControl._skip=true;
	},
	//after dnd end, remove all traces and used html elements
	destroyDrag:function(){
		var a=dhtmlx.DragControl._active;
		if (a.dhx_drag && a.dhx_drag.onDestroyDrag)
			a.dhx_drag.onDestroyDrag(a,dhtmlx.DragControl._html);
		else dhtmlx.html.remove(dhtmlx.DragControl._html);
		dhtmlx.DragControl._landing=dhtmlx.DragControl._active=dhtmlx.DragControl._last=dhtmlx.DragControl._html=null;
	},
	top:5,	 //relative position of drag marker to mouse cursor
	left:5,
	//called when mouse was moved in drop area
	onDragIn:function(s,t,e){
		var m=this._drag_masters[t.dhx_drop];
		if (m.onDragIn && m!=this) return m.onDragIn(s,t,e)
		t.className=t.className+" dhx_drop_zone";
		return t;
	},
	//called when mouse was moved out drop area
	onDragOut:function(s,t,n,e){
		var m=this._drag_masters[t.dhx_drop];
		if (m.onDragOut && m!=this) return m.onDragOut(s,t,n,e)
		t.className=t.className.replace("dhx_drop_zone","");
		return null;
	},
	//called when mouse was released over drop area
	onDrop:function(s,t,d,e){
		var m=this._drag_masters[t.dhx_drop];
		if (m.onDrop && m!=this) return m.onDrop(s,t,d,e)
		t.appendChild(s);
	},
	//called when dnd just started
	onDrag:function(s,e){
		var m=this._drag_masters[s.dhx_drag];
		if (m.onDrag && m!=this) return m.onDrag(s,e)
		dhtmlx.DragControl._drag_context = {source:s, from:s};
		return "<div style='"+s.style.cssText+"'>"+s.innerHTML+"</div>";
	}	
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//drag.js'*/


/*
	Behavior:DragItem - adds ability to move items by dnd
	
	@export
		getDragContext
		
	dnd context can have next properties
		from - source object
		to - target object
		source - id of dragged item(s)
		target - id of drop target, null for drop on empty space
		start - id from which DND was started
*/

/*DHX:Depend dnd.js*/		/*DHX:Depend move.js*/		/*DHX:Depend compatibility_drag.js*/ 	
/*DHX:Depend dhtmlx.js*/



dhtmlx.DragItem={
	_init:function(){
		dhtmlx.assert(this.move,"DragItem :: Component doesn't have DataMove interface");
		dhtmlx.assert(this.locate,"DragItem :: Component doesn't have RenderStack interface");
		dhtmlx.assert(dhtmlx.DragControl,"DragItem :: DragControl is not included");
		
		dhtmlx.assert_event(this,{
			/*
				@param		dnd context
				@param		native event object
			*/
        	ondragout:true,
        	/*
				@param		dnd context
				@param		native event object
			*/
        	onbeforedrag:true,
        	/*
				@param		dnd context
				@param		native event object
			*/
        	onbeforedragin:true,
        	/*
				@param		dnd context
				@param		native event object
			*/
        	onbeforedrop:true,
        	/*
				@param		dnd context
				@param		native event object
			*/
        	onafterdrop:true
        });
        dhtmlx.assert_property(this,{
        	drag:{ "true":true, "false":true }
    	});
			
		if (!this._settings || this._settings.drag)
			dhtmlx.DragItem._initHandlers(this);
		else if (this._settings){
			//define setter, which may be triggered by config call
			this.drag_setter=function(prop, value){
				if (value){
					this._initHandlers(this);
					delete this.drag_setter;	//prevent double initialization
				}
				return value;
			}
		}
		//if custom dnd marking logic is defined - attach extra handlers
		if (this.dragMarker){
			this.attachEvent("onBeforeDragIn",this.dragMarker);
			this.attachEvent("onDragOut",this.dragMarker);
		}
			
	},
	//helper - defines component's container as active zone for dragging and for dropping
	_initHandlers:function(obj){
		dhtmlx.DragControl.addDrop(obj._obj,obj,true);
		dhtmlx.DragControl.addDrag(obj._obj,obj);	
	},
	/*
		s - source html element
		t - target html element
		d - drop-on html element ( can be not equal to the target )
		e - native html event 
	*/
	//called when drag moved over possible target
	onDragIn:function(s,t,e){
		var id = this.locate(e) || null;
		var context = dhtmlx.DragControl._drag_context;
		var to = dhtmlx.DragControl.getMaster(s);
		//previous target
		var html = (this._locateHTML(id)||this._obj);
		//prevent double processing of same target
		if (html == dhtmlx.DragControl._landing) return html;
		
		context.target = id;
		context.to = to;
		
		if (!this.callEvent("onBeforeDragIn",[context,e]))
			id = null;
		//mark target
		dhtmlx.html.addCss(html,"dhx_drag_over");
		dhtmlx.DragControl._drag_context.target=id;

		return html;
	},
	//called when drag moved out from possible target
	onDragOut:function(s,t,n,e){ 
		var id = this.locate(e) || null;
		//previous target
		var html = (this._locateHTML(id)||(n?dhtmlx.DragControl.getMaster(n)._obj:window.undefined));
		if (html == dhtmlx.DragControl._landing) return null;
		//unmark previous target
		var context = dhtmlx.DragControl._drag_context;
		dhtmlx.html.removeCss(dhtmlx.DragControl._landing,"dhx_drag_over");
		context.target = context.to = null;
		
		this.callEvent("onDragOut",[context,e]);
		return null;
	},
	//called when drag moved on target and button is released
	onDrop:function(s,t,d,e){
		var context = dhtmlx.DragControl._drag_context;
		//finalize context details
		context.from = dhtmlx.DragControl.getMaster(s);
		context.to = this;
		context.index = context.target?this.indexById(context.target):this.dataCount();
		context.new_id = dhtmlx.uid();
		if (!this.callEvent("onBeforeDrop",[context,e])) return;
		//moving
		if (context.from==context.to){
			this.move(context.source,context.index);	//inside the same component
		} else {
			if (context.from)	//from different component
				context.from.move(context.source,context.index,context.to,context.new_id);
			else
				dhtmlx.error("Unsopported d-n-d combination");
		}
		this.callEvent("onAfterDrop",[context,e]);
	},
	//called when drag action started
	onDrag:function(s,e){
		var id = this.locate(e);
		var list = [id];
		if (id){
			if (this.getSelected){ //has selection model
				var selection = this.getSelected();	//if dragged item is one of selected - drag all selected
				if (dhtmlx.PowerArray.find.call(selection,id)!=-1)
					list = selection;
			}
			//save initial dnd params
			var context = dhtmlx.DragControl._drag_context= { source:list, start:id };
			context.from = this;
			
			if (this.callEvent("onBeforeDrag",[context,e]))
				return context.html||this._toHTML(this.get(id));	//set drag representation
		}
		return null;
	},
	//returns dnd context object
	getDragContext:function(){
		return dhtmlx.DragControl._drag_context;
	}
}


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//edit.js'*/


/*
	Behavior:EditAbility - enables item operation for the items
	
	@export
		edit
		stopEdit
*/
dhtmlx.EditAbility={
	_init: function(id){
		this._edit_id = null;		//id of active item 
		this._edit_bind = null;		//array of input-to-property bindings
		
		
		dhtmlx.assert(this.data,"EditAbility :: Component doesn't have DataStore");
		dhtmlx.assert(this._locateHTML,"EditAbility :: Component doesn't have RenderStack");
			
		dhtmlx.assert_event(this,{
			/*
				@param id of item
			*/
			onbeforeeditstart:true,
			/*
				@param id of item
			*/
			onaftereditstart:true,
			/*
				@param id of item
			*/
			onbeforeeditstop:true,
			/*
				@param id of item
			*/
			onaftereditstop:true
		});
		dhtmlx.assert_property(this,{
        	edit:{ "true":true, "false":true }	//allow or deny edit operations
    	});
    	
		this.attachEvent("onEditKeyPress",function(code){
			if (code == 13)
				this.stopEdit();
			else if (code == 27) 
				this.stopEdit(true);
		});
    	
	},
	//switch item to the edit state
	edit:function(id){
		//edit operation can be blocked from editStop - when previously active editor can't be closed			
		if (!this._edit_id || this.stopEdit(false, id)){
			if (!this.callEvent("onBeforeEditStart",[id])) 
				return;			
			var data = this.data.get(id);			
			//object with custom templates is not editable
			if (data.$template) return;
			
			//item must have have "edit" template
 			data.$template="edit";	
			this.data.refresh(id);
			this._edit_id = id;
			
			//parse templates and save input-property mapping
			this._save_binding(id);
			this._edit_bind(true,data);	//fill inputs with data
			
			this.callEvent("onAfterEditStart",[id]);	
		}
	},
	//close currently active editor
	stopEdit:function(mode, if_not_id){
		if (!this._edit_id || this._edit_id == if_not_id) return false;
		if (!this.callEvent("onBeforeEditStop",[this._edit_id]))
			return false;
			
		var data=this.data.get(this._edit_id);
		data.$template=null;	//set default template
		
		//load data from inputs
		//if mode is set - close editor without saving
		if (!mode) this._edit_bind(false,data);
		
		this.data.refresh(this._edit_id);
		var id = this._edit_id;
		this._edit_bind=this._edit_id=null;
		
		this.callEvent("onAfterEditStop",[id]);
		return true;
	},
	//parse template and save inputs which need to be mapped to the properties
	_save_binding:function(id){
		var cont = this._locateHTML(id);
		var code = "";			//code of prop-to-inp method
		var back_code = "";		//code of inp-to-prop method
		var bind_elements = [];	//binded inputs
		if (cont){
			var elements = cont.getElementsByTagName("*");		//all sub-tags
			var bind = "";
			for (var i=0; i < elements.length; i++) {
				if(elements[i].nodeType==1 && (bind = elements[i].getAttribute("bind"))){	//if bind present
					//code for element accessing 
					code+="els["+bind_elements.length+"].value="+bind+";";
					back_code+=bind+"=els["+bind_elements.length+"].value;";
					bind_elements.push(elements[i]);
					//clear block-selection for the input
					elements[i].className+=" dhx_allow_selection";
					elements[i].onselectstart=this._block_native;
				}
			}
			elements = null;
		}
		//create accessing methods, for later usage
		code = Function("obj","els",code);
		back_code = Function("obj","els",back_code);
		this._edit_bind = function(mode,obj){
			if (mode){	//property to input
				code(obj,bind_elements);	
				if (bind_elements.length && bind_elements[0].select) //focust first html input, if possible
					bind_elements[0].select();						 
			}
			else 		//input to propery
				back_code(obj,bind_elements);
		}
	},
	//helper - blocks event bubbling, used to stop click event on editor level
	_block_native:function(e){ (e||event).cancelBubble=true; return true; }
}


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//key.js'*/


/*
	Behavior:KeyEvents - hears keyboard 
*/
dhtmlx.KeyEvents = {
	_init:function(){
		dhtmlx.assert_event(this,{
			/*
				for each key action in default state
				@param key code
				@param ctrl flag
				@param shift flag
				@param native event
			*/
			onkeypress:true,
			/*
				for each key action in edit state
				@param key code
				@param ctrl flag
				@param shift flag
				@param native event
			*/
			oneditkeypress:true
		});
		//attach handler to the main container
		dhtmlx.event(this._obj,"keypress",this.onKeyPress,this);
	},
	//called on each key press , when focus is inside of related component
	onKeyPress:function(e){
		e=e||event;
		var code = e.keyCode; //FIXME  better solution is required
		this.callEvent((this._edit_id?"onEditKeyPress":"onKeyPress"),[code,e.ctrlKey,e.shiftKey,e]);
	}
}


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//mouse.js'*/


/*
	Behavior:MouseEvents - provides inner evnets for  mouse actions
*/
dhtmlx.MouseEvents={
	_init: function(){
		//attach dom events if related collection is defined
		if (this._click)
			dhtmlx.event(this._obj,"click",this.onClick,this);
		if (this._dblclick)
			dhtmlx.event(this._obj,"dblclick",this.onDblClick,this);
		if (this._mouse_move){
			dhtmlx.event(this._obj,"mousemove",this.onMouse,this);
			dhtmlx.event(this._obj,(dhtmlx._isIE?"mouseleave":"mouseout"),this.onMouse,this);
		}
			
		dhtmlx.assert_event(this,{
			/*
				@param	id
				@param	native event object
			*/
			onitemclick:true,
			/*
				@param	id
				@param	native event object
			*/
			onitemdblclick:true,
			/*
				@param	id
				@param	native event object
				@param	target html element
			*/
			onmousemove:true,
			/*
				@param	native event object
			*/
			onmouseout:true,
			/*
				@param	native event object
			*/
			onmousemoving:true
		});
	},
	//inner onclick object handler
	onClick: function(e) {
		return this._mouseEvent(e,this._click,"ItemClick");
	},
	//inner ondblclick object handler
	onDblClick: function(e) {
		return this._mouseEvent(e,this._dblclick,"ItemDblClick");
	},
	
	/*
		event throttler - ignore events which occurs too fast
		during mouse moving there are a lot of event firing - we need no so much
		also, mouseout can fire when moving inside the same html container - we need to ignore such fake calls
	*/
	onMouse:function(e){
		if (dhtmlx._isIE)	//make a copy of event, will be used in timed call
			e = document.createEventObject(event);
			
		if (this._mouse_move_timer)	//clear old event timer
			window.clearTimeout(this._mouse_move_timer);
				
		//this event just inform about moving operation, we don't care about details
		this.callEvent("onMouseMoving",[e]);
		//set new event timer
		this._mouse_move_timer = window.setTimeout(dhtmlx.bind(function(){
			//called only when we have at least 100ms after previous event
			if (e.type == "mousemove")
				this.onMouseMove(e);
			else
				this.onMouseOut(e);
		},this),500);
	},
	//inner mousemove object handler
	onMouseMove: function(e) {
		if (!this._mouseEvent(e,this._mouse_move,"MouseMove"))
			this.callEvent("onMouseOut",[e||event]);
	},
	//inner mouseout object handler
	onMouseOut: function(e) {
		this.callEvent("onMouseOut",[e||event]);
	},
	//common logic for click and dbl-click processing
	_mouseEvent:function(e,hash,name){
		e=e||event;
		var trg=e.target||e.srcElement;
		var css = "";
		var id = null;
		var found = false;
		//loop through all parents
		while (trg && trg.parentNode){
			if (!found){													//if element with ID mark is not detected yet
				id = trg.getAttribute(this._id);							//check id of current one
				if (id){
					if (!this.callEvent("on"+name,[id,e,trg])) return;		//it will be triggered only for first detected ID, in case of nested elements
					found = true;											//set found flag
				}
			}
			
			if (css=trg.className){		//check if pre-defined reaction for element's css name exists
				css = css.split(" ");
				css = css[0]||css[1]; //FIXME:bad solution, workaround css classes which are starting from whitespace
				if (hash[css])
					return hash[css].call(this,e,id,trg);
			}
			trg=trg.parentNode;
		}		
		return found;	//returns true if item was located and event was triggered
	}
}


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//selection.js'*/


/*
	Behavior:SelectionModel - manage selection states
	@export
		select
		unselect
		selectAll
		unselectAll
		isSelected
		getSelected
*/
dhtmlx.SelectionModel={
	_init:function(){
		//collection of selected IDs
		this._selected = dhtmlx.toArray();
		dhtmlx.assert(this.data, "SelectionModel :: Component doesn't have DataStore");
         	
		//remove selection from deleted items
		this.data.attachEvent("onStoreUpdated",dhtmlx.bind(this._data_updated,this));
		
		
		dhtmlx.assert_event(this,{
			/*
				@param		ID of item 
				@param		true if we selecting, false if unselecting
			*/
			onbeforeselect:true,
			/*
				@param		ID of item 
			*/
			onafterselect:true,
			/*
				@param		aray of IDs, which selection state was changed
			*/
			onselectchange:true
		});
		
		
		dhtmlx.assert_property(this,{
        	select:{ "true":true, "false":true, "multiselect":true } //true == single select
    	});
	},
	//helper - linked to onStoreUpdated
	_data_updated:function(id,obj,type){
		if (type == "delete")				//remove selection from deleted items
			this._selected.remove(id);
		else if (!this.data.dataCount())	//remove selection for clearAll
			this._selected = dhtmlx.toArray();
	},
	//helper - changes state of selection for some item
	_select_mark:function(id,state,refresh){
		if (!refresh && !this.callEvent("onBeforeSelect",[id,state])) return false;
		
		this.data.get(id).$selected=state;	//set custom mark on item
		if (refresh)
			refresh.push(id);				//if we in the mass-select mode - collect all changed IDs
		else
			this._refresh_selection(id);	//othervise trigger repainting
			
		return true;
	},
	//select some item
	select:function(id,non_modal,continue_old){
		//if id not provide - works as selectAll
		if (!id) return this.selectAll();
		
		//allow an array of ids as parameter
		if (id instanceof Array){
			for (var i=0; i < id.length; i++)
				this.select(id[i], non_modal, continue_old);
			return;
		}
		
		//block selection mode
		if (continue_old && this._selected.length)
			return this.selectAll(this._selected[this._selected.length-1],id);
		//single selection mode
		if (!non_modal && (this._selected.length!=1 || this._selected[0]!=id)) 
			this.unselectAll();
		if (this.isSelected(id)){
			if (non_modal) this.unselect(id);	//ctrl-selection of already selected item
			return;
		}
		
		if (this._select_mark(id,true)){	//if not blocked from event
			this._selected.push(id);		//then add to list of selected items
			this.callEvent("onAfterSelect",[id])
		}
	},
	//unselect some item
	unselect:function(id){
		//if id is not provided  - unselect all items
		if (!id) return this.unselectAll();
		if (!this.isSelected(id)) return;
		
		if (this._select_mark(id,false))
			this._selected.remove(id);
	},
	//select all items, or all in defined range
	selectAll:function(from,to){
		var range;
		var refresh=[];
		
		if (from||to)
			range = this.data.getRange(from,to);	//get limited set if bounds defined
		else
			range = this.data.getRange();			//get all items in other case
													//in case of paging - it will be current page only
		range.each(function(obj){ 
			var d = this.data.get(obj.id);
			if (!d.$selected){	
				this._selected.push(obj.id);	
				this._select_mark(obj.id,true,refresh);
			}
			return obj.id; 
		},this);
		//repaint self
		this._refresh_selection(refresh);
	},
	//remove selection from all items
	unselectAll:function(){
		var refresh=[];
		
		this._selected.each(function(id){
			this._select_mark(id,false,refresh);	//unmark selected only
		},this);
		
		this._selected=dhtmlx.toArray();
		this._refresh_selection(refresh);	//repaint self
	},
	//returns true if item is selected
	isSelected:function(id){
		return this._selected.find(id)!=-1;
	},
	/*
		returns ID of selected items or array of IDs
		to make result predictable - as_array can be used, 
			with such flag command will always return an array 
			empty array in case when no item was selected
	*/
	getSelected:function(as_array){	
		switch(this._selected.length){
			case 0: return as_array?[]:"";
			case 1: return as_array?[this._selected[0]]:this._selected[0];
			default: return ([].concat(this._selected)); //isolation
		}
	},
	//detects which repainting mode need to be used
	_is_mass_selection:function(obj){
		 // crappy heuristic, but will do the job
		return obj.length>100 || obj.length > this.data.dataCount/2;
	},
	_refresh_selection:function(refresh){
		if (typeof refresh != "object") refresh = [refresh];
		if (!refresh.length) return;	//nothing to repaint
		
		if (this._is_mass_selection(refresh))	
			this.data.refresh();	//many items was selected - repaint whole view
		else
			for (var i=0; i < refresh.length; i++)	//repaint only selected
				this.render(refresh[i],this.data.get(refresh[i]),"update");
			
		this.callEvent("onSelectChange",[refresh]);
	}
}


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//render.js'*/


/*
	Renders collection of items
	Behavior uses plain strategy which suits only for relative small datasets
	
	@export
		locate
		show
		render
*/
dhtmlx.RenderStack={
	_init:function(){
		dhtmlx.assert(this.data,"RenderStack :: Component doesn't have DataStore");
        dhtmlx.assert(dhtmlx.Template,"dhtmlx.Template :: dhtmlx.Template is not accessible");

		//used for temporary HTML elements
		//automatically nulified during destruction
		this._html = document.createElement("DIV");


		dhtmlx.assert_event(this,{
			/*
				@param data - array of data-items, which will be rendered
				@blockable
			*/
			onbeforerender:true,
			
			/*
				@details - event will be called , only if item has a custom template
				@param obj - single data item 
			*/
			onitemrender:true,
			
			/*
			*/
			onafterrender:true
		});
		dhtmlx.assert_property(this,{
			//defines width in item-widths
			x_count:{ __type:"integer" },
			//defines height in item-heights
			y_count:{ __type:"integer" },
			//attaches paging object 
			pager:{ "__type":["object","string"]},
			//enables auto-height mode
			height:{ "auto":true }
		});
	},
	//convert single item to HTML text (templating)
	_toHTML:function(obj){
			//check if related template exist
			dhtmlx.assert((!obj.$template || this.type["template_"+obj.$template]),"RenderStack :: Unknown template: "+obj.$template);
                        
			/*mm: fix allows to call the event for all objects (PropertySheet)*/	
			//if (obj.$template) //custom template
				this.callEvent("onItemRender",[obj]);
			/*
				$template property of item, can contain a name of custom template
			*/	
			return this.type._item_start(obj,this.type)+(obj.$template?this.type["template_"+obj.$template]:this.type.template)(obj,this.type)+this.type._item_end;
	},
	//convert item to HTML object (templating)
	_toHTMLObject:function(obj){
		this._html.innerHTML = this._toHTML(obj);
		return this._html.firstChild;
	},
	//return html container by its ID
	//can return undefined if container doesn't exists
	_locateHTML:function(search_id){
		if (this._htmlmap)
			return this._htmlmap[search_id];
			
		//fill map if it doesn't created yet
		this._htmlmap={};
		
		var t = this._dataobj.childNodes;
		for (var i=0; i < t.length; i++){
			var id = t[i].getAttribute(this._id); //get item's
			if (id) 
				this._htmlmap[id]=t[i];
		}
		//call locator again, when map is filled
		return this._locateHTML(search_id);
	},
	//return id of item from html event
	locate:function(e){ return dhtmlx.html.locate(e,this._id); },
	//change scrolling state of top level container, so related item will be in visible part
	show:function(id){
		var html = this._locateHTML(id);
		if (html)
			this._dataobj.scrollTop = html.offsetTop-this._dataobj.offsetTop;
	},
	//update view after data update
	//method calls low-level rendering for related items
	//when called without parameters - all view refreshed
	render:function(id,data,type,after){
		if (id){
			var cont = this._locateHTML(id); //get html element of updated item
			switch(type){
				case "update":
					//in case of update - replace existing html with updated one
					if (!cont) return;
					var t = this._htmlmap[id] = this._toHTMLObject(data);
					dhtmlx.html.insertBefore(t, cont); 
					dhtmlx.html.remove(cont);
					break;
				case "delete":
					//in case of delete - remove related html
					if (!cont) return;
					dhtmlx.html.remove(cont);
					delete this._htmlmap[id];
					break;
				case "add":
					//in case of add - put new html at necessary position
					var t = this._htmlmap[id] = this._toHTMLObject(data);
					dhtmlx.html.insertBefore(t, this._locateHTML(this.data.next(id)), this._dataobj);
					break;
				case "move":
					//in case of move , simulate add - delete sequence
					//it will affect only rendering 
					this.render(id,data,"delete");
					this.render(id,data,"add");
					break;
			}
		} else {
			//full reset
			if (this.callEvent("onBeforeRender",[this.data])){
				//getRange - returns all elements
				this._dataobj.innerHTML = this.data.getRange().map(this._toHTML,this).join("");
				this._htmlmap = null; //clear map, it will be filled at first _locateHTML
				this.callEvent("onAfterRender",[]);
			}
		}
	},
	//pager attachs handler to onBeforeRender, to limit visible set of data 
	//data.min and data.max affect result of data.getRange()
	pager_setter:function(mode,value){ 
		this.attachEvent("onBeforeRender",function(){
			var s = this._settings.pager._settings;
			//initial value of pager = -1, waiting for real value
			if (s.page == -1) return false;	
			
			this.data.min = s.page*s.size;	//affect data.getRange
			this.data.max = (s.page+1)*s.size-1;
			return true;
		});
	
		var pager = new dhtmlx.ui.pager(value);
		//update functor
		var update = dhtmlx.bind(function(){
			this.data.refresh();
		},this);
		
		//when values of pager are changed - repaint view
		pager.attachEvent("onRefresh",update);
		//when something changed in DataStore - update configuration of pager
		//during refresh - pager can call onRefresh method which will cause repaint of view
		this.data.attachEvent("onStoreUpdated",function(data){
			var count = this.dataCount();
			if (count != pager._settings.count){
				pager._settings.count = count;
				//set first page
				//it is called first time after data loading
				//until this time pager is not rendered
				if (pager._settings.page == -1)
					pager._settings.page = 0;
				pager.refresh();
			}
		});
		return pager;
	},
	//can be used only to trigger auto-height
	height_setter:function(mode,value){
		if (value=="auto"){
			//react on resize of window and self-repainting
			this.attachEvent("onAfterRender",this._correct_height);
			dhtmlx.event(window,"resize",dhtmlx.bind(this._correct_height,this));
		}
		return value;
	},
	//update height of container to remove inner scrolls
	_correct_height:function(){
		//disable scrolls - if we are using auto-height , they are not necessary
		this._dataobj.style.overflow="hidden";
		//set min. size, so we can fetch real scroll height
		this._dataobj.style.height = "1px";
		
		var t = this._dataobj.scrollHeight;
		this._dataobj.style.height = t+"px";
		// FF has strange issue with height caculation 
		// it incorrectly detects scroll height when only small part of item is invisible
		if (dhtmlx._isFF){ 
			var t2 = this._dataobj.scrollHeight;
			if (t2!=t)
				this._dataobj.style.height = t2+"px";
		}
		this._obj.style.height = this._dataobj.style.height;
	},
	//get size of single item
	_getDimension:function(){
		var t = this.type;
		var d = (t.border||0)+(t.padding||0)*2+(t.margin||0)*2;
		//obj.x  - widht, obj.y - height
		return {x : t.width+d, y: t.height+d };
	},
	//x_count propery sets width of container, so N items can be placed on single line
	x_count_setter:function(mode,value){
		var dim = this._getDimension();
		this._dataobj.style.width = dim.x*value+18+"px";
		return value;
	},
	//x_count propery sets height of container, so N items a visible in one column
	//column will have scroll if real count of lines is greater than N
	y_count_setter:function(mode,value){
		var dim = this._getDimension();
		this._dataobj.style.height = dim.y*value+"px";
		return value;
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//virtual_render.js'*/


/*
	Renders collection of items
	Always shows y-scroll
	Can be used with huge datasets
	
	@export
		show
		render
*/

/*DHX:Depend render.js*/ 

dhtmlx.VirtualRenderStack={
	_init:function(){
		dhtmlx.assert(this.render,"VirtualRenderStack :: Object must use RenderStack first");
        	
        this._htmlmap={}; //init map of rendered elements
        //in this mode y-scroll is always visible
        //it simplifies calculations a lot
        this._dataobj.style.overflowY="scroll";
        
        //we need to repaint area each time when view resized or scrolling state is changed
        dhtmlx.event(this._dataobj,"scroll",dhtmlx.bind(this._render_visible_rows,this));
        dhtmlx.event(window,"resize",dhtmlx.bind(function(){ this.render(); },this));

		//here we store IDs of elemenst which doesn't loadede yet, but need to be rendered
		this._unrendered_area=[];
        this.attachEvent("onItemRender",function(obj){ 			//each time, during item rendering
        	if (obj.$template == "loading")						//if real data is not loaded yet
        		this._unrendered_area.push(this.data.id(obj));	//store item ID for later loading
	    });
	},
	//return html object by item's ID. Can return null for not-rendering element
	_locateHTML:function(search_id){
		//collection was filled in _render_visible_rows
		return this._htmlmap[search_id];
	},
	//adjust scrolls to make item visible
	show:function(id){
		range = this._getVisibleRange();
		var ind = this.data.indexById(id);
		//we can't use DOM method for not-rendered-yet items, so fallback to pure math
		var dy = Math.floor(ind/range._dx)*range._y;
		this._dataobj.scrollTop = dy;
	},	
	//repain self after changes in DOM
	//for add, delete, move operations - render is delayed, to minify performance impact
	render:function(id,data,type,after){
		if (id){
			var cont = this._locateHTML(id);	//old html element
			switch(type){
				case "update":
					if (!cont) return;
					//replace old with new
					var t = this._htmlmap[id] = this._toHTMLObject(data);
					dhtmlx.html.insertBefore(t, cont); 
					dhtmlx.html.remove(cont);
					break;
				case "add":
				case "delete":
				case "move":
					/*
						for all above operations, full repainting is necessary
						but from practical point of view, we need only one repainting per thread
						code below initiates double-thread-rendering trick
					*/
					this._render_delayed();
					break;
			}
		} else {
			//full repainting
			if (this.callEvent("onBeforeRender",[this.data])){
				this._htmlmap = {}; 					//nulify links to already rendered elements
				this._render_visible_rows(null, true);	
				// clear delayed-rendering, because we already have repaint view
				this._wait_for_render = false;			
				this.callEvent("onAfterRender",[]);
			}
		}
	},
	//implement double-thread-rendering pattern
	_render_delayed:function(){
		//this flag can be reset from outside, to prevent actual rendering 
		if (this._wait_for_render) return;
		this._wait_for_render = true;	
		
		window.setTimeout(dhtmlx.bind(function(){
			this.render();
		},this),1);
	},
	//create empty placeholders, which will take space before rendering
	_create_placeholder:function(height){
		var node = document.createElement("DIV");
			node.style.cssText = "height:"+height+"px; width:100%; overflow:hidden;";
		return node;
	},
	/*
		Methods get coordinatest of visible area and checks that all related items are rendered
		If, during rendering, some not-loaded items was detected - extra data loading is initiated.
		reset - flag, which forces clearing of previously rendered elements
	*/
	_render_visible_rows:function(e,reset){
		this._unrendered_area=[]; //clear results of previous calls
		
		var viewport = this._getVisibleRange();	//details of visible view
		if (!this._dataobj.firstChild || reset){	//create initial placeholder - for all view space
			this._dataobj.innerHTML="";
			this._dataobj.appendChild(this._create_placeholder(viewport._max));
			//register placeholder in collection
			this._htmlrows = [this._dataobj.firstChild];
		}
		
		/*
			virtual rendering breaks all view on rows, because we know widht of item
			we can calculate how much items can be placed on single row, and knowledge 
			of that, allows to calculate count of such rows
			
			each time after scrolling, code iterate through visible rows and render items 
			in them, if they are not rendered yet
			
			both rendered rows and placeholders are registered in _htmlrows collection
		*/

		//position of first visible row
		var t = viewport._from;
			
		while(t<=viewport._height){	//loop for all visible rows
			//skip already rendered rows
			while(this._htmlrows[t] && this._htmlrows[t]._filled && t<=viewport._height){
				t++; 
			}
			//go out if all is rendered
			if (t>viewport._height) break;
			
			//locate nearest placeholder
			var holder = t;
			while (!this._htmlrows[holder]) holder--;
			var holder_row = this._htmlrows[holder];
			
			//render elements in the row			
			var base = t*viewport._dx+(this.data.min||0);	//index of rendered item
			if (base > (this.data.max||Infinity)) break;	//check that row is in virtual bounds, defined by paging
			var nextpoint =  Math.min(base+viewport._dx-1,(this.data.max||Infinity));
			var node = this._create_placeholder(viewport._y);
			//all items in rendered row
			var range = this.data.getIndexRange(base, nextpoint);
			if (!range.length) break; 
			
			node.innerHTML=range.map(this._toHTML,this).join(""); 	//actual rendering
			for (var i=0; i < range.length; i++)					//register all new elements for later usage in _locateHTML
				this._htmlmap[this.data.idByIndex(base+i)]=node.childNodes[i];
			
			//correct placeholders
			var h = parseInt(holder_row.style.height,10);
			var delta = (t-holder)*viewport._y;
			var delta2 = (h-delta-viewport._y);
			
			//add new row to the DOOM
			dhtmlx.html.insertBefore(node,delta?holder_row.nextSibling:holder_row,this._dataobj);
			this._htmlrows[t]=node;
			node._filled = true;
			
			/*
				if new row is at start of placeholder - decrease placeholder's height
				else if new row takes whole placeholder - remove placeholder from DOM
				else 
					we are inserting row in the middle of existing placeholder
					decrease height of existing one, and add one more, 
					before the newly added row
			*/
			if (delta <= 0 && delta2>0){
				holder_row.style.height = delta2+"px";
				this._htmlrows[t+1] = holder_row;
			} else {
				if (delta<0)
					dhtmlx.html.remove(holder_row);
				else
					holder_row.style.height = delta+"px";
				if (delta2>0){ 
					var new_space = this._htmlrows[t+1] = this._create_placeholder(delta2);
					dhtmlx.html.insertBefore(new_space,node.nextSibling,this._dataobj);
				}
			}
			
			
			t++;
		}
		
		//when all done, check for non-loaded items
		if (this._unrendered_area.length){
			//we have some data to load
			//detect borders
			var from = this.indexById(this._unrendered_area[0]);
			var to = this.indexById(this._unrendered_area.pop())+1;
			if (to>from){
				//initiate data loading
				if (!this.callEvent("onDataRequest",[from, to-from])) return false;
				dhtmlx.assert(this.data.feed,"Data feed is missed");
				this.data.feed.call(this,from,to-from);
			}
		}
	},
	//calculates visible view
	_getVisibleRange:function(){
		var top = this._dataobj.scrollTop;
		var width = Math.max(this._dataobj.scrollWidth,this._dataobj.offsetWidth)-18; 	// opera returns zero scrollwidth for the empty object
		var height = this._dataobj.offsetHeight;									// 18 - scroll
		//size of single item
		var t = this.type;
		var dim = this._getDimension();

		var dx = Math.floor(width/dim.x)||1; //at least single item per row
		
		var min = Math.floor(top/dim.y);				//index of first visible row
		var dy = Math.ceil((height+top)/dim.y)-1;		//index of last visible row
		//total count of items, paging can affect this math
		var count = this.data.max?(this.data.max-this.data.min):this.data.dataCount();
		var max = Math.ceil(count/dx)*dim.y;			//size of view in rows
		
		return { _from:min, _height:dy, _top:top, _max:max, _y:dim.y, _dx:dx};
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//datastore.js'*/


/*DHX:Depend dhtmlx.js*/

/*
	DataStore is not a behavior, it standalone object, which represents collection of data.
	Call provideAPI to map data API

	@export
		exists
		idByIndex
		indexById
		get
		set
		refresh
		dataCount
		sort
		filter
		next
		previous
		clearAll
		first
		last
*/
dhtmlx.DataStore = function(){
	dhtmlx.extend(this, dhtmlx.EventSystem);
	
	this.setDriver("xml");	//default data source is an XML
	this.pull = {};						//hash of IDs
	this.order = dhtmlx.toArray();		//order of IDs
};

dhtmlx.DataStore.prototype={
	//defines type of used data driver
	//data driver is an abstraction other different data formats - xml, json, csv, etc.
	setDriver:function(type){
		dhtmlx.assert(dhtmlx.DataDriver[type],"incorrect DataDriver");
		this.driver = dhtmlx.DataDriver[type];
	},
	//process incoming raw data
	_parse:function(data){
		//get size and position of data
		var info = this.driver.getInfo(data);
		//get array of records
		var recs = this.driver.getRecords(data);
		
		var from = (info._from||0)*1;
		var j=0;
		for (var i=0; i<recs.length; i++){
			//get has of details for each record
			var temp = this.driver.getDetails(recs[i]);
			var id = this.id(temp); 	//generate ID for the record
			if (!this.pull[id]){		//if such ID already exists - update instead of insert
				this.order[j+from]=id;	
				j++;
			}
			this.pull[id]=temp;
		}
		//for all not loaded data
		for (var i=0; i < info._size; i++)
			if (!this.order[i]){
				var id = dhtmlx.uid();
				var temp = {id:id, $template:"loading"};	//create fake records
				this.pull[id]=temp;
				this.order[i]=id;
			}
		this.callEvent("onStoreLoad",[this.driver, data]);
		//repaint self after data loading
		this.refresh();
	},
	//generate id for data object
	id:function(data){
		return data.id||(data.id=dhtmlx.uid());
	},
	//get data from hash by id
	get:function(id){
		return this.pull[id];
	},
	//assigns data by id
	set:function(id,data){
		this.pull[id]=data;
		this.refresh();
	},
	//sends repainting signal
	refresh:function(id){
		if (id)
			this.callEvent("onStoreUpdated",[id, this.pull[id], "update"]);
		else
			this.callEvent("onStoreUpdated");
	},
	//converts range IDs to array of all IDs between them
	getRange:function(from,to){		
		if (!arguments.length){
			//if indexes not provided - return all visible rows
			from = this.min||0; to = Math.min((this.max||Infinity),(this.dataCount()-1));
		} else{
			from = this.indexById(from);
			to = this.indexById(to);
			if (from>to){ //can be in case of backward shift-selection
				var a=to; to=from; from=a;
			}
		}
		return this.getIndexRange(from,to);
	},
	//converts range of indexes to array of all IDs between them
	getIndexRange:function(from,to){
		to=Math.min(to,this.dataCount()-1);
		
		var ret=dhtmlx.toArray(); //result of method is rich-array
		for (var i=from; i <= to; i++)
			ret.push(this.get(this.order[i]));
		return ret;
	},
	//returns total count of elements
	dataCount:function(){
		return this.order.length;
	},
	//returns truy if item with such ID exists
	exists:function(id){
		return !!(this.pull[id]);
	},
	//nextmethod is not visible on component level, check DataMove.move
	//moves item from source index to the target index
	move:function(sindex,tindex){
		if (sindex<0 || tindex<0 || tindex > this.order.length){
			dhtmlx.error("DataStore::move","Incorrect indexes");
			return;
		}
		
			
		var id = this.idByIndex(sindex);
		var obj = this.get(id);
		
		this.order.removeAt(sindex);	//remove at old position
		//if (sindex>tindex) tindex--;	//correct shift, caused by element removing
		this.order.insertAt(id,tindex);	//insert at new position
		
		//repaint signal
		this.callEvent("onStoreUpdated",[id,obj,"move"]);
	},
	//adds item to the store
	add:function(obj,index){
		//generate id for the item
		var id = this.id(obj);
		
		//by default item is added to the end of the list
		var data_size = this.dataCount();
		if (dhtmlx.isNotDefined(index))
			index = data_size; 
			
		//check to prevent too big indexes			
		if (index > data_size){
			dhtmlx.log("Warning","DataStore:add","Index of out of bounds");
			index = Math.min(this.order.length,index);
		}
		
		if (this.exists(id)) return dhtmlx.error("Not unique ID");
		
		this.pull[id]=obj;
		this.order.insertAt(id,index);
		if (this._filter_order){	//adding during filtering
			//we can't know the location of new item in full dataset, making suggestion
			//put at end by default
			var original_index = this._filter_order.length;
			//put at start only if adding to the start and some data exists
			if (index==0 && this.order.length)
				original_index = 0;
			
			this._filter_order.insertAt(id,original_index);
		}
		
		//repaint signal
		this.callEvent("onStoreUpdated",[id,obj,"add"]);
		return id;
	},
	
	//removes element from datastore
	remove:function(id){
		//id can be an array of IDs - result of getSelect, for example
		if (id instanceof Array){
			for (var i=0; i < id.length; i++)
				this.remove(id[i]);
			return;
		}
		
		if (!this.exists(id)) return dhtmlx.error("Not existing ID",id);
		var obj = this.get(id);	//save for later event
		//clear from collections
		this.order.remove(id);
		if (this._filter_order) 
			this._filter_order.remove(id);
			
		delete this.pull[id];
		//repaint signal
		this.callEvent("onStoreUpdated",[id,obj,"delete"]);
	},
	//deletes all records in datastore
	clearAll:function(){
		//instead of deleting one by one - just reset inner collections
		this.pull = {};
		this.order = dhtmlx.toArray();
		this._filter_order = null;
		this.refresh();
	},
	//converts id to index
	idByIndex:function(index){
		if (index>=this.order.length || index<0)
			dhtmlx.log("Warning","DataStore::idByIndex Incorrect index");
			
		return this.order[index];
	},
	//converts index to id
	indexById:function(id){
		var res = this.order.find(id);	//slower than idByIndex
		
		if (!res)
			dhtmlx.log("Warning","DataStore::indexById Non-existing ID");
			
		return res;
	},
	//returns ID of next element
	next:function(id,step){
		return this.order[this.indexById(id)+(step||1)];
	},
	//returns ID of first element
	first:function(){
		return this.order[0];
	},
	//returns ID of last element
	last:function(){
		return this.order[this.order.length-1];
	},
	//returns ID of previous element
	previous:function(id,step){
		return this.order[this.indexById(id)-(step||1)];
	},
	/*
		sort data in collection
		text - name of property
		direction - "asc" or "desc"
		
		or
		
		text - sorting function
		direction - "asc" or "desc"
		
		Sorting function will accept 2 parameters and must return 1,0,-1, based on desired order
	*/
	sort:function(text,direction){
		var sorter = text;
		if (typeof text == "string") 
			sorter = function(a,b){		//default sorting, compare as case-insensitive string
				return (a[text]||"").toLowerCase()>(b[text]||"").toLowerCase()?1:-1;
			};
		//get array of IDs
		var neworder = this.getRange();
		neworder.sort(sorter);
		this.order = neworder.map(function(obj){ return this.id(obj); },this);
		
		//reverse order for desc sorting
		if (direction && direction.toLowerCase() != "asc")
			this.order.reverse();
		//repaint self
		this.refresh();
	},
	/*
		Filter datasource
		
		text - property, by which filter
		value - filter mask
		
		or
		
		text  - filter method
		
		Filter method will receive data object and must return true or false
	*/
	filter:function(text,value){
		//remove previous filtering , if any
		if (this._filter_order){
			this.order = this._filter_order;
			delete this._filter_order;
		}
		//if text not define -just unfilter previous state and exit
		if (text){
			var filter = text;
			if (typeof text == "string")
				filter = function(obj,value){	//default filter - string start from, case in-sensitive
					return obj[text].toLowerCase().indexOf(value)!=-1;
				};
			
			value = value.toString().toLowerCase();			
			var neworder = dhtmlx.toArray();
			this.order.each(function(id){
				if (filter(this.get(id),value))
					neworder.push(id);
			},this);
			//set new order of items, store original
			this._filter_order = this.order;
			this.order = neworder;
		}
		//repaint self
		this.refresh();
	},
	/*
		Iterate through collection
	*/
	each:function(method,master){
		for (var i=0; i<this.order.length; i++)
			method.call((master||this), this.get(this.order[i]));
	},
	/*
		map inner methods to some distant object
	*/
	provideApi:function(target,eventable){
		
		dhtmlx.assert_event(target,{
			onBeforeAdd:true,
			onAfterAdd:true,
			onBeforeDelete:true,
			onAfterDelete:true
		});
		
		target.add 		= dhtmlx.methodPush(this,"add",(eventable?"Add":0));
		target.remove 	= dhtmlx.methodPush(this,"remove",(eventable?"Delete":0));
		var list = ["exists","idByIndex","indexById","get","set","refresh","dataCount","sort","filter","next","previous","clearAll","first","last"];
		for (var i=0; i < list.length; i++)
			target[list[i]]=dhtmlx.methodPush(this,list[i]);
	}
};


/* DHX DEPEND FROM FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//load.js'*/


/* 
   ajax operations 
   
   can be used for direct loading as
      dhtmlx.ajax(ulr, callback)
   or
      dhtmlx.ajax().get(url)
      dhtmlx.ajax().post(url)

*/

/*DHX:Depend datastore.js*/
/*DHX:Depend dhtmlx.js*/

dhtmlx.ajax = function(url,call,master){
   //if parameters was provided - made fast call
   if (arguments.length!=0){
      var http_request = new dhtmlx.ajax();
      if (master) http_request.master=master;
      http_request.get(url,null,call);
   }
   if (!this.getXHR) return new dhtmlx.ajax(); //allow to create new instance without direct new declaration
   
   return this;
};
dhtmlx.ajax.prototype={
   //creates xmlHTTP object
   getXHR:function(){   
      if (dhtmlx._isIE)
         return new ActiveXObject("Microsoft.xmlHTTP");
      else 
         return new XMLHttpRequest();
   },
   /*
      send data to the server
      params - hash of properties which will be added to the url
      call - callback, can be an array of functions
   */
   send:function(url,params,call){
      var x=this.getXHR();
      if (typeof call == "function")
         call = [call];
      //add extra params to the url
      if (typeof params == "object"){
          var t=[];
          for (var a in params)
             t.push(a+"="+encodeURIComponent(params[a]));// utf-8 escaping
          params=t.join("&");
       }
       if (params && !this.post){
          url=url+(url.indexOf("?")!=-1 ? "&" : "?")+params;
          params=null;
       }
       
      x.open(this.post?"POST":"GET",url,!this._sync);
      if (this.post)
         x.setRequestHeader('Content-type','application/x-www-form-urlencoded');
         
      //async mode, define loading callback
      if (!this._sync){
         var self=this;             
         x.onreadystatechange= function(){
            if  (!x.readyState || x.readyState == 4){
               dhtmlx.log_full_time("data_loading");   //log rendering time
               if (call && self) 
                  for (var i=0; i < call.length; i++)   //there can be multiple callbacks
                     if (call[i])
                        call[i].call((self.master||self),x.responseText,x.responseXML,x);
               self.master=null;
               call=x=self=null;   //anti-leak
            }
         };
      }
      
      x.send(params||null);
      return x; //return XHR, which can be used in case of sync. mode
   },
   //GET request
   get:function(url,params,call){
      this.post=false;
      return this.send(url,params,call);
   },
   //POST request
   post:function(url,params,call){
      this.post=true;
      return this.send(url,params,call);
   }, 
   sync:function(){
		this._sync = true;
		return this;
   }
};


/*
   Behavior:DataLoader - load data in the component
   
   @export
      load
      parse
*/
dhtmlx.DataLoader={
   _init:function(){
      //prepare data store
      this.data = new dhtmlx.DataStore();
      
      dhtmlx.assert_event(this,{
         onxls:true,
         onxle:true
      });
   },
   //loads data from external URL
   load:function(url,call){
      this.callEvent("onXLS",[]);
      if (typeof call == "string"){   //second parameter can be a loading type or callback
         this.data.setDriver(call);
         call = arguments[2];
      }
      //prepare data feed for dyn. loading
      if (!this.data.feed)
         this.data.feed = function(from,count){
            //allow only single request at same time
            if (this._load_count)   
               return this._load_count=[from,count];   //save last ignored request
            else
               this._load_count=true;
               
            this.load(url+((url.indexOf("?")==-1)?"?":"&")+"posStart="+from+"&count="+count,function(){
               //after loading check if we have some ignored requests
               var temp = this._load_count;
               this._load_count = false;
               if (typeof temp =="object")
                  this.data.feed.apply(this, temp);   //load last ignored request
            });
         };
      //load data by async ajax call
      dhtmlx.ajax(url,[this._onLoad,call],this);
   },
   //loads data from object
   parse:function(data,type){
      this.callEvent("onXLS",[]);
      if (type)
         this.data.setDriver(type);
      this._onLoad(data,null);
   },
   //default after loading callback
   _onLoad:function(text,xml,loader){
      this.data._parse(this.data.driver.toObject(text,xml));
      this.callEvent("onXLE",[]);
   }
};

/*
   Abstraction layer for different data types
*/

dhtmlx.DataDriver={};
dhtmlx.DataDriver.json={
   //convert json string to json object if necessary
   toObject:function(data){
      if (typeof data == "string"){
         eval ("dhtmlx.temp="+data);
         return dhtmlx.temp;
      }
      return data;
   },
   //get array of records
   getRecords:function(data){
      if (data && !(data instanceof Array))
         return [data];
      return data;
   },
   //get hash of properties for single record
   getDetails:function(data){
      return data;
   },
   //get count of data and position at which new data need to be inserted
   getInfo:function(data){
      return { 
         _size:(data.total_count||0),
         _from:(data.pos||0)
      };
   }
};

dhtmlx.DataDriver.html={
   /*
      incoming data can be
         - collection of nodes
         - ID of parent container
         - HTML text
   */
   toObject:function(data){
      if (typeof data == "string"){
         var t=null;
         if (data.indexOf("<")==-1)   //if no tags inside - probably its an ID
            t = dhtmlx.toNode(data);
         if (!t){
            t=document.createElement("DIV");
            t.innerHTML = data;
         }
         
         return t.getElementsByTagName(this.tag);
      }
      return data;
   },
   //get array of records
   getRecords:function(data){
      if (data.tagName)
         return data.childNodes;
      return data;
   },
   //get hash of properties for single record
   getDetails:function(data){
      return dhtmlx.DataDriver.xml.tagToObject(data);
   },
   //dyn loading is not supported by HTML data source
   getInfo:function(data){
      return { 
         _size:0,
         _from:0
      };
   },
   tag: "LI"
};

dhtmlx.DataDriver.jsarray={
   //eval jsarray string to jsarray object if necessary
   toObject:function(data){
      if (typeof data == "string"){
         eval ("dhtmlx.temp="+data);
         return dhtmlx.temp;
      }
      return data;
   },
   //get array of records
   getRecords:function(data){
      return data;
   },
   //get hash of properties for single record, in case of array they will have names as "data{index}"
   getDetails:function(data){
      var result = {};
      for (var i=0; i < data.length; i++) 
         result["data"+i]=data[i];
         
      return result;
   },
   //dyn loading is not supported by js-array data source
   getInfo:function(data){
      return { 
         _size:0,
         _from:0
      };
   }
};

dhtmlx.DataDriver.csv={
   //incoming data always a string
   toObject:function(data){
      return data;
   },
   //get array of records
   getRecords:function(data){
      return data.split(this.row);
   },
   //get hash of properties for single record, data named as "data{index}"
   getDetails:function(data){
      data = this.stringToArray(data);
      var result = {};
      for (var i=0; i < data.length; i++) 
         result["data"+i]=data[i];
         
      return result;
   },
   //dyn loading is not supported by csv data source
   getInfo:function(data){
      return { 
         _size:0,
         _from:0
      };
   },
   //split string in array, takes string surrounding quotes in account
   stringToArray:function(data){
      data = data.split(this.cell);
      for (var i=0; i < data.length; i++)
         data[i] = data[i].replace(/^[ \t\n\r]*(\"|)/g,"").replace(/(\"|)[ \t\n\r]*$/g,"");
      return data;
   },
   row:"\n",   //default row separator
   cell:","   //default cell separator
};

dhtmlx.DataDriver.xml={
   //convert xml string to xml object if necessary
   toObject:function(text,xml){
      if (xml && (xml=this.checkResponse(text,xml)))   //checkResponse - fix incorrect content type and extra whitespaces errors
         return xml;
      if (typeof text == "string"){
         return this.fromString(text);
      }
      return text;
   },
   //get array of records
   getRecords:function(data){
      return this.xpath(data,this.records);
   },
   records:"/*/item",
   userdata:"/*/userdata",
   //get hash of properties for single record
   getDetails:function(data){
      return this.tagToObject(data,{});
   },
   getUserData:function(data,col){
   		col = col || {};
		var ud = this.xpath(data,this.userdata);
		for (var i=0; i < ud.length; i++) {
			var udx = this.tagToObject(ud[i]);
			col[udx.name] = udx.value;
		}
		return col;
   },
   //get count of data and position at which new data_loading need to be inserted
   getInfo:function(data){
      return { 
         _size:(data.documentElement.getAttribute("total_count")||0),
         _from:(data.documentElement.getAttribute("pos")||0)
      };
   },
   //xpath helper
   xpath:function(xml,path){
      if (window.XPathResult){   //FF, KHTML, Opera
         var node=xml;
         if(xml.nodeName.indexOf("document")==-1)
         xml=xml.ownerDocument;
         var res = [];
         var col = xml.evaluate(path, node, null, XPathResult.ANY_TYPE, null);
         var temp;
         while (temp = col.iterateNext()) 
            res.push(temp);
         return res;
      }                      //IE
      return xml.selectNodes(path);
   },
   //convert xml tag to js object, all subtags and attributes are mapped to the properties of result object
   tagToObject:function(tag,z){
      z=z||{};
      //map attributes
      var a=tag.attributes;
      for (var i=0; i<a.length; i++)
         z[a[i].name]=a[i].value;
      //map subtags
      var flag=false;
      var b=tag.childNodes;
      for (var i=0; i<b.length; i++){
         if (b[i].nodeType==1){
            z[b[i].tagName]=this.tagToObject(b[i],{});   //sub-object for complex subtags
            flag=true;
         }
      }
      
      if (!a.length && !flag)
         return this.nodeValue(tag);
      //each object will have its text content as "value" property
      z.value = this.nodeValue(tag);
      return z;
   },
   //get value of xml node 
   nodeValue:function(node){
      if (node.firstChild)
         return node.firstChild.data;   //FIXME - long text nodes in FF not supported for now
      return "";
   },
   //convert XML string to XML object
   fromString:function(xmlString){
      if (window.DOMParser)      // FF, KHTML, Opera
         return (new DOMParser()).parseFromString(xmlString,"text/xml");
      if (window.ActiveXObject){   // IE, utf-8 only 
         temp=new ActiveXObject("Microsoft.xmlDOM");
         temp.loadXML(xmlString);
         return temp;
      }
      dhtmlx.error("Load from xml string is not supported");
   },
   //check is XML correct and try to reparse it if its invalid
   checkResponse:function(text,xml){ 
      if (xml && ( xml.firstChild && xml.firstChild.tagName != "parsererror") )
          return xml;
       //parsing as string resolves incorrect content type
       //regexp removes whitespaces before xml declaration, which is vital for FF
      var a=this.from_string(text.responseText.replace(/^[\s]+/,""));
      if (a) return a;
      
      dhtmlx.error("xml can't be parsed",text.responseText);
   }
};




/* DHX INITIAL FILE 'd:/http/dhtmlx/main/dhtmlxCore/sources//dataview.js'*/


/*
	UI:DataView
*/

/*DHX:Depend dataview.css*/
/*DHX:Depend types*/
/*DHX:Depend ../imgs/dataview*/


/*DHX:Depend load.js*/ 		/*DHX:Depend virtual_render.js*/ 		/*DHX:Depend selection.js*/
/*DHX:Depend mouse.js*/ 	/*DHX:Depend key.js*/ 					/*DHX:Depend edit.js*/ 
/*DHX:Depend drag.js*/		/*DHX:Depend dataprocessor_hook.js*/ 	/*DHX:Depend autotooltip.js*/ 
/*DHX:Depend pager.js*/		/*DHX:Depend destructor.js*/			/*DHX:Depend dhtmlx.js*/
/*DHX:Depend config.js*/




//container - can be a HTML container or it's ID
dhtmlXDataView = function(container){
	//next data is only for debug purposes
	this.name = "DataView";	//name of component
	this.version = "3.0";	//version of component
	
	//enable configuration
	dhtmlx.extend(this, dhtmlx.Settings);
	this._parseContainer(container,"dhx_dataview");	//assigns parent container
	
	//behaviors
	dhtmlx.extend(this, dhtmlx.DataLoader);	//includes creation of DataStore
	dhtmlx.extend(this, dhtmlx.EventSystem);
	dhtmlx.extend(this, dhtmlx.RenderStack);
	dhtmlx.extend(this, dhtmlx.SelectionModel);
	dhtmlx.extend(this, dhtmlx.MouseEvents);
	dhtmlx.extend(this, dhtmlx.KeyEvents);
	dhtmlx.extend(this, dhtmlx.EditAbility);
	dhtmlx.extend(this, dhtmlx.DataMove);
	dhtmlx.extend(this, dhtmlx.DragItem);
	dhtmlx.extend(this, dhtmlx.DataProcessor);
	dhtmlx.extend(this, dhtmlx.AutoTooltip);
	dhtmlx.extend(this, dhtmlx.Destruction);
	
	
	//all other properties is nested from behaviors
	dhtmlx.assert_property(this,{
		auto_scroll:{ "true":true, "false":false}
	});
	
	//default settings
	this._parseSettings(container,{
		drag:false,
		edit:false,
		select:"multiselect", //multiselection is enabled by default
		type:"default"
	});
	
	//in case of auto-height we use plain rendering
	if (this._settings.height!="auto")
		dhtmlx.extend(this, dhtmlx.VirtualRenderStack);	//extends RenderStack behavior
	
	//render self , each time when data is updated
	this.data.attachEvent("onStoreUpdated",dhtmlx.bind(this.render,this));
	//map API of DataStore on self
	this.data.provideApi(this,true);
};
dhtmlXDataView.prototype={
	/*
		Called each time when dragIn or dragOut situation occurs
		context - drag context object
		ev - native event
	*/
	dragMarker:function(context,ev){
		//get HTML element by item ID
		//can be null - when item is not rendered yet
		var el = this._locateHTML(context.target);
		
		//ficon and some other types share common bg marker
		if (this.type.drag_marker){
			if (this._drag_marker){
				//clear old drag marker position
				this._drag_marker.style.backgroundImage="";
				this._drag_marker.style.backgroundRepeat="";
			}
			
			// if item already rendered
			if (el) {
				//show drag marker
				el.style.backgroundImage="url("+(dhtmlx.image_path||"")+this.type.drag_marker+")";
				el.style.backgroundRepeat="no-repeat";
				this._drag_marker = el;
			}
		}
		
		//auto-scroll during d-n-d, only if related option is enabled
		if (el && this._settings.auto_scroll){
			//maybe it can be moved to the drag behavior
			var dy = el.offsetTop;
			var dh = el.offsetHeight;
			var py = this._obj.scrollTop;
			var ph = this._obj.offsetHeight;
			//scroll up or down is mouse already pointing on top|bottom visible item
			if (dy-dh > 0 && dy-dh*0.75 < py)
				py = Math.max(dy-dh, 0);
			else if (dy+dh/0.75 > py+ph)
				py = py+dh;
			
			this._obj.scrollTop = py;
		}
		return true;
	},
	//attribute , which will be used for ID storing
	_id:"dhx_f_id",
	//css class to action map, for onclick event
	_click:{
		dhx_dataview_item:function(e,id){ 
			//click on item
			this.stopEdit(false,id);	
			if (this._settings.select){
				if (this._settings.select=="multiselect")
					this.select(id, e.ctrlKey, e.shiftKey); 	//multiselection
				else
					this.select(id);
			}
		}	
	},
	//css class to action map, for dblclick event
	_dblclick:{
		dhx_dataview_item:function(e,id){ 
			//dblclick on item
			if (this._settings.edit)
				this.edit(id);	//edit it!
		}
	},
	//css class to action map, for mousemove event
	_mouse_move:{
	},
	types:{
		"default":{
			css:"default",
			//normal state of item
			template:dhtmlx.Template.fromHTML("<div style='padding:10px; white-space:nowrap; overflow:hidden;'>{obj.text}</div>"),
			//template for edit state of item
			template_edit:dhtmlx.Template.fromHTML("<div style='padding:10px; white-space:nowrap; overflow:hidden;'><textarea style='width:100%; height:100%;' bind='obj.text'></textarea></div>"),
			//in case of dyn. loading - temporary spacer
			template_loading:dhtmlx.Template.fromHTML("<div style='padding:10px; white-space:nowrap; overflow:hidden;'>Loading...</div>"),
			width:210,
			height:115,
			margin:0,
			padding:10,
			border:1
		}
	},
	template_item_start:dhtmlx.Template.fromHTML("<div dhx_f_id='{-obj.id}' class='dhx_dataview_item dhx_dataview_{obj.css}_item{-obj.$selected?_selected:}' style='width:{obj.width}px; height:{obj.height}px; padding:{obj.padding}px; margin:{obj.margin}px; float:left; overflow:hidden;'>"),
	template_item_end:dhtmlx.Template.fromHTML("</div>")
};