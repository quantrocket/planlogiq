<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent">
<table width="100%">

<tr><td colspan="4">
<div class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SSNewButtonClicked"/><com:TActiveButton OnCallback="SSNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SSSavedButtonClicked"/><com:TActiveButton OnCallback="SSSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="SSDeleteButtonClicked"/><com:TActiveButton OnCallback="SSDeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
</div>
</td></tr>

<tr>
<td class="FormLabel"><%[ Field ]%>:</td>
<td><com:TActiveDropDownList Id="SSedidta_feldfunktion" CssClass="mandantory" /></td>
<td class="FormLabel"><%[ Map to Element ]%>:</td>
<td><com:TActiveDropDownList Id="SSedidtm_struktur_to" CssClass="mandantorylarge" /></td>
</tr>


<tr><td colspan="4" class="portlet-title"><%[ List of ]%> <%[ Mappings ]%></td></tr>

<tr>
<td colspan="4">
<com:TPanel>
<com:TActiveDataGrid ID="StrukturStrukturListe" AllowPaging="true" AllowSorting="false" PageSize="10" OnPageIndexChanged="page.StrukturStrukturContainer.StrukturStrukturList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_StrukturStruktur" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="ss_idtm_struktur_tm_struktur" DataField="idtm_struktur_tm_struktur" HeaderText="ID" SortExpression="idtm_struktur_tm_struktur" />
	<com:TActiveBoundColumn ID="ss_idtm_struktur_from" DataField="idtm_struktur_from" HeaderText="From" />
	<com:TActiveBoundColumn ID="ss_idtm_struktur_to" DataField="idtm_struktur_to" HeaderText="To" />
        <com:TActiveBoundColumn ID="ss_idta_feldfunktion" DataField="idta_feldfunktion" HeaderText="Field" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="SSedstrukturstruktur_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SSedidtm_struktur_tm_struktur" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SSedidtm_struktur_from" CssClass="hiddeninput" visible="false" />
</com:TActivePanel>