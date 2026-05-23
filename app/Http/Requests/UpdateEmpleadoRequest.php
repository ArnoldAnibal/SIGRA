<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Request para validar los datos al actualizar un empleado existente. Este request define las reglas de validación para los campos "nombre", "rol", "username" y "password". El campo "nombre" es opcional, pero si se proporciona, debe ser una cadena de texto y no puede exceder los 100 caracteres. El campo "rol" es opcional, pero si se proporciona, debe ser uno de los siguientes valores: "Administrador", "Mesero", "Cajero", "Recepcionista" o "Cocinero". El campo "username" es opcional, pero si se proporciona, debe ser una cadena de texto, no puede exceder los 50 caracteres y debe ser único en la tabla "empleado", excepto para el empleado que se está actualizando. El campo "password" también es opcional, pero si se proporciona, debe ser una cadena de texto y debe tener al menos 6 caracteres. Al utilizar este request, se asegura que los datos enviados para actualizar un empleado existente cumplan con las reglas de validación definidas antes de procesarlos en el controlador.
class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:100',

            'rol' => 'sometimes|in:Administrador,Mesero,Cajero,Recepcionista,Cocinero',

            'username' => 'sometimes|string|max:50|unique:empleado,username,' . $this->route('id') . ',id_empleado',

            'password' => 'sometimes|string|min:6'
        ];
    }

        public function messages(): array
{
    return [
        'rol.in' => 'El rol debe ser uno de los siguientes valores: Administrador, Mesero, Cajero, Recepcionista o Cocinero.',
        'username.unique' => 'El nombre de usuario ya está en uso.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.'
    ];
}
}