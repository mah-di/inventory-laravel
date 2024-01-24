<div class="container-fluid">
    <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="card px-5 py-5">
            <div class="row justify-content-between ">
                <div class="align-items-center col">
                    <h5>Invoices</h5>
                </div>
                <div class="align-items-center col">
                    <a    href="{{ route('sale.view') }}" class="float-end btn m-0 bg-gradient-primary">Create Sale</a>
                </div>
            </div>
            <hr class="bg-dark "/>
            <table class="table" id="tableData">
                <thead>
                <tr class="bg-light">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>Vat</th>
                    <th>Payable</th>
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

    showLoader()
    getRoles().then(() => getList())

    async function getList() {
        let hasPermission = false
        let tableList = $("#tableList")
        let tableData = $("#tableData")

        let res = await axios.get("{{ route('invoice.all') }}")
        hideLoader()

        if (res.data['status'] === 'fail') {
            return errorToast(res.data['message'])
        }

        tableData.DataTable().destroy()
        tableList.empty()

        res.data['data'].forEach(function (element) {
            hasPermission = checkPermission(['owner', 'manager'])

            let row = `
                    <tr>
                        <td>${element['id']}</td>
                        <td>${element['customer']['name']}</td>
                        <td>${element['customer']['contact']}</td>
                        <td>${element['total']}</td>
                        <td>${element['discount']}</td>
                        <td>${element['vat']}</td>
                        <td>${element['payable']}</td>
                        <td>
                            <button data-id="${element['id']}" class="viewBtn btn btn-outline-dark text-sm px-3 py-1 btn-sm m-0"><i class="fa text-sm fa-eye"></i></button>` +
                            (
                                hasPermission
                                    ? `&nbsp;&nbsp;<button data-id="${element['id']}" class="deleteBtn btn btn-outline-dark text-sm px-3 py-1 btn-sm m-0"><i class="fa text-sm  fa-trash-alt"></i></button>`
                                    : ``
                            )
                        + `
                        </td>
                    </tr>
                `

            tableList.append(row)
        })

        $('.viewBtn').on('click', async function () {
            let id = $(this).data('id')

            await invoiceDetails(id)
        })

        if (hasPermission) {
            $('.deleteBtn').on('click',function () {
                let id = $(this).data('id')

                document.getElementById('deleteID').value = id

                $("#delete-modal").modal('show')
            })
        }

        new DataTable('#tableData', {
            order : [[0, 'desc']],
            lengthMenu : [10, 20, 50]
        })

    }

</script>

