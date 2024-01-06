<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90  p-4">
                <div class="card-body">
                    <h4>VERIFY YOUR EMAIL</h4>
                    <br/>
                    <label>4 Digit OTP Code Here</label>
                    <input id="otp" placeholder="Code" class="form-control" type="text"/>
                    <br/>
                    <button onclick="verifyEmail()"  class="btn w-100 float-end bg-gradient-primary">Verify</button>
                    <div class="float-end mt-3">
                        <span>
                            <span style="cursor: pointer" onclick="resendOTP()" class="text-center ms-3 h6" href="{{ route('request.otp.view') }}">Resend Verification OTP</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function verifyEmail() {
        let otp = document.querySelector('#otp').value

        if (otp.length !== 4) {
            errorToast("Invalid OTP")
            return
        }

        showLoader()

        let res = await axios.post("{{ route('verify.email') }}", {
            otp : otp
        })

        if (res.data['status'] === 'success') {
            successToast(res.data['message'] + " Taking you to your dashboard.")

            setTimeout(() => {
                window.location.href = "{{ route('dashboard.view') }}"
            }, 2000);
        } else {
            hideLoader()

            errorToast(res.data['message'])
        }
    }

    async function resendOTP() {
        showLoader()

        let res = await axios.get("{{ route('resend.emailVerification') }}")

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
