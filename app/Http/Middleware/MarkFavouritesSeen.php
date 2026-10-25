<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarkFavouritesSeen
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // GET only, so clicking a heart (POST) does not clear the badge
        if ($user && $request->isMethod('GET')) {
            $data = ['fav_avatar_seen_at' => now()];

            if ($request->routeIs('account.favourites')) {
                $data['fav_menu_seen_at'] = now();
            }

            $user->forceFill($data)->save();
        }

        return $next($request);
    }
}