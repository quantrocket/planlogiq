<table width="100%">

<tr><td colspan="3" class="portlet-title">Zeiterfassung</td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCommand="RCNewButtonClicked"/>new
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCommand="RCSavedButtonClicked"/>save
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/editdelete.png" OnCommand="RCDeleteButtonClicked"/>delete
</td></tr>

<tr>
<td>Benutzer:</td>
<td><com:TActiveDropDownList Id="RCedidtm_organisation"/></td>
<td id="infoboxsmall">
Wer führt die Aufgabe durch?
</td>
</tr>

<tr>
<td colspan="1">Datum:</td>
<td colspan="1"><com:TActiveDatePicker Id="RCedzeit_date" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="DropDownList" /></td>
<td id="infoboxsmall">
Für welchen Tag gilt die Buchung?
</td>
</tr>

<tr>
<td colspan="1">Zeit(00:00):</td>
<td colspan="1">von:<com:MaskedTextBox ID="RCedzeit_starttime" Mask="##:##" CssClass="mandantorytime" /> bis:<com:MaskedTextBox ID="RCedzeit_endtime" Mask="##:##" CssClass="mandantorytime" /></td>
<td id="infoboxsmall">
Bis wann soll die Aufgabe geschlossen werden?
</td>
</tr>

<tr>
<td>Kosten Status:</td>
<td><com:TActiveDropDownList Id="RCedidta_kosten_status" /></td>
<td id="infoboxsmall">
Ist die Zeit abrechenbar/ausweisbar?
</td>
</tr>


<tr>
<td colspan="1">Pause:</td>
<td colspan="1"><com:TActiveTextBox Id="RCedzeit_break" CssClass="inputsmall" onTextChanged="page.ZeiterfassungContainer.calcDauer"/>min - Dauer:<com:TActiveLabel Id="RCedzeit_dauer" Text="0"/></td>
<td id="infoboxsmall">
Bis wann soll die Aufgabe geschlossen werden?
</td>
</tr>


<tr>
<td>PSP Element:</td><td><com:TActiveDropDownList Id="RCedidtm_activity" /></td>
<td id="infoboxsmall">
Zu welchem PSP-Element gehört die Zeiterfassung?
</td>
</tr>

<tr>
<td>Beschreibung:</td>
<td><com:TActiveTextBox Id="RCedzeit_descr" TextMode="MultiLine" /></td>
<td id="infoboxsmall">
Zusätzliche Informationen
</td>
</tr>

<tr><td colspan="3" class="portlet-title">Liste der Einstufungen (Werte)</td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="ZeiterfassungListe" EnableViewState="false" AllowPaging="true" AllowSorting="false" PageSize="10" OnPageIndexChanged="page.ZeiterfassungContainer.rcvList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.ZeiterfassungContainer.load_rcvalue" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="RCedzeiterfassung_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidtm_zeiterfassung" CssClass="hiddeninput" visible="false" />
