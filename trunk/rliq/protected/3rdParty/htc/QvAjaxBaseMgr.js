// Build $BuildVersion$

if (!Qva.Mgr) Qva.Mgr = {}

function onclick_action() {
    var binder = Qva.GetBinder(this.binderid);
    if (!binder.Enabled) return;
    binder.Set (this.Name, "action", "", true);
}

function onclick_ContextClientAction(event) { 
    Qva.GetBinder(this.binderid).ContextClientAction (event, this); 
}

Qva.Mgr.show = function (owner, elem, name, prefix, condition) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Condition = condition;
    owner.AddManager(this);
    this.Element = elem;
    owner.Append (this, this.Name, 'value');
}

Qva.Mgr.show.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var show;
    if (mode == 'n') {
        show = (this.ModeIfNotEnabled == 'd');
    } else {
        show = (node.getAttribute (this.Attr) == this.Condition);
    }
    this.Element.style.display = show ? '' : 'none';
}

Qva.Mgr.hide = function (owner, elem, name, prefix, condition) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Condition = condition;
    this.Element = elem;
    owner.AddManager(this);
    owner.Append (this, this.Name, 'value');
}

Qva.Mgr.hide.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var hide;
    if (mode == 'n') {
        if (this.Condition == null) {
            hide = (this.ModeIfNotEnabled != 'd');
        } else {
            hide = false;
        }
    } else {
        hide = (node.getAttribute (this.Attr) == this.Condition);
    }
    this.Element.style.display = hide ? 'none' : '';
}

Qva.Mgr.disable = function (owner, elem, name, prefix) {
    this.Name = Qva.MgrMakeName (name, prefix);
    this.Element = elem;
    owner.AddManager(this);
}

Qva.Mgr.disable.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.disable.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.disable.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.disabled = (mode != 'e');
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}

Qva.Mgr.caption = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    owner.AddManager(this);
    this.PageBinder = owner;
    
    elem.SelectStart = Qva.Mgr.caption.SelectStart;
    elem.Select      = Qva.Mgr.caption.Select;
    elem.SelectEnd   = Qva.Mgr.caption.SelectEnd;
    elem.onmousedown = function (event) { Qva.MouseDown(event, this); }
    elem.onmousemove = function (event) { Qva.MouseMove(event, this, 1); }
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    if (Qva.LabelClick) {
        elem.onclick = function (event) { Qva.GetBinder(this.binderid).SetClick(event, this.Name); }
    }
}

Qva.Mgr.caption.SelectStart = function (X, Y) {
    var elem = this;
    var at = Qva.GetPageCoords (elem.parentNode);
    this.Resize = (X - at.x) < 10 && (Y - at.y) < 10;
    var bkgid = elem.parentNode.id.replace ("_frame", "_bkg");
    var bkgelem = window.document.getElementById (bkgid);
    if (bkgelem) {
        this.BkgElem = bkgelem;
    }
    this.StartX = X;
    this.StartY = Y;
    this.OrigX = elem.parentNode.style.left;
    this.OrigY = elem.parentNode.style.top;
    this.OrigCoords = {'x': elem.parentNode.offsetLeft, 'y': elem.parentNode.offsetTop };
    if (this.Factor == null) {
        this.Factor = (300.0 * parseFloat(this.OrigX)) / (72.0 * this.OrigCoords.x);
    }
    if (this.Resize) {
        this.StartH = elem.parentNode.offsetHeight;
        this.StartW = elem.parentNode.offsetWidth;
        Qva.OpenSizeRect(X, Y, at.x, at.y, this.StartW, this.StartH + 2);
    } else {
        elem.parentNode.className += " QvMoveRect";
    }
}
Qva.Mgr.caption.Select = function (X, Y) {
    var elem = this;
    if (this.Resize) {
        Qva.SizeSizeRect(X, Y);
    } else {
        var x = this.OrigCoords.x + (X - this.StartX);
        var y = this.OrigCoords.y + (Y - this.StartY);
        elem.parentNode.style.left = x + "px";
        elem.parentNode.style.top = y + "px";
        if (this.BkgElem) {
            this.BkgElem.style.left = x + "px";
            this.BkgElem.style.top = y + "px";
        }
    }
}
Qva.Mgr.caption.SelectEnd = function (X, Y) {
    var elem = this;
    if (this.Resize) {
        Qva.CloseSizeRect(X, Y, this.Name, this.Factor, this.binderid);
    } else {
        var classname = elem.parentNode.className;
        elem.parentNode.className = classname.replace (" QvMoveRect", "");
        if (Math.abs(X - this.StartX) <= 4 && Math.abs(Y - this.StartY) <= 4) {
            elem.parentNode.style.left = this.OrigX;
            elem.parentNode.style.top = this.OrigY;
            if (this.BkgElem) {
                this.BkgElem.style.left = this.OrigX;
                this.BkgElem.style.top = this.OrigY;
            }
        } else {
            var x = this.OrigCoords.x + X - this.StartX;
            var y = this.OrigCoords.y + Y - this.StartY;
            elem.parentNode.style.left = x + "px";
            elem.parentNode.style.top = y + "px";
            if (this.BkgElem) {
                this.BkgElem.style.left = x + "px";
                this.BkgElem.style.top = y + "px";
            }
            var binder = Qva.GetBinder(elem.binderid);
            binder.Set (this.Name, "moveto", Math.round(x * this.Factor) + ':' + Math.round(y * this.Factor), true);
        }
    }
}

