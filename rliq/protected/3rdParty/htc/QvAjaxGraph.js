function purge(d) {
    var a = d.attributes, i, l, n;
    if (a) {
        l = a.length;
        for (i = 0; i < l; ++i) {
            n = a[i].name;
            if (typeof d[n] === 'function') {
                d[n] = null;
            }
        }
    }
    a = d.childNodes;
    if (a) {
        l = a.length;
        for (i = 0; i < l; ++i) {
            purge(d.childNodes[i]);
        }
    }
}

function Node() { }
Node.prototype.get = function () { return null; }
Node.prototype.getNum = function (attr) { return parseFloat(this.get(attr)); }
Node.prototype.getBool = function (attr) {
    var val = this.get(attr);
    return val === "true" || val === "on";
}

function ObjectNode(obj) { this.obj = obj; }
ObjectNode.prototype = new Node();
ObjectNode.prototype.get = function (attr) { return this.obj[attr]; }
ObjectNode.prototype.type = "ObjectNode";
function PrefixNode(prefix, node) { this.prefix = prefix; this.node = node }
PrefixNode.prototype.get = function (attr) { return this.node.get(this.prefix + attr); }
PrefixNode.prototype.type = "PrefixNode";
function FunctionNode(funcs) { this.funcs = funcs; }
FunctionNode.prototype.get = function (attr) { return this.funcs[attr] && this.funcs[attr](); }
FunctionNode.prototype.type = "FunctionNode";
function XmlNode(node) { this.node = node; }
XmlNode.prototype.get = function (attr) { return this.node.getAttribute(attr); }
XmlNode.prototype.type = "XmlNode";
function CacheNode(node) { this.node = node; this.cache = {}; }
CacheNode.prototype = new Node();
CacheNode.prototype.get = function (attr) {
    if(this.cache[attr] === undefined) {
        this.cache[attr] = this.node.get(attr);
        if(this.cache[attr] === undefined) this.cache[attr] = null;
    }
    return this.cache[attr];
}
CacheNode.prototype.type = "CacheNode";
function OrNode() { this.values = arguments; }
OrNode.prototype = new Node();
OrNode.prototype.get = function (attr) {
    var val;
    for(var ix = 0; ix < this.values.length; ++ix) {
        val = this.values[ix].get(attr);
        if(val || val === 0) break;
    }
    return val;
}
OrNode.prototype.type = "OrNode";

function GraphObj() { }
GraphObj.prototype = new Node();
GraphObj.prototype.get = function (attr) { return this.data.get(attr); }
GraphObj.prototype.type = "GraphObj";
GraphObj.prototype.draw = function () {};
GraphObj.prototype.init = function (graph, data) {
    this.data = data;
    this.Graph = graph;
}
GraphObj.prototype.xml_init = function (graph, parent, elem) {
    this.init(graph, new OrNode(new XmlNode(elem), parent));
}
GraphObj.prototype.update1 = function () {};
GraphObj.prototype.update2 = function () {};

var graphObjs = { }
function createGraphObj(tagName) {
    var f = graphObjs[tagName];
    return f && new f();
}
function createGraphObjType(name, needPlotarea, base) {
    function f() {}
    f.prototype = new (base || GraphObj)();
    f.prototype.type = name;
    f.prototype.needPlotarea = needPlotarea;
    graphObjs[name] = f;
    return f;
}

function Legend(graph) { this.Graph = graph; }
Legend.prototype = new Node();
Legend.prototype.add = function(item) { this.items[this.items.length] = item; }
Legend.prototype.clear = function() { this.items = []; }
Legend.prototype.update = function(size) {
    if(this.items.length === 0) return;
    this.fontSize = 12;
    this.symbolSize = MeasureText("åg", this.fontSize).height;
    var max_w = 0;
    for(var i = 0; i < this.items.length; ++i) {
        var w = MeasureText(this.items[i].text, this.fontSize).width;
        if(w > max_w) max_w = w;
    }
    var width = 20 + this.symbolSize + max_w;
    
    this.x = size.width - width + 10;
    this.y = size.y;
    
    size.width -= width;
}
Legend.prototype.draw = function() {
    var G = this.Graph.G;

    for(var i = 0; i < this.items.length; ++i) {
        var item = this.items[i];
        
        var x = this.x;
        var y = this.y;
        
        var symbol_size = this.symbolSize;
        y += i * (symbol_size + 2);
        
        var symbol = G.CreateRect([x, y], [symbol_size, symbol_size], item.color, this.Graph.Root, false);
        var label = CreateText(this.Graph, item.text, this.fontSize);
        label.div.style.left = (x + symbol_size + 5) + "px";
        label.div.style.top  = Math.round(y + (symbol_size - label.height) / 2) + "px";
    }
}

