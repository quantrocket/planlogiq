<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<com:THead>
</com:THead>
<body>
<com:TForm>
    <div id="page">
    <table border="0" cellpadding="0" cellspacing="0" class="haupttabelle">
        <tr><td colspan="2">            
            <div id="header">
                <com:XGlobalCallbackOptions>
                        <prop:ClientSide.OnLoading>
                                $('loading').show();
                        </prop:ClientSide.OnLoading>
                        <prop:ClientSide.OnComplete>
                                $('loading').hide();
                        </prop:ClientSide.OnComplete>
                </com:XGlobalCallbackOptions>
                <div ID="loading" class="loading1" Style="display:none;"><%[ please wait.. ]%></div>
                <com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kuser.png" />
                <%= $this->User->Name %>
                <com:TLabel id="UserLevel" />
                <com:THyperLink Text="Login"
                    NavigateUrl="<%= $this->getRequest()->constructUrl('page','user.loginuser') %>"
                    Visible="<%= $this->User->IsGuest %>" />
                <com:TLinkButton Text="Logout"
                    OnClick="logoutButtonClicked"
                    Visible="<%= !$this->User->IsGuest %>"
                    CausesValidation="false" />
                <p><%= $this->CurrentCulture %><com:Application.layouts.LanguageList /></p>

            </div>
        </td></tr>
        <tr><td valign="top" id="sidebar">
                    <com:TContentPlaceHolder ID="Navigation" />
                    <com:Application.portlets.loginportlet Visible=<%= $this->User->IsGuest %> />
                    <com:Application.portlets.managerportlet Visible=<%= $this->User->isInRole('Administrator') %> />
                    <com:Application.portlets.accountportlet Visible=<%= !$this->User->IsGuest %> />
    </td><td valign="top" bgcolor="#ffffff" align="left" width="100%">
        <div id="main">

            <com:TPanel Visible=<%= $this->User->IsGuest %> >
                <com:TContentPlaceHolder ID="GuestMain" />
            </com:TPanel>

            <com:TPanel Visible=<%= !$this->User->IsGuest %> >
                <com:TContentPlaceHolder ID="Main" />
            </com:TPanel>
        </div>
    </td></tr>
    <tr><td colspan="2">
        <div id="footer">
        &copy;2009 SoftLogIQ, Wien(AT)
        </div>
    </td></tr></table>
        </div>
    <com:TJavascriptLogger Visible=<%= $this->User->isInRole('Administrator') %> />
</com:TForm>
</body>
</html>