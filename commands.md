# Comandos del proyecto

## Reprogramación de pedidos pendientes

Permite reprogramar los pedidos pendientes para una fecha determinada.

**Reprogramar para mañana (por defecto):**
```bash
sail artisan orders:reschedule-pending
```

**Reprogramar para hoy:**
```bash
sail artisan orders:reschedule-pending today
```

**Reprogramar para una fecha específica:**
```bash
sail artisan orders:reschedule-pending 2026-11-07
```

## Queue (Colas)

Levantar el worker para procesar los Jobs en desarrollo.

```bash
sail artisan queue:work
```

> Dejar este proceso corriendo mientras se trabaja en desarrollo para que Laravel procese los Jobs encolados.

## Scheduler (Planificador)

Comprobar las tareas programadas y sus próximas ejecuciones.

```bash
sail artisan schedule:list
```

Ejecutar manualmente las tareas programadas (útil para probarlas en desarrollo):

```bash
sail artisan schedule:run
```

