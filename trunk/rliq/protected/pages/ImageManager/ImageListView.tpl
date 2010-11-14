<div class="image-list">
<com:TRepeater ID="list"
	OnItemCreated="list_OnItemCreated">
	<prop:ItemTemplate>
		<com:THyperLink ID="link"
			CssClass="file"
			NavigateUrl="#" EnableViewState="false">
			<div class="thumbnail">
				<com:TImage ID="thumbnail" />
			</div>
			<span class="caption">
				<%# $this->DataItem->getFileName() %>
			</span>
		</com:THyperLink>
	</prop:ItemTemplate>
	<prop:EmptyTemplate>
		<div class="no-files"><span class="caption">No files found.</span></div>
	</prop:EmptyTemplate>
</com:TRepeater>
</div>

<com:TPlaceHolder Visible=<%= $this->Manager->EnableUpload %>>
<div id="image_manager_upload" class="dialog-panel upload-images" style="display:none">
<h3 class="title">Update Image Files</h3>
	<div class="content-frame">
		<div class="content">
		<div class="upload file-1">
			<com:TLabel Text="File 1:" ForControl="upload1" />
			<com:TFileUpload ID="upload1" onFileUpload="uploadFiles" />
		</div>
		<div class="upload file-2">
			<com:TLabel Text="File 2:" ForControl="upload2" />
			<com:TFileUpload ID="upload2" onFileUpload="uploadFiles" />
		</div>
		<div class="upload file-3">
			<com:TLabel Text="File 3:" ForControl="upload3"/>
			<com:TFileUpload ID="upload3" onFileUpload="uploadFiles"/>
		</div>
		</div>
		<div class="buttons">
			<com:TButton ID="UploadButton" Text="OK" OnClick="uploadCompleted"/>
			<input type="button" value="Cancel" onclick="$('image_manager_upload').hide()"/>
		</div>
	</div>
</div>
</com:TPlaceHolder>

<com:TPlaceHolder Visible=<%= $this->Manager->EnableNewFolder %>>
<div id="image_manager_new_folder" class="dialog-panel new-folder" style="display:none">
<com:TPanel DefaultButton="NewFolderOKButton">
<h3 class="title">Create New Folder</h3>
	<div class="content-frame">
		<div class="content">
			<com:TLabel Text="Folder Name:" ForControl="NewFolder" />
			<com:TTextBox ID="NewFolder" />
			<com:TRequiredFieldValidator
				ValidationGroup="new_folder"
				FocusOnError="true"
				ControlToValidate="NewFolder" ErrorMessage="*" />
		</div>
		<div class="buttons">
			<com:TButton ID="NewFolderOKButton" Text="OK" ValidationGroup="new_folder" OnClick="createNewFolder" />
			<input type="button" value="Cancel" onclick="$('image_manager_new_folder').hide()" />
		</div>
	</div>
</com:TPanel>
</div>
</com:TPlaceHolder>

<com:TPlaceHolder Visible=<%= $this->Manager->EnableDelete %>>
<div id="image_manager_delete" class="dialog-panel delete-file" style="display:none">
<h3 class="title">Delete File/Folder</h3>
	<div class="content-frame">
		<div class="content">
			Delete "<code><span id="delete_filename">file</span></code>"?
		</div>
		<com:THiddenField ID="FileToDelete" />
		<div class="buttons">
			<com:TButton ID="DeleteButton" Text="Delete" OnClick="deleteSelectedFile" />
			<input type="button" id="delete_cancel_button"
				value="Cancel" onclick="$('image_manager_delete').hide()" />
		</div>
	</div>
</div>

</com:TPlaceHolder>