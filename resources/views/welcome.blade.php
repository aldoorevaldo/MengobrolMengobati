{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('title','Home - MengobrolMengobati')
@section('body-class','home-page')

@vite(['resources/css/home.css'])

@section('content')
<section class="hero">
    <div class="container">

        <div class="hero-content">

            <h1 class="hero-title">
                <span class="hero-keyword">HEALTHY</span>
                <span class="hero-subtitle">Conversation for Better Life</span>
            </h1>

            <p class="hero-desc">
                MengobrolMengobati is a safe space to share stories, find solutions, and maintain mental health together.
            </p>

            <div class="cta-group">
                <a href="{{ route('services') }}" class="btn-cta">
                    Explore Services
                </a>

                @guest
                    <a href="{{ route('login') }}" class="btn-login">
                        Login
                    </a>
                @endguest
            </div>

        </div>

    </div>
</section>

 {{-- FOOTER --}}
  <footer class="site-footer">
    <div class="container text-center">
        <p>© {{ date('Y') }} MengobrolMengobati. All rights reserved.</p>
    </div>
  </footer>

@endsection