function Range(graph, id) { this.datasets = []; this.id = id; this.Graph = graph; }
Range.prototype = new Node();
Range.prototype.get = function (attr) { return this.data.get(attr); }
Range.prototype.getLength = function(l) { 
    l = Math.round(l * Math.abs(this.start - this.end));
    return l + this.end_off - this.start_off; 
}
Range.prototype.scale = function (v, l, rev) {
    v = ScaleFrom(v, this.min, this.max);
    if(this.rev || rev) v = 1 - v;
    v = ScaleTo(v, this.start, this.end);
    return ScaleTo(v, this.start_off, l + this.end_off);
}
Range.prototype.update = function () {
    var from = this.get("from");
    if(from) {
        from = from.split(";");
        for(var i = 0; i < from.length; ++i) {
            from[i] = this.Graph.Datasets[from[i]];
        }
    }
    var data = from || this.datasets;
    
    this.datasets = [];
    
    this.start = 0;
    this.end   = 1;
    this.start_off = 0;
    this.end_off   = 0;
    
    var start = this.get("start") || "10px";
    var end   = this.get("end")   || "-10px";
    if(start.charAt(start.length - 1) === "%") {
        this.start = parseFloat(start) / 100;
    } else if((/px$/).test(start)) {
        this.start_off = parseInt(start);
    } else {
        this.start_off = 10;
    }
    if(end.charAt(end.length - 1) === "%") {
        this.end = parseFloat(end) / 100;
    } else if((/px$/).test(end)) {
        this.end_off = parseInt(end);
    } else {
        this.end_off = -10;
    }

    if(data) {
        var _min = null;
        var _max = null;
        for(var i = 0; i < data.length; ++i) {
            if(_min === null || data[i].min < _min) _min = data[i].min;
            if(_max === null || data[i].max > _max) _max = data[i].max;
        }
    }
    var min = this.get("min") || "100%";
    var max = this.get("max") || "100%";
    if(min == null)
        this.min = _min || 0;
    else if(min.charAt(min.length - 1) === "%")
        this.min = (_min || 0) * parseFloat(min) / 100;
    else
        this.min = parseFloat(min);
    if(max == null)
        this.max = _max || 0;
    else if(max.charAt(max.length - 1) === "%")
        this.max = (_max || 0) * parseFloat(max) / 100;
    else
        this.max = parseFloat(max);
    
    if(this.min === this.max) ++this.max;
}

function Dataset(id) { this.id = id; this.continuous = true; }
Dataset.prototype.xml_update = function(node) {
    
    var subNode = node.firstChild;
    for (; subNode; subNode = subNode.nextSibling) {
        if (subNode.getAttribute('name') == this.id) break;
    }
    
    var counter = 0;
    var map = {};
    
    this.vals = [];
    this.txts = [];
    this.unique = [];
    var min = null;
    var max = null;
    for(var valNode = subNode.childNode; valNode; valNode.nextSibling) {
        if (valNode.nodeName != 'element') continue;
        var txt = valNode.getAttribute("text")
        this.txts[this.txts.length] = txt;
        
        var val;
        if(valNode.getAttribute("isnum") === "true" && this.continuous) {
            val = parseFloat(valNode.getAttribute("value"));
        } else {
            this.continuous = false;
            if(map[txt] == null) {
                this.unique[this.unique.length] = txt;
                map[txt] = counter;
                ++counter;
            }
            val = map[txt];
        }
        if(min === null || val < min) min = val;
        if(max === null || val > max) max = val;
        this.vals[this.vals.length] = val;
    }
    this.min = min;
    this.max = max;
}
Dataset.prototype.getLength = function() { return this.vals.length; }
Dataset.prototype.getVal  = function(i) { return this.vals[i]; }
Dataset.prototype.getText = function(i) { return this.txts[i]; }
Dataset.prototype.getUnique = function() { return this.unique; }

function Graph() { }
Graph.prototype = new Node();
Graph.prototype.getNextColor = function () {
    var c = this.Colors[this.nextColor % this.Colors.length];
    ++this.nextColor;
    return c;
}
Graph.prototype.getToolTip = function () {
    if(!this.tooltip) {
        this.tooltip = document.createElement("div");
        this.tooltip.style.position = "absolute";
        this.tooltip.style.width    = "auto";
        this.tooltip.style.height   = "auto";
        this.tooltip.style.zIndex   = 666;
        this.tooltip.style.backgroundColor = "FFFFCC";
        this.tooltip.style.borderWidth = 1;
        this.tooltip.style.borderColor = "black";
        this.tooltip.style.borderStyle = "solid";
        this.tooltip.style.padding = 3;
        document.body.insertBefore (this.tooltip, document.body.firstChild);
    }
    return this.tooltip;
}
Graph.prototype.addToLegend = function (legendItem) { if(this.Legend) this.Legend.add(legendItem); }
Graph.prototype.addDataset = function (id) {
    if(!id) return;
    if(!this.Datasets[id]) this.Datasets[id] = new Dataset(id);
    return this.Datasets[id];
}
Graph.prototype.getRange = function (id, type) {
    if(!id) id = "__default__" + type;
    if(!this.Ranges[id]) {
        this.Ranges[id] = new Range(this, id);
        this.Ranges[id].data = new Node();
    }
    return this.Ranges[id];
}

Graph.prototype.get = function (attr) { return this.data.get(attr); }
Graph.prototype.init = function (G, htmlRoot) {
    this.G = G;
    this.data = new Node();
    this.ById = {};
    this.HtmlRoot = htmlRoot;
    this.Datasets = {};
    this.Ranges = {};
    this.Colors = ["#3366CC", "#CC3333", "#33CC66", "#CCCC33", "#33CCCC", "#CC66CC", 
                   "#336699", "#993333", "#339966", "#999933"];
}
Graph.prototype.xml_init = function (mgr, elem) {
    this.init(mgr.G, elem.parentNode);
    this.data = new OrNode(new Node(), new XmlNode(elem)); //, defaultNode
    this.Mgr = mgr; // not used
    
    var usePlotarea = false;
    var children = []
    for(var i = 0; i < elem.childNodes.length; ++i) {
        var child = elem.childNodes[i];
        var tagName = CheckNamespace(child);
        if(!tagName) continue;
        
        if(tagName === "dataset") {
            var d = this.addDataset(child.getAttribute("id"));
            if(d) d.continuous = child.getAttribute("continuous") !== "false";
        } else if(tagName === "legend") {
            this.Legend = new Legend(this);
        } else if(tagName === "range") {
            var range = new Range(this);
            range.data = new OrNode(new XmlNode(child), this.data);
            var id = range.get("id");
            range.id = id;
            if(id) this.Ranges[id] = range;
        } else {
            var obj = createGraphObj(tagName);
            if(obj) {
                obj.xml_init(this, this, child);
                if(obj.needPlotarea && !usePlotarea) {
                    children = [];
                    usePlotarea = true;
                }
                if(obj.needPlotarea === usePlotarea) children[children.length] = obj;
            }
        }
    }
    if(usePlotarea) {
        var plotarea = new Plotarea();
        plotarea.init(this, this);
        plotarea.children = children;
        this.children = [plotarea];
    } else {
        this.children = children;
    }
    
    for(var id in this.Datasets) {
        mgr.Owner.Append({ 'Paint' : function() {} }, mgr.Name + '.' + id);
    }
}

Graph.prototype.xml_update = function(node) {
    this.data.values[0] = new XmlNode(node);
    for(var id in this.Datasets) this.Datasets[id].xml_update(node);
}
Graph.prototype.update = function() {
    this.nextColor = 0;
    if(this.Legend) this.Legend.clear();
    for(var i = 0; i < this.children.length; ++i) this.children[i].update1();
    for(var id in this.Ranges) this.Ranges[id].update();
    
    var top_padding = MeasureText("åg", 18).height * 2;
    var bottom_padding = 5;
    var height = this.HtmlRoot.offsetHeight - top_padding - bottom_padding;
    var left_padding = 5;
    var right_padding = 5;
    var width = this.HtmlRoot.offsetWidth - left_padding - right_padding;
    var size = { "x": left_padding, "y": top_padding, "width": width, "height": height };
    
    if(this.Legend) this.Legend.update(size);
    
    var len = this.children.length;
    for(var i = 0; i < len; ++i) {
        var s = { "x": Math.round(size.x + i * size.width / len), "y": size.y, "width": Math.round(size.width / len), "height": size.height };
        this.children[i].update2(s || size);
    }
}
Graph.prototype.draw = function() {
    var G = this.G;
    var div = this.HtmlRoot
    
    var width = div.offsetWidth;
    var height = div.offsetHeight;
    
    var r = document.createElement("div");
    r.style.position = "absolute";
    this.TextRoot = r;
    
    // Create VML root
    this.Root = G.CreateArea([width, height], r);

    // Chart Title
    var label = CreateText(this, this.get("label") || "???", 18);
    label.div.style.top = Math.round(label.height / 2) + "px";
    label.div.style.left = Math.round((width - label.width) / 2) + "px";
    
    for(var i = 0; i < this.children.length; ++i) this.children[i].draw();
    
    if(this.Legend) this.Legend.draw();
    
    if(this.lastDiv) {
        purge(this.lastDiv); // Fix IE memory leak
        div.replaceChild(r, this.lastDiv);
    } else {
        div.appendChild(r);
    }
    this.lastDiv = r;
}

