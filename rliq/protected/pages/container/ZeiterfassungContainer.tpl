    <div id="tabhtml_1" style="overflow:auto;height:100%;background-color:#efefef;">
        <div style="overflow:hidden">

<div class="mytoolbar">
    <com:TActiveImage
        ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png"/>
    <com:TActiveButton OnCallback="RCNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
</div>

<table><tr><td>
<fieldset>
    <legend>Details</legend>

<table width="100%">

    <tr>
    <td class="FormLabel"><%[ Datum ]%>:</td>
    <td><com:TActiveDatePicker Id="RCedzeit_date" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="TextBox" />
    <com:TActiveLinkButton Text="Fahrtenbuch anzeigen" Id="FahrtenSichtButton" OnCallback="showFahrtenDialog" ToolTip="Fahrtenbuch anzeigen" CssClass="wcbutton"/>
    </td>
    <td class="FormLabel"><%[ Benutzer ]%>:</td>
    <td>
    <com:POrganisationSelection Id="RCedidtm_organisation"/>
    </td>
    </tr>

    <tr>
    <td class="FormLabel"><%[ von ]%></td>
    <td>
    <com:PTimeSelection Id="RCedzeit_starttime"/>

    <%[ bis ]%>

    <com:PTimeSelection Id="RCedzeit_endtime"/>
    </td>
    <td class="FormLabel"><%[ Pause ]%>:</td>
    <td><com:TActiveTextBox Id="RCedzeit_break"
        CssClass="inputsmall"
        onTextChanged="page.ZeiterfassungContainer.calcDauer"/> min - Dauer:<com:TActiveLabel Id="RCedzeit_dauer" Text="0"/>
    </td>
    </tr>

    <tr>
    <td class="FormLabel" valign="top"><%[ Status ]%>/<%[ Prozess ]%>:</td>
    <td><com:TActiveListBox
            Id="RCedidta_kosten_status"
            AutoPostBack="false"
            SelectionMode="Single"
            CssClass="inputmedium"/>
    <com:TActiveListBox
            Id="RCedidtm_prozess"
            AutoPostBack="false"
            SelectionMode="Single"
            CssClass="inputnormal"/></td>
    <td class="FormLabel" rowspan="2" valign="top"><%[ Projekt ]%>:</td>
    <td rowspan="2" valign="top">
        <com:PActivitySelection Id="RCedidtm_activity" StartPunkt="2" />
    </td>
    </tr>

    <tr>
    <td class="FormLabel" valign="top"><%[ Beschreibung ]%>:</td>
    <td><com:TActiveTextBox Id="RCedzeit_descr" TextMode="MultiLine" Width="340px"/></td>
    </tr>
    
    <tr><td colspan="4">

        <com:TActivePanel ID="FahrtenDialog" Display="None">
        <table width="100%">
        <tr><td colspan="2" class="portlet-title"><%[ Fahrtenbuch ]%>
        <com:TActiveLinkButton Text="Fahrtenbuch ausblenden" OnCallback="hideFahrtenDialog" ToolTip="Fahrtenbuch ausblenden" CssClass="wcbutton"/></td></tr>

        <tr>
        <td colspan="1" class="FormLabel" style="background-color:#efefef;"><%[ von ]%>/<%[ nach ]%>/<%[ km ]%>:</td>
        <td colspan="1">
            <com:TAutoComplete Id="fahrt_von" ResultPanel.Style="position:relative"
                OnSuggest="suggestOrganisation" Suggestions.DataKeyField="org_name" ResultPanel.CssClass="acomplete">
                    <prop:Suggestions.ItemTemplate>
                        <li><%# $this->Data['org_name'] %></li>
                    </prop:Suggestions.ItemTemplate>
            </com:TAutoComplete>
            <com:TAutoComplete Id="fahrt_nach" ResultPanel.Style="position:relative"
                OnSuggest="suggestOrganisation" Suggestions.DataKeyField="org_name" ResultPanel.CssClass="acomplete">
                    <prop:Suggestions.ItemTemplate>
                        <li><%# $this->Data['org_name'] %></li>
                    </prop:Suggestions.ItemTemplate>
            </com:TAutoComplete>
            <com:TActiveTextBox Id="fahrt_km" CssClass="inputsmall" />
        </td>
        </tr>

        <tr>
        <td class="FormLabel" style="background-color:#efefef;"><%[ Fahrtkostenstatus ]%>:</td><td><com:TActiveDropDownList Id="fahrt_status" /></td>
        </tr>

        </table>
        </com:TActivePanel>
    </td></tr>
    <tr>
        <td colspan="4">
        <hr/>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="left">
            <com:TActivePanel ID="SpeichernDialog" Display="None">
                <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" />
                <com:TActiveButton OnCallback="RCSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
                <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/editdelete.png" />
                <com:TActiveButton OnCallback="RCDeleteButtonClicked" Text=<%[ entfernen ]%> CssClass="windowcontent-button"/>
            </com:TActivePanel>
        </td>
        <td colspan="2" align="right">
            <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/pencil.png"/>
            <com:TActiveButton OnCallback="RCSavedButtonClicked"
                Text="<%[ hinzufügen ]%>"
                CssClass="windowcontent-button"/>
        </td>
    </tr>
    </table>
    </fieldset>
