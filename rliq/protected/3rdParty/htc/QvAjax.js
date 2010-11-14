// Build $BuildVersion$

IS_CHROME = navigator.userAgent.toLowerCase().indexOf("chrome") != -1;
IS_SAFARI = !IS_CHROME && (navigator.userAgent.toLowerCase().indexOf("safari") != -1 || navigator.userAgent.toLowerCase().indexOf("konqueror") != -1);
IS_OPERA = navigator.userAgent.toLowerCase().indexOf("opera") != -1;
IS_GECKO = !IS_SAFARI && !IS_CHROME && navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
IS_MAC = navigator.userAgent.toLowerCase().indexOf("macintosh") != -1;
IS_IE6 = navigator.userAgent.toLowerCase().indexOf ("msie 6.0") != -1;

if(IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
    // support "innerText"
    HTMLElement.prototype.__defineGetter__("innerText", function() { return this.textContent; });
    HTMLElement.prototype.__defineSetter__("innerText", function($value) { this.textContent = $value; });
    
//    Event.prototype.__defineGetter__("srcElement", function() {
//        return (this.target.nodeType == Node.ELEMENT_NODE) ? this.target : this.target.parentNode;
//    });
    Event.prototype.__defineGetter__("toElement", function() {
        return (this.type == "mouseout") ? this.relatedTarget : (this.type == "mouseover") ? this.srcElement : null;
    });
}
if(typeof XMLDocument === "undefined" && typeof Document !== "undefined") XMLDocument = Document;

Qva = {} 

Qva.binders = {}
Qva.GetBinder = function(id, view) {
    var binder = Qva.binders[id || ""];
    if(!binder && view) {
        binder = new Qva.PageBinding(id);
        binder.View = view;
        binder.Modal = new Qva.Modal(qva);
        
        var def_binder = Qva.GetBinder();
        if(def_binder) {
            binder.Autoview   = def_binder.Autoview;
            binder.ScriptPath = def_binder.ScriptPath;
            binder.Remote     = def_binder.Remote;
            binder.JSON       = def_binder.JSON;
        }
    }
    return binder;
}
Qva.Start = function() {
    function _Start() {
        if(Qva.Scanner && Qva.Scanner.instance) Qva.Scanner.instance.Start();
        for(var id in Qva.binders) Qva.binders[id].Start();
        if (document.addEventListener) { 
            document.addEventListener ("mouseup", function (event) { Qva.OnMouseUp(event); }, false);
            document.addEventListener ("keyup", function (event) { Qva.OnKeyUp(event); }, false);
        } else { 
            document.attachEvent ("onmouseup", function (event) { Qva.OnMouseUp(event); });
            document.attachEvent ("onkeyup", function (event) { Qva.OnKeyUp(event); });
        }
    }
    
    var userid = Qva.ExtractProperty ("userid", null, true);
    var password = Qva.ExtractProperty ("password", null, true);

    if(typeof qva === "object" && (userid === "" || password === "")) {
        var old_Set = qva.Set;
        qva.Set = function() {
            qva.Set = old_Set;
            _Start();
        };
        qva.Modal.Show(qva.Modal.ScriptPath + 'login.htm?userid=' + escape(userid), 254, 140);
    } else {
        _Start();
    }
}

Qva.AsyncPostPaintMgrQueue = [];
Qva.CurrentObject = null;
Qva.CurrentTab = null;
Qva.PopupInput = null;
Qva.ContextMenu = null;
Qva.ContextMenuMgr = null;
Qva.KeepContextMenuAlive = false;
Qva.MgrWithMouseDown = null;
Qva.MgrWithSelectStart = null;
Qva.StartoffsetX = 0;
Qva.StartoffsetY = 0;
Qva.ActiveObject = null;
Qva.ActiveElement = null;
Qva.PopupSearch = null;
Qva.KeepPopupSearchAlive = false;
Qva.PendingSearchName = '';
Qva.PendingSearchKeyName = '';
Qva.DragRect = null;
Qva.DragStartX = 0;
Qva.DragStartY = 0;
Qva.DragRectLeft = 0;
Qva.DragRectTop = 0;
Qva.DragRectWidth = 0;
Qva.DragRectHeight = 0;
Qva.LabelClick = true;

Qva.PageBinding = function(id) {
    this.ID = id || "";
    if(Qva.binders[this.ID]) { alert("Need unique binderid"); return; }
    Qva.binders[this.ID] = this;
    
    this.OnUpdateBegin = null;
    this.IsUpdating = false;
    this.HasPendingLoad = false;
    this.ScrollLeftToRemember = 0;
    this.ScrollTopToRemember = 0;
    this.IsPartialLoad = false;
    this.CurrentLoadIsPartial = false;
    this.Enabled = false;
    this.First = true;
    this.Managers = new Array();
    this.Members = {}
    this.ScriptPath = '/QvAJAXZfc/htc/';
    this.Remote = '/QvAJAXZfc/QvsViewClient.asp';
    this.UsePost = true;
    this.JSON = false;
    this.Body = '';
    this.InitialSets = '';
    this.Mark = '';
    this.Stamp = '';
    this.RecursiveReadyLevel = 0;
    this.ShowMessage = Qva.DefaultShowMessage;
    this.OnSessionLost = Qva.DefaultOnSessionLost;
    this.OnUpdateComplete = Qva.NoAction;
    this.OnCreateContextMenu = Qva.DefaultOnCreateContextMenu;
    this.AllowComAgent = true;
    this.InlineStyle = true;
    this.TableLimit = 5000;
    this.Ident = null;
    this.TransientObject = '';
    this.ToggleSelect = "";
    this.ToggleSelects = null;
    
    this.PendingDblClickName = '';
    this.DefaultScope = 'Document';
    
    try {
        var impl = window.document.implementation;
        if (impl && impl.createDocument) {
            var doc = impl.createDocument ("", "", null);
            if (doc.readyState == null) {
                doc.readyState = 1;
                doc.addEventListener("load", function () {
                    doc.readyState = 4;
                    if (typeof doc.onreadystatechange == "function") {
                        doc.onreadystatechange ();
                    }
                }, false);
            }
            this.Doc = doc;
            this.LeftButton = 0;
        } else if (window.ActiveXObject) {
            this.Doc = new ActiveXObject ("Microsoft.XMLDOM");
            this.LeftButton = 1;
        }
    } catch (e) {}
    if (this.Doc == null) throw new Error ("Your browser does not support XmlDocument objects");
}

Qva.PageBinding.prototype.Start = function () {
    this.IsRemote = true;
    this.IsHosted = false;
    if (this.View == null && top.qva != null) this.View = top.qva.View;
    if (this.JSON) this.Session = Qva.ExtractProperty ("session", this.Session);
    this.Ident = Qva.ExtractProperty ("ident", this.Ident);
    this.Userid = Qva.ExtractProperty ("userid", this.Userid);
    this.Xuserid = Qva.ExtractProperty ("xuserid", this.Xuserid);
    this.Password = Qva.ExtractProperty ("password", this.Password);
    this.Xpassword = Qva.ExtractProperty ("xpassword", this.Xpassword);
    this.Bookmark = Qva.ExtractProperty ("bookmark", this.Bookmark);
    this.View = Qva.ExtractProperty ("application", this.View);
    if (Qva.Benchmark) {
        this.Benchmark = new Qva.Benchmark();
    }
    this.Url = this.Remote;
    this.Url += (this.Url.indexOf ('?') == -1) ? '?mark=' : '&mark=';
    
    if (window.ActiveXObject && window.location.protocol == "file:") {
        this.TryAltAgent();
    }
    
    var _this = this;
    this.CollaborationContainer = document.getElementById("PageContainer");
    if (this.CollaborationContainer != null) {
        this.CollaborationContainer.oncontextmenu = function (event) { return _this.OnContextMenu(event); }
        this.CollaborationContainer.onclick = function () { Qva.CloseContextMenu (); Qva.ClosePopupSearch (); Qva.ClosePopupInput (); }
    }
    this.LoadBegin();
    // TODO: KeepAliveKicker ();
}

Qva.QueuePostPaintMessage = function (mgr) {
    for (var ix = 0; ix < Qva.AsyncPostPaintMgrQueue.length; ++ ix) {
        if (this.AsyncPostPaintMgrQueue [ix] == mgr) {
            return; // No need for multiple paints
        }
    }
    this.AsyncPostPaintMgrQueue.push (mgr);
//    var stupiddelay = ((IS_IE6 || IS_OPERA || IS_CHROME) && mgr.FinalFix);
//    window.setTimeout (avqAsyncPostPaint, stupiddelay ? 100 : 0);
    window.setTimeout (avqAsyncPostPaint, 0);
}

function avqAsyncPostPaint () {
    if (Qva.AsyncPostPaintMgrQueue.length == 0) {
        alert ("AsyncPostPaintMgrQueue.length == 0");
        return;
    }
    var mgr = Qva.AsyncPostPaintMgrQueue.shift ();
    mgr.PostPaint ();
}

Qva.MgrGetName = function (elem) {
    while (elem) {
        if (elem.AvqMgr != null) return elem.AvqMgr.Name;
        elem = elem.parentNode;
    }
    return "";
}

