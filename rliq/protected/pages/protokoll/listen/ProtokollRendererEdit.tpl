<fieldset style="background-color:#FFFFFF;">
    <legend>Protokollpunkt - <com:TActiveLabel Id="idtm_protokoll_detail" Text=<%#$this->data->idtm_protokoll_detail%> />
        <com:TActiveLabel Id="idtm_protokoll" Text=<%#$this->data->idtm_protokoll%> />
    </legend>

<%#$this->initPullDown()%>

<table width="100%" border="0" cellpadding="0">
<tr>
        <td valign="top" align="left">

            <b>WVL:</b><com:TActiveCheckBox Id="prtdet_wvl" Checked=<%#$this->data->prtdet_wvl%> AutoPostBack="false"/>

                <com:Application.pages.protokoll.prtAufgabenContainer ID="prtAufgabenContainer"/>
                <com:TActiveTextBox id="Tedauf_tabelle" Text="tm_protokoll_detail" visible="false" />
                <com:TActiveTextBox id="Tedauf_id" Text=<%#$this->data->idtm_protokoll_detail%> visible="false" />
                <com:TActiveTextBox id="Tedauf_visible" Text=<%#$this->data->idta_protokoll_ergebnistype<3?'Dynamic':'None'%> visible="false" />
                <%#$this->initPrtAufgaben()%>

         </td>               
         <td width="100%">
                    <table width="100%">
                        <tr>
                        <td class="FormLabelMa" width="250px">
                            <%[ Betreff ]%>:
                        </td>
                        <td>
                            <com:TActiveTextBox Id="prtdet_topic" Text=<%#$this->data->prtdet_topic%> Width="100%"/>
                        </td></tr>
                        <tr>
                        <td class="FormLabelMa">
                            <%[ Ergebnistyp ]%>:
                        </td>
                        <td>
                             <com:TActiveRadioButtonList RepeatColumns="4" Id="idta_protokoll_ergebnistype" Text=<%#$this->data->idta_protokoll_ergebnistype%> AutoPostBack="true" onCallback="DisplayMyTaskPanel"/>
                        </td></tr>
                        <tr>
                            <td class="FormLabelMa">
                                <com:TActiveListBox Id="idtm_activity"
                                    Text=<%#$this->data->idtm_activity%>
                                    AutoPostBack="false"
                                    Rows="12"
                                    CssClass="inputnormal"/>
                            </td>
                            <td valign="top">
                            <com:TActiveTextBox Id="prtdet_descr"
                                Text="<%#$this->data->prtdet_descr%>"
                                TextMode="MultiLine"
                                Rows="12"
                                Width="100%"/>
                        </td>
                      </tr>
                    </table>
                </td>
                
       </tr>
        <tr>
            <td colspan="3" align="right">
                <hr/>
                <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filesave.png"/>
                <com:TActiveLinkButton Text="speichern" CommandName="update" CssClass="windowcontent-button"/>
                <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/cancel.png"/>
                <com:TActiveLinkButton Text="abbrechen" CommandName="cancel" CssClass="windowcontent-button"/>
            </td>
        </tr>
</table>
</fieldset>
