<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoiceAI – Intelligent Voice Agent Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: #080c10;
            --surface: #0e1419;
            --border: #1a2332;
            --accent: #00d4ff;
            --accent2: #7b61ff;
            --green: #00ff88;
            --text: #e8edf2;
            --muted: #5a6a7a;
            --card: #111820;
        }

        html { scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            overflow-x: hidden;
            cursor: none;
        }

        /* Custom Cursor */
        .cursor {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.1s;
        }
        .cursor-ring {
            width: 32px; height: 32px;
            border: 1px solid rgba(0, 212, 255, 0.4);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9998;
            transition: all 0.15s ease;
        }

        /* Noise overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
            opacity: 0.4;
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 24px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            background: rgba(8, 12, 16, 0.7);
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-dot {
            width: 8px; height: 8px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(0,212,255,0.4); }
            50% { transform: scale(1.2); opacity: 0.8; box-shadow: 0 0 0 6px rgba(0,212,255,0); }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .nav-links a:hover { color: var(--text); background: var(--border); }

        .btn-nav {
            background: var(--accent) !important;
            color: var(--bg) !important;
            font-weight: 600 !important;
        }

        .btn-nav:hover { opacity: 0.9; transform: translateY(-1px); }

        /* HERO */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 48px 80px;
            position: relative;
            text-align: center;
        }

        /* Radial glow */
        .hero::after {
            content: '';
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0,212,255,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 32px;
            background: var(--surface);
            animation: fadeUp 0.6s ease both;
        }

        .badge-dot {
            width: 6px; height: 6px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(48px, 8vw, 96px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -3px;
            margin-bottom: 24px;
            animation: fadeUp 0.6s ease 0.1s both;
        }

        .hero-title .line2 {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: 18px;
            color: var(--muted);
            max-width: 500px;
            margin: 0 auto 48px;
            line-height: 1.6;
            font-weight: 300;
            animation: fadeUp 0.6s ease 0.2s both;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            animation: fadeUp 0.6s ease 0.3s both;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: none;
            transition: all 0.2s;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--bg);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,212,255,0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--muted);
            transform: translateY(-2px);
        }

        /* WAVEFORM */
        .waveform {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin: 64px auto 0;
            height: 60px;
            animation: fadeUp 0.6s ease 0.4s both;
        }

        .wave-bar {
            width: 3px;
            background: linear-gradient(to top, var(--accent), var(--accent2));
            border-radius: 10px;
            animation: wave 1.4s ease-in-out infinite;
            opacity: 0.7;
        }

        .wave-bar:nth-child(1)  { height: 16px; animation-delay: 0s; }
        .wave-bar:nth-child(2)  { height: 28px; animation-delay: 0.1s; }
        .wave-bar:nth-child(3)  { height: 40px; animation-delay: 0.2s; }
        .wave-bar:nth-child(4)  { height: 52px; animation-delay: 0.3s; }
        .wave-bar:nth-child(5)  { height: 36px; animation-delay: 0.4s; }
        .wave-bar:nth-child(6)  { height: 48px; animation-delay: 0.5s; }
        .wave-bar:nth-child(7)  { height: 60px; animation-delay: 0.6s; }
        .wave-bar:nth-child(8)  { height: 44px; animation-delay: 0.7s; }
        .wave-bar:nth-child(9)  { height: 56px; animation-delay: 0.8s; }
        .wave-bar:nth-child(10) { height: 32px; animation-delay: 0.9s; }
        .wave-bar:nth-child(11) { height: 48px; animation-delay: 1.0s; }
        .wave-bar:nth-child(12) { height: 24px; animation-delay: 1.1s; }
        .wave-bar:nth-child(13) { height: 40px; animation-delay: 1.2s; }
        .wave-bar:nth-child(14) { height: 20px; animation-delay: 1.3s; }
        .wave-bar:nth-child(15) { height: 36px; animation-delay: 0.05s; }

        @keyframes wave {
            0%, 100% { transform: scaleY(0.4); opacity: 0.4; }
            50% { transform: scaleY(1); opacity: 1; }
        }

        /* STATS */
        .stats {
            display: flex;
            justify-content: center;
            gap: 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            margin: 0 48px;
        }

        .stat {
            flex: 1;
            max-width: 240px;
            padding: 48px 32px;
            text-align: center;
            border-right: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .stat:last-child { border-right: none; }

        .stat::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,212,255,0.03), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat:hover::before { opacity: 1; }

        .stat-number {
            font-family: 'Syne', sans-serif;
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* FEATURES */
        .features {
            padding: 100px 48px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--accent);
            margin-bottom: 16px;
            font-weight: 600;
        }

        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 800;
            letter-spacing: -1.5px;
            margin-bottom: 64px;
            max-width: 400px;
            line-height: 1.1;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .feature-card {
            background: var(--card);
            padding: 40px;
            transition: background 0.3s;
            position: relative;
        }

        .feature-card:hover { background: #141c26; }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
            background: var(--border);
        }

        .feature-title {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .feature-desc {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* FLOW */
        .flow {
            padding: 80px 48px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .flow-inner {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .flow-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-top: 56px;
            flex-wrap: wrap;
        }

        .flow-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 120px;
        }

        .step-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: var(--card);
            transition: all 0.3s;
            position: relative;
        }

        .flow-step:hover .step-icon {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(0,212,255,0.2);
        }

        .step-label {
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            font-weight: 500;
        }

        .flow-arrow {
            font-size: 20px;
            color: var(--border);
            flex-shrink: 0;
            padding: 0 8px;
            margin-bottom: 24px;
        }

        /* CTA */
        .cta {
            padding: 120px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(123,97,255,0.08) 0%, transparent 70%);
        }

        .cta-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(36px, 5vw, 60px);
            font-weight: 800;
            letter-spacing: -2px;
            margin-bottom: 16px;
        }

        .cta-sub {
            color: var(--muted);
            font-size: 16px;
            margin-bottom: 40px;
        }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border);
            padding: 32px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer p { font-size: 13px; color: var(--muted); }

        footer .footer-links {
            display: flex;
            gap: 24px;
        }

        footer .footer-links a {
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        footer .footer-links a:hover { color: var(--text); }

        /* ANIMATIONS */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: all 0.6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            nav { padding: 16px 24px; }
            .nav-links { display: none; }
            .hero { padding: 100px 24px 60px; }
            .stats { flex-wrap: wrap; margin: 0 24px; }
            .stat { min-width: 50%; border-bottom: 1px solid var(--border); }
            .features { padding: 60px 24px; }
            .features-grid { grid-template-columns: 1fr; }
            .flow-steps { gap: 16px; }
            .flow-arrow { display: none; }
            footer { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
</head>
<body>

<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- NAV -->
<nav>
    <div class="logo">
        <div class="logo-dot"></div>
        VoiceAI
    </div>
    <div class="nav-links">
        <a href="#features">Features</a>
        <a href="#flow">How it works</a>
        <a href="/admin" class="btn-nav btn">Admin Panel</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div>
        <div class="hero-badge">
            <div class="badge-dot"></div>
            System Online – AI Agent Active
        </div>
        <h1 class="hero-title">
            Voice Support<br>
            <span class="line2">Reimagined.</span>
        </h1>
        <p class="hero-sub">
            AI-powered voice agent that handles customer queries 24/7 — no wait times, no hold music.
        </p>
        <div class="hero-actions">
            <a href="/admin" class="btn btn-primary">Open Admin Panel</a>
            <a href="/portal" class="btn btn-secondary">Customer Portal</a>
        </div>

        <!-- Waveform -->
        <div class="waveform">
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
            <div class="wave-bar"></div>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats">
    <div class="stat reveal">
        <div class="stat-number">24/7</div>
        <div class="stat-label">Always Online</div>
    </div>
    <div class="stat reveal">
        <div class="stat-number">&lt;5s</div>
        <div class="stat-label">Response Time</div>
    </div>
    <div class="stat reveal">
        <div class="stat-number">100%</div>
        <div class="stat-label">Calls Logged</div>
    </div>
    <div class="stat reveal">
        <div class="stat-number">AI</div>
        <div class="stat-label">Powered by Gemini</div>
    </div>
</div>

<!-- FEATURES -->
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
            <div class="feature-desc">Instantly retrieves and communicates order status and delivery information to customers.</div>
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

<!-- FLOW -->
<section class="flow" id="flow">
    <div class="flow-inner">
        <div class="section-label">How it works</div>
        <h2 class="section-title" style="max-width:100%">Call → AI → Response</h2>

        <div class="flow-steps">
            <div class="flow-step reveal">
                <div class="step-icon">📱</div>
                <div class="step-label">Customer<br>Calls</div>
            </div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal">
                <div class="step-icon">📡</div>
                <div class="step-label">Twilio<br>Receives</div>
            </div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal">
                <div class="step-icon">🎙️</div>
                <div class="step-label">Speech<br>to Text</div>
            </div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal">
                <div class="step-icon">🧠</div>
                <div class="step-label">Gemini<br>Processes</div>
            </div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal">
                <div class="step-icon">🔊</div>
                <div class="step-label">Voice<br>Response</div>
            </div>
            <div class="flow-arrow">→</div>
            <div class="flow-step reveal">
                <div class="step-icon">💾</div>
                <div class="step-label">Logged<br>to DB</div>
            </div>
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

<!-- FOOTER -->
<footer>
    <p>© 2025 VoiceAI – AI Voice Agent Platform</p>
    <div class="footer-links">
        <a href="/admin">Admin</a>
        <a href="/portal">Portal</a>
        <a href="/ai-demo">Demo</a>
    </div>
</footer>

<script>
    // Custom cursor
    const cursor = document.getElementById('cursor');
    const ring = document.getElementById('cursorRing');
    let mouseX = 0, mouseY = 0, ringX = 0, ringY = 0;

    document.addEventListener('mousemove', e => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        cursor.style.left = mouseX - 4 + 'px';
        cursor.style.top = mouseY - 4 + 'px';
    });

    function animateRing() {
        ringX += (mouseX - ringX) * 0.12;
        ringY += (mouseY - ringY) * 0.12;
        ring.style.left = ringX - 16 + 'px';
        ring.style.top = ringY - 16 + 'px';
        requestAnimationFrame(animateRing);
    }
    animateRing();

    document.querySelectorAll('a, button').forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(2)';
            ring.style.transform = 'scale(1.5)';
            ring.style.borderColor = 'rgba(0,212,255,0.8)';
        });
        el.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            ring.style.transform = 'scale(1)';
            ring.style.borderColor = 'rgba(0,212,255,0.4)';
        });
    });

    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(el => observer.observe(el));
</script>
</body>
</html>
