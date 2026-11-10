<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AQDesk Ask Question Page</title>

        <script src="js/vue.js"></script>
        <script src="/js/session-guard.js"></script>

        <link rel="stylesheet" href="/css/mobile.css">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <style>
            body {
                background: #f4f7fb;
                font-family: Arial, Helvetica, sans-serif;
                color: #212529;
            }

            .navbar-custom {
                background: linear-gradient(135deg, #071a33, #0d3b66);
                box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
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

            .main-container {
                max-width: 1150px;
                margin: 40px auto;
            }

            .main-card {
                background: white;
                border-radius: 15px;
                border: none;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
                padding: 30px;
                margin-bottom: 30px;
            }

            .section-title {
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 25px;
                color: #102a43;
            }

            .section-title i {
                color: #0d6efd;
                margin-right: 8px;
            }

            .form-label {
                font-weight: 600;
                color: #34495e;
            }

            .form-control,
            .form-select {
                border: 1px solid #d5dce5;
                border-radius: 8px;
                padding: 11px 13px;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
            }

            textarea {
                resize: vertical;
            }

            .post-btn {
                background: linear-gradient(135deg, #0d6efd, #0056d6);
                border: none;
                border-radius: 8px;
                padding: 11px 22px;
                font-weight: 600;
                color: white;
                transition: 0.2s;
            }

            .post-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 5px 12px rgba(13, 110, 253, 0.3);
            }

            .question-card {
                border: 1px solid #e1e7ef;
                border-radius: 12px;
                background: white;
                padding: 22px;
                margin-bottom: 18px;
                transition: 0.2s;
            }

            .question-card:hover {
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
                transform: translateY(-2px);
            }

            .question-title {
                color: #0d6efd;
                font-size: 20px;
                font-weight: 700;
                margin-bottom: 10px;
            }

            .question-body {
                color: #495057;
                line-height: 1.6;
                margin-top: 12px;
            }

            .question-meta {
                color: #6c757d;
                font-size: 13px;
            }

            .question-meta i {
                color: #0d6efd;
                margin-right: 4px;
            }

            .category-badge {
                background: #e8f1ff;
                color: #0d6efd;
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .answers-box {
                margin-top: 18px;
                padding: 15px;
                background: #f8fafc;
                border-left: 3px solid #0d6efd;
                border-radius: 6px;
            }

            .answer {
                padding: 8px 0;
                border-bottom: 1px solid #e3e8ee;
                color: #495057;
            }

            .answer:last-child {
                border-bottom: none;
            }

            .answer strong {
                color: #102a43;
            }

            .reply-input {
                border-radius: 7px 0 0 7px !important;
            }

            .reply-btn {
                background: #0d6efd;
                color: white;
                border: none;
                border-radius: 0 7px 7px 0;
                padding: 0 18px;
                font-weight: 600;
            }

            .reply-btn:hover {
                background: #0056d6;
            }

            .custom-alert {
                border-radius: 8px;
                font-size: 14px;
                margin-top: 20px;
            }

            .empty-box {
                text-align: center;
                padding: 45px 20px;
                color: #6c757d;
            }

            .empty-box i {
                font-size: 45px;
                color: #adb5bd;
                display: block;
                margin-bottom: 12px;
            }

            .footer {
                text-align: center;
                color: #6c757d;
                font-size: 13px;
                padding: 20px;
            }

            @media (max-width: 768px) {
                .main-container {
                    margin: 20px auto;
                    padding: 0 12px;
                }

                .main-card {
                    padding: 20px;
                }

                .question-card {
                    padding: 17px;
                }

                .brand {
                    font-size: 23px;
                }

                .tagline {
                    display: none;
                }

                .reply-input {
                    border-radius: 7px !important;
                    margin-bottom: 8px;
                }

                .reply-btn {
                    width: 100%;
                    border-radius: 7px;
                    padding: 9px;
                }
            }
        </style>
    </head>

    <body>
        <div id="app">
          <nav class="navbar navbar-custom">
                <div class="container">
                    <a href="#" class="brand">
                        <i class="bi bi-chat-dots"></i>
                        AQ<span>Desk</span>
                    </a>

                    <div class="tagline">Ask. Share. Learn.</div>
                </div>
            </nav>

            <a href="/home" class="scroll-fab" title="Jump to Home Tab">
                <i class="fa-solid fa-house"></i>
            </a>

            <div class="container main-container">

                <div class="main-card">

                    <h2 class="section-title">
                        <i class="bi bi-pencil-square"></i>
                        Ask a Question
                    </h2>

                    <div class="row g-3">

                        <!-- TITLE -->
                        <div class="col-md-8">

                            <label class="form-label">
                                Title
                            </label>

                            <input
                                v-model="title"
                                type="text"
                                class="form-control"
                                placeholder="Enter a short title for your question..."
                            >

                        </div>


                        <!-- CATEGORY -->
                        <div class="col-md-4">

                            <label class="form-label">
                                Category
                            </label>

                            <select
                                v-model="category"
                                class="form-select"
                            >

                                <option value="EdTech">EdTech</option>
                                <option value="AI">AI</option>
                                <option value="Web Dev">Web Dev</option>
                                <option value="Internet Governance">
                                    Internet Governance
                                </option>

                            </select>

                        </div>


                        <!-- DESCRIPTION -->
                        <div class="col-12">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                v-model="body"
                                rows="5"
                                class="form-control"
                                placeholder="Describe your question in detail..."
                            ></textarea>

                        </div>


                        <!-- POST BUTTON -->
                        <div class="col-12">

                            <button
                                @click="postQuestion"
                                class="post-btn"
                            >

                                <i class="bi bi-send me-2"></i>
                                Post Question

                            </button>

                        </div>

                    </div>


                    <!-- ERROR MESSAGE -->
                    <div
                        v-if="ErrorMessage"
                        class="alert alert-danger custom-alert"
                    >

                        <i class="bi bi-exclamation-circle me-2"></i>

                        @{{ErrorMessage}}

                    </div>


                    <!-- SUCCESS MESSAGE -->
                    <div
                        v-if="SuccessMessage"
                        class="alert alert-success custom-alert"
                    >

                        <i class="bi bi-check-circle me-2"></i>

                        @{{SuccessMessage}}

                    </div>

                </div>



                <!-- COMMUNITY QUESTIONS -->
                <div class="main-card">

                    <h2 class="section-title">

                        <i class="bi bi-people"></i>
                        Community Questions

                    </h2>


                    <!-- NO QUESTIONS -->
                    <div
                        v-if="questions.length === 0"
                        class="empty-box"
                    >

                        <i class="bi bi-chat-square-text"></i>

                        <h5>No questions yet.</h5>

                        <p>
                            Be the first to ask a question!
                        </p>

                    </div>


                    <!-- QUESTIONS -->
                    <div
                        v-for="q in questions"
                        :key="q.question_id"
                        class="question-card"
                    >

                        <!-- QUESTION TITLE -->
                        <h3 class="question-title">

                            @{{q.title}}

                        </h3>


                        <!-- QUESTION META -->
                        <div class="question-meta d-flex flex-wrap gap-3 align-items-center">

                            <span class="category-badge">

                                @{{q.category}}

                            </span>


                            <span>

                                <i class="bi bi-person"></i>

                                Asked by:

                                <strong>
                                    @{{q.user ? q.user.name : 'Unknown'}}
                                </strong>

                            </span>


                            <span>

                                <i class="bi bi-chat"></i>

                                @{{q.answers ? q.answers.length : 0}}
                                answers

                            </span>

                        </div>


                        <!-- QUESTION BODY -->
                        <p class="question-body">

                            @{{q.body}}

                        </p>


                        <!-- ANSWERS -->
                        <div class="answers-box">

                            <div
                                v-for="a in q.answers"
                                :key="a.answer_id"
                                class="answer"
                            >

                                <strong>

                                    @{{a.user ? a.user.name : 'Unknown'}}:

                                </strong>

                                @{{a.body}}

                            </div>


                            <!-- REPLY -->
                            <div class="input-group mt-3">

                                <input
                                    v-model="newAnswers[q.question_id]"
                                    type="text"
                                    class="form-control reply-input"
                                    placeholder="Write an answer..."
                                >

                                <button
                                    @click="postAnswer(q.question_id)"
                                    class="reply-btn"
                                >

                                    <i class="bi bi-reply me-1"></i>

                                    Reply

                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->
                <div class="footer">

                    AQDesk &copy; 2026 — Ask. Share. Learn.

                </div>

            </div>

        </div>



        <!-- VUE SCRIPT -->
        <script>

            const {createApp} = Vue;

            createApp({

                data() {

                    return {

                        title: '',
                        body: '',
                        category: 'AI',

                        ErrorMessage: '',
                        SuccessMessage: '',

                        questions: [],

                        newAnswers: {}

                    };

                },


                methods: {

                    async loadQuestions(){

                        try {

                            const response =
                                await fetch(
                                    'http://127.0.0.1:8000/api/questions'
                                );

                            this.questions =
                                await response.json();

                        } catch(err) {

                            console.log(
                                'Could not load questions.'
                            );

                        }

                    },


                    async postQuestion(){

                        this.ErrorMessage = '';
                        this.SuccessMessage = '';

                        const token =
                            localStorage.getItem(
                                'aqdesk_token'
                            );

                        if(!token){

                            this.ErrorMessage =
                                'Please Log In First.';

                            return;

                        }


                        try{

                            const response =
                                await fetch(
                                    'http://127.0.0.1:8000/api/questions',
                                    {

                                        method: 'POST',

                                        headers: {

                                            'Content-Type':
                                                'application/json',

                                            'Authorization':
                                                'Bearer ' + token

                                        },

                                        body: JSON.stringify({

                                            title: this.title,

                                            body: this.body,

                                            category: this.category

                                        })

                                    }
                                );


                            const data =
                                await response.json();


                            if(response.ok){

                                this.SuccessMessage =
                                    'Question posted successfully!';

                                this.title = '';

                                this.body = '';

                            }

                            else {

                                this.ErrorMessage =
                                    data.message ||
                                    'Failed to post question.';

                            }

                        }

                        catch (err) {

                            this.ErrorMessage =
                                'Could not connect to server.';

                        }

                    },


                    async postAnswer(questionId) {

                        const token =
                            localStorage.getItem(
                                'aqdesk_token'
                            );


                        if(!token){

                            alert('Please LogIn first.');

                            return;

                        }


                        const answerText =
                            this.newAnswers[questionId];


                        if(!answerText)

                            return;


                        try{

                            const response =
                                await fetch(
                                    'http://127.0.0.1:8000/api/questions/'
                                    + questionId
                                    + '/answers',
                                    {

                                        method: 'POST',

                                        headers: {

                                            'Content-Type':
                                                'application/json',

                                            'Authorization':
                                                'Bearer ' + token

                                        },

                                        body: JSON.stringify({

                                            body: answerText

                                        })

                                    }
                                );


                            if(response.ok){

                                this.newAnswers[questionId] = '';

                                this.loadQuestions();

                            }

                            else {

                                alert(
                                    'Failed to post answer.'
                                );

                            }

                        }

                        catch (err) {

                            alert(
                                'Could not connect to server.'
                            );

                        }

                    }
                },
                mounted(){
                    this.loadQuestions();
                }
            }).mount('#app');
        </script>
    </body>
</html>