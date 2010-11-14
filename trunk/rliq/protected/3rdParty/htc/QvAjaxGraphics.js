// VML
var Graphics = {
    'VML': {
        'CreateElement': function (type, parent) {
            var elem = document.createElement("v:" + type);
            elem.unselectable = "on";
            if(parent) parent.appendChild(elem);
            return elem;
        },
        'CreateShape': function (path, coordsize, color, parent) {
            var s = Graphics.VML.CreateElement("shape", parent);
            s.coordsize = coordsize;
            if(color) s.fillcolor = color;
            s.path = path;
            return s;
        },
        'CreateArea': function (size, parent) {
            var area = Graphics.VML.CreateElement("group", parent);
            area.style.width = size[0] + "px";
            area.style.height = size[1] + "px";
            area.coordsize = size.join(",");
            area.style.position = "absolute";
            return area;
        },
        'CreatePolygonObj': function (points, color, parent, pos, size) {
            var path = "m " + points[0][0] + "," + points[0][1] + " l";
            for(var i = 1; i < points.length; ++i) {
                path += " " + points[i][0] + " " + points[i][1];
            }
            path += " x e";
            var polygon = Graphics.VML.CreateShape(path, size.join(","), color, parent);
            polygon.style.left = pos[0] + "px";
            polygon.style.top = pos[1] + "px";
            polygon.style.width = size[0] + "px";
            polygon.style.height = size[1] + "px";
            polygon.style.position = "absolute";
            return polygon;
        },
        'CreatePolygon': function (points, color, parent) {
            return Graphics.VML.CreatePolygonObj(points, color, parent, [0,0], [parent.style.pixelWidth, parent.style.pixelHeight])
        },
        'CreateLine': function (from, to, color, parent, width) {
            var l = Graphics.VML.CreateElement("line", parent);
            l.from = from.join(",");
            l.to = to.join(",");
            if(color) l.strokecolor = color;
            if(width) l.strokeweight = width;
            return l;
        },
        'CreatePolyLine': function (points, color, parent, width) {
            var l = Graphics.VML.CreateElement("polyline", parent);
            l.points = points.join(" ");
            if(color) l.strokecolor = color;
            
            var f = Graphics.VML.CreateElement("fill");
            f.on = "false";
            l.appendChild(f);
            
            if(width) l.strokeweight = width;
            return l;
        },
        'CreateRect': function (pos, size, color, parent, border) {
            var r = Graphics.VML.CreateElement("rect", parent);
            r.fillcolor = color;
            r.style.left   = pos[0] + "px";
            r.style.top    = pos[1] + "px";
            r.style.width  = size[0] + "px";
            r.style.height = size[1] + "px";
            if(!border) Graphics.VML.CreateElement("stroke", r).on = "false";
            return r;
        },
        'FixPolygon': function (polygon, points, size, color) {
            var path = "m " + points[0][0] + "," + points[0][1] + " l";
            for(var i = 1; i < points.length; ++i) {
                path += " " + points[i][0] + " " + points[i][1];
            }
            path += " x e";
            polygon.path = path;

            polygon.coordsize = size.join(",");
            polygon.style.width = size[0] + "px";
            polygon.style.height = size[1] + "px";
        },
        'Init': function () {
            // create xmlns
            if (!document.namespaces["v"]) document.namespaces.add("v", "urn:schemas-microsoft-com:vml");
            // setup default css
            if(!Graphics.VML.styleSheet) {
                Graphics.VML.styleSheet = document.createStyleSheet()
                Graphics.VML.styleSheet.cssText = "v\\:*{behavior:url(#default#VML);display: inline-block;}";
            }
        },
        'styleSheet': null
    },

    'Canvas': {
        'CreateArea': function (size, parent) {
            var area = document.createElement("canvas");
            area.style.width = size[0] + "px";
            area.style.height = size[1] + "px";
            area.width = size[0];
            area.height = size[1];
            if(parent) parent.appendChild(area);
            return area;
        },
        'CreatePolygonObj': function (points, color, parent, pos, size) {
            var area = Graphics.Canvas.CreateArea(size, parent);
            area.style.position = "absolute";
            area.style.left = pos[0] + "px";
            area.style.top = pos[1] + "px";
            Graphics.Canvas.CreatePolygon(points, color, area);
            return area;
        },
        'CreatePolygon': function (points, color, parent) {
            var ctx = parent.getContext("2d");
            ctx.fillStyle = color || 'rgb(0,0,0)';
            ctx.beginPath();
            ctx.moveTo(points[0][0], points[0][1]);
            for(var i = 1; i < points.length; ++i) {
                ctx.lineTo(points[i][0], points[i][1]);
            }
            ctx.fill();
        },
        'CreateLine': function (from, to, color, parent, width) {
            var ctx = parent.getContext("2d");
            ctx.lineWidth = width || 1;
            ctx.strokeStyle = color || 'rgb(0,0,0)';
            ctx.beginPath();
            ctx.moveTo(from[0], from[1]);
            ctx.lineTo(to[0], to[1]);
            ctx.stroke();
        },
        'CreatePolyLine': function (points, color, parent, width) {
            if(points.length === 0) return;
            var ctx = parent.getContext("2d");
            ctx.lineWidth = width || 1;
            ctx.strokeStyle = color || 'rgb(0,0,0)';
            ctx.beginPath();
            ctx.moveTo(points[0][0], points[0][1]);
            for(var i = 1; i < points.length; ++i) {
                ctx.lineTo(points[i][0], points[i][1]);
            }
            ctx.stroke();
        },
        'CreateRect': function (pos, size, color, parent, border) {
            var ctx = parent.getContext("2d");
            if(border) {
                ctx.strokeStyle = border === true ? 'rgb(0,0,0)' : border;
                ctx.strokeRect(pos[0], pos[1], size[0], size[1]);
            }
            ctx.fillStyle = color;
            ctx.fillRect(pos[0], pos[1], size[0], size[1]);
        },
        'FixPolygon': function (polygon, points, size, color) {
            polygon.style.width = size[0] + "px";
            polygon.style.height = size[1] + "px";
            polygon.width = size[0];
            polygon.height = size[1];
            //clear
            var ctx = polygon.getContext("2d");
            ctx.clearRect(0, 0, size[0], size[1]);
            Graphics.Canvas.CreatePolygon(points, color, polygon);
        },
        'Init': function () {}
    },

    'SVG': {
        'CreateElement': function (type, parent) {
            var SVG_namespace = 'http://www.w3.org/2000/svg';
            try {
                var elem = document.createElementNS(SVG_namespace, type);
            } catch (e) {
                var elem = document.createElement(type);
                elem.setAttribute("xmlns", SVG_namespace);
            }
            
            if(parent) parent.appendChild(elem);
            return elem;
        },
        'CreateArea': function (size, parent) {
            var area = Graphics.SVG.CreateElement("svg", parent);
            area.setAttribute("width",  size[0] + "px");
            area.setAttribute("height", size[1] + "px");
            area.style.width = size[0] + "px";
            area.style.height = size[1] + "px";
            return area;
        },
        'CreatePolygonObj': function (points, color, parent, pos, size) {
            var area = Graphics.SVG.CreateArea(size, parent);
            area.style.position = "absolute";
            area.style.left = pos[0] + "px";
            area.style.top = pos[1] + "px";
            Graphics.SVG.CreatePolygon(points, color, area);
            return area;
        },
        'CreatePolygon': function (points, color, parent) {
            var polygon = Graphics.SVG.CreateElement("polygon", parent);
            polygon.setAttribute("points", points.join(","));
            polygon.setAttribute("fill", color || "black");
            return polygon;
        },
        'CreateLine': function (from, to, color, parent, width) {
            var line = Graphics.SVG.CreateElement("line", parent);
            line.setAttribute("x1", from[0] + "px");
            line.setAttribute("y1", from[1] + "px");
            line.setAttribute("x2", to[0] + "px");
            line.setAttribute("y2", to[1] + "px");
            line.setAttribute("stroke", color || "black");
            line.setAttribute("strokeWidth", width);
            return line;
        },
        'CreatePolyLine': function (points, color, parent, width) {
            var line = Graphics.SVG.CreateElement("polyline", parent);
            line.setAttribute("points", points.join(","));
            line.setAttribute("stroke", color || "black");
            line.setAttribute("strokeWidth", width);
            line.setAttribute("fill", 'none');
            return line;
        },
        'CreateRect': function (pos, size, color, parent, border) {
            var rect = Graphics.SVG.CreateElement("rect", parent);
            rect.setAttribute("x", pos[0] + "px");
            rect.setAttribute("y", pos[1] + "px");
            rect.setAttribute("width",  size[0] + "px");
            rect.setAttribute("height", size[1] + "px");
            rect.setAttribute("fill", color || "black");
            if(border) { rect.setAttribute("stroke", "black"); }
            return rect;
        },
        'FixPolygon': function () { debugger; },
        'Init': function () {}
    }
};

