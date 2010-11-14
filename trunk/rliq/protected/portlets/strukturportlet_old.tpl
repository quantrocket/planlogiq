
<com:NModalPanel ID="mpnlTest" >
    <div id="test" style="border:solid 1px #660000; min-height:420px; width: 650px; background-color: #ffffff; padding:5px; ">
        <com:Application.pages.container.VariantenContainer ID="VariantenContainer"  />
    </div>
</com:NModalPanel>

<div class="portlet-white">

    <h2 class="portlet-title"><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/atlantik.png" /> DataWH</h2>

    <div class="portlet-content">

        <table>
        <tr>
            <td><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kdisknav.png" />Variante:</td><td><com:TActiveDropDownList id="DWH_idta_variante" CssClass="mandantory" /> <com:TActiveImageButton ImageUrl="/rliq/themes/basic/gfx/16x16/actions/filefind.png" OnCommand="OpenVariantenContainer"/></td>
        </tr>
        <tr>
            <td><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kalarm.png" />Zeit:</td><td><com:TDropDownList id="DWH_idta_perioden" CssClass="mandantory" AutoPostback="true" /></td>
        </tr>
        </table>

    </div><!-- end of portlet-content -->

<h2 class="portlet-title"><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/atlantik.png" /><%[ Structure ]%></h2>

<div class="portlet-content">

<table>
<tr><td>
<com:TCallback ID="MyCallback" OnCallback="callback_MyCallback"> </com:TCallback>
<com:TActivePanel Width="230px" ScrollBars="Auto">
        <com:PFTafelTree ID="TreeView"/>
</com:TActivePanel>
</td></tr>
</table>

<ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','reports.gewinnundverlust') %>">01. <%[ Profit and Loss ]%></a></li>
</ul>

<table class="strukturtabelle">
<tr><td>
<com:MyTreeList ID="MyTree" CssClass="mytree" NodeType=<%=MyTreeList::NODE_TYPE_INTERNAL_ACTIVE_LINK%> Deploy="true" CanDeploy="false"/>
</td></tr>

</table>

</div><!-- end of portlet-content -->

</div><!-- end of portlet -->
