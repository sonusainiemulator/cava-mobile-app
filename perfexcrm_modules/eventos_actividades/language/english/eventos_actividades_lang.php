<?php
// Path: modules/eventos_actividades/language/english/eventos_actividades_lang.php
defined('BASEPATH') or exit('No direct script access allowed');

$lang['eventos_actividades_menu']                       = 'Events and Activities';
$lang['eventos_actividades_actividades_title']          = 'Activities and Points Table';
$lang['eventos_actividades_registrar_puntos_title']     = 'Register Points by Activity';
$lang['eventos_actividades_registrar_puntos_btn']       = 'Register Points';
$lang['eventos_actividades_historial_title']            = 'Points History';
$lang['eventos_actividades_totales_title']              = 'Total Points by User';
$lang['eventos_actividades_totales_puntos']             = 'Total Points';
$lang['eventos_actividades_field_nombre']               = 'Activity';
$lang['eventos_actividades_field_puntos']               = 'Points';
$lang['eventos_actividades_field_actividad']            = 'Activity';
$lang['eventos_actividades_actividad']                  = 'Activity';
$lang['eventos_actividades_log']                        = 'Points Log';

$lang['eventos_actividades_eventos_title']              = 'Event Management';
$lang['eventos_actividades_eventos_mapa_title']         = 'Event Map';
$lang['eventos_actividades_eventos_mapa_help']          = 'Enter the latitude and longitude of the event to mark the location on the map. Example: 19.4326, -99.1332 (Mexico City).';

$lang['eventos_actividades_field_evento_nombre']        = 'Event Name';
$lang['eventos_actividades_field_fecha']                = 'Date';
$lang['eventos_actividades_field_hora']                 = 'Time';
$lang['eventos_actividades_field_pais']                 = 'Country';
$lang['eventos_actividades_field_estado']               = 'State / Province';
$lang['eventos_actividades_field_ciudad']               = 'City';
$lang['eventos_actividades_field_direccion']            = 'Address';
$lang['eventos_actividades_field_lat']                  = 'Latitude';
$lang['eventos_actividades_field_lng']                  = 'Longitude';

$lang['eventos_actividades_evento']                     = 'Event';

$lang['eventos_actividades_field_auto']                 = 'Auto';
$lang['eventos_actividades_field_trigger']              = 'Trigger';
$lang['eventos_actividades_field_threshold']            = 'Threshold (X)';
$lang['eventos_actividades_trigger_manual']             = 'Manual';
$lang['eventos_actividades_trigger_scans']              = 'Scans';
$lang['eventos_actividades_auto_info_title']            = 'Automatic Assignment';
$lang['eventos_actividades_auto_info_body']             = 'Activate "Auto" + Trigger "Scans" + Threshold (e.g., 10). When the user reaches X scans, the system automatically assigns points and updates the profile field "Points (Events and activities)".';
$lang['eventos_actividades_apply_auto_btn']             = 'Apply Auto (Recalculate)';
$lang['eventos_actividades_auto_award_applied']         = 'Auto-award Applied';
$lang['eventos_actividades_scan_recorded']              = 'Scan Recorded';
$lang['eventos_actividades_scan_title']                 = 'Register Scan (Test)';
$lang['eventos_actividades_scan_barcode']               = 'Barcode';
$lang['eventos_actividades_scan_btn']                   = 'Register Scan';
$lang['eventos_actividades_scan_help']                  = 'Each scan is saved in the module. If an activity with Auto=ON, Trigger=Scans, and Threshold=X exists, when the user reaches X scans, points are awarded automatically (only once).';
$lang['eventos_actividades_escaneo_integration_note']   = 'Integration: Scans are automatically read from the SCAN module if it exists (otherwise, it uses the internal table).';

// Common keys (added to ensure they display correctly if missing from core)
$lang['dropdown_non_selected_text']                     = 'Nothing selected';
$lang['no_results_found']                               = 'No results found';
$lang['date']                                           = 'Date';
$lang['options']                                        = 'Options';
$lang['submit']                                         = 'Save';

