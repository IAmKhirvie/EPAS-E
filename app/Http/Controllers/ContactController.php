<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Log the inquiry for now (can be extended to send email)
            Log::info('Contact Form Submission', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'submitted_at' => now(),
            ]);

            // Optional: Send email notification
            // Mail::to('ietimarikina8@yahoo.com')->send(new ContactInquiry($validated));

            return back()->with('success', 'Thank you for your inquiry! We will get back to you within 1-2 business days.');
        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage());
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again later.')->withInput();
        }
    }
}
