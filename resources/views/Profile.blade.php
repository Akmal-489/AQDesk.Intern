<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AQDesk Profile Update Page</title>

        <script src="js/vue.js"></script>
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

            .tagline {
                color: #dce9f7;
                font-size: 14px;
            }

            .profile-wrapper {
                max-width: 850px;
                margin: 45px auto;
                padding: 0 15px;
            }

            .profile-card {
                background: white;
                border-radius: 16px;
                padding: 35px;
                box-shadow:
                    0 5px 22px
                    rgba(0, 0, 0, 0.07);
            }

            .profile-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .profile-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto 15px;
                border-radius: 50%;
                background: #e8f1ff;
                display: flex;
                justify-content: center;
                align-items: center;
                color: #0d6efd;
                font-size: 38px;
            }

            .profile-title {
                color: #102a43;
                font-size: 27px;
                font-weight: 700;
                margin-bottom: 5px;
            }

            .profile-subtitle {
                color: #6c757d;
                font-size: 14px;
            }

            .info-box {
                background: #f8fafc;
                border: 1px solid #e5eaf0;
                border-radius: 10px;
                padding: 17px;
                margin-bottom: 25px;
            }

            .info-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 9px 0;
            }

            .info-item:not(:last-child) {
                border-bottom:
                    1px solid #e3e8ee;
            }

            .info-icon {
                width: 35px;
                height: 35px;
                border-radius: 8px;
                background: #e8f1ff;
                color: #0d6efd;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .info-label {
                font-size: 12px;
                color: #6c757d;
                margin-bottom: 2px;
            }

            .info-value {
                font-size: 15px;
                font-weight: 600;
                color: #34495e;
            }

            .form-label {
                font-weight: 600;
                color: #34495e;
                margin-bottom: 7px;
            }

            .form-control {
                border-color: #d5dce5;
                border-radius: 8px;
                padding: 11px 13px;
            }

            .form-control:focus {
                border-color: #0d6efd;
                box-shadow:
                    0 0 0 0.2rem
                    rgba(13, 110, 253, 0.12);
            }

            textarea {
                resize: vertical;
            }


            .save-btn {
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

            .save-btn:hover {
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

            .loading-box {
                min-height: 350px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: #6c757d;
            }

            .loading-icon {
                font-size: 35px;
                color: #0d6efd;
                margin-bottom: 12px;
            }


            .footer {
                text-align: center;
                color: #6c757d;
                font-size: 13px;
                padding: 20px;
            }

            @media (max-width: 576px) {
                .profile-wrapper {
                    margin: 25px auto;
                }

                .profile-card {
                    padding: 25px 20px;
                }

                .brand {
                    font-size: 23px;
                }

                .tagline {
                    display: none;
                }

                .profile-title {
                    font-size: 23px;
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

        <a href="/Dashboard" class="scroll-fab"
            title="Jump to Home Tab">
            <i class="fa-solid fa-house"></i>
        </a>

        <div id="app" class="profile-wrapper">

            <div class="profile-card">

                <div v-if="loading"
                    class="loading-box">
                    <div class="loading-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>

                    <h5>Loading Profile...</h5>
                    
                    <p>Please wait.</p>
                </div>>

                <div v-else>
                    <div class="profile-header">
                        <div class="profile-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>

                        <h2 class="profile-title">My Profile</h2>

                        <p class="profile-subtitle">
                            View and update your AQDesk profile
                        </p>
                    </div>

                    <div class="info-box">

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>

                            <div>
                                <div class="info-label">Email</div>

                                <div class="info-value">
                                    @{{ email }}
                                    <span
                                        class="badge text-bg-secondary ms-2"
                                        style="font-size: 10px;">
                                        Cannot be changed
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>

                            <div>
                                <div class="info-label">Role</div>
                                <div class="info-value">
                                    @{{ roleName }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>

                            <input v-model="name"
                                type="text"
                                class="form-control"
                                placeholder="Enter your name">
                        </div>
                    </div>

                    <div class="mb-4">

                        <label class="form-label">

                            Bio

                        </label>


                        <textarea
                            v-model="bio"
                            rows="5"
                            class="form-control"
                            placeholder="Tell the AQDesk community a little about yourself..."
                        ></textarea>

                    </div>



                    <!-- SAVE -->

                    <button
                        @click="saveProfile"
                        class="save-btn"
                    >

                        <i class="bi bi-check2-circle me-2"></i>

                        Save Changes

                    </button>



                    <!-- SUCCESS -->

                    <div
                        v-if="SuccessMessage"
                        class="alert alert-success custom-alert"
                    >

                        <i class="bi bi-check-circle me-2"></i>

                        @{{ SuccessMessage }}

                    </div>



                    <!-- ERROR -->

                    <div
                        v-if="ErrorMessage"
                        class="alert alert-danger custom-alert"
                    >

                        <i class="bi bi-exclamation-circle me-2"></i>

                        @{{ ErrorMessage }}

                    </div>


                </div>

            </div>

        </div>



        <!-- ================= FOOTER ================= -->

        <div class="footer">

            AQDesk &copy; 2026 — Ask. Share. Learn.

        </div>



        <!-- ================= VUE ================= -->

        <script>

            const { createApp } = Vue;

            createApp({

                data() {

                    return {

                        loading: true,

                        name: '',

                        email: '',

                        bio: '',

                        roleName: '',

                        SuccessMessage: '',

                        ErrorMessage: ''

                    };

                },


                methods: {


                    async loadProfile() {

                        const token =
                            localStorage.getItem(
                                'aqdesk_token'
                            );


                        if(!token) {

                            this.ErrorMessage =
                                'Please LogIn first.';

                            this.loading = false;

                            return;

                        }


                        try {

                            const response =
                                await fetch(
                                    'http://127.0.0.1:8000/api/profile',
                                    {

                                        headers: {

                                            'Authorization':
                                                'Bearer ' + token

                                        }

                                    }
                                );


                            const data =
                                await response.json();


                            this.name =
                                data.name;

                            this.email =
                                data.email;

                            this.bio =
                                data.bio;

                            this.roleName =
                                data.role
                                    ? data.role.role_name
                                    : '';


                            this.loading = false;

                        }

                        catch (err) {

                            this.ErrorMessage =
                                'Could not load profile.';

                            this.loading = false;

                        }

                    },


                    async saveProfile() {

                        this.SuccessMessage = '';

                        this.ErrorMessage = '';


                        const token =
                            localStorage.getItem(
                                'aqdesk_token'
                            );


                        try {

                            const response =
                                await fetch(
                                    'http://127.0.0.1:8000/api/profile',
                                    {

                                        method: 'PUT',

                                        headers: {

                                            'Content-Type':
                                                'application/json',

                                            'Authorization':
                                                'Bearer ' + token

                                        },


                                        body: JSON.stringify({

                                            name:
                                                this.name,

                                            bio:
                                                this.bio

                                        })

                                    }
                                );


                            const data =
                                await response.json();


                            if(response.ok) {

                                this.SuccessMessage =
                                    'Profile updated successfully!';

                            }

                            else {

                                this.ErrorMessage =
                                    'Failed to update profile.';

                            }
                        } catch (err) {
                            this.ErrorMessage =
                                'Coutld not connect to server.';
                        }
                    }
                },
                mounted() {
                    this.loadProfile();
                }
            }).mount('#app');
        </script>
    </body>
</html>