var Plotarea = createGraphObjType("plotarea", false);
Plotarea.prototype.update1 = function () {
    for(var i = 0; i < this.children.length; ++i) this.children[i].update1();
}
Plotarea.prototype.update2 = function (size) {
    for(var i = 0; i < this.children.length; ++i) {
        if(this.children[i].type === "x-axis") {
            size.height -= MeasureText("åg", this.children[i].fontSize).height + 5;
            break;
        }
    }
    
    var l_max_w = 0;
    var r_max_w = 0;
    for(var i = 0; i < this.children.length; ++i) {
        if(this.children[i].type === "y-axis") {
            var w = this.children[i].updateStep(size) + 5;
            if(this.children[i].alt) {
                if(w > r_max_w) r_max_w = w;
            } else {
                if(w > l_max_w) l_max_w = w;
            }
        }
    }
    size.width -= l_max_w + r_max_w;
    size.x += l_max_w;
    
    this.x = size.x;
    this.y = size.y;
    this.width  = size.width;
    this.height = size.height;
    
    for(var i = 0; i < this.children.length; ++i) this.children[i].update2(size);
    
    // Update_Bars
    var range_set = {};
    for(var i = 0; i < this.children.length; ++i) {
        var child = this.children[i];
        if(child.type === "bar") {
            var bar = child;
            if(!range_set[bar.xrange.id]) range_set[bar.xrange.id] = [];
            var l = range_set[bar.xrange.id];
            l[l.length] = bar;
        } else if(child.type === "stack") {
            for(var j = 0; child.children.length; ++j) {
                if(child.children[j].type !== "bar") continue;
                var bar = child.children[j];
                if(!range_set[bar.xrange.id]) range_set[bar.xrange.id] = [];
                var l = range_set[bar.xrange.id];
                l[l.length] = child;
                break;
            }
        }
    }
    
    var bar_dist = 2;
    var cluster_dist = 7;
    
    for(var range_id in range_set) {
        var bars = range_set[range_id];
        var range = this.Graph.Ranges[range_id];
        var width = Math.round(range.getLength(this.width));
        
        var x_len;
        for(var i = 0; !x_len && i < bars.length; ++i) {
            if(bars[i].type === "bar") {
                x_len = this.Graph.Datasets[bars[i].get("x")].getLength();
            } else if(bars[i].type === "stack") {
                for(var j = 0; j < !x_len && bars[i].children.length; ++j) {
                    if(child.children[j].type !== "bar") continue;
                    x_len = this.Graph.Datasets[bars[i].children[j].get("x")].getLength();
                }
            } else { debugger; }
        }
        if(!x_len) { debugger; continue; }
        var y_len = bars.length;
        
        var left = width - ((x_len - 1) * cluster_dist + x_len * (y_len - 1) * bar_dist);
        var bar_width = Math.floor(left / (x_len * y_len));
        var cluster_size = bar_width * y_len + bar_dist * (y_len - 1);
        
        var range_offset = cluster_size / 2;
        range.start_off += range_offset;
        range.end_off   -= range_offset;
        
        for(var i = 0; i < bars.length; ++i) {
            if(bars[i].type === "bar") {
                var bar = bars[i];
                bar.width = bar_width;
                bar.offset = -cluster_size/2 + i*(bar_width+bar_dist);
            } else if(bars[i].type === "stack") {
                for(var j = 0; j < bars[i].children.length; ++j) {
                    if(child.children[j].type !== "bar") continue;
                    var bar = bars[i].children[j];
                    bar.width = bar_width;
                    bar.offset = -cluster_size/2 + i*(bar_width+bar_dist);
                }
            } else { debugger; }
        }
    }
}
Plotarea.prototype.draw = function () {
    var G = this.Graph.G;

    var div = this.Graph.TextRoot;
    var r = document.createElement("div");
    div.appendChild(r);
    
    r.style.position = "absolute";
    r.style.left = this.x + "px";
    r.style.top  = this.y + "px";
    r.style.width  = this.width + "px";
    r.style.height = this.height + "px";
    r.style.overflow = "hidden";
    r.style.backgroundColor = this.get("bgcolor") || "#EDECEC";
    
    this.area = G.CreateArea([this.width, this.height], r);
    
    for(var i = 0; i < this.children.length; ++i) { 
        this.children[i].draw(this); 
    }
}

function Update_Bars(graph) {
    if(graph.flip_orientation) {
        var Width = graph.area.height;
    } else {
        var Width = graph.area.width;
    }
    Width = Math.round(Math.abs(graph.data.x[0].range.end - graph.data.x[0].range.start) * Width);
    
    var x_len = Math.min(graph.data.x[0].length, graph.maxvalues);
    var bar_dist = 2;
    var cluster_dist = 7;

    var y_len = graph.data.y.length;
    if(graph.stacked) y_len = 1;
    
    var left = Width - ((x_len - 1) * cluster_dist + x_len * (y_len - 1) * bar_dist);
    var bar_width = Math.floor(left / (x_len * y_len));
    var cluster_size = bar_width * y_len + bar_dist * (y_len - 1);
    var range_offset = cluster_size / 2 / Width;
    graph.data.x[0].range.end   -= range_offset;
    graph.data.x[0].range.start += range_offset;
    
    for(var i = 0; i < graph.data.y.length; ++i) {
        var bar = graph.data.y[i];
        bar.width = bar_width;
        if(graph.stacked) {
            bar.offset = -bar_width/2;
        } else {
            bar.offset = -cluster_size/2 + i*(bar_width+bar_dist);
        }
        
    }
}

function Axis() {}
Axis.prototype = new GraphObj();
Axis.prototype.init = function(graph, data) {
    GraphObj.prototype.init.apply(this, arguments);
    graph.addDataset(this.get("data"));
    this.fontSize = 12;
    this.axistype = this.type.split("-")[0];
}
Axis.prototype.update1 = function() {
    this.range = this.Graph.getRange(this.get(this.axistype + "range"), this.axistype);
    this.offset = 0;
    this.values = this.get("data") && this.Graph.Datasets[this.get("data")];
    this.continuous = !this.values;
    this.alt = /^alt/.test(this.get("placement"));
    
    this.dist = parseInt(this.get("dist") || 3);
}

