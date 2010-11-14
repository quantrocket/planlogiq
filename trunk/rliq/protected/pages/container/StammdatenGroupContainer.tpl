<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent">

<com:TTabPanel>
<com:TTabView Caption="Gruppen">

<table width="100%">

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png"/><com:TActiveButton OnCallback="RCNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
|<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png"/><com:TActiveButton OnCallback="RCSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
||<com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png"/><com:TActiveButton OnCommand="RCDeleteButtonClicked" Text=<%[ entfernen ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td class="FormLabelMa"><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="RCedstammdaten_group_name" CssClass="inputlarge" /></td>
<td rowspan="3" valign="top">
<fieldset>
    <legend>Optionen</legend>
<table width="100%">
    <tr>
        <td class="PSPFAZ"><%[ Original Level ]%>:</td>
        <td class="PSPFAZ"><com:TActiveCheckBox Id="RCedstammdaten_group_original" CssClass="mandantorysmall" AutoPostback="false"/></td>
    </tr>
    <tr>
        <td class="PSPFAZ"><%[ Multi Level ]%>:</td>
        <td class="PSPFAZ"><com:TActiveCheckBox Id="RCedstammdaten_group_multi" CssClass="mandantorysmall" AutoPostback="false"/></td>
    </tr>
    <tr>
        <td class="PSPFAZ"><%[ Create Group-Seperator ]%>:</td>
        <td class="PSPFAZ"><com:TActiveCheckBox Id="RCedstammdaten_group_create" CssClass="mandantorysmall" AutoPostback="false"/></td>
    </tr>
</table>
</fieldset>
</td>
</tr>

<tr>
<td class="FormLabelMa"><%[ Structure Type ]%>:</td>
<td><com:TActiveDropDownList Id="RCedidta_struktur_type" CssClass="inputlarge" /></td>
</tr>

<tr>
<td class="FormLabelMa"><%[ Parent Dimension ]%>:</td>
<td>

<com:TActiveRepeater ID="TTStammdatenansichtListe" 
    OnItemCommand="propertyAction"
    OnItemDatabound="reloadStammdatensichtPullDown">
        <prop:HeaderTemplate>
            <table width="100%">
                <tr class="thead">
                    <th><%[ Id ]%></th>
                    <th><%[ Sicht ]%></th>
                    <th><%[ Parent Element ]%></th>
                    <th><%[ verwendet ]%></th>
                    <th width="15px">Action</th>
                </tr>
         </prop:HeaderTemplate>

        <prop:EmptyTemplate>
            <table width="100%">
                <tr>
                    <td align="left">
                        <hr/>
                        <h2><%[ Zum anlegen eines neuen Eintrags bitte hinzufügen klicken... ]%></h2>
                    </td>
                </tr>
             </table>
        </prop:EmptyTemplate>

        <prop:ItemTemplate>
             <tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                onMouseOut="setRowBackground(this,this.style.backgroundColor)"
                class="<%# $this->ItemIndex%2?'alternating':'nonealternating' %>">
                <td>
                    <com:TActiveLabel Id="idtt_stammdatensicht" Text="<%#$this->Data->idtt_stammdatensicht%>" />
                    </td>
                <td>
                    <com:TActiveLabel Id="sts_name" Text="<%#StammdatensichtRecord::finder()->findByPK($this->Data->idta_stammdatensicht)->sts_name%>" />
                    </td>
                <td style="background-color:#efefef;align:right;">
                    <com:TActiveDropDownList id="parent_idta_stammdaten_group" CssClass="mandantory" Text="<%#$this->Data->parent_idta_stammdaten_group%>" />
                </td>
                <td style="background-color:#efefef;align:right;">
                    <com:TActiveCheckBox id="sts_stammdaten_group_use" CssClass="mandantory" Checked="<%#$this->Data->sts_stammdaten_group_use%>" />
                </td>
                <td>
                  <com:TActiveLinkButton
                                Text="save"
                                CommandName="save"
                                CommandParameter=<%#$this->Data->idtt_stammdatensicht%>
                                CssClass="wcbutton"
                                />
                </td>
             </tr>
         </prop:ItemTemplate>
         <prop:FooterTemplate>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
             </table>
         </prop:FooterTemplate>
</com:TActiveRepeater>

</td>
</tr>

<tr><td colspan="3" class="portlet-title"><%[ Liste der ]%> <%[ Dimension Groups ]%></td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="StammdatenGroupListe" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" AllowPaging="true" AllowSorting="false" PageSize="10" OnPageIndexChanged="SGCStammdatenGroupList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_StammdatenGroup" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="lst_idta_stammdaten_group" DataField="idta_stammdaten_group" HeaderText="ID" SortExpression="idta_stammdaten_group" />
	<com:TActiveBoundColumn ID="lst_stammdaten_group_name" DataField="stammdaten_group_name" HeaderText="Bezeichnung" />
	<com:TActiveTemplateColumn>
        <prop:HeaderTemplate><%[ Structure Type ]%></prop:HeaderTemplate>
        <prop:ItemTemplate>
            <com:TActiveLabel Text=<%#StrukturTypeRecord::finder()->findByPK($this->Parent->DataItem->idta_struktur_type)->struktur_type_name%> />
        </prop:ItemTemplate>
        </com:TActiveTemplateColumn>
        <com:TActiveBoundColumn ID="lst_stammdaten_group_original" DataField="stammdaten_group_original" HeaderText="ORG" />
	<com:TActiveBoundColumn ID="lst_stammdaten_group_multi" DataField="stammdaten_group_multi" HeaderText="MULTI" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="RCedstammdaten_group_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidta_stammdaten_group" CssClass="hiddeninput" visible="false" />

