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
      MengobrolMengobati is a digital mental health platform designed to provide a safe, supportive, and accessible space for individuals to care for their psychological well-being. We believe that open conversation, professional guidance, and timely support play a crucial role in maintaining mental health.
    </p>
    <p class="about-subtitle">
      Our platform connects users with licensed psychologists and psychiatrists through secure online consultations, allowing individuals to seek help without barriers of distance, time, or stigma. In addition to one-on-one consultations, MengobrolMengobati offers moderated therapy groups where users can share experiences anonymously, fostering empathy, understanding, and collective healing.
    </p>
    <p class="about-subtitle">
      We are committed to professionalism, confidentiality, and user safety. All interactions on our platform are handled with strict privacy standards and ethical practices, ensuring that every user feels respected and protected.
    </p>
    <p class="about-subtitle">
        At MengobrolMengobati, our mission is to make mental health support more approachable, inclusive, and effective. By combining technology with compassionate care, we strive to empower individuals to better understand themselves, overcome challenges, and improve their overall quality of life.
    </p>
  </div>
</section>

{{-- ======================
   TEAM
====================== --}}
{{-- <section class="team-section">
  <div class="container">
    <h2 class="section-title">Our Team</h2>

    <div class="row g-4"> --}}

      {{-- MEMBER 1 --}}
      {{-- <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team1.png') }}" alt="Aldo Revaldo">
          <h4>Aldo Revaldo</h4>
          <span>10123163</span>
        </div>
      </div> --}}

      {{-- MEMBER 2 --}}
      {{-- <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team2.png') }}" alt="Dimas Akbar Alhafidz">
          <h4>Dimas Akbar Al.hafidz</h4>
          <span>10123162</span>
        </div>
      </div> --}}

      {{-- MEMBER 3 --}}
      {{-- <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team3.png') }}" alt="Randi Adittiawan">
          <h4>Randi Adittiawan</h4>
          <span>10123143</span>
        </div>
      </div> --}}

      {{-- MEMBER 4 --}}
      {{-- <div class="col-12 col-sm-6 col-md-3">
        <div class="team-card">
          <img src="{{ asset('images/team4.png') }}" alt="Mochamad Yogi Ady Pratama">
          <h4>Mochamad Yogi Ady Pratama</h4>
          <span>10123170</span>
        </div>
      </div> --}}

    {{-- </div>
  </div>
</section> --}}
@endsection
