<com:TActivePanel ScrollBars="Auto"  CssClass="windowcontent">

<com:TTabPanel>
<com:TTabView id="VARStartseite" Caption="Settings">

<table width="100%">

<tr><td colspan="3" class="portlet-title">Varianten</td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="RCNewButtonClicked"/><com:TActiveButton OnCallback="RCNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="RCSavedButtonClicked"/><com:TActiveButton OnCallback="RCSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCallback="RCClosedButtonClicked"/><com:TActiveButton OnCallback="RCClosedButtonClicked" Text=<%[ close ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td>Name:</td>
<td><com:TActiveTextBox Id="RCedvar_descr" CssClass="inputlarge" /></td>
<td id="infoboxsmall">
Bennenen/beschreiben Sie hier die Variante oder die Chance
</td>
</tr>

<tr>
<td>W-ID:</td>
<td><com:TActiveTextBox Id="RCedw_id_variante" CssClass="inputlarge" /></td>
<td id="infoboxsmall">
Bennenen/beschreiben Sie hier die Variante oder die Chance
</td>
</tr>

<tr>
<td>Start-Periode:</td>
<td><com:TActiveDropDownList id="RCedidta_perioden" CssClass="mandantory" /> / Default: <com:TActiveCheckBox Id="RCedvar_default" /></td>
<td id="infoboxsmall">
Bennenen/beschreiben Sie hier die Variante oder die Chance
</td>
</tr>


<tr><td colspan="3" class="portlet-title">Liste der Risiken/Chancen</td></tr>

<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid ID="VarianteListe" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.VariantenContainer.varianteList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_variante" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="lst_idta_variante" DataField="idta_variante" HeaderText="ID" SortExpression="idta_variante" />
	<com:TActiveBoundColumn ID="lst_var_descr" DataField="var_descr" HeaderText="Bezeichnung" />
	<com:TActiveBoundColumn ID="lst_idtm_user" DataField="idtm_user" HeaderText="UserID" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="RCedvariante_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidta_variante" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="RCedidtm_user" CssClass="hiddeninput" visible="false" Text=<%= $this->User->GetUserId($this->User->Name) %> />

</com:TTabView>

<com:TTabView id="VARUSERADMIN" Caption=<%[ Usermanagement ]%> Visible=<%=$this->User->getisAdmin()%>>
        <table width="100%">
            <tr><td colspan="3" class="portlet-title"><%[ Userrights ]%></td></tr>
            <tr>
                <td colspan="3" class="mytoolbar">
                    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="XXXRNewClicked"/><com:TActiveButton OnCallback="XXXRNewClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
                    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="XXXRSaveClicked"/><com:TActiveButton OnCallback="XXXRSaveClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
                    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCallback="XXXRDeleteClicked"/><com:TActiveButton OnCallback="XXXRDeleteClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
                    <com:TActiveTextBox id="var_idxx_berechtigung" Text="" CssClass="hiddeninput" visible="false" />
                    <com:TActiveTextBox id="var_xx_id" Text="" CssClass="hiddeninput" visible="false" />
                    <com:TActiveTextBox id="var_xx_modul" Text="idta_struktur_bericht" CssClass="hiddeninput" visible="false" />
                    <com:TActiveTextBox id="var_berechtigung_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
                </td>
            </tr>
            <tr>
                <td><%[ User ]%></td>
                <td><com:TActiveListBox ID="var_idtm_user" Rows="4"/></td>
                <td id="infoboxsmall"><%[ please choose the user, the rights are given ]%></td>
            </tr>
            <tr>
                <td><%[ Rights ]%></td>
                <td>
                    Read:<com:TActiveCheckBox ID="var_xx_read" />
                    Write:<com:TActiveCheckBox ID="var_xx_write" />
                    Create:<com:TActiveCheckBox ID="var_xx_create" />
                    Delete:<com:TActiveCheckBox ID="var_xx_delete" />
                </td>
                <td id="infoboxsmall"><%[ Please add the rights for the selected user ]%></td>
            </tr>
            <tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ User rights ]%></td></tr>
            <tr>
                <td colspan="3">
                    <com:TActiveDataGrid ID="var_lstBerechtigung" PageSize="20" AllowPaging="true" onPageIndexChanged="var_lstBerechtigung_pageIndexChanged" AutoGenerateColumns="false" onEditCommand="var_editlstBerechtigung" PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
                        <com:TActiveBoundColumn HeaderText=<%[ Key ]%> DataField="idxx_berechtigung" ID="var_lst_idxx_berechtigung" />
                        <com:TActiveBoundColumn HeaderText=<%[ Modul ]%> DataField="xx_modul" ID="var_lst_xx_modul" />
                        <com:TActiveBoundColumn HeaderText=<%[ StrID ]%> DataField="xx_id" ID="var_lst_xx_id" />
                        <com:TActiveBoundColumn HeaderText=<%[ UserID ]%> DataField="idtm_user" ID="var_lst_idtm_user" />
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