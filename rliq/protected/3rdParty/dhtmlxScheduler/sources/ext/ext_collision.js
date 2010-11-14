(function(){

var temp_section,temp_time;
var before;

scheduler.config.collision_limit = 1;	
scheduler.attachEvent("onBeforeDrag",function(id){
	var pr = scheduler._props?scheduler._props[this._mode]:null;
	if (pr && id){
		temp_section = this.getEvent(id)[pr.map_to];
		temp_time = this.getEvent(id).start_date;
	}
	return true;
});
scheduler.attachEvent("onBeforeLightbox",function(id){
	var ev = scheduler.getEvent(id);
	before = [ev.start_date, ev.end_date];
	return true;
});
scheduler.attachEvent("onEventChanged",function(id){
	if (!id) return true;
	var ev = scheduler.getEvent(id);
	if (!collision_check(ev)){
		if (!before) return false;
		ev.start_date = before[0];
		ev.end_date = before[1];
		ev._timed=this.is_one_day_event(ev);
	};
	return true;
});
scheduler.attachEvent("onBeforeEventChanged",function(ev,e,is_new){
	return collision_check(ev);
});
scheduler.attachEvent("onEventSave",function(id, edited_ev){
	//var ev = scheduler.getEvent(id);
	if(edited_ev.rec_type)
		scheduler._roll_back_dates(edited_ev);
	
	return collision_check(edited_ev);
});


function collision_check(ev){
	var evs = [];
	
	if (ev.rec_type) {
		var evs_dates = scheduler.getRecDates(ev);
		for(var k=0; k<evs_dates.length; k++) {
			var tevs = scheduler.getEvents(evs_dates[k].start_date, evs_dates[k].end_date);
			for(var j=0; j<tevs.length; j++) { 
				if ((tevs[j].event_pid || tevs[j].id) != ev.id )
					evs.push(tevs[j]);
			}
		}
		evs.push(ev);
	} else {
		evs = scheduler.getEvents(ev.start_date, ev.end_date);
	}
	
	var pr = scheduler._props?scheduler._props[scheduler._mode]:null;
	var single = true;
	
	if (pr){
		var count=0;
		for (var i=0; i < evs.length; i++)
			if (evs[i][pr.map_to]==ev[pr.map_to])
				count++;

		if (count>scheduler.config.collision_limit){
			scheduler._drag_event.start_date = temp_time;
			ev[pr.map_to] = temp_section;
			single = false;
		}
	} else
		if (evs.length>scheduler.config.collision_limit)
			single = false;
			
	if (!single) return !scheduler.callEvent("onEventCollision",[ev,evs]);
	return single;
	
};

})();