<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // 1. Verificar Autenticación
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // 2. Definir Roles Permitidos
        // Convierte la cadena de roles (ej: 'administrador,empleado') en un array
        $allowedRoles = explode(',', $roles);
        
        // 3. Verificar Permiso
        if (!in_array($user->role, $allowedRoles)) {
            // Si no tiene el rol permitido, lo redirige al home con un mensaje
            return redirect('/')->with('error', 'Acceso denegado. No tiene los permisos necesarios.');
        }

        return $next($request);
    }
}