<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<com:THead>

<script type="text/javascript">
function pass_value_to_parent(url) {
    window.opener.location=url;
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
</script>

</com:THead>

<body style="overflow:auto">

<com:TForm>

    <com:TPanel Visible=<%= !$this->User->IsGuest %> >
        <com:TContentPlaceHolder ID="MainWindow" />
    </com:TPanel>

</com:TForm>


</body>
</html>