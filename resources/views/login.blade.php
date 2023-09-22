@extends('master')

@section('content')

<section class="vh-100">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
          <div class="card shadow-2-strong" style="border-radius: 1rem;">
            <div class="card-body p-5 text-center">
  
              <h3 class="mb-5">Sign in</h3>
  
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-outline mb-4">
                    <label class="form-label" style="font-weight: 700" for="email">Email</label>
                    <input type="text" id="email" name="email" class="form-control form-control-lg" />
                  </div>
      
                  <div class="form-outline mb-4">
                    <label class="form-label" style="font-weight: 700" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control form-control-lg" />
                  </div>
                  @if($errorLogin)
                    <p class="text-danger" id="error">Invalid Credentials</p>
                  @endif
                  <button id="submit" class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
              </form>
  
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('js')

<script>

// $(function() {
//     $("#submit").click(function(){
        
//         $("#submit").attr("disabled", true);

//         $.ajax({
//                     url: '/api/login',
//                     type: "POST",
//                     contentType: "application/json; charset=utf-8",
//                     data: JSON.stringify({
//                         email: $('#email').val(),
//                         password: $('#password').val()
                
//                     }),
//                     success: function(data){

//                         $("#submit").attr("disabled", false);

//                         if(data){
//                         //     localStorage.setItem(id, 1);
//                         //    window.location.replace("/reports");
                        
//                         }else{
//                             $('#error').css("display","block");
//                         }
//                     }
//                 });

//     });
    
// });

</script>

@endsection