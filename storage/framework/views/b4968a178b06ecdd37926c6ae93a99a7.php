<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <title>Login</title>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <form method="POST" action="<?php echo e(route('login.submit')); ?>" class="bg-white p-6 rounded-lg shadow w-full max-w-sm">
        <?php echo csrf_field(); ?>

        <h1 class="text-xl font-semibold mb-4">Admin Login</h1>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <div class="text-sm text-green-600 bg-green-50 border border-green-200 rounded px-3 py-2 mb-3">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="text-sm text-red-600 bg-red-50 border border-red-200 rounded px-3 py-2 mb-3">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <label class="block mb-2 text-sm">Email</label>
        <input
            name="email"
            type="email"
            value="<?php echo e(old('email')); ?>"
            class="w-full border rounded px-3 py-2 mb-3"
            required
            autofocus
        >

        <label class="block mb-2 text-sm">Password</label>
        <input
            name="password"
            type="password"
            class="w-full border rounded px-3 py-2 mb-2"
            required
        >

        <div class="mb-3 text-right">
            <a href="<?php echo e(route('password.request')); ?>" class="text-sm text-blue-600 hover:underline">
                Forgot Password?
            </a>
        </div>

        <button type="submit" class="w-full bg-black text-white rounded px-3 py-2">
            Login
        </button>
    </form>
</body>
</html><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/auth/login.blade.php ENDPATH**/ ?>