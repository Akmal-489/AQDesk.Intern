<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AQDesk — Answer Question Desk</title>

        <link rel="stylesheet" href="/css/mobile.css">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,600;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
            :root {
                --ink: #1B2A4A;
                --amber: #E8A33D;
                --teal: #1F7A7A;
                --paper: #F7F5F0;
                --line: #E3DED2;
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                background: var(--paper);
                color: var(--ink);
                font-family: 'Inter', sans-serif;
            }
            h1, h2, .display {
                font-family: 'Newsreader', serif;
            }

            nav {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 22px 6%;
                border-bottom: 1px solid var(--line);
            }

            .logo {
                font-family: 'Newsreader', serif;
                font-weight: 600;
                font-size: 50px;
                color: var(--ink);
                letter-spacing: 0.5px;
            }
            .logo span { color: var(--amber); }
            nav a {
                color: var(--ink);
                text-decoration: none;
                margin-left: 28px;
                font-size: 15px;
                font-weight: 500;
            }

            .nav-cta {
                background: var(--ink);
                color: white !important;
                padding: 9px 18px;
                border-radius: 4px;
            }

            .nav1 {
                background-color: #B0332F;
                }

            .hero {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 80px 6% 60px;
                gap: 40px;
                flex-wrap: wrap;
            }
            .hero-text { max-width: 560px; }
            .eyebrow {
                text-transform: uppercase;
                letter-spacing: 2px;
                font-size: 12px;
                font-weight: 600;
                color: var(--teal);
                margin-bottom: 18px;
            }
            .hero h1 {
                font-size: 36px;
                line-height: 1.12;
                font-weight: 600;
                margin: 0 0 22px;
            }
 
            .hero h1 em { color: var(--amber); font-style: italic; }
            .hero p {
                font-size: 17px;
                line-height: 1.6;
                color: #4b5468;
                margin-bottom: 32px;
            }
            .btn-row { display: flex; gap: 14px; flex-wrap: wrap; }
            .btn {
                display: inline-block;
                padding: 13px 26px;
                border-radius: 4px;
                font-weight: 600;
                font-size: 15px;
                text-decoration: none;
                transition: transform 0.15s ease;
            }
            .btn:hover { transform: translateY(-2px); }
            .btn-primary { background: var(--ink); color: white; }
            .btn-secondary { background: transparent; color: var(--ink); border: 1.5px solid var(--ink); }

            .network-wrap {
                width: 380px;
                height: 380px;
                position: relative;
                flex-shrink: 0;
            }
            .node {
                position: absolute;
                border-radius: 50%;
                background: var(--ink);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 11px;
                font-weight: 600;
                font-family: 'Inter', sans-serif;
                animation: float 4s ease-in-out infinite;
            }
            .node.amber { background: var(--amber); color: var(--ink); }
            .node.teal { background: var(--teal); }
            .n1 { width: 92px; height: 92px; top: 10px; left: 130px; animation-delay: 0s; }
            .n2 { width: 68px; height: 68px; top: 150px; left: 10px; animation-delay: 0.6s; }
            .n3 { width: 76px; height: 76px; top: 130px; left: 270px; animation-delay: 1.1s; }
            .n4 { width: 58px; height: 58px; top: 280px; left: 90px; animation-delay: 1.6s; }
            .n5 { width: 64px; height: 64px; top: 290px; left: 250px; animation-delay: 2.1s; }
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }
            .network-wrap svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
            .network-wrap line { stroke: #c9c2ae; stroke-width: 1.5; }

            .services {
                padding: 70px 6%;
                background: white;
                border-top: 1px solid var(--line);
                border-bottom: 1px solid var(--line);
            }
            .services h2 {
                font-size: 32px;
                font-weight: 600;
                margin-bottom: 8px;
            }
            .services > p { color: #4b5468; margin-bottom: 44px; }
            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
                gap: 22px;
            }
            .card {
                border: 1px solid var(--line);
                border-radius: 6px;
                padding: 26px;
                background: var(--paper);
                transition: box-shadow 0.2s ease, transform 0.2s ease;
            }
            .card:hover { box-shadow: 0 8px 22px rgba(27,42,74,0.08); transform: translateY(-3px); }
            .card .icon {
                width: 40px; height: 40px;
                border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                font-size: 19px;
                margin-bottom: 14px;
                color: white;
            }
            .card h3 { font-size: 17px; margin: 0 0 8px; font-family: 'Inter', sans-serif; font-weight: 600; }
            .card p { font-size: 14px; color: #5a6274; margin: 0; line-height: 1.5; }

            .about {
                padding: 70px 6%;
                display: flex;
                gap: 50px;
                flex-wrap: wrap;
                align-items: center;
            }
            .about-stat {
                font-family: 'Newsreader', serif;
                font-size: 42px;
                font-weight: 600;
                color: var(--ink);
            }
            .about-stat-label { font-size: 13px; color: #5a6274; margin-top: 4px; }
            .stats-row { display: flex; gap: 40px; margin-top: 26px; }
            .about-text { max-width: 480px; }
            .about-text .eyebrow { color: var(--amber); }
            .about-text h2 { font-size: 30px; margin-bottom: 16px; }
            .about-text p { color: #4b5468; line-height: 1.65; font-size: 15.5px; }

            .final-cta {
                background: var(--ink);
                color: white;
                padding: 60px 6%;
                text-align: center;
            }
            .final-cta h2 { color: white; font-size: 30px; margin-bottom: 10px; }
            .final-cta p { color: #c9d0e0; margin-bottom: 28px; }
            .final-cta .btn-primary { background: var(--amber); color: var(--ink); }

            footer {
                padding: 24px 6%;
                text-align: center;
                font-size: 13px;
                color: #8a8f9c;
            }

            @media (max-width: 720px) {
                .hero h1 { font-size: 36px; }
                .network-wrap { display: none; }
                .hero { padding-top: 50px; }
            }

            .menu-btn { display:none; background:none; border:1.5px solid var(--ink); border-radius:6px; font-size:22px; padding:4px 12px; cursor:pointer; color:var(--ink); }
            .nav-links { display:flex; align-items:center; }

            @media (max-width: 720px) {
                nav { position:relative; }
                .menu-btn { display:block; }
                .nav-links { display:none; position:absolute; top:100%; left:0; right:0; background:#fff; flex-direction:column; align-items:stretch; padding:8px 6% 18px; border-bottom:1px solid var(--line); z-index:20; box-shadow:0 10px 20px rgba(0,0,0,.08); }
                .nav-links.open { display:flex; }
                .nav-links a { margin:0; padding:14px 0; border-bottom:1px solid var(--line); }
                .nav-links a.nav-cta { margin-top:14px; text-align:center; border-bottom:0; }
            }
        </style>
        </head>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const menuBtn = document.getElementById('menu-btn');
                const navLinks = document.getElementById('navLinks');

                if (menuBtn && navLinks) {
                    menuBtn.addEventListener('click', function () {
                        navLinks.classList.toggle('open');
                    });
                }
            });
        </script>

        <body>

        <nav class="nav1">
            <div class="logo">AQ<span>Desk</span></div>
            
            <!-- Hamburger button for mobile screens -->
            <button class="menu-btn" id="menu-btn" aria-label="Toggle Navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <!-- Wrapper with class 'nav-links' and id 'navLinks' for JS toggling -->
            <div class="nav-links" id="navLinks">
                <a href="/ask-questions">Q&A</a>
                <a href="/opportunities">Jobs</a>
                <a href="/Login">Log in</a>
                <a href="/register" class="nav-cta">Sign up</a>
            </div>
        </nav>

        <section class="hero">
            <div class="hero-text">
                <div class="eyebrow">Community · Knowledge · Emerging Technologies</div>
                <h1>Where curious minds <em>ask</em>, share, and build together.</h1>
                <p>AQDesk brings students, instructors, researchers and technology professionals into one place to answer questions, collaborate on projects, and discover opportunities in EdTech, AI, and beyond.</p>
                <div class="btn-row">
                    <a href="/register" class="btn btn-primary">Join AQDesk</a>
                    <a href="/ask-question" class="btn btn-secondary">Browse Questions</a>
                </div>
            </div>

            <div class="network-wrap">
                <svg>
                    <line x1="176" y1="56" x2="44" y2="184"></line>
                    <line x1="176" y1="56" x2="308" y2="168"></line>
                    <line x1="44" y1="184" x2="119" y2="309"></line>
                    <line x1="308" y1="168" x2="282" y2="322"></line>
                    <line x1="119" y1="309" x2="282" y2="322"></line>
                    <line x1="176" y1="56" x2="119" y2="309"></line>
                </svg>
                <div class="node n1">Q&A</div>
                <div class="node n2 amber">Jobs</div>
                <div class="node n3 teal">Events</div>
                <div class="node n4 teal">Projects</div>
                <div class="node n5 amber">Consulting</div>
            </div>
        </section>

        <section class="services">
            <h2>Everything in one desk</h2>
            <p>Six services, one unified platform.</p>
            <div class="grid">
                <div class="card">
                    <div class="icon" style="background:#B8791A;">💬</div>
                    <h3>Community Q&A</h3>
                    <p>Ask, answer, and tag questions across EdTech, AI, Web Dev, and Internet Governance.</p>
                </div>
                <div class="card">
                    <div class="icon" style="background:#2E7D4F;">🤝</div>
                    <h3>Project Collaboration</h3>
                    <p>Open a project space, invite collaborators, and share files with your team.</p>
                </div>
                <div class="card">
                    <div class="icon" style="background:#6A3FA0;">🎙️</div>
                    <h3>Events & Podcasts</h3>
                    <p>Join webinars, register for events, and catch up on expert podcast episodes.</p>
                </div>
                <div class="card">
                    <div class="icon" style="background:#B0332F;">💼</div>
                    <h3>Jobs & Internships</h3>
                    <p>Explore opportunities from the community and apply directly on AQDesk.</p>
                </div>
                <div class="card">
                    <div class="icon" style="background:#1F7A7A;">🧭</div>
                    <h3>Consulting</h3>
                    <p>Book time with experts and track proposals through to completion.</p>
                </div>
                <div class="card">
                    <div class="icon" style="background:#8A6D1D;">📰</div>
                    <h3>Newsletter</h3>
                    <p>Stay in the loop with community announcements and curated updates.</p>
                </div>
            </div>
        </section>

        <section class="about">
            <div class="about-text">
                <div class="eyebrow">Why AQDesk</div>
                <h2>One desk, for a whole community of emerging technology.</h2>
                <p>Built as a final year project, AQDesk unifies discussion, collaboration, and opportunity — designed to be accessible, modular, and easy to grow. No more switching between five different tools to learn, connect, and get hired.</p>
            </div>
            <div>
                <div class="stats-row">
                    <div>
                        <div class="about-stat">6</div>
                        <div class="about-stat-label">Core services</div>
                    </div>
                    <div>
                        <div class="about-stat">4</div>
                        <div class="about-stat-label">User roles supported</div>
                    </div>
                    <div>
                        <div class="about-stat">1</div>
                        <div class="about-stat-label">Unified platform</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="final-cta">
            <h2>Ready to join the desk?</h2>
            <p>Create your account and ask your first question in under a minute.</p>
            <a href="/register" class="btn btn-primary">Get Started — It's Free</a>
        </section>

        <footer>
            AQDesk — Answer Question Desk · Final Year Project
        </footer>
    </body>
</html>