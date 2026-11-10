<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>AQDesk - Jobs & Internships</title>

<link rel="stylesheet" href="/css/mobile.css">

<style>
:root { --ink:#1B2A4A; --amber:#E8A33D; --teal:#1F7A7A; --blue:#2C5FA8; --paper:#F7F5F0; --line:#E3DED2; }
* { box-sizing: border-box; }
body { margin:0; font-family:'Segoe UI',Tahoma,sans-serif; background:var(--paper); color:var(--ink); min-height:100vh; }

.topbar { display:flex; justify-content:space-between; align-items:center; padding:18px 6%; border-bottom:1px solid var(--line); background:#fff; }
.logo { font-weight:700; font-size:22px; text-decoration:none; color:var(--ink); }
.logo span { color:var(--amber); }
.back { text-decoration:none; color:var(--ink); font-weight:600; font-size:15px; padding:8px 16px; border:1.5px solid var(--ink); border-radius:30px; transition:.25s; }
.back:hover { background:var(--ink); color:#fff; }

.wrap { max-width:1000px; margin:0 auto; padding:50px 6% 60px; }
.intro { text-align:center; margin-bottom:40px; animation:fadeUp .7s both; }
.eyebrow { text-transform:uppercase; letter-spacing:2px; font-size:12px; font-weight:700; color:var(--teal); margin-bottom:10px; }
.intro h1 { font-size:40px; margin:0 0 12px; line-height:1.15; }
.intro p { color:#5a6274; font-size:17px; line-height:1.6; max-width:560px; margin:0 auto; }

.choices { display:grid; grid-template-columns:1fr 1fr; gap:26px; }
.choice { position:relative; overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:#fff; border-radius:20px; padding:36px 32px; min-height:340px; transition:transform .3s, box-shadow .3s; animation:fadeUp .8s both; }
.choice:nth-child(2) { animation-delay:.15s; }
.choice:hover { transform:translateY(-8px); box-shadow:0 22px 44px rgba(27,42,74,.28); }
.choice.jobs { background:linear-gradient(135deg,#1B2A4A 0%,#2C5FA8 100%); }
.choice.interns { background:linear-gradient(135deg,#1F7A7A 0%,#1B2A4A 100%); }
.choice::after { content:''; position:absolute; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,.07); right:-80px; top:-80px; transition:transform .5s; }
.choice:hover::after { transform:scale(1.4); }
.icon { font-size:64px; margin-bottom:14px; display:inline-block; animation:float 4s ease-in-out infinite; }
.choice h2 { margin:0 0 10px; font-size:30px; }
.choice p { margin:0 0 16px; color:#d6deee; line-height:1.55; font-size:15.5px; }
.points { list-style:none; padding:0; margin:0 0 22px; font-size:14.5px; color:#e8edf8; }
.points li { padding:3px 0; }
.points li::before { content:'✓ '; color:var(--amber); font-weight:700; }
.count { display:inline-block; align-self:flex-start; background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.35); padding:5px 14px; border-radius:20px; font-size:13px; font-weight:600; margin-bottom:18px; }
.go { margin-top:auto; align-self:flex-start; background:var(--amber); color:var(--ink); font-weight:700; padding:14px 28px; border-radius:30px; font-size:16px; transition:.25s; }
.choice:hover .go { box-shadow:0 8px 20px rgba(232,163,61,.5); padding-right:36px; }

@keyframes fadeUp { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }
@keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-10px); } }

footer { text-align:center; color:#8a8f9c; font-size:13px; padding:20px; }

/* ---------- MOBILE ---------- */
@media (max-width: 760px) {
  .choices { grid-template-columns:1fr; gap:20px; }
  .wrap { padding:32px 5% 40px; }
  .intro h1 { font-size:28px; }
  .intro p { font-size:15.5px; }
  .choice { min-height:0; padding:28px 24px; }
  .icon { font-size:52px; }
  .choice h2 { font-size:26px; }
  .go { width:100%; text-align:center; padding:15px; }
  .choice:hover { transform:none; }
  .topbar { padding:14px 5%; }
  .logo { font-size:19px; }
}
</style>
</head>
<body>

<div class="topbar">
  <a href="/home" class="logo">AQ<span>Desk</span></a>
  <a href="/home" class="back">← Home</a>
</div>

<div class="wrap">
  <div class="intro">
    <div class="eyebrow">Opportunities</div>
    <h1>What are you looking for?</h1>
    <p>Find a job, or build your skills and earn a place through our internship program.</p>
  </div>

  <div class="choices">
    <a href="/jobs" class="choice jobs">
      <div class="icon">💼</div>
      <h2>Jobs</h2>
      <p>Browse job and internship postings from the community and companies.</p>
      <ul class="points">
        <li>Registration links and advertisement PDFs</li>
        <li>Ask the poster a query</li>
        <li>Post your own opening</li>
      </ul>
      <span class="count" id="jobCount">Browse listings</span>
      <span class="go">Explore Jobs →</span>
    </a>

    <a href="/internships" class="choice interns">
      <div class="icon">🎓</div>
      <h2>Internships</h2>
      <p>Skill-based internships. Take weekend tests, build your profile, and get referred to companies.</p>
      <ul class="points">
        <li>Weekend skill tests</li>
        <li>Interview eligibility by score</li>
        <li>Talent referral to partner companies</li>
      </ul>
      <span class="count" id="internCount">Browse internships</span>
      <span class="go">Explore Internships →</span>
    </a>
  </div>
</div>

<footer>AQDesk — Answer Question Desk</footer>

<script>
function setCount(url, id, label) {
  fetch(url)
    .then(function (r) { return r.json(); })
    .then(function (d) {
      if (Array.isArray(d)) {
        document.getElementById(id).textContent = d.length + ' ' + label;
      }
    })
    .catch(function () { /* keep the default text if the server is unreachable */ });
}
setCount('/api/jobs', 'jobCount', 'open listings');
setCount('/api/internships', 'internCount', 'open internships');
</script>
</body>
</html>