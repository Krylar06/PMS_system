import './bootstrap';

// NOTE: Do NOT manually import/start Alpine.js here.
// Livewire (v3/v4) already bundles and auto-starts its own Alpine instance
// via the @livewireScripts directive in resources/views/admin/layouts/app.blade.php.
// Starting a second Alpine instance on top of it causes two independent
// reactive runtimes to fight over the same DOM: click handlers fire against
// one instance while x-bind/:action bindings are read from the other, which
// is exactly what produced the "bulk toggle does nothing" and
// "delete confirmation not submitting" bugs on the Colleges page.