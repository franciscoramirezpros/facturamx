<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo _l('facturamx_configuracion'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('facturamx/guardar_configuracion')); ?>
                        
                        <!-- Credenciales API -->
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#credenciales">
                                            <?php echo _l('facturamx_credenciales_api'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="credenciales" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="api_key"><?php echo _l('facturamx_api_key'); ?></label>
                                                    <input type="text" id="api_key" name="api_key" class="form-control" 
                                                           value="<?php echo get_option('facturama_api_key'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="api_secret"><?php echo _l('facturamx_api_secret'); ?></label>
                                                    <input type="password" id="api_secret" name="api_secret" class="form-control" 
                                                           value="<?php echo get_option('facturama_api_secret'); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox" name="sandbox" id="sandbox" 
                                                           <?php echo get_option('facturama_sandbox') ? 'checked' : ''; ?>>
                                                    <label for="sandbox">
                                                        <?php echo _l('facturamx_modo_sandbox'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de Facturación -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#facturacion">
                                            <?php echo _l('facturamx_config_facturacion'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="facturacion" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="serie"><?php echo _l('facturamx_serie'); ?></label>
                                                    <input type="text" id="serie" name="serie" class="form-control" 
                                                           value="<?php echo get_option('facturama_serie'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="regimen_fiscal"><?php echo _l('facturamx_regimen_fiscal'); ?></label>
                                                    <select name="regimen_fiscal" id="regimen_fiscal" class="form-control selectpicker" required>
                                                        <?php foreach(get_regimenes_fiscales() as $id => $nombre) { ?>
                                                            <option value="<?php echo $id; ?>" 
                                                                    <?php echo get_option('facturama_regimen_fiscal') == $id ? 'selected' : ''; ?>>
                                                                <?php echo $id . ' - ' . $nombre; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lugar_expedicion"><?php echo _l('facturamx_lugar_expedicion'); ?></label>
                                                    <input type="text" id="lugar_expedicion" name="lugar_expedicion" class="form-control" 
                                                           value="<?php echo get_option('facturama_lugar_expedicion'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox" name="auto_invoice" id="auto_invoice" 
                                                           <?php echo get_option('facturama_auto_invoice') ? 'checked' : ''; ?>>
                                                    <label for="auto_invoice">
                                                        <?php echo _l('facturamx_auto_invoice'); ?>
                                                    </label>
                                                </div>
                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox" name="auto_cancel" id="auto_cancel" 
                                                           <?php echo get_option('facturama_auto_cancel') ? 'checked' : ''; ?>>
                                                    <label for="auto_cancel">
                                                        <?php echo _l('facturamx_auto_cancel'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de Correo -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#correo">
                                            <?php echo _l('facturamx_config_correo'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="correo" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox checkbox-primary">
                                                    <input type="checkbox" name="email_cfdi" id="email_cfdi" 
                                                           <?php echo get_option('facturama_email_cfdi') ? 'checked' : ''; ?>>
                                                    <label for="email_cfdi">
                                                        <?php echo _l('facturamx_enviar_email'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary pull-right">
                                    <?php echo _l('settings_save'); ?>
                                </button>
                            </div>
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
$(function() {
    $('.selectpicker').selectpicker();
    
    // Validación del formulario
    $('form').on('submit', function(e) {
        var apiKey = $('#api_key').val();
        var apiSecret = $('#api_secret').val();
        var lugarExpedicion = $('#lugar_expedicion').val();

        if (!apiKey || !apiSecret || !lugarExpedicion) {
            e.preventDefault();
            alert('<?php echo _l('facturamx_campos_requeridos'); ?>');
            return false;
        }
    });
});