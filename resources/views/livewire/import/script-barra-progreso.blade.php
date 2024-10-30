<script>

                            window.livewire.on('estatus-proceso-importacion', (filaProcesada, totalFilas) => {
                                if (filaProcesada <= totalFilas) {
                                    window.livewire.emit('checkProgress');
                                } else {
                                    location.reload();
                                }
                            });
                            
                            window.livewire.on('estatus-proceso-validacion', (filaProcesada, totalFilas,import_id) => {
                                if (filaProcesada <= totalFilas) {
                                    window.livewire.emit('checkProgressValidacion');
                                } else {
                                    window.livewire.emit('checkValidacion',import_id);
                                }
                            });
                            window.livewire.on('progressUpdated', (progress) => {
                                const progressBar = document.querySelector('.progress-bar');
                                progressBar.style.width = progress + '%';
                                progressBar.innerHTML = (Math.round(progress * 100) / 100) + '%';
                            });
                            
                            
                        </script>