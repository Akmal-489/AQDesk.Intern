<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AQDesk - Dashboard</title>
    <script src="/js/vue.js"></script>
    <script src="/js/session-guard.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="/css/mobile.css">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f8fa; }
        .topbar { display:flex; justify-content:space-between; align-items:center; padding:14px 5%; border-bottom:1px solid var(--line); background:#fff; }
        .logo { font-weight:700; font-size:19px; text-decoration:none; color:var(--ink); }
        .logo span { color:var(--amber); }
        .back1 { text-decoration:none; color:var(--ink); font-weight:600; font-size:15px; padding:8px 16px; border:1.5px solid ; border-radius:30px; transition:.25s; }
        .back1:hover { background:var(--ink); color:#fff; }
        .progress-bar-bg { background:#eee; border-radius:6px; height:10px; width:100%; }
        .progress-bar-fill { background:#2E7D4F; height:10px; border-radius:6px; transition:width .4s; }
        .stat-box { text-align:center; padding:18px; border-radius:8px; background:white; box-shadow:0 1px 4px rgba(0,0,0,0.08); }
        .stat-num { font-size:28px; font-weight:700; }
        .internship-card { border:1px solid #ddd; border-radius:8px; padding:16px; margin-bottom:12px; background:white; cursor:pointer; }
        .internship-card:hover { border-color:#2C5FA8; }
        .internship-actions { margin-top:10px; padding-top:10px; border-top:1px solid #eee; }
        nav { display:flex; justify-content:space-between; align-items:center; padding:22px 6%; border-bottom:1px solid var(--line); }
        nav a { color: var(--ink); text-decoration:none; margin-left:28px; font-size:15px; font-weight:500; }
        .nav-cta { background: var(--ink); color:white !important; padding:9px 18px; border-radius:4px; }
        .nav1 { background-color:#1d0358; }
        .nav-links { display:flex; align-items:center; }

        /* 5-stage journey */
        .stage-row { border-left:4px solid #adb5bd; padding:8px 12px; margin:8px 0; background:#fafbfc; border-radius:6px; }
        .stage-row.passed { border-color:#2E7D4F; }
        .stage-row.open, .stage-row.submitted { border-color:#B8791A; }
        .stage-row.failed { border-color:#c0392b; }
        .stage-row.locked { opacity:.6; }
        .stage-dots { display:flex; gap:6px; margin:10px 0; }
        .stage-dot { flex:1; text-align:center; font-size:12px; padding:6px 2px; border-radius:6px; background:#eee; border:2px solid #ddd; }
        .stage-dot.passed { background:#e6f4ea; border-color:#2E7D4F; }
        .stage-dot.open, .stage-dot.submitted { background:#fff6e5; border-color:#B8791A; }
        .stage-dot.failed { background:#fdecea; border-color:#c0392b; }
        .spec-text { white-space:pre-line; font-size:13px; }

        /* ranking checklist + upcoming */
        .rank-row { display:flex; gap:10px; align-items:flex-start; padding:7px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
        .rank-row:last-child { border-bottom:0; }
        .rank-ico { font-weight:700; width:20px; text-align:center; }
        .rank-ok { color:#2E7D4F; }
        .rank-no { color:#c0392b; }
        .up-row { display:flex; gap:12px; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
        .up-row:last-child { border-bottom:0; }
        .up-date { min-width:92px; font-weight:600; color:#2C5FA8; }

        @media (max-width: 720px) {
            nav { position:relative; }
            .nav-links { display:none; position:absolute; top:100%; left:0; right:0; background:#fff; flex-direction:column; align-items:stretch; padding:8px 6% 18px; border-bottom:1px solid var(--line); z-index:20; box-shadow:0 10px 20px rgba(0,0,0,.08); }
            .nav-links.open { display:flex; }
            .nav-links a { margin:0; padding:14px 0; border-bottom:1px solid var(--line); }
            .nav-links a.nav-cta { margin-top:14px; text-align:center; border-bottom:0; }
        }
    </style>
</head>
<body>

<div class="topbar">
  <a href="/home" class="logo"><u>Home</u></a>
  <a href="/internships" class="back1">← Internships</a>
</div>

@verbatim
<div id="app">

    <nav class="navbar navbar-dark" style="background:#2C5FA8;">
        <div class="container">
            <span class="navbar-brand fw-bold">AQDesk</span>
            <div>
                <span class="text-white me-3" v-if="user">{{ user.name }} ({{ roleName }})</span>
                <button class="btn btn-sm btn-outline-light" @click="logout">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mt-4" v-if="!loading">

        <!-- 1. WELCOME -->
        <div class="p-4 mb-4 rounded" style="background:#EAF2FB;">
            <h3 class="mb-1">Welcome back, {{ user.name }}!</h3>
            <p class="text-muted mb-0">Role: <strong>{{ roleName }}</strong> · Track your applications, internship stages and certificates here.</p>
        </div>

        <p class="text-success" v-if="successMessage">{{ successMessage }}</p>
        <p class="text-danger" v-if="errorMessage">{{ errorMessage }}</p>

        <!-- 2. STATS -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num" style="color:#2C5FA8;">{{ appRows.length }}</div>
                    <div class="text-muted">Applications</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num" style="color:#B8791A;">{{ countStatus('shortlisted') }}</div>
                    <div class="text-muted">Shortlisted</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num" style="color:#6f42c1;">{{ countStatus('accepted') }}</div>
                    <div class="text-muted">Accepted</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-num" style="color:#2E7D4F;">{{ completedCount }}</div>
                    <div class="text-muted">Completed</div>
                </div>
            </div>
        </div>

        <!-- 3. MY INTERNSHIP JOURNEY (always visible) -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>🚀 My Internship Journey</h5>
            <p class="text-muted" style="font-size:13px;">Complete all 5 stages to finish your internship and earn a certificate.</p>
            <div v-if="wfMsg" class="alert alert-info py-2">{{ wfMsg }}</div>

            <!-- empty state -->
            <div v-if="!activeWorkflows.length">
                <div class="stage-dots">
                    <div v-for="n in 5" :key="'p' + n" class="stage-dot">Stage {{ n }}<br><small>{{ stageNames[n - 1] }}</small></div>
                </div>
                <p class="text-muted mb-2" style="font-size:13px;">
                    You have no active internship yet. Apply to an internship below. Once you are accepted, the AQDesk team starts your 5 stages and they appear here.
                </p>
                <a href="/internships" class="btn btn-sm btn-primary">Browse internships</a>
            </div>

            <div v-for="w in activeWorkflows" :key="w.id" class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-1">
                    <div>
                        <strong>{{ w.internship ? w.internship.title : 'Internship' }}</strong>
                        <span class="text-muted" style="font-size:13px;" v-if="w.internship && w.internship.company_name"> — {{ w.internship.company_name }}</span>
                    </div>
                    <span class="badge" :class="completionClass(w)">{{ completionLabel(w) }}</span>
                </div>

                <div v-if="!w.stages.length" class="text-muted mt-2" style="font-size:13px;">
                    You have been accepted! The AQDesk team will start your 5 stages soon.
                </div>

                <div v-if="w.stages.length">
                    <div class="d-flex justify-content-between mt-2" style="font-size:13px;">
                        <span>Progress</span><strong>{{ passedCount(w) }} of 5 stages passed</strong>
                    </div>
                    <div class="progress-bar-bg"><div class="progress-bar-fill" :style="{ width: (passedCount(w) / 5 * 100) + '%' }"></div></div>

                    <div class="stage-dots">
                        <div v-for="s in sortedStages(w)" :key="'d' + s.stage_id" class="stage-dot" :class="s.status">
                            Stage {{ s.stage_no }}<br><small>{{ s.status }}</small>
                        </div>
                    </div>

                    <div v-for="s in sortedStages(w)" :key="s.stage_id" class="stage-row" :class="s.status">
                        <div class="d-flex justify-content-between">
                            <strong style="font-size:14px;">{{ s.stage_no }}. {{ s.title }}</strong>
                            <span class="badge bg-secondary">{{ s.status }}</span>
                        </div>
                        <div class="spec-text" v-if="s.instructions">{{ s.instructions }}</div>
                        <div class="text-muted" style="font-size:12px;" v-if="s.due_date">Due: {{ dateOnly(s.due_date) }}</div>
                        <div style="font-size:13px;" v-if="s.stage_no === 5 && (s.interview_at || s.interview_link)">
                            Interview: {{ s.interview_at ? s.interview_at.replace('T', ' ') : 'To be announced' }}
                            <a v-if="s.interview_link" :href="s.interview_link" target="_blank" rel="noopener noreferrer">Join link</a>
                        </div>

                        <form v-if="s.status === 'open' && w.completion_status === 'in_progress'"
                              @submit.prevent="submitStage(s)" class="mt-2">
                            <textarea class="form-control mb-2" rows="3" v-model="s.form.text" required maxlength="10000"
                                      :placeholder="s.stage_no === 4 ? 'Describe your solution or paste a link to your work' : (s.stage_no === 5 ? 'Your theory test answers' : 'Your work / solution')"></textarea>
                            <input type="file" class="form-control mb-2" accept=".pdf,.zip,.doc,.docx,.txt"
                                   @change="s.form.file = $event.target.files[0]">
                            <div class="text-muted mb-2" style="font-size:11px;">Optional file: pdf, zip, doc, docx, txt (max 5 MB)</div>
                            <button class="btn btn-sm btn-primary" :disabled="wfBusy">Submit for review</button>
                        </form>

                        <div v-if="s.status === 'submitted'" class="text-warning" style="font-size:13px;">
                            Submitted<span v-if="s.has_file"> (file attached)</span>. Waiting for review by the AQDesk team.
                        </div>
                        <div v-if="s.status === 'passed' || s.status === 'failed'" style="font-size:13px;">
                            Score: {{ s.score !== null ? s.score : '—' }}<span v-if="s.feedback"> · {{ s.feedback }}</span>
                        </div>
                    </div>

                    <div v-if="w.completion_status === 'unsuccessful'" class="alert alert-warning mt-2 mb-0">
                        Your internship was not completed successfully, so a certificate cannot be issued at this time.
                    </div>
                    <div v-if="w.completion_status === 'completed' && !w.certificate" class="alert alert-info mt-2 mb-0">
                        You completed all stages! The AQDesk team will issue your certificate soon.
                    </div>
                    <div v-if="w.certificate" class="alert alert-success mt-2 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>🎓 Your certificate is ready: <strong>{{ w.certificate.certificate_no }}</strong></span>
                        <a class="btn btn-sm btn-warning" :href="'/certificate/' + w.certificate.certificate_no" target="_blank">View / Download</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. MY APPLICATIONS -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>📋 My Applications</h5>
            <div v-if="!appRows.length" class="text-muted">You have not applied to any internship yet. <a href="/internships">Browse internships</a>.</div>
            <div class="table-responsive" v-else>
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr><th>Internship</th><th>Company</th><th>Status</th><th>Applied</th><th>Progress</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="a in appRows" :key="a.id">
                            <td>{{ a.title }}</td>
                            <td>{{ a.company }}</td>
                            <td><span class="badge" :class="statusClass(a.status)">{{ a.status }}</span></td>
                            <td>{{ a.applied || '—' }}</td>
                            <td>
                                <span v-if="a.certificate">🎓 Certificate</span>
                                <span v-else-if="a.hasStages">{{ a.passed }} of 5 stages</span>
                                <span v-else class="text-muted">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. MY CERTIFICATES (always visible) -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>🎓 My Certificates</h5>
            <div v-if="!certificates.length" class="text-muted">
                Your certificate appears here after you successfully complete all 5 stages of an internship.
            </div>
            <div v-for="c in certificates" :key="c.certificate_id" class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>{{ c.internship_title }}</strong> — {{ c.company_name }}
                    <div class="text-muted" style="font-size:12px;">No. {{ c.certificate_no }} · Completed {{ dateOnly(c.completion_date) }}</div>
                </div>
                <div>
                    <a class="btn btn-sm btn-warning me-1" :href="'/certificate/' + c.certificate_no" target="_blank">View / Print</a>
                    <a class="btn btn-sm btn-outline-secondary" :href="'/verify-certificate/' + c.certificate_no" target="_blank">Verify</a>
                </div>
            </div>
        </div>

        <!-- 6. UPCOMING -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>📅 Upcoming</h5>
            <div v-if="!upcoming.length" class="text-muted">Nothing due soon. Task deadlines, interviews and weekend tests will appear here.</div>
            <div v-for="(u, idx) in upcoming" :key="idx" class="up-row">
                <div class="up-date">{{ u.date }}</div>
                <div><strong>{{ u.label }}</strong><div class="text-muted" style="font-size:12px;">{{ u.sub }}</div></div>
            </div>
        </div>

        <!-- 7. YOUR RANKING -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>🏆 Your Ranking</h5>
            <p class="text-muted" style="font-size:13px;">
                Meet every requirement below to enter the <strong>Top Interns</strong> list and be eligible for company recommendations.
            </p>
            <div v-for="(r, idx) in rankChecks" :key="idx" class="rank-row">
                <div class="rank-ico" :class="r.ok ? 'rank-ok' : 'rank-no'">{{ r.ok ? '✔' : '✘' }}</div>
                <div>{{ r.label }}<div class="text-muted" style="font-size:12px;">{{ r.detail }}</div></div>
            </div>
            <div class="mt-2" style="font-size:13px;">
                <strong>{{ rankMet }} of {{ rankChecks.length }}</strong> requirements met
                <span v-if="rankMet === rankChecks.length" class="text-success"> — 🎉 you are eligible for the ranking!</span>
            </div>
        </div>

        <!-- Skill performance -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>My Skill Performance</h5>
            <p class="text-muted" style="font-size:13px;">Updated from your weekly test results.</p>
            <div v-if="mySkills.length === 0" class="text-muted">No tests conducted yet. Take a skill test from an internship below to build your profile.</div>
            <div v-for="s in mySkills" :key="s.user_skill_id" style="margin-bottom:10px;">
                <div class="d-flex justify-content-between">
                    <strong>{{ s.skill ? s.skill.skill_name : 'Skill' }}</strong>
                    <span>{{ s.percentage }}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" :style="{ width: s.percentage + '%' }"></div>
                </div>
            </div>
        </div>

        <!-- Weekend tests -->
        <div class="bg-white rounded shadow-sm p-3 mb-4">
            <h5>🔔 Weekend Tests</h5>
            <div v-if="!schedules.length" class="text-muted">No upcoming tests right now.</div>
            <div v-for="s in schedules" :key="s.schedule_id" class="border rounded p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ s.title }}</strong>
                        <span class="text-muted" style="font-size:12px;">({{ s.skill ? s.skill.skill_name : '' }}) · {{ dateOnly(s.test_date) }}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" @click="startScheduleTest(s)">Take Test</button>
                </div>
                <div v-if="activeScheduleId === s.schedule_id" class="mt-3 p-3" style="background:#f7f7f7; border-radius:6px;">
                    <div v-for="(q, idx) in demoQuestions" :key="idx" class="mb-2">
                        <p class="mb-1" style="font-size:14px;">{{ q.question }}</p>
                        <div v-for="(opt, oidx) in q.options" :key="oidx" class="form-check">
                            <input class="form-check-input" type="radio" :name="'s' + s.schedule_id + 'q' + idx" @change="testAnswers[idx] = oidx">
                            <label class="form-check-label" style="font-size:13px;">{{ opt }}</label>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary" @click="submitScheduleTest(s)">Submit Test</button>
                </div>
            </div>
        </div>

        <!-- Talent Program -->
        <div class="bg-white rounded shadow-sm p-3 mb-4" v-if="talent">
            <h5>🌟 AQDesk Talent Program</h5>
            <p class="text-muted" style="font-size:13px;">
                Average at least {{ talent.min_average }}% across {{ talent.min_tests }}+ tests and AQDesk can recommend your profile to partner companies.
            </p>
            <div class="d-flex justify-content-between"><span>Average skill score</span><strong>{{ talent.average }}% / {{ talent.min_average }}%</strong></div>
            <div class="progress-bar-bg"><div class="progress-bar-fill" :style="{ width: Math.min(100, talent.average / talent.min_average * 100) + '%' }"></div></div>
            <div class="d-flex justify-content-between mt-2"><span>Tests taken</span><strong>{{ talent.attempts }} / {{ talent.min_tests }}</strong></div>
            <p v-if="talent.qualifies" class="text-success mt-2 mb-1">🎉 You meet the criteria!</p>
            <p v-else class="text-muted mt-2 mb-1" style="font-size:13px;">Keep taking the weekend tests to qualify.</p>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="consent" :checked="talent.consent" @change="toggleConsent($event.target.checked)">
                <label class="form-check-label" for="consent" style="font-size:13px;">I agree that AQDesk may share my name, email, bio and skill scores with partner companies.</label>
            </div>
            <div v-if="talent.referrals.length" class="mt-3">
                <strong style="font-size:13px;">Your referrals</strong>
                <div v-for="r in talent.referrals" :key="r.referral_id" style="font-size:13px;">
                    {{ r.company ? r.company.name : 'Company' }} — {{ r.status }}
                </div>
            </div>
        </div>

        <!-- Available internships -->
        <h5 class="mb-3">Available Internships</h5>
        <div v-if="!internships.length" class="text-muted mb-3">No internships are open right now. Please check back soon.</div>
        <div v-for="i in internships" :key="i.internship_id" class="internship-card" @click="toggleInternship(i.internship_id)">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                <h6 class="mb-1">{{ i.title }}</h6>
                <div>
                    <span class="badge" :class="i.is_paid ? 'bg-success' : 'bg-secondary'">{{ i.is_paid ? ('Paid' + (i.stipend ? ' · ' + i.stipend : '')) : 'Unpaid' }}</span>
                    <span class="badge bg-light text-dark border" v-if="i.category">{{ i.category }}</span>
                </div>
            </div>
            <div class="mb-1" style="font-size:13px;" v-if="i.company_name"><strong>{{ i.company_name }}</strong></div>
            <p class="mb-1" style="font-size:14px;">{{ i.description }}</p>
            <div class="text-muted" style="font-size:12px;">
                <span v-if="i.duration_weeks">⏱ {{ i.duration_weeks }} weeks &nbsp;|&nbsp; </span>
                <span v-if="i.work_mode">📍 {{ i.work_mode }}<span v-if="i.location"> · {{ i.location }}</span> &nbsp;|&nbsp; </span>
                <span v-if="i.application_deadline">⏳ Last date: <strong>{{ dateOnly(i.application_deadline) }}</strong> &nbsp;|&nbsp; </span>
                <span v-if="i.hosting_type !== 'company'">Requires <strong>{{ i.required_skill ? i.required_skill.skill_name : '' }}</strong> at {{ i.required_percentage }}% &nbsp;|&nbsp; </span>
                Status: <strong>{{ applicationStatus(i.internship_id) }}</strong>
            </div>

            <div v-if="expandedInternshipId === i.internship_id" class="internship-actions" @click.stop>

                <!-- company-hosted: details + apply -->
                <template v-if="i.hosting_type === 'company'">
                    <div class="spec-text mb-2" v-if="i.specifications"><strong>Specifications</strong><br>{{ i.specifications }}</div>
                    <div class="text-muted mb-2" style="font-size:12px;" v-if="i.start_date || i.end_date">
                        Period: {{ i.start_date || '?' }} to {{ i.end_date || '?' }}
                    </div>
                    <a v-if="i.apply_link" :href="i.apply_link" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary me-2">Company page</a>
                    <button class="btn btn-sm btn-warning"
                            :disabled="applicationStatus(i.internship_id) !== 'not applied' || isExpired(i)"
                            @click="applyDirect(i)">
                        {{ applicationStatus(i.internship_id) !== 'not applied' ? 'Already applied' : (isExpired(i) ? 'Applications closed' : 'Apply Now') }}
                    </button>
                </template>

                <!-- platform internships: original skill test + interview -->
                <template v-else>
                    <button class="btn btn-sm btn-outline-primary me-2" @click="goConductTest(i)">Conduct Test</button>
                    <button class="btn btn-sm btn-success" @click="goAppearInterview(i)">Appear in Interview</button>

                    <div v-if="activeTestInternshipId === i.internship_id" class="mt-3 p-3" style="background:#f7f7f7; border-radius:6px;">
                        <h6>{{ i.required_skill ? i.required_skill.skill_name : '' }} Skill Test</h6>
                        <div v-for="(q, idx) in demoQuestions" :key="idx" class="mb-2">
                            <p class="mb-1" style="font-size:14px;">{{ q.question }}</p>
                            <div v-for="(opt, oidx) in q.options" :key="oidx" class="form-check">
                                <input class="form-check-input" type="radio" :name="'q'+idx" @change="testAnswers[idx] = oidx">
                                <label class="form-check-label" style="font-size:13px;">{{ opt }}</label>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary" @click="submitSkillTest(i)">Submit Test</button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div v-else class="text-center mt-5">Loading...</div>
</div>
@endverbatim

<script>
const { createApp } = Vue;

// Ranking requirements (change these numbers to change the rule)
const RANK = { minStageAvg: 80, minTests: 3, minSkillAvg: 75 };

createApp({
    data() {
        return {
            loading: true,
            user: null,
            roleName: '',
            mySkills: [],
            internships: [],
            myApplications: [],
            expandedInternshipId: null,
            activeTestInternshipId: null,
            testAnswers: {},
            successMessage: '',
            errorMessage: '',
            demoQuestions: [
                { question: 'Sample question 1 for this skill', options: ['Option A', 'Option B', 'Option C'], correct: 1 },
                { question: 'Sample question 2 for this skill', options: ['Option A', 'Option B', 'Option C'], correct: 0 },
                { question: 'Sample question 3 for this skill', options: ['Option A', 'Option B', 'Option C'], correct: 2 },
            ],
            schedules: [],
            activeScheduleId: null,
            talent: null,
            workflows: [],
            certificates: [],
            wfBusy: false,
            wfMsg: '',
            stageNames: ['Task 1', 'Task 2', 'Task 3', 'Performance Test', 'Theory Test + Interview'],
        };
    },

    computed: {
        // Workflows the intern should see in the journey card
        activeWorkflows() {
            return this.workflows.filter(w => (w.stages && w.stages.length) || w.status === 'accepted');
        },

        // One simple list used by the stats and the applications table
        appRows() {
            if (this.workflows.length) {
                return this.workflows.map(a => ({
                    id: a.id,
                    status: a.status,
                    completion_status: a.completion_status,
                    title: a.internship ? a.internship.title : 'Internship #' + a.internship_id,
                    company: a.internship && a.internship.company_name ? a.internship.company_name : 'AQDesk',
                    applied: a.applied_at ? String(a.applied_at).substring(0, 10) : '',
                    hasStages: (a.stages || []).length > 0,
                    passed: (a.stages || []).filter(s => s.status === 'passed').length,
                    certificate: a.certificate ? a.certificate.certificate_no : null,
                }));
            }
            // fallback if /api/my-workflows is unavailable
            return this.myApplications.map(a => {
                const i = this.internships.find(x => x.internship_id === a.internship_id);
                return {
                    id: a.application_id || a.id || a.internship_id,
                    status: a.status,
                    completion_status: a.completion_status,
                    title: i ? i.title : 'Internship #' + a.internship_id,
                    company: i && i.company_name ? i.company_name : 'AQDesk',
                    applied: a.applied_at ? String(a.applied_at).substring(0, 10) : '',
                    hasStages: false, passed: 0, certificate: null,
                };
            });
        },

        completedCount() {
            return this.appRows.filter(a => a.completion_status === 'completed').length;
        },

        // Next 6 deadlines, interviews and tests
        upcoming() {
            const today = new Date().toISOString().slice(0, 10);
            const items = [];
            this.workflows.forEach(w => (w.stages || []).forEach(s => {
                const t = w.internship ? w.internship.title : 'Internship';
                if (s.status === 'open' && s.due_date)
                    items.push({ date: this.dateOnly(s.due_date), label: s.title + ' due', sub: t });
                if (s.stage_no === 5 && s.interview_at && s.status !== 'passed' && s.status !== 'failed')
                    items.push({ date: this.dateOnly(s.interview_at), label: 'Interview', sub: t + ' · ' + String(s.interview_at).replace('T', ' ').substring(11, 16) });
            }));
            this.schedules.forEach(s => items.push({
                date: this.dateOnly(s.test_date),
                label: 'Weekend test: ' + s.title,
                sub: s.skill ? s.skill.skill_name : '',
            }));
            return items.filter(x => x.date >= today).sort((a, b) => (a.date < b.date ? -1 : 1)).slice(0, 6);
        },

        // Checklist for the Top Interns list
        rankChecks() {
            const stages = [];
            this.workflows.forEach(w => (w.stages || []).forEach(s => stages.push(s)));
            const scored = stages.filter(s => s.score !== null && s.score !== undefined);
            const avg = scored.length ? Math.round(scored.reduce((t, s) => t + Number(s.score), 0) / scored.length) : null;
            const anyFailed = stages.some(s => s.status === 'failed');
            const t = this.talent;
            return [
                { label: 'Complete an internship and earn a certificate',
                  ok: this.certificates.length > 0,
                  detail: this.certificates.length + ' certificate(s) so far' },
                { label: 'No failed stage',
                  ok: stages.length > 0 && !anyFailed,
                  detail: !stages.length ? 'No stages started yet' : (anyFailed ? 'At least one stage was failed' : 'No failed stages') },
                { label: 'Internship stage average ' + RANK.minStageAvg + '% or higher',
                  ok: avg !== null && avg >= RANK.minStageAvg,
                  detail: avg !== null ? 'Your average: ' + avg + '%' : 'No scores yet' },
                { label: RANK.minTests + '+ skill tests with an average of ' + RANK.minSkillAvg + '% or higher',
                  ok: !!t && t.attempts >= RANK.minTests && t.average >= RANK.minSkillAvg,
                  detail: t ? t.attempts + ' tests, average ' + t.average + '%' : 'No test data yet' },
                { label: 'Allow AQDesk to share my profile (consent)',
                  ok: !!t && !!t.consent,
                  detail: t && t.consent ? 'Consent is on' : 'Turn it on in the Talent Program card below' },
            ];
        },
        rankMet() { return this.rankChecks.filter(r => r.ok).length; },
    },

    methods: {
        token() { return localStorage.getItem('aqdesk_token'); },

        // One helper for every call to the API (adds the login token)
        api(path, opts = {}) {
            opts.headers = Object.assign(
                { 'Accept': 'application/json', 'Authorization': 'Bearer ' + this.token() },
                opts.headers || {}
            );
            return fetch('/api' + path, opts);
        },

        // Load a list from the API into this[key]; never crashes the page
        async loadList(path, key, auth = true) {
            try {
                const r = auth ? await this.api(path) : await fetch('/api' + path, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const d = await r.json();
                if (Array.isArray(d)) this[key] = d;
            } catch (e) {}
        },

        dateOnly(v) { return v ? String(v).substring(0, 10) : ''; },
        isExpired(i) { return !!i.application_deadline && this.dateOnly(i.application_deadline) < new Date().toISOString().slice(0, 10); },

        statusClass(s) {
            return { applied: 'bg-primary', pending: 'bg-warning text-dark', shortlisted: 'bg-info text-dark',
                     accepted: 'bg-success', rejected: 'bg-danger' }[s] || 'bg-secondary';
        },
        completionLabel(w) {
            return { completed: 'Completed', unsuccessful: 'Not successful', in_progress: 'In progress' }[w.completion_status] || 'Waiting to start';
        },
        completionClass(w) {
            return { completed: 'bg-success', unsuccessful: 'bg-danger', in_progress: 'bg-warning text-dark' }[w.completion_status] || 'bg-secondary';
        },
        sortedStages(w) { return (w.stages || []).slice().sort((a, b) => a.stage_no - b.stage_no); },
        passedCount(w) { return (w.stages || []).filter(s => s.status === 'passed').length; },

        async loadProfile() {
            if (!this.token()) { window.location.href = '/login'; return; }
            const response = await this.api('/profile');
            if (!response.ok) { window.location.href = '/login'; return; }
            const data = await response.json();
            this.user = data;
            this.roleName = data.role ? data.role.role_name : '';
        },

        loadMySkills()       { return this.loadList('/my-skills', 'mySkills'); },
        loadInternships()    { return this.loadList('/internships', 'internships', false); },
        loadMyApplications() { return this.loadList('/my-applications', 'myApplications'); },
        loadSchedules()      { return this.loadList('/test-schedules', 'schedules', false); },
        loadCertificates()   { return this.loadList('/my-certificates', 'certificates'); },

        async loadWorkflows() {
            try {
                const response = await this.api('/my-workflows');
                if (!response.ok) return;
                const list = await response.json();
                if (!Array.isArray(list)) return;
                list.forEach(a => { a.stages = a.stages || []; a.stages.forEach(s => { s.form = { text: '', file: null }; }); });
                this.workflows = list;
            } catch (e) {}
        },

        async loadTalent() {
            try {
                const response = await this.api('/talent/status');
                if (response.ok) this.talent = await response.json();
            } catch (e) {}
        },

        countStatus(status) { return this.appRows.filter(a => a.status === status).length; },

        applicationStatus(internshipId) {
            const app = this.myApplications.find(a => a.internship_id === internshipId)
                     || this.workflows.find(a => a.internship_id === internshipId);
            return app ? app.status : 'not applied';
        },

        toggleInternship(id) {
            this.expandedInternshipId = this.expandedInternshipId === id ? null : id;
            this.activeTestInternshipId = null;
        },

        // company-hosted internship: apply directly
        async applyDirect(internship) {
            this.successMessage = '';
            this.errorMessage = '';
            const response = await this.api('/internships/' + internship.internship_id + '/apply-direct', { method: 'POST' });
            const data = await response.json().catch(() => ({}));
            if (response.ok) this.successMessage = data.message || 'Application submitted';
            else this.errorMessage = data.message || 'Could not apply';
            this.loadMyApplications();
            this.loadWorkflows();
        },

        // submit work for a stage
        async submitStage(s) {
            this.wfBusy = true;
            this.wfMsg = '';
            try {
                const fd = new FormData();
                fd.append('submission_text', s.form.text);
                if (s.form.file) fd.append('file', s.form.file);
                const response = await this.api('/stages/' + s.stage_id + '/submit', { method: 'POST', body: fd });
                const data = await response.json().catch(() => ({}));
                if (response.ok) {
                    this.wfMsg = 'Submitted for review';
                    await this.loadWorkflows();
                } else {
                    let m = data.message || 'Could not submit';
                    if (data.errors) m = Object.values(data.errors)[0][0];
                    this.wfMsg = m;
                }
            } catch (e) {
                this.wfMsg = 'Network error. Please try again.';
            }
            this.wfBusy = false;
        },

        // ---- original platform-internship tests (unchanged logic) ----
        async goConductTest(internship) {
            const response = await this.api('/internships/' + internship.internship_id + '/apply-test', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            });
            const data = await response.json();
            this.successMessage = data.message;
            this.activeTestInternshipId = internship.internship_id;
            this.testAnswers = {};
            this.loadMyApplications();
            this.loadWorkflows();
        },

        async submitSkillTest(internship) {
            let correct = 0;
            this.demoQuestions.forEach((q, idx) => { if (this.testAnswers[idx] === q.correct) correct++; });
            const score = Math.round((correct / this.demoQuestions.length) * 100);

            const response = await this.api('/skills/' + internship.required_skill_id + '/test', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ score })
            });
            const data = await response.json();
            this.successMessage = data.message;
            this.activeTestInternshipId = null;
            this.loadMySkills();
            this.loadTalent();
        },

        async goAppearInterview(internship) {
            const response = await this.api('/internships/' + internship.internship_id + '/interview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            });
            const data = await response.json();
            if (data.eligible) this.successMessage = data.message;
            else this.errorMessage = data.message;
            this.loadMyApplications();
            this.loadWorkflows();
        },

        startScheduleTest(s) {
            this.activeScheduleId = this.activeScheduleId === s.schedule_id ? null : s.schedule_id;
            this.testAnswers = {};
        },

        async submitScheduleTest(s) {
            let correct = 0;
            this.demoQuestions.forEach((q, idx) => { if (this.testAnswers[idx] === q.correct) correct++; });
            const score = Math.round((correct / this.demoQuestions.length) * 100);

            const response = await this.api('/skills/' + s.skill_id + '/test', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ score })
            });
            const data = await response.json();
            this.successMessage = data.message;
            this.activeScheduleId = null;
            this.loadMySkills();
            this.loadTalent();
        },

        async toggleConsent(checked) {
            const response = await this.api('/talent/consent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ consent: checked })
            });
            if (response.ok) {
                this.talent.consent = checked;
                this.successMessage = checked ? 'Thank you. Your profile can now be recommended to companies.' : 'Consent withdrawn.';
            }
        },

        logout() {
            localStorage.removeItem('aqdesk_token');
            localStorage.removeItem('aqdesk_user');
            window.location.href = '/login';
        }
    },

    async mounted() {
        await this.loadProfile();
        if (!this.user) return;

        // Employers and admins have their own pages
        const HOME = { admin: '/admin', employer: '/employer' };
        if (HOME[this.roleName]) { window.location.href = HOME[this.roleName]; return; }

        await Promise.all([
            this.loadMySkills(),
            this.loadInternships(),
            this.loadMyApplications(),
            this.loadSchedules(),
            this.loadTalent(),
            this.loadWorkflows(),
            this.loadCertificates(),
        ]);
        this.loading = false;
    }
}).mount('#app');
</script>
</body>
</html>