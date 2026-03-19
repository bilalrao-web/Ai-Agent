<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoiceAI – Intelligent Voice Agent Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:      #080c10;
            --surface: #0e1419;
            --border:  #1a2332;
            --accent:  #00d4ff;
            --accent2: #7b61ff;
            --green:   #00ff88;
            --text:    #e8edf2;
            --muted:   #5a6a7a;
            --card:    #111820;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.35;
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 20px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            background: rgba(8,12,16,0.75);
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
        }

        .logo-dot {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            flex-shrink: 0;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0,212,255,.4); }
            50%      { transform: scale(1.3); box-shadow: 0 0 0 6px rgba(0,212,255,0); }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all .2s;
            font-weight: 500;
        }

        .nav-links a:hover { color: var(--text); background: var(--border); }

        .btn-nav {
            background: var(--accent) !important;
            color: var(--bg) !important;
            font-weight: 600 !important;
        }

        .btn-nav:hover { opacity: .9; transform: translateY(-1px); }

        .menu-btn {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 6px;
            border: none;
            background: none;
        }

        .menu-btn span {
            display: block;
            width: 22px; height: 2px;
            background: var(--text);
            border-radius: 2px;
            transition: all .3s;
        }

        .menu-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .menu-btn.open span:nth-child(2) { opacity: 0; }
        .menu-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .mobile-nav {
            display: none;
            position: fixed;
            top: 65px; left: 0; right: 0;
            background: rgba(8,12,16,.97);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(20px);
            z-index: 99;
            padding: 16px 24px 24px;
            flex-direction: column;
            gap: 8px;
            transform: translateY(-110%);
            transition: transform .3s ease;
        }

        .mobile-nav.open { transform: translateY(0); }

        .mobile-nav a {
            color: var(--muted);
            text-decoration: none;
            font-size: 16px;
            padding: 12px 16px;
            border-radius: 8px;
            transition: all .2s;
            font-weight: 500;
        }

        .mobile-nav a:hover { color: var(--text); background: var(--border); }

        .mobile-nav .btn-nav-m {
            background: var(--accent);
            color: var(--bg) !important;
            font-weight: 600;
            text-align: center;
            margin-top: 4px;
        }

        /* HERO */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 24px 80px;
            position: relative;
            text-align: center;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 25%; left: 50%;
            transform: translateX(-50%);
            width: min(600px, 90vw);
            height: min(600px, 90vw);
            background: radial-gradient(circle, rgba(0,212,255,.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-inner { position: relative; z-index: 1; max-width: 700px; width: 100%; }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 28px;
            background: var(--surface);
            animation: fadeUp .6s ease both;
        }

        .badge-dot {
            width: 6px; height: 6px;
            background: var(--green);
            border-radius: 50%;
            flex-shrink: 0;
            animation: pulse 2s infinite;
        }

        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(40px, 10vw, 92px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 20px;
            animation: fadeUp .6s ease .1s both;
        }

        .gradient {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: clamp(15px, 3vw, 18px);
            color: var(--muted);
            max-width: 480px;
            margin: 0 auto 40px;
            line-height: 1.65;
            font-weight: 300;
            animation: fadeUp .6s ease .2s both;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp .6s ease .3s both;
        }

        .btn {
            padding: 14px 26px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            display: inline-block;
        }

        .btn-primary { background: var(--accent); color: var(--bg); border: none; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,212,255,.3); }

        .btn-secondary { background: transparent; color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { border-color: var(--muted); transform: translateY(-2px); }

        /* WAVEFORM */
        .waveform {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin: 56px auto 0;
            height: 56px;
            animation: fadeUp .6s ease .4s both;
        }

        .wave-bar {
            width: 3px;
            background: linear-gradient(to top, var(--accent), var(--accent2));
            border-radius: 10px;
            animation: wave 1.4s ease-in-out infinite;
            opacity: .7;
        }

        .w1{height:14px;animation-delay:0s}.w2{height:26px;animation-delay:.1s}
        .w3{height:38px;animation-delay:.2s}.w4{height:50px;animation-delay:.3s}
        .w5{height:34px;animation-delay:.4s}.w6{height:46px;animation-delay:.5s}
        .w7{height:56px;animation-delay:.6s}.w8{height:42px;animation-delay:.7s}
        .w9{height:52px;animation-delay:.8s}.w10{height:30px;animation-delay:.9s}
        .w11{height:46px;animation-delay:1s}.w12{height:22px;animation-delay:1.1s}
        .w13{height:38px;animation-delay:1.2s}.w14{height:18px;animation-delay:1.3s}
        .w15{height:34px;animation-delay:.05s}

        @keyframes wave {
            0%,100% { transform: scaleY(.4); opacity: .4; }
            50%      { transform: scaleY(1);  opacity: 1; }
        }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(4,1fr);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            margin: 0 48px;
        }

        .stat {
            padding: 44px 20px;
            text-align: center;
            border-right: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            transition: background .3s;
        }

        .stat:last-child { border-right: none; }

        .stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,rgba(0,212,255,.03),transparent);
            opacity: 0;
            transition: opacity .3s;
        }

        .stat:hover::before { opacity: 1; }

        .stat-number {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px,4vw,48px);
            font-weight: 800;
            background: linear-gradient(135deg,var(--accent),var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* FEATURES */
        .features {
            padding: 100px 48px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--accent);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(26px,4vw,44px);
            font-weight: 800;
            letter-spacing: -1.5px;
            margin-bottom: 52px;
            max-width: 360px;
            line-height: 1.1;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .feature-card {
            background: var(--card);
            padding: 34px 28px;
            transition: background .3s;
        }

        .feature-card:hover { background: #141c26; }

        .feature-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
            background: var(--border);
        }

        .feature-title {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .feature-desc { font-size: 13px; color: var(--muted); line-height: 1.6; }

        /* FLOW */
        .flow {
            padding: 80px 24px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .flow-inner { max-width: 900px; margin: 0 auto; text-align: center; }

        .flow-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 52px;
            flex-wrap: wrap;
            gap: 4px;
            row-gap: 20px;
        }

        .flow-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 80px;
        }

        .step-icon {
            width: 52px; height: 52px;
            border-radius: 50%;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: var(--card);
            transition: all .3s;
        }

        .flow-step:hover .step-icon {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(0,212,255,.2);
        }

        .step-label { font-size: 12px; color: var(--muted); text-align: center; font-weight: 500; line-height: 1.3; }

        .flow-arrow { font-size: 16px; color: var(--border); flex-shrink: 0; padding: 0 4px; margin-bottom: 22px; }

        /* CTA */
        .cta {
            padding: 110px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            width: min(800px,100%);
            height: 400px;
            background: radial-gradient(ellipse,rgba(123,97,255,.07) 0%,transparent 70%);
        }

        .cta-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px,6vw,56px);
            font-weight: 800;
            letter-spacing: -2px;
            margin-bottom: 14px;
            position: relative;
        }

        .cta-sub { color: var(--muted); font-size: 16px; margin-bottom: 36px; position: relative; }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 28px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        footer p { font-size: 13px; color: var(--muted); }

        .footer-links { display: flex; gap: 20px; flex-wrap: wrap; }

        .footer-links a {
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            transition: color .2s;
        }

        .footer-links a:hover { color: var(--text); }

        /* ANIMATIONS */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .reveal { opacity:0; transform:translateY(24px); transition:all .6s ease; }
        .reveal.visible { opacity:1; transform:translateY(0); }

        /* ══════════════════════════
           RESPONSIVE BREAKPOINTS
        ══════════════════════════ */

        /* Tablet ≤ 900px */
        @media (max-width: 900px) {
            .stats { margin: 0 24px; }
            .features { padding: 80px 32px; }
            .features-grid { grid-template-columns: repeat(2,1fr); }
        }

        /* Tablet ≤ 768px */
        @media (max-width: 768px) {
            nav { padding: 16px 20px; }
            .nav-links { display: none; }
            .menu-btn { display: flex; }
            .mobile-nav { display: flex; }

            .hero { padding: 100px 20px 60px; }
            .hero-title { letter-spacing: -1px; }
            .waveform { height: 44px; margin-top: 40px; }

            .stats {
                grid-template-columns: repeat(2,1fr);
                margin: 0 20px;
            }

            .stat { padding: 30px 16px; border-bottom: 1px solid var(--border); }
            .stat:nth-child(even) { border-right: none; }
            .stat:nth-child(3),
            .stat:nth-child(4) { border-bottom: none; }

            .features { padding: 60px 20px; }
            .section-title { max-width: 100%; }

            .flow { padding: 60px 20px; }
            .flow-arrow { display: none; }
            .flow-steps { gap: 16px; row-gap: 24px; }
            .flow-step { flex: 0 0 calc(33.33% - 12px); min-width: 80px; }

            .cta { padding: 80px 20px; }
            footer { padding: 24px 20px; justify-content: center; text-align: center; }
            .footer-links { justify-content: center; }
        }

        /* Mobile ≤ 540px */
        @media (max-width: 540px) {
            .hero-badge { font-size: 11px; padding: 5px 12px; }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .hero-actions .btn {
                width: 100%;
                max-width: 280px;
                text-align: center;
            }

            .waveform { gap: 3px; }
            .wave-bar { width: 2.5px; }

            .stats { grid-template-columns: repeat(2,1fr); margin: 0 16px; }
            .stat { padding: 24px 12px; }

            .features { padding: 50px 16px; }
            .features-grid { grid-template-columns: 1fr; }
            .feature-card { padding: 28px 22px; }

            .flow-step { flex: 0 0 calc(50% - 10px); }

            .cta-title { letter-spacing: -1px; }
            .cta .hero-actions { flex-direction: column; align-items: center; }
            .cta .hero-actions .btn { width: 100%; max-width: 280px; text-align: center; }

            footer { flex-direction: column; gap: 14px; padding: 20px 16px; }
        }

        /* Small mobile ≤ 380px */
        @media (max-width: 380px) {
            .logo { font-size: 17px; }
            nav { padding: 14px 16px; }
            .hero-title { letter-spacing: -.5px; }
            .stat-number { font-size: 26px; }
            .step-icon { width: 44px; height: 44px; font-size: 18px; }
            .flow-step { flex: 0 0 calc(50% - 8px); }
        }
    </style>