var useragent = "" + window.window.navigator.userAgent;
if(useragent.indexOf ('MSIE') != -1) {
    var SelectGraphics = function () { return Graphics.VML; }
} else {
    var SelectGraphics = function (type) {
        if(Graphics[type] && type !== 'VML') return Graphics[type];
        return Graphics.Canvas;
    }
}
function SelectInitGraphics(type) {
    var g = SelectGraphics(type);
    g.Init();
    return g;
}

//var UseSVG;
//var useragent = "" + window.window.navigator.userAgent;
//if(useragent.indexOf ('MSIE') != -1) {
//    var CreateArea = CreateArea_VML;
//    var CreatePolygonObj = CreatePolygonObj_VML;
//    var CreatePolygon = CreatePolygon_VML;
//    var CreateLine = CreateLine_VML;
//    var CreatePolyLine = CreatePolyLine_VML;
//    var CreateRect = CreateRect_VML;
//    var FixPolygon = FixPolygon_VML;
//    var Draw_Init = Init_VML;
//} else if(UseSVG === true) {
//    var CreateArea = CreateArea_SVG;
//    var CreatePolygonObj = CreatePolygonObj_SVG;
//    var CreatePolygon = CreatePolygon_SVG;
//    var CreateLine = CreateLine_SVG;
//    var CreatePolyLine = CreatePolyLine_SVG;
//    var CreateRect = CreateRect_SVG;
//    var Draw_Init = Init_SVG;
//} else {
//    var CreateArea = CreateArea_Canvas;
//    var CreatePolygonObj = CreatePolygonObj_Canvas;
//    var CreatePolygon = CreatePolygon_Canvas;
//    var CreateLine = CreateLine_Canvas;
//    var CreatePolyLine = CreatePolyLine_Canvas;
//    var CreateRect = CreateRect_Canvas;
//    var FixPolygon = FixPolygon_Canvas;
//    var Draw_Init = Init_Canvas;
//}

function MeasureText(text, fontSize) {
    if(!MeasureText.div) {
        MeasureText.div = document.createElement("div");
        MeasureText.div.style.position = "absolute";
        MeasureText.div.style.width = "auto";
        MeasureText.div.style.height = "auto";
        MeasureText.div.style.visibility = "hidden";
        document.body.insertBefore (MeasureText.div, document.body.firstChild);
    }
    MeasureText.div.style.fontSize = fontSize;
    MeasureText.div.innerText = text;
    return { 'width': MeasureText.div.offsetWidth, 'height': MeasureText.div.offsetHeight };
}
MeasureText.div = null;
