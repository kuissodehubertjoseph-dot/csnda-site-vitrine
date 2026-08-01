<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if (! $contactMessage->read) {
            $contactMessage->update(['read' => true]);
        }

        return view('admin.messages.show', compact('contactMessage'));
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message supprimé.');
    }
}
