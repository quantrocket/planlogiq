<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent">

<com:TTabPanel>
<com:TTabView Caption=<%[ Planungssichten ]%>>

<fieldset>
<table width="100%">

<tr><td colspan="3" class="mytoolbar">
<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png"/>
<com:TActiveButton OnCommand="pageAction" CommandName="new" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png"/>
<com:TActiveButton OnCommand="pageAction" CommandName="save" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td class="FormLabel"><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="RCedsts_name" CssClass="inputlarge" /></td>
</tr>

<tr>
<td class="FormLabelMa"><%[ Aktiv ]%>:</td>
<td><com:TActiveCheckBox Id="RCedsts_aktiv" CssClass="inputnormal" /></td>
</tr>

<tr>
<td class="FormLabelMa"><%[ Reporting ]%>:</td>
<td><com:TActiveCheckBox Id="RCedsts_reporting" CssClass="inputnormal" /></td>
</tr>

</table>

</fieldset>

<div style="background-color:#D3E5E8;border:1px 1px 1px 1px solid #000000;">
<%[ Liste der ]%> <%[ Planungssichten ]%>
</div>

<com:TPanel>
<com:TActiveDataGrid ID="StammdatensichtListe"
        PagerStyle.Mode="Numeric"
        PagerStyle.HorizontalAlign="Right"
        AllowPaging="true"
        AllowSorting="false"
        PageSize="5"
        OnPageIndexChanged="StammdatensichtList_PageIndexChanged"
        AutoGenerateColumns="false"
        OnEditCommand="pageAction"
        CssClass="datagrid"
        PagerStyle.CssClass="pager"
        AlternatingItemStyle.CssClass="alternating">
            <com:TActiveBoundColumn ID="lst_idta_stammdatensicht" DataField="idta_stammdatensicht" HeaderText="ID" SortExpression="idta_stammdatensicht" />
            <com:TActiveBoundColumn ID="lst_sts_name" DataField="sts_name" HeaderText="Bezeichnung" HeaderStyle.Width="400px"/>
            <com:TActiveTemplateColumn>
            <prop:HeaderTemplate><%[ Aktiv ]%></prop:HeaderTemplate>
            <prop:ItemTemplate>
                <com:TActiveLabel Text=<%#$this->Parent->DataItem->sts_aktiv%> />
            </prop:ItemTemplate>
            </com:TActiveTemplateColumn>
            <com:TActiveTemplateColumn>
            <prop:HeaderTemplate><%[ Reporting ]%></prop:HeaderTemplate>
            <prop:ItemTemplate>
                <com:TActiveLabel Text=<%#$this->Parent->DataItem->sts_reporting%> />
            </prop:ItemTemplate>
            </com:TActiveTemplateColumn>
            <com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>


<com:TActiveTextBox id="RCedstammdatensicht_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidta_stammdatensicht" CssClass="hiddeninput" visible="false" />

</com:TTabView>

</com:TTabPanel>

</com:TActivePanel>