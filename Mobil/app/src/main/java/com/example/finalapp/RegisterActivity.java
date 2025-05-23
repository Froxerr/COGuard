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
import com.google.firebase.database.DatabaseReference;
import com.google.firebase.database.FirebaseDatabase;
import com.google.firebase.database.ServerValue;

import java.util.HashMap;
import java.util.Map;

public class RegisterActivity extends AppCompatActivity {

    private FirebaseAuth mAuth;
    private DatabaseReference mDatabase;
    private EditText usernameEditText, emailEditText, passwordEditText, confirmPasswordEditText;
    private Button registerButton;
    private LinearLayout containerLogin, containerSignUp;
    private ImageView logoImage;
    private LinearLayout formContainer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        // Firebase Auth
        mAuth = FirebaseAuth.getInstance();
        mDatabase = FirebaseDatabase.getInstance().getReference();

        // UI bileşenlerini tanımla
        usernameEditText = findViewById(R.id.usernameEditText);
        emailEditText = findViewById(R.id.emailEditText);
        passwordEditText = findViewById(R.id.passwordEditText);
        confirmPasswordEditText = findViewById(R.id.confirmPasswordEditText);
        registerButton = findViewById(R.id.registerButton);
        containerLogin = findViewById(R.id.container_login);
        containerSignUp = findViewById(R.id.container_sign_up);
        logoImage = findViewById(R.id.img_vector1);
        formContainer = findViewById(R.id.container_content);

        // Animasyonları uygula
        applyAnimations();
        
        // Register butonu
        registerButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Tıklama animasyonu
                Animation clickAnim = AnimationUtils.loadAnimation(RegisterActivity.this, R.anim.button_scale);
                v.startAnimation(clickAnim);
                
                registerUser();
            }
        });
        
        // Giriş sayfasına yönlendirme
        containerLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Tıklama animasyonu
                Animation clickAnim = AnimationUtils.loadAnimation(RegisterActivity.this, R.anim.button_scale);
                v.startAnimation(clickAnim);
                
                Intent intent = new Intent(RegisterActivity.this, LoginActivity.class);
                startActivity(intent);
                overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right);
            }
        });
    }
    
    private void applyAnimations() {
        // Logo animasyonu
        Animation bounceAnim = AnimationUtils.loadAnimation(this, R.anim.bounce);
        logoImage.startAnimation(bounceAnim);
        
        // Form animasyonu
        Animation fadeInAnimation = AnimationUtils.loadAnimation(this, R.anim.fade_slide_down);
        formContainer.startAnimation(fadeInAnimation);
        
        // Buton animasyonu
        Animation buttonScaleAnim = AnimationUtils.loadAnimation(this, R.anim.button_scale);
        registerButton.startAnimation(buttonScaleAnim);
    }
    
    private void registerUser() {
        final String username = usernameEditText.getText().toString().trim();
        final String email = emailEditText.getText().toString().trim();
        String password = passwordEditText.getText().toString().trim();
        String confirmPassword = confirmPasswordEditText.getText().toString().trim();
        
        // Alanları kontrol et
        if (TextUtils.isEmpty(username)) {
            usernameEditText.setError("Kullanıcı adı gerekli!");
            return;
        }
        
        if (TextUtils.isEmpty(email)) {
            emailEditText.setError("Email gerekli!");
            return;
        }
        
        if (TextUtils.isEmpty(password)) {
            passwordEditText.setError("Şifre gerekli!");
            return;
        }
        
        if (TextUtils.isEmpty(confirmPassword)) {
            confirmPasswordEditText.setError("Şifre tekrarı gerekli!");
            return;
        }
        
        if (password.length() < 6) {
            passwordEditText.setError("Şifre en az 6 karakter olmalıdır!");
            return;
        }
        
        if (!password.equals(confirmPassword)) {
            confirmPasswordEditText.setError("Şifreler eşleşmiyor!");
            return;
        }
        
        // Firebase ile kayıt işlemi
        mAuth.createUserWithEmailAndPassword(email, password)
                .addOnCompleteListener(this, new OnCompleteListener<AuthResult>() {
                    @Override
                    public void onComplete(@NonNull Task<AuthResult> task) {
                        if (task.isSuccessful()) {
                            // Kayıt başarılı, kullanıcı bilgilerini veritabanına ekle
                            String userId = mAuth.getCurrentUser().getUid();
                            Map<String, Object> user = new HashMap<>();
                            user.put("kulad", username);
                            user.put("email", email);
                            user.put("sifre", password);
                            user.put("created_at", ServerValue.TIMESTAMP);
                            
                            mDatabase.child("users").child(userId).setValue(user)
                                    .addOnCompleteListener(new OnCompleteListener<Void>() {
                                        @Override
                                        public void onComplete(@NonNull Task<Void> task) {
                                            if (task.isSuccessful()) {
                                                CustomToast.showSuccess(RegisterActivity.this, "Kayıt başarılı!");
                                                
                                                // Ana sayfaya yönlendir
                                                Intent intent = new Intent(RegisterActivity.this, MainActivity.class);
                                                startActivity(intent);
                                                finish();
                                                overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                                            } else {
                                                CustomToast.showError(RegisterActivity.this, "Veritabanı kaydı başarısız: " + task.getException().getMessage());
                                            }
                                        }
                                    });
                        } else {
                            // Kayıt başarısız
                            CustomToast.showError(RegisterActivity.this, "Kayıt başarısız: " + task.getException().getMessage());
                        }
                    }
                });
    }
    
    @Override
    public void onBackPressed() {
        Intent intent = new Intent(RegisterActivity.this, LoginActivity.class);
        startActivity(intent);
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right);
        finish();
    }
}