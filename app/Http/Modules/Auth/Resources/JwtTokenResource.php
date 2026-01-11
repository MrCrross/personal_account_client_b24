<?php

namespace App\Http\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @response {"user": {}, "access_token":"", "token_type":"Bearer", "expires_on":3600}
 */
class JwtTokenResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'error' => false,
            'data' => [
                'user' => Auth::user(),
                'access_token' => $this['token'],
                'token_type' => 'Bearer',
                'expires_on' => Auth::guard('api')->getTTL() * 60,
            ],
        ];
    }
}
