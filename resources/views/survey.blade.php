@extends('master')

@section('content')

<div class="page-heading">
    <div class="flexbox mb-4">
        <div class="flexbox">
            <h1 class="page-title">Survey</h1>
        </div>
        <a href="{{url('/rating')}}" class="btn btn-primary btn-air mt-5 mr-5">Ratings</a>
    </div>
</div>
<div class="page-content fade-in-up">
<div class="ibox">
    <div class="ibox-body">
        <h5 class="font-strong mb-4">SURVEY LIST</h5>
        <div class="flexbox mb-4">
            <div class="flexbox">
                <label class="mb-0 mr-2">Country:</label>
                <select class="form-control" id="type-filter" title="Please select" data-style="btn-solid" data-width="150px">
                    <option value="">All</option>
                    @foreach ($countries as $item)
                        <option value="{{$item->country_name}}">{{$item->country_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group-icon input-group-icon-left mr-3">
                <span class="input-icon input-icon-right font-16"><i class="fa fa-search"></i></span>
                <input class="form-control form-control-rounded form-control-solid" id="key-search" type="text" placeholder="Search ...">
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
                        <th class="no-sort">Country</th>
                        <th class="no-sort">Address</th>
                        <th class="no-sort">Location</th>
                        <th class="no-sort">Is replied</th>
                        <th class="no-sort">Is received</th>
                        <th class="no-sort">Value</th>
                        <th class="no-sort">Is getton Coupon</th>
                        <th class="no-sort">Service rating</th>
                        <th class="no-sort">Agent rating</th>

                        <th class="no-sort">Acknowledged</th>
                        <th class="no-sort">Started</th>
                        <th class="no-sort">Arrived</th>
                        <th class="no-sort">Completed</th>
                        <th class="no-sort">Total distance</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-messaging.js"></script>

<script>

    $(function() {
        var utc = new Date().toJSON().slice(0,10);
        $('#startdate').val(utc);
        $('#enddate').val(utc);
            
        var table = $('#table').DataTable({
            pageLength: 100,
            responsive: true,
            fixedHeader: true,
            processing: true,
            serverSide: true,
            ajax: {
                    "url": '/api/survey-table',
                    "type": "POST",
                    data: function (d) {
                        d.startdate = $('#startdate').val();
                        d.enddate = $('#enddate').val();
                
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
                {data : 'country'},
                {data : 'job_pickup_address'},
                {data : 'location', 
                    render: function (data) {
                        return '<a target="_blank" href="' + data + '">' + data+ '</a>';
                    }
                },
                {data : 'is_reply', 
                    render: function (data) {
                        return data == 1 ? '<i class="fa fa-check-circle text-success font-20"></i>' :  (data == 0 ? '<i class="fa fa-times text-danger font-20"></i>' : '-');
                    }
                },
                {data : 'is_receipt', 
                    render: function (data) {
                        return data == 1 ? '<i class="fa fa-check-circle text-success font-20"></i>' : (data == 0 ? '<i class="fa fa-times text-danger font-20"></i>' : '-');
                    }
                },
                {data : 'price'},
                {data : 'is_coupon', 
                    render: function (data) {
                        return data == 1 ? '<i class="fa fa-check-circle text-success font-20"></i>' : (data == 0 ? '<i class="fa fa-times text-danger font-20"></i>' : '-');
                    }
                },
                {data : 'service_rating', 
                    render: function (data) {
                       return '<span>' + (data == 0 ?  '-' : Math.round(data * 100) / 100) + ' <i class="fa fa-star text-warning"></i></span>';
                    }
                },
                {data : 'fleet_rating', 
                    render: function (data) {
                       return '<span>' + (data == 0 ?  '-' : Math.round(data * 100) / 100) + ' <i class="fa fa-star text-warning"></i></span>';
                    }
                },
                {data : 'acknowledged_datetime'},
                {data : 'started_datetime'},
                {data : 'arrived_datetime'},
                {data : 'completed_datetime'},
                {data : 'total_distance_travelled'},

            ],

            select: true,
        });
        
        $('#startdate, #enddate').change(function () {
            table.draw();
        });

        $('#key-search').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#type-filter').on('change', function() {
            table.column(4).search($(this).val()).draw();
        });
        
        
    });



    $(document).ready(function(){
        Notification.requestPermission();
        initFirebaseMessagingRegistration();
        console.log(Notification.permission);
    });

    var firebaseConfig = {
        apiKey: "{{env('NOTIFICATION_API_API')}}",
        authDomain: "{{env('NOTIFICATION_Auth_DOMAIN')}}",
        projectId: "{{env('NOTIFICATION_PROJECT_ID')}}",
        storageBucket: "{{env('NOTIFICATION_STORAGE_BUCKET')}}",
        messagingSenderId: "{{env('NOTIFICATION_MESSAGING_SENDER_ID')}}",
        appId: "{{env('NOTIFICATION_APP_ID')}}"
    };
    
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    
    function initFirebaseMessagingRegistration() {
            messaging
                .requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function(token) {
                    subscribeTokenToTopic(token, "survey")
                    console.log(token);
                }).catch(function (err) {
                    console.log('Catch '+ err);
                });
     }  
      
    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: 'https://cdn-icons-png.flaticon.com/512/1486/1486464.png',
        };
        new Notification(noteTitle, noteOptions);
        
        location.reload(true);
    });

    function subscribeTokenToTopic(token, topic) {
            fetch('https://iid.googleapis.com/iid/v1/'+token+'/rel/topics/'+topic, {
                method: 'POST',
                headers: new Headers({
                'Authorization': "key={{env('NOTIFICATION_SERVER_API')}}"
                })
            }).then(response => {
                if (response.status < 200 || response.status >= 400) {
                    throw 'Error subscribing to topic: '+response.status + ' - ' + response.text();
                }
                console.log('Subscribed to "'+topic+'"');
            }).catch(error => {
                console.error(error);
            })
    }
  
    </script>

@endsection