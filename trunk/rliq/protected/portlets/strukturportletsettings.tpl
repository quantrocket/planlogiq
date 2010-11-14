<com:PWCWindow ID="mpnlStammdatensichtContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MyStammdatensichtContainer->ClientID%>"
               Mode="Existing"
               Width="700px"
               Left="100"
               Top="100"
               Title="Stammdatensicht">
</com:PWCWindow>

<com:TActivePanel ID="MyStammdatensichtContainer" Display="None">
        <com:Application.pages.container.StammdatensichtContainer ID="StammdatensichtContainer"  />
</com:TActivePanel>


<com:PWCWindow ID="mpnlStammdatenGroupContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MyStammdatenGroupContainer->ClientID%>"
               Mode="Existing"
               Width="700px"
               Left="100"
               Top="100"
               Title="Stammdaten Group">
</com:PWCWindow>

<com:TActivePanel ID="MyStammdatenGroupContainer" Display="None">
        <com:Application.pages.container.StammdatenGroupContainer ID="StammdatenGroupContainer"  />
</com:TActivePanel>


<com:PWCWindow ID="mpnlStammdatenContainer"
               AutoPosition="false"
               Theme="alphacube"
               Content="<%=$this->MyStammdatenContainer->ClientID%>"
               Mode="Existing"
               Width="700px"
               Left="100"
               Top="100"
               Title="Stammdaten Details">
</com:PWCWindow>

<com:TActivePanel ID="MyStammdatenContainer" Display="None">
        <com:Application.pages.container.StammdatenContainer ID="StammdatenContainer"  />
</com:TActivePanel>

<div class="portlet">
<div class="portlet-title"><com:TImage ImageUrl="/rliq/themes/basic/gfx/16x16/apps/aim.png" /><%[ Settings ]%></div>

   <div class="portlet-content">

        <table width="100%">
            <tr><td class="portlet-link"><com:TActiveLinkButton Text=<%[ Dimension Views ]%> OnCommand="OpenStammdatensichtContainer"/></td></tr>
            <tr><td class="portlet-link"><com:TActiveLinkButton Text=<%[ Dimension Groups ]%> OnCommand="OpenStammdatenGroupContainer"/></td></tr>
            <tr><td class="portlet-link"><com:TActiveLinkButton Text=<%[ Dimension Values ]%> OnCommand="OpenStammdatenContainer"/></td></tr>
        </table>
    </div><!-- end of portlet-content -->

</div><!-- end of portlet -->