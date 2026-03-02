<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <h4><?php echo _l('escaneo_bebidas_title'); ?></h4>
          </div>
          <div class="panel-body">
            <a href="<?php echo admin_url('escaneo/bebida_form'); ?>" class="btn btn-primary mbot15">
              <?php echo _l('escaneo_bebidas_new'); ?>
            </a>

            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th><?php echo _l('escaneo_imagen'); ?></th>
                    <th><?php echo _l('escaneo_codigo_barras'); ?></th>
                    <th><?php echo _l('escaneo_marca'); ?></th>
                    <th><?php echo _l('escaneo_nombre'); ?></th>
                    <th><?php echo _l('escaneo_presentacion'); ?></th>
                    <th><?php echo _l('escaneo_grados_alcohol'); ?></th>
                    <th><?php echo _l('escaneo_precio'); ?></th>
                    <th><?php echo _l('escaneo_actions'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($bebidas as $b) { ?>
                    <tr>
                      <td style="width:110px;">
                        <?php if (!empty($b['imagen'])) { 
                          $url = base_url('uploads/escaneo/bebidas/' . $b['imagen']);
                        ?>
                          <img src="<?php echo $url; ?>" style="max-width:90px;max-height:90px;border-radius:6px;border:1px solid #ddd;" alt="img">
                        <?php } else { ?>
                          <span class="text-muted"><?php echo _l('escaneo_sin_imagen'); ?></span>
                        <?php } ?>
                      </td>
                      <td>
                        <div><strong><?php echo htmlspecialchars($b['codigo_barras']); ?></strong></div>
                        <?php if (!empty($b['barcode_svg'])) { ?>
                          <div style="max-width:260px;overflow:auto;">
                            <?php echo $b['barcode_svg']; ?>
                          </div>
                        <?php } ?>
                      </td>
                      <td><?php echo htmlspecialchars($b['marca']); ?></td>
                      <td><?php echo htmlspecialchars($b['nombre_tequila']); ?></td>
                      <td><?php echo htmlspecialchars($b['presentacion']); ?></td>
                      <td><?php echo htmlspecialchars($b['grados_alcohol']); ?></td>
                      <td><?php echo htmlspecialchars($b['precio']); ?></td>
                      <td>
                        <a class="btn btn-default btn-sm" href="<?php echo admin_url('escaneo/bebida_form/' . $b['id']); ?>">
                          <?php echo _l('escaneo_edit'); ?>
                        </a>
                        <a class="btn btn-danger btn-sm" href="<?php echo admin_url('escaneo/delete_bebida/' . $b['id']); ?>" onclick="return confirm('<?php echo _l('escaneo_confirm_delete'); ?>');">
                          <?php echo _l('escaneo_delete'); ?>
                        </a>
                      </td>
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
