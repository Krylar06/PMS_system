<?php $__env->startSection('title', 'Issued Devices'); ?>
<?php $__env->startSection('page_title', 'Issued Devices'); ?>

<?php $__env->startSection('content'); ?>
<div
    x-data="{
        issueOpen: <?php echo e($errors->has('device_id') ? 'true' : 'false'); ?>,
        editOpen: false,
        deleteOpen: false,

        deviceSearch: '',
        selectedDevice: null,

        availableDevices: <?php echo \Illuminate\Support\Js::from(
            $availableDevices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'type' => $device->type?->name ?? 'Device',
                    'property_number' => $device->property_number,
                    'brand' => $device->brand,
                    'model' => $device->model,
                    'status' => $device->status,
                ];
            })->values()
        )->toHtml() ?>,

        editDevice: {
            id: null,
            device_type_id: '',
            type_name: '',
            property_number: '',
            brand: '',
            model: '',
            mac_address: '',
            unit_price: '',
            date_acquired: '',
            last_maintenance_date: '',
            maintenance_remarks: '',
            status: '',
            notes: '',
            specs: {}
        },

        deleteDeviceId: null,

        filteredDevices() {
            let search = this.deviceSearch.toLowerCase().trim();

            let devices = this.availableDevices.filter(device => {
                return device.status === 'available';
            });

            if (!search) {
                return devices.slice(0, 8);
            }

            return devices.filter(device => {
                return [
                    device.type,
                    device.property_number,
                    device.brand,
                    device.model
                ].filter(Boolean).join(' ').toLowerCase().includes(search);
            }).slice(0, 10);
        },

        selectDevice(device) {
            this.selectedDevice = device;
            this.deviceSearch = `${device.type} - ${device.property_number} - ${device.brand ?? ''} ${device.model ?? ''}`.trim();
        },

        clearSelectedDevice() {
            this.selectedDevice = null;
            this.deviceSearch = '';
        },

        openIssueModal() {
            this.issueOpen = true;
            this.deviceSearch = '';
            this.selectedDevice = null;
        },

        isComputerType(typeName) {
            return ['desktop', 'laptop'].includes(String(typeName || '').toLowerCase());
        },

        openEdit(device) {
            device.specs = device.specs ?? {};
            device.specs.os = device.specs.os ?? '';

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
    
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="<?php echo e(route('admin.colleges.index')); ?>">
            Colleges
        </a>

        <span class="mx-1">/</span>

        <a class="text-blue-600 hover:underline" href="<?php echo e(route('admin.offices.index', $staff->office->college)); ?>">
            <?php echo e($staff->office->college->name); ?>

        </a>

        <span class="mx-1">/</span>

        <a class="text-blue-600 hover:underline" href="<?php echo e(route('admin.staff.index', $staff->office)); ?>">
            <?php echo e($staff->office->name); ?>

        </a>

        <span class="mx-1">/</span>

        <span class="font-medium text-gray-700">
            <?php echo e($staff->last_name); ?>, <?php echo e($staff->first_name); ?>

        </span>

        <span class="mx-1">/</span>

        <span>Issued Devices</span>
    </div>

    
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Issued Devices
            </h1>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="openIssueModal()"
        >
            + Issue Device
        </button>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="rounded-xl bg-green-100 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
        <div class="rounded-xl bg-blue-100 px-4 py-3 text-sm text-blue-700">
            <?php echo e(session('info')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="grid grid-cols-1 gap-3 md:hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $issued; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $dev = $assignment->device;
                $typeName = $dev?->type?->name ?? 'Device';
                $isComputer = in_array(strtolower($typeName), ['desktop', 'laptop']);
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev): ?>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="space-y-3">
                        <div>
                            <div class="text-xs font-medium uppercase text-gray-500">
                                <?php echo e($typeName); ?>

                            </div>

                            <a
                                href="<?php echo e(route('admin.devices.show', $dev)); ?>"
                                class="mt-1 block text-base font-semibold text-blue-600 hover:underline"
                            >
                                <?php echo e($dev->property_number); ?>

                            </a>

                            <div class="mt-1 text-sm text-gray-600">
                                <?php echo e($dev->brand ?: '-'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev->model): ?>
                                    • <?php echo e($dev->model); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isComputer && $dev->mac_address): ?>
                            <div>
                                <div class="text-xs text-gray-500">MAC Address</div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo e($dev->mac_address); ?>

                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div>
                            <div class="text-xs text-gray-500">Issued At</div>
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo e($assignment->issued_at ? $assignment->issued_at->format('Y-m-d H:i') : '-'); ?>

                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Last Maintenance</div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev->last_maintenance_date): ?>
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo e($dev->last_maintenance_date->format('M d, Y')); ?>

                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev->maintenance_remarks): ?>
                                    <div class="text-xs text-gray-500">
                                        <?php echo e($dev->maintenance_remarks); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <div class="text-sm text-gray-400">
                                    Not yet checked
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <form method="POST" action="<?php echo e(route('admin.staff.devices.return', [$staff, $assignment])); ?>">
                                <?php echo csrf_field(); ?>

                                <button
                                    type="submit"
                                    onclick="return confirm('Return this device?')"
                                    class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                >
                                    Return
                                </button>
                            </form>

                            <form method="POST" action="<?php echo e(route('admin.devices.markChecked', $dev)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>

                                <button
                                    type="submit"
                                    class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                >
                                    Mark Checked
                                </button>
                            </form>

                            <a
                                href="<?php echo e(route('admin.devices.history', $dev)); ?>"
                                class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                            >
                                History
                            </a>

                            <button
                                type="button"
                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                @click="openEdit({
                                    id: <?php echo e($dev->id); ?>,
                                    device_type_id: <?php echo \Illuminate\Support\Js::from($dev->device_type_id)->toHtml() ?>,
                                    type_name: <?php echo \Illuminate\Support\Js::from($typeName)->toHtml() ?>,
                                    property_number: <?php echo \Illuminate\Support\Js::from($dev->property_number)->toHtml() ?>,
                                    brand: <?php echo \Illuminate\Support\Js::from($dev->brand ?? '')->toHtml() ?>,
                                    model: <?php echo \Illuminate\Support\Js::from($dev->model ?? '')->toHtml() ?>,
                                    mac_address: <?php echo \Illuminate\Support\Js::from($dev->mac_address ?? '')->toHtml() ?>,
                                    unit_price: <?php echo \Illuminate\Support\Js::from($dev->unit_price ?? '')->toHtml() ?>,
                                    date_acquired: <?php echo \Illuminate\Support\Js::from($dev->date_acquired ? $dev->date_acquired->format('Y-m-d') : '')->toHtml() ?>,
                                    last_maintenance_date: <?php echo \Illuminate\Support\Js::from($dev->last_maintenance_date ? $dev->last_maintenance_date->format('Y-m-d') : '')->toHtml() ?>,
                                    maintenance_remarks: <?php echo \Illuminate\Support\Js::from($dev->maintenance_remarks ?? '')->toHtml() ?>,
                                    status: <?php echo \Illuminate\Support\Js::from($dev->status ?? '')->toHtml() ?>,
                                    notes: <?php echo \Illuminate\Support\Js::from($dev->notes ?? '')->toHtml() ?>,
                                    specs: <?php echo \Illuminate\Support\Js::from(['os' => data_get($dev->specs, 'os', '')])->toHtml() ?>
                                })"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                @click="openDelete(<?php echo e($dev->id); ?>)"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500">
                No issued devices.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Type</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Property #</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Brand</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">MAC</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Issued At</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Last Maintenance</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $issued; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $dev = $assignment->device;
                            $typeName = $dev?->type?->name ?? 'Device';
                            $isComputer = in_array(strtolower($typeName), ['desktop', 'laptop']);
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">
                                    <?php echo e($typeName); ?>

                                </td>

                                <td class="px-4 py-3">
                                    <a
                                        href="<?php echo e(route('admin.devices.show', $dev)); ?>"
                                        class="font-medium text-blue-600 hover:underline"
                                    >
                                        <?php echo e($dev->property_number); ?>

                                    </a>
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    <?php echo e($dev->brand ?: '-'); ?>

                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    <?php echo e($isComputer ? ($dev->mac_address ?: '-') : '-'); ?>

                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    <?php echo e($assignment->issued_at ? $assignment->issued_at->format('Y-m-d H:i') : '-'); ?>

                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev->last_maintenance_date): ?>
                                        <div class="font-medium text-gray-900">
                                            <?php echo e($dev->last_maintenance_date->format('M d, Y')); ?>

                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dev->maintenance_remarks): ?>
                                            <div class="max-w-xs truncate text-xs text-gray-500">
                                                <?php echo e($dev->maintenance_remarks); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Not yet checked</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="<?php echo e(route('admin.staff.devices.return', [$staff, $assignment])); ?>">
                                            <?php echo csrf_field(); ?>

                                            <button
                                                type="submit"
                                                onclick="return confirm('Return this device?')"
                                                class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                            >
                                                Return
                                            </button>
                                        </form>

                                        <form method="POST" action="<?php echo e(route('admin.devices.markChecked', $dev)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                            >
                                                Mark Checked
                                            </button>
                                        </form>

                                        <a
                                            href="<?php echo e(route('admin.devices.history', $dev)); ?>"
                                            class="rounded-lg bg-purple-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-purple-700"
                                        >
                                            History
                                        </a>

                                        <button
                                            type="button"
                                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700"
                                            @click="openEdit({
                                                id: <?php echo e($dev->id); ?>,
                                                device_type_id: <?php echo \Illuminate\Support\Js::from($dev->device_type_id)->toHtml() ?>,
                                                type_name: <?php echo \Illuminate\Support\Js::from($typeName)->toHtml() ?>,
                                                property_number: <?php echo \Illuminate\Support\Js::from($dev->property_number)->toHtml() ?>,
                                                brand: <?php echo \Illuminate\Support\Js::from($dev->brand ?? '')->toHtml() ?>,
                                                model: <?php echo \Illuminate\Support\Js::from($dev->model ?? '')->toHtml() ?>,
                                                mac_address: <?php echo \Illuminate\Support\Js::from($dev->mac_address ?? '')->toHtml() ?>,
                                                unit_price: <?php echo \Illuminate\Support\Js::from($dev->unit_price ?? '')->toHtml() ?>,
                                                date_acquired: <?php echo \Illuminate\Support\Js::from($dev->date_acquired ? $dev->date_acquired->format('Y-m-d') : '')->toHtml() ?>,
                                                last_maintenance_date: <?php echo \Illuminate\Support\Js::from($dev->last_maintenance_date ? $dev->last_maintenance_date->format('Y-m-d') : '')->toHtml() ?>,
                                                maintenance_remarks: <?php echo \Illuminate\Support\Js::from($dev->maintenance_remarks ?? '')->toHtml() ?>,
                                                status: <?php echo \Illuminate\Support\Js::from($dev->status ?? '')->toHtml() ?>,
                                                notes: <?php echo \Illuminate\Support\Js::from($dev->notes ?? '')->toHtml() ?>,
                                                specs: <?php echo \Illuminate\Support\Js::from(['os' => data_get($dev->specs, 'os', '')])->toHtml() ?>
                                            })"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                            @click="openDelete(<?php echo e($dev->id); ?>)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No issued devices.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'issueOpen','title' => 'Issue a device to '.e($staff->first_name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'issueOpen','title' => 'Issue a device to '.e($staff->first_name).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.staff.devices.issue', $staff)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Available Devices
                </label>

                <input
                    type="text"
                    x-model="deviceSearch"
                    @input="selectedDevice = null"
                    placeholder="Search device type, property number, brand, or model..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                <input
                    type="hidden"
                    name="device_id"
                    :value="selectedDevice ? selectedDevice.id : ''"
                >

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['device_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600">
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div
                x-show="!selectedDevice"
                class="max-h-64 overflow-y-auto rounded-lg border border-gray-200"
            >
                <template x-if="filteredDevices().length === 0">
                    <div class="px-4 py-3 text-sm text-gray-500">
                        No available devices found.
                    </div>
                </template>

                <template x-for="device in filteredDevices()" :key="device.id">
                    <button
                        type="button"
                        @click="selectDevice(device)"
                        class="block w-full border-b border-gray-100 px-4 py-3 text-left hover:bg-blue-50"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    <span x-text="device.property_number"></span>
                                </div>

                                <div class="mt-1 text-sm text-gray-600">
                                    <span x-text="device.type"></span>
                                    <span> • </span>
                                    <span x-text="device.brand || 'No brand'"></span>

                                    <template x-if="device.model">
                                        <span>
                                            • <span x-text="device.model"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                Available
                            </span>
                        </div>
                    </button>
                </template>
            </div>

            <div
                x-show="selectedDevice"
                x-cloak
                class="rounded-lg border border-green-200 bg-green-50 p-4"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-medium text-green-700">
                            Selected Device
                        </div>

                        <div class="mt-1 font-semibold text-gray-900">
                            <span x-text="selectedDevice?.property_number"></span>
                        </div>

                        <div class="mt-1 text-sm text-gray-600">
                            <span x-text="selectedDevice?.type"></span>
                            <span> • </span>
                            <span x-text="selectedDevice?.brand || 'No brand'"></span>

                            <template x-if="selectedDevice?.model">
                                <span>
                                    • <span x-text="selectedDevice?.model"></span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="clearSelectedDevice()"
                        class="text-sm font-medium text-red-600 hover:text-red-700"
                    >
                        Remove
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="issueOpen = false"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    :disabled="!selectedDevice"
                    :class="!selectedDevice ? 'cursor-not-allowed bg-blue-300' : 'bg-blue-600 hover:bg-blue-700'"
                    class="rounded-lg px-4 py-2 text-white"
                >
                    Issue Device
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

            <input type="hidden" name="device_type_id" x-model="editDevice.device_type_id">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Device Type</label>
                    <input
                        type="text"
                        class="mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2"
                        x-model="editDevice.type_name"
                        readonly
                    >
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
                    >
                </div>

                <div x-show="isComputerType(editDevice.type_name)" x-cloak>
                    <label class="text-sm font-medium">MAC Address</label>
                    <input
                        name="mac_address"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.mac_address"
                        :disabled="!isComputerType(editDevice.type_name)"
                    >
                </div>

                <div x-show="isComputerType(editDevice.type_name)" x-cloak>
                    <label class="text-sm font-medium">Operating System</label>
                    <input
                        name="specs[os]"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.specs.os"
                        :disabled="!isComputerType(editDevice.type_name)"
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
                    <label class="text-sm font-medium">Status</label>
                    <select
                        name="status"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        x-model="editDevice.status"
                    >
                        <option value="available">Available</option>
                        <option value="issued">Issued</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="repair">Repair</option>
                        <option value="retired">Retired</option>
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

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="editOpen = false"
                >
                    Cancel
                </button>

                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
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

                <button type="submit" x-ref="confirmDeleteBtn" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Confirm
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
                    @click="deleteOpen = false"
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
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/staff/devices.blade.php ENDPATH**/ ?>