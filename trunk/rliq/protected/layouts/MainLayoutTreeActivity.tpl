<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<com:THead>

<script type="text/javascript">

function win_organisation_openwin(url) {
   window.open(url,'org_window','location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no,width=550,height=400');
}

function win_orgstb_openwin(url) {
   window.open(url,'orgstb_window','location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no,width=400,height=250');
}

function setRowBackground(theRow,theColor)
{
   var theCells = theRow.cells;
   var rowCellsCount = theCells.length;
   var c = null;
   for( c=0; c<rowCellsCount; c++ )
   {
      theCells[c].style.backgroundColor = theColor;
   }
}

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

<com:TContentPlaceHolder ID="HeaderJavascript" />

</script>

</com:THead>

<body onLoad="doOnLoad();">

<com:TForm>

<com:TCallback ID="MyMenuCallback" OnCallback="mainMenuAction" />

<div id="dhtmlxSubLayout">
<div id="dhtmlxLayout">
        <div id="header">
                <com:XGlobalCallbackOptions>
                        <prop:ClientSide.OnLoading>
                                dhxLayout.progressOn();
                        </prop:ClientSide.OnLoading>
                        <prop:ClientSide.OnComplete>
                                dhxLayout.progressOff();
                        </prop:ClientSide.OnComplete>
                </com:XGlobalCallbackOptions>
                <com:Application.layouts.LanguageList />
            </div>

    <div id="sidebar">
                    <com:TContentPlaceHolder ID="Navigation" />
                    <com:Application.portlets.loginportlet Visible=<%= $this->User->IsGuest %> />                    
    </div>

    <div id="dhtmlxSubLayout">

        <div id="main">
            
            <com:TPanel Visible=<%= !$this->User->IsGuest %> >
                <com:TContentPlaceHolder ID="Main" />                    
            </com:TPanel>

            <com:TPanel Visible=<%= $this->User->IsGuest||$this->User->isInRole('Administrator') %> >
                <com:TContentPlaceHolder ID="GuestMain" />
            </com:TPanel>

        </div>
    </div> <!-- dhtmlxSubLayout /-->

    <div id="footer">
        <%= $this->CurrentCulture %>
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
        &copy;2009 SoftLogIQ, Wien(AT)
        <com:TJavascriptLogger Visible=<%= $this->User->isInRole('Administrator') %> />
    </div>
</div>
</com:TForm>

<script type="text/javascript">
 var viewportwidth; var viewportheight;

 if (typeof window.innerWidth != 'undefined')
  {
    viewportwidth = window.innerWidth;
    viewportheight = window.innerHeight }

    else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0){
        viewportwidth = document.documentElement.clientWidth;
        viewportheight = document.documentElement.clientHeight
    }
    // older versions of IE
    else{
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth;
        viewportheight = document.getElementsByTagName('body')[0].clientHeight;
    }

   var width= viewportwidth;
   var height=viewportheight;

    document.getElementById("dhtmlxLayout").style.width =width + "px";
    document.getElementById("dhtmlxLayout").style.height = height + "px";
</script>

<com:PFDHTMLXTreeActivityLayout />

</body>
</html>