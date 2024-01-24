<div class="modal animated zoomIn" id="role-create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Role</h5>
                </div>
                <div class="modal-body">
                    <form id="save-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label class="form-label">Role Name *</label>
                                <input type="text" class="form-control" id="roleName">
                                <label class="form-label">Role Slug</label>
                                <input type="email" class="form-control" id="roleSlug">
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="role-modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                    <button onclick="save()" id="save-btn" class="btn bg-gradient-success" >Save</button>
                </div>
            </div>
    </div>
</div>


<script>

    async function save() {
        let name = document.querySelector('#roleName').value
        let slug = document.querySelector('#roleSlug').value

        if (name.length < 3) {
            return errorToast("Role name must be at least 3 characters long.")
        }

        if (slug.length < 3) {
            return errorToast("Role slug must be at least 3 characters long.")
        }

        showLoader()

        let res = await axios.post("{{ route('role.create') }}", {
            name : name,
            slug : slug
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])

            document.querySelector('#roleName').value = null
            document.querySelector('#roleSlug').value = null
            document.querySelector('#role-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
