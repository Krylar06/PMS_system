<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PMS System – Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0d1b3e;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1;
            background: linear-gradient(145deg, #0a1628 0%, #0d2155 50%, #0a1a45 100%);
            display: flex;
            flex-direction: column;
            padding: 40px 48px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        /* subtle dot-grid overlay */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(99,160,255,0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* glowing orb behind illustration */
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
            width: 420px;
            height: 220px;
            background: radial-gradient(ellipse, rgba(59,130,246,0.28) 0%, transparent 70%);
            pointer-events: none;
        }

        /* brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon svg { color: #fff; }

        .brand-text { line-height: 1.2; }
        .brand-name  { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: .5px; }
        .brand-sub   { font-size: 10px; font-weight: 500; color: #93c5fd; letter-spacing: 1.5px; text-transform: uppercase; }

        /* hero copy */
        .hero {
            margin-top: 56px;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 34px;
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
        }

        .hero h1 span { color: #60a5fa; }

        .hero p {
            margin-top: 16px;
            font-size: 14px;
            color: #93c5fd;
            line-height: 1.7;
            max-width: 360px;
        }

        /* illustration placeholder — rings + device icons */
        .illustration {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .ring-wrap {
            position: relative;
            width: 360px;
            height: 260px;
        }

        /* elliptical rings */
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(99,160,255,0.25);
        }
        .ring-1 { width: 360px; height: 130px; top: 80px; left: 0; }
        .ring-2 { width: 280px; height: 100px; top: 90px; left: 40px; }

        /* center laptop silhouette */
        .laptop-wrap {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .laptop { font-size: 72px; filter: drop-shadow(0 0 24px rgba(59,130,246,0.55)); }

        /* floating device bubbles */
        .bubble {
            position: absolute;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(99,160,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(6px);
        }

        .b-printer  { top: 60px;  left: 20px;  }
        .b-monitor  { top: 10px;  left: 120px; }
        .b-print2   { top: 0px;   right: 80px; }
        .b-qr       { top: 55px;  right: 20px; }
        .b-gear     { bottom: 30px; right: 40px; }
        .b-phone    { bottom: 10px; left: 60px; }

        /* bottom feature strip */
        .features {
            display: flex;
            gap: 32px;
            position: relative;
            z-index: 1;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .feature { display: flex; gap: 10px; align-items: flex-start; }
        .feature-icon { font-size: 20px; margin-top: 2px; }
        .feature-title { font-size: 13px; font-weight: 600; color: #fff; }
        .feature-desc  { font-size: 11px; color: #93c5fd; margin-top: 2px; line-height: 1.5; }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 480px;
            min-height: 100vh;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10);
        }

        /* card logo */
        .card-logo {
            width: 64px;
            height: 64px;
            background: #f0f4ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .card-logo svg { color: #2563eb; }

        /* headings */
        .card h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
        }

        .card .subtitle {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            margin-top: 6px;
            margin-bottom: 36px;
        }

        /* alerts */
        .alert-success {
            font-size: 13px;
            color: #15803d;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        .alert-error {
            font-size: 13px;
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        /* field */
        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            display: flex;
        }

        .input-wrap input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 13px 42px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .input-wrap input::placeholder { color: #94a3b8; }
        .input-wrap input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
            background: #fff;
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            padding: 0;
        }

        /* remember + forgot */
        .row-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .forgot {
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot:hover { text-decoration: underline; }

        /* submit */
        .btn-login {
            width: 100%;
            background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            letter-spacing: .3px;
            transition: opacity .2s, transform .15s;
        }

        .btn-login:hover  { opacity: .92; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        /* ── responsive ── */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; }
        }
    </style>
</head>
<body>

    <!-- ════════════ LEFT PANEL ════════════ -->
    <div class="left-panel">

        <!-- Brand -->
        <div class="brand">
            <div class="brand-icon">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div class="brand-text">
                <div class="brand-name">PMS SYSTEM</div>
                <div class="brand-sub">Device Management System</div>
            </div>
        </div>

        <!-- Hero copy -->
        <div class="hero">
            <h1>Preventive Maintenance and<br>Your <span>Asset Monitoring System</span></h1>
            <p>Efficiently manage, monitor, and maintain all devices in your organization through a centralized and secure system.</p>
        </div>

        <!-- Illustration -->
        <div class="illustration">
            <div class="ring-wrap">
                <div class="ring ring-1"></div>
                <div class="ring ring-2"></div>

                <div class="bubble b-printer">🖨️</div>
                <div class="bubble b-monitor">🖥️</div>
                <div class="bubble b-print2">🖨️</div>
                <div class="bubble b-qr">📱</div>
                <div class="bubble b-gear">⚙️</div>
                <div class="bubble b-phone">📲</div>

                <div class="laptop-wrap">
                    <div class="laptop">💻</div>
                </div>
            </div>
        </div>

        <!-- Feature strip -->
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🛡️</div>
                <div>
                    <div class="feature-title">Secure</div>
                    <div class="feature-desc">Your data is safe<br>with us</div>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <div>
                    <div class="feature-title">Efficient</div>
                    <div class="feature-desc">Streamline your<br>workflows</div>
                </div>
            </div>
            <div class="feature">
                <div class="feature-icon">🕐</div>
                <div>
                    <div class="feature-title">Real-time</div>
                    <div class="feature-desc">Get updates and<br>insights instantly</div>
                </div>
            </div>
        </div>

    </div><!-- /left-panel -->


    <!-- ════════════ RIGHT PANEL ════════════ -->
    <div class="right-panel">
        <div class="card">

            <!-- Logo mark -->
            <div class="card-logo">
                <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>

            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to continue to PMS System</p>

            <!-- Flash messages -->
            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <!-- Email -->
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 11V7a4 4 0 10-8 0v4M5 11h14a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1v-7a1 1 0 011-1z"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Enter your password"
                            required
                        >
                        <button type="button" class="toggle-pw" onclick="
                            const f = document.getElementById('password');
                            f.type = f.type === 'password' ? 'text' : 'password';
                        ">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember me / Forgot password -->
                <div class="row-options">
                    <label class="remember">
                        <input type="checkbox" name="remember" checked>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login">
                    Login
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>

            </form>
        </div>
    </div><!-- /right-panel -->

</body>
</html>