function setStyle (node, element) {
    if (node.getAttribute ('color')) element.style.color = node.getAttribute ('color');
    if (node.getAttribute ('bkgcolor')) element.style.backgroundColor = node.getAttribute ('bkgcolor');
    if (node.getAttribute ('fontfamily')) element.style.fontFamily = node.getAttribute ('fontfamily');
    if (node.getAttribute ('fontsize')) element.style.fontSize = node.getAttribute ('fontsize') + "pt";
    if (node.getAttribute ('fontstyle')) element.style.fontStyle = node.getAttribute ('fontstyle');
    if (node.getAttribute ('fontweight')) element.style.fontWeight = node.getAttribute ('fontweight');
    if (node.getAttribute ('textalign')) element.style.textAlign = node.getAttribute ('textalign');
    if (node.getAttribute ('verticalalign')) element.style.verticalAlign = node.getAttribute ('verticalalign');
}


Qva.Mgr.caption.prototype.Paint = function(mode, node, name) {
    this.Touched = true;
    var parentnode = node.parentNode
    if (parentnode.getAttribute ("menu") == "visible") return;
    var element = this.Element;
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    if (element.style.display == "none") return;
    if (parentnode.getAttribute ("allowmovesize") == "false") {
        element.SelectStart = null;
        element.Select      = null;
        element.SelectEnd   = null;
        element.onmousedown = null;
        element.onmousemove = null;
    }
    var bordercolor = parentnode.getAttribute ('bordercolor');
    if (bordercolor) {
        var borderstyle = parentnode.getAttribute ('borderstyle');
        var borderwidth = parentnode.getAttribute ('borderwidth');
        var bordercssstyle = borderstyle + " " + borderwidth + "pt " + bordercolor;
        element.style.borderLeft = bordercssstyle;
        element.style.borderRight = bordercssstyle;
        element.style.borderTop = bordercssstyle;
        if (parentnode.getAttribute ('simple') != "true") {
            element.style.borderBottom = borderstyle + " " + "1pt " + bordercolor;
        } else {
            element.style.borderBottom = borderstyle + " " + "0pt " + bordercolor;
        }
    }
    if (element.sourcenode == node) return;
    element.sourcenode = node;
    setStyle (node, element);
    if (mode != 'n') {
        element.innerHTML = "";
        var hascontent = false;
        var icons = parentnode.getElementsByTagName ("action");
        if (icons.length >= 1) {
            for (var i = (icons.length - 1); i >= 0; i--) {
                hascontent = true;
                var icon = icons [i];
                if (icon.getAttribute ("type") == "divider") continue;
                if (icon.getAttribute ("caption") != "true") continue;
                var iconnodes = icon.getElementsByTagName("icon");
                var iconnode = null;
                for (var iIcon = 0; iIcon < iconnodes.length; iIcon++) {
                    if (iconnodes[iIcon].getAttribute ("usage") != null && iconnodes[iIcon].getAttribute ("usage") != "caption") continue;
                    iconnode = iconnodes[iIcon];
                }
                if (iconnode == null) {
                    debugger;
                } else {
                    var stamp = iconnode.getAttribute ("stamp");
                    var img = document.createElement ("img");
                    img.alt = "";
                    img.className = "CaptionIcon";
                    img.title = icon.getAttribute ("text");
                    var style = iconnode.getAttribute ("style");
                    if (style) {
                        img.style.cssText = style;
                    }
                    var url = this.PageBinder.BuildBinaryUrl(iconnode.getAttribute ("path"), stamp);
                    img.src = url + (this.PageBinder.IsHosted ? "" : '&name=' + stamp);
                    if (icon.getAttribute ("mode") == "enabled") {
                        img.binderid = element.binderid;
                        var action = icon.getAttribute ("name");
                        if (action) {
                            img.onclick = onclick_action;
                            img.Name = this.Name + "." + action;
                        }
                        var clientaction = icon.getAttribute ("clientaction");
                        if (clientaction) {
                            img.onclick = onclick_ContextClientAction;
                            img.Name = this.Name;
                            img.AvqMgr = this;
                            img.clientaction = icon.getAttribute ('clientaction');
                            if (icon.getAttribute ('param')) {
                                img.param = icon.getAttribute ('param');
                            }
                        }
                    }
                    element.appendChild  (img);
                }
            }
        }
        if (node.getAttribute ('label')) {
            hascontent = true;
            var span = document.createElement ("span");
            span.innerText = node.getAttribute ('label');
            element.appendChild  (span);
        }
        if (! hascontent) {
            element.innerHTML += "&nbsp;";
        }
    }
}

Qva.Mgr.bind = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    owner.AddManager(this);
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
}

Qva.Mgr.bind.prototype.Paint = function(mode, node, name) {
    this.Touched = true;
    var element = this.Element;
    if (mode != 'n') {
        setStyle (node, element);
        var attrValue = '';
        if (mode != 'n') {
            attrValue = node.getAttribute (this.Attr);
        }
        if (attrValue == '' && this.TextIfNull) attrValue = this.TextIfNull;
        var text;
        if (this.Dec != null) {
            text = Qva.Trunc (attrValue, this.Dec);
        } else {
            text = attrValue;
        }
        var icons = node.getElementsByTagName ("icon");
        if (icons.length >= 1) {
            element.innerHTML = text;
            var stamp = icons[0].getAttribute ("stamp");
            var url = this.PageBinder.BuildBinaryUrl(icons[0].getAttribute ("path"), stamp);
            var innerhtml = '&nbsp;<img alt="" src="' + url + (this.PageBinder.IsHosted ? "" : '&name=' + stamp) + '" />';   
            element.innerHTML += innerhtml;
        } else {
            element.innerText = text;
        }
        if (node.getAttribute ('action')) {
            element.onclick = onclick_action;
        }
        if (IS_MAC) {
            if (node.getAttribute ('selected') == "true") {
                element.style.top = "3pt";
            } else {
                element.style.top = "2.5pt";
            }
        } else if (IS_GECKO) {
            if (node.getAttribute ('selected') == "true") {
                element.style.top = "2pt";
            } else {
                element.style.top = "1pt";
            }
        } else if (IS_CHROME) {
            if (node.getAttribute ('selected') == "true") {
                element.style.top = "2.5pt";
            } else {
                element.style.top = "2pt";
            }
        } else if (IS_OPERA) {
            if (node.getAttribute ('selected') == "true") {
                element.style.top = "3.5pt";
            } else {
                element.style.top = "3pt";
            }
        }
    }
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}