var YAxis = createGraphObjType("y-axis", true, Axis);
YAxis.prototype.updateStep = function (size) {
    this.min = this.range.min;
    this.max = this.range.max;
    if(this.min === this.max) { debugger; return; }
    
    var h = Math.round(this.range.getLength(size.height));

    var min = this.min;
    var max = this.max;
    
    var th = MeasureText("0", this.fontSize).height + this.dist;
    var step = 1;
    
    var zero = this.range.scale(0, h);
    while(true) {
        if(Math.abs(this.range.scale(step, h) - zero) >= th) break;
        if(Math.abs(this.range.scale(step * 2, h) - zero) >= th) {
            step *= 2;
            break;
        }
        if(Math.abs(this.range.scale(step * 5, h) - zero) >= th) {
            step *= 5;
            break;
        }
        step *= 10;
    }
    
    var max_w = 0;
    for (var val = min - (min % step - step) % step; val <= max; val += step) {
        var w = MeasureText(val, this.fontSize).width;
        if(w > max_w) max_w = w;
    }
    
    var label = this.get("axis-label");
    if(label) {
        var w = MeasureText(label, this.fontSize).width;
        if(w > max_w) max_w = w;
    }
    
    this.step = step;
    return max_w;
}
YAxis.prototype.draw = function (plotarea) {
    var G = this.Graph.G;

    if(this.continuous) {
        if(this.min <= 0 && 0 <= this.max) {
            var y0 = Math.round(this.range.scale(0, plotarea.height, true));
            G.CreateLine([0, y0], [plotarea.width, y0], null, plotarea.area);
        }
        Continuous_Axis(this.Graph, plotarea, this, Put_Y_AxisLabel);
    } else {
        Discrete_Axis(this.Graph, plotarea, this, Put_Y_AxisLabel);
    }
    var label = this.get("axis-label");
    if(label) {
        var xbase = plotarea.x;
        if(this.alt) xbase += plotarea.width
        
        var label = CreateText(this.Graph, label, this.fontSize);
        if(this.alt) {
            label.div.style.left = (xbase + 5) + "px";
        } else {
            label.div.style.left = (xbase - 5 - label.width) + "px";
}
        label.div.style.top = Math.round(plotarea.y - label.height - 5) + "px";
    }
}

var XAxis = createGraphObjType("x-axis", true, Axis);
XAxis.prototype.update2 = function (size) {
    this.min = this.range.min;
    this.max = this.range.max;
    
    var w = Math.round(this.range.getLength(size.width));
    var min = this.min;
    var max = this.max;
    
    if(this.continuous) {
        var tw = Math.max(MeasureText(min, this.fontSize).width, MeasureText(max, this.fontSize).width);
    } else {
        var tw = 1;
        var l = this.values.getUnique();
        for(var i = 0; i < l.length; ++i) {
            var tw = Math.max(tw, MeasureText(l[i], this.fontSize).width);
        }
    }
    tw += this.dist;
    
    var step = 1;
    
    var zero = this.range.scale(0, w);
    while(true) {
        if(Math.abs(this.range.scale(step, w) - zero) >= tw) break;
        if(Math.abs(this.range.scale(step * 2, w) - zero) >= tw) {
            step *= 2;
            break;
        }
        if(Math.abs(this.range.scale(step * 5, w) - zero) >= tw) {
            step *= 5;
            break;
        }
        step *= 10;
    }
    this.step = step;
}
XAxis.prototype.draw = function (plotarea) {
    var G = this.Graph.G;
    if(this.continuous) {
        if(this.min <= 0 && 0 <= this.max) {
            var x0 = Math.round(this.range.scale(0, plotarea.width));
            G.CreateLine([x0, 0], [x0, plotarea.height], null, plotarea.area);
        }
        Continuous_Axis(this.Graph, plotarea, this, Put_X_AxisLabel)
    } else {
        Discrete_Axis(this.Graph, plotarea, this, Put_X_AxisLabel);
    }
}

