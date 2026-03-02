<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">

      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Catálogo Maestro - Agregar Vino (Admin)</h4>
          </div>
          <div class="panel-body">
            <?php echo form_open_multipart(admin_url('cava/master_add')); ?>

              <div class="form-group">
                <label for="name">Nombre del vino</label>
                <input type="text" class="form-control" name="name" id="name" required>
              </div>

              <div class="form-group">
                <label for="image">Imagen (PNG)</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/png" required>
                <span class="text-muted">Solo PNG. Máx 4MB.</span>
              </div>

              <button type="submit" class="btn btn-primary">Guardar en catálogo</button>
              <a href="<?php echo admin_url('cava'); ?>" class="btn btn-default">Volver a Mi Cava</a>

            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Vinos en catálogo maestro</h4>
          </div>
          <div class="panel-body">
            <?php if (!isset($wines) || count($wines) == 0) { ?>
              <p class="text-muted">No hay vinos aún.</p>
            <?php } else { ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Imagen</th>
                      <th>Nombre</th>
                      <th class="text-right">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($wines as $i => $wine) : ?>
                      <?php $img = base_url(CAVA_UPLOADS_REL . $wine->image); ?>
                      <tr>
                        <td><?php echo (int)($i + 1); ?></td>
                        <td style="width:90px;">
                          <img src="<?php echo html_escape($img); ?>" style="max-width:80px; max-height:50px; border-radius:4px;">
                        </td>
                        <td><?php echo html_escape($wine->name); ?></td>
                        <td class="text-right">
                          <a class="btn btn-xs btn-danger _delete" href="<?php echo admin_url('cava/master_delete/' . $wine->id); ?>">
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Todas las cavas (selecciones de usuarios)</h4>
          </div>
          <div class="panel-body">
            <?php if (!isset($selections) || count($selections) == 0) { ?>
              <p class="text-muted">No hay selecciones aún.</p>
            <?php } else { ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Usuario</th>
                      <th>Email</th>
                      <th>Vino</th>
                      <th>Imagen</th>
                      <th>Fecha</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($selections as $sel) : ?>
                      <?php $img = base_url(CAVA_UPLOADS_REL . $sel->wine_image); ?>
                      <tr>
                        <td><?php echo html_escape(trim($sel->firstname . ' ' . $sel->lastname)); ?></td>
                        <td><?php echo html_escape($sel->email); ?></td>
                        <td><?php echo html_escape($sel->wine_name); ?></td>
                        <td style="width:90px;">
                          <img src="<?php echo html_escape($img); ?>" style="max-width:80px; max-height:50px; border-radius:4px;">
                        </td>
                        <td><?php echo html_escape($sel->date_added); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<?php init_tail(); ?>
