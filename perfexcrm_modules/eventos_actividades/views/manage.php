<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .tequila-dashboard {
        background: #fdfdfd;
        padding-top: 20px;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #f0f0f0;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .stat-card .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 20px;
    }
    .stat-card .value {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }
    .stat-card .label {
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .nav-tabs-tequila {
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 30px;
        display: flex;
        gap: 8px;
    }
    .nav-tabs-tequila > li > a {
        border: none !important;
        color: #6b7280;
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
        margin-right: 0;
    }
    .nav-tabs-tequila > li.active > a {
        color: #c5a059 !important;
        background: transparent !important;
        position: relative;
    }
    .nav-tabs-tequila > li.active > a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #c5a059;
        border-radius: 3px 3px 0 0;
    }

    .card-tequila {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .card-header-tequila {
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-header-tequila h4 {
        margin: 0;
        font-weight: 700;
        color: #1f2937;
        font-size: 16px;
    }

    #eventos_mapa {
        border-radius: 12px;
        border: 1px solid #eee;
    }

    .btn-gold {
        background: #c5a059;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-gold:hover {
        background: #b68d40;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(197, 160, 89, 0.3);
    }

    .table-tequila thead th {
        background: #f9fafb;
        color: #4b5563;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.025em;
        padding: 16px 24px;
        border: none;
    }
    .table-tequila tbody td {
        padding: 16px 24px;
        vertical-align: middle;
        border-top: 1px solid #f3f4f6;
    }
    
    .manual-award-box {
        max-width: 500px;
        margin: 40px auto;
        padding: 40px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #f0f0f0;
    }
</style>

<div id="wrapper" class="tequila-dashboard">
    <div class="content">
        <div class="container-fluid">
            
            <!-- HEADER SECTION -->
            <div class="row tw-mb-8">
                <div class="col-md-12">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <div>
                            <h3 class="tw-font-bold tw-text-2xl tw-m-0 tw-text-gray-800">Experience Management</h3>
                            <p class="tw-text-gray-500 tw-mt-1">Control activities, events, and point allocations.</p>
                        </div>
                        <div class="tw-flex tw-gap-3">
                            <a href="<?php echo admin_url('eventos_actividades/apply_auto'); ?>" class="btn btn-default tw-rounded-lg">
                                <i class="fa fa-refresh tw-mr-1"></i> Sync Logic
                            </a>
                            <button onclick="new_actividad()" class="btn btn-gold">
                                <i class="fa fa-plus tw-mr-1"></i> New Activity
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATS ROW -->
            <div class="row tw-mb-10">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box tw-bg-amber-50 tw-text-amber-600">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <div class="value"><?php echo count($actividades); ?></div>
                        <div class="label">Activities</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box tw-bg-blue-50 tw-text-blue-600">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="value"><?php echo count($eventos); ?></div>
                        <div class="label">Total Events</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box tw-bg-emerald-50 tw-text-emerald-600">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="value"><?php echo count($totales); ?></div>
                        <div class="label">Participants</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box tw-bg-purple-50 tw-text-purple-600">
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="value"><?php echo array_sum(array_column($totales, 'total_puntos')); ?></div>
                        <div class="label">Points Awarded</div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT TABS -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-tabs nav-tabs-tequila" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_events" aria-controls="tab_events" role="tab" data-toggle="tab">
                                <i class="fa fa-map-marker tw-mr-2"></i> Events & Map
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_activities" aria-controls="tab_activities" role="tab" data-toggle="tab">
                                <i class="fa fa-list-alt tw-mr-2"></i> Activity Catalog
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_manual" aria-controls="tab_manual" role="tab" data-toggle="tab">
                                <i class="fa fa-gift tw-mr-2"></i> Manual Award
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_ranking" aria-controls="tab_ranking" role="tab" data-toggle="tab">
                                <i class="fa fa-trophy tw-mr-2"></i> High Scores
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        
                        <!-- EVENTS TAB -->
                        <div role="tabpanel" class="tab-pane active" id="tab_events">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card-tequila">
                                        <div class="card-header-tequila">
                                            <h4>Activity Map</h4>
                                            <span class="tw-text-xs tw-text-gray-400">Markers for live events</span>
                                        </div>
                                        <div class="tw-p-4">
                                            <div id="eventos_mapa" style="height: 500px; width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card-tequila">
                                        <div class="card-header-tequila">
                                            <h4>Event Registry</h4>
                                            <a href="<?php echo admin_url('eventos_actividades/event'); ?>" class="btn btn-default btn-xs">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                        <div class="tw-max-h-[500px] tw-overflow-y-auto">
                                            <table class="table table-hover tw-m-0">
                                                <tbody>
                                                    <?php foreach ($eventos as $e) { ?>
                                                        <tr class="tw-group">
                                                            <td class="tw-py-4 tw-px-6">
                                                                <span class="tw-font-bold tw-text-gray-800 tw-block"><?php echo html_escape($e['nombre']); ?></span>
                                                                <small class="tw-text-gray-500">
                                                                    <i class="fa fa-map-pin tw-mr-1"></i> <?php echo html_escape($e['ciudad']); ?>
                                                                    <span class="tw-mx-1">•</span>
                                                                    <?php echo $e['fecha'] ?: 'N/A'; ?>
                                                                </small>
                                                            </td>
                                                            <td class="text-right tw-py-4 tw-px-6">
                                                                <a href="<?php echo admin_url('eventos_actividades/event/' . $e['id']); ?>" class="tw-text-gray-300 hover:tw-text-blue-500 tw-mr-2">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>
                                                                <a href="<?php echo admin_url('eventos_actividades/delete_event/' . $e['id']); ?>" class="tw-text-gray-300 hover:tw-text-red-500 _delete">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    <?php if(empty($eventos)) { ?>
                                                        <tr><td colspan="2" class="text-center tw-py-10 tw-text-gray-400">No events registered yet.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIVITIES TAB -->
                        <div role="tabpanel" class="tab-pane" id="tab_activities">
                            <div class="card-tequila">
                                <table class="table table-tequila dt-table">
                                    <thead>
                                        <tr>
                                            <th>Trigger / Task</th>
                                            <th>Reward</th>
                                            <th>Type</th>
                                            <th>Criteria</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($actividades as $a) { ?>
                                            <tr>
                                                <td class="tw-font-bold tw-text-gray-800"><?php echo html_escape($a['nombre']); ?></td>
                                                <td><span class="tw-px-3 tw-py-1 tw-bg-emerald-50 tw-text-emerald-700 tw-rounded-full tw-text-xs tw-font-bold">
                                                    +<?php echo (int) $a['puntos']; ?> PTS
                                                </span></td>
                                                <td>
                                                    <?php echo $a['auto_award'] ? 
                                                        '<span class="tw-text-blue-600 tw-font-medium"><i class="fa fa-refresh tw-mr-1"></i> Automated</span>' : 
                                                        '<span class="tw-text-gray-400">Manual</span>'; ?>
                                                </td>
                                                <td class="tw-text-gray-500 tw-text-sm">
                                                    <?php if ($a['auto_award']) {
                                                        echo "<b>" . html_escape(ucfirst($a['trigger_type'])) . "</b> at " . (int)$a['threshold'] . " target.";
                                                    } else { echo "Admin discretion"; } ?>
                                                </td>
                                                <td class="text-right">
                                                    <button onclick='edit_actividad(<?php echo json_encode($a); ?>)' class="btn btn-default btn-xs tw-mr-2">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <a href="<?php echo admin_url('eventos_actividades/delete_activity/' . $a['id']); ?>" class="btn btn-danger btn-xs _delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- MANUAL AWARD TAB -->
                        <div role="tabpanel" class="tab-pane" id="tab_manual">
                            <div class="manual-award-box">
                                <h4 class="tw-font-black tw-text-center tw-mb-8 tw-text-gray-800">Assign Points Manually</h4>
                                <?php 
                                $csrf_name = $this->security->get_csrf_token_name();
                                $csrf_hash = $this->security->get_csrf_hash();
                                ?>
                                <form action="<?php echo admin_url('eventos_actividades/manual_points'); ?>" method="post">
                                    <input type="hidden" name="<?php echo $csrf_name; ?>" value="<?php echo $csrf_hash; ?>">
                                    
                                    <div class="form-group tw-mb-6">
                                        <label class="control-label tw-mb-2">Staff Member / User</label>
                                        <select name="staff_id" class="selectpicker" data-width="100%" data-live-search="true" required>
                                            <option value=""></option>
                                            <?php foreach ($staff as $m) { ?>
                                                <option value="<?php echo (int) $m['staffid']; ?>" data-subtext="<?php echo html_escape($m['email']); ?>">
                                                    <?php echo html_escape($m['firstname'] . ' ' . $m['lastname']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group tw-mb-10">
                                        <label class="control-label tw-mb-2">Activity Reference</label>
                                        <select name="actividad_id" class="selectpicker" data-width="100%" data-live-search="true" required>
                                            <option value=""></option>
                                            <?php foreach ($actividades as $a) { ?>
                                                <option value="<?php echo (int) $a['id']; ?>">
                                                    <?php echo html_escape($a['nombre']); ?> (+<?php echo (int) $a['puntos']; ?> pts)
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-gold btn-block tw-py-4 tw-text-lg">
                                        Award Recognition <i class="fa fa-check tw-ml-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- RANKING TAB -->
                        <div role="tabpanel" class="tab-pane" id="tab_ranking">
                            <div class="card-tequila col-md-8 col-md-offset-2">
                                <div class="card-header-tequila">
                                    <h4>Leaderboard</h4>
                                    <i class="fa fa-trophy tw-text-yellow-500"></i>
                                </div>
                                <table class="table table-tequila">
                                    <thead>
                                        <tr>
                                            <th>Position</th>
                                            <th>User</th>
                                            <th class="text-right">Total Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        usort($totales, function($a, $b) { return $b['total_puntos'] - $a['total_puntos']; });
                                        foreach ($totales as $index => $t) { 
                                        ?>
                                            <tr>
                                                <td class="tw-font-bold tw-text-gray-400">#<?php echo $index + 1; ?></td>
                                                <td>
                                                    <div class="tw-flex tw-items-center">
                                                        <?php echo staff_profile_image($t['staff_id'], ['staff-profile-image-small tw-mr-3 tw-rounded-lg']); ?>
                                                        <span class="tw-font-bold"><?php echo html_escape($t['firstname'] . ' ' . $t['lastname']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-right">
                                                    <span class="tw-text-xl tw-font-black tw-text-gray-900"><?php echo (int) $t['total_puntos']; ?></span>
                                                    <small class="tw-text-gray-400 tw-ml-1">PTS</small>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div> <!-- end tab content -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->

<!-- ACTIVITY MODAL -->
<div class="modal fade" id="actividad_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?php echo admin_url('eventos_actividades/activity'); ?>" method="post" id="actividad_form">
            <div class="modal-content tw-rounded-xl">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title tw-font-bold">Configure Activity</h4>
                </div>
                <div class="modal-body tw-p-8">
                    <input type="hidden" name="id" id="act_id">
                    <div class="form-group tw-mb-6">
                        <label class="control-label">Activity Name</label>
                        <input type="text" name="nombre" id="act_nombre" class="form-control" required placeholder="e.g. Visit Tasting Event">
                    </div>
                    <div class="form-group tw-mb-6">
                        <label class="control-label">Point Reward</label>
                        <input type="number" name="puntos" id="act_puntos" class="form-control" required min="0">
                    </div>
                    <hr>
                    <div class="tw-bg-neutral-50 tw-p-6 tw-rounded-lg tw-border tw-border-neutral-100">
                        <div class="checkbox checkbox-primary tw-m-0">
                            <input type="checkbox" name="auto_award" id="m_auto_award">
                            <label for="m_auto_award"><strong>Automate using Scan Logic</strong></label>
                        </div>
                        <div id="auto_fields" class="tw-hidden tw-mt-6">
                            <div class="form-group">
                                <label class="control-label">Trigger Event</label>
                                <select name="trigger_type" id="act_trigger" class="form-control">
                                    <option value="scans">Product Scans</option>
                                    <option value="cava">Cava Additions</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Achievement Goal (Count)</label>
                                <input type="number" name="threshold" id="act_threshold" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer tw-bg-gray-50 tw-p-6">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-gold">Save Strategy</button>
                </div>
            </div>
        </form>
    </div>
</div>



<?php init_tail(); ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" crossorigin=""></script>

<script>
    function new_actividad() {
        $('#act_id').val('');
        $('#act_nombre').val('');
        $('#act_puntos').val('');
        $('#m_auto_award').prop('checked', false).change();
        $('#act_trigger').val('scans');
        $('#act_threshold').val('');
        $('#actividad_modal').modal('show');
    }

    function edit_actividad(data) {
        $('#act_id').val(data.id);
        $('#act_nombre').val(data.nombre);
        $('#act_puntos').val(data.puntos);
        if(data.auto_award == 1) {
            $('#m_auto_award').prop('checked', true).change();
        } else {
            $('#m_auto_award').prop('checked', false).change();
        }
        $('#act_trigger').val(data.trigger_type);
        $('#act_threshold').val(data.threshold);
        $('#actividad_modal').modal('show');
    }



    document.addEventListener('DOMContentLoaded', function () {
        appValidateForm($('#actividad_form'), {
            nombre: 'required',
            puntos: {
                required: true,
                number: true
            }
        });
        


        $('#m_auto_award').on('change', function() {
            if($(this).is(':checked')) {
                $('#auto_fields').removeClass('tw-hidden');
            } else {
                $('#auto_fields').addClass('tw-hidden');
            }
        });

        // Map initialization
        var eventos = <?php echo json_encode($eventos); ?> || [];
        var map = L.map('eventos_mapa', {zoomControl: false}).setView([23.6345, -102.5528], 5);
        L.control.zoom({position: 'bottomright'}).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        if (eventos.length > 0) {
            var bounds = [];
            eventos.forEach(function (e) {
                if (e.lat && e.lng) {
                    var lat = parseFloat(e.lat);
                    var lng = parseFloat(e.lng);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        var marker = L.marker([lat, lng]).addTo(map);
                        marker.bindPopup('<div style="padding:10px;"><b style="font-size:14px;">' + e.nombre + '</b><hr style="margin:5px 0;"><small>' + e.ciudad + ', ' + e.pais + '</small><br><small>' + e.fecha + '</small></div>');
                        bounds.push([lat, lng]);
                    }
                }
            });
            if (bounds.length > 0) map.fitBounds(bounds, {padding: [50, 50]});
        }
    });
</script>
