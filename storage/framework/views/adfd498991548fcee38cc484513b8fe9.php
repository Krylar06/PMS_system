<?php $__env->startSection('title', 'Device Manager'); ?>
<?php $__env->startSection('page_title', 'Device Manager'); ?>

<?php $__env->startSection('content'); ?>

<div
    x-data="{
        addOpen: false,
        editOpen: false,
        deleteOpen: false,

        addTypeId: '<?php echo e(old('device_type_id', $types->first()?->id)); ?>',

        typeNames: <?php echo \Illuminate\Support\Js::from($types->pluck('name', 'id'))->toHtml() ?>,

        editDevice: {
            id: null,
            device_type_id: '',
            property_number: '',
            serial_number: '',
            brand: '',
            model: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            last_maintenance_date: '',
            maintenance_remarks: '',
            notes: '',
            status: 'available',
            condition: 'serviceable',
            specs: {
                os: '',
                memory: '',
                storage: '',
                form_factor: ''
            }
        },

        deleteDeviceId: null,

        getTypeName(typeId) {
            return (this.typeNames[typeId] || '').toLowerCase();
        },

        isComputerType(typeId) {
            let name = this.getTypeName(typeId);
            return name === 'desktop' || name === 'laptop';
        },

        openEdit(device) {
            device.specs = device.specs ?? {};
            device.specs.os = device.specs.os ?? '';
            device.specs.memory = device.specs.memory ?? '';
            device.specs.storage = device.specs.storage ?? '';
            device.specs.form_factor = device.specs.form_factor ?? '';
            device.serial_number = device.serial_number ?? '';
            device.status = device.status ?? 'available';
            device.condition = device.condition ?? 'serviceable';

            this.editDevice = device;
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteDeviceId = id;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.confirmDeleteBtn && this.$refs.confirmDeleteBtn.focus());
        }
    }"
    class="space-y-5"
