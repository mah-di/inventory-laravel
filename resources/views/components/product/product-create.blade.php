<div class="modal animated zoomIn" id="create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Product</h5>
                </div>
                <div class="modal-body">
                    <form id="save-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">

                                <label class="form-label">Category</label>
                                <select type="text" class="form-control form-select" id="productCategory"></select>

                                <label class="form-label mt-2">Name</label>
                                <input type="text" class="form-control" id="productName">

                                <label class="form-label mt-2">Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="productPrice">

                                <label class="form-label mt-2">Stock</label>
                                <input type="number" min="0" class="form-control" id="productStock">

                                <br/>
                                <img class="w-15" id="newImg" src="{{ asset('images/default.jpg')}} "/>
                                <br/>

                                <label class="form-label">Image</label>
                                <input oninput="newImg.src = window.URL.createObjectURL(this.files[0])" type="file" class="form-control" id="productImg">

                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-close" class="btn bg-gradient-primary mx-2" data-bs-dismiss="modal" aria-label="Close">Close</button>
                    <button onclick="save()" id="save-btn" class="btn bg-gradient-success" >Save</button>
                </div>
            </div>
    </div>
</div>


<script>

    fillCategoryDropDown()

    async function fillCategoryDropDown(){
        let res = await axios.get("{{ route('category.all') }}")

        document.getElementById('productCategory').innerHTML = `<option value="">Select Category</option>`

        res.data['data'].forEach(function (element) {
            let option = `<option value="${element['id']}">${element['name']}</option>`

            $("#productCategory").append(option)
        })
    }

    async function save() {
        let category_id = document.getElementById('productCategory').value
        let name = document.getElementById('productName').value
        let price = document.getElementById('productPrice').value
        let stock = document.getElementById('productStock').value
        let img = document.getElementById('productImg').files[0] ?? null

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

        if(stock.length === 0) {
            return errorToast("Product Stock is Required.")
        }

        if(stock < 0) {
            return errorToast("Product Stock Can't Be Negative.")
        }

        let data = new FormData();

        data.append('category_id', category_id)
        data.append('name', name)
        data.append('price', price)
        data.append('stock', stock)
        img !== null ? data.append('img', img) : null

        const config = {
            headers: {
                'content-type': 'multipart/form-data'
            }
        }

        showLoader()

        let res = await axios.post("{{ route('product.create') }}", data, config)

        hideLoader()

        if(res.data['status'] === 'success'){
            successToast(res.data['message']);

            document.getElementById("save-form").reset();

            document.getElementById('modal-close').click();

            await getList();
        }
        else{
            errorToast(res.data['message'])
        }
    }

</script>
