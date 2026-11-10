@verbatim
<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AQDesk - Internships</title>
    <script src="/js/vue.js"></script>
    <script src="/js/session-guard.js"></script>

    <link rel="stylesheet" href="/css/mobile.css">

    <style>
    :root { --ink:#1B2A4A; --blue:#2C5FA8; --amber:#E8A33D; --teal:#1F7A7A; --paper:#F7F5F0; --line:#E3DED2; }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin:0; font-family:'Segoe UI',Tahoma,sans-serif; background:var(--paper); color:var(--ink); }
    a { color: inherit; }

    /* TOP BAR */
    .topbar { position:fixed; top:0; left:0; right:0; z-index:50; display:flex; justify-content:space-between; align-items:center; padding:14px 5%; background:rgba(27,42,74,.55); backdrop-filter:blur(8px); }
    .brand { color:#fff; font-weight:700; font-size:21px; letter-spacing:.5px; }
    .brand span { color:var(--amber); }
    .top-actions { display:flex; gap:12px; }
    .pill-btn { position:relative; cursor:pointer; text-decoration:none; color:#fff; padding:10px 18px; border-radius:30px; font-weight:600; font-size:14px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.4); transition:.25s; font-family:inherit; }
    .pill-btn:hover { background:var(--amber); color:var(--ink); transform:translateY(-2px); }
    .badge-dot { position:absolute; top:-7px; right:-7px; background:#B0332F; color:#fff; border-radius:50%; min-width:21px; height:21px; font-size:11px; display:flex; align-items:center; justify-content:center; animation:pulse 1.8s infinite; }
    @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(176,51,47,.6)} 70%{box-shadow:0 0 0 10px rgba(176,51,47,0)} 100%{box-shadow:0 0 0 0 rgba(176,51,47,0)} }

    /* HERO CAROUSEL */
    .hero { position:relative; height:580px; overflow:hidden; background:var(--ink); }
    .slide { position:absolute; inset:0; opacity:0; transform:scale(1.06); transition:opacity 1s ease, transform 6s ease; display:flex; align-items:center; padding:0 8%; background-size:cover; background-position:center; }
    .slide.active { opacity:1; transform:scale(1); z-index:1; }
    .slide-content { max-width:560px; color:#fff; opacity:0; transform:translateY(26px); transition:all .9s .3s; }
    .slide.active .slide-content { opacity:1; transform:none; }
    .slide h1 { font-size:46px; line-height:1.1; margin:0 0 14px; }
    .slide p { font-size:18px; line-height:1.6; color:#e3e8f4; margin:0; }
    .slide-icon { position:absolute; right:10%; top:50%; margin-top:-90px; font-size:150px; animation:float 5s ease-in-out infinite; filter:drop-shadow(0 12px 24px rgba(0,0,0,.35)); }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-18px)} }
    .cta { display:inline-block; margin-top:24px; padding:13px 28px; background:var(--amber); color:var(--ink); font-weight:700; border-radius:30px; text-decoration:none; transition:.25s; }
    .cta:hover { transform:translateY(-3px); box-shadow:0 10px 24px rgba(232,163,61,.5); }
    .arrow { position:absolute; top:50%; z-index:5; transform:translateY(-50%); width:46px; height:46px; border-radius:50%; border:0; cursor:pointer; background:rgba(255,255,255,.2); color:#fff; font-size:26px; transition:.25s; }
    .arrow:hover { background:var(--amber); color:var(--ink); }
    .arrow.left { left:18px; } .arrow.right { right:18px; }
    .dots { position:absolute; bottom:22px; left:0; right:0; z-index:5; display:flex; justify-content:center; gap:10px; }
    .dots span { width:11px; height:11px; border-radius:50%; background:rgba(255,255,255,.45); cursor:pointer; transition:.3s; }
    .dots span.on { width:34px; border-radius:8px; background:var(--amber); }

    /* SECTIONS */
    .section { padding:70px 6%; }
    .section h2 { font-size:32px; margin:0 0 8px; }
    .sub { color:#5a6274; margin:0 0 36px; max-width:640px; line-height:1.6; }
    .eyebrow { text-transform:uppercase; letter-spacing:2px; font-size:12px; font-weight:700; color:var(--teal); margin-bottom:10px; }
    .reveal { opacity:0; transform:translateY(32px); transition:all .7s ease; }
    .reveal.visible { opacity:1; transform:none; }

    .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:18px; margin-top:34px; }
    .stat { background:var(--ink); color:#fff; border-radius:12px; padding:24px; text-align:center; }
    .stat b { display:block; font-size:38px; color:var(--amber); }
    .stat small { color:#c9d0e0; }

    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; }
    .feature { background:#fff; border:1px solid var(--line); border-radius:12px; padding:26px; transition:.3s; }
    .feature:hover { transform:translateY(-6px); box-shadow:0 14px 30px rgba(27,42,74,.12); border-color:var(--amber); }
    .feature .ico { font-size:34px; margin-bottom:10px; }
    .feature h3 { margin:0 0 8px; font-size:18px; }
    .feature p { margin:0; color:#5a6274; font-size:14.5px; line-height:1.55; }

    .steps { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:20px; counter-reset:step; }
    .step { position:relative; background:#fff; border-radius:12px; padding:30px 22px 22px; border-top:4px solid var(--teal); }
    .step::before { counter-increment:step; content:counter(step); position:absolute; top:-18px; left:20px; width:36px; height:36px; border-radius:50%; background:var(--amber); color:var(--ink); font-weight:700; display:flex; align-items:center; justify-content:center; }
    .step h4 { margin:0 0 6px; } .step p { margin:0; color:#5a6274; font-size:14px; line-height:1.5; }

    .talent { background:linear-gradient(120deg,#1B2A4A,#1F7A7A,#1B2A4A); background-size:200% 200%; animation:shift 10s ease infinite; color:#fff; border-radius:18px; padding:46px; display:flex; gap:30px; flex-wrap:wrap; align-items:center; }
    @keyframes shift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
    .talent h2 { color:#fff; } .talent p { color:#d6deee; line-height:1.65; max-width:560px; }
    .criteria { display:flex; gap:14px; flex-wrap:wrap; margin-top:10px; }
    .criteria div { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.3); border-radius:12px; padding:16px 22px; text-align:center; }
    .criteria b { display:block; font-size:30px; color:var(--amber); }

    .chips { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px; }
    .chip-btn { border:1.5px solid var(--ink); background:transparent; color:var(--ink); padding:8px 18px; border-radius:30px; cursor:pointer; font-weight:600; font-family:inherit; transition:.2s; }
    .chip-btn.on, .chip-btn:hover { background:var(--ink); color:#fff; }
    .i-card { background:#fff; border:1px solid var(--line); border-left:5px solid var(--teal); border-radius:12px; padding:22px; animation:fadeUp .6s both; transition:.3s; }
    .i-card:hover { transform:translateY(-6px); box-shadow:0 14px 30px rgba(27,42,74,.12); }
    .i-card.cat-web_development { border-left-color:var(--blue); }
    .i-card.cat-software_development { border-left-color:var(--amber); }
    @keyframes fadeUp { from{opacity:0; transform:translateY(24px)} to{opacity:1; transform:none} }
    .tag { display:inline-block; padding:3px 11px; border-radius:20px; font-size:12px; font-weight:600; background:#EAF2FB; color:var(--blue); margin-right:6px; }
    .tag.dur { background:#FFF6E5; color:#8A6D1D; }
    .i-card h3 { margin:12px 0 6px; } .i-card p { color:#5a6274; font-size:14.5px; line-height:1.5; }
    .req { font-size:13px; margin:12px 0; padding:8px 12px; background:var(--paper); border-radius:8px; }
    .go-btn { display:inline-block; text-decoration:none; background:var(--ink); color:#fff; padding:10px 20px; border-radius:30px; font-weight:600; font-size:14px; transition:.25s; }
    .go-btn:hover { background:var(--amber); color:var(--ink); }

    /* NEW: company-hosted internships + certificates */
    .tag.co { background:#E7F6F4; color:var(--teal); }
    .co-line { font-size:13px; margin:10px 0 4px; color:#44506a; }
    .co-line b { color:var(--ink); }
    .apply-btn { display:inline-block; border:0; cursor:pointer; font-family:inherit; background:var(--amber); color:var(--ink); padding:10px 20px; border-radius:30px; font-weight:700; font-size:14px; transition:.25s; }
    .apply-btn:hover { transform:translateY(-2px); box-shadow:0 8px 18px rgba(232,163,61,.45); }
    .flash { background:#fff; border:1px solid var(--line); border-left:5px solid var(--amber); border-radius:10px; padding:12px 16px; margin-bottom:18px; font-size:14px; }
    .cert-card { background:#fff; border:1px solid var(--line); border-left:5px solid var(--amber); border-radius:12px; padding:20px; display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
    .cert-card small { color:#6b7385; display:block; margin-top:4px; }

    footer { text-align:center; padding:30px; color:#8a8f9c; font-size:13px; }

    /* NOTIFICATION DRAWER */
    .overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:90; opacity:0; pointer-events:none; transition:.3s; }
    .overlay.open { opacity:1; pointer-events:auto; }
    .drawer { position:fixed; top:0; right:0; width:380px; max-width:92vw; height:100%; background:#fff; z-index:100; transform:translateX(100%); transition:transform .4s ease; box-shadow:-8px 0 30px rgba(0,0,0,.25); overflow-y:auto; padding:22px; }
    .drawer.open { transform:none; }
    .drawer-head { display:flex; justify-content:space-between; align-items:center; }
    .drawer-head h3 { margin:0; }
    .x-btn { border:0; background:#eee; width:34px; height:34px; border-radius:50%; cursor:pointer; font-size:16px; }
    .muted { color:#6b7385; font-size:14px; line-height:1.5; }
    .notice { display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--line); }
    .notice-date { min-width:56px; height:62px; border-radius:10px; background:var(--ink); color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .notice-date b { font-size:22px; color:var(--amber); line-height:1; } .notice-date small { font-size:11px; text-transform:uppercase; }
    .notice h4 { margin:0 0 4px; font-size:15px; } .notice p { margin:6px 0; font-size:13px; color:#5a6274; }
    .when { font-size:12px; color:#B0332F; font-weight:600; }
    .mini-btn { display:inline-block; margin-top:6px; font-size:13px; font-weight:600; color:var(--blue); text-decoration:none; }

    @media (max-width:700px) {
    .slide h1 { font-size:30px; } .slide-icon { display:none; } .hero { height:520px; }
    .talent { padding:28px; } .brand { font-size:16px; }
    .pill-btn { padding:8px 12px; font-size:12px; }

        @media (max-width:480px) {
        .brand { display:none; }
        .top-actions { width:100%; justify-content:space-between; }
        .pill-btn { flex:1; text-align:center; }
        }
    }
    </style>
    </head>
    <body>
    <div id="app">

    <!-- TOP BAR with the two buttons -->
    <div class="topbar">
        <div class="brand">AQ<span>Desk</span> Internships</div>
        <div class="top-actions">
        <a href="/dashboard" class="pill-btn">📊 Dashboard</a>
        <button class="pill-btn" @click="drawerOpen = true">
            🔔 Notifications
            <span v-if="schedules.length" class="badge-dot">{{ schedules.length }}</span>
        </button>
        </div>
    </div>

    <!-- AUTO-SCROLLING HERO -->
    <section class="hero" @mouseenter="paused = true" @mouseleave="paused = false">
        <div v-for="(s, i) in slides" :key="i" class="slide" :class="{ active: i === current }" :style="slideStyle(s)">
        <div class="slide-content">
            <h1>{{ s.title }}</h1>
            <p>{{ s.text }}</p>
            <a href="#internships" class="cta">Explore Internships</a>
        </div>
        <div class="slide-icon">{{ s.icon }}</div>
        </div>
        <button class="arrow left" @click="prev">&#8249;</button>
        <button class="arrow right" @click="next">&#8250;</button>
        <div class="dots">
        <span v-for="(s, i) in slides" :key="'d' + i" :class="{ on: i === current }" @click="go(i)"></span>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">
        <div class="reveal">
        <div class="eyebrow">About our internships</div>
        <h2>Prove your skills. Then get the internship.</h2>
        <p class="sub">AQDesk internships are skill-based. Instead of sending a CV and hoping, you take tests, build a visible skill profile on your dashboard and apply for an interview once you meet the requirement of the internship you want.</p>
        </div>

        <div class="grid">
        <div class="feature reveal"><div class="ico">🎯</div><h3>Skill-based eligibility</h3><p>Every internship lists the skill and percentage it needs, so you always know exactly what to aim for.</p></div>
        <div class="feature reveal"><div class="ico">🗓️</div><h3>Weekend tests</h3><p>New tests every weekend in Java, Python, CSS, C++, JavaScript and Web Development. Each result updates your dashboard.</p></div>
        <div class="feature reveal"><div class="ico">🎤</div><h3>Interview gate</h3><p>Click "Appear in Interview" once your score meets the requirement. If it doesn't, you're guided to take a test first.</p></div>
        <div class="feature reveal"><div class="ico">🌟</div><h3>Direct company referral</h3><p>Top performers are recommended straight to partner companies, with their consent.</p></div>
        </div>

        <div class="stats reveal">
        <div class="stat"><b>{{ counters.internships }}</b><small>Open internships</small></div>
        <div class="stat"><b>{{ counters.tests }}</b><small>Upcoming weekend tests</small></div>
        <div class="stat"><b>{{ counters.skills }}</b><small>Skills tracked</small></div>
        <div class="stat"><b>80%+</b><small>Talent referral benchmark</small></div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="section" style="padding-top:20px;">
        <div class="reveal">
        <div class="eyebrow">How it works</div>
        <h2>Four steps from signup to opportunity</h2>
        <p class="sub">&nbsp;</p>
        </div>
        <div class="steps">
        <div class="step reveal"><h4>Create your account</h4><p>Register and choose your role. Your dashboard is created automatically.</p></div>
        <div class="step reveal"><h4>Take weekend tests</h4><p>Check the 🔔 notifications for the next test and take it from your dashboard.</p></div>
        <div class="step reveal"><h4>Meet the requirement</h4><p>Watch your skill bars rise. Compare them with what each internship needs.</p></div>
        <div class="step reveal"><h4>Interview or referral</h4><p>Apply for the interview, or earn a recommendation to partner companies.</p></div>
        </div>
    </section>

    <!-- TALENT PROGRAM -->
    <section class="section" style="padding-top:20px;">
        <div class="talent reveal">
        <div>
            <div class="eyebrow" style="color:var(--amber);">AQDesk Talent Program</div>
            <h2>Do well and we introduce you to companies.</h2>
            <p>When an internee consistently performs at the top, AQDesk sends their profile directly to a hiring company. It only happens with your permission, which you can give or withdraw on your dashboard at any time.</p>
            <a href="/dashboard" class="cta">Check my progress</a>
        </div>
        <div class="criteria">
            <div><b>80%</b>average score</div>
            <div><b>3+</b>tests taken</div>
            <div><b>✔</b>your consent</div>
        </div>
        </div>
    </section>

    <!-- INTERNSHIP LIST -->
    <section class="section" id="internships" style="padding-top:20px;">
        <div class="eyebrow">Open positions</div>
        <h2>Available internships</h2>
        <p class="sub">Pick one, then continue on your dashboard to take the test or request an interview. Company internships can be applied for directly.</p>

        <!-- NEW: message after applying -->
        <div v-if="applyMsg" class="flash" role="status">{{ applyMsg }}</div>

        <div class="chips">
        <button v-for="f in filters" :key="f.key" class="chip-btn" :class="{ on: filter === f.key }" @click="filter = f.key">{{ f.label }}</button>
        </div>

        <div v-if="!filteredInternships.length" class="muted">No internships in this category yet.</div>

        <div class="grid">
        <div v-for="(i, idx) in filteredInternships" :key="i.internship_id" class="i-card" :class="'cat-' + i.category" :style="{ animationDelay: (idx * 0.08) + 's' }">
            <!-- CHANGED: tag shows the real category text for company internships -->
            <span class="tag" :class="{ co: i.hosting_type === 'company' }">{{ i.hosting_type === 'company' ? i.category : catLabel(i.category) }}</span>
            <span class="tag dur">{{ i.duration_weeks }} weeks</span>
            <h3>{{ i.title }}</h3>
            <div class="small muted" style="margin:6px 0 10px">
            <div v-if="i.company_name"><b>Company:</b> {{ i.company_name }}</div>
            <div v-if="i.duration_weeks"><b>Duration:</b> {{ i.duration_weeks }} weeks</div>
            <div v-if="i.application_deadline"><b>Last date:</b> {{ i.application_deadline }}</div>
            <div v-if="i.work_mode"><b>Mode:</b> {{ i.work_mode }}</div>
            </div>
            <span class="tag" :style="{ background: i.is_paid ? '#16a34a' : '#6b7280', color: '#fff' }">
            {{ i.is_paid ? ('Paid' + (i.stipend ? ' · ' + i.stipend : '')) : 'Unpaid' }}
            </span>
            <p>{{ i.description }}</p>

            <!-- NEW: company-hosted internship -->
            <template v-if="i.hosting_type === 'company'">
                <div class="co-line"><b>🏢 {{ i.company_name }}</b></div>
                <div class="co-line">📅 Deadline: <b>{{ i.application_deadline || 'Open' }}</b> &nbsp;·&nbsp; ⏱ <b>{{ i.duration_weeks }} weeks</b>
                    <span v-if="i.start_date"> ({{ i.start_date }} → {{ i.end_date || '?' }})</span></div>
                <div class="co-line">💼 {{ i.work_mode }}<span v-if="i.location"> · {{ i.location }}</span></div>
                <div class="req" style="white-space:pre-line;" v-if="i.specifications"><strong>Specifications</strong><br>{{ i.specifications }}</div>
                <button class="apply-btn" @click="applyDirect(i)">Apply Now</button>
                <a href="/dashboard" class="go-btn" style="margin-left:6px;">Track on dashboard →</a>
            </template>

            <!-- your original skill-based internship (unchanged) -->
            <template v-else>
                <div class="req">Requires <strong>{{ i.required_skill ? i.required_skill.skill_name : '-' }}</strong> at <strong>{{ i.required_percentage }}%</strong></div>
                <a href="/dashboard" class="go-btn">Continue on dashboard →</a>
            </template>
        </div>
        </div>
    </section>

    <!-- NEW: MY CERTIFICATES -->
    <section class="section" id="certificates" style="padding-top:20px;" v-if="certificates.length">
        <div class="eyebrow">Achievements</div>
        <h2>🎓 My certificates</h2>
        <p class="sub">Official certificates issued after you successfully completed an internship.</p>
        <div v-for="c in certificates" :key="c.certificate_id" class="cert-card">
            <div>
                <strong>{{ c.internship_title }}</strong> — {{ c.company_name }}
                <small>Certificate No. {{ c.certificate_no }} · Completed {{ c.completion_date }}</small>
            </div>
            <a :href="'/certificate/' + c.certificate_no" target="_blank" class="go-btn">View / Download</a>
        </div>
    </section>

    <footer>AQDesk — Answer Question Desk · Internship Program</footer>

    <!-- NOTIFICATIONS DRAWER -->
    <div class="overlay" :class="{ open: drawerOpen }" @click="drawerOpen = false"></div>
    <aside class="drawer" :class="{ open: drawerOpen }">
        <div class="drawer-head">
        <h3>🔔 Weekend Tests</h3>
        <button class="x-btn" @click="drawerOpen = false">✕</button>
        </div>
        <p class="muted">Every test you take lifts your skill score and makes applying easier.</p>

        <div v-if="!schedules.length" class="muted">No tests are scheduled right now. Check back soon.</div>

        <div v-for="s in schedules" :key="s.schedule_id" class="notice">
        <div class="notice-date"><b>{{ dayNum(s) }}</b><small>{{ monthName(s) }}</small></div>
        <div>
            <h4>{{ s.title }}</h4>
            <span class="tag">{{ s.skill ? s.skill.skill_name : '' }}</span>
            <p>{{ s.description }}</p>
            <div class="when">{{ daysLeft(s) }}</div>
            <a href="/dashboard" class="mini-btn">Take it from your dashboard →</a>
        </div>
        </div>
    </aside>

    </div>

    <script>
    const { createApp } = Vue;

    createApp({
    data() {
        return {
        slides: [
            { title: 'Launch your tech career', text: 'Real internships in web and software development. Apply once your skills show you are ready.', icon: '💻', bg: 'linear-gradient(135deg,#1B2A4A 0%,#2C5FA8 100%)', img: '' },
            { title: 'Weekend tests. Weekly growth.', text: 'Every weekend we run skill tests. Each score lifts your dashboard and strengthens your application.', icon: '📝', bg: 'linear-gradient(135deg,#1F7A7A 0%,#1B2A4A 100%)', img: '' },
            { title: 'Get noticed by companies', text: 'Top performers are recommended directly to partner companies for hiring.', icon: '🌟', bg: 'linear-gradient(135deg,#B8791A 0%,#1B2A4A 100%)', img: '' },
            { title: 'Build your skill profile', text: 'Java, Python, CSS, C++, JavaScript and Web Development, each tracked as a percentage.', icon: '📈', bg: 'linear-gradient(135deg,#6A3FA0 0%,#1B2A4A 100%)', img: '' }
        ],
        current: 0,
        paused: false,
        timer: null,
        observer: null,
        drawerOpen: false,
        internships: [],
        schedules: [],
        skillsCount: 0,
        certificates: [],      // NEW
        applyMsg: '',          // NEW
        counters: { internships: 0, tests: 0, skills: 0 },
        filter: 'all',
        filters: [
            { key: 'all', label: 'All' },
            { key: 'web_development', label: 'Web Development' },
            { key: 'software_development', label: 'Software Development' },
            { key: 'other', label: 'Other' },
            { key: 'company', label: 'Company Internships' }   // NEW
        ]
        };
    },
    computed: {
        // CHANGED: supports the "Company Internships" chip and free-text categories under "Other"
        filteredInternships() {
        if (this.filter === 'all') return this.internships;
        if (this.filter === 'company') return this.internships.filter(i => i.hosting_type === 'company');
        if (this.filter === 'other') {
            return this.internships.filter(i => i.category !== 'web_development' && i.category !== 'software_development');
        }
        return this.internships.filter(i => i.category === this.filter);
        }
    },
    methods: {
        // NEW: apply to a company-hosted internship
        async applyDirect(i) {
        const t = localStorage.getItem('aqdesk_token');
        if (!t) { window.location.href = '/login'; return; }
        this.applyMsg = '';
        try {
            const r = await fetch('/api/internships/' + i.internship_id + '/apply-direct', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + t }
            });
            const d = await r.json().catch(() => ({}));
            this.applyMsg = r.ok
                ? '✅ Application submitted to ' + i.company_name + '. Track it on your dashboard.'
                : (d.message || 'Could not apply right now.');
        } catch (e) {
            this.applyMsg = 'Network error. Please try again.';
        }
        window.scrollTo({ top: document.getElementById('internships').offsetTop - 80, behavior: 'smooth' });
        },

        // NEW: load my certificates (only when logged in)
        loadCertificates() {
        const t = localStorage.getItem('aqdesk_token');
        if (!t) return;
        fetch('/api/my-certificates', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + t } })
            .then(r => r.ok ? r.json() : [])
            .then(c => { this.certificates = Array.isArray(c) ? c : []; })
            .catch(() => {});
        },

        // ----- carousel -----
        next() { this.current = (this.current + 1) % this.slides.length; },
        prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; },
        go(i) { this.current = i; },
        slideStyle(s) {
        if (s.img) {
            return { backgroundImage: 'linear-gradient(rgba(27,42,74,.7),rgba(27,42,74,.7)), url(' + s.img + ')' };
        }
        return { background: s.bg };
        },

        // ----- data -----
        async loadAll() {
        try {
            const [i, s, k] = await Promise.all([
            fetch('http://127.0.0.1:8000/api/internships').then(r => r.json()),
            fetch('http://127.0.0.1:8000/api/test-schedules').then(r => r.json()),
            fetch('http://127.0.0.1:8000/api/skills').then(r => r.json())
            ]);
            this.internships = Array.isArray(i) ? i : [];
            this.schedules = Array.isArray(s) ? s : [];
            this.skillsCount = Array.isArray(k) ? k.length : 0;
        } catch (e) {
            console.log('Could not load data.');
        }
        this.$nextTick(() => { this.animateCounters(); this.observeReveals(); });
        },
        animateCounters() {
        const targets = { internships: this.internships.length, tests: this.schedules.length, skills: this.skillsCount };
        const start = performance.now();
        const duration = 1200;
        const step = (now) => {
            const p = Math.min((now - start) / duration, 1);
            for (const k in targets) { this.counters[k] = Math.round(targets[k] * p); }
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        },

        // ----- scroll reveal -----
        observeReveals() {
        if (!this.observer) {
            this.observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                e.target.classList.add('visible');
                this.observer.unobserve(e.target);
                }
            });
            }, { threshold: 0.15 });
        }
        document.querySelectorAll('.reveal:not(.visible)').forEach(el => this.observer.observe(el));
        },

        // ----- helpers -----
        catLabel(c) {
        return { web_development: 'Web Development', software_development: 'Software Development', other: 'Other' }[c] || c;
        },
        dateOf(s) { return new Date(String(s.test_date).substring(0, 10) + 'T00:00:00'); },
        dayNum(s) { return this.dateOf(s).getDate(); },
        monthName(s) { return this.dateOf(s).toLocaleString('en-GB', { month: 'short' }); },
        daysLeft(s) {
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const diff = Math.round((this.dateOf(s) - today) / 86400000);
        if (diff <= 0) return 'Today';
        if (diff === 1) return 'Tomorrow';
        return 'In ' + diff + ' days';
        }
    },
    mounted() {
        this.loadAll();
        this.loadCertificates();   // NEW
        this.timer = setInterval(() => { if (!this.paused) this.next(); }, 4500);
    },
    beforeUnmount() { clearInterval(this.timer); }
    }).mount('#app');
    </script>
    </body>
    </html>
    @endverbatim