function ctrlKeyPressed (event) {
	// ctrlKey valid for windows - IE, Firefox
	// metaKey valid for Mac - Safari, Firefox
	// keyCode 224 for stopping onKeyUp on Firefox for Mac
	// keyCode 91 for stopping onKeyUp on Safari for Mac
	return event.ctrlKey || event.metaKey || event.keyCode == 224 || event.keyCode == 91;
}

Qva.OnMouseUp = function (event) {
    Qva.MgrWithMouseDown = null;
    if (! event) {
        event = window.event;
    }
    if (Qva.MgrWithSelectStart != null) {
        var mgr = Qva.MgrWithSelectStart;
        Qva.MgrWithSelectStart = null;
        var X = event.clientX;
        var Y = event.clientY;
        mgr.SelectEnd (X, Y, ctrlKeyPressed (event));
    } else {
        var elem = event.target;
        if (elem == null) elem = event.srcElement;
        if (Qva.MgrGetName (Qva.PopupSearch) != Qva.MgrGetName (elem)) {
            if (Qva.PopupSearch != null) {
                var binder = Qva.ClosePopupSearch();
                if (binder.Body != '') binder.LoadBegin ();
            }
        }
    }
}

Qva.OnKeyUp = function (event) {
    if (Qva.PopupInput != null) return;
    if (!Qva.LabelClick) return;
    if (Qva.ActiveObject) {
        var element = document.getElementById (Qva.ActiveObject);
        if (element && element.AvqMgr) {
            if (! event) {
                event = window.event;
            }
            var binder = element.AvqMgr.PageBinder;
            if (binder.ToggleSelect != "" && (event.keyCode == 17 || event.keyCode == 224 || event.keyCode == 91)) {
                var objectName = binder.ToggleSelect;
                for (var i = 0; i < binder.ToggleSelects.length; i++) {
                    binder.Set (objectName, 'value', binder.ToggleSelects [i], false);
                }
                var transientname = binder.TransientObject;
                if (transientname == objectName) {
                    binder.Set (transientname, "closetransient", "ok", false);
                    binder.TransientObject = '';
                }
                binder.ToggleSelects = null;
                binder.ToggleSelect = "";
                binder.LoadBegin ();
            } else if (element.AvqMgr.Searchable) {
                if (Qva.PopupSearch == null && event.keyCode >= 32 && ! ctrlKeyPressed (event)) {
                    Qva.OpenPopupSearch (element);
                    var key = event.keyCode;
                    Qva.PopupSearch.value = '*' + String.fromCharCode (key).toLowerCase () + '*';
                    Qva.SetCursor (Qva.PopupSearch);
                    Qva.Search (element.AvqMgr, Qva.PopupSearch, key);
                }
            }
        }
    }
}

Qva.Search = function (mgr, elem, key) {
    Qva.KeepPopupSearchAlive = (elem == Qva.PopupSearch);
    if (mgr.SearchName != "") {
        var binder = mgr.PageBinder || Qva.GetBinder(mgr.binderid);
		if (binder.Enabled) {
			binder.Set (mgr.SearchName, "pageoffset", 0, false);
			binder.Set (mgr.SearchName, "search", elem.value, true);
		} else {
			Qva.PendingSearchName = mgr.SearchName;
			Qva.PendingSearchValue = elem.value;
		}
	}
}
Qva.CloseSearch = function (mgr, elem, accept, ctrl) {
    Qva.KeepPopupSearchAlive = false;
    var SearchName = mgr.SearchName;
    var currentsearchvalue = elem.value;
    elem.value = "";
    elem.onkeyup = null;
    if (elem == Qva.PopupSearch) mgr.SearchName = null;
    
    var binder = mgr.PageBinder || Qva.GetBinder(mgr.binderid);
    if (Qva.PopupSearch.searchcol != null) {
	    if (accept) binder.Set (SearchName + ":" + Qva.PopupSearch.searchcol, "searchcolumn", currentsearchvalue, true);
    } else {
        if (binder.Enabled) {
		    if (accept) binder.Set (SearchName, "search", currentsearchvalue, false);
		    var cmd;
		    if (accept) {
		        cmd = ctrl ? "ctrlaccept" : "accept";
		    } else {
		        cmd = "abort";
		    }
            binder.Set (SearchName, "closesearch", cmd, true);
        } else {
            Qva.PendingSearchKey = (accept ? "accept" : "abort");
            Qva.PendingSearchKeyName = SearchName;
        }
    }
}

Qva.GetFrame = function (elem) {
    function EndsWidth(str, end) {
        return str && str.length >= end.length && str.substr(str.length - end.length) == end;
    }
    while(elem && !EndsWidth(elem.id, "_frame")) {
        elem = elem.parentNode;
    }
    return elem;
}

Qva.OpenPopupSearch = function (elem, param) {
    var mgr = elem.AvqMgr;
    var searchname = mgr.Name || mgr.PageName;
    if (! searchname) {
        searchname = mgr.ColList [0].Name.split ('.') [1];
    }
    if (searchname == mgr.SearchName && mgr.Search != null) return;
    mgr.SearchName = searchname;
    Qva.PopupSearch = document.createElement ('input');
    Qva.PopupSearch.param = param;
    Qva.PopupSearch.tabIndex = 1;
    Qva.PopupSearch.className = 'avqEdit';
    Qva.PopupSearch.onkeydown = AvqAction_Search_KeyDown;
    Qva.PopupSearch.onkeyup = AvqAction_Search_KeyUp;
    Qva.PopupSearch.onfocus = AvqAction_Search_Focus;

    if (elem.xx != null) {
        if (elem.yy != 0) debugger;
        Qva.PopupSearch.searchcol = elem.xx;
    }
    mgr.Search = Qva.PopupSearch;
    Qva.PopupSearch.AvqMgr = mgr;
    var positionsource = Qva.GetFrame (mgr.Element);
    if (positionsource == null) {
        var element = document.getElementById (searchname.replace (mgr.PageBinder.DefaultScope + ".", ""));
        positionsource = Qva.GetFrame (element);
        if (positionsource == null) {
            positionsource = element;
        }
    }

    var elemPageCoords = Qva.GetAbsolutePageCoords (positionsource);
    Qva.PopupSearch.style.position = "absolute";
    Qva.PopupSearch.style.left = elemPageCoords.x + "px";
    Qva.PopupSearch.style.top = elemPageCoords.y - 19 + "px";
    Qva.PopupSearch.style.width = '100pt';
    Qva.PopupSearch.style.zIndex = 666;
    Qva.PopupSearch.style.border = '1pt solid black';
    Qva.PopupSearch.style.display = '';

    document.body.insertBefore (Qva.PopupSearch, document.body.firstChild);
    Qva.PopupSearch.focus ();
    Qva.KeepPopupSearchAlive = true;
}
Qva.ClosePopupSearch = function () {
    if (Qva.PopupSearch == null) return;
    var mgr = Qva.PopupSearch.AvqMgr;
    var binder = mgr.PageBinder || Qva.GetBinder(mgr.binderid);
    if (mgr.SearchName != null) {
        binder.Set (mgr.SearchName, "closesearch", "abort", false);
    }
    if (Qva.ActiveElement == Qva.PopupSearch) Qva.ActiveElement = mgr.Element;
    mgr.SearchName = null;
    mgr.Search = null;
    document.body.removeChild (Qva.PopupSearch);
    Qva.PopupSearch = null;
    return binder;
}

Qva.OpenPopupInput = function (elem) {
    Qva.ClosePopupInput ();
    var parent = elem.parentNode;
    Qva.PopupInput = document.createElement ("input");
    if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
        var gs = document.defaultView.getComputedStyle (parent, "");
        Qva.PopupInput.style.fontFamily = gs.getPropertyValue ("font-family");
        Qva.PopupInput.style.fontSize = gs.getPropertyValue ("font-size");
    } else {
        Qva.PopupInput.style.fontFamily = parent.currentStyle.fontFamily;
        Qva.PopupInput.style.fontSize = parent.currentStyle.fontSize;
    }
    Qva.PopupInput.style.top = parent.offsetTop + "px";
    Qva.PopupInput.style.left = parent.offsetLeft + "px";
    Qva.PopupInput.style.width = parent.offsetWidth + "px";
    Qva.PopupInput.style.zIndex = 666;
    Qva.PopupInput.style.position = "absolute";
    Qva.PopupInput.value = parent.innerText;
    Qva.PopupInput.onmousedown = Qva.CancelAction;
    Qva.PopupInput.onmouseup = Qva.CancelAction;
    Qva.PopupInput.onclick = Qva.CancelAction;
    Qva.PopupInput.onkeydown = AvqAction_Input_KeyDown;
    Qva.PopupInput.binderid = elem.binderid;
    Qva.PopupInput.xx = elem.xx;
    Qva.PopupInput.yy = elem.yy;
    Qva.PopupInput.targetname = elem.targetname;
    parent.offsetParent.offsetParent.appendChild (Qva.PopupInput);
    
    Qva.PopupInput.focus ();
    Qva.SetCursor (Qva.PopupInput, true);
}
Qva.ClosePopupInput = function () {
    if (Qva.PopupInput == null) return;
    Qva.PopupInput.parentNode.removeChild (Qva.PopupInput);
    Qva.PopupInput = null;
}

