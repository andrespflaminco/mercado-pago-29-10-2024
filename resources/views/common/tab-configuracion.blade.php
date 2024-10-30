				       <ul class="nav nav-tabs">
                        <li style="background:white; border: solid 1px #eee;" class="nav-item">
                            <a class="nav-link {{ $componentName == 'general' ? 'active' : '' }}" href=" {{ url('mi-comercio') }}" class="nav-link" > GENERAL </a>
                        </li>
                        
                        @can('ver configuracion productos')
                        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                            <a class="nav-link {{ $componentName == 'productos' ? 'active' : '' }}"  href=" {{ url('configuracion-productos') }}" > PRODUCTOS </a>
                        </li>
                        @endcan 
                        
                        @can('ver configuracion cuenta corriente')
                        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                            <a class="nav-link {{ $componentName == 'cta_cte' ? 'active' : '' }} "  href=" {{ url('configuracion-cta-cte') }}" > CUENTA CORRIENTE CLIENTES</a>
                        </li>
                        @endcan
                        
                        @can('configurar cajas')
                        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                            <a class="nav-link {{ $componentName == 'cajas' ? 'active' : '' }} "  href=" {{ url('configuracion-cajas') }}" > CAJAS </a>
                        </li>
                        @endcan
                        
                        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                            <a class="nav-link {{ $componentName == 'impresion' ? 'active' : '' }} "  href=" {{ url('configuracion-impresion') }}" > IMPRESION </a>
                        </li>
                        
                    	</ul>