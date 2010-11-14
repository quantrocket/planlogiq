<com:TActivePanel Height="340px" ScrollBars="Auto">
<table>

<tr><td colspan="3" class="portlet-title">Pivot Bericht</td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCommand="PBNewButtonClicked"/>new
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCommand="PBSavedButtonClicked"/>save
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCommand="PBClosedButtonClicked"/>close
</td></tr>

<tr>
<td><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="PBedpivot_bericht_name" CssClass="inputlarge" /></td>
<td id="infoboxsmall">
<%[ Give the pivot report a name ]%>
</td>
</tr>

<tr>
<td><%[ Field ]%> / <%[ Variante ]%>:</td>
<td><com:TActiveDropDownList Id="PBedidta_feldfunktion" CssClass="mandantory" /> / <com:TActiveDropDownList Id="PBedidta_variante" CssClass="mandantory" /></td>
<td id="infoboxsmall">
<%[ The value will be viewed inside the pivot ]%>
</td>
</tr>

<tr>
<td><%[ Field operator ]%>:</td>
<td><com:TActiveDropDownList Id="PBedpivot_bericht_operator" CssClass="mandantorylarge" /></td>
<td id="infoboxsmall">
<%[ The value will be viewed inside the pivot ]%>
</td>
</tr>


<tr><td colspan="3" class="portlet-title">Liste der Risiken/Chancen</td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="PivotBerichtListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.PivotBerichtContainer.pivotberichtList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_pivotbericht" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="pb_idta_pivot_bericht" DataField="idta_pivot_bericht" HeaderText="ID" SortExpression="idta_pivot_bericht" />
	<com:TActiveBoundColumn ID="pb_pivot_bericht_name" DataField="pivot_bericht_name" HeaderText="Bezeichnung" />
	<com:TActiveBoundColumn ID="pb_pivot_bericht_operator" DataField="pivot_bericht_operator" HeaderText="Operator" />
	<com:TActiveBoundColumn ID="pb_idtm_user" DataField="idtm_user" HeaderText="UserID" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="PBedpivotbericht_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBedidta_pivot_bericht" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBedidtm_user" CssClass="hiddeninput" visible="false" Text=<%= $this->User->GetUserId($this->User->Name) %> />
<com:TActiveTextBox id="PBedpivot_bericht_cdate" CssClass="hiddeninput" visible="false" Text=<%= date('Y-m-d') %> />
</com:TActivePanel>