<?php

return [
    'roles' => [
        'admin' => [
            'dashboard.view',
            'transportistas.view',
            'transportistas.manage',
            'registro.view',
            'registro.manage',
            'clientes.view',
            'clientes.manage',
            'proveedores.view',
            'proveedores.manage',
            'altas.view',
            'altas.manage',
            'tarifas.view',
            'tarifas.manage',
            'tarifas.precio',
            'unidades.view',
            'unidades.manage',
        ],
        'operador' => [
            'dashboard.view',
            'registro.view',
            'registro.manage',
            'clientes.view',
            'clientes.manage',
            'proveedores.view',
            'proveedores.manage',
            'altas.view',
            'altas.manage',
            'tarifas.view',
            'tarifas.manage',
            'unidades.view',
            'unidades.manage',
        ],
        'visor' => [
            'dashboard.view',
            'registro.view',
            'clientes.view',
            'proveedores.view',
            'altas.view',
            'tarifas.view',
            'unidades.view',
        ],
        'transportista' => [
            'dashboard.view',
            'unidades.view',
            'unidades.manage',
        ],
    ],

    'defaults' => [
        'role' => 'visor',
    ],
];


