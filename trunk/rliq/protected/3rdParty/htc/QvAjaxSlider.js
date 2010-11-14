
if (!Qva.Mgr) Qva.Mgr = {}
Qva.Mgr.slider = function (owner, elem, name, prefix) {

    if (!Qva.MgrSplit (this, name, prefix)) return;
    this.Element = elem;
    this.Touched = false;
    owner.AddManager (this);
    
    this.G = SelectInitGraphics();
}
Qva.Mgr.slider.prototype.Lock = Qva.LockDisabled;
Qva.Mgr.slider.prototype.Unlock = Qva.UnlockDisabled;

Qva.Mgr.slider.prototype.Paint = function(mode, node) {

    this.Touched = true;
    var element = this.Element;
    element.style.display = Qva.MgrGetDisplayFromMode(this, mode);
    
    var newheight = parseInt (getContentMaxHeight (element));
    if (! isNaN (newheight)) {
        element.parentNode.style.height = newheight + "px";
    }
    
    for(var child = node.firstChild; child; child = child.nextSibling) {
        if (child.nodeName == "layout") {
            AvqMgr_CreateSlider(this, element, child, true);
        } else if (child.nodeName == "choice") {
            if(!this.Choice) this.Choice = [];
            var index = 0;
            for(var elem = child.firstChild; elem; elem = elem.nextSibling) {
                this.Choice[index++] = elem.getAttribute("text");
            }
            this.Choice.length = index;
        }
    }
}

Qva.Mgr.slider.tooltip = null;

