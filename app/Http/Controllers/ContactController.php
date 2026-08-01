<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $telephone = Setting::get('contact_telephone', '+229 00 00 00 00');
        $email = Setting::get('contact_email', 'contact@csnda-cotonou.bj');
        $adresse = Setting::get('contact_adresse', 'Cotonou, Bénin');
        $mapEmbed = Setting::get('contact_map_embed', '');

        return view('public.contact', compact('telephone', 'email', 'adresse', 'mapEmbed'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create($data);

        $recipient = Setting::get('contact_email', config('mail.from.address'));

        if ($recipient) {
            Mail::to($recipient)->send(new ContactMessageMail($contactMessage));
        }

        return back()->with('status', 'Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.');
    }
}
