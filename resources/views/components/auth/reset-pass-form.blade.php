<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90 p-4">
                <div class="card-body">
                    <h4>SET NEW PASSWORD</h4>
                    <br/>
                    <label>New Password</label>
                    <input id="password" placeholder="New Password" class="form-control" type="password"/>
                    <br/>
                    <label>Confirm Password</label>
                    <input id="cpassword" placeholder="Confirm Password" class="form-control" type="password"/>
                    <br/>
                    <button onclick="resetPassword()" class="btn w-100 bg-gradient-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function resetPassword() {
        let password = document.querySelector('#password').value
        let cpassword = document.querySelector('#cpassword').value

        if (password.length === 0) {
            errorToast("Password is required")
            return
        }

        if (password !== cpassword) {
            errorToast("Passwords don't match")
            return
        }

        showLoader()

        let res = await axios.patch("{{ route('password.reset') }}", {
            password : password
        })

        if (res.data['status'] === 'success') {
            successToast(res.data['message'] + " Redirecting to login page.")

            setTimeout(() => {
                window.location.href = "{{ route('login.view') }}"
            }, 2000);
        } else {
            hideLoader()

            errorToast(res.data['message'])
        }
    }

</script>
