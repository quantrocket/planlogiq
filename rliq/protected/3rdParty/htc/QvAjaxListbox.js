// Build $BuildVersion$

if (!Qva.Mgr) Qva.Mgr = {} 

Qva.Mgr.listbox = function (owner, elem, name, prefix, condition) {
    this.Name = Qva.MgrMakeName (name || '', prefix);
    this.Condition = condition;
    owner.AddManager(this);
    this.LeftButton = owner.LeftButton;
    this.Element = elem;
    elem.AvqMgr = this;
    this.TableScan (owner, prefix);
    
    owner.Append (this, this.PageName, 'choice');
    //owner.Append (this, this.Name, 'choice');
    //owner.Append ({'Paint': function () {}}, this.PageName + ".CH01");
//    elem.style.height = "100%";
//    this.Temp = elem.clientHeight;
}

Qva.Mgr.listbox.prototype.ColMgr = function (index, cell) {
    this.Index = index;
    if (cell != null) {
        this.ClassName = cell.className;
        this.Align = cell.align;
        this.Html = cell.innerHTML;
    }
}

Qva.Mgr.listbox.prototype.CellObject = function (optval, value) {
    this.val = optval ? optval : "";
    var index = value.getAttribute ("value");
    if (index) {
        this.intval = index;
        this.selected = value.getAttribute ("selected") == "yes";
        this.deselected = value.getAttribute ("deselected") == "yes";
		this.locked = value.getAttribute ("locked") == "yes";
    }
    this.disabled = value.getAttribute ("mode") == "disabled";
    this.style = value.getAttribute ("style");
    this.isnum = value.getAttribute ("isnum") == "true";
    this.icons = value.selectNodes ("icon");
    this.subcell = value.getAttribute ("subcell");
    this.first = value.getAttribute ("first");
    var selecttype = value.getAttribute ("selecttype");
    if (selecttype) {
        this.selecttype = selecttype;
        this.selectsource = value.getAttribute ("selectsource") == "true";
    }
}

