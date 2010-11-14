// Build $BuildVersion$

if (!Qva.Mgr) Qva.Mgr = {} 

Qva.Mgr.table = function (owner, elem, name, prefix, condition) {
    this.Name = Qva.MgrMakeName ((name != null) ? name : '', prefix);
//    this.Condition = condition;
    owner.AddManager(this);
    this.LeftButton = owner.LeftButton;
    this.Element = elem;
    elem.AvqMgr = this;
    this.TableScan (owner, prefix);
    owner.Append (this, this.Name, 'choice');
}

Qva.Mgr.table.prototype.CellObject = function (optval, value) {
    this.val = optval ? optval : "";
    var index = value.getAttribute ("value");
    if (index) {
        this.intval = index;
        this.selected = value.getAttribute ("selected") == "yes";
        this.deselected = value.getAttribute ("deselected") == "yes";
        this.selectedexcluded = value.getAttribute ("selectedexcluded") == "yes";
        this.locked = value.getAttribute ("locked") == "yes";
        this.frequency = value.getAttribute ("frequency");
    } else {
        if (value.getAttribute ("locked")) {
            this.locked = value.getAttribute ("locked") == "yes";
            this.byval = true;
        }
        if (value.getAttribute ("selected")) {
            this.selected = value.getAttribute ("selected") == "yes";
            this.byval = true;
        }
    }
    this.disabled = value.getAttribute ("mode") == "disabled";
    this.style = value.getAttribute ("style");
    this.isnum = value.getAttribute ("isnum") == "true";
    this.icons = value.getElementsByTagName ("icon");
    this.subcell = value.getAttribute ("subcell");
    this.first = value.getAttribute ("first");
    var selecttype = value.getAttribute ("selecttype");
    if (selecttype) {
        this.selecttype = selecttype;
        this.selectsource = value.getAttribute ("selectsource") == "true";
    }
    this.input = value.getAttribute ("input") == "true";
}

Qva.Mgr.table.prototype.TableScan = function (owner, prefix) {
    var element = this.Element;
    this.Selected = new Array ();
    var groupname = element.getAttribute ('AvqGroup');
    if (groupname != null) {
        this.Group = Qva.MgrMakeName (groupname, prefix);
        owner.Append (this, this.Group);
    }
    this.PageName = this.Name;
    this.PageOffset = 0;
    this.PageIncr = 0;
    this.PageSize = 0; // unlimited
    this.TotalSize = 0;
    this.ColMgr = [];
    this.FinalFix = false;
    this.Fixed = false;
    this.RowHeight = -1;
    this.RowSpan = 1;
    this.AllwaysFullWidth = false;
    this.PageFirst = null;
    this.PagePrev = null;
    this.PageNext = null;
    this.PageLast = null;
    this.Search = null;
    this.PageHandler = 'client';
    this.SearchName = this.Name;
    this.Searchable = false;
    this.IsAsync = false;
    this.IsTransient = false;
    this.TableLimit = owner.TableLimit;
    this.InlineStyle = owner.InlineStyle;
    if (element.getAttribute ('AvqStyle')) {
        this.InlineStyle = element.getAttribute ('AvqStyle') == "true";
    }
    var page = element.getAttribute ('AvqPage');
    var navigation = element.getAttribute ('AvqNavigation');
    var async = element.getAttribute ('AvqAsync');
    var header = element.getAttribute ('avqheader');
    var body = element.getAttribute ('avqbody');
    this.IsHeader = false;
    this.IsBody = false;
    if (header != null) {
        this.IsHeader = header == "true";
    }
    if (body != null) {
        this.IsBody = body == "true";
    }
    if (page != null && navigation != null) {
        m_errors [m_errors.length] = 'AvqPage and AvqNavigation are mutually exclusive';
        return;
    }
    if (page != null && async != null) {
        m_errors [m_errors.length] = 'AvqPage and AvqAsync are mutually exclusive';
        return;
    }
    if (page != null) {
        var pages = page.split (':');
        if (pages.length > 3) {
            m_errors [m_errors.length] = 'Invalid AvqPage: ' + page;
            return;
        }
        var pageSize = parseInt (pages[0]);
        if (isNaN (pageSize) || pageSize <= 0) {
            m_errors [m_errors.length] = 'Invalid page-size in AvqPage: ' + page;
            return;
        }
        this.PageSize = pageSize;
        if (pages.length >= 2) {
            if (pages.length == 2 || pages[1] == '+') {
                this.PageIncr = this.PageSize;
            } else {
                var next = document.getElementById (pages [1]);
                if (next == null) {
                    m_errors [m_errors.length] = 'Invalid next in AvqPage: ' + page;
                    return;
                }
                this.PageNext = next;
            }
        }
        if (pages.length >= 3) {
            var prev = document.getElementById (pages [2]);
            if (prev == null) {
                m_errors [m_errors.length] = 'Invalid prev in AvqPage: ' + page;
                return;
            }
            this.PagePrev = prev;
        }
    }
    if (navigation != null) {
        var navigations = navigation.split (':');
        if (navigations.length > 3) {
            m_errors [m_errors.length] = 'Invalid AvqPage: ' + navigation;
            return;
        }
        if (navigations [0] != '') {
            var pageSize = parseInt (navigations [0]);
            if (isNaN (pageSize) || pageSize <= 0) {
                m_errors [m_errors.length] = 'Invalid page-size in AvqNavigation: ' + page;
                return;
            }
            this.PageSize = pageSize;
        }
        if (navigations.length >= 2) {
            var pageHandler = navigations [1];
            if (pageHandler != 'client' && pageHandler != 'server') {
                m_errors [m_errors.length] = 'Invalid page-handler in AvqNavigation: ' + pageHandler;
                return;
            }
            this.PageHandler = pageHandler;
        }
        if (navigations.length >= 3) {
            if (navigations [2] != '') {
                this.SearchName = navigations [2];
                owner.Append (this, this.SearchName);
            } else {
                this.SearchName = null;
            }
        }
    }
    if (async != null) {
        if (async != '') {
            var asyncs = async.split (':');
            if (asyncs.length > 2) {
                m_errors [m_errors.length] = 'Invalid AvqAsync: ' + navigation;
                return;
            }
            var pageSize = parseInt (asyncs [0]);
            if (isNaN (pageSize) || pageSize <= 0) {
                m_errors [m_errors.length] = 'Invalid page-size in AvqAsync: ' + page;
                return;
            }
            this.PageSize = pageSize;
            if (asyncs.length >= 2) {
                this.PageName = Qva.MgrMakeName (asyncs [1], prefix);
            }
        } else {
            this.PageSize = 20;
        }
        this.IsAsync = true;
    }
    if (this.PageIncr == 0 && this.PageHandler == 'client') {
        this.PageIncr = this.PageSize > 0 ? this.PageSize : 20;
    }
    if (this.PageHandler == 'server' || this.IsAsync) {
        owner.Append (this, this.PageName, 'pageoffset');
        owner.Append (this, this.PageName, 'pagesize');
        owner.Append (this, this.PageName, 'totalsize');
        owner.SetInitial (this.PageName, 'pagesize', this.PageSize);
        owner.SetInitial (this.PageName, 'pageoffset', 0);
    }
    if (this.IsHeader || this.IsBody) {
        owner.Append (this, this.PageName, 'fixedrows');
    }
    if (element.tagName == 'TABLE') {
        this.Body = element.tBodies [0];
        if (this.Body == null) {
            element.insertRow(-1);
            this.Body = element.tBodies [0];
        }
    } else {
        this.Body = element;
    }
    
    var body = this.Body;
    if (body.rows.length < 1) {
        body.appendChild(document.createElement("tr"));
        //m_errors [m_errors.length] = 'At least one row must exist: ' + this.Name;
    }
    
    var row = body.rows [0];
    row.rix = 0;
    this.Width = row.cells.length;
    
    this.AutoCol = this.Width === 0;
    
    var choice = null;
    if (this.Name != '') {
        choice = element.getAttribute ('AvqChoice');
        if (choice == null) choice = this.Name;
    }
    this.Lines = new Array ();
    this.RowNumbers = new Array ();
    this.Style = new Array ();
    this.BorderStyle = new Array ();
    this.IsPainted = new Array ();
    this.RowClassNames = new Array ();
    this.ColList = new Array ();
    this.ColDict = {};
    
    this.ChoiceIx = -1;
    
    var cix = 0;
    for (; cix < this.Width; ++cix) {
        var cell = row.cells[cix];
        
        var colfld = cell.getAttribute ('AvqCol');
        if (colfld == null) continue;
        if (colfld.indexOf (':') == -1 && colfld.indexOf ('.') >= 0) {
            // no separator specified but field specified -> assume 'edit'
            colfld = 'edit:' + colfld;
        }
        var parts = colfld.split (':');
        
        var cmd = parts[0];
        var name = (parts.length > 1) ? parts[1] : this.Name;
        var extra = (parts.length > 2) ? parts.slice(2).join (':') : null;
        if(this.ColMgr[cix]) {
            cmd = this.ColMgr[cix].cmd;
            extra = this.ColMgr[cix].extra;
        }
        
        var type = Qva.ColMgr[cmd] || Qva.ColMgr.basic;
        var colmgr = new type(this, cix, cell, name, prefix, extra);
        colmgr.Cmd = cmd;
        
        if (colmgr.Name == null) {
            m_errors [m_errors.length] = 'Invalid AvqCol "' + parts [1] + '": ' + this.Name;
            continue;
        }
//        switch (colmgr.Cmd) {
//        case 'tip':
//            break;
//        case 'show':
//        case 'hide':
//            if (parts.length == 2) {
//                this.Condition = null;
//            } else if (parts.length == 3) {
//                this.Condition = parts [2];
//            } else {
//                this.Condition = parts.slice (2).join (':');
//            }
//            break;
        if (this.Group == null) owner.Append (this, colmgr.Name);
        this.ColDict[colmgr.Name] = colmgr;
        this.ColList [this.ColList.length] = colmgr;
    }
    this.RowClassNames [0] = body.rows [0].className;
    
    var stripes = element.getAttribute ('AvqStripeClasses');
    if (stripes != null) {
        this.RowClassNames = this.RowClassNames.concat(('' + stripes).split(/\s/));
    }
    if (choice != null) {
        var colmgr = this.ColDict[choice];
        if (colmgr == null) {
            colmgr = new Qva.ColMgr.basic(this, cix, null, choice, prefix);
            if (this.Group == null) owner.Append (this, colmgr.Name);
            this.ColDict[choice] = colmgr;
            ++cix;
        }
        this.ChoiceIx = colmgr.Index;
    }
    if (navigation != null) {
        var newBody = document.createElement ('tbody');
        body.parentNode.insertBefore (newBody, body);
        var row = newBody.insertRow (-1);
        var cell = row.insertCell (-1);
        cell.className = "avqNavigation";
        cell.colSpan = this.Width;
        var s = '';
        var hasSearch = this.SearchName != null;
        var hasPaging = this.PageSize != 0 && ! this.IsAsync;
        if (hasSearch) {
            s += '<INPUT class="avqEdit" width="100%">';
        }
        if (hasPaging) {
            s += '<BUTTON class="avqButton">|&lt;</BUTTON><BUTTON class="avqButton">&lt;</BUTTON><BUTTON class="avqButton">&gt;</BUTTON><BUTTON class="avqButton">&gt;|</BUTTON>';
        }
        cell.innerHTML = s;
        if (hasSearch) {
            this.Search = cell.firstChild;
        }
        if (hasPaging) {
            this.PageFirst = this.SearchName != null ? this.Search.nextSibling : cell.firstChild;
            this.PagePrev = this.PageFirst.nextSibling;
            this.PageNext = this.PagePrev.nextSibling;
            this.PageLast = this.PageNext.nextSibling;
        }
    }
    if (this.Search != null) {
        this.Search.onkeydown = AvqAction_Search_KeyDown;
        this.Search.onkeyup = AvqAction_Search_KeyUp;
        this.Search.AvqMgr = mgr;
    }
    if (this.PageFirst != null || this.PagePrev != null || this.PageNext != null || this.PageLast != null) {
        if (this.PageFirst != null) {
            this.PageFirst.onclick = AvqAction_First;
            this.PageFirst.AvqMgr = mgr;
        }
        if (this.PagePrev != null) {
            this.PagePrev.onclick = AvqAction_Prev;
            this.PagePrev.AvqMgr = mgr;
        }
        if (this.PageNext != null) {
            this.PageNext.onclick = AvqAction_Next;
            this.PageNext.AvqMgr = mgr;
        }
        if (this.PageLast != null) {
            this.PageLast.onclick = AvqAction_Last;
            this.PageLast.AvqMgr = mgr;
        }
        this.Lock = AvqMgr_LockTable;
        this.Unlock = AvqMgr_UnlockTable;
    }
    if (this.PageIncr > 0 && ! this.IsHeader) {
        try {
            var pp = element.parentNode;
            pp.AvqMgrForScroll = this;
            pp.onscroll = this.ParentScroll;
            //alert (pp.tagName + ' ' + pp.id);
        } catch (e) {
            alert ("Error: " + element.parentNode.tagName);
        }
    }
}

