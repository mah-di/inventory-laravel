<div class="modal animated zoomIn" id="employee-create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Employee</h5>
            </div>
            <div class="modal-body">
                <form id="save-form">
                <div class="container">
                    <div class="row">
                        <div class="col-12 p-1">
                            <label class="form-label">Assign Role(s)</label>
                            <select class="form-control" id="RoleIds" multiple></select>
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="firstName">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="lastName">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email">
                            <label class="form-label">Contact</label>
                            <input type="tel" class="form-control" id="contact">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" id="cpassword">
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="employee-modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                <button onclick="save()" id="save-btn" class="btn bg-gradient-success" >Save</button>
            </div>
        </div>
    </div>
</div>


<script>

    fillRoles()

    async function fillRoles() {
        showLoader()
        let res = await axios.get("{{ route('role.all') }}")
        hideLoader()

        relevantRoles = res.data['data']

        createOptions('RoleIds')
    }

    async function save() {
        let firstName = document.querySelector('#firstName').value
        let lastName = document.querySelector('#lastName').value
        let email = document.querySelector('#email').value
        let contact = document.querySelector('#contact').value
        let password = document.querySelector('#password').value
        let cpassword = document.querySelector('#cpassword').value

        let options = document.querySelector('#RoleIds').options

        let roles = []
        let length = options.length

        for (let i = 1; i < length; i++) {
            options[i].selected ? roles.push(options[i].value) : null
        }

        if (firstName.length < 2) {
            errorToast("First Name must be at least 2 charatcers.")
            return
        }

        if (lastName.length < 2) {
            errorToast("Last Name must be at least 2 charatcers.")
            return
        }

        if (email.length === 0) {
            errorToast("Email is required.")
            return
        }

        if (password.length < 8) {
            errorToast("Password must be at least 8 characters long.")
            return
        }

        if (password !== cpassword) {
            errorToast("Passwords doesn't match.")
            return
        }

        showLoader()

        let res = await axios.post("{{ route('employee.create') }}", {
            roles : roles,
            firstName : firstName,
            lastName : lastName,
            email : email,
            contact : contact,
            password : password
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])

            document.querySelector('#save-form').reset()
            document.querySelector('#employee-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])

            document.querySelector('#password').value = ""
            document.querySelector('#cpassword').value = ""
        }
    }

</script>
