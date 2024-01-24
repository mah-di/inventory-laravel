<div class="container-fluid">
    <div class="row">
    <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="card px-5 py-5">
            <div class="row justify-content-between ">
                <div class="align-items-center col">
                    <h4>Product</h4>
                </div>
                <div class="align-items-center col">
                    <button data-bs-toggle="modal" data-bs-target="#create-modal" class="float-end btn m-0  bg-gradient-primary">Create</button>
                </div>
            </div>
            <hr class="bg-dark "/>
            <table class="table" id="dataTable">
                <thead>
                <tr class="bg-light">
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
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
        let dataTable = $("#dataTable")

        let res = await axios.get("{{ route('product.all') }}")
        hideLoader()

        dataTable.DataTable().destroy()
        tableList.empty()

        res.data['data'].forEach(function (element) {
            hasPermission = checkPermission(['owner', 'manager'])

            let row = `<tr>
                        <td><img class="w-15 h-auto" alt="" src="${element['img_url']}"></td>
                        <td>${element['name']}</td>
                        <td>${element['price']}</td>
                        <td>${element['stock']}</td>
                        <td>
                            <button data-path="${element['img_url']}" data-id="${element['id']}" class="btn editBtn btn-sm btn-outline-info">Edit</button>` +
                            (
                                hasPermission
                                    ? `&nbsp;&nbsp;<button data-path="${element['img_url']}" data-id="${element['id']}" class="btn deleteBtn btn-sm btn-outline-danger">Delete</button>`
                                    : ``
                            )
                        + `</td>
                    </tr>`

            tableList.append(row)
        })

        $('.editBtn').on('click', async function () {
            let id = $(this).data('id')
            let filePath = $(this).data('path')

            await fillUpdateForm(id, filePath)

            $("#update-modal").modal('show')
        })

        if (hasPermission) {
            $('.deleteBtn').on('click', function () {
                let id = $(this).data('id')
                let path = $(this).data('path')

                $("#deleteID").val(id)
                $("#deleteFilePath").val(path)

                $("#delete-modal").modal('show')
            })
        }

        new DataTable('#dataTable', {
            order : [[0, 'desc']],
            lengthMenu : [10, 20, 50]
        });
    }

</script>