</head>
<body>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
    <a href="#features">Features</a>
    <a href="#flow">How it Works</a>
    <a href="/portal">Customer Portal</a>
    <a href="/admin" class="btn-nav-m">Admin Panel</a>
</div>

<!-- Nav -->
<nav>
    <a href="/" class="logo">
        <div class="logo-dot"></div>
        VoiceAI
    </a>
    <div class="nav-links">
        <a href="#features">Features</a>
        <a href="#flow">How it Works</a>
        <a href="/portal">Portal</a>
        <a href="/admin" class="btn-nav">Admin Panel</a>
    </div>
    <button class="menu-btn" id="menuBtn" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <div class="badge-dot"></div>
            System Online — AI Agent Active
        </div>
        <h1 class="hero-title">
            Voice Support<br>
            <span class="gradient">Reimagined.</span>
        </h1>
        <p class="hero-sub">
            AI-powered voice agent that handles customer queries 24/7 — no wait times, no hold music.
        </p>
        <div class="hero-actions">
            <a href="/admin" class="btn btn-primary">Open Admin Panel</a>
            <a href="/portal" class="btn btn-secondary">Customer Portal</a>
        </div>
        <div class="waveform">
            <div class="wave-bar w1"></div><div class="wave-bar w2"></div>
            <div class="wave-bar w3"></div><div class="wave-bar w4"></div>
            <div class="wave-bar w5"></div><div class="wave-bar w6"></div>
            <div class="wave-bar w7"></div><div class="wave-bar w8"></div>
            <div class="wave-bar w9"></div><div class="wave-bar w10"></div>
            <div class="wave-bar w11"></div><div class="wave-bar w12"></div>
            <div class="wave-bar w13"></div><div class="wave-bar w14"></div>
            <div class="wave-bar w15"></div>
        </div>
    </div>
