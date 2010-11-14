scheduler.xy.map_date_width=188;scheduler.xy.map_description_width=400;scheduler.config.map_resolve_event_location=true;scheduler.config.map_resolve_user_location=true;scheduler.config.map_initial_position=new google.maps.LatLng(48.724,8.215);scheduler.config.map_error_position=new google.maps.LatLng(15,15);scheduler.config.map_infowindow_max_width=300;scheduler.config.map_type=google.maps.MapTypeId.ROADMAP;scheduler.config.map_zoom_after_resolve=15;scheduler.locale.labels.marker_geo_success="It seems you are here.";scheduler.locale.labels.marker_geo_fail="Sorry, could not get your current position using geolocation.";scheduler.templates.marker_date=scheduler.date.date_to_str("%Y-%m-%d %H:%i");scheduler.templates.marker_text=function(C,A,B){return"<div><b>"+B.text+"</b><br/><br/>"+(B.event_location||"")+"<br/><br/>"+scheduler.templates.marker_date(C)+" - "+scheduler.templates.marker_date(A)+"</div>"};scheduler.dblclick_dhx_map_area=function(){if(!this.config.readonly&&this.config.dblclick_create){this.addEventNow()}};scheduler.templates.map_time=function(C,A,B){if(B._timed){return this.day_date(B.start_date,B.end_date,B)+" "+this.event_date(C)}else{return scheduler.templates.day_date(C)+" &ndash; "+scheduler.templates.day_date(A)}};scheduler.templates.map_text=function(A){return A.text};scheduler.date.map_start=function(A){return A};scheduler.attachEvent("onTemplatesReady",function(){function D(){_isPositionSet=false;var H=document.createElement("div");H.className="dhx_map";H.id="dhx_gmap";H.style.dispay="none";node=document.getElementById("scheduler_here");node.appendChild(H);scheduler._els.dhx_gmap=[];scheduler._els.dhx_gmap.push(H);C("dhx_gmap");var G={zoom:scheduler.config.map_inital_zoom||10,center:scheduler.config.map_initial_position,mapTypeId:scheduler.config.map_type||google.maps.MapTypeId.ROADMAP};map=new google.maps.Map(document.getElementById("dhx_gmap"),G);map.disableDefaultUI=false;map.disableDoubleClickZoom=true;google.maps.event.addListener(map,"dblclick",function(J){if(!scheduler.config.readonly&&scheduler.config.dblclick_create){point=J.latLng;geocoder.geocode({latLng:point},function(L,K){if(K==google.maps.GeocoderStatus.OK){point=L[0].geometry.location;scheduler.addEventNow({lat:point.lat(),lng:point.lng(),event_location:L[0].formatted_address})}})}});var I={content:""};if(scheduler.config.map_infowindow_max_width){I.maxWidth=scheduler.config.map_infowindow_max_width}scheduler.map={_points:[],_markers:[],_infowindow:new google.maps.InfoWindow(I),_infowindows_content:[],_initialization_count:-1};geocoder=new google.maps.Geocoder();if(scheduler.config.map_resolve_user_location){if(navigator.geolocation){if(!_isPositionSet){navigator.geolocation.getCurrentPosition(function(J){var K=new google.maps.LatLng(J.coords.latitude,J.coords.longitude);map.setCenter(K);map.setZoom(scheduler.config.map_zoom_after_resolve||10);scheduler.map._infowindow.setContent(scheduler.locale.labels.marker_geo_success);scheduler.map._infowindow.position=map.getCenter();scheduler.map._infowindow.open(map);_isPositionSet=true},function(){scheduler.map._infowindow.setContent(scheduler.locale.labels.marker_geo_fail);scheduler.map._infowindow.setPosition(map.getCenter());scheduler.map._infowindow.open(map);_isPositionSet=true})}}}google.maps.event.addListener(map,"resize",function(J){H.style.zIndex="5";map.setZoom(map.getZoom())});google.maps.event.addListener(map,"tilesloaded",function(J){H.style.zIndex="5"})}D();scheduler.attachEvent("onSchedulerResize",function(){if(this._mode=="map"){this.map_view(true)}});var A=scheduler.render_data;scheduler.render_data=function(G,J){if(this._mode=="map"){E();var I=scheduler.get_visible_events();for(var H=0;H<I.length;H++){if(!scheduler.map._markers[I[H].id]){B(I[H],false,false)}}}else{return A.apply(this,arguments)}};function F(H){if(H){var G=scheduler.locale.labels;scheduler._els.dhx_cal_header[0].innerHTML="<div class='dhx_map_line' style='width: "+(scheduler.xy.map_date_width+scheduler.xy.map_description_width+2)+"px;' ><div style='width: "+scheduler.xy.map_date_width+"px;'>"+G.date+"</div><div class='headline_description' style='width: "+scheduler.xy.map_description_width+"px;'>"+G.description+"</div></div>";scheduler._table_view=true;scheduler.set_sizes()}}function E(){var H=scheduler._date;var L=scheduler.get_visible_events();L.sort(function(N,M){return N.start_date>M.start_date?1:-1});var K="<div class='dhx_map_area'>";for(var J=0;J<L.length;J++){var G=(L[J].id==scheduler._selected_event_id)?"dhx_map_line highlight":"dhx_map_line";K+="<div class='"+G+"' event_id='"+L[J].id+"' style='"+(L[J]._text_style||"")+" width: "+(scheduler.xy.map_date_width+scheduler.xy.map_description_width+2)+"px;'><div style='width: "+scheduler.xy.map_date_width+"px;' >"+scheduler.templates.map_time(L[J].start_date,L[J].end_date,L[J])+"</div>";K+="<div class='dhx_event_icon icon_details'>&nbsp</div>";K+="<div class='line_description' style='width:"+(scheduler.xy.map_description_width-25)+"px;'>"+scheduler.templates.map_text(L[J])+"</div></div>"}K+="<div class='dhx_v_border' style='left: "+(scheduler.xy.map_date_width-2)+"px;'></div><div class='dhx_v_border_description'></div></div>";scheduler._els.dhx_cal_data[0].scrollTop=0;scheduler._els.dhx_cal_data[0].innerHTML=K;scheduler._els.dhx_cal_data[0].style.width=(scheduler.xy.map_date_width+scheduler.xy.map_description_width+1)+"px";var I=scheduler._els.dhx_cal_data[0].firstChild.childNodes;scheduler._els.dhx_cal_date[0].innerHTML="";scheduler._rendered=[];for(var J=0;J<I.length-2;J++){scheduler._rendered[J]=I[J]}}function C(G){var H=document.getElementById(G);H.style.height=(scheduler._y-scheduler.xy.nav_height)+"px";H.style.width=(scheduler._x-scheduler.xy.map_date_width-scheduler.xy.map_description_width-1)+"px";H.style.marginLeft=(scheduler.xy.map_date_width+scheduler.xy.map_description_width+1)+"px";H.style.marginTop=(scheduler.xy.nav_height+2)+"px"}scheduler.map_view=function(J){scheduler.map._initialization_count++;var I=scheduler._els.dhx_gmap[0];scheduler._els.dhx_cal_data[0].style.width=(scheduler.xy.map_date_width+scheduler.xy.map_description_width+1)+"px";scheduler._min_date=scheduler.config.map_start||(new Date());scheduler._max_date=scheduler.config.map_end||(new Date(9999,1,1));scheduler._table_view=true;F(J);if(J){E();I.style.display="block";C("dhx_gmap");var H=scheduler.get_visible_events();for(var G=0;G<H.length;G++){if(!scheduler.map._markers[H[G].id]){B(H[G])}}}else{I.style.display="none"}google.maps.event.trigger(map,"resize");if(scheduler.map._initialization_count===0){map.setCenter(scheduler.config.map_initial_position)}};function B(K,H,I){if(K.lat&&K.lng){var G=new google.maps.LatLng(K.lat,K.lng)}else{var G=scheduler.config.map_error_position}var J=scheduler.templates.marker_text(K.start_date,K.end_date,K);if(!scheduler._new_event){scheduler.map._markers[K.id]=new google.maps.Marker({position:G,map:map});scheduler.map._infowindows_content[K.id]=J;google.maps.event.addListener(scheduler.map._markers[K.id],"click",function(){scheduler.map._infowindow.setContent(scheduler.map._infowindows_content[K.id]);scheduler.map._infowindow.open(map,scheduler.map._markers[K.id]);scheduler._selected_event_id=K.id;scheduler.render_data()});scheduler.map._points[K.id]=G;if(H){map.setCenter(scheduler.map._points[K.id])}if(I){scheduler.callEvent("onClick",[K.id])}}}scheduler.attachEvent("onClick",function(I,G){if(this._mode=="map"){scheduler._selected_event_id=I;for(var H=0;H<scheduler._rendered.length;H++){scheduler._rendered[H].className="dhx_map_line";if(scheduler._rendered[H].getAttribute("event_id")==I){scheduler._rendered[H].className+=" highlight"}}if(scheduler.map._points[I]&&scheduler.map._markers[I]){map.panTo(scheduler.map._points[I]);google.maps.event.trigger(scheduler.map._markers[I],"click")}}return true});_displayEventOnMap=function(G){if(G.event_location&&geocoder){geocoder.geocode({address:G.event_location},function(J,I){var H={};if(I!=google.maps.GeocoderStatus.OK){H=scheduler.callEvent("onLocationError",[G.id]);if(!H||H===true){H=scheduler.config.map_error_position}}else{H=J[0].geometry.location}G.lat=H.lat();G.lng=H.lng();scheduler._selected_event_id=G.id;B(G,true,true);dp.setUpdated(G.id,true,"updated")})}else{B(G,true,true)}};_updateEventLocation=function(G){if(G.event_location&&geocoder){geocoder.geocode({address:G.event_location},function(J,I){var H={};if(I!=google.maps.GeocoderStatus.OK){H=scheduler.callEvent("onLocationError",[G.id]);if(!H||H===true){H=scheduler.config.map_error_position}}else{H=J[0].geometry.location}G.lat=H.lat();G.lng=H.lng();dp.setUpdated(G.id,true,"updated")})}};_delay=function(J,H,I,G){setTimeout(function(){var K=J.apply(H,I);J=obj=I=null;return K},G||1000)};scheduler.attachEvent("onEventChanged",function(H,I){if(scheduler.is_visible_events(scheduler.getEvent(H))){scheduler.map._markers[H].setMap(null);var G=scheduler.getEvent(H);_displayEventOnMap(G)}else{scheduler.map._infowindow.close();scheduler.map._markers[H].setMap(null)}return true});scheduler.attachEvent("onEventIdChange",function(H,G){if(scheduler.is_visible_events(scheduler.getEvent(G))){if(scheduler.map._markers[H]){scheduler.map._markers[H].setMap(null)}var I=scheduler.getEvent(G);_displayEventOnMap(I)}return true});scheduler.attachEvent("onBeforeEventDelete",function(G,H){if(scheduler.map._markers[G]){scheduler.map._markers[G].setMap(null)}scheduler.map._infowindow.close();return true});scheduler._event_resolve_delay=500;scheduler.attachEvent("onEventLoading",function(G){if(scheduler.config.map_resolve_event_location&&G.event_location&&!G.lat&&!G.lng){scheduler._event_resolve_delay+=500;_delay(_updateEventLocation,this,[G],scheduler._event_resolve_delay)}return true});scheduler.attachEvent("onEventCancel",function(G,H){if(H){if(scheduler.map._markers[G]){scheduler.map._markers[G].setMap(null)}scheduler.map._infowindow.close()}return true})});