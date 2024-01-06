<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90  p-4">
                <div class="card-body">
                    <h4>ENTER OTP CODE</h4>
                    <br/>
                    <label>4 Digit Code Here</label>
                    <input id="otp" placeholder="Code" class="form-control" type="text"/>
                    <br/>
                    <button onclick="verifyOTP()"  class="btn w-100 float-end bg-gradient-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function verifyOTP() {
        let otp = document.querySelector('#otp').value
        let email = localStorage.getItem('email')

        if (otp.length !== 4) {
            errorToast("Invalid OTP")
            return
        }

        showLoader()

        let res = await axios.post("{{ route('verify.otp') }}", {
            email : email,
            otp : otp
        })

        if (res.data['status'] === 'success') {
            successToast(res.data['message'] + " Redirecting to password resetting page.")

            localStorage.removeItem('email')

            setTimeout(() => {
                window.location.href = "{{ route('password.reset.view') }}"
            }, 2000);
        } else {
            hideLoader()

            errorToast(res.data['message'])
        }
    }

</script>
