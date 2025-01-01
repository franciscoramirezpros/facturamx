<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo _l('facturamx_crear_cfdi'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open(admin_url('facturamx/crear'), ['id' => 'form-crear-cfdi']); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Cliente -->
                                <div class="form-group">
                                    <label for="cliente_id"><?php echo _l('facturamx_cliente'); ?></label>
                                    <select name="cliente_id" id="cliente_id" class="form-control selectpicker" 
                                            data-live-search="true" required>
                                        <option value=""><?php echo _l('facturamx_seleccionar_cliente'); ?></option>
                                        <?php foreach($clientes as $cliente) { ?>
                                            <option value="<?php echo $cliente['userid']; ?>">
                                                <?php echo $cliente['company']; ?> 
                                                (<?php echo get_custom_field_value($cliente['userid'], 'rfc', 'customers'); ?>)
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Serie y Folio -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="serie"><?php echo _l('facturamx_serie'); ?></label>
                                            <input type="text" name="serie" id="serie" class="form-control" 
                                                   value="<?php echo get_option('facturama_serie'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="folio"><?php echo _l('facturamx_folio'); ?></label>
                                            <input type="text" name="folio" id="folio" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Forma y Método de Pago -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="forma_pago"><?php echo _l('facturamx_forma_pago'); ?></label>
                                            <select name="forma_pago" id="forma_pago" class="form-control selectpicker" required>
                                                <?php foreach($formas_pago as $forma) { ?>
                                                    <option value="<?php echo $forma['id']; ?>">
                                                        <?php echo $forma['id'] . ' - ' . $forma['description']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metodo_pago"><?php echo _l('facturamx_metodo_pago'); ?></label>
                                            <select name="metodo_pago" id="metodo_pago" class="form-control selectpicker" required>
                                                <?php foreach($metodos_pago as $metodo) { ?>
                                                    <option value="<?php echo $metodo['id']; ?>">
                                                        <?php echo $metodo['id'] . ' - ' . $metodo['description']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Uso CFDI -->
                                <div class="form-group">
                                    <label for="uso_cfdi"><?php echo _l('facturamx_uso_cfdi'); ?></label>
                                    <select name="uso_cfdi" id="uso_cfdi" class="form-control selectpicker" required>
                                        <?php foreach($usos_cfdi as $uso) { ?>
                                            <option value="<?php echo $uso['id']; ?>">
                                                <?php echo $uso['id'] . ' - ' . $uso['description']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="hr-panel-heading" />

                        <!-- Conceptos -->
                        <div class="items">
                            <div class="table-responsive">
                                <table class="table items table-main-items-create">
                                    <thead>
                                        <tr>
                                            <th width="20%"><?php echo _l('facturamx_clave_producto'); ?></th>
                                            <th width="10%"><?php echo _l('facturamx_clave_unidad'); ?></th>
                                            <th width="25%"><?php echo _l('facturamx_descripcion'); ?></th>
                                            <th width="10%"><?php echo _l('facturamx_cantidad'); ?></th>
                                            <th width="15%"><?php echo _l('facturamx_valor_unitario'); ?></th>
                                            <th width="15%"><?php echo _l('facturamx_importe'); ?></th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="main">
                                            <td>
                                                <input type="text" name="items[0][clave_producto]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][clave_unidad]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="items[0][descripcion]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][cantidad]" class="form-control cantidad" 
                                                       min="0.000001" step="0.000001" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][valor_unitario]" class="form-control valor-unitario" 
                                                       min="0.000001" step="0.000001" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][importe]" class="form-control importe" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-icon remove-item">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-info add-item mtop15">
                                <i class="fa fa-plus"></i> <?php echo _l('facturamx_agregar_concepto'); ?>
                            </button>
                        </div>

                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-8 col-md-offset-4">
                                <table class="table text-right">
                                    <tbody>
                                        <tr>
                                            <td><span class="bold"><?php echo _l('facturamx_subtotal'); ?></span></td>
                                            <td>
                                                <input type="text" name="subtotal" class="form-control text-right" 
                                                       readonly value="0.00">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="bold"><?php echo _l('facturamx_iva'); ?> (16%)</span></td>
                                            <td>
                                                <input type="text" name="total_impuestos" class="form-control text-right" 
                                                       readonly value="0.00">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="bold"><?php echo _l('facturamx_total'); ?></span></td>
                                            <td>
                                                <input type="text" name="total" class="form-control text-right" 
                                                       readonly value="0.00">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mtop20">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary pull-right">
                                    <?php echo _l('facturamx_generar_cfdi'); ?>
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
    // Inicializar selectpicker
    $('.selectpicker').selectpicker();

    // Calcular importes
    function calcularImportes() {
        var subtotal = 0;
        $('.items tbody tr').each(function() {
            var cantidad = parseFloat($(this).find('.cantidad').val()) || 0;
            var valorUnitario = parseFloat($(this).find('.valor-unitario').val()) || 0;
            var importe = cantidad * valorUnitario;
            
            $(this).find('.importe').val(importe.toFixed(2));
            subtotal += importe;
        });

        var iva = subtotal * 0.16;
        var total = subtotal + iva;

        $('input[name="subtotal"]').val(subtotal.toFixed(2));
        $('input[name="total_impuestos"]').val(iva.toFixed(2));
        $('input[name="total"]').val(total.toFixed(2));
    }

    // Eventos para cálculos
    $(document).on('input', '.cantidad, .valor-unitario', calcularImportes);

    // Agregar concepto
    $('.add-item').on('click', function() {
        var $lastRow = $('.items tbody tr:last');
        var $newRow = $lastRow.clone();
        var index = $('.items tbody tr').length;

        $newRow.find('input').each(function() {
            var name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                $(this).val('');
            }
        });

        $('.items tbody').append($newRow);
        calcularImportes();
    });

    // Eliminar concepto
    $(document).on('click', '.remove-item', function() {
        if ($('.items tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calcularImportes();
        }
    });

    // Validación del formulario
    $('#form-crear-cfdi').on('submit', function(e) {
        if ($('.items tbody tr').length === 0) {
            alert('<?php echo _l('facturamx_error_sin_conceptos'); ?>');
            e.preventDefault();
            return false;
        }
    });
});
</script>