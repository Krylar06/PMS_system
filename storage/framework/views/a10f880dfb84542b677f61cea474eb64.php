<?php $__env->startSection('title', 'Maintenance History'); ?>
<?php $__env->startSection('page_title', 'Maintenance History'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Maintenance History</h1>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo e($device->type?->name ?? 'Device'); ?> |
                Property #: <?php echo e($device->property_number); ?>

            </p>
        </div>

        <a
            href="<?php echo e(route('admin.devices.index')); ?>"
            class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
        >
            Back
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Type</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Remarks</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Checked By</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Recorded At</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <?php echo e($record->maintenance_date?->format('M d, Y')); ?>

                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            <?php echo e($record->maintenance_type); ?>

                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            <?php echo e($record->remarks ?? '-'); ?>

                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            <?php echo e($record->checkedBy?->name ?? $record->checkedBy?->email ?? '-'); ?>

                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            <?php echo e($record->created_at?->format('M d, Y h:i A')); ?>

                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No maintenance records yet.
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/devices/maintenance-history.blade.php ENDPATH**/ ?>