<div class="modal animated zoomIn" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h3 class=" mt-3 text-warning">Delete !</h3>
                <p class="mb-3">Once delete, you can't get it back.</p>
                <input class="d-none" id="deleteID"/>

            </div>
            <div class="modal-footer justify-content-end">
                <div>
                    <button type="button" id="delete-modal-close" class="btn mx-2 bg-gradient-primary" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="deleteCustomer()" type="button" id="confirmDelete" class="btn  bg-gradient-danger" >Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function deleteCustomer() {
        let id = document.querySelector('#deleteID').value

        showLoader()

        let res = await axios.delete("{{ route('customer.delete') }}", {
            data : {
                id : id
            }
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])
            document.querySelector('#delete-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
