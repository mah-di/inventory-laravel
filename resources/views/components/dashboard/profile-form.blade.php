<div class="container">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card animated fadeIn w-100 p-3">
                <div class="card-body">
                    <h4>Profile Information</h4>
                    <hr/>
                    <div class="container-fluid m-0 p-0">
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <label>Email Address</label>
                                <input readonly id="email" placeholder="User Email" class="form-control" type="email"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>First Name</label>
                                <input id="firstName" placeholder="First Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Last Name</label>
                                <input id="lastName" placeholder="Last Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Contact Number</label>
                                <input id="contact" placeholder="Cotact Number" class="form-control" type="tel"/>
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <button onclick="update()" class="btn mt-3 w-100  bg-gradient-primary">Update</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row m-0 p-0">
                            <div class="col-md-3 p-2">
                                <a href="{{ route('password.change.view') }}" class="btn mt-3 w-100 bg-secondary text-white">Change Password</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    getProfile()

    async function getProfile() {
        showLoader()

        let res = await axios.get("{{ route('user.details') }}")

        if (res.data['status'] === 'success'){

            document.querySelector('#email').value = res.data['user']['email']
            document.querySelector('#firstName').value = res.data['user']['firstName']
            document.querySelector('#lastName').value = res.data['user']['lastName']
            document.querySelector('#contact').value = res.data['user']['contact']

        } else {
            errorToast("Unable to retrieve data at this moment...")
        }

        hideLoader()
    }

    async function update() {
        let firstName = document.querySelector('#firstName').value
        let lastName = document.querySelector('#lastName').value
        let contact = document.querySelector('#contact').value

        if (firstName.length === 0) {
            errorToast("First Name is required.")

            return
        }

        if (lastName.length === 0) {
            errorToast("First Name is required.")

            return
        }

        showLoader()

        let res = await axios.patch("{{ route('update.user') }}", {
            firstName : firstName,
            lastName : lastName,
            contact : contact
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])

            await getProfile()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>

