@extends('layouts.app')

@section('title','Services - Mengobrol Mengobati')
@section('body-class','services-page')

@vite('resources/css/services.css')

@section('content')
<section class="services-section">
  <div class="container">

    {{-- TITLE --}}
    <h2 class="services-title">Our Services</h2>
    <p class="services-subtitle">Choose the service that suits your needs to get the best support.</p>

    {{-- SERVICES CARDS --}}
    <div class="row justify-content-center g-5 services-row">

      {{-- Psikiater --}}
      <div class="col-md-4">
        <div class="service-card">
          <img src="{{ asset('images/psikiater.png') }}" alt="Psikiater">
          <h4>Psikiater</h4>
          <p>Medical consultation with a mental health specialist.</p>
          <a href="{{ route('psikiater.index') }}">Book Now</a>
        </div>
      </div>

      {{-- Therapy Group --}}
      <div class="col-md-4">
        <div class="service-card">
          <img src="{{ asset('images/chat.png') }}" alt="Therapy Group">
          <h4>Therapy Group</h4>
          <p>Sharing stories and support in a safe group session.</p>
          <a href="{{ route('therapy.index') }}">Book Now</a>
        </div>
      </div>

      {{-- Psikolog --}}
      <div class="col-md-4">
        <div class="service-card">
          <img src="{{ asset('images/psikiater.png') }}" alt="Psikolog">
          <h4>Psikolog</h4>
          <p>Professional counseling to help understand yourself.</p>
          <a href="{{ route('psikolog.index') }}">Book Now</a>
        </div>
      </div>

    </div>
  </div>
</section>
@endsection
