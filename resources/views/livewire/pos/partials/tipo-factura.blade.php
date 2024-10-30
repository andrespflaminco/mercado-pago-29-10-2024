                    <!-------------- TIPO DE FACTURA --------------------->    
                        
						<div class="mt-3" wire:ignore>
						<select wire:model.lazy='tipo_comprobante' wire:change="UpdateTipoFactura()"  style="font-size: 14px !important;" class="form-control mb-2" >
							<option value="" disabled> Tipo de comprobante</option>
							<option value="CF">Consumidor final</option>
							<option value="A">Factura A</option>
							<option value="B">Factura B</option>
							<option value="C">Factura C</option>

						</select>
					    </div>
					    <!-------------- / TIPO DE FACTURA --------------------->    
                      