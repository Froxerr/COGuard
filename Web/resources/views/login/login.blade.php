@extends('layouts.layout')
@section('main')
<section class="register-section">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 d-none d-md-block">
                <div class="text-center">
                    <img src="{{asset('assets/img/register.png')}}" class="img-fluid" alt="Login illustration">
                    <div class="mt-4">
                        <h3 class="fw-bold">Tekrar Hoş Geldiniz</h3>
                        <p class="text-muted">CO Guard hesabınıza giriş yapın</p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 col-md-6 col-lg-5">
                <div class="auth-form">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Giriş Yap</h2>
                        <p class="text-muted">Hesabınıza erişin</p>
                    </div>

                    <form action="/login" method="POST">
                        @csrf
                        <!-- Email input -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="email">E-posta Adresi</label>
                            <input type="email" name="email" id="email" 
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="E-posta adresinizi giriniz" 
                                value="{{ old('email') }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password input -->
                        <div class="form-group mb-4">
                            <label class="form-label" for="password">Şifre</label>
                            <input type="password" name="password" id="password" 
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Şifrenizi giriniz" required />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" 
                                    id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Beni Hatırla
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Giriş Yap
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">Hesabınız yok mu?</p>
                            <a href="/register" class="fw-semibold">Hesap Oluştur</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
