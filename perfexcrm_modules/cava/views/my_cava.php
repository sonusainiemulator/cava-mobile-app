<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Mi Cava</h4>
          </div>
          <div class="panel-body">
            <div class="clearfix">
              <p class="text-muted pull-left">Selecciona vinos del catálogo maestro para agregarlos a tu cava personal.</p>
              <?php if (is_admin()) { ?>
                <a class="btn btn-default pull-right" href="<?php echo admin_url('cava/master'); ?>">
                  <i class="fa fa-cog"></i> Ir a Catálogo Maestro
                </a>
              <?php } ?>
            </div>

            <div class="clearfix"></div>

            <?php if (!isset($wines) || count($wines) == 0) { ?>
              <p class="text-muted">Aún no hay vinos en el catálogo maestro. Pide al administrador que agregue vinos.</p>
            <?php } else { ?>

              <div class="row">
                <?php foreach ($wines as $wine) : ?>
                  <?php
                    $img = base_url(CAVA_UPLOADS_REL . $wine->image);
                    $isSelected = in_array((int)$wine->id, $selected_ids);
                  ?>
                  <div class="col-md-3">
                    <div class="panel_s" style="border:1px solid #eee;">
                      <div class="panel-body">
                        <div style="height:160px; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:6px; background:#fafafa;">
                          <img src="<?php echo html_escape($img); ?>" alt="<?php echo html_escape($wine->name); ?>" style="max-width:100%; max-height:160px;">
                        </div>
                        <h5 class="mtop15" style="margin-top:15px;"><?php echo html_escape($wine->name); ?></h5>

                        <?php if ($isSelected) { ?>
                          <a href="<?php echo admin_url('cava/remove_from_my/' . $wine->id); ?>" class="btn btn-danger btn-sm">
                            Quitar de mi cava
                          </a>
                        <?php } else { ?>
                          <a href="<?php echo admin_url('cava/add_to_my/' . $wine->id); ?>" class="btn btn-primary btn-sm">
                            Agregar a mi cava
                          </a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <hr>
              <h4>Mi colección</h4>
              <?php
                $selected = [];
                foreach ($wines as $w) {
                  if (in_array((int)$w->id, $selected_ids)) $selected[] = $w;
                }
              ?>
              <?php if (count($selected) == 0) { ?>
                <p class="text-muted">Aún no has agregado vinos.</p>
              <?php } else { ?>
                <ul>
                  <?php foreach ($selected as $w) : ?>
                    <li><?php echo html_escape($w->name); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php } ?>

            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
