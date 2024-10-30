<?php

namespace App\Http\Livewire;
use App\Models\ClientesMostrador;

use Livewire\Component;

class Select2AutoSearch extends Component
{
  public $query;
    public $contacts;
    public $highlightIndex;

    public function mount()
    {
      $this->query = '';
      $this->contacts = [];
      $this->highlightIndex = 0;
    }



    public function incrementHighlight()
    {
        if ($this->highlightIndex === count($this->contacts) - 1) {
            $this->highlightIndex = 0;
            return;
        }
        $this->highlightIndex++;
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex === 0) {
            $this->highlightIndex = count($this->contacts) - 1;
            return;
        }
        $this->highlightIndex--;
    }

    public function selectContact()
    {
        $contact = $this->contacts[$this->highlightIndex] ?? null;
        if ($contact) {
            $this->redirect(route('show-contact', $contact['id']));
        }
    }

    public function updatedQuery()
    {
        $this->contacts = ClientesMostrador::where('nombre', 'like', '%' . $this->query . '%')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.clientes-mostrador.component');
    }
}
