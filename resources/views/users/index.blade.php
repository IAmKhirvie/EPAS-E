@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <h1>Users</h1>
    <p>Manage user accounts and approvals.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                    <td>
                        @if($user->stat)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-warning">Pending Approval</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $user->id)}}" class="btn btn-sm btn-primary">Edit</a>
                        
                        @if(!$user->stat)
                            <form action="{{ route('users.approve', $user->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button class="btn btn-sm btn-success" onclick="return confirm('Approve this user?')">Approve</button>
                            </form>
                        @endif
                        
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to DELETE this USER?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection