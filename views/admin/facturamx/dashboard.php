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
                                    <?php echo _l('facturamx_dashboard'); ?>
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
                        
                        <?php $this->load->view('admin/facturamx/includes/_stats'); ?>

                        <div class="row mtop20">
                            <div class="col-md-12">
                                <h4><?php echo _l('facturamx_ultimas_facturas'); ?></h4>
                                <hr class="hr-panel-heading" />
                                
                                <table class="table dt-table table-facturas">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('facturamx_uuid'); ?></th>
                                            <th><?php echo _l('facturamx_rfc_cliente'); ?></th>
                                            <th><?php echo _l('facturamx_nombre_cliente'); ?></th>
                                            <th><?php echo _l('facturamx_fecha'); ?></th>
                                            <th><?php echo _l('facturamx_total'); ?></th>
                                            <th><?php echo _l('facturamx_estado'); ?></th>
                                            <th><?php echo _l('facturamx_opciones'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($facturas as $factura) { ?>
                                            <tr>
                                                <td><?php echo $factura['uuid']; ?></td>
                                                <td><?php echo $factura['rfc_cliente']; ?></td>
                                                <td><?php echo $factura['nombre_cliente']; ?></td>
                                                <td><?php echo _dt($factura['fecha']); ?></td>
                                                <td><?php echo app_format_money($factura['total'], 'MXN'); ?></td>
                                                <td>
                                                    <span class="label label-<?php echo get_factura_status_label($factura['estado']); ?>">
                                                        <?php echo _l('facturamx_estado_' . $factura['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo admin_url('facturamx/descargar_pdf/' . $factura['uuid']); ?>" 
                                                           class="btn btn-default btn-icon" title="PDF">
                                                            <i class="fa fa-file-pdf"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('facturamx/descargar_xml/' . $factura['uuid']); ?>" 
                                                           class="btn btn-default btn-icon" title="XML">
                                                            <i class="fa fa-file-code"></i>
                                                        </a>
                                                        <?php if ($factura['estado'] !== 'cancelada' && staff_can('delete', 'facturamx')) { ?>
                                                            <a href="<?php echo admin_url('facturamx/cancelar/' . $factura['uuid']); ?>" 
                                                               class="btn btn-danger btn-icon" 
                                                               onclick="return confirm('<?php echo _l('facturamx_confirmar_cancelacion'); ?>');">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        <?php } ?>
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
<?php init_tail(); ?>
<script>
$(function() {
    var tblFacturas = $('.table-facturas').DataTable({
        language: appLang,
        responsive: true,
        order: [[3, 'desc']] // Ordenar por fecha descendente
    });
});
</script>