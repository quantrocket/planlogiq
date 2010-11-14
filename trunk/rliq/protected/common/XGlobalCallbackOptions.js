// JavaScript Document

if(typeof(Ajax) != 'undefined'){

	if(xgco.onLoading){
		Ajax.Responders.register({"onLoading" : xgco.onLoading});					
	}
	if(xgco.onSuccess){
		Ajax.Responders.register({"onSuccess" : xgco.onSuccess});					
	}
	if(xgco.onFailure){
		Ajax.Responders.register({"onFailure" : xgco.onFailure});					
	}	
	if(xgco.onLoaded){
		Ajax.Responders.register({"onLoaded" : xgco.onLoaded});					
	}	
	if(xgco.onInteractive){
		Ajax.Responders.register({"onInteractive" : xgco.onInteractive});					
	}	
	if(xgco.onComplete){
		Ajax.Responders.register({"onComplete" : xgco.onComplete});					
	}	
}
