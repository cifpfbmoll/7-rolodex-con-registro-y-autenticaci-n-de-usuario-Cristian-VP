<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Auth::user()->contacts()->get();
        return view('dashboard', compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
        ]);

        $contact = Auth::user()->contacts()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contacto guardado exitosamente',
            'contact' => $contact
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        // Verificar que el contacto pertenece al usuario autenticado
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contacto actualizado exitosamente',
            'contact' => $contact
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        // Verificar que el contacto pertenece al usuario autenticado
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contacto eliminado exitosamente'
        ]);
    }

    /**
     * Export contacts to CSV
     */
    public function export()
    {
        $contacts = Auth::user()->contacts()->get();

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');

            // Encabezados del CSV
            fputcsv($file, ['Nombre Completo', 'Teléfono', 'Email', 'Fecha de Creación']);

            // Datos de los contactos
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->full_name,
                    $contact->phone_number,
                    $contact->email_address,
                    $contact->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contactos_' . date('Y-m-d_H-i-s') . '.csv"'
        ]);
    }
}

