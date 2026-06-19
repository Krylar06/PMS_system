<?php $__env->startSection('title', 'QR Scanner'); ?>
<?php $__env->startSection('page_title', 'QR Scanner'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span>Scanner</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <div class="bg-white rounded shadow-sm p-6">
        <h1 class="text-2xl font-semibold mb-2">QR Scanner</h1>
        <p class="text-sm text-gray-600 mb-4">
            Use your device camera to scan a device QR code. When a valid code is detected,
            the system will open the corresponding device page automatically.
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div id="reader" class="w-full max-w-2xl border rounded bg-black/5 overflow-hidden"></div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button id="start-scanner"
                            type="button"
                            class="px-4 py-2 rounded bg-blue-600 text-white">
                        Start Scanner
                    </button>

                    <button id="stop-scanner"
                            type="button"
                            class="px-4 py-2 rounded bg-gray-900 text-white"
                            disabled>
                        Stop Scanner
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 rounded border p-4">
                <h2 class="font-semibold mb-3">Scan Result</h2>

                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-gray-500">Status</div>
                        <div id="scan-status" class="font-medium">Idle</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Scanned Value</div>
                        <div id="scan-result" class="font-medium break-all">-</div>
                    </div>

                    <div class="text-gray-600">
                        Tip: point the camera steadily at the QR code and wait for it to focus.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startBtn = document.getElementById('start-scanner');
        const stopBtn = document.getElementById('stop-scanner');
        const statusEl = document.getElementById('scan-status');
        const resultEl = document.getElementById('scan-result');

        let scanner = null;
        let isRunning = false;

        function setStatus(text) {
            statusEl.textContent = text;
        }

        function setResult(text) {
            resultEl.textContent = text || '-';
        }

        function handleScanSuccess(decodedText) {
            setStatus('QR code detected');
            setResult(decodedText);

            if (scanner && isRunning) {
                scanner.stop().then(() => {
                    isRunning = false;
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    redirectFromQr(decodedText);
                }).catch(() => {
                    redirectFromQr(decodedText);
                });
            } else {
                redirectFromQr(decodedText);
            }
        }

        function redirectFromQr(decodedText) {
            try {
                const url = new URL(decodedText, window.location.origin);

                if (url.origin === window.location.origin) {
                    window.location.href = url.href;
                    return;
                }

                setStatus('Blocked external QR URL');
            } catch (e) {
                setStatus('Invalid QR content');
            }
        }

        startBtn.addEventListener('click', function () {
            if (isRunning) return;

            scanner = new Html5Qrcode("reader");

            Html5Qrcode.getCameras().then(devices => {
                if (!devices || devices.length === 0) {
                    setStatus('No camera found');
                    return;
                }

                const backCamera = devices.find(d =>
                    d.label && d.label.toLowerCase().includes('back')
                );

                const cameraId = backCamera ? backCamera.id : devices[0].id;

                scanner.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: { width: 280, height: 280 }
                    },
                    handleScanSuccess,
                    function () {}
                ).then(() => {
                    isRunning = true;
                    setStatus('Scanning...');
                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                }).catch(err => {
                    setStatus('Unable to start scanner');
                    setResult(err);
                });
            }).catch(err => {
                setStatus('Camera access error');
                setResult(err);
            });
        });

        stopBtn.addEventListener('click', function () {
            if (!scanner || !isRunning) return;

            scanner.stop().then(() => {
                isRunning = false;
                setStatus('Scanner stopped');
                startBtn.disabled = false;
                stopBtn.disabled = true;
            }).catch(err => {
                setStatus('Error stopping scanner');
                setResult(err);
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\PMS_system\resources\views/admin/scanner.blade.php ENDPATH**/ ?>