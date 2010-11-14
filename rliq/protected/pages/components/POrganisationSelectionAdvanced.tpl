<com:TAutoComplete Id="XXXsuggest_idtm_organisation"
                    OnSuggest="XXXsuggestOrganisation"
                    MinChars="2"
                    OnSuggestionSelected="XXXsuggestionSelectedOne"
                    Suggestions.DataKeyField="idtm_organisation"
                    ResultPanel.CssClass="acomplete"
                    ResultPanel.Style="position:relative"
                    CssClass="mandantorylarge"
                    Text=""
                    width="175px">
<prop:Suggestions.ItemTemplate>
    <li><b><%# $this->Data['org_vorname'] %> <%# $this->Data['org_name'] %></b></li>
</prop:Suggestions.ItemTemplate>
</com:TAutoComplete>
<com:TActiveImageButton onCallBack="showSuggestBox" ImageUrl="/rliq/themes/basic/gfx/16x16/actions/find.png"/>
<com:TActiveImageButton onCallBack="clearSuggestBox" ImageUrl="/rliq/themes/basic/gfx/16x16/actions/redo.png"/>
<com:PWCWindow ID="mpnlOrganisationContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MyOrganisationContainer->ClientID%>"
               Mode="Existing"
               Width="500px"
               Left="100"
               Top="100"
               Title="Feldfunktion">
</com:PWCWindow>

<com:TActivePanel ID="MyOrganisationContainer" Display="None">

<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/Kp_neu_small.jpg"/><br/>

<fieldset class="alternating">
    <legend><small>Filter</small></legend>
<table>
<tr>
        <td class="FormLabel"><%[ Suchkriterium ]%>: </td>
        <td><com:TActiveTextBox Id="WINOrgaorg_name" Text="" AutoPostback="true" OnCallback="bindListOrgListe" CssClass="inputnormal" /></td>
    </tr>
<tr>
        <td class="FormLabel">Typ: </td>
        <td><com:TActiveDropDownList Id="WINOrgaidta_organisation_type" AutoPostback="true" OnCallback="bindListOrgListe" CssClass="inputnormal" /></td>
    </tr>
</table>
</fieldset>

<div class="mytoolbar"><%[ Organisationen ]%>
<%[ Blättern ]%>: <com:TActivePager
                ID="PagerOrgListe"
                ControlToPaginate="OrgListe"
                PageButtonCount="7"
                CssClass="pager"
                Mode="Numeric"
                OnPageIndexChanged="dtgList_PageIndexChanged"/>
</div>
    <com:TActiveRepeater DataKeyField="idtm_organisation"
                         ID="OrgListe"
                         AllowPaging="true"
                         PageSize="10"
                         CssClass="datagrid"
                         OnItemCommand="XXOrgaSelected">

        <prop:HeaderTemplate>
            <table width="100%">
                <tr class="thead">
                    <td>Id</td>
                    <td>Action</td>
                    <td>I-Key</td>
                    <td>Anrede</td>
                    <td>Name</td>
                    <td>Vorname</td>
                 </tr>
        </prop:HeaderTemplate>

        <prop:ItemTemplate>
            <tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                onMouseOut="setRowBackground(this,this.style.backgroundColor)"
                class="<%# $this->ItemIndex%2?'alternating':'nonealternating' %>">
                <td width="20px">
                    <com:TActiveLabel Id="xxidtm_organisation" Text="<%#$this->Data->idtm_organisation%>"/>
                </td><td width="30px">
                    <com:TActiveLinkButton
                        Text="select"
                        CssClass="wcbutton"/>
                </td>
                <td width="60px"><com:TActiveLabel Text=<%#$this->Data->org_fk_internal%>/></td>
                <td width="60px"><com:TActiveLabel Text=<%#$this->Data->org_anrede%>/></td>
                <td><com:TActiveLabel Text=<%#$this->Data->org_name%>/></td>
                <td><com:TActiveLabel Text=<%#$this->Data->org_vorname%>/></td>
            </tr>
         </prop:ItemTemplate>

        <prop:FooterTemplate>
            </table>
        </prop:FooterTemplate>


    </com:TActiveRepeater>

</com:TActivePanel>