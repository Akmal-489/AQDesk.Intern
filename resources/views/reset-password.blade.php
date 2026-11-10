<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AQDesk - Reset Password</title>
    <script src="/js/vue.js"></script>

    <link rel="stylesheet" href="/css/mobile.css">
</head>
<body style="font-family: sans-serif; max-width: 400px; margin: 60px auto;">
@verbatim
    <div id="app">
        <h2>Set a new password</h2>

        <label>New Password</label>
        <input v-model="password" type="password" style="width:100%; padding:8px;">

        <label style="display:block; margin-top:10px;">Confirm New Password</label>
        <input v-model="password_confirmation" type="password" style="width:100%; padding:8px;">

        <button @click="resetPassword" :disabled="loading" style="margin-top:15px; padding:10px; width:100%;">
            {{ loading ? 'Saving...' : 'Reset Password' }}
        </button>

        <p style="color:green;">{{ successMessage }}</p>
        <p style="color:red;">{{ errorMessage }}</p>
    </div>
@endverbatim

    <script>
        const { createApp } = Vue;
        createApp({
            data() {
                const params = new URLSearchParams(window.location.search);
                return {
                    token: params.get('token') || '',
                    email: params.get('email') || '',
                    password: '',
                    password_confirmation: '',
                    loading: false,
                    successMessage: '',
                    errorMessage: ''
                };
            },
            methods: {
                async resetPassword() {
                    this.successMessage = '';
                    this.errorMessage = '';
                    this.loading = true;
                    try {
                        const response = await fetch('http://127.0.0.1:8000/api/reset-password', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({
                                email: this.email,
                                token: this.token,
                                password: this.password,
                                password_confirmation: this.password_confirmation
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.successMessage = data.message + ' Redirecting to login...';
                            setTimeout(() => { window.location.href = '/login'; }, 2000);
                        } else {
                            this.errorMessage = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
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