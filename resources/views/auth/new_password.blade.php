@extends('layout.index_auth')
@section('title',$title)
@section('content')
<div class="card-body login-card-body">
  @if ($errors->has('error'))
    <div class="alert alert-{{$errors->first('error')?'danger':'success'}} alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h5><i class="icon fas fa-{{$errors->first('error')?'ban':'check'}}"></i> {{$errors->first('error')?'Error':'Sucess'}}</h5>
     {{$errors->first('message') }}
    </div>
    @endif
    <p class="login-box-msg">{{$title}}</p>

    <form action="{{route('newpassword')}}" method="post">
      @csrf
      <input type="hidden" name="validator" value="{{$email}}">
      <input type="hidden" name="from" value="{{$from}}">
      <div class="form-group">
        <label for="inputPassword">Password</label>
        <div class="input-group">
          <input type="password" id="password" name="password" class="form-control {{$errors->has('password')?'is-invalid':''}}" placeholder="Password" aria-describedby="inputPassword-error" aria-invalid="true">
          <div class="input-group-append">
            <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
              <i class="fas fa-eye" id="password-icon"></i>
            </span>
          </div>
        </div>
        @if($errors->has('password'))
            <span id="inputPassword-error" class="error invalid-feedback">{{ $errors->first('password') }}</span>
        @endif
      </div>
      <div class="form-group">
        <label for="retypePassword">Retype Password</label>
        <div class="input-group">
          <input type="password" id="retype_password" name="retype_password" class="form-control {{$errors->has('retype_password')?'is-invalid':''}}" placeholder="Password" aria-describedby="inputPassword-error" aria-invalid="true">
          <div class="input-group-append">
            <span class="input-group-text cursor-pointer" onclick="togglePassword('retype_password')">
              <i class="fas fa-eye" id="retype_password-icon"></i>
            </span>
          </div>
        </div>
        @if($errors->has('retype_password'))
            <span id="inputPassword-error" class="error invalid-feedback">{{ $errors->first('retype_password') }}</span>
        @endif
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-4">
          <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <script>
      function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (passwordField.type === 'password') {
          passwordField.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          passwordField.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }
    </script>

    {{-- <div class="social-auth-links text-center mb-3">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-primary">
        <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
      </a>
      <a href="#" class="btn btn-block btn-danger">
        <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
      </a>
    </div> --}}
    <!-- /.social-auth-links -->

    <p class="mb-1">
      <a href="forgot-password.html">I forgot my password</a>
    </p>
    {{-- <p class="mb-0">
      <a href="register.html" class="text-center">Register a new membership</a>
    </p> --}}
  </div>
@endsection