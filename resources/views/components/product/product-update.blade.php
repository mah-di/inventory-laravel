<div class="modal animated zoomIn" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Product</h5>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">


                                <label class="form-label">Category</label>
                                <select type="text" class="form-control form-select" id="productCategoryUpdate"></select>

                                <label class="form-label mt-2">Name</label>
                                <input type="text" class="form-control" id="productNameUpdate">

                                <label class="form-label mt-2">Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="productPriceUpdate">

                                <input type="number" class="d-none" id="productExistingStock">

                                <label class="form-label mt-2">Add Stock</label>
                                <input type="number" value="0" min="0" class="form-control" id="productStockUpdate">
                                <br/>
                                <img class="w-15" id="oldImg" src="{{ asset('images/default.jpg') }}"/>
                                <br/>
                                <label class="form-label mt-2">Image</label>
                                <input oninput="oldImg.src=window.URL.createObjectURL(this.files[0])"  type="file" class="form-control" id="productImgUpdate">

                                <input type="text" class="d-none" id="updateID">
                                <input type="text" class="d-none" id="filePath">


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

    async function fillUpdateForm(id, filePath) {
        document.getElementById('updateID').value = id
        document.getElementById('filePath').value = filePath
        document.getElementById('oldImg').src = filePath

        showLoader()

        await updateFillCategoryDropDown()
        let res = await axios.get(`{{ url('/api/product') }}/${id}`)

        hideLoader()

        document.getElementById('productCategoryUpdate').value = res.data['data']['category_id']
        document.getElementById('productNameUpdate').value = res.data['data']['name']
        document.getElementById('productPriceUpdate').value = res.data['data']['price']
        document.getElementById('productExistingStock').value = res.data['data']['stock']
    }

    async function updateFillCategoryDropDown() {
        let res = await axios.get("{{ route('category.all') }}")

        document.getElementById('productCategoryUpdate').innerHTML = `<option value="">Select Category</option>`

        res.data['data'].forEach(function (element) {
            let option = `<option value="${element['id']}">${element['name']}</option>`

            $("#productCategoryUpdate").append(option)
        })
    }

    async function update() {
        let id = document.getElementById('updateID').value
        let category_id = document.getElementById('productCategoryUpdate').value
        let name = document.getElementById('productNameUpdate').value
        let price = document.getElementById('productPriceUpdate').value

        let existingStock = document.getElementById('productExistingStock').value
        let addStock = document.getElementById('productStockUpdate').value ?? 0
        let stock = Number(existingStock) + Number(addStock)

        let img = document.getElementById('productImgUpdate').files[0] ?? null
        let img_url = document.getElementById('filePath').value

        if (category_id.length === 0) {
            return errorToast("Product Category is Required.")
        }

        if(name.length === 0) {
            return errorToast("Product Name is Required.")
        }

        if(price.length === 0) {
            return errorToast("Product Price is Required.")
        }

        if(price < 0) {
            return errorToast("Product Price Can't Be Negative.")
        }

        if(addStock < 0) {
            return errorToast("Add Stock Field Can't Be Negative.")
        }

        let data = new FormData()

        data.append('_method', 'PATCH')
        data.append('id', id)
        data.append('category_id', category_id)
        data.append('name', name)
        data.append('price', price)
        data.append('stock', stock)

        img !== null ? data.append('img', img) : null

        data.append('img_url', img_url)

        const config = {
            headers : {
                'content-type': 'multipart/form-data'
            }
        }

        showLoader()

        let res = await axios.post("{{ route('product.update') }}", data, config)

        hideLoader()

        if(res.data['status'] === 'success'){
            successToast(res.data['message'])

            document.getElementById("update-form").reset()

            document.getElementById('update-modal-close').click()

            await getList()
        } else {
            errorToast(res.data['message'])
        }
    }

</script>