var busy = false;

Qva.Mgr.table.prototype.ParentScroll = function() {
    if (busy) return;
    var mgr = this.AvqMgrForScroll;
    if (mgr == null) mgr = element.document.body.AvqMgrForScroll; // daft workaround for daft design... (onscroll on body behaves strangely)
    if (mgr == null) return;
    if (mgr.HeaderId != null) {
        busy = true;
        var header = document.getElementById (mgr.HeaderId);
        if (header.parentNode.scrollLeft != this.scrollLeft) {
            header.parentNode.scrollLeft = this.scrollLeft;
        }
        busy = false;
    }
    Qva.QueuePostPaintMessage (mgr);
}

Qva.Mgr.table.prototype.Lock = Qva.NoAction;
Qva.Mgr.table.prototype.Unlock = Qva.NoAction;

Qva.Mgr.table.prototype.FixCol = function (mode, node, name, partial) {
    var vals = new Array();
    for (var memb = node.firstChild; memb; memb = memb.nextSibling) {
        if (memb.nodeName != 'value') continue;
        if (memb.getAttributeNode('width') == null) continue;
        vals[vals.length] = memb;
    }
    if (this.Width == vals.length) return;

    var element = this.Element;
    var objectframeNode = element.parentNode.parentNode;
    if (objectframeNode.maxwidth) {
        setContentWidth (element.parentNode, element, objectframeNode.maxwidth, false);
        objectframeNode.removeAttribute ("maxwidth");
    }

    this.Width = vals.length;
    
    var bodyParent = this.Body.parentNode;
    var body = this.Body.cloneNode (false);
    var row = document.createElement ("tr");
    body.appendChild(row);
    row.rix = 0;
    
    this.ColList = [];
    if (this.Width == 0) return;    
    var cix = 0;
    var elem = vals [0] ? vals [0].childNodes [0] : null;
    var edit = elem && elem.getAttribute ('value');

    var tabletotwidth = 0;
    this.UnmodifiedTableWidth = null;
    for (; cix < this.Width; ++cix) {
        var cell = row.cells[cix];
        if (cell == null) cell = row.appendChild (document.createElement("td"));
        if (this.IsTransient) {
            this.AllwaysFullWidth = true;
        } else {
            cell.innerText = "Gg";
            cell.style.color = this.IsHeader ? "Menu" : "White";
            var width = vals[cix].getAttribute ("width");
            if (width) {
                if (width == "auto") {
                    cell.style.width = "" + 100 / this.Width + "%";
                    this.AllwaysFullWidth = true;
                } else {
                    var colwidth = parseInt (parseFloat (width) * 72 / 300);
                    cell.style.width = "" + colwidth + "pt";
                    tabletotwidth += colwidth;
                    if (IS_CHROME || IS_SAFARI) tabletotwidth += 2;
                    if (cix == (this.Width - 1)) {
                        this.LastColWidth = colwidth + "pt"
                    }
                }
            }
            if (this.InlineStyle) {
                var val = vals[cix].getElementsByTagName('element') [0];
                if (val) {
                    var data = new this.CellObject (val.getAttribute ("text"), val);
                    cell.style.cssText += this.SetCellStyle (data, true, this.Style, this.BorderStyle, ! this.IsBody, false, cix == 0, cix == (this.Width - 1), false, true);
                }
            }
        }
        
        var extra = null;
        var cmd = edit ? (this.WindowsSelectionstyle ? 'windowsedit' : 'edit') : 'text';
        if (this.ColMgr[cix]) {
            cmd = this.ColMgr[cix].cmd;
            extra = this.ColMgr[cix].extra;
        }
        
        name = "." + vals[cix].getAttribute('name');
        var colmgr = new Qva.ColMgr[cmd] (this, cix, cell, name, this.PageName, extra);
        
        colmgr.Cmd = cmd;
        
        if (this.Group == null) this.PageBinder.Append (this, colmgr.Name, null, true);
        this.ColDict [colmgr.Name] = colmgr;
        this.ColList [this.ColList.length] = colmgr;
    }
    
    bodyParent.replaceChild (body, this.Body);
    this.Body = body;
    this.RowNumbers = [];

    if (this.AllwaysFullWidth) {
        var element = this.Element;
        var scrollParent = element.parentNode;
        var objectframeNode = scrollParent.parentNode;
        if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
            this.Element.style.width = "100%";
            var numberofchildren = objectframeNode.childNodes.length;
            for (var ichild = 0; ichild < numberofchildren; ichild++) {
                var child = objectframeNode.childNodes [ichild];
                if (child.nodeName != "DIV") continue;
                child.style.width = "auto";
            }
        } else {
            this.Element.style.width = "auto";
        }
    } else {
        if (tabletotwidth != 0) {
            this.Element.style.width = tabletotwidth + "pt";
        } else {
            debugger;
        }
    }
}

Qva.Mgr.table.prototype.AppendIfMissing = function(list, entry) {
    var len = list.length;
    for (var six = 0; six < len; ++six) {
        if (list [six] == entry) return;
    }
    list [len] = entry;
}

