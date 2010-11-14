<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent" EnableViewState="True">

<com:TCallbackOptions ID="options">
	<prop:ClientSide.RequestTimeOut>720000</prop:ClientSide.RequestTimeOut>
</com:TCallbackOptions>

<com:TTabPanel>
<com:TTabView Caption=<%[ Basic Information ]%> >
<table>

<tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ Continuance ]%></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="FortschreibungListe" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" AllowPaging="true" AllowSorting="true" PageSize="12" AutoGenerateColumns="false" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating" OnPageIndexChanged="page.FortschreibungContainer.FortschreibungListe_PageIndexChanged" OnEditCommand="load_fortschreibung">
    <com:TActiveBoundColumn HeaderText=<%[ ID ]%> DataField="idta_fortschreibung" ID="lst_idta_fortschreibung" />
    <com:TActiveBoundColumn HeaderText=<%[ Name ]%> DataField="for_name" />
    <com:TActiveBoundColumn HeaderText=<%[ Faktor ]%> DataField="for_faktor" />
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
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="FCNewButtonClicked"/><com:TActiveButton OnCallback="FCNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="FCSavedButtonClicked"/><com:TActiveButton OnCallback="FCSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/run.png" OnCallback="FCRunSeasonsButtonClicked"/><com:TActiveButton OnCallback="FCRunSeasonsButtonClicked" Text=<%[ run ]%> CssClass="windowcontent-button" ActiveControl.CallbackOptions="options"/>
</td></tr>

<tr>
<td><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="for_for_name" CssClass="inputlarge" /></td>
<td id="infoboxsmall">
<%[ Please give the season a name, that you can relate to ]%>
</td>
</tr>

<tr>
<td><%[ Variante ]%>:</td>
<td><com:TActiveDropDownList Id="for_idta_variante" CssClass="mandantory" EnableViewState="True" /> <%[ Growth Rate PRZ ]%>:<com:TActiveTextbox Id="for_for_faktor" CssClass="mandantorysmall" /></td>
<td id="infoboxsmall">
<%[ Here you select the variante affected by the continuance ]%>
</td>
</tr>

<tr>
<td><%[ From Period ]%>:</td>
<td><com:TActiveDropDownList Id="for_from_idta_periode" CssClass="mandantory" EnableViewState="True" /> / <%[ To Period ]%><com:TActiveDropDownList Id="for_to_idta_periode" CssClass="mandantory" EnableViewState="True" /></td>
<td id="infoboxsmall">
<%[ Here you select from which periode, to which period the values are written ]%>
</td>
</tr>

<tr>
<td><%[ Logical Field ]%>:</td>
<td><com:TActiveDropDownList Id="for_idta_feldfunktion" CssClass="inputlarge" EnableViewState="True" /></td>
<td id="infoboxsmall">
<%[ Here you select the logical field, that will be affected by the seasonality ]%>
</td>
</tr>

<tr>
<td><%[ growth type ]%>:</td>
<td><com:TActiveDropDownList Id="for_idta_fortschreibungs_type" CssClass="inputlarge" EnableViewState="True" /></td>
<td id="infoboxsmall">
<%[ Here you select the growthtype ]%>
</td>
</tr>

<tr>
<td><%[ Start Node ]%>:</td>
<td colspan="2">
    <com:TActiveTextBox Id="for_idtm_struktur" CssClass="inputlarge" EnableViewState="True"/>
    <com:Application.pages.container.SelectFromTreeFortschreibungContainer ID="SelectFromTreeContainer" />
    <com:TActiveTextBox ID="SFTauf_tabelle" Text="for_idtm_struktur" Visible="false"/>
    <com:TActiveTextBox ID="SFTstart_id" Text="0" Visible="false"/>
</td>
</tr>

</table>
</com:TTabView>
</com:TTabPanel>
<com:TActiveTextBox id="for_idta_fortschreibung" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="fortschreibung_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
</com:TActivePanel>