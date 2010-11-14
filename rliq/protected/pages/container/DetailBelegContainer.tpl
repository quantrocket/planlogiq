<com:TActiveTextBox id="Teddeb_edit_status" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Teddeb_tabelle" Text="tm_allgemein" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Teddeb_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Teddeb_user_id" Text="0" CssClass="hiddeninput" visible="false" />
<com:TActiveTextBox id="Tedidtm_detail_beleg" CssClass="hiddeninput" visible="false" />


<table width="100%"><tr><td>

<fieldset style="background-color:#ffffff;">
        <legend><%[ Belege ]%></legend>
<com:TActiveRepeater ID="CCDetailBelegListe" OnItemCommand="propertyAction">
        <prop:HeaderTemplate>
            <table width="100%">
                <tr class="thead">
                    <td width="15px">Action</td>
                    <td width="15px">In/Out</td>
                    <td ><%[ Pos. ]%></td>
                    <td ><%[ QTY ]%></td>
                    <td ><%[ Nr. ]%></td>
                    <td ><%[ Beschreibung ]%></td>
                    <td ><%[ Art ]%></td>
                    <td ><%[ Preis ]%></td>
                    <td ><%[ Steuer ]%></td>
                    <td ><%[ Betrag ]%></td>
                    <td ><%[ Datum ]%></td>
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
                  <com:TActiveLinkButton
                                Text="Remove"
                                CommandName="remove"
                                CommandParameter=<%#$this->Data->idtm_detail_beleg%>
                                CssClass="wcbutton"
                                />
                </td>
                <td>
                    <com:TActiveLabel Id="idtm_detail_beleg" Text="<%#$this->Data->idtm_detail_beleg%>" />
                    <com:TActiveCheckBox Id="deb_inout"
                        Checked="<%#$this->Data->deb_inout%>"
                        onCallback="page.recalcSumme"
                        ActiveControl.CallbackParameter="<%#$this->Data->idtm_detail_beleg%>"
                        AutoPostBack="true"/></td>
                <td style="background-color:#efefef;align:right;">
                    <com:TActiveTextBox ReadOnly="true "id="deb_order" Text="<%#$this->Data->deb_order%>" CssClass="inputsmall" onCallback="page.recalcSumme" AutoPostback="true"/>
                </td>
                <td style="background-color:#efefef;align:right;">
                    <com:TActiveTextBox id="deb_menge" CssClass="mandantorysmall" Text="<%#$this->Data->deb_menge%>" onCallback="page.recalcSumme" AutoPostback="true"/>
                </td>
                <td>
                    <com:TActiveTextBox id="deb_nummer" Text="<%#$this->Data->deb_nummer%>" CssClass="mandantorysmall" onCallback="page.recalcSumme" AutoPostback="true"/>
                </td>
                <td style="background-color:#efefef;align:right;">
                    <com:TActiveTextBox id="deb_descr" Text="<%#$this->Data->deb_descr%>" CssClass="inputnormal" onCallback="page.recalcSumme" AutoPostback="true"/>
                </td>
                <td style="background-color:#efefef;align:right;">
                    <com:TActiveTextBox id="deb_konto" Text="<%#$this->Data->deb_konto%>" CssClass="inputmedium" onCallback="page.recalcSumme" AutoPostback="true"/>
                </td>
                <td style="background-color:#efefef;align:right;">
                     <com:TActiveTextBox id="deb_preis" CssClass="inputmedium" Text="<%#$this->Data->deb_preis%>" onCallback="page.recalcSumme" AutoPostback="true"/>
                </td><td style="background-color:#efefef;align:right;">
                     <com:TActiveTextBox id="deb_tax" CssClass="inputsmall" Text="<%#$this->Data->deb_tax%>" onCallback="page.recalcSumme" AutoPostback="true"/>%
                </td>
                <td>
                = <com:TActiveLabel Id="deb_summe" Text="<%#$this->Data->deb_summe%>"/>
                </td>
                <td>
                    <com:TActiveDatePicker Id="deb_date" Mode="ImageButton" Text="<%#$this->Data->deb_date%>" DateFormat="yyyy-MM-dd" InputMode="TextBox" Width="80px" onCallback="page.recalcSumme"/>
                </td>
             </tr>
         </prop:ItemTemplate>
         <prop:FooterTemplate>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-top:1px double"><b><com:TActiveLabel Id="InvoiceSumLabel" Text="0.00"/></b></td>
                    <td></td>
                </tr>
             </table>
         </prop:FooterTemplate>
</com:TActiveRepeater>
<hr/>
                        <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/pencil.png"/>
                        <com:TActiveButton OnCallback="parent.CDEBSaveButtonClicked"
                                Text="<%[ hinzufügen ]%>"
                                CssClass="windowcontent-button"/>
</fieldset>

</td>
</tr>
</table>