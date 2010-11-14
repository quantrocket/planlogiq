// Build $BuildVersion$

Qva.Modal = function(owner) {
    this.Owner = owner;
    this.ScriptPath = owner.ScriptPath;
    this.HideSelects = false;
    this.DefaultPage = "modal/loading.html";
    this.TabIndexes = new Array();
    this.TabbableTags = new Array("A","BUTTON","TEXTAREA","INPUT","IFRAME");	
	this.PopupIsShown = false;
}

Qva.Modal.prototype.Show = function(url, width, height) {
    this.Init();
    document.getElementById("popCloseBox").style.display = "block";
	this.PopupIsShown = true;
	this.DisableTabs();
	this.PopupMask.style.display = "block";
	this.PopupContainer.style.display = "block";
	// calculate where to place the window on screen
	this.CenterWin(width, height);
	
	var titleBarHeight = parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);


	this.PopupContainer.style.width = width + "px";
	this.PopupContainer.style.height = (height+titleBarHeight) + "px";
	
	this.SetMaskSize();

	// need to set the width of the iframe to the title bar width because of the dropshadow
	// some oddness was occuring and causing the frame to poke outside the border in IE6
	this.PopFrame.style.width = parseInt(document.getElementById("popupTitleBar").offsetWidth, 10) + "px";
	this.PopFrame.style.height = (height) + "px";
	
	// set the url
	this.PopFrame.src = url;
	
	// for IE
	if (this.HideSelects == true) {
		this.HideSelectBoxes();
	}
}

Qva.Modal.prototype.Init = function() {
    if (this.PopupMask != null) return;
	// Add the HTML to the body

	theBody = document.getElementsByTagName('BODY')[0];
	popmask = document.createElement('div');
	popmask.id = 'popupMask';
	popcont = document.createElement('div');
	popcont.id = 'popupContainer';
	popcont.innerHTML = '' +
		'<div id="popupInner">' +
			'<div id="popupTitleBar">' +
				'<div id="popupTitle" style="width:90%"></div>' +
				'<div id="popupControls" style="width:15px">' +
					'<img src="' + this.ScriptPath + 'modal/close.gif" onclick="Qva.CloseModal();" id="popCloseBox" />' +
				'</div>' +
			'</div>' +
			'<iframe src="'+ this.ScriptPath + this.DefaultPage +'" style="width:100%;height:100%;background-color:transparent;" scrolling="auto" frameborder="0" allowtransparency="true" id="popupFrame" name="popupFrame" width="100%" height="100%"></iframe>' +
		'</div>';
	theBody.appendChild(popmask);
	theBody.appendChild(popcont);
	
	this.PopupMask = document.getElementById("popupMask");
	this.PopupContainer = document.getElementById("popupContainer");
	this.PopFrame = document.getElementById("popupFrame");
	
	// check to see if this is IE version 6 or lower. hide select boxes if so
	// maybe they'll fix this in version 7?
	var brsVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
	if (brsVersion <= 6 && window.navigator.userAgent.indexOf("MSIE") > -1) {
		this.HideSelects = true;
	}
}

// For IE.  Go through predefined tags and disable tabbing into them.
Qva.Modal.prototype.DisableTabs = function() {
	if (document.all) {
		var i = 0;
		for (var j = 0; j < this.TabbableTags.length; j++) {
			var tagElements = document.getElementsByTagName(this.TabbableTags[j]);
			for (var k = 0 ; k < tagElements.length; k++) {
				this.TabIndexes[i] = tagElements[k].tabIndex;
				tagElements[k].tabIndex="-1";
				i++;
			}
		}
	}
}

// For IE. Restore tab-indexes.
Qva.Modal.prototype.RestoreTabs = function() {
	if (document.all) {
		var i = 0;
		for (var j = 0; j < this.TabbableTags.length; j++) {
			var tagElements = document.getElementsByTagName(this.TabbableTags[j]);
			for (var k = 0 ; k < tagElements.length; k++) {
				tagElements[k].tabIndex = this.TabIndexes[i];
				tagElements[k].tabEnabled = true;
				i++;
			}
		}
	}
}

Qva.Modal.prototype.CenterWin = function(width, height) {
	if (this.PopupIsShown) {
		if (width == null || isNaN(width)) {
			width = this.PopupContainer.offsetWidth;
		}
		if (height == null) {
			height = this.PopupContainer.offsetHeight;
		}
		
		var theBody = document.getElementsByTagName("BODY")[0];
		var scTop = parseInt(Qva.GetScrollTop(),10);
		var scLeft = parseInt(theBody.scrollLeft,10);
	
		this.SetMaskSize();
	
		var titleBarHeight = parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);
		
		var fullHeight = Qva.GetViewportHeight();
		var fullWidth = Qva.GetViewportWidth();
		
		this.PopupContainer.style.top = (scTop + ((fullHeight - (height+titleBarHeight)) / 2)) + "px";
		this.PopupContainer.style.left =  (scLeft + ((fullWidth - width) / 2)) + "px";
	}
}

Qva.Modal.prototype.SetMaskSize = function() {
	var theBody = document.getElementsByTagName("BODY")[0];
			
	var fullHeight = Qva.GetViewportHeight();
	var fullWidth = Qva.GetViewportWidth();
	
	// Determine what's bigger, scrollHeight or fullHeight / width
	if (fullHeight > theBody.scrollHeight) {
		popHeight = fullHeight;
	} else {
		popHeight = theBody.scrollHeight;
	}
	
	if (fullWidth > theBody.scrollWidth) {
		popWidth = fullWidth;
	} else {
		popWidth = theBody.scrollWidth;
	}
	
	this.PopupMask.style.height = popHeight + "px";
	this.PopupMask.style.width = popWidth + "px";
}

Qva.Modal.prototype.HideSelectBoxes = function() {
	for(var i = 0; i < document.forms.length; i++) {
		for(var e = 0; e < document.forms[i].length; e++){
			if(document.forms[i].elements[e].tagName == "SELECT") {
				document.forms[i].elements[e].style.visibility="hidden";
			}
		}
	}
}

Qva.Modal.prototype.DisplaySelectBoxes = function() {
	for(var i = 0; i < document.forms.length; i++) {
		for(var e = 0; e < document.forms[i].length; e++){
			if(document.forms[i].elements[e].tagName == "SELECT") {
			document.forms[i].elements[e].style.visibility="visible";
			}
		}
	}
}

Qva.Modal.prototype.Hide = function () {
	this.PopupIsShown = false;
	var theBody = document.getElementsByTagName("BODY")[0];
	theBody.style.overflow = "";
	this.RestoreTabs();
	if (this.PopupMask == null) {
		return;
	}
	this.PopupMask.style.display = "none";
	this.PopupContainer.style.display = "none";
	this.PopFrame.src = this.ScriptPath + this.DefaultPage;
	// display all select boxes
	if (this.HideSelects == true) {
		this.DisplaySelectBoxes();
	}
}

Qva.Modal.prototype.SetTitle = function (text) {
    try {
        document.getElementById("popupTitle").innerText = text;
    } catch(e) {
    }
}
