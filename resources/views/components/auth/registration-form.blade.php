<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-10 center-screen">
            <div class="card animated fadeIn w-100 p-3">
                <div class="card-body">
                    <h4>Sign Up</h4>
                    <hr/>
                    <div class="container-fluid m-0 p-0">
                        <div class="row m-0 p-0">

                            <div class="col-md-6 p-2">
                                <label>First Name</label>
                                <input id="firstName" placeholder="First Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-6 p-2">
                                <label>Last Name</label>
                                <input id="lastName" placeholder="Last Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-6 p-2">
                                <label>Email Address</label>
                                <input id="email" placeholder="Email" class="form-control" type="email"/>
                            </div>
                            <div class="col-md-6 p-2"></div>
                            <div class="col-md-6 p-2">
                                <label>Contact Number</label>
                                <input id="contact" placeholder="Contact Number" class="form-control" type="tel" />
                            </div>
                            <div class="col-md-6 p-2"></div>
                            <div class="col-md-6 p-2">
                                <label>Password</label>
                                <input id="password" placeholder="Password" class="form-control" type="password"/>
                            </div>
                            <div class="col-md-6 p-2">
                                <label>Confirm Password</label>
                                <input id="cpassword" placeholder="Confirm Password" class="form-control" type="password"/>
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <button onclick="submitRegistration()" class="btn mt-3 w-100  bg-gradient-primary">Complete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function submitRegistration() {
        let firstName = document.querySelector('#firstName').value
        let lastName = document.querySelector('#lastName').value
        let email = document.querySelector('#email').value
        let contact = document.querySelector('#contact').value
        let password = document.querySelector('#password').value
        let cpassword = document.querySelector('#cpassword').value

        if (firstName.length === 0) {
            errorToast("First Name is required")
            return
        }

        if (lastName.length === 0) {
            errorToast("Last Name is required")
            return
        }

        if (email.length === 0) {
            errorToast("Email is required")
            return
        }

        if (password.length === 0) {
            errorToast("Password is required")
            return
        }

        if (password !== cpassword) {
            errorToast("Passwords don't match")
            return
        }

        showLoader()

        let res = await axios.post("{{ route('register') }}", {
                firstName :firstName,
                lastName : lastName,
                email : email,
                contact : contact,
                password : password
            })

        if (res.data['status'] === 'success') {
            window.location.href = "{{ route('dashboard.view') }}"
        } else {
            errorToast(res.data['message'])

            document.querySelector('#password').value = ""
            document.querySelector('#cpassword').value = ""
        }

        hideLoader()
    }

</script>