function Discrete_Axis(graph, plotarea, axis, putAxisLabel) {
    var txts = axis.values.getUnique();
    var step = axis.step || 1;
    for (var i = 0; i < txts.length; i += step) {
        putAxisLabel(graph, plotarea, i, txts[i], axis);
    }
}
function Continuous_Axis(graph, plotarea, axis, putAxisLabel) {
    var min = axis.min;
    var max = axis.max;
    
    var step = axis.step || 1;
    for (var val = min - (min % step - step) % step; val <= max; val += step) {
        putAxisLabel(graph, plotarea, val, val, axis);
    }
}
function Put_X_AxisLabel(graph, plotarea, val, text, axis) {
    var G = graph.G;
    var xbase = Math.round(axis.range.scale(val, plotarea.width));
    if(axis.showgrid && (val !== 0 || !axis.continuous)) {
        G.CreateLine([xbase, 0], [xbase, plotarea.width], axis.gridcolor, plotarea.area);
    }
    xbase += plotarea.x;

    var ybase = axis.offset + plotarea.y;
    if(!axis.alt) ybase += plotarea.height;
    G.CreateLine([xbase, ybase + (axis.alt ? -1 : 1)], [xbase, ybase], null, graph.Root);

    var fontSize = axis.fontSize;
    var label = CreateText(graph, text, fontSize);
    if(axis.alt) {
        label.div.style.top = (ybase - 5 - label.height) + "px";
    } else {
        label.div.style.top = (ybase + 5) + "px";
    }
    label.div.style.left = Math.round(xbase - label.width / 2) + "px";
}
function Put_Y_AxisLabel(graph, plotarea, val, text, axis) {
    var G = graph.G;
    var ybase = Math.round(axis.range.scale(val, plotarea.height, true));
    if(axis.showgrid && (val !== 0 || !axis.continuous)) {
        G.CreateLine([0, ybase], [plotarea.width, ybase], axis.gridcolor, plotarea.area);
    }
    ybase += plotarea.y;
    
    var xbase = plotarea.x;
    if(axis.alt) xbase += plotarea.width
    G.CreateLine([xbase + (axis.alt ? 1 : -1), ybase], [xbase, ybase], null, graph.Root);
    
    var fontSize = axis.fontSize;
    var label = CreateText(graph, text, fontSize);
    if(axis.alt) {
        label.div.style.left = (xbase + 5) + "px";
    } else {
        label.div.style.left = (xbase - 5 - label.width) + "px";
    }
    label.div.style.top = Math.round(ybase - label.height / 2) + "px";
}

var Stack = createGraphObjType("stack");
Stack.prototype.xml_init = function (graph, parent, elem) {
    GraphObj.prototype.xml_init.apply(this, arguments);
    
    var usePlotarea = false;
    var children = []
    for(var i = 0; i < elem.childNodes.length; ++i) {
        var child = elem.childNodes[i];
        var tagName = CheckNamespace(child);
        if(!tagName) continue;
        
        var obj = createGraphObj(tagName);
        if(obj) {
            obj.xml_init(graph, this, child);
            if(obj.needPlotarea && !usePlotarea) {
                children = [];
                usePlotarea = true;
            }
            if(obj.needPlotarea === usePlotarea) children[children.length] = obj
        }
    }
    this.needPlotarea = usePlotarea
    this.children = children;
}
Stack.prototype.update1 = function () {
    for(var i = this.children.length - 1; i >= 0; --i) this.children[i].update1();
}
Stack.prototype.update2 = function (size) {
    for(var i = 0; i < this.children.length; ++i) this.children[i].update2(size);
}
Stack.prototype.draw = function (plotarea) {
    var stack = [];
    for(var i = 0; i < this.children.length; ++i) {
        this.children[i].draw(plotarea, stack);
    }
}

function updateRange(obj, type) {
    var data = obj.get(type) && obj.Graph.Datasets[obj.get(type)];
    if(data) {
        obj[type + "range"] = obj.Graph.getRange(obj.get(type + "range"), type);
        obj[type + "range"].datasets[obj[type + "range"].datasets.length] = data;
    }
}

var Line = createGraphObjType("line", true);
Line.prototype.init = function (graph) {
    GraphObj.prototype.init.apply(this, arguments);
    var x = this.get("x");
    var y = this.get("y");
    var r = this.get("r");
    var a = this.get("angle");
    if(x && y) {
        graph.addDataset(x);
        graph.addDataset(y);
        this.draw = this.drawXY;
    } else if(r && a) {
        graph.addDataset(r);
        graph.addDataset(a);
        this.draw = this.drawRA;
    }
}
Line.prototype.update1 = function () {
    updateRange(this, "x");
    updateRange(this, "y");
    updateRange(this, "r");
    updateRange(this, "angle");
    this.color = this.get("color") || this.Graph.getNextColor();
    var label = this.get("label");
    if(label) this.Graph.addToLegend({ "text": label, "color": this.color });
}
Line.prototype.drawXY = function (plotarea) {
    var G = this.Graph.G;
    
    var xrange = this.xrange;
    var yrange = this.yrange;
    var xdata = this.Graph.Datasets[this.get("x")];
    var ydata = this.Graph.Datasets[this.get("y")];
    
    var points = [];
    for (var i = 0; i < xdata.getLength(); ++i) {
        var xpos = Math.round(xrange.scale(xdata.getVal(i), plotarea.width));
        var ypos = Math.round(yrange.scale(ydata.getVal(i), plotarea.height, true));
        points[points.length] = [xpos, ypos];
    }
    
    var line = G.CreatePolyLine(points, this.color, plotarea.area, 1.2);
    if(line) { //&& this.getBool("highlight")
        line.onmouseover = function() { line.strokeweight *= 2 }
        line.onmouseout  = function() { line.strokeweight /= 2 }
    }
    return line;
}

