package com.example.finalapp;

import android.os.Bundle;
import android.view.View;
import android.widget.TextView;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import androidx.appcompat.app.AppCompatActivity;
import androidx.annotation.NonNull;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.card.MaterialCardView;
import com.google.firebase.database.DataSnapshot;
import com.google.firebase.database.DatabaseError;
import com.google.firebase.database.DatabaseReference;
import com.google.firebase.database.FirebaseDatabase;
import com.google.firebase.database.ValueEventListener;
import android.widget.Toast;
import java.util.Date;
import java.util.Locale;
import java.text.SimpleDateFormat;
import java.util.Map;
import java.util.HashMap;

public class AlarmControlActivity extends AppCompatActivity {

    private MaterialCardView buzzerCardView, warningLightCardView, windowCardView;
    private TextView buzzerStatusTextView, warningLightStatusTextView, windowStatusTextView;
    private MaterialButton backButton;
    private DatabaseReference deviceOutputsRef;
    private DatabaseReference alarmsLogRef;
    private DatabaseReference systemControlRef;
    private ValueEventListener buzzerListener;
    private ValueEventListener ledListener;
    private ValueEventListener servoListener;
    private ValueEventListener systemControlListener;
    private boolean isBuzzerFirstAlarm = false;
    private boolean isWarningLightFirstAlarm = false;
    private boolean isWindowFirstAlarm = false;
    private boolean isMechanicalIntervention = false;
    private boolean isUserIntervention = false;
    private View buzzerOverlay, warningLightOverlay, windowOverlay;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.alarm_control);

        // Firebase Database referanslarını başlat
        deviceOutputsRef = FirebaseDatabase.getInstance().getReference("device_outputs");
        alarmsLogRef = FirebaseDatabase.getInstance().getReference("alarms_log");
        systemControlRef = FirebaseDatabase.getInstance().getReference("system_control");

        // Card View'ları tanımla
        buzzerCardView = findViewById(R.id.buzzerCardView);
        warningLightCardView = findViewById(R.id.warningLightCardView);
        windowCardView = findViewById(R.id.windowCardView);

        // Overlay'leri tanımla
        buzzerOverlay = findViewById(R.id.buzzerOverlay);
        warningLightOverlay = findViewById(R.id.warningLightOverlay);
        windowOverlay = findViewById(R.id.windowOverlay);

        // Durum text view'larını tanımla
        buzzerStatusTextView = findViewById(R.id.buzzerStatusTextView);
        warningLightStatusTextView = findViewById(R.id.warningLightStatusTextView);
        windowStatusTextView = findViewById(R.id.windowStatusTextView);
        
        // Progress bar'ları tanımla
        View buzzerProgressBar = findViewById(R.id.buzzerProgressBar);
        View warningLightProgressBar = findViewById(R.id.warningLightProgressBar);
        View windowProgressBar = findViewById(R.id.windowProgressBar);
        
        // Progress bar'ları başlangıçta göster
        buzzerProgressBar.setVisibility(View.VISIBLE);
        warningLightProgressBar.setVisibility(View.VISIBLE);
        windowProgressBar.setVisibility(View.VISIBLE);
        
        // Durum text view'larını başlangıçta gizle
        buzzerStatusTextView.setVisibility(View.GONE);
        warningLightStatusTextView.setVisibility(View.GONE);
        windowStatusTextView.setVisibility(View.GONE);

        // Butonları tanımla
        backButton = findViewById(R.id.backButton);
        MaterialButton buzzerOnButton = findViewById(R.id.buzzerOnButton);
        MaterialButton buzzerOffButton = findViewById(R.id.buzzerOffButton);
        MaterialButton warningLightOnButton = findViewById(R.id.warningLightOnButton);
        MaterialButton warningLightOffButton = findViewById(R.id.warningLightOffButton);
        MaterialButton windowOpenButton = findViewById(R.id.windowOpenButton);
        MaterialButton windowCloseButton = findViewById(R.id.windowCloseButton);

        // Giriş animasyonlarını uygula
        Animation fadeIn = AnimationUtils.loadAnimation(this, android.R.anim.fade_in);
        fadeIn.setDuration(500);
        
        buzzerCardView.startAnimation(fadeIn);
        warningLightCardView.startAnimation(fadeIn);
        windowCardView.startAnimation(fadeIn);

        // Buton animasyonları
        Animation scaleUp = AnimationUtils.loadAnimation(this, R.anim.scale_up);
        buzzerOnButton.startAnimation(scaleUp);
        buzzerOffButton.startAnimation(scaleUp);
        warningLightOnButton.startAnimation(scaleUp);
        warningLightOffButton.startAnimation(scaleUp);
        windowOpenButton.startAnimation(scaleUp);
        windowCloseButton.startAnimation(scaleUp);

        // Geri dönüş butonu işlevi
        backButton.setOnClickListener(v -> {
            finish();
            overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right);
        });

        // Firebase'den sistem kontrol durumunu dinle
        setupSystemControlListener();

        // Firebase'den alarm durumlarını dinle
        setupDeviceOutputsListeners();

        // Buzzer kontrolleri
        setupBuzzerControls(buzzerOnButton, buzzerOffButton);

        // Uyarı Işığı kontrolleri
        setupWarningLightControls(warningLightOnButton, warningLightOffButton);

        // Pencere kontrolleri
        setupWindowControls(windowOpenButton, windowCloseButton);
    }

    private void setupSystemControlListener() {
        systemControlListener = new ValueEventListener() {
            @Override
            public void onDataChange(@NonNull DataSnapshot snapshot) {
                if (snapshot.exists()) {
                    // mechanical_intervention durumunu kontrol et
                    if (snapshot.child("mechanical_intervention").exists()) {
                        isMechanicalIntervention = Boolean.TRUE.equals(snapshot.child("mechanical_intervention").getValue(Boolean.class));
                    }
                    
                    // user_intervention durumunu kontrol et
                    if (snapshot.child("user_intervention").exists()) {
                        isUserIntervention = Boolean.TRUE.equals(snapshot.child("user_intervention").getValue(Boolean.class));
                    }
                    
                    // Overlay'leri güncelle
                    updateOverlays();
                }
            }

            @Override
            public void onCancelled(@NonNull DatabaseError error) {
                // Hata durumunda varsayılan değerleri kullan
                isMechanicalIntervention = false;
                isUserIntervention = false;
                updateOverlays();
            }
        };
        systemControlRef.addValueEventListener(systemControlListener);
    }

    private void updateOverlays() {
        // Eğer mechanical_intervention true ise ve user_intervention false ise overlay'leri göster
        boolean showOverlays = isMechanicalIntervention && !isUserIntervention;
        
        // Buzzer overlay'ini güncelle
        if (buzzerOverlay != null) {
            buzzerOverlay.setVisibility(showOverlays || isBuzzerFirstAlarm ? View.VISIBLE : View.GONE);
        }
        
        // Warning light overlay'ini güncelle
        if (warningLightOverlay != null) {
            warningLightOverlay.setVisibility(showOverlays || isWarningLightFirstAlarm ? View.VISIBLE : View.GONE);
        }
        
        // Window overlay'ini güncelle
        if (windowOverlay != null) {
            windowOverlay.setVisibility(showOverlays || isWindowFirstAlarm ? View.VISIBLE : View.GONE);
        }
    }

    private void setupDeviceOutputsListeners() {
        // Progress bar referanslarını tekrar al
        final View buzzerProgressBar = findViewById(R.id.buzzerProgressBar);
        final View warningLightProgressBar = findViewById(R.id.warningLightProgressBar);
        final View windowProgressBar = findViewById(R.id.windowProgressBar);
        
        // Buton referanslarını al
        final MaterialButton buzzerOnButton = findViewById(R.id.buzzerOnButton);
        final MaterialButton buzzerOffButton = findViewById(R.id.buzzerOffButton);
        final MaterialButton warningLightOnButton = findViewById(R.id.warningLightOnButton);
        final MaterialButton warningLightOffButton = findViewById(R.id.warningLightOffButton);
        final MaterialButton windowOpenButton = findViewById(R.id.windowOpenButton);
        final MaterialButton windowCloseButton = findViewById(R.id.windowCloseButton);
        
        // Buzzer (output_type_1) durumunu dinle
        buzzerListener = new ValueEventListener() {
            @Override
            public void onDataChange(@NonNull DataSnapshot snapshot) {
                // Progress bar'ı gizle, durum metnini göster
                buzzerProgressBar.setVisibility(View.GONE);
                buzzerStatusTextView.setVisibility(View.VISIBLE);
                
                if (snapshot.exists()) {
                    // first_alarm durumunu kontrol et
                    if (snapshot.child("first_alarm").exists()) {
                        isBuzzerFirstAlarm = Boolean.TRUE.equals(snapshot.child("first_alarm").getValue(Boolean.class));
                        // Overlay'i güncelle
                        buzzerOverlay.setVisibility(isBuzzerFirstAlarm ? View.VISIBLE : View.GONE);
                    }
                    
                    if (snapshot.child("status").exists()) {
                        Integer status = snapshot.child("status").getValue(Integer.class);
                        if (status != null) {
                            boolean isActive = status == 1;
                            updateStatusText(buzzerStatusTextView, isActive);
                            
                            // Butonların etkinliğini ayarla
                            buzzerOnButton.setEnabled(!isActive);
                            buzzerOffButton.setEnabled(isActive);
                            
                            // Butonların görünümünü güncelle
                            updateButtonAppearance(buzzerOnButton, !isActive);
                            updateButtonAppearance(buzzerOffButton, isActive);
                        }
                    }
                } else {
                    buzzerStatusTextView.setText("Veri Yok");
                    buzzerStatusTextView.setTextColor(getResources().getColor(android.R.color.darker_gray));
                    
                    // Veri yoksa her iki butonu da etkinleştir
                    buzzerOnButton.setEnabled(true);
                    buzzerOffButton.setEnabled(true);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(buzzerOnButton, true);
                    updateButtonAppearance(buzzerOffButton, true);
                }
            }

            @Override
            public void onCancelled(@NonNull DatabaseError error) {
                // Hata yönetimi
                buzzerProgressBar.setVisibility(View.GONE);
                buzzerStatusTextView.setVisibility(View.VISIBLE);
                buzzerStatusTextView.setText("Hata: " + error.getMessage());
                buzzerStatusTextView.setTextColor(getResources().getColor(android.R.color.holo_red_dark));
                
                // Hata durumunda her iki butonu da etkinleştir
                buzzerOnButton.setEnabled(true);
                buzzerOffButton.setEnabled(true);
                
                // Butonların görünümünü güncelle
                updateButtonAppearance(buzzerOnButton, true);
                updateButtonAppearance(buzzerOffButton, true);
            }
        };
        deviceOutputsRef.child("output_type_1").addValueEventListener(buzzerListener);

        // LED (output_type_2) durumunu dinle
        ledListener = new ValueEventListener() {
            @Override
            public void onDataChange(@NonNull DataSnapshot snapshot) {
                // Progress bar'ı gizle, durum metnini göster
                warningLightProgressBar.setVisibility(View.GONE);
                warningLightStatusTextView.setVisibility(View.VISIBLE);
                
                if (snapshot.exists()) {
                    // first_alarm durumunu kontrol et
                    if (snapshot.child("first_alarm").exists()) {
                        isWarningLightFirstAlarm = Boolean.TRUE.equals(snapshot.child("first_alarm").getValue(Boolean.class));
                        // Overlay'i güncelle
                        warningLightOverlay.setVisibility(isWarningLightFirstAlarm ? View.VISIBLE : View.GONE);
                    }
                    
                    if (snapshot.child("status").exists()) {
                        Integer status = snapshot.child("status").getValue(Integer.class);
                        if (status != null) {
                            boolean isActive = status == 1;
                            updateStatusText(warningLightStatusTextView, isActive);
                            
                            // Butonların etkinliğini ayarla
                            warningLightOnButton.setEnabled(!isActive);
                            warningLightOffButton.setEnabled(isActive);
                            
                            // Butonların görünümünü güncelle
                            updateButtonAppearance(warningLightOnButton, !isActive);
                            updateButtonAppearance(warningLightOffButton, isActive);
                        }
                    }
                } else {
                    warningLightStatusTextView.setText("Veri Yok");
                    warningLightStatusTextView.setTextColor(getResources().getColor(android.R.color.darker_gray));
                    
                    // Veri yoksa her iki butonu da etkinleştir
                    warningLightOnButton.setEnabled(true);
                    warningLightOffButton.setEnabled(true);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(warningLightOnButton, true);
                    updateButtonAppearance(warningLightOffButton, true);
                }
            }

            @Override
            public void onCancelled(@NonNull DatabaseError error) {
                // Hata yönetimi
                warningLightProgressBar.setVisibility(View.GONE);
                warningLightStatusTextView.setVisibility(View.VISIBLE);
                warningLightStatusTextView.setText("Hata: " + error.getMessage());
                warningLightStatusTextView.setTextColor(getResources().getColor(android.R.color.holo_red_dark));
                
                // Hata durumunda her iki butonu da etkinleştir
                warningLightOnButton.setEnabled(true);
                warningLightOffButton.setEnabled(true);
                
                // Butonların görünümünü güncelle
                updateButtonAppearance(warningLightOnButton, true);
                updateButtonAppearance(warningLightOffButton, true);
            }
        };
        deviceOutputsRef.child("output_type_2").addValueEventListener(ledListener);

        // Servo Motor (output_type_3) durumunu dinle
        servoListener = new ValueEventListener() {
            @Override
            public void onDataChange(@NonNull DataSnapshot snapshot) {
                // Progress bar'ı gizle, durum metnini göster
                windowProgressBar.setVisibility(View.GONE);
                windowStatusTextView.setVisibility(View.VISIBLE);
                
                if (snapshot.exists()) {
                    // first_alarm durumunu kontrol et
                    if (snapshot.child("first_alarm").exists()) {
                        isWindowFirstAlarm = Boolean.TRUE.equals(snapshot.child("first_alarm").getValue(Boolean.class));
                        // Overlay'i güncelle
                        windowOverlay.setVisibility(isWindowFirstAlarm ? View.VISIBLE : View.GONE);
                    }
                    
                    if (snapshot.child("status").exists()) {
                        Integer status = snapshot.child("status").getValue(Integer.class);
                        if (status != null) {
                            boolean isActive = status == 1;
                            updateStatusText(windowStatusTextView, isActive);
                            
                            // Butonların etkinliğini ayarla
                            windowOpenButton.setEnabled(!isActive);
                            windowCloseButton.setEnabled(isActive);
                            
                            // Butonların görünümünü güncelle
                            updateButtonAppearance(windowOpenButton, !isActive);
                            updateButtonAppearance(windowCloseButton, isActive);
                        }
                    }
                } else {
                    windowStatusTextView.setText("Veri Yok");
                    windowStatusTextView.setTextColor(getResources().getColor(android.R.color.darker_gray));
                    
                    // Veri yoksa her iki butonu da etkinleştir
                    windowOpenButton.setEnabled(true);
                    windowCloseButton.setEnabled(true);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(windowOpenButton, true);
                    updateButtonAppearance(windowCloseButton, true);
                }
            }

            @Override
            public void onCancelled(@NonNull DatabaseError error) {
                // Hata yönetimi
                windowProgressBar.setVisibility(View.GONE);
                windowStatusTextView.setVisibility(View.VISIBLE);
                windowStatusTextView.setText("Hata: " + error.getMessage());
                windowStatusTextView.setTextColor(getResources().getColor(android.R.color.holo_red_dark));
                
                // Hata durumunda her iki butonu da etkinleştir
                windowOpenButton.setEnabled(true);
                windowCloseButton.setEnabled(true);
                
                // Butonların görünümünü güncelle
                updateButtonAppearance(windowOpenButton, true);
                updateButtonAppearance(windowCloseButton, true);
            }
        };
        deviceOutputsRef.child("output_type_3").addValueEventListener(servoListener);
    }

    private void setupBuzzerControls(MaterialButton onButton, MaterialButton offButton) {
        onButton.setOnClickListener(v -> {
            if (isBuzzerFirstAlarm || (isMechanicalIntervention && !isUserIntervention)) {
                CustomToast.showError(this, isBuzzerFirstAlarm ? 
                    "Lütfen alarmın çalmasını bekleyiniz" : 
                    "Şu an sistem otomatik modunda");
                return;
            }
            // Progress bar göster
            View buzzerProgressBar = findViewById(R.id.buzzerProgressBar);
            buzzerProgressBar.setVisibility(View.VISIBLE);
            buzzerStatusTextView.setVisibility(View.GONE);
            
            // Firebase'i güncelle
            Map<String, Object> updates = new HashMap<>();
            updates.put("status", 1);
            updates.put("is_alarm_acknowledged", false);
            
            deviceOutputsRef.child("output_type_1").updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    // UI güncelleme işlemi listener'lar tarafından yapılacak
                    // Buton durumlarını güncelle
                    onButton.setEnabled(false);
                    offButton.setEnabled(true);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(onButton, false);
                    updateButtonAppearance(offButton, true);
                    
                    // Alarm log kaydını ekle
                    Map<String, Object> logData = new HashMap<>();
                    logData.put("alarm_description", "Kullanıcı tarafından manuel olarak alarm kontrolü açıldı");
                    logData.put("alarm_type", "Tehlike");
                    logData.put("created_at", System.currentTimeMillis() / 1000); // Unix timestamp (saniye)
                    logData.put("device_id", "device1");
                    logData.put("sensor_id", "sensor_type_1");
                    logData.put("threshold", 0);
                    logData.put("triggered_output", "output_type_1");
                    logData.put("value", 0);
                    
                    alarmsLogRef.push().setValue(logData)
                        .addOnSuccessListener(logSuccess -> {
                            CustomToast.showSuccess(AlarmControlActivity.this, "İşlem kaydedildi");
                        })
                        .addOnFailureListener(logError -> {
                            CustomToast.showError(AlarmControlActivity.this, "Log kaydı başarısız: " + logError.getMessage());
                        });
                })
                .addOnFailureListener(e -> {
                    // Hata durumunda kullanıcıyı bilgilendir
                    CustomToast.showError(AlarmControlActivity.this, "İşlem başarısız: " + e.getMessage());
                    buzzerProgressBar.setVisibility(View.GONE);
                    buzzerStatusTextView.setVisibility(View.VISIBLE);
                });
        });

        offButton.setOnClickListener(v -> {
            if (isBuzzerFirstAlarm || (isMechanicalIntervention && !isUserIntervention)) {
                CustomToast.showError(this, isBuzzerFirstAlarm ? 
                    "Lütfen alarmın çalmasını bekleyiniz" : 
                    "Şu an sistem otomatik modunda");
                return;
            }
            // Progress bar göster
            View buzzerProgressBar = findViewById(R.id.buzzerProgressBar);
            buzzerProgressBar.setVisibility(View.VISIBLE);
            buzzerStatusTextView.setVisibility(View.GONE);
            
            // Mevcut zaman
            String currentDateTime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).format(new Date());
            
            // Firebase'i güncelle
            Map<String, Object> updates = new HashMap<>();
            updates.put("status", 0);
            updates.put("is_alarm_acknowledged", true);
            updates.put("stopped_date", currentDateTime);
            
            deviceOutputsRef.child("output_type_1").updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    // UI güncelleme işlemi listener'lar tarafından yapılacak
                    // Buton durumlarını güncelle
                    onButton.setEnabled(true);
                    offButton.setEnabled(false);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(onButton, true);
                    updateButtonAppearance(offButton, false);
                    
                    // Alarm log kaydını ekle
                    Map<String, Object> logData = new HashMap<>();
                    logData.put("alarm_description", "Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı");
                    logData.put("alarm_type", "Tehlike");
                    logData.put("created_at", System.currentTimeMillis() / 1000); // Unix timestamp (saniye)
                    logData.put("device_id", "device1");
                    logData.put("sensor_id", "sensor_type_1");
                    logData.put("threshold", 0);
                    logData.put("triggered_output", "output_type_1");
                    logData.put("value", 0);
                    
                    alarmsLogRef.push().setValue(logData)
                        .addOnSuccessListener(logSuccess -> {
                            CustomToast.showSuccess(AlarmControlActivity.this, "İşlem kaydedildi");
                        })
                        .addOnFailureListener(logError -> {
                            CustomToast.showError(AlarmControlActivity.this, "Log kaydı başarısız: " + logError.getMessage());
                        });
                })
                .addOnFailureListener(e -> {
                    // Hata durumunda kullanıcıyı bilgilendir
                    CustomToast.showError(AlarmControlActivity.this, "İşlem başarısız: " + e.getMessage());
                    buzzerProgressBar.setVisibility(View.GONE);
                    buzzerStatusTextView.setVisibility(View.VISIBLE);
                });
        });
        
        // Başlangıçta buton durumlarını ayarla
        if (buzzerStatusTextView.getText().toString().equals("Açık")) {
            onButton.setEnabled(false);
            offButton.setEnabled(true);
        } else {
            onButton.setEnabled(true);
            offButton.setEnabled(false);
        }
    }

    private void setupWarningLightControls(MaterialButton onButton, MaterialButton offButton) {
        onButton.setOnClickListener(v -> {
            if (isWarningLightFirstAlarm || (isMechanicalIntervention && !isUserIntervention)) {
                CustomToast.showError(this, isWarningLightFirstAlarm ? 
                    "Lütfen alarmın çalmasını bekleyiniz" : 
                    "Şu an sistem otomatik modunda");
                return;
            }
            // Progress bar göster
            View warningLightProgressBar = findViewById(R.id.warningLightProgressBar);
            warningLightProgressBar.setVisibility(View.VISIBLE);
            warningLightStatusTextView.setVisibility(View.GONE);
            
            // Firebase'i güncelle
            Map<String, Object> updates = new HashMap<>();
            updates.put("status", 1);
            updates.put("is_alarm_acknowledged", false);
            
            deviceOutputsRef.child("output_type_2").updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    // UI güncelleme işlemi listener'lar tarafından yapılacak
                    // Buton durumlarını güncelle
                    onButton.setEnabled(false);
                    offButton.setEnabled(true);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(onButton, false);
                    updateButtonAppearance(offButton, true);
                    
                    // Alarm log kaydını ekle
                    Map<String, Object> logData = new HashMap<>();
                    logData.put("alarm_description", "Kullanıcı tarafından manuel olarak alarm kontrolü açıldı");
                    logData.put("alarm_type", "Tehlike");
                    logData.put("created_at", System.currentTimeMillis() / 1000); // Unix timestamp (saniye)
                    logData.put("device_id", "device1");
                    logData.put("sensor_id", "sensor_type_2");
                    logData.put("threshold", 0);
                    logData.put("triggered_output", "output_type_2");
                    logData.put("value", 0);
                    
                    alarmsLogRef.push().setValue(logData)
                        .addOnSuccessListener(logSuccess -> {
                            CustomToast.showSuccess(AlarmControlActivity.this, "İşlem kaydedildi");
                        })
                        .addOnFailureListener(logError -> {
                            CustomToast.showError(AlarmControlActivity.this, "Log kaydı başarısız: " + logError.getMessage());
                        });
                })
                .addOnFailureListener(e -> {
                    // Hata durumunda kullanıcıyı bilgilendir
                    CustomToast.showError(AlarmControlActivity.this, "İşlem başarısız: " + e.getMessage());
                    warningLightProgressBar.setVisibility(View.GONE);
                    warningLightStatusTextView.setVisibility(View.VISIBLE);
                });
        });

        offButton.setOnClickListener(v -> {
            if (isWarningLightFirstAlarm || (isMechanicalIntervention && !isUserIntervention)) {
                CustomToast.showError(this, isWarningLightFirstAlarm ? 
                    "Lütfen alarmın çalmasını bekleyiniz" : 
                    "Şu an sistem otomatik modunda");
                return;
            }
            // Progress bar göster
            View warningLightProgressBar = findViewById(R.id.warningLightProgressBar);
            warningLightProgressBar.setVisibility(View.VISIBLE);
            warningLightStatusTextView.setVisibility(View.GONE);
            
            // Mevcut zaman
            String currentDateTime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).format(new Date());
            
            // Firebase'i güncelle
            Map<String, Object> updates = new HashMap<>();
            updates.put("status", 0);
            updates.put("is_alarm_acknowledged", true);
            updates.put("stopped_date", currentDateTime);
            
            deviceOutputsRef.child("output_type_2").updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    // UI güncelleme işlemi listener'lar tarafından yapılacak
                    // Buton durumlarını güncelle
                    onButton.setEnabled(true);
                    offButton.setEnabled(false);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(onButton, true);
                    updateButtonAppearance(offButton, false);
                    
                    // Alarm log kaydını ekle
                    Map<String, Object> logData = new HashMap<>();
                    logData.put("alarm_description", "Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı");
                    logData.put("alarm_type", "Tehlike");
                    logData.put("created_at", System.currentTimeMillis() / 1000); // Unix timestamp (saniye)
                    logData.put("device_id", "device1");
                    logData.put("sensor_id", "sensor_type_2");
                    logData.put("threshold", 0);
                    logData.put("triggered_output", "output_type_2");
                    logData.put("value", 0);
                    
                    alarmsLogRef.push().setValue(logData)
                        .addOnSuccessListener(logSuccess -> {
                            CustomToast.showSuccess(AlarmControlActivity.this, "İşlem kaydedildi");
                        })
                        .addOnFailureListener(logError -> {
                            CustomToast.showError(AlarmControlActivity.this, "Log kaydı başarısız: " + logError.getMessage());
                        });
                })
                .addOnFailureListener(e -> {
                    // Hata durumunda kullanıcıyı bilgilendir
                    CustomToast.showError(AlarmControlActivity.this, "İşlem başarısız: " + e.getMessage());
                    warningLightProgressBar.setVisibility(View.GONE);
                    warningLightStatusTextView.setVisibility(View.VISIBLE);
                });
        });
        
        // Başlangıçta buton durumlarını ayarla
        if (warningLightStatusTextView.getText().toString().equals("Açık")) {
            onButton.setEnabled(false);
            offButton.setEnabled(true);
        } else {
            onButton.setEnabled(true);
            offButton.setEnabled(false);
        }
    }

    private void setupWindowControls(MaterialButton openButton, MaterialButton closeButton) {
        openButton.setOnClickListener(v -> {
            if (isWindowFirstAlarm || (isMechanicalIntervention && !isUserIntervention)) {
                CustomToast.showError(this, isWindowFirstAlarm ? 
                    "Lütfen alarmın çalmasını bekleyiniz" : 
                    "Şu an sistem otomatik modunda");
                return;
            }
            // Progress bar göster
            View windowProgressBar = findViewById(R.id.windowProgressBar);
            windowProgressBar.setVisibility(View.VISIBLE);
            windowStatusTextView.setVisibility(View.GONE);
            
            // Firebase'i güncelle
            Map<String, Object> updates = new HashMap<>();
            updates.put("status", 1);
            updates.put("is_alarm_acknowledged", false);
            
            deviceOutputsRef.child("output_type_3").updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    // UI güncelleme işlemi listener'lar tarafından yapılacak
                    // Buton durumlarını güncelle
                    openButton.setEnabled(false);
                    closeButton.setEnabled(true);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(openButton, false);
                    updateButtonAppearance(closeButton, true);
                    
                    // Alarm log kaydını ekle
                    Map<String, Object> logData = new HashMap<>();
                    logData.put("alarm_description", "Kullanıcı tarafından manuel olarak alarm kontrolü açıldı");
                    logData.put("alarm_type", "Tehlike");
                    logData.put("created_at", System.currentTimeMillis() / 1000); // Unix timestamp (saniye)
                    logData.put("device_id", "device1");
                    logData.put("sensor_id", "sensor_type_3");
                    logData.put("threshold", 0);
                    logData.put("triggered_output", "output_type_3");
                    logData.put("value", 0);
                    
                    alarmsLogRef.push().setValue(logData)
                        .addOnSuccessListener(logSuccess -> {
                            CustomToast.showSuccess(AlarmControlActivity.this, "İşlem kaydedildi");
                        })
                        .addOnFailureListener(logError -> {
                            CustomToast.showError(AlarmControlActivity.this, "Log kaydı başarısız: " + logError.getMessage());
                        });
                })
                .addOnFailureListener(e -> {
                    // Hata durumunda kullanıcıyı bilgilendir
                    CustomToast.showError(AlarmControlActivity.this, "İşlem başarısız: " + e.getMessage());
                    windowProgressBar.setVisibility(View.GONE);
                    windowStatusTextView.setVisibility(View.VISIBLE);
                });
        });

        closeButton.setOnClickListener(v -> {
            if (isWindowFirstAlarm || (isMechanicalIntervention && !isUserIntervention)) {
                CustomToast.showError(this, isWindowFirstAlarm ? 
                    "Lütfen alarmın çalmasını bekleyiniz" : 
                    "Şu an sistem otomatik modunda");
                return;
            }
            // Progress bar göster
            View windowProgressBar = findViewById(R.id.windowProgressBar);
            windowProgressBar.setVisibility(View.VISIBLE);
            windowStatusTextView.setVisibility(View.GONE);
            
            // Mevcut zaman
            String currentDateTime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).format(new Date());
            
            // Firebase'i güncelle
            Map<String, Object> updates = new HashMap<>();
            updates.put("status", 0);
            updates.put("is_alarm_acknowledged", true);
            updates.put("stopped_date", currentDateTime);
            
            deviceOutputsRef.child("output_type_3").updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    // UI güncelleme işlemi listener'lar tarafından yapılacak
                    // Buton durumlarını güncelle
                    openButton.setEnabled(true);
                    closeButton.setEnabled(false);
                    
                    // Butonların görünümünü güncelle
                    updateButtonAppearance(openButton, true);
                    updateButtonAppearance(closeButton, false);
                    
                    // Alarm log kaydını ekle
                    Map<String, Object> logData = new HashMap<>();
                    logData.put("alarm_description", "Kullanıcı tarafından manuel olarak alarm kontrolü kapatıldı");
                    logData.put("alarm_type", "Tehlike");
                    logData.put("created_at", System.currentTimeMillis() / 1000); // Unix timestamp (saniye)
                    logData.put("device_id", "device1");
                    logData.put("sensor_id", "sensor_type_3");
                    logData.put("threshold", 0);
                    logData.put("triggered_output", "output_type_3");
                    logData.put("value", 0);
                    
                    alarmsLogRef.push().setValue(logData)
                        .addOnSuccessListener(logSuccess -> {
                            CustomToast.showSuccess(AlarmControlActivity.this, "İşlem kaydedildi");
                        })
                        .addOnFailureListener(logError -> {
                            CustomToast.showError(AlarmControlActivity.this, "Log kaydı başarısız: " + logError.getMessage());
                        });
                })
                .addOnFailureListener(e -> {
                    // Hata durumunda kullanıcıyı bilgilendir
                    CustomToast.showError(AlarmControlActivity.this, "İşlem başarısız: " + e.getMessage());
                    windowProgressBar.setVisibility(View.GONE);
                    windowStatusTextView.setVisibility(View.VISIBLE);
                });
        });
        
        // Başlangıçta buton durumlarını ayarla
        if (windowStatusTextView.getText().toString().equals("Açık")) {
            openButton.setEnabled(false);
            closeButton.setEnabled(true);
        } else {
            openButton.setEnabled(true);
            closeButton.setEnabled(false);
        }
    }

    private void updateButtonAppearance(MaterialButton button, boolean isEnabled) {
        if (isEnabled) {
            button.setAlpha(1.0f);
            button.setClickable(true);
            button.setEnabled(true);
        } else {
            button.setAlpha(0.5f);
            button.setClickable(true);
            button.setEnabled(false);
        }
    }

    private void updateStatusText(TextView statusText, boolean isOn) {
        if (isOn) {
            statusText.setText("Açık");
            statusText.setTextColor(getResources().getColor(android.R.color.holo_green_dark));
        } else {
            statusText.setText("Kapalı");
            statusText.setTextColor(getResources().getColor(android.R.color.holo_red_dark));
        }

        // Durum değişikliği animasyonu
        Animation pulse = AnimationUtils.loadAnimation(this, R.anim.pulse);
        statusText.startAnimation(pulse);
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        // Listener'ları temizle
        if (deviceOutputsRef != null) {
            if (buzzerListener != null) {
                deviceOutputsRef.child("output_type_1").removeEventListener(buzzerListener);
            }
            if (ledListener != null) {
                deviceOutputsRef.child("output_type_2").removeEventListener(ledListener);
            }
            if (servoListener != null) {
                deviceOutputsRef.child("output_type_3").removeEventListener(servoListener);
            }
        }
        if (systemControlRef != null && systemControlListener != null) {
            systemControlRef.removeEventListener(systemControlListener);
        }
    }
} 