Qva.Mgr.table.prototype.CreateTransientListBox = function (name) {

    var frame = document.createElement ("div");
    frame.style.cssText = "z-index: 100; display: none; left: 10pt; top: 10pt; width: 10pt; height: 800pt; position:absolute;";
    frame.className = "Frame";

    var body = document.createElement("div");
    body.style.cssText = "overflow: auto; width: auto; height: 100%;";

    var table = document.createElement ("table");
    table.style.cssText = "background-color: White;";

    if (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME) {
        table.style.cssText += "width: 100%;";
    } else {
        table.style.cssText += "width: auto;";
    }
    table.setAttribute ("avqasync", "40:" + name);
    table.id = "DS";

    body.appendChild (table);
    frame.appendChild (body);
    document.body.appendChild (frame);

    new Qva.Mgr.label (this.PageBinder, frame, name);
    new Qva.Mgr.table (this.PageBinder, table);
}

 
Qva.Mgr.table.prototype.Paint = function (mode, node, name, partial) {
    
    var transientname = this.PageBinder.DefaultScope + ".DS";
    if (Qva.LabelClick && ! this.PageBinder.Members [transientname]) {
        this.CreateTransientListBox (transientname);
    }
    this.Touched = true;
    if (node.getAttribute ("menu") == "visible") return;
    
    var NodeWithAttributes = (this.Name == name || this.PageName == name) ? node : node.parentNode;
    this.WindowsSelectionstyle = NodeWithAttributes.getAttribute ("windowsselectionstyle") == "true";
    var owner = node.getAttribute ('owner');
    if (owner) {
        var row = parseInt (node.getAttribute ('row'));
        var col = parseInt (node.getAttribute ('col'));
        if ((this.Owner && this.Owner != owner) || 
            (this.OwnerRow && row != this.OwnerRow) || 
            (this.OwnerCol && row != this.OwnerCol)) 
        {
            while (this.Body.rows.length > 0) {
                this.Body.deleteRow (this.Body.rows.length - 1);
            }
            this.RowNumbers.length = 0;
            this.ByValue = null;
        }
        this.Owner = owner;
        this.OwnerRow = row;
        this.OwnerCol = col;
        this.IsTransient = true;
        this.PageBinder.TransientObject = this.PageName;
        // Dropdownselect and multiboxes
        var objectframeNode = this.Element.parentNode.parentNode;
        var ownerelement = document.getElementById (this.Owner);
        if (! ownerelement) return;
        if (ownerelement.nodeName == "TABLE") { 
            if (ownerelement.rows.length > this.OwnerRow) {
                var cell = ownerelement.rows[this.OwnerRow].cells [this.OwnerCol];
                if (cell) {
                    var left = 3;
                    var width = cell.offsetWidth;
                    if (cell.offsetWidth > 0) {
                        if (cell.offsetWidth < 20) {
                            // Difference between multiboxes and otheer tables
                            cell = ownerelement.rows[this.OwnerRow].cells [this.OwnerCol + 1];
                        } else {
                            var righticon = false;
                            for (var ichild = 0; ichild < cell.childNodes.length; ichild++) {
                                var child = cell.childNodes [ichild];
                                if (child.action == "CDDC" || child.action == "ODDC") {
                                    if (child.offsetLeft - cell.offsetLeft > 30) {
                                        righticon = true;
                                        width = child.offsetLeft - cell.offsetLeft;
                                        left -= 3;
                                    } else {
                                        left += child.offsetWidth;
                                    }
                                    break;
                                }
                            }
                            left += 3;
                        }
                        left += cell.offsetLeft;
                        var top = cell.offsetTop;
                        var offsetparent = cell.offsetParent;
                        var height = 0;
                        while (offsetparent) {
                            left += offsetparent.offsetLeft;
                            top += offsetparent.offsetTop;
                            height = offsetparent.offsetHeight;
                            offsetparent = offsetparent.offsetParent;
                        }
                        left -= (ownerelement.parentNode.scrollLeft - Qva.GetScrollLeft ());
                        height -= (top - Qva.GetScrollTop ());
                        // Take tabs into consideration
                        if (height > 80) height -= 40;
                        top++;
                        objectframeNode.style.left = left + "px";
                        objectframeNode.style.top = top + "px";
                        if (height > 0) objectframeNode.style.height = height + "px";
                        if (! righticon) {
                            width = cell.offsetWidth;
                        }
                        if (parseInt (objectframeNode.style.width) != width) {
                            objectframeNode.style.width = width + "px";
                            if (! (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME)) {
                                var scrollParent = this.Element.parentNode;
                                if (objectframeNode.maxwidth) {
                                    objectframeNode.removeAttribute ("maxwidth");
                                }
                                setContentWidth (scrollParent, this.Element, width, false);
                            }
                        }
                    }
                }
            }
        }
    }
    var stylenodes = node.getElementsByTagName ('style');
    if (stylenodes.length == 0) stylenodes = node.parentNode.getElementsByTagName ('style');
    if (stylenodes.length > 0) {
        stylenodes = stylenodes[0].getElementsByTagName ('style');
        for (var istyle = 0; istyle < stylenodes.length; istyle++) {
            this.Style [istyle] = new this.StyleObject (stylenodes [istyle]);
        }
    }
    stylenodes = node.getElementsByTagName ('borderstyle');
    if (stylenodes.length == 0) stylenodes = node.parentNode.getElementsByTagName ('borderstyle');
    if (stylenodes.length > 0) {
        stylenodes = stylenodes[0].getElementsByTagName ('borderstyle');
        for (istyle = 0; istyle < stylenodes.length; istyle++) {
            this.BorderStyle [istyle] = new this.BorderStyleObject (stylenodes [istyle]);
        }
    }
    this.SizeTodata = NodeWithAttributes.getAttribute ("sizetodata") == "true";
    if(this.PageName == name && this.AutoCol && mode != 'h') {
        this.FixCol(mode, node, name, partial);
    }
    
    this.FinalFix = false;
    this.Searchable = NodeWithAttributes.getAttribute ("searchable") == "true";
    if (this.IsHeader) {
        var rowspan = parseInt (NodeWithAttributes.getAttribute ("headerrowspan"));
        if (! isNaN (rowspan)) {
            this.RowSpan = rowspan;
        }
    } else if (this.IsBody) {
        var rowspan = parseInt (NodeWithAttributes.getAttribute ("rowspan"));
        if (! isNaN (rowspan)) {
            this.RowSpan = rowspan;
        }
    }

    var element = this.Element;
    try {
        var newmode = Qva.MgrGetDisplayFromMode (this, mode);
        var colmgr = this.ColDict[name];
        if (colmgr == null) {
            if (newmode != element.style.display) {
                element.style.display = newmode;
            }
        } else if (newmode == 'none') {
            if (colmgr.Index != 0) {
                for (var rix = 0; rix < this.Lines.length && this.Lines [rix]; ++rix) {
                    this.Lines [rix][colmgr.Index] = null;
                }
            }
            return;
        }
    } catch (e) {
        debugger
    }
    element.disabled = (mode != 'e');
    if (this.InlineStyle) setStyle (node, element);
    this.Dirty = true;
    var sortable = node.parentNode.getAttribute ('issortable');
    if (sortable && sortable == 'true') this.sortable = true;
    if (node.getAttribute ("menu") != null) { 
        this.Menu = true;
    }
    if (this.Group == name) {
        var entries = node.getElementsByTagName ('element');
        var height = entries.length;
        if (height > this.TableLimit) {
            TableTruncateAlert (this.Name, height);
            height = this.TableLimit;
        }
        var rowsskipped = 0;
        for (rix = 0; rix < height; ++ rix) {
            var entry = entries [rix];
            if (this.ByValue == null) {
                this.ByValue = (entry.getAttribute("value") != null);
            }
            var position = entry.getAttribute ('position');
            if (this.IsHeader) {
                if (! position == 'top') continue;
                if (! position) break;
            }
            if (this.IsBody) {
                if (position) {
                    rowsskipped++
                    continue;
                }
            }
            var actualrow = rix - rowsskipped;
            if (actualrow >= this.Lines.length) {
                this.Lines [actualrow] = new Array ();
            }
            var header = entry.getAttribute ('isheader');
            if (header && header == 'true') this.Lines [actualrow].IsHeader = true;
            this.IsPainted [actualrow] = false;
            var values = entry.getElementsByTagName ('value');
            var width = values.length;
            for (var cix = 0; cix < width; ++ cix) {
                var value = values [cix];
                name = value.getAttribute ("name");
                colmgr = this.ColDict[name];
                if (colmgr == null) continue;
                var optval = value.getAttribute ("text");
                if (colmgr.Dec != null) optval = Qva.Trunc (optval, colmgr.Dec);
                this.Lines [actualrow][colmgr.Index] = new this.CellObject (optval, value);
            }
        }
        this.Lines.length = height;
        this.TotalSize = height;

		if (element.id == "" || (! this.HeaderId && this.IsBody)) {
            var objectname = name;
            objectname = objectname.substring (1, objectname.lastIndexOf ("."));
            objectname = objectname.substr (objectname.indexOf (".") + 1);
		    if (element.id == "" && (rowsskipped == 0 || this.IsHeader)) {
                element.id = objectname;
            }
            if (! this.HeaderId && this.IsBody && this.IsAsync) {
                this.HeaderId = objectname;
            } 
        }
        return;
    }
    this.AndMode = NodeWithAttributes.getAttribute ("andmode") == "true";
    this.SupressHorizontalScroll = NodeWithAttributes.getAttribute ("suppresshorizontalscroll") == "true";
    if (this.IsBody) {
        if (this.SupressHorizontalScroll && this.IsBody) {
            element.parentNode.style.overflowX = "hidden";
        } else {
            element.parentNode.style.overflowX = "auto";
        }
    }
    var prevTotSize = this.TotalSize;
    if (this.PageHandler == 'server') {
        if (this.Name == name || colmgr.Index == 0) this.Lines.length = 0;
        this.PageOffset = parseInt (NodeWithAttributes.getAttribute ("pageoffset"));
        this.PageSize = parseInt (NodeWithAttributes.getAttribute ("pagesize"));
        this.TotalSize = parseInt (NodeWithAttributes.getAttribute ("totalsize"));
    } else if (this.PageName == "" || this.PageName == name) {
        this.ChunkOffset = parseInt (NodeWithAttributes.getAttribute ("pageoffset"));
        this.ChunkSize = parseInt (NodeWithAttributes.getAttribute ("pagesize"));
        this.TotalSize = parseInt (NodeWithAttributes.getAttribute ("totalsize"));
        if (this.TotalSize < 500) {
            this.EffectiveSize = this.TotalSize;
        } else if (this.ChunkOffset + this.ChunkSize >= 500) {
            this.EffectiveSize = this.ChunkOffset + 2 * this.ChunkSize;
            if (this.EffectiveSize > this.TotalSize) this.EffectiveSize = this.TotalSize;
        } else {
            this.EffectiveSize = 500;
        }
        if (this.IsAsync) {
            if (! partial) {
                this.Lines.length = 0;
                for (var ix = 0; ix < this.IsPainted.length; ix ++) {
                    this.IsPainted [ix] = false;
                }
                if (this.ChunkOffset == 0) {
                    var scrollParent = element.parentNode;
                    scrollParent.scrollTop = 0;
                }
            }
            if (this.TotalSize == 0) return;
        }
    }
    this.Fixed = false;
    if (this.Name == name) {
        this.Current = node.getAttribute ("text");
        if (this.Current != null) return;
        
        entries = node.getElementsByTagName ('choice');
        var checkSelected = entries.length >= 1;
        if (checkSelected) {
            entries = entries[0].getElementsByTagName ('element');
        } else {
            entries = node.getElementsByTagName ('element');
        }
        height = entries.length;

        if (this.Disabled == null) this.Disabled = new Array ();
        if (this.Locked == null) this.Locked = new Array ();

        if (this.IsAsync && partial) {
            for (rix = 0; rix < height; ++rix) {
                entry = entries [rix].getAttribute("text");
                if (!checkSelected || entries [rix].getAttribute("selected") == "yes") {
                    this.AppendIfMissing(this.Selected, entry);
                }
                if (checkSelected) {
                    if (entries [rix].getAttribute("mode") == "disabled") {
                        this.AppendIfMissing(this.Disabled, entry);
                    }
                    if (entries [rix].getAttribute("locked") == "yes") {
                        this.AppendIfMissing(this.Locked, entry);
                    }
                }
            }
        } else {
            var selix = 0;
            var disix = 0;
            var lckix = 0;
            for (rix = 0; rix < height; ++rix) {
                entry = entries [rix].getAttribute("text");
                if (!checkSelected || entries [rix].getAttribute("selected") == "yes") {
                    this.Selected [selix++] = entry;
                }
                if (checkSelected) {
                    if (entries [rix].getAttribute("mode") == "disabled") {
                        this.Disabled [disix++] = entry;
                    }
                    if (entries [rix].getAttribute("locked") == "yes") {
                        this.Locked [lckix++] = entry;
                    }
                }
            }
            this.Selected.length = selix;
            this.Disabled.length = disix;
            this.Locked.length = lckix;
        }
        {
            if (this.AllTexts == null) this.AllTexts = new Array ();
            entries = node.getElementsByTagName ("choice");
            if (entries.length >= 1) entries = entries[0].getElementsByTagName ("element");
            height = entries.length;
            if (this.IsAsync) {
                if (! partial) {
                    this.AllTexts.length = this.EffectiveSize;
                }
                var ChunkOffset = this.ChunkOffset;
                for (rix = 0; rix < height; ++rix) {
                    if (this.ByValue == null) {
                        this.ByValue = (entries [rix].getAttribute("value") != null);
                    }
                    this.AllTexts [rix + ChunkOffset] = entries [rix].getAttribute("text");
                }
            } else {
                this.AllTexts.length = height;
                for (var rix = 0; rix < height; ++rix) {
                    if (this.ByValue == null) {
                        this.ByValue = (entries [rix].getAttribute("value") != null);
                    }
                    this.AllTexts [rix] = entries [rix].getAttribute("text");
                }
            }
        }
        if (this.ByValue == true) {
            if (this.AllValues == null) this.AllValues = new Array ();
            var entries = node.getElementsByTagName ("choice");
            if (entries.length >= 1) entries = entries[0].getElementsByTagName ("element");
            var height = entries.length;
            if (this.IsAsync) {
                if (! partial) {
                    this.AllValues.length = this.EffectiveSize;
                }
                var ChunkOffset = this.ChunkOffset;
                for (var rix = 0; rix < height; ++rix) {
                    this.AllValues [rix + ChunkOffset] = entries [rix].getAttribute("value");
                }
            } else {
                this.AllValues.length = height;
                for (var rix = 0; rix < height; ++rix) {
                    this.AllValues [rix] = entries [rix].getAttribute("value");
                }
            }
        }
        if (element.id == "" && this.Search == null) {
            var objectname = name;
            objectname = objectname.substr (objectname.indexOf (".") + 1);
            element.id = objectname;
        } 
    }
    if (this.Count == name) {
        if (this.Counts == null) this.Counts = new Array ();
        var entries = node.getElementsByTagName ('element');
        var height = entries.length;
        this.Counts.length = height;
        for (var rix = 0; rix < height; ++rix) {
            this.Counts [rix] = entries [rix].getAttribute("text");
        }
        if (this.Name != name) return;
    }
    if (this.Name == "" && this.PageName == name) {
        // navigation has been updated, that is enough
        return;
    }
    var colmgr = this.ColDict[name];
    if (colmgr == null) {
        alert ('PaintTable unknown name:' + name);
        return;
    }
    var xIx = (this.Name == name && colmgr.Index != 0) ? 0 : -1;
    var entries;
    if(this.Name != name) {
        entries = node.getElementsByTagName('element');
    } else {
        entries = node.getElementsByTagName ("choice");
        if (entries.length >= 1) entries = entries[0].getElementsByTagName ("element");
    }
    var height = entries.length;
    if (height > this.TableLimit) {
        TableTruncateAlert (this.Name, height);
        height = this.TableLimit;
    }
    var rowsskipped = 0;
    var full_rix = 0;
    for (var rix = 0; rix < height; ++rix) {
        var entry = entries [rix];
        var optval = entry.getAttribute("text");
        var position = entry.getAttribute ('position');
        if (this.IsHeader) {
            if (! position == 'top') continue;
            if (! position) break;
        }
        if (this.IsBody) {
            if (position) {
                rowsskipped++
                continue;
            }
        }
        full_rix = rix - rowsskipped;
        if (this.IsAsync) {
            full_rix += this.ChunkOffset;
        }
        if (this.Lines [full_rix] == null) this.Lines [full_rix] = new Array ();
        var header = entry.getAttribute ('isheader');
        if (header && header == 'true') this.Lines [full_rix].IsHeader = true;
        this.IsPainted [full_rix] = false;
        this.Lines [full_rix][colmgr.Index] = new this.CellObject (optval, entry);
        if (xIx >= 0) this.Lines [full_rix][xIx] = new this.CellObject (optval, entry);       
    }
    if (element.id == "" || (! this.HeaderId && this.IsBody)) {
        var parts = name.split ('.');
        var objectname = parts [1] == "Fields" ? parts [2] : parts [1];
        if (element.id == "" && (rowsskipped == 0 || this.IsHeader)) {
            element.id = objectname;
        }
        if (! this.HeaderId && this.IsBody && this.IsAsync) {
            this.HeaderId = objectname;
        } 
    }
    if (! this.IsAsync && height > 0) {
        this.Lines.length = full_rix + 1;
    }
    if (height == 0) {
        this.Lines.length = 0;
    }
}

