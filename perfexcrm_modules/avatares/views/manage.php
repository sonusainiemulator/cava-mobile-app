<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6">
                <h4 class="no-margin"><?php echo _l('avatares_menu'); ?></h4>
              </div>
              <div class="col-md-6 text-right">
                <button class="btn btn-primary" data-toggle="collapse" data-target="#avatar-form">
                  <i class="fa fa-plus"></i> <?php echo _l('avatares_add'); ?>
                </button>
              </div>
            </div>

            <div id="avatar-form" class="collapse mtop20 <?php echo isset($avatar) ? 'in' : ''; ?>">
              <?php echo form_open_multipart(admin_url('avatares' . (isset($avatar) ? '/edit/' . $avatar->id : ''))); ?>

              <div class="row">
                <div class="col-md-4">
                  <?php echo render_input('name', 'avatares_name', isset($avatar) ? $avatar->name : ''); ?>
                </div>
                <div class="col-md-4">
                  <?php echo render_input('points_required', 'avatares_points_required', isset($avatar) ? $avatar->points_required : 0, 'number'); ?>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="image" class="control-label"><?php echo _l('avatares_image'); ?></label>
                    <input type="file" name="image" id="image" class="form-control" />
                    <?php if (isset($avatar) && !empty($avatar->image)) : ?>
                      <div class="mtop10">
                        <img src="<?php echo avatares_upload_url($avatar->image); ?>" style="max-width:120px;">
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="checkbox checkbox-primary">
                <input type="checkbox" name="active" id="active" <?php echo (!isset($avatar) || (isset($avatar) && (int) $avatar->active === 1)) ? 'checked' : ''; ?>>
                <label for="active"><?php echo _l('avatares_active'); ?></label>
              </div>

              <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>

              <?php echo form_close(); ?>
              <hr />
            </div>

            <div class="table-responsive mtop20">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo _l('avatares_name'); ?></th>
                    <th><?php echo _l('avatares_points_required'); ?></th>
                    <th><?php echo _l('avatares_image'); ?></th>
                    <th><?php echo _l('avatares_active'); ?></th>
                    <th><?php echo _l('options'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (isset($avatares) && count($avatares) > 0) : ?>
                    <?php foreach ($avatares as $a) : ?>
                      <tr>
                        <td><?php echo (int) $a->id; ?></td>
                        <td><?php echo html_escape($a->name); ?></td>
                        <td><?php echo (int) $a->points_required; ?></td>
                        <td>
                          <?php if (!empty($a->image)) : ?>
                            <img src="<?php echo avatares_upload_url($a->image); ?>" style="max-width:60px;">
                          <?php else : ?>
                            <span class="text-muted"><?php echo _l('avatares_no_image'); ?></span>
                          <?php endif; ?>
                        </td>
                        <td><?php echo $a->active ? _l('yes') : _l('no'); ?></td>
                        <td>
                          <a href="<?php echo admin_url('avatares/edit/' . $a->id); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                          <a href="<?php echo admin_url('avatares/delete/' . $a->id); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr>
                      <td colspan="6" class="text-center"><?php echo _l('no_results_found'); ?></td>
                    </tr>
                  <?php endif; ?>
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
</body>
</html>
