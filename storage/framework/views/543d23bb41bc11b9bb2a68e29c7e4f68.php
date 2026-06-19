<?php $__env->startSection('title', 'Device Details'); ?>
<?php $__env->startSection('page_title', 'Device Details'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $deviceTypeName = strtolower($device->type?->name ?? '');
    $isComputerType = in_array($deviceTypeName, ['desktop', 'laptop']);
    $deviceUrl = route('admin.devices.show', $device);
?>

<div
    x-data="{
        editOpen: false,
        selectedTypeId: <?php echo json_encode(old('device_type_id', $device->device_type_id), 512) ?>,

        typeNames: <?php echo json_encode($types->pluck('name', 'id'), 512) ?>,
        getTypeName(typeId) {
            return (this.typeNames[typeId] || '').toLowerCase();
        },

        isComputerType(typeId = null) {
            let selected = typeId ?? this.selectedTypeId;
            let name = this.getTypeName(selected);

            return name === 'desktop' || name === 'laptop';
        }
    }"
    class="grid grid-cols-1 gap-6 lg:grid-cols-3"
>
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        <?php echo e($device->property_number); ?>

                    </h1>

                    <p class="mt-1 text-gray-500">
                        <?php echo e($device->type?->name ?? 'Device'); ?>

                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a
                        href="<?php echo e(route('admin.devices.index')); ?>"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                    >
                        Back
                    </a>

                    <a
                        href="<?php echo e(route('admin.devices.history', $device)); ?>"
                        class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700"
                    >
                        History
                    </a>

                    <form
                        method="POST"
                        action="<?php echo e(route('admin.devices.markChecked', $device)); ?>"
                        onsubmit="return confirm('Mark this device as checked/maintained today?')"
                    >
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>

                        <button
                            type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                        >
                            Mark as Checked
                        </button>
                    </form>

                    <button
                        type="button"
                        x-on:click="editOpen = true"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Edit
                    </button>

                    <form
                        method="POST"
                        action="<?php echo e(route('admin.devices.destroy', $device)); ?>"
                        onsubmit="return confirm('Delete this device?')"
                    >
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

                        <button
                            type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <div class="text-sm text-gray-500">Device Type</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->type?->name ?? '-'); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Property Number</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->property_number); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Serial Number</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->serial_number ?: '-'); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Brand</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->brand ?: '-'); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Model</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->model ?: '-'); ?>

                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isComputerType): ?>
                    <div>
                        <div class="text-sm text-gray-500">MAC Address</div>
                        <div class="font-medium text-gray-900">
                            <?php echo e($device->mac_address ?: '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Operating System</div>
                        <div class="font-medium text-gray-900">
                            <?php echo e(data_get($device->specs, 'os', '-') ?: '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Memory</div>
                        <div class="font-medium text-gray-900">
                            <?php echo e(data_get($device->specs, 'memory', '-') ?: '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Storage</div>
                        <div class="font-medium text-gray-900">
                            <?php echo e(data_get($device->specs, 'storage', '-') ?: '-'); ?>

                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Form Factor</div>
                        <div class="font-medium text-gray-900">
                            <?php echo e(data_get($device->specs, 'form_factor', '-') ?: '-'); ?>

                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div>
                    <div class="text-sm text-gray-500">Unit Price</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->unit_price ? number_format($device->unit_price, 2) : '-'); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Date Acquired</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->date_acquired ? $device->date_acquired->format('Y-m-d') : '-'); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Condition</div>
                    <div class="font-medium text-gray-900 capitalize">
                        <?php echo e($device->condition ?? 'serviceable'); ?>

                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Last Maintenance</div>
                    <div class="font-medium text-gray-900">
                        <?php echo e($device->last_maintenance_date ? $device->last_maintenance_date->format('M d, Y') : 'Not yet checked'); ?>

                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($device->maintenance_remarks): ?>
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="font-semibold text-gray-900">
                        Maintenance Remarks
                    </h2>

                    <p class="mt-3 text-gray-700">
                        <?php echo e($device->maintenance_remarks); ?>

                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-8 border-t border-gray-200 pt-6">
                <h2 class="font-semibold text-gray-900">
                    Current Assignment
                </h2>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($device->currentAssignment && $device->currentAssignment->staff): ?>
                    <div class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="font-medium text-gray-900">
                            <?php echo e($device->currentAssignment->staff->last_name); ?>,
                            <?php echo e($device->currentAssignment->staff->first_name); ?>

                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            <?php echo e($device->currentAssignment->staff->office?->name ?? 'No office'); ?>


                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($device->currentAssignment->staff->office?->college): ?>
                                /
                                <?php echo e($device->currentAssignment->staff->office->college->name); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Issued:
                            <?php echo e($device->currentAssignment->issued_at ? $device->currentAssignment->issued_at->format('M d, Y h:i A') : '-'); ?>

                        </div>
                    </div>
                <?php else: ?>
                    <p class="mt-3 text-gray-700">
                        This device is not currently issued.
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($device->notes): ?>
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="font-semibold text-gray-900">
                        Notes
                    </h2>

                    <p class="mt-3 text-gray-700">
                        <?php echo e($device->notes); ?>

                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'editOpen','title' => 'Edit Device']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'editOpen','title' => 'Edit Device']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.devices.update', $device)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <input type="hidden" name="status" value="<?php echo e($device->status ?? 'available'); ?>">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select
                        name="device_type_id"
                        x-model="selectedTypeId"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($type->id); ?>">
                                <?php echo e($type->name); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input
                        name="property_number"
                        value="<?php echo e(old('property_number', $device->property_number)); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        value="<?php echo e(old('serial_number', $device->serial_number)); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        value="<?php echo e(old('brand', $device->brand)); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        value="<?php echo e(old('model', $device->model)); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        value="<?php echo e(old('mac_address', $device->mac_address)); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <input
                        name="specs[os]"
                        value="<?php echo e(old('specs.os', data_get($device->specs, 'os'))); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        value="<?php echo e(old('specs.memory', data_get($device->specs, 'memory'))); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        value="<?php echo e(old('specs.storage', data_get($device->specs, 'storage'))); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div x-show="isComputerType()" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <input
                        name="specs[form_factor]"
                        value="<?php echo e(old('specs.form_factor', data_get($device->specs, 'form_factor'))); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        :disabled="!isComputerType()"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        type="number"
                        step="0.01"
                        value="<?php echo e(old('unit_price', $device->unit_price)); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        type="date"
                        value="<?php echo e(old('date_acquired', $device->date_acquired ? $device->date_acquired->format('Y-m-d') : '')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Condition</label>
                    <select
                        name="condition"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                        <option value="serviceable" <?php if(old('condition', $device->condition ?? 'serviceable') === 'serviceable'): echo 'selected'; endif; ?>>
                            Serviceable
                        </option>

                        <option value="unserviceable" <?php if(old('condition', $device->condition) === 'unserviceable'): echo 'selected'; endif; ?>>
                            Unserviceable
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Last Maintenance Date</label>
                    <input
                        name="last_maintenance_date"
                        type="date"
                        value="<?php echo e(old('last_maintenance_date', $device->last_maintenance_date ? $device->last_maintenance_date->format('Y-m-d') : '')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Maintenance Remarks</label>
                <textarea
                    name="maintenance_remarks"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                ><?php echo e(old('maintenance_remarks', $device->maintenance_remarks)); ?></textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                ><?php echo e(old('notes', $device->notes)); ?></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    x-on:click="editOpen = false"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Save Changes
                </button>
            </div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/devices/show.blade.php ENDPATH**/ ?>