Qva.AddRule = function (ss, name, style) {
    if (ss.addRule) {
        ss.addRule (name, style);
    } else {
        ss.insertRule (name + " { " + style + " }", ss.cssRules.length);
    }
}

Qva.PageBinding.prototype.Refresh = function () {
    if (!this.Enabled) return;
    this.LoadBegin ();
}
Qva.PageBinding.prototype.LoadBegin = function (xmldata) {
    if (this.IsUpdating) {
        if (xmldata != null) alert ('State error - xmldata not allowed');
        this.HasPendingLoad = true;
        return;
    }
    if (this.OnUpdateBegin != null) this.OnUpdateBegin ();	
    
    this.IsUpdating = true;
    this.ScrollLeftToRemember = 0;
    this.ScrollTopToRemember = 0;
    Qva.ActiveElement = null;
    try {
        this.ScrollLeftToRemember = document.body.scrollLeft;
        this.ScrollTopToRemember = document.body.scrollTop;
        Qva.ActiveElement = document.activeElement;
        document.body.style.cursor = 'wait';
    } catch(e) { }

    if (! this.IsPartialLoad) {
        if (!Qva.KeepPopupSearchAlive && Qva.PopupSearch != null) {
            var mgr = Qva.PopupSearch.AvqMgr;
            var binder = mgr.PageBinder || Qva.GetBinder(mgr.binderid);
            if(binder == this) {
                Qva.ClosePopupSearch();
            }
        }
        if (! Qva.KeepContextMenuAlive) {
            Qva.CloseContextMenu ();
        }
        Qva.ClosePopupInput ();
    }

    this.Enabled = false;
    if (!this.First) {
        for (var mix = 0; mix < this.Managers.length; ++mix) {
            var mgr = this.Managers [mix];
            if (this.IsRemote && mgr.Lock) mgr.Lock ();
            mgr.Touched = false;
        }
    }

    if (this.Benchmark != null) this.Benchmark.Load.Start();

    if (xmldata != null || this.View == null && this.Kind == null) {
        this.Ready (xmldata);
    } else {
        if (this.AutoviewDictionary != null) {
            this.SetAutoviewAddCommands ();
        }
        this.Load ();
    }
}

Qva.PageBinding.prototype.Load = function () {
    this.HasPendingLoad = false;
    
    this.CurrentLoadIsPartial = this.IsPartialLoad;
    this.IsPartialLoad = false;

    if (Qva.PendingSearchName != '' || Qva.PendingSearchKeyName != '') {
        if (Qva.PendingSearchName != '') {
            this.Set (Qva.PendingSearchName, "pageoffset", 0, false);
            this.Set (Qva.PendingSearchName, "search", Qva.PendingSearchValue, false);
            Qva.PendingSearchName = '';
        } 
        if (Qva.PendingSearchKeyName != '' && Qva.PopupSearch != null) {
            var mgr = Qva.PopupSearch.AvqMgr;
            var binder = mgr.PageBinder || Qva.GetBinder(mgr.binderid);
            if(binder == this) {
                this.Set (Qva.PendingSearchKeyName, "closesearch", Qva.PendingSearchKey, false);
                Qva.ClosePopupSearch();
                Qva.PendingSearchKeyName = '';
            }
        }
    } else if (this.PendingDblClickName != '') {
        this.Set (this.PendingDblClickName, "click", this.PendingDblClickName, false);
        this.PendingDblClickName = '';
    }
    var cmd = '<update mark="' + this.Mark + '" stamp="' + this.Stamp + '"';
    if (window.navigator && window.navigator.cookieEnabled) {
        cmd += ' cookie="true"';
    } else {
        cmd += ' cookie="false"';
    }
    if (this.DefaultScope != null) cmd += ' scope="' + this.DefaultScope + '"';
    if (this.Session != null) cmd += ' session="' + this.Session + '"';
    if (this.View != null) cmd += ' view="' + Qva.XmlEncode (this.View) + '"';
    if (this.Autoview != null && this.Autoview != "") cmd += ' autoview="' + Qva.XmlEncode (this.Autoview) + '"';
    cmd += ' ident="' + Qva.XmlEncode (this.Ident) + '"';
    if (this.Userid != null) cmd += ' userid="' + this.Userid + '"';
    if (this.Xuserid != null) cmd += ' xuserid="' + this.Xuserid + '"';
    if (this.Password != null) cmd += ' password="' + this.Password + '"';
    if (this.Xpassword != null) cmd += ' xpassword="' + this.Xpassword + '"';
    if (this.Kind != null) cmd += ' kind="' + this.Kind + '"';
    cmd += '>';
    cmd += this.Body;
    if (!this.HasAutoviewAddCommands ()) {   // Do not add initial sets until all autoviews are done and we have space
        cmd += this.InitialSets;
        this.InitialSets = '';
    }
    cmd += '</update>';
    this.Body = '';
    if (this.Trace != null && this.Trace.Request != null) this.Trace.Request.innerText = cmd;
    var pb = this;
    if (this.Agent == null) {
        var url = this.Url;
        if (this.Session != null && this.JSON) url += '&session=' + escape (this.Session);
        if (this.View != null) url += '&view=' + escape (this.View);
        if (this.Userid != null) url += '&userid=' + escape (this.Userid);
        if (this.Xuserid != null) url += '&xuserid=' + escape (this.Xuserid);
        if (this.Password != null) url += '&password=' + escape (this.Password);
        if (this.Xpassword != null) url += '&xpassword=' + escape (this.Xpassword);
        
        if(this.JSON) {
            url += '&json=' + escape (this.ID);
            url += '&cmd=' + escape (cmd);
            
            var scriptTag = document.createElement("script");
            
            // Add script object attributes
            scriptTag.setAttribute("type", "text/javascript");
            scriptTag.setAttribute("src", url);
            
            // Create the script tag
            var head = document.getElementsByTagName("head").item(0);
            if(this.ScriptTag) {
                head.replaceChild(scriptTag, this.ScriptTag)
            } else {
                head.appendChild(scriptTag);
            }
            this.ScriptTag = scriptTag;
            
        } else if (this.UsePost) {
            var xmlhttp;
            if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest()
            } else {
                xmlhttp = new ActiveXObject("MSXML2.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4) {
                    pb.Doc = xmlhttp.responseXML;
                    pb.Ready ();
                }
            }

            xmlhttp.open("POST", url, true);
            try {
                xmlhttp.send(cmd);
            } catch(e) {
                alert('Crash');
                this.Enabled = true;
            }
        } else {
            url += '&cmd=' + escape (cmd);
            this.Doc.onreadystatechange = function () {
                if (pb.Doc.readyState == 4) {
                    pb.Ready ();
                }
            }
            if (!this.Doc.load(url)) {
                alert('Crash');
                this.Enabled = true;
            }
        }
    } else {
        var data;
        try {
            data = this.UseExecute ? this.Agent.Execute (cmd) : this.Agent.XmlUpdate (cmd);
        } catch(e) {
            data = '<result><message text="Server not responding" /></result>';
        }
        if (!this.Doc.loadXML (data)) {
            alert ('Unexpected error loading');
            this.Enabled = true;
            return;
        }
        window.setTimeout (function () { pb.Ready() }, 0);	// make local loading asynchronous as well
    }
}

Qva.PageBinding.prototype.PartialLoad = function (name, pageoffset) {
    if (isNaN (parseInt (pageoffset))) return;
    this.IsPartialLoad = true;
    this.Set (name, "pageoffset", pageoffset, true);
}

Qva.PageBinding.prototype.AddManager = function (mgr) {
    mgr.PageBinder = this;
    mgr.Touched = false;
    mgr.Dirty = false;
    if (mgr.SelectedClassName == null) {
        mgr.SelectedClassName = 'AvqSelected';
        mgr.DeselectedClassName = 'AvqDeselected';
        mgr.EnabledClassName = 'AvqEnabled';
        mgr.DisabledClassName = 'AvqDisabled';
        mgr.LockedClassName = 'AvqLocked';
        mgr.ModeIfNotEnabled = 'n';    // Use "true" non-enabled mode
    }
    this.Managers [this.Managers.length] = mgr;
    this.Append (mgr, mgr.Name, mgr.Attr);
}
Qva.PageBinding.prototype.Append = function (mgr, name, attr, noautoview) {
    if (name == null || name.substr (0,1) == ".") debugger;
    if (name == null || name == "") return;
    var list = this.Members[name];
    if (list == null) {
        list = new Array ();
        this.Members[name] = list;
    }
    for (var i = 0; i < list.length; i ++) {
        if (list [i] == mgr) break;
    }
    if (i == list.length) {
        list [list.length] = mgr;
    }
    if (this.Autoview != null && ! noautoview) {
        var nameattr = name.split ('@');
        var autoviewname = nameattr [0];
        var autoviewattr = nameattr.length > 1 ? nameattr [1] : attr;
        this.AutoViewAppend (mgr, autoviewname, autoviewattr);
    }
}

