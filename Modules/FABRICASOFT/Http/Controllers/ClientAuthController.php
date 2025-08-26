<?php

namespace Modules\FABRICASOFT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\FABRICASOFT\Entities\Preregistration;

class ClientAuthController extends Controller
{
    /**
     * Mostrar el formulario de login para clientes externos
     */
    public function showLoginForm()
    {
        return view('fabricasoft::client.login');
    }

    /**
     * Procesar el login de clientes externos
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            // Buscar el preregistro por email
            $preregistration = Preregistration::where('email', $request->email)->first();

            if (!$preregistration) {
                return back()->withErrors([
                    'email' => 'No se encontró una solicitud con este correo electrónico.'
                ])->withInput($request->only('email'));
            }

            // Verificar si la solicitud está aprobada
            if ($preregistration->status !== 'approved') {
                return back()->withErrors([
                    'email' => 'Tu solicitud aún no ha sido aprobada. Te notificaremos cuando esté lista.'
                ])->withInput($request->only('email'));
            }

            // Verificar la contraseña
            if (!Hash::check($request->password, $preregistration->password)) {
                return back()->withErrors([
                    'password' => 'La contraseña ingresada no es correcta.'
                ])->withInput($request->only('email'));
            }

            // Crear o actualizar el usuario en el sistema principal
            $user = $this->createOrUpdateUser($preregistration);

            // Iniciar sesión
            Auth::login($user);

            // Log del login exitoso
            Log::info('Cliente externo logueado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email,
                'preregistration_id' => $preregistration->id
            ]);

            return redirect()->route('cliente_externo.dashboard')
                           ->with('success', '¡Bienvenido! Has iniciado sesión correctamente.');

        } catch (\Exception $e) {
            Log::error('Error en login de cliente externo', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Ha ocurrido un error durante el inicio de sesión. Por favor, inténtalo de nuevo.'
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Cerrar sesión del cliente externo
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('fabricasoft.index')
                       ->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Crear o actualizar el usuario en el sistema principal
     */
    private function createOrUpdateUser($preregistration)
    {
        // Buscar si ya existe un usuario con este email
        $user = \App\Models\User::where('email', $preregistration->email)->first();

        if (!$user) {
            // Crear nueva persona
            $person = \Modules\SICA\Entities\Person::create([
                'first_name' => $this->extractFirstName($preregistration->full_name),
                'first_last_name' => $this->extractLastName($preregistration->full_name),
                'personal_email' => $preregistration->email,
                'telephone1' => $preregistration->phone,
                'document_type' => 'Cédula de ciudadanía', // Por defecto
                'document_number' => (int) $preregistration->phone, // Usar teléfono como documento temporal
                'eps_id' => 1, // ASMESALUD como valor por defecto
                'population_group_id' => 1, // Valor por defecto
                'pension_entity_id' => 1, // Valor por defecto
            ]);

            // Crear nuevo usuario
            $user = \App\Models\User::create([
                'email' => $preregistration->email,
                'password' => $preregistration->password, // Ya está hasheada
                'phone' => $preregistration->phone,
                'nickname' => $preregistration->full_name,
                'person_id' => $person->id,
            ]);

            // Asignar rol de cliente externo
            $this->assignExternalClientRole($user);
        } else {
            // Actualizar contraseña si es necesario
            if (!Hash::check($preregistration->password, $user->password)) {
                $user->update([
                    'password' => $preregistration->password
                ]);
            }
        }

        return $user;
    }

    /**
     * Extraer el primer nombre del nombre completo
     */
    private function extractFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? '';
    }

    /**
     * Extraer el apellido del nombre completo
     */
    private function extractLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }
        return '';
    }

    /**
     * Asignar rol de cliente externo al usuario
     */
    private function assignExternalClientRole($user)
    {
        try {
            // Buscar el rol de cliente externo
            $role = \Modules\SICA\Entities\Role::where('slug', 'fabricasoft.cliente_externo')->first();
            
            if ($role && !$user->hasCustomRole('fabricasoft.cliente_externo')) {
                $user->roles()->attach($role->id);
                
                Log::info('Rol de cliente externo asignado al usuario', [
                    'user_id' => $user->id,
                    'role_id' => $role->id
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('No se pudo asignar el rol de cliente externo', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
