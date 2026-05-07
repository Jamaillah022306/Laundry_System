@extends('layouts.cashier')

@section('title', 'Register Customer')

@section('content')

<h1 class="page-title">Register Customer</h1>

<div class="form-wrapper">
    <form method="POST" action="{{ route('cashier.customers.store') }}">
        @csrf

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name') }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username') }}" required>
            @error('username') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Email</span></label>
            <input type="email" name="email" class="form-control"
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions" style="display:flex; justify-content:space-between; align-items:center; margin-top:4rem;">
            <a href="{{ route('cashier.customers.index') }}" class="btn-secondary">← Back</a>
            <button type="submit" class="btn-primary">Register</button>
        </div>
    </form>
</div>

@endsection