Qva.PageBinding.prototype.HasAutoviewAddCommands = function () {
    if(this.AutoviewDictionary == null) return false;
    for (var name in this.AutoviewDictionary) {
        var item = this.AutoviewDictionary [name];
        if (item.dirty) return true;
    }
    return false;
}
Qva.PageBinding.prototype.AutoViewAppend = function (mgr, name, attr) {
    if (attr == null) attr = "text";
    if (this.AutoviewDictionary == null) this.AutoviewDictionary = {}
    var item = this.AutoviewDictionary[name];
    if (item == null) {
        item = {}
        item.dirty = true;
        item.attrs = "mode";
        this.AutoviewDictionary[name] = item;
    }
    if (item.attrs.indexOf(attr) == -1) {
        item.dirty = true;
        item.attrs += ";" + attr;
    }
}
Qva.PageBinding.prototype.SetAutoviewAddCommands = function () {
    for (var name in this.AutoviewDictionary) {
        var item = this.AutoviewDictionary[name];
        if (! item.dirty) continue;
        item.dirty = false;
        this.Set (name, 'add', item.attrs, false);
        if (this.First && this.Body.length > 900 && ! this.UsePost) return;     // arbitrary length to keep URL below 1K
    }
    if (this.First && this.Bookmark != null) {
        this.Set ('bookmark-apply', 'docaction', this.Bookmark, false);
    } 
}

Qva.PageBinding.prototype.Ready = function (xmldata) {
    if (this.RecursiveReadyLevel > 0) {
        return;
    }

    try {
        document.body.style.cursor = 'auto';
    } catch(e) { debugger }

    if (this.Enabled) { alert ('State error'); }
    this.Enabled = true;
    
    var openUrlWindow = null;	// window opened from server using 'open/@url'
    for (;;) {
        if (xmldata) {
            this.RecursiveReadyLevel ++;
            this.Doc.loadXML (xmldata);
            this.RecursiveReadyLevel --;
        }
        if (this.Doc == null) {
            debugger;
            return;
        }
        var root = this.Doc.documentElement;
        if (this.View != null || this.Kind != null) {
            if (root == null) {
                var error_string = "";
                var parseError = this.Doc.parseError;
                if (parseError != null) {
                    error_string += 'Line: ' + parseError.line +
                    '\r\n\r\nChar: ' + parseError.filepos +
                    '\r\n\r\nReason: ' + parseError.reason + '\r\n\r\n';
                }
                error_string += 'Failed to load:\r\n\r\nURL: ' + this.Doc.url;
                this.ShowMessage (error_string);
                return;
            }
            switch(Qva.GetMessage(root)) {
                case "Failed to open document, you don't have access to the server.":
                    var loc = Qva.FixUrl("" + window.location, 'userid', "");
                    if(this.Session && this.JSON) loc = Qva.FixUrl(loc, 'session', this.Session);
                    window.location = loc;
                    return;
            }
            if (!this.IsHosted) {
                var session = root.getAttribute ('session');
                if (this.Session == null) {
                    this.Session = session; // remember;
                } else if (this.Session != session) {
                    var message = Qva.GetMessage(root);
                    if (message == null) {
                        message = "Session timed out";
                    }
                    this.OnSessionLost (message);
                    return;
                }
            }
            var currentobjects = root.getElementsByTagName ('object');
            if (currentobjects.length >= 1) {
                Qva.ActiveObject = currentobjects[0].getAttribute ('currentobject');
            } else {
                Qva.ActiveObject = null;
            }
            var open_nodes = root.getElementsByTagName ('open');
            if (open_nodes.length >= 1) {
                var open_node = open_nodes[0];
                var url = open_node.getAttribute ('url');
                if (url) {
                    try {
					    openUrlWindow = window.open (url);
				    } catch (e) {
				        this.ShowMessage ("Can't open '" + url + "' due to error: " + e.description);
				    }
					open_node.removeAttribute ("url");
				}
            }
            var ident = root.getAttribute ('ident');
            if (ident != null) this.Ident = ident;
            var kind = root.getAttribute ('kind');
            if (kind != null) this.Kind = kind;
            var mark = root.getAttribute ('mark');
            var stamp = root.getAttribute ('stamp');
            if (mark != null && stamp != null) {
                this.Mark = mark;
                this.Stamp = stamp;
            }

            var partial = this.CurrentLoadIsPartial;
            this.CurrentLoadIsPartial = false;

            if (this.Benchmark != null) this.Benchmark.Load.Stop();
            if (this.Benchmark != null) this.Benchmark.Paint.Start();
            this.PaintTree (root, '', partial);
            if (this.Benchmark != null) this.Benchmark.Paint.Stop();

            if (this.PendingXmlData != null) {
                xmldata = this.PendingXmlData;
                this.PendingXmlData = null;
                if (this.Benchmark != null) this.Benchmark.Load.Start();
            } else {
                var MoreAutoviewsToAdd = this.First && (this.HasAutoviewAddCommands () || this.InitialSets != '');
                if (Qva.PendingSearchName != '' || Qva.PendingSearchKeyName != '' || this.PendingDblClickName != '' || 
                    MoreAutoviewsToAdd || this.HasPendingLoad) 
                {
                    this.Enabled = false;
                    if (MoreAutoviewsToAdd) this.SetAutoviewAddCommands ();
                    if (this.Benchmark != null) this.Benchmark.Load.Start();
                    this.Load ();
                    return;
                }
                this.IsUpdating = false;
                break;
            }
        }
    }
    this.PaintDone (true, root);

    if (this.View != null || this.Kind != null) {
        var msg = Qva.GetMessage(root);
		if (msg != null) {
			this.ShowMessage (msg);
		}
        if (this.Trace != null && this.Trace.Response != null) this.Trace.Response.innerText = this.Doc.xml;
    }
    if (this.First && this.DeveloperMode) {
        var errmsg = this.OnceAfterLoad();
        if (errmsg != null) this.ShowMessage (errmsg);
    }
    if (this.IsHosted && this.first) window.focus ();
    if (this.IsRemote) {
        try {
            if (document.selection && document.selection.type != 'None') {
                var y = document.selection.createRange();
                if (y != null) y.select ();
            }
        } catch(e) { debugger }
    }
    this.First = false;
    try {
        document.body.scrollLeft = this.ScrollLeftToRemember;
        document.body.scrollTop = this.ScrollTopToRemember;
        if (document.activeElement != Qva.ActiveElement) {
            Qva.ActiveElement.focus ();
        }
    } catch(e) { }

    if (Qva.PopupSearch != null) Qva.KeepPopupSearchAlive = false;

    if (openUrlWindow != null) {
        openUrlWindow.focus ();
    }
}

Qva.PageBinding.prototype.OnceAfterLoad = function () {
    var errors = [];
    var dix = 0;
    for (var mix = 0; mix < this.Managers.length; ++mix) {
        var mgr = this.Managers [mix];
        if (mgr.Name == '' || mgr.Name == '#edit#' || mgr.Touched) {
            if (dix != mix) this.Managers [dix] = this.Managers [mix];
            ++dix;
        } else {
            if (mgr.Name != '') {
                try { mgr.Element.style.display = 'none'; } catch (e) { debugger }
                errors [errors.length] = 'Not found: ' + mgr.Name;
            }
        }
    }
    this.Managers.length = dix;
    
    if (errors.length == 0) return null;
    var msg = 'Errors:\n' + errors.join ('\n');
    return msg;
}
Qva.PageBinding.prototype.PaintTree = function (rootnode, prefix, partial) {
    for (var node = rootnode.firstChild; node != null; node = node.nextSibling) {
        if (node.nodeName != 'value' && node.nodeName != 'action' && node.nodeName != 'group') continue;
        var name = prefix + node.getAttribute ('name');
        var list = this.Members[name];
        if (list != null) {
            var mode = 'd';
            switch (node.getAttribute ('mode')) {
            case "not-applicable":
                mode = 'n';
                break;
            case "hidden":
                mode = 'h';
                break;
            case "enabled":
                mode = 'e';
                break;
            }
            var xlen = list.length;
            for (var ixx = 0; ixx < xlen; ++ixx) {
                list [ixx].Paint (mode, node, name, partial);
            }
        }
    }
    for (var group = rootnode.firstChild; group != null; group = group.nextSibling) {
        if (group.nodeName != 'value' && group.nodeName != 'object' && group.nodeName != 'group') continue;
        var grpprefix = prefix + group.getAttribute('name') + '.';
        this.PaintTree (group, grpprefix, partial);
    }
}
Qva.PageBinding.prototype.PaintDone = function (remote, root) {
    if (this.First) {
        if (!this.IsHosted) {
            try {
                if (document.cookie.indexOf ("qlikmachineid") == -1) {
                    if (window.navigator && window.navigator.cookieEnabled) {
                        var machineid = root.getAttribute ('machineid');
                        if (machineid != null) {
                            var expires = new Date();
                            expires.setFullYear(2222, 2, 2);
                            document.cookie = "qlikmachineid=" + machineid + "; expires=" + expires.toGMTString();
                        }
                    }
                }
            } catch(e) {
                debugger;
            }
        }
    }

    if (this.Benchmark != null) this.Benchmark.Unlock.Start();
    for (var mix = 0; mix < this.Managers.length; ++mix) {
        var mgr = this.Managers [mix];
        if (mgr.Dirty) {
            mgr.PostPaint ();
            mgr.Dirty = false;
        }
        if (this.IsRemote && !mgr.Touched && mgr.Unlock) mgr.Unlock ();
    }
    if (this.Benchmark != null) this.Benchmark.Unlock.Stop();

    if (this.Benchmark != null) this.Benchmark.UpdateComplete.Start();
    this.OnUpdateComplete ();
    if (this.Benchmark != null) this.Benchmark.UpdateComplete.Stop();

    if (this.Benchmark != null) this.Benchmark.Display();
}

