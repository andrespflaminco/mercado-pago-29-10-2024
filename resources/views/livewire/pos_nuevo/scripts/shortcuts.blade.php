<script>
	var listener = new window.keypress.Listener();

	listener.simple_combo("f6", function() {
		console.log('f6')
	    livewire.emit('IrPasoAnterior')
	//	livewire.emit('aCash(0)')
	//	livewire.emit('saveSale')
	})

	listener.simple_combo("f7", function() {
		console.log('print last : f10')
	//	livewire.emit('print-last')
	livewire.emit('IrPasoPosterior')
	})
	
	listener.simple_combo("f8", function() {
		livewire.emit('IrPasoPosteriorConFacturacion')
	})


	listener.simple_combo("f4", function() {
		var total = parseFloat(document.getElementById('hiddenTotal').value)
		if(total > 0) {
			Confirm(0, 'clearCart', '¿SEGURO DE ELIMINAR EL CARRITO?')
		} else
		{
			noty('AGREGA PRODUCTOS A LA VENTA')
		}
	})



</script>
