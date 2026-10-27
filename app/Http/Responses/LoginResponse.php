<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract {
    public function toResponse($request): Response {
        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->route('home');
    }
}
