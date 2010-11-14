<fieldset style="width:98%;background-color:#ffffff;">
    <legend>
        <b><%#$this->data->idtm_protokoll_detail_group%></b> -
        <%#$this->data->idtm_protokoll_detail%>
    </legend>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr style="border-top-style:solid">

        <td valign="top" width="380px">

        <fieldset style="background-color:#ffffff;">
            <legend>Kommentare</legend>

                <com:Application.pages.container.KommentarContainerNOP ID="KommentarContainerNOP"/>
                <com:TActiveTextBox Id="Tedcom_tabelle" Text="tmprotokolldetail" Visible="false"/>
                <com:TActiveTextBox Id="Tedcom_id" Text=<%#$this->data->idtm_protokoll_detail%> Visible="false"/>
                <%#$this->initComments()%>

        </fieldset>

                <com:Application.pages.protokoll.prtAufgabenContainer ID="prtAufgabenContainer"/>
                <com:TActiveTextBox id="Tedauf_tabelle" Text="tm_protokoll_detail" visible="false" />
                <com:TActiveTextBox id="Tedauf_id" Text=<%#$this->data->idtm_protokoll_detail%> visible="false" />
                <com:TActiveTextBox id="Tedauf_visible" Text=<%#$this->data->idta_protokoll_ergebnistype<3?'Dynamic':'None'%> visible="false" />
                <%#$this->initPrtAufgaben()%>

        </td>

        <td valign="top">
            <table width="100%">
                <tr>
                    <td colspan="2">
                        <h2><com:TActiveLinkButton Text="<%#$this->data->prtdet_topic%>" CommandName="edit" CssClass="h2" CommandParameter="<%#$this->data->idtm_protokoll_detail_group%>"/></h2>
                        <com:TActiveLinkButton Text="bearbeiten" CommandName="edit" CommandParameter="<%#$this->data->idtm_protokoll_detail_group%>" CssClass="wcbutton"/>
                        <com:TActiveLinkButton Text="entfernen" onCallback="page.removeDetailGroup" ActiveControl.CallbackParameter="<%#$this->data->idtm_protokoll_detail%>" CssClass="wcbutton"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left" colspan="2">
                        <table width="100%" class="<%#$this->data->idta_protokoll_ergebnistype>2?'PSPFAZ':'PSPSAZ'%>" cellpadding="2" border="0" cellspacing="1">
                            <tr>
                                <td width="25px">Typ:</td>
                                <td width="100px">
                                    <b>
                                        <%#ProtokollErgebnistypeRecord::finder()->findByPK($this->data->idta_protokoll_ergebnistype)->prt_ergtype_name%>
                                    </b>
                                </td>
                                <td width="30px">WVL:</td>
                                <td width="40px">
                                    <img src="<%#"/rliq/themes/basic/gfx/16x16/actions/".$this->data->prtdet_wvl.".png"%>">
                                </td>
                                <td width="40px">Projektbezug:</td>
                                <td><b><%#$this->data->act_pspcode%> :: <%#$this->data->act_name%></b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width:65px;border-right-style:solid;border-right-width:1px;" valign="top">
                        <img src="<%#"/rliq/themes/basic/gfx/0".$this->data->idta_protokoll_ergebnistype."et.png"%>"></td>
                    <td valign="top">
                        <%#$this->wiki2html($this->data->prtdet_descr)%>
                        <com:Application.pages.container.DMSFileBrowser ID="DMSFileBrowser"/>
                        <com:TActiveTextBox Id="DMSFileTabelle" Text="tmprotokolldetail" Visible="false"/>
                        <com:TActiveTextBox Id="DMSFileId" Text=<%#$this->data->idtm_protokoll_detail%> Visible="false"/>
                        <%#$this->initBrowser()%>
                    </td>
                </tr>
          </table>
        </td>
        
        
</tr>
</table>

</fieldset>
