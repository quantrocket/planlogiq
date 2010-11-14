
<com:TActiveTextBox id="Tedauf_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_tabelle" Text="tm_allgemein" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedauf_user_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidtm_aufgaben" CssClass="hiddeninput" visible="false" />

<fieldset>
    <legend>Aufgaben</legend>

<div id="infobox">
Stats: Sum <com:TActiveLabel Id="SUM_auf_dauer" Text="0.00" />h / <com:TActiveLabel Id="SUM_auf_dauer_day" Text="0.00" />d
</div>

<div style="width:400px;overflow:auto;padding-top:5px;padding-bottom:5px">

<com:TActivePanel id="prtAufgabenDialog" Display="None">

<fieldset style="background-color:#ffffff;">
        <legend><%[ Beschl. / Auftrag ]%></legend>

<table width="100%">
<tr>
    <td valign="top" colspan="2">
        <com:TActiveTextBox Id="auf_beschreibung"
            TextMode="MultiLine"
            Text="Bitte beschreiben Sie die Aufgabe..."
            Rows="4"
            CssClass="commentbox"
            Width="350px"/>
    </td>
</tr>
<tr>
    <td width="40px" class="FormLabelMa">
        <%[ Wer ]%>:
    </td>
    <td>
        <com:POrganisationSelection Id="idtm_organisation"/>
        GD: <com:TActiveTextBox Id="auf_dauer" Text="0.00" Width="40px"/>h
    </td>
</tr>
<tr>
    <td width="40px" class="FormLabelMa">
        Bis:
    </td>
    <td>
        <com:TActiveDatePicker Id="auf_tdate"
            Mode="Button"
            DateFormat="yyyy-MM-dd"
            InputMode="TextBox"
            Text=<%# date('Y-m-d')%>/>
    </td>
</tr>
</table>

<fieldset collapsed="true">
    <legend>Details</legend>
<div>
<table width="100%">
<tr>
    <td width="30px" class="FormLabel">
        <%[ Mit ]%>:
    </td>
    <td>
        <com:POrganisationSelection Id="auf_idtm_organisation"/>
    </td>
</tr>
<tr>
    <td class="FormLabel">Status: </td>
    <td>
    <com:TActiveDropDownList Id="auf_priority" Text="" AutoPostBack="false" CssClass="inputmedium">
        <com:TListItem Text="offen" Value="1" />
        <com:TListItem Text="Definition" Value="2" />
        <com:TListItem Text="Umsetzung" Value="3" />
        <com:TListItem Text="Test" Value="4" />
        <com:TListItem Text="Live" Value="5" />
        <com:TListItem Text="Produktiv" Value="6" />
    </com:TActiveDropDownList>
    </td>
</tr>
<tr>
    <td class="FormLabel">
        Erledigt: </td>
    <td>
        <com:TActiveDatePicker Id="auf_ddate"
            Mode="Button"
            DateFormat="yyyy-MM-dd"
            InputMode="TextBox"
            Text=<%# date('Y-m-d')%>/>
        <com:TActiveCheckBox ID="auf_done" Checked="0" AutoPostBack="false" />
    </td>
</tr>
</table>
</div>
</fieldset>

<table width="100%">
<tr><td colspan="4" align="right">
<hr/>
<div>
    <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/pencil.png"/>
    <com:TActiveButton id="AddOrSaveButtonPRTAUF"
            OnClick="CPRTAddButtonClicked"
            Text="<%[ hinzufügen ]%>"
            CssClass="windowcontent-button"/>
     <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png"/>
     <com:TActiveButton OnClick="hideprtAufgabenDialog"
            Text="<%[ abbrechen ]%>"
            CssClass="windowcontent-button"/>
</div>
</td></tr>
</table>


</fieldset>
</com:TActivePanel>

<com:TActivePanel Id="MyPrtAufgabenPanel" Display="Dynamic">

<com:TActiveLinkButton Text="Aufgabe hinzufügen" Id="AufgabeSichtButton" OnCallback="showprtAufgabenDialog" ToolTip="Aufgabe hinzufügen" CssClass="wcbutton"/>

     <com:TActiveRepeater
            ID="CCprtAufgabenListe"
            Width="100%"
            DataKeyField="idtm_aufgaben"
            OnItemCommand="editPrtAufgabe">

            <prop:ItemTemplate>

<fieldset style="background-color:#ffffff;">
        <table style="width:100%;" cellpadding="2" cellspacing="1">
        <tr>
            <td class="FormLabel" valign="top">Auftrag:</td>
            <td class="alternating" width="100%" colspan="3"><%#$this->data->auf_beschreibung%></td>
            <td>
                <com:TActiveImageButton
                    id="edImgButton"
                    ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit.png"
                    CommandName="edit"
                    CommandParameter=<%# $this->data->idtm_aufgaben %> />
            </td>
        </tr>
        <tr>
            <td class="FormLabel">Wer:(AP):</td>
            <td class="PSPFAZ">
                <a href="javascript:win_orgstb_openwin('<%#$this->getRequest()->constructUrl('page','organisation.window.orgstbwindow&idtm_organisation='.$this->data->idtm_organisation)%>')" Title="OrgStbWindow">
                    <%#is_Object(OrganisationRecord::finder()->findByPK($this->data->idtm_organisation))?OrganisationRecord::finder()->findByPK($this->data->idtm_organisation)->org_name:'-'%>
                </a>
                 <%#$this->data->auf_dauer%>h
            </td>
            <td class="FormLabel" width="20px">Mit:(AP):</td>
            <td class="alternating">
                <a href="javascript:win_orgstb_openwin('<%#$this->getRequest()->constructUrl('page','organisation.window.orgstbwindow&idtm_organisation='.$this->data->auf_idtm_organisation)%>')" Title="OrgStbWindow">
                    <%#is_Object(OrganisationRecord::finder()->findByPK($this->data->auf_idtm_organisation))?OrganisationRecord::finder()->findByPK($this->data->auf_idtm_organisation)->org_name:'-'%>
                </a>
            </td>
            <td>
                <com:TActiveButton Text="Mail" CssClass="wcbutton" CommandName="mail" CommandParameter="<%#$this->data->idtm_aufgaben%>"/>
            </td>
         </tr>
         <tr>
            <td class="FormLabel">Bis:/Status:</td>
            <td class="alternating"><%#$this->data->auf_tdate%> <i><%#$this->parent->parent->parent->StatusArray[$this->data->auf_priority] %></i></td>
            <td class="FormLabel">Erl. am:</td>
            <td class="alternating">
                <%#$this->data->auf_done?$this->data->auf_ddate:''%>
            </td>
            <td>
                <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/0.png"
                    Visible="<%#$this->data->auf_done==0?'true':'false'%>"
                    CommandName="taskdone"
                    CommandParameter="<%#$this->data->idtm_aufgaben%>"/>
                <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/clean.png" Visible="<%#$this->data->auf_done==1?'true':'false'%>"/>
            </td>
        </tr>
        </table>
</fieldset>

            </prop:ItemTemplate>

        </com:TActiveRepeater>

</com:TActivePanel>

</div>
</fieldset>