Qva.Mgr.label = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    owner.AddManager(this);
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
}

Qva.Mgr.label.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    if (node.getAttribute ("menu") != null) {
        element.oncontextmenu = function (event) { return Qva.GetBinder(this.binderid).OnContextMenu(event, this.Name); }
    }
    setStyle (node, element);
}

Qva.Mgr.frame = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    owner.AddManager(this);
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    elem.AvqMgr = this;
    this.BorderColor = null;
    this.BorderStyle = null;
    this.BorderWidth = null;
}

Qva.Mgr.frame.prototype.PostPaint = function () {
    var elem = this.Element;
    var borderstyle = this.BorderWidth + "pt " + this.BorderStyle + " " + this.BorderColor;
    var bkgid = elem.id.replace ("_frame", "_bkg");
    var bkgelem = window.document.getElementById (bkgid);
    if (bkgelem) {
        bkgelem.style.border = borderstyle;
    } else {
        var topchild = true;
        var lastchild = null;
        var numberofchildren = elem.childNodes.length;
        for (var ichild = 0; ichild < numberofchildren; ichild++) {
            var child = elem.childNodes [ichild];
            if (child.tagName != "DIV") continue;
            if (child.style.display == 'none') continue;
            child.style.borderLeft = borderstyle;
            child.style.borderRight = borderstyle;
            if (topchild) {
                child.style.borderTop = borderstyle;
                topchild = false;
            }
            lastchild = child;
        }
        if (lastchild) {
            lastchild.style.borderBottom = borderstyle;
        }
    }
}

Qva.Mgr.frame.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    if (node.getAttribute ("menu") != null) {
        element.oncontextmenu = function (event) { return Qva.GetBinder(this.binderid).OnContextMenu(event, this.Name); }
    }
    setStyle (node, element);
    if (node.getAttribute ('bordercolor')) {
        this.BorderColor = node.getAttribute ('bordercolor');
        if (node.getAttribute ('borderstyle')) {
            this.BorderStyle = node.getAttribute ('borderstyle');
            if (node.getAttribute ('borderwidth')) {
                this.BorderWidth = node.getAttribute ('borderwidth');
                this.Dirty = true;
                Qva.QueuePostPaintMessage (this);
            }
        }
    }
}

Qva.Mgr.edit = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    owner.AddManager(this);
    this.Element = elem;
    owner.Append (this, this.Name, 'choice');
}

Qva.Mgr.edit.prototype.Paint = function(mode, node, name) {
    this.Touched = true;
    var element = this.Element;
    if (name == this.ToolTip && node.getAttribute ('text')) {
        element.title = node.getAttribute ('text');
        return;
    }
    var attrValue = '';
    if (mode != 'n') {
        attrValue = node.getAttribute (this.Attr);
        switch (this.Attr) {
        case 'value':
            if (node.getAttribute ('text') == '') attrValue = '';
            break;
        case 'color':
            if (node.getAttribute ('color') && node.getAttribute ('bkgcolor')) {
                element.style.color = node.getAttribute ('color');
                element.style.backgroundColor = node.getAttribute ('bkgcolor');
                return;
            }
        }
    }
    if (attrValue == '' && this.TextIfNull) attrValue = this.TextIfNull;
    var text;
    if (this.Dec != null) {
        text = Qva.Trunc (attrValue, this.Dec);
    } else {
        text = attrValue;
    }
    element.innerText = text;
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    setStyle (node, element);
}

Qva.Mgr.inputcheckbox = function (owner, elem, name, prefix, condition) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    owner.AddManager(this);
    if (condition != null) {
        var sep = condition.substr(1,1);
        var parts = condition.split(sep);
        this.Conditional = (parts[0] == '-') ? -1 : 1;
        var alt1 = (parts.length > 1) ? parts[1] : '';
        var alt2 = (parts.length > 2) ? parts[2] : null;
        elem.True = (this.Conditional > 0) ? alt1 : alt2;
        elem.False = (this.Conditional < 0) ? alt1 : alt2;
    } else {
        this.Conditional = 0;
        elem.True = '1';
        elem.False = '0';
        this.Attr = 'value';
    }
    
    elem.Name = this.Name;
    elem.Attr = this.Attr;
    elem.binderid = owner.ID;
    
    elem.onclick = Qva.Mgr.inputcheckbox.OnClick;
    owner.Append (this, this.Name, 'choice');
}

