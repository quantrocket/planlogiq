<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent">

<com:TCallbackOptions ID="options">
	<prop:ClientSide.RequestTimeOut>120000</prop:ClientSide.RequestTimeOut>
</com:TCallbackOptions>

<com:TTabPanel>
<com:TTabView Caption=<%[ Basic Information ]%> >
<table>

<tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ Seasons ]%></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="SaisonListe" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" AllowPaging="true" AllowSorting="true" PageSize="5" AutoGenerateColumns="false" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating" OnPageIndexChanged="page.SaisonContainer.SaisonListe_PageIndexChanged" OnEditCommand="load_saison">
    <com:TActiveBoundColumn HeaderText=<%[ ID ]%> DataField="idta_saisonalisierung" ID="lst_idta_saison" />
    <com:TActiveBoundColumn HeaderText=<%[ Name ]%> DataField="sai_name" />
    <com:TActiveTemplateColumn>
        <prop:HeaderTemplate><%[ Field ]%></prop:HeaderTemplate>
        <prop:ItemTemplate>
            <com:TActiveLabel Text=<%#FeldfunktionRecord::finder()->findByPK($this->Parent->DataItem->idta_feldfunktion)->ff_name%> />
        </prop:ItemTemplate>
    </com:TActiveTemplateColumn>
    <com:TActiveTemplateColumn>
        <prop:HeaderTemplate><%[ Start Node ]%></prop:HeaderTemplate>
        <prop:ItemTemplate>
            <com:TActiveLabel Text=<%#StrukturRecord::finder()->findByPK($this->Parent->DataItem->idtm_struktur)->struktur_name%> />
        </prop:ItemTemplate>
    </com:TActiveTemplateColumn>
    <com:TActiveEditCommandColumn HeaderText=<%[ Action ]%> EditText="view" />
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>


<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SCNewButtonClicked"/><com:TActiveButton OnCallback="SCNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SCSavedButtonClicked"/><com:TActiveButton OnCallback="SCSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/run.png" OnCallback="SCRunSeasonsButtonClicked"/><com:TActiveButton OnCallback="SCRunSeasonsButtonClicked" Text=<%[ run ]%> CssClass="windowcontent-button" ActiveControl.CallbackOptions="options"/>
</td></tr>

<tr>
<td><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="sai_sai_name" CssClass="inputlarge" /></td>
<td id="infoboxsmall">
<%[ Please give the season a name, that you can relate to ]%>
</td>
</tr>

<tr>
<td><%[ Logical Field ]%>:</td>
<td><com:TActiveDropDownList Id="sai_idta_feldfunktion" CssClass="inputlarge" /></td>
<td id="infoboxsmall">
<%[ Here you select the logical field, that will be affected by the seasonality ]%>
</td>
</tr>

<tr>
<td><%[ Start Node ]%>:</td>
<td colspan="2">
    <com:TActiveTextBox Id="sai_idtm_struktur" CssClass="inputsmall" />
    <com:Application.pages.container.SelectFromTreeSaisonContainer ID="SelectFromTreeContainer" />
    <com:TActiveTextBox ID="SFTauf_tabelle" Text="sai_idtm_struktur" Visible="false"/>
    <com:TActiveTextBox ID="SFTstart_id" Text="0" Visible="false"/>
</td>
</tr>

</table>
</com:TTabView>
<com:TTabView Caption=<%[ Season ]%> >

<table width="100%">

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SCNewButtonClicked"/><com:TActiveButton OnCallback="SCLoadButtonClicked" Text=<%[ load ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCallback="SCClosedButtonClicked"/><com:TActiveButton OnCallback="SCClosedButtonClicked" Text=<%[ close ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
    <td colspan="3">
        <com:TActiveDatagrid ID="SaisonTTListe" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" AllowPaging="true" AllowSorting="true" PageSize="8" AutoGenerateColumns="true" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating" OnPageIndexChanged="page.SaisonContainer.SaisonTTListe_PageIndexChanged" />
    </td>
</tr>

</table>

</com:TTabView>
</com:TTabPanel>
<com:TActiveTextBox id="sai_idta_saisonalisierung" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="saison_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
</com:TActivePanel>