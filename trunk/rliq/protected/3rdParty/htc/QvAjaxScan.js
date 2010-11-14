// Build $BuildVersion$

Qva.Scanner = function (binder, ns) {
    this.DefaultBinder = binder;
    this.Errors = new Array ();
    if (ns) {
        this.NameSpace = ns;
        this.Prefix = ns + ':';
        this.Attr = ns + ':bind';
    } else {
        this.NameSpace = 'avq';
        this.Prefix = 'avq';
        this.Attr = 'avq';
    }
    Qva.Scanner.instance = this;
}

Qva.Scanner.prototype.Start = function () {
    var binder = this.DefaultBinder || Qva.GetBinder();
    var scope = (binder && (binder.Autoview != null || binder.Kind != null)) ? binder.DefaultScope : null;
    
    //if (?.Benchmark != null) ?.Benchmark.Scan.Start();
    this.Scan(document.body, scope, binder)
    //if (?.Benchmark != null) ?.Benchmark.Scan.Stop();
    
    if (this.Errors.length > 0) {
        var msg = 'Errors:\n' + this.Errors.join ('\n');
        this.Errors.length = 0;
        alert(msg);
    }
}

Qva.Scanner.prototype.Scan = function (elem, parPrefix, binder) {
    if(!elem.getAttribute) return;
    var doc = elem.getAttribute (this.Prefix + 'doc');
    if(doc != null) {
        doc = doc.split(':');
        binder = Qva.GetBinder(doc[0], doc[1]);
        parPrefix = (binder.Autoview != null || binder.Kind != null) ? binder.DefaultScope : null;
    }
    var view = elem.getAttribute (this.Prefix + 'view');
    if(view != null) {
        binder = Qva.GetBinder(view, view);
        parPrefix = (binder.Autoview != null || binder.Kind != null) ? binder.DefaultScope : null;
    }
    
    var prefix = elem.getAttribute (this.Prefix + 'scope');
    if (prefix == null) {
        prefix = parPrefix;
    } else if (prefix.substr(0,1) == '.') {
        prefix = parPrefix + prefix;
    }
    
    var avqatt = elem.getAttribute (this.Attr);
    if (avqatt != null) {
        if (avqatt.indexOf (':') == -1 && avqatt.indexOf ('.') >= 0) {
            avqatt = 'edit:' + avqatt;
        }
        var parts = avqatt.split (':');
        var mgrtype = parts [0].toLowerCase();
        if (mgrtype == 'edit') {
            var alttype = elem.tagName.toLowerCase();
            if (alttype == 'input') alttype += elem.type.toLowerCase();
            if (Qva.Mgr[alttype]) { mgrtype = alttype; } else { if (alttype != "span") alert(alttype); }
        }
        if (Qva.Mgr[mgrtype]) {
            var name = (parts.length > 1) ? parts[1] : null;
            var condition = (parts.length > 2) ? parts.slice(2).join (':') : null;
            var mgr = new Qva.Mgr[mgrtype] (binder, elem, name, prefix, condition);
            if (mgr.Name != null) {
                this.PostManager(mgr);
            } else {
                this.Errors [this.Errors.length] = 'Invalid ' + this.NameSpace + '-attribute: ' + avqatt;
            }
        } else {
            this.Errors [this.Errors.length] = 'Unknown type: ' + mgrtype;
        }
    }
    
    var len = elem.childNodes.length;
    for (var ix = 0; ix < len; ++ix) {
        this.Scan(elem.childNodes[ix], prefix, binder);
    }
}

Qva.Scanner.prototype.PostManager = function (mgr) {
    var elem = mgr.Element;
    mgr.SelectedClassName = elem.getAttribute ('AvqSelected');
    if (mgr.SelectedClassName == null) {
        mgr.SelectedClassName = 'AvqSelected';
    }
    mgr.DeselectedClassName = elem.getAttribute ('AvqDeselected');
    if (mgr.DeselectedClassName == null) {
        mgr.DeselectedClassName = 'AvqDeselected';
    }
    mgr.EnabledClassName = elem.getAttribute ('AvqEnabled');
    if (mgr.EnabledClassName == null) {
        mgr.EnabledClassName = 'AvqEnabled';
    }
    mgr.DisabledClassName = elem.getAttribute ('AvqDisabled');
    if (mgr.DisabledClassName == null) {
        mgr.DisabledClassName = 'AvqDisabled';
    }
    mgr.LockedClassName = elem.getAttribute ('AvqLocked');
    if (mgr.LockedClassName == null) {
        mgr.LockedClassName = 'AvqLocked';
    }
    mgr.TextIfNull = mgr.Element.getAttribute (this.Prefix + 'textifnull');
    mgr.Icon = mgr.Element.getAttribute (this.Prefix + 'icon');
    mgr.ModeIfNotEnabled = 'n';    // Use "true" non-enabled mode
    var ifNotEnabled = mgr.Element.getAttribute (this.Prefix + 'ifnotenabled');
    if (ifNotEnabled == null) return;
    switch (ifNotEnabled) {
    case "disabled":
        this.ModeIfNotEnabled = 'd';
        break;
    case "hidden":
        this.ModeIfNotEnabled = 'h';
        break;
    default:
        this.Errors [this.Errors.length] = 'IfNotEnabled is not implemented for ' + ifNotEnabled;
        return;
    }
}

