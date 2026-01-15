@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Page Heading --}}
    <h1 class="h3 mb-4 text-gray-800">Profile</h1>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- ================== UPDATE PROFILE ================== --}}
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Profile</h6>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ $user->name }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ $user->email }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $user->role }}"
                                   readonly>
                        </div>

                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================== UPDATE PASSWORD ================== --}}
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Change Password</h6>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label>Current Password</label>
                            <div class="input-group">
                                <input type="password"
                                    name="current_password"
                                    id="current_password"
                                    class="form-control"
                                    required>

                                <div class="input-group-append">
                                    <span class="input-group-text cursor-pointer"
                                        onclick="togglePassword('current_password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <div class="input-group">
                                <input type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    required>

                                <div class="input-group-append">
                                    <span class="input-group-text"
                                        onclick="togglePassword('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <div class="input-group">
                                <input type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control"
                                    required>

                                <div class="input-group-append">
                                    <span class="input-group-text"
                                        onclick="togglePassword('password_confirmation', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
function togglePassword(inputId, el) {
    const input = document.getElementById(inputId);
    const icon  = el.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@endsection

