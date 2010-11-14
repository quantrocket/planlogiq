<div class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCommand="RCNewButtonClicked"/>
 <com:TActiveButton OnCallback="RCNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCommand="RCSavedButtonClicked"/>
 <com:TActiveButton OnCallback="RCSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
</div>

<fieldset style="background-color:#ffffff">
    <legend>Stammdaten</legend>

<table width="100%">

<tr>
<td class="FormLabel">Risiko oder Chance:</td>
<td class="alternating"><com:TActiveRadioButtonList Id="RCedrcv_type" RepeatColumns="3" AutoPostBack="false"/></td>
<td class="FormLabel" rowspan="3">Beschreibung:</td>
<td class="alternating" rowspan="3"><com:TActiveTextBox Id="RCedrcv_comment" TextMode="MultiLine" /></td>
</tr>

<tr>
<td class="FormLabel">Verantwortlich:</td>
<td class="alternating"><com:POrganisationSelection Id="RCedidtm_organisation" /></td>
</tr>


<tr>
<td class="FormLabel">Risikoklasse:</td>
<td class="alternating"><com:TActiveDropDownList Id="RCedidtm_risiko" /></td>
</tr>

<tr><td colspan="4" class="portlet-title">Liste der Risiken/Chancen</td></tr>

<tr>
<td colspan="4">
<com:TPanel>
<com:TActiveDataGrid ID="RCValueListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.RiskValueContainer.rcvList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.RiskValueContainer.load_rcvalue" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
        <com:TActiveBoundColumn ID="lst_rcvalue_idtm_rcvalue" DataField="idtm_rcvalue" HeaderText="ID" SortExpression="idtm_rcvalue" />
	<com:TActiveBoundColumn ID="lst_rcv_comment" DataField="rcv_comment" HeaderText="Bezeichnung" />
	<com:TActiveBoundColumn ID="lst_rcvidtm_organisation" DataField="idtm_organisation" HeaderText="Organisation" />
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

</fieldset>


<div class="portlet-title">Quantifizierung</div>

<div class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCommand="RCTTNewButtonClicked"/>
<com:TActiveButton OnCallback="RCTTNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCommand="RCTTSavedButtonClicked"/>
<com:TActiveButton OnCallback="RCTTSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
</div>



<table width="100%">
    <tr><td valign="top">

<fieldset style="background-color:#ffffff">
    <legend>Brutto-Chance/Risiko</legend>

<table width="100%">

<tr>
<td class="FormLabel">Eintritt-Wahr.keit:</td>
<td class="alternating"><com:TActiveDropDownList Id="RCTTedrcv_ewk" CssClass="mandantory"/></td>
</tr>

<tr>
<td class="FormLabel">Entdeckungswahr.keit:</td>
<td class="alternating"><com:TActiveDropDownList Id="RCTTedrcv_prio" />Punkte</td>
</tr>


<tr>
<td class="FormLabel">Schadens-/ Gewinnhöhe:</td>
<td class="alternating"><com:TActiveTextBox id="RCTTedrcv_schaden" Text="0.00" />CUR
</td>
</tr>

<tr><td colspan="2" class="portlet-title">Liste der Einstufungen (Werte)</td></tr>

<tr>
<td colspan="2">
<com:TPanel>
<com:TActiveDataGrid ID="RCTTValueListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.RiskValueContainer.RCrcvttList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.RiskValueContainer.load_rcttvalue" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
        <com:TActiveBoundColumn ID="lst_rcttvalue_idtt_rcvalue" DataField="idtt_rcvalue" HeaderText="ID" SortExpression="idtt_rcvalue" />
	<com:TActiveBoundColumn ID="lst_rcv_ewk" DataField="rcv_ewk" HeaderText="Eintritt-Wahr.keit" />
	<com:TActiveBoundColumn ID="lst_rcv_prio" DataField="rcv_prio" HeaderText="Prio" />
	<com:TActiveBoundColumn ID="lst_rcv_schaden" DataField="rcv_schaden" HeaderText="Schaden/Chance" />
	<com:TActiveBoundColumn ID="lst_rcv_cdate" DataField="rcv_cdate" HeaderText="Datum" />
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

    </table>
  </fieldset>
</td>
<td valign="top">

<fieldset style="background-color:#ffffff">
    <legend>Netto-Chance/Risiko</legend>

<table width="100%">

<tr>
<td class="FormLabel">Strategie:<br/>
<small>vermindern, vermeiden, verschieben, <br/>akzeptieren oder versichern</small></td>
<td class="alternating"><com:TActiveTextBox Id="NETRCTTedrcv_descr" TextMode="MultiLine" /></td>
</tr>

<tr>
<td class="FormLabel">Kosten für Absicherung:</td>
<td class="alternating"><com:TActiveTextBox Id="NETRCTTedrcv_kosten" Text="0.00"/>CUR</td>
</tr>

<tr>
<td class="FormLabel">Eintritt-Wahr.keit:</td>
<td class="alternating"><com:TActiveDropDownList Id="NETRCTTedrcv_ewk" /></td>
</tr>

<tr>
<td class="FormLabel">Entdeckungswahr.keit:</td>
<td class="alternating"><com:TActiveDropDownList Id="NETRCTTedrcv_prio" />Punkte</td>
</tr>

<tr>
<td class="FormLabel">Schadens-/ Gewinnhöhe:</td>
<td class="alternating"><com:TActiveTextBox id="NETRCTTedrcv_schaden" Text="0.00"/>CUR
</td>
</tr>

</table>

</td></tr>
<tr>
    <td>
        <div id="infobox">Bruttorisiko Verlauf</div>
    </td>
    <td>
        <div id="infobox">Nettorisiko Verlauf</div>
    </td>
</tr>
<tr>
    <td align="center" style="background-color:#efefef;">
        <com:TActiveImage ID="RisikoVerlaufImage" width="300"/>
    </td>
    <td align="center" style="background-color:#efefef;">
        <com:TActiveImage ID="RisikoVerlaufNettoImage" width="300"/>
    </td>
</tr>

</table>

<com:TActiveTextBox id="RCedrcvalue_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTTedrcvalue_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTTedrcv_cby" Text=<%= $this->User->GetUserId($this->User->Name) %> CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="NETRCTTedrcv_cby" Text=<%= $this->User->GetUserId($this->User->Name) %> CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidtm_rcvalue" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTTedidtm_rcvalue" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="NETRCTTedidtm_rcvalue" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCTTedidtt_rcvalue" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="NETRCTTedidtt_rcvalue" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedrcv_tabelle" Text="tm_risiko" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedrcv_id" Text="0" CssClass="hiddeninput" visible="false" />
