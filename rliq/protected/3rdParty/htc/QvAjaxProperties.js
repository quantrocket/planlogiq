
var qva = new Qva.PageBinding();
var first = true;
function Start(type) {
    qva.Type = type;
    Qva.SetModalTitle(document.title);
    Qva.LabelClick = false;
    qva.Kind = qva.Type + "_obj";
    qva.Ident ="new:" + Qva.ExtractProperty("target", "");;
    qva.OnUpdateComplete = OnAnswer;
    qva.Trace = new Qva.Trace(qva);
    new Qva.Scanner(qva);
    Qva.Start();
}
function Finish() {
    qva.Set (qva.Type + '.CreateFromActiveObject', 'action', '', true);
}
function OnAnswer() {
    var node = qva.Select(qva.Type + '.CreateFromActiveObject');
    if (node == null) {
        if (first) alert(qva.Type + ' not availbale');
        Qva.CloseModal();
    }
    first = false;
}

function GetCurrentPageIndex() {
    var pageContainer = document.getElementById('PageContainer');
    var len = pageContainer.childNodes.length;
    for (var ix = 0; ix < len; ++ix) {
        var node = pageContainer.childNodes[ix];
        if(!node.style) continue;
        if (node.style.visibility != 'visible') continue;
        return ix;
    }
    return -1;
}

function GetNextPageIndex(ix, step) {
    var pageContainer = document.getElementById('PageContainer');
    var pageHeader = document.getElementById('PageHeader');
    var len = pageContainer.childNodes.length;
    for (var nix = ix + step; nix >= 0 && nix < len; nix += step) {
        if(pageContainer.childNodes[nix] == null) return -1; // no page available;
        if (pageHeader.childNodes[nix].tagName != 'DIV') continue;
        if (pageHeader.childNodes[nix].AvqHidden) continue;
        return nix;
    }
    return -1;
}

function ChangePage(step) {
    var pageContainer = document.getElementById('PageContainer');
    var pageHeader = document.getElementById('PageHeader');
    var ix = GetCurrentPageIndex();
    if (ix < 0) return;
    var nix = GetNextPageIndex(ix, step);
    if (nix < 0) return;
    pageHeader.childNodes[ix].style.display = 'none';
    pageHeader.childNodes[nix].style.display = '';
    pageContainer.childNodes[ix].style.visibility = 'hidden';
    pageContainer.childNodes[nix].style.visibility = 'visible';
    var pageNext = document.getElementById('PageNext');
    var pageBack = document.getElementById('PageBack');
    pageBack.disabled = step < 0 && GetNextPageIndex(nix, step) < 0;
    pageNext.disabled = step > 0 && GetNextPageIndex(nix, step) < 0;
}

function NextPage() {
    ChangePage(1);
}

function PrevPage() {
    ChangePage(-1);
}

function GoTab(tab, event) {
    var pageContainer = document.getElementById('PageContainer');
    var len = pageContainer.childNodes.length;
    for (var ix = 0; ix < len; ++ix) {
        var node = pageContainer.childNodes[ix];
        if(node.style) node.style.display = 'none';
    }
    
    var pageNavigation = document.getElementById('PageNavigation');
    len = pageNavigation.childNodes.length;
    for (ix = 0; ix < len; ++ix) {
        var node = pageNavigation.childNodes[ix];
        if (node.tagName != 'A') continue;
        node.className = '';
    }
    
    var elem = event.target;
    if (elem == null) elem = event.srcElement;
    elem.parentNode.className = 'selected';
    
    tab.style.display = '';
}

Qva.Mgr.imgradio = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    elem.Name = this.Name;
    elem.binderid = owner.ID;
    owner.AddManager(this);
    
    elem.onclick = function () {
        var binder = Qva.GetBinder(this.binderid);
        if (!binder.Enabled) return;
        binder.Set (this.Name, "text", this.getAttribute("value"), true);
    };
    owner.Append (this, this.Name, 'choice');
}

Qva.Mgr.imgradio.prototype.Paint = function(mode, node) {
    this.Touched = true;
    var element = this.Element;
    var disabled = true;
    if (mode == 'e') {
        // check if should be disabled because not in choice
        var choices = node.getElementsByTagName ("choice");
        if (choices.length >= 1) choices = choices[0].getElementsByTagName ("element");
        var cholen = choices.length;
        for (var ix = 0; ix < cholen; ++ix) {
            if (choices [ix].getAttribute("text") == element.getAttribute("value")) {
                disabled = false;
                break;
            }
        }
    }
    element.className = (element.getAttribute("value") == node.getAttribute("text")) ? 'selectedborder' : 'unselectedborder';
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
}


Qva.Mgr.wizardpage = function (owner, elem, name, prefix) {
    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    elem.AvqHidden = false;
    owner.AddManager(this);
}

Qva.Mgr.wizardpage.prototype.Paint = function(mode, node) {
    this.Touched = true;
    this.Element.AvqHidden = mode == 'h';
}
