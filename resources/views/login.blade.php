<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in | AQDesk</title>
<link rel="stylesheet" href="/css/auth.css">
<script src="/js/vue.js"></script>
<style>.wrap{max-width:440px}.pw{position:relative}.pw button{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:var(--muted);cursor:pointer;font-size:13px;font-weight:600}</style>
</head>
<body>
@verbatim
<div id="app" class="wrap">
  <a href="/" class="brand">AQDesk</a>
  <div class="card">
    <h1>Welcome back</h1>
    <p class="sub">Log in to continue to your workspace.</p>

    <form @submit.prevent="submit" novalidate>
      <div class="grid" style="grid-template-columns:1fr;margin-top:18px">
        <div class="f">
          <label>Email</label>
          <input type="email" v-model="f.email" placeholder="you@example.com" autocomplete="email" :class="{ bad: err('email') }">
          <small class="err" v-if="err('email')">{{ err('email') }}</small>
        </div>
        <div class="f">
          <label>Password</label>
          <div class="pw">
            <input :type="show ? 'text' : 'password'" v-model="f.password" placeholder="Your password" autocomplete="current-password" :class="{ bad: err('password') }">
            <button type="button" @click="show = !show">{{ show ? 'Hide' : 'Show' }}</button>
          </div>
          <small class="err" v-if="err('password')">{{ err('password') }}</small>
        </div>
      </div>

      <div class="alert" v-if="msg">{{ msg }}</div>
      <button class="btn" :disabled="loading">{{ loading ? 'Logging in…' : 'Log in' }}</button>
    </form>

    <p class="login">New to AQDesk? <a href="/register">Create an account</a></p>
  </div>
</div>

<script>
// Where each role goes after login. Everyone else goes to /dashboard.
const HOME = { admin: '/admin', employer: '/employer' };

Vue.createApp({
  data() {
    return { f: { email: '', password: '' }, show: false, loading: false, msg: '', errors: {} };
  },
  methods: {
    err(k) { return this.errors[k] ? this.errors[k][0] : ''; },
    async submit() {
      this.errors = {}; this.msg = '';
      if (!this.f.email || !this.f.password) { this.msg = 'Please enter your email and password.'; return; }

      this.loading = true;
      try {
        const r = await fetch('/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify(this.f)
        });
        const d = await r.json();

        if (r.ok) {
          localStorage.setItem('aqdesk_token', d.token);
          localStorage.setItem('aqdesk_user', JSON.stringify(d.user));
          const role = d.user && d.user.role ? d.user.role.role_name : '';
          window.location.href = HOME[role] || '/dashboard';
          return;
        }
        if (r.status === 422) { this.errors = d.errors || {}; }
        else { this.msg = d.message || 'Login failed. Please try again.'; }   // 401 wrong password, 403 suspended
      } catch (e) { this.msg = 'Network error. Is the server running?'; }
      this.loading = false;
    }
  }
}).mount('#app');
</script>
@endverbatim
</body>
</html>