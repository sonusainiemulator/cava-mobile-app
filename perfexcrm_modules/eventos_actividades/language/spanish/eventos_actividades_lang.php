<?php
// Path: modules/eventos_actividades/language/spanish/eventos_actividades_lang.php
defined('BASEPATH') or exit('No direct script access allowed');

$lang['eventos_actividades_menu']                       = 'Eventos y actividades';
$lang['eventos_actividades_actividades_title']          = 'Tabla de actividades y puntos';
$lang['eventos_actividades_registrar_puntos_title']     = 'Registrar puntos por actividad';
$lang['eventos_actividades_registrar_puntos_btn']       = 'Registrar puntos';
$lang['eventos_actividades_historial_title']            = 'Historial de puntos registrados';
$lang['eventos_actividades_totales_title']              = 'Totales de puntos por usuario';
$lang['eventos_actividades_totales_puntos']             = 'Puntos totales';
$lang['eventos_actividades_field_nombre']               = 'Actividad';
$lang['eventos_actividades_field_puntos']               = 'Puntos';
$lang['eventos_actividades_field_actividad']            = 'Actividad';
$lang['eventos_actividades_actividad']                  = 'Actividad';
$lang['eventos_actividades_log']                        = 'Registro de puntos';

$lang['eventos_actividades_eventos_title']              = 'Gestión de eventos';
$lang['eventos_actividades_eventos_mapa_title']         = 'Mapa de eventos';
$lang['eventos_actividades_eventos_mapa_help']          = 'Ingresa la latitud y longitud del evento para marcar la ubicación en el mapa. Ejemplo: 19.4326, -99.1332 (Ciudad de México).';

$lang['eventos_actividades_field_evento_nombre']        = 'Nombre del evento';
$lang['eventos_actividades_field_fecha']                = 'Fecha';
$lang['eventos_actividades_field_hora']                 = 'Hora';
$lang['eventos_actividades_field_pais']                 = 'País';
$lang['eventos_actividades_field_estado']               = 'Estado / Provincia';
$lang['eventos_actividades_field_ciudad']               = 'Ciudad';
$lang['eventos_actividades_field_direccion']            = 'Dirección';
$lang['eventos_actividades_field_lat']                  = 'Latitud';
$lang['eventos_actividades_field_lng']                  = 'Longitud';

$lang['eventos_actividades_evento']                     = 'Evento';

$lang['eventos_actividades_field_auto'] = 'Auto';
$lang['eventos_actividades_field_trigger'] = 'Disparador';
$lang['eventos_actividades_field_threshold'] = 'Umbral (X)';
$lang['eventos_actividades_trigger_manual'] = 'Manual';
$lang['eventos_actividades_trigger_scans'] = 'Escaneos';
$lang['eventos_actividades_auto_info_title'] = 'Asignación automática';
$lang['eventos_actividades_auto_info_body'] = 'Activa "Auto" + Disparador "Escaneos" + Umbral (ej. 10). Cuando el usuario llegue a X escaneos, el sistema asigna puntos automáticamente y actualiza el campo de perfil "Puntos (Eventos y actividades)".';
$lang['eventos_actividades_apply_auto_btn'] = 'Aplicar auto (recalcular)';
$lang['eventos_actividades_auto_award_applied'] = 'Auto-award aplicado';
$lang['eventos_actividades_scan_recorded'] = 'Escaneo registrado';
$lang['eventos_actividades_scan_title'] = 'Registrar escaneo (prueba)';
$lang['eventos_actividades_scan_barcode'] = 'Código de barras';
$lang['eventos_actividades_scan_btn'] = 'Registrar escaneo';
$lang['eventos_actividades_scan_help'] = 'Cada escaneo se guarda en el módulo. Si existe una actividad Auto=ON con Disparador=Escaneos y Umbral=X, al llegar a X escaneos el usuario recibe los puntos automáticamente (solo 1 vez).';
$lang['eventos_actividades_escaneo_integration_note'] = 'Integración: los escaneos se leen automáticamente del módulo ESCANEO si existe (si no, usa la tabla interna).';

// Common keys (added to ensure they display correctly if missing from core)
$lang['dropdown_non_selected_text'] = 'Nada seleccionado';
$lang['no_results_found']           = 'No se encontraron resultados';
$lang['date']                       = 'Fecha';
$lang['options']                    = 'Opciones';
$lang['submit']                     = 'Guardar';

