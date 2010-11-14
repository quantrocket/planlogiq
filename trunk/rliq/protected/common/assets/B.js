Prado.WebUI.Response = {
	redirect : function(element,url) {
		window.location.href = url;
	}
}



/**
 * @author toddcullen
 * @author http://forwarddevelopment.blogspot.com/2007/06/updated-custom-event-system-for.html
 */
CustomEvent = {};
CustomEvent.Events = {};
CustomEvent.Events.Base = Class.create();
CustomEvent.Events.Base.prototype = {
 initialize : function(){
  this.type = "CustomEvent.Events.Base";
 }
}
CustomEvent.EventController = Class.create();
CustomEvent.EventController.prototype = {
 initialize: function(){
  this.listeners = $A([]);
 },
 addEventListener: function(n, f){
  this.listeners.push({name: n, callback: f});
 },
 removeListener: function(n, f){
  this.listeners = this.listeners.without({name: n, callback: f});
 },
 dispatchEvent: function(n, e){
  for(var x=0; x<this.listeners.length; x++){
   if(this.listeners[x].name == n){
    this.listeners[x].callback(e);
   }
  }
 }
}
var EventController = new CustomEvent.EventController();



var B = B || {};