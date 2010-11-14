var XLAB6 = {};

XLAB6.ImageManager = Base.extend(
{
	options : {},

	selectedFile : null,

	opener : null,

	constructor: function(options)
	{
		this.options = options;
		XLAB6.ImageManager.registry[options.ID] = this;

		Event.observe(this.$('assetDir'),
			'change', this.locationChanged.bind(this));
		Event.observe(this.$('UpButton'),
			'click', this.moveUpLocation.bind(this));
		Event.observe(this.$('RefreshButton'),
			'click', this.refreshImageList.bind(this));
		var OKButton = this.$('OKButton');

		if(OKButton)
		{
			Event.observe(OKButton,
				'click', this.OKButtonClicked.bind(this));
		}

		var CancelButton = this.$('CancelButton');
		if(CancelButton)
		{
			Event.observe(CancelButton,
				'click', this.CancelButtonClicked.bind(this));
		}

		var NewFolderButton = this.$('NewFolderButton');
		if(NewFolderButton)
		{
			Event.observe(NewFolderButton,
				'click', this.NewFolderButtonClicked.bind(this));
		}

		var UploadButton = this.$('UploadButton');
		if(UploadButton)
		{
			Event.observe(UploadButton,
				'click', this.UploadButtonClicked.bind(this));
		}

		this.initWindows();
		OKButton.disabled=true;
	},

	getListFrame : function()
	{
		var frame = this.options.Name+"$listFrame";
		if(window.top && window.top[frame])
		{
			if(typeof(window.top[frame].XLAB6) != "undefined")
			{
				if(typeof(window.top[frame].XLAB6.ImageManagerListView) != "undefined")
				{
					return window.top[frame].XLAB6.ImageManagerListView.registry[this.options.ID];
				}
			}
		}
	},

	initWindows : function()
	{
		var win = window.opener ? window.opener : window.dialogArguments;
		if(win && typeof(win.tinyMCE) != "undefined")
		{
			this.opener = win;
			window.focus();
		}
	},

	updateDirList : function(dirs)
	{
		var selection = this.$('assetDir');
		var current = this.getSelectedPath();
		while(selection.length > 0)
			selection.options[0] = null;
		for(var i in dirs)
		{
			var selected = dirs[i]==current;
			selection.options[selection.length] = new Option(dirs[i],i,selected,selected);
		}
	},

	$ : function(id)
	{
		return $(this.options.ID+"_"+id);
	},

	hideLoadingPanel : function()
	{
		this.$('LoadingPanel').hide();
	},

	showLoadingPanel : function()
	{
		this.$('LoadingPanel').show();
	},

	locationChanged : function(ev,ignoreCache)
	{
		this.changeUrl(this.getSelectedPath(), ignoreCache);
	},

	updateLocation : function(folder)
	{
		var select = this.$('assetDir');
		for(var i = 0,j=select.options.length; i<j; i++)
		{
			if(select.options[i].text == folder)
			{
				select.selectedIndex = i;
				return;
			}
		}
	},

	moveUpLocation : function(ev)
	{
		var current = this.getSelectedPath();
		var paths = current.split('/');
		var folder = '/';
		for(var i=1,j=paths.length; i<j-2;i++)
			folder += paths[i]+'/';
		if(current != folder)
		{
			this.updateLocation(folder);
			this.changeUrl(folder);
		}
		Event.stop(ev)
	},

	setSelectedFile : function(file)
	{
		if(!file.Directory)
		{
			this.$('OKButton').disabled=false;
			this.selectedFile = file;
		}
	},

	getSelectedPath : function()
	{
		var select = this.$('assetDir');
		return select.options[select.selectedIndex].text;
	},

	changeUrl : function(folder,ignoreCache)
	{
		var doDefault = true;
		if(typeof(this.options.OnUrlChange) == "function")
			doDefault= this.options.OnUrlChange(this,folder)
		if(doDefault != false)
		{
			var path = encodeURIComponent(folder);
			var frame = this.$('listFrame');
			var url = frame.src.replace(/&__IMPATH=[^&]+&/, '&__IMPATH='+path+'&');
			this.showLoadingPanel();
			this.selectedFile = null;
			this.$('OKButton').disabled = true;
			frame.src = ignoreCache ? url+"&"+(new Date().getTime()) : url;
		}
	},

	onThumbnailClicked : function(options)
	{
		if(typeof(this.options.OnThumbnailClick) == "function")
			return this.options.OnThumbnailClick(this,options);
		return true;
	},

	onThumbnailDblClicked : function(options)
	{
		if(typeof(this.options.OnThumbnailDblClick) == "function")
			return this.options.OnThumbnailDblClick(this,options);
		return true;
	},
	
	refreshImageList : function(ev)
	{
		this.locationChanged(ev,true);
		Event.stop(ev);
	},

	setStatusText : function(text)
	{
		this.$('StatusPanel').innerHTML = text;
	},

	OKButtonClicked : function(ev)
	{
		var doDefault = false;
		if(typeof(this.options.onOKClick) == "function")
			doDefault = this.options.onOKClick(this,this.selectedFile);
		if(this.opener && this.selectedFile)
		{
			if(this.options.onCreateHtml)
				var html = this.onCreateHtml(this,this.selectedFile);
			else
				var html = this.createImageHtml(this.selectedFile);
			this.ExecuteTinyMCECommand('mceImageManagerInsertImage',html);
			window.close();
		}
		if(!doDefault)
			Event.stop(ev);
	},

	NewFolderButtonClicked : function(ev)
	{
		var frame = this.getListFrame();
		if(typeof(frame) != "undefined")
			frame.showNewFolderForm();
		Event.stop(ev);
	},

	UploadButtonClicked : function(ev)
	{
		var frame = this.getListFrame();
		if(typeof(frame) != "undefined")
			frame.showUploadForm();
		Event.stop(ev);
	},

	createImageHtml : function(file)
	{
		var html = "<img";
		html += this.makeAttrib("src", file.Filename);
		html += this.makeAttrib("title", file.Caption);
		html += this.makeAttrib("alt", file.Caption);
		html += this.makeAttrib("width", file.Width);
		html += this.makeAttrib("height", file.Height);
		html += "/>";
		return html;
	},

	makeAttrib : function(name,value)
	{
		if(typeof(value) == "string")
		{
			if(value.length == 0)
				return '';
			value = value.replace(/&/g, '&amp;');
			value = value.replace(/\"/g, '&quot;');
			value = value.replace(/</g, '&lt;');
			value = value.replace(/>/g, '&gt;');
		}
		return ' ' + name + '="' + value + '"';
	},

	CancelButtonClicked : function(ev)
	{
		var doDefault = false;
		if(typeof(this.options.onCancelClick) == "function")
			doDefault = this.options.onCancelClick(this,null);
		if(this.opener)
			window.close();
		if(!doDefault)
			Event.stop(ev);
	},

	ExecuteTinyMCECommand: function(command,value)
	{
		var inst = this.opener.tinyMCE.selectedInstance;
		inst.execCommand(command, false, value);
	}
},
{
	registry : {}
});