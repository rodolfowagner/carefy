<div class="container-fluid bg-primary">
  <div class="row text-center">
    <div class="col">
      <a class="navbar-brand" href="/"><img src="{{ asset('files/img/logo-white.png') }}" alt="Carefy" width="" height="70"></a>
    </div>
  </div>
</div>
<nav class="navbar navbar-expand-lg bg-primary mb-3" data-bs-theme="dark">
  <div class="container-fluid">
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fs-3">
        <li class="nav-item mx-2"><a class="nav-link text-white" href="#"><i class="fa-solid fa-hospital-user"></i> Pacientes</a></li>
        <li class="nav-item mx-2"><a class="nav-link text-white" href="#"><i class="fa-solid fa-book-medical"></i> Internamentos</a></li>
        <li class="nav-item mx-2"><a class="nav-link text-white" href="{{ route('upload.index') }}"><i class="fa-solid fa-upload"></i> Upload</a></li>
      </ul>
    </div>
  </div>
</nav>