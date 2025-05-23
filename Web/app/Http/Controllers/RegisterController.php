<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $auth;
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/' . env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->auth = $factory->createAuth();
        $this->database = $factory->createDatabase();
    }

    public function index()
    {
        return view('login.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kulad' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]{3,}$/'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/', 'confirmed'],
            'password_confirmation' => ['required']
        ], [
            'kulad.required' => 'Kullanıcı adı alanı zorunludur.',
            'kulad.regex' => 'Kullanıcı adı en az 3 karakter olmalı ve sadece harf içermelidir.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.regex' => 'Şifre en az bir harf ve bir rakam içermelidir.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'password_confirmation.required' => 'Şifre tekrarı alanı zorunludur.'
        ]);

        // Firebase bağlantı kontrolü
        if (!$this->auth || !$this->database) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Şu anda kayıt yapılamıyor. Lütfen daha sonra tekrar deneyin.']);
        }

        try {
            // Önce e-posta kontrolü yap
            try {
                $existingUser = $this->auth->getUserByEmail($validated['email']);
                if ($existingUser) {
                    return back()
                        ->withInput()
                        ->withErrors(['email' => 'Bu e-posta adresi zaten kullanımda.']);
                }
            } catch (\Exception $e) {
                // Kullanıcı bulunamadı, devam et
            }

            // Firebase'de kullanıcı oluştur
            $userProperties = [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'displayName' => $validated['kulad'],
            ];

            $firebaseUser = $this->auth->createUser($userProperties);

            // Şifreyi hashle
            $hashedPassword = Hash::make($validated['password']);

            try {
                // Firebase Realtime Database'e kullanıcı bilgilerini kaydet
                $this->database->getReference('users/' . $firebaseUser->uid)
                    ->set([
                        'kulad' => $validated['kulad'],
                        'email' => $validated['email'],
                        'sifre' => $hashedPassword,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                // Session'a success mesajını kaydet ve view'a yönlendir
                session()->flash('success', 'Hesabınız başarıyla oluşturuldu! Giriş yapabilirsiniz.');
                session()->flash('registered', true);
                return redirect('/login');

            } catch (\Exception $e) {
                // Veritabanına kayıt başarısız olursa Firebase Authentication'dan da sil
                try {
                    $this->auth->deleteUser($firebaseUser->uid);
                } catch (\Exception $e) {
                    // Silme işlemi başarısız olursa loglayabilirsiniz
                }

                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Kayıt işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.']);
            }

        } catch (\Exception $e) {
            $errorMessage = 'Kayıt işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.';

            if (strpos($e->getMessage(), 'EMAIL_EXISTS') !== false) {
                $errorMessage = 'Bu e-posta adresi zaten kullanımda.';
            } elseif (strpos($e->getMessage(), 'WEAK_PASSWORD') !== false) {
                $errorMessage = 'Şifreniz çok zayıf. Lütfen daha güçlü bir şifre seçin.';
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $errorMessage]);
        }
    }
}
