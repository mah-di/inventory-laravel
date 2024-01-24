<div class="container-fluid">
    <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="card px-5 py-5">
            <div class="row justify-content-between ">
                <div class="align-items-center col">
                    <h4>Role</h4>
                </div>
                <div class="align-items-center col">
                    <button data-bs-toggle="modal" data-bs-target="#role-create-modal" class="float-end btn m-0 bg-gradient-primary">Create</button>
                </div>
            </div>
            <hr class="bg-dark "/>
            <table class="table" id="dataTable">
                <thead>
                <tr class="bg-light">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Roles</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="tableList">

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>

    getList()

    async function getList() {
        let dataTable = $('#dataTable')
        let tableList = $('#tableList')

        showLoader()

        let res = await axios.get("{{ route('employee.all') }}")

        hideLoader()

        dataTable.DataTable().destroy()
        tableList.empty()

        if (res.data['status'] === 'success') {
            res.data['data'].forEach(element => {
                let roleNames = ''
                let roleIds = ''

                element['roles'].forEach(role => {
                    roleNames += `${role['name']} | `
                    roleIds += `${role['id']},`
                })

                roleNames = roleNames.slice(0, -3)
                roleIds = roleIds.slice(0, -1)

                let row = `<tr>
                        <td>${element['firstName']} ${element['lastName']}</td>
                        <td>${element['email']}</td>
                        <td>${element['contact']}</td>
                        <td>${roleNames}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info assign-btn" data-role-ids="${roleIds}" data-employee-id="${element['id']}">Assign Role</button>
                            <button class="btn btn-sm btn-outline-warning remove-btn" data-role-ids="${roleIds}" data-role-names="${roleNames}" data-employee-id="${element['id']}">Remove Role</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${element['id']}">Delete Employee</button>
                        </td>
                    </tr>`

                tableList.append(row)
            })

            new DataTable('#dataTable', {
                order: [[0, 'desc']],
                lengthMenu: [10, 20, 50]
            })

            $('.assign-btn').on('click', async function () {
                let employeeID = $(this).data('employee-id')
                let roleIds = $(this).data('role-ids').toString().split(',')

                await fillAssignForm(employeeID, roleIds)
                $('#assign-modal').modal('show')
            })

            $('.remove-btn').on('click', async function () {
                let employeeID = $(this).data('employee-id')
                let roleIds = $(this).data('role-ids').toString().split(',')
                let roleNames = $(this).data('role-names').toString().split(' | ')

                await fillRemoveForm(employeeID, roleIds, roleNames)
                $('#remove-modal').modal('show')
            })

            $('.delete-btn').on('click', async function () {
                let id = $(this).data('id')
                $('#deleteID').val(id)
                $('#delete-modal').modal('show')
            })

        } else {
            errorToast(res.data['message'])
        }
    }

    let relevantRoles = []

    async function fillAssignForm(employeeID, roleIds) {
        showLoader()
        let res = await axios.post("{{ route('role.assignable') }}", {roleIds : roleIds})
        hideLoader()

        if (res.data['status'] === 'success') {
            document.getElementById('assignEmployeeID').value = employeeID

            relevantRoles = res.data['data']

            createOptions('assignRoleIds')
        } else {
            errorToast(res.data['message'])
        }
    }

    async function fillRemoveForm(employeeID, roleIds, roleNames) {
        document.getElementById('removeEmployeeID').value = employeeID

        relevantRoles = []

        roleIds.forEach(roleID => relevantRoles.push({id : roleID}))

        roleNames.forEach((roleName, index) => relevantRoles[index].name = roleName)

        createOptions('removeRoleIds')
    }

    function createOptions(selectID) {
        let selectElement = document.getElementById(selectID)

        selectElement.innerHTML = `<option>Select One Or More Options</option>`

        relevantRoles.forEach(role => {
            let option = document.createElement('option')
            option.value = role.id
            option.innerText = role.name

            selectElement.appendChild(option)
        })
    }

</script>