Qva.Mgr.table.prototype.StyleObject = function (node) {
    this.BgColor = node.getAttribute ('bgcolor');
    this.Color = node.getAttribute ('color');
    this.NumAdjust = node.getAttribute ('numadjust');
    this.TextAdjust = node.getAttribute ('textadjust');
    this.BorderStyle = node.getAttribute ('borderstyle');
    this.FontMod = node.getAttribute ('fontmod');
    this.SizeMod = node.getAttribute ('sizemod');
}

Qva.Mgr.table.prototype.BorderStyleObject = function (node) {
    this.Top = node.getAttribute ('top');
    this.Bottom = node.getAttribute ('bottom');
    this.Left = node.getAttribute ('left');
    this.Right = node.getAttribute ('right');
}

Qva.Mgr.table.prototype.GetIndex = function (line) {
    if (this.ChoiceIx < 0) return 0;
    var val = line [this.ChoiceIx].val;
    if (this.Current != null) {
        return (this.Current === val) ? 1 : 0;
    }
    for (var ix = 0; ix < this.Selected.length; ++ix) {
        if (this.Selected [ix] === val) {
            if (this.Counts == null) return 1;
            if (this.Counts [ix] == '') return 1;
            var res = parseInt (this.Counts [ix]);
            if (isNaN(res)) alert ('NaN at ' + ix + '/' + this.Counts.length + ': ' + this.Counts [ix]);
            return res;
        }
    }
    return 0;
}

Qva.Mgr.table.prototype.GetSelected = function (rix, cix) {
    var line = this.Lines [rix];
    if (cix != null) {
        return line [cix].selected;
    } else { 
        return this.GetIndex (this.Lines [rix]);
    }
}

Qva.Mgr.table.prototype.GetDisabled = function (rix, cix) {
    var line = this.Lines [rix];
    return line [cix].disabled;
}

Qva.Mgr.table.prototype.GetLocked = function (rix) {
    if (this.Locked == null) return null;
    var line = this.Lines [rix];
    var val = line [this.ChoiceIx].val;
    for (var ix = 0; ix < this.Locked.length; ++ix) {
        if (this.Locked [ix] === val) return 1;
    }
    return 0;
}

function AvqAction_TableCheck () {
    var box = this;
    var row = box.parentNode.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (! mgr.PageBinder.Enabled) return;
    var selIx = row.rix;
    var SearchActive = (mgr.Search != null && mgr.Search.value != '');
    var valName = (mgr.Count != null) ? mgr.Count : mgr.Name;
    if (mgr.ByValue && mgr.PageBinder.Autoview != null) {
        // This code is not bw compat, check against AvqAutoView for "QlikView-only" activation
        // There is no other logical connection with AvqAutoView
        var ixVal = mgr.AllValues [selIx];
        mgr.PageBinder.Set (valName, 'count', (box.checked ? ' ' : '-') + ixVal, ! SearchActive);
    } else if (mgr.ChoiceIx != -1)  {
        var ixName = mgr.Lines [selIx][mgr.ChoiceIx].val;
        mgr.PageBinder.Set (valName, 'count', (box.checked ? ' ' : '-') + ixName, ! SearchActive);
    } else {
        var colname = mgr.ColList[box.parentNode.cellIndex].Name;
        mgr.PageBinder.Set (mgr.Group, 'cell', selIx + ':' + colname + ':' + (box.checked ? '1' : '0'), ! SearchActive);
    }
    if (SearchActive) {
        mgr.PageBinder.Set (mgr.SearchName, "closesearch", "abort", true);      // break out of search mode
        if (mgr.Search != null) mgr.Search.value = '';				// Allow for popup search being closed
    }
}

function AvqAction_TableInput () {
    var box = this;
    var row = box.parentNode.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (! mgr.PageBinder.Enabled) return;
    var selIx = row.rix;
    var colname = mgr.ColList[box.parentNode.cellIndex].Name;
    mgr.PageBinder.Set (mgr.Group, 'cell', selIx + ':' + colname + ':' + box.value, true);
}

function appendCellIcon (icon, targetname, cix, rix, avqmgr) {
    var img = document.createElement ("img");
    img.alt = "";
    var style = 'cursor: pointer;';
    var iconstyle = icon.getAttribute ("iconstyle");
    if (iconstyle != null) {
        style += ' left:0px; top:0px; ' + iconstyle;   
    } else {
        style += ' position:relative; float:right; right:0px; top:3px; height:10px; width:10px;';   
    }
    img.style.cssText = style;
    
    var url = icon.getAttribute ("url");
    if (url) {
        img.src = url;
    } else {
        var stamp = icon.getAttribute ("stamp");
        var url = avqmgr.PageBinder.BuildBinaryUrl (icon.getAttribute ("path"), stamp);
        url += (avqmgr.PageBinder.IsHosted ? "" : '&name=' + stamp);
        img.src = url;
        var action = icon.getAttribute ("action");
        var clientaction = icon.getAttribute ("clientaction");
        if (action || clientaction) {
            var binder = avqmgr.PageBinder;
            img.binderid = avqmgr.PageBinder.ID;
            if (icon.getAttribute ("action")) {
                img.onmousedown = avq_iconaction_md;
                img.onmouseup = avq_iconaction_mu;
                img.pressed = false;
                img.action = icon.getAttribute ("action");
            } else if (icon.getAttribute ("clientaction")) {
                img.onmousedown = Qva.CancelAction;
                img.onmouseup = Qva.CancelAction;
                img.onclick = onclick_ContextClientAction;
                img.AvqMgr = avqmgr;
                img.clientaction = icon.getAttribute ("clientaction");
                img.param = icon.getAttribute ("param");
            }
            img.targetname = targetname;
            img.xx = cix;
            img.yy = rix;
        }
    }
    return img;
}

function appendCellContent (cell, icons, text, targetname, cix, rix, avqmgr) {
    cell.innerHTML = "";
    for (var iicons = 0; iicons < icons.length; iicons++) {
        var icon = icons [iicons];
        var iconelem = appendCellIcon (icon, targetname, cix, rix, avqmgr);
        cell.appendChild (iconelem);
    }
    var textnode = document.createTextNode (text);
    cell.appendChild (textnode);
}

Qva.Mgr.table.prototype.GetScrollWidth = function () {
    if (IS_MAC) {
        return 15;
    } else {
        return 17;
    }
}

Qva.Mgr.table.prototype.GetScrollHeight = function () {
    if (IS_MAC) {
        return 15;
    } else {
        return 17;
    }
}

Qva.Mgr.table.prototype.HasHorizontalScrollbar = function () {
    var objectframeNode = this.Element.parentNode.parentNode;
    return getClientWidth (objectframeNode) < this.Element.offsetWidth && ! this.SupressHorizontalScroll;
}

Qva.Mgr.table.prototype.HasVerticalScrollbar = function (maxheight, scrollheight) {
    var numberofrows = isNaN (this.TotalSize) ? this.Lines.length : this.TotalSize;
    var calculatedscrollheight = this.RowHeight * numberofrows * this.RowSpan;
    var HasVerticalScrollbar = (maxheight - scrollheight) < calculatedscrollheight;
    HasVerticalScrollbar = HasVerticalScrollbar || this.Element.offsetHeight > (maxheight - scrollheight);
    return HasVerticalScrollbar;
}

