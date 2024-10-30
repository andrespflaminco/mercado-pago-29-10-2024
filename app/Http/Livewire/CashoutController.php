<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use Carbon\Carbon;


class CashoutController extends Component
{
    public $fromDate, $toDate, $userid, $total, $items, $sales, $details, $comercio_id, $usuario_id;

    public function mount()
    {
        $this->fromDate = null;
        $this->toDate = null;
        $this->userid = 0;
        $this->total =0;
        $this->sales = [];
        $this->details = [];
    }

    public function render()
    {
        $usuario_id = Auth::user()->id;    
    
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;
      
        return view('livewire.cashout.component', [
            'users' => User::where('comercio_id', 'like', $comercio_id)->orWhere('id', 'like', $usuario_id)->orderBy('name','asc')->get()
        ])->extends('layouts.theme.app')
        ->section('content');
    }


    public function Consultar()
    {

        $fi= Carbon::parse($this->fromDate)->format('Y-m-d') . ' 00:00:00';
        $ff= Carbon::parse($this->toDate)->format('Y-m-d') . ' 23:59:59';

        $this->sales = Sale::whereBetween('created_at', [$fi, $ff] )
        ->where('status', 'Paid')
        ->where('user_id', $this->userid)
        ->get();

        $this->total = $this->sales ? $this->sales->sum('total') : 0;
        $this->items = $this->sales ? $this->sales->sum('items') : 0;


    }


    public function viewDetails(Sale $sale)
    {
       $fi= Carbon::parse($this->fromDate)->format('Y-m-d') . ' 00:00:00';
       $ff= Carbon::parse($this->toDate)->format('Y-m-d') . ' 23:59:59';



       $this->details = Sale::join('sale_details as d','d.sale_id','sales.id')
       ->join('products as p','p.id','d.product_id')
       ->select('d.sale_id','p.name as product','d.quantity','d.price')
       ->whereBetween('sales.created_at', [$fi, $ff])
       ->where('sales.status', 'Paid')
       ->where('sales.user_id', $this->userid)
       ->where('sales.id', $sale->id)
       ->get();

       $this->emit('show-modal','open modal');

   }


   public function Print()
   {
       
   }
}
