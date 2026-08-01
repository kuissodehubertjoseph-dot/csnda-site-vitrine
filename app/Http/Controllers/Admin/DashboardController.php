<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\GalleryPhoto;
use App\Models\News;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'news' => News::count(),
            'photos' => GalleryPhoto::count(),
            'unreadMessages' => ContactMessage::where('read', false)->count(),
        ];

        $latestMessages = ContactMessage::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestMessages'));
    }
}