>
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Equipment Manager
            </h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
               href="<?php echo e(route('admin.devices.qr.index')); ?>"
               class="shrink-0 inline-flex items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
            >
                Generate QR
            </a>

            <a
                href="<?php echo e(route('admin.reports.preventiveMaintenance.export')); ?>"
                class="shrink-0 inline-flex items-center rounded-xl bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-violet-700"
            >
                Export Excel Report
            </a>

            <button
                type="button"
                class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                x-on:click="addOpen = true"
            >
                + Add Device
            </button>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">
            <div class="font-semibold">Please check the form.</div>
            <ul class="mt-1 list-inside list-disc">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="lg:w-64">
                <select
                    name="type"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">All device types</option>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($t->id); ?>" <?php if((int)($typeId ?? 0) === $t->id): echo 'selected'; endif; ?>>
                            <?php echo e($t->name); ?>

                        </option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div class="lg:w-64">
                <select
                    name="condition"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="">All conditions</option>
                    <option value="serviceable" <?php if(($condition ?? '') === 'serviceable'): echo 'selected'; endif; ?>>
                        Serviceable
                    </option>
                    <option value="unserviceable" <?php if(($condition ?? '') === 'unserviceable'): echo 'selected'; endif; ?>>
                        Unserviceable
                    </option>
                </select>
            </div>

            <input
                name="q"
                value="<?php echo e($q); ?>"
                placeholder="Search property #, serial #..."
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    Search
                </button>

                <a
                    href="<?php echo e(route('admin.devices.index')); ?>"
                    class="inline-flex items-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    
    <div class="grid grid-cols-1 gap-3 md:hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $deviceTypeName = strtolower($d->type?->name ?? '');
                $isComputer = in_array($deviceTypeName, ['desktop', 'laptop']);
            ?>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-gray-500">Type</div>
                        <div class="font-semibold text-gray-900">
                            <?php echo e($d->type?->name ?? '-'); ?>

                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-gray-500">Property #</div>
                            <div class="text-gray-900"><?php echo e($d->property_number); ?></div>
                        </div>

                        <div>
                            <div class="text-gray-500">Serial #</div>
                            <div class="text-gray-900"><?php echo e($d->serial_number ?: '-'); ?></div>
                        </div>

                        <div>
                            <div class="text-gray-500">Acquired</div>
                            <div class="text-gray-900">
                                <?php echo e($d->date_acquired ? $d->date_acquired->format('M d, Y') : '-'); ?>

                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Condition</div>
                            <div class="text-gray-900 capitalize">
                                <?php echo e($d->condition ?? 'serviceable'); ?>

                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isComputer): ?>
                            <div>
                                <div class="text-gray-500">MAC Address</div>
                                <div class="text-gray-900">
                                    <?php echo e($d->mac_address ?: '-'); ?>

                                </div>
                            </div>

                            <div>
                                <div class="text-gray-500">Operating System</div>
                                <div class="text-gray-900">
                                    <?php echo e(data_get($d->specs, 'os', '-') ?: '-'); ?>

                                </div>
                            </div>

                            <div>
                                <div class="text-gray-500">Memory</div>
                                <div class="text-gray-900">
                                    <?php echo e(data_get($d->specs, 'memory', '-') ?: '-'); ?>

                                </div>
                            </div>

                            <div>
                                <div class="text-gray-500">Storage</div>
                                <div class="text-gray-900">
                                    <?php echo e(data_get($d->specs, 'storage', '-') ?: '-'); ?>

                                </div>
                            </div>

                            <div>
                                <div class="text-gray-500">Form Factor</div>
                                <div class="text-gray-900">
                                    <?php echo e(data_get($d->specs, 'form_factor', '-') ?: '-'); ?>

                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div>
                            <div class="text-gray-500">Last Maintenance</div>
                            <div class="text-gray-900">
                                <?php echo e($d->last_maintenance_date ? $d->last_maintenance_date->format('M d, Y') : 'Not yet checked'); ?>

                            </div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d->maintenance_remarks): ?>
                        <div class="text-sm">
                            <div class="text-gray-500">Maintenance Remarks</div>
                            <div class="text-gray-900"><?php echo e($d->maintenance_remarks); ?></div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <a
                            href="<?php echo e(route('admin.devices.show', $d)); ?>"
                            class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                        >
                            View
                        </a>

                        <a
                            href="<?php echo e(route('admin.devices.history', $d)); ?>"
                            class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                        >
                            History
                        </a>

                        <form method="POST" action="<?php echo e(route('admin.devices.markChecked', $d)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                Mark Checked
                            </button>
                        </form>

                        <button
                            type="button"
                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                            x-on:click="openEdit({
                                id: <?php echo e($d->id); ?>,
                                device_type_id: '<?php echo e($d->device_type_id); ?>',
                                property_number: <?php echo \Illuminate\Support\Js::from($d->property_number)->toHtml() ?>,
                                serial_number: <?php echo \Illuminate\Support\Js::from($d->serial_number ?? '')->toHtml() ?>,
                                brand: <?php echo \Illuminate\Support\Js::from($d->brand ?? '')->toHtml() ?>,
                                model: <?php echo \Illuminate\Support\Js::from($d->model ?? '')->toHtml() ?>,
                                mac_address: <?php echo \Illuminate\Support\Js::from($d->mac_address ?? '')->toHtml() ?>,
                                unit_price: <?php echo \Illuminate\Support\Js::from($d->unit_price ?? '')->toHtml() ?>,
                                date_acquired: <?php echo \Illuminate\Support\Js::from($d->date_acquired ? $d->date_acquired->format('Y-m-d') : '')->toHtml() ?>,
                                last_maintenance_date: <?php echo \Illuminate\Support\Js::from($d->last_maintenance_date ? $d->last_maintenance_date->format('Y-m-d') : '')->toHtml() ?>,
                                maintenance_remarks: <?php echo \Illuminate\Support\Js::from($d->maintenance_remarks ?? '')->toHtml() ?>,
                                status: <?php echo \Illuminate\Support\Js::from($d->status ?? 'available')->toHtml() ?>,
                                condition: <?php echo \Illuminate\Support\Js::from($d->condition ?? 'serviceable')->toHtml() ?>,
                                notes: <?php echo \Illuminate\Support\Js::from($d->notes ?? '')->toHtml() ?>,
                                specs: {
                                    os: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'os', ''))->toHtml() ?>,
                                    memory: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'memory', ''))->toHtml() ?>,
                                    storage: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'storage', ''))->toHtml() ?>,
                                    form_factor: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'form_factor', ''))->toHtml() ?>
                                }
                            })"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                            x-on:click="openDelete(<?php echo e($d->id); ?>)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No devices found.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Type</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Property #</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Serial #</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Acquired</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Last Maintenance</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Condition</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-900">
                                <?php echo e($d->type?->name ?? '-'); ?>

                            </td>

                            <td class="px-4 py-3 text-gray-900">
                                <?php echo e($d->property_number); ?>

                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                <?php echo e($d->serial_number ?: '-'); ?>

                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                <?php echo e($d->date_acquired ? $d->date_acquired->format('M d, Y') : '-'); ?>

                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d->last_maintenance_date): ?>
                                    <div class="font-medium text-gray-900">
                                        <?php echo e($d->last_maintenance_date->format('M d, Y')); ?>

                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d->maintenance_remarks): ?>
                                        <div class="max-w-xs truncate text-xs text-gray-500">
                                            <?php echo e($d->maintenance_remarks); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">
                                        Not yet checked
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                            <td class="px-4 py-3 text-gray-700 capitalize">
                                <?php echo e($d->condition ?? 'serviceable'); ?>

                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a
                                        href="<?php echo e(route('admin.devices.show', $d)); ?>"
                                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="<?php echo e(route('admin.devices.history', $d)); ?>"
                                        class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                                    >
                                        History
                                    </a>

                                    <form method="POST" action="<?php echo e(route('admin.devices.markChecked', $d)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <button
                                            type="submit"
                                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                        >
                                            Mark Checked
                                        </button>
                                    </form>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        x-on:click="openEdit({
                                            id: <?php echo e($d->id); ?>,
                                            device_type_id: '<?php echo e($d->device_type_id); ?>',
                                            property_number: <?php echo \Illuminate\Support\Js::from($d->property_number)->toHtml() ?>,
                                            serial_number: <?php echo \Illuminate\Support\Js::from($d->serial_number ?? '')->toHtml() ?>,
                                            brand: <?php echo \Illuminate\Support\Js::from($d->brand ?? '')->toHtml() ?>,
                                            model: <?php echo \Illuminate\Support\Js::from($d->model ?? '')->toHtml() ?>,
                                            mac_address: <?php echo \Illuminate\Support\Js::from($d->mac_address ?? '')->toHtml() ?>,
                                            unit_price: <?php echo \Illuminate\Support\Js::from($d->unit_price ?? '')->toHtml() ?>,
                                            date_acquired: <?php echo \Illuminate\Support\Js::from($d->date_acquired ? $d->date_acquired->format('Y-m-d') : '')->toHtml() ?>,
                                            last_maintenance_date: <?php echo \Illuminate\Support\Js::from($d->last_maintenance_date ? $d->last_maintenance_date->format('Y-m-d') : '')->toHtml() ?>,
                                            maintenance_remarks: <?php echo \Illuminate\Support\Js::from($d->maintenance_remarks ?? '')->toHtml() ?>,
                                            status: <?php echo \Illuminate\Support\Js::from($d->status ?? 'available')->toHtml() ?>,
                                            condition: <?php echo \Illuminate\Support\Js::from($d->condition ?? 'serviceable')->toHtml() ?>,
                                            notes: <?php echo \Illuminate\Support\Js::from($d->notes ?? '')->toHtml() ?>,
                                            specs: {
                                                os: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'os', ''))->toHtml() ?>,
                                                memory: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'memory', ''))->toHtml() ?>,
                                                storage: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'storage', ''))->toHtml() ?>,
                                                form_factor: <?php echo \Illuminate\Support\Js::from(data_get($d->specs, 'form_factor', ''))->toHtml() ?>
                                            }
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        x-on:click="openDelete(<?php echo e($d->id); ?>)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No devices found.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <?php echo e($devices->links()); ?>

    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'addOpen','title' => 'Add Device']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'addOpen','title' => 'Add Device']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.devices.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="status" value="available">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select
                        name="device_type_id"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                        x-model="addTypeId"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($t->id); ?>">
                                <?php echo e($t->name); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input
                        name="property_number"
                        value="<?php echo e(old('property_number')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        value="<?php echo e(old('serial_number')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        value="<?php echo e(old('brand')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        value="<?php echo e(old('model')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: Epson L3110, Acer Aspire"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        value="<?php echo e(old('mac_address')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="00:1A:2B:3C:4D:5E"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <input
                        name="specs[os]"
                        value="<?php echo e(old('specs.os')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: Windows 10, Windows 11, Ubuntu"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        value="<?php echo e(old('specs.memory')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: 8GB RAM"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        value="<?php echo e(old('specs.storage')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: 256GB SSD / 1TB HDD"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div x-show="isComputerType(addTypeId)" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <input
                        name="specs[form_factor]"
                        value="<?php echo e(old('specs.form_factor')); ?>"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        placeholder="Example: Tower, SFF, Mini PC, All-in-One"
                        :disabled="!isComputerType(addTypeId)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        value="<?php echo e(old('unit_price')); ?>"
                        type="number"
                        step="0.01"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        value="<?php echo e(old('date_acquired')); ?>"
                        type="date"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Condition</label>
                    <select
                        name="condition"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    >
                        <option value="serviceable" <?php if(old('condition', 'serviceable') === 'serviceable'): echo 'selected'; endif; ?>>
                            Serviceable
                        </option>
                        <option value="unserviceable" <?php if(old('condition') === 'unserviceable'): echo 'selected'; endif; ?>>
                            Unserviceable
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Last Maintenance Date</label>
                    <input
                        name="last_maintenance_date"
                        value="<?php echo e(old('last_maintenance_date')); ?>"
                        type="date"
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
                    placeholder="Example: Initial check, cleaned, inspected"
                ><?php echo e(old('maintenance_remarks')); ?></textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                ><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Save
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    x-on:click="addOpen = false"
                >
                    Cancel
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

        <form method="POST" :action="`<?php echo e(url('/admin/devices')); ?>/${editDevice.id}`" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <input type="hidden" name="status" x-model="editDevice.status">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <select
                        name="device_type_id"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                        x-model="editDevice.device_type_id"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($t->id); ?>">
                                <?php echo e($t->name); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Property Number</label>
                    <input
                        name="property_number"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.property_number"
                        required
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Serial Number</label>
                    <input
                        name="serial_number"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.serial_number"
                        placeholder="Enter serial number"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Brand</label>
                    <input
                        name="brand"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.brand"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Model</label>
                    <input
                        name="model"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.model"
                        placeholder="Example: Epson L3110, Acer Aspire"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.mac_address"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <input
                        name="specs[os]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.os"
                        placeholder="Example: Windows 10, Windows 11, Ubuntu"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Memory</label>
                    <input
                        name="specs[memory]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.memory"
                        placeholder="Example: 8GB RAM"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Storage</label>
                    <input
                        name="specs[storage]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.storage"
                        placeholder="Example: 256GB SSD / 1TB HDD"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.device_type_id)" x-cloak>
                    <label class="text-sm font-medium">Form Factor</label>
                    <input
                        name="specs[form_factor]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.form_factor"
                        placeholder="Example: Tower, SFF, Mini PC, All-in-One"
                        :disabled="!isComputerType(editDevice.device_type_id)"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input
                        name="unit_price"
                        type="number"
                        step="0.01"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.unit_price"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Date Acquired</label>
                    <input
                        name="date_acquired"
                        type="date"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.date_acquired"
                    >
                </div>

                <div>
                    <label class="text-sm font-medium">Condition</label>
                    <select
                        name="condition"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.condition"
                    >
                        <option value="serviceable">Serviceable</option>
                        <option value="unserviceable">Unserviceable</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Last Maintenance Date</label>
                    <input
                        name="last_maintenance_date"
                        type="date"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.last_maintenance_date"
                    >
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Maintenance Remarks</label>
                <textarea
                    name="maintenance_remarks"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editDevice.maintenance_remarks"
                ></textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Notes</label>
                <textarea
                    name="notes"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editDevice.notes"
                ></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Save Changes
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    x-on:click="editOpen = false"
                >
                    Cancel
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

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'deleteOpen','title' => 'Delete Device']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'deleteOpen','title' => 'Delete Device']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this device?
            </div>

            <form
                method="POST"
                :action="`<?php echo e(url('/admin/devices')); ?>/${deleteDeviceId}`"
                @submit="if (!deleteDeviceId) $event.preventDefault()"
                class="flex gap-2"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button
                    type="submit"
                    x-ref="confirmDeleteBtn"
                    class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                >
                    Confirm
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    x-on:click="deleteOpen = false"
                >
                    Cancel
                </button>
            </form>
        </div>
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
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/devices/index.blade.php ENDPATH**/ ?>