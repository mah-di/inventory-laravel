<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90  p-4">
                <div class="card-body">
                    <h4>EMAIL ADDRESS</h4>
                    <br/>
                    <label>Your email address</label>
                    <input id="email" placeholder="User Email" class="form-control" type="email"/>
                    <br/>
                    <button onclick="requestOTP()"  class="btn w-100 float-end bg-gradient-primary">Next</button>
                    <div class="float-end mt-3">
                        <span>
                            <a class="text-center ms-3 h6" href="{{ route('verify.otp.view') }}">Already requested for a OTP?</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function requestOTP() {
        let email = document.querySelector('#email').value

        if (email.length === 0) {
            errorToast("Email is required")
        }

        showLoader()

        let res = await axios.post("{{ route('send.otp') }}", {
            email : email
        })

        if (res.data['status'] === 'success') {
            successToast(res.data['message'] + " Redirecting to OTP verification page..")

            localStorage.setItem('email', email)

            setTimeout(() => {
                window.location.href = "{{ route('verify.otp.view') }}"
            }, 2000);
        } else {
            hideLoader()

            errorToast(res.data['message'])
        }
    }

</script>