Qva.Mgr.listbox.prototype.TableScan = function (owner, prefix) {
    var element = this.Element;
    this.Selected = new Array ();
    
    this.PageName = this.Name;
    this.PageOffset = 0;
    this.PageIncr = 0;
    this.PageSize = 0; // unlimited
    this.TotalSize = 0;
    this.Search = null;
    this.PageHandler = 'client';
    this.SearchName = this.Name;
    this.Searchable = false;
    this.IsAsync = false;
    this.TableLimit = owner.TableLimit;
    this.InlineStyle = owner.InlineStyle;
    if (element.getAttribute ('AvqStyle')) {
        this.InlineStyle = element.getAttribute ('AvqStyle') == "true";
    }
    var async = element.getAttribute ('AvqAsync');
    
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
            this.PageSize = 100;
        }
        this.IsAsync = true;
    }
    if (this.PageIncr == 0) {
        this.PageIncr = this.PageSize > 0 ? this.PageSize : 100;
    }
    if (this.IsAsync) {
        owner.Append (this, this.PageName, 'pageoffset');
        owner.Append (this, this.PageName, 'pagesize');
        owner.Append (this, this.PageName, 'totalsize');
        owner.Append (this, this.PageName, 'listbox');
        owner.SetInitial (this.PageName, 'pagesize', this.PageSize);
        owner.SetInitial (this.PageName, 'pageoffset', 0);
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
    if (body.rows.length < 1) body.appendChild(document.createElement("tr"));
    
    var row = body.rows [0];
    row.rix = 0;
    
    var flag = null;
    this.Lines = new Array ();
    this.RowNumbers = new Array ();
    this.Style = new Array ();
    this.BorderStyle = new Array ();
    this.IsPainted = new Array ();
    this.RowClassNames = new Array ();
    this.ColList = new Array ();
    this.ColDict = {};
    this.ChoiceIx = -1;
    this.FlagIx = -1;
    this.CountIx = -1;
    
    this.RowClassNames[0] = body.rows [0].className;
    var stripes = element.getAttribute ('AvqStripeClasses');
    if (stripes != null) {
        this.RowClassNames = this.RowClassNames.concat(('' + stripes).split(/\s/));
    }
    if (this.Search != null) {
        this.Search.onkeydown = AvqAction_Search_KeyDown;
        this.Search.onkeyup = AvqAction_Search_KeyUp;
        this.Search.AvqMgr = mgr;
    }
    if (this.PageIncr > 0) {
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

Qva.Mgr.listbox.prototype.ParentScroll = function() {
    var mgr = this.AvqMgrForScroll;
    if (mgr == null) mgr = element.document.body.AvqMgrForScroll; // daft workaround for daft design... (onscroll on body behaves strangely)
    if (mgr == null) return;
    if (mgr.HeaderId != null) {
        var header = document.getElementById (mgr.HeaderId);
        header.parentNode.scrollLeft = this.scrollLeft;
    }
    Qva.QueuePostPaintMessage (mgr);
}

Qva.Mgr.listbox.prototype.Lock = Qva.NoAction;
Qva.Mgr.listbox.prototype.Unlock = Qva.NoAction;

Qva.Mgr.listbox.prototype.FixCol = function (mode, node, name, partial) {
    var bodyParent = this.Body.parentNode;
    var body = this.Body.cloneNode (false);
    var row = document.createElement ("tr");
    body.appendChild(row);
    row.rix = 0;
    
    this.ColList = [];
    
    var cix = 0;
    for (; cix < this.Width; ++cix) {
        var cell = row.cells[cix];
        if(cell == null) cell = row.appendChild(document.createElement("td"));
        var colmgr = new this.ColMgr (cix, cell);
        
        colmgr.Cmd = 'edit'
        colmgr.Attr = 'text';
        //colmgr.ToolTip = ?;
        
        this.ColList [this.ColList.length] = colmgr;
    }
    
    bodyParent.replaceChild (body, this.Body);
    this.Body = body;
    this.RowNumbers = [];
}

Qva.Mgr.listbox.prototype.Paint = function (mode, node, name, partial) {
    this.Touched = true;
    if (node.selectSingleNode ("menu")) return;
    
    this.FinalFix = false;
    this.Searchable = node.getAttribute ("searchable") == "true";
    var element = this.Element;
    
    element.disabled = (mode != 'e');
    element.style.display = Qva.MgrGetDisplayFromMode (this, mode);
    
    this.Dirty = true;
    
    var stylenode = node.selectSingleNode ('style');
    if (stylenode) {
        var stylenodes = stylenode.selectNodes ('style');
        for (var istyle = 0; istyle < stylenodes.length; istyle++) {
            this.Style [istyle] = new this.StyleObject (stylenodes [istyle]);
        }
    }
    var borderstylenode = node.selectSingleNode ('borderstyle');
    if (borderstylenode) {
        var stylenodes = borderstylenode.selectNodes ('borderstyle');
        for (var istyle = 0; istyle < stylenodes.length; istyle++) {
            this.BorderStyle [istyle] = new this.BorderStyleObject (stylenodes [istyle]);
        }
    }
    if (node.getAttribute ("menu") == "true") this.Menu = true;
    
    this.AndMode = node.getAttribute ("andmode") == "true";
    if (this.PageName == "" || this.PageName == name) {
        this.ChunkOffset = parseInt (node.getAttribute ("pageoffset"));
        this.ChunkSize = parseInt (node.getAttribute ("pagesize"));
        this.TotalSize = parseInt (node.getAttribute ("totalsize"));
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
//                avqShowMessage (this.Name + ' <> ' + this.ChunkOffset);
                this.Lines.length = 0;
                for (var ix = 0; ix < this.IsPainted.length; ix ++) {
                    this.IsPainted [ix] = false;
                }
                // or this.IsPainted.length = 0
                // or this.IsPainted = [];
                
                if (this.ChunkOffset == 0) {
                    var scrollParent = element.parentNode;
                    scrollParent.scrollTop = 0;
                }
            }
            if (this.TotalSize == 0) return;
        }
    }
    
    var layoutnode = node.selectSingleNode ('layout');
    if(!layoutnode) return;
    var multicolumn = layoutnode.getAttribute('multicolumn') === "true";
    var fixedcolcount = parseInt(layoutnode.getAttribute('fixedcolcount'));
    this.Orderbycol = !(layoutnode.getAttribute('orderbyrow') === "true");
    var rowheight = parseFloat(layoutnode.getAttribute('rowheight'))
    var colwidth = parseFloat(layoutnode.getAttribute('colwidth'))
    
    var objectNode = element.parentNode.parentNode;
    var height = objectNode.clientHeight;
    for(var i = 0; i < objectNode.childNodes.length; ++i) {
        var child = objectNode.childNodes[i];
        if(child.tagName != "DIV") continue;
        if(child == element.parentNode) continue;
        height -= child.offsetHeight;
    }
    var width = objectNode.clientWidth;
    
    var cols = 1;
    var rows;
    if(fixedcolcount > 0) {
        cols = fixedcolcount;
    } else if(multicolumn) {
        // calc cols
        if(this.Orderbycol) {
            rows = Math.max(1, Math.floor(height / rowheight));
            cols = Math.ceil(this.EffectiveSize / rows);
            
            if(cols * colwidth > width) {
                height -= 17;
                rows = Math.max(1, Math.floor(height / rowheight));
                cols = Math.ceil(this.EffectiveSize / rows);
            }
        } else {
            cols = Math.max(1, Math.floor(width / colwidth));
        }
    }
    if(cols == 1) this.Orderbycol = false;
    
    rows = Math.ceil(this.EffectiveSize / cols);
    if(rows * rowheight > height) width -= 17;
    element.style.width = Math.max(width - 2, colwidth * cols);
    
    var size = cols;
    if(this.Orderbycol) size = Math.max(1, rows);
    
    if(this.Width != cols) {
        this.Width = cols;
        this.FixCol(mode, node, name, partial);
    }

    var entries = node.selectNodes('value[@name="C0"]/element');
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
        
        full_rix = rix;
        if (this.IsAsync) { full_rix += this.ChunkOffset; }
        
        var col = full_rix % size;
        full_rix = (full_rix - col) / size;
        if(this.Orderbycol) {
            var temp = col;
            col = full_rix;
            full_rix = temp;
        }
        
        if (this.Lines [full_rix] == null) this.Lines [full_rix] = new Array ();
        this.IsPainted [full_rix] = false;
        this.Lines [full_rix][col] = new this.CellObject (optval, entry);
    }
    if (! this.IsAsync && height > 0) {
        this.Lines.length = full_rix + 1;
    }
}

