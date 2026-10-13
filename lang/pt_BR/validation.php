<?php

declare(strict_types=1);

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'Informe um e-mail válido.',
    'string' => 'O campo :attribute deve ser um texto.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'date' => 'Informe uma data válida para :attribute.',
    'date_format' => 'O campo :attribute deve seguir o formato :format.',
    'after_or_equal' => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'in' => 'Selecione uma opção válida para :attribute.',
    'min' => ['numeric' => 'O campo :attribute deve ser no mínimo :min.', 'string' => 'O campo :attribute deve ter no mínimo :min caracteres.'],
    'max' => ['numeric' => 'O campo :attribute deve ser no máximo :max.', 'string' => 'O campo :attribute deve ter no máximo :max caracteres.'],
    'confirmed' => 'A confirmação de :attribute não corresponde.',
    'attributes' => [],
];
