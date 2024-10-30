<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('scan-ok', Msg => {
			noty(Msg)
		})

		window.livewire.on('scan-notfound', barcode => {
			noty('El producto no esta registrado', 2)
			doAction(barcode)
		})

		window.livewire.on('no-stock', Msg => {
			noty(Msg, 2)
		})

		window.livewire.on('sale-ok', Msg => {
			console.log('sale-ok')
		//@this.printTicket(Msg)
		noty(Msg)
	    })

		window.livewire.on('sale-error', Msg => {
			noty(Msg, 2)
		})

		window.livewire.on('print-ticket', info => {
			window.open("print://" + info,  '_self').close()
		})
		window.livewire.on('print-last-id', saleId => {
			window.open("print://" + saleId,  '_self').close()
		})


		window.livewire.on('show-modal', info => {
				$('#modalDetails').modal('show')
		})

		window.livewire.on('hide-modal', Msg =>{
				$('#modalDetails').modal('hide')
		})


	})
</script>
