<!-- <style type="text/css">
  .logo-main img {
    max-width: 600px;
}
</style>
 -->

<x-guest-layout>
  <!-- End Navbar -->
  <div class="wrapper wrapper-full-page">
    <div class="text-center logo-main">
      <img src="{{ url('/').'/'.asset('assets/img/header_bg1.jpeg') }}" width="700" class="rounded" alt="...">
    </div>
    <div class="text-center logo-main" style="margin-top: 30px;margin-bottom: 40px;">
      <img src="{{ url('/').'/'.asset('assets/img/brand_logo.jpg') }}" class="rounded" alt="...">
    </div>
    <div class="page-header login-page">
      <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
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
              <div class="card card-login">
                <div class="card-header card-header-theme text-center blue-login-button">
                  <h4 class="card-title color-white">Login</h4>
                </div>
                <div class="card-body ">
                  <!-- <p class="card-description text-center">Or Be Classical</p> -->
                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">email</i>
                        </span>
                      </div>
                      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                      @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                  </span>
                  <span class="bmd-form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">lock_outline</i>
                        </span>
                      </div>
                      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required autocomplete="current-password">
                      @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                  </span>
                </div>
                <div class="card-footer justify-content-center pb-4">
                  <button class="btn btn-theme btn-link btn-lg blue-login-button color-white" onClick="this.form.submit(); this.disabled=true; this.value='Sending…'; " style="color:white !important;">Login</button>
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
      <footer class="footer">
        <div class="container">

        </div>
      </footer>
    </div>
  </div>
</x-guest-layout>