<com:TActiveLinkButton Text="Kommentar hinzufügen" Id="KommentarSichtButton" OnCallback="showCommentDialog" ToolTip="Kommentar hinzufügen" CssClass="wcbutton"/>

<com:TActivePanel Id="CommentDialog" Display="None">
    <fieldset style="width:98%;background-color:#ffffff;">
        <legend><%[ Kommentar erfassen ]%></legend>

<table width="100%">

<tr>
<td width="50%" valign="top">
    <com:TActiveTextBox Id="com_content" TextMode="MultiLine" Rows="5" CssClass="commentbox" />
</td>
<td valign="top">
    
</td>
</tr>

<tr><td colspan="2">
<div class="mytoolbar">
    <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png"/>
    <com:TActiveButton OnCallback="CCOMSaveButtonClicked"
            Text="<%[ hinzufügen ]%>"
            CssClass="windowcontent-button"/>
     <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png"/>
     <com:TActiveButton OnCallback="hideCommentDialog"
            Text="<%[ abbrechen ]%>"
            CssClass="windowcontent-button"/>
</div>
</td></tr>


</table>

    </fieldset>
</com:TActivePanel>
<table width="100%">
<tr>
<td colspan="2">
<com:TActivePanel>
<!-- Here comes the repeater /-->
    <com:TActivePager ID="PagerProKommentarListe" ControlToPaginate="KommentarListe" PageButtonCount="3" Mode="Numeric" OnPageIndexChanged="pageKommentarListeChanged"/>
    <com:TActiveRepeater ID="KommentarListe" AllowPaging="true" PageSize="6">

        <prop:HeaderTemplate>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
        </prop:HeaderTemplate>

        <prop:FooterTemplate>
                </table>
        </prop:FooterTemplate>

        <prop:ItemTemplate>
                <tr>
                        <td class="commentheader" width="300px"><strong><%# PeriodenRecord::finder()->findByper_intern($this->DataItem->idta_periode)->per_extern%> - <%# OrganisationRecord::finder()->findByidtm_organisation($this->DataItem->idtm_organisation)->org_name%> - <%# $this->DataItem->com_cdate %></strong></td>
                        <td><com:TActiveTextBox ID="lstidqs_comments" Text=<%# $this->DataItem->idqs_comments %> CssClass="hiddeninput" visible="false" />
                        <com:TActiveLinkButton CssClass="wcbutton" Id="commentDeleteLink" Text=<%[ delete ]%> OnClick="page.KommentarContainer.CCOMDeleteButtonClicked" CommandParameter=<%# $this->DataItem->idqs_comments %> Visible=<%# $this->DataItem->idtm_organisation==$this->User->GetUserOrgId($this->User->GetUserId($this->User->Name))?'true':'false'%>/></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="pboxuseractionsmall">
                            <com:TActiveLabel ID="lstcom_content" Text=<%# $this->DataItem->com_content %> />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p>&nbsp;</p>
                    </td>
                </tr>
        </prop:ItemTemplate>
        
    </com:TActiveRepeater>
<!-- Here ends the repeater /-->
</com:TActivePanel>

</td>
</tr>

</table>
</div>