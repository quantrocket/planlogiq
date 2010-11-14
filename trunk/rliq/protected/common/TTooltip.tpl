<div id="<%= $this->getForControl() %>_tooltip" style="display: none" class="<%= $this->getCssClass() %>"><%= $this->getText() %></div>
<com:TClientScript>
var <%=$this->getForControl() %> = new Tooltip('<%=$this->getForControlClientId() %>', '<%=$this->getForControl() %>_tooltip');
</com:TClientScript>