Qva.Mgr.table.prototype.FixTableWidth = function (header) {
    if (this.Element.offsetWidth < 10) debugger;
    var scrollParent = this.Element.parentNode;
    var objectframeNode = scrollParent.parentNode;
    if ( ! this.UnmodifiedTableWidth) {
        this.UnmodifiedTableWidth = this.Element.offsetWidth;
    }    
    var scrollheight = 0;
    if (this.HasHorizontalScrollbar ()) {
        scrollheight = this.GetScrollHeight ();
    }
    var HasVerticalScrollbar = this.HasVerticalScrollbar (parseInt (getContentMaxHeight (this.Element)), scrollheight);
    var scrollwidth = 0;
    if (HasVerticalScrollbar) {
        scrollwidth = this.GetScrollWidth ();
    }
    var maxwidth = getClientWidth (objectframeNode);
    var deltaparent = scrollParent.offsetWidth - getClientWidth (scrollParent);
    var delta = maxwidth - deltaparent - this.UnmodifiedTableWidth;
    var modifylastcolumn = this.SupressHorizontalScroll && delta < 0;
    if (HasVerticalScrollbar) {
        modifylastcolumn = modifylastcolumn || (delta >= 0 && delta < 18);
    }

    var lastheadercells = new Array ();
    if (header) {
        for (var i = 0; i < header.rows.length; i ++) {
            lastheadercells [i] = header.rows [i].cells [this.Width - 1];
        }
    }
    var newscrollwidth = maxwidth;
    var newheaderwidth = newscrollwidth;
    var lastheadercellfixed = false;
    var cell = this.Body.rows[0].cells [this.Width - 1];
    if (modifylastcolumn) {
        cell.style.width = "";
        for (var i = 0; i < lastheadercells.length; i ++) {
            lastheadercells [i].style.width = "";
        }
        if (IS_GECKO && ! this.SizeTodata) {
            //Ugly fix for disappearing right borders
            newscrollwidth--
        }
        if (this.SupressHorizontalScroll) {
            newheaderwidth = newscrollwidth;
            newheaderwidth -= deltaparent;
            var newwidth = newheaderwidth - scrollwidth;
            if (parseInt (this.Element.style.width) != newwidth) {
                this.Element.style.width = newwidth + "px";
            }
        } else {
            debugger;
        }
    } else {
        cell.style.width = this.LastColWidth;
        if (this.m_HasVerticalScrollbar != HasVerticalScrollbar) {
            this.Element.style.width = this.UnmodifiedTableWidth + "px";
        }
        if (! HasVerticalScrollbar) {
            for (var i = 0; i < lastheadercells.length; i ++) {
                lastheadercells [i].style.width = this.LastColWidth;
            }
        }
        if (! this.SizeTodata && this.Element.offsetWidth < newscrollwidth) {
            newheaderwidth = this.Element.offsetWidth;
            if ((IS_CHROME || IS_GECKO || IS_SAFARI) && lastheadercells.length > 0) {
                if (parseInt (lastheadercells [0].style.width) != parseInt (this.LastColWidth)) {
                    for (var i = 0; i < lastheadercells.length; i ++) {
                        lastheadercells [i].style.width = this.LastColWidth;
                    }
                }
                lastheadercellfixed = true;
            }
            scrollwidth = 0;
        } else if (this.SupressHorizontalScroll) {
            newscrollwidth = Math.max (this.Element.offsetWidth, 1);
            newscrollwidth += scrollwidth;
            newheaderwidth = newscrollwidth;
            if (! (IS_CHROME || IS_SAFARI)) {
                newheaderwidth -= deltaparent;
            }
            newscrollwidth += deltaparent;
        } else {
            if (this.Element.offsetWidth < newscrollwidth) {
                if (this.SizeTodata) {
                    newscrollwidth = this.Element.offsetWidth;
                    newscrollwidth += scrollwidth;
                    newheaderwidth = newscrollwidth;
                } else {
                    debugger;
                }
                if (! HasVerticalScrollbar) newscrollwidth += deltaparent;
            } else {
                // Horizontal scrollbar
                newheaderwidth = this.UnmodifiedTableWidth;
                if (scrollwidth != 0) {
                    if (IS_CHROME || IS_SAFARI) {
                       newheaderwidth += scrollwidth;
                    } else {
                        scrollwidth++
                    }
                }
                if (! (IS_CHROME || IS_SAFARI)) {
                    newheaderwidth -= deltaparent;
                }
            }
        }
    }
    
    if (header) {
        if (parseInt (header.style.width) != newheaderwidth) {
            header.style.width = newheaderwidth + "px";
        }
        var bodycell = this.Body.rows [0] ? this.Body.rows [0].cells [this.Width - 1] : null;
        if (lastheadercells.length > 0 && bodycell) {
            if (IS_GECKO || IS_SAFARI || IS_CHROME) {
                if (HasVerticalScrollbar && ! (this.SupressHorizontalScroll && modifylastcolumn) && ! lastheadercellfixed) {
                    var cellwidth;
                    if (IS_CHROME || IS_SAFARI) {
                        cellwidth = getClientWidth (bodycell);
                        cellwidth += scrollwidth;
                        cellwidth++;
                    } else {
                        cellwidth = bodycell.offsetWidth;
                    }
                    if (parseInt (lastheadercells [0].style.width) != cellwidth) {
                        for (var i = 0; i < lastheadercells.length; i ++) {
                            lastheadercells [i].style.width = cellwidth + "px";
                        }
                    }
                }
            }
            var rightpadding = scrollwidth != 0 ? scrollwidth + "px" : "";
            if (parseInt (lastheadercells [0].style.paddingRight) != parseInt (rightpadding)) {
                for (var i = 0; i < lastheadercells.length; i ++) {
                    lastheadercells [i].style.paddingRight = scrollwidth != 0 ? scrollwidth + "px" : "";
                }
            }
        }
    }
    var resetwidth = ! this.m_HasVerticalScrollbar && HasVerticalScrollbar;
    setContentWidth (scrollParent, this.Element, newscrollwidth, resetwidth);
    var resetpaint = resetwidth && this.m_HasVerticalScrollbar != null;
    this.m_HasVerticalScrollbar = HasVerticalScrollbar;
    return resetpaint;
}

Qva.Mgr.table.prototype.FixTableHeight = function () {
    var scrollParent = this.Element.parentNode;
    if (! scrollParent.style) return;
    if (scrollParent.clientHeight == 0) return;
    var scrollheight = 0;
    if (this.HasHorizontalScrollbar ()) {
        scrollheight = this.GetScrollHeight ();
    }
    var maxheight = parseInt (getContentMaxHeight (this.Element));
    var HasVerticalScrollbar = this.HasVerticalScrollbar (maxheight, scrollheight);
    var newheight;
    if (this.Element.offsetHeight > 10 && ! HasVerticalScrollbar && this.SizeTodata) {
        newheight = Math.max (this.Element.offsetHeight, 1);
        newheight += scrollheight;
    } else {
        newheight = maxheight;
        if (IS_GECKO) {
            newheight--;
        }
    }
    if (parseInt (scrollParent.style.height) != newheight || scrollParent.style.height.search ("pt") != -1) {
        scrollParent.style.height = Math.max (newheight, 1) + "px";
    }
    return this.m_HasVerticalScrollbar != null && this.m_HasVerticalScrollbar != HasVerticalScrollbar;
}

Qva.Mgr.table.prototype.PostPaint = function () {
    if (this.Lines == null) return;
    var WantedChunkNumber = null;
    var scrollParent = this.Element.parentNode;
    if (scrollParent.style && scrollParent.style.display == 'none') return;

    var postpaintposted = false;

    if (this.RowHeight == -1 && this.Body.rows [0] && this.Body.rows [0].cells [0]) {
        if (this.Body.rows [0].cells [0].offsetHeight > 0) {
            this.RowHeight = getClientHeight (this.Body.rows [0].cells [0]);
        }
    }
    if (! this.FinalFix) {
        var objectframeNode = scrollParent.parentNode;
        var frame = Qva.GetFrame (this.Element);
        if (frame && frame.AvqMgr.Dirty) {
            Qva.QueuePostPaintMessage (this);
            return;
        }
        if (frame && frame.style && frame.style.display == 'none') return;
        var header;
        if (this.HeaderId != null) {
            header = document.getElementById (this.HeaderId);
            if (header != this.Element && header.parentNode.style.display != "none") {
                if (! header.AvqMgr.Fixed) {
                    Qva.QueuePostPaintMessage (this);
                    return;
                }
            } else {
                header = null;
            }
        }
        if (frame && this.InlineStyle && ! this.IsTransient && ! (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME)) {
            // In IE "medium" means that the element is not yet attached to the DOM
            if (scrollParent.currentStyle.borderLeftWidth == "medium") {
                Qva.QueuePostPaintMessage (this);
                return;
            }
        }
        if (this.AllwaysFullWidth) {
            if (! (IS_GECKO || IS_SAFARI || IS_OPERA || IS_CHROME)) {
                setContentWidth (scrollParent, this.element, getClientWidth (objectframeNode), false);
            }
        } else if (this.IsBody && this.LastColWidth) {
            if (objectframeNode.style && objectframeNode.style.display == 'none') return;
            if (this.FixTableWidth (header)) {
                Qva.QueuePostPaintMessage (this);
                return;
            }
        }

        var height;
        if (this.PageHandler == 'client' && ! this.IsHeader) {
            if (this.TotalSize == 0) {
                this.PageOffset = 0;
                height = 0;
            } else if (this.IsAsync) {
                height = this.EffectiveSize;
            } else {
                height = this.Lines.length - this.PageOffset;
                while (height <= 0 && this.Lines.length > 0) {
                    this.PageOffset -= this.PageSize;
                    height = this.Lines.length - this.PageOffset;
                }
            }
        } else {
            height = this.Lines.length;
        }
        if (this.PageIncr == 0 && this.PageSize > 0 && height > this.PageSize) height = this.PageSize;
        var rowlen = this.RowNumbers.length;
        if (rowlen == 0) {
            rowlen = this.Body.rows.length;
            if (rowlen > 0) {
                if (rowlen != 1) debugger;
                if (this.Body.rows [0].rix != 0) debugger;
            } 
        }
        if (height != rowlen) {
            if (this.IsAsync && height < rowlen && rowlen <= this.TotalSize) {
                // special case: new table data is shorter for Mozilla
                this.EffectiveSize = rowlen;
                height = this.EffectiveSize;
            } else {
                this.Inflate (height, rowlen);	// make sure table is right size
            }
        }
        var newscrollheight;
        if (this.InlineStyle && this.IsBody) {
            newscrollheight = getContentMaxHeight (this.Element);
        } else {
            newscrollheight = scrollParent.offsetHeight;
        }
        var rix_start = 0;
        var rix_stop = height;
        if (this.PageIncr > 0 && this.IsAsync) {
            if (this.TotalSize < this.PageSize) {
                rix_start = 0;
                rix_stop = this.TotalSize;
            } else {
                var scrollpos = scrollParent.scrollTop;
                rix_start = this.RowForPos (this.Body.rows, scrollpos);
                if (this.ChunkOffset == 0 && scrollpos == 0 && rix_start != 0) {
                    rix_start = 0;
                    rix_stop = this.PageSize;
                } else {
                    rix_stop = this.RowForPos (this.Body.rows, scrollpos + newscrollheight) + 1;
                    var visibleScrollRowsDiv2 = Math.ceil ((rix_stop - rix_start) / 2);
                    rix_start -= visibleScrollRowsDiv2;
                    rix_stop += visibleScrollRowsDiv2;
                }
                if (rix_start < 0) rix_start = 0;
                if (rix_stop > height) rix_stop = height;
                if (rix_stop > this.TotalSize) rix_stop = this.TotalSize;
            }
        }

        var rows = this.Body.rows;
        var PaintedLines = 0;
        var LastRowAdjusted = false;
        var headerheight = 0;
        if (header) {
            headerheight = header.rows.length;
        }
        for (var rix = rix_start; rix < rix_stop; ++ rix) {
            if (this.IsPainted [rix]) continue;
            var lix = rix + this.PageOffsetForPainting ();
            var line = this.Lines [lix];
            var rowNumber = this.RowNumbers [rix];
            if (rowNumber == null) {
                var row = null;
                if (rix == 0) {
                    row = this.Body.rows [0];
                } else {
                    row = this.Body.rows [0].cloneNode (true);
                    row.style.position = "absolute";
                    row.style.left = this.Body.rows [0].offsetLeft + "px";
                    row.style.top = this.Body.rows [0].offsetHeight * rix + "px";
                    row.rix = rix;
                    this.Body.appendChild (row);
                }
                rowNumber = this.Body.rows.length - 1;
                this.RowNumbers [rix] = rowNumber;
                
                if (! LastRowAdjusted) { // Adjust last row position if necessary
                    LastRowAdjusted = true;
                    var wantedLastRowTop = this.Body.rows [0].offsetHeight * (height - 1);
                    var lastRow = this.Body.rows [this.RowNumbers [height - 1]];
                    if (lastRow.style == null || lastRow.style.top != wantedLastRowTop) {
                        lastRow.style.top = wantedLastRowTop + "px";
                    }
                }
            }
            var row = this.Body.rows [this.RowNumbers [rix]];
            if (line == null) {
                if (WantedChunkNumber == null) {
                    WantedChunkNumber = Math.floor (lix / this.ChunkSize);
                }
                if (row != null) {
                    for (var cix = 0; cix < row.cells.length; ++ cix) {
                        row.cells [cix].innerText = '|';
                    }
                }
                break;
            }
            var IsSelected = this.GetSelected (lix);
            var IsLocked = this.GetLocked (lix);
            var rcix = rix % this.RowClassNames.length;
            if (row.className != this.RowClassNames [rcix]) {
                row.className = this.RowClassNames [rcix];
            }
            if (row.rix == null) row.rix = rix;
            var autocolwidth = false;
            for (var cix = 0; cix < this.Width; ++ cix) {
                var colmgr = this.ColList [cix];
                if (this.ByValue == null) {
                    this.ByValue = colmgr.Cmd == 'edit' || colmgr.Cmd == 'windowsedit';
                }
                var cell = row.cells [cix];
                if (cell == null) {
                    cell = document.createElement ("td");
                    row.appendChild (cell);
                }
                if (line [cix] == null) {
                    cell.style.display = "none";
                } else {
                    cell.style.display = "";
                    var IsDisabled = this.GetDisabled (lix, cix);
                    colmgr.PaintCell(line, cell, rix, headerheight, IsDisabled || IsSelected, height);
                    cell.oncontextmenu = function (event) { return Qva.GetBinder(this.binderid).OnContextMenu(event); }
                    cell.binderid = this.PageBinder.ID;
                    cell.position = cix + ":" + rix + ":";
                    cell.position += this.IsHeader ? "Head" : "Body";
                    var targetname = colmgr.Name.split ('.') [1];
                    if (targetname == "Fields") targetname += "." + colmgr.Name.split ('.') [2]
                    cell.targetname = targetname;
                    if (this.RowHeight > 1) {
                        var rowheight = this.RowSpan * this.RowHeight;
                        if (this.WindowsSelectionstyle) {
                            rowheight = Math.max (rowheight, 20);
                        }
                        cell.style.height = rowheight + "px";
                    }
                }
            }

            this.IsPainted [rix] = true;
            PaintedLines ++;
            if (this.PageIncr > 0 && PaintedLines >= this.PageIncr) {
                postpaintposted = true;
                Qva.QueuePostPaintMessage (this);
                break;
            }
        }
    } else if (this.IsBody && ! this.Fixed) {
        if (this.FixTableHeight ()) {
            this.FinalFix = false;
            Qva.QueuePostPaintMessage (this);
            return;
        }
    } 
    
    this.Unlock ();
    this.Element.style.display = "";
    if (WantedChunkNumber != null) {
        this.ChunkOffset = WantedChunkNumber * this.ChunkSize;
        this.PageBinder.PartialLoad (this.PageName, this.ChunkOffset);
    } else if (this.InlineStyle && ! this.FinalFix && ! postpaintposted && ! this.Fixed) {
        this.FinalFix = true;
        Qva.QueuePostPaintMessage (this);
    } else if (this.FinalFix) {
        this.FinalFix = false;
        this.Fixed = true;
    }
}

