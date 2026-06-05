<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ResendVerificationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request, $id, $hash): View
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return view('auth.verify-email', [
                'title' => 'Invalid Link',
                'message' => 'This verification link is invalid or has expired.',
                'success' => false,
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return view('auth.verify-email', [
                'title' => 'Already Verified',
                'message' => 'Your email address has already been verified.',
                'success' => true,
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return view('auth.verify-email', [
            'title' => 'Email Verified',
            'message' => 'Your email address has been verified successfully.',
            'success' => true,
        ]);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(ResendVerificationRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'Verification link sent.']);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    }
}