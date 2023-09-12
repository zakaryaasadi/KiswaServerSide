@extends('master')

@section('content')

<div class="page-heading">
    <div class="flexbox mb-4">
        <div class="flexbox">
            <h1 class="page-title">Reports</h1>
        </div>
    </div>
</div>
<div class="page-content fade-in-up">
        <div class="row mb-4">

            <div class="col-lg-4 col-md-6">
                <div class="card mb-4">
                    <div class="card-body flexbox-b">
                        <div class="easypie mr-4" data-percent="100" data-bar-color="18C5A9" data-size="80" data-line-width="8">
                            <span class="easypie-data text-success" style="font-size: 32px"><i class="fa fa-heart"></i></span>
                        </div>
                        <div>
                            <h3 class="font-strong">Donation orders <i class="fa fa-star text-warning"></i></h3>
                            <div class="text-muted" id="donation_orders">Please wait...</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card mb-4">
                    <div class="card-body flexbox-b">
                        <div class="easypie mr-4" data-percent="100" data-bar-color="18C5A9" data-size="80" data-line-width="8">
                            <span class="easypie-data text-success" style="font-size:32px;"><i class="fa fa-dollar"></i></span>
                        </div>
                        <div>
                            <h3 class="font-strong">Sell orders <i class="fa fa-star text-warning"></i></h3>
                            <div class="text-muted" id="sell_orders">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card mb-4">
                    <div class="card-body flexbox-b">
                        <div class="easypie mr-4" data-percent="100" data-bar-color="18C5A9" data-size="80" data-line-width="8">
                            <span class="easypie-data text-success" style="font-size:32px;"><i class="fa fa-balance-scale"></i></span>
                        </div>
                        <div>
                            <h3 class="font-strong">Total weights <i class="fa fa-star text-warning"></i></h3>
                            <div class="text-muted" id="total_weights">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card mb-4">
                    <div class="card-body flexbox-b">
                        <div class="easypie mr-4" data-percent="100" data-bar-color="18C5A9" data-size="80" data-line-width="8">
                            <span class="easypie-data text-success" style="font-size:32px;"><i class="fa fa-balance-scale"></i></span>
                        </div>
                        <div>
                            <h3 class="font-strong">Donation weights <i class="fa fa-star text-warning"></i></h3>
                            <div class="text-muted" id="donate_weights">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card mb-4">
                    <div class="card-body flexbox-b">
                        <div class="easypie mr-4" data-percent="100" data-bar-color="18C5A9" data-size="80" data-line-width="8">
                            <span class="easypie-data text-success" style="font-size:32px;"><i class="fa fa-balance-scale"></i></span>
                        </div>
                        <div>
                            <h3 class="font-strong">Sell weights <i class="fa fa-star text-warning"></i></h3>
                            <div class="text-muted" id="sell_weights">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>
            
    </div>
<div class="ibox">
    <div class="ibox-body">
        <h5 class="font-strong mb-4">Report LIST</h5>

        <div class="row mb-4">
            <div class="col-md-2">
                <label class="mb-0 mr-2">Order type:</label>
                <select class="form-control" id="order-type-filter" title="Please select" data-style="btn-solid" data-width="150px">
                    <option value="">All</option>
                    <option value="Sell">Sell</option>
                    <option value="Donate">Donate</option>
                </select>
            </div>

            <div class="col-md-8">
            </div>

            <div class="col-md-2">
                <div class="input-group-icon input-group-icon-left mr-3">
                    <span class="input-icon input-icon-right font-16"><i class="fa fa-search"></i></span>
                    <input class="form-control form-control-rounded form-control-solid" id="key-search" type="text" placeholder="Search ...">
                </div>
            </div>
        </div>

                

        

        <div class="row">
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text" >Start date</span>
                    <input type="date" class="form-control" id="startdate">
                  </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">End date</span>
                    <input type="date" class="form-control" id="enddate">
                  </div>
            </div>
        </div>
        <div class="table-responsive row">
            <table class="table table-hover" id="table">
                <thead class="thead-default thead-lg">
                    <tr>
                        <th class="no-sort">Job ID</th>
                        <th class="no-sort">Customer name</th>
                        <th class="no-sort">Customer phone</th>
                        <th class="no-sort">Agent name</th>
                        <th class="no-sort">Address</th>
                        <th class="no-sort">Weights</th>
                        <th class="no-sort">Order type</th>
                        <th class="no-sort">Charity</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')

<script src="{{asset('vendors/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js')}}"></script>


<script>

    $(function() {

        var today = new Date();
        var yesterday = new Date();

        yesterday.setDate(today.getDate() - 1);


        $('#startdate').val(yesterday.toJSON().slice(0,10));
        $('#enddate').val(today.toJSON().slice(0,10));


        GetTotalReport();
            
        var table = $('#table').DataTable({
            pageLength: 100,
            responsive: true,
            fixedHeader: true,
            processing: true,
            serverSide: true,
            ajax: {
                    "url": '/api/report_table',
                    "type": "POST",
                    data: function (d) {
                        d.start_date = $('#startdate').val();
                        d.end_date = $('#enddate').val();
                
                    },
                },
            dom: 'rtip',
            columnDefs: [{
                targets: 'no-sort',
                orderable: false,
            }],

            columns: [
                {data: 'job_id'},
                {data : 'job_pickup_name'},
                {data : 'job_pickup_phone'},
                {data : 'fleet_name'},
                {data : 'job_pickup_address'},
                {data : 'weight', 
                    render: function (data) {
                        return data + ' KG';
                    }
                },
                {data : 'order_type'},
                {data : 'charity'},
            ],

            select: true,
        });
        
        $('#startdate, #enddate').change(function () {
            table.draw();
            GetTotalReport();
        });

        $('#key-search').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#order-type-filter').on('change', function() {
            table.column(6).search($(this).val()).draw();
        });




        function GetTotalReport(){
            $('#donation_orders', '#sell_orders', '#total_weights', '#sell_weights', '#donate_weights').html("Please wait...");

            $.ajax({
                    url: '/api/total_report',
                    type: "POST",
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify({
                        start_date: $('#startdate').val(),
                        end_date: $('#enddate').val()
                
                    }),
                    success: function(data){
                        var object = JSON.parse(data);
                        $('#donation_orders').html(object.number_of_donates);
                        $('#sell_orders').html(object.number_of_sells);
                        $('#total_weights').html(object.donate_weights + object.sell_weights + " KG");
                        $('#sell_weights').html(object.sell_weights + " KG");
                        $('#donate_weights').html(object.donate_weights + " KG");

                    }
                });
        }
        
        
    });

  
    </script>

@endsection