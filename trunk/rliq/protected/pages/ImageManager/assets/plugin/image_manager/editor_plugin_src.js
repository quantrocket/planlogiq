/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('image_manager');

	tinymce.create('tinymce.plugins.ExamplePlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceImageManager', function() {
				ed.windowManager.open({
					file : ed.getParam('image_manager_url', '../../plugins/image_manager/popup.htm'),
					width : 640 + parseInt(ed.getLang('image_manager.delta_width', 0)),
					height : 420 + parseInt(ed.getLang('image_manager.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register example button
			ed.addButton('image_manager', {
				title : 'image_manager.desc',
				cmd : 'mceImageManager',
				image : url + '/img/pictures.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('image_manager', n.nodeName == 'IMG');
			});
		},


		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Advanced image with Image Manager',
				author : 'Wei Zhuo',
				authorurl : 'http://xlab6.com',
				infourl : 'http://xlab6.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},
		
		
		
		/**
		 * Executes a specific command, this function handles plugin commands.
		 *
		 * @param {string} command Command name to be executed.
		 * @param {string} user_interface True/false if a user interface should be presented.
		 * @param {mixed} value Custom value argument, can be anything.
		 * @return true/false if the command was executed by this plugin or not.
		 * @type
		 */
		execCommand : function(command, user_interface, value) {
			// Handle commands
			switch (command) {
				// Remember to have the "mce" prefix for commands so they don't intersect with built in ones in the browser.
				case "mceImageManagerInsertImage":
					// Insert image into HTML
					if(typeof(value) == "string" && value.length > 0)
					{
						var inst = tinyMCE.activeEditor; //old command: tinyMCE.getInstanceById(editor_id);
						inst.execCommand('mceInsertContent', false, value);
					}
	
					return true;
			}
	
			// Pass to next handler in chain
			return false;
		},
		
		monitorImages : function()
		{
			var doc = tinyMCE.selectedInstance.getDoc();
			var imgs = doc.getElementsByTagName("img"), src, i;
			for (i=0; i<imgs.length; i++)
			{
				var alt = tinyMCE.getAttrib(imgs[i],'alt');
				if(alt.indexOf('IMG://') > -1)
					this.updateImage(imgs[i],doc,alt.substring(6))
			}
		},
	
		updateImage : function(img,doc,url)
		{
			var atitle = '', imgtitle = img.title;
	
			//change
			img.alt = img.title;
			img.src = url;
	
			if(img.parentNode && img.parentNode)
			{
				var wrap = img.parentNode.parentNode;
				atitle = wrap.title;
			}
	
			//clean up for FireFox
			if(atitle == imgtitle)
			{
				var newImg = doc.createElement("img");
				newImg.title = img.title;
				newImg.src = url;
				newImg.alt = img.title;
				var rng = doc.createRange();
				rng.selectNode(wrap);
				rng.deleteContents();
				rng.insertNode(newImg);
				var as = doc.getElementsByTagName("a"), i;
				for(i = 0; i<as.length;i++)
				{
					if(as[i].title == imgtitle);
					{
						rng = document.createRange();
						rng.selectNode(as[i]);
						rng.deleteContents();
					}
				}
			}
		},
	
		handleEvent : function(e)
		{
			if(e.type == 'dragdrop' || e.type == 'drop')
			{
				var self = this;
				setTimeout(function()
				{
					self.monitorImages();
				}, 50);
			}
			return true; // Pass to next handler
		}

		
	});


	// Register plugin
	tinymce.PluginManager.add('image_manager', tinymce.plugins.ExamplePlugin);
})();