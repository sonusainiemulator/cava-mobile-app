<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">
              <?php echo isset($edit_mode) && $edit_mode ? _l('edit_marca') : _l('marcas'); ?>
            </h4>
            <hr />
            <div class="row">
              <div class="col-md-4">
                <?php
                  $action_url = isset($edit_mode) && $edit_mode && isset($marca['id'])
                    ? admin_url('marcas/update/' . $marca['id'])
                    : admin_url('marcas/create');
                ?>
                <?php echo form_open_multipart($action_url); ?>

                  <?php if (isset($fields) && is_array($fields)): ?>
                    <?php foreach ($fields as $field): ?>
                      <?php if ($field === 'id') { continue; } ?>
                      <div class="form-group">
                        <label for="<?php echo $field; ?>">
                          <?php echo ucfirst(str_replace('_', ' ', $field)); ?>
                        </label>

                        <?php
                          $value = '';
                          if (isset($edit_mode) && $edit_mode && isset($marca[$field])) {
                              $value = $marca[$field];
                          }
                        ?>

                        <?php if (in_array($field, $image_fields)): ?>
                          <?php if (!empty($value)): ?>
                            <div style="margin-bottom:5px;">
                              <img src="<?php echo base_url('uploads/marcas/' . $value); ?>"
                                   alt="<?php echo _l('marca_current_image'); ?>"
                                   style="width:50px;height:auto;border-radius:4px;">
                            </div>
                          <?php endif; ?>
                          <input type="file"
                                 class="form-control"
                                 name="<?php echo $field; ?>"
                                 id="<?php echo $field; ?>"
                                 accept="image/*">
                        <?php else: ?>
                          <input type="text"
                                 class="form-control"
                                 name="<?php echo $field; ?>"
                                 id="<?php echo $field; ?>"
                                 value="<?php echo html_escape($value); ?>">
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>

                  <button type="submit" class="btn btn-primary">
                    <?php echo isset($edit_mode) && $edit_mode ? _l('marca_update') : _l('marca_save'); ?>
                  </button>
                  <?php if (isset($edit_mode) && $edit_mode): ?>
                    <a href="<?php echo admin_url('marcas'); ?>" class="btn btn-default">
                      <?php echo _l('marca_cancel'); ?>
                    </a>
                  <?php endif; ?>
                <?php echo form_close(); ?>
              </div>
              <div class="col-md-8">
                <table class="table dt-table">
                  <thead>
                    <tr>
                      <?php if (isset($fields) && is_array($fields)): ?>
                        <?php foreach ($fields as $field): ?>
                          <th><?php echo ucfirst(str_replace('_', ' ', $field)); ?></th>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      <th><?php echo _l('marca_options'); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (isset($marcas) && is_array($marcas)): ?>
                      <?php foreach ($marcas as $m): ?>
                        <tr>
                          <?php foreach ($fields as $field): ?>
                            <td>
                              <?php if (in_array($field, $image_fields) && !empty($m[$field])): ?>
                                <img src="<?php echo base_url('uploads/marcas/' . $m[$field]); ?>"
                                     alt="<?php echo _l('marca_image_alt'); ?>"
                                     style="width:50px;height:auto;border-radius:4px;">
                              <?php else: ?>
                                <?php echo isset($m[$field]) ? html_escape($m[$field]) : ''; ?>
                              <?php endif; ?>
                            </td>
                          <?php endforeach; ?>
                          <td>
                            <?php if (isset($m['id'])): ?>
                              <a href="<?php echo admin_url('marcas/edit/' . $m['id']); ?>"
                                 class="btn btn-default btn-icon">
                                <i class="fa fa-pencil"></i>
                              </a>
                              <a href="<?php echo admin_url('marcas/delete/' . $m['id']); ?>"
                                 class="btn btn-danger btn-icon _delete">
                                <i class="fa fa-trash"></i>
                              </a>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
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
</div>
<?php init_tail(); ?>
</body>
</html>
