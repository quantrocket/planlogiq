// Build $BuildVersion$

Qva.Trace = function(owner) {
    this.Owner = owner;
    var _this = this;
    if (document.addEventListener){ 
        document.addEventListener ("keypress", function (event) { _this.OnKeyPress(event); }, false);
    } else { 
        document.attachEvent ("onkeypress", function (event) { _this.OnKeyPress(event); });
    } 
}

Qva.Trace.prototype.OnKeyPress = function (event) {
    if (! event) { event = window.event; }
    if (event.shiftKey && ctrlKeyPressed (event) && event.keyCode == 4) { 
        this.ShowMenu ();
    }
}

Qva.Trace.prototype.ShowMenu = function () {
    if (this.Menu == null) {
        this.Menu = window.createPopup (); // createPopup() is IE only ?
        var ibody = this.Menu.document.body;
        var html = '<table width="100%" style="FONT-SIZE: 12px; COLOR: black;">';
        var revdate = "2008-01-25 02.00.15";
        var revno = "5689";
        html += '<tr><td><nobr>Rev: ' + revno + '</nobr></td></tr>';
        html += '<tr><td><nobr>Date:' + revdate + '</nobr></td></tr>';
        html += '<tr><td><hr></td></tr>';
        html += '<tr><td onmouseover="this.style.backgroundColor=\'silver\'" onmouseout="this.style.backgroundColor=\'\'"><nobr>Refresh</nobr></td></tr>';
        html += '<tr><td onmouseover="this.style.backgroundColor=\'silver\'" onmouseout="this.style.backgroundColor=\'\'"><nobr>Refresh All</nobr></td></tr>';
        html += '<tr><td onmouseover="this.style.backgroundColor=\'silver\'" onmouseout="this.style.backgroundColor=\'\'"><nobr>Trace</nobr></td></tr>';
        html += '<tr><td onmouseover="this.style.backgroundColor=\'silver\'" onmouseout="this.style.backgroundColor=\'\'"><nobr><hr /></nobr></td></tr>';
        html += '<tr><td onmouseover="this.style.backgroundColor=\'silver\'" onmouseout="this.style.backgroundColor=\'\'"><nobr>Show Cookie</nobr></td></tr>';
        html += '<tr><td onmouseover="this.style.backgroundColor=\'silver\'" onmouseout="this.style.backgroundColor=\'\'"><nobr>Clear MachineId</nobr></td></tr>';
        html += '</table>';
        ibody.innerHTML = html;
        ibody.style.backgroundColor = "gainsboro";
        ibody.style.borderColor = 'gainsboro';
        ibody.style.borderStyle = 'outset';
        ibody.style.borderWidth = 'thin';
        this.Rows = ibody.firstChild.rows;
        var _this = this;
        this.Rows[3].cells[0].onclick = function () { _this.Refresh(); }
        this.Rows[4].cells[0].onclick = function () { _this.RefreshAll(); }
        this.Rows[5].cells[0].onclick = function () { _this.TraceOn(); }
        this.Rows[7].cells[0].onclick = function () { _this.ShowCookie(); }
        this.Rows[8].cells[0].onclick = function () { _this.ClearMachineId(); }
        ibody.style.cursor = 'pointer';
    }
    for (var ix = 2; ix < this.Rows.length; ++ix) {
        this.Rows[ix].cells[0].style.backgroundColor='';
    }
    this.Menu.show(0, 17, 120, 6 + this.Rows.length * 20, event.srcElement);
}

Qva.Trace.prototype.Refresh = function () {
    if (this.Menu != null) this.Menu.hide();
    this.Owner.Refresh ();
}

Qva.Trace.prototype.RefreshAll = function () {
    if (this.Menu != null) this.Menu.hide();
    this.Owner.Stamp = '';
    this.Owner.Refresh ();
}

Qva.Trace.prototype.TraceOn = function () {
    if (this.Menu != null) this.Menu.hide();
    if (this.Dialog == null) {
        this.Dialog = window.showModelessDialog ("AvqtTracing.htm", null, "resizable:yes");
        this.Dialog.document.body.innerHTML =
            '<textarea wrap="off" readonly style="OVERFLOW: auto; WIDTH: 100%; HEIGHT: 10%" id="AvqRequestTrace"></textarea>' +
            '<textarea wrap="off" readonly style="OVERFLOW: auto; WIDTH: 100%; HEIGHT: 89%" id="AvqResponseTrace"></textarea>';
        var _this = this;
        this.Dialog.document.body.onunload = function () { 
            _this.Response = null; 
            _this.Request = null;
            _this.Dialog = null;
        }
        this.Response = this.Dialog.document.getElementById ('AvqResponseTrace');
        this.Request = this.Dialog.document.getElementById ('AvqRequestTrace');
    } else {
        this.Response = null;
        this.Request = null;
        this.Dialog.close ();
        this.Dialog = null;
    }
}

Qva.Trace.prototype.ShowCookie = function () {
    if (this.Menu != null) this.Menu.hide();
    alert('Cookies:\n' + document.cookie);
}

Qva.Trace.prototype.ClearMachineId = function () {
    if (this.Menu != null) this.Menu.hide();
    var expires = new Date();
    expires.setFullYear(2001, 2, 2);
    document.cookie = "qlikmachineid=; expires=" + expires.toGMTString();
    alert('Cookies:\n' + document.cookie);
}