package com.example.finalapp;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.AuthResult;
import com.google.firebase.auth.FirebaseAuth;

public class LoginActivity extends AppCompatActivity {
    
    private FirebaseAuth mAuth;
    private EditText emailEditText, passwordEditText;
    private Button loginButton;
    private TextView signUpTextView;
    private LinearLayout containerLogin, containerSignUp;
    private ImageView logoImage;
    private LinearLayout formContainer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        // Firebase Auth
        mAuth = FirebaseAuth.getInstance();

        // Eğer kullanıcı zaten giriş yapmışsa, doğrudan MainActivity'e yönlendir
        if (mAuth.getCurrentUser() != null) {
            startActivity(new Intent(LoginActivity.this, MainActivity.class));
            finish();
        }
        
        // UI bileşenlerini tanımla
        emailEditText = findViewById(R.id.emailEditText);
        passwordEditText = findViewById(R.id.passwordEditText);
        loginButton = findViewById(R.id.loginButton);
        containerLogin = findViewById(R.id.container_login);
        containerSignUp = findViewById(R.id.container_sign_up);
        logoImage = findViewById(R.id.img_vector1);
        formContainer = findViewById(R.id.container_content);

        // Animasyonları uygula
        applyAnimations();
        
        // Login butonu
        loginButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Tıklama animasyonu
                Animation clickAnim = AnimationUtils.loadAnimation(LoginActivity.this, R.anim.button_scale);
                v.startAnimation(clickAnim);
                
                loginUser();
            }
        });
        
        // Kayıt ol sayfasına yönlendirme
        containerSignUp.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Tıklama animasyonu
                Animation clickAnim = AnimationUtils.loadAnimation(LoginActivity.this, R.anim.button_scale);
                v.startAnimation(clickAnim);
                
                Intent intent = new Intent(LoginActivity.this, RegisterActivity.class);
                startActivity(intent);
                overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
            }
        });
    }
    
    private void applyAnimations() {
        // Logo animasyonu
        Animation bounceAnim = AnimationUtils.loadAnimation(this, R.anim.bounce);
        logoImage.startAnimation(bounceAnim);
        
        // Form animasyonu
        Animation fadeInAnimation = AnimationUtils.loadAnimation(this, R.anim.fade_slide_up);
        formContainer.startAnimation(fadeInAnimation);
        
        // Buton animasyonu
        Animation buttonScaleAnim = AnimationUtils.loadAnimation(this, R.anim.button_scale);
        loginButton.startAnimation(buttonScaleAnim);
    }
    
    private void loginUser() {
        String email = emailEditText.getText().toString().trim();
        String password = passwordEditText.getText().toString().trim();
        
        // Email ve şifre alanları boş mu kontrol et
        if (TextUtils.isEmpty(email)) {
            emailEditText.setError("Email gerekli!");
            return;
        }
        
        if (TextUtils.isEmpty(password)) {
            passwordEditText.setError("Şifre gerekli!");
            return;
        }
        
        // Firebase auth ile giriş işlemi
        mAuth.signInWithEmailAndPassword(email, password)
                .addOnCompleteListener(this, new OnCompleteListener<AuthResult>() {
                    @Override
                    public void onComplete(@NonNull Task<AuthResult> task) {
                        if (task.isSuccessful()) {
                            // Giriş başarılı, ana sayfaya yönlendir
                            Intent intent = new Intent(LoginActivity.this, MainActivity.class);
                            startActivity(intent);
                            finish();
                            overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                        } else {
                            // Giriş başarısız, hata mesajı göster
                            CustomToast.showError(LoginActivity.this, "Giriş başarısız: " + task.getException().getMessage());
                        }
                    }
                });
    }
    
    @Override
    public void onBackPressed() {
        finishAffinity();
    }
} 