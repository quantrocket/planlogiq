<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent">
<table width="100%">

<tr><td colspan="4" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="FFNewButtonClicked"/><com:TActiveButton OnCallback="FFNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="FFSavedButtonClicked"/><com:TActiveButton OnCallback="FFSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="FFDeleteButtonClicked"/><com:TActiveButton OnCallback="FFDeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td colspan="4">
<com:TPanel>
<com:TActiveDataGrid ID="FeldfunktionListe" AllowPaging="true" AllowSorting="false" PageSize="5" OnPageIndexChanged="page.FeldfunktionContainer.FeldfunktionList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_Feldfunktion" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="FF_idta_feldfunktion" DataField="idta_feldfunktion" HeaderText="ID" SortExpression="idta_feldfunktion" />
	<com:TActiveBoundColumn ID="FF_ff_name" DataField="ff_name" HeaderText="Name" />
	<com:TActiveBoundColumn ID="FF_ff_type" DataField="ff_type" HeaderText="Type" HeaderStyle.Width="70px"/>
	<com:TActiveBoundColumn ID="FF_ff_operator" DataField="ff_operator" HeaderText="Operator" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

<tr><td colspan="4" class="portlet-title"><%[ Einstellungen ]%></td></tr>

<tr>
<td class="FormLabel" style="background:#efefef"><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="FFedff_name" CssClass="mandantorylarge" /></td>
<td class="FormLabel" style="background:#efefef"><%[ Type ]%>:</td>
<td><com:TActiveDropDownList Id="FFedff_type" CssClass="mandantory" /></td>
</tr>

<tr>
<td class="FormLabel" style="background:#efefef"><%[ Cashbalance ]%>:</td>
<td><com:TActiveDropDownList Id="FFedff_cashbalance" CssClass="mandantory" /></td>
<td class="FormLabel" style="background:#efefef"><%[ recalc opening ]%>:</td>
<td><com:TActiveCheckBox Id="FFedff_calcopening" />
Order: <com:TActiveTextBox Id="FFedff_order" CssClass="inputsmall" /></td>
</tr>


<tr>
<td class="FormLabel" style="background:#efefef"><%[ Pre-ID ]%>:</td>
<td><com:TActiveDropDownList Id="FFedpre_idta_feldfunktion" CssClass="mandantory" /></td>
<td class="FormLabel" style="background:#efefef"><%[ Factor ]%> / <%[ Weight ]%>:</td>
<td>
    <com:TActiveDropDownList Id="FFedff_operator" />
    <com:TActiveTextBox Id="FFedff_faktor" CssClass="mandantory" />
</td>
</tr>

<tr>
<td class="FormLabel" style="background:#efefef"><%[ Gewichtung ]%>:</td>
<td><com:TActiveTextBox Id="FFedff_gewichtung" CssClass="mandantorysmall" /></td>
<td class="FormLabel" style="background:#efefef"><%[ Default value ]%>:</td>
<td>
    <com:TActiveTextBox Id="FFedff_default" CssClass="inputnormal" />
</td>
</tr>

<tr>
<td class="FormLabel" style="background:#efefef"><%[ Fix ]%>:</td>
<td><com:TActiveCheckBox Id="FFedff_fix" />/READONLY<com:TActiveCheckBox Id="FFedff_readonly" /></td>
<td class="FormLabel" style="background:#efefef"><%[ Short Description ]%>:</td>
<td><com:TActiveTextBox Id="FFedff_descr" CssClass="inputnormal" /></td>
</tr>

<tr><td colspan="4">
<com:TActivePanel ID="COLLECTOR" visible="1">

<table width="100%">

<tr><td colspan="3" class="portlet-title"><%[ Collector Settings ]%></td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="COLNewButtonClicked"/><com:TActiveButton OnCallback="COLNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="COLSavedButtonClicked"/><com:TActiveButton OnCallback="COLSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="COLDeleteButtonClicked"/><com:TActiveButton OnCallback="COLDeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td class="FormLabel"><%[ Field ]%>/<%[ Base operator ]%>:</td>
<td>
    <com:TActiveDropDownList Id="COLedcol_idtafeldfunktion" CssClass="mandantory" />/
    <com:TActiveDropDownList Id="COLedcol_operator" /></td>
<td id="infoboxsmall">
<%[ this is a special function, only used by administrator ]%>
</td>
</tr>


<tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ Calculation ]%></td></tr>
<tr>
<td colspan="3">
<com:TActivePanel>
<com:TActiveDataGrid ID="CollectorListe" AllowPaging="true" AllowSorting="false" PageSize="5" OnPageIndexChanged="page.FeldfunktionContainer.CollectorList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_Collector" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="COL_idta_collector" DataField="idta_collector" HeaderText="ID" SortExpression="idta_collector" />
	<com:TActiveBoundColumn ID="COL_col_operator" DataField="col_operator" HeaderText="Operator" />
	<com:TActiveBoundColumn ID="COL_ff_name" DataField="ff_name" HeaderText="Feld" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TActivePanel>
</td>
</tr>

<com:TActiveTextBox id="COLedidta_feldfunktion" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="COLedcollector_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="COLedidta_collector" CssClass="hiddeninput" visible="false" />
</table>
</com:TActivePanel>
</td></tr>
</table>

<com:TActiveTextBox id="FFedfeldfunktion_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="FFedidta_feldfunktion" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="FFedidta_struktur_type" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="FFedidtm_user" CssClass="hiddeninput" visible="false" Text=<%= $this->User->GetUserId($this->User->Name) %> />
</com:TActivePanel>