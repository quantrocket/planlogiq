(function(){var C,D;var B;scheduler.config.collision_limit=1;scheduler.attachEvent("onBeforeDrag",function(F){var E=scheduler._props?scheduler._props[this._mode]:null;if(E&&F){C=this.getEvent(F)[E.map_to];D=this.getEvent(F).start_date}return true});scheduler.attachEvent("onBeforeLightbox",function(F){var E=scheduler.getEvent(F);B=[E.start_date,E.end_date];return true});scheduler.attachEvent("onEventChanged",function(F){if(!F){return true}var E=scheduler.getEvent(F);if(!A(E)){if(!B){return false}E.start_date=B[0];E.end_date=B[1];E._timed=this.is_one_day_event(E)}return true});scheduler.attachEvent("onBeforeEventChanged",function(E,F,G){return A(E)});scheduler.attachEvent("onEventSave",function(F,E){if(E.rec_type){scheduler._roll_back_dates(E)}return A(E)});function A(L){var N=[];if(L.rec_type){var F=scheduler.getRecDates(L);for(var G=0;G<F.length;G++){var K=scheduler.getEvents(F[G].start_date,F[G].end_date);for(var H=0;H<K.length;H++){if((K[H].event_pid||K[H].id)!=L.id){N.push(K[H])}}}N.push(L)}else{N=scheduler.getEvents(L.start_date,L.end_date)}var E=scheduler._props?scheduler._props[scheduler._mode]:null;var M=true;if(E){var J=0;for(var I=0;I<N.length;I++){if(N[I][E.map_to]==L[E.map_to]){J++}}if(J>scheduler.config.collision_limit){scheduler._drag_event.start_date=D;L[E.map_to]=C;M=false}}else{if(N.length>scheduler.config.collision_limit){M=false}}if(!M){return !scheduler.callEvent("onEventCollision",[L,N])}return M}})();