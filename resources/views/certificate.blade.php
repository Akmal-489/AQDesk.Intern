<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Certificate | AQDesk</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body{background:#0b1d3a;color:#e8eef8}
  .sheet{max-width:900px;margin:1.5rem auto;background:#fffdf7;color:#12233f;border:12px double #f5a623;
         padding:2.5rem 1.5rem;text-align:center;border-radius:6px}
  .sheet h1{font-family:Georgia,serif;letter-spacing:.15em;color:#0b1d3a}
  .name{font-family:Georgia,serif;font-size:2.2rem;color:#0f766e;border-bottom:2px solid #f5a623;display:inline-block;padding:0 1rem}
  .meta{font-size:.8rem;color:#555}
  @media print{
    body{background:#fff}.noprint{display:none!important}
    .sheet{margin:0;border-width:10px}
    @page{size:A4 landscape;margin:10mm}
  }
</style>
</head>
<body>
<script>window.CERT_NO = @json($number);</script>
<div id="app">
@verbatim
<div class="container">
  <div class="noprint text-center mt-3" v-if="cert">
    <button class="btn btn-warning" @click="print">Download / Print as PDF</button>
    <a class="btn btn-outline-light" href="/dashboard">Back to dashboard</a>
  </div>
  <div v-if="err" class="alert alert-danger mt-4">{{ err }}</div>
  <div v-if="loading" class="text-center mt-5">Loading…</div>

  <div class="sheet" v-if="cert">
    <div class="meta">AQDesk — Answer Question Desk</div>
    <h1>CERTIFICATE OF COMPLETION</h1>
    <p class="mt-4 mb-1">This is to certify that</p>
    <div class="name">{{ cert.candidate_name }}</div>
    <p class="mt-4 mb-1">has successfully completed the internship</p>
    <h3>{{ cert.internship_title }}</h3>
    <p>at <strong>{{ cert.company_name }}</strong></p>
    <p>Completion date: <strong>{{ cert.completion_date }}</strong></p>
    <hr>
    <div class="meta">Certificate No: <strong>{{ cert.certificate_no }}</strong><br>
      Verify at: {{ origin }}/verify-certificate/{{ cert.certificate_no }}</div>
  </div>
</div>
@endverbatim
</div>
<script src="/js/vue.js"></script>
<script>
Vue.createApp({
  data() { return { cert: null, err: '', loading: true, origin: location.origin }; },
  async mounted() {
    const token = localStorage.getItem('aqdesk_token');
    if (!token) { location.href = '/login'; return; }
    try {
      const res = await fetch('/api/certificates/' + encodeURIComponent(window.CERT_NO),
        { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } });
      if (!res.ok) throw new Error('Certificate not found or you do not have access.');
      this.cert = await res.json();
    } catch (e) { this.err = e.message; }
    this.loading = false;
  },
  methods: { print() { window.print(); } }
}).mount('#app');
</script>
</body>
</html>