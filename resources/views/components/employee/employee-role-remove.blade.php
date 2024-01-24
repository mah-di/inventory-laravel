<div class="modal animated zoomIn" id="remove-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h3 class=" mt-3 text-warning">Remove Role(s)</h3>
                <input class="d-none" id="removeEmployeeID"/>
                <label class="form-label">Remove Role(s)</label>
                <select class="form-control" id="removeRoleIds" multiple></select>
            </div>
            <div class="modal-footer justify-content-end">
                <div>
                    <button type="button" id="remove-modal-close" class="btn mx-2 bg-gradient-primary" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="removeRole()" type="button" id="confirmDelete" class="btn  bg-gradient-success" >Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function removeRole() {
        let employeeID = document.querySelector('#removeEmployeeID').value
        let options = document.querySelector('#removeRoleIds').options

        let roleIds = []
        let length = options.length

        for (let i = 1; i < length; i++) {
            options[i].selected ? roleIds.push(options[i].value) : null
        }

        if (roleIds.length === 0) {
            return errorToast('Please Select At Least 1 Role To Remove.')
        }

        showLoader()

        let res = await axios.delete("{{ route('userRole.delete') }}", {
            data : {
                employee_id : employeeID,
                roleIds : roleIds
            }
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])
            document.querySelector('#remove-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
