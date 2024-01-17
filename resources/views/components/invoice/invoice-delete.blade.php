<div class="modal animated zoomIn" id="delete-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 10000">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h3 class=" mt-3 text-warning">Delete !</h3>
                <p class="mb-3">Once delete, you can't get it back.</p>
                <input class="d-none" id="deleteID"/>
            </div>
            <div class="modal-footer justify-content-end">
                <div>
                    <button type="button" onclick="document.querySelector('#details-modal').style.zIndex = 1050" id="delete-modal-close" class="btn bg-gradient-success" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="itemDelete()" type="button" id="confirmDelete" class="btn bg-gradient-danger" >Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function itemDelete() {
        let id = document.getElementById('deleteID').value

        showLoader()

        let res = await axios.delete("{{ route('invoice.delete') }}", {
            data : {
                id : id
            }
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            document.getElementById('delete-modal-close').click()

            document.querySelector('#details-modal').style.zIndex = 1050
            document.querySelector('#details-modal-close').click()

            successToast(res.data['message'])

            try {
                soldItemList.forEach(item => {
                    restock(item)
                })
            } catch (error) {
                await getList()
            }

        }
        else {
            errorToast(res.data['message'])
        }
    }

</script>
