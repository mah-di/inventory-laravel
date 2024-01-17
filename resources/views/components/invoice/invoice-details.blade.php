<!-- Modal -->
<div class="modal animated zoomIn" id="details-modal" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Invoice</h1>
                <button onclick="deleteInvoice()" id="DeleteInv" class="btn btn-danger">Delete Invoice</button>
            </div>
            <div id="invoice" class="modal-body p-3">
                <div class="container-fluid">
                    <br/>
                        <p class="text-sm">Invoice ID: #<span id="invId"></span></p>
                        <br/>
                        <div class="row">
                            <div class="col-8">
                                <span class="text-bold text-dark">BILLED TO </span>
                                <p class="text-xs mx-0 my-1">Name:  <span id="CusName"></span> </p>
                                <p class="text-xs mx-0 my-1">Email:  <span id="CusEmail"></span></p>
                                <p class="text-xs mx-0 my-1">User ID:  <span id="CusId"></span> </p>
                            </div>
                            <div class="col-4">
                                <img class="w-40" src="{{"images/logo.png"}}">
                                <p class="text-bold mx-0 my-1 text-dark">Invoice  </p>
                                <p class="text-xs mx-0 my-1">Date: {{ date('Y-m-d') }} </p>
                            </div>
                        </div>
                        <hr class="mx-0 my-2 p-0 bg-secondary"/>
                        <div class="row">
                            <div class="col-12">
                                <table class="table w-100" id="invoiceTable">
                                    <thead class="w-100">
                                    <tr class="text-xs text-bold">
                                        <td>Name</td>
                                        <td>Qty</td>
                                        <td>Total</td>
                                    </tr>
                                    </thead>
                                    <tbody  class="w-100" id="invoiceList">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="mx-0 my-2 p-0 bg-secondary"/>
                        <div class="row">
                            <div class="col-12">
                                <p class="text-bold text-xs my-1 text-dark"> TOTAL: <span id="invTotal"></span></p>
                                <p class="text-bold text-xs my-1 text-dark"> Discount: <span id="invDiscount"></span></p>
                                <p class="text-bold text-xs my-1 text-dark"> VAT(5%): <span id="invVat"></span></p>
                                <hr class="mx-0 my-2 p-0 bg-secondary"/>
                                <p class="text-bold text-xs my-2 text-dark"> PAYABLE: <span id="invPayable"></span></p>
                            </div>

                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="details-modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal">Close</button>
                <button onclick="printPage()" class="btn bg-gradient-success">Print</button>
            </div>
        </div>
    </div>
</div>


<script>

    async function invoiceDetails(id) {
        showLoader()

        let res = await axios.get(`{{ url('/api/invoice') }}/${id}`)

        hideLoader()

        if (res.data['status'] === 'success') {
            populateInvoiceModal(res.data['data'])
        }
        else {
            errorToast(res.data['message'])
        }
    }

    function populateInvoiceModal(data) {
        document.getElementById('invId').innerText = data['id']
        document.getElementById('CusName').innerText = data['customer']['name']
        document.getElementById('CusId').innerText = data['customer']['user_id']
        document.getElementById('CusEmail').innerText = data['customer']['email']
        document.getElementById('invTotal').innerText = data['total'] + data['discount']
        document.getElementById('invDiscount').innerText = data['discount']
        document.getElementById('invVat').innerText = data['vat']
        document.getElementById('invPayable').innerText = data['payable']

        let invoiceList = $('#invoiceList')
        invoiceList.empty()

        soldItemList = []

        data['invoice_products'].forEach(function (element) {
            let row = `
                    <tr class="text-xs">
                        <td>${element['product']['name']}</td>
                        <td>${element['qty']}</td>
                        <td>${element['sale_price']}</td>
                    </tr>
                `

                invoiceList.append(row)

                soldItemList.push({
                    product_id : element['product_id'],
                    product_name : element['product']['name'],
                    price : element['product']['price'],
                    qty : element['qty']
                })
        })

        $("#details-modal").modal('show')
    }

    function deleteInvoice() {
        document.getElementById('deleteID').value = document.getElementById('invId').innerText

        document.querySelector('#details-modal').style.zIndex = 1040

        $('#delete-modal').modal('show')
    }

    function printPage() {
        let printContents = document.getElementById('invoice').innerHTML
        let pdfView = window.open("", "_blank")

        pdfView.document.write(document)

        pdfView.document.querySelector('head').innerHTML = document.querySelector('head').innerHTML
        pdfView.document.body.innerHTML = printContents

        let script = pdfView.document.createElement('script')

        script.innerHTML = `window.addEventListener("load", () => window.print())`
        pdfView.document.body.append(script)

        pdfView.document.close()

        $("#details-modal").modal('hide')
    }

</script>
