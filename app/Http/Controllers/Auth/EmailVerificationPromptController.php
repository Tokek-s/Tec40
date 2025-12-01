<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmailVerificationPromptController
{
	/**
	 * Display the email verification prompt.
	 */
	public function __invoke(Request $request)
	{
		if ($request->user() && $request->user()->hasVerifiedEmail()) {
			return redirect()->intended(route('dashboard', absolute: false));
		}

		return Inertia::render('Auth/VerifyEmail');
	}

	// Middleware se aplica desde las rutas en routes/auth.php
}

