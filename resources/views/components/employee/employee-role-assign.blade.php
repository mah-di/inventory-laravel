<div class="modal animated zoomIn" id="assign-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h3 class=" mt-3 text-warning">Assign Role(s)</h3>
                <input class="d-none" id="assignEmployeeID"/>
                <label class="form-label">Assign Role(s)</label>
                <select class="form-control" id="assignRoleIds" multiple></select>
            </div>
            <div class="modal-footer justify-content-end">
                <div>
                    <button type="button" id="assign-modal-close" class="btn mx-2 bg-gradient-primary" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="assignRole()" type="button" id="confirmDelete" class="btn  bg-gradient-success" >Assign</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    async function assignRole() {
        let employeeID = document.querySelector('#assignEmployeeID').value
        let options = document.querySelector('#assignRoleIds').options

        let roleIds = []
        let length = options.length

        for (let i = 1; i < length; i++) {
            options[i].selected ? roleIds.push(options[i].value) : null
        }

        if (roleIds.length === 0) {
            return errorToast('Please Select At Least 1 Role To Assign.')
        }

        showLoader()

        let res = await axios.post("{{ route('userRole.create') }}", {
            employee_id : employeeID,
            roleIds : roleIds
        })

        hideLoader()

        if (res.data['status'] === 'success') {
            successToast(res.data['message'])
            document.querySelector('#assign-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
