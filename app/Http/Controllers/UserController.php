<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
     public function sendout()
    {
        return view ('send');
    }

    public function setting()
    {
        return view ('setting');
    }

     public function linkaccount()
    {
        return view ('link');
    }

    public function payBills()
    {
        return view ('paybills');
    }

      public function receive()
    {
        return view ('receive');
    }

    public function withdraw()
    {
        return view ('withdraw');
    }

    public function topUp()
    {
        return view ('topup');
    }

    public function history()
    {
        return view ('history');
    }

    public function scan()
    {
        return view ('scan');
    }

    public function card()
    {
        return view('cards');
    }

       public function support()
    {
        return view('support');
    }

    
}
