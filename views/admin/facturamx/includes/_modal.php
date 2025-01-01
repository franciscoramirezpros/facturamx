<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Modal para enviar CFDI por email -->
<div class="modal fade" id="facturaEmailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('facturamx_enviar_email'); ?></h4>
            </div>
            <?php echo form_open(admin_url('facturamx/enviar_email'), ['id' => 'form-enviar-email']); ?>
            <div class="modal-body">
                <input type="hidden" name="uuid" id="uuid">
                <div class="form-group">
                    <label for="email"><?php echo _l('facturamx_email_destino'); ?></label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mensaje"><?php echo _l('facturamx_mensaje'); ?></label>
                    <textarea name="mensaje" id="mensaje" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php echo _l('facturamx_enviar'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de factura -->
<div class="modal fade" id="facturaDetallesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('facturamx_detalles_factura'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><?php echo _l('facturamx_datos_generales'); ?></h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_uuid'); ?>:</strong></td>
                                    <td class="uuid"></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_fecha'); ?>:</strong></td>
                                    <td class="fecha"></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_serie_folio'); ?>:</strong></td>
                                    <td class="serie-folio"></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_estado'); ?>:</strong></td>
                                    <td class="estado"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><?php echo _l('facturamx_datos_cliente'); ?></h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_rfc'); ?>:</strong></td>
                                    <td class="rfc-cliente"></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_nombre'); ?>:</strong></td>
                                    <td class="nombre-cliente"></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_uso_cfdi'); ?>:</strong></td>
                                    <td class="uso-cfdi"></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo _l('facturamx_regimen_fiscal'); ?>:</strong></td>
                                    <td class="regimen-fiscal"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-12">
                        <h5><?php echo _l('facturamx_conceptos'); ?></h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('facturamx_clave_producto'); ?></th>
                                        <th><?php echo _l('facturamx_descripcion'); ?></th>
                                        <th><?php echo _l('facturamx_cantidad'); ?></th>
                                        <th><?php echo _l('facturamx_valor_unitario'); ?></th>
                                        <th><?php echo _l('facturamx_importe'); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="conceptos">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong><?php echo _l('facturamx_subtotal'); ?></strong></td>
                                        <td class="subtotal"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong><?php echo _l('facturamx_iva'); ?></strong></td>
                                        <td class="total-impuestos"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong><?php echo _l('facturamx_total'); ?></strong></td>
                                        <td class="total"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para relacionar facturas -->
<div class="modal fade" id="facturaRelacionarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('facturamx_relacionar_facturas'); ?></h4>
            </div>
            <?php echo form_open(admin_url('facturamx/relacionar'), ['id' => 'form-relacionar']); ?>
            <div class="modal-body">
                <input type="hidden" name="uuid_origen" id="uuid_origen">
                <div class="form-group">
                    <label for="tipo_relacion"><?php echo _l('facturamx_tipo_relacion'); ?></label>
                    <select name="tipo_relacion" id="tipo_relacion" class="form-control selectpicker" required>
                        <?php foreach(get_tipos_relacion() as $id => $nombre) { ?>
                            <option value="<?php echo $id; ?>"><?php echo $id . ' - ' . $nombre; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="uuid_destino"><?php echo _l('facturamx_uuid_relacionar'); ?></label>
                    <input type="text" name="uuid_destino" id="uuid_destino" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php echo _l('facturamx_relacionar'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
$(function() {
    // Inicializar selectpicker en modales
    $('.selectpicker').selectpicker();

    // Validación del formulario de email
    $('#form-enviar-email').on('submit', function(e) {
        var email = $('#email').val();
        if (!email) {
            alert('<?php echo _l('facturamx_email_requerido'); ?>');
            e.preventDefault();
            return false;
        }
    });

    // Validación del formulario de relación
    $('#form-relacionar').on('submit', function(e) {
        var uuid = $('#uuid_destino').val();
        if (!uuid || uuid.length !== 36) {
            alert('<?php echo _l('facturamx_uuid_invalido'); ?>');
            e.preventDefault();
            return false;
        }
    });
});

// Función para cargar detalles de factura
function cargarDetallesFactura(uuid) {
    $.get(admin_url + 'facturamx/obtener_detalles/' + uuid, function(response) {
        var data = JSON.parse(response);
        
        // Llenar datos generales
        $('#facturaDetallesModal .uuid').text(data.uuid);
        $('#facturaDetallesModal .fecha').text(data.fecha);
        $('#facturaDetallesModal .serie-folio').text(data.serie + '-' + data.folio);
        $('#facturaDetallesModal .estado').html('<span class="label label-' + data.estado_clase + '">' + data.estado + '</span>');
        
        // Llenar datos del cliente
        $('#facturaDetallesModal .rfc-cliente').text(data.rfc_cliente);
        $('#facturaDetallesModal .nombre-cliente').text(data.nombre_cliente);
        $('#facturaDetallesModal .uso-cfdi').text(data.uso_cfdi);
        $('#facturaDetallesModal .regimen-fiscal').text(data.regimen_fiscal);
        
        // Limpiar y llenar conceptos
        var $conceptos = $('#facturaDetallesModal .conceptos').empty();
        data.conceptos.forEach(function(concepto) {
            $conceptos.append(
                '<tr>' +
                    '<td>' + concepto.clave_producto + '</td>' +
                    '<td>' + concepto.descripcion + '</td>' +
                    '<td>' + concepto.cantidad + '</td>' +
                    '<td>' + formatMoney(concepto.valor_unitario) + '</td>' +
                    '<td>' + formatMoney(concepto.importe) + '</td>' +
                '</tr>'
            );
        });
        
        // Llenar totales
        $('#facturaDetallesModal .subtotal').text(formatMoney(data.subtotal));
        $('#facturaDetallesModal .total-impuestos').text(formatMoney(data.total_impuestos));
        $('#facturaDetallesModal .total').text(formatMoney(data.total));
        
        $('#facturaDetallesModal').modal('show');
    });
}
</script>