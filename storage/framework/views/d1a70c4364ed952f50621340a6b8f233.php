<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'show' => 'open',
    'title' => 'Modal Title',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'show' => 'open',
    'title' => 'Modal Title',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    x-show="<?php echo e($show); ?>"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-modal="true"
    role="dialog"
>
    <div
        class="fixed inset-0 bg-black/40"
        @click="<?php echo e($show); ?> = false"
    ></div>

    <div class="flex min-h-full items-start justify-center p-3 sm:items-center sm:p-6">
        <div
            x-transition
            class="relative mt-6 w-full max-w-lg rounded-2xl bg-white shadow-xl ring-1 ring-gray-200 sm:mt-0 max-h-[92vh] flex flex-col"
        >
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-5">
                <h2 class="text-lg font-semibold text-gray-900"><?php echo e($title); ?></h2>

                <button
                    type="button"
                    class="rounded-lg px-2 py-1 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                    @click="<?php echo e($show); ?> = false"
                >
                    ✕
                </button>
            </div>

            <div class="overflow-y-auto p-4 sm:p-5">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\pms-system\resources\views/components/modal.blade.php ENDPATH**/ ?>