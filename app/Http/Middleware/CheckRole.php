<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware // Nombre de la clase ajustado
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles  Lista de roles permitidos separados por coma (ej: 'administrador,trainer')
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // 1. Verificar autenticación
        if (! $request->user()) {
            return redirect('/login'); 
        }

        // 2. Obtener roles y rol del usuario
        $allowedRoles = explode(',', $roles);
        $userRole = $request->user()->role;
        
        // 3. Comprobar si el rol del usuario está en la lista de permitidos
        if (in_array($userRole, $allowedRoles)) {
            return $next($request); // Acceso concedido
        }

        // 4. Acceso denegado
        return abort(403, 'Acceso no autorizado: No tienes el rol requerido.');
    }
}