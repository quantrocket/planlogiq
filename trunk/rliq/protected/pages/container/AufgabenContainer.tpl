<com:TTabPanel>
<com:TTabView Caption=<%[ Aufgaben ]%>>

<div class="mytoolbar">
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="TANewButtonClicked"/><com:TActiveButton OnCallback="TANewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="TASavedButtonClicked"/><com:TActiveButton OnCallback="TASavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/editdelete.png" OnCallback="TADeleteButtonClicked"/><com:TActiveButton OnCallback="TADeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
</div>

<table width="100%">

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Type ]%>:</td>
<td><com:TActiveDropDownList id="Tedidta_aufgaben_type" CssClass="inputnormal" />
</td>
<td colspan="2" rowspan="3" valign="top">

<table border="0" width="100%">
    <tr>
        <td class="FormLabel" style="background-color:#efefef;"><%[ Tags ]%>:</td>
        <td><com:TActiveTextBox id="Tedauf_tag" CssClass="inputnormal"/></td>
    </tr>
    <tr>
        <td class="FormLabel" style="background-color:#efefef;"><%[ Dauer ]%>:</td>
        <td><com:TActiveTextBox id="Tedauf_dauer" CssClass="inputsmall"/>h</td>
    </tr>
    <tr>
        <td class="FormLabel" style="background-color:#efefef;"><%[ Priorität ]%>:</td>
        <td><com:TActiveTextBox id="Tedauf_priority" CssClass="inputsmall"/></td>
    </tr>
    <tr>
        <td class="FormLabel" style="background-color:#efefef;"><%[ Erledigen bis ]%>:</td>
        <td><com:TActiveDatePicker Id="Tedauf_tdate" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="TextBox" /></td>
    </tr>
    <tr>
        <td class="FormLabel" style="background-color:#efefef;"><%[ Done ]%>:</td>
        <td>
            <com:TActiveCheckBox Id="Tedauf_done" />
        </td>
    </tr>
</table>
</td>
</tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Label ]%>:</td>
<td><com:TActiveTextBox id="Tedauf_name" CssClass="inputnormal" />
<com:TActiveTextBox id="Tedauf_idtm_organisation" CssClass="inputsmall" />
</td>
</tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Description ]%>:</td>
<td>
<com:TActiveTextBox Id="Tedauf_beschreibung" TextMode="MultiLine" Rows="5" CssClass="commentbox" />
</td>
</tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Responsible ]%>:</td>
<td>
<com:POrganisationSelection Id="Tedidtm_organisation" />
</tr>

<tr>
    <td colspan="4" class="portlet-title">
        <%[ List of ]%> <%[ tasks ]%>
    </td>
</tr>

<tr>
<td colspan="4">
<com:TActivePanel>
<com:TActiveDataGrid ID="CCAufgabenListe" DataKeyField="idtm_aufgaben" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" AllowPaging="true" AllowSorting="false" PageSize="10" AutoGenerateColumns="false" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating" OnPageIndexChanged="page.AufgabenContainer.rcvList_PageIndexChanged" OnEditCommand="load_aufgabenvalue">
        <com:TActiveBoundColumn ID="lstcc_idtm_aufgaben" DataField="idtm_aufgaben" HeaderText="ID" SortExpression="idtm_aufgaben" />
        <com:TActiveBoundColumn ID="lstcc_auf_name" DataField="auf_name" HeaderText="Bezeichnung" />
        <com:TActiveBoundColumn ID="lstcc_auf_beschreibung" DataField="auf_beschreibung" HeaderText="Beschreibung" />
        <com:TActiveBoundColumn ID="lstcc_auf_tdate" DataField="auf_tdate" HeaderText="bis Datum" />
        <com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="Select" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TActivePanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="Tedaufgaben_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_tabelle" Text="tm_allgemein" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_user_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidtm_aufgaben" CssClass="hiddeninput" visible="false" />

</com:TTabView>
<com:TTabView Caption=<%[ Ressources ]%>>
<table width="100%">

<tr>
    <td valign="top" class="FormLabel" style="background-color:#efefef;"><%[ available ]%> <%[ Ressource ]%>:<br />
    <com:TActiveListBox Id="ttidtm_ressource" Rows="6" SelectionMode="Multiple" /></td>
    <td valign="top"><%[ Duration ]%>/<%[ Amount ]%><com:TActiveTextBox Id="ttauf_res_dauer" CssClass="mandantorysmall" /><com:TActiveTextBox Id="ttidtm_aufgabe_ressource" CssClass="hidden" visible="false" />
    <com:TActiveButton id="addRessources" Text=" > " onCallBack="addRessource" CssClass="windowcontent-button" /></td>
    <td valign="top">
        <%[ selected ]%> <%[ Ressources ]%>:<br />
        <com:TActiveDataGrid ID="RessourceListe" AllowPaging="true" AllowSorting="false" PageSize="10" OnPageIndexChanged="ressource_PageIndexChanged" OnEditCommand="removeRessource" AutoGenerateColumns="false" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
            <com:TActiveBoundColumn ID="lstpart_idtm_aufgabe_ressource" DataField="idtm_aufgabe_ressource" HeaderText="ID" SortExpression="idtm_aufgabe_ressource" />
            <com:TActiveBoundColumn ID="lstpart_res_name" DataField="res_name" HeaderText="Res. Name" />
            <com:TActiveBoundColumn ID="lstpart_auf_res_dauer" DataField="auf_res_dauer" HeaderText="Dauer" />
            <com:TActiveEditCommandColumn HeaderText="Action" HeaderStyle.Width="100px" UpdateText="update" EditText="remove" CancelText="Cancel"/>
        </com:TActiveDataGrid>
    </td>
</tr>

</table>
</com:TTabView>
</com:TTabPanel>