Qva.Mgr.table.prototype.Inflate = function (height, rowlen) {
    var mgr = this;    
    var body = mgr.Body;
    var swapBody = (height - rowlen > 20 || height - rowlen < -20);
    var bodyParent = body.parentNode;
    mgr.RowNumbers = new Array ();    
    if (swapBody) {
        if (height < mgr.Body.rows.length) {
            body = mgr.Body.cloneNode (false);
            rowlen = 0;
        } else {
            for (var rix = 0; rix < mgr.Body.rows.length; ++ rix) {
                mgr.RowNumbers [rix] = rix;
                mgr.Body.rows [rix].rix = rix;
            }
            body = mgr.Body.cloneNode (true);
        }
    }
    var onerow = document.createElement ("tr");
    for(var i = 0; i < this.Width; ++i) {
        var cell = document.createElement ("td");
        var colmgr = mgr.ColList [i];
        cell.className = colmgr.ClassName;
        cell.align = colmgr.Align;
        cell.innerHTML = "|";
        onerow.appendChild (cell);
    }
    mgr.RowNumbers.length = height;
    var height_to_inflate = height;
    var chunksize = mgr.ChunkSize;
    if (chunksize == null || chunksize <= 0) chunksize = 20;
    if (! swapBody) {
        for (var rix = 0; rix < rowlen && rix < height_to_inflate; ++ rix) {
            mgr.IsPainted [rix] = false;
            var row = body.rows [rix];
            row.rix = rix;
            mgr.RowNumbers [rix] = rix;
            row.style.position = "static";
        }
    }
    for (var rix = rowlen; rix < height_to_inflate; ++ rix) {
        mgr.IsPainted [rix] = false;
        var row = null;
        if (rix == 0 && mgr.Body.rows [0]) {
            row = mgr.Body.rows [0].cloneNode (true);
        } else {
            row = onerow.cloneNode (true);
        }
        row.rix = rix;
        mgr.RowNumbers [rix] = rix;
        var rcix = rix % mgr.RowClassNames.length;
        row.className = mgr.RowClassNames [rcix];
        body.appendChild (row);
    }
    if (height_to_inflate < height) {
        var last_rix = height - 1;
        var rcix = last_rix % mgr.RowClassNames.length;
        var row = null;
        if (body.rows.length <= height_to_inflate) {
            row = onerow.cloneNode (true);
            row.className = mgr.RowClassNames [rcix];
            row.style.position = "absolute";
            row.style.left = body.rows [1].offsetLeft + "px";
            row.style.top = body.rows [1].offsetHeight * last_rix + "px";
            body.appendChild (row);
        } else {
            while (body.rows.length > height_to_inflate + 1) {
                body.deleteRow (height_to_inflate + 1);
            }
            mgr.RowNumbers.length = height_to_inflate + 1;
            row = body.rows [height_to_inflate];
            row.className = mgr.RowClassNames [rcix];
            row.style.position = "absolute";
            row.style.left = body.rows [1].offsetLeft + "px";
            row.style.top = body.rows [1].offsetHeight * last_rix + "px";
        }
        row.rix = last_rix;
        mgr.RowNumbers [last_rix] = body.rows.length - 1;
    }
    
//var fillTime = new Date ().valueOf () - Time;
    if (swapBody) {
        bodyParent.replaceChild (body, mgr.Body);
        mgr.Body = body;
    }
    if (height_to_inflate == height) {
        while (mgr.Body.rows.length > height_to_inflate) {
            mgr.Body.deleteRow (height_to_inflate);
        }
        mgr.RowNumbers.length = height_to_inflate;
    }
//Time = new Date ().valueOf () - Time;
//if (timeIt) alert ("Time: " + Time + " cloneTime: " + cloneTime + " fillTime: " + fillTime + " swapBody: " + swapBody + " height: " + height);
}

Qva.Mgr.table.prototype.RowForPos = function (rows, pos) {
    var lo = 0;
    var hi = rows.length;
    while (hi - lo > 1) {
        var m = Math.floor ((hi + lo) / 2);
        if (rows [m].offsetTop > pos) {
            hi = m;
        } else {
            lo = m;
        }
    }
    return lo;
}


Qva.Mgr.table.prototype.PageOffsetForPainting = function () {
    return (this.PageHandler == 'client') ? this.PageOffset : 0;
}

Qva.Mgr.table.prototype.GetSelectionStateClassName = function (is_selected, is_enabled, is_disabled, is_locked, is_deselected) {
    if (is_selected == true) {
        return this.SelectedClassName;
    } else if (is_deselected == true) {
        return this.DeselectedClassName;
    } else if (is_enabled == true) {
        return this.EnabledClassName;
    } else if (is_disabled == true) {
        return this.DisabledClassName;
    } else if (is_locked == true) {
        return this.LockedClassName;
    } else {
        return "";
    }
}

Qva.Mgr.table.prototype.ClearSelection = function () {
    if (window.getSelection) {
        window.getSelection().removeAllRanges();
    } else {
        window.document.selection.empty ();
    }
}

Qva.Mgr.table.prototype.IndicateCellsToSelect = function (ctrl, clearselection) {
    if (this.SelectionStartRow == null) return;
    var rix_start = this.SelectionStartRow.rix;
    var rix_end = this.SelectionEndRow.rix;
    if (rix_start > rix_end) {
        var tmp = rix_end;
        rix_end = rix_start;
        rix_start = tmp;
    }
    var cix_start = this.SelectionStartCol;
    var cix_end = this.SelectionEndCol;
    if (cix_start > cix_end) {
        var tmp = cix_end;
        cix_end = cix_start;
        cix_start = tmp;
    }
    if (this.prev_cix_start == null) this.prev_cix_start = new Array ();
    if (this.prev_cix_end == null) this.prev_cix_end = new Array ();
    var row_loop_start = this.prev_rix_start != null && this.prev_rix_start < rix_start ? this.prev_rix_start : rix_start;
    var row_loop_end = this.prev_rix_end != null && this.prev_rix_end > rix_end ? this.prev_rix_end : rix_end;
    for (var rix = row_loop_start; rix <= row_loop_end; rix ++) {
        var rowNumber = this.RowNumbers [rix];
        if (rowNumber == null) {
            var row = this.Body.rows [1].cloneNode (true);
            row.style.position = "absolute";
            row.style.left = this.Body.rows [1].offsetLeft + "px";
            row.style.top = this.Body.rows [1].offsetHeight * rix + "px";
            row.rix = rix;
            this.Body.appendChild (row);
            rowNumber = this.Body.rows.length - 1;
            this.RowNumbers [rix] = rowNumber;
        }
        var row = this.Body.rows [rowNumber];
        var selIx = rix + this.PageOffsetForPainting ();
        var col_loop_start = this.prev_cix_start [rix] != null && this.prev_cix_start [rix] < cix_start ? this.prev_cix_start [rix] : cix_start;
        var col_loop_end = this.prev_cix_end [rix] != null && this.prev_cix_end [rix] > cix_end ? this.prev_cix_end [rix] : cix_end;
        var actualcixstart = (this.ByValue && rix != row_loop_start) ? 0 : cix_start;
        var actualcixend = (this.ByValue && rix < row_loop_end) ? (row.cells.length - 1) : cix_end;
        col_loop_start = Math.min (col_loop_start, actualcixstart);
        col_loop_end = Math.max (col_loop_end, actualcixend);
        this.prev_cix_start [rix] = actualcixstart;
        this.prev_cix_end [rix] = actualcixend;
        for (var cix = col_loop_start; cix <= col_loop_end; ++ cix) {
            var StateClassName = null;
            var cell = row.cells [cix];
            if (rix < rix_start || rix > rix_end || cix < actualcixstart || cix > actualcixend) {
                // restore state
                var IsDisabled = this.GetDisabled (selIx, cix);
                var IsSelected = (ctrl || cell.windowsselectionstyle) ? (this.GetSelected (selIx, cix) != 0) : false;
                StateClassName = this.GetSelectionStateClassName (IsSelected, IsDisabled == false, IsDisabled == true);
            } else {
                // change to selected state or toggle if ctrl pressed
                var IsDisabled = false;
                var IsSelected = (ctrl || cell.windowsselectionstyle) ? ! (this.GetSelected (selIx, cix) != 0) : true;
                StateClassName = this.GetSelectionStateClassName (IsSelected, IsDisabled == false, IsDisabled == true);
            }
            var rcix = rix % this.RowClassNames.length;
            if (row.className != this.RowClassNames [rcix]) {
                row.className = this.RowClassNames [rcix];
            }
            var colmgr = this.ColList [cix];
            if (this.ByValue && cell.value == -1) continue;
            var cellClassName = colmgr.ClassName;
            if (StateClassName != '') {
                cellClassName += " " + StateClassName;
            }
            if (cell.className != cellClassName) {
                var deselect = clearselection || (StateClassName == this.EnabledClassName);
                this.IndicateSingleSelect (selIx, cix, deselect, StateClassName);
            }
        }
    }
    this.prev_rix_start = rix_start;
    this.prev_rix_end = rix_end;
}


