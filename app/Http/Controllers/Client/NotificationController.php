<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(15);

        return view('client.notifications', ['notifications' => $notifications]);
    }

    public function lireTout()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('succes', 'Toutes vos notifications ont été marquées comme lues.');
    }
}
