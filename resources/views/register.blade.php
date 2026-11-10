<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Join AQDesk</title>
<style>
  :root{--blue:#2563eb;--blue-d:#1d4ed8;--ink:#0f172a;--muted:#64748b;--line:#e2e8f0;--bg:#f1f5f9}
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:linear-gradient(160deg,#eff6ff,#f1f5f9 60%);color:var(--ink);min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:40px 16px}
  .brand{font-size:26px;font-weight:800;color:var(--blue);text-decoration:none;letter-spacing:.3px}
  h1{font-size:30px;margin:18px 0 6px;text-align:center}
  .sub{color:var(--muted);margin-bottom:34px;text-align:center}
  .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px;width:100%;max-width:820px}
  .card{background:#fff;border:2px solid var(--line);border-radius:18px;padding:28px 26px;display:flex;flex-direction:column;transition:.2s}
  .card:hover{border-color:var(--blue);transform:translateY(-4px);box-shadow:0 14px 30px rgba(37,99,235,.14)}
  .icon{width:58px;height:58px;border-radius:14px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:30px;margin-bottom:16px}
  .card h2{font-size:21px;margin-bottom:4px}
  .tag{color:var(--muted);font-size:14px;margin-bottom:14px}
  ul{list-style:none;margin-bottom:22px;flex:1}
  li{padding:6px 0 6px 26px;position:relative;font-size:14.5px}
  li::before{content:"✓";position:absolute;left:0;color:#16a34a;font-weight:700}
  .btn{display:block;text-align:center;text-decoration:none;background:var(--blue);color:#fff;font-weight:600;padding:13px;border-radius:12px;transition:.2s}
  .btn:hover{background:var(--blue-d)}
  .note{font-size:12.5px;color:var(--muted);margin-top:10px;text-align:center}
  .login{margin-top:30px;color:var(--muted)}
  .login a{color:var(--blue);font-weight:600;text-decoration:none}
  @media(max-width:640px){.grid{grid-template-columns:1fr}h1{font-size:25px}}
</style>
</head>
<body>

<a href="/" class="brand">AQDesk</a>
<h1>Join the AQDesk community</h1>
<p class="sub">Choose how you want to use AQDesk. You can only pick one for each account.</p>

<div class="grid">

  <!-- EMPLOYEE / STUDENT -->
  <div class="card">
    <div class="icon">🎓</div>
    <h2>I'm an Employee / Student</h2>
    <div class="tag">Looking for internships, learning and answers</div>
    <ul>
      <li>Apply to company internships</li>
      <li>Complete the 5-stage internship journey</li>
      <li>Earn verified certificates</li>
      <li>Ask questions and learn from experts</li>
    </ul>
    <a href="/register/employee" class="btn">Register as Employee</a>
    <div class="note">Your account is active immediately</div>
  </div>

  <!-- EMPLOYER / COMPANY -->
  <div class="card">
    <div class="icon">🏢</div>
    <h2>I'm an Employer</h2>
    <div class="tag">Hiring interns for my company</div>
    <ul>
      <li>Post internships with full details</li>
      <li>Receive and manage applicants</li>
      <li>Shortlist, accept or reject candidates</li>
      <li>Reach students and tech talent</li>
    </ul>
    <a href="/register/employer" class="btn">Register as Employer</a>
    <div class="note">Company accounts are verified by an admin first</div>
  </div>

</div>

<p class="login">Already have an account? <a href="/login">Log in</a></p>

</body>
</html>