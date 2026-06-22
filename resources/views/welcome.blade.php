<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telko Digital Identity API</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.5);
            --card-bg: #1e293b;
            --border: #334155;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Ambient Background Effect */
        .ambient-light {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            opacity: 0.6;
            filter: blur(80px);
            animation: pulse 8s infinite alternate ease-in-out;
        }

        @keyframes pulse {
            0% { opacity: 0.4; transform: translate(-50%, -50%) scale(0.9); }
            100% { opacity: 0.7; transform: translate(-50%, -50%) scale(1.1); }
        }

        .container {
            position: relative;
            z-index: 10;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.6), 0 0 40px var(--accent-glow);
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            border-radius: 20px;
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px -5px var(--accent-glow);
            color: white;
        }

        .logo svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
            background: linear-gradient(to right, #f8fafc, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.6;
            font-weight: 300;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.2);
            margin-bottom: 2rem;
        }

        .status-badge .dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            margin-right: 8px;
            box-shadow: 0 0 10px #10b981;
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.4; }
            100% { opacity: 1; }
        }

        .meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            border-top: 1px solid var(--border);
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .meta-item strong {
            color: var(--text-main);
            display: block;
            margin-bottom: 0.25rem;
        }

        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px 0 rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body>

    <div class="ambient-light"></div>

    <div class="container">
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        
        <div class="status-badge">
            <span class="dot"></span> API is Online
        </div>

        <h1>Telko Digital Identity</h1>
        <p>Welcome to the core API services. The application frontend is running separately. Please interact with this service via REST API endpoints.</p>

        <a href="{{ route('login') }}" class="login-button">Go to Admin Login</a>

        <div class="meta">
            <div class="meta-item">
                <strong>Version</strong>
                <span>v1.0.0</span>
            </div>
            <div class="meta-item">
                <strong>Environment</strong>
                <span>{{ app()->environment() }}</span>
            </div>
            <div class="meta-item">
                <strong>PHP</strong>
                <span>{{ PHP_VERSION }}</span>
            </div>
        </div>
    </div>

</body>
</html>
