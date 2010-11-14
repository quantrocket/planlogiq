var XLAB6 = {};

XLAB6.ImageManagerListView = Base.extend(
{
	options : {},

	currentFile : null,

	constructor: function(options)
	{
		this.options = options;
		XLAB6.ImageManagerListView.registry[options.DialogID] = this;
		var manager = this.getManager();
		if(manager)
		{
			manager.hideLoadingPanel();
			var text = options.Files+" pictures &nbsp; "+options.TotalSize;
			manager.setStatusText(text);
		}
		var NewFolderOKButton = this.$('NewFolderOKButton');
		if(NewFolderOKButton)
		{
			Event.observe(NewFolderOKButton,'click',
				this.NewFolderSubmit.bind(this));
		}
		Event.observe(document.body, 'keydown', this.keyPressed.bind(this));
		this.updateDirList(options,manager);
	},

	updateDirList : function(options,manager)
	{
		if(typeof(options.Dirs) != "undefined")
			manager.updateDirList(options.Dirs);
	},

	$ : function(id)
	{
		return $(this.options.ID+"_"+id);
	},

	getManager : function()
	{
		if(window.parent && window.parent.XLAB6 && window.parent.XLAB6.ImageManager)
			return window.parent.XLAB6.ImageManager.registry[this.options.DialogID];
		return false;
	},

	changeFolder : function(folder)
	{
		var manager = this.getManager();
		if(manager)
		{
			manager.updateLocation(folder);
			manager.changeUrl(folder);
		}
	},

	thumbnailClicked : function(options)
	{
		this.currentFile = options;
		var manager = this.getManager();
		if(manager)
		{
			var doDefault = manager.onThumbnailClicked(options);
			if(doDefault != false && !options.Folder)
			{
				manager.setStatusText(this.getFileStats(options));
				manager.setSelectedFile(options);
			}
		}
	},

	thumbnailDblClicked : function(options)
	{
		var doDefault = true;
		var manager = this.getManager();
		if(manager)
			doDefault = manager.onThumbnailDblClicked(options);
		if(doDefault != false)
		{
			if(!options.Folder)
				this.previewImage(options);
			else
				this.changeFolder(options.Filename);
		}
	},

	keyPressed : function(ev)
	{
		if(this.currentFile != null && Event.keyCode(ev) == Event.KEY_DELETE)
		{
			var dialog = $('image_manager_delete');
			if(dialog)
			{
				$('delete_filename').innerHTML = this.currentFile.Name;
				this.$('FileToDelete').value = this.currentFile.Name;
				dialog.show();
				$('delete_cancel_button').focus();
			}
		}
	},

	previewImage : function(options,args)
	{
		var html, width, height, x, y, resizable, scrollbars;

		width = 600;
		height = 400;

		// Add to height in M$ due to SP2 WHY DON'T YOU GUYS IMPLEMENT innerWidth of windows!!
		if (navigator.appName == "Microsoft Internet Explorer")
			height += 40;
		else
			height += 20;

		x = parseInt(screen.width / 2.0) - (width / 2.0);
		y = parseInt(screen.height / 2.0) - (height / 2.0);

		resizable = (args && args['resizable']) ? args['resizable'] : "no";
		scrollbars = (args && args['scrollbars']) ? args['scrollbars'] : "yes";

		var modal = "no";
		var win = window.open(options.Filename, "IM_preview", "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=" + modal + ",minimizable=" + resizable + ",modal=" + modal + ",width=" + width + ",height=" + height + ",resizable=" + resizable);
	},

	getFileStats : function(options)
	{
		var text = options.Filename+ " &nbsp; "+options.LastModified+" &nbsp; ";
		text += options.Width+"x"+options.Height+" pixels &nbsp; ";
		text += options.DisplaySize;
		return text;
	},

	showNewFolderForm : function()
	{
		$('image_manager_new_folder').show();
		this.$('NewFolder').focus();
	},

	showUploadForm : function()
	{
		$('image_manager_upload').show();
	},

	NewFolderSubmit : function(ev)
	{
		if(Prado.Validation.isValid(this.options.FormID, 'new_folder'))
		{
			if(this.$('NewFolder').value.trim() != '')
			{
				var manager = this.getManager();
				if(manager)
					manager.showLoadingPanel();
				$('image_manager_new_folder').hide();
			}
		}
	}
},
{
	registry : {},
	files : {},

	getFile : function(element)
	{
		var ID = element.id;
		if(typeof(XLAB6.ImageManagerListView.files[ID]) != "undefined")
			return XLAB6.ImageManagerListView.files[ID];
		else
		{	
			var option;
			eval('option = ('+element.type+')');
			XLAB6.ImageManagerListView.files[ID] = option;
			return option;
		}
			
	}
});

function IM_Click(element,ID)
{
	$$('a.file').each(function(el){ el.className = "file"; });
	element.className="file selected"
	var list = XLAB6.ImageManagerListView;
	var view = list.registry[ID];
	if(view)
		view.thumbnailClicked(list.getFile(element));
	return false;
}

function IM_DblClick(element,ID)
{
	var list = XLAB6.ImageManagerListView;
	var view = list.registry[ID];
	if(view)
		view.thumbnailDblClicked(list.getFile(element));
}