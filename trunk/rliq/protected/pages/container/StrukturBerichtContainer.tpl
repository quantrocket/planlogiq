<com:TActivePanel ScrollBars="Auto" CssClass="windowcontent">

<com:TTabPanel>
<com:TTabView id="SBStartseite" Caption="Settings">

<table width="100%" border="0" cellpadding="2" cellspacing="0">

<tr><td colspan="4" class="windowcontent-title"><%[ Structure Report Settings ]%></td></tr>

<tr><td colspan="4">
<div class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SBNewButtonClicked"/><com:TActiveButton OnCallback="SBNewButtonClicked" Text=<%[ neu ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SBSavedButtonClicked"/><com:TActiveButton OnCallback="SBSavedButtonClicked" Text=<%[ speichern ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SBSavedAsButtonClicked"/><com:TActiveButton OnCallback="SBSavedAsButtonClicked" Text=<%[ speichern unter ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="SBDeleteButtonClicked"/><com:TActiveButton OnCallback="SBDeleteButtonClicked" Text=<%[ entfernen ]%> CssClass="windowcontent-button"/>
</div>
</td></tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Name ]%>:</td>
<td><com:TActiveTextBox Id="SBedpivot_struktur_name" CssClass="mandantorylarge" /></td>
<td class="FormLabel" style="background-color:#efefef;"><%[ Order ]%>/<%[ Start Report ]%>:</td>
<td><com:TActiveTextBox Id="SBedsb_order" CssClass="mandantorysmall" /> / <com:TActiveCheckBox Id="SBedsb_startbericht" /></td>
</tr>

<tr><td colspan="4" class="portlet-title"><%[ List of ]%> <%[ Berichte ]%></td></tr>

<tr>
<td colspan="4">
<com:TPanel>
<com:TActiveDataGrid PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" ID="StrukturBerichtListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.StrukturBerichtContainer.StrukturBerichtList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_StrukturBericht" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="80px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
        <com:TActiveBoundColumn ID="SB_idta_struktur_bericht" DataField="idta_struktur_bericht" HeaderText="ID" SortExpression="idta_struktur_bericht" HeaderStyle.Width="50px" />
	<com:TActiveBoundColumn ID="SB_pivot_struktur_name" DataField="pivot_struktur_name" HeaderText="Berichtsname" />
	<com:TActiveBoundColumn ID="SB_sb_order" DataField="sb_order" HeaderText="Rang" HeaderStyle.Width="40px"/>
	
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

<tr><td colspan="4">
<com:TTabPanel>
<com:TTabView id="TABONE" Caption=<%[ Report Rows Settings ]%>>
<table width="100%">

<tr><td colspan="4">
    <div class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SBZNewButtonClicked"/><com:TActiveButton OnCallback="SBZNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SBZSavedButtonClicked"/><com:TActiveButton OnCallback="SBZSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="SBZDeleteButtonClicked"/><com:TActiveButton OnCallback="SBZDeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
    </div>
</td></tr>

<tr>
    <td class="FormLabel" style="background-color:#efefef;"><%[ Feldfunktion ]%>:</td>
    <td><com:TActiveDropDownList Id="SBZedidta_feldfunktion" CssClass="mandantory" /></td>
    <td class="FormLabel" style="background-color:#efefef;"><%[ Berichtszeilen Typ ]%>:</td>
    <td><com:TActiveDropDownList Id="SBZedsbz_type" CssClass="mandantory" /></td>
</tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Dimension ]%>/<%[ Input ]%>:</td>
<td><com:TActiveDropDownList Id="SBZedidtm_stammdaten" CssClass="mandantory" /><com:TActiveCheckBox Id="SBZedsbz_input" /></td>
<td class="FormLabel" style="background-color:#efefef;"><%[ Label ]%>/<%[ Order ]%>:</td>
<td><com:TActiveTextBox Id="SBZedsbz_label" CssClass="mandantory" />/<com:TActiveTextBox Id="SBZedsbz_order" CssClass="mandantorysmall"/></td>
</tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Spacer Label ]%>:</td>
<td><com:TActiveTextBox Id="SBZedsbz_spacer_label" /></td>
<td class="FormLabel" style="background-color:#efefef;"><%[ View Details ]%>:</td>
<td><com:TActiveCheckBox Id="SBZedsbz_detail" /></td>
</tr>

