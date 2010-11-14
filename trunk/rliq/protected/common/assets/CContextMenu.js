/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

CContextMenu=Class.create (
{
	initialize: function (options)
	{
		this.options=options || {};
		document.observe('dom:loaded', this.initProtoMenu.bind(this));
	},

	initProtoMenu: function ()
	{
		var menuOptions = Object.extend (this.options, {
			menuItems: this.createMenuItems()
		});

		new Proto.Menu(menuOptions);
	},

	createMenuItems: function ()
	{
		var Items=[];

		this.options.items.each(function(item,index) {
			if (item.separator)
				Items[index] = { separator: true };
			else
				Items[index]={
					name: item.name,
					className: item.className,
					disabled: item.disabled,
					callback: this.itemCallback.bindEvent(this,index)
				};
		},this);
		return Items;
	},

	itemCallback: function (event, index)
	{
		var item=this.options.items[index];
		var doPostBack=true;
		var onclicked=null;
		var element=Event.element(event);
		var options = Object.extend(this.options, { EventParameter : [ index, element.id ] });

		// Call the clientside function first
		if (item) {
			if (typeof(item.onClick)=='function')
				onclicked = item.onClick();
			if (typeof(onclicked)=="boolean") doPostBack=onclicked;
		}
		// Do postback
		if (doPostBack)
			this.onPostBack(event, options)
		if(typeof(onclicked) == "boolean" && !onclicked)
			Event.stop(event)

	},

	onPostBack: function(event,options)
	{
		Prado.PostBack(event, options)
	}
}
);

CActiveContextMenu=Class.create(CContextMenu, {

	initialize: function ($super, options)
	{
		$super(options);
		CActiveContextMenu.register(this);
	},
	
	onPostBack: function (event, options)
	{
		Prado.Callback(this.options.EventTarget, options.EventParameter, null, this.options);
		Event.stop(event);
	}
});

Object.extend(CActiveContextMenu, {
	menus: {},

	register: function (object)
	{
		CActiveContextMenu.menus[object.options.ID]=object;
	},

	reinit: function (id)
	{
		if (CActiveContextMenu.menus[id])
			CActiveContextMenu.menus[id].initProtoMenu();
	}

});
