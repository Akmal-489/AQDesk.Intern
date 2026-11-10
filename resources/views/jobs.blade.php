<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AQDesk Job Posting Page</title>

    <script src="/js/vue.js"></script>
    <script src="/js/session-guard.js"></script>

    <link rel="stylesheet" href="/css/mobile.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #f4f7fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #212529;
        }

        .navbar-custom {
            background:
                linear-gradient(
                    135deg,
                    #071a33,
                    #0d3b66
                );

            padding: 15px 0;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.15);
        }

        .brand {
            font-size: 27px;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }


        .brand span {
            color: #0d6efd;
        }


        .tagline {
            color: #dce9f7;
            font-size: 14px;
        }

        .jobs-wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .main-card {
            background: white;
            border-radius: 16px;
            padding: 35px;
            box-shadow:
                0 5px 22px
                rgba(0, 0, 0, 0.07);
            margin-bottom: 35px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #e8f1ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            font-size: 34px;
        }

        .page-title {
            color: #102a43;
            font-size: 27px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 14px;
        }

        .form-label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 7px;
        }

        .form-control,
        .form-select {
            padding: 11px 13px;
            border-color: #d5dce5;
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow:
                0 0 0 0.2rem
                rgba(13, 110, 253, 0.12);
        }

        textarea {
            resize: vertical;
        }

        .file-box {
            border: 1px dashed #b9c6d5;
            background: #f8fafc;
            border-radius: 9px;
            padding: 15px;
        }

        .file-help {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .post-btn {
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 12px;
            background:
                linear-gradient(
                    135deg,
                    #0d6efd,
                    #0056d6
                );
            color: white;
            font-size: 16px;
            font-weight: 600;
            transition: 0.2s;
        }

        .post-btn:hover {
            transform: translateY(-1px);
            box-shadow:
                0 6px 15px
                rgba(13, 110, 253, 0.3);
        }

        .custom-alert {
            border-radius: 8px;
            font-size: 14px;
            margin-top: 18px;
        }

        .section-title {
            color: #102a43;
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .section-title i {
            color: #0d6efd;
            margin-right: 8px;
        }

        .job-card {
            background: white;
            border: 1px solid #e2e7ed;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 18px;
            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.04);
            transition: 0.2s;
        }

        .job-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 7px 20px
                rgba(0, 0, 0, 0.08);
        }

        .job-title {
            color: #102a43;
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .job-type {
            background: #e8f1ff;
            color: #0d6efd;
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 5px;
        }

        .job-meta {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .job-meta i {
            color: #0d6efd;
            margin-right: 4px;
        }

        .job-description {
            color: #495057;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .registration-btn {
            display: inline-block;
            background: #2e7d4f;
            color: white;
            text-decoration: none;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-right: 7px;
            transition: 0.2s;
        }

        .registration-btn:hover {
            background: #25663f;
            color: white;
        }

        .pdf-btn {
            display: inline-block;
            background: #b0332f;
            color: white;
            text-decoration: none;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 13px;
            transition: 0.2s;
        }

        .pdf-btn:hover {
            background: #8f2825;
            color: white;
        }

        .query-area {
            display: flex;
            gap: 8px;
            margin-top: 18px;
        }

        .query-input {
            flex: 1;
            padding: 9px 12px;
            border: 1px solid #d5dce5;
            border-radius: 7px;
            outline: none;
        }

        .query-input:focus {
            border-color: #0d6efd;
            box-shadow:
                0 0 0 0.15rem
                rgba(13, 110, 253, 0.12);
        }

        .query-btn {
            border: none;
            background: #0d6efd;
            color: white;
            padding: 9px 16px;
            border-radius: 7px;
            font-weight: 600;
            white-space: nowrap;
        }

        .query-btn:hover {
            background: #0056d6;
        }

        .scroll-fab {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            text-decoration: none;
            box-shadow:
                0 5px 15px
                rgba(13, 110, 253, 0.35);
            z-index: 100;
            transition: 0.2s;
        }

        .scroll-fab:hover {
            background: #0056d6;
            color: white;
            transform: translateY(-2px);
        }

        .scroll-fab2 {
                position: fixed;
                bottom: 25px;
                left: 44%;
                width: 152px;
                height: 25px;
                border-radius: 50%;
                background: #0d6efd;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                text-decoration: none;
                box-shadow:
                    0 5px 15px
                    rgba(13, 110, 253, 0.35);
                z-index: 100;
                transition: 0.2s;
            }

            .scroll-fab2:hover {
                background: #041e45;
                color: white;
                transform: translateY(-2px);
            }

        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            padding: 20px;
        }

        @media (max-width: 576px) {
            .jobs-wrapper {
                margin: 25px auto;
            }

            .main-card {
                padding: 25px 20px;
            }

            .brand {
                font-size: 23px;
            }

            .tagline {
                display: none;
            }

            .page-title {
                font-size: 23px;
            }

            .query-area {
                flex-direction: column;
            }

            .query-btn {
                width: 100%;
            }

            .scroll-fab {
                width: 46px;
                height: 46px;
                right: 18px;
                bottom: 18px;
            }

        }

    </style>

</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <a href="#" class="brand">
                <i class="bi bi-chat-dots"></i>
                AQ<span>Desk</span>
            </a>

            <div class="tagline">
                Ask. Share. Learn.
            </div>
        </div>
    </nav>

    <a href="#available-jobs" class="scroll-fab" title="Jump to Available Jobs">
        <i class="bi bi-briefcase"></i>
    </a>

    <a href="/home" class="scroll-fab2" title="Jump to Home Tab">
        <i class="fa-solid fa-house"></i>
    </a>

    <div id="app" class="jobs-wrapper">

        <div class="main-card">
            <div class="page-header">

                <div class="page-icon">
                    <i class="bi bi-briefcase-fill"></i>
                </div>

                <h2 class="page-title">
                    Post a Job / Internship
                </h2>

                <p class="page-subtitle">Share opportunities with the AQDesk community</p>
            </div>

            <div class="mb-3">
                <label class="form-label">Job / Internship Title</label>

                <input
                    v-model="title"
                    type="text"
                    class="form-control"
                    placeholder="Enter job or internship title">
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>

                <select
                    v-model="type"
                    class="form-select">
                    <option value="job">Job</option>
                    <option value="internship">Internship</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>

                <textarea
                    v-model="description"
                    rows="4"
                    class="form-control"
                    placeholder="Enter job or internship description"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>

                <input
                    v-model="deadline"
                    type="date"
                    class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Job Registration Link</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-link-45deg"></i>
                    </span>

                    <input
                        v-model="registrationLink"
                        type="url"
                        placeholder="https://..."
                        class="form-control">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Advertisement PDF
                    <span class="text-muted fw-normal">
                        (optional)
                    </span>
                </label>

                <div class="file-box">

                    <input
                        type="file"
                        accept="application/pdf"
                        @change="onFileSelected"
                        class="form-control">

                    <div class="file-help">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Upload the job advertisement in PDF format.
                    </div>
                </div>
            </div>

            <button
                @click="postJob"
                class="post-btn">
                <i class="bi bi-send me-2"></i>
                Post Job
            </button>

            <div
                v-if="ErrorMessage"
                class="alert alert-danger custom-alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                @{{ ErrorMessage }}
            </div>

            <div
                v-if="SuccessMessage"
                class="alert alert-success custom-alert">
                <i class="bi bi-check-circle me-2"></i>
                @{{ SuccessMessage }}
            </div>
        </div>

        <div id="available-jobs">
            <h2 class="section-title">
                <i class="bi bi-list-ul"></i>
                Available Job & Internship
            </h2>

            <div
                v-for="j in jobs"
                :key="j.job_id"
                class="job-card">

                <h3 class="job-title">
                    @{{ j.title }}
                    <span class="job-type">
                        @{{ j.type }}
                    </span>
                </h3>

                <p class="job-meta mb-3">
                    <span>
                        <i class="bi bi-person"></i>
                        Posted by:
                        @{{ j.poster ? j.poster.name : 'Unknown' }}
                    </span>
                    &nbsp; | &nbsp;
                    <span>
                        <i class="bi bi-calendar-event"></i>
                        Deadline:
                        @{{ j.deadline || 'Not specified' }}
                    </span>
                    &nbsp; | &nbsp;
                    <span>
                        <i class="bi bi-chat-dots"></i>
                        @{{ j.applications ? j.applications.length : 0 }}
                        queries
                    </span>
                </p>

                <p class="job-description">
                    @{{ j.description }}
                </p>

                <div
                    v-if="j.registration_link || j.advertisement_pdf"
                    class="mb-3">
                    <a
                        v-if="j.registration_link"
                        :href="j.registration_link"
                        target="_blank"
                        class="registration-btn">
                        <i class="bi bi-link-45deg me-1"></i>
                        Registration Link
                    </a>

                    <a
                        v-if="j.advertisement_pdf"
                        :href="'/storage/' + j.advertisement_pdf"
                        target="_blank"
                        class="pdf-btn">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        View Advertisement PDF
                    </a>
                </div>

                <div class="query-area"> 
                    <input
                        v-model="resumeNotes[j.job_id]"
                        type="text"
                        placeholder="Type your question..."
                        class="query-input">

                    <button @click="applyToJob(j.job_id)" class="query-btn"><i class="bi bi-send me-1"></i>Ask Query</button>
                </div>

                <div v-if="j.applications && j.applications.length" style="background:#f7f7f7; border:1px solid #ddd; border-radius:6px; padding:10px; margin:10px 0;">
                        <strong style="font-size:13px; color:#555;">Queries:</strong>
                    <div v-for="app in j.applications" :key="app.application_id" style="margin-top:6px; font-size:14px;">
                        <strong> @{{ app.applicant ? app.applicant.name : 'Unknown' }}:</strong> @{{ app.resume_note }}
                    </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="footer">
        AQDesk &copy; 2026 — Ask. Share. Learn.
    </div>

    <script>
        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    title: '',
                    type: 'job',
                    description: '',
                    deadline: '',
                    registrationLink: '',
                    selectedFile: null,
                    ErrorMessage: '',
                    SuccessMessage: '',
                    jobs: [],
                    resumeNotes: {}
                };
            },
            methods: {
                onFileSelected(event) {
                    this.selectedFile =
                        event.target.files[0] || null;
                },

                async loadJobs() {
                    try {
                        const response =
                            await fetch(
                                'http://127.0.0.1:8000/api/jobs'
                            );
                        this.jobs =
                            await response.json();
                    } catch (err) {
                        console.log(
                            'Could not load jobs.'
                        );
                    }
                },

                async postJob() {
                    this.ErrorMessage = '';
                    this.SuccessMessage = '';
                    const token =
                        localStorage.getItem(
                            'aqdesk_token'
                        );

                    if (!token) {
                        this.ErrorMessage =
                            'Please Log In first.';
                        return;
                    }

                    const formData =
                        new FormData();

                    formData.append(
                        'title',
                        this.title
                    );

                    formData.append(
                        'type',
                        this.type
                    );

                    formData.append(
                        'description',
                        this.description
                    );

                    formData.append(
                        'deadline',
                        this.deadline
                    );

                    formData.append(
                        'registration_link',
                        this.registrationLink
                    );

                    if (this.selectedFile) {

                        formData.append(
                            'advertisement_pdf',
                            this.selectedFile
                        );
                    }

                    try {
                        const response =
                            await fetch(
                                'http://127.0.0.1:8000/api/jobs',
                                {
                                    method: 'POST',
                                    headers: {
                                        'Authorization':
                                            'Bearer ' + token
                                    },
                                    body: formData
                                }
                            );

                        const data =
                            await response.json();

                        if (response.ok) {
                            this.SuccessMessage =
                                'Job posted successfully!';
                            this.title = '';
                            this.description = '';
                            this.deadline = '';
                            this.registrationLink = '';
                            this.selectedFile = null;
                            this.loadJobs();
                        }

                        else {
                            this.ErrorMessage =
                                data.message ||
                                'Failed to post job.';
                        }

                    } catch (err) {
                        this.ErrorMessage =
                            'Could not connect to server.';
                    }
                },

                async applyToJob(jobId) {
                    const token =
                        localStorage.getItem(
                            'aqdesk_token'
                        );

                    if (!token) {
                        alert(
                            'Please Log In first.'
                        );
                        return;
                    }

                    try {
                        const response =
                            await fetch(
                                'http://127.0.0.1:8000/api/jobs/'
                                + jobId
                                + '/apply',
                                {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type':
                                            'application/json',
                                        'Authorization':
                                            'Bearer ' + token

                                    },

                                    body: JSON.stringify({
                                        resume_note:
                                            this.resumeNotes[jobId]
                                            || ''
                                    })
                                }
                            );

                        if (response.ok) {
                            alert(
                                'Query submitted!'
                            );
                            this.resumeNotes[jobId] = '';
                            this.loadJobs();
                        } else {
                            alert(
                                'Failed to submit query.'
                            );
                        }
                    } catch (err) {
                        alert(
                            'Could not connect to server.'
                        );
                    }
                }
            },
            mounted() {
                this.loadJobs();
            }
        }).mount('#app');
    </script>
</body>
</html>