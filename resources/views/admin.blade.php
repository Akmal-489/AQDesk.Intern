<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AQDesk - Admin</title>
<script src="/js/vue.js"></script>
<link href="/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f7f8fa; }
    .box { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:18px; margin-bottom:16px; }
    .stat { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:16px; text-align:center; height:100%; }
    .stat .n { font-size:28px; font-weight:700; color:#2C5FA8; }
    .nav-pills .nav-link { cursor:pointer; }
    .note { display:flex; gap:10px; padding:9px 0; border-bottom:1px solid #f0f0f0; cursor:pointer; font-size:14px; }
    .note:last-child { border-bottom:0; }
    .stage-card { border-left:4px solid #adb5bd; background:#fafbfc; border-radius:8px; padding:12px 14px; margin:12px 0; }
    .stage-card.passed { border-color:#2E7D4F; }
    .stage-card.open, .stage-card.submitted { border-color:#B8791A; }
    .stage-card.failed { border-color:#c0392b; }
    .stage-card.locked { opacity:.75; }
    .sub-text { white-space:pre-wrap; font-size:13px; background:#fff; border:1px solid #e3e6ea; border-radius:6px; padding:8px; max-height:180px; overflow:auto; }
</style>
</head>
<body>
@verbatim
<div id="app">

    <nav class="navbar navbar-dark" style="background:#1d0358;">
        <div class="container">
            <span class="navbar-brand fw-bold">AQDesk <small class="opacity-75">Admin</small></span>
            <div>
                <span class="text-white me-3" v-if="me">{{ me.name }}</span>
                <button class="btn btn-sm btn-outline-light" @click="logout">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5" v-if="!loading">

        <ul class="nav nav-pills mb-3 flex-wrap">
            <li class="nav-item" v-for="t in tabs" :key="t.k">
                <a class="nav-link" :class="{ active: tab === t.k }" @click="tab = t.k">
                    {{ t.l }}<span class="badge bg-danger ms-1" v-if="t.k === 'companies' && stats && stats.pending_companies">{{ stats.pending_companies }}</span>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="/talent-admin">🌟 Talent Program</a></li>
        </ul>

        <div v-if="msg" class="alert alert-success py-2">{{ msg }}</div>
        <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>

        <!-- ================= OVERVIEW ================= -->
        <div v-show="tab === 'overview'">
            <div class="row g-3 mb-3" v-if="stats">
                <div class="col-6 col-md-3" v-for="c in statCards" :key="c.l">
                    <div class="stat"><div class="n">{{ c.n }}</div><div class="text-muted small">{{ c.l }}</div></div>
                </div>
            </div>
            <div class="box">
                <h5>🔔 Notifications</h5>
                <div v-if="!notes.length" class="text-muted">Nothing new right now.</div>
                <div class="note" v-for="(n, idx) in notes" :key="idx" @click="tab = n.tab">
                    <div>{{ n.icon }}</div>
                    <div class="flex-grow-1">{{ n.text }}</div>
                    <div class="text-muted small">{{ n.time ? String(n.time).substring(0, 16).replace('T', ' ') : '' }}</div>
                </div>
            </div>
        </div>

        <!-- ================= COMPANIES ================= -->
        <div v-show="tab === 'companies'">
            <div class="mb-3">
                <button class="btn btn-sm me-1" :class="empFilter === f ? 'btn-primary' : 'btn-outline-primary'"
                        v-for="f in ['pending','approved','rejected','all']" :key="f" @click="empFilter = f">{{ f }}</button>
            </div>
            <div class="box text-muted" v-if="!shownEmployers.length">No companies in this list.</div>
            <div class="box" v-for="p in shownEmployers" :key="p.user_id">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="mb-0">{{ p.company_name }}</h6>
                        <div class="text-muted small">{{ p.industry }} · {{ p.city }}, {{ p.country }}<span v-if="p.company_size"> · {{ p.company_size }} staff</span></div>
                    </div>
                    <span class="badge align-self-start" :class="p.approval_status === 'approved' ? 'bg-success' : (p.approval_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark')">{{ p.approval_status }}</span>
                </div>
                <div class="small mt-2">
                    <div><strong>Website:</strong> <a :href="p.website" target="_blank" rel="noopener noreferrer">{{ p.website }}</a></div>
                    <div><strong>Contact:</strong> {{ p.contact_name }} ({{ p.contact_designation }}) · {{ p.email }} · {{ p.phone }}</div>
                    <div v-if="p.registration_no"><strong>Registration no:</strong> {{ p.registration_no }}</div>
                    <div class="mt-1">{{ p.description }}</div>
                    <div class="text-danger" v-if="p.rejection_reason">Rejected: {{ p.rejection_reason }}</div>
                </div>
                <div class="mt-2">
                    <button class="btn btn-sm btn-success me-1" v-if="p.approval_status !== 'approved'" :disabled="busy" @click="approve(p)">Approve</button>
                    <button class="btn btn-sm btn-outline-danger" v-if="p.approval_status !== 'rejected'" :disabled="busy" @click="reject(p)">Reject</button>
                </div>
            </div>
        </div>

        <!-- ================= INTERNS & STAGES ================= -->
        <div v-show="tab === 'programs'">
            <div class="box">
                <h5>Accepted candidates and active programs</h5>
                <div v-if="!programs.length" class="text-muted">No accepted candidates yet. When an employer accepts an applicant, they appear here.</div>
                <div class="table-responsive" v-else>
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Candidate</th><th>Internship</th><th>Program</th><th>Progress</th><th class="text-end"></th></tr></thead>
                        <tbody>
                            <tr v-for="p in programs" :key="p.id" :class="{ 'table-active': detail && detail.id === p.id }">
                                <td><strong>{{ p.name }}</strong><div class="text-muted small">{{ p.email }}</div></td>
                                <td>{{ p.title }}<div class="text-muted small">{{ p.company }}</div></td>
                                <td><span class="badge" :class="progClass(p)">{{ progLabel(p) }}</span></td>
                                <td>
                                    <span v-if="p.certificate">🎓 {{ p.certificate }}</span>
                                    <span v-else-if="p.stages">{{ p.passed }} of 5 passed</span>
                                    <span v-else class="text-muted">—</span>
                                </td>
                                <td class="text-end"><button class="btn btn-sm btn-primary" @click="openProgram(p.id)">Manage</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- program detail -->
            <div class="box" v-if="detail">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">{{ detail.student.name }} <small class="text-muted">{{ detail.student.email }}</small></h5>
                        <div class="text-muted">{{ detail.internship.title }} — {{ detail.internship.company_name }}</div>
                    </div>
                    <div>
                        <span class="badge" :class="progClass(detail)">{{ progLabel(detail) }}</span>
                        <button class="btn btn-sm btn-outline-secondary ms-2" @click="detail = null">Close</button>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-sm btn-primary me-2" v-if="!detail.stages.length && detail.status === 'accepted'" :disabled="busy" @click="startProgram">Start the 5-stage program</button>
                    <button class="btn btn-sm btn-success me-2" v-if="detail.completion_status === 'in_progress'" :disabled="busy || !allPassed" @click="completeProgram">Mark internship completed</button>
                    <button class="btn btn-sm btn-outline-danger me-2" v-if="detail.completion_status === 'in_progress'" :disabled="busy" @click="failProgram">Mark not successful</button>
                    <button class="btn btn-sm btn-warning me-2" v-if="detail.completion_status === 'completed' && !detail.certificate" :disabled="busy" @click="issueCert">🎓 Issue certificate</button>
                    <a class="btn btn-sm btn-outline-secondary" v-if="detail.certificate" :href="'/certificate/' + detail.certificate.certificate_no" target="_blank">View certificate {{ detail.certificate.certificate_no }}</a>
                    <div class="text-muted small mt-1" v-if="detail.completion_status === 'in_progress' && !allPassed">The completion button unlocks once all 5 stages are passed.</div>
                </div>

                <div v-if="!detail.stages.length" class="text-muted mt-3">The program has not started yet.</div>

                <div class="stage-card" :class="s.status" v-for="s in detail.stages" :key="s.stage_id">
                    <div class="d-flex justify-content-between">
                        <strong>Stage {{ s.stage_no }} · {{ s.title }}</strong>
                        <span class="badge" :class="stageClass(s.status)">{{ s.status }}</span>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-6"><label class="small">Title</label><input class="form-control form-control-sm" v-model="s.edit.title"></div>
                        <div class="col-md-6" v-if="s.stage_no !== 5"><label class="small">Due date</label><input type="date" class="form-control form-control-sm" v-model="s.edit.due_date"></div>
                        <div class="col-md-3" v-if="s.stage_no === 5"><label class="small">Interview time</label><input type="datetime-local" class="form-control form-control-sm" v-model="s.edit.interview_at"></div>
                        <div class="col-md-3" v-if="s.stage_no === 5"><label class="small">Interview link</label><input class="form-control form-control-sm" v-model="s.edit.interview_link" placeholder="https://meet.google.com/..."></div>
                        <div class="col-12"><label class="small">Instructions for the intern</label><textarea rows="2" class="form-control form-control-sm" v-model="s.edit.instructions"></textarea></div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary mt-2" :disabled="busy" @click="saveStage(s)">Save details</button>

                    <!-- review panel -->
                    <div v-if="s.status === 'open' || s.status === 'submitted'" class="mt-3 p-2 border rounded" style="background:#fff;">
                        <div v-if="s.submitted_at">
                            <strong class="small">Submitted work</strong>
                            <div class="sub-text" v-if="s.submission_text">{{ s.submission_text }}</div>
                            <button class="btn btn-sm btn-outline-primary mt-1" v-if="s.has_file" @click="downloadFile(s)">⬇ Download attached file</button>
                        </div>
                        <div v-else class="text-muted small">No submission yet. You can still score this stage, for example after a live test or interview.</div>

                        <div class="row g-2 mt-1">
                            <div class="col-md-3"><label class="small">Score (0-100)</label><input type="number" min="0" max="100" class="form-control form-control-sm" v-model="s.rev.score"></div>
                            <div class="col-md-9"><label class="small">Feedback</label><input class="form-control form-control-sm" v-model="s.rev.feedback" placeholder="Short feedback for the intern"></div>
                        </div>
                        <button class="btn btn-sm btn-success mt-2 me-1" :disabled="busy" @click="review(s, 'pass')">Pass</button>
                        <button class="btn btn-sm btn-outline-danger mt-2" :disabled="busy" @click="review(s, 'fail')">Fail</button>
                    </div>

                    <div v-if="s.status === 'passed' || s.status === 'failed'" class="small mt-2">
                        Score: <strong>{{ s.score }}</strong><span v-if="s.feedback"> · {{ s.feedback }}</span>
                        <button class="btn btn-sm btn-outline-warning ms-2" v-if="s.status === 'failed' && detail.completion_status === 'in_progress'" :disabled="busy" @click="reopen(s)">Allow resubmission</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= CERTIFICATES ================= -->
        <div class="box" v-show="tab === 'certificates'">
            <h5>Certificates</h5>
            <div v-if="!certs.length" class="text-muted">No certificates issued yet.</div>
            <div class="table-responsive" v-else>
                <table class="table table-sm align-middle">
                    <thead><tr><th>Number</th><th>Candidate</th><th>Internship</th><th>Completed</th><th>Status</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        <tr v-for="c in certs" :key="c.certificate_id">
                            <td><strong>{{ c.certificate_no }}</strong></td>
                            <td>{{ c.candidate_name }}</td>
                            <td>{{ c.internship_title }}<div class="text-muted small">{{ c.company_name }}</div></td>
                            <td>{{ dateOnly(c.completion_date) }}</td>
                            <td><span class="badge" :class="c.status === 'valid' ? 'bg-success' : 'bg-danger'">{{ c.status }}</span></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary me-1" :href="'/certificate/' + c.certificate_no" target="_blank">View</a>
                                <button class="btn btn-sm btn-outline-danger" v-if="c.status === 'valid'" :disabled="busy" @click="setCert(c, 'revoked')">Revoke</button>
                                <button class="btn btn-sm btn-outline-success" v-else :disabled="busy" @click="setCert(c, 'valid')">Restore</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= INTERNSHIPS ================= -->
        <div class="box" v-show="tab === 'internships'">
            <h5>All internships</h5>
            <div v-if="!internships.length" class="text-muted">No internships yet.</div>
            <div class="table-responsive" v-else>
                <table class="table table-sm align-middle">
                    <thead><tr><th>Title</th><th>Company</th><th>Posted by</th><th>Last date</th><th>Applicants</th><th>Status</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        <tr v-for="i in internships" :key="i.internship_id">
                            <td>{{ i.title }}<div class="text-muted small">{{ i.hosting_type }}</div></td>
                            <td>{{ i.company_name }}</td>
                            <td>{{ i.posted_by_name }}</td>
                            <td>{{ dateOnly(i.application_deadline) }}</td>
                            <td>{{ i.applicants_count }}</td>
                            <td><span class="badge" :class="i.status === 'published' ? 'bg-success' : 'bg-secondary'">{{ i.status }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger me-1" v-if="i.status === 'published'" :disabled="busy" @click="setInternship(i, 'closed')">Close</button>
                                <button class="btn btn-sm btn-outline-success me-1" v-else :disabled="busy" @click="setInternship(i, 'published')">Reopen</button>
                                <button class="btn btn-sm btn-outline-dark" v-if="i.applicants_count === 0" :disabled="busy" @click="removeInternship(i)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= USERS ================= -->
        <div class="box" v-show="tab === 'users'">
            <h5>Users</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        <tr v-for="u in users" :key="u.id">
                            <td>{{ u.name }}</td>
                            <td>{{ u.email }}</td>
                            <td><span class="badge bg-light text-dark border">{{ u.role_name }}</span></td>
                            <td><span class="badge" :class="u.status === 'suspended' ? 'bg-danger' : 'bg-success'">{{ u.status }}</span></td>
                            <td class="text-end">
                                <span v-if="u.role_name === 'admin'" class="text-muted small">—</span>
                                <button class="btn btn-sm btn-outline-danger" v-else-if="u.status !== 'suspended'" :disabled="busy" @click="setUser(u, 'suspended')">Suspend</button>
                                <button class="btn btn-sm btn-outline-success" v-else :disabled="busy" @click="setUser(u, 'active')">Activate</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div v-else class="text-center mt-5">Loading...</div>
</div>
@endverbatim

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            loading: true, busy: false, msg: '', error: '', me: null,
            tab: 'overview',
            tabs: [
                { k: 'overview', l: '📊 Overview' }, { k: 'companies', l: '🏢 Companies' },
                { k: 'programs', l: '🚀 Interns & Stages' }, { k: 'certificates', l: '🎓 Certificates' },
                { k: 'internships', l: '📋 Internships' }, { k: 'users', l: '👥 Users' },
            ],
            stats: null, notes: [], employers: [], empFilter: 'pending',
            programs: [], detail: null, certs: [], internships: [], users: [],
        };
    },
    computed: {
        statCards() {
            const s = this.stats || {};
            return [
                { l: 'Employees', n: s.employees }, { l: 'Companies', n: s.employers },
                { l: 'Companies pending', n: s.pending_companies }, { l: 'Open internships', n: s.open_internships },
                { l: 'Applications', n: s.applications }, { l: 'Awaiting program start', n: s.awaiting_start },
                { l: 'In program', n: s.in_program }, { l: 'Completed', n: s.completed },
                { l: 'Certificates issued', n: s.certificates },
            ];
        },
        shownEmployers() {
            return this.empFilter === 'all' ? this.employers : this.employers.filter(p => p.approval_status === this.empFilter);
        },
        allPassed() {
            return !!this.detail && this.detail.stages.length === 5 && this.detail.stages.every(s => s.status === 'passed');
        },
    },
    watch: {
        tab(t) { this.loadTab(t); }
    },
    methods: {
        token() { return localStorage.getItem('aqdesk_token'); },
        api(path, opts = {}) {
            opts.headers = Object.assign({ 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token() }, opts.headers || {});
            return fetch('/api' + path, opts);
        },
        async call(path, method = 'GET', body = null) {
            const opts = { method, headers: {} };
            if (body !== null) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
            const r = await this.api(path, opts);
            const data = await r.json().catch(() => ({}));
            return { ok: r.ok, status: r.status, data };
        },
        // run an action, show the result, then refresh
        async act(path, method, body, okMsg, after) {
            this.msg = ''; this.error = ''; this.busy = true;
            const r = await this.call(path, method, body);
            if (r.ok) {
                this.msg = okMsg || r.data.message || 'Done.';
                if (after) await after();
            } else {
                this.error = r.data.message || (r.data.errors ? Object.values(r.data.errors)[0][0] : 'Something went wrong.');
            }
            this.busy = false;
            return r.ok;
        },
        dateOnly(v) { return v ? String(v).substring(0, 10) : '—'; },

        progLabel(p) {
            return { in_progress: 'In progress', completed: 'Completed', unsuccessful: 'Not successful' }[p.completion_status] || 'Ready to start';
        },
        progClass(p) {
            return { in_progress: 'bg-warning text-dark', completed: 'bg-success', unsuccessful: 'bg-danger' }[p.completion_status] || 'bg-secondary';
        },
        stageClass(s) {
            return { passed: 'bg-success', failed: 'bg-danger', open: 'bg-warning text-dark', submitted: 'bg-info text-dark' }[s] || 'bg-secondary';
        },

        async checkRole() {
            if (!this.token()) { window.location.href = '/login'; return false; }
            const r = await this.api('/profile');
            if (!r.ok) { window.location.href = '/login'; return false; }
            const u = await r.json();
            const role = u.role ? u.role.role_name : '';
            if (role !== 'admin') { window.location.href = role === 'employer' ? '/employer' : '/dashboard'; return false; }
            this.me = u;
            return true;
        },

        async loadTab(t) {
            if (t === 'overview')     return this.loadOverview();
            if (t === 'companies')    return this.loadCompanies();
            if (t === 'programs')     return this.loadPrograms();
            if (t === 'certificates') return this.loadCerts();
            if (t === 'internships')  return this.loadInternships();
            if (t === 'users')        return this.loadUsers();
        },
        async loadOverview() {
            const r = await this.call('/admin/overview');
            if (r.ok) { this.stats = r.data.stats; this.notes = r.data.notes || []; }
        },
        async loadCompanies()   { const r = await this.call('/admin/employers');    if (r.ok && Array.isArray(r.data)) this.employers = r.data; },
        async loadPrograms()    { const r = await this.call('/admin/programs');     if (r.ok && Array.isArray(r.data)) this.programs = r.data; },
        async loadCerts()       { const r = await this.call('/admin/certificates'); if (r.ok && Array.isArray(r.data)) this.certs = r.data; },
        async loadInternships() { const r = await this.call('/admin/internships');  if (r.ok && Array.isArray(r.data)) this.internships = r.data; },
        async loadUsers()       { const r = await this.call('/admin/users');        if (r.ok && Array.isArray(r.data)) this.users = r.data; },

        // ---- companies ----
        approve(p) {
            this.act('/admin/employers/' + p.user_id + '/approve', 'POST', null, 'Company approved.',
                async () => { await this.loadCompanies(); await this.loadOverview(); });
        },
        reject(p) {
            const reason = window.prompt('Reason for rejecting this company (the company will see it):');
            if (!reason) return;
            this.act('/admin/employers/' + p.user_id + '/reject', 'POST', { reason }, 'Company rejected.',
                async () => { await this.loadCompanies(); await this.loadOverview(); });
        },

        // ---- interns & stages ----
        async openProgram(id) {
            const r = await this.call('/admin/programs/' + id);
            if (!r.ok) { this.error = r.data.message || 'Could not load this program.'; return; }
            const d = r.data;
            d.stages.forEach(s => {
                s.edit = {
                    title: s.title,
                    instructions: s.instructions || '',
                    due_date: s.due_date ? String(s.due_date).substring(0, 10) : '',
                    interview_at: s.interview_at || '',
                    interview_link: s.interview_link || '',
                };
                s.rev = { score: s.score !== null && s.score !== undefined ? s.score : '', feedback: s.feedback || '' };
            });
            this.detail = d;
        },
        async refresh() {
            await Promise.all([this.openProgram(this.detail.id), this.loadPrograms(), this.loadOverview()]);
        },
        startProgram() {
            this.act('/admin/programs/' + this.detail.id + '/start', 'POST', null, null, () => this.refresh());
        },
        completeProgram() {
            this.act('/admin/programs/' + this.detail.id + '/complete', 'POST', null, null, () => this.refresh());
        },
        failProgram() {
            if (!window.confirm('Mark this internship as not successful? No certificate can be issued.')) return;
            this.act('/admin/programs/' + this.detail.id + '/unsuccessful', 'POST', null, null, () => this.refresh());
        },
        issueCert() {
            this.act('/admin/programs/' + this.detail.id + '/certificate', 'POST', null, 'Certificate issued.', () => this.refresh());
        },
        saveStage(s) {
            const e = s.edit;
            this.act('/admin/stages/' + s.stage_id, 'PUT', {
                title: e.title,
                instructions: e.instructions || null,
                due_date: e.due_date || null,
                interview_at: e.interview_at || null,
                interview_link: e.interview_link || null,
            }, 'Stage details saved.', () => this.refresh());
        },
        review(s, result) {
            if (s.rev.score === '' || s.rev.score === null) { this.error = 'Enter a score from 0 to 100 first.'; return; }
            this.act('/admin/stages/' + s.stage_id + '/review', 'POST',
                { result, score: Number(s.rev.score), feedback: s.rev.feedback || null },
                result === 'pass' ? 'Stage passed. The next stage is now open.' : 'Stage marked as failed.',
                () => this.refresh());
        },
        reopen(s) {
            this.act('/admin/stages/' + s.stage_id + '/reopen', 'POST', null, null, () => this.refresh());
        },
        async downloadFile(s) {
            const r = await this.api('/stages/' + s.stage_id + '/file');
            if (!r.ok) { this.error = 'The file is not available.'; return; }
            const blob = await r.blob();
            const cd = r.headers.get('Content-Disposition') || '';
            const m = cd.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = m ? decodeURIComponent(m[1]) : 'submission-stage-' + s.stage_no;
            a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 5000);
        },

        // ---- certificates ----
        setCert(c, status) {
            this.act('/admin/certificates/' + c.certificate_id + '/status', 'POST', { status }, null, () => this.loadCerts());
        },

        // ---- internships ----
        setInternship(i, status) {
            this.act('/admin/internships/' + i.internship_id + '/status', 'POST', { status }, null, () => this.loadInternships());
        },
        removeInternship(i) {
            if (!window.confirm('Delete "' + i.title + '" permanently?')) return;
            this.act('/admin/internships/' + i.internship_id, 'DELETE', null, null, () => this.loadInternships());
        },

        // ---- users ----
        setUser(u, status) {
            if (status === 'suspended' && !window.confirm('Suspend ' + u.name + '? They will be logged out and unable to log in.')) return;
            this.act('/admin/users/' + u.id + '/status', 'POST', { status }, null, () => this.loadUsers());
        },

        logout() {
            localStorage.removeItem('aqdesk_token');
            localStorage.removeItem('aqdesk_user');
            window.location.href = '/login';
        }
    },
    async mounted() {
        if (!(await this.checkRole())) return;
        await this.loadOverview();
        this.loading = false;
    }
}).mount('#app');
</script>
</body>
</html>