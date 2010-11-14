<div class="portlet">
<fieldset style="background-color:#ffffff">
    <legend>Anmeldung</legend>
            <table cellspacing="4" width="100%"><tr><td>&nbsp</td><td>
            <com:TActivePanel CssClass="login_sidebar" DefaultButton="LoginButton">
                <b><%[ Username ]%></b>
                <com:TRequiredFieldValidator
                        ControlToValidate="Username"
                        ValidationGroup="login"
                        Text="...is required"
                        Display="Dynamic"/>
                <br/>
                <com:TTextBox ID="Username" CssClass="inputmedium"/>
                <br/>
                <b><%[ Password ]%></b>
                <com:TCustomValidator
                        ControlToValidate="Password"
                        ValidationGroup="login"
                        Text="...is invalid"
                        Display="Dynamic"
                        OnServerValidate="validateUser" />
                <br/>
                <com:TTextBox ID="Password" TextMode="Password" CssClass="inputmedium"/>
        </com:TActivePanel>
        </td></tr>
        <tr><td align="right" colspan="2">
            <hr/>
            <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/actions/unlock.png"/>
            <com:TLinkButton
                        ID="LoginButton"
                        Text="Login"
                        ValidationGroup="login"
                        CssClass="windowcontent-button"
                        OnClick="loginButtonClicked" />
                <com:TActiveImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kaddressbook.png"/>
                <com:THyperLink
                        Text="Register"
                        NavigateUrl=<%= $this->getRequest()->constructUrl('page','user.newuser') %>
                        Visible=<%=$this->Application->Parameters['allowUserRegistration']%>
                        CssClass="windowcontent-button" />
        </td></tr>
     </table>
     <!-- end of portlet -->
</fieldset>
</div>

