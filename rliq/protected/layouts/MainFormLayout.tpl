<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<com:THead>
function constructCollapsableFieldsets()
    {
        var allFsets = document.getElementsByTagName('fieldset');
        var fset = null;
        for (var i=0; i<allFsets.length; i++)
        {
            fset = allFsets[i];
            if(fset.attributes['collapsed']!=null)
                constructCollapsableFieldset(fset, fset.attributes['collapsed'].value);
        }
    }

//for collapsable fieldset:
function constructCollapsableFieldset(fset, collapsed)
    {
        //main content:
        var divContent = fset.getElementsByTagName('div')[0];
        if (divContent == null)
            return;

        if (collapsed == 'true')
            divContent.style.display = 'none';

        //+/- ahref:
        var ahrefText = getAHrefForToogle(collapsed);

        //legend:
        var legend = fset.getElementsByTagName('legend')[0];
        if (legend != null){
            var tmpString = legend.innerHTML.replace(/<a.*\+<\/a>/g,'');
            legend.innerHTML = ahrefText + tmpString;
        }else{
            fset.innerHTML = '<legend>' + ahrefText + '</legend>' + fset.innerHTML;
        }
    }

function getAHrefForToogle(collapsed)
    {
        var ahrefText = "<a onClick='toogleFieldset(this.parentNode.parentNode);' style='text-decoration: none;'>";
        ahrefText = ahrefText + getExpanderItem(collapsed) + "</a>";
        return ahrefText;
    }

function getExpanderItem(collapsed)
    {
        var ecChar;
        if (collapsed=='true')
            ecChar='+';
        else
            ecChar='-';

        return ecChar;
    }

function toogleFieldset(fset)
    {
        var ahref = fset.getElementsByTagName('a')[0];
        var div = fset.getElementsByTagName('div')[0];

        if (div.style.display != "none")
        {
            ahref.innerHTML=getExpanderItem('true');
            div.style.display = 'none';
        }
        else
        {
            ahref.innerHTML=getExpanderItem('false');
            div.style.display = '';
        }
    }
</com:THead>

<body>
<com:TForm>
<div id="page">
<table border="0" cellpadding="0" cellspacing="0"><tr><td colspan="2"> 
<div id="header">
<com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kdevelop.png" />
<%= $this->User->Name %>
<com:TLabel id="UserLevel" />
<com:THyperLink Text="Login"
    NavigateUrl="<%= $this->getRequest()->constructUrl('page','user.loginuser') %>"
    Visible="<%= $this->User->IsGuest %>" />
<com:TLinkButton Text="Logout"
    OnClick="logoutButtonClicked"
    Visible="<%= !$this->User->IsGuest %>"
    CausesValidation="false" /><br /><br />
<%= $this->CurrentCulture %><com:Application.layouts.LanguageList />
</div>
</td></tr>

<tr><td valign="top">
<div id="sidebar">
	<com:TContentPlaceHolder ID="Navigation" />
	<com:Application.portlets.loginportlet Visible=<%= $this->User->IsGuest %> />
	<com:Application.portlets.managerportlet Visible=<%= !$this->User->IsGuest %> />
	<com:Application.portlets.accountportlet Visible=<%= !$this->User->IsGuest %> />
	<com:Application.pages.navigation.nav_main Visible=<%= 0 %> />
</div>
</td><td valign="top">
<div id="main">
<com:TPanel Visible=<%= !$this->User->IsGuest %> >
<com:TContentPlaceHolder ID="Main" />
<com:TProtectUnsavedForm Message="Achtung! Es wurden Daten ge�ndert!" />
</com:TPanel>
</div>
</td></tr>
<tr><td colspan="2"> 
<div id="footer">
&copy;2009 RiskLogIQ, Wien(AT)
</div>
</td></tr></table> 
</div>

<com:TJavascriptLogger Visible=<%= $this->User->isInRole('Administrator') %> /> 

</com:TForm>
</body>
</html>