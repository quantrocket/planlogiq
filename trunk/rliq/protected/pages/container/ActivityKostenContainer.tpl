<table>

<tr><td colspan="3" class="portlet-title"><%[ plan ]%> : <%[ cost per activity ]%></td></tr>

<tr>
<td colspan="3"><com:TActiveImage ID="CostActivityImage" width="600" height="300"/></td>
</tr>

<tr><td colspan="3" class="portlet-title"><%[ Values ]%></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:XActiveDataGrid ID="ActivityKostenListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="50" OnPageIndexChanged="page.ActivityKostenContainer.actcostList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.ActivityKostenContainer.actcostList_Load" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:XActiveBoundColumn ID="lst_idtm_activity" DataField="idtm_activity" HeaderText="ID" SortExpression="idtm_activity" />
	<com:XActiveBoundColumn ID="lst_act_name" DataField="act_name" HeaderText="Bezeichnung" />
	<com:XActiveBoundColumn ID="lst_act_dauer" DataField="act_dauer" HeaderText="Dauer" />
	<com:XActiveBoundColumn ID="lst_ttact_kosten" DataField="ttact_kosten" HeaderText="Kosten" />
</com:XActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>