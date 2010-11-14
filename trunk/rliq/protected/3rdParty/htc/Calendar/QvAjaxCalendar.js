// Build $BuildVersion$

function ToDate(str) {
    var s = str.split("-");
    if(s.length === 3) {
        for(var i = 0; i < 3; ++i) {
            while(s[i].charAt(0) === "0") s[i] = s[i].substr(1);
        }
        var y = parseInt(s[0]);
        var m = parseInt(s[1]);
        var d = parseInt(s[2]);
        return new Date(y, m-1, d);
    } else {
        return new Date(str);
    }
}

if (!Qva.Mgr) Qva.Mgr = {}
Qva.Mgr.date = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    
    this.Owner = owner;
    this.Element = elem;
    this.Touched = false;
    this.Dirty = false;
    owner.Append (this, this.Name, 'value');
    
    this.Selected = {};
    var _this = this;
    
    function getDateStatus(cal, date) {
        if(cal.startDate && Calendar.CompareDate(date, cal.startDate) < 0) return true;
        if(cal.endDate && Calendar.CompareDate(date, cal.endDate) > 0) return true;
        var sdate = date.print("%Y-%m-%d");
        if(_this.Selected[sdate]) return "selected";
        return false;
    }
    
    function onSelect(cal, event) {
        if (cal.dateClicked) {
            var ctrlKey = false; //event.ctrlKey; // multiple date dont work
            
            var sdate = cal.date.print("%Y-%m-%d");
            if(ctrlKey) {
                if(_this.Selected[sdate])
                    delete _this.Selected[sdate];
                else
                    _this.Selected[sdate] = cal.date;
                cal.refresh();
            } else {
                _this.Selected = {};
                _this.Selected[sdate] = cal.date;

                _this.Owner.Set(_this.Name, "value", sdate, true);
                
//                avqSet (mgr.Name, "clear", "", false);
//                for (var d in mgr.Selected) {
//                    var v = mgr.Selected[d].getTime();
//                    avqSet(mgr.Name, "value", v, false);
//                }
//                avqLoadBegin();
            }
        } else {
            //fråga efter möjliga datum i vald månad(+år)?
        }
        if (cal.dateClicked && !ctrlKey) cal.callCloseHandler();
    }
    function onClose(cal) { cal.hide(); }
    
    var firstDay = null; // numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
    var cal = new Calendar(firstDay, null, onSelect, onClose);
    this.Cal = cal;
    
    cal.showsTime = false;
    cal.weekNumbers = true;   // (true/false) if it's true the calendar will display week numbers
    
    cal.showsOtherMonths = true;
    cal.yearStep = 1;
    cal.setRange(new Date(2004, 1-1, 1), new Date(2005, 11-1, 23));
    cal.setDateStatusHandler(getDateStatus);
    cal.getDateText = null; //function() { ... };
    cal.setDateFormat("%m/%d/%Y");
    cal.create();
    
    elem.onclick = function() {
        cal.hide();
        cal.refresh();
        var align = "Tl"; // alignment (defaults to "Bl")
        cal.showAtElement(elem, align);
        return false;
    }
}
Qva.Mgr.date.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.date.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.date.prototype.Paint = function (mode, node) {
    this.Touched = true;
    this.Element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    
    var cal = this.Cal;
    var values = node.getElementsByTagName("value");
    if (values.length >= 1) {
        var value = values[0];
        var min = ToDate(value.getAttribute("min"));
        var max = ToDate(value.getAttribute("max"));
        if(min && max) cal.setRange(min, max);
        var current = value.getAttribute("current");
        this.Selected = {};
        if(current) {
            var date = ToDate(current);
            var sdate = date.print("%Y-%m-%d");
            this.Selected[sdate] = date;
            cal.setDate(new Date(date));
        }
    }
    //cal.refresh();
    this.Unlock ();
}
