@extends('layouts.layout')
@section('main')
<section class="register-section">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 d-none d-md-block">
                <div class="text-center">
                    <img src="{{asset('assets/img/register.png')}}" class="img-fluid" alt="Register illustration">
                    <div class="mt-4">
                        <h3 class="fw-bold">CO Guard'a Hoş Geldiniz</h3>
                        <p class="text-muted">Güvenliğiniz bizim önceliğimiz</p>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 col-md-6 col-lg-5">
                <div class="auth-form">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Hesap Oluştur</h2>
                        <p class="text-muted">Hemen ücretsiz hesabınızı oluşturun</p>
                    </div>

                    <form action="/register" method="POST">
                        @csrf
                        <!-- Username input -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="username">Kullanıcı Adı</label>
                            <input type="text" name="kulad" id="username" 
                                class="form-control @error('kulad') is-invalid @enderror"
                                placeholder="Kullanıcı adınızı giriniz" 
                                value="{{ old('kulad') }}" />
                            @error('kulad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email input -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="email">E-posta Adresi</label>
                            <input type="email" name="email" id="email" 
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="E-posta adresinizi giriniz" 
                                value="{{ old('email') }}" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password input -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="sifre">Şifre</label>
                            <input type="password" name="password" id="sifre" 
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Şifrenizi giriniz" />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group mb-4">
                            <label class="form-label" for="sifre2">Şifre Tekrarı</label>
                            <input type="password" name="password_confirmation" id="sifre2" 
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Şifrenizi tekrar giriniz" />
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Kayıt Ol
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">Zaten hesabınız var mı?</p>
                            <a href="/login" class="fw-semibold">Giriş Yap</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
