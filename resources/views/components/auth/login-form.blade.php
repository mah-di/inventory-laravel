<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 animated fadeIn col-lg-6 center-screen">
            <div class="card w-90  p-4">
                <div class="card-body">
                    <h4>SIGN IN</h4>
                    <br/>
                    <input id="email" placeholder="User Email" class="form-control" type="email"/>
                    <br/>
                    <input id="password" placeholder="User Password" class="form-control" type="password"/>
                    <br/>
                    <button onclick="submitLogin()" class="btn w-100 bg-gradient-primary">Next</button>
                    <hr/>
                    <div class="float-end mt-3">
                        <span>
                            <a class="text-center ms-3 h6" href="{{ route('register.view') }}">Sign Up </a>
                            <span class="ms-1">|</span>
                            <a class="text-center ms-3 h6" href="{{ route('request.otp.view') }}">Forget Password</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    async function submitLogin() {
        let email = document.querySelector('#email').value
        let password = document.querySelector('#password').value

        if (email.length === 0) {
            errorToast("Email is required")
            return
        }

        if (password.length === 0) {
            errorToast("Password is required")
            return
        }

        showLoader()

        let res = await axios.post("{{ route('login') }}", {
                email : email,
                password : password
            })

        if (res.data['status'] === 'success') {
            window.location.href = "{{ route('dashboard.view') }}"
        } else {
            errorToast(res.data['message'])
        }

        hideLoader()
    }

</script>
