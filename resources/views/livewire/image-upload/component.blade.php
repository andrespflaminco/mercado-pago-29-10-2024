
<div class="row">
    <form wire:submit.prevent="saveImages" id="uploadForm">
        <div style="background: white;" id="dropzone" class="dropzone">
            Elegi las imagenes <br>
            o
            <br>Arrastra y solta las imágenes aca.
            <input type="file" id="fileInput" wire:model="images" multiple style="display: none;">
        </div>
        @error('images.*') <span class="error">{{ $message }}</span> @enderror

        <div class="progress" style="display: {{ $progress > 0 ? 'block' : 'none' }};">
            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
        </div>

        <div class="image-preview">
            @foreach ($images as $key => $image)
                <div class="image-container">
                    <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="image-preview">
                    <p>{{ $image->getClientOriginalName() }}</p>
                </div>
            @endforeach
        </div>
    </form>

<div class="row">
                
@foreach($imagenes as $product)   
<div class="col-2 mb-4">
    <img src="{{ asset('storage/products/' . $product->url ) }}" alt="{{$product->name}}"  title="{{$product->name}}"  class="rounded w-100">
    <div class="d-flex align-items-center" style="background: white !important;">
        <div class="col-10">
            <span style="padding: 2px 0px 2px 10px; width: 100%; white-space: pre-wrap; word-wrap: break-word;   display: inline-block;">{{$product->name}}</span>
        </div>
        <div class="col-2 text-center">
            <a href="javascript:void(0)" id="borrar-imagen" onclick="Confirm('{{$product->id}}')" style="background:white;" title="Eliminar imagen">
                <i class="far fa-times-circle"></i>
            </a>				    
        </div>
    </div>
</div>
@endforeach


            
</div>

</div>


    <script>
        document.addEventListener('livewire:load', function () {
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('fileInput');
            const progressBar = document.querySelector('.progress-bar');

            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });

            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            });

            dropzone.addEventListener('click', function() {
                fileInput.click();
            });

            Livewire.on('fileUploadProgress', function(progress) {
                progressBar.style.width = progress + '%';
                progressBar.innerText = progress + '%';
            });
        });
    </script>

<script>
    	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: 'CONFIRMAS ELIMINAR LA IMAGEN?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteImage', id)
				swal.close()
			}

		})
	}
    
</script>