</section>

<!-- Stats -->
<div class="stats">
    <div class="stat reveal"><div class="stat-number">24/7</div><div class="stat-label">Always Online</div></div>
    <div class="stat reveal"><div class="stat-number">&lt;5s</div><div class="stat-label">Response Time</div></div>
    <div class="stat reveal"><div class="stat-number">100%</div><div class="stat-label">Calls Logged</div></div>
    <div class="stat reveal"><div class="stat-number">AI</div><div class="stat-label">Powered by Gemini</div></div>
</div>

<!-- Features -->
<section class="features" id="features">
    <div class="section-label">Capabilities</div>
    <h2 class="section-title">Everything you need</h2>
    <div class="features-grid">
        <div class="feature-card reveal">
            <div class="feature-icon">📞</div>
            <div class="feature-title">Inbound Call Handling</div>
            <div class="feature-desc">Receives and processes voice calls via Twilio with real-time speech recognition.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon">🧠</div>
            <div class="feature-title">AI Conversation Engine</div>
            <div class="feature-desc">Gemini AI understands context, remembers conversation history, and responds naturally.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon">📦</div>
            <div class="feature-title">Order Status</div>
            <div class="feature-desc">Instantly retrieves and communicates order and delivery information to customers.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon">🎫</div>
            <div class="feature-title">Ticket Management</div>
            <div class="feature-desc">Creates and tracks support tickets automatically from voice interactions.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon">📊</div>
            <div class="feature-title">Admin Dashboard</div>
            <div class="feature-desc">Full visibility into all calls, transcripts, and conversation logs in real-time.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon">🔐</div>
            <div class="feature-title">Role Based Access</div>
            <div class="feature-desc">Granular permissions for admins, support agents, and customers via Spatie.</div>
        </div>
    </div>
