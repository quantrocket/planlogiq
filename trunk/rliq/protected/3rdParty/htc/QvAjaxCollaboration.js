// Build $BuildVersion$

Qva.Collaboration = function (owner, elem) {
    this.Name = owner.DefaultScope + "." + owner.Autoview;
    this.Attr = 'collobj';
    this.Element = elem;
    this.PageBinder = owner;
    this.NewObjects = new Array();
    owner.AddManager(this);
}

Qva.Collaboration.prototype.Scale = function (node, attr, min) {
    var dim = (parseFloat(node.getAttribute (attr)) * 72.0) / 300.0;
    if (isNaN(dim) || dim < 0) dim = 100;
    if (dim < min) dim = min;
    return dim;
}

Qva.Collaboration.prototype.MakeCssRect = function (data, zix) {
    return 'left: ' + data.left + 'pt; top:' + data.top + 'pt; width: ' + data.width + 'pt; height: ' + data.height + 'pt; z-index: ' + zix + ';';
}

Qva.Collaboration.prototype.Paint = function (mode, rootnode) {
    this.Touched = true;
    if (this.Element == null) this.Element = document.getElementById("Main");
    if (this.Element == null) return;
    this.NewObjects = new Array();
    var nodes = rootnode.getElementsByTagName ('object');
    var count = nodes.length;
    for (var ix = 0; ix < count; ++ix) {
        var node = nodes [ix];
        var name = this.PageBinder.DefaultScope + "." + node.getAttribute ('name');
        if (this.PageBinder.Members[name]) continue;
        var data = {}
        data.name = name;
        data.type = node.getAttribute ('type');
        data.left = parseInt (this.Scale(node, 'left', 0));
        data.top = parseInt (this.Scale(node, 'top', 0));
        data.width = parseInt (this.Scale(node, 'width', 2));
        data.height = parseInt (this.Scale(node, 'height', 2));
        data.minimize = node.getAttribute ('minimized_left') != null;
        data.minimized_left   = parseInt (this.Scale(node, 'minimized_left', 0));
        data.minimized_top    = parseInt (this.Scale(node, 'minimized_top',  0));
        data.minimized_width  = parseInt (this.Scale(node, 'minimized_width', 2));
        data.minimized_height = parseInt (this.Scale(node, 'minimized_height', 2));
        this.NewObjects.push(data);
    }
    if (this.NewObjects.length < 1) return;
    Qva.QueuePostPaintMessage (this);
}

Qva.Collaboration.prototype.PostPaint = function () {
    var count = this.NewObjects.length;
    if (count < 1) return;
    for (var ix = 0; ix < count; ++ix) {
        var data = this.NewObjects[ix];
        switch(data.type) {
        case 'BM':
            //this.CreateBackground(data, "BookmarkObject");
            this.CreateBookmark(data);
            break;
        case 'BU':
            this.CreateButton(data);
            break;
        case 'CH':
            this.CreateChart(data);
            break;
        case 'CS':
            this.CreateCurrentSelectionBox(data);
            break;
        case 'IB':
            this.CreateInputBox(data);
            break;
        case 'LA':
            this.CreateLineArrow(data);
            break;
        case 'LB':
            this.CreateListBox(data);
            break;
        case 'MB':
            this.CreateMultiBox(data);
            break;
        case 'SB':
            this.CreateStatisticsBox(data);
            break;
        case 'SL':
            this.CreateSlider(data);
            break;
        case 'TB':
            this.CreateTableBox(data);
            break;
        case 'TX':
            //this.CreateBackground(data, "TextObject");
            this.CreateTextObject(data);
            break;
        default:
            this.CreateBackground(data, "Unknown");
            break;            
        }
        this.CreateMinimized(data);
    }
    var pb = this.PageBinder;
    window.setTimeout (function () { pb.LoadBegin() }, (IS_CHROME || IS_OPERA || IS_SAFARI) ? 200 : 50);	
}