Qva.PageBinding.prototype.Set = function (name, type, value, _final) {
    if (this.TransientObject != '') {
        if (this.TransientObject != name.substr (0, this.TransientObject.length) && ! this.IsPartialLoad) {
            var transientname = this.TransientObject;
            this.TransientObject = '';
            if (transientname == this.ToggleSelect) {
                this.ToggleSelect = "";
                this.ToggleSelects = null;
            }
            this.Set (transientname, "closetransient", "ok", false);
        }
    }
    name = Qva.MgrMakeName (name, this.DefaultScope);
    this.Body += '<set name="' + name + '" ' + type + '="' + Qva.XmlEncode(value) + '"/>';
    if (_final) this.LoadBegin ();
}
Qva.PageBinding.prototype.SetInitial = function (name, type, value) {
    this.InitialSets += '<set name="' + name + '" ' + type + '="' + Qva.XmlEncode(value) + '"/>';
}

Qva.PageBinding.prototype.SelectChild = function(root, names, ix) {
    for (var node = root.firstChild; node; node = node.nextSibling) {
        switch(node.nodeName) {
        case 'object':
            if (node.getAttribute('name') != names[ix]) break;
            return (ix == 0) ? node : null;
        case 'group':
            if (node.getAttribute('name') != names[ix]) break;
            return node;
        case 'value':
        case 'action':
            if (node.getAttribute('name') != names[ix]) break;
            return (ix == names.length - 1) ? node : null;
        }
    }
    return null;
}
Qva.PageBinding.prototype.Select = function(path) {
    if (path == null) return null;
    path = Qva.MgrMakeName (path, this.DefaultScope);
    var parts = path.split ('.');
    var node = this.Doc.documentElement;
    for (var ix = 0; node != null && ix < parts.length; ++ix) {
        node = this.SelectChild(node, parts, ix);
    }
    return node;
}

Qva.PageBinding.prototype.SetClick = function (event, name, elem) {
    if (! this.Enabled) return;
    if (this.ToggleSelect != "") return;
    Qva.MgrWithMouseDown = null;
    var clickstring = "";
    if (elem) {
        var offsetX = 0;
        var offsetY = 0;
        if (! event) {
            offsetX = window.event.offsetX;
            offsetY = window.event.offsetY;
        } else {
            var evtOffsets = Qva.GetOffsets (event);
            offsetX = evtOffsets.offsetX;
            offsetY = evtOffsets.offsetY;
        }
        clickstring += '' + offsetX + ':' + offsetY;
        var objectframeNode = elem.parentNode.parentNode;
        var graphwidth = parseInt (imagewidth (elem));
        var graphheight = parseInt (imageheight (objectframeNode, elem));
        clickstring += ':' + graphwidth + ':' + graphheight;
    }
    this.Set (name, "click", clickstring, true);
}

Qva.ChangeZIndex = function (id, revert) {
    var elem = window.document.getElementById (id);
    if (elem) {
        var zindex = parseInt (elem.style.zIndex);
        if (isNaN (zindex)) return;
        zindex += revert ? -1 : 1;
        elem.style.zIndex = zindex;
    }
}

Qva.BringToFront = function (id) {
    if (Qva.CurrentObject ==  id) return;
    Qva.ChangeZIndex (Qva.CurrentObject + '_bkg', true);
    Qva.ChangeZIndex (Qva.CurrentObject, true);
    Qva.CurrentObject = id;
    Qva.ChangeZIndex (Qva.CurrentObject + '_bkg', false);
    Qva.ChangeZIndex (Qva.CurrentObject, false);
}

Qva.SetBackgroundSize = function () {
    var BackgroundContainer = window.document.getElementById ("BackgroundContainer");
    if (! BackgroundContainer) return;
    var height = Qva.GetViewportHeight ();
    height -= 1;
    height -= BackgroundContainer.offsetTop;
    height -= BackgroundContainer.parentNode.offsetTop;
    var width = Qva.GetViewportWidth ();
    if (parseInt (BackgroundContainer.style.height) != height) BackgroundContainer.style.height = height + "px";
    if (parseInt (BackgroundContainer.style.width) != width) BackgroundContainer.style.width = width + "px";
}

Qva.PageBinding.prototype.SetNewSheet = function() {
    var prevtab = window.document.getElementById (Qva.CurrentTab);
    if (prevtab) {
        prevtab.className = "";
    }
    var ActiveSheetNode = this.Select (".ActiveSheet");
    if (ActiveSheetNode) Qva.CurrentTab = ActiveSheetNode.getAttribute ("text");
    if (Qva.CurrentTab) {
        var newtab = window.document.getElementById (Qva.CurrentTab);
        if (newtab) {
            newtab.className = "selected";
        }
        this.NavigateToSheet (Qva.CurrentTab);
    }
}

Qva.PageBinding.prototype.NavigateToSheet = function (id) {
    var parameters = window.location.search;
    var url = id + ".htm";
    var loc = "" + window.location.pathname;
    if (loc.length < url.length || loc.substr (loc.length - url.length) != url) {
        if (parameters.length > 0) url += parameters;
        if (this.JSON) url = Qva.FixUrl(url, 'session', this.Session);
        window.location = url;
    }
}

Qva.CloseModal = function () {
    if (window.parent.qva != null && window.parent.qva.Modal != null) {
        var master = window.parent.qva;
        master.Modal.Hide();
        master.Set('.Nothing', 'add', 'nothing', true);
    }
}
Qva.SetModalTitle = function (text) {
    if (window.parent.qva != null && window.parent.qva.Modal != null) window.parent.qva.Modal.SetTitle(text);
}

Qva.OpenDragRect = function (X, Y) {
    X = Qva.StartoffsetX - Qva.SelectClient2OffsetX + Qva.GetScrollLeft ();
    Y = Qva.StartoffsetY - Qva.SelectClient2OffsetY + Qva.GetScrollTop ();
    if (Qva.DragRect == null) {
        Qva.DragRect = document.createElement ("div");
        Qva.DragRect.className = "QvDragRect";
        Qva.DragRect.style.position = "absolute";
        Qva.DragRect.style.left = X + "px";
        Qva.DragRect.style.top = Y + "px";
        Qva.DragRect.style.width = "0px";
        Qva.DragRect.style.height = "0px";
        Qva.DragRect.style.zIndex = 666;
        Qva.DragRect.style.display = '';

        Qva.DragRect.onmousemove = function (event) { Qva.MouseMove(event, Qva.MgrWithSelectStart); }
        
        document.body.insertBefore (Qva.DragRect, document.body.firstChild);
    } else {
        Qva.DragRect.style.left = X + "px";
        Qva.DragRect.style.top = Y + "px";
        Qva.DragRect.style.display = '';
    }
    
    Qva.DragStartX = X;
    Qva.DragStartY = Y;
    Qva.DragRectLeft = X;
    Qva.DragRectTop = Y;
    Qva.DragRectWidth = 0;
    Qva.DragRectHeight = 0;
}

Qva.SizeDragRect = function (X, Y) {
    X += Qva.GetScrollLeft ();
    Y += Qva.GetScrollTop ();
    Qva.DragRectLeft = X > Qva.DragStartX ? Qva.DragStartX : X;
    Qva.DragRectTop = Y > Qva.DragStartY ? Qva.DragStartY : Y;
    Qva.DragRectWidth = X > Qva.DragStartX ? X - Qva.DragStartX : Qva.DragStartX - X;
    Qva.DragRectHeight = Y > Qva.DragStartY ? Y - Qva.DragStartY : Qva.DragStartY - Y;
    Qva.DragRect.style.left = Qva.DragRectLeft + "px";
    Qva.DragRect.style.top = Qva.DragRectTop + "px";
    Qva.DragRect.style.width = Qva.DragRectWidth + "px";
    Qva.DragRect.style.height = Qva.DragRectHeight + "px";
}

Qva.CloseDragRect = function (X, Y, name, binderid, elem) {
    Qva.SizeDragRect (X, Y);
    var left = Qva.DragRectLeft + Qva.SelectClient2OffsetX - Qva.GetScrollLeft ();
    var top = Qva.DragRectTop + Qva.SelectClient2OffsetY - Qva.GetScrollTop ();
    var width = Qva.DragRectWidth;
    var height = Qva.DragRectHeight;
    var rectstring = '' + left + ':' + top + ':' + width + ':' + height;
    Qva.DragRect.style.display = 'none';
    var objectframeNode = elem.parentNode.parentNode;
    var graphwidth = parseInt (imagewidth (elem));
    var graphheight = parseInt (imageheight (objectframeNode, elem));
    rectstring += ':' + graphwidth + ':' + graphheight;
    Qva.GetBinder(binderid).Set (name, "rect", rectstring, true);
}