</section>

<!-- Flow -->
<section class="flow" id="flow">
    <div class="flow-inner">
        <div class="section-label">How it Works</div>
        <h2 class="section-title" style="max-width:100%">Call → AI → Response</h2>
        <div class="flow-steps">
            <div class="flow-step reveal"><div class="step-icon">📱</div><div class="step-label">Customer<br>Calls</div></div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal"><div class="step-icon">📡</div><div class="step-label">Twilio<br>Receives</div></div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal"><div class="step-icon">🎙️</div><div class="step-label">Speech<br>to Text</div></div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal"><div class="step-icon">🧠</div><div class="step-label">Gemini<br>Processes</div></div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal"><div class="step-icon">🔊</div><div class="step-label">Voice<br>Response</div></div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal"><div class="step-icon">💾</div><div class="step-label">Logged<br>to DB</div></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <h2 class="cta-title reveal">Ready to get started?</h2>
    <p class="cta-sub reveal">Access your admin panel to monitor calls and manage the system.</p>
    <div class="hero-actions reveal">
        <a href="/admin" class="btn btn-primary">Go to Admin Panel</a>
        <a href="/portal" class="btn btn-secondary">Customer Portal</a>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>© 2025 VoiceAI – AI Voice Agent Platform</p>
    <div class="footer-links">
        <a href="/admin">Admin</a>
        <a href="/portal">Portal</a>
        <a href="/ai-demo">Demo</a>
    </div>
</footer>

<script>
    // Mobile menu
    const menuBtn = document.getElementById('menuBtn');
    const mobileNav = document.getElementById('mobileNav');

    menuBtn.addEventListener('click', () => {
        menuBtn.classList.toggle('open');
        mobileNav.classList.toggle('open');
    });

    mobileNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            menuBtn.classList.remove('open');
            mobileNav.classList.remove('open');
        });
    });

    // Scroll reveal
    const observer = new IntersectionObserver(entries => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
</body>
</html>
