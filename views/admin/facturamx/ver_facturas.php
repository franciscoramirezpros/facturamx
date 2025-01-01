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
                                <h4 class="no-margin">
                                    <?php echo _l('facturamx_listado_cfdi'); ?>
                                </h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (staff_can('create', 'facturamx')) { ?>
                                    <a href="<?php echo admin_url('facturamx/crear'); ?>" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> <?php echo _l('facturamx_nueva_factura'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php $this->load->view('admin/facturamx/includes/_filters'); ?>
                        
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table dt-table table-facturas" data-order-col="3" data-order-type="desc">
                                        <thead>
                                            <tr>
                                                <th><?php echo _l('facturamx_uuid'); ?></th>
                                                <th><?php echo _l('facturamx_rfc_cliente'); ?></th>
                                                <th><?php echo _l('facturamx_nombre_cliente'); ?></th>
                                                <th><?php echo _l('facturamx_fecha'); ?></th>
                                                <th><?php echo _l('facturamx_serie_folio'); ?></th>
                                                <th><?php echo _l('facturamx_total'); ?></th>
                                                <th><?php echo _l('facturamx_estado'); ?></th>
                                                <th><?php echo _l('facturamx_opciones'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($facturas as $factura) { ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?php echo admin_url('facturamx/ver/' . $factura['uuid']); ?>">
                                                            <?php echo $factura['uuid']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $factura['rfc_cliente']; ?></td>
                                                    <td><?php echo $factura['nombre_cliente']; ?></td>
                                                    <td><?php echo _dt($factura['fecha']); ?></td>
                                                    <td><?php echo $factura['serie'] . '-' . $factura['folio']; ?></td>
                                                    <td><?php echo app_format_money($factura['total'], 'MXN'); ?></td>
                                                    <td>
                                                        <span class="label label-<?php echo get_factura_status_label($factura['estado']); ?>">
                                                            <?php echo _l('facturamx_estado_' . $factura['estado']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group action-btn">
                                                            <button type="button" 
                                                                    class="btn btn-default dropdown-toggle" 
                                                                    data-toggle="dropdown" 
                                                                    aria-haspopup="true" 
                                                                    aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <li>
                                                                    <a href="<?php echo admin_url('facturamx/descargar_pdf/' . $factura['uuid']); ?>">
                                                                        <i class="fa fa-file-pdf"></i> <?php echo _l('facturamx_descargar_pdf'); ?>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?php echo admin_url('facturamx/descargar_xml/' . $factura['uuid']); ?>">
                                                                        <i class="fa fa-file-code"></i> <?php echo _l('facturamx_descargar_xml'); ?>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" onclick="enviarEmail('<?php echo $factura['uuid']; ?>')">
                                                                        <i class="fa fa-envelope"></i> <?php echo _l('facturamx_enviar_email'); ?>
                                                                    </a>
                                                                </li>
                                                                <?php if ($factura['estado'] !== 'cancelada' && staff_can('delete', 'facturamx')) { ?>
                                                                    <li>
                                                                        <a href="#" 
                                                                           onclick="cancelarFactura('<?php echo $factura['uuid']; ?>')">
                                                                            <i class="fa fa-ban"></i> <?php echo _l('facturamx_cancelar'); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
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
</div>

<?php $this->load->view('admin/facturamx/includes/_modal'); ?>
<?php init_tail(); ?>

<script>
$(function() {
    var tblFacturas = $('.table-facturas').DataTable({
        language: appLang,
        responsive: true,
        order: [[3, 'desc']], // Ordenar por fecha descendente
        pageLength: 25
    });
});

function enviarEmail(uuid) {
    var modal = $('#facturaEmailModal');
    modal.find('input[name="uuid"]').val(uuid);
    modal.modal('show');
}

function cancelarFactura(uuid) {
    if (confirm(appLang.facturamx_confirmar_cancelacion)) {
        window.location.href = admin_url + 'facturamx/cancelar/' + uuid;
    }
}
</script>