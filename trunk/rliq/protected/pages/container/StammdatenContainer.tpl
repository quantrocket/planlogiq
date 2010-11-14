<com:TActivePanel ScrollBars="Auto">
<div id="infobox">
    <%[ Dimension Group ]%>: 
    <com:TActiveDropDownList Id="RCedidta_stammdaten_group" CssClass="mandantorylarge"/>
    &nbsp;<com:TActiveButton OnCallback="bindListStammdatenValue" Text=<%[ show values ]%> CssClass="windowcontent-button"/>
</div>

<fieldset>
    <legend><b><%[ Elemente der Ebene ]%></b></legend>

<table width="100%" cellspacing="2px" cellpadding="1px" style="background-color:#ffffff;">
<tr><td colspan="2" class="mytoolbar">
    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="RCNewButtonClicked"/><com:TActiveButton OnCallback="RCNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="RCSavedButtonClicked"/><com:TActiveButton OnCallback="RCSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="RCDeleteButtonClicked"/><com:TActiveButton OnCallback="RCDeleteButtonClicked" Text=<%[ entfernen ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
    <td class="FormLabel" style="width:200px;background:#efefef"><%[ Name ]%>:</td>
    <td><com:TActiveTextBox Id="RCedstammdaten_name" CssClass="inputlarge" />
    <b><%[ Nicht Aktiv ]%></b>:
    <com:TActiveCheckbox Id="RCedstammdaten_aktiv" /></td>
</tr>

<tr>
    <td class="FormLabel" style="width:200px;background:#efefef"><%[ External ID ]%>:</td>
    <td><com:TActiveTextBox Id="RCedstammdaten_key_extern" CssClass="inputlarge" /></td>
</tr>

<tr><td colspan="2" class="portlet-title"><%[ Liste der ]%> <%[ Dimension Groups ]%></td></tr>

<tr>
<td colspan="2">
<com:TActiveDataGrid ID="StammdatenListe" AllowPaging="true" AllowSorting="false" PageSize="8" OnPageIndexChanged="StammdatenList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_Stammdaten" CssClass="datagrid" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Left" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
        <com:TActiveBoundColumn ID="lst_idtm_stammdaten" DataField="idtm_stammdaten" HeaderText="ID" SortExpression="idtm_stammdaten" />
	<com:TActiveBoundColumn ID="lst_stammdaten_name" DataField="stammdaten_name" HeaderText="Bezeichnung" />
	<com:TActiveBoundColumn ID="lst_stammdaten_key_extern" DataField="stammdaten_key_extern" HeaderText="ID Extern" />
	<com:TActiveBoundColumn ID="lst_stammdaten_aktiv" DataField="stammdaten_aktiv" HeaderText="Nicht Aktiv" HeaderStyle.Width="80px" />
	<com:TActiveBoundColumn ID="lst_idta_stammdaten_group" DataField="idta_stammdaten_group" HeaderText="SG-Gruppe ID" />
</com:TActiveDataGrid>
</td>
</tr>

</table>
</fieldset>

<fieldset>
    <legend><b><%[ Standard-/Startwerte für Elemente der Ebene ]%></b></legend>
<table width="100%" cellpadding="2">

<tr><td colspan="4" class="mytoolbar">
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="RCTNewButtonClicked"/><com:TActiveButton OnCallback="RCTNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="RCTSavedButtonClicked"/><com:TActiveButton OnCallback="RCTSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td class="FormLabel" style="background:#efefef"><%[ Field Reciever ]%>:</td>
<td><com:TActiveDropDownList Id="RCTedidta_feldfunktion" CssClass="mandantorylarge" /></td>
<td class="FormLabel" style="background:#efefef"><%[ Variante ]%>:</td>
<td><com:TActiveDropDownList Id="RCTedidta_variante" CssClass="mandantorylarge" /></td>
</tr>

<tr>
<td class="FormLabel" style="width:200px;background:#efefef"><%[ Value ]%>:</td>
<td><com:TActiveTextBox Id="RCTedtt_stammdaten_value" /></td>
<td class="FormLabel" style="width:200px;background:#efefef"><%[ Start ]%> <%[ Periode ]%>:</td>
<td><com:TActiveDropDownList Id="RCTedidta_periode" CssClass="mandantory" /></td>
</tr>

<tr><td colspan="4" class="portlet-title"><%[ List of ]%> <%[ Values ]%></td></tr>

<tr>
<td colspan="4">
<com:TActiveDataGrid ID="TTStammdatenListe" 
    AllowPaging="true"
    AllowSorting="false"
    PageSize="5"
    OnPageIndexChanged="page.StammdatenContainer.TTStammdatenList_PageIndexChanged"
    AutoGenerateColumns="false"
    OnEditCommand="load_TTStammdaten"
    CssClass="datagrid"
    PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Left"
    PagerStyle.CssClass="pager"
    AlternatingItemStyle.CssClass="alternating">
        <com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
	<com:TActiveBoundColumn ID="lst_idtt_stammdaten" DataField="idtt_stammdaten" HeaderText="ID" SortExpression="idtt_stammdaten" />
        <com:TActiveTemplateColumn>
            <prop:HeaderTemplate><%[ Field ]%></prop:HeaderTemplate>
            <prop:ItemTemplate>
                <com:TActiveLabel Text=<%#FeldfunktionRecord::finder()->findByPK($this->Parent->DataItem->idta_feldfunktion)->ff_name%> />
            </prop:ItemTemplate>
        </com:TActiveTemplateColumn>
	<com:TActiveTemplateColumn>
            <prop:HeaderTemplate><%[ Variante ]%></prop:HeaderTemplate>
            <prop:ItemTemplate>
                <com:TActiveLabel Text=<%#VarianteRecord::finder()->findByPK($this->Parent->DataItem->idta_variante)->var_descr%> />
            </prop:ItemTemplate>
        </com:TActiveTemplateColumn>
	<com:TActiveTemplateColumn>
            <prop:HeaderTemplate><%[ Period ]%></prop:HeaderTemplate>
            <prop:ItemTemplate>
                <com:TActiveLabel Text=<%#PeriodenRecord::finder()->findByPK($this->Parent->DataItem->idta_periode)->per_extern%> />
            </prop:ItemTemplate>
        </com:TActiveTemplateColumn>
	<com:TActiveBoundColumn ID="lst_tt_stammdaten_value" DataField="tt_stammdaten_value" HeaderText="Wert" />	
</com:TActiveDataGrid>
</td>
</tr>
</table>
</fieldset>

<fieldset>
    <legend><b><%[ Treiber und Verknüpfungen ]%></b></legend>
<table width="100%" cellpadding="2">
<tr><td colspan="4" class="mytoolbar">
    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="RCTTNewButtonClicked"/><com:TActiveButton OnCallback="RCTTNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="RCTTSavedButtonClicked"/><com:TActiveButton OnCallback="RCTTSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="RCTTDeleteButtonClicked"/><com:TActiveButton OnCallback="RCTTDeleteButtonClicked" Text=<%[ entfernen ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
    <td class="FormLabel" style="background:#efefef"><%[ Feldfunktion ]%>:</td>
    <td><com:TActiveDropDownList Id="RCTDedidta_feldfunktion_from" CssClass="mandantorylarge" /></td>
    <td class="FormLabel" style="background:#efefef"><%[ Von Dimension ]%>:</td>
    <td><com:TActiveTextBox Id="RCTDedidtm_stammdaten_from" CssClass="inputsmall" />
        <com:TActiveTextBox Id="RCTDedidtm_stammdaten_from_label" CssClass="inputnormal" /></td>
</tr>

<tr>
    <td class="FormLabel" style="background:#efefef"><%[ Filterdimension ]%>:</td>
    <td><com:TActiveDropDownList Id="RCTDedidta_stammdaten_group" CssClass="mandantorylarge" /></td>
    <td class="FormLabel" style="background:#efefef"><%[ Ziel Dimension ]%>:</td>
    <td><com:TActiveDropDownList Id="RCTDedidtm_stammdaten_to" CssClass="mandantorylarge" /></td>
</tr>

<tr><td colspan="4" class="portlet-title"><%[ Liste von ]%> <%[ Treibern ]%></td></tr>

</table>

<com:TActiveDataGrid ID="StammdatenLinkListe"
    AllowPaging="false"
    AllowSorting="false"
    AutoGenerateColumns="true"
    OnEditCommand="load_StammdatenLink"
    CssClass="datagrid"
    PagerStyle.Mode="Numeric"
    PagerStyle.HorizontalAlign="Left"
    PagerStyle.CssClass="pager"
    AlternatingItemStyle.CssClass="alternating"/>

</fieldset>

<com:TActiveTextBox id="RCedstammdatenlink_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTDedidta_stammdaten_link" Text="0" CssClass="hiddeninput" visible="false" />

<com:TActiveTextBox id="RCTedidtm_stammdaten" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTedttstammdaten_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTedidtt_stammdaten" CssClass="hiddeninput" visible="false" />

<com:TActiveTextBox id="RCedstammdaten_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidtm_stammdaten" CssClass="hiddeninput" visible="false" />
</com:TActivePanel>