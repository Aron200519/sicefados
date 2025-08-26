<?php

namespace Modules\FABRICASOFT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Modules\FABRICASOFT\Entities\Preregistration;
use Modules\FABRICASOFT\Emails\NewPreregistrationNotification;

class PreregistroController extends Controller
{
    /**
     * Mostrar el formulario de pre-registro
     */
    public function index()
    {
        return view('fabricasoft::preregistro');
    }

    /**
     * Procesar el pre-registro
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'software_type' => 'required|string|max:255',
            'project_description' => 'required|string|max:1000',
            'additional_requirements' => 'nullable|string|max:500',
            'terms_accepted' => 'required|accepted',
        ]);

        try {
            // Crear el pre-registro usando el modelo
            $preregistration = Preregistration::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'organization' => $validated['organization'],
                'password' => bcrypt($validated['password']), // Hashear la contraseña
                'software_type' => $validated['software_type'],
                'project_description' => $validated['project_description'],
                'additional_requirements' => $validated['additional_requirements'],
                'status' => 'pending',
                'client_type' => 'cliente_externo', // Siempre es cliente externo para pre-registro
            ]);

            // Enviar notificación por email al admin si está habilitado
            if (Config::get('fabricasoft.notifications.email_enabled', true)) {
                $this->sendNotificationToAdmin($preregistration);
            }

            // Log de la solicitud
            Log::info('New FABRICASOFT software development request', [
                'id' => $preregistration->id,
                'email' => $validated['email'],
                'full_name' => $validated['full_name'],
                'organization' => $validated['organization'],
                'software_type' => $validated['software_type']
            ]);

            return redirect()->route('fabricasoft.preregistro.success')
                           ->with('success', 'Tu solicitud de desarrollo de software ha sido enviada exitosamente. Nuestro equipo técnico la revisará y te contactaremos en las próximas 24-48 horas para discutir tu proyecto.');

        } catch (\Exception $e) {
            Log::error('Error processing FABRICASOFT software development request', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return back()->withInput()
                        ->withErrors(['error' => 'Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Mostrar página de éxito
     */
    public function success()
    {
        return view('fabricasoft::preregistro_success');
    }

    /**
     * Enviar notificación por email al admin
     */
    private function sendNotificationToAdmin($preregistration)
    {
        try {
            // Obtener emails de administradores desde la configuración del módulo
            $adminEmails = Config::get('fabricasoft.admin_emails', ['admin@fabricasoft.com']);
            
            // Si no hay emails configurados, usar un email por defecto
            if (empty($adminEmails)) {
                $adminEmails = ['admin@fabricasoft.com'];
            }
            
            // Enviar email a cada admin
            foreach ($adminEmails as $adminEmail) {
                Mail::to($adminEmail)->send(new NewPreregistrationNotification($preregistration));
            }
            
            Log::info('Admin notification sent successfully', [
                'solicitud_id' => $preregistration->id,
                'admin_emails' => $adminEmails
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Could not send admin notification email', [
                'solicitud_id' => $preregistration->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
