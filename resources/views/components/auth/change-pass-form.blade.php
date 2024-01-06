<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90 p-4">
                <div class="card-body">
                    <h4>CHANGE PASSWORD</h4>
                    <br/>
                    <label>Current Password</label>
                    <input id="password" placeholder="Current Password" class="form-control" type="password"/>
                    <br/>
                    <label>New Password</label>
                    <input id="new_password" placeholder="New Password" class="form-control" type="password"/>
                    <br/>
                    <label>Confirm Password</label>
                    <input id="cpassword" placeholder="Confirm Password" class="form-control" type="password"/>
                    <br/>
                    <button onclick="changePassword()" class="btn w-100 bg-gradient-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function changePassword() {
        let password = document.querySelector('#password').value
        let newPassword = document.querySelector('#new_password').value
        let cpassword = document.querySelector('#cpassword').value

        if (password.length === 0) {
            errorToast("Password is required")
            return
        }

        if (newPassword.length === 0) {
            errorToast("New Password is required")
            return
        }

        if (newPassword !== cpassword) {
            errorToast("Passwords don't match")
            return
        }

        showLoader()

        let res = await axios.patch("{{ route('password.change') }}", {
            password : password,
            new_password : newPassword
        })

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])

            setTimeout(() => {
                window.location.href = "{{ route('profile.view') }}"
            }, 2000);
        } else {
            hideLoader()

            errorToast(res.data['message'])
        }
    }
</script>
