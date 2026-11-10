<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Provider Workspace | AQDesk</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  :root{--navy:#0b1d3a;--navy2:#12305c;--amber:#f5a623;--teal:#14b8a6;--ink:#e8eef8}
  body{background:var(--navy);color:var(--ink)}
  .topbar{background:var(--navy2);padding:.8rem 1rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap}
  .topbar a{color:var(--amber);text-decoration:none;font-weight:600}
  .box{background:var(--navy2);border:1px solid #234a85;border-radius:12px;padding:1rem;margin-bottom:1rem}
  .form-control,.form-select{background:#0e2650;color:var(--ink);border-color:#2a5aa5}
  .form-control:focus,.form-select:focus{background:#0e2650;color:var(--ink);border-color:var(--teal);box-shadow:0 0 0 .2rem rgba(20,184,166,.25)}
  label{font-size:.85rem;color:#b9c8e3}
  .btn-amber{background:var(--amber);color:#1a1a1a;border:0;font-weight:600}
  .btn-teal{background:var(--teal);color:#06231f;border:0;font-weight:600}
  .stage{border-left:4px solid #2a5aa5;padding:.6rem .8rem;margin:.6rem 0;background:#0e2650;border-radius:8px}
  .stage.passed{border-color:var(--teal)}.stage.failed{border-color:#ef4444}.stage.submitted{border-color:var(--amber)}
  .stage.locked{opacity:.6}
  pre{white-space:pre-wrap;color:var(--ink);background:#081530;padding:.6rem;border-radius:6px}
</style>
</head>
<body>
<div id="app">
@verbatim
<nav class="topbar">
  <a href="/home">AQDesk</a><span>Provider Workspace</span>
  <a href="/internships">Internships</a><a href="/dashboard">Dashboard</a>
</nav>

<div class="container py-4">
  <div v-if="msg" class="alert alert-success" role="status">{{ msg }}</div>
  <div v-if="err" class="alert alert-danger" role="alert">{{ err }}</div>

  <section class="box">
    <h5>{{ editingId ? 'Edit internship' : 'Post a company-hosted internship' }}</h5>
    <form @submit.prevent="save" class="row g-2">
      <div class="col-md-6"><label for="cn">Company name *</label>
        <input id="cn" class="form-control" v-model="form.company_name" required maxlength="150"></div>
      <div class="col-md-6"><label for="ti">Internship title *</label>
        <input id="ti" class="form-control" v-model="form.title" required maxlength="255"></div>
      <div class="col-md-4"><label for="ca">Category *</label>
        <input id="ca" class="form-control" v-model="form.category" required placeholder="e.g. Web Development"></div>
      <div class="col-md-4"><label for="dl">Application deadline *</label>
        <input id="dl" type="date" class="form-control" v-model="form.application_deadline" required></div>
      <div class="col-md-4"><label for="dw">Duration (weeks) *</label>
        <input id="dw" type="number" min="1" max="104" class="form-control" v-model="form.duration_weeks" required></div>
      <div class="col-md-3"><label for="sd">Start date</label>
        <input id="sd" type="date" class="form-control" v-model="form.start_date"></div>
      <div class="col-md-3"><label for="ed">End date</label>
        <input id="ed" type="date" class="form-control" v-model="form.end_date"></div>
      <div class="col-md-3"><label for="wm">Work mode *</label>
        <select id="wm" class="form-select" v-model="form.work_mode">
          <option value="remote">Remote</option><option value="onsite">On-site</option><option value="hybrid">Hybrid</option>
        </select></div>

        <label>Paid?</label>
        <select v-model="form.is_paid" class="form-select">
        <option :value="0">Unpaid</option>
        <option :value="1">Paid</option>
        </select>

        <label>Stipend (optional)</label>
        <input v-model="form.stipend" class="form-control" placeholder="e.g. PKR 20,000 / month">
        
      <div class="col-md-3"><label for="lo">Location</label>
        <input id="lo" class="form-control" v-model="form.location" maxlength="120"></div>
      <div class="col-12"><label for="al">Company page link (optional)</label>
        <input id="al" type="url" class="form-control" v-model="form.apply_link" placeholder="https://..."></div>
      <div class="col-12"><label for="de">Short description *</label>
        <textarea id="de" rows="2" class="form-control" v-model="form.description" required maxlength="3000"></textarea></div>
      <div class="col-12"><label for="sp">Specifications (skills, tools, responsibilities) *</label>
        <textarea id="sp" rows="4" class="form-control" v-model="form.specifications" required maxlength="5000"></textarea></div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-amber" :disabled="busy">{{ editingId ? 'Save changes' : 'Create (draft)' }}</button>
        <button type="button" class="btn btn-outline-light" v-if="editingId" @click="resetForm">Cancel</button>
      </div>
    </form>
  </section>

  <section class="box">
    <h5>My internships</h5>
    <div v-if="loading">Loading…</div>
    <div v-else-if="!internships.length" class="text-secondary">No internships yet.</div>
    <div class="table-responsive" v-else>
      <table class="table table-dark align-middle">
        <thead><tr><th>Title</th><th>Company</th><th>Deadline</th><th>Status</th><th>Applicants</th><th></th></tr></thead>
        <tbody>
          <tr v-for="i in internships" :key="i.internship_id">
            <td>{{ i.title }}</td><td>{{ i.company_name }}</td><td>{{ i.application_deadline }}</td>
            <td><span class="badge" :class="i.status==='published'?'bg-success':(i.status==='closed'?'bg-secondary':'bg-warning text-dark')">{{ i.status }}</span></td>
            <td>{{ i.applications_count }}</td>
            <td class="text-nowrap">
              <button class="btn btn-sm btn-teal" @click="openApplicants(i)">Applicants</button>
              <button class="btn btn-sm btn-outline-light" @click="edit(i)">Edit</button>
              <button class="btn btn-sm btn-amber" v-if="i.status!=='published'" @click="setStatus(i,'published')">Publish</button>
              <button class="btn btn-sm btn-outline-danger" v-if="i.status==='published'" @click="setStatus(i,'closed')">Close</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="box" v-if="selected">
    <h5>Applicants — {{ selected.title }}</h5>
    <div v-if="!applicants.length" class="text-secondary">No applications yet.</div>

    <div v-for="a in applicants" :key="a.id" class="box" style="background:#0e2650">
      <div class="d-flex flex-wrap justify-content-between gap-2">
        <div><strong>{{ a.applicant ? a.applicant.name : 'User' }}</strong>
          <div class="small text-secondary">{{ a.applicant ? a.applicant.email : '' }}</div></div>
        <div class="d-flex gap-2 align-items-center">
          <select class="form-select form-select-sm" v-model="a.status" :disabled="a.stages.length>0"
                  @change="changeStatus(a)" aria-label="Application status">
            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
          </select>
          <button class="btn btn-sm btn-amber" v-if="a.status==='accepted' && !a.stages.length" @click="start(a)">Start 5-stage workflow</button>
        </div>
      </div>

      <div v-if="a.stages.length" class="mt-2">
        <div class="small">Progress: stage {{ a.current_stage }} / 5 · <span class="badge bg-info text-dark">{{ a.completion_status }}</span></div>

        <div v-for="s in a.stages" :key="s.stage_id" class="stage" :class="s.status">
          <div class="d-flex justify-content-between flex-wrap">
            <strong>{{ s.title }}</strong>
            <span class="badge bg-secondary">{{ s.status }}</span>
          </div>

          <div v-if="s.status==='open' || s.status==='locked'" class="mt-2">
            <label :for="'ins'+s.stage_id">Instructions for the intern</label>
            <textarea :id="'ins'+s.stage_id" rows="2" class="form-control" v-model="s.instructions"></textarea>
            <div class="row g-2 mt-1">
              <div class="col-md-4"><label>Due date</label><input type="date" class="form-control" v-model="s.due_date"></div>
            </div>
          </div>
          <div v-else-if="s.instructions" class="small mt-1">{{ s.instructions }}</div>

          <div v-if="s.stage_no===5 && s.status!=='passed' && s.status!=='failed'" class="row g-2 mt-1">
            <div class="col-md-5"><label>Interview date/time</label><input type="datetime-local" class="form-control" v-model="s.interview_at"></div>
            <div class="col-md-7"><label>Interview link</label><input type="url" class="form-control" v-model="s.interview_link" placeholder="https://meet..."></div>
          </div>
          <button class="btn btn-sm btn-outline-light mt-2" v-if="s.status==='open'||s.status==='locked'" @click="saveStage(s)">Save stage details</button>

          <div v-if="s.status==='submitted'" class="mt-2">
            <div class="small text-warning">Intern submission:</div>
            <pre>{{ s.submission_text }}</pre>
            <button v-if="s.has_file" class="btn btn-sm btn-outline-light mb-2" @click="downloadFile(s)">Download attached file</button>
            <div class="row g-2">
              <div class="col-md-3"><label>Score (0-100)</label><input type="number" min="0" max="100" class="form-control" v-model="s.r.score"></div>
              <div class="col-md-3" v-if="s.stage_no===5"><label>Interview result</label>
                <select class="form-select" v-model="s.r.interview_result"><option value="">Select…</option><option value="passed">Passed</option><option value="failed">Failed</option></select></div>
              <div class="col"><label>Feedback</label><input class="form-control" v-model="s.r.feedback"></div>
            </div>
            <div class="mt-2 d-flex gap-2">
              <button class="btn btn-sm btn-teal" @click="review(s,'passed')">Pass</button>
              <button class="btn btn-sm btn-outline-danger" @click="review(s,'failed')">Fail</button>
            </div>
          </div>
          <div v-if="s.status==='passed'||s.status==='failed'" class="small mt-1">
            Score: {{ s.score ?? '—' }} · {{ s.feedback }} <span v-if="s.stage_no===5">· Interview: {{ s.interview_result }}</span>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-2" v-if="a.completion_status==='in_progress'">
          <button class="btn btn-sm btn-teal" @click="complete(a,'completed')">Mark internship completed</button>
          <button class="btn btn-sm btn-outline-danger" @click="complete(a,'unsuccessful')">Mark unsuccessful</button>
        </div>
        <div v-if="a.completion_status==='completed'" class="mt-2">
          <button class="btn btn-sm btn-amber" v-if="!a.certificate" @click="issue(a)">Issue certificate</button>
          <span v-else class="badge bg-success">Certificate {{ a.certificate.certificate_no }} issued</span>
        </div>
      </div>
    </div>
  </section>
</div>
@endverbatim
</div>

<script src="/js/vue.js"></script>
<script src="/js/session-guard.js"></script>
<script>
const token = localStorage.getItem('aqdesk_token');
if (!token) location.href = '/login';

async function api(url, opts = {}) {
  const headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token };
  if (opts.body && !(opts.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(opts.body);
  }
  const res = await fetch(url, Object.assign({}, opts, { headers: headers }));
  let data = null; try { data = await res.json(); } catch (e) {}
  if (res.status === 401) { localStorage.removeItem('aqdesk_token'); location.href = '/login'; }
  if (!res.ok) {
    let m = (data && data.message) || 'Request failed';
    if (data && data.errors) m = Object.values(data.errors)[0][0];
    throw new Error(m);
  }
  return data;
}

const blank = () => ({ company_name:'', title:'', category:'', description:'', specifications:'',
  application_deadline:'', duration_weeks:'', start_date:'', end_date:'', work_mode:'remote', location:'', apply_link:'' });

Vue.createApp({
  data() {
    return { form: blank(), editingId: null, internships: [], loading: true, busy: false,
      selected: null, applicants: [], msg: '', err: '',
      statuses: ['applied','pending','shortlisted','accepted','rejected'] };
  },
  mounted() { this.load(); },
  methods: {
    flash(m) { this.msg = m; this.err = ''; setTimeout(() => this.msg = '', 4000); },
    fail(e) { this.err = e.message; this.msg = ''; window.scrollTo({ top: 0, behavior: 'smooth' }); },
    async load() { try { this.internships = await api('/api/provider/internships'); } catch (e) { this.fail(e); } this.loading = false; },
    resetForm() { this.form = blank(); this.editingId = null; },
    edit(i) {
      this.editingId = i.internship_id;
      this.form = Object.assign(blank(), {
        company_name: i.company_name, title: i.title, category: i.category, description: i.description,
        specifications: i.specifications, application_deadline: i.application_deadline,
        duration_weeks: i.duration_weeks, start_date: i.start_date, end_date: i.end_date,
        work_mode: i.work_mode, location: i.location, apply_link: i.apply_link });
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    async save() {
      this.busy = true;
      try {
        const url = this.editingId ? '/api/provider/internships/' + this.editingId : '/api/provider/internships';
        await api(url, { method: this.editingId ? 'PUT' : 'POST', body: this.form });
        this.flash(this.editingId ? 'Internship updated' : 'Draft created. Press Publish when ready');
        this.resetForm(); await this.load();
      } catch (e) { this.fail(e); }
      this.busy = false;
    },
    async setStatus(i, status) {
      if (!confirm('Change status to ' + status + '?')) return;
      try { await api('/api/provider/internships/' + i.internship_id + '/status', { method: 'PATCH', body: { status: status } });
            this.flash('Status updated'); await this.load(); } catch (e) { this.fail(e); }
    },
    async openApplicants(i) {
      this.selected = i;
      try {
        const list = await api('/api/provider/internships/' + i.internship_id + '/applications');
        list.forEach(a => a.stages.forEach(s => {
          s.r = { score: s.score ?? '', feedback: '', interview_result: s.interview_result === 'pending' ? '' : s.interview_result };
        }));
        this.applicants = list;
      } catch (e) { this.fail(e); }
    },
    async refresh() { await this.openApplicants(this.selected); await this.load(); },
    async changeStatus(a) {
      try { await api('/api/provider/applications/' + a.id + '/status', { method: 'PATCH', body: { status: a.status } }); this.flash('Applicant status updated'); }
      catch (e) { this.fail(e); this.refresh(); }
    },
    async start(a) {
      if (!confirm('Start the 5-stage workflow for this candidate?')) return;
      try { await api('/api/provider/applications/' + a.id + '/start', { method: 'POST', body: {} }); this.flash('Workflow started'); this.refresh(); }
      catch (e) { this.fail(e); }
    },
    async saveStage(s) {
      try {
        await api('/api/provider/stages/' + s.stage_id, { method: 'PUT', body: {
          title: s.title, instructions: s.instructions, due_date: s.due_date || null,
          interview_at: s.interview_at || null, interview_link: s.interview_link || null } });
        this.flash('Stage saved');
      } catch (e) { this.fail(e); }
    },
    async review(s, status) {
      if (!confirm('Mark this stage as ' + status.toUpperCase() + '?')) return;
      try {
        await api('/api/provider/stages/' + s.stage_id + '/review', { method: 'POST', body: {
          status: status, score: s.r.score === '' ? null : s.r.score, feedback: s.r.feedback,
          interview_result: s.r.interview_result || null } });
        this.flash('Stage reviewed'); this.refresh();
      } catch (e) { this.fail(e); }
    },
    async complete(a, result) {
      if (!confirm('Mark this internship as ' + result + '? This cannot be undone.')) return;
      try { await api('/api/provider/applications/' + a.id + '/complete', { method: 'POST', body: { result: result } });
            this.flash('Internship marked ' + result); this.refresh(); } catch (e) { this.fail(e); }
    },
    async issue(a) {
      if (!confirm('Issue an official certificate to this intern?')) return;
      try { const c = await api('/api/provider/applications/' + a.id + '/certificate', { method: 'POST', body: {} });
            this.flash('Certificate ' + c.certificate_no + ' issued'); this.refresh(); } catch (e) { this.fail(e); }
    },
    async downloadFile(s) {
      try {
        const res = await fetch('/api/stages/' + s.stage_id + '/file', { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) throw new Error('File not available');
        const blob = await res.blob(); const a = document.createElement('a');
        a.href = URL.createObjectURL(blob); a.download = 'submission-' + s.stage_id; a.click();
      } catch (e) { this.fail(e); }
    }
  }
}).mount('#app');
</script>
</body>
</html>