<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);

        return view('admin.notifications', ['notifications' => $notifications]);
    }

    public function lireTout()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('succes', 'Notifications marquées comme lues.');
    }
}
