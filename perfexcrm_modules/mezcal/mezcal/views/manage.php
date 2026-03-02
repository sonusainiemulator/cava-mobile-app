<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4><i class="fa fa-beer" aria-hidden="true"></i> Mezcal</h4>
                    </div>
                    <div class="panel-body">

                        <!-- Formulario para nuevo contenido sobre Mezcal -->
                        <?php echo form_open_multipart(admin_url('mezcal/mezcal'), array('id' => 'mezcal-form')); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('title', 'mezcal_title'); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type" class="control-label">
                                        <?php echo _l('mezcal_type'); ?>
                                    </label>
                                    <select name="type" id="type" class="selectpicker" data-width="100%">
                                        <option value="image"><?php echo _l('mezcal_option_image'); ?></option>
                                        <option value="text"><?php echo _l('mezcal_option_text'); ?></option>
                                        <option value="video"><?php echo _l('mezcal_option_video'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="file-wrapper">
                                <div class="form-group">
                                    <label for="file" class="control-label">
                                        <?php echo _l('mezcal_file'); ?>
                                    </label>
                                    <input type="file" name="file" class="form-control" id="file">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="control-label">
                                    <?php echo _l('mezcal_description'); ?>
                                </label>
                                <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                                <span class="help-block">
                                    <?php echo _l('mezcal_description_help'); ?>
                                </span>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo _l('mezcal_save'); ?>
                                </button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

                        <hr/>

                        <!-- Contenidos de Mezcal separados por tipo -->
                        <div class="row">
                            <!-- Imágenes de Mezcal -->
                            <div class="col-md-4">
                                <h4 class="text-center">
                                    <i class="fa fa-picture-o" aria-hidden="true"></i>
                                    <?php echo _l('mezcal_images'); ?>
                                </h4>
                                <div class="mezcal-section">
                                    <?php if (count($images) == 0) { ?>
                                        <p class="text-muted text-center"><?php echo _l('mezcal_no_elements'); ?></p>
                                    <?php } else { ?>
                                        <?php foreach ($images as $item) { ?>
                                            <div class="panel panel-default mezcal-item">
                                                <div class="panel-heading">
                                                    <strong><?php echo html_escape($item->title); ?></strong>
                                                    <a href="<?php echo admin_url('mezcal/mezcal/delete/' . $item->id); ?>"
                                                       class="pull-right text-danger _delete" title="<?php echo _l('mezcal_delete'); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                                <div class="panel-body text-center">
                                                    <?php if (!empty($item->file)) { ?>
                                                        <img src="<?php echo base_url($item->file); ?>"
                                                             class="img-responsive img-thumbnail" style="margin:0 auto;"/>
                                                    <?php } ?>
                                                    <?php if (!empty($item->description)) { ?>
                                                        <p class="mtop10">
                                                            <?php echo nl2br(html_escape($item->description)); ?>
                                                        </p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Textos sobre Mezcal -->
                            <div class="col-md-4">
                                <h4 class="text-center">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                    <?php echo _l('mezcal_texts'); ?>
                                </h4>
                                <div class="mezcal-section">
                                    <?php if (count($texts) == 0) { ?>
                                        <p class="text-muted text-center"><?php echo _l('mezcal_no_elements'); ?></p>
                                    <?php } else { ?>
                                        <?php foreach ($texts as $item) { ?>
                                            <div class="panel panel-default mezcal-item">
                                                <div class="panel-heading">
                                                    <strong><?php echo html_escape($item->title); ?></strong>
                                                    <a href="<?php echo admin_url('mezcal/mezcal/delete/' . $item->id); ?>"
                                                       class="pull-right text-danger _delete" title="<?php echo _l('mezcal_delete'); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                                <div class="panel-body">
                                                    <?php if (!empty($item->description)) { ?>
                                                        <p><?php echo nl2br(html_escape($item->description)); ?></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Videos sobre Mezcal -->
                            <div class="col-md-4">
                                <h4 class="text-center">
                                    <i class="fa fa-video-camera" aria-hidden="true"></i>
                                    <?php echo _l('mezcal_videos'); ?>
                                </h4>
                                <div class="mezcal-section">
                                    <?php if (count($videos) == 0) { ?>
                                        <p class="text-muted text-center"><?php echo _l('mezcal_no_elements'); ?></p>
                                    <?php } else { ?>
                                        <?php foreach ($videos as $item) { ?>
                                            <div class="panel panel-default mezcal-item">
                                                <div class="panel-heading">
                                                    <strong><?php echo html_escape($item->title); ?></strong>
                                                    <a href="<?php echo admin_url('mezcal/mezcal/delete/' . $item->id); ?>"
                                                       class="pull-right text-danger _delete" title="<?php echo _l('mezcal_delete'); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                                <div class="panel-body text-center">
                                                    <?php if (!empty($item->file)) { ?>
                                                        <video controls style="width:100%;max-height:240px;">
                                                            <source src="<?php echo base_url($item->file); ?>">
                                                            Tu navegador no soporta la etiqueta de video.
                                                        </video>
                                                    <?php } ?>
                                                    <?php if (!empty($item->description)) { ?>
                                                        <p class="mtop10">
                                                            <?php echo nl2br(html_escape($item->description)); ?>
                                                        </p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div> <!-- /panel-body -->
                </div>
            </div>

        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    (function(){
        function toggleFileField() {
            var type = $('#type').val();
            if (type === 'text') {
                $('#file-wrapper').hide();
            } else {
                $('#file-wrapper').show();
            }
        }
        $(function(){
            toggleFileField();
            $('#type').on('change', toggleFileField);
        });
    })();
</script>

</body>
</html>