var Bar = createGraphObjType("bar", true);
Bar.prototype.init = function (graph) {
    GraphObj.prototype.init.apply(this, arguments);
    graph.addDataset(this.get("x"));
    graph.addDataset(this.get("y"));
}
Bar.prototype.update1 = function () {
    updateRange(this, "x");
    updateRange(this, "y");
    
    this.color = this.get("color") || this.Graph.getNextColor();
    var label = this.get("label");
    if(label) this.Graph.addToLegend({ "text": label, "color": this.color });
    
    this.width = 10;
    this.offset = -5; //- this.width/2
}
Bar.prototype.draw = function (plotarea, stack) {
    var G = this.Graph.G;
    var xrange = this.xrange;
    var yrange = this.yrange;
    var xdata = this.Graph.Datasets[this.get("x")];
    var ydata = this.Graph.Datasets[this.get("y")];

    for(var i = 0; i < xdata.getLength(); ++i) {
        var yval1 = ydata.getVal(i);
        var yval0 = (stack && stack[i]) || 0;
        yval1 += yval0;
        if(stack) stack[i] = yval1;
        
        var xpos   = Math.round(xrange.scale(xdata.getVal(i), plotarea.width));
        var ypos0  = Math.round(yrange.scale(yval0, plotarea.height, true));
        var ypos1  = Math.round(yrange.scale(yval1, plotarea.height, true));
        var x      = xpos + this.offset;
        var y      = Math.min(ypos0, ypos1);
        var width  = this.width;
        var height = Math.abs(ypos0  - ypos1);
        
        var r = G.CreateRect([x, y], [width, height], this.color, plotarea.area, true);
        
        AddToolTip(r, this, { "<dim>": xdata.getText(i), "<expr>": ydata.getText(i) }, "Dim: <dim><br>Expr: <expr>");
    }
}

function AddToolTip(target, obj, data, default_tooltip) {
    var tooltip_txt = obj.get("tooltip");
    
    if(target && tooltip_txt != null && tooltip_txt != "false") {
        if(typeof(tooltip_txt) !== "string" || tooltip_txt == "true") tooltip_txt = default_tooltip || "???";
        var tooltip = obj.Graph.getToolTip();
        
        target.onmouseover = function (event) {
            if (!event) event = window.event;
            
            for(var key in data) {
                tooltip_txt = tooltip_txt.replace(key, data[key]);
            }
            tooltip.innerHTML = tooltip_txt

            tooltip.style.left = (event.clientX + 5) + "px";
            tooltip.style.top  = (event.clientY + 5) + "px";
            tooltip.style.visibility = "visible";
        }
        target.onmousemove = target.onmouseover;
        target.onmouseout = function (event) {
            if (!event) event = window.event;
            
            tooltip.style.visibility = "hidden";
        }
    }
}

var Scatter = createGraphObjType("scatter", true);
Scatter.prototype.init = function (graph) {
    GraphObj.prototype.init.apply(this, arguments);
    graph.addDataset(this.get("x"));
    graph.addDataset(this.get("y"));
    graph.addDataset(this.get("size"));
    graph.addDataset(this.get("color"));
    graph.addDataset(this.get("key"));
}
Scatter.prototype.update1 = function () {
    updateRange(this, "x");
    updateRange(this, "y");
    
    this.colors = (this.get("color") && this.Graph.Datasets[this.get("color")]) || new ColorList(this.Graph);
    addKeysToLegend(this);
}
Scatter.prototype.draw = function (plotarea, stack) {
    var G = this.Graph.G;
    var xrange = this.xrange;
    var yrange = this.yrange;
    var xdata = this.Graph.Datasets[this.get("x")];
    var ydata = this.Graph.Datasets[this.get("y")];

    var size_data = this.get("size") && this.Graph.Datasets[this.get("size")];
    
    for(var i = 0; i < xdata.getLength(); ++i) {
        var xpos = Math.round(xrange.scale(xdata.getVal(i), plotarea.width));
        var ypos = Math.round(yrange.scale(ydata.getVal(i), plotarea.height, true));
        
        var size = size_data ? size_data.getVal(i) : 10;
        
        //var o = CreateVMLElement("oval", plotarea.area);
        var o = G.CreateElement("oval", plotarea.area);
        o.style.left   = Math.round(xpos - size/2) + "px";
        o.style.top    = Math.round(ypos - size/2) + "px";
        o.style.width  = size + "px";
        o.style.height = size + "px";
        o.fillcolor = this.colors.getText(i);
        
        AddToolTip(o, this, { "<x>": xdata.getText(i), "<y>": ydata.getText(i) }, "x: <x><br>y: <y>");
    }
}

function ColorList(graph) { this.colors = []; this.Graph = graph; }
ColorList.prototype.getText = function (i) {
    if(!this.colors[i]) this.colors[i] = this.Graph.getNextColor();
    return this.colors[i];
}
function addKeysToLegend(obj) {
    var keys = obj.get("key") && obj.Graph.Datasets[obj.get("key")];
    if(keys) {
        for(var i = 0; i < keys.getLength(); ++i) {
            obj.Graph.addToLegend({ "text": keys.getText(i), "color": obj.colors.getText(i) });
        }
    }
}

