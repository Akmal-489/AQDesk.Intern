<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verify Certificate | AQDesk</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#0b1d3a;color:#e8eef8}.box{background:#12305c;border:1px solid #234a85;border-radius:12px;padding:1.2rem}
.form-control{background:#0e2650;color:#e8eef8;border-color:#2a5aa5}</style>
</head>
<body>
<script>window.CERT_NO = @json($number);</script>
<div id="app">
@verbatim
<div class="container py-5" style="max-width:640px">
  <h4>Verify a certificate</h4>
  <form @submit.prevent="check" class="d-flex gap-2 my-3">
    <label for="no" class="visually-hidden">Certificate number</label>
    <input id="no" class="form-control" v-model="no" placeholder="AQD-2026-XXXXXXXX" required>
    <button class="btn btn-warning">Verify</button>
  </form>
  <div v-if="done && !res" class="alert alert-danger">No certificate found with this number.</div>
  <div v-if="res" class="box">
    <h5 :class="res.valid ? 'text-success' : 'text-danger'">{{ res.valid ? 'Valid certificate' : 'Revoked certificate' }}</h5>
    <div><strong>{{ res.candidate_name }}</strong></div>
    <div>{{ res.internship_title }} — {{ res.company_name }}</div>
    <div class="small text-secondary">Completed: {{ res.completion_date }} · No. {{ res.certificate_no }}</div>
  </div>
</div>
@endverbatim
</div>
<script src="/js/vue.js"></script>
<script>
Vue.createApp({
  data() { return { no: window.CERT_NO || '', res: null, done: false }; },
  mounted() { if (this.no) this.check(); },
  methods: {
    async check() {
      this.res = null; this.done = false;
      try {
        const r = await fetch('/api/certificates/verify/' + encodeURIComponent(this.no.trim()), { headers: { 'Accept': 'application/json' } });
        if (r.ok) this.res = await r.json();
      } catch (e) {}
      this.done = true;
    }
  }
}).mount('#app');
</script>
</body>
</html>