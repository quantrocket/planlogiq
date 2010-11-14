<table width="100%">

<tr><td colspan="3" class="portlet-title">Dimension an Struktur</td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="RCNewButtonClicked"/><com:TActiveButton OnCallback="RCNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="RCSavedButtonClicked"/><com:TActiveButton OnCallback="RCSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCallback="RCDeleteButtonClicked"/><com:TActiveButton OnCallback="RCDeleteButtonClicked" Text=<%[ remove ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td><%[ Children ]%>:</td>
<td><com:TActiveDropDownList Id="RCedidta_stammdaten_group" /></td>
<td id="infoboxsmall">
<%[ Declare the auto-children for this element ]%>
</td>
</tr>


<tr><td colspan="3" class="windowcontent-title"><%[ List of attached Dimensions ]%></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="StrukturStammdatenGroupListe" AllowPaging="true" AllowSorting="false" PageSize="10" OnPageIndexChanged="page.StrukturStammdatenGroupContainer.StrukturStammdatenGroupList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.StrukturStammdatenGroupContainer.load_StrukturStammdatenGroup" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="lst_idtm_struktur_has_ta_stammdaten_group" DataField="idtm_struktur_has_ta_stammdaten_group" HeaderText="ID" SortExpression="idtm_struktur_stammdaten_group" />
	<com:TActiveBoundColumn ID="lst_stammdaten_group_name" DataField="stammdaten_group_name" HeaderText="Dimension" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

<tr><td colspan="3" class="windowcontent-title"><%[ List of possible Basiselements ]%></td></tr>

<tr><td colspan="3" class="mytoolbar"><com:TActiveButton OnCallback="page.StrukturStammdatenGroupContainer.create_StammdatenAll" Text=<%[ apply all ]%> CssClass="windowcontent-button"/></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="SGStammdatenListe" AllowPaging="true" AllowSorting="false" PageSize="20" OnPageIndexChanged="page.StrukturStammdatenGroupContainer.StammdatenList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.StrukturStammdatenGroupContainer.create_Stammdaten" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="lst_idtm_stammdaten" DataField="idtm_stammdaten" HeaderText="ID" SortExpression="idtm_stammdaten" />
	<com:TActiveBoundColumn ID="lst_stammdaten_name" DataField="stammdaten_name" HeaderText="Bezeichnung" />
	<com:TActiveBoundColumn ID="lst_stammdaten_key_extern" DataField="stammdaten_key_extern" HeaderText="ID Extern" />
	<com:TActiveBoundColumn ID="lst_ttstammdaten_created" DataField="ttstammdaten_created" HeaderText="angelegt" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="create" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>


</table>

<com:TActiveTextBox id="RCedidtm_struktur" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedstruktur_stammdaten_group_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidtm_struktur_has_ta_stammdaten_group" CssClass="hiddeninput" visible="false" />
