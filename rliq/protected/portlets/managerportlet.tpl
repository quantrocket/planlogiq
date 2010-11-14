<com:JsCookMenu
         ID="JSCookMenu"
         ThemeName="ThemeGray"
         JsCookMenuPath="../rliq/protected/common/JSCookMenu/JSCookMenu.js"
         StyleSheetThemePath="../rliq/protected/common/JSCookMenu/ThemeGray/theme.css"
         JsThemePath="../rliq/protected/common/JSCookMenu/ThemeGray/theme.js"
         MenuOrientation="hbr">

<com:JsCookMenuItem Url="index.php?page=Home" Title=<%[ Home ]%> Disabled="False">
    <com:JsCookMenuItem Title="Handbuch" Url="http://wiki.planlogiq.com" Target="_blank" Description = "Manual - Handbuch"/>
</com:JsCookMenuItem>
<com:JsCookMenuItem Url="index.php?page=organisation.orgworkspace" Title=<%[ Organisation ]%> Disabled=<%= !$this->User->getModulRights('mod_organisation') %>>
    <com:JsCookMenuItem Title=<%[ Organisation ]%> Url="index.php?page=organisation.orgworkspace" />
    <com:JsCookMenuItem Title=<%[ Timesheet ]%> Url="index.php?page=organisation.zeiterfassung" />
    <com:JsCookMenuItem Title=<%[ Lists ]%> Url="index.php?page=verteiler.verteilerworkspace" />
    <com:JsCookMenuItem Title=<%[ Stakeholder-Map ]%> Url="index.php?page=organisation.stakeholdermap" />
    <com:JsCookMenuItem Title=<%[ Ressources ]%> Disabled=<%= !$this->User->isInRole('Administrator') %> Url="index.php?page=organisation.ressourcenworkspace" />
    <com:JsCookMenuItem Title=<%[ Ressources Plan ]%> Disabled=<%= !$this->User->isInRole('Administrator') %> Url="index.php?page=organisation.ressourcenbelegung" />
    <com:JsCookMenuItem Break="true"/>
    <com:JsCookMenuItem Title=<%[ Translationtable ]%> Disabled=<%= !$this->User->isInRole('Administrator') %> Url="index.php?page=organisation.translationtable" />
</com:JsCookMenuItem>
<com:JsCookMenuItem Url="index.php?page=prozess.proworkspace" Title=<%[ Process ]%> Disabled=<%= !$this->User->getModulRights('mod_process') %> />
<com:JsCookMenuItem Url="index.php?page=ziele.zieworkspace" Title=<%[ Targets ]%> Disabled=<%= !$this->User->getModulRights('mod_ziele') %> />
<com:JsCookMenuItem Url="index.php?page=activity.actworkspace" Title=<%[ Projectstructure ]%> Disabled=<%= !$this->User->getModulRights('mod_activity') %> />
<com:JsCookMenuItem Url="index.php?page=risiko.risworkspace" Title=<%[ Risc ]%> Disabled=<%= !$this->User->getModulRights('mod_risiko') %> />
<com:JsCookMenuItem Url="index.php?page=protokoll.prtworkspace" Title=<%[ Reporting ]%> Disabled=<%= !$this->User->getModulRights('mod_protokoll') %> />

</com:JsCookMenu>