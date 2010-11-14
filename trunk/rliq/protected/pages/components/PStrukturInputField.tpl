<com:TActiveTextBox Id="XXXInputField"
                    CssClass="custominput"
                    Text=""
                    width="120px"
                    AutoPostback="true"
                    onCallback="page.updateDBValue"/>
<com:TActiveImageButton onCallback="showInputBox"
    ImageUrl="/rliq/themes/basic/gfx/16x16/actions/redo.png"
    Visible=<%=$this->getApplication()->User->IsAdmin%>/>

<com:TActiveImageButton onCallback="showCommentBox"
    ImageUrl="/rliq/themes/basic/gfx/16x16/actions/info.png"/>

<com:TActiveLabel Text="(<%=$this->getID()%>)" Visible=<%=$this->getApplication()->User->IsAdmin%>/>

<com:PWCWindow ID="mpnlPStrukturInputContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MyPStrukturInputContainer->ClientID%>"
               Mode="Existing"
               Width="600px"
               Left="100"
               Top="100"
               Title="Einstellungen Eingabefeld">
</com:PWCWindow>

<com:PWCWindow ID="mpnlKommentarContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MympnlKommentarContainer->ClientID%>"
               Mode="Existing"
               Width="600px"
               Left="100"
               Top="100"
               Title="Kommentar">
</com:PWCWindow>

<com:TActivePanel ID="MympnlKommentarContainer" Display="None">
                <com:Application.pages.container.KommentarContainerNOP ID="KommentarContainerNOP"/>
                <%#$this->initComments()%>
</com:TActivePanel>

<com:TActivePanel ID="MyPStrukturInputContainer" Display="None">
<fieldset>
<legend>Optionen</legend>
<table>
    <tr>
        <td><p style="color:#000000:font-weight:bold;">Variante:</p></td>
        <td><com:TActiveDropDownList Id="idta_variante" 
                        DataTextField="var_descr"
                        DataValueField="idta_variante"/></td>
    </tr>
    <tr>
        <td><p style="color:#000000:font-weight:bold;">Periode:</p></td>
        <td><com:TActiveDropDownList Id="idta_perioden" 
                        DataTextField="per_extern"
                        DataValueField="idta_perioden"/></td>
    </tr>
    <tr>
        <td><p style="color:#000000:font-weight:bold;">Dimension:</p></td>
        <td><com:TActiveDropDownList Id="idtm_stammdaten" 
                        DataTextField="stammdaten_name"
                        DataValueField="idtm_stammdaten"
                        DataGroupField="idta_stammdaten_group" /></td>
    </tr>
    <tr>
        <td><p style="color:#000000:font-weight:bold;">Feldfunktion:</p></td>
        <td><com:TActiveDropDownList Id="idta_feldfunktion"
                        DataTextField="ff_name"
                        DataValueField="idta_feldfunktion"
                        DataGroupField="idta_struktur_type" /></td>
    </tr>
    <tr>
        <td><p style="color:#000000:font-weight:bold;">Format:</p></td>
        <td><com:TActiveDropDownList Id="cuf_numberformat" /></td>
    </tr>
</table>
<hr />
<com:TActiveButton Text="apply" OnCommand="pageAction" CommandName="apply" CssClass="windowcontent-button"/>
<p style="color:#454545;"><%=$this->Id%></p>
</fieldset>
</com:TActivePanel>