</com:TTabView>

<com:TTabView Caption="Rechte">

<table width="100%">
    <tr>
        <td colspan="3" class="mytoolbar">
            <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="XXRNewClicked"/><com:TActiveButton OnCallback="XXRNewClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
            <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="XXRSaveClicked"/><com:TActiveButton OnCallback="XXRSaveClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
            <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCallback="XXRDeleteClicked"/><com:TActiveButton OnCallback="XXRDeleteClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
            <com:TActiveTextBox id="idxx_berechtigung" Text="" CssClass="hiddeninput" visible="false" />
            <com:TActiveTextBox id="xx_id" Text="" CssClass="hiddeninput" visible="false" />
            <com:TActiveTextBox id="xx_modul" Text="ta_stammdaten_group" CssClass="hiddeninput" visible="false" />
            <com:TActiveTextBox id="berechtigung_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
        </td>
    </tr>
    <tr>
        <td><%[ User ]%></td>
        <td><com:TActiveListBox ID="idtm_user" Rows="4"/></td>
        <td id="infoboxsmall"><%[ please choose the user, the rights are given ]%></td>
    </tr>
    <tr>
        <td><%[ Rights ]%></td>
        <td>
            Read:<com:TActiveCheckBox ID="xx_read" />
            Write:<com:TActiveCheckBox ID="xx_write" />
            Create:<com:TActiveCheckBox ID="xx_create" />
            Delete:<com:TActiveCheckBox ID="xx_delete" />
        </td>
        <td id="infoboxsmall"><%[ Please add the rights for the selected user ]%></td>
    </tr>
    <tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ User rights ]%></td></tr>
    <tr>
        <td colspan="3">
            <com:TActiveDataGrid ID="lstBerechtigung" PageSize="20" AllowPaging="true" onPageIndexChanged="lstBerechtigung_pageIndexChanged" AutoGenerateColumns="false" onEditCommand="editlstBerechtigung" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
                <com:TActiveBoundColumn HeaderText=<%[ Key ]%> DataField="idxx_berechtigung" ID="lst_idxx_berechtigung" />
                <com:TActiveBoundColumn HeaderText=<%[ Modul ]%> DataField="xx_modul" ID="lst_xx_modul" />
                <com:TActiveBoundColumn HeaderText=<%[ StrID ]%> DataField="xx_id" ID="lst_xx_id" />
                <com:TActiveBoundColumn HeaderText=<%[ UserID ]%> DataField="idtm_user" ID="lst_idtm_user" />
                <com:TActiveTemplateColumn>
                    <prop:HeaderTemplate><%[ User ]%></prop:HeaderTemplate>
                    <prop:ItemTemplate>
                        <com:TActiveLabel Text=<%#UserRecord::finder()->findByidtm_user($this->Parent->DataItem->idtm_user)->user_name%> />
                    </prop:ItemTemplate>
                </com:TActiveTemplateColumn>
                <com:TActiveTemplateColumn>
                    <prop:HeaderTemplate><%[ R ]%></prop:HeaderTemplate>
                    <prop:ItemTemplate>
                        <com:TActiveImage ImageUrl=<%#"/rliq/themes/basic/gfx/16x16/actions/".$this->Parent->DataItem->xx_read.".png"%> />
                    </prop:ItemTemplate>
                </com:TActiveTemplateColumn>
                <com:TActiveTemplateColumn>
                    <prop:HeaderTemplate><%[ U ]%></prop:HeaderTemplate>
                    <prop:ItemTemplate>
                        <com:TActiveImage ImageUrl=<%#"/rliq/themes/basic/gfx/16x16/actions/".$this->Parent->DataItem->xx_write.".png"%> />
                    </prop:ItemTemplate>
                </com:TActiveTemplateColumn>
                <com:TActiveTemplateColumn>
                    <prop:HeaderTemplate><%[ C ]%></prop:HeaderTemplate>
                    <prop:ItemTemplate>
                        <com:TActiveImage ImageUrl=<%#"/rliq/themes/basic/gfx/16x16/actions/".$this->Parent->DataItem->xx_create.".png"%> />
                    </prop:ItemTemplate>
                </com:TActiveTemplateColumn>
                <com:TActiveTemplateColumn>
                    <prop:HeaderTemplate><%[ D ]%></prop:HeaderTemplate>
                    <prop:ItemTemplate>
                        <com:TActiveImage ImageUrl=<%#"/rliq/themes/basic/gfx/16x16/actions/".$this->Parent->DataItem->xx_delete.".png"%> />
                    </prop:ItemTemplate>
                </com:TActiveTemplateColumn>
                <com:TActiveEditCommandColumn HeaderText=<%[ actions ]%> />
            </com:TActiveDataGrid>
        </td>
    </tr>
</table>

</com:TTabView>
</com:TTabPanel>

</com:TActivePanel>