Prado.WebUI.BActiveDatePicker = Class.create();
Prado.WebUI.BActiveDatePicker.OnActiveDateChanged = function(sender,parameter){
	var request = new Prado.CallbackRequest(sender.options.EventTarget,sender.options);
	request.dispatch();
}