Qva.Collaboration.prototype.CreateBookmark = function (data) {

    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "BookmarkObject Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var content = document.createElement("div");
    content.className = "content";
    var table = document.createElement("table");
    table.style.cssText = "width: auto; height: auto;";
    var row1 = table.insertRow(-1);
    row1.style.cssText = "vertical-align: top;";
    var cell1 = row1.insertCell(-1);
    cell1.style.cssText = "padding: 10 10 10 10;";
    var select = document.createElement("select");
    select.className = "BookmarkObject";
    select.style.cssText = "width: 100%;";
    cell1.appendChild(select);
    var row2 = table.insertRow(-1);
    row2.style.cssText = "height: 100%; vertical-align: top;";
    var cell2 = row2.insertCell(-1);
    cell2.style.cssText = "padding: 0 10 10 10;";
    var button = document.createElement("button");
    button.className = "BookmarkObject";
    button.style.cssText = "background-color: transparent;border: none;";
    var img = document.createElement("img");
    img.className = "BookmarkObject";
    button.appendChild(img);
    var span = document.createElement("span");
    button.appendChild(span);
    cell2.appendChild(button);

    content.appendChild(table);
    frame.appendChild(content);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, content, data.name + ".Content");
    new Qva.Mgr.select(this.PageBinder, select, data.name + ".Content");
    new Qva.Mgr.label (this.PageBinder, row2, data.name + ".Content.ADD");
    new Qva.Mgr.disable (this.PageBinder, button, data.name + ".Content.ADD");
    new Qva.Mgr.binary (this.PageBinder, img, data.name + ".Content.ADD");
    new Qva.Mgr.text (this.PageBinder, span, data.name + ".Content.ADD");
}

Qva.Collaboration.prototype.CreateButton = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "Button Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var content = document.createElement("div");
    content.className = "content";
    var button = document.createElement("button");
    button.style.cssText = "cursor: pointer; width: auto; height: auto;";
    content.appendChild(button);
    frame.appendChild(content);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, content, data.name + ".Content");
    new Qva.Mgr.binaryaction(this.PageBinder, button, data.name + ".Content");
}

Qva.Collaboration.prototype.CreateChart = function (data) {
    var frame = document.createElement("div");
    frame.className = "Chart Frame";
    frame.style.cssText = this.MakeCssRect(data, 1);
    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);
    
    var head = document.createElement("div");
    head.className = "header";
    var headTable = document.createElement("table");
    headTable.style.cssText = "width: auto; height: inherit;";
    headTable.className = "Chart";
    headTable.setAttribute("avqheader", "true");
    head.appendChild(headTable);
    frame.appendChild(head);
    
    var body = document.createElement("div");
    body.style.cssText = "position: relative;overflow:auto;height:" + (data.height - 9) + "pt;width:auto;";
    body.className = "body";
    var bodyTable = document.createElement("table");
    bodyTable.style.cssText = "width: auto;";
    bodyTable.className = "Chart";
    bodyTable.setAttribute("avqasync", "20:" + data.name);
    bodyTable.setAttribute("avqbody", "true");
    body.appendChild(bodyTable);
    frame.appendChild(body);
    
    var graph = document.createElement("div");
    graph.className = "graph";
    var chart = document.createElement("img");
    chart.style.cssText = "width:auto;height:auto;";
    graph.appendChild(chart);
    frame.appendChild(graph);
    
    this.Element.appendChild(frame);

    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, head, data.name + ".Head");
    new Qva.Mgr.table(this.PageBinder, headTable, data.name);
    new Qva.Mgr.label(this.PageBinder, body, data.name + ".Body");
    new Qva.Mgr.table(this.PageBinder, bodyTable);
    new Qva.Mgr.label(this.PageBinder, graph, data.name + ".Graph");
    new Qva.Mgr.binary(this.PageBinder, chart, data.name + ".Graph");
}

Qva.Collaboration.prototype.CreateCurrentSelectionBox = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "CurrentSelectionBox Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var body = document.createElement("div");
    body.className = "body";
    body.style.cssText = "position: relative; overflow: auto; height: " + (data.height - 9) + "pt; width: 100%;";

    var table = document.createElement("table");
    table.className = "body CurrentSelectionBox";
    table.style.cssText = "width: 100%;";
    var row = table.insertRow(-1);
    var cell = row.insertCell(-1);
    cell.setAttribute("avqcol", "text:" + data.name + ".C0");
    cell = row.insertCell(-1);
    cell.setAttribute("avqcol", "text:" + data.name + ".C2");
    body.appendChild(table);
    frame.appendChild(body);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.table(this.PageBinder, table);
}