Qva.Mgr.listbox.prototype.StyleObject = function (node) {
    this.BgColor = node.getAttribute ('bgcolor');
    this.Color = node.getAttribute ('color');
    this.NumAdjust = node.getAttribute ('numadjust');
    this.TextAdjust = node.getAttribute ('textadjust');
    this.BorderStyle = node.getAttribute ('borderstyle');
    this.FontMod = node.getAttribute ('fontmod');
    this.SizeMod = node.getAttribute ('sizemod');
}
Qva.Mgr.listbox.prototype.BorderStyleObject = function (node) {
    this.Top = node.getAttribute ('top');
    this.Bottom = node.getAttribute ('bottom');
    this.Left = node.getAttribute ('left');
    this.Right = node.getAttribute ('right');
}

Qva.Mgr.listbox.prototype.GetIndex = function (line) {
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
Qva.Mgr.listbox.prototype.GetSelected = function (rix) { return this.GetIndex (this.Lines [rix]); }
Qva.Mgr.listbox.prototype.GetDisabled = function (rix, cix) { return this.Lines[rix][cix].disabled; }
Qva.Mgr.listbox.prototype.GetLocked = function (rix) {
    if (this.Locked == null) return null;
    var line = this.Lines [rix];
    var val = line [this.ChoiceIx].val;
    for (var ix = 0; ix < this.Locked.length; ++ix) {
        if (this.Locked [ix] === val) return 1;
    }
    return 0;
}

Qva.Mgr.listbox.prototype.PostPaint = function () {
    if (this.Lines == null) return;
    if (!this.Width) return;
    
    var WantedChunkNumber = null;
    var scrollParent = this.Element.parentNode;
    if (scrollParent.style.display == 'none') return;
    var postpaintposted = false;
    
    var totalHeight = Math.ceil(this.TotalSize / this.Width);
    if (! this.FinalFix) {
        var height;
        if (this.TotalSize == 0) {
            this.PageOffset = 0;
            height = 0;
        } else if (this.IsAsync) {
            height = Math.ceil(this.EffectiveSize / this.Width);
        } else {
            height = this.Lines.length - this.PageOffset;
            while (height <= 0 && this.Lines.length > 0) {
                this.PageOffset -= this.PageSize;
                height = this.Lines.length - this.PageOffset;
            }
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
                this.Inflate (height, rowlen, this.Width == 20 ? 1 : this.Width);	// make sure table is right size
            }
        }
        var objectframeNode = this.Element.parentNode.parentNode;
        if (this.InlineStyle) {
            if (IS_GECKO || IS_SAFARI) {
                this.newscrollheight = objectframeNode.offsetHeight;
                var gs = document.defaultView.getComputedStyle (scrollParent, "");
                var pad = parseInt (gs.getPropertyValue ("padding-top")) + parseInt (gs.getPropertyValue("padding-bottom"));
                var bor = parseInt (gs.getPropertyValue ("border-top-width")) + parseInt (gs.getPropertyValue("border-bottom-width"));
                this.newscrollheight -= pad;
                this.newscrollheight -= bor;
            } else {
                this.newscrollheight = objectframeNode.clientHeight;
            }
            
            var numberofchildren = objectframeNode.childNodes.length;
            for (var ichild = 0; ichild < numberofchildren; ichild++) {
                var child = objectframeNode.childNodes [ichild];
                if (child == this.Element.parentNode) continue;
                if (child.nodeName != "DIV") continue;
                this.newscrollheight -= child.offsetHeight;
            }
        } else {
            this.newscrollheight = scrollParent.offsetHeight;
        }
        
        var rix_start = 0;
        var rix_stop = height;
        if (this.PageIncr > 0 && this.IsAsync) {
            if (totalHeight < this.PageSize) {
                rix_start = 0;
                rix_stop = totalHeight;
            } else {
                var scrollpos = scrollParent.scrollTop;
                rix_start = this.RowForPos (this.Body.rows, scrollpos);
                if (this.ChunkOffset == 0 && scrollpos == 0 && rix_start != 0) {
                    rix_start = 0;
                    rix_stop = this.PageSize;
                } else {
                    rix_stop = this.RowForPos (this.Body.rows, scrollpos + this.newscrollheight) + 1;
                    var visibleScrollRowsDiv2 = Math.ceil ((rix_stop - rix_start) / 2);
                    rix_start -= visibleScrollRowsDiv2;
                    rix_stop += visibleScrollRowsDiv2;
                }
                if (rix_start < 0) rix_start = 0;
                if (rix_stop > height) rix_stop = height;
                if (rix_stop > totalHeight) rix_stop = totalHeight;
            }
        }

        var rows = this.Body.rows;
        var PaintedLines = 0;
        var LastRowAdjusted = false;
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
                    row.style.left = this.Body.rows [0].offsetLeft;
                    row.style.top = this.Body.rows [0].offsetHeight * rix;
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
                        lastRow.style.top = wantedLastRowTop;
                    }
                }
            }
            var row = this.Body.rows [this.RowNumbers [rix]];
            if (line == null) {
                if (WantedChunkNumber == null) {
                    WantedChunkNumber = Math.floor (lix * this.Width / this.ChunkSize);
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
            row.className += " R" + rix;
            if (row.rix == null) row.rix = rix;
            var autocolwidth = false;
            for (var cix = 0; cix < this.Width; ++ cix) {
                var colmgr = this.ColList [cix];
                
                var cell = row.cells [cix];
                if (cell == null) {
                    cell = document.createElement ("td");
                    row.appendChild (cell);
                }
                if (this.InlineStyle && rix == rix_start) {
                    if (colmgr.width) {
                        autocolwidth = true;
                        try {
                            var colwidth = colmgr.width * 72 / 300;
                            cell.style.width = "" + colwidth + "pt";
                        } catch (e) {}
                    } else if (autocolwidth) {
                        try {
                            cell.style.width = "0pt";
                        } catch (e) {}
                    }
                }
                if (line [cix] == null) {
                    if(this.Orderbycol && cix * this.Body.rows.length + rix < this.EffectiveSize) {
                        if (WantedChunkNumber == null) {
                            WantedChunkNumber = Math.floor ((cix * this.Body.rows.length + rix) / this.ChunkSize);
                        }
                        break;
                    } else {
                        cell.style.display = "none";
                    }
                } else {
                    cell.style.display = "";
                    var IsDisabled = this.GetDisabled (lix, cix);
                    
                    var StateClassName = this.GetSelectionStateClassName (line [cix].selected, ! line [cix].disabled && ! line [cix].locked, line [cix].disabled, line [cix].locked, line [cix].deselected);
                    var cellClassName = colmgr.ClassName;
                    if (StateClassName != '') {
                        cellClassName += " " + StateClassName;
                    }
                    if (cell.className != cellClassName) {
                        cell.className = cellClassName;
                    }
                    cell.innerText = "";
                    if (this.AndMode) {
                        if (line [cix].selected) {
                            cell.innerText = "&  ";
                        } else if (line [cix].deselected) {
                            cell.innerText = "!  ";
                        }
                    }
                    cell.innerText += line [cix].val || ' '; //(line [cix].val != '') ? line [cix].val : ' ';
                    cell.title = line [cix].val || ' ';
                    cell.value = line [cix].intval;
                    if (this.Element.disabled || line [cix].locked) {
                        cell.onmousedown = Qva.NoAction;
                        cell.onmousemove = Qva.NoAction;
                        cell.onmouseup = Qva.NoAction;
                    } else {
                        cell.onmousedown = AvqAction_TableEditMouseDown;
                        cell.onmousemove = AvqAction_TableEditMouseMove;
                        cell.onmouseup = AvqAction_TableEditMouseUp;
                        if (line [cix].selecttype == "single") {
                             cell.singleselect = true;
                        }
                    }
                    if (line [cix].style && this.InlineStyle) {
                        cell.style.cssText += this.SetCellStyle (line [cix], (IsDisabled || IsSelected), this.Style, this.BorderStyle, false, rix == (height - 1) && ! this.IsHeader, cix == 0, cix == (line.length - 1));
                    }
                    cell.style.cursor = 'pointer';
                    
					var binder = this.PageBinder;
                    cell.oncontextmenu = function (event) { return binder.OnContextMenu(event); }
                    cell.position = cix + ":" + rix + ":";
                    cell.position += "body";
//                    cell.targetname = colmgr.Name.split ('.') [1];
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
    } else {
        var objectframeNode = scrollParent.parentNode;
        var hasverticalscrollbar = scrollParent.offsetHeight < this.Element.offsetHeight;
	    if (this.Element.offsetHeight > 10 && this.Element.offsetHeight < this.newscrollheight) {
		    this.newscrollheight = this.Element.offsetHeight;
		    if (! (IS_GECKO || IS_SAFARI)) {
		        this.newscrollheight += scrollParent.offsetHeight - scrollParent.clientHeight;
		    }

	    }
	    if (parseInt (scrollParent.style.height) != this.newscrollheight || scrollParent.style.height.search ("pt") != -1) {
            scrollParent.style.height = this.newscrollheight;
        } 
        if (this.HeaderId != null) {
            var header = document.getElementById (this.HeaderId);
            if (header) {
                var sourcerow = this.Body.rows [0];
                var targetrow = header.rows [0];
                if (sourcerow && targetrow && (sourcerow != targetrow)) {
                    for (iCol = 0; iCol < sourcerow.cells.length; iCol++) {
                        var sourcecell = sourcerow.cells [iCol];
                        var targetcell = targetrow.cells [iCol];
                        if (targetcell && sourcecell) {
                            targetcell.style.width = sourcecell.offsetWidth;
                        } else {
                            debugger;
                        }
                    }
                    var targetcell = targetrow.cells [sourcerow.cells.length];
                    if (targetcell) {
                        if (hasverticalscrollbar) {
                            targetcell.style.width = 17;
                        } else {
                            targetcell.style.width = 0;
                        }
                    }
                }
            } else {
                debugger;
            }
        }

        var deltaframe = 0;
        var deltaparent = 0;
        if (IS_GECKO || IS_SAFARI) {
	        var gs = document.defaultView.getComputedStyle (scrollParent, "");
	        var pad = parseInt (gs.getPropertyValue ("padding-left")) + parseInt (gs.getPropertyValue("padding-right"));
	        var bor = parseInt (gs.getPropertyValue ("border-left-width")) + parseInt (gs.getPropertyValue("border-right-width"));
	        deltaparent += pad;
	        deltaparent += bor;
	        gs = document.defaultView.getComputedStyle (objectframeNode, "");
	        pad = parseInt (gs.getPropertyValue ("padding-left")) + parseInt (gs.getPropertyValue("padding-right"));
	        bor = parseInt (gs.getPropertyValue ("border-left-width")) + parseInt (gs.getPropertyValue("border-right-width"));
	        deltaframe += pad;
	        deltaframe += bor;
	        deltaframe += (hasverticalscrollbar ? 19 : 0)
        } else {
            deltaparent += scrollParent.offsetWidth - scrollParent.clientWidth;
            deltaframe += objectframeNode.offsetWidth - objectframeNode.clientWidth;
        }
        var totwidth = this.Element.offsetWidth + deltaparent + deltaframe;
        var widthdiff = objectframeNode.offsetWidth - totwidth;
        var changerect = widthdiff != 0;
        if (changerect) {
            if (! objectframeNode.maxwidth) {
                objectframeNode.maxwidth = objectframeNode.offsetWidth;
            }
            var newwidth = objectframeNode.offsetWidth - widthdiff;
            newwidth = Math.min (newwidth, objectframeNode.maxwidth);
            var currentwidth = parseInt (objectframeNode.style.width);
            if (currentwidth != newwidth) {
                objectframeNode.style.width = newwidth;
                this.FinalFix = false;
                if (IS_GECKO || IS_SAFARI) {
                    newwidth -= deltaparent; 
                }
                scrollParent.style.width = newwidth;
                if (this.HeaderId != null) {
                    var header = document.getElementById (this.HeaderId);
                    if (header) {
                        header.parentNode.style.width = newwidth;
                        var mgr  = header.AvqMgr;
                        mgr.FinalFix = false;
                        for (var iRow = 0; iRow < mgr.IsPainted.length; iRow++) {
                            mgr.IsPainted [iRow] = false;
                        }
                        Qva.QueuePostPaintMessage (mgr);
                    }
                }
            }
        }
    } 
    
    this.Unlock ();
    this.Element.style.display = "";
    if (WantedChunkNumber != null) {
        this.ChunkOffset = WantedChunkNumber * this.ChunkSize;
        this.PageBinder.PartialLoad (this.PageName, this.ChunkOffset);
    } else if (this.InlineStyle && ! this.FinalFix && ! postpaintposted) {
        this.FinalFix = true;
        Qva.QueuePostPaintMessage (this);
    } else if (this.FinalFix) {
        this.FinalFix = false;
    }
}

Qva.Mgr.listbox.prototype.Inflate = function (height, rowlen, width) {
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
    for(var i = 0; i < width; ++i) {
        var cell = document.createElement ("td");
        var colmgr = mgr.ColList [i];
        cell.className = colmgr.ClassName;
        cell.align = colmgr.Align;
        switch (colmgr.Cmd) {
        case 'check':
            cell.innerHTML = '<input class="avqCheckbox" type=checkbox >';
            break;
        case 'count':
            cell.innerHTML = '<input class="avqEdit" style="width:100%" value="" >';
            break;
        case 'img':
            cell.innerHTML = '<img class="avqImage" >';
            break;
        default:
            cell.innerHTML = "|";
        }
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
            row.style.left = body.rows [1].offsetLeft;
	        row.style.top = body.rows [1].offsetHeight * last_rix;
	        body.appendChild (row);
        } else {
            while (body.rows.length > height_to_inflate + 1) {
                body.deleteRow (height_to_inflate + 1);
            }
            mgr.RowNumbers.length = height_to_inflate + 1;
            row = body.rows [height_to_inflate];
	        row.className = mgr.RowClassNames [rcix];
	        row.style.position = "absolute";
            row.style.left = body.rows [1].offsetLeft;
	        row.style.top = body.rows [1].offsetHeight * last_rix;
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

Qva.Mgr.listbox.prototype.RowForPos = function (rows, pos) {
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

Qva.Mgr.listbox.prototype.PageOffsetForPainting = function () { return this.PageOffset; }

Qva.Mgr.listbox.prototype.GetSelectionStateClassName = function (is_selected, is_enabled, is_disabled, is_locked, is_deselected) {
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

Qva.Mgr.listbox.prototype.ClearSelection = function () {
    if (window.getSelection) {
        window.getSelection().removeAllRanges();
    } else {
        window.document.selection.empty ();
    }
}

Qva.Mgr.listbox.prototype.IndicateCellsToSelect = function (ctrl, clearselection) {
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
    
    if(this.Orderbycol) {
        if (this.prev_rix_start == null) this.prev_rix_start = new Array ();
        if (this.prev_rix_end == null) this.prev_rix_end = new Array ();
        var col_loop_start = this.prev_cix_start != null && this.prev_cix_start < cix_start ? this.prev_cix_start : cix_start;
        var col_loop_end = this.prev_cix_end != null && this.prev_cix_end > cix_end ? this.prev_cix_end : cix_end;
        for (var cix = col_loop_start; cix <= col_loop_end; cix ++) {
            var row_loop_start = this.prev_rix_start [cix] != null && this.prev_rix_start [cix] < rix_start ? this.prev_rix_start [cix] : rix_start;
            var row_loop_end = this.prev_rix_end [cix] != null && this.prev_rix_end [cix] > rix_end ? this.prev_rix_end [cix] : rix_end;
            var actualrixstart = (cix != col_loop_start) ? 0 : rix_start;
            var actualrixend = (cix < col_loop_end) ? (this.Body.rows.length - 1) : rix_end;
            row_loop_start = Math.min (row_loop_start, actualrixstart);
            row_loop_end = Math.max (row_loop_end, actualrixend);
            this.prev_rix_start [cix] = actualrixstart;
            this.prev_rix_end [cix] = actualrixend;
            
            for (var rix = row_loop_start; rix <= row_loop_end; ++ rix) {
                var rowNumber = this.RowNumbers [rix];
                if (rowNumber == null) {
                    var row = this.Body.rows [1].cloneNode (true);
                    row.style.position = "absolute";
                    row.style.left = this.Body.rows [1].offsetLeft;
                    row.style.top = this.Body.rows [1].offsetHeight * rix;
                    row.rix = rix;
                    this.Body.appendChild (row);
                    rowNumber = this.Body.rows.length - 1;
                    this.RowNumbers [rix] = rowNumber;
                }
                var selIx = rix + this.PageOffsetForPainting ();
                
                var row = this.Body.rows [rowNumber];
                var StateClassName = null;
                if (cix < cix_start || cix > cix_end || rix < actualrixstart || rix > actualrixend) {
                    // restore state
                    var IsDisabled = this.GetDisabled (selIx, cix);
                    var IsSelected = ctrl ? this.GetSelected (selIx) : false;
                    StateClassName = this.GetSelectionStateClassName (IsSelected != 0, IsDisabled == false, IsDisabled == true);
                } else {
                    // change to selected state or toggle if ctrl pressed
                    var IsDisabled = false;
                    var IsSelected = ctrl ? ! this.GetSelected (selIx) : true;
                    StateClassName = this.GetSelectionStateClassName (IsSelected != 0, IsDisabled == false, IsDisabled == true);
                }
                var rcix = rix % this.RowClassNames.length;
                if (row.className != this.RowClassNames [rcix]) {
                    row.className = this.RowClassNames [rcix];
                }
                var colmgr = this.ColList [cix];
                var cell = row.cells [cix];
                if (cell.value == -1) continue;
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
        this.prev_cix_start = cix_start;
        this.prev_cix_end = cix_end;
    } else {
        if (this.prev_cix_start == null) this.prev_cix_start = new Array ();
        if (this.prev_cix_end == null) this.prev_cix_end = new Array ();
        var row_loop_start = this.prev_rix_start != null && this.prev_rix_start < rix_start ? this.prev_rix_start : rix_start;
        var row_loop_end = this.prev_rix_end != null && this.prev_rix_end > rix_end ? this.prev_rix_end : rix_end;
        for (var rix = row_loop_start; rix <= row_loop_end; rix ++) {
            var rowNumber = this.RowNumbers [rix];
            if (rowNumber == null) {
                var row = this.Body.rows [1].cloneNode (true);
                row.style.position = "absolute";
                row.style.left = this.Body.rows [1].offsetLeft;
                row.style.top = this.Body.rows [1].offsetHeight * rix;
                row.rix = rix;
                this.Body.appendChild (row);
                rowNumber = this.Body.rows.length - 1;
                this.RowNumbers [rix] = rowNumber;
            }
            var row = this.Body.rows [rowNumber];
            var selIx = rix + this.PageOffsetForPainting ();
            var col_loop_start = this.prev_cix_start [rix] != null && this.prev_cix_start [rix] < cix_start ? this.prev_cix_start [rix] : cix_start;
            var col_loop_end = this.prev_cix_end [rix] != null && this.prev_cix_end [rix] > cix_end ? this.prev_cix_end [rix] : cix_end;
            var actualcixstart = (rix != row_loop_start) ? 0 : cix_start;
            var actualcixend = (rix < row_loop_end) ? (row.cells.length - 1) : cix_end;
            col_loop_start = Math.min (col_loop_start, actualcixstart);
            col_loop_end = Math.max (col_loop_end, actualcixend);
            this.prev_cix_start [rix] = actualcixstart;
            this.prev_cix_end [rix] = actualcixend;
            for (var cix = col_loop_start; cix <= col_loop_end; ++ cix) {
                var StateClassName = null;
                if (rix < rix_start || rix > rix_end || cix < actualcixstart || cix > actualcixend) {
                    // restore state
                    var IsDisabled = this.GetDisabled (selIx, cix);
                    var IsSelected = ctrl ? this.GetSelected (selIx) : false;
                    StateClassName = this.GetSelectionStateClassName (IsSelected != 0, IsDisabled == false, IsDisabled == true);
                } else {
                    // change to selected state or toggle if ctrl pressed
                    var IsDisabled = false;
                    var IsSelected = ctrl ? ! this.GetSelected (selIx) : true;
                    StateClassName = this.GetSelectionStateClassName (IsSelected != 0, IsDisabled == false, IsDisabled == true);
                }
                var rcix = rix % this.RowClassNames.length;
                if (row.className != this.RowClassNames [rcix]) {
                    row.className = this.RowClassNames [rcix];
                }
                var colmgr = this.ColList [cix];
                var cell = row.cells [cix];
                if (cell.value == -1) continue;
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
}

Qva.Mgr.listbox.prototype.SetCellSelected = function (cell, newclassName, deselect, colmgr) {
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

Qva.Mgr.listbox.prototype.IndicateSingleSelect = function (rowindex, colindex, deselect, newclassname) {
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

Qva.Mgr.listbox.prototype.SetCellStyle = function (data, ignorecolor, Styles, BorderStyles, firstrow, lastrow, firstcol, lastcol) {
    var style = Styles [data.style]; 
    var csstext = "";
    if (style) {
		if (! ignorecolor) {
			csstext += "; background-color:" + style.BgColor;
			csstext += "; color:" + style.Color;
		}
		csstext += "; text-align:" + (data.isnum ? style.NumAdjust : style.TextAdjust);
		if (style.FontMod == 1) {
			csstext += "; font-style:italic";
		} else if (style.FontMod == 2) {
			csstext += "; font-weight:bold";
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
			if (! lastrow) {
			    if (hasbottomborder) {
			        csstext += "; border-bottom:" + borderstyle.Bottom;
			    } else {

			    }
		    }
			if (! firstrow) {
			    if (hastopborder) {
			        csstext += "; border-top:" + borderstyle.Top;
			    }
		    }
			var hasleftborder = false;
			var hasrightborder = false;
			if (! (data.subcell == "x")) {
				hasleftborder = true;
				if (! (data.first == "x")) {
					hasrightborder = true;
				}
			}
			if (! firstcol) {
			    if (hasleftborder) {
			        csstext += "; border-left:" + borderstyle.Left;
			    }
			}
			if (! lastcol) {
			    if (hasrightborder) {
			        csstext += "; border-right:" + borderstyle.Right;
			    }
		    }
		}
	}
    return csstext;
}

Qva.Mgr.listbox.prototype.SelectRows = function (ctrl) {
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
    
    var singlecellselection = (rix_start == rix_end && cix_start == cix_end);
    if (singlecellselection) {
        if (this.Body.rows [this.RowNumbers [rix_start]].cells [cix_start].value == -1) return;
    }
    var selIx = rix_start + this.PageOffsetForPainting ();
    var IsSelected = this.GetSelected (selIx);
    var valName = this.PageName;
    if (valName == "") {
        valName = this.ColList [0].Name.split ('.') [1];
    }
    if (! ctrl) {	// Not toggle mode
        this.PageBinder.Set (valName, 'clear', '', false);
        if (IsSelected && this.Selected.length == 1 && singlecellselection) {
            rix_start ++; // nothing more to do
        }
    }
    
    if(this.Orderbycol) {
        for (var cix = cix_start; cix <= cix_end; cix ++) {
            var actualrixstart = (cix != cix_start) ? 0 : rix_start;
            var actualrixend = (cix < cix_end) ? (this.Body.rows.length - 1) : rix_end;
            for (var rix = actualrixstart; rix <= actualrixend; rix ++) {
                var row = this.Body.rows [this.RowNumbers [rix]];
                var cell = row.cells [cix];
                var IsSelected = this.GetSelected (rix + this.PageOffsetForPainting ());
                var valValue = cell.value;
                if (ctrl) {	// Toggle mode
                    this.PageBinder.Set (valName, 'count', (IsSelected ? '-' : ' ') + valValue, false);
                } else {
                    this.PageBinder.Set (valName, 'value', valValue, false);
                }
            }
        }
    } else {
        for (var rix = rix_start; rix <= rix_end; rix ++) {
            var row = this.Body.rows [this.RowNumbers [rix]];
            var actualcixstart = (rix != rix_start) ? 0 : cix_start;
            var actualcixend = (rix < rix_end) ? (row.cells.length - 1) : cix_end;
            for (var cix = actualcixstart; cix <= actualcixend; cix ++) {
                var cell = row.cells [cix];
                var IsSelected = this.GetSelected (rix + this.PageOffsetForPainting ());
                var valValue = cell.value;
                if (ctrl) {	// Toggle mode
                    this.PageBinder.Set (valName, 'count', (IsSelected ? '-' : ' ') + valValue, false);
                } else {
                    this.PageBinder.Set (valName, 'value', valValue, false);
                }
            }
        }
    }
    
    if (SearchActive) {
        this.PageBinder.Set (this.SearchName, "closesearch", "abort", true);      // break out of search mode
        if (this.Search != null) this.Search.value = '';				// Allow for popup search being closed
    } else {
        this.PageBinder.LoadBegin ();
    }
}

var m_MgrWithSelectStart  = null;

function AvqAction_TableEditMouseMove (event) {
    if (!event) event = window.event;
    
    var row = this.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (mgr.SelectionStartRow == null) return;
    if (m_MgrWithSelectStart != null && m_MgrWithSelectStart != mgr) return;
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
    if (!event) event = window.event;
    
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
    if (!event) event = window.event;
    
    var row = this.parentNode;
    if (row.tagName != 'TR') return;
    var mgr = row.parentNode.AvqMgr;
    if (mgr == null) mgr = row.parentNode.parentNode.AvqMgr;
    if (mgr == null) return;
    if (event.button != mgr.LeftButton) return;
    if (mgr.SelectionStartRow == null) return;
    if (m_MgrWithSelectStart != null && m_MgrWithSelectStart != mgr) return;
    
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
