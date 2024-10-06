<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FileOwner
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $file = File::query()->find($request->file_id);
        if(!$file)
        {
            return response()->json('Invalid id', 401);
        }

//        $user= auth()->guard('user-api')->user();

        if(Auth::id() == $file->user_id)
        {
            return $next($request);
        }
        else return response()->json('Invalid Access', 401);

    }
}
