<div class="modal animated zoomIn" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Role</h5>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label class="form-label">Role Name *</label>
                                <input type="text" class="form-control" id="roleNameUpdate">

                                <label class="form-label mt-3">Role Slug *</label>
                                <input type="text" class="form-control" id="roleSlugUpdate">

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
        let name = document.querySelector('#roleNameUpdate').value
        let slug = document.querySelector('#roleSlugUpdate').value

        if (name.length < 3) {
            return errorToast("Role name must be at least 3 characters long.")
        }

        if (slug.length < 3) {
            return errorToast("Role slug must be at least 3 characters long.")
        }

        showLoader()

        let res = await axios.patch("{{ route('role.update') }}", {
            id : id,
            name : name,
            slug : slug
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