function AvqMgr_CreateSlider(mgr, div, node, swap) {
    var dir = node.getAttribute("orientation") || "horizontal";
    var slider_color = node.getAttribute("color") || "#AABCDD";
    var tics = null;
    var thumb = null;
    var arrows = null;
    for(var child = node.firstChild; child; child = child.nextSibling) {
        if (tics == null && child.nodeName == 'tics') tics = child;
        if (thumb == null && child.nodeName == "thumb") thumb = child;
        if (arrows == null && child.nodeName == "arrows") arrows = child;
    }
    var max = parseFloat(node.getAttribute("max"));
    var min = parseFloat(node.getAttribute("min"));
    var current_min = parseFloat(node.getAttribute("current_min"));
    var current_max = parseFloat(node.getAttribute("current_max"));
    var userange = node.getAttribute("selectionrange") == "multi";
    var selectionvalid = node.getAttribute("selectionvalid") == "true";
    if (min == max) return;
    
    var step = node.getAttribute("step");
    if(step) step = parseFloat(step);
    
    var top = div.offsetTop;
    var width = div.clientWidth;
    var objectframeNode = div.parentNode;
    var height = objectframeNode.clientHeight - top;
    var fontSize = objectframeNode.style.fontSize;
    
    var length = dir == "horizontal" ? width : height;
    var offset = 19;
    
    var _left =   dir == "horizontal" ? "left"   : "top";
    var _top =    dir == "horizontal" ? "top"    : "left";
    var _width =  dir == "horizontal" ? "width"  : "height";
    var _height = dir == "horizontal" ? "height" : "width";
    
    var _line =        dir == "horizontal" ? height - 15 : 12;
    var _textAlign =   dir == "horizontal" ? "center"    : "left";
    var _textTop =     dir == "horizontal" ? _line - 44  : _line + 28;

    var _oPos = dir == "horizontal" ? "left"    : "top";
    var _mPos = dir == "horizontal" ? "clientX" : "clientY";
    
    var _thumb = dir == "horizontal"
                 ? [[0,4], [4,0], [8,4], [8,12], [0,12]]
                 : [[0,0], [8,0], [12,4] ,[8,8], [0,8]];
    var fix_point = dir == "horizontal" 
                    ? function (x, y) { return [x, y]; }
                    : function (y, x) { return [x, y]; };
    
    var sign = dir == "horizontal" ? -1  : 1;
    var _ticStart = _line + sign * 10;
    var _ticMajorEnd = _ticStart + sign * 13;
    var _ticMinorEnd = _ticStart + sign * 7;
    
    var root = document.createElement("div");
    root.style.width = width + "px";
    root.style.height = height + "px";
    var slider = mgr.G.CreateArea([width, height], root);
    
    var l = mgr.G.CreateLine(fix_point(offset, _line), fix_point(length - offset, _line), slider_color, slider, 4);
    if(!l) l = slider;
    
    var tics_space = length - 2 * (offset + 5);
    var valueCount = max - min + 1;
    var dval = tics_space / (valueCount - 1);
    if(tics) {
        var color = tics.getAttribute("color") || "#A4A4A4";
        function create_line(pos, start, end) { mgr.G.CreateLine(fix_point(pos, end), fix_point(pos, start), color, slider); };
        
        var labelCount = parseInt(tics.getAttribute("labels"));
        var majorCount = parseInt(tics.getAttribute("major"));
        if(majorCount < 2) majorCount = 0;
        var minorCount = parseInt(tics.getAttribute("minor"));
        if(minorCount < 0) minorCount = 0;
        
        var iv = 0;
        var labels = tics.getElementsByTagName("label");
        
        var dmajor = tics_space / (majorCount - 1);
        for(var imajor = 0; imajor < majorCount; ++imajor) {
            var labelnode = labels[iv++];
            var pos;
            if (labelnode.getAttribute("index")) {
                pos = Math.round (offset + 5 + parseInt (labelnode.getAttribute("index")) * dval);
            } else {
                pos = Math.round (offset + 5 + imajor * dmajor);
            }
            create_line(pos, _ticStart, _ticMajorEnd, color);
            if (labelCount > 0 && imajor % labelCount == 0 && labelnode) {
                var text = labelnode.getAttribute("text");
                
                var size = MeasureText(text, fontSize);
                var attrib = {"position": "absolute",
                              "fontSize": fontSize,
                              "overflow": "hidden",
                              "color": color,
                              "fontFamily": div.style.fontFamily,
                              "textAlign": _textAlign };
                attrib[_left] = (dir == "horizontal" ? Math.round(pos - size.width/2) : Math.floor(pos - size.height/2)) + "px";
                attrib[_top] = _textTop + "px";
                attrib["width"] = size.width + "px";
                attrib["height"] = size.height + "px";
                
                var label = document.createElement("div");
                for(var k in attrib) { label.style[k] = attrib[k]; }
                label.innerText = text;
                root.appendChild(label);
            }
            if(imajor == majorCount - 1) continue;
            
            var dminor = dmajor / (minorCount + 1);
            for(var iminor = 1; iminor <= minorCount; ++iminor) {
                var pos2 = Math.round(pos + iminor * dminor);
                create_line(pos2, _ticStart, _ticMinorEnd, color);
            }
        }
    }
    if(Qva.Mgr.slider.tooltip === null) {
        Qva.Mgr.slider.tooltip = document.createElement("div");
        Qva.Mgr.slider.tooltip.style.position = "absolute";
        Qva.Mgr.slider.tooltip.style.width    = "auto";
        Qva.Mgr.slider.tooltip.style.height   = "auto";
        Qva.Mgr.slider.tooltip.style.zIndex   = 666;
        Qva.Mgr.slider.tooltip.style.backgroundColor = "#FFFFCC";
        Qva.Mgr.slider.tooltip.style.borderWidth = "1px";
        Qva.Mgr.slider.tooltip.style.borderColor = "black";
        Qva.Mgr.slider.tooltip.style.borderStyle = "solid";
        Qva.Mgr.slider.tooltip.style.padding = "3px";
        document.body.insertBefore (Qva.Mgr.slider.tooltip, document.body.firstChild);
    }
    Qva.Mgr.slider.tooltip.style.visibility = "hidden";

    var thumb_color = thumb.getAttribute("color") || "#3796FF";
    var thumb_pos = fix_point(30, _line - 6);
    var thumb_size = fix_point(8, 12);
    
    var thumb = mgr.G.CreatePolygonObj (_thumb, thumb_color, root, thumb_pos, thumb_size);
    thumb.style.cursor = "pointer";
    function val_to_pos(val) {
        if(max == min) return 0;
        return (thumb.maxVal - thumb.minVal) * (val - min) / (max - min) + thumb.minVal; 
    }
    function pos_to_val(pos) {
        return (pos - thumb.minVal) * (max - min) / (thumb.maxVal - thumb.minVal) + min;
    }
    function fix_thumb(min, max, end, done) {
        thumb._min = min;
        thumb._max = max;
        
        var min_range = 8;
        var range = Math.round(val_to_pos(max) - val_to_pos(min));
        
        if(range > min_range) {
            _fix_thumb(min, range);
        } else if(end == "min") {
            _fix_thumb(max, 0);
            if(done) thumb._min = thumb._max;
        } else {
            _fix_thumb(min, 0);
            if(done) thumb._max = thumb._min;
        }
    }
    function _fix_thumb(min, range) {
        thumb.style[_oPos] = Math.round(val_to_pos(min)) + "px";
        if(range > 0) {
            var path = [[4,0], [8,4], [range,4], [range+4,0], [range+4,12], [range,8], [8,8], [4,12]];
            if(dir != "horizontal") {
                for(var i = 0; i < path.length; ++i) {
                    var temp = path[i][0];
                    path[i][0] = path[i][1];
                    path[i][1] = temp;
                }
            }
            mgr.G.FixPolygon(thumb, path, fix_point(range+8, 12), thumb_color);
        } else {
            mgr.G.FixPolygon(thumb, _thumb, thumb_size, thumb_color);
        }
    }
    if(userange) {
        thumb.onmouseover = function(event) {
            var diff = 3;
            
            if(!event) event = window.event;
            var pos = Qva.GetPageCoords(thumb);
            
            var i = dir === "horizontal" ? "x" : "y";
            var l = thumb._min == thumb._max ? 0 : 8;
            
            if(Math.abs(pos[i] - event[_mPos]) < diff) {
                document.body.style.cursor = dir === "horizontal" ? "w-resize" : "n-resize";
            } else if(Math.abs(pos[i] + (parseFloat(thumb.style[_width]) - l) - event[_mPos]) < diff) {
                document.body.style.cursor = dir === "horizontal" ? "e-resize" : "s-resize";
            } else {
                document.body.style.cursor = "default";
            }
            
        }
        thumb.onmousemove = thumb.onmouseover;
        thumb.onmouseout = function() { document.body.style.cursor = "default" }
    }
    
    thumb._setPos = function(done) {
        var val = [thumb._min, thumb._max];
        
        if(step) {
            val [0] = (val [0] + step / 2) - ((val [0] + step / 2) % step);
            val [1] = (val [1] + step / 2) - ((val [1] + step / 2) % step);
        }
        
        if(done) {
            var setvalue = thumb.lastValue != val[0] || thumb.lastValue != val[1];
            Qva.Mgr.slider.tooltip.style.visibility = "hidden";
            if(userange) {
                val = val [0] + ":" + val [1];
            } else {
                val = val [0];
            }
            if(setvalue) mgr.PageBinder.Set (mgr.Name, 'value', val, true);
        } else {
            var pos = Qva.GetPageCoords(thumb);
            
            val[0] = Math.round(val[0] * 100) / 100;
            val[1] = Math.round(val[1] * 100) / 100;
            
            if(userange) {
                if(mgr.Choice) {
                    Qva.Mgr.slider.tooltip.innerHTML = "Min: " + (mgr.Choice[val[0]] || "?") + "</br>" + "Max: " + (mgr.Choice[val[1]] || "?");
                } else {
                    Qva.Mgr.slider.tooltip.innerHTML = "Min: " + val[0] + "</br>" + "Max: " + val[1];
                }
            } else {
                Qva.Mgr.slider.tooltip.innerText = mgr.Choice ? (mgr.Choice[val[0]] || "?") : val[0];
            }
            if(dir == "horizontal") {
                Qva.Mgr.slider.tooltip.style.left = (pos.x - 5) + "px";
                Qva.Mgr.slider.tooltip.style.top  = (pos.y - 50) + "px";
            } else {
                Qva.Mgr.slider.tooltip.style.left = (pos.x + 50) + "px";
                Qva.Mgr.slider.tooltip.style.top  = (pos.y - 5) + "px";
            }
            Qva.Mgr.slider.tooltip.style.visibility = "visible";
        }
    }
    thumb.setPos = function(npos, done) {
        if (npos < thumb.minVal) npos = thumb.minVal;
        if (npos > thumb.maxVal) npos = thumb.maxVal;
        var m = pos_to_val(npos);
        fix_thumb(m, m, "max", done);
        thumb._setPos(done);
    }
    thumb.onmousedown = function(event) {
        if (!event) event = window.event;
        switch(document.body.style.cursor) {
            case "w-resize":
            case "n-resize":
                thumb.setPos = function(npos, done) {
                    var t_max = val_to_pos(thumb._max);
                    if(t_max < npos) npos = t_max;
                    if(npos < thumb.minVal) npos = thumb.minVal;
                    fix_thumb(pos_to_val(npos), thumb._max, "min", done);
                    thumb._setPos(done)
                }
                break;
            case "e-resize":
            case "s-resize":
                var t_len = parseFloat(thumb.style[_width]) - 8;
                thumb.setPos = function(npos, done) {
                    npos += t_len;
                    var t_min = val_to_pos(thumb._min);
                    if(npos < t_min) npos = t_min;
                    if(thumb.maxVal < npos) npos = thumb.maxVal;
                    fix_thumb(thumb._min, pos_to_val(npos), "max", done);
                    thumb._setPos(done)
                }
                break;
            default:
                var t_len = parseFloat(thumb.style[_width]) - 8;
                var range = thumb._max - thumb._min;
                thumb.setPos = function(npos, done) {
                    var t_end = thumb.maxVal - t_len;
                    if (npos < thumb.minVal) npos = thumb.minVal;
                    if (npos > t_end) npos = t_end
                    var m = pos_to_val(npos);
                    fix_thumb(m, m + range, "max", done);
                    thumb._setPos(done);
                }
                break;
        }
        var x = parseFloat(thumb.style[_oPos]);
        thumb.mouseZero = event[_mPos] - x;
        thumb._setPos(false);
        
        var old_onmousemove = document.onmousemove;
        var old_onmouseup = document.onmouseup;
        
        function SlideEnd(event) {
            document.onmousemove = old_onmousemove;
            document.onmouseup   = old_onmouseup;
            
            if (!event) event = window.event;
            var npos = event[_mPos] - thumb.mouseZero;
            thumb.setPos(npos, true);
        }
        function SlideDrag(event) {
            if (!event) event = window.event;
            var npos = event[_mPos] - thumb.mouseZero;
            thumb.setPos(npos)
        }
        document.onmousemove = SlideDrag;
        document.onmouseup   = SlideEnd;
    }
    
    thumb.minVal = 17 + 3;
    thumb.maxVal = parseFloat(thumb.parentNode.style[_width]) - parseFloat(thumb.style[_width]) - 17 - 3;
    thumb.lastValue = current_min == current_max ? current_min : -1;
    if(selectionvalid) {
        fix_thumb(current_min, current_max, "max", true);
    } else {
        thumb.style.visibility = "hidden";
        l.onmouseover = function (event) {
            var old_onmousedown = thumb.onmousedown;
            var old_onmouseover = thumb.onmouseover;
            var old_onmousemove = thumb.onmousemove;
            var old_onmouseout = thumb.onmouseout;
            
            thumb.onmouseover = null;
            thumb.onmousedown = function(event) {
                thumb.style.visibility = "visible";
                thumb.onmouseout = null;
                thumb.onmousemove = null;
                l.onmouseover = null;
                l.onmousemove = null;
                l.onmouseout = null;
                
                thumb.onmousedown = old_onmousedown;
                thumb.onmouseover = old_onmouseover;
                thumb.onmousemove = old_onmousemove;
                thumb.onmouseout = old_onmouseout;
                if(userange) document.body.style.cursor = "e-resize";
                old_onmousedown(event);
            };
            thumb.onmouseout = function() {
                thumb.style.visibility = "hidden";
                thumb.onmouseout = null;
                thumb.onmousemove = null;
                l.onmousemove = null;
                l.onmouseout = null;
                Qva.Mgr.slider.tooltip.style.visibility = "hidden";
            };
            thumb.onmousemove = function(event) {
                if(!event) event = window.event;
                var x = dir == "horizontal" ? "x" : "y";
                var _scroll = dir == "horizontal" ? "scrollLeft" : "scrollTop";
                var npos = event[_mPos] - (Qva.GetPageCoords(div)[x] + 4 - document.body[_scroll]);
                thumb.setPos(npos, false);
            };
            l.onmouseout = function(event) {
                if(!event) event = window.event;
                if(!event.toElement) { debugger; }
                if(event.toElement !== thumb) {
                    thumb.onmouseout();
                }
            };
            l.onmousemove = thumb.onmousemove;
            thumb.onmousemove(event);
            thumb.style.visibility = "visible";
        };
    }
    
    function create_arrow(side, color) {
        var x0 = side == "start" ? 0 : 10;
        var x2 = side == "start" ? 10 : 0;
        
        var points = [fix_point(x0,0), fix_point(x2,6), fix_point(x0,12)];
        var pos = fix_point(side == "start" ? (length - 15) : 5, _line - 6);
        var size = fix_point(10,12);
        var arrow = mgr.G.CreatePolygonObj(points, color, root, pos, size);

        var diff = userange ? 1 : (step != null ? step : 1);
        if(side !== "start") diff = -diff;
        arrow.onmousedown = function () { 
            if(thumb._min != -1 && thumb._min + diff >= min && thumb._max + diff <= max) {
                thumb._min += diff;
                thumb._max += diff;
                thumb._setPos(true);
            }
        };
    };
    
    if(arrows) {
        var arrow_color = arrows.getAttribute("color") || "#0080C0";
        create_arrow("start", arrow_color);
        create_arrow("end", arrow_color);
    }
    
    if(swap && div.firstChild) {
        div.replaceChild(root, div.firstChild);
    } else {
        div.appendChild(root);
    }
}

// End Slider
