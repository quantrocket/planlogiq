<com:TActivePanel ID="panelNewTask" Display="None">


<table>
<tr><td valign="top">

<fieldset>
    <legend>Basis</legend>
<table width="100%" cellspacing="1">
<tr>
<td class="FormLabelMa"><%[ Typ ]%>:</td>
<td><com:TActiveDropDownList id="Tedidta_aufgaben_type" CssClass="mandantorylarge" AutoPostBack="false"/>
<td class="FormLabelMa"><%[ Betr. ]%>:</td>
<td><com:TActiveTextBox id="Tedauf_name" CssClass="mandantorylarge" />
</td>
</tr>

<tr>
<td class="FormLabel"><%[ Verantwortlich ]%>:</td>
<td>
    <com:POrganisationSelection Id="Tedidtm_organisation"/>
</td>
<td class="FormLabel"><%[ Ansprechpartner ]%>:</td>
<td>
    <com:POrganisationSelection Id="Tedauf_idtm_organisation" Text=<%#$this->data->auf_idtm_organisation%>/>
</td>
</tr>

<tr>
<td class="FormLabel" valign="top"><%[ Beschreibung ]%>:</td>
<td colspan="6">
    <com:TActiveTextbox Id="Tedauf_beschreibung"
    TextMode="MultiLine"
    Rows="6"
    Width="100%"/>
</td>
</tr>

<tr><td colspan="4" align="right">
    <hr/>
    <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png"/>
    <com:TActiveButton OnCallback="TASavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
    <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png"/>
    <com:TActiveButton OnCallback="TACancelButtonClicked" Text=<%[ abbrechen ]%> CssClass="windowcontent-button"/>
</td></tr>

</table>

</fieldset>

</td><td valign="top">

<fieldset>
        <legend>Attribute</legend>
    <table width="100%">
        <tr>
            <td class="FormLabel"><%[ Gültig bis ]%>:</td>
            <td><com:TActiveDatePicker Id="Tedauf_tdate" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="TextBox" />
            </td>
        </tr>
        <tr>
            <td class="FormLabel"><%[ Tags ]%>:</td>
            <td><com:TActiveTextBox id="Tedauf_tag" CssClass="inputmedium"/></td>
        </tr>
        <tr>
            <td class="FormLabel"><%[ Internes Zeichen ]%>:</td>
            <td><com:TActiveTextBox id="Tedauf_zeichen_eigen" CssClass="inputmedium"/></td>
        </tr>
        <tr>
            <td class="FormLabel"><%[ Externes Zeichen ]%>:</td>
            <td><com:TActiveTextBox id="Tedauf_zeichen_fremd" CssClass="inputmedium"/></td>
        </tr>
        <tr>
            <td class="FormLabel"><%[ Dauer ]%>:</td>
            <td><com:TActiveTextBox id="Tedauf_dauer" CssClass="inputsmall"/>h
            <%[ Priorität ]%>:<com:TActiveTextBox id="Tedauf_priority" CssClass="inputsmall"/>
            <%[ Done ]%><com:TActiveCheckBox Id="Tedauf_done" AutoPostBack="false"/>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset>
        <legend>Ressourcen</legend>
        <table width="100%">
            <tr>
                <td valign="top"><%[ verfügbare ]%> <%[ Ressourcen ]%>:<br />
                <com:TActiveListBox Id="ttidtm_ressource" Rows="6" SelectionMode="Multiple" /></td>
                <td valign="top"><%[ Duration ]%>/<%[ Amount ]%>:<com:TActiveTextBox Id="ttauf_res_dauer" CssClass="mandantorysmall" /><com:TActiveTextBox Id="ttidtm_aufgabe_ressource" CssClass="hidden" visible="false" />
                <com:TActiveButton id="addRessources" Text=" > " onCallBack="addRessource" CssClass="windowcontent-button" /></td>
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
</td></tr>
</table>

<com:TActiveTextBox id="Tedaufgaben_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_tabelle" Text="tm_allgemein" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_user_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidtm_aufgaben" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidtm_aufgaben_single" Text="0" CssClass="hiddeninput" visible="false" />


</com:TActivePanel>

    <div class="mytoolbar">
        <com:TActiveButton OnCallback="TANewButtonClicked" Text=<%[ neue Aktivität ]%> CssClass="windowcontent-button"/>
        | Filter: <%[ Jahr/Monat ]%>: <com:TActiveDropDownList Id="CCAufgabenContainerOrganisationYear" CssClass="mandantory" OnSelectedIndexChanged="bindListTAValue"/>
          <com:TActiveDropDownList Id="CCAufgabenContainerOrganisationMonth"
                CssClass="mandantory"
                OnSelectedIndexChanged="bindListTAValue"/>
        | Status: <com:TActiveDropDownList OnSelectedIndexChanged="bindListTAValue" Text="2" id="CBAufgabeDone" CssClass="mandantory" />
        | <%[ Pager ]%>: <com:TActivePager
                ID="CCPagerAufgabenRepeater"
                ControlToPaginate="CCAufgabenRepeater"
                PageButtonCount="5"
                CssClass="pager"
                Mode="Numeric"
                OnPageIndexChanged="page.AufgabenContainerOrganisation.rcvList_PageIndexChanged"/>
        Anzahl: <com:TActiveDropDownList Id="CCAufgabenContainerPageSize" CssClass="mandantory" OnSelectedIndexChanged="bindListTAValue"/>
    </div>

        <com:TActiveDataList
            AllowPaging="true"
            AllowCustomPaging="true"
            PageSize="5"
            RepeatColumns="1"
            RepeatDirection="Vertical"
            onEditCommand="lstCCAufgabenRepeaterEdit"
            onCancelCommand="lstCCAufgabenRepeaterCancel"
            onUpdateCommand="lstCCAufgabenRepeaterSave"
            ID="CCAufgabenRepeater"
            ItemRenderer="Application.pages.container.listen.AufgabenRenderer"
            EditItemRenderer="Application.pages.container.listen.AufgabenRendererEdit"
            CssClass="datagrid">

            <prop:HeaderTemplate>
            </prop:HeaderTemplate>

            <prop:FooterTemplate>
            </prop:FooterTemplate>

        </com:TActiveDataList>