Qva.OpenSizeRect = function (X, Y, left, top, width, height) {
    if (Qva.SizeRect == null) {
        Qva.SizeRect = document.createElement ("div");
        Qva.SizeRect.className = "QvMoveRect";
        Qva.SizeRect.style.position = "absolute";
        Qva.SizeRect.style.zIndex = 666;
        Qva.SizeRect.style.display = '';
        document.body.insertBefore (Qva.SizeRect, document.body.firstChild);
    } else {
        Qva.SizeRect.style.display = '';
    }
    document.onmousemove = function (event) { Qva.MouseMove(event, Qva.MgrWithSelectStart); }
    
    Qva.SizeStartX = X;
    Qva.SizeStartY = Y;
    Qva.SizeRectLeft = left;
    Qva.SizeRectTop = top;
    Qva.SizeRectWidth = width;
    Qva.SizeRectHeight = height;
    Qva.SizeRect.style.left = Qva.SizeRectLeft + "px";
    Qva.SizeRect.style.top = Qva.SizeRectTop + "px";
    Qva.SizeRect.style.width = Qva.SizeRectWidth + "px";
    Qva.SizeRect.style.height = Qva.SizeRectHeight + "px";
}

Qva.SizeSizeRect = function (X, Y) {
    Qva.SizeRect.style.left = Qva.SizeRectLeft + (X - Qva.SizeStartX) + "px";
    Qva.SizeRect.style.top = Qva.SizeRectTop + (Y - Qva.SizeStartY) + "px";
    Qva.SizeRect.style.width = Math.max(20, Qva.SizeRectWidth - (X - Qva.SizeStartX)) + "px";
    Qva.SizeRect.style.height = Math.max(20, Qva.SizeRectHeight - (Y - Qva.SizeStartY)) + "px";
}

Qva.CloseSizeRect = function (X, Y, name, factor, binderid) {
    document.onmousemove = null;
    var width = Math.round(Math.max(20, Qva.SizeRectWidth - (X - Qva.SizeStartX)) * factor);
    var height = Math.round(Math.max(20, Qva.SizeRectHeight - (Y - Qva.SizeStartY)) * factor);
    Qva.SizeRect.style.display = 'none';
    Qva.GetBinder(binderid).Set (name, "resize", 'tl:' + width + ':' + height, true);
}

Qva.MouseDown = function (event, mgr) {
    //if (! this.Enabled) return;
    Qva.MgrWithMouseDown = mgr;
    Qva.MgrWithSelectStart = null;
    if (! event) {
        event = window.event;
        if (mgr.Select != null) {
            event.returnValue = false;
        }
        Qva.StartoffsetX = window.event.offsetX;
        Qva.StartoffsetY = window.event.offsetY;
    } else {
        if (mgr.Select != null) {
            event.preventDefault ();
        }
        var evtOffsets = Qva.GetOffsets (event);
        Qva.StartoffsetX = evtOffsets.offsetX;
        Qva.StartoffsetY = evtOffsets.offsetY;
        Qva.StartoffsetX -= Qva.GetScrollLeft ();
        Qva.StartoffsetY -= Qva.GetScrollTop ();
    }
}

Qva.MouseMove = function (event, mgr, mindelta) {
    //if (! this.Enabled) return;
    if (Qva.MgrWithMouseDown != mgr && Qva.MgrWithSelectStart != mgr) return;
    var offsetX = 0;
    var offsetY = 0;
    if (! event) {
        event = window.event;
        if (mgr.Select != null) {
            event.returnValue = false;
        }
        offsetX = event.offsetX;
        offsetY = event.offsetY;
    } else {
        if (mgr.Select != null) {
            event.preventDefault ();
        }
        var evtOffsets = Qva.GetOffsets (event);
        offsetX = evtOffsets.offsetX - Qva.GetScrollLeft ();
        offsetY = evtOffsets.offsetY - Qva.GetScrollTop ();
    }
    var X = event.clientX;
    var Y = event.clientY;
    if (Qva.MgrWithMouseDown == mgr) {
        var deltaX = offsetX - Qva.StartoffsetX;
        var deltaY = offsetY - Qva.StartoffsetY;
        if (mindelta == null) mindelta = 4;
        if (deltaX < -mindelta || deltaX > mindelta || deltaY < -mindelta || deltaY > mindelta) {
            Qva.MgrWithMouseDown = null;
            Qva.MgrWithSelectStart = mgr;
            Qva.SelectClient2OffsetX = offsetX - X;
            Qva.SelectClient2OffsetY = offsetY - Y;
            if (Qva.MgrWithSelectStart.SelectStart != null) {
                Qva.MgrWithSelectStart.SelectStart (X, Y);
            }
        }
    }
    if (Qva.MgrWithSelectStart == mgr) {
        if (Qva.MgrWithSelectStart.Select != null) {
            Qva.MgrWithSelectStart.Select (X, Y);
        }
    }
}

Qva.PageBinding.prototype.ContextClientAction = function (event, elem) {
    if (! event) { event = window.event; }
    event.cancelBubble = true;
    if (elem.clientaction == "search") {
        Qva.OpenPopupSearch (elem, elem.param);
    } else if (elem.clientaction == "modal" && this.Modal != null) {
        var modals = elem.param.split(':');
        this.Modal.Show (this.ScriptPath + modals[0] + '.htm?target=' + escape(elem.Name || elem.AvqMgr.Name), parseInt(modals[1]), parseInt(modals[2]));
    } else if (elem.clientaction == "inputfield") {
        Qva.OpenPopupInput (elem);
    } else if (elem.clientaction == "confirm") {
        var parts = elem.param.split(':');
        var name = (elem.Name || elem.AvqMgr.Name) + '.' + parts[0];
        var msg = parts.slice(1).join(':');
        if (window.confirm(msg)) {
            this.Set(name, 'action', '', true);
        }
    } else if (elem.clientaction == "url") {
        window.open (elem.param);
    } else {
        alert ("Not supported clientside action: " + elem.clientaction); 
    }
    Qva.CloseContextMenu ();
    return false;
}

Qva.PageBinding.prototype.OnContextMenu = function (event, name) {
    if (! event) event = window.event;
    if (event.shiftKey && ctrlKeyPressed (event)) return; 
    Qva.CloseContextMenu ();
    var position = null;
    var fullname;
    var srcelement = event.srcElement;
    if (! srcelement) {
        srcelement = event.target;
    }
    if (srcelement.position != null) {
        fullname = this.DefaultScope + "." + srcelement.targetname;
        position = srcelement.position;
    } else if (name) {
        fullname = name;
        position = "";
    } else {
        fullname = this.DefaultScope + '.ActiveSheet.StandardActions';
    }
    return this.OnCreateContextMenu (this, event, fullname, position);
}

Qva.CloseContextMenu = function () {
    if (Qva.ContextMenu == null) return;
    var mgr = Qva.ContextMenuMgr;
    var binder = mgr.PageBinder || Qva.GetBinder(mgr.binderid);
    binder.Set (mgr.Name, 'remove', 'menu', false);
    binder.RemoveFromManagers (mgr);
    binder.RemoveFromMembers (mgr);
    document.body.removeChild (Qva.ContextMenu);
    Qva.ContextMenu = null;
    Qva.ContextMenuMgr = null;
}

Qva.PageBinding.prototype.TryAltAgent = function () {
    try {
        if(external && typeof (external.AvqIdent) == "string") {
            var _this = this;
            external.AvqInitServer (this.View, function() { if (_this.Enabled) _this.LoadBegin(); });
            if (this.HostedTitle == null) {
                external.AvqTitle (document.title);
            } else if (this.HostedTitle != "") {
                external.AvqTitle (this.HostedTitle);
            }
            this.IsRemote = false;
            this.IsHosted = true;
            this.Ident = external.AvqIdent;
            this.Kind = external.AvqKind;
            this.Agent = external;
        } else if (this.AllowComAgent) {
            try {
                var segm = /[\?\&]admin=/.exec (this.Remote);
                this.Agent = new ActiveXObject ("QvsRemote.Client");
            this.UseExecute = true;
                if (segm == null) {
                    this.Agent.Connect("localhost", true);
                } else {
                    this.Agent.AdminConnect("localhost");
                }
                this.IsRemote = false;
            } catch (e) {
                // will fail later
            }
        }
    } catch (e) {
        debugger
    }
}

Qva.PageBinding.prototype.BuildBinaryUrl = function (path, stamp) {
    if (this.IsHosted) return path;
    var url = this.Url;
    url = url.replace ('mark=', '');
    url += 'datamode=binary' + '&ident=' + escape (this.Ident);
    if (stamp != null) {
        url += '&stamp=' + stamp;
    }
    if (this.Session != null && this.JSON) url += '&session=' + escape (this.Session);
    if (this.View != null) url += '&view=' + escape (this.View);
    if (this.Kind != null) url += '&kind=' + this.Kind;
    if (this.Userid != null) url += '&userid=' + this.Userid;
    if (this.Xuserid != null) url += '&xuserid=' + this.Xuserid;
    if (this.Password != null) url += '&password=' + this.Password;
    if (this.Xpassword != null) url += '&xpassword=' + this.Xpassword;
    return url;
}

