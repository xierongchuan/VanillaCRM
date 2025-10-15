@extends('layouts.main')

@section('title', 'User Update')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h2 class="text-center mt-4">Update User in <b>{{ $company->name }}</b></h2>
            <form action="{{ route('company.user.modify', compact('company', 'user')) }}" method="post"
                class="bg-body-tertiary rounded p-3">
                @csrf

                {{-- Stage 5: Vue Component for Department/Post Selector with dynamic loading --}}
                <department-post-selector
                    company-id="{{ $company->id }}"
                    :departments='@json($departments)'
                    selected-department-id="{{ $user->dep_id }}"
                    selected-post-id="{{ $user->post_id }}"
                    :show-post="true"
                    department-name="department"
                    post-name="post">
                </department-post-selector>

                <div class="mb-3">
                    <label class="form-label fw-bold">Role:</label>
                    <div class="btn-group d-flex flex-wrap gap-2" role="group" aria-label="Role selection">
                        <input type="radio" class="btn-check" name="in_bot_role" id="role_user" value="user"
                            autocomplete="off" required {{ $user->in_bot_role == 'user' ? 'checked' : '' }}>
                        <label class="btn btn-lg btn-outline-primary rounded-3 flex-fill py-3" for="role_user">
                            <i class="bi-person me-2"></i>User
                        </label>

                        <input type="radio" class="btn-check" name="in_bot_role" id="role_cashier" value="cashier"
                            autocomplete="off" {{ $user->in_bot_role == 'cashier' ? 'checked' : '' }}>
                        <label class="btn btn-lg btn-outline-secondary rounded-3 flex-fill py-3" for="role_cashier">
                            <i class="bi-calculator me-2"></i>Cashier
                        </label>

                        <input type="radio" class="btn-check" name="in_bot_role" id="role_director" value="director"
                            autocomplete="off" {{ $user->in_bot_role == 'director' ? 'checked' : '' }}>
                        <label class="btn btn-lg btn-outline-success rounded-3 flex-fill py-3" for="role_director">
                            <i class="bi-graph-up me-2"></i>Director
                        </label>
                    </div>
                    <div class="form-text text-muted mt-2">Select appropriate role for access permissions</div>
                </div>

                <div class="form-group mb-2">
                    <label for="full_name">Name:</label>
                    <input type="text" class="form-control" id="full_name" name="full_name"
                        value="{{ $user->full_name }}" required>
                </div>
                <div class="form-group mb-2">
                    <label for="phone_number">Phone:</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                        value="{{ $user->phone_number }}" required>
                </div>

                <div class="form-group mb-2">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" autocomplete="off" name="password"
                        placeholder="Write the password here if you want to change it">
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection
