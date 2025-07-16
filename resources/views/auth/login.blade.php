<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>

  <style>
    .login_cont {
      font-family: 'Roboto', sans-serif;
      background-color: #E0F7FA;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .main-container {
      display: flex;
      width: 80%;
      max-width: 1000px;
      background: transparent;
      border-radius: 10px;
      overflow: hidden;
      align-items: center;
    }

    .left-container {
      background: linear-gradient(135deg, #00BCD4, #673AB7);
      color: white;
      padding: 80px 50px;
      width: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      left: 20px;
      height: 500px;
      border-radius: 10px;
    }

    .left-container .content {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 30px;
      flex-direction: column;

    }

    .right-container {
      padding: 50px 50px;
      width: 50%;
      display: flex;
      justify-content: center;
      /* align-items: center; */
      background-color: #FFF;
      border-radius: 10px;
      height: 600px
    }

    .right-container .german-logo {
      width: 100%;
      max-width: 250px;
    }

    .german-logo-cont {
      text-align: center;
    }

    .right-container .form-cont {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .login-box {
      width: 100%;
      max-width: 400px;
      display: flex;
      flex-direction: column;
      gap: 30px;
      /* align-items: center; */
    }

    .login-box h2 {
      font-size: 28px;
      margin-bottom: 15px;
      color: #7d94c6;

    }



    .form-group {
      margin-bottom: 20px;
    }

    .form-group input {
      width: 100%;
      padding: 15px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      position: relative;
      background-color: rgb(224 224 224 / 58%);
    }

    .form-group input:focus {
      outline: none;
      border-left: 2px solid #00BCD4;
      box-shadow: 0 0 5px rgba(0, 188, 212, 0.3);
      background-color: #FFF;

    }

    .password-eye-cont {
      position: relative;
    }

    .password-eye {
      position: absolute;
      top: 15px;
      right: 13px;
      cursor: pointer;
    }

    button.log-in-btn {
      width: 100%;
      padding: 15px;
      background: linear-gradient(270deg, #06BCE7 0%, #7B7AD3 100%);
      border: none;
      border-radius: 5px;
      color: white;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s ease;

    }



    /* Responsive Design */
    @media (max-width: 768px) {
      .main-container {
        flex-direction: column;
      }

      .left-container,
      .right-container {
        width: 100%;
      }

      .left-container {
        padding: 30px;
      }

      .right-container {
        padding: 30px;
      }

      .login-box {
        max-width: 100%;
      }
    }

    @media (max-width: 1024px) {
      .left-container {

        left: 0px;

      }

      .main-container {
        width: 90%;
      }

      .left-container,
      .right-container {
        padding: 40px;
      }
    }
  </style>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>
  <div class="login_cont">
    <div class="main-container">
      <div class="left-container">
        <div class="content">
          <img src="{{asset('assets/img/brand_logo.png') }}" alt="" width="100%">

        </div>
      </div>
      <div class="right-container">
        <div class="login-box">
          <div class="german-logo-cont">
            <img src="{{ asset('assets/img/german_logo.png') }}" alt="" class="german-logo">
          </div>

          <h2>Hello!</h2>

          @if(session()->has('error'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session()->get('error') }}
            </span>
          </div>
          @endif
          @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
          </div>
          @endif
          @if($errors->any())
          <div>
            <ul class="alert alert-danger">
              @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-cont">

              <div class=" ">
                <!-- <p class="card-description text-center">Or Be Classical</p> -->
                <span class="bmd-form-group">
                  <div class="form-group">
                    <input id="email" type="email" class=" @error('email') is-invalid @enderror" name="email" placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </span>
                <span class="bmd-form-group">
                  <div class="form-group ">
                    <div class="password-eye-cont">
                      <input id="password" type="password" class=" @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required autocomplete="current-password">
                      <span class="material-symbols-outlined password-eye" title="Show" id="pass-seen">visibility_off</span>
                    </div>

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </span>
              </div>
              <div class="">
                <button class="log-in-btn" onClick="this.form.submit(); this.disabled=true; this.value='Sending…'; " style="color:white !important;">Login</button>
              </div>
              @if (Route::has('password.request'))
              <!-- <a class="btn btn-link" href="{{ route('password.request') }}">
              {{ __('Forgot Your Password?') }}
              </a> -->
              @endif
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $("#pass-seen").on('click', function() {
      let currentText = $(this).text();
      let passwordField = $("#password");

      if (currentText === 'visibility') {
        $(this).text('visibility_off');
        $(this).attr('title','Show');
        passwordField.attr("type", "password");
      } else {
        $(this).text('visibility');
        $(this).attr('title','Hide');
        passwordField.attr("type", "text");
      }
    });
  </script>
</body>

</html>