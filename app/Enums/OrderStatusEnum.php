<?php

namespace App\Enums;


enum OrderStatusEnum : string {

    case PENDIENTE = 'pendiente';

    case PROCESANDO = 'procesando';

    case COMPLETADO = 'completado';

    case RECHAZADO = 'rechazado';
}
