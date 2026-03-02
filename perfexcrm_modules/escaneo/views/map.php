<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <h4><?php echo _l('escaneo_mapa_global'); ?></h4>
          </div>
          <div class="panel-body">

            <?php echo form_open(admin_url('escaneo/store'), ['id' => 'escaneo-form', 'class' => 'm-b-20']); ?>
              <div class="row">
                <div class="col-md-6">
                  <label><?php echo _l('escaneo_bebida'); ?></label>
                  <select name="bebida_id" id="bebida_id" class="selectpicker" data-live-search="true" data-width="100%">
                    <option value=""><?php echo _l('escaneo_bebida_manual'); ?></option>
                    <?php foreach ($bebidas as $b) { ?>
                      <option value="<?php echo $b['id']; ?>">
                        <?php echo htmlspecialchars($b['marca'] . ' - ' . $b['nombre_tequila'] . ' (' . $b['codigo_barras'] . ')'); ?>
                      </option>
                    <?php } ?>
                  </select>
                  <small class="text-muted"><?php echo _l('escaneo_bebida_hint'); ?></small>
                </div>
              </div>

              <hr/>

              <div class="row">
                <div class="col-md-4">
                  <label for="codigo_barras"><?php echo _l('escaneo_codigo_barras'); ?></label>
                  <input type="text" name="codigo_barras" id="codigo_barras" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="marca"><?php echo _l('escaneo_marca'); ?></label>
                  <input type="text" name="marca" id="marca" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="nombre_tequila"><?php echo _l('escaneo_nombre'); ?></label>
                  <input type="text" name="nombre_tequila" id="nombre_tequila" class="form-control" required>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-4">
                  <label for="presentacion"><?php echo _l('escaneo_presentacion'); ?></label>
                  <input type="text" name="presentacion" id="presentacion" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="grados_alcohol"><?php echo _l('escaneo_grados_alcohol'); ?></label>
                  <input type="number" step="0.01" name="grados_alcohol" id="grados_alcohol" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="precio"><?php echo _l('escaneo_precio'); ?></label>
                  <input type="number" step="0.01" name="precio" id="precio" class="form-control" required>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-6">
                  <label for="lat"><?php echo _l('escaneo_lat'); ?></label>
                  <input type="text" name="lat" id="lat" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label for="lng"><?php echo _l('escaneo_lng'); ?></label>
                  <input type="text" name="lng" id="lng" class="form-control" required>
                </div>
              </div>

              <div class="row mtop15">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary"><?php echo _l('escaneo_guardar'); ?></button>
                  <small class="text-muted m-l-10"><?php echo _l('escaneo_geo_hint'); ?></small>
                </div>
              </div>
            <?php echo form_close(); ?>

            <hr />
            <div id="escaneo-map" style="width:100%;height:500px;"></div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (pos) {
      var lat = document.getElementById('lat');
      var lng = document.getElementById('lng');
      if (lat && lng && !lat.value && !lng.value) {
        lat.value = pos.coords.latitude.toFixed(7);
        lng.value = pos.coords.longitude.toFixed(7);
      }
    });
  }

  var bebidaSelect = document.getElementById('bebida_id');
  if (bebidaSelect) {
    bebidaSelect.addEventListener('change', function() {
      var id = this.value;
      if (!id) return;
      fetch('<?php echo admin_url('escaneo/bebida_json'); ?>/' + id)
        .then(r => r.json())
        .then(b => {
          if (!b) return;
          document.getElementById('codigo_barras').value  = b.codigo_barras || '';
          document.getElementById('marca').value          = b.marca || '';
          document.getElementById('nombre_tequila').value = b.nombre_tequila || '';
          document.getElementById('presentacion').value   = b.presentacion || '';
          document.getElementById('grados_alcohol').value = b.grados_alcohol || '';
          document.getElementById('precio').value         = b.precio || '';
        })
        .catch(()=>{});
    });
  }

  var map = L.map('escaneo-map').setView([20, 0], 2);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  fetch('<?php echo admin_url('escaneo/scans_json'); ?>')
    .then(r => r.json())
    .then(data => {
      data.forEach(scan => {
        if (scan.lat && scan.lng) {
          var m = L.marker([scan.lat, scan.lng]).addTo(map);
          m.bindPopup(
            '<strong>' + (scan.marca || '') + ' - ' + (scan.nombre_tequila || '') + '</strong><br>' +
            '<?php echo _l('escaneo_codigo_barras'); ?>: ' + (scan.codigo_barras || '') + '<br>' +
            '<?php echo _l('escaneo_presentacion'); ?>: ' + (scan.presentacion || '') + '<br>' +
            '<?php echo _l('escaneo_grados_alcohol'); ?>: ' + (scan.grados_alcohol || '') + '°<br>' +
            '<?php echo _l('escaneo_precio'); ?>: $' + (scan.precio || '') + '<br>' +
            '<?php echo _l('escaneo_fecha_escaneo'); ?>: ' + (scan.fecha_escaneo || '')
          );
        }
      });
    })
    .catch(()=>{});
});
</script>

<?php init_tail(); ?>
