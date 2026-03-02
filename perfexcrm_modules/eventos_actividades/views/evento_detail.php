<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('eventos_actividades/event'), array('id' => 'evento_form')); ?>
                            <input type="hidden" name="id" value="<?php echo isset($evento) ? $evento->id : ''; ?>">

                            <?php echo render_input('nombre', 'Event Name', isset($evento) ? $evento->nombre : '', 'text', ['required' => 'true']); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo render_date_input('fecha', 'Date', isset($evento) ? $evento->fecha : ''); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('hora', 'Time', isset($evento) ? $evento->hora : '', 'time'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo render_input('pais', 'Country', isset($evento) ? $evento->pais : ''); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('ciudad', 'City/State', isset($evento) ? $evento->ciudad : ''); ?>
                                </div>
                            </div>

                            <?php echo render_input('direccion', 'Address', isset($evento) ? $evento->direccion : ''); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo render_input('lat', 'Latitude', isset($evento) ? $evento->lat : ''); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('lng', 'Longitude', isset($evento) ? $evento->lng : ''); ?>
                                </div>
                            </div>

                            <div class="text-right">
                                <a href="<?php echo admin_url('eventos_actividades'); ?>" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-info"><?php echo isset($evento) ? 'Update Event' : 'Publish Event'; ?></button>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        appValidateForm($('#evento_form'), {
            nombre: 'required'
        });
    });
</script>
