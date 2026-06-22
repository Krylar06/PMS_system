<?php $__env->startSection('title', 'Colleges'); ?>
<?php $__env->startSection('page_title', 'Colleges'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $addBag = $errors->getBag('add');
    $editBag = $errors->getBag('edit');

    $oldNames = old('names', []);
    $oldCodes = old('codes', []);
    $bulkSeedCount = $oldNames ? max(1, min(3, count($oldNames))) : 2;

    $bulkRowsSeed = [];
    for ($i = 0; $i < $bulkSeedCount; $i++) {
        $bulkRowsSeed[] = [
            'name' => $oldNames[$i] ?? '',
            'code' => $oldCodes[$i] ?? '',
            'nameError' => $addBag->first("names.$i"),
            'codeError' => $addBag->first("codes.$i"),
        ];
    }
?>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('collegeManager', () => ({
        addOpen: <?php echo e($addBag->any() ? 'true' : 'false'); ?>,
        editOpen: <?php echo e($editBag->any() ? 'true' : 'false'); ?>,
        deleteOpen: false,
        bulkEnabled: <?php echo e(old('names') !== null ? 'true' : 'false'); ?>,

        addSingle: {
            name: <?php echo \Illuminate\Support\Js::from(old('name', ''))->toHtml() ?>,
            code: <?php echo \Illuminate\Support\Js::from(old('code', ''))->toHtml() ?>,
            nameError: <?php echo \Illuminate\Support\Js::from($addBag->first('name'))->toHtml() ?>,
            codeError: <?php echo \Illuminate\Support\Js::from($addBag->first('code'))->toHtml() ?>
        },

        bulkRows: <?php echo json_encode($bulkRowsSeed, 15, 512) ?>,

        editCollege: {
            id: <?php echo \Illuminate\Support\Js::from(old('editing_id') !== null ? (int) old('editing_id') : null)->toHtml() ?>,
            name: <?php echo \Illuminate\Support\Js::from(old('name', ''))->toHtml() ?>,
            code: <?php echo \Illuminate\Support\Js::from(old('code', ''))->toHtml() ?>,
            nameError: <?php echo \Illuminate\Support\Js::from($editBag->first('name'))->toHtml() ?>,
            codeError: <?php echo \Illuminate\Support\Js::from($editBag->first('code'))->toHtml() ?>
        },
        deleteCollegeId: null,

        openAdd() {
            this.addOpen = true;
            this.bulkEnabled = false;
            this.addSingle = { name: '', code: '', nameError: '', codeError: '' };
            this.bulkRows = [
                { name: '', code: '', nameError: '', codeError: '' },
                { name: '', code: '', nameError: '', codeError: '' },
            ];
        },

        addBulkRow() {
            if (this.bulkRows.length < 3) {
                this.bulkRows.push({ name: '', code: '', nameError: '', codeError: '' });
            }
        },

        removeBulkRow() {
            if (this.bulkRows.length > 1) {
                this.bulkRows.pop();
            }
        },

        openEdit(college) {
            this.editCollege = {
                id: college.id,
                name: college.name,
                code: college.code,
                nameError: '',
                codeError: ''
            };
            this.editOpen = true;
        },

        openDelete(id) {
            this.deleteCollegeId = id;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.confirmDeleteBtn && this.$refs.confirmDeleteBtn.focus());
        }
    }));
});
</script>
<div
    x-data="collegeManager"
    class="space-y-5"
