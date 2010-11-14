<com:TActiveLinkButton Text="Kommentar hinzufügen"
    Id="KommentarSichtButton" OnCallback="showCommentDialog" ToolTip="Kommentar hinzufügen" CssClass="wcbutton"/>
<com:TActiveTextBox id="Tedcom_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedcom_tabelle" Text="tm_allgemein" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedcom_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedcom_user_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidqs_comments" CssClass="hiddeninput" visible="false" />

<com:TActivePanel ID="CommentDialog" Display="None">

<table width="100%">
<tr>
<td valign="top">
    <com:TActiveTextBox Id="com_content" TextMode="MultiLine" Rows="5" CssClass="commentbox" />
</td>
<td valign="top">

<table width="100%">
            <tr>
                <td class="FormLabel"><i>Sichtbar für:</i></td>
                <td><com:TActiveListBox
                    Text="-1"
                    id="CBidta_organisation_art"
                    CssClass="mandantory"/>
                </td>
             </tr>
</table>

</td>
</tr>
<tr><td colspan="2" align="right">
<hr/>
<div>
    <img src="/rliq/themes/basic/gfx/16x16/actions/pencil.png"/>
    <com:TActiveButton OnCallback="CCOMSaveButtonClicked"
            Text="<%[ hinzufügen ]%>"
            CssClass="windowcontent-button"/>
     <img src="/rliq/themes/basic/gfx/16x16/actions/cancel.png"/>
     <com:TActiveButton OnCallback="hideCommentDialog"
            Text="<%[ abbrechen ]%>"
            CssClass="windowcontent-button"/>
</div>
</td></tr>
</table>

</com:TActivePanel>

     <com:TActiveRepeater
            ID="CCKommentarListe"
            Width="100%"
            EnableViewState="true">

            <prop:HeaderTemplate>
            </prop:HeaderTemplate>

            <prop:ItemTemplate>
            <div Class="commenttable">
            <small><%#$this->data->com_cdate%></small><br/>
            <img src="/rliq/themes/basic/gfx/16x16/apps/enhanced_browsing.png"/>
              <strong>
                <a href="javascript:win_orgstb_openwin('<%#$this->getRequest()->constructUrl('page','organisation.window.orgstbwindow&idtm_organisation='.$this->data->idtm_organisation)%>')" Title="OrgStbWindow">
                    <%#OrganisationRecord::finder()->findByPK($this->data->idtm_organisation)->org_name%>
                </a>
              </strong>&nbsp;
                <%#$this->data->com_content%>
            </div>
            </prop:ItemTemplate>

            <prop:FooterTemplate>
            </prop:FooterTemplate>

        </com:TActiveRepeater>