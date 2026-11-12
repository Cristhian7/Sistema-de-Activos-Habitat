<?php

/*
|--------------------------------------------------------------------------
| Permisos por rol
|--------------------------------------------------------------------------
|
| La llave es el nombre del rol tal como está en la tabla rol (sin tildes y en minúsculas).
| Cada permiso tiene la forma "modulo.accion":
|
|   ver      : listado, detalle e impresión (solo lectura)
|   crear    : formulario de alta y su guardado (incluye las búsquedas que usa el formulario)
|   editar   : modificar un registro (también "Devolver" un equipo en traslados)
|   eliminar : eliminar o dar de baja un registro
|
| "*" significa todo el sistema. Para cambiar lo que puede hacer un rol basta con editar esta lista.
|
| Módulos: dashboard, equipos, empleados, traslados, mantenimientos, bajas, bitacora, tarjetas, reportes
|
*/

return [

    'roles' => [

        'administrador' => ['*'],

        'analista' => [
            'dashboard.ver',
            'equipos.ver', 'equipos.crear',
            'empleados.ver', 'empleados.crear',
            'traslados.ver', 'traslados.crear',
            'mantenimientos.ver', 'mantenimientos.crear',
            'bajas.ver', 'bajas.crear',
            'bitacora.ver',
            'tarjetas.ver',
        ],

        // El Técnico no sube ni modifica nada: solo consulta y revisa lo que suben el Administrador y el Analista.
        // "bajas.ver" es solo lectura y hace falta para abrir las notificaciones de bajas.
        'tecnico' => [
            'equipos.ver',
            'empleados.ver',
            'traslados.ver',
            'bajas.ver',
            'tarjetas.ver',
            'notificaciones.ver',
        ],

    ],

    // Roles que reciben una notificación cuando se sube el documento firmado de un traslado o una baja.
    // Cada uno la marca como "revisada". Para que otro rol también las reciba, agrégalo aquí.
    'notificar' => ['tecnico'],

    // Orden en que se busca la pantalla de inicio de un rol (la primera que pueda ver)
    'inicio' => [
        'dashboard.ver'   => 'dashboard',
        'equipos.ver'     => 'equipos.index',
        'empleados.ver'   => 'empleados.index',
        'traslados.ver'   => 'traslados.index',
        'tarjetas.ver'    => 'tarjetas.index',
    ],

];
