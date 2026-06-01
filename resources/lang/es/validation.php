<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio.',
    'numeric' => 'El campo :attribute debe ser numérico.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'string' => 'El campo :attribute no debe superar :max caracteres.',
    ],
    'in' => 'El valor seleccionado en :attribute no es válido.',

    'attributes' => [
        'package_weight_kg' => 'peso del paquete (kg)',
        'package_height_cm' => 'alto del paquete (cm)',
        'package_width_cm' => 'ancho del paquete (cm)',
        'package_length_cm' => 'largo del paquete (cm)',
        'recipient_name' => 'nombre del destinatario',
        'recipient_contact_number' => 'teléfono del destinatario',
        'item_description' => 'descripción del paquete',
        'destination_payment_method' => 'método de pago en destino',
        'offered_fare' => 'tarifa ofrecida',
        'service_id' => 'tipo de servicio',
    ],
];