</td></tr></table>

<fieldset>
<div class="mytoolbar">
        Projektfilter: <com:TActiveDropDownList Id="FFidtm_activity"
                CssClass="mandantory"
                OnCallBack="bindListRCValue"/>
        <b><%[ Start ]%>:</b>
        <com:TActiveDatePicker Id="zeiterfassung_datestart" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="DropDownList" OnTextChanged="bindListRCValue" />
        <b><%[ End ]%>:</b>
        <com:TActiveDatePicker Id="zeiterfassung_dateende" Mode="ImageButton" DateFormat="yyyy-MM-dd" InputMode="DropDownList" OnTextChanged="bindListRCValue" />
</div>

<com:TActiveDataGrid ID="ZeiterfassungListe" 
        PagerStyle.Mode="Numeric"
        PagerStyle.HorizontalAlign="Right"
        AllowPaging="true"
        AllowSorting="true"
        PageSize="20"
        AutoGenerateColumns="false"
        PagerStyle.CssClass="pager"
        AlternatingItemStyle.CssClass="alternating"
        ItemStyle.CssClass="nonealternating"
        OnPageIndexChanged="page.ZeiterfassungContainer.rcvList_PageIndexChanged"
        OnEditCommand="load_rcvalue"
        Width="100%">
    <com:TActiveEditCommandColumn HeaderText=<%[ Action ]%> EditText="view" HeaderStyle.Width="45px"/>
    <com:TActiveBoundColumn HeaderText=<%[ ID ]%> DataField="idtm_zeiterfassung" ID="lst_idtm_zeiterfassung" />
    <com:TActiveTemplateColumn>
        <prop:HeaderTemplate><%[ User ]%></prop:HeaderTemplate>
        <prop:ItemTemplate>
            <com:TActiveLabel Text=<%#OrganisationRecord::finder()->findByPK($this->Parent->DataItem->idtm_organisation)->org_name%> />
        </prop:ItemTemplate>
    </com:TActiveTemplateColumn>
    <com:TActiveTemplateColumn>
        <prop:HeaderTemplate><%[ Activity ]%></prop:HeaderTemplate>
        <prop:ItemTemplate>
            <com:TActiveLabel Text=<%#ActivityRecord::finder()->findByPK($this->Parent->DataItem->idtm_activity)->act_name%> />
        </prop:ItemTemplate>
    </com:TActiveTemplateColumn>
    <com:TActiveBoundColumn HeaderText=<%[ Date ]%> DataField="zeit_date" ID="lst_zeit_date" />
    <com:TActiveBoundColumn HeaderText=<%[ CT ]%> DataField="idta_kosten_status" ID="idta_kosten_status" />
    <com:TActiveBoundColumn HeaderText=<%[ S-time ]%> DataField="zeit_starttime" ID="lst_zeit_starttime" />
    <com:TActiveBoundColumn HeaderText=<%[ E-time ]%> DataField="zeit_endtime" ID="lst_zeit_endtime" />
    <com:TActiveBoundColumn HeaderText=<%[ Break ]%> DataField="zeit_break" ID="lst_zeit_break" />
    <com:TActiveBoundColumn HeaderText=<%[ Nettime ]%> DataField="zeit_dauer" ID="lst_zeit_dauer" />
</com:TActiveDataGrid>

</fieldset>

<com:TActiveTextBox id="RCedzeiterfassung_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidtm_zeiterfassung" CssClass="hiddeninput" visible="false" />

        </div>
    </div>

<div id="tabhtml_2" style="overflow:auto;height:100%;background:#EBEEFA">
        <div style="overflow:hidden">

<com:Application.pages.container.DetailBelegContainer ID="DetailBelegContainer"/>
        <com:TActiveTextBox id="Teddeb_tabelle" Text="tm_zeiterfassung" visible="false" />
        <com:TActiveTextBox id="Teddeb_id" Text="0" visible="false" />

    </div>
</div>
