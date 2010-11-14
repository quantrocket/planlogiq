<div class="mytoolbar">
    <com:TActiveButton CommandParameter="newInvoice"
        OnCallback="ccActions"
        Text=<%[ neue Rechnung ]%>
        CssClass="windowcontent-button"/>
</div>

<h2>Rechnungen</h2>

<com:TActiveDataGrid Id="CCRechnungListe"
    AutoGenerateColumns="false"
    DataKeyField="idtm_rechnung"
    AlternatingItemStyle.CssClass="alternating"
    OnEditCommand="editItem"
    width="100%">

    <com:TActiveEditCommandColumn
        HeaderText="Edit"
        HeaderStyle.Width="100px"
        ItemStyle.HorizontalAlign="Center"
        ItemStyle.Font.Italic="false"
        />
    <com:TActiveBoundColumn
        Id="idtm_rechnung"
        HeaderText="ID"
        DataField="idtm_rechnung" />
    <com:TActiveBoundColumn
        HeaderText="Rechnungsnummer"
        DataField="rech_number" />
    <com:TActiveBoundColumn
        ItemStyle.HorizontalAlign="Right"
        ItemStyle.Wrap="false"
        ItemStyle.Font.Italic="false"
        ItemStyle.ForeColor="green"
        HeaderText="Datum"
        DataField="rech_date"
        />

</com:TActiveDataGrid>

<table width="100%">
    <tr>
        <td width="50%">
        <fieldset>
            <legend>RE-Steller</legend>
            Emp: <com:POrganisationSelection Id="idtm_organisation_from" /><br/>
            UID: <com:TActiveLabel Id="FROM_org_uid" Text="empty"/>
        </fieldset>
        </td>
        <td width="50%">
        <fieldset>
            <legend>RE-Empfänger</legend>
            Emp: <com:POrganisationSelection Id="idtm_organisation_to" /><br/>
            UID: <com:TActiveLabel Id="TO_org_uid" Text="empty"/>
        </fieldset>
        </td>
    </tr>
</table>

<com:Application.pages.container.DetailBelegContainer ID="DetailBelegContainer"/>