>
    

    
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Colleges</h1>
        </div>

        <button
            type="button"
            class="shrink-0 inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            @click="openAdd()"
        >
            + Add College
        </button>
    </div>

    
    <div class="grid grid-cols-1 gap-3 md:hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="space-y-3">
                    <div>
                        <a
                            class="font-semibold text-blue-700 hover:underline"
                            href="<?php echo e(route('admin.offices.index', $c)); ?>"
                        >
                            <?php echo e($c->name); ?>

                        </a>
                    </div>

                    <div class="text-sm">
                        <div class="text-gray-500">Code</div>
                        <div class="text-gray-900"><?php echo e($c->code ?: '-'); ?></div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button
                            type="button"
                            class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                            @click="openEdit({
                                id: <?php echo e($c->id); ?>,
                                name: <?php echo \Illuminate\Support\Js::from($c->name)->toHtml() ?>,
                                code: <?php echo \Illuminate\Support\Js::from($c->code ?? '')->toHtml() ?>
                            })"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                            @click="openDelete(<?php echo e($c->id); ?>)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
                No colleges found.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="hidden md:block overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Code</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a
                                    class="font-medium text-blue-700 hover:underline"
                                    href="<?php echo e(route('admin.offices.index', $c)); ?>"
                                >
                                    <?php echo e($c->name); ?>

                                </a>
                            </td>

                            <td class="px-4 py-3 text-gray-700"><?php echo e($c->code ?: '-'); ?></td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-black"
                                        @click="openEdit({
                                            id: <?php echo e($c->id); ?>,
                                            name: <?php echo \Illuminate\Support\Js::from($c->name)->toHtml() ?>,
                                            code: <?php echo \Illuminate\Support\Js::from($c->code ?? '')->toHtml() ?>
                                        })"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                        @click="openDelete(<?php echo e($c->id); ?>)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                No colleges found.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <?php echo e($colleges->links()); ?>

    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'addOpen','title' => 'Add College']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'addOpen','title' => 'Add College']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form method="POST" action="<?php echo e(route('admin.colleges.store')); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>

            <div>
                <div class="flex items-center justify-between gap-3">
                    <label class="text-sm font-medium">Bulk Add</label>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium border"
                    x-bind:class="bulkEnabled ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        @click="bulkEnabled = !bulkEnabled"
                    >
                        <span x-text="bulkEnabled ? 'ON' : 'OFF'">OFF</span>
                    </button>


                </div>
            </div>

            <div class="space-y-3">
                <!-- Bulk controls -->
                <div x-show="bulkEnabled" class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-lg bg-gray-100 px-3 py-1.5 text-gray-700 hover:bg-gray-200"
                        @click="removeBulkRow()"
                    >-
                    </button>

                    <input type="hidden" name="count" :value="bulkRows.length">

                    <div class="text-sm text-gray-700">
                        Records: <span class="font-semibold" x-text="bulkRows.length"></span>
                    </div>

                    <button
                        type="button"
                        class="rounded-lg bg-gray-100 px-3 py-1.5 text-gray-700 hover:bg-gray-200"
                        @click="addBulkRow()"
                    >+
                    </button>
                </div>

                <!-- Bulk form -->
                <template x-if="bulkEnabled">
                    <div class="space-y-4">
                        <template x-for="(row, idx) in bulkRows" :key="idx">
                            <div class="space-y-2 rounded-lg border border-gray-200 p-3 bg-gray-50">
                                <div class="text-xs font-semibold text-gray-600" x-text="`College ${idx + 1}`"></div>

                                <div>
                                    <label class="text-sm font-medium">College Name</label>
                                    <input
                                        :name="`names[${idx}]`"
                                        x-model="row.name"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                        required
                                        :placeholder="`e.g. College ${idx + 1}`"
                                    >
                                    <div class="mt-1 text-sm text-red-600" x-show="row.nameError" x-text="row.nameError"></div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Code (optional)</label>
                                    <input
                                        :name="`codes[${idx}]`"
                                        x-model="row.code"
                                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                        placeholder="Optional (unique)"
                                    >
                                    <div class="mt-1 text-sm text-red-600" x-show="row.codeError" x-text="row.codeError"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Single form -->
                <template x-if="!bulkEnabled">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium">College Name</label>
                            <input
                                name="name"
                                x-model="addSingle.name"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                required
                                placeholder="Enter college name"
                            >
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.nameError" x-text="addSingle.nameError"></div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Code (optional)</label>
                            <input
                                name="code"
                                x-model="addSingle.code"
                                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                                placeholder="Optional (unique)"
                            >
                            <div class="mt-1 text-sm text-red-600" x-show="addSingle.codeError" x-text="addSingle.codeError"></div>
                        </div>
                    </div>
                </template>
            </div>


            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="addOpen = false">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'editOpen','title' => 'Edit College']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'editOpen','title' => 'Edit College']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form
            method="POST"
            action="<?php echo e(route('admin.colleges.update', '__ID__')); ?>"
            x-bind:action="'<?php echo e(route('admin.colleges.update', '__ID__')); ?>'.replace('__ID__', editCollege.id)"
            class="space-y-3"
        >
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <input type="hidden" name="editing_id" :value="editCollege.id">

            <div>
                <label class="text-sm font-medium">College Name</label>
                <input
                    name="name"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editCollege.name"
                    required
                >
                <div class="mt-1 text-sm text-red-600" x-show="editCollege.nameError" x-text="editCollege.nameError"></div>
            </div>

            <div>
                <label class="text-sm font-medium">Code (optional)</label>
                <input
                    name="code"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                    x-model="editCollege.code"
                >
                <div class="mt-1 text-sm text-red-600" x-show="editCollege.codeError" x-text="editCollege.codeError"></div>
            </div>

            <div class="flex gap-2 pt-2">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Update</button>
                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="editOpen = false">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['show' => 'deleteOpen','title' => 'Delete College']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show' => 'deleteOpen','title' => 'Delete College']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="space-y-3">
            <div class="text-sm text-gray-700">
                Are you sure you want to delete this college?
            </div>



            <form
                method="POST"
                :action="`<?php echo e(route('admin.colleges.destroy', ['college' => '__ID__'])); ?>`.replace('__ID__', deleteCollegeId)"
                @submit="if (!deleteCollegeId) $event.preventDefault()"
                class="flex gap-2"
            >

                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button type="submit" x-ref="confirmDeleteBtn" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Confirm</button>

                <button type="button" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200" @click="deleteOpen = false">
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
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/colleges/index.blade.php ENDPATH**/ ?>