Qva.Mgr.inputcheckbox.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.inputcheckbox.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.inputcheckbox.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    var val = node.getAttribute (this.Attr);
    if (this.Conditional < 0) {
        element.checked = (val != element.False);
    } else {
        element.checked = (val == element.True);
    }
    var choices = node.getElementsByTagName ("choice");
    if (choices.length == 0) {
        element.disabled = (mode != 'e');
    } else {
        choices = choices[0].getElementsByTagName ("element");
        var range = choices.length;
        if (range > 1) {
            element.disabled = (mode != 'e');
            if (this.Conditional < 0) {
                if (element.True == null) {
                    element.True = choices[0].getAttribute (this.Attr);
                    if (element.True == element.False) {
                        element.True = choices[1].getAttribute (this.Attr);
                    }
                }
            } else if (this.Conditional > 0) {
                if (element.False == null) {
                    element.False = choices[0].getAttribute (this.Attr);
                    if (element.True == this.False) {
                        element.False = choices[1].getAttribute (this.Attr);
                    }
                }
            }
        } else {
            element.disabled = true;
        }
    }
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}

Qva.Mgr.inputcheckbox.OnClick = function() {
    var binder = Qva.GetBinder(this.binderid);
    if (!binder.Enabled) return;
    var newval = this.checked ? this.True : this.False;
    if (newval == null) newval = '';
    binder.Set (this.Name, this.Attr, newval, true);
}

Qva.Mgr.inputradio = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    owner.AddManager(this);
    
    elem.onclick = function () {
        var binder = Qva.GetBinder(this.binderid);
        if (!binder.Enabled) return;
        binder.Set (this.Name, "text", this.value, true);
    };
    owner.Append (this, this.Name, 'choice');
}

Qva.Mgr.inputradio.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.inputradio.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.inputradio.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.checked = (element.value == node.getAttribute("text"));
    if (mode != 'e' || element.checked) {
        element.disabled = (mode != 'e');
    } else {
        // check if should be disabled because not in choice
        element.disabled = true;
        var choices = node.getElementsByTagName ("choice");
        if (choices.length >= 1) choices = choices[0].getElementsByTagName ("element");
        var cholen = choices.length;
        for (var ix = 0; ix < cholen; ++ix) {
            if (choices [ix].getAttribute("text") == element.value) {
                element.disabled = false;
                break;
            }
        }
    }
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}

Qva.Mgr.inputtext = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    elem.Attr = this.Attr;
    
    owner.AddManager(this);
    var _onclick = elem.onclick;
    elem.onclick = function (event) { Qva.ActiveObject = null;
                                        if (! event) {
                                            event = window.event;
                                        }
                                        event.cancelBubble = true;
                               };
    elem.onchange = function () {
        var binder = Qva.GetBinder(this.binderid);
        if (!binder.Enabled) return;
        if (this.value == '') {
            binder.Set(this.Name, 'text', '', true);
        } else {
            binder.Set(this.Name, this.Attr, this.value, true);
        }
    };
    owner.Append (this, elem.Name, 'choice');
}
Qva.Mgr.inputtext.prototype.Lock = Qva.LockReadOnly;
Qva.Mgr.inputtext.prototype.Unlock = Qva.UnlockReadOnly;
Qva.Mgr.inputtext.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.readOnly = (mode != 'e');
    var attrValue = '';
    if (mode != 'n') {
        attrValue = node.getAttribute (this.Attr);
        if (this.Attr == 'value' && node.getAttribute ('text') == '') attrValue = '';
    }
    if (attrValue == '' && this.TextIfNull) attrValue = this.TextIfNull;
    if (this.Dec != null) {
        element.value = Qva.Trunc (attrValue, this.Dec);
    } else {
        element.value = attrValue;
    }
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    setStyle (node, element);
}

Qva.Mgr.textarea = Qva.Mgr.inputtext;

Qva.Mgr.text = function (owner, elem, name, prefix, tooltip) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    if (tooltip != null) {
        this.ToolTip = Qva.MgrMakeName (tooltip, prefix);
        owner.Append (this, this.ToolTip);
    }
    if (elem.tagName == 'SELECT') { debugger; }
    owner.AddManager(this);
    this.Element = elem;
    owner.Append (this, this.Name, 'value');
}

Qva.Mgr.text.prototype.Paint = Qva.Mgr.edit.prototype.Paint;

Qva.Mgr.step = function (owner, elem, name, prefix, step) {
    switch (step) {
    case "next":
        this.Next = true;
        break;
    case "prev":
        this.Next = false;
        break;
    default:
        elem.Step = parseFloat (step);
        if (isNaN (elem.Step)) return;
        break;
    }
    if (!Qva.MgrSplit (this, name, prefix)) return;
    if (elem.Step == null) elem.Choice = new Array();
    owner.AddManager(this);
    this.Element = elem;
    
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    elem.Attr = this.Attr;
    elem.onclick = Qva.Mgr.step.OnClick;
    owner.Append (this, this.Name, 'choice');
}

Qva.Mgr.step.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.step.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.step.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.disabled = (mode != 'e');
    if (mode == 'n') {
        // no action
    } else if (element.Step != null) {
        element.Last = parseFloat (node.getAttribute (this.Attr));
    } else {
        var value = node.getAttribute (this.Attr);
        var choiceNodes = node.getElementsByTagName ("choice");
        element.Pending = -1;
        if (choiceNodes.length >= 1) {
            var choices = choiceNodes[0].getElementsByTagName ("element");
            var height = choices.length;
            for (var rix = 0; rix < height; ++rix) {
                element.Choice [rix] = choices[rix].getAttribute(this.Attr);
                if (choices[rix].getAttribute("selected") == "yes") {
                    if (this.Next) {
                        element.Pending = rix + 1;
                    } else {
                        element.Pending = rix - 1;
                    }
                }
            }
            element.Choice.length = height;
        }
        if (element.Pending < 0 || element.Pending >= element.Choice.length) element.disabled = true;
    }
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}

