
<table width="100%" cellpadding="0" cellspacing="0" style="border-top-style:solid;border-top-width:1px;border-left-style:solid;border-left-width:1px;">
<tr>
    <td valign="top" width="120px" style="background-color:#ffffff">
            <h2>
                <img src="<%#"/rliq/themes/basic/gfx/64x64/apps/g".$this->data->idta_aufgaben_type.".png"%>" width="25" height="25">
                <%#$this->data->auf_type_name%>
            </h2>
            <p class="orgalink"><%#date('d.M Y',strtotime($this->data->auf_cdate))%></p>
            <com:TActiveLinkButton Text=<%[ bearbeiten ]%>
                CommandName="edit"
                CssClass="wcbutton"/><br/><br/>
            <com:TActiveHyperLink NavigateUrl="javascript:win_organisation_openwin('<%#$this->CreatePDFLink($this->data->idtm_aufgaben)%>')"
                Text=<%[ PDF ]%>
                CssClass="wcbutton"/><br/><br/>
            <com:TActiveLinkButton OnCallback="PFMailSend"
                Text=<%[ Mail ]%>
                CssClass="wcbutton"
                CommandParameter="<%#$this->data->idtm_aufgaben%>"/>
                <com:TActiveLabel Id="PFMAILER" Text=""/>
    </td>
    <td valign="top" style="background-color:#ffffff;">

        <a class="orgalink" href="javascript:win_orgstb_openwin('<%#$this->getRequest()->constructUrl('page','organisation.window.orgstbwindow&idtm_organisation='.$this->data->idtm_organisation)%>')" Title="OrgStbWindow"><%#$this->data->org_responsible%></a>
        <i><%[ und ]%> </i><a class="orgalink" href="javascript:win_orgstb_openwin('<%#$this->getRequest()->constructUrl('page','organisation.window.orgstbwindow&idtm_organisation='.$this->data->auf_idtm_organisation)%>')" Title="OrgStbWindow"><%#$this->data->org_speachpartner%></a>

        <com:TActiveImageButton
                ImageUrl=<%#"/rliq/themes/basic/gfx/16x16/actions/".$this->data->auf_done.".png"%>
                onCallback="setTaskDone"
                Tooltip="Done"/>

        <fieldset>
            <legend>
                <%#$this->data->auf_name%>
            </legend>
        
        <table width="100%" cellpadding="0" border="0">
        <tr style="border-top-style:solid">
            <td valign="top">
                <%#$this->wiki2html($this->data->auf_beschreibung)%>
            </td>
        </tr>
        <tr style="border-top-style:solid">
            <td valign="top">
                <br/>
                <com:Application.pages.container.KommentarContainerNOP ID="KommentarContainerNOP"/>
                <com:TActiveTextBox Id="Tedcom_tabelle" Text="tmaufgaben" Visible="false"/>
                <com:TActiveTextBox Id="Tedcom_id" Text=<%#$this->data->idtm_aufgaben%> Visible="false"/>
                <%#$this->initComments()%>
                <br/>
                <com:Application.pages.container.DMSFileBrowser ID="DMSFileBrowser"/>
                <com:TActiveTextBox Id="DMSFileTabelle" Text="tmaufgaben" Visible="false"/>
                <com:TActiveTextBox Id="DMSFileId" Text="<%#$this->data->idtm_aufgaben%>" Visible="false"/>
                <%#$this->initBrowser()%>

                <com:TActivePanel Display="<%#$this->data->auf_tabelle=='tm_protokoll_detail'?'Dynamic':'None'%>" CssClass="paperback">
                    <hr/>
                    <i>Info:</i><br/>
                    <%#$this->data->auf_tabelle=='tm_protokoll_detail'?$this->wiki2html(ProtokollDetailRecord::finder()->findByPK($this->data->auf_id)->prtdet_descr):''%>
                </com:TActivePanel>
            </td>
        </tr>
    </table>
    </fieldset>
</td>

<td valign="top" width="250px">
            <table width="100%" cellpadding="1" cellspacing="1" border="0">
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                    onMouseOut="setRowBackground(this,this.style.backgroundColor)">
                    <td class="FormLabel" width="90px">Tags:</td>
                    <td>
                        <com:TInPlaceTextBox Text=<%#$this->data->auf_tag==''?'add':$this->data->auf_tag%> Id="auf_tag" onCallback="infoChanged"/>
                    </td>
                </tr><tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                    onMouseOut="setRowBackground(this,this.style.backgroundColor)">
                    <td class="FormLabel">Dauer:</td>
                    <td>
                        <com:TInPlaceTextBox Text=<%#$this->data->auf_dauer==''?'add':$this->data->auf_dauer%> Id="auf_dauer" onCallback="infoChanged"/> h
                    </td>
                </tr>
                 <tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                    onMouseOut="setRowBackground(this,this.style.backgroundColor)">
                    <td class="FormLabel">Zeichen Int.:</td>
                    <td>
                        <com:TInPlaceTextBox Text=<%#$this->data->auf_zeichen_eigen==''?'add':$this->data->auf_zeichen_eigen%> Id="auf_zeichen_eigen" onCallback="infoChanged"/>
                    </td>
                 </tr><tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                    onMouseOut="setRowBackground(this,this.style.backgroundColor)">
                    <td class="FormLabel">Zeichen Ext.:</td>
                    <td>
                        <com:TInPlaceTextBox Text=<%#$this->data->auf_zeichen_fremd==''?'add':$this->data->auf_zeichen_fremd%> Id="auf_zeichen_fremd" onCallback="infoChanged"/>
                    </td>
                </tr>
                 <tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                    onMouseOut="setRowBackground(this,this.style.backgroundColor)">
                    <td class="FormLabel">Bis:</td>
                    <td>
                        <com:TInPlaceTextBox Text=<%#$this->data->auf_tdate==''?'add':$this->data->auf_tdate%> Id="auf_tdate" onCallback="infoChanged"/>
                    </td>
                 </tr><tr onMouseOver="setRowBackground(this,'#c3e3ef')"
                    onMouseOut="setRowBackground(this,this.style.backgroundColor)">
                    <td class="FormLabel"><%[ Priority ]%>:</td>
                    <td>
                        <com:TInPlaceTextBox Text=<%#$this->data->auf_priority==''?'add':$this->data->auf_priority%> Id="auf_priority" onCallback="infoChanged"/>
                    </td>
                </tr>
            </table>
</td>

</tr></table>
