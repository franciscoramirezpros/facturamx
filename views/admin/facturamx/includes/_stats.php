<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row mtop15">
    <!-- Total de Facturas -->
    <div class="col-md-3">
        <div class="widget-panel widget-style-1 bg-info-light">
            <i class="fa fa-file-invoice pull-right text-info"></i>
            <h2 class="m-0 counter text-info"><?php echo $total_facturas; ?></h2>
            <div><?php echo _l('facturamx_total_facturas'); ?></div>
        </div>
    </div>

    <!-- Facturas Activas -->
    <div class="col-md-3">
        <div class="widget-panel widget-style-1 bg-success-light">
            <i class="fa fa-check-circle pull-right text-success"></i>
            <h2 class="m-0 counter text-success"><?php echo $facturas_activas; ?></h2>
            <div><?php echo _l('facturamx_facturas_activas'); ?></div>
        </div>
    </div>

    <!-- Facturas Canceladas -->
    <div class="col-md-3">
        <div class="widget-panel widget-style-1 bg-danger-light">
            <i class="fa fa-times-circle pull-right text-danger"></i>
            <h2 class="m-0 counter text-danger"><?php echo $facturas_canceladas; ?></h2>
            <div><?php echo _l('facturamx_facturas_canceladas'); ?></div>
        </div>
    </div>

    <!-- Monto Total -->
    <div class="col-md-3">
        <div class="widget-panel widget-style-1 bg-primary-light">
            <i class="fa fa-dollar-sign pull-right text-primary"></i>
            <h2 class="m-0 counter text-primary">
                <?php echo app_format_money($monto_total, 'MXN'); ?>
            </h2>
            <div><?php echo _l('facturamx_monto_total'); ?></div>
        </div>
    </div>
</div>

<!-- Gráfica de Facturación Mensual -->
<div class="row mtop15">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin">
                    <?php echo _l('facturamx_grafica_mensual'); ?>
                </h4>
                <hr class="hr-panel-heading" />
                
                <div class="relative" style="max-height:400px;">
                    <canvas id="facturacion-mensual" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen por Cliente -->
<div class="row mtop15">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin">
                    <?php echo _l('facturamx_resumen_clientes'); ?>
                </h4>
                <hr class="hr-panel-heading" />
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _l('facturamx_cliente'); ?></th>
                                <th><?php echo _l('facturamx_rfc'); ?></th>
                                <th class="text-right"><?php echo _l('facturamx_facturas_emitidas'); ?></th>
                                <th class="text-right"><?php echo _l('facturamx_monto_total'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resumen_clientes as $cliente) { ?>
                                <tr>
                                    <td><?php echo $cliente['nombre_cliente']; ?></td>
                                    <td><?php echo $cliente['rfc_cliente']; ?></td>
                                    <td class="text-right"><?php echo $cliente['total_facturas']; ?></td>
                                    <td class="text-right"><?php echo app_format_money($cliente['monto_total'], 'MXN'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    // Inicializar contadores
    $('.counter').each(function () {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 1500,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    // Gráfica de facturación mensual
    var ctx = document.getElementById('facturacion-mensual').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($grafica_mensual, 'mes')); ?>,
            datasets: [
                {
                    label: '<?php echo _l('facturamx_monto_facturado'); ?>',
                    data: <?php echo json_encode(array_column($grafica_mensual, 'monto')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: '<?php echo _l('facturamx_total_facturas'); ?>',
                    data: <?php echo json_encode(array_column($grafica_mensual, 'total')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});