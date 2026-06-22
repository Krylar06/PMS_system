<?php $__env->startSection('title', 'Staff'); ?>
<?php $__env->startSection('page_title', 'Staff'); ?>

<?php $__env->startSection('content'); ?>
<div
    x-data="{
        addOpen:false,
        editOpen:false,
        deleteOpen:false,

        editStaff:{
            id:null,
            first_name:'',
            last_name:'',
            position:'',
            email:'',
            phone:'',
            is_active:true
        },

        deleteStaffId:null,

        openEdit(staff){
            this.editStaff = {
                id: staff.id,
                first_name: staff.first_name ?? '',
                last_name: staff.last_name ?? '',
                position: staff.position ?? '',
                email: staff.email ?? '',
                phone: staff.phone ?? '',
                is_active: !!staff.is_active
            }
            this.editOpen = true
        },

        openDelete(id){
            this.deleteStaffId = id
            this.deleteOpen = true
        }
    }"
    class="space-y-5"
>
    
    <div class="text-sm text-gray-500 leading-6 break-words">
        <a class="text-blue-600 hover:underline" href="<?php echo e(route('admin.colleges.index')); ?>">Colleges</a>
        <span class="mx-1">/</span>
        <a class="text-blue-600 hover:underline" href="<?php echo e(route('admin.offices.index', $office->college)); ?>">
            <?php echo e($office->college->name); ?>

        </a>
        <span class="mx-1">/</span>
        <span class="text-gray-700 font-medium"><?php echo e($office->name); ?></span>
        <span class="mx-1">/</span>
        <span>Staff</span>
    </div>

    
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Staff in <?php echo e($office->name); ?></h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
                href="<?php echo e(route('admin.offices.preventiveMaintenance.export', $office)); ?>"
                class="shrink-0 inline-flex items-center rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
            >
                Export Excel Report
            </a>

            <button
                type="button"
                class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                @click="addOpen = true"
            >
                + Add Staff
            </button>
        </div>
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

    
    <div class="grid grid-cols-1 gap-3 md:hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <a
                            class="font-semibold text-blue-700 hover:underline"
                            href="<?php echo e(route('admin.staff.devices.index', $s)); ?>"
                        >
                            <?php echo e($s->last_name); ?>, <?php echo e($s->first_name); ?>

                        </a>

                        <div class="mt-1 text-sm text-gray-500">
                            <?php echo e($s->position ?: 'No position set'); ?>

                        </div>
                    </div>

                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->is_active): ?>
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Active
                            </span>
                        <?php else: ?>
                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                Inactive
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="break-all text-gray-900"><?php echo e($s->email ?: '-'); ?></div>
                    </div>

                    <div>
                        <div class="text-gray-500">Phone</div>
                        <div class="text-gray-900"><?php echo e($s->phone ?: '-'); ?></div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a
                        href="<?php echo e(route('admin.staff.devices.index', $s)); ?>"
                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                    >
                        Devices
                    </a>

                    <button
                        type="button"
                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                        @click="openEdit({
                            id: <?php echo e($s->id); ?>,
                            first_name: <?php echo \Illuminate\Support\Js::from($s->first_name)->toHtml() ?>,
                            last_name: <?php echo \Illuminate\Support\Js::from($s->last_name)->toHtml() ?>,
                            position: <?php echo \Illuminate\Support\Js::from($s->position ?? '')->toHtml() ?>,
                            email: <?php echo \Illuminate\Support\Js::from($s->email ?? '')->toHtml() ?>,
                            phone: <?php echo \Illuminate\Support\Js::from($s->phone ?? '')->toHtml() ?>,
                            is_active: <?php echo e($s->is_active ? 'true' : 'false'); ?>

                        })"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                        @click="openDelete(<?php echo e($s->id); ?>)"
                    >
                        Delete
                    </button>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No staff found.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Position</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Phone</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a
                                    class="font-medium text-blue-700 hover:underline"
                                    href="<?php echo e(route('admin.staff.devices.index', $s)); ?>"
                                >
                                    <?php echo e($s->last_name); ?>, <?php echo e($s->first_name); ?>

                                </a>
                            </td>

                            <td class="px-4 py-3 text-gray-700"><?php echo e($s->position ?: '-'); ?></td>
                            <td class="px-4 py-3 text-gray-700"><?php echo e($s->email ?: '-'); ?></td>
                            <td class="px-4 py-3 text-gray-700"><?php echo e($s->phone ?: '-'); ?></td>

                            <td class="px-4 py-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->is_active): ?>
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        Inactive
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a
                                        href="<?php echo e(route('admin.staff.devices.index', $s)); ?>"
                                        class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700"
                                    >
                                        Devices
                                    </a>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: <?php echo e($s->id); ?>,
                                            first_name: <?php echo \Illuminate\Support\Js::from($s->first_name)->toHtml() ?>,
                                            last_name: <?php echo \Illuminate\Support\Js::from($s->last_name)->toHtml() ?>,
                                            position: <?php echo \Illuminate\Support\Js::from($s->position ?? '')->toHtml() ?>,
                                            email: <?php echo \Illuminate\Support\Js::from($s->email ?? '')->toHtml() ?>,
                                            phone: <?php echo \Illuminate\Support\Js::from($s->phone ?? '')->toHtml() ?>,
                                            is_active: <?php echo e($s->is_active ? 'true' : 'false'); ?>

                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        @click="openDelete(<?php echo e($s->id); ?>)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No staff found.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <?php echo e($staff->links()); ?>

    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'addOpen','title' => 'Add Staff']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'addOpen','title' => 'Add Staff']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.staff.store', $office)); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>

            <div>
                <label class="text-sm font-medium">First Name</label>
                <input name="first_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
            </div>

            <div>
                <label class="text-sm font-medium">Last Name</label>
                <input name="last_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
            </div>

            <div>
                <label class="text-sm font-medium">Position</label>
                <input name="position" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input name="email" type="email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Phone</label>
                <input name="phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked>
                Active
            </label>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="addOpen=false">Cancel</button>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'editOpen','title' => 'Edit Staff']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'editOpen','title' => 'Edit Staff']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form
            method="POST"
            :action="`<?php echo e(url('/offices/'.$office->id.'/staff')); ?>/${editStaff.id}`"
            class="space-y-3"
        >
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="text-sm font-medium">First Name</label>
                <input name="first_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.first_name" required>
            </div>

            <div>
                <label class="text-sm font-medium">Last Name</label>
                <input name="last_name" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.last_name" required>
            </div>

            <div>
                <label class="text-sm font-medium">Position</label>
                <input name="position" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.position">
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input name="email" type="email" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.email">
            </div>

            <div>
                <label class="text-sm font-medium">Phone</label>
                <input name="phone" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" x-model="editStaff.phone">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" x-model="editStaff.is_active">
                Active
            </label>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="editOpen=false">Cancel</button>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'deleteOpen','title' => 'Delete Staff']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'deleteOpen','title' => 'Delete Staff']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this staff member?
            </div>

            <form
                method="POST"
                :action="`<?php echo e(url('/offices/'.$office->id.'/staff')); ?>/${deleteStaffId}`"
                class="flex gap-2"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Yes, Delete</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="deleteOpen=false">Cancel</button>
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
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/staff/index.blade.php ENDPATH**/ ?>