<tr><td colspan="4" class="portlet-title"><%[ List of ]%> <%[ Rows ]%></td></tr>
<tr>
<td colspan="4">
<com:TPanel>
<com:TActiveDataGrid PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" ID="StrukturBerichtZeilenListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.StrukturBerichtContainer.StrukturBerichtZeilenList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_StrukturBerichtZeilen" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="80px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
        <com:TActiveBoundColumn ID="SBZ_idta_struktur_bericht_zeilen" DataField="idta_struktur_bericht_zeilen" HeaderText="ID" SortExpression="idta_struktur_bericht_zeilen" HeaderStyle.Width="50px"/>
	<com:TActiveBoundColumn ID="SBZ_sbz_label" DataField="sbz_label" HeaderText="Beschriftung" />
	<com:TActiveBoundColumn ID="SBZ_sbz_order" DataField="sbz_order" HeaderText="Rang" HeaderStyle.Width="40px"/>
	
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>


</table>

<com:TActivePanel ID="SBZCOLLECTOR" visible="1">

<table width="100%">

<tr><td colspan="3" class="portlet-title"><%[ Row-Collector Settings ]%></td></tr>

<tr><td colspan="3" class="mytoolbar">
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SBZCOLNewButtonClicked"/><com:TActiveButton OnCallback="SBZCOLNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SBZCOLSavedButtonClicked"/><com:TActiveButton OnCallback="SBZCOLSavedButtonClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
|<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="SBZCOLDeleteButtonClicked"/><com:TActiveButton OnCallback="SBZCOLDeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td class="FormLabel" style="background-color:#efefef;"><%[ Base operator ]%>/<%[ Field ]%>:</td>
<td><com:TActiveDropDownList Id="SBZCOLedsbz_collector_operator" CssClass="mandantorysmall" /> / <com:TActiveDropDownList Id="SBZCOLedrow_idta_struktur_bericht_zeilen" CssClass="mandantory" /></td>
<td id="infoboxsmall">
<%[ this is a special function, only used by administrator ]%>
</td>
</tr>


<tr><td colspan="4" class="portlet-title"><%[ List of ]%> <%[ Calculation ]%></td></tr>
<tr>
<td colspan="4">
<com:TPanel>
<com:TActiveDataGrid PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" ID="SBZCollectorListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.StrukturBerichtContainer.SBZCollectorList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_SBZCollector" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="SBZCOL_idta_collector" DataField="idta_sbz_collector" HeaderText="ID" SortExpression="idta_collector" />
	<com:TActiveBoundColumn ID="SBZCOL_col_operator" DataField="sbz_collector_operator" HeaderText="Operator" />
	<com:TActiveBoundColumn ID="SBZCOL_row_idta_struktur_bericht_zeilen" DataField="row_idta_struktur_bericht_zeilen" HeaderText="Feld" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

</table>

<com:TActiveTextBox id="SBZCOLedidta_struktur_bericht_zeilen" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBZCOLedsbzcollector_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBZCOLedidta_sbz_collector" CssClass="hiddeninput" visible="false" />

</com:TActivePanel>

<com:TActiveTextBox id="SBZedidta_struktur_bericht" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBZedidta_struktur_bericht_zeilen" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBZedstruktur_bericht_zeilen_edit_status" Text="0" CssClass="hiddeninput" visible="false" />

</com:TTabView> <!--ende report row----------------------------------------------------------------------------------------------------------------------->

<com:TTabView id="TABTWO" Caption=<%[ Report Columns Settings ]%>>
<table width="100%">

<tr><td colspan="3" class="portlet-title"><%[ List of ]%> <%[ Columns ]%></td></tr>
<tr>
<td colspan="3">
<com:TPanel>
<com:TActiveDataGrid PagerStyle.Mode="Numeric" PagerStyle.HorizontalAlign="Right" ID="StrukturBerichtSpaltenListe" AllowPaging="true" AllowSorting="false" AllowCustomPaging="true" PageSize="5" OnPageIndexChanged="page.StrukturBerichtContainer.StrukturBerichtSpaltenList_PageIndexChanged" AutoGenerateColumns="false" OnEditCommand="load_StrukturBerichtSpalten" CssClass="datagrid" PagerStyle.CssClass="pager" AlternatingItemStyle.CssClass="alternating">
	<com:TActiveBoundColumn ID="SBS_idta_struktur_bericht_spalten" DataField="idta_struktur_bericht_spalten" HeaderText="ID" SortExpression="idta_struktur_bericht_spalten" />
	<com:TActiveBoundColumn ID="SBS_idta_variante" DataField="idta_variante" HeaderText="Variante" />
	<com:TActiveBoundColumn ID="SBS_sbs_order" DataField="sbs_order" HeaderText="Order" />
	<com:TActiveEditCommandColumn HeaderText="SELECT" HeaderStyle.Width="100px" UpdateText="Save" EditText="view" CancelText="Cancel"/>
