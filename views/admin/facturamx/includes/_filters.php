<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row mtop15">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <?php echo form_open(admin_url('facturamx/ver_facturas'), ['method' => 'get', 'id' => 'filtros-form']); ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search"><?php echo _l('facturamx_buscar'); ?></label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="<?php echo $this->input->get('search'); ?>" 
                                   placeholder="<?php echo _l('facturamx_buscar_placeholder'); ?>">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="estado"><?php echo _l('facturamx_estado'); ?></label>
                            <select name="estado" id="estado" class="form-control selectpicker" data-width="100%">
                                <option value=""><?php echo _l('facturamx_todos'); ?></option>
                                <option value="activa" <?php echo $this->input->get('estado') === 'activa' ? 'selected' : ''; ?>>
                                    <?php echo _l('facturamx_estado_activa'); ?>
                                </option>
                                <option value="cancelada" <?php echo $this->input->get('estado') === 'cancelada' ? 'selected' : ''; ?>>
                                    <?php echo _l('facturamx_estado_cancelada'); ?>
                                </option>
                                <option value="error" <?php echo $this->input->get('estado') === 'error' ? 'selected' : ''; ?>>
                                    <?php echo _l('facturamx_estado_error'); ?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="desde"><?php echo _l('facturamx_desde'); ?></label>
                            <div class="input-group date">
                                <input type="text" name="desde" id="desde" class="form-control datepicker" 
                                       value="<?php echo $this->input->get('desde'); ?>" 
                                       autocomplete="off">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="hasta"><?php echo _l('facturamx_hasta'); ?></label>
                            <div class="input-group date">
                                <input type="text" name="hasta" id="hasta" class="form-control datepicker" 
                                       value="<?php echo $this->input->get('hasta'); ?>" 
                                       autocomplete="off">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cliente"><?php echo _l('facturamx_cliente'); ?></label>
                            <select name="cliente" id="cliente" class="form-control selectpicker" 
                                    data-live-search="true" data-width="100%">
                                <option value=""><?php echo _l('facturamx_todos'); ?></option>
                                <?php foreach($clientes as $cliente) { ?>
                                    <option value="<?php echo $cliente['userid']; ?>" 
                                            <?php echo $this->input->get('cliente') == $cliente['userid'] ? 'selected' : ''; ?>>
                                        <?php echo $cliente['company']; ?> 
                                        (<?php echo get_custom_field_value($cliente['userid'], 'rfc', 'customers'); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-search"></i> <?php echo _l('facturamx_buscar'); ?>
                            </button>
                            <a href="<?php echo admin_url('facturamx/ver_facturas'); ?>" class="btn btn-default">
                                <?php echo _l('facturamx_limpiar'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    // Inicializar selectpicker
    $('.selectpicker').selectpicker();

    // Inicializar datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        language: 'es'
    });
});
</script>