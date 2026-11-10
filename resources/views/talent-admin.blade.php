@verbatim
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AQDesk - Talent Admin</title>
    <script src="/js/vue.js"></script>
    <script src="/js/session-guard.js"></script>

    <link rel="stylesheet" href="/css/mobile.css">
<style>
body { font-family: sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; background:#f7f8fa; }
.card { border:1px solid #ddd; border-radius:8px; padding:16px; margin-bottom:16px; background:#fff; }
input, select, textarea { padding:8px; margin:4px 0; width:100%; box-sizing:border-box; }
button { padding:8px 14px; cursor:pointer; margin-top:6px; }
.chip { display:inline-block; background:#EAF2FB; color:#2C5FA8; padding:2px 10px; border-radius:12px; font-size:12px; margin:2px; }
</style>
</head>
<body>
<div id="app">
  <h2>Talent Admin</h2>
  <p><a href="/dashboard">← Dashboard</a></p>
  <p style="color:green;">{{ successMessage }}</p>
  <p style="color:red;">{{ errorMessage }}</p>

  <div class="card">
    <h3>Qualified candidates (with consent)</h3>
    <p v-if="!candidates.length" style="color:#666;">No one qualifies yet.</p>
    <div v-for="c in candidates" :key="c.user_id" class="card">
      <strong>{{ c.name }}</strong> · {{ c.email }}<br>
      Average {{ c.average }}% over {{ c.attempts }} tests<br>
      <span v-for="s in c.skills" :key="s.skill" class="chip">{{ s.skill }} {{ s.percentage }}%</span>
      <select v-model="selectedCompany[c.user_id]">
        <option value="">Select company…</option>
        <option v-for="co in companies" :key="co.company_id" :value="co.company_id">{{ co.name }}</option>
      </select>
      <input v-model="notes[c.user_id]" placeholder="Note to the company (optional)">
      <button @click="refer(c)">Send profile to company</button>
    </div>
  </div>

  <div class="card">
    <h3>Add a company</h3>
    <input v-model="newCompany.name" placeholder="Company name">
    <input v-model="newCompany.contact_email" type="email" placeholder="HR contact email">
    <input v-model="newCompany.industry" placeholder="Industry (optional)">
    <button @click="addCompany">Add company</button>
  </div>

  <div class="card">
    <h3>Schedule a weekend test</h3>
    <select v-model="newTest.skill_id">
      <option value="">Select skill…</option>
      <option v-for="s in skills" :key="s.skill_id" :value="s.skill_id">{{ s.skill_name }}</option>
    </select>
    <input v-model="newTest.title" placeholder="Title, e.g. Weekend Java Challenge">
    <textarea v-model="newTest.description" rows="2" placeholder="Description"></textarea>
    <input v-model="newTest.test_date" type="date">
    <button @click="addTest">Schedule test</button>
  </div>
</div>

<script>
const { createApp } = Vue;
createApp({
  data() {
    return {
      candidates: [], companies: [], skills: [],
      selectedCompany: {}, notes: {},
      newCompany: { name: '', contact_email: '', industry: '' },
      newTest: { skill_id: '', title: '', description: '', test_date: '' },
      successMessage: '', errorMessage: ''
    };
  },
  methods: {
    headers() {
      return { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('aqdesk_token') };
    },
    async call(path, method, body) {
      const response = await fetch('http://127.0.0.1:8000/api' + path, {
        method, headers: this.headers(), body: body ? JSON.stringify(body) : undefined
      });
      const data = await response.json();
      return { ok: response.ok, status: response.status, data };
    },
    showResult(r, okText) {
      this.successMessage = ''; this.errorMessage = '';
      if (r.ok) { this.successMessage = r.data.message || okText; return true; }
      if (r.status === 403) this.errorMessage = 'This page is for admins only.';
      else this.errorMessage = r.data.errors ? Object.values(r.data.errors).flat().join(' ') : (r.data.message || 'Something went wrong.');
      return false;
    },
    async loadAll() {
      if (!localStorage.getItem('aqdesk_token')) { window.location.href = '/Login'; return; }
      const [c, co, sk] = await Promise.all([
        this.call('/admin/talent/candidates', 'GET'),
        this.call('/admin/companies', 'GET'),
        this.call('/skills', 'GET')
      ]);
      if (c.status === 403) { this.errorMessage = 'This page is for admins only.'; return; }
      this.candidates = c.ok ? c.data : [];
      this.companies = co.ok ? co.data : [];
      this.skills = sk.ok ? sk.data : [];
    },
    async refer(c) {
      const r = await this.call('/admin/talent/referrals', 'POST', {
        user_id: c.user_id, company_id: this.selectedCompany[c.user_id], note: this.notes[c.user_id] || ''
      });
      this.showResult(r, 'Sent.');
    },
    async addCompany() {
      const r = await this.call('/admin/companies', 'POST', this.newCompany);
      if (this.showResult(r, 'Company added.')) { this.newCompany = { name: '', contact_email: '', industry: '' }; this.loadAll(); }
    },
    async addTest() {
      const r = await this.call('/test-schedules', 'POST', this.newTest);
      if (this.showResult(r, 'Test scheduled.')) { this.newTest = { skill_id: '', title: '', description: '', test_date: '' }; }
    }
  },
  mounted() { this.loadAll(); }
}).mount('#app');
</script>
</body>
</html>
@endverbatim