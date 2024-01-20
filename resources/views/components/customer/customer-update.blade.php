<div class="modal animated zoomIn" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Customer</h5>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label class="form-label">Customer Name *</label>
                                <input type="text" class="form-control" id="customerNameUpdate">

                                <label class="form-label mt-3">Customer Email *</label>
                                <input type="text" class="form-control" id="customerEmailUpdate">

                                <label class="form-label mt-3">Customer Mobile *</label>
                                <input type="text" class="form-control" id="customerContactUpdate">

                                <input type="text" class="d-none" id="updateID">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="update-modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                <button onclick="update()" id="update-btn" class="btn bg-gradient-success" >Update</button>
            </div>
        </div>
    </div>
</div>


<script>

    async function update() {
        let id = document.querySelector('#updateID').value
        let name = document.querySelector('#customerNameUpdate').value
        let email = document.querySelector('#customerEmailUpdate').value
        let contact = document.querySelector('#customerContactUpdate').value

        if (name.length < 3) {
            return errorToast("Customer name must be at least 3 characters long.")
        }

        if (contact.length < 11) {
            return errorToast("Customer contact must be at least 11 digits.")
        }

        if (contact.length > 15) {
            return errorToast("Customer contact can't be more than 15 digits.")
        }

        showLoader()

        let res = await axios.patch("{{ route('customer.update') }}", {
            id : id,
            name : name,
            email : email,
            contact : contact
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])
            document.querySelector('#update-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
