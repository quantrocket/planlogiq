<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<com:THead>

<style type="text/css">

body{
    font-family:Verdana, Arial;
    color:#efefef;
    background: #121212;
}

#footer {
    text-align: center;
    margin-top: 0px;
    padding-top: 0px;
    padding-bottom: 3px;
    border-top: 1px solid silver;
    border-bottom: 0px solid silver;
    background-color: #666666;
    color: #ffffff;
    font-weight: bold;
    font-size: 9px;
}

.windowcontent-button{
    font-size:10px;
    color: #000000;
    text-align: center;
    border-right-style: solid;
    border-left-style: solid;
    border-bottom-style: solid;
    border-top-style: solid;
    border-right-width: 1px;
    border-left-width: 1px;
    border-bottom-width: 1px;
    border-top-width: 1px;
    padding-top: 4px;
    padding-bottom: 4px;
    padding-left: 10px;
    padding-right: 10px;
    background-color: silver;
    background-image: url(/rliq/themes/basic/gfx/background_toolbar.jpg);
    font-weight: bold;
}

#infobox {
    color:#232323;
    background-color:#f0f0f4;
    padding: 5px 5px 5px 5px;
    border-right-style: solid;
    border-left-style: solid;
    border-bottom-style: solid;
    border-top-style: solid;
    border-right-width: 1px;
    border-left-width: 1px;
    border-bottom-width: 1px;
    border-top-width: 1px;
    border-right-color: #ffffcc;
    border-left-color: #ffffcc;
    border-bottom-color: #ffffcc;
    border-top-color: #ffffcc;
    text-align: left;
    vertical-align: top;
}

#mainmain{
    align:center;
}

h2{
    color:#ffffff;
    font-family:Verdana, Arial;
    font-size:12px;
}

h3{
    color:orange;
    font-family:Verdana, Arial;
    font-size:12px;
}

legend{
    color:orange;
    font-family:Verdana, Arial;
    font-size:11px;
}

.custominput{
    text-align:right;
}

input,select,textarea{
    font-size:13px;
    background-attachment: scroll;
    background-color: white;
    background-image: none;
    border-bottom-color: #999;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    border-collapse: separate;
    border-left-color: #CCC;
    border-left-style: solid;
    border-left-width: 1px;
    border-right-color: #999;
    border-right-style: solid;
    border-right-width: 1px;
    border-top-color: #CCC;
    border-top-style: solid;
    border-top-width: 1px;
}

input:focus{
    color: #000000;
    background-color: #ffffcc;
}

input:hover,select:hover,textarea:hover{
    background-color: #ffffcc;
}

</style>

<script type="text/javascript">

function win_organisation_openwin(url) {
   window.open(url,'org_window','location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no,width=700,height=500');
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

<body>

<com:TForm>

        <div id="mainmain">
            
            <com:TPanel Visible=<%=!$this->User->IsGuest%> >
                <com:TContentPlaceHolder ID="Main" />
            </com:TPanel>

            <com:TPanel Visible=<%=$this->User->IsGuest%> >
                <com:TContentPlaceHolder ID="GuestMain" />
            </com:TPanel>

        </div>

    <div id="footer">
        <%= $this->CurrentCulture %>
        <com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kuser.png" />
                <%= $this->User->Name %>
                <com:TLinkButton Text="Logout"
                    OnClick="logoutButtonClicked"
                    Visible="<%= !$this->User->IsGuest %>"
                    CausesValidation="false" />
        &copy;2010 Frenzel GmbH, Stuttgart(DE)
    </div>

</com:TForm>

</body>
</html>