<%=$this->initPullDown()%>

<table border="0">
<tr><td valign="top">

<fieldset>
    <legend>Basis</legend>
<table width="100%" cellspacing="1">
<tr>
<td class="FormLabelMa"><%[ Typ ]%>:</td>
<td colspan="3">
<com:TActiveDropDownList AutoPostBack="false" Id="Tedidta_aufgaben_type" CssClass="inputmedium" Text="<%#$this->data->idta_aufgaben_type%>"/>
</td>
</tr>

<tr>
<td class="FormLabelMa"><%[ Verantwortlich ]%>:</td>
<td>
<com:POrganisationSelection Id="Tedidtm_organisation" Text=<%#$this->data->idtm_organisation%>/>
</td>
<td class="FormLabel"><%[ Ansprechpartner ]%>:</td>
<td>
<com:POrganisationSelection Id="Tedauf_idtm_organisation" Text=<%#$this->data->auf_idtm_organisation%>/>
</td>
</tr>

<tr>
<td class="FormLabelMa"><%[ Betreff ]%>:</td>
<td colspan="3">
    <com:TActiveTextBox id="Tedauf_name" CssClass="inputlarge" Text=<%#$this->data->auf_name%>/>
</td>
</tr>

<tr>
<td colspan="4">
<com:TActiveTextbox Id="Tedauf_beschreibung"
            Text=<%#$this->data->auf_beschreibung%> 
            TextMode="MultiLine"
            Rows="9"
            Width="100%"/>
</td>
</tr>
<tr>
    <td colspan="4" align="right">
        <hr/>
        <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png"/>
        <com:TActiveButton Text=<%[ speichern ]%> CommandName="update" CssClass="windowcontent-button"/>
        <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png"/>
        <com:TActiveButton Text=<%[ abbrechen ]%> CommandName="cancel" CssClass="windowcontent-button"/>
    </td>
</tr>
</table>
</fieldset>
</td>
<td valign="top">
    <fieldset>
        <legend>Attribute</legend>
            <table width="100%">
                <tr>
                    <td><%[ Gueltig bis ]%>:</td>
                    <td><com:TActiveDatePicker
                            Id="Tedauf_tdate"
                            Mode="ImageButton"
                            DateFormat="yyyy-MM-dd"
                            InputMode="TextBox"
                            Date="<%#$this->data->auf_tdate%>"/> <%[ Done ]%><com:TActiveCheckBox Id="Tedauf_done" Checked=<%#$this->data->auf_done%> AutoPostBack="false"/>
                    </td>
                </tr>
                <tr>
                    <td><%[ Tags ]%>:</td>
                    <td><com:TActiveTextBox id="Tedauf_tag" CssClass="inputnormal" Text=<%#$this->data->auf_tag%>/></td>
                </tr>
                <tr>
                    <td><%[ Internes Zeichen ]%>:</td>
                    <td><com:TActiveTextBox id="Tedauf_zeichen_eigen" CssClass="inputnormal" Text=<%#$this->data->auf_zeichen_eigen%>/></td>
                </tr>
                <tr>
                    <td><%[ Externes Zeichen ]%>:</td>
                    <td><com:TActiveTextBox id="Tedauf_zeichen_fremd" CssClass="inputnormal" Text=<%#$this->data->auf_zeichen_fremd%>/></td>
                </tr>
                <tr>
                    <td><%[ Dauer ]%>:</td>
                    <td><com:TActiveTextBox id="Tedauf_dauer" CssClass="inputsmall" Text=<%#$this->data->auf_dauer%>/>h
                    <%[ Priority ]%>:<com:TActiveTextBox id="Tedauf_priority" CssClass="inputsmall" Text=<%#$this->data->auf_priority%>/></td>
                </tr>
            </table>
        </fieldset>


        <fieldset>
        <legend>Ressourcen</legend>
        <table width="100%">
            <tr>
                <td valign="top"><%[ available ]%> <%[ Ressource ]%>:<br />
                <com:TActiveListBox Id="ttidtm_ressource" Rows="4" SelectionMode="Multiple" CssClass="inputmedium"/></td>
                <td valign="top"><%[ Duration ]%>/<%[ Amount ]%>: <com:TActiveTextBox Id="ttauf_res_dauer" CssClass="mandantorysmall" />
                <com:TActiveTextBox Id="ttidtm_aufgabe_ressource" CssClass="hidden" visible="false" />
                <com:TActiveButton id="addRessources" Text="add" onCallBack="addRessource" CssClass="windowcontent-button" /></td>
            </tr>
            <tr>
                <td valign="top" colspan="2">
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
        </fieldset>

</td></tr></table>


<com:TActiveTextBox id="Tedauf_tabelle" CssClass="hiddeninput" visible="false" Text=<%#$this->data->auf_tabelle%>/>
<com:TActiveTextBox id="Tedauf_id" Text=<%#$this->data->auf_id%> CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidtm_aufgaben" CssClass="hiddeninput" visible="false" Text=<%#$this->data->idtm_aufgaben%>/>



<%=$this->bindListRessource()%>