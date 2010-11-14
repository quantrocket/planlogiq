
<com:TActiveTextBox Id="DMSAufTabelle" Text="tmp" Visible="false"/>
<com:TActiveTextBox Id="DMSAufId" Text="0" Visible="false"/>

<com:TActiveRepeater Id="DMSFileBrowserRepeater">
    <prop:ItemTemplate>
        <table width="75%">
        <tr style="height:24px;">
            <td style="width:20px"><com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/mimetypes/<%#$this->data['filetype']%>.png"/></td>
            <td><a href="/rliq/assets/dms/<%#$this->data['internalfile']%>" target="_blank"><%#$this->data['filename']%></a></td>
            <td style="width:80px">S: <%#$this->data['filesize']%> kb</td>
            <td style="width:70px">D: <%#$this->data['filedate']%></td>
            <td style="width:40px">T: <%#$this->data['filetime']%></td>
            <td style="width:40px"><com:TActiveLinkButton onCallback="parent.parent.parent.DMSremoveFile" ActiveControl.CallbackParameter=<%#$this->data['internalfile']%> Text=<%[ delete ]%> CssClass="wcbutton"/></td>
        </tr>
        </table>
    </prop:ItemTemplate>
</com:TActiveRepeater>

<com:TActiveLinkButton Text="Datei anhängen" OnCallback="showUploadDialog" CssClass="wcbutton" ToolTip="Datei anhängen"/>
<com:TActivePanel Id="UploadDialog" Display="None">
<fieldset>
        <legend><%[ Datei hochladen ]%></legend>
            <%[ FileManager ]%>:<com:TFileUpload OnFileUpload="fileUploaded" id="UPFile" MaxFileSize="5000000" />
            <com:TButton Text="Upload" id="UPFileButton" CssClass="windowcontent-button"/>
                <com:TActiveLabel Id="UploadFileError" Text="" />
                &nbsp;<com:TActiveButton Text="abbrechen" OnCallback="hideUploadDialog" CssClass="windowcontent-button"/>
</fieldset>
</com:TActivePanel>

