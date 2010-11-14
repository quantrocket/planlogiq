scheduler.templates.calendar_month=scheduler.date.date_to_str("%F %Y");scheduler.templates.calendar_scale_date=scheduler.date.date_to_str("%D");scheduler.templates.calendar_date=scheduler.date.date_to_str("%d");scheduler.renderCalendar=function(G,A){var B=null;var D=G.date||(new Date());if(typeof D=="string"){D=this.templates.api_date(D)}if(!A){var M=G.container;var K=G.position;if(typeof M=="string"){M=document.getElementById(M)}if(typeof K=="string"){K=document.getElementById(K)}if(K&&(typeof K.left=="undefined")){var H=getOffset(K);K={top:H.top+K.offsetHeight,left:H.left}}if(!M){M=scheduler._get_def_cont(K)}B=this._render_calendar(M,D,G);B.onclick=function(P){P=P||event;var Q=P.target||P.srcElement;if(Q.className.indexOf("dhx_month_head")!=-1){var O=Q.parentNode.className;if(O.indexOf("dhx_after")==-1&&O.indexOf("dhx_before")==-1){var N=scheduler.templates.xml_date(this.getAttribute("date"));N.setDate(parseInt(Q.innerHTML,10));scheduler.unmarkCalendar(this);scheduler.markCalendar(this,N,"dhx_calendar_click");this._last_date=N;if(this.conf.handler){this.conf.handler.call(scheduler,N,this)}}}}}else{B=this._render_calendar(A.parentNode,D,G,A);scheduler.unmarkCalendar(B)}var C=scheduler.date.month_start(D);var E=scheduler.date.add(C,1,"month");var L=this.getEvents(C,E);for(var F=0;F<L.length;F++){var J=L[F];var I=J.start_date;if(I.valueOf()<C.valueOf()){I=C}while(I<=J.end_date){this.markCalendar(B,I,"dhx_year_event");I=this.date.add(I,1,"day");if(I.valueOf()>=E.valueOf()){break}}}B.conf=G;return B};scheduler._get_def_cont=function(A){if(!this._def_count){this._def_count=document.createElement("DIV");this._def_count.style.cssText="position:absolute;z-index:10100;width:251px; height:175px;";this._def_count.onclick=function(B){(B||event).cancelBubble=true};document.body.appendChild(this._def_count)}this._def_count.style.left=A.left+"px";this._def_count.style.top=A.top+"px";this._def_count._created=new Date();return this._def_count};scheduler._locateCalendar=function(C,A){var B=C.childNodes[2].childNodes[0];if(typeof A=="string"){A=scheduler.templates.api_date(A)}var D=C.week_start+A.getDate()-1;return B.rows[Math.floor(D/7)].cells[D%7].firstChild};scheduler.markCalendar=function(C,A,B){this._locateCalendar(C,A).className+=" "+B};scheduler.unmarkCalendar=function(D,A,B){A=A||D._last_date;B=B||"dhx_calendar_click";if(!A){return }var C=this._locateCalendar(D,A);C.className=(C.className||"").replace(RegExp(B,"g"))};scheduler._week_template=function(B){var F=(B||250);var E=0;var C=document.createElement("div");var D=this.date.week_start(new Date());for(var A=0;A<7;A++){this._cols[A]=Math.floor(F/(7-A));this._render_x_header(A,E,D,C);D=this.date.add(D,1,"day");F-=this._cols[A];E+=this._cols[A]}C.lastChild.className+=" dhx_scale_bar_last";return C};scheduler.updateCalendar=function(B,A){B.conf.date=A;this.renderCalendar(B.conf,B)};scheduler._mini_cal_arrows=["&nbsp","&nbsp"];scheduler._render_calendar=function(G,A,F,D){var B=scheduler.templates;var K=this._cols;this._cols=[];var S=this._mode;this._mode="calendar";var R=this._colsS;this._colsS={height:0};var Q=new Date(this._min_date);var O=new Date(this._max_date);var N=new Date(scheduler._date);var L=B.month_day;B.month_day=B.calendar_date;A=this.date.month_start(A);var E=this._week_template(G.offsetWidth-1);var M;if(D){M=D}else{var M=document.createElement("DIV");M.className="dhx_cal_container dhx_mini_calendar"}M.setAttribute("date",this.templates.xml_format(A));M.innerHTML="<div class='dhx_year_month'></div><div class='dhx_year_week'>"+E.innerHTML+"</div><div class='dhx_year_body'></div>";M.childNodes[0].innerHTML=this.templates.calendar_month(A);if(F.navigation){var C=document.createElement("DIV");C.className="dhx_cal_prev_button";C.style.cssText="left:1px;top:2px;position:absolute;";C.innerHTML=this._mini_cal_arrows[0];M.firstChild.appendChild(C);C.onclick=function(){scheduler.updateCalendar(M,scheduler.date.add(M._date,-1,"month"))};C=document.createElement("DIV");C.className="dhx_cal_next_button";C.style.cssText="left:auto; right:1px;top:2px;position:absolute;";C.innerHTML=this._mini_cal_arrows[1];M.firstChild.appendChild(C);C.onclick=function(){scheduler.updateCalendar(M,scheduler.date.add(M._date,1,"month"))};M._date=new Date(A)}M.week_start=(A.getDay()-(this.config.start_on_monday?1:0)+7)%7;var P=this.date.week_start(A);this._reset_month_scale(M.childNodes[2],A,P);var I=M.childNodes[2].firstChild.rows;for(var J=I.length;J<6;J++){I[0].parentNode.appendChild(I[0].cloneNode(true));for(var H=0;H<I[J].childNodes.length;H++){I[J].childNodes[H].className="dhx_after"}}if(!D){G.appendChild(M)}this._cols=K;this._mode=S;this._colsS=R;this._min_date=Q;this._max_date=O;scheduler._date=N;B.month_day=L;return M};scheduler.destroyCalendar=function(B,A){if(!B&&this._def_count&&this._def_count.firstChild){if(A||(new Date()).valueOf()-this._def_count._created.valueOf()>500){B=this._def_count.firstChild}}if(!B){return }B.onclick=null;B.innerHTML="";if(B.parentNode){B.parentNode.removeChild(B)}if(this._def_count){this._def_count.style.top="-1000px"}};scheduler.isCalendarVisible=function(){if(this._def_count&&parseInt(this._def_count.style.top,10)>0){return this._def_count}return false};scheduler.attachEvent("onTemplatesReady",function(){dhtmlxEvent(document.body,"click",function(){scheduler.destroyCalendar()})});scheduler.templates.calendar_time=scheduler.date.date_to_str("%d-%m-%Y");scheduler.form_blocks.calendar_time={render:function(){var D="<input class='dhx_readonly' type='text' readonly='true'>";var B=scheduler.config;var E=this.date.date_part(new Date());if(B.first_hour){E.setHours(B.first_hour)}D+=" <select>";for(var C=60*B.first_hour;C<60*B.last_hour;C+=this.config.time_step*1){var F=this.templates.time_picker(E);D+="<option value='"+C+"'>"+F+"</option>";E=this.date.add(E,this.config.time_step,"minute")}D+="</select>";var A=scheduler.config.full_day;return"<div style='height:30px; padding-top:0px; font-size:inherit;' class='dhx_cal_lsection dhx_section_time'>"+D+"<span style='font-weight:normal; font-size:10pt;'> &nbsp;&ndash;&nbsp; </span>"+D+"</div>"},set_value:function(B,K,H){var D=B.getElementsByTagName("input");var F=B.getElementsByTagName("select");var A=function(N,L,M){N.onclick=function(){scheduler.destroyCalendar(null,true);scheduler.renderCalendar({position:N,date:new Date(this._date),navigation:true,handler:function(O){N.value=scheduler.templates.calendar_time(O);N._date=new Date(O);scheduler.destroyCalendar();if(scheduler.config.event_duration&&M==0){J()}}})}};if(scheduler.config.full_day){if(!B._full_day){B.previousSibling.innerHTML+="<div class='dhx_fullday_checkbox'><label><input type='checkbox' name='full_day' value='true'> "+scheduler.locale.labels.full_day+"&nbsp;</label></input></div>";B._full_day=true}var I=B.previousSibling.getElementsByTagName("input")[0];var E=(scheduler.date.time_part(H.start_date)==0&&scheduler.date.time_part(H.end_date)==0&&H.end_date.valueOf()-H.start_date.valueOf()<2*24*60*60*1000);I.checked=E;for(var C in F){F[C].disabled=I.checked}for(var C=0;C<D.length-1;C++){D[C].disabled=I.checked}I.onclick=function(){if(I.checked==true){var M=new Date(H.start_date);var P=new Date(H.end_date);scheduler.date.date_part(M);P=scheduler.date.add(M,1,"day")}var O=M||H.start_date;var L=P||H.end_date;G(D[0],O);G(D[1],L);F[0].value=O.getHours()*60+O.getMinutes();F[1].value=L.getHours()*60+L.getMinutes();for(var N in F){F[N].disabled=I.checked}for(var N=0;N<D.length-1;N++){D[N].disabled=I.checked}}}if(scheduler.config.event_duration){function J(){H.start_date=scheduler.date.add(D[0]._date,F[0].value,"minute");H.end_date.setTime(H.start_date.getTime()+(scheduler.config.event_duration*60*1000));D[1].value=scheduler.templates.calendar_time(H.end_date);D[1]._date=scheduler.date.date_part(new Date(H.end_date));F[1].value=H.end_date.getHours()*60+H.end_date.getMinutes()}for(var C in F){F[C].onchange=J}}function G(N,L,M){A(N,L,M);N.value=scheduler.templates.calendar_time(L);N._date=scheduler.date.date_part(new Date(L))}G(D[0],H.start_date,0);G(D[1],H.end_date,1);A=function(){};F[0].value=H.start_date.getHours()*60+H.start_date.getMinutes();F[1].value=H.end_date.getHours()*60+H.end_date.getMinutes()},get_value:function(D,C){var A=D.getElementsByTagName("input");var B=D.getElementsByTagName("select");C.start_date=scheduler.date.add(A[0]._date,B[0].value,"minute");C.end_date=scheduler.date.add(A[1]._date,B[1].value,"minute");if(C.end_date<=C.start_date){C.end_date=scheduler.date.add(C.start_date,scheduler.config.time_step,"minute")}},focus:function(A){}};scheduler.linkCalendar=function(B,C){var A=function(){var D=scheduler._date;var G=scheduler._mode;var F=new Date(D.valueOf());if(C){F=C(F)}F.setDate(1);scheduler.updateCalendar(B,F);if(!C){if(G=="day"){scheduler.markCalendar(B,D,"dhx_calendar_click")}else{if(G=="week"){F=scheduler.date.week_start(new Date(D.valueOf()));for(i=0;i<7;i++){var E=F.getMonth()+F.getYear()*12-D.getMonth()-D.getYear()*12;if(E&&E>0){continue}scheduler.markCalendar(B,F,"dhx_calendar_click");F=scheduler.date.add(F,1,"day")}}}}return true};scheduler.attachEvent("onViewChange",A);scheduler.attachEvent("onXLE",A);scheduler.attachEvent("onEventAdded",A);scheduler.attachEvent("onEventChanged",A);scheduler.attachEvent("onAfterEventDelete",A);A()};