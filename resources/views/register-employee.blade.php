<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — Employee | AQDesk</title>
<link rel="stylesheet" href="/css/auth.css">
<script src="/js/vue.js"></script>
</head>
<body>
@verbatim
<div id="app" class="wrap">
  <a href="/" class="brand">AQDesk</a>
  <div class="card">
    <a href="/register" class="back">← Change account type</a>
    <h1>🎓 Create your Employee account</h1>
    <p class="sub">For students and professionals looking for internships, learning and answers.</p>

    <form @submit.prevent="submit" novalidate>
      <div v-for="g in groups" :key="g.title">
        <h3 class="sec">{{ g.title }}</h3>
        <div class="grid">
          <div class="f" :class="{ full: x.full }" v-for="x in g.fields" :key="x.k">
            <label>{{ x.l }}<b v-if="x.req"> *</b></label>
            <select v-if="x.t === 'select'" v-model="f[x.k]" :class="{ bad: err(x.k) }">
              <option v-for="o in roles" :key="o.value" :value="o.value">{{ o.label }}</option>
            </select>
            <input v-else :type="x.t" v-model="f[x.k]" :placeholder="x.ph" :autocomplete="x.ac" :class="{ bad: err(x.k) }">
            <small class="hint" v-if="x.h">{{ x.h }}</small>
            <small class="err" v-if="err(x.k)">{{ err(x.k) }}</small>
          </div>
        </div>
      </div>

      <label class="terms">
        <input type="checkbox" v-model="f.terms">
        <span>I agree to the AQDesk terms and to provide accurate information. My name will appear on certificates.</span>
      </label>
      <small class="err" v-if="err('terms')">{{ err('terms') }}</small>

      <div class="alert" v-if="msg">{{ msg }}</div>
      <button class="btn" :disabled="loading">{{ loading ? 'Creating account…' : 'Create Employee Account' }}</button>
    </form>

    <p class="login">Already have an account? <a href="/login">Log in</a></p>
  </div>
</div>

<script>
Vue.createApp({
  data() {
    return {
      loading: false, msg: '', errors: {},
      roles: [{ value: 'student', label: 'Student' }],
      f: { name:'', email:'', role_name:'student', university:'', field_of_study:'', phone:'', city:'', country:'', password:'', password_confirmation:'', terms:false },
      groups: [
        { title: 'About you', fields: [
          { k:'name',  l:'Full name', t:'text',  req:true, full:true, ph:'As it should appear on certificates', ac:'name' },
          { k:'email', l:'Email',     t:'email', req:true, full:true, ph:'you@example.com', ac:'email' },
          { k:'role_name', l:'I am a', t:'select', req:true, full:true }
        ]},
        { title: 'Study / work', fields: [
          { k:'university',     l:'University / Organization', t:'text', req:true, ph:'e.g. Virtual University of Pakistan' },
          { k:'field_of_study', l:'Field of study / Job title', t:'text', req:true, ph:'e.g. Software Engineering' },
          { k:'city',    l:'City',    t:'text', ph:'e.g. Lahore' },
          { k:'country', l:'Country', t:'text', ph:'e.g. Pakistan' },
          { k:'phone',   l:'Phone (optional)', t:'tel', full:true, ph:'+92 300 1234567' }
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
        const r = await fetch('/api/register/employee', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(this.f)
        });
        const d = await r.json();
        if (r.ok) {
          localStorage.setItem('aqdesk_token', d.token);
          localStorage.setItem('aqdesk_user', JSON.stringify(d.user));
          window.location.href = '/dashboard';
          return;
        }
        if (r.status === 422) { this.errors = d.errors || {}; this.msg = 'Please fix the highlighted fields.'; }
        else { this.msg = d.message || 'Registration failed. Please try again.'; }
      } catch (e) { this.msg = 'Network error. Is the server running?'; }
      this.loading = false;
    }
  },
  mounted() {
    fetch('/api/register/roles', { headers: { Accept: 'application/json' } })
      .then(r => r.json())
      .then(list => {
        if (!Array.isArray(list) || !list.length) return;
        this.roles = list;
        if (!list.find(o => o.value === this.f.role_name)) this.f.role_name = list[0].value;
      }).catch(() => {});
  }
}).mount('#app');
</script>
@endverbatim
</body>
</html>