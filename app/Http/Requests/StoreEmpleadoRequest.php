<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al crear un nuevo empleado. Este request define las reglas de validación para los campos "nombre", "rol", "username" y "password". El campo "nombre" es obligatorio, debe ser una cadena de texto y no puede exceder los 100 caracteres. El campo "rol" es obligatorio y debe ser uno de los siguientes valores: "Administrador", "Mesero", "Cajero", "Recepcionista" o "Cocinero". El campo "username" es obligatorio, debe ser una cadena de texto, no puede exceder los 50 caracteres y debe ser único en la tabla "empleado". El campo "password" es obligatorio, debe ser una cadena de texto y debe tener al menos 6 caracteres. Al utilizar este request, se asegura que los datos enviados para crear un nuevo empleado cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',

            'rol' => 'required|in:Administrador,Mesero,Cajero,Recepcionista,Cocinero',

            'username' => 'required|string|max:50|unique:empleado,username',

            'password' => 'required|string|min:6'
        ];
    }

    // Método para personalizar los mensajes de error de validación. En este caso, se personaliza el mensaje para la regla "in" del campo "rol", indicando que el rol debe ser uno de los valores permitidos: Administrador, Mesero, Cajero, Recepcionista o Cocinero.
    public function messages(): array
{
    return [
        'rol.in' => 'El rol debe ser uno de los siguientes valores: Administrador, Mesero, Cajero, Recepcionista o Cocinero.',
        'username.unique' => 'El nombre de usuario ya está en uso.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.'
    ];
}
}