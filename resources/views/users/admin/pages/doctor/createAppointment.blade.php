@extends('users.admin.components.structure')

@section('title', 'createAppointment')
@section('header', 'Dashboard')

@section('content')
<div class="container">
    @include('modules.appointment.createAppointment')
</div>
@endsection

@section('scripts')
<script>
  // On DOM ready, verify token; if missing, redirect home
  document.addEventListener('DOMContentLoaded', function() {
    if (!sessionStorage.getItem('token')) {
      window.location.href = '/';
    }
  });
</script>
@endsection
