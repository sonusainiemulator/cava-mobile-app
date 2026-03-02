<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin">Tequila</h4>
            <hr />
            <?php echo form_open_multipart(admin_url('tequila'), ['id' => 'tequila-form']); ?>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="type">Tipo de contenido</label>
                    <select name="type" id="type" class="selectpicker" data-width="100%">
                      <option value="image">Imagen</option>
                      <option value="text">Texto</option>
                      <option value="video">Video</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="title">Título</label>
                    <input type="text" name="title" id="title" class="form-control" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group file-wrapper">
                    <label for="file">Archivo (imagen / video)</label>
                    <input type="file" name="file" id="file" class="form-control" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="content">Texto</label>
                    <textarea name="content" id="content" rows="3" class="form-control" placeholder="Texto o descripción opcional"></textarea>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row mtop30">
      <div class="col-md-4">
        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Imágenes</h4>
          </div>
          <div class="panel-body tequila-list tequila-images">
            <?php if (count($images) == 0) { ?>
              <p class="text-muted">No hay imágenes todavía.</p>
            <?php } else { ?>
              <?php foreach ($images as $item) { ?>
                <div class="tequila-item mtop15">
                  <?php if ($item['title']) { ?>
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong><br />
                  <?php } ?>
                  <?php if ($item['file_path']) { ?>
                    <img src="<?php echo base_url($item['file_path']); ?>" class="img-responsive" style="max-height:200px; margin-bottom:5px;" />
                  <?php } ?>
                  <?php if ($item['content']) { ?>
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                  <?php } ?>
                </div>
                <hr />
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Textos</h4>
          </div>
          <div class="panel-body tequila-list tequila-texts">
            <?php if (count($texts) == 0) { ?>
              <p class="text-muted">No hay textos todavía.</p>
            <?php } else { ?>
              <?php foreach ($texts as $item) { ?>
                <div class="tequila-item mtop15">
                  <?php if ($item['title']) { ?>
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong><br />
                  <?php } ?>
                  <?php if ($item['content']) { ?>
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                  <?php } ?>
                </div>
                <hr />
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="panel_s">
          <div class="panel-heading">
            <h4 class="panel-title">Videos</h4>
          </div>
          <div class="panel-body tequila-list tequila-videos">
            <?php if (count($videos) == 0) { ?>
              <p class="text-muted">No hay videos todavía.</p>
            <?php } else { ?>
              <?php foreach ($videos as $item) { ?>
                <div class="tequila-item mtop15">
                  <?php if ($item['title']) { ?>
                    <strong><?php echo htmlspecialchars($item['title']); ?></strong><br />
                  <?php } ?>
                  <?php if ($item['file_path']) { ?>
                    <video src="<?php echo base_url($item['file_path']); ?>" controls style="width:100%; max-height:240px;"></video>
                  <?php } ?>
                  <?php if ($item['content']) { ?>
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                  <?php } ?>
                </div>
                <hr />
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script>
  (function(){
    // JS adicional si lo necesitas más adelante
  })();
</script>
