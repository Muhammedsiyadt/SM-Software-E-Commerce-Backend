@extends('admin.dashboard.layouts.master')

@section('styles')
    <style>

    </style>
@endsection

@section('admin-content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">

            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Customers</h4>
                    <p class="mb-0">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla, temporibus.</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Customers</a></li>
                </ol>
            </div>


        </div>

        @if (Session::has('success'))
            <div class="alert alert-success text-white  alert-dismissible fade show" role="alert">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger text-white  alert-dismissible fade show" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif




        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"> Customers</h4>
                        <button id="downloadButton" class="btn btn-sm btn-info text-white">Download Excel</button>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table header-border table-responsive-sm product-datatable w-100"
                                    id="product-datatable">
                                    <thead>
                                        <tr>
                                            <th class="font-weight-bold text-dark">#</th>
                                            {{-- <th class="font-weight-bold text-dark">Image</th> --}}
                                            <th class="font-weight-bold text-dark">Name</th>
                                            <th class="font-weight-bold text-dark">Email</th>
                                            <th class="font-weight-bold text-dark">Phone</th>

                                            <th class="font-weight-bold text-dark">No. of Order</th>

                                            <th class="font-weight-bold text-dark exclude">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>




                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        function truncateText(text, maxLength) {
            if (text.length > maxLength) {
                return text.substring(0, maxLength) + "...";
            }
            return text;
        }

        var datad = [];


        $.ajax({
            url: "{{ route('admin.customers.index') }}",
            method: "GET",
            dataType: "json",
            success: function(response) {
                // Handle the successful response here
                datad = response.data;
                console.log(response.data);
            },
            error: function(xhr, status, error) {
                // Handle the error here
                console.log("Error:", error);
            }
        });

        const customFields = [{
                label: "#",
                value: "id"
            },

            {
                label: "Name",
                value: "name"
            },
            {
                label: "Email",
                value: "email"
            },
            {
                label: "Phone",
                value: "phone"
            },
            {
                label: "Order",
                value: "orders"
            },

        ];

        function generateExcel() {
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet(datad.map(item => {
                const row = {};
                customFields.forEach(field => {
                    if (typeof field.value === "function") {
                        row[field.label] = field.value(item);
                    } else {
                        row[field.label] = item[field.value];
                    }
                });
                return row;
            }));

            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

            // Save the workbook as an Excel file
            XLSX.writeFile(wb, '{Customer}.xlsx');
        }



        // Add click event listener to the button
        const downloadButton = document.getElementById('downloadButton');
        downloadButton.addEventListener('click', generateExcel);


        $(function() {

            var table = $('.product-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.customers.index') }}",
                columnDefs: [
                    {
                        targets: 0,
                        render: function(data, type, row, meta) {
                           return meta.row+1;
                        }
                    },
                    {
                        targets: 1,
                        render: function(data, type, row, meta) {
                            return truncateText(data, 50)
                        }
                    },
                    {
                        targets: 4,
                        render: function(data, type, row, meta) {
                            return row.orders.length
                        }
                    },

                    {
                        targets: 5,
                        render: function(data, type, row, meta) {
                            var editUrl = "{{ route('admin.customers.edit', ':id') }}"
                                .replace(':id', row.id);
                            var deleteUrl = "{{ route('admin.customers.destroy', ':id') }}"
                                .replace(':id', row.id);

                            return `
                        <a class="" href="${editUrl}">
                            <i class="fa fa-edit text-warning btn"></i>
                        </a>
                        <a class="" href="${deleteUrl}" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this Condition?')) { document.getElementById('delete-form-${row.id}').submit(); }">
                            <i class="fa fa-trash text-danger btn"></i>
                        </a>
                        <form id="delete-form-${row.id}" action="${deleteUrl}" method="POST" style="display: none;">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>`;
                        }
                    },

                    {
                        targets:0,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }



                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },

                    {
                        data: 'phone',
                        name: 'phone'
                    },

                    {
                        data: 'orders',
                        name: 'orders'
                    },

                    {
                        data: 'id',
                        name: 'id',
                        orderable: false
                    },



                ],
            });

        });
    </script>
@endsection