</com:TActiveDataGrid>
</com:TPanel>
</td>
</tr>

<tr><td colspan="3" class="mytoolbar">
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="SBSNewButtonClicked"/><com:TActiveButton OnCallback="SBSNewButtonClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="SBSSavedButtonClicked"/><com:TActiveButton OnCallback="SBSSavedButtonClicked" Text=<%[ save]%> CssClass="windowcontent-button"/>
<com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/remove.png" OnCallback="SBSDeleteButtonClicked"/><com:TActiveButton OnCallback="SBSDeleteButtonClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
</td></tr>

<tr>
<td><%[ Variante ]%>/<%[ Period Gap ]%>:</td>
<td><com:TActiveDropDownList Id="SBSedidta_variante" CssClass="mandantory" />/<com:TActiveTextBox Id="SBSedidta_perioden_gap" CssClass="mandantory" /></td>
<td id="infoboxsmall">
<%[ this is a special function, only used by administrator ]%>
</td>
</tr>

<tr>
<td><%[ Order ]%>:</td>
<td><com:TActiveTextBox Id="SBSedsbs_order" CssClass="mandantorysmall" /> <%[ Fix Period ]%>:<com:TActiveCheckBox Id="SBSedsbs_perioden_fix" /> <%[ Fix Variante ]%>:<com:TActiveCheckBox Id="SBSedsbs_idta_variante_fix" /></td>
<td id="infoboxsmall">
<%[ please define the order and the gap for the period ]%>
</td>
</tr>

<tr>
<td><%[ Operator ]%>:</td>
<td><com:TActiveDropDownList Id="SBSedsbs_bericht_operator" CssClass="input" /> <%[ Cumulated ]%>:<com:TActiveCheckBox Id="SBSedsbs_cumulated" /> <%[ Input ]%>:<com:TActiveCheckBox Id="SBSedsbs_input" /></td>
<td id="infoboxsmall">
<%[ Which operator will be used in column? ]%>
</td>
</tr>

<tr>
<td><%[ Start Node ]%>:</td>
<td colspan="2"><com:TActiveDropDownList Id="SBSedsbs_struktur_switch_type" CssClass="mandantorylarge" />/
<com:TActiveTextBox Id="SBSedsbs_idtm_struktur" CssClass="input" />
<com:Application.pages.container.SelectFromTreeStrukturBerichtContainer ID="SelectFromTreeContainer" />
<com:TActiveTextBox ID="SFTauf_tabelle" Text="SBSedsbs_idtm_struktur" Visible="false"/>
<com:TActiveTextBox ID="SFTstart_id" Text="0" Visible="false"/>
</td>
</tr>

<com:TActiveTextBox id="SBSedidta_struktur_bericht" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBSedidta_struktur_bericht_spalten" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBSedstruktur_bericht_spalten_edit_status" Text="0" CssClass="hiddeninput" visible="false" />

</table>
</com:TTabView> <!--ende report column-->

</com:TTabPanel>
</td></tr>

</table>

<com:TActiveTextBox id="SBedstruktur_bericht_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBedidta_struktur_bericht" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="SBedidtm_user" CssClass="hiddeninput" visible="false" Text=<%= $this->User->GetUserId($this->User->Name) %> />
<com:TActiveTextBox id="SBedpivot_struktur_cdate" CssClass="hiddeninput" visible="false" Text=<%= date('Y-m-d') %> />

</com:TTabView>

<com:TTabView id="USERADMIN" Caption=<%[ Usermanagement ]%> Visible=<%=$this->User->getisAdmin()%>>
        <table width="100%">
            <tr>
                <td colspan="3" class="mytoolbar">
                    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/edit_add.png" OnCallback="XXRNewClicked"/><com:TActiveButton OnCallback="XXRNewClicked" Text=<%[ new ]%> CssClass="windowcontent-button"/>
                    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png" OnCallback="XXRSaveClicked"/><com:TActiveButton OnCallback="XXRSaveClicked" Text=<%[ save ]%> CssClass="windowcontent-button"/>
                    <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png" OnCallback="XXRDeleteClicked"/><com:TActiveButton OnCallback="XXRDeleteClicked" Text=<%[ delete ]%> CssClass="windowcontent-button"/>
                    <com:TActiveTextBox id="idxx_berechtigung" Text="" CssClass="hiddeninput" visible="false" />
                    <com:TActiveTextBox id="xx_id" Text="" CssClass="hiddeninput" visible="false" />
                    <com:TActiveTextBox id="xx_modul" Text="idta_struktur_bericht" CssClass="hiddeninput" visible="false" />
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