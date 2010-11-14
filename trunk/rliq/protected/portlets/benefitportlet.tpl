<div class="portlet">

    <b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
    <div class="xboxcontent">
        <h2 class="portlet-title">Navigation</h2>
        <div class="portlet-content">
            <com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kuser.png" /> <%[ Hello ]%>, <b><%= $this->User->Name %></b>!
            <table width="100%">
            <tr><td class="portlet-link"><a href="<%= $this->getRequest()->constructUrl('page','Home') %>"><%[ Home ]%></a></td></tr>
            <tr><td class="portlet-link"><a href="<%= $this->getRequest()->constructUrl('page','manual.introduction') %>"><%[ Manual ]%></a></td></tr>
            <tr><td class="portlet-link"><a href="<%= $this->getRequest()->constructUrl('page','manual.introduction') %>"><%[ Contact ]%></a></td></tr>
            </table>
        </div>
     </div>
     <b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
     <!-- end of portlet -->

</div>


<div class="portlet">

<h2 class="portlet-title"><%[ Your Benefits ]%></h2>

<com:TPanel CssClass="portlet-content">

<ul>
    <li><%[ 24/7 Online available ]%></li>
    <li><%[ Web 2.0 - AJAX ]%></li>
    <li><%[ Multilanguage support ]%></li>
    <li><%[ Office integration ]%></li>
</ul>

</com:TPanel><!-- end of portlet-content -->

<h2 class="portlet-title"><%[ Contact ]%></h2>

<com:TPanel CssClass="portlet-content">

<p>
(AT) - RiskLogIQ<br/>
Biberstrasse 8<br/>
1010 Vienna<br/><br/>
<com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/kmail.png" /><a href="mailto:info@risklogiq.de?subject=RISKLOGIQ">E-Mail senden...</a>
</p>

</com:TPanel><!-- end of portlet-content -->

<h2 class="portlet-title"><%[ Lisence ]%></h2>

<com:TPanel CssClass="portlet-content">

<p>Risklogiq is published under a dual lisence modell.
Please <a href="mailto:info@risklogiq.de?subject=RISKLOGIQ">contact us</a> for further informations! </p>

</com:TPanel><!-- end of portlet-content -->

<h2 class="portlet-title"><%[ Manual ]%></h2>

<com:TPanel CssClass="portlet-content">

<ul>
    <li><a href="<%= $this->getRequest()->constructUrl('page','manual.introduction') %>">z. <%[ Manual ]%></a></li>
</ul>

</com:TPanel><!-- end of portlet-content -->

</div><!-- end of portlet -->
