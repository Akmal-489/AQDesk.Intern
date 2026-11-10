<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — Employer | AQDesk</title>
<link rel="stylesheet" href="/css/auth.css">
<script src="/js/vue.js"></script>
</head>
<body>
@verbatim
<div id="app" class="wrap">
  <a href="/" class="brand">AQDesk</a>
  <div class="card">

    <!-- SUCCESS SCREEN -->
    <div class="ok" v-if="done">
      <div class="big">✅</div>
      <h2>Application submitted</h2>
      <p class="sub">Thank you, {{ f.company_name }}. Your company is now waiting for admin approval.</p>
      <ol>
        <li>An AQDesk admin reviews your company details.</li>
        <li>Once approved, you can post internships.</li>
        <li>You can log in any time to check your status.</li>
      </ol>
      <a href="/login" class="btn">Go to Login</a>
    </div>

    <!-- FORM -->
    <div v-else>
      <a href="/register" class="back">← Change account type</a>
      <h1>🏢 Create your Employer account</h1>
      <p class="sub">For companies hiring interns. Every company is verified by an admin before it can post.</p>

      <form @submit.prevent="submit" novalidate>
        <div v-for="g in groups" :key="g.title">
          <h3 class="sec">{{ g.title }}</h3>
          <div class="grid">
            <div class="f" :class="{ full: x.full }" v-for="x in g.fields" :key="x.k">
              <label>{{ x.l }}<b v-if="x.req"> *</b></label>
              <select v-if="x.t === 'select'" v-model="f[x.k]" :class="{ bad: err(x.k) }">
                <option value="">Select…</option>
                <option v-for="o in x.o" :key="o" :value="o">{{ o }}</option>
              </select>
              <textarea v-else-if="x.t === 'area'" v-model="f[x.k]" :placeholder="x.ph" :class="{ bad: err(x.k) }"></textarea>
              <input v-else :type="x.t" v-model="f[x.k]" :placeholder="x.ph" :autocomplete="x.ac" :class="{ bad: err(x.k) }">
              <small class="hint" v-if="x.h">{{ x.h }}</small>
              <small class="err" v-if="err(x.k)">{{ err(x.k) }}</small>
            </div>
          </div>
        </div>

        <div class="notice">ℹ️ Your account will be <b>pending</b> until an admin verifies your company. You will not be able to post internships before then.</div>

        <label class="terms">
          <input type="checkbox" v-model="f.terms">
          <span>I confirm that I am authorised to represent this company and that the details above are accurate.</span>
        </label>
        <small class="err" v-if="err('terms')">{{ err('terms') }}</small>

        <div class="alert" v-if="msg">{{ msg }}</div>
        <button class="btn" :disabled="loading">{{ loading ? 'Submitting…' : 'Submit for Approval' }}</button>
      </form>

      <p class="login">Already registered? <a href="/login">Log in</a></p>
    </div>
  </div>
</div>

<script>
Vue.createApp({
  data() {
    return {
      loading: false, done: false, msg: '', errors: {},
      f: { name:'', designation:'', email:'', phone:'', company_name:'', website:'', industry:'', company_size:'',
           city:'', country:'', address:'', registration_no:'', description:'', password:'', password_confirmation:'', terms:false },
      groups: [
        { title: 'Contact person', fields: [
          { k:'name',        l:'Your full name', t:'text', req:true, ac:'name' },
          { k:'designation', l:'Your designation', t:'text', req:true, ph:'e.g. HR Manager, CEO' },
          { k:'email', l:'Official work email', t:'email', req:true, h:'Use your company email address if you have one', ac:'email' },
          { k:'phone', l:'Phone', t:'tel', req:true, ph:'+92 300 1234567' }
        ]},
        { title: 'Company details', fields: [
          { k:'company_name', l:'Company name', t:'text', req:true },
          { k:'website',      l:'Company website', t:'text', req:true, ph:'www.yourcompany.com' },
          { k:'industry', l:'Industry', t:'select', req:true,
            o:['Software / IT','Education','Finance','Healthcare','Telecom','Manufacturing','Media & Marketing','Other'] },
          { k:'company_size', l:'Company size', t:'select', o:['1-10','11-50','51-200','201-500','500+'] },
          { k:'city',    l:'City',    t:'text', req:true },
          { k:'country', l:'Country', t:'text', req:true },
          { k:'address', l:'Address (optional)', t:'text', full:true },
          { k:'registration_no', l:'Registration / NTN number (optional)', t:'text', full:true, h:'Helps the admin approve your company faster' },
          { k:'description', l:'About your company', t:'area', req:true, full:true, ph:'What does your company do? (at least 20 characters)' }
        ]},
        { title: 'Security', fields: [
          { k:'password', l:'Password', t:'password', req:true, h:'At least 8 characters', ac:'new-password' },
          { k:'password_confirmation', l:'Confirm password', t:'password', req:true, ac:'new-password' }
        ]}
      ]
    };
  },
  methods: {
    err(k) { return this.errors[k] ? this.errors[k][0] : ''; },
    async submit() {
      this.errors = {}; this.msg = '';
      if (this.f.password !== this.f.password_confirmation) {
        this.errors = { password_confirmation: ['Passwords do not match.'] };
        this.msg = 'Please fix the highlighted fields.'; return;
      }
      if (!this.f.terms) { this.errors = { terms: ['Please accept the terms to continue.'] }; return; }

      this.loading = true;
      try {
        const r = await fetch('/api/register/employer', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(this.f)
        });
        const d = await r.json();
        if (r.ok) { this.done = true; window.scrollTo(0, 0); }
        else if (r.status === 422) { this.errors = d.errors || {}; this.msg = 'Please fix the highlighted fields.'; }
        else { this.msg = d.message || 'Submission failed. Please try again.'; }
      } catch (e) { this.msg = 'Network error. Is the server running?'; }
      this.loading = false;
    }
  }
}).mount('#app');
</script>
@endverbatim
</body>
</html>