Qva.Collaboration.prototype.CreateInputBox = function (data) {
    var frame = document.createElement("div");
    frame.className = "InputBox Frame";
    frame.style.cssText = this.MakeCssRect(data, 1);

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var content = document.createElement("div");
    content.className = "body";
    content.style.cssText = "position: relative; overflow: auto; height: 201pt; width: auto;";
    var table = document.createElement("table");
    table.className = "InputBox";
    table.style.cssText = "width: auto;";
    table.setAttribute("avqbody", "true");

    content.appendChild(table);
    frame.appendChild(content);
    
    this.Element.appendChild(frame);

    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.table(this.PageBinder, table, data.name);
}

Qva.Collaboration.prototype.CreateLineArrow = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "LineArrowObject Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var content = document.createElement("div");
    content.className = "content";
    var img = document.createElement("img");
    img.style.cssText = "width: auto; height: auto;";
    content.appendChild(img);
    frame.appendChild(content);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, content, data.name + ".Content");
    new Qva.Mgr.binary(this.PageBinder, img, data.name + ".Content");
}

Qva.Collaboration.prototype.CreateListBox = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "ListBox Frame";

    var caption = document.createElement ("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild (caption);

    var body = document.createElement ("div");
    body.className = "body";
    body.style.cssText = "position: relative; overflow: auto; height: " + (data.height - 9) + "pt; width: 100%;";

    var table = document.createElement ("table");
    table.className = "ListBox";
    table.style.cssText = "width: 100%;";
    table.setAttribute ("avqasync", "20:" + data.name);
    table.setAttribute ("avqbody", "true");
    body.appendChild (table);
    frame.appendChild (body);
    
    this.Element.appendChild (frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.table(this.PageBinder, table);
}

Qva.Collaboration.prototype.CreateMultiBox = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "MultiBox Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var body = document.createElement("div");
    body.className = "body";
    body.style.cssText = "position: relative; overflow: auto; height: " + (data.height - 9) + "pt; width: auto;";

    var table = document.createElement("table");
    table.className = "MultiBox";
    table.style.cssText = "width: auto;";
    table.setAttribute("avqbody", "true");
    body.appendChild(table);
    frame.appendChild(body);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.table(this.PageBinder, table, data.name);
}

Qva.Collaboration.prototype.CreateStatisticsBox = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "StatisticsBox Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var body = document.createElement("div");
    body.className = "body";
    body.style.cssText = "position: relative; overflow: auto; height: " + (data.height - 9) + "pt; width: auto;";

    var table = document.createElement("table");
    table.className = "StatisticsBox";
    table.style.cssText = "width: auto;";
    table.setAttribute("avqbody", "true");
    body.appendChild(table);
    frame.appendChild(body);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.table(this.PageBinder, table, data.name);
}

Qva.Collaboration.prototype.CreateSlider = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "SliderObject Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var content = document.createElement("div");
    content.className = "content";
    var slider = document.createElement("div");
    slider.style.cssText = "position: relative; width: 100%;";
    content.appendChild(slider);
    frame.appendChild(content);
    
    var calendar = document.createElement("div");
    calendar.className = "content";

    var table = document.createElement("table");
    table.style.cssText = "width: 100%; height: 100%;";
    var colgroup = document.createElement("colgroup");
    var col = document.createElement("col");
    col.style.cssText = "width: 80%;";
    colgroup.appendChild(col);
    col = document.createElement("col");
    colgroup.appendChild(col);
    table.appendChild(colgroup);
    var row = table.insertRow(-1);
    var cell = row.insertCell(-1);
    cell.style.cssText = "padding-left: 2pt;";
    var span = document.createElement("span");
    span.style.cssText = "background-color: White; width: 95%; padding-left: 2pt;";
    cell.appendChild(span);
    
    cell = row.insertCell(-1);
    var img = document.createElement("img");
    img.src = "/QvAjaxZfc/htc/calendar/img.gif";
    img.title = "Date selector";
    img.style.cssText = "cursor: pointer; border: 1px solid red;";
    cell.appendChild(img);

    calendar.appendChild(table);
    frame.appendChild(calendar);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, content, data.name + ".Slider");
    new Qva.Mgr.slider(this.PageBinder, slider, data.name + ".Slider");
    new Qva.Mgr.label(this.PageBinder, calendar, data.name + ".Calendar");
    new Qva.Mgr.edit(this.PageBinder, span, data.name + ".Calendar");
    new Qva.Mgr.date(this.PageBinder, img, data.name + ".Calendar");
}