Qva.PageBinding.prototype.RemoveFromManagers = function (mgr) {
    var mix = -1;
    for (var ix = 0; ix < this.Managers.length; ++ ix) {
        if (this.Managers [ix] == mgr) {
            mix = ix;
            break;
        }
    }
    if (mix == -1) {
        debugger;
    } else {
        if (mix != this.Managers.length - 1) {
            var swap = this.Managers [mix];
            this.Managers [mix] = this.Managers [this.Managers.length - 1];
            this.Managers [this.Managers.length - 1] = swap;
            mix = this.Managers.length - 1;
        }
    }
    if (this.Managers [mix].Element.AvqMgr != null) {
        if (this.Managers [mix].Element.AvqMgr.Detach !== null) this.Managers [mix].Element.AvqMgr.Detach ();
        this.Managers [mix].Element.AvqMgr = null;
    }
    this.Managers [mix].Element = null;
    this.Managers.length = mix;
}
Qva.PageBinding.prototype.RemoveFromMembers = function (mgr) {
    var name = mgr.Name;
    if (name == null) debugger;
    if (name == null || name == "") return;
    var oldlist = this.Members[name];
    if (oldlist == null) {
        debugger;
        return;
    }
    var newlist = new Array ();
    for (var i = 0; i < oldlist.length; i ++) {
        if (oldlist [i] != mgr) {
            newlist [newlist.length] = oldlist [i];
        }
    }
    if (oldlist.length == newlist.length) {
        debugger;
    }
    this.Members[name] = newlist;
}

function AvqAction_Input_KeyDown (event) {
    if (! event) { event = window.event; }
    var key = event.keyCode;
    switch (key) {
    case 13:    // <Enter>
        Qva.GetBinder(this.binderid).Set (this.targetname, "inputvalue", this.xx + ":" + this.yy + ":" + this.value, true);
    case 27:    // <Escape>
        break;
    }
}

Qva.NoAction = function () { }
Qva.CancelAction = function (event) {
    if (! event) event = window.event;
    event.cancelBubble = true;
    this.pressed = true;
}

Qva.DefaultShowMessage = function (msg) { alert(msg); }
Qva.DefaultOnSessionLost = function (msg) {
    if (!window.confirm(msg + '\r\nReconnect?')) return;
    var url = "" + document.location;
    var re = new RegExp ("[\?\&]session=([^\&]+)", "i");
    url = url.replace(re, "");
    document.location = url;
}

Qva.DefaultOnCreateContextMenu = function (binder, event, fullname, position) {
    event.cancelBubble = true;
    Qva.ContextMenu = document.createElement ('table');
    var X = event.clientX + Qva.GetScrollLeft ();
    var Y = event.clientY + Qva.GetScrollTop ();
    Qva.ContextMenu.style.left = X + "px";
    Qva.ContextMenu.style.top = Y + "px";
    Qva.ContextMenu.style.width = "150pt";
    var tr = Qva.ContextMenu.insertRow (-1);
    var td = tr.insertCell (-1);
    td.innerHTML = "<\BR>";
    Qva.ContextMenu.style.position = "absolute";
    Qva.ContextMenu.style.zIndex = 666;
    Qva.ContextMenu.className = "contextmenu";
    var mgr = new Qva.Mgr.menu (binder, Qva.ContextMenu, fullname);
    Qva.ContextMenuMgr = mgr;
    if (mgr.Name != null) {
        mgr.AdjustSize = true;
        binder.Set (mgr.Name, 'add', 'menu', false);
        if (position != null) binder.Set (mgr.Name, 'position', position, false);
    } else {
        debugger;
    }
    document.body.insertBefore (Qva.ContextMenu, document.body.firstChild);
    Qva.ContextMenu.focus ();
    Qva.KeepContextMenuAlive = true;
    binder.LoadBegin ();
    return false;
}

Qva.XmlEncode = function (value) {
    var val = '' + value;
    val = val.replace (/&/g, '&amp;');
    val = val.replace (/</g, '&lt;');
    val = val.replace (/>/g, '&gt;');
    val = val.replace (/"/g, '&quot;');
    return val;
}
Qva.GetMessage = function(root) {
    var msg_nodes = root.getElementsByTagName ('message');
    if (msg_nodes.length >= 1) {
        var msg = msg_nodes[0].getAttribute ('text');
        if (msg && msg != '') return msg;
    }
    return null;
}

Qva.GetViewportHeight = function () {
    if (window.innerHeight!=window.undefined) return window.innerHeight;
    if (document.compatMode=='CSS1Compat') return document.documentElement.clientHeight;
    if (document.body) return document.body.clientHeight; 
}
Qva.GetViewportWidth = function () {
    if (window.innerWidth!=window.undefined) return window.innerWidth; 
    if (document.compatMode=='CSS1Compat') return document.documentElement.clientWidth; 
    if (document.body) return document.body.clientWidth; 
}
Qva.GetScrollTop = function () {
    if (self.pageYOffset) return self.pageYOffset; // all except Explorer
    if (document.documentElement && document.documentElement.scrollTop) return document.documentElement.scrollTop; // Explorer 6 Strict
    return document.body.scrollTop; // all other Explorers
}

Qva.GetScrollLeft = function () {
    if (self.pageXOffset) return self.pageXOffset; // all except Explorer
    if (document.documentElement && document.documentElement.scrollLeft) return document.documentElement.scrollLeft; // Explorer 6 Strict
    return document.body.scrollLeft; // all other Explorers
}

Qva.MgrSplit = function (mgr, name, namePrefix) {
    if (name == null) return false;
    var segm = name.split ('@');
    switch (segm.length) {
    case 1:
        mgr.Attr = 'text';
        break;
    case 2:
        mgr.Attr = segm [1];
        break;
    case 3:
        mgr.Attr = (segm [1] != '') ? segm [1] : 'text';
        mgr.Dec = parseInt (segm [2]);
        break;
    default:
        return false;
    }
    mgr.Name = Qva.MgrMakeName (segm [0], namePrefix);
    return true;
}
Qva.MgrMakeName = function (name, namePrefix) {
    if (name == null) debugger;
    if (name.substr (0,1) == '.') {
        if (namePrefix == null) debugger; 
        return namePrefix + name;
    } else {
        return name;
    }
}

Qva.MgrGetDisplayFromMode = function (mgr, mode) {
    if (mgr.Element.disabled && mgr.ModeIfNotEnabled == 'h') {
        return 'none';
    } else if (mode == 'd' || mode == 'e' || mgr.ModeIfNotEnabled == 'd') {
        return '';
    } else {
        return 'none';
    }
}

Qva.Trunc = function (txt, ndec) {
    var dot = txt.indexOf ('.');
    if (dot < 0) return txt;
    var adec = txt.length - dot - 1;
    if (adec <= ndec) return txt;
    var f = parseFloat (txt);
    if (isNaN (f)) return txt;
    var fact = Math.pow (10, ndec);
    f = Math.round (f * fact) / fact;
    return f.toString ();
}

Qva.LockDisabled = function () {
    this.Locked = this.Element.disabled;
    this.Element.disabled = true;
}
Qva.UnlockDisabled = function () { this.Element.disabled = this.Locked; }
Qva.LockReadOnly = function () {
    this.Locked = this.Element.readOnly;
    this.Element.readOnly = true;
}
Qva.UnlockReadOnly = function () { this.Element.readOnly = this.Locked; }

Qva.FixUrl = function (url, parameter, value) {
    var re = new RegExp ("[\?\&]" + parameter + "=[^\&]*", "i");
    url = url.replace(re, "");
    if(value != null) url += '&' + parameter + '=' + escape(value);
    if(url.indexOf ('?') == -1) url = url.replace(/&/, "?");
    return url;
}
Qva.ExtractProperty = function (name, defprop, allowEmpty) {
    if(allowEmpty) {
        var re = new RegExp ("[\?\&]" + name + "=([^\&]*)", "i");
    } else {
        var re = new RegExp ("[\?\&]" + name + "=([^\&]+)", "i");
    }
    var segm = re.exec (window.location);
    try {
        if (segm == null) segm = re.exec (top.location);
    } catch (e) {
    }
    return segm != null ? unescape (segm [1]) : defprop;
}

Qva.GetAbsolutePageCoords = function (element) {
    var coords = {x : 0, y : 0};
    while (element) {
        coords.x += element.offsetLeft;
        coords.y += element.offsetTop;
        element = element.offsetParent;
    }
    return coords;
}

Qva.GetPageCoords = function (element) {
    var coords = {x : 0, y : 0};
    while (element) {
        coords.x += element.offsetLeft;
        coords.y += element.offsetTop;
        element = element.offsetParent;
    }
    coords.x -= Qva.GetScrollLeft ();
    coords.y -= Qva.GetScrollTop ();
    return coords;
}

Qva.GetOffsets = function (event) {
    var target = event.target;
    if (typeof target.offsetLeft == 'undefined') {
        target = target.parentNode;
    }
    var pageCoords = Qva.GetPageCoords (target);
    var eventCoords = { 
        x: window.pageXOffset + event.clientX,
        y: window.pageYOffset + event.clientY
    };
    var offsets = {
        offsetX: eventCoords.x - pageCoords.x,
        offsetY: eventCoords.y - pageCoords.y
    };
    return offsets;
}

Qva.SetCursor = function (elem, input) {
    if (window.document.selection) { // the deep ie caret position magic
        var sel = window.document.selection.createRange ();
        sel.moveStart ('character', -elem.value.length);
        sel.moveEnd ('character', -elem.value.length);
        if (input) {
            sel.moveStart ('character', 0);
            sel.moveEnd ('character', elem.value.length);
        } else {
            sel.moveStart ('character', elem.value.length > 0 ? elem.value.length - 1 : 1);
        }
        sel.select ();
    } else if (elem.selectionStart) { // mozilla
        if (input) {
            elem.selectionStart = 0;
            elem.selectionEnd = elem.value.length;
        } else {
            elem.selectionStart = Math.max (1, elem.value.length - 1);
            elem.selectionEnd = Math.max (1, elem.value.length - 1);
        }
    }
}

Qva.CancelBubble = function (event) {
    if (!event) {
        window.event.cancelBubble = true;
    } else {
        event.stopPropagation();
    }
}

function dosearch (elem) { Qva.OpenPopupSearch (elem); }


function AvqAction_Search_KeyDown(event) {
    if (! event) { event = window.event; }
    var mgr = this.AvqMgr;
    var key = event.keyCode;
    switch (key) {
    case 13:    // <Enter>
        Qva.CloseSearch (mgr, this, true, ctrlKeyPressed (event));
        break;
    case 27:    // <Escape>'
        var _this = this;
        window.setTimeout (function () { Qva.CloseSearch (mgr, _this, false); }, 0);
        break;
    }
}
function AvqAction_Search_KeyUp(event) {
    if (!event) { event = window.event; }
    var key = event.keyCode;
    switch (key) {
    case 13:    // <Enter>
    case 27:    // <Escape>
        break;
    default:
        if (this.searchcol == null) {
            Qva.Search(this.AvqMgr, this, key);
        }
        break;
    }
}

function AvqAction_Search_Focus () {
    if (this.value == "") {
        this.value = this.param != null ? this.param : "**";
        Qva.SetCursor (this);
    }
}

function imageheight (objectframeNode, element) {
	var graphheight = element.style.height;
	if (graphheight == 'auto' || element.autosize) {
	    if (graphheight == 'auto') element.autosize = true;
        if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
            graphheight = objectframeNode.offsetHeight;
            var gs = document.defaultView.getComputedStyle (element.parentNode, "");
            var pad = parseInt (gs.getPropertyValue ("padding-top")) + parseInt (gs.getPropertyValue("padding-bottom"));
            var bor = parseInt (gs.getPropertyValue ("border-top-width")) + parseInt (gs.getPropertyValue("border-bottom-width"));
            graphheight -= pad;
            graphheight -= bor;
        } else {
            graphheight = objectframeNode.clientHeight;
        }
        if (objectframeNode != element.parentNode) {
            var numberofchildren = objectframeNode.childNodes.length;
            for (var ichild = 0; ichild < numberofchildren; ichild++) {
                var child = objectframeNode.childNodes [ichild];
		        if (child.tagName != "DIV") continue;
		        if (child == element.parentNode) continue;
		        graphheight -= child.offsetHeight;    
		    }
	    }
        if (graphheight < 0) {
	        graphheight = element.parentNode.clientHeight;
        }
		element.style.height = graphheight + "px";
	    return graphheight;
	} else {
	    return element.offsetHeight;
	}
}

function getOffsetHeight (elem) {
    var offsetheight = elem.offsetHeight;
    if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
        var gs = document.defaultView.getComputedStyle (elem, "");
        var pad = parseInt (gs.getPropertyValue ("padding-top")) + parseInt (gs.getPropertyValue("padding-bottom"));
        var bor = parseInt (gs.getPropertyValue ("border-top-width")) + parseInt (gs.getPropertyValue("border-bottom-width"));
        offsetheight -= pad;
        offsetheight -= bor;
    } else {
        var pad = parseInt (elem.style.paddingTop);
        if (! isNaN (pad)) offsetheight -= pad;
        pad = parseInt (elem.style.paddingBottom);
        if (! isNaN (pad)) offsetheight -= pad;
        var bor = parseInt (elem.style.borderTopWidth);
        if (! isNaN (bor)) offsetheight -= bor;
        bor = parseInt (elem.style.borderBottomWidth);
        if (! isNaN (bor)) offsetheight -= bor;
    }
    return offsetheight;
}

function getContentMaxHeight (element) {
    var objectframeNode = element.parentNode.parentNode;
    var newparentheigh = objectframeNode.offsetHeight;
    newparentheigh -= (element.parentNode.offsetHeight - getOffsetHeight (element.parentNode));
    var numberofchildren = objectframeNode.childNodes.length;
    for (var ichild = 0; ichild < numberofchildren; ichild++) {
        var child = objectframeNode.childNodes [ichild];
        if (child == element.parentNode) break;
        if (child.nodeName != "DIV") continue;
        newparentheigh -= child.offsetHeight;
    }
    if (newparentheigh > 0) return newparentheigh;
    return element.parentNode.style.height;
}

function getClientWidth (elem, hasverticalscrollbar) {
    var clientWidth;
    if (hasverticalscrollbar) {
        if (IS_OPERA) {
            clientWidth = elem.clientWidth;
        } else {
            clientWidth = (elem.scrollWidth < elem.clientWidth) ? elem.scrollWidth : elem.clientWidth;
        }
    } else {
        clientWidth = elem.offsetWidth;
    }
    if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
        var gs = document.defaultView.getComputedStyle (elem, "");
        var pad = parseInt (gs.getPropertyValue ("padding-left")) + parseInt (gs.getPropertyValue("padding-right"));
        var bor = parseInt (gs.getPropertyValue ("border-left-width")) + parseInt (gs.getPropertyValue("border-right-width"));
        clientWidth -= pad;
        clientWidth -= bor;
    } else {
        var pad = parseInt (elem.currentStyle.paddingLeft);
        if (! isNaN (pad)) clientWidth -= pad;
        pad = parseInt (elem.currentStyle.paddingRight);
        if (! isNaN (pad)) clientWidth -= pad;
        var bor = parseInt (elem.currentStyle.borderLeftWidth);
        if (! isNaN (bor)) clientWidth -= bor;
        bor = parseInt (elem.currentStyle.borderRightWidth);
        if (! isNaN (bor)) clientWidth -= bor;
    }
    return clientWidth;
}

function getClientHeight (elem) {
    var clientHeight = elem.offsetHeight;
    if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
        var gs = document.defaultView.getComputedStyle (elem, "");
        var pad = parseInt (gs.getPropertyValue ("padding-top")) + parseInt (gs.getPropertyValue("padding-bottom"));
        var bor = parseInt (gs.getPropertyValue ("border-top-width")) + parseInt (gs.getPropertyValue("border-bottom-width"));
        clientHeight -= pad;
        clientHeight -= bor;
    } else {
        var pad = parseInt (elem.style.paddingTop);
        if (! isNaN (pad)) clientHeight -= pad;
        pad = parseInt (elem.style.paddingBottom);
        if (! isNaN (pad)) clientHeight -= pad;
        var bor = parseInt (elem.style.borderTopWidth);
        if (! isNaN (bor)) clientHeight -= bor;
        bor = parseInt (elem.style.borderBottomWidth);
        if (! isNaN (bor)) clientHeight -= bor;
    }
    return clientHeight;
}

function setContentWidth (parentNode, element, newwidth, resetwidth) {
    var objectframeNode = parentNode.parentNode;
    if (resetwidth && objectframeNode.maxwidth) {
        newwidth = objectframeNode.maxwidth;
    } else if (! objectframeNode.maxwidth) {
        objectframeNode.maxwidth = objectframeNode.offsetWidth;
    }
    var numberofchildren = objectframeNode.childNodes.length;
    var anychanges = false;
    for (var ichild = 0; ichild < numberofchildren; ichild++) {
        var child = objectframeNode.childNodes [ichild];
        if (child.nodeName != "DIV") continue;
        if (child.style.display == 'none') continue;
        var currentwidth = parseInt (child.style.width);
        var newchildwidth = newwidth;
        newchildwidth = newwidth - child.offsetWidth + getClientWidth (child);
        if (currentwidth != newchildwidth) {
            child.style.width = newchildwidth + "px";
            anychanges = true;
        }
    }
    return anychanges; 
}

function imagewidth (element) {
	var graphwidth = element.style.width;
	if (graphwidth == 'auto' || element.autosize) {
	    if (graphwidth == 'auto') element.autosize = true;
		graphwidth = getClientWidth (element.parentNode);
		element.style.width = graphwidth + "px";
	    return graphwidth;
	} else {
	    return element.offsetWidth;
	}
}

Qva.Hover = null;
Qva.PageBinding.prototype.GetHoverDiv = function () {
    if(!Qva.Hover) {
        Qva.Hover = document.createElement("div");
        Qva.Hover.className = "QvHover";
        Qva.Hover.style.zIndex = 666;
        Qva.Hover.style.display = "none";
        Qva.Hover.style.position = "absolute";
        Qva.Hover.style.backgroundColor = "#FFFFCC";
        Qva.Hover.style.border = "solid 1px black";
        Qva.Hover.style.padding = "1px 3px 2px 3px";
        
        document.body.appendChild(Qva.Hover);
        
        new Qva.Mgr.hover (this, Qva.Hover, this.DefaultScope + ".Hover");
    }
    return Qva.Hover;
}

// TODO: remove
Qva.PageBinding.prototype.BringToFront = Qva.BringToFront;
Qva.PageBinding.prototype.SetModalTitle = Qva.SetModalTitle;
Qva.PageBinding.prototype.CloseModal = Qva.CloseModal;
