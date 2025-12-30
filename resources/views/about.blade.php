@extends('layouts.app')

@section('title','About - MengobrolMengobati')
@section('body-class','about-page')

@vite('resources/css/about.css')

@section('content')

{{-- ======================
   ABOUT HERO
====================== --}}
<section class="about-hero">
  <div class="container">
    <h1 class="about-title">About Us</h1>
    <p class="about-subtitle">
      MengobrolMengobati is a mental health platform designed as a safe space for sharing stories, expressing feelings, and receiving responsible support. We believe that mental health plays an equally important role as physical health, and every individual has the right to be heard and understood.
    </p>
    {{-- <p class="about-subtitle">
      Through professional consultation services, group therapy, and communication features that protect user privacy, Talking and Healing aims to provide accessible, inclusive, and empathy-based solutions. This platform is developed with a focus on security, comfort, and user confidentiality.
    </p> --}}
  </div>
</section>

{{-- ======================
   TEAM
====================== --}}
<section class="team-section">
  <div class="container">
    <h2 class="section-title">Our Team</h2>

    <div class="row g-4">

      {{-- MEMBER 1 --}}
      <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team1.png') }}" alt="Aldo Revaldo">
          <h4>Aldo Revaldo</h4>
          <span>10123163</span>
        </div>
      </div>

      {{-- MEMBER 2 --}}
      <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team2.png') }}" alt="Dimas Akbar Alhafidz">
          <h4>Dimas Akbar Al.hafidz</h4>
          <span>10123162</span>
        </div>
      </div>

      {{-- MEMBER 3 --}}
      <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team3.png') }}" alt="Randi Adittiawan">
          <h4>Randi Adittiawan</h4>
          <span>10123143</span>
        </div>
      </div>

      {{-- MEMBER 4 --}}
      <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team4.png') }}" alt="Mochamad Yogi Ady Pratama">
          <h4>Mochamad Yogi Ady Pratama</h4>
          <span>10123170</span>
        </div>
      </div>

    </div>
  </div>
</section>
@endsection
