<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorController extends Controller
{
    protected $lid=1;
    protected $name;

    public function __construct(){
        $this->lid =12345;
        $this->name='Angelo';
    }

    public function showConstruct(){
        return $this->lid.' '.$this->name;
    }



  //  public function index($name, $lid){
  //      return view ('other/doctors', compact('name','lid'));
 //   }

   // public function store(){
   //     return 'save data';
   // }

   // public function update(){
   //     return 'update data';
   // }

  // public function delete(){
    //    return 'delete';
   // }
}
