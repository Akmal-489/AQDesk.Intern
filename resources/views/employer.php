<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AQDesk - Employer</title>
<script src="/js/vue.js"></script>
<link href="/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background:#f7f8fa; }
    .box { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:20px; }
    .i-card { border:1px solid #e3e6ea; border-radius:10px; padding:14px 16px; margin-bottom:12px; background:#fff; }
    .nav-pills .nav-link { cursor:pointer; }
</style>
</head>
<body>
@verbatim
<div id="app">

    <nav class="navbar navbar-dark" style="background:#2C5FA8;">
        <div class="container">
            <span class="navbar-brand fw-bold">AQDesk <small class="opacity-75">Employer</small></span>
            <div>
                <span class="text-white me-3" v-if="me">{{ me.company_name || me.name }}</span>
                <button class="btn btn-sm btn-outline-light" @click="logout">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5" v-if="!loading">

        <!-- 1. APPROVAL BANNER -->
        <div class="alert alert-warning" v-if="status === 'pending'">
            <strong>⏳ Your company is pending admin approval.</strong>
            An AQDesk admin is verifying your details. You can look around, but posting is switched off until you are approved.
        </div>
        <div class="alert alert-danger" v-if="status === 'rejected'">
            <strong>Your company application was not approved.</strong>
            <span v-if="me.rejection_reason"> Reason: {{ me.rejection_reason }}</span>
            Please contact the AQDesk admin.
        </div>
        <div class="alert alert-danger" v-if="status === 'missing'">
            <strong>Company profile not found.</strong> Please register through the employer sign-up page or contact the admin.
        </div>
        <div class="alert alert-success py-2" v-if="status === 'approved'">
            ✔ <strong>{{ me.company_name }}</strong> is an approved company.
        </div>

        <div v-if="msg" class="alert alert-success py-2">{{ msg }}</div>
        <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>

        <!-- TABS -->
        <ul class="nav nav-pills mb-3">
            <li class="nav-item"><a class="nav-link" :class="{ active: tab === 'post' }" @click="tab = 'post'">{{ editingId ? 'Edit Internship' : 'Post an Internship' }}</a></li>
            <li class="nav-item"><a class="nav-link" :class="{ active: tab === 'mine' }" @click="tab = 'mine'">My Internships ({{ internships.length }})</a></li>
            <li class="nav-item"><a class="nav-link" :class="{ active: tab === 'applicants' }" @click="tab = 'applicants'">Applicants</a></li>
        </ul>

        <!-- 2. POST AN INTERNSHIP -->
        <div class="box" v-show="tab === 'post'">
            <h5>{{ editingId ? 'Edit internship' : 'Post an internship' }}</h5>
            <p class="text-muted" style="font-size:13px;" v-if="me">
                Posting as <strong>{{ me.company_name || 'your company' }}</strong>. Your internship goes live straight away and students can apply.
            </p>

            <form @submit.prevent="publish">
                <fieldset :disabled="!canPost || busy">
                    <div class="row g-3">
                        <div :class="x.full ? 'col-12' : 'col-12 col-md-6'" v-for="x in fields" :key="x.k">
                            <label class="form-label fw-semibold" style="font-size:14px;">{{ x.l }}<span class="text-danger" v-if="x.req"> *</span></label>

                            <select v-if="x.t === 'select'" class="form-select" v-model="f[x.k]">
                                <option v-for="o in x.o" :key="o[0]" :value="o[0]">{{ o[1] }}</option>
                            </select>
                            <textarea v-else-if="x.t === 'area'" class="form-control" rows="5" v-model="f[x.k]" :placeholder="x.ph"></textarea>
                            <input v-else class="form-control" :type="x.t" v-model="f[x.k]" :placeholder="x.ph" :list="x.list">

                            <div class="form-text" v-if="x.h">{{ x.h }}</div>
                            <div class="text-danger small" v-if="err(x.k)">{{ err(x.k) }}</div>
                        </div>
                    </div>
                    <datalist id="cats"><option>web</option><option>mobile</option><option>data</option><option>design</option><option>marketing</option><option>other</option></datalist>

                    <div class="mt-3">
                        <button class="btn btn-primary me-2">{{ busy ? 'Saving…' : (editingId ? 'Save changes' : 'Publish internship') }}</button>
                        <button type="button" class="btn btn-outline-secondary" v-if="editingId" @click="resetForm">Cancel edit</button>
                    </div>
                </fieldset>
                <div class="text-muted small mt-2" v-if="!canPost">Posting is disabled until your company is approved.</div>
            </form>
        </div>

        <!-- 3. MY INTERNSHIPS -->
        <div v-show="tab === 'mine'">
            <div v-if="!internships.length" class="box text-muted">
                You have not posted any internship yet. Use <strong>Post an Internship</strong> to create your first one.
            </div>
            <div class="i-card" v-for="i in internships" :key="i.internship_id">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="mb-1">{{ i.title }}</h6>
                        <span class="badge" :class="i.status === 'published' ? 'bg-success' : 'bg-secondary'">{{ i.status === 'published' ? 'Open' : 'Closed' }}</span>
                        <span class="badge" :class="i.is_paid ? 'bg-success' : 'bg-secondary'">{{ i.is_paid ? ('Paid' + (i.stipend ? ' · ' + i.stipend : '')) : 'Unpaid' }}</span>
                    </div>
                    <div class="text-end">
                        <div class="fs-4 fw-bold" style="color:#2C5FA8;">{{ i.applicants_count }}</div>
                        <div class="text-muted small">applicants</div>
                    </div>
                </div>
                <div class="text-muted small mt-1">
                    ⏱ {{ i.duration_weeks }} weeks · 📍 {{ i.work_mode }}<span v-if="i.location"> · {{ i.location }}</span>
                    · ⏳ Last date: {{ dateOnly(i.application_deadline) }}
                </div>
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-primary me-1" @click="openApplicants(i)">View applicants</button>
                    <button class="btn btn-sm btn-outline-secondary me-1" @click="edit(i)" :disabled="!canPost">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" v-if="i.status === 'published'" @click="setStatus(i, 'closed')" :disabled="!canPost">Close</button>
                    <button class="btn btn-sm btn-outline-success" v-else @click="setStatus(i, 'published')" :disabled="!canPost">Reopen</button>
                </div>
            </div>
        </div>

        <!-- 4. APPLICANTS -->
        <div class="box" v-show="tab === 'applicants'">
            <h5>Applicants</h5>
            <div v-if="!internships.length" class="text-muted">Post an internship first. Applicants will appear here.</div>
            <div v-else>
                <select class="form-select mb-3" v-model="selectedId" @change="loadApplicants">
                    <option :value="null" disabled>Choose an internship…</option>
                    <option v-for="i in internships" :key="i.internship_id" :value="i.internship_id">{{ i.title }} ({{ i.applicants_count }})</option>
                </select>

                <div v-if="selectedId && !applicants.length" class="text-muted">No one has applied to this internship yet.</div>

                <div class="table-responsive" v-if="applicants.length">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr><th>Candidate</th><th>University / Field</th><th>Applied</th><th>Status</th><th class="text-end">Action</th></tr>
                        </thead>
                        <tbody>
                            <tr v-for="a in applicants" :key="a.application_id">
                                <td><strong>{{ a.name }}</strong><div class="text-muted small">{{ a.email }}</div></td>
                                <td>{{ a.university || '—' }}<div class="text-muted small">{{ a.field_of_study }}</div></td>
                                <td>{{ a.applied_at || '—' }}</td>
                                <td>
                                    <span class="badge" :class="statusClass(a.status)">{{ a.status }}</span>
                                    <div class="small text-muted" v-if="locked(a)">In AQDesk program</div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info me-1" :disabled="locked(a) || a.status === 'shortlisted'" @click="setApplicant(a, 'shortlisted')">Shortlist</button>
                                    <button class="btn btn-sm btn-success me-1" :disabled="locked(a) || a.status === 'accepted'" @click="setApplicant(a, 'accepted')">Accept</button>
                                    <button class="btn btn-sm btn-outline-danger" :disabled="locked(a) || a.status === 'rejected'" @click="setApplicant(a, 'rejected')">Reject</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-muted small mt-2">
                    After you accept a candidate, the AQDesk team runs their 5-stage program (3 tasks, a performance test, and a theory test with interview) and issues the certificate.
                </div>
            </div>
        </div>
    </div>

    <div v-else class="text-center mt-5">Loading...</div>
</div>
@endverbatim

<script>
const { createApp } = Vue;

const BLANK = {
    title: '', description: '', specifications: '', category: '', duration_weeks: '',
    application_deadline: '', work_mode: 'remote', location: '', start_date: '', end_date: '',
    apply_link: '', is_paid: 0, stipend: ''
};

createApp({
    data() {
        return {
            loading: true, busy: false, msg: '', error: '', errors: {},
            tab: 'post', me: null, internships: [], applicants: [], selectedId: null,
            editingId: null, f: Object.assign({}, BLANK),
            fields: [
                { k:'title', l:'Internship title', t:'text', req:true, full:true, ph:'e.g. Laravel Backend Intern', h:'Max 100 characters' },
                { k:'description', l:'Short summary', t:'text', req:true, full:true, ph:'One or two lines shown on the internship card', h:'Max 255 characters' },
                { k:'specifications', l:'About the internship and specifications', t:'area', req:true, full:true, ph:'Responsibilities, required skills, what the intern will learn…', h:'At least 20 characters' },
                { k:'category', l:'Category', t:'text', list:'cats', ph:'e.g. web, data, design', h:'Optional' },
                { k:'duration_weeks', l:'Duration (weeks)', t:'number', req:true, ph:'e.g. 8' },
                { k:'application_deadline', l:'Last date to apply', t:'date', req:true },
                { k:'work_mode', l:'Work mode', t:'select', req:true, o:[['remote','Remote'],['onsite','Onsite'],['hybrid','Hybrid']] },
                { k:'location', l:'Location', t:'text', ph:'City (for onsite or hybrid)' },
                { k:'start_date', l:'Start date (optional)', t:'date' },
                { k:'end_date', l:'End date (optional)', t:'date' },
                { k:'is_paid', l:'Paid or unpaid', t:'select', req:true, o:[[0,'Unpaid'],[1,'Paid']] },
                { k:'stipend', l:'Stipend (optional)', t:'text', ph:'e.g. PKR 20,000 / month' },
                { k:'apply_link', l:'Company page link (optional)', t:'text', full:true, ph:'https://yourcompany.com/careers' },
            ]
        };
    },
    computed: {
        status() { return this.me ? this.me.approval_status : ''; },
        canPost() { return this.status === 'approved'; },
    },
    methods: {
        token() { return localStorage.getItem('aqdesk_token'); },
        api(path, opts = {}) {
            opts.headers = Object.assign({ 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token() }, opts.headers || {});
            return fetch('/api' + path, opts);
        },
        err(k) { return this.errors[k] ? this.errors[k][0] : ''; },
        dateOnly(v) { return v ? String(v).substring(0, 10) : '—'; },
        statusClass(s) {
            return { applied: 'bg-primary', pending: 'bg-warning text-dark', shortlisted: 'bg-info text-dark',
                     accepted: 'bg-success', rejected: 'bg-danger' }[s] || 'bg-secondary';
        },
        locked(a) { return ['in_progress', 'completed', 'unsuccessful'].includes(a.completion_status); },

        async checkRole() {
            if (!this.token()) { window.location.href = '/login'; return false; }
            const r = await this.api('/profile');
            if (!r.ok) { window.location.href = '/login'; return false; }
            const u = await r.json();
            const role = u.role ? u.role.role_name : '';
            if (role !== 'employer') { window.location.href = role === 'admin' ? '/admin' : '/dashboard'; return false; }
            return true;
        },
        async loadMe() {
            const r = await this.api('/employer/me');
            if (r.ok) this.me = await r.json();
        },
        async loadInternships() {
            const r = await this.api('/employer/internships');
            if (r.ok) { const d = await r.json(); if (Array.isArray(d)) this.internships = d; }
        },

        resetForm() { this.f = Object.assign({}, BLANK); this.editingId = null; this.errors = {}; },

        edit(i) {
            this.editingId = i.internship_id;
            this.f = {
                title: i.title, description: i.description, specifications: i.specifications || '',
                category: i.category === 'other' ? '' : (i.category || ''),
                duration_weeks: i.duration_weeks,
                application_deadline: this.dateOnly(i.application_deadline) === '—' ? '' : this.dateOnly(i.application_deadline),
                work_mode: i.work_mode || 'remote', location: i.location || '',
                start_date: i.start_date ? this.dateOnly(i.start_date) : '',
                end_date: i.end_date ? this.dateOnly(i.end_date) : '',
                apply_link: i.apply_link || '', is_paid: i.is_paid ? 1 : 0, stipend: i.stipend || ''
            };
            this.errors = {}; this.msg = ''; this.error = '';
            this.tab = 'post';
            window.scrollTo(0, 0);
        },

        async publish() {
            this.errors = {}; this.msg = ''; this.error = '';
            this.busy = true;
            const body = {};
            Object.keys(this.f).forEach(k => { body[k] = this.f[k] === '' ? null : this.f[k]; });
            const editing = this.editingId;
            try {
                const r = await this.api('/employer/internships' + (editing ? '/' + editing : ''), {
                    method: editing ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const d = await r.json().catch(() => ({}));
                if (r.ok) {
                    this.msg = editing ? 'Changes saved.' : 'Internship published. Students can now see it and apply.';
                    this.resetForm();
                    await this.loadInternships();
                    this.tab = 'mine';
                } else if (r.status === 422) {
                    this.errors = d.errors || {};
                    this.error = 'Please fix the highlighted fields.';
                } else {
                    this.error = d.message || 'Could not save the internship.';
                }
            } catch (e) { this.error = 'Network error. Please try again.'; }
            this.busy = false;
        },

        async setStatus(i, status) {
            this.msg = ''; this.error = '';
            const r = await this.api('/employer/internships/' + i.internship_id + '/status', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ status })
            });
            const d = await r.json().catch(() => ({}));
            if (r.ok) { this.msg = status === 'closed' ? 'Internship closed.' : 'Internship reopened.'; await this.loadInternships(); }
            else this.error = d.message || 'Could not update.';
        },

        openApplicants(i) {
            this.selectedId = i.internship_id;
            this.tab = 'applicants';
            this.loadApplicants();
        },
        async loadApplicants() {
            this.applicants = [];
            if (!this.selectedId) return;
            const r = await this.api('/employer/internships/' + this.selectedId + '/applicants');
            if (r.ok) { const d = await r.json(); if (Array.isArray(d)) this.applicants = d; }
        },
        async setApplicant(a, status) {
            this.msg = ''; this.error = '';
            const r = await this.api('/employer/applications/' + a.application_id + '/status', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ status })
            });
            const d = await r.json().catch(() => ({}));
            if (r.ok) { this.msg = 'Applicant marked as ' + status + '.'; await this.loadApplicants(); }
            else this.error = d.message || 'Could not update.';
        },

        logout() {
            localStorage.removeItem('aqdesk_token');
            localStorage.removeItem('aqdesk_user');
            window.location.href = '/login';
        }
    },
    async mounted() {
        if (!(await this.checkRole())) return;
        await Promise.all([this.loadMe(), this.loadInternships()]);
        this.loading = false;
    }
}).mount('#app');
</script>
</body>
</html>