Qva.Mgr.step.OnClick = function() {
    var binder = Qva.GetBinder(this.binderid);
    if (!binder.Enabled) return;
    var newval;
    if (this.Step != null) {
        newval = this.Last + this.Step;
    } else {
        if (this.Pending < 0 || this.Pending >= this.Choice.length) return;
        newval = this.Choice[this.Pending];
    }
    binder.Set (this.Name, this.Attr, '' + newval, true);
}

Qva.Mgr.action = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Attr = "mode";
    owner.AddManager(this);
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    elem.onclick = onclick_action;
    owner.Append (this, this.Name, 'action');
}

Qva.Mgr.action.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.action.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.action.prototype.Paint = function (mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.disabled = (mode != 'e');
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    setStyle (node, element);
}

Qva.Mgr.restore = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Attr = "mode";
    owner.AddManager(this);
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    elem.onclick = onclick_action;
    owner.Append (this, this.Name, 'action');
}

Qva.Mgr.restore.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.restore.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.restore.prototype.Paint = function (mode, node) {
    this.Touched = true;
    var element = this.Element;
    element.disabled = (mode != 'e');
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    var parentnode = node.parentNode;
    if (parentnode.getAttribute ("menu") != null) {
        element.oncontextmenu = function (event) { return Qva.GetBinder(this.binderid).OnContextMenu(event, this.Name.replace (".RE", "")); }
    }
}

Qva.Mgr.select = function (owner, elem, name, prefix, condition) {
    if (elem.multiple) { debugger; return; }
    if (elem.size > 1) { debugger; return; }
    if (!Qva.MgrSplit (this, name, prefix)) return;
    owner.AddManager(this);
    this.Element = elem;
    this.Condition = condition;
    this.Texts = new Array ();
    this.Values = new Array ();
    this.Disabled = new Array ();
    this.Locked = new Array ();
    
    elem.binderid = owner.ID;
    elem.Name = this.Name;
    
    elem.onchange = Qva.Mgr.select.OnChange;
    elem.onclick = Qva.CancelBubble;
}

Qva.Mgr.select.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.select.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.select.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    if (mode != 'n' && element.ByValue == null) {
        element.ByValue = (node.getAttribute('value') != null);
    }
    setStyle (node, element);
    var currentValue = node.getAttribute("text");
    if (this.Dec != null) currentValue = Qva.Trunc (currentValue, this.Dec);
    if (currentValue == null) currentValue = "";
    if (this.TextIfNull && currentValue == "") currentValue = this.TextIfNull;
    var optlen = element.options.length;
    if (mode == 'e' && (this.Condition == null || currentValue != '')) {
        var choices = node.getElementsByTagName ("choice");
        if (choices.length >= 1) choices = choices[0].getElementsByTagName ("element");
        var first = (this.Condition == null) ? 0 : 1;
        var cholen = choices.length - first;
        element.options.length = cholen;
        if (cholen >= 1) {
            this.SelectedIndex = -1;
            for (var ix = 0; ix < cholen; ++ix) {
                var cho = choices [ix + first];
                var optval = cho.getAttribute("text");
                if (this.TextIfNull && optval == "") optval = this.TextIfNull;
                if (this.Dec != null) optval = Qva.Trunc (optval, this.Dec);
                var opt = element.options [ix];
                opt.text = optval.replace (/\t/g, ' ');
                opt.value = element.ByValue ? cho.getAttribute('value') : optval;
                var selected = false;
                if (optval == currentValue) {
                    this.SelectedIndex = ix;
                    selected = true;
                }
                if (selected) opt.selected = true;
            }
            if (this.SelectedIndex == -1) {
                element.options [cholen-1].selected = true;
            }
        }
        element.disabled = false;
    } else {
        element.disabled = true;
        if (optlen < 1 || this.Condition != null) {
            element.options.length = 1;
            var opt0 = element.options [0];
            opt0.text = (currentValue != null) ? currentValue.replace (/\t/g, ' ') : '';
            opt0.value = element.ByValue ? node.getAttribute('value') : currentValue;
            opt0.selected = true;
        }
    }
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}

Qva.Mgr.select.OnChange = function() {
    var binder = Qva.GetBinder(this.binderid);
    if (!binder.Enabled) return;
    if (this.selectedIndex < 0) return;
    var opt = this.options [this.selectedIndex];
    if (this.ByValue === true) {
        binder.Set (this.Name, 'value', opt.value, true);
    } else {
        binder.Set (this.Name, "text", opt.text, true);
    }
}


Qva.Mgr.binary = function (owner, elem, name, prefix) {
    elem.Name = this.Name = Qva.MgrMakeName (name, prefix);
    elem.binderid = owner.ID;
    this.Attr = "mode";
    owner.AddManager(this);
    this.Element = elem;
    this.start_url = elem.src;
    
    elem.SelectStart = Qva.Mgr.binary.SelectStart;
    elem.Select      = Qva.Mgr.binary.Select;
    elem.SelectEnd   = Qva.Mgr.binary.SelectEnd;
    elem.onclick = function (event) { Qva.GetBinder(this.binderid).SetClick(event, this.Name, this); }
    elem.onmousedown = function (event) { Qva.MouseDown(event, this); }
    elem.onmousemove = function (event) { 
        Qva.MouseMove(event, this); 
        this.onmouseover(event);
    }

    elem.onmouseover = function (event) {
        if(!this.hover) return;
        if(!event) event = window.event;
        var binder = Qva.GetBinder(this.binderid);
        var name   = this.Name;
        
        var hover = binder.GetHoverDiv();
        hover.style.left = (event.clientX + Qva.GetScrollLeft() + 5) + "px";
        hover.style.top  = (event.clientY + Qva.GetScrollTop() + 5)  + "px";
        hover.style.display = "none";
        
        var pos = Qva.GetPageCoords(this);
        var click = (event.clientX - pos.x) + ':' + (event.clientY - pos.y);
        var objectframeNode = this.parentNode.parentNode;
        
        if (this.hoverTimout) { this.hoverTimout = clearTimeout(this.hoverTimout); }
        this.hoverTimout = setTimeout(function () {
            if (!binder.Enabled) return;
            binder.IsPartialLoad = true; // Prevent ContextMenu from closing
            binder.Set(name, "hover", click, true);
        }, 1000 );
    };
    elem.onmouseout  = function (event) {
        if (this.hoverTimout) { this.hoverTimout = clearTimeout(this.hoverTimout); }
    }

    elem.style.cursor = 'crosshair';
    if (owner.IsHosted && ! elem.sizefixed) {
        var objectframeNode = elem.parentNode.parentNode;
        if (elem.style.height != '') {
            var height = imageheight (objectframeNode, elem);
            owner.Append (this, this.Name, 'h' + height + "px");
        }
        if (elem.style.width != '') {
            var width = imagewidth (elem);
            owner.Append (this, this.Name, 'w' + width + "px", true);
        }
    }
    var useragent = "" + window.window.navigator.userAgent;
    var version = parseInt (useragent.substr (useragent.indexOf ('MSIE') + 5, 3));
    owner.Append (this, this.Name, 'ie6' + (useragent.indexOf ('MSIE') != -1 && version < 7));
}

Qva.Mgr.binary.prototype.Unlock = function () {
    this.Touched = true;
    if (this.Element.style.display != 'none') {
        this.PostPaint ();
    }
}

Qva.Mgr.binary.prototype.Paint = function (mode, node) {
    this.Touched = true;
    if (node.getAttribute ("menu") == "visible") return;
    var element = this.Element;
    element.disabled = false;
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    if (element.style.display == 'none') {
        return;
    }
    
    if (node.getAttribute ('clientaction')) {
        element.onclick = onclick_ContextClientAction;
        element.Name = this.Name;
        element.AvqMgr = this;
        element.clientaction = node.getAttribute ('clientaction');
    }

    element.hover = node.getAttribute ('hover') === "true";
    
    var url = this.PageBinder.BuildBinaryUrl (node.getAttribute ('path'), node.getAttribute ('stamp'));
    this.avq_url = url;
    Qva.QueuePostPaintMessage (this);
}

Qva.Mgr.binary.prototype.PostPaint = function () {
    var elem = this.Element;
    if (elem.style.display == 'none') return;
    var frame = Qva.GetFrame (elem);
    if (frame && frame.AvqMgr.Dirty) {
        Qva.QueuePostPaintMessage (this);
        return;
    }
    var imageelem = null;
    var isbutton = false;
    if (elem.tagName == "BUTTON") {
        for (var i = 0; i < elem.childNodes.length; i++) {
            if (elem.childNodes [i].tagName == "IMG") {
                imageelem = elem.childNodes [i];
                break;
            }
        }
        isbutton = true;
    } else {
        imageelem = elem;
    }
    var parentNode = elem.parentNode;
    if (parentNode.tagName == "DIV") {
        var newheight = parseInt (getContentMaxHeight (elem));
        if (! isNaN (newheight)) {
            if (IS_GECKO) {
                //Ugly fix for disappearing bottom borders
                newheight--;
            }
            if (parseInt (parentNode.style.height) != newheight) {
                parentNode.style.height = newheight + "px";
            }
        }
    }
    var objectframeNode = parentNode.parentNode;

    var newwidth = objectframeNode.offsetWidth;
    if (objectframeNode.maxwidth) {
        newwidth = objectframeNode.maxwidth;
    } else {
        objectframeNode.maxwidth = newwidth;
    }
    if (IS_GECKO) {
        //Ugly fix for disappearing right borders
        newwidth--;
    }
    setContentWidth (parentNode, elem, newwidth , false);
    if (imageelem) {
        var url = this.avq_url;
        if (this.PageBinder.IsHosted) {
            if (elem.style.width != '') {
                var width = imagewidth (elem);
                this.PageBinder.Append (this, this.Name, 'w' + width + "px", false);
                elem.sizefixed = true;
                imageelem.style.width = width + "px";
            }
            if (elem.style.height != '') {
                var height = imageheight (objectframeNode, elem);
                this.PageBinder.Append (this, this.Name, 'h' + height + "px", false);
                elem.sizefixed = true;
                imageelem.style.height = height + "px";
            }
        } else {
            url += '&name=' + escape (this.Name);
            if (elem.style.width != '') {
                var graphwidth = imagewidth (elem);
                if ((IS_GECKO || IS_CHROME) && isbutton) {
                    graphwidth -= 3;
                }
                url += '&width=' + escape (graphwidth);
            }

            if (elem.style.height != '') {
                var graphheight = imageheight (objectframeNode, elem);
                if ((IS_GECKO || IS_CHROME) && isbutton) {
                    graphheight -= 1;
                }
                url += '&height=' + escape (graphheight);
            }
        }
        imageelem.src = url;
    } else {
        debugger;
    }
}

Qva.Mgr.binary.SelectStart = function (X, Y) { Qva.OpenDragRect(X, Y); }
Qva.Mgr.binary.Select      = function (X, Y) { Qva.SizeDragRect(X, Y); }
Qva.Mgr.binary.SelectEnd   = function (X, Y) { Qva.CloseDragRect(X, Y, this.Name, this.binderid, this); }


Qva.Mgr.binaryaction = function (owner, elem, name, prefix) {
    elem.Name = this.Name = Qva.MgrMakeName (name, prefix);
    elem.binderid = owner.ID;
    this.Attr = "mode";
    owner.AddManager(this);
    this.Element = elem;
    var imageelem = null;
    if (elem.tagName == "BUTTON") {
        for (var i = 0; i < elem.childNodes.length; i++) {
            if (elem.childNodes [i].tagName == "IMG") {
                imageelem = elem.childNodes [i];
                break;
            }
        }
        if (! imageelem) {
            imageelem = document.createElement ("img");
            elem.appendChild (imageelem);
        }
    } else {
        imageelem = elem;
    }
    this.start_url = imageelem.src;
    if (owner.IsHosted && ! elem.sizefixed) {
        var objectframeNode = elem.parentNode.parentNode;
        if (elem.style.width != '') {
            var width = imagewidth (elem);
            owner.Append (this, this.Name, 'w' + width + "px", true);
            imageelem.style.width = elem.style.width;
        }
        if (elem.style.height != '') {
            var height = imageheight (objectframeNode, elem);
            owner.Append (this, this.Name, 'h' + height + "px", true);
            imageelem.style.height = elem.style.height;
        }
    }
    elem.onclick = onclick_action;
    var useragent = "" + window.window.navigator.userAgent;
    var version = parseInt (useragent.substr (useragent.indexOf ('MSIE') + 5, 3));
    owner.Append (this, this.Name, 'ie6' + (useragent.indexOf ('MSIE') != -1 && version < 7));
    owner.Append (this, this.Name, 'action');
}

Qva.Mgr.binaryaction.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.binaryaction.prototype.Unlock = Qva.UnlockDisabled;
Qva.Mgr.binaryaction.prototype.Paint = Qva.Mgr.binary.prototype.Paint;
Qva.Mgr.binaryaction.prototype.PostPaint = Qva.Mgr.binary.prototype.PostPaint;


Qva.Mgr.menu = function (owner, elem, name, prefix) {
    this.Name = Qva.MgrMakeName ((name != null) ? name : '', prefix);
    owner.AddManager(this);
    this.Element = elem;
    owner.Append (this, this.Name, 'menu');
}

Qva.Mgr.menu.prototype.Paint = function (mode, node) {
    var itemnodes = node.getElementsByTagName ("action");
    if (itemnodes.length == 0) {
        Qva.CloseContextMenu ();
        return;
    }
    this.Touched = true;
    var element = this.Element;
    var numberofactions = 0;
    for (var i = 0; i < itemnodes.length; i++) {
        if (itemnodes [i].getAttribute ("menu") != "true") continue;
        numberofactions++;
        var action = itemnodes [i].getAttribute ("name");
        var clientaction = itemnodes [i].getAttribute ("clientaction");
        var iconnodes = itemnodes [i].getElementsByTagName("icon");
        if (! action && ! clientaction && i == (itemnodes.length -1)) break;
        var row;
        var cell1;
        var cell2;
        if (element.rows [i] && element.rows [i].cells [0]) {
            row = element.rows [i];
            cell1 = row.cells [0];
            cell2 = row.cells [1];
        } else {
            row = element.insertRow (-1);
            cell1 = row.insertCell (-1);
            cell2 = row.insertCell (-1);
        }
        cell1.style.width = "20pt";
        if (row.cells [1]) {
            cell2 = row.cells [1];
        } else {
            cell2 = row.insertCell (-1);
        }
        cell2.style.borderLeft = "1pt #cccccc solid";

        if (itemnodes [i].getAttribute ("type") == "divider")  {
            cell2.style.backgroundColor = "#cccccc";
            cell2.style.height = "1pt";
            cell1.style.padding = "0px";
            cell2.style.padding = "0px";
        }  else {
            for (var iIcon = 0; iIcon < iconnodes.length; iIcon++) {
                if (iconnodes[iIcon].getAttribute ("usage") != null && iconnodes[iIcon].getAttribute ("usage") != "menu") continue;
                var stamp = iconnodes[iIcon].getAttribute ("stamp");
                var url = this.PageBinder.BuildBinaryUrl (iconnodes[iIcon].getAttribute ("path"), stamp);
                cell1.innerHTML = '<img alt="" src="' + url + (this.PageBinder.IsHosted ? "" : '&name=' + stamp) + '" />';
            }
            if (action || clientaction) {
                cell2.innerText = itemnodes [i].getAttribute ("text");
                var disabled = itemnodes [i].getAttribute ("mode") == "disabled";
                row.disabled = disabled;
                row.binderid = this.PageBinder.ID;
                if (! disabled) {
                    if (action) {
                        row.onclick = onclick_action;
                        row.Name = this.Name + "." + action;
                    }
                    if (clientaction) {
                        row.onclick = onclick_ContextClientAction;
                        row.Name = this.Name;
                        row.AvqMgr = this;
                        row.clientaction = clientaction;
                        row.param = itemnodes [i].getAttribute ("param");
                    }
                    row.onmouseover = function () { this.className = "ContextHighlightedRow"; }
                    row.onmouseout = function () { this.className = ""; }
                } else {
                    cell1.style.color = "InactiveCaptionText";
                    cell2.style.color = "InactiveCaptionText";
                }
            } else {
                debugger;
            }
        }
    }
    Qva.KeepContextMenuAlive = false;
    if (numberofactions == 0) {
        Qva.CloseContextMenu ();
    }
}

Qva.Mgr.toolbar = function (owner, elem, name, prefix) {
    this.Name = Qva.MgrMakeName ((name != null) ? name : '', prefix);
    owner.AddManager(this);
    this.Element = elem;
    owner.Append (this, this.Name, 'toolbar');
}

Qva.Mgr.toolbar.prototype.Paint = function (mode, node) {
    var itemnodes = node.getElementsByTagName ("action");
    this.Touched = true;
    var element = this.Element;
    if (element.sourcenode && element.sourcenode.xml == node.xml) return;
    element.sourcenode = node;
    
    var row;
    if (element.rows[0]) {
        row = element.rows[0];
    } else {
        row = element.insertRow (-1);
    }
    
    if (row.cells.length > itemnodes.length) {
        // TODO: fix ?
    }
    
    var cellcount = 0;
    for (var i = 0; i < itemnodes.length; i++) {
        if (itemnodes [i].getAttribute ("caption") != "true") continue;
        if (itemnodes [i].getAttribute ("type") == "divider") continue;
        var action = itemnodes [i].getAttribute ("name");
        var clientaction = itemnodes [i].getAttribute ("clientaction");
        var iconnodes = itemnodes [i].getElementsByTagName ("icon");
        if (! action && ! clientaction && i == (itemnodes.length -1)) break;
        
        var cell;
        if (row.cells[cellcount]) {
            cell = row.cells[cellcount];
        } else {
            cell = row.insertCell(-1);
            
        }
        cellcount++;
        
        cell.style.width = "20pt";
        cell.align = "center";
        cell.className = ""
        cell.binderid = this.PageBinder.ID;
        
        var text = itemnodes [i].getAttribute ("text");
        cell.title = text;
        
        for (var iIcon = 0; iIcon < iconnodes.length; iIcon++) {
            if (iconnodes[iIcon].getAttribute ("usage") != null && iconnodes[iIcon].getAttribute ("usage") != "caption") continue;
            var stamp = iconnodes[iIcon].getAttribute ("stamp");
            var url = this.PageBinder.BuildBinaryUrl (iconnodes[iIcon].getAttribute ("path"), stamp);
            cell.innerHTML = '<img title="' + text + '" style="height:16px; width:16px;" alt="&nbsp;" src="' + url + (this.PageBinder.IsHosted ? "" : '&name=' + stamp) + '" />';
        }
        if (action || clientaction) {
            if (action) {
                cell.onclick = onclick_action;
                cell.Name = this.Name + "." + action;
            }
            if (clientaction) {
                cell.onclick = onclick_ContextClientAction;
                cell.Name = this.Name;
                cell.AvqMgr = this;
                cell.clientaction = clientaction;
                cell.param = itemnodes [i].getAttribute ("param");
            }
            cell.disabled = itemnodes [i].getAttribute ("mode") == "disabled";
            cell.onmouseover = function () { this.className = "ContextHighlightedRow"; }
            cell.onmouseout = function () { this.className = ""; }
        } else {
            cell.style.padding = "0px";
            cell.style.backgroundColor = "#cccccc";
            cell.style.width = "1px";
        }
    }
    var cell;
    if (row.cells[cellcount]) {
        cell = row.cells[cellcount];
    } else {
        cell = row.insertCell(-1);
    }
    var BMnode = node.getElementsByTagName ("value")[0];
    if (BMnode) {
        for (var i = 0; i < cell.childNodes.length; i++) {
            if (cell.childNodes [i].tagName == "SELECT") return;
        }
        cell.style.paddingLeft = "3pt";
        var select = window.document.createElement ("SELECT");
        cell.appendChild (select);
        new Qva.Mgr.select (this.PageBinder, select, this.Name + ".Bookmarks");
    }
}


Qva.MgrLinkScan = function (mgr, owner, lnkname, namePrefix) {
    mgr.LinkParts = lnkname.split ('$');
    var plen = mgr.LinkParts.length;
    mgr.LinkArgs = lnkname.split ('$');
    if (plen >= 2) {
        for (var lix = 1; lix < plen; lix += 2) {
            var name = namePrefix + mgr.LinkParts [lix];
            owner.Append (mgr, name);
            mgr.LinkParts [lix] = name;
        }
    }
};

function onclick_link () { 
    window.open (this.link);
}

Qva.Mgr.link = function (owner, elem, name, prefix, path) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    Qva.MgrLinkScan (this, owner, path, prefix);
    owner.AddManager(this);
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    elem.onclick = onclick_link;
}

Qva.Mgr.link.prototype.Paint = function (mode, node, name) {
    this.Touched = true;
    var element = this.Element;
    if (element.Name == name) {
        element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    }
    for (var lix = 1; lix < this.LinkParts.length; lix += 2) {
        if (this.LinkParts [lix] == name) {
            this.LinkArgs [lix] = node.getAttribute ("text");
        }
    }
    element.link = "";
    for (var lix = 0; lix < this.LinkArgs.length; lix ++) {
        element.link += this.LinkArgs [lix];
    }
}


Qva.Mgr.hover = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    owner.AddManager(this);
    this.Element = elem;
}

Qva.Mgr.hover.prototype.Paint = function(mode, node, name) {
    var element = this.Element;
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    element.innerHTML = "";
    var lines = node.getElementsByTagName ("line");
    for (var i = 0; i < lines.length; i++) {
        var p = document.createElement ("span");
        p.innerText = lines [i].getAttribute ("text");
        element.appendChild (p);
        var br = document.createElement ("br");
        element.appendChild (br);
    }
}
