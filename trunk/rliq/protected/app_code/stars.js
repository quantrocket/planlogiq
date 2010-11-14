/*  Starry Widget
 * 
 * @author Edward Stow <edward@etk.com.au>
 * @author Bradley Booms <bradley.booms@gmail.com>
 *
 *  Javascript component based upon starry control version 1.1 (April 27, 2007)
 *  (c) 2007 Chris Iufer <chris@duarte.com>
 *  Starry is freely distributable under the terms of an MIT-style license.
 *  See the Duarte Design web site: http://www.duarte.com/starry/
 *
 *  This component is released under a BSD licence compatible with the
 *  Prado license at http://pradosoft.com/license/
 *
 *  Eternity Technologies Pty Ltd (www.etk.com.au) retain copyright.
 *  
 */


var EtkStarry = Class.create({

	initialize : function(options)
	{
		//this.onInit(options);
		this.options = options|| {};
		this.element = $(options.ID);
		this.hidden = $(options.ID + '_' + options.ID + '_hidden');
		
		this.maxStars = Number(options.MaxStars);
		this.showNullStar = Boolean(options.ShowNullStar);
		this.showHalfStar = Boolean(options.ShowHalfStar);
		this.starRating = Number(options.StarRating);
		this.enabled = Boolean(options.Enabled);
		this.autoPostBack = Boolean(options.AutoPostBack);
		
		this.stars = [];

		this.backgroundOffsets = {'dot' :0, 'grey' :1,'standard': 2,
						'outline': 3, 'half-grey': 4, 'half-standard': 5 } ;	
	
		if (this.enabled)
		{
			this.enableStars();
		}
	},

	/* Add observers to the stars.
	*/
	enableStars : function()
	{
		for (var i = 0; i <= this.maxStars; i++)
		{
			this.stars[i] = $(this.options.ID + "_" + this.options.ID + "_" + String(i));
		}

		this.updateStars(this.starRating, 'standard');
		
		if (this.showNullStar)
		{
			var elem = this.stars[0];
			elem.appendChild(this.starOverlay(elem.id + '_' + 0, 30));	
		}

		for (var i = 1; i <= this.maxStars; i++)
		{
			var elem = this.stars[i];
			if (this.showHalfStar)
			{
				elem.appendChild(this.starOverlay(elem.id + '_' + (i - 0.5), 15));	
				elem.appendChild(this.starOverlay(elem.id + '_' + i, 15));				
			}
			else
			{
				elem.appendChild(this.starOverlay(elem.id + '_' + i, 30));	
			}
		}
	},
	
	starOverlay : function(id, width)
	{
		var starOverlay = new Element('div', {id: id, 'class': 'star-overlay',
			'style': 'width: ' + width + 'px; height: 30px ; float: left'});
		
		starOverlay.style.cursor = 'pointer';
		
		var self = this;
		starOverlay.observe('click', function(event)
		{
			var rating = Number(event.element().id.split('_').last());
			self.updateStars(rating, 'standard');
			self.starRating = rating;
			self.hidden.value = rating;	
			self.notifyPostBack(event);
		});
			
		starOverlay.observe('mouseover', function(event)
		{
			var rating = Number(event.element().id.split('_').last());
			self.updateStars(rating, 'grey');
		});
	
		starOverlay.observe('mouseout', function(event)
		{
			self.updateStars(self.starRating, 'standard');
		});
		return starOverlay;
	},
	
	notifyPostBack : function(event)
	{
		if (this.autoPostBack==true)
		{
			Prado.PostBack(event, this.options);
		}		
	},
	
	updateStars : function(rating, starType)
	{
		if (this.showHalfStar && this.isStarRatingSomethingAndHalf(rating))
		{
			this.updateStarsInRange(1, Math.floor(rating), starType);
			this.updateStarsInRange(Math.floor(rating) + 1 ,Math.floor(rating) + 1 , 'half-' + starType);			
			this.updateStarsInRange(Math.floor(rating) + 2, this.maxStars, 'dot');		
		}
		else
		{
			this.updateStarsInRange(1, rating, starType);
			this.updateStarsInRange(rating + 1, this.maxStars, 'dot');			
		}
	},
	
	updateStarsInRange : function(start, stop, starType )
	{
		var backgroundPosition = this.backgroundOffset(starType);
		for(var i = start ; i <= stop ; i++)
		{
			this.stars[i].style.backgroundPosition = backgroundPosition;
		}		
	},
	
	backgroundOffset : function(starType)
	{
		return '0 -' + String(this.backgroundOffsets[starType] * 30) + 'px';
	},
	
	setStarRating : function(value)
	{
		this.starRating = value;
		this.updateStars(value, 'standard');
	},
	
	getStarRating : function()
	{
		return this.starRating;
	},
	
	isStarRatingSomethingAndHalf: function(rating) 
	{
		return ((rating * 10) % 10) > 0;
	}
});


var EtkActiveStarry = Class.create(EtkStarry,
{
	notifyPostBack : function(event)
	{
		if (this.autoPostBack==true)
		{
			Prado.Callback(this.options.EventTarget, null, null, this.options);
		}
	}
});