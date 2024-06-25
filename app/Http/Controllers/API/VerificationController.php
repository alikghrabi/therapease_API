<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified as VerifiedEvent;
use Illuminate\Support\Facades\Validator;
class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = \App\Models\User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification link',
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email already verified',
            ], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new VerifiedEvent($user));
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully',
        ]);
    }
}
