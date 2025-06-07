<!-- resources/views/home.blade.php -->
@extends('sb-admin-2.layouts.app')

@section('content')

<style>
li {
    color: black;
}
</style>

<!-- Content Row -->
<div class="jumbotron">
    <h1 style="color:black">Status Order</h1>
    @if($data->value == 'open') 
        <form method="POST" action="/CloseOrder" style="display: inline;">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $data->id }}">
            <button type="submit" name="proses" value="closed" class="btn btn-success mb-2">OPEN ORDER</button>
        </form> 
    @else
        <form method="POST" action="/CloseOrder" style="display: inline;">
            @csrf    
            <input type="hidden" name="menu_id" value="{{ $data->id }}">
            <button type="submit" name="proses" value="open" class="btn btn-warning mb-2">CLOSE ORDER</button>
        </form> 
    @endif
</div>

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif
@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif

@endsection