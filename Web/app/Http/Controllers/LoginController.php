<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
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
        return view('login.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/'],
        ], [
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.regex' => 'Şifre en az bir harf ve bir rakam içermelidir.',
        ]);

        try {
            // Firebase bağlantı kontrolü
            if (!$this->database) {
                return back()
                    ->withInput(['email' => $validated['email']])
                    ->withErrors(['email' => 'Şu anda giriş yapılamıyor. Lütfen daha sonra tekrar deneyin.']);
            }

            // Kullanıcıları getir
            $usersRef = $this->database->getReference('users');
            $snapshot = $usersRef->getSnapshot();
            $users = $snapshot->getValue();

            if (!$users) {
                return back()
                    ->withInput(['email' => $validated['email']])
                    ->withErrors(['email' => 'Kullanıcı bulunamadı.']);
            }

            // E-posta ile kullanıcıyı bul
            $foundUser = null;
            $userId = null;
            foreach ($users as $uid => $user) {
                if ($user['email'] === $validated['email']) {
                    $foundUser = $user;
                    $userId = $uid;
                    break;
                }
            }

            if (!$foundUser) {
                return back()
                    ->withInput(['email' => $validated['email']])
                    ->withErrors(['email' => 'Bu e-posta adresi ile kayıtlı bir hesap bulunamadı.']);
            }

            // Şifre kontrolü
            if (!Hash::check($validated['password'], $foundUser['sifre'])) {
                return back()
                    ->withInput(['email' => $validated['email']])
                    ->withErrors(['password' => 'Girdiğiniz şifre hatalı.']);
            }

            // Session'a kullanıcı bilgilerini kaydet
            Session::put('user', [
                'uid' => $userId,
                'kulad' => $foundUser['kulad'],
                'email' => $foundUser['email'],
                'is_logged_in' => true
            ]);

            return redirect('/')->with('success', 'Başarıyla giriş yaptınız!');

        } catch (Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return back()
                ->withInput(['email' => $validated['email']])
                ->withErrors(['email' => 'Giriş yapılırken bir hata oluştu. Lütfen tekrar deneyin.']);
        }
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/')->with('success', 'Başarıyla çıkış yaptınız!');
    }

}
