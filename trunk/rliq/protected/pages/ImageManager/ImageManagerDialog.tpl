<div class="image-manager">
<table class="location-bar">
<tr><td width="1%">
<com:TLabel Text="Location:" ForControl="assetDir" EnableViewState="false" />
</td><td class="location">
<com:TDropDownList ID="assetDir" CssClass="location" EnableViewState="false"  />
</td>
<td class="buttons">

<com:THyperLink
	ID="UpButton"
	NavigateUrl="#"
	ImageUrl=<%~ assets/folder_up.gif %>
	CssClass="image-button" Style="width:18px"
	Attributes.title="Go Up"
	EnableViewState="false" />

<com:THyperLink
	ID="RefreshButton"
	NavigateUrl="#"
	ImageUrl=<%~ assets/arrow_refresh.gif %>
	CssClass="image-button" Style="width:18px"
	Attributes.title="Refresh"
	EnableViewState="false" />

<com:TPlaceHolder Visible=<%= $this->Manager->EnableNewFolder %>>
<div class="separator"></div>

<com:THyperLink
	ID="NewFolderButton"
	NavigateUrl="#"
	CssClass="image-button" Style="width:83px"
	Attributes.title="New Folder"
	EnableViewState="false">
<img src="<%~ assets/folder_add.gif %>" />
<span class="caption">New Folder</span>
</com:THyperLink>

</com:TPlaceHolder>

<com:TPlaceHolder Visible=<%= $this->Manager->EnableUpload %>>
<div class="separator"></div>

<com:THyperLink
	ID="UploadButton"
	NavigateUrl="#"
	CssClass="image-button" Style="width:98px"
	Attributes.title="Upload Image"
	EnableViewState="false">
<img src="<%~ assets/picture_add.gif %>" />
<span class="caption">Upload Image</span>
</com:THyperLink>

</com:TPlaceHolder>

</td></tr></table>
<com:TInlineFrame
	ID="listFrame"
	FrameUrl=<%= $this->getManager()->getImageListViewUrl() %>
	CssClass="content-frame" ShowBorder="false"
	EnableViewState="false" />

<com:TPanel ID="StatusPanel" CssClass="status" EnableViewState="false">&nbsp;</com:TPanel>

<div class="action-buttons">

	<com:TButton ID="OKButton" Text="Insert" />
	<com:TButton ID="CancelButton" Text="Close" />
</div>

<com:TPanel ID="LoadingPanel" CssClass="loading-panel" EnableViewState="false">
<com:TLabel ID="loadingStatus" Text="Loading" EnableViewState="false" />
<img src="<%~ assets/dots.gif %>" />
</com:TPanel>
</div>