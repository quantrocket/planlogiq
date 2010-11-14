<com:TActivePanel Height="340px" ScrollBars="Auto">
<table>

<tr><td colspan="3" class="portlet-title"><%[ Pivot Report Dimensions ]%></td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCommand="PBDNewButtonClicked"/>new
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCommand="PBDSavedButtonClicked"/>save
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCommand="PBDDeleteButtonClicked"/>delete
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/button_ok.png" OnCommand="PBDClosedButtonClicked"/>close
</td></tr>

<tr>
<td><%[ Dimension ]%>:</td>
<td><com:TActiveDropDownList Id="PBDedidta_stammdaten_group" CssClass="mandantorylarge" /></td>
<td id="infoboxsmall">
<%[ The dimension viewed inside the pivot ]%>
</td>
</tr>

<tr>
<td><%[ Parent Colum ]%>:</td>
<td><com:TActiveDropDownList Id="PBDedparent_idtm_pivot" CssClass="mandantorylarge" /></td>
<td id="infoboxsmall">
<%[ The value will be viewed inside the pivot ]%>
</td>
</tr>


<tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ Dimensions ]%></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="PivotListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.PivotContainer.PivotList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_Pivot" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="pbd_idtm_pivot" DataField="idtm_pivot" HeaderText="ID" SortExpression="idtm_pivot" />
	<com:TActiveBoundColumn ID="pbd_stammdaten_group_name" DataField="stammdaten_group_name" HeaderText="Stammdaten Gruppe" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="PBDedPivot_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBDedpivot_position" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBDedpivot_filter" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBDedidtm_pivot" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBDedidta_pivot_bericht" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="PBDedidtm_user" CssClass="hiddeninput" visible="false" Text=<%= $this->User->GetUserId($this->User->Name) %> />
</com:TActivePanel>