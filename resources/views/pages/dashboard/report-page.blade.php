@extends('layout.sidenav-layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Sales Report</h4>
                        <label class="form-label mt-2">Date From</label>
                        <input id="fromDate" type="date" class="form-control"/>
                        <label class="form-label mt-2">Date To</label>
                        <input id="toDate" type="date" class="form-control"/>
                        <button onclick="salesReport()" class="btn mt-3 bg-gradient-primary">Download</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script>

    async function salesReport() {
        let fromDate = document.getElementById('fromDate').value
        let toDate = document.getElementById('toDate').value

        if (fromDate.length === 0 || toDate.length === 0) {
            return errorToast("Date Range Is Required.")
        }

        showLoader()
        let res = await axios.post("{{ route('salesReport') }}", {
            fromDate : fromDate,
            toDate : toDate
        }, {
            responseType : "blob"
        })
        hideLoader()

        if (res.data['status'] === 'error') {
            return errorToast(res.data['message'])
        }

        const blob = new Blob([res.data], {
            type : "application/pdf"
        })
        const url = window.URL.createObjectURL(new Blob([res.data]))
        let link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `sales-report-${fromDate}-${toDate}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
    }

</script>
