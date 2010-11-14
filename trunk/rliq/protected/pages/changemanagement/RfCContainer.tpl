<table width="100%">

<tr><td colspan="3"><div id="infobox">RfC Change Management</div></td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" onCallback="RCNewButtonClicked"/>new
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" onCallback="RCSavedButtonClicked"/>save
</td></tr>

<tr>
<td>RfC-Code:</td>
<td><com:TActiveTextBox Id="RfCedrfc_code" CssClass="mandantorylarge"/>
</td>
<td id="infoboxsmall">
Tragen Sie hier den PSP-Code ein
</td>
</tr>

<tr>
<td colspan="1">Vorgeschlagen am:</td>
<td colspan="1"><com:TActiveDatePicker Id="RfCedrfc_suggestdate" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="DropDownList" /></td>
<td id="infoboxsmall">
Wann wird die Phase geplant beendet?
</td>
</tr>

<tr>
<td>Beantragt durch:</td>
<td><com:TActiveDropDownList Id="RfCedsuggest_idtm_organisation" /></td>
<td id="infoboxsmall">
Auf welches Arbeitspaket bezieht sich der Request of Change?
</td>
</tr>

<tr>
<td colspan="1">Beantragungsdatum:</td>
<td colspan="1"><com:TActiveDatePicker Id="RfCedrfc_date" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="DropDownList" /></td>
<td id="infoboxsmall">
Wann wird die Phase geplant beendet?
</td>
</tr>

<tr>
<td>Beschreibung:</td>
<td><com:TActiveTextBox Id="RfCedrfc_descr" TextMode="MultiLine" /></td>
<td id="infoboxsmall">
Bennen/beschreiben Sie hier die neue Anforderung...
</td>
</tr>

<tr>
<td>Neue Dauer / Status:</td>
<td><com:TActiveTextBox Id="RfCedrfc_dauer" CssClass="mandantorysmall"/>
 <com:TActiveCheckBox Id="RfCedrfc_status" /> Genehmigt
</td>
<td id="infoboxsmall">
Wie ist die neue Dauer? Wurde Sie genehmigt?
</td>
</tr>

<tr>
<td>Auswirkung wenn nicht:</td>
<td><com:TActiveTextBox Id="RfCedrfc_ifnot" TextMode="MultiLine" /></td>
<td id="infoboxsmall">
Was passiert, wenn die �nerung nicht implementiert wird?
</td>
</tr>

<tr>
<td>Arbeitspaket:</td>
<td><com:TActiveDropDownList Id="RfCedidtm_activity" /></td>
<td id="infoboxsmall">
Auf welches Arbeitspaket bezieht sich der Request of Change?
</td>
</tr>

<tr>
<td colspan="1">Genehmigt am:</td>
<td colspan="1"><com:TActiveDatePicker Id="RfCedrfc_gdate" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="DropDownList" /></td>
<td id="infoboxsmall">
Wann wurde der RfC genehmigt?
</td>
</tr>

<tr>
<td>Genehmigt durch:</td>
<td><com:TActiveDropDownList Id="RfCedgenemigt_idtm_organisation" /></td>
<td id="infoboxsmall">
Auf welches Arbeitspaket bezieht sich der Request of Change?
</td>
</tr>

<tr><td colspan="3" class="portlet-title">Liste aller Changerequests</td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="ChangeListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.RfCContainer.rcvList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="page.RfCContainer.load_rfcvalue" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="lst_idtm_changerequest" DataField="idtm_changerequest" HeaderText="ID" SortExpression="idtm_changerequest" />
	<com:TActiveBoundColumn ID="lst_rfc_descr" DataField="rfc_descr" HeaderText="Bezeichnung" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="RfCedrfc_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RfCedrfc_tabelle" Text="tm_risiko" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RfCedidtm_changerequest" Text="0" CssClass="hiddeninput" visible="false" />
