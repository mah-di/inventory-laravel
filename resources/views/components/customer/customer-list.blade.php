<div class="container-fluid">
    <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="card px-5 py-5">
            <div class="row justify-content-between ">
                <div class="align-items-center col">
                    <h4>Customer</h4>
                </div>
                <div class="align-items-center col">
                    <button data-bs-toggle="modal" data-bs-target="#create-modal" class="float-end btn m-0 bg-gradient-primary">Create</button>
                </div>
            </div>
            <hr class="bg-dark "/>
            <table class="table" id="dataTable">
                <thead>
                <tr class="bg-light">
                    <th class="d-none">No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
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

        let res = await axios.get("{{ route('customer.all') }}")

        hideLoader()

        dataTable.DataTable().destroy()
        tableList.empty()

        if (res.data['status'] === 'success') {
            res.data['data'].forEach(element => {
                let row = `<tr>
                        <td class="d-none">${element['id']}</td>
                        <td>${element['name']}</td>
                        <td>${element['email'] ?? "N/A"}</td>
                        <td>${element['contact']}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info update-btn" data-id="${element['id']}">Update</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${element['id']}">Delete</button>
                        </td>
                    </tr>`

                tableList.append(row)
            });

            new DataTable('#dataTable', {
                order: [[0, 'desc']],
                lengthMenu: [10, 20, 50]
            })

            $('.update-btn').on('click', async function () {
                let id = $(this).data('id')
                await fillUpdateForm(id)
                $('#update-modal').modal('show')
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

    async function fillUpdateForm(id) {
        showLoader()
        let res = await axios.get(`{{ url('/api/customer') }}/${id}`)
        hideLoader()

        if (res.data['status'] === 'success'){
            document.getElementById('updateID').value = id
            document.getElementById('customerNameUpdate').value = res.data['data']['name']
            document.getElementById('customerEmailUpdate').value = res.data['data']['email']
            document.getElementById('customerContactUpdate').value = res.data['data']['contact']

        } else {
            errorToast(res.data['message'])
        }
    }

</script>

