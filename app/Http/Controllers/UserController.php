<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserController extends Controller
{
    public function sendout(): View
    {
        return view('send');
    }

    public function setting(): View
    {
        return view('setting');
    }

    public function linkaccount(): View
    {
        return view('link');
    }

    public function payBills(): View
    {
        return view('paybills');
    }

    public function receive(): View
    {
        return view('receive');
    }

    public function withdraw(): View
    {
        return view('withdraw');
    }

    public function topUp(): View
    {
        return view('topup');
    }

    public function history(): View
    {
        return view('history');
    }

    public function scan(): View
    {
        return view('scan');
    }

    public function card(): View
    {
        return view('cards');
    }

    public function support(): View
    {
        return view('support');
    }
}
