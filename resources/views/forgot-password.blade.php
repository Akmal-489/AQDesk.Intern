<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AQDesk - Forgot Password</title>
    <script src="/js/vue.js"></script>

    <link rel="stylesheet" href="/css/mobile.css">
</head>
<body style="font-family: sans-serif; max-width: 400px; margin: 60px auto;">
@verbatim
    <div id="app">
        <h2>Forgot your password?</h2>
        <p>Enter your registered email and we'll send you a reset link.</p>

        <input v-model="email" type="email" placeholder="Email" style="width:100%; padding:8px;">

        <button @click="sendLink" :disabled="loading" style="margin-top:15px; padding:10px; width:100%;">
            {{ loading ? 'Sending...' : 'Send Reset Link' }}
        </button>

        <p style="color:green;">{{ successMessage }}</p>
        <p style="color:red;">{{ errorMessage }}</p>
        <p><a href="/login">Back to login</a></p>
    </div>
@endverbatim

    <script>
        const { createApp } = Vue;
        createApp({
            data() {
                return { email: '', loading: false, successMessage: '', errorMessage: '' };
            },
            methods: {
                async sendLink() {
                    this.successMessage = '';
                    this.errorMessage = '';
                    this.loading = true;
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/forgot-password', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ email: this.email })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.successMessage = data.message;
                        } else {
                            this.errorMessage = data.errors ? Object.values(data.errors).flat().join(' ') : 'Something went wrong.';
                        }
                    } catch (err) {
                        this.errorMessage = 'Could not connect to server.';
                    }
                    this.loading = false;
                }
            }
        }).mount('#app');
    </script>
</body>
</html>