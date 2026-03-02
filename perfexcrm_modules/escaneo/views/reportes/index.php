<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <h4><?php echo _l('escaneo_reportes_title'); ?></h4>
          </div>
          <div class="panel-body">

            <?php echo form_open(admin_url('escaneo/reportes'), ['method' => 'get']); ?>
              <div class="row">
                <div class="col-md-3">
                  <label><?php echo _l('escaneo_desde'); ?></label>
                  <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($from ?? ''); ?>" required>
                </div>
                <div class="col-md-3">
                  <label><?php echo _l('escaneo_hasta'); ?></label>
                  <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($to ?? ''); ?>" required>
                </div>
                <div class="col-md-3" style="margin-top:24px;">
                  <button type="submit" class="btn btn-primary"><?php echo _l('escaneo_generar'); ?></button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <hr/>

            <?php if (!empty($from) && !empty($to)) { ?>
              <div class="alert alert-info">
                <strong><?php echo _l('escaneo_total_general'); ?>:</strong> <?php echo (int)$total; ?>
              </div>

              <h4 class="mbot15"><?php echo _l('escaneo_total_por_usuario'); ?></h4>

              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th><?php echo _l('escaneo_usuario'); ?></th>
                      <th><?php echo _l('escaneo_total_escaneos'); ?></th>
                      <th><?php echo _l('escaneo_primer_escaneo'); ?></th>
                      <th><?php echo _l('escaneo_ultimo_escaneo'); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rows as $r) { ?>
                      <tr>
                        <td><?php echo htmlspecialchars(trim(($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? ''))); ?></td>
                        <td><strong><?php echo (int)$r['total_scans']; ?></strong></td>
                        <td><?php echo htmlspecialchars($r['first_scan']); ?></td>
                        <td><?php echo htmlspecialchars($r['last_scan']); ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <div class="alert alert-warning"><?php echo _l('escaneo_selecciona_rango'); ?></div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
