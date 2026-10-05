<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        // 1. Multi-role session resolution (Allows simultaneous Admin and Kader side-by-side tabs)
        $wantsAdmin = in_array('admin', $roles) && !in_array('kader', $roles);
        $wantsKader = in_array('kader', $roles) && !in_array('admin', $roles);
        $allowsBoth = in_array('admin', $roles) && in_array('kader', $roles);

        if ($wantsAdmin && session()->has('admin_user_id')) {
            $adminUser = User::find(session('admin_user_id'));
            if ($adminUser && $adminUser->role === 'admin') {
                Auth::setUser($adminUser);
            }
        } elseif ($wantsKader && session()->has('kader_user_id')) {
            $kaderUser = User::find(session('kader_user_id'));
            if ($kaderUser && $kaderUser->role === 'kader') {
                Auth::setUser($kaderUser);
            }
        } elseif ($allowsBoth) {
            $referer = (string) $request->headers->get('referer', '');
            $isKaderContext = $request->get('role') === 'kader' || str_contains($referer, '/kader');

            if ($isKaderContext && session()->has('kader_user_id')) {
                $kaderUser = User::find(session('kader_user_id'));
                if ($kaderUser && $kaderUser->role === 'kader') {
                    Auth::setUser($kaderUser);
                }
            } elseif (session()->has('admin_user_id')) {
                $adminUser = User::find(session('admin_user_id'));
                if ($adminUser && $adminUser->role === 'admin') {
                    Auth::setUser($adminUser);
                }
            }
        }

        // 2. Check Authentication
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $currentUser = Auth::user();

        // 3. Fallback switch if user has the required role stored in dual session
        if (!in_array($currentUser->role, $roles)) {
            if (in_array('kader', $roles) && session()->has('kader_user_id')) {
                $kaderUser = User::find(session('kader_user_id'));
                if ($kaderUser && $kaderUser->role === 'kader') {
                    Auth::setUser($kaderUser);
                    return $next($request);
                }
            }
            if (in_array('admin', $roles) && session()->has('admin_user_id')) {
                $adminUser = User::find(session('admin_user_id'));
                if ($adminUser && $adminUser->role === 'admin') {
                    Auth::setUser($adminUser);
                    return $next($request);
                }
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}