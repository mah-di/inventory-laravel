<div class="modal animated zoomIn" id="create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Customer</h5>
                </div>
                <div class="modal-body">
                    <form id="save-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label class="form-label">Customer Name *</label>
                                <input type="text" class="form-control" id="customerName">
                                <label class="form-label">Customer Email</label>
                                <input type="email" class="form-control" id="customerEmail">
                                <label class="form-label">Customer Contact *</label>
                                <input type="tel" class="form-control" id="customerContact">
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                    <button onclick="save()" id="save-btn" class="btn bg-gradient-success" >Save</button>
                </div>
            </div>
    </div>
</div>


<script>

    async function save() {
        let name = document.querySelector('#customerName').value
        let email = document.querySelector('#customerEmail').value
        let contact = document.querySelector('#customerContact').value

        if (name.length < 3) {
            return errorToast("Customer name must be at least 3 characters long.")
        }

        if (contact.length < 11) {
            return errorToast("Customer contact must be at least 11 digits.")
        }

        if (contact.length > 14) {
            return errorToast("Customer contact can't be more than 14 digits.")
        }

        showLoader()

        let res = await axios.post("{{ route('customer.create') }}", {
            name : name,
            email : email,
            contact : contact
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])

            document.querySelector('#customerName').value = null
            document.querySelector('#customerEmail').value = null
            document.querySelector('#customerContact').value = null
            document.querySelector('#modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
