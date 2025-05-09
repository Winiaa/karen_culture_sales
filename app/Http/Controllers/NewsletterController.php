<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Subscribe to the newsletter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:newsletter_subscribers,email']
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already subscribed to our newsletter.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $subscriber = NewsletterSubscriber::create([
                'email' => $request->email,
                'is_active' => true,
                'subscribed_at' => now(),
            ]);

            Log::info('New newsletter subscription', [
                'email' => $subscriber->email,
                'subscribed_at' => $subscriber->subscribed_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);
        } catch (\Exception $e) {
            Log::error('Newsletter subscription failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your subscription. Please try again.'
            ], 500);
        }
    }

    /**
     * Unsubscribe from the newsletter
     *
     * @param string $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unsubscribe($email)
    {
        try {
            $subscriber = NewsletterSubscriber::where('email', $email)->first();

            if (!$subscriber) {
                return redirect()->route('home')->with('error', 'Invalid unsubscribe link.');
            }

            $subscriber->update(['is_active' => false]);

            Log::info('Newsletter unsubscription', [
                'email' => $email,
                'unsubscribed_at' => now()
            ]);

            return redirect()->route('home')->with('success', 'You have been unsubscribed from our newsletter.');
        } catch (\Exception $e) {
            Log::error('Newsletter unsubscription failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('home')->with('error', 'An error occurred while processing your unsubscription. Please try again.');
        }
    }
} 