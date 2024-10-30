<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
          <b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'VER' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
        <fieldset disabled>
          <div class="row">

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>User</label>
                <input type="text" wire:model.lazy="user_id" class="form-control">
                @error('user_id') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Suscripcion Id</label>
                <input type="text" wire:model.lazy="suscripcion_id" class="form-control">
                @error('suscripcion_id') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Plan Id</label>
                <input type="text" wire:model.lazy="plan_id" class="form-control">
                @error('plan_id') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Payer Id</label>
                <input type="text" wire:model.lazy="payer_id" class="form-control">
                @error('payer_id') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Payer Email</label>
                <input type="text" wire:model.lazy="payer_email" class="form-control">
                @error('payer_email') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Suscripción Status</label>
                <input type="text" wire:model.lazy="suscripcion_status" class="form-control">
                @error('suscripcion_status') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Cobro Status</label>
                <input type="text" wire:model.lazy="cobro_status" class="form-control">
                @error('cobro_status') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Nombre comercio</label>
                <input type="text" wire:model.lazy="nombre_comercio" class="form-control">
                @error('nombre_comercio') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Fecha suscripción</label>
                <input type="text" wire:model.lazy="fecha_suscripcion" class="form-control">
                @error('fecha_suscripcion') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Monto Mensual</label>
                <input type="text" wire:model.lazy="monto_mensual" class="form-control">
                @error('monto_mensual') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Monto Plan</label>
                <input type="text" wire:model.lazy="monto_plan" class="form-control">
                @error('monto_plan') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Users amount</label>
                <input type="text" wire:model.lazy="users_amount" class="form-control">
                @error('users_amount') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Users acount</label>
                <input type="text" wire:model.lazy="users_count" class="form-control">
                @error('users_count') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Init Point</label>
                <input type="text" wire:model.lazy="init_point" class="form-control">
                @error('init_point') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>External Reference</label>
                <input type="text" wire:model.lazy="external_reference" class="form-control">
                @error('external_reference') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Plan Flaminco</label>
                <input type="text" wire:model.lazy="plan_id_flaminco" class="form-control">
                @error('plan_id_flaminco') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Próximo cobro</label>
                <input type="text" wire:model.lazy="proximo_cobro" class="form-control">
                @error('proximo_cobro') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Pagado</label>
                <input type="text" wire:model.lazy="pagado" class="form-control">
                @error('pagado') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Reintentos</label>
                <input type="text" wire:model.lazy="reintentos" class="form-control">
                @error('reintentos') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="form-group">
                <label>Action</label>
                <input type="text" wire:model.lazy="action" class="form-control">
                @error('action') <span class="text-danger er">{{ $message}}</span>@enderror
              </div>
            </div>

          </div>
        </fieldset>

      </div>
      <div class="modal-footer">

        <button type="button" wire:click.prevent="Close()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
        <!--
        @if($selected_id < 1) 
          <button type="button" wire:click.prevent="CreateSuscripcionControl()" class="btn btn-submit">GUARDAR</button>
          @else
          <button type="button" wire:click.prevent="UpdateSuscripcionControl()" class="btn btn-submit">ACTUALIZAR</button>
          @endif
        -->

      </div>
    </div>
  </div>
</div>