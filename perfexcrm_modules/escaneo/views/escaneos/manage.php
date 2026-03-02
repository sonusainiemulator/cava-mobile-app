<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <h4><?php echo _l('escaneo_scans_title'); ?></h4>
          </div>
          <div class="panel-body">
            <a href="<?php echo admin_url('escaneo/reportes'); ?>" class="btn btn-default mbot15">
              <?php echo _l('escaneo_ver_reportes'); ?>
            </a>
            <a href="<?php echo admin_url('escaneo/mapa'); ?>" class="btn btn-info mbot15">
              <?php echo _l('escaneo_ir_mapa'); ?>
            </a>

            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th><?php echo _l('escaneo_fecha_escaneo'); ?></th>
                    <th><?php echo _l('escaneo_usuario'); ?></th>
                    <th><?php echo _l('escaneo_codigo_barras'); ?></th>
                    <th><?php echo _l('escaneo_marca'); ?></th>
                    <th><?php echo _l('escaneo_nombre'); ?></th>
                    <th><?php echo _l('escaneo_presentacion'); ?></th>
                    <th><?php echo _l('escaneo_grados_alcohol'); ?></th>
                    <th><?php echo _l('escaneo_precio'); ?></th>
                    <th><?php echo _l('escaneo_lat'); ?></th>
                    <th><?php echo _l('escaneo_lng'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($scans as $s) { ?>
                    <tr>
                      <td><?php echo htmlspecialchars($s['fecha_escaneo']); ?></td>
                      <td><?php echo htmlspecialchars(trim(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? ''))); ?></td>
                      <td><?php echo htmlspecialchars($s['codigo_barras']); ?></td>
                      <td><?php echo htmlspecialchars($s['marca']); ?></td>
                      <td><?php echo htmlspecialchars($s['nombre_tequila']); ?></td>
                      <td><?php echo htmlspecialchars($s['presentacion']); ?></td>
                      <td><?php echo htmlspecialchars($s['grados_alcohol']); ?></td>
                      <td><?php echo htmlspecialchars($s['precio']); ?></td>
                      <td><?php echo htmlspecialchars($s['lat']); ?></td>
                      <td><?php echo htmlspecialchars($s['lng']); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
