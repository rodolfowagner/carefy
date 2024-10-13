<nav class="navbar navbar-expand-md bg-primary p-0 mb-3 sticky-top" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand d-block" href="{{ route('home') }}"><img src="{{ asset('files/img/logo-white.png') }}" alt="Carefy" width="" height="70"></a>
    <button class="navbar-toggler btn-close-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fs-4">
        <li class="nav-item mx-lg-2"><a class="nav-link text-white" href="{{ route('patients.index') }}"><i class="fa-solid fa-users"></i> Pacientes</a></li>
        <li class="nav-item mx-lg-2"><a class="nav-link text-white" href="{{ route('hospitalizations.index') }}"><i class="fa-solid fa-book-medical"></i> Internações</a></li>
        <li class="nav-item mx-lg-2"><a class="nav-link text-white" href="{{ route('upload.index') }}"><i class="fa-solid fa-upload"></i> Upload</a></li>
      </ul>
    </div>
  </div>
</nav>