Qva.Mgr.table.prototype.SetCellSelected = function (cell, newclassName, deselect, colmgr) {
    if (cell.windowsselectionstyle) { 
        var input = null;
        for (var iElem = 0; iElem < cell.childNodes.length; iElem++) {
            if (cell.childNodes [iElem].tagName == "INPUT") {
                input = cell.childNodes [iElem];
                break;
            }
        }
        if (input == null) debugger;
        var checked = this.SelectedClassName == newclassName;
        input.checked = checked;
    } else {
        if (deselect) {
            if (cell.origcolor != "") {
                cell.style.color = cell.origcolor;
                cell.origcolor = "";
            }
            if (cell.origbackgroundColor != "") {
                cell.style.backgroundColor = cell.origbackgroundColor;
                cell.origbackgroundColor = "";
            }
        } else {
            if (cell.style.color != "") {
                cell.origcolor = cell.style.color;
                cell.style.color = "";
            }
            if (cell.style.backgroundColor != "") {
                cell.origbackgroundColor = cell.style.backgroundColor;
                cell.style.backgroundColor = "";
            }
        }
        var cellClassName = colmgr.ClassName;
        if (newclassName != '') {
            cellClassName += " " + newclassName;
        }
        if (cell.className != cellClassName) {
            cell.className = cellClassName;
        }
    }
}

Qva.Mgr.table.prototype.IndicateSingleSelect = function (rowindex, colindex, deselect, newclassname) {
    var rows = this.Body.rows;
    var row = rows [this.RowNumbers [rowindex]];
    var cell = row.cells [colindex];
    var colmgr = this.ColList [colindex];
    if (cell.selectsource) {
        var selectedclassName = this.GetSelectionStateClassName (true, false, false);
        for (var cix = 0; cix < row.cells.length ; cix ++) {
            var cell = row.cells [cix];
            if (! cell.selectsource && (cell.singleselect || cell.multiselect)) {
                this.SetCellSelected (cell, selectedclassName, deselect, colmgr);
            }
        }
        for (var rix = 0; rix < rowindex; rix ++) {
            var cell = rows [this.RowNumbers [rix]].cells [colindex];
            if (! cell.selectsource && (cell.singleselect || cell.multiselect)) {
                this.SetCellSelected (cell, selectedclassName, deselect, colmgr);
            }
        }
    } else {
        this.SetCellSelected (cell, newclassname, deselect, colmgr);
    }
}

Qva.Mgr.table.prototype.SetCellStyle = function (data, ignorecolor, Styles, BorderStyles, firstrow, lastrow, firstcol, lastcol, showfrequency, hidetext) {
    var style = Styles [data.style]; 
    var csstext = "";
    if (style) {
        if (hidetext) {
            csstext += "; background-color:" + style.BgColor;
            csstext += "; color:" + style.BgColor;
        } else if (! ignorecolor) {
            csstext += "; background-color:" + style.BgColor;
            csstext += "; color:" + style.Color;
        }
        if (showfrequency) {
            csstext += "; text-align:left;";
        } else {
            csstext += "; text-align:" + (data.isnum ? style.NumAdjust : style.TextAdjust);
        }
        if (style.FontMod == 1) {
            csstext += "; font-style:italic";
            csstext += "; font-weight:400";
        } else if (style.FontMod == 2) {
            csstext += "; font-weight:bold";
            csstext += "; font-style:normal";
        } else {
            csstext += "; font-weight:400";
            csstext += "; font-style:normal";
        }
        switch (style.SizeMod) {
            case "2":
                csstext += "; font-size:large";
                break;
            case "1":
                csstext += "; font-size:larger";
                break;
            case "-1":
                csstext += "; font-size:smaller";
                break;
            case "-2":
                csstext += "; font-size:small";
                break;
        }
        var borderstyle = BorderStyles [style.BorderStyle];
        if (borderstyle) {
            var hastopborder = false;
            var hasbottomborder = false;
            if (! (data.subcell == "y")) {
                hastopborder = true;
                if (! (data.first == "y")) {
                    hasbottomborder = true;
                }
            }
            if (lastrow && this.SizeTodata && ! this.IsHeader || ! hasbottomborder) {
                csstext += "; border-bottom:none";
            } else {
                csstext += "; border-bottom:" + borderstyle.Bottom;
            }
            if (firstrow || ! hastopborder) {
                csstext += "; border-top:none";
            } else {
                csstext += "; border-top:" + borderstyle.Top;
            }
            var hasleftborder = false;
            var hasrightborder = false;
            if (! (data.subcell == "x")) {
                hasleftborder = true;
                if (! (data.first == "x")) {
                    hasrightborder = true;
                }
            }
            if (firstcol || ! hasleftborder) {
                csstext += "; border-left:none";
            } else {
                csstext += "; border-left:" + borderstyle.Left;
            }
            if (lastcol && this.SizeTodata || ! hasrightborder) {
                csstext += "; border-right:none";
            } else {
                csstext += "; border-right:" + borderstyle.Right;
            }
        }
    }
    return csstext;
}

Qva.Mgr.table.prototype.SelectRows = function (ctrl) {
    var rix_start = this.SelectionStartRow.rix;
    var rix_end = this.SelectionEndRow.rix;
    if (rix_start > rix_end) {
        var tmp = rix_end;
        rix_end = rix_start;
        rix_start = tmp;
    }
    var cix_start = this.SelectionStartCol;
    var cix_end = this.SelectionEndCol;
    if (cix_start > cix_end) {
        var tmp = cix_end;
        cix_end = cix_start;
        cix_start = tmp;
    }
    this.IndicateCellsToSelect (ctrl, true);
    this.SelectionStartRow = null;
    this.SelectionEndRow = null;
    this.SelectionStartCol = null;
    this.SelectionEndCol = null;
    
    var SearchActive = (this.Search != null && this.Search.value != '');
    if (this.ByValue) {
        var sendtext = this.Body.rows [this.RowNumbers [0]].cells [0].value == null;
        var singlecellselection = (rix_start == rix_end && cix_start == cix_end);
        var windowsselectionstyleandradio = false;
        if (singlecellselection) {
            if (this.Body.rows [this.RowNumbers [rix_start]].cells [cix_start].value == -1) return;
            windowsselectionstyleandradio = this.WindowsSelectionstyle && this.Body.rows [this.RowNumbers [rix_start]].cells [cix_start].singleselect == true;
        }
        var selIx = rix_start + this.PageOffsetForPainting ();
        var IsSelected = this.GetSelected (selIx);
        var valName = this.PageName;
        if (valName == "") {
            valName = this.ColList [0].Name.split ('.') [1];
        }
        if (! ctrl && ! sendtext && (! this.WindowsSelectionstyle || windowsselectionstyleandradio)) {	// Not toggle mode
            this.PageBinder.Set (valName, 'clear', '', false);
            if (IsSelected && this.Selected.length == 1 && singlecellselection) {
                rix_start ++; // nothing more to do
            }
        }
        for (var rix = rix_start; rix <= rix_end; rix ++) {
            var row = this.Body.rows [this.RowNumbers [rix]];
            var actualcixstart = (rix != rix_start) ? 0 : cix_start;
            var actualcixend = (rix < rix_end) ? (row.cells.length - 1) : cix_end;
            for (var cix = actualcixstart; cix <= actualcixend; cix ++) {
                var cell = row.cells [cix];
                var valValue = cell.value;
                if (ctrl) {	// Toggle mode
                    this.Lines [rix] [cix].selected = ! this.Lines [rix] [cix].selected;
                    this.PageBinder.ToggleSelect = valName;
                    Qva.ActiveObject = this.Element.id;
                    if (this.PageBinder.ToggleSelects == null) {
                        this.PageBinder.ToggleSelects = new Array ();
                    }
                    this.PageBinder.ToggleSelects [this.PageBinder.ToggleSelects.length] = valValue;
                } else {
                    if (sendtext) {
                        this.PageBinder.Set (valName, 'text', cell.innerText, false);
                    } else {
                        this.PageBinder.Set (valName, 'value', valValue, false);
                    }
                }
            }
        }
    } else {
        var valName = this.PageName;
        if (valName == "") {
            valName = this.ColList [0].Name.split ('.') [1];
        }
        var rectstring = '' + cix_start + ':' + (rix_start + this.PageOffsetForPainting ()) + ':' + (cix_end - cix_start + 1) + ':' + (rix_end - rix_start + 1);
        if (this.IsHeader) rectstring += ':Head';
        this.PageBinder.Set (valName, "rect", rectstring, true);
    }
    if (SearchActive) {
        this.PageBinder.Set (this.SearchName, "closesearch", "abort", true);      // break out of search mode
        if (this.Search != null) this.Search.value = '';				// Allow for popup search being closed
    } else if (! ctrl) {
        this.PageBinder.LoadBegin ();
    }
}

var m_MgrWithSelectStart  = null;

function AvqAction_TableEditMouseMove (event) {
    if (! event) event = window.event;
    
    var row = this.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (mgr.SelectionStartRow == null) return;
    if (m_MgrWithSelectStart != null && m_MgrWithSelectStart != mgr) {
        return;
    }
    if (mgr.SelectSource != this.selectsource) return;
    mgr.ClearSelection ();
    if (! mgr.PageBinder.Enabled) return;
    mgr.SelectionEndRow = row;
    mgr.SelectionEndCol = this.cellIndex;
    if (this.singleselect) {
        mgr.SelectionStartRow = mgr.SelectionEndRow;
        mgr.SelectionStartCol = mgr.SelectionEndCol;
    }
    mgr.IndicateCellsToSelect (ctrlKeyPressed (event));
}

function AvqAction_TableEditMouseDown (event) {
    if (! event) event = window.event;
    
    var row = this.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (event.button != mgr.LeftButton) return;
    if (! mgr.PageBinder.Enabled) return;
    mgr.prev_rix_start = null;
    mgr.prev_rix_end = null;
    mgr.SelectionStartRow = row;
    mgr.SelectionEndRow = row;
    mgr.prev_cix_start = null;
    mgr.prev_cix_end = null;
    mgr.SelectionStartCol = this.cellIndex;
    mgr.SelectionEndCol = this.cellIndex;
    mgr.SelectSource = this.selectsource;
    mgr.IndicateCellsToSelect (ctrlKeyPressed (event));
}

function AvqAction_TableEditMouseUp (event) {
    if (! event) event = window.event;
    
    var row = this.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (event.button != mgr.LeftButton) return;
    if (mgr.SelectionStartRow == null) return;
    if (m_MgrWithSelectStart != null && m_MgrWithSelectStart != mgr) {
        return;
    }
    mgr.ClearSelection ();
    if (! mgr.PageBinder.Enabled) return;
    if (mgr.SelectionStartRow == null) {
        mgr.SelectionStartRow = row;
        mgr.SelectionStartCol = this.cellIndex;
    }
    if (mgr.SelectSource == this.selectsource) {
        mgr.SelectionEndRow = row;
        mgr.SelectionEndCol = this.cellIndex;
    }
    mgr.SelectRows (ctrlKeyPressed (event));
}

function TableHeaderDblClick (event) {
    if (! event) { event = window.event; }
    var row = this.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    var name = mgr.ColList [this.cellIndex].Name;
    if (mgr.PageBinder.Enabled) {
        mgr.PageBinder.Set (name, "click", name, true);
    } else {
        mgr.PageBinder.PendingDblClickName = name;
    }
}

function avq_iconaction_md (event) {
    if (! event) event = window.event;
    event.cancelBubble = true;
    this.pressed = true;
}

function avq_iconaction_mu (event) {
    if (! this.pressed) return;
    if (! event) event = window.event;
    event.cancelBubble = true;
    this.pressed = false;
    if (this.targetname != null) {
        Qva.GetBinder(this.binderid).Set (this.targetname, this.action, this.xx + ":" + this.yy, true);
    }
}


Qva.ColMgr = { };
Qva.ColMgr.Init = function (parent, cix, cell, name, prefix) {
    this.Index = cix;
    if (cell != null) {
        this.ClassName = cell.className;
        this.Align = cell.align;
        this.Html = cell.innerHTML;
    }
    this.Parent = parent;
    Qva.MgrSplit (this, name, prefix)
}

Qva.ColMgr.basic = function (parent, cix, cell, name, prefix) {
    Qva.ColMgr.Init.apply(this, arguments);
}
Qva.ColMgr.basic.prototype.PaintCell = function (line, cell, rix) {
    cell.innerHTML = this.Html;
    debugger;
}

Qva.ColMgr.edit = function (parent, cix, cell, name, prefix, toolTip) {
    Qva.ColMgr.Init.apply(this, arguments);
    this.ToolTip = toolTip;
}

Qva.ColMgr.edit.prototype.PaintCell = function (line, cell, rix, headerheight, ignorecolor, height) {
    cell.style.color = "";
    var cix = this.Index;
    cell.value = line [cix].intval;
    var text = ""
    if (this.Parent.AndMode) {
        if (line [cix].selected) {
            text = "&  ";
        } else if (line [cix].deselected) {
            text = "!  ";
        }
    }
    text += (line [cix].val != '') ? line [cix].val : ' ';

    var StateClassName = this.Parent.GetSelectionStateClassName (line [cix].selected, ! line [cix].disabled && ! line [cix].locked, line [cix].disabled, line [cix].locked, line [cix].deselected);
    var cellClassName = this.ClassName;
    if (StateClassName != '') cellClassName += " " + StateClassName;
    if (cell.className != cellClassName) cell.className = cellClassName;
    appendCellContent (cell, line [cix].icons, text, this.Name.split ('.') [1], cix, rix + headerheight, this.Parent);
    cell.title = text;
    if (line [cix].frequency) {
        var span = document.createElement ("span");
        span.innerText = line [cix].frequency;
        span.style.position = "absolute";
        cell.appendChild  (span);
        var left = cell.offsetLeft + cell.offsetWidth - span.offsetWidth - 1;
        if (IS_GECKO || IS_OPERA) {
            left -= 3;
        }
        span.style.left = left + "px";
    }
    if (this.Parent.Element.disabled || line [cix].locked) {
        cell.onmousedown = Qva.NoAction;
        cell.onmousemove = Qva.NoAction;
        cell.onmouseup = Qva.NoAction;
    } else {
        cell.onmousedown = AvqAction_TableEditMouseDown;
        cell.onmousemove = AvqAction_TableEditMouseMove;
        cell.onmouseup = AvqAction_TableEditMouseUp;
    }
    if (line [cix].selecttype == "single") {
         cell.singleselect = true;
    }
    if (line [cix].style && this.Parent.InlineStyle) {
        cell.style.cssText += this.Parent.SetCellStyle (line [cix], ignorecolor && this.Parent.ByValue, this.Parent.Style, this.Parent.BorderStyle, rix == 0, rix == (height - 1), cix == 0, cix == (line.length - 1), line [cix].frequency);
    }
}

Qva.ColMgr.text = function (parent, cix, cell, name, prefix, toolTip) {
    Qva.ColMgr.Init.apply(this, arguments);
    this.ToolTip = toolTip;
}
Qva.ColMgr.text.prototype.PaintCell = function (line, cell, rix, headerheight, ignorecolor, height) {
    var cix = this.Index;
    
    if (line.IsHeader && line.IsHeader == true) {
        if (this.Parent.sortable && this.Parent.sortable == true) {
            cell.ondblclick = TableHeaderDblClick;
        }
    }
    var innertext = (line [cix].val != '') ? line [cix].val : ' ';
    if (this.ToolTip && this.Parent.ColDict[this.ToolTip]) cell.title = line [this.Parent.ColDict[this.ToolTip].Index].val;
    var showstateclass = false;
    if (line [cix].byval) {
        var StateClassName = this.Parent.GetSelectionStateClassName (line [cix].selected, ! line [cix].disabled && ! line [cix].locked, line [cix].disabled, line [cix].locked, line [cix].deselected);
        var cellClassName = this.ClassName;
        if (StateClassName != '') cellClassName += " " + StateClassName;
        if (cell.className != cellClassName) cell.className = cellClassName;
        showstateclass = true;
        cell.style.color = "";
        cell.style.backgroundColor = "";
    }
    if (this.Parent.InlineStyle && line [cix].style) {
        cell.style.cssText += this.Parent.SetCellStyle (line [cix], (ignorecolor && this.Parent.ByValue) || showstateclass, this.Parent.Style, this.Parent.BorderStyle, rix == 0, rix == (height - 1), cix == 0, cix == (line.length - 1), false);
    }
    if (cell.className && cell.className != "" && ! showstateclass) cell.className = "";
    if (line [cix].subcell) {
        cell.innerText = "";
        cell.title = innertext;
    } else {
        cell.innerText = innertext;
        if (innertext != " ") {
            cell.title = innertext;
        } else {
            cell.removeAttribute ("title");
        }
    }    
    if (line [cix].icons.length > 0 && ! line [cix].subcell) {
        appendCellContent (cell, line [cix].icons, line [cix].val, this.Name.split ('.') [1], cix, rix + headerheight, this.Parent);
    }
    var selecttype = line [cix].selecttype;
    if (selecttype == null) {
        cell.onclick = Qva.NoAction;
        cell.onmousedown = Qva.NoAction;
        cell.onmousemove = Qva.NoAction;
        cell.onmouseup = Qva.NoAction;
        cell.removeAttribute ("selectsource");
        cell.removeAttribute ("multiselect");
        cell.removeAttribute ("singleselect");
    } else if (selecttype == "input") {
        cell.innerText = "";
        var input = document.createElement ("input");
        input.value = innertext;
        var style = "border:none; height:"
        style += cell.offsetHeight;
        style += "px; width:";
        style += cell.offsetWidth;
        style += "px; ";
        if (this.Parent.Element.style.fontFamily) {
            style += "font-family:" + this.Parent.Element.style.fontFamily + "; ";
        }
        if (this.Parent.Element.style.fontSize) {
            style += "font-size:" + this.Parent.Element.style.fontSize + "; ";
        }
        input.style.cssText = style;
        input.onmousedown = Qva.CancelAction;
        input.onmouseup = Qva.CancelAction;
        input.onclick = Qva.CancelAction;
        new Qva.Mgr.inputtext (this.Parent.PageBinder, input, this.Parent.Name + ".V" + rix);
        cell.appendChild (input);
    } else { 
        cell.onmousedown = AvqAction_TableEditMouseDown;
        cell.onmousemove = AvqAction_TableEditMouseMove;
        cell.onmouseup = AvqAction_TableEditMouseUp;
        if (selecttype == "multi") {
            cell.multiselect = true;
        } else {
            cell.singleselect = true;
        }
        cell.selectsource = line [cix].selectsource;
    }
}

Qva.ColMgr.check = function (parent, cix, cell, name, prefix) {
    Qva.ColMgr.Init.apply(this, arguments);
}
Qva.ColMgr.check.prototype.PaintCell = function (line, cell, rix) {
    var cix = this.Index;
    
    if (cell.firstChild == null || cell.firstChild.tagName != 'INPUT' || cell.firstChild.type != 'checkbox') {
        cell.innerHTML = '<input class="avqCheckbox" type=checkbox >';
    }
    cell.firstChild.onclick = AvqAction_TableCheck;
    if (this.Parent.ChoiceIx != -1) {
        var lix = rix + this.Parent.PageOffsetForPainting();
        var IsSelected = this.Parent.GetSelected(lix);
        cell.firstChild.checked = (IsSelected != 0);
    } else {
        cell.firstChild.checked = line[cix].val != 0;
        cell.firstChild.disabled = line[cix].disabled;
    }
}

Qva.ColMgr.input = function (parent, cix, cell, name, prefix) {
    Qva.ColMgr.Init.apply(this, arguments);
}
Qva.ColMgr.input.prototype.PaintCell = function (line, cell, rix) {
    var cix = this.Index;
    
    if (cell.firstChild == null || cell.firstChild.tagName != 'INPUT' || cell.firstChild.type != 'text') {
        cell.innerHTML = '<input class="avqEdit" style="width:100%" value="" >';
        cell.firstChild.onchange = AvqAction_TableInput;
    }
    cell.firstChild.value = line[cix].val;
    cell.firstChild.disabled = line[cix].disabled;
}

Qva.ColMgr.windowsedit = function (parent, cix, cell, name, prefix, toolTip) {
    Qva.ColMgr.Init.apply (this, arguments);
    this.ToolTip = toolTip;
}

Qva.ColMgr.windowsedit.prototype.PaintCell = function (line, cell, rix, headerheight, ignorecolor, height) {
    cell.style.color = "";

    var cix = this.Index;
    cell.value = line [cix].intval;
    var text = ""
    if (this.Parent.AndMode) {
        if (line [cix].selected) {
            text = "&  ";
        } else if (line [cix].deselected) {
            text = "!  ";
        }
    }
    text += (line [cix].val != '') ? line [cix].val : ' ';

    var radio = line [cix].selecttype == "single";
    var input = null;
    for (var iElem = 0; iElem < cell.childNodes.length; iElem++) {
        if (cell.childNodes [iElem].tagName == "INPUT") {
            input = cell.childNodes [iElem];
        } else {
            cell.removeChild (cell.childNodes [iElem]);
        }
    }
    if (! input) {
        input = document.createElement ("INPUT");
        input.type = radio ? "radio" : "checkbox";
        cell.appendChild (input);
    }
    input.checked = line [cix].selected || line [cix].locked || line [cix].selectedexcluded;
    input.disabled = line [cix].disabled;
    var textnode = document.createTextNode (text);
    cell.appendChild (textnode);
    cell.windowsselectionstyle = true;
    if (this.Parent.Element.disabled || line [cix].locked) {
        cell.onmousedown = Qva.NoAction;
        cell.onmousemove = Qva.NoAction;
        cell.onmouseup = Qva.NoAction;
    } else {
        cell.onmousedown = AvqAction_TableEditMouseDown;
        cell.onmousemove = AvqAction_TableEditMouseMove;
        cell.onmouseup = AvqAction_TableEditMouseUp;
    }
    if (line [cix].selecttype == "single") {
         cell.singleselect = true;
    }
    if (line [cix].style && this.Parent.InlineStyle) {
        cell.style.cssText += this.Parent.SetCellStyle (line [cix], ignorecolor && this.Parent.ByValue, this.Parent.Style, this.Parent.BorderStyle, rix == 0, rix == (height - 1), cix == 0, cix == (line.length - 1), line [cix].frequency);
    }
}