Qva.Collaboration.prototype.CreateTableBox = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 1);
    frame.className = "TableBox Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var head = document.createElement("div");
    head.className = "header";
    var headTable = document.createElement("table");
    headTable.style.cssText = "width: auto; height: inherit;";
    headTable.className = "TableBox";
    headTable.setAttribute("avqheader", "true");
    head.appendChild(headTable);
    frame.appendChild(head);
    
    var body = document.createElement("div");
    body.style.cssText = "position: relative;overflow:auto;height:" + (data.height - 9) + "pt;width:auto;";
    body.className = "body";
    var bodyTable = document.createElement("table");
    bodyTable.style.cssText = "width: auto;";
    bodyTable.className = "TableBox";
    bodyTable.setAttribute("avqasync", "20:" + data.name);
    bodyTable.setAttribute("avqbody", "true");
    body.appendChild(bodyTable);
    frame.appendChild(body);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, head, data.name + ".Head");
    new Qva.Mgr.table(this.PageBinder, headTable, data.name);
    new Qva.Mgr.table(this.PageBinder, bodyTable);
}

Qva.Collaboration.prototype.CreateTextObject = function (data) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 0);
    frame.className = "TextObject Frame";

    var caption = document.createElement("div");
    caption.className = "caption Label";
    caption.innerText = " ";
    frame.appendChild(caption);

    var content = document.createElement("div");
    content.className = "content";
    var table = document.createElement("table");
    table.className = "TextObject";
    var row = table.insertRow(-1);
    var cell = row.insertCell(-1);
    cell.style.cssText = "width: 100%; height: 100%; padding: 2pt 2pt 2pt 2pt;";
    content.appendChild(table);
    frame.appendChild(content);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.caption(this.PageBinder, caption, data.name + ".Caption");
    new Qva.Mgr.label(this.PageBinder, content, data.name + ".Content");
    new Qva.Mgr.text(this.PageBinder, cell, data.name + ".Content");
}

Qva.Collaboration.prototype.CreateBackground = function (data, too) {
    var frame = document.createElement("div");
    frame.style.cssText = this.MakeCssRect(data, 0);
    frame.className = too + " Frame";
    
    var content = document.createElement("div");
    content.className =  "content";
    var table = document.createElement("table");
    table.style.cssText = "width: 100%; height: 100%;";
    var row = table.insertRow(-1);
    var cell = row.insertCell(-1);
    cell.style.cssText = "background-color: #d6e7f8; filter: alpha(opacity=100); -moz-opacity: 1; opacity: 1;";
    content.appendChild(table);
    frame.appendChild(content);
    
    this.Element.appendChild(frame);

    new Qva.Mgr.frame (this.PageBinder, frame, data.name);
    new Qva.Mgr.label (this.PageBinder, content, data.name + ".Content");
}

Qva.Collaboration.prototype.CreateMinimized = function (data) {
    if (!data.minimize) return;
    var frame = document.createElement("div");
    frame.className = "Frame";
    frame.style.cssText = "left:" + data.minimized_left + "pt;top:" + data.minimized_top + "pt;" +
                          "width:" + data.minimized_width + "pt;height:" + data.minimized_height + "pt;" +
                          "vertical-align:middle;text-align:left";
    
    var div = document.createElement("div");
    frame.appendChild(div);
    
    var button = document.createElement("button");
    button.style.cssText = "width:auto; height:auto;";
    div.appendChild(button);
    
    this.Element.appendChild(frame);
    
    new Qva.Mgr.restore (this.PageBinder, frame, data.name + ".RE");
    new Qva.Mgr.binaryaction (this.PageBinder, button, data.name + ".RE");
}

