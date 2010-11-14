<div class="portlet">

<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
<div class="xboxcontent">

<h2 class="portlet-title"><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/easymoblog.png" /> Dokumentation</h2>

<div class="portlet-content">

<com:AxListMenu CssClass="portlet-content" ActCss="portlet-link" IActCss="portlet-link" Colapse="true">
    <com:AxListMenuItem Text="Item1" PagePath="Page1" NavigateUrl="/page1" />
    <com:AxListMenuItem Text="Item2" PagePath="('Page2','Page1')" />
    <com:AxListMenuItem Text="Item3" Visible="false" PagePath="Page3" ActCss="active" IActCss="" />
    <com:AxListMenuColl Text="Sub1">
        <com:AxListMenuItem Text="Item41" />
    </com:AxListMenuColl>
    <com:AxListMenuColl Text="Sub2" Visible="false" Colapse="true">
        <com:AxListMenuItem Text="Item51" />
    </com:AxListMenuColl>
</com:AxListMenu>

<ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.introduction') %>">Handbuch - Intro</a></li>
<hr />
<li>Organisation</li>
<ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.orga.orga') %>">Organisation</a></li>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.orga.zeiterfassung') %>">Zeiterfassung</a></li>
<li>Ressourcen</li>
</ul>
<li>Planung</li>
<ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.planung.planung_intro') %>">Workspace</a></li>
</ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.ziele.ziele') %>">Ziele</a></li>
<ul>
<li>Ziele anlegen, bearbeiten</li>
</ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.ziele.ziele') %>">Risikomanagement</a></li>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.ziele.ziele') %>">Projektstrukturplan</a></li>
<ul>
<li>PSP-Eemente</li>
<li>Netzplan</li>
<li>Terminplan</li>
</ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.ziele.ziele') %>">Struktur</a></li>
<ul>
<li>Varianten</li>
<li>Perioden</li>
<li>Strukturelemente</li>
</ul>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.ziele.ziele') %>">Ziele</a></li>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.kosten.kosten') %>">Kosten</a></li>
<li><a href="<%= $this->getRequest()->constructUrl('page','manual.todo.todo') %>">ToDo s</a></li>
<hr />
<li><a href="<%= $this->getRequest()->constructUrl('page','Home') %>">Startseite</a></li>
</ul>

</div><!-- end of portlet-content -->

</div>
<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div><!-- end of portlet -->
