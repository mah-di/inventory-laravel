@extends('layout.sidenav-layout')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-lg-4 p-2">
                <div class="shadow-sm h-100 bg-white rounded-3 p-3">
                    <div class="row">
                        <div class="col-8">
                            <span class="text-bold text-dark">BILLED TO </span>
                            <p class="text-xs mx-0 my-1">Name:  <span id="CName"></span> </p>
                            <p class="text-xs mx-0 my-1">Contact:  <span id="CContact"></span></p>
                            <p class="text-xs mx-0 my-1">Customer ID:  <span id="CId"></span> </p>
                        </div>
                        <div class="col-4">
                            <img class="w-50" src="{{"images/logo.png"}}">
                            <p class="text-bold mx-0 my-1 text-dark">Invoice  </p>
                            <p class="text-xs mx-0 my-1">Date: {{ date('Y-m-d') }} </p>
                        </div>
                    </div>
                    <hr class="mx-0 my-2 p-0 bg-secondary"/>
                    <div class="row">
                        <div class="col-12">
                            <table class="table w-100" id="invoicePreviewTable">
                                <thead class="w-100">
                                <tr class="text-xs">
                                    <td>Name</td>
                                    <td>Qty</td>
                                    <td>Total</td>
                                    <td>Remove</td>
                                </tr>
                                </thead>
                                <tbody  class="w-100" id="invoicePreviewList">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="mx-0 my-2 p-0 bg-secondary"/>
                    <div class="row">
                       <div class="col-12">
                           <p class="text-bold text-xs my-1 text-dark"> TOTAL: <span id="total"></span></p>
                           <p class="text-bold text-xs my-2 text-dark"> PAYABLE:  <span id="payable"></span></p>
                           <p class="text-bold text-xs my-1 text-dark"> VAT(5%):  <span id="vat"></span></p>
                           <p class="text-bold text-xs my-1 text-dark"> Discount:  <span id="discount"></span></p>
                           <span class="text-xxs">Discount(%):</span>
                           <input value="0" min="0" type="number" step="0.25" onchange="discountChange()" class="form-control w-40 " id="discountP"/>
                           <p>
                              <button onclick="createInvoice()" class="btn  my-3 bg-gradient-primary w-40">Confirm</button>
                           </p>
                       </div>
                        <div class="col-12 p-2">

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 p-2">
                <div class="shadow-sm h-100 bg-white rounded-3 p-3">
                    <table class="table  w-100" id="productTable">
                        <thead class="w-100">
                        <tr class="text-xs text-bold">
                            <td>Product</td>
                            <td>Stock</td>
                            <td>Pick</td>
                        </tr>
                        </thead>
                        <tbody  class="w-100" id="productList">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 p-2">
                <div class="shadow-sm h-100 bg-white rounded-3 p-3">
                    <table class="table table-sm w-100" id="customerTable">
                        <thead class="w-100">
                        <tr class="text-xs text-bold">
                            <td>Customer</td>
                            <td>Contact</td>
                            <td>Pick</td>
                        </tr>
                        </thead>
                        <tbody  class="w-100" id="customerList">

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <div class="modal animated zoomIn" id="create-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Add Product</h6>
                </div>
                <div class="modal-body">
                    <form id="add-form">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 p-1">
                                    <input type="text" class="d-none" id="PId">
                                    <p class="text-lg">Name: <span id="PName"></span></p>
                                    <p class="">Price: <span id="PPrice"></span></p>
                                    <p class="">Stock: <span id="PStock"></span></p>
                                    <label class="form-label mt-2">Product Qty *</label>
                                    <input type="number" value="0" class="form-control" id="PQty">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                    <button onclick="add()" id="save-btn" class="btn bg-gradient-success" >Add</button>
                </div>
            </div>
        </div>
    </div>


    <script>

        (async () => {
            showLoader()

            await  customerList()
            await productList()

            hideLoader()
        })()

        let invoiceItemList = []

        let soldItemList = []

        function showInvoiceItem() {
            let invoicePreviewList = $('#invoicePreviewList');

            invoicePreviewList.empty();

            invoiceItemList.forEach(function (element, index) {
                let row = `<tr class="text-xs">
                        <td>${element['product_name']}</td>
                        <td>${element['qty']}</td>
                        <td>${element['sale_price']}</td>
                        <td><a data-index="${index}" class="btn remove text-xxs px-2 py-1  btn-sm m-0">Remove</a></td>
                     </tr>`

                invoicePreviewList.append(row)
            })

            calculateGrandTotal()

            $('.remove').on('click', async function () {
                let index= $(this).data('index')

                removeItem(index)
            })

        }

        function restock(item) {
            let prevStock = document.getElementById(`stock-${item.product_id}`).innerText
            document.getElementById(`stock-${item.product_id}`).innerText = Number(prevStock) + Number(item.qty)

            if (prevStock == 0) {
                document.getElementById(`productAdd-${item.product_id}`).innerHTML = `<button data-id="${item.product_id}" data-name="${item.product_name}" data-price="${item.price}" data-stock="${item.stock}" class="btn btn-outline-dark text-xxs px-2 py-1 addProduct btn-sm m-0">Add</button>`

                addProductOnClick()
            }
        }

        function removeItem(index) {
            let item = invoiceItemList[index]

            restock(item)

            invoiceItemList.splice(index, 1)

            showInvoiceItem()
        }

        function discountChange() {
            calculateGrandTotal()
        }

        function calculateGrandTotal() {
            let total = 0
            let discount = 0
            let discountPercentage = (parseFloat(document.getElementById('discountP').value))

            invoiceItemList.forEach((element) => {
                total += parseFloat(element['sale_price'])
            })

            if(discountPercentage > 0){
                discount = ((total * discountPercentage) / 100).toFixed(2)

                total = (total - ((total * discountPercentage) / 100)).toFixed(2)
            }

            let vat = ((total * 5) / 100).toFixed(2)
            let payable = (parseFloat(total) + parseFloat(vat)).toFixed(2)

            document.getElementById('total').innerText = total
            document.getElementById('payable').innerText = payable
            document.getElementById('vat').innerText = vat
            document.getElementById('discount').innerText = discount
        }

        function add() {
            let product_id = document.getElementById('PId').value
            let name = document.getElementById('PName').innerText
            let price = document.getElementById('PPrice').innerText
            let qty = document.getElementById('PQty').value
            let stock = document.getElementById(`stock-${product_id}`).innerText

            let sale_price = (parseFloat(price) * parseFloat(qty)).toFixed(2)

            if (qty < 1) {
                return errorToast("Sale Quantity Must Be Greater Than 0")
            }

            if (Number(qty) > Number(stock)) {
                return errorToast("Sale Quantity Exceeds Available Stock")
            }

            let item={
                product_name : name,
                product_id : product_id,
                price : price,
                qty : qty,
                sale_price : sale_price,
            }

            invoiceItemList.push(item)

            let prevStock = document.getElementById(`stock-${product_id}`).innerText
            let newStock = Number(prevStock) - Number(qty)

            document.getElementById(`stock-${product_id}`).innerText = newStock

            if (newStock === 0) {
                document.getElementById(`productAdd-${product_id}`).innerHTML = `<span class="text-danger">Out Of Stock</span>`
            }

            document.getElementById('PQty').value = 0
            $('#create-modal').modal('hide')

            showInvoiceItem()
        }

        function addModal(id, name, price) {
            document.getElementById('PId').value = id
            document.getElementById('PName').innerText = name
            document.getElementById('PPrice').innerText = price
            document.getElementById('PStock').innerText = document.getElementById(`stock-${id}`).innerText

            $('#create-modal').modal('show')
        }

        async function customerList() {
            let res = await axios.get("{{ route('customer.all') }}")

            let customerList = $("#customerList")
            let customerTable = $("#customerTable")

            customerTable.DataTable().destroy()
            customerList.empty()

            res.data['data'].forEach(function (element) {
                let row = `
                    <tr class="text-xs">
                        <td><i class="bi bi-person"></i> ${element['name']}</td>
                        <td><i class="bi bi-phone"></i> ${element['contact']}</td>
                        <td>
                            <button data-name="${element['name']}" data-contact="${element['contact']}" data-id="${element['id']}" class="btn btn-outline-dark addCustomer text-xxs px-2 py-1 btn-sm m-0">Add</button>
                        </td>
                    </tr>
                    `

                customerList.append(row)
            })

            $('.addCustomer').on('click', async function () {
                let name = $(this).data('name')
                let contact = $(this).data('contact')
                let id = $(this).data('id')

                $("#CName").text(name)
                $("#CContact").text(contact)
                $("#CId").text(id)
            })

            new DataTable('#customerTable', {
                order : [[0, 'desc']],
                scrollCollapse : false,
                info : false,
                lengthChange : false
            })
        }

        async function productList() {
            let res = await axios.get("{{ route('product.all') }}")

            let productList = $("#productList")
            let productTable = $("#productTable")

            productTable.DataTable().destroy()
            productList.empty()

            res.data['data'].forEach(function (element) {
                let addButton = `<td id="productAdd-${element['id']}"><span class="text-danger">Out Of Stock</span></td>`

                if (element['stock'] > 0) {
                    addButton = `<td id="productAdd-${element['id']}"><button data-id="${element['id']}" data-name="${element['name']}" data-price="${element['price']}" class="btn btn-outline-dark text-xxs px-2 py-1 addProduct btn-sm m-0">Add</button></td>`
                }

                let row = `
                    <tr class="text-xs">
                        <td> <img class="w-10" src="${element['img_url']}"/> ${element['name']} ($ ${element['price']})</td>
                        <td id="stock-${element['id']}">${element['stock']}</td>
                        ${addButton}
                    </tr>
                     `

                productList.append(row)
            })

            addProductOnClick()

            new DataTable('#productTable', {
                order : [[0, 'desc']],
                scrollCollapse : false,
                info : false,
                lengthChange : false
            })
        }

        async function addProductOnClick() {
            $('.addProduct').on('click', async function () {
                let id = $(this).data('id')
                let name = $(this).data('name')
                let price = $(this).data('price')

                addModal(id, name, price)
            })
        }

        async function createInvoice() {
            let total = document.getElementById('total').innerText
            let discount = document.getElementById('discount').innerText
            let vat = document.getElementById('vat').innerText
            let payable = document.getElementById('payable').innerText
            let customer_id = document.getElementById('CId').innerText

            if (customer_id.length === 0) {
                return errorToast("Please Select A Customer.")
            }

            if (invoiceItemList.length === 0) {
                return errorToast("At Least 1 Product Is Required.")
            }

            let data = {
                "total" : total,
                "discount" : discount,
                "vat" : vat,
                "payable" : payable,
                "customer_id" : customer_id,
                "products" : invoiceItemList
            }

            showLoader()
            let res = await axios.post("{{ route('invoice.create') }}", data)
            hideLoader()

            if (res.data['status'] === 'success') {
                successToast(res.data['message'])

                populateInvoiceModal(res.data['data'])

                document.getElementById('CName').innerText = ""
                document.getElementById('CContact').innerText = ""
                document.getElementById('CId').innerText = ""

                invoiceItemList = []

                showInvoiceItem()
            }
            else {
                errorToast(res.data['message'])
            }
        }

    </script>

    @include('components.invoice.invoice-delete')

    @include('components.invoice.invoice-details')

@endsection
