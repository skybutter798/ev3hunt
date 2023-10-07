<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckClickLimit
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $todayClicks = $user->clicks()->whereDate('clicked_at', now('Asia/Kuala_Lumpur')->toDateString())->count();
    
        if ($todayClicks >= 2) {
            return redirect('/')->with('error', 'You have reached your daily click limit.');
        }
    
        return $next($request);
    }

}
