<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate QR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: white; font-family: Arial, sans-serif; padding: 5mm; }

        @page { size: legal portrait; margin: 5mm; }

        .print-btn {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }

        .print-btn button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .qr-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 3px;
        }

        .qr-card {
            border: 1px dashed black;
            border-radius: 6px;
            padding: 4px;
            text-align: center;
            page-break-inside: avoid;
            break-inside: avoid;
            overflow: hidden;
        }

        .qr-card svg {
            width: 80px !important;
            height: 80px !important;
        }

        .qr-prop {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .qr-type {
            font-size: 8px;
            margin-top: 1px;
        }

        .qr-serial {
            font-size: 7px;
            color: #555;
        }

        @media print {
            .print-btn { display: none; }
            body { background: white; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-btn">
        <button onclick="window.print()">Print</button>
    </div>

    <div class="qr-container">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="qr-card">
                <div class="qr-prop"><?php echo e(collect(explode('-', $device->property_number ?? ''))->last()); ?></div>
                <div><?php echo $qrCodes[$device->id]; ?></div>
                <div class="qr-type"><?php echo e($device->type?->name); ?></div>
                <div class="qr-serial">Serial: <?php echo e($device->serial_number ?: 'N/A'); ?></div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\pms_system\resources\views/admin/devices/generate-qr.blade.php ENDPATH**/ ?>