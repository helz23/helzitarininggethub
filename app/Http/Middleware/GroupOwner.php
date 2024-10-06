<?php

namespace App\Http\Middleware;

use App\Models\File;
use App\Models\Group;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class GroupOwner
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
        $group = Group::query()->find($request->group_id);
        if(!$group)
        {
            return response()->json('Invalid id', 401);
        }

//        $user= auth()->guard('user-api')->user();

        if(Auth::id() == $group->user_id)
        {
            return $next($request);
        }
        else return response()->json('Invalid Access', 401);

    }
}