var Pie = createGraphObjType("pie", false);
Pie.prototype.init = function (graph) {
    GraphObj.prototype.init.apply(this, arguments);
    graph.addDataset(this.get("size"));
    graph.addDataset(this.get("color"));
    graph.addDataset(this.get("key"));
}
Pie.prototype.update1 = function () {
    this.colors = (this.get("color") && this.Graph.Datasets[this.get("color")]) || new ColorList(this.Graph);
    addKeysToLegend(this);
}
Pie.prototype.update2 = function (size) {
    this.radius  = Math.round(Math.min(size.width, size.height) / 2);
    this.centerx = size.x + this.radius;
    this.centery = size.y + this.radius;
}
Pie.prototype.draw = function () {
    var G = this.Graph.G;
    var root = this.Graph.Root;

    var radius  = this.radius;
    var centerx = this.centerx;
    var centery = this.centery;
    
    var inner_radius = this.inner_radius || 0;
    
    function each(start, end, color) {
        var startangle = Math.PI * 2 * start - Math.PI/2
        var endangle = Math.PI * 2 * end - Math.PI/2;
        
        var startx = Math.round(centerx + Math.cos(startangle) * radius);
        var starty = Math.round(centery + Math.sin(startangle) * radius);
        var endx = Math.round(centerx + Math.cos(endangle) * radius);
        var endy = Math.round(centery + Math.sin(endangle) * radius);
        
        var path = "wr " + (centerx - radius) + "," + (centery - radius) + "," + (centerx + radius) + "," + (centery + radius) + "," + startx + "," + starty + "," + endx + "," + endy;
        startx = Math.round(centerx + Math.cos(endangle) * inner_radius);
        starty = Math.round(centery + Math.sin(endangle) * inner_radius);
        endx = Math.round(centerx + Math.cos(startangle) * inner_radius);
        endy = Math.round(centery + Math.sin(startangle) * inner_radius);
        path += " at " + (centerx - inner_radius) + "," + (centery - inner_radius) + "," + (centerx + inner_radius) + "," + (centery + inner_radius) + "," + startx + "," + starty + "," + endx + "," + endy + " x e";
        
        var s = G.CreateShape(path, root.coordsize, color, root);
        s.style.width = root.style.width;
        s.style.height = root.style.height;
        return s;
    }
    DrawProportionalChart(this, each);
}

function DrawProportionalChart(obj, each) {
    var size_data = obj.Graph.Datasets[obj.get("size")];
    var color_data = obj.colors;
    var key_data = obj.get("key") && obj.Graph.Datasets[obj.get("key")];
    
    var sum = 0;
    for(var i = 0; i < size_data.getLength(); ++i) sum += size_data.vals[i];
    
    var start = 0;
    for(var i = 0; i < size_data.getLength(); ++i) {
        var diff = size_data.getVal(i);
//        if(key_data) {
//            for(var key = key_data.getText(i); (i < key_data.getLength() - 1) && key_data.getText(i+1) === k_val; ++i) {
//                diff += size_data.getVal(i+1);
//            }
//        }
        var end = start + diff / sum;
        
        var g = each(start, end, color_data.getText(i));
        start = end;
        
        AddToolTip(g, obj, { "<size>": size_data.getText(i) }, "size: <size>");
    }
}

function CreateText(graph, text, fontSize) {
    var attrib = {'position' : "absolute",
                  'fontSize' : fontSize + "px",
                  'width'    : "auto",
                  'height'   : "auto"};
    var label = document.createElement("div");
    for(var k in attrib) { label.style[k] = attrib[k]; }
    label.innerText = text;
    graph.TextRoot.appendChild(label);
    var res = MeasureText(text, fontSize);
    res.div = label;
    return res;
}

function ScaleFrom(v, start, end) { return (v - start) / (end - start);  }
function ScaleTo(v, start, end) { return v * (end - start) + start; }

function CheckNamespace(elem) {
    //return elem.tagUrn === "???";
    if(!elem.tagName) return false;
    var tagName = elem.tagName.toLowerCase();
    if((/^qvg:/i).test(tagName)) {
        return tagName.substr(4);
    } else if(elem.scopeName === "qvg") {
        return tagName;
    } else {
        return false;
    }
}

if(typeof(Qva) !== "undefined") {
    Qva.Mgr.graph = function (owner, elem, name, prefix) {
        if (!Qva.MgrSplit (this, name, prefix)) return;
        
        this.Owner = owner;
        this.Element = elem;
        this.Touched = false;
        this.Dirty = false;
        
        owner.Append (this, this.Name, 'value');
        owner.Append (this, this.Name, 'graph');
        owner.Append (this, this.Name, 'totalsize');
        
        //var type = "SVG";
        //this.G = SelectInitGraphics(type)
        this.G = SelectInitGraphics('SVG');
        //this.G = SelectInitGraphics();
        
        for(var i = 0; i < elem.childNodes.length; ++i) {
            var child = elem.childNodes[i];

            var tagName = CheckNamespace(child);
            if(!tagName) continue;
            if(tagName !== "chart") debugger;
            
            this.Graph = new Graph();
            this.Graph.xml_init(this, child);
            break;
        }
    }

    Qva.Mgr.graph.prototype.Paint = function(mode, node) {
        this.Touched = true;
        var element = this.Element;
        element.style.display = Qva.MgrGetDisplayFromMode(this, mode);
        if (element.style.display == 'none') return;

        this.Node = node;
        if(this.Graph) {
            this.Graph.xml_update(node);
            this.Graph.update();
            this.Graph.draw();
        } else {
            debugger
        }
    }
}
