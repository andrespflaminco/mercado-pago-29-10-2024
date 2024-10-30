<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CommonTrait {

  //use classTestCommon;

  public $testingCommon;

  public function testCommonTrait(){
      //return 'testing common funciona';
     // return 'testing common funciona';
    // $this->testingCommon = 'testing common funciona';

      $testClass = new classTestCommon();

      return $testClass->test();
  }

    //SET COMMERCIO ID
   /* public function setComercioId(){
      return Auth::user()->comercio_id != 1 ? Auth::user()->comercio_id : Auth::user()->id;
    }*/


}


class classTestCommon 
{
    public function test()
    {
      return 'test of a some class';
    }
}