package com.example.finalapp;

import android.annotation.SuppressLint;
import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.Uri;
import android.os.Bundle;
import android.os.Build;
import android.os.Handler;
import android.util.Log;
import android.widget.TextView;
import android.os.Vibrator;
import android.os.VibrationEffect;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;
import android.widget.ProgressBar;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.NotificationCompat;
import android.media.AudioAttributes;
import android.media.SoundPool;

import com.google.firebase.database.DataSnapshot;
import com.google.firebase.database.DatabaseError;
import com.google.firebase.database.DatabaseReference;
import com.google.firebase.database.FirebaseDatabase;
import com.google.firebase.database.ValueEventListener;
import androidx.core.content.ContextCompat;
import android.content.SharedPreferences;
import androidx.annotation.NonNull;
import android.content.pm.PackageManager;
import androidx.core.app.ActivityCompat;

import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.database.ServerValue;

import java.util.HashMap;
import java.util.Map;
import java.util.Locale;
import java.text.SimpleDateFormat;
import java.util.Date;

public class MainActivity extends AppCompatActivity {
    private static final String CHANNEL_ID = "danger_channel";
    private static final String CHANNEL_NAME = "Tehlike Bildirimleri";
    private static final String CHANNEL_DESC = "Yüksek CO seviyeleri için önemli bildirimler";
    private static final int NOTIFICATION_ID = 1;
    private static final int CO_NOTIFICATION_ID = 1;
    private static final int HUMIDITY_NOTIFICATION_ID = 2;
    private static final int TEMPERATURE_NOTIFICATION_ID = 3;
    private static final String PREFS_NAME = "AppPreferences";
    private static final String NOTIFICATION_FLAG = "notification_flag";
    private static final int NOTIFICATION_PERMISSION_REQUEST_CODE = 1;

    // Sistem kontrol değerleri için değişkenler
    private boolean phoneVibrateEnabled = false;
    private boolean silentModeEnabled = false;

    // Tanımladığımız aksiyon string'i
    private static final String ACTION_STOP_ALARM = "com.example.finalapp.ACTION_STOP_ALARM";

    private SoundPool soundPool;
    private int alarmSoundId;
    private boolean isAlarmPlaying = false;

    private DatabaseReference sensorDataRef;
    private ValueEventListener sensorDataListener;

    private TextView coTextView, humidityTextView, temperatureTextView, timestampTextView;
    private TextView textView5, textView7, textView3, textView9, moodId, moodId2;
    private CustomProgressBar coProgressBar, temperatureProgressBar, humidityProgressBar;
    private android.widget.ImageView emojiImage;

    private static final String INSTANCE_STATE_CO = "instance_state_co";
    private static final String INSTANCE_STATE_HUMIDITY = "instance_state_humidity";
    private static final String INSTANCE_STATE_TEMPERATURE = "instance_state_temperature";
    private static final String INSTANCE_STATE_TIMESTAMP = "instance_state_timestamp";
    private static final String INSTANCE_STATE_ALARM_PLAYING = "instance_state_alarm_playing";
    
    private boolean isFirstLoad = true;
    private boolean isActivityResumed = false;

    private SharedPreferences sensorPrefs;
    private int lastCoValue = 0;

    private Button logoutButton;
    private Button stopAlarmButton;
    private FirebaseAuth mAuth;

    // Alarm states for each sensor
    private boolean isCoAlarmPlaying = false;
    private boolean isHumidityAlarmPlaying = false;
    private boolean isTemperatureAlarmPlaying = false;
    
    // İlk yükleme sırasında bildirim göndermemek için bayrak
    private boolean isInitialThresholdCheck = true;

    // Alarm durumlarını SharedPreferences'ta saklamak için anahtarlar
    private static final String PREFS_CO_ALARM = "co_alarm_state";
    private static final String PREFS_HUMIDITY_ALARM = "humidity_alarm_state";
    private static final String PREFS_TEMP_ALARM = "temperature_alarm_state";
    private static final String PREFS_LAST_CHECK_TIME = "last_threshold_check_time";

    // Threshold değerlerini dinamik olarak tutacak değişkenler
    private double CO2_THRESHOLD = 1000.0;
    private double TEMP_THRESHOLD = 30.0;
    private double HUMIDITY_THRESHOLD = 60.0;

    // Firebase'den gelen verileri tutacak değişkenler
    private Double temperature = null;
    private Double humidity = null;
    private Double coLevel = null;
    private String timestamp = null;

    // Firebase threshold referansı
    private DatabaseReference thresholdRef;

    @SuppressLint("NewApi")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        try {
            setContentView(R.layout.activity_main);

            // Initialize SharedPreferences
            sensorPrefs = getSharedPreferences("SensorPrefs", MODE_PRIVATE);
            
            // İlk yükleme sırasında eşik değeri kontrolü
            isInitialThresholdCheck = true;

            // Initialize Firebase Auth
            mAuth = FirebaseAuth.getInstance();

            // Initialize views
            initializeViews();
            
            // Initialize listeners and other components
            setupListeners();
            createNotificationChannel();
            setupSoundPool();
            setupBroadcastReceiver();

            // Check notification permission for Android 13+
            checkNotificationPermission();

            // Load data
            if (savedInstanceState != null) {
                restoreInstanceState(savedInstanceState);
            } else {
                loadLastKnownValues();
                loadThresholdValues(); // Eşik değerlerini yükle
            }

            // Setup Firebase listener last to ensure all UI components are ready
            setupFirebaseListener();

            // Apply animations
            applyAnimations();
        } catch (Exception e) {
            Log.e("MainActivity", "Error in onCreate: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void initializeViews() {
        try {
            coTextView = findViewById(R.id.coTextView);
            humidityTextView = findViewById(R.id.humidityTextView);
            temperatureTextView = findViewById(R.id.temperatureTextView);
            timestampTextView = findViewById(R.id.timestampTextView);
            
            // Initialize progress bars
            coProgressBar = findViewById(R.id.coProgressBar);
            temperatureProgressBar = findViewById(R.id.temperatureProgressBar);
            humidityProgressBar = findViewById(R.id.humidityProgressBar);

            // Set max values for progress bars
            coProgressBar.setMaxValue(1000); // Karbonmonoksit değeri 400'den 1000'e çıkarıldı
            temperatureProgressBar.setMaxValue(100); // Sıcaklık değeri 50'den 100'e çıkarıldı
            humidityProgressBar.setMaxValue(100);
            
            // Set initial threshold values
            coProgressBar.setThresholdValue((float) CO2_THRESHOLD);
            temperatureProgressBar.setThresholdValue((float) TEMP_THRESHOLD);
            humidityProgressBar.setThresholdValue((float) HUMIDITY_THRESHOLD);
            
            // Setup threshold change listeners
            setupThresholdChangeListeners();
            
            // Initialize other UI elements
            textView5 = findViewById(R.id.textView5);
            textView7 = findViewById(R.id.textView7);
            textView3 = findViewById(R.id.textView3);
            textView9 = findViewById(R.id.textView9);
            moodId = findViewById(R.id.moodId);
            moodId2 = findViewById(R.id.moodId2);
            emojiImage = findViewById(R.id.emojiImage);
            logoutButton = findViewById(R.id.logoutButton);
            stopAlarmButton = findViewById(R.id.stopAlarm);
            
            // Alarm kontrol butonunu ayarla
            com.google.android.material.floatingactionbutton.FloatingActionButton alarmControlButton = findViewById(R.id.alarmControlButton);
            if (alarmControlButton != null) {
                alarmControlButton.setOnClickListener(v -> {
                    // Apply button animation
                    Animation buttonAnimation = AnimationUtils.loadAnimation(this, R.anim.button_scale);
                    alarmControlButton.startAnimation(buttonAnimation);
                    
                    Intent intent = new Intent(MainActivity.this, AlarmControlActivity.class);
                    startActivity(intent);
                    overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
                });
            }
            
            // Set initial state of stopAlarm button
            updateStopAlarmButtonState(false);
        } catch (Exception e) {
            Log.e("MainActivity", "Error initializing views: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void applyAnimations() {
        try {
            // Apply animations to progress bars with sequential delay
            Animation fadeIn = AnimationUtils.loadAnimation(this, R.anim.fade_in);
            
            // CO Progress Bar animation
            coProgressBar.startAnimation(fadeIn);
            
            // Temperature Progress Bar animation with delay
            Animation tempAnim = AnimationUtils.loadAnimation(this, R.anim.fade_in);
            tempAnim.setStartOffset(200); // 200ms delay
            temperatureProgressBar.startAnimation(tempAnim);
            
            // Humidity Progress Bar animation with longer delay
            Animation humidityAnim = AnimationUtils.loadAnimation(this, R.anim.fade_in);
            humidityAnim.setStartOffset(400); // 400ms delay
            humidityProgressBar.startAnimation(humidityAnim);
            
            // Emoji animation
            if (emojiImage != null) {
                Animation emojiAnimation = AnimationUtils.loadAnimation(this, R.anim.bounce);
                emojiAnimation.setStartOffset(600); // 600ms delay
                emojiImage.startAnimation(emojiAnimation);
            }
            
            // Mood text animations
            if (moodId != null) {
                Animation moodAnimation = AnimationUtils.loadAnimation(this, R.anim.fade_in_rotate);
                moodAnimation.setStartOffset(700); // 700ms delay
                moodId.startAnimation(moodAnimation);
            }
            
            if (moodId2 != null) {
                Animation mood2Animation = AnimationUtils.loadAnimation(this, R.anim.fade_in);
                mood2Animation.setStartOffset(800); // 800ms delay
                moodId2.startAnimation(mood2Animation);
            }
            
            // Animate alarm control button and other buttons
            com.google.android.material.floatingactionbutton.FloatingActionButton alarmControlButton = findViewById(R.id.alarmControlButton);
            if (alarmControlButton != null) {
                Animation fabAnim = AnimationUtils.loadAnimation(this, R.anim.card_enter);
                fabAnim.setStartOffset(900); // 900ms delay
                alarmControlButton.startAnimation(fabAnim);
            }
            
            if (logoutButton != null) {
                Animation buttonAnim = AnimationUtils.loadAnimation(this, R.anim.fade_in);
                buttonAnim.setStartOffset(1000); // 1000ms delay
                logoutButton.startAnimation(buttonAnim);
            }
            
            if (stopAlarmButton != null) {
                Animation buttonAnim = AnimationUtils.loadAnimation(this, R.anim.fade_in);
                buttonAnim.setStartOffset(1100); // 1100ms delay
                stopAlarmButton.startAnimation(buttonAnim);
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error applying animations: " + e.getMessage());
        }
    }

    private void setupListeners() {
        try {
            // Stop Alarm button listener
            if (stopAlarmButton != null) {
                stopAlarmButton.setOnClickListener(v -> {
                    // Apply button animation
                    Animation buttonAnimation = AnimationUtils.loadAnimation(this, R.anim.button_scale);
                    stopAlarmButton.startAnimation(buttonAnimation);
                    
                    // Tüm alarm durumlarını sıfırla
                    isAlarmPlaying = false;
                    isCoAlarmPlaying = false;
                    isHumidityAlarmPlaying = false;
                    isTemperatureAlarmPlaying = false;

                    // Ses ve titreşimi durdur
                    if (soundPool != null) {
                        soundPool.stop(alarmSoundId);
                        soundPool.autoPause();
                    }
                    
                    Vibrator vibrator = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
                    if (vibrator != null) {
                        vibrator.cancel();
                    }

                    // Tüm bildirimleri iptal et
                    NotificationManager notificationManager = 
                        (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
                    if (notificationManager != null) {
                        notificationManager.cancelAll();
                    }

                    // Tüm device_outputs'ların is_alarm_acknowledged değerini true yap
                    updateAllDeviceOutputsAcknowledged();

                    // Butonun görünümünü güncelle
                    stopAlarmButton.setBackgroundResource(R.drawable.alarm);
                    stopAlarmButton.setEnabled(false);
                });
            }

            // Logout button listener
            if (logoutButton != null) {
                logoutButton.setOnClickListener(v -> {
                    // Apply button animation
                    Animation buttonAnimation = AnimationUtils.loadAnimation(this, R.anim.button_scale);
                    logoutButton.startAnimation(buttonAnimation);
                    
                    logoutUser();
                });
            }

            // Logo button listener
            Button logoButton = findViewById(R.id.button);
            if (logoButton != null) {
                logoButton.setOnClickListener(v -> {
                    try {
                        // Apply button animation
                        Animation buttonAnimation = AnimationUtils.loadAnimation(this, R.anim.button_scale);
                        logoButton.startAnimation(buttonAnimation);
                        
                        Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://www.google.com"));
                        startActivity(browserIntent);
                    } catch (Exception e) {
                        Log.e("MainActivity", "Google'a yönlendirme hatası: " + e.getMessage());
                        CustomToast.showError(MainActivity.this, "Web sayfası açılamadı");
                    }
                });
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error setting up listeners: " + e.getMessage());
            e.printStackTrace();
        }
    }

    // Tüm device_outputs'lara is_alarm_acknowledged=true ayarı yapar
    private void updateAllDeviceOutputsAcknowledged() {
        DatabaseReference deviceOutputsRef = FirebaseDatabase.getInstance().getReference("device_outputs");
        
        // Güncelleme için map
        Map<String, Object> updates = new HashMap<>();
        updates.put("is_alarm_acknowledged", true);
        
        // Tüm çıkış tipleri için güncelleme yap
        String[] outputTypes = {"output_type_1", "output_type_2", "output_type_3"};
        
        for (String outputType : outputTypes) {
            // Lambda içinde kullanılacak değişkeni final olarak tanımla
            final String finalOutputType = outputType;
            
            deviceOutputsRef.child(outputType).updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    Log.d("MainActivity", "Device output " + finalOutputType + " is_alarm_acknowledged updated to true");
                })
                .addOnFailureListener(e -> {
                    Log.e("MainActivity", "Error updating device output: " + e.getMessage());
                });
        }
    }

    private void checkNotificationPermission() {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                if (ContextCompat.checkSelfPermission(this, android.Manifest.permission.POST_NOTIFICATIONS)
                        != PackageManager.PERMISSION_GRANTED) {
                    ActivityCompat.requestPermissions(this,
                            new String[]{android.Manifest.permission.POST_NOTIFICATIONS},
                            NOTIFICATION_PERMISSION_REQUEST_CODE);
                }
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error checking notification permission: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void setupSoundPool() {
        AudioAttributes audioAttributes = new AudioAttributes.Builder()
                .setUsage(AudioAttributes.USAGE_NOTIFICATION)
                .setContentType(AudioAttributes.CONTENT_TYPE_MUSIC)
                .build();

        soundPool = new SoundPool.Builder()
                .setMaxStreams(1)
                .setAudioAttributes(audioAttributes)
                .build();

        alarmSoundId = soundPool.load(this, R.raw.alarm_sound, 1);
    }

    private void setupBroadcastReceiver() {
        IntentFilter filter = new IntentFilter(ACTION_STOP_ALARM);
        ContextCompat.registerReceiver(
                this,
                alarmStopReceiver,
                filter,
                ContextCompat.RECEIVER_NOT_EXPORTED
        );
    }

    private void setupFirebaseListener() {
        DatabaseReference devicesRef = FirebaseDatabase.getInstance().getReference();
        Log.d("MainActivity", "Setting up Firebase listener...");

        devicesRef.addValueEventListener(new ValueEventListener() {
            @Override
            public void onDataChange(@NonNull DataSnapshot dataSnapshot) {
                if (dataSnapshot.exists()) {
                    try {
                        Log.d("MainActivity", "Firebase data received. Checking device_sensors...");
                        
                        // System control verilerini al
                        DataSnapshot systemControlSnapshot = dataSnapshot.child("system_control");
                        if (systemControlSnapshot.exists()) {
                            Log.d("MainActivity", "System control data exists: " + systemControlSnapshot.getValue());

                            // phone_vibrate ve silent_mode değerlerini al
                            if (systemControlSnapshot.child("phone_vibrate").exists()) {
                                phoneVibrateEnabled = Boolean.TRUE.equals(systemControlSnapshot.child("phone_vibrate").getValue(Boolean.class));
                                Log.d("MainActivity", "Phone vibrate mode: " + phoneVibrateEnabled);
                            }
                            
                            if (systemControlSnapshot.child("silent_mode").exists()) {
                                silentModeEnabled = Boolean.TRUE.equals(systemControlSnapshot.child("silent_mode").getValue(Boolean.class));
                                Log.d("MainActivity", "Silent mode: " + silentModeEnabled);
                            }
                        }
                        
                        // Threshold değerlerini al ve güncelle
                        DataSnapshot deviceSensorsSnapshot = dataSnapshot.child("device_sensors");
                        if (deviceSensorsSnapshot.exists()) {
                            Log.d("MainActivity", "Device sensors data exists: " + deviceSensorsSnapshot.getValue());

                            // Sıcaklık threshold
                            DataSnapshot tempSensor = deviceSensorsSnapshot.child("sensor_type_1");
                            if (tempSensor.exists()) {
                                String tempLimit = tempSensor.child("limits").getValue(String.class);
                                if (tempLimit != null) {
                                    TEMP_THRESHOLD = Double.parseDouble(tempLimit);
                                    Log.d("MainActivity", "Temperature threshold updated from Firebase: " + TEMP_THRESHOLD);
                                }
                            }

                            // Nem threshold
                            DataSnapshot humiditySensor = deviceSensorsSnapshot.child("sensor_type_2");
                            if (humiditySensor.exists()) {
                                String humidityLimit = humiditySensor.child("limits").getValue(String.class);
                                if (humidityLimit != null) {
                                    HUMIDITY_THRESHOLD = Double.parseDouble(humidityLimit);
                                    Log.d("MainActivity", "Humidity threshold updated from Firebase: " + HUMIDITY_THRESHOLD);
                                }
                            }

                            // CO threshold
                            DataSnapshot coSensor = deviceSensorsSnapshot.child("sensor_type_3");
                            if (coSensor.exists()) {
                                String coLimit = coSensor.child("limits").getValue(String.class);
                                if (coLimit != null) {
                                    CO2_THRESHOLD = Double.parseDouble(coLimit);
                                    Log.d("MainActivity", "CO2 threshold updated from Firebase: " + CO2_THRESHOLD);
                                }
                            }
                        } else {
                            Log.d("MainActivity", "No device_sensors data found");
                        }

                        // Latest readings'den sensör verilerini al
                        DataSnapshot measurementsSnapshot = dataSnapshot.child("measurements");
                        Log.d("MainActivity", "Checking measurements data...");
                        
                        if (measurementsSnapshot.exists()) {
                            Log.d("MainActivity", "Measurements data exists: " + measurementsSnapshot.getValue());
                            
                            // En son ölçümleri bul
                            Double latestTemp = null;
                            Double latestHumidity = null;
                            Double latestCO = null;
                            String latestTempTimestamp = null;
                            String latestHumidityTimestamp = null;
                            String latestCOTimestamp = null;

                            // Tüm ölçümleri döngüyle kontrol et
                            for (DataSnapshot measurement : measurementsSnapshot.getChildren()) {
                                // Sıcaklık ölçümü
                                DataSnapshot tempMeasurement = measurement.child("sicaklik_measurements");
                                if (tempMeasurement.exists()) {
                                    Double tempValue = tempMeasurement.child("value").getValue(Double.class);
                                    String tempTimestamp = String.valueOf(tempMeasurement.child("timestamp").getValue());
                                    if (tempValue != null && (latestTemp == null || 
                                        Long.parseLong(tempTimestamp) > Long.parseLong(latestTempTimestamp))) {
                                        latestTemp = tempValue;
                                        latestTempTimestamp = tempTimestamp;
                                    }
                                }

                                // Nem ölçümü
                                DataSnapshot humidityMeasurement = measurement.child("nem_measurements");
                                if (humidityMeasurement.exists()) {
                                    Double humidityValue = humidityMeasurement.child("value").getValue(Double.class);
                                    String humidityTimestamp = String.valueOf(humidityMeasurement.child("timestamp").getValue());
                                    if (humidityValue != null && (latestHumidity == null || 
                                        Long.parseLong(humidityTimestamp) > Long.parseLong(latestHumidityTimestamp))) {
                                        latestHumidity = humidityValue;
                                        latestHumidityTimestamp = humidityTimestamp;
                                    }
                                }

                                // CO ölçümü
                                DataSnapshot coMeasurement = measurement.child("karbonmonoksit_measurements");
                                if (coMeasurement.exists()) {
                                    Double coValue = coMeasurement.child("value").getValue(Double.class);
                                    String coTimestamp = String.valueOf(coMeasurement.child("timestamp").getValue());
                                    if (coValue != null && (latestCO == null || 
                                        Long.parseLong(coTimestamp) > Long.parseLong(latestCOTimestamp))) {
                                        latestCO = coValue;
                                        latestCOTimestamp = coTimestamp;
                                    }
                                }
                            }

                            // Bulunan en son değerleri kaydet
                            if (latestTemp != null) {
                                temperature = latestTemp;
                                timestamp = latestTempTimestamp;
                                SharedPreferences.Editor editor = sensorPrefs.edit();
                                editor.putString("last_temperature", String.valueOf(latestTemp));
                                editor.putString("last_temperature_timestamp", latestTempTimestamp);
                                editor.apply();
                                Log.d("MainActivity", "Latest temperature: " + latestTemp + " at " + latestTempTimestamp);
                                Log.d("MainActivity", "Temperature timestamp difference: " + 
                                    (System.currentTimeMillis() - Long.parseLong(latestTempTimestamp)) / 1000 + " seconds ago");
                            }

                            if (latestHumidity != null) {
                                humidity = latestHumidity;
                                timestamp = latestHumidityTimestamp;
                                SharedPreferences.Editor editor = sensorPrefs.edit();
                                editor.putString("last_humidity", String.valueOf(latestHumidity));
                                editor.putString("last_humidity_timestamp", latestHumidityTimestamp);
                                editor.apply();
                                Log.d("MainActivity", "Latest humidity: " + latestHumidity + " at " + latestHumidityTimestamp);
                                Log.d("MainActivity", "Humidity timestamp difference: " + 
                                    (System.currentTimeMillis() - Long.parseLong(latestHumidityTimestamp)) / 1000 + " seconds ago");
                            }

                            if (latestCO != null) {
                                coLevel = latestCO;
                                timestamp = latestCOTimestamp;
                                SharedPreferences.Editor editor = sensorPrefs.edit();
                                editor.putString("last_co", String.valueOf(latestCO));
                                editor.putString("last_co_timestamp", latestCOTimestamp);
                                editor.apply();
                                Log.d("MainActivity", "Latest CO: " + latestCO + " at " + latestCOTimestamp);
                                Log.d("MainActivity", "CO timestamp difference: " + 
                                    (System.currentTimeMillis() - Long.parseLong(latestCOTimestamp)) / 1000 + " seconds ago");
                            }

                            // SharedPreferences'dan son bilinen değerleri logla
                            Log.d("MainActivity", "Last known values from SharedPreferences:");
                            Log.d("MainActivity", "Temperature: " + sensorPrefs.getString("last_temperature", "N/A") + 
                                " at " + sensorPrefs.getString("last_temperature_timestamp", "N/A"));
                            Log.d("MainActivity", "Humidity: " + sensorPrefs.getString("last_humidity", "N/A") + 
                                " at " + sensorPrefs.getString("last_humidity_timestamp", "N/A"));
                            Log.d("MainActivity", "CO: " + sensorPrefs.getString("last_co", "N/A") + 
                                " at " + sensorPrefs.getString("last_co_timestamp", "N/A"));

                            // UI güncellemeleri
                            runOnUiThread(() -> {
                                Log.d("MainActivity", "Updating UI with latest values...");
                                
                                // CO Seviyesi Güncellemesi
                                if (coLevel != null) {
                                    String coDisplay = String.format(Locale.US, "%.2f ppm", coLevel);
                                    coTextView.setText(coDisplay);
                                    coProgressBar.setCurrentValue((float) coLevel.doubleValue());
                                    coProgressBar.setThresholdValue((float) CO2_THRESHOLD);
                                    Log.d("MainActivity", "CO UI updated: " + coDisplay);
                                    
                                    // CO için zaman gösterimini güncelle
                                    String coTimestamp = sensorPrefs.getString("last_co_timestamp", "");
                                    if (!coTimestamp.isEmpty()) {
                                        try {
                                            // Unix timestamp hesaplaması
                                            long currentTime = System.currentTimeMillis() / 1000;
                                            long firebaseTime = Long.parseLong(coTimestamp);
                                            long diffInSeconds = Math.abs(currentTime - firebaseTime);
                                            long diffInMinutes = diffInSeconds / 60;
                                            long diffInHours = diffInMinutes / 60;
                                            
                                            Log.d("MainActivity", "CO timestamp difference: " + diffInSeconds + " seconds");
                                            
                                            String timeAgo;
                                            if (diffInSeconds < 300) {
                                                timeAgo = "az önce";
                                            } else if (diffInHours > 0) {
                                                timeAgo = diffInHours + " saat önce";
                                            } else {
                                                timeAgo = diffInMinutes + " dk önce";
                                            }
                                            
                                            textView7.setText(timeAgo);
                                        } catch (Exception e) {
                                            Log.e("MainActivity", "CO timestamp error: " + e.getMessage());
                                            textView7.setText("--");
                                        }
                                    }
                                }
                                
                                // Nem Seviyesi Güncellemesi
                                if (humidity != null) {
                                    String humidityDisplay = String.format(Locale.US, "%.2f %%", humidity);
                                    humidityTextView.setText(humidityDisplay);
                                    humidityProgressBar.setCurrentValue((float) humidity.doubleValue());
                                    humidityProgressBar.setThresholdValue((float) HUMIDITY_THRESHOLD);
                                    Log.d("MainActivity", "Humidity UI updated: " + humidityDisplay);
                                    
                                    // Nem için zaman gösterimini güncelle
                                    String humidityTimestamp = sensorPrefs.getString("last_humidity_timestamp", "");
                                    if (!humidityTimestamp.isEmpty()) {
                                        try {
                                            // Unix timestamp hesaplaması
                                            long currentTime = System.currentTimeMillis() / 1000;
                                            long firebaseTime = Long.parseLong(humidityTimestamp);
                                            long diffInSeconds = Math.abs(currentTime - firebaseTime);
                                            long diffInMinutes = diffInSeconds / 60;
                                            long diffInHours = diffInMinutes / 60;
                                            
                                            Log.d("MainActivity", "Humidity timestamp difference: " + diffInSeconds + " seconds");
                                            
                                            String timeAgo;
                                            if (diffInSeconds < 300) {
                                                timeAgo = "az önce";
                                            } else if (diffInHours > 0) {
                                                timeAgo = diffInHours + " saat önce";
                                            } else {
                                                timeAgo = diffInMinutes + " dk önce";
                                            }
                                            
                                            textView3.setText(timeAgo);
                                        } catch (Exception e) {
                                            Log.e("MainActivity", "Humidity timestamp error: " + e.getMessage());
                                            textView3.setText("--");
                                        }
                                    }
                                }
                                
                                // Sıcaklık Seviyesi Güncellemesi
                                if (temperature != null) {
                                    String tempDisplay = String.format(Locale.US, "%.2f °C", temperature);
                                    temperatureTextView.setText(tempDisplay);
                                    temperatureProgressBar.setCurrentValue((float) temperature.doubleValue());
                                    temperatureProgressBar.setThresholdValue((float) TEMP_THRESHOLD);
                                    Log.d("MainActivity", "Temperature UI updated: " + tempDisplay);
                                    
                                    // Sıcaklık için zaman gösterimini güncelle
                                    String tempTimestamp = sensorPrefs.getString("last_temperature_timestamp", "");
                                    if (!tempTimestamp.isEmpty()) {
                                        try {
                                            // Unix timestamp hesaplaması
                                            long currentTime = System.currentTimeMillis() / 1000;
                                            long firebaseTime = Long.parseLong(tempTimestamp);
                                            long diffInSeconds = Math.abs(currentTime - firebaseTime);
                                            long diffInMinutes = diffInSeconds / 60;
                                            long diffInHours = diffInMinutes / 60;
                                            
                                            Log.d("MainActivity", "Temperature timestamp difference: " + diffInSeconds + " seconds");
                                            
                                            String timeAgo;
                                            if (diffInSeconds < 300) {
                                                timeAgo = "az önce";
                                            } else if (diffInHours > 0) {
                                                timeAgo = diffInHours + " saat önce";
                                            } else {
                                                timeAgo = diffInMinutes + " dk önce";
                                            }
                                            
                                            textView5.setText(timeAgo);
                                        } catch (Exception e) {
                                            Log.e("MainActivity", "Temperature timestamp error: " + e.getMessage());
                                            textView5.setText("--");
                                        }
                                    }
                                }

                                // Timestamp'i formatla ve ekranda göster (global saat gösterimi)
                                if (timestamp != null && !timestamp.isEmpty()) {
                                    try {
                                        // Unix timestamp'i milisaniyeye çevir
                                        long timestampMillis = Long.parseLong(timestamp) * 1000;
                                        Date date = new Date(timestampMillis);
                                        
                                        SimpleDateFormat outputFormat = new SimpleDateFormat("HH:mm:ss", Locale.getDefault());
                                        String timeStr = outputFormat.format(date);
                                        Log.d("MainActivity", "Formatted timestamp: " + timeStr);
                                        timestampTextView.setText(timeStr);
                                        
                                    } catch (Exception e) {
                                        Log.e("MainActivity", "Timestamp parse error: " + e.getMessage());
                                        timestampTextView.setText("--:--:--");
                                    }
                                } else {
                                    Log.d("MainActivity", "No timestamp available");
                                    timestampTextView.setText("--:--:--");
                                }

                                // Durum güncellemesi
                                updateMoodStatus();
                            });
                        } else {
                            Log.d("MainActivity", "No measurements data found");
                        }
                    } catch (Exception e) {
                        Log.e("MainActivity", "Error processing Firebase data: " + e.getMessage());
                        e.printStackTrace();
                    }
                } else {
                    Log.d("MainActivity", "No data exists in Firebase");
                }
            }

            @Override
            public void onCancelled(@NonNull DatabaseError error) {
                Log.e("MainActivity", "Firebase listener cancelled", error.toException());
            }
        });
    }
    

    private void checkThreshold(String sensorType, double value, double threshold) {
        Log.d("MainActivity", "Checking threshold for " + sensorType + ": value=" + value + ", threshold=" + threshold);
        Log.d("MainActivity", "Current alarm states - CO: " + isCoAlarmPlaying + 
            ", Humidity: " + isHumidityAlarmPlaying + 
            ", Temperature: " + isTemperatureAlarmPlaying);
            
        // İlk yükleme kontrolü sırasında bildirimleri engelle
        boolean shouldSendNotifications = !isInitialThresholdCheck;
        
        // Son kontrol zamanını güncelle
        SharedPreferences.Editor editor = sensorPrefs.edit();
        editor.putLong(PREFS_LAST_CHECK_TIME, System.currentTimeMillis());
        editor.apply();
        
        if (value > threshold) {
            Log.d("MainActivity", sensorType + " exceeded threshold! Value: " + value + " > " + threshold);
            
            // Output kontrolü yapılacak outputType değişkenini belirle
            final String outputType;
            final int notificationId;
            
            switch (sensorType) {
                case "Karbonmonoksit":
                    outputType = "output_type_3";  // CO sensörü için output tipi
                    notificationId = CO_NOTIFICATION_ID;
                    break;
                case "Nem":
                    outputType = "output_type_2";  // Nem sensörü için output tipi
                    notificationId = HUMIDITY_NOTIFICATION_ID;
                    break;
                case "Sıcaklık":
                    outputType = "output_type_1";  // Sıcaklık sensörü için output tipi
                    notificationId = TEMPERATURE_NOTIFICATION_ID;
                    break;
                default:
                    return;
            }
            
            // Firebase'den output durumunu kontrol et
            DatabaseReference deviceOutputsRef = FirebaseDatabase.getInstance().getReference("device_outputs");
            deviceOutputsRef.child(outputType).addListenerForSingleValueEvent(new ValueEventListener() {
                @Override
                public void onDataChange(@NonNull DataSnapshot snapshot) {
                    if (snapshot.exists()) {
                        // MEVCUT ALARM SİSTEMİ İÇİN KONTROLLER
                        // Status ve is_alarm_acknowledged durumlarını kontrol et
                        Integer status = snapshot.child("status").getValue(Integer.class);
                        Boolean isAlarmAcknowledged = snapshot.child("is_alarm_acknowledged").getValue(Boolean.class);
                        
                        // Yalnızca status=1 VE is_alarm_acknowledged=false ise bildirim gönder
                        boolean shouldSendAlarm = (status != null && status == 1) && 
                                                (isAlarmAcknowledged != null && !isAlarmAcknowledged);
                                                
                        // YENİ SESSİZ BİLDİRİM SİSTEMİ İÇİN KONTROLLER
                        // first_alarm durumunu kontrol et
                        Boolean firstAlarm = snapshot.child("first_alarm").getValue(Boolean.class);
                        boolean isFirstAlarmFalse = (firstAlarm != null && !firstAlarm);
                        
                        // Sessiz bildirim kontrolü
                        boolean isSilentNotification = phoneVibrateEnabled && silentModeEnabled && 
                                                      isFirstAlarmFalse && (isAlarmAcknowledged != null && !isAlarmAcknowledged);
                        
                        Log.d("MainActivity", sensorType + " output check - status: " + status + 
                              ", is_alarm_acknowledged: " + isAlarmAcknowledged + 
                              ", shouldSendAlarm: " + shouldSendAlarm +
                              ", silent mode conditions: vibrate=" + phoneVibrateEnabled + 
                              ", silent=" + silentModeEnabled + 
                              ", firstAlarm=" + firstAlarm +
                              ", isSilentNotification=" + isSilentNotification);
                        
                        if (shouldSendAlarm) {
                            // Normal alarm bildirim koşullarını kontrol et
                            handleAlarmForSensor(sensorType, value, shouldSendNotifications, notificationId, isSilentNotification);
                        } else {
                            Log.d("MainActivity", "Normal alarm skipped for " + sensorType + 
                                  " because status=" + status + " or is_alarm_acknowledged=" + isAlarmAcknowledged);
                            
                            // Eğer sadece sessiz bildirim kriterleri sağlanıyorsa
                            if (isSilentNotification && shouldSendNotifications) {
                                Log.d("MainActivity", "Sending silent notification for " + sensorType);
                                // Sessiz bildirimi gönder ama alarm çalmadan
                                handleSilentNotification(sensorType, value, notificationId);
                            }
                        }
                    } else {
                        // Output verisi yoksa, her ihtimale karşı normal kontrol yap
                        handleAlarmForSensor(sensorType, value, shouldSendNotifications, notificationId, false);
                    }
                }
                
                @Override
                public void onCancelled(@NonNull DatabaseError error) {
                    Log.e("MainActivity", "Firebase error checking output status: " + error.getMessage());
                    // Hata durumunda, en azından normal kontrol yap
                    handleAlarmForSensor(sensorType, value, shouldSendNotifications, notificationId, false);
                }
            });
        } else {
            Log.d("MainActivity", sensorType + " within safe limits. Value: " + value + " <= " + threshold);
            switch (sensorType) {
                case "Karbonmonoksit":
                    coProgressBar.setCurrentValue((float) value);
                    coProgressBar.setThresholdValue((float) threshold);
                    coTextView.setText(String.format(Locale.US, "%.2f ppm", value));
                    if (isCoAlarmPlaying) {
                        Log.d("MainActivity", "CO value back to normal, stopping alarm");
                        stopAlarmAndVibrate();
                        cancelNotification(CO_NOTIFICATION_ID);
                        isCoAlarmPlaying = false;
                        
                        // Alarm durumunu SharedPreferences'a kaydet
                        editor = sensorPrefs.edit();
                        editor.putBoolean(PREFS_CO_ALARM, false);
                        editor.apply();
                    }
                    break;
                case "Nem":
                    humidityProgressBar.setCurrentValue((float) value);
                    humidityProgressBar.setThresholdValue((float) threshold);
                    humidityTextView.setText(String.format(Locale.US, "%.2f %%", value));
                    if (isHumidityAlarmPlaying) {
                        Log.d("MainActivity", "Humidity value back to normal, stopping alarm");
                        stopAlarmAndVibrate();
                        cancelNotification(HUMIDITY_NOTIFICATION_ID);
                        isHumidityAlarmPlaying = false;
                        
                        // Alarm durumunu SharedPreferences'a kaydet
                        editor = sensorPrefs.edit();
                        editor.putBoolean(PREFS_HUMIDITY_ALARM, false);
                        editor.apply();
                    }
                    break;
                case "Sıcaklık":
                    temperatureProgressBar.setCurrentValue((float) value);
                    temperatureProgressBar.setThresholdValue((float) threshold);
                    temperatureTextView.setText(String.format(Locale.US, "%.2f °C", value));
                    if (isTemperatureAlarmPlaying) {
                        Log.d("MainActivity", "Temperature value back to normal, stopping alarm");
                        stopAlarmAndVibrate();
                        cancelNotification(TEMPERATURE_NOTIFICATION_ID);
                        isTemperatureAlarmPlaying = false;
                        
                        // Alarm durumunu SharedPreferences'a kaydet
                        editor = sensorPrefs.edit();
                        editor.putBoolean(PREFS_TEMP_ALARM, false);
                        editor.apply();
                    }
                    break;
            }
        }
    }
    
    // Sensor tipine göre alarm işleme metodu
    private void handleAlarmForSensor(String sensorType, double value, boolean shouldSendNotifications, int notificationId, boolean isSilentNotification) {
        switch (sensorType) {
            case "Karbonmonoksit":
                if (!isCoAlarmPlaying && shouldSendNotifications) {
                    Log.d("MainActivity", "Triggering CO alarm - Current value: " + value + " ppm");
                    
                    // Her zaman ses ve titreşim çal
                    playAlarmSoundAndVibrate();
                    
                    isCoAlarmPlaying = true;
                    
                    // Alarm durumunu SharedPreferences'a kaydet
                    SharedPreferences.Editor editor = sensorPrefs.edit();
                    editor.putBoolean(PREFS_CO_ALARM, true);
                    editor.apply();
                    
                    sendNotification(sensorType + " seviyesi tehlikeli: " + value, notificationId, isSilentNotification);
                    Log.d("MainActivity", "CO alarm triggered and notification sent" + (isSilentNotification ? " (silent mode)" : ""));
                } else if (isInitialThresholdCheck) {
                    Log.d("MainActivity", "Initial check - Skipping CO alarm trigger");
                } else {
                    Log.d("MainActivity", "CO alarm already playing, skipping trigger");
                }
                coProgressBar.setCurrentValue((float) value);
                coProgressBar.setThresholdValue((float) CO2_THRESHOLD);
                coTextView.setText(String.format(Locale.US, "%.2f ppm", value));
                break;
            case "Nem":
                if (!isHumidityAlarmPlaying && shouldSendNotifications) {
                    Log.d("MainActivity", "Triggering Humidity alarm - Current value: " + value + " %");
                    
                    // Silent notification değilse alarm sesi ve titreşim çal
                    if (!isSilentNotification) {
                        playAlarmSoundAndVibrate();
                    }
                    
                    isHumidityAlarmPlaying = true;
                    
                    // Alarm durumunu SharedPreferences'a kaydet
                    SharedPreferences.Editor editor = sensorPrefs.edit();
                    editor.putBoolean(PREFS_HUMIDITY_ALARM, true);
                    editor.apply();
                    
                    sendNotification(sensorType + " seviyesi tehlikeli: " + value, notificationId, isSilentNotification);
                    Log.d("MainActivity", "Humidity alarm triggered and notification sent" + (isSilentNotification ? " (silent mode)" : ""));
                } else if (isInitialThresholdCheck) {
                    Log.d("MainActivity", "Initial check - Skipping Humidity alarm trigger");
                } else {
                    Log.d("MainActivity", "Humidity alarm already playing, skipping trigger");
                }
                humidityProgressBar.setCurrentValue((float) value);
                humidityProgressBar.setThresholdValue((float) HUMIDITY_THRESHOLD);
                humidityTextView.setText(String.format(Locale.US, "%.2f %%", value));
                break;
            case "Sıcaklık":
                if (!isTemperatureAlarmPlaying && shouldSendNotifications) {
                    Log.d("MainActivity", "Triggering Temperature alarm - Current value: " + value + " °C");
                    
                    // Silent notification değilse alarm sesi ve titreşim çal
                    if (!isSilentNotification) {
                        playAlarmSoundAndVibrate();
                    }
                    
                    isTemperatureAlarmPlaying = true;
                    
                    // Alarm durumunu SharedPreferences'a kaydet
                    SharedPreferences.Editor editor = sensorPrefs.edit();
                    editor.putBoolean(PREFS_TEMP_ALARM, true);
                    editor.apply();
                    
                    sendNotification(sensorType + " seviyesi tehlikeli: " + value, notificationId, isSilentNotification);
                    Log.d("MainActivity", "Temperature alarm triggered and notification sent" + (isSilentNotification ? " (silent mode)" : ""));
                } else if (isInitialThresholdCheck) {
                    Log.d("MainActivity", "Initial check - Skipping Temperature alarm trigger");
                } else {
                    Log.d("MainActivity", "Temperature alarm already playing, skipping trigger");
                }
                temperatureProgressBar.setCurrentValue((float) value);
                temperatureProgressBar.setThresholdValue((float) TEMP_THRESHOLD);
                temperatureTextView.setText(String.format(Locale.US, "%.2f °C", value));
                break;
        }
    }

    // Sadece sessiz bildirim göndermek için özel metod
    private void handleSilentNotification(String sensorType, double value, int notificationId) {
        // Ses ve titreşim çal
        Log.d("MainActivity", "Sending silent notification for " + sensorType + " - Value: " + value + " (with sound and vibration)");
        playAlarmSoundAndVibrate();
        
        // Bu sensör için değerleri güncelle
        switch (sensorType) {
            case "Karbonmonoksit":
                coProgressBar.setCurrentValue((float) value);
                coProgressBar.setThresholdValue((float) CO2_THRESHOLD);
                coTextView.setText(String.format(Locale.US, "%.2f ppm", value));
                isCoAlarmPlaying = true;
                
                // Alarm durumunu SharedPreferences'a kaydet
                SharedPreferences.Editor editor = sensorPrefs.edit();
                editor.putBoolean(PREFS_CO_ALARM, true);
                editor.apply();
                break;
            case "Nem":
                humidityProgressBar.setCurrentValue((float) value);
                humidityProgressBar.setThresholdValue((float) HUMIDITY_THRESHOLD);
                humidityTextView.setText(String.format(Locale.US, "%.2f %%", value));
                isHumidityAlarmPlaying = true;
                
                // Alarm durumunu SharedPreferences'a kaydet
                SharedPreferences.Editor editor2 = sensorPrefs.edit();
                editor2.putBoolean(PREFS_HUMIDITY_ALARM, true);
                editor2.apply();
                break;
            case "Sıcaklık":
                temperatureProgressBar.setCurrentValue((float) value);
                temperatureProgressBar.setThresholdValue((float) TEMP_THRESHOLD);
                temperatureTextView.setText(String.format(Locale.US, "%.2f °C", value));
                isTemperatureAlarmPlaying = true;
                
                // Alarm durumunu SharedPreferences'a kaydet
                SharedPreferences.Editor editor3 = sensorPrefs.edit();
                editor3.putBoolean(PREFS_TEMP_ALARM, true);
                editor3.apply();
                break;
        }
        
        // Sessiz bildirimi gönder (true parametresi ile)
        sendNotification(sensorType + " seviyesi tehlikeli: " + value, notificationId, true);
    }

    private void updateUIForSensor(String sensorType, String displayValue, Object timestamp) {
        runOnUiThread(() -> {
            TextView targetTextView;
            TextView timeTextView;
            
            switch (sensorType) {
                case "Karbonmonoksit":
                    targetTextView = coTextView;
                    timeTextView = textView7;
                    break;
                case "Nem":
                    targetTextView = humidityTextView;
                    timeTextView = textView3;
                    break;
                case "Sıcaklık":
                    targetTextView = temperatureTextView;
                    timeTextView = textView9;
                    break;
                default:
                    return;
            }

            if (targetTextView != null) {
                targetTextView.setText(displayValue);
            }

            // Zaman farkını hesapla ve göster
            if (timeTextView != null && timestamp != null) {
                try {
                    long timestampLong;
                    if (timestamp instanceof Long) {
                        timestampLong = (Long) timestamp;
                    } else if (timestamp instanceof String) {
                        timestampLong = Long.parseLong((String) timestamp);
                    } else {
                        throw new IllegalArgumentException("Unexpected timestamp type");
                    }

                    long currentTime = System.currentTimeMillis();
                    long diffInMillis = currentTime - timestampLong;
                    long diffInHours = diffInMillis / (60 * 60 * 1000);
                    
                    if (diffInHours < 1) {
                        long diffInMinutes = diffInMillis / (60 * 1000);
                        timeTextView.setText(diffInMinutes + " dk önce");
                    } else {
                        timeTextView.setText(diffInHours + " saat önce");
                    }
                } catch (Exception e) {
                    Log.e("MainActivity", "Zaman hesaplama hatası: " + e.getMessage());
                    timeTextView.setText("Zaman bilgisi alınamadı");
                }
            }
        });
    }

    private void updateMoodStatus() {
        try {
            // Sayıları nokta ile ayrılmış formata çevir
            String coText = coTextView.getText().toString()
                .replace(" ppm", "")
                .replace(",", ".")
                .replace("--", "0")
                .trim();
            String humidityText = humidityTextView.getText().toString()
                .replace(" %", "")
                .replace(",", ".")
                .replace("--", "0")
                .trim();
            String temperatureText = temperatureTextView.getText().toString()
                .replace(" °C", "")
                .replace(",", ".")
                .replace("--", "0")
                .trim();

            Log.d("MainActivity", "Parsing values - CO: " + coText + 
                ", Humidity: " + humidityText + ", Temperature: " + temperatureText);

            double coLevel = Double.parseDouble(coText);
            double humidity = Double.parseDouble(humidityText);
            double temperature = Double.parseDouble(temperatureText);

            Log.d("MainActivity", String.format(Locale.US, "Parsed values - CO2: %.2f (threshold: %.2f), " + 
                "Temp: %.2f (threshold: %.2f), Humidity: %.2f (threshold: %.2f)", 
                coLevel, CO2_THRESHOLD, temperature, TEMP_THRESHOLD, humidity, HUMIDITY_THRESHOLD));

            // Eşik değeri kontrollerini yap ve logla
            Log.d("MainActivity", "Checking thresholds in updateMoodStatus:");
            Log.d("MainActivity", "CO check: " + coLevel + " > " + CO2_THRESHOLD + " = " + (coLevel > CO2_THRESHOLD));
            Log.d("MainActivity", "Temperature check: " + temperature + " > " + TEMP_THRESHOLD + " = " + (temperature > TEMP_THRESHOLD));
            Log.d("MainActivity", "Humidity check: " + humidity + " > " + HUMIDITY_THRESHOLD + " = " + (humidity > HUMIDITY_THRESHOLD));

            // Her sensör için eşik değeri kontrolü yap
            checkThreshold("Karbonmonoksit", coLevel, CO2_THRESHOLD);
            checkThreshold("Sıcaklık", temperature, TEMP_THRESHOLD);
            checkThreshold("Nem", humidity, HUMIDITY_THRESHOLD);
            
            // İlk kontrol tamamlandı, bayrağı kaldır
            if (isInitialThresholdCheck) {
                Log.d("MainActivity", "Initial threshold check completed, notifications will be enabled for future checks");
                isInitialThresholdCheck = false;
            }

            // Emoji ve durum güncellemesi
            if (coLevel > CO2_THRESHOLD) {
                // Kötü durum
                Log.d("MainActivity", "Setting mood: Kötü (CO threshold exceeded)");
                emojiImage.setImageResource(R.drawable.bad);
                moodId.setText("TEHLİKE!");
                moodId2.setText("ACİL DURUM! EVİ DERHAL TAHLİYE EDİN!");
            } else if (temperature > TEMP_THRESHOLD || humidity > HUMIDITY_THRESHOLD) {
                // Normal durum ama havalandırma gerekli
                Log.d("MainActivity", "Setting mood: Normal (Temperature or Humidity threshold exceeded)");
                emojiImage.setImageResource(R.drawable.normal);
                moodId.setText("DİKKAT");
                moodId2.setText("Değerler yükseliyor! Havalandırma gerekli.");
            } else {
                // Güvenli durum
                Log.d("MainActivity", "Setting mood: Güvenli (All values within limits)");
                emojiImage.setImageResource(R.drawable.good);
                moodId.setText("GÜVENLİ");
                moodId2.setText("Tüm değerler normal seviyede ✓");
            }
        } catch (NumberFormatException e) {
            Log.e("MainActivity", "Sensor değerleri parse edilemedi: " + e.getMessage());
            // Hata durumunda varsayılan değerleri göster
            emojiImage.setImageResource(R.drawable.good);
            moodId.setText("GÜVENLİ");
            moodId2.setText("Değerler yükleniyor...");
        }
    }

    private void restoreInstanceState(Bundle savedInstanceState) {
        String co = savedInstanceState.getString(INSTANCE_STATE_CO, "N/A");
        String humidity = savedInstanceState.getString(INSTANCE_STATE_HUMIDITY, "N/A");
        String temperature = savedInstanceState.getString(INSTANCE_STATE_TEMPERATURE, "N/A");
        String timestamp = savedInstanceState.getString(INSTANCE_STATE_TIMESTAMP, "N/A");
        isAlarmPlaying = savedInstanceState.getBoolean(INSTANCE_STATE_ALARM_PLAYING, false);

        coTextView.setText(co);
        humidityTextView.setText(humidity);
        temperatureTextView.setText(temperature);
        timestampTextView.setText(timestamp);

        try {
            lastCoValue = Integer.parseInt(co);
        } catch (NumberFormatException e) {
            lastCoValue = 0;
        }

        // Eğer alarm çalıyorsa, durumu koru
        if (isAlarmPlaying) {
            playAlarmSoundAndVibrate();
        }
    }

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
        outState.putString(INSTANCE_STATE_CO, coTextView.getText().toString());
        outState.putString(INSTANCE_STATE_HUMIDITY, humidityTextView.getText().toString());
        outState.putString(INSTANCE_STATE_TEMPERATURE, temperatureTextView.getText().toString());
        outState.putString(INSTANCE_STATE_TIMESTAMP, timestampTextView.getText().toString());
        outState.putBoolean(INSTANCE_STATE_ALARM_PLAYING, isAlarmPlaying);
    }

    @Override
    protected void onResume() {
        super.onResume();
        try {
            isActivityResumed = true;
            
            // Uygulama ön plana geldiğinde ilk kontrol bayrağını yeniden ayarla
            isInitialThresholdCheck = true;
            Log.d("MainActivity", "App resumed - Initial notification check flag reset");
            
            // Yeniden başlatıldığında Firebase listener'ı tekrar kur
            if (sensorDataRef == null || sensorDataListener == null) {
                setupFirebaseListener();
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error in onResume: " + e.getMessage());
            e.printStackTrace();
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        try {
            isActivityResumed = false;
        } catch (Exception e) {
            Log.e("MainActivity", "Error in onPause: " + e.getMessage());
            e.printStackTrace();
        }
    }

    @Override
    protected void onDestroy() {
        try {
            // Önce parent'ın onDestroy'unu çağır
            super.onDestroy();
            
            // Aktivite kapatılırken tüm alarmları temizle
            stopAlarmAndVibrate();
            
            // Firebase listener'ı temizle
            if (sensorDataRef != null && sensorDataListener != null) {
                sensorDataRef.removeEventListener(sensorDataListener);
            }
            
            // SoundPool'u temizle
            if (soundPool != null) {
                try {
                    soundPool.release();
                } catch (Exception e) {
                    Log.e("MainActivity", "Error releasing soundpool", e);
                }
                soundPool = null;
            }
            
            // BroadcastReceiver'ı temizle
            try {
                unregisterReceiver(alarmStopReceiver);
            } catch (IllegalArgumentException e) {
                Log.e("MainActivity", "Receiver was not registered", e);
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error in onDestroy: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            );
            channel.setDescription(CHANNEL_DESC);
            channel.enableVibration(true);
            channel.setVibrationPattern(new long[]{0, 500, 1000});
            channel.setLockscreenVisibility(Notification.VISIBILITY_PUBLIC);
            channel.setShowBadge(true);
            channel.enableLights(true);

            NotificationManager notificationManager = getSystemService(NotificationManager.class);
            if (notificationManager != null) {
                notificationManager.createNotificationChannel(channel);
            }
        }
    }

    // Bildirim gönderme metodu:
    // - Bildirim üzerine tıklandığında MainActivity açılır.
    // - "Durdur" butonuna basıldığında yalnızca Broadcast tetiklenir.
    private void sendNotification(String message, int notificationId) {
        sendNotification(message, notificationId, false);
    }

    // Sessiz bildirim seçeneği ile geliştirilmiş bildirim metodu
    private void sendNotification(String message, int notificationId, boolean isSilentNotification) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, android.Manifest.permission.POST_NOTIFICATIONS)
                    != PackageManager.PERMISSION_GRANTED) {
                showNotificationPermissionDialog();
                return;
            }
        }

        Intent contentIntent = new Intent(this, MainActivity.class);
        contentIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
        PendingIntent contentPendingIntent = PendingIntent.getActivity(
            this, 
            notificationId, // Use notificationId for unique PendingIntent
            contentIntent, 
            PendingIntent.FLAG_UPDATE_CURRENT | PendingIntent.FLAG_IMMUTABLE
        );

        Intent stopIntent = new Intent(ACTION_STOP_ALARM);
        stopIntent.setPackage(getPackageName());
        stopIntent.putExtra("notification_id", notificationId); // Add notificationId to Intent
        PendingIntent stopPendingIntent = PendingIntent.getBroadcast(
            this, 
            notificationId, // Use notificationId for unique PendingIntent
            stopIntent, 
            PendingIntent.FLAG_UPDATE_CURRENT | PendingIntent.FLAG_IMMUTABLE
        );

        try {
            // Bildirim başlığını sessiz mod durumuna göre belirle
            String notificationTitle = isSilentNotification ? "Sessiz Bildirimi" : "TEHLİKE!!!!";
            
            NotificationCompat.Builder builder = new NotificationCompat.Builder(this, CHANNEL_ID)
                .setSmallIcon(R.drawable.main_logo)
                .setContentTitle(notificationTitle)
                .setContentText(message)
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setCategory(NotificationCompat.CATEGORY_ALARM)
                .setDefaults(Notification.DEFAULT_ALL)
                .setAutoCancel(false)
                .setOngoing(true)
                .setContentIntent(contentPendingIntent)
                .addAction(android.R.drawable.ic_media_pause, "Durdur", stopPendingIntent);

            NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
            if (notificationManager != null) {
                notificationManager.notify(notificationId, builder.build());
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error sending notification", e);
        }
    }

    private void showNotificationPermissionDialog() {
        // Modern bir bildirim izni diyaloğu oluştur
        androidx.appcompat.app.AlertDialog.Builder builder = new androidx.appcompat.app.AlertDialog.Builder(this);
        
        // Özel görünüm oluştur
        View dialogView = getLayoutInflater().inflate(android.R.layout.select_dialog_item, null);
        TextView messageView = new TextView(this);
        messageView.setText("Güvenliğiniz için bildirim iznine ihtiyacımız var. " +
                "CO2, sıcaklık veya nem değerlerinin tehlikeli seviyelere ulaşması durumunda " +
                "sizi anında bilgilendirmemiz gerekiyor.\n\n" +
                "İzin vererek güvende kalın!");
        messageView.setPadding(30, 30, 30, 30);
        messageView.setTextSize(16);
        
        builder.setTitle("Bildirim İzni Gerekli")
               .setIcon(R.drawable.main_logo)
               .setView(messageView)
               .setPositiveButton("Ayarlara Git", (dialog, which) -> {
                   Intent intent = new Intent(android.provider.Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
                   intent.setData(android.net.Uri.parse("package:" + getPackageName()));
                   startActivity(intent);
                   // İşlem sonrası bilgi ver
                   CustomToast.showInfo(this, "Ayarlar açılıyor...");
               })
               .setNegativeButton("İptal", (dialog, which) -> {
                   // İptal edildiğinde bilgilendirme yap
                   CustomToast.showWarning(this, "Bildirimler olmadan tehlike durumlarında uyarılamayabilirsiniz!");
               })
               .setCancelable(false);
        
        // Dialog animasyonlu olarak göster
        androidx.appcompat.app.AlertDialog dialog = builder.create();
        try {
            if (dialog.getWindow() != null) {
                dialog.getWindow().getAttributes().windowAnimations = android.R.style.Animation_Dialog;
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Dialog animasyonu ayarlanırken hata oluştu: " + e.getMessage());
        }
        dialog.show();
    }

    // Alarm sesini çalmaya başlat ve titreşimi aktif et
    private void playAlarmSoundAndVibrate() {
        if (!isAlarmPlaying) {
            soundPool.play(alarmSoundId, 1, 1, 0, -1, 1);
            isAlarmPlaying = true;
            updateStopAlarmButtonState(true);

            Vibrator vibrator = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
            if (vibrator != null) {
                long[] pattern = {0, 500, 1000};  // 500ms titreşim, 1000ms ara
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                    vibrator.vibrate(VibrationEffect.createWaveform(pattern, 0));
                } else {
                    vibrator.vibrate(pattern, 0);
                }
            }
        }
    }

    // Alarm sesini ve titreşimi durdur
    public void stopAlarmAndVibrate() {
        Log.d("MainActivity", "stopAlarmAndVibrate called");
        
        // Önce alarm durumunu güncelle
        isAlarmPlaying = false;
        isCoAlarmPlaying = false;
        isHumidityAlarmPlaying = false;
        isTemperatureAlarmPlaying = false;
        
        // Alarm durumlarını SharedPreferences'a kaydet
        SharedPreferences.Editor editor = sensorPrefs.edit();
        editor.putBoolean(PREFS_CO_ALARM, false);
        editor.putBoolean(PREFS_HUMIDITY_ALARM, false);
        editor.putBoolean(PREFS_TEMP_ALARM, false);
        editor.putBoolean(NOTIFICATION_FLAG, false);
        editor.apply();
        
        // Firebase'deki device_outputs'ların is_alarm_acknowledged değerlerini güncelle
        // Bu sadece aktif alarm olduğunda çağrılacak şekilde
        if (isCoAlarmPlaying || isHumidityAlarmPlaying || isTemperatureAlarmPlaying) {
            updateAllDeviceOutputsAcknowledged();
        }
        
        // Update stop alarm button state
        if (stopAlarmButton != null) {
            stopAlarmButton.setBackgroundResource(R.drawable.alarm);
            stopAlarmButton.setEnabled(false);
        }
        
        // SoundPool'u durdur
        try {
            if (soundPool != null) {
                soundPool.stop(alarmSoundId);
                soundPool.autoPause(); // Tüm sesleri durdur
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error stopping sound", e);
        }

        // Titreşimi durdur
        try {
            Vibrator vibrator = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
            if (vibrator != null) {
                vibrator.cancel();
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error stopping vibration", e);
        }

        // UI thread'de son CO değerini güncelle
        runOnUiThread(() -> {
            try {
                String currentCo = coTextView.getText().toString();
                lastCoValue = Integer.parseInt(currentCo.equals("N/A") ? "0" : currentCo);
            } catch (NumberFormatException e) {
                lastCoValue = 0;
            }
        });
    }

    // Bildirimi alarm olmadan güncelle
    private void updateNotificationWithoutAlarm(int notificationId) {
        String message = "";
        switch (notificationId) {
            case CO_NOTIFICATION_ID:
                message = "Son ölçülen CO seviyesi: " + coTextView.getText().toString();
                break;
            case HUMIDITY_NOTIFICATION_ID:
                message = "Son ölçülen Nem seviyesi: " + humidityTextView.getText().toString();
                break;
            case TEMPERATURE_NOTIFICATION_ID:
                message = "Son ölçülen Sıcaklık seviyesi: " + temperatureTextView.getText().toString();
                break;
        }
        
        if (!message.isEmpty()) {
            try {
                Intent contentIntent = new Intent(this, MainActivity.class);
                contentIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
                PendingIntent contentPendingIntent = PendingIntent.getActivity(
                    this, 
                    notificationId,
                    contentIntent, 
                    PendingIntent.FLAG_UPDATE_CURRENT | PendingIntent.FLAG_IMMUTABLE
                );

                NotificationCompat.Builder builder = new NotificationCompat.Builder(this, CHANNEL_ID)
                    .setSmallIcon(R.drawable.main_logo)
                    .setContentTitle("Sensör Uyarısı")
                    .setContentText(message)
                    .setPriority(NotificationCompat.PRIORITY_HIGH)
                    .setCategory(NotificationCompat.CATEGORY_ALARM)
                    .setAutoCancel(true)
                    .setContentIntent(contentPendingIntent);

                NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
                if (notificationManager != null) {
                    notificationManager.notify(notificationId, builder.build());
                }
            } catch (Exception e) {
                Log.e("MainActivity", "Error updating notification", e);
            }
        }
    }

    private void cancelNotification(int notificationId) {
        try {
            NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
            if (notificationManager != null) {
                notificationManager.cancel(notificationId);
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error canceling notification", e);
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == NOTIFICATION_PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                Log.d("MainActivity", "Bildirim izni alındı");
                // İzin verildikten sonra Firebase listener'ı yeniden başlat
                setupFirebaseListener();
            } else {
                Log.d("MainActivity", "Bildirim izni reddedildi");
                showNotificationPermissionDialog();
            }
        }
    }

    private void loadLastKnownValues() {
        try {
            // Load from SharedPreferences
            String lastCo = sensorPrefs.getString("last_co", "0");
            String lastHumidity = sensorPrefs.getString("last_humidity", "0");
            String lastTemperature = sensorPrefs.getString("last_temperature", "0");
            String lastTimestamp = sensorPrefs.getString("last_timestamp", "");
            
            // Alarm durumlarını yükle
            isCoAlarmPlaying = sensorPrefs.getBoolean(PREFS_CO_ALARM, false);
            isHumidityAlarmPlaying = sensorPrefs.getBoolean(PREFS_HUMIDITY_ALARM, false);
            isTemperatureAlarmPlaying = sensorPrefs.getBoolean(PREFS_TEMP_ALARM, false);
            
            // Son kontrol zamanını yükle
            long lastCheckTime = sensorPrefs.getLong(PREFS_LAST_CHECK_TIME, 0);
            long currentTime = System.currentTimeMillis();
            
            // Eğer son kontrol üzerinden 1 saatten fazla zaman geçtiyse, ilk yükleme gibi davran
            if (currentTime - lastCheckTime > 60 * 60 * 1000) {
                isInitialThresholdCheck = true;
                Log.d("MainActivity", "More than 1 hour since last check, resetting initial threshold check flag");
            } else {
                isInitialThresholdCheck = false;
                Log.d("MainActivity", "Less than 1 hour since last check, restoring alarm states");
            }
            
            // Eğer herhangi bir alarm aktifse, alarm durumunu güncelle
            if (isCoAlarmPlaying || isHumidityAlarmPlaying || isTemperatureAlarmPlaying) {
                isAlarmPlaying = true;
                updateStopAlarmButtonState(true);
                if (isActivityResumed) {
                    playAlarmSoundAndVibrate();
                }
                Log.d("MainActivity", "Alarm states restored - CO: " + isCoAlarmPlaying + 
                    ", Humidity: " + isHumidityAlarmPlaying + 
                    ", Temperature: " + isTemperatureAlarmPlaying);
            }

            // Update UI with cached values
            if (!lastCo.equals("0")) {
                coTextView.setText(lastCo + " ppm");
            } else {
                coTextView.setText("--");
            }

            if (!lastHumidity.equals("0")) {
                humidityTextView.setText(lastHumidity + " %");
            } else {
                humidityTextView.setText("--");
            }

            if (!lastTemperature.equals("0")) {
                temperatureTextView.setText(lastTemperature + " °C");
            } else {
                temperatureTextView.setText("--");
            }

            if (!lastTimestamp.isEmpty()) {
                timestampTextView.setText(lastTimestamp);
            } else {
                timestampTextView.setText("--:--:--");
            }

            // Try to parse last CO value
            try {
                lastCoValue = Integer.parseInt(lastCo);
            } catch (NumberFormatException e) {
                lastCoValue = 0;
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error loading last known values: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private void saveSensorData(String co, String humidity, String temperature, String timestamp) {
        // Save to SharedPreferences for quick access
        SharedPreferences.Editor editor = sensorPrefs.edit();
        editor.putString("last_co", co);
        editor.putString("last_humidity", humidity);
        editor.putString("last_temperature", temperature);
        editor.putString("last_timestamp", timestamp);
        editor.apply();
    }

    private void logoutUser() {
        try {
            if (mAuth != null) {
                // Stop any ongoing alarms or notifications
                stopAlarmAndVibrate();
                cancelAllNotifications();
                
                // Sign out from Firebase
                mAuth.signOut();
                
                // Show logout message using CustomToast
                CustomToast.showSuccess(MainActivity.this, "Çıkış yapıldı.");
                
                // Create intent for LoginActivity
                Intent loginIntent = new Intent(MainActivity.this, LoginActivity.class);
                loginIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                
                // Start LoginActivity after a short delay
                new Handler().postDelayed(() -> {
                    startActivity(loginIntent);
                    // Add custom transition animation
                    overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
                    finish();
                }, 1000);
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Logout error: " + e.getMessage());
            CustomToast.showError(MainActivity.this, "Çıkış yapılırken bir hata oluştu.");
        }
    }

    private void cancelAllNotifications() {
        try {
            NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
            if (notificationManager != null) {
                notificationManager.cancelAll();
            }
        } catch (Exception e) {
            Log.e("MainActivity", "Error canceling notifications", e);
        }
    }

    // Update BroadcastReceiver to handle specific notification
    private final BroadcastReceiver alarmStopReceiver = new BroadcastReceiver() {
        @Override
        public void onReceive(Context context, Intent intent) {
            if (intent != null && ACTION_STOP_ALARM.equals(intent.getAction())) {
                int notificationId = intent.getIntExtra("notification_id", -1);
                Log.d("MainActivity", "Durdur action received for notification: " + notificationId);
                
                SharedPreferences.Editor editor = sensorPrefs.edit();
                
                // Hangi alarm tipinin durdurulduğuna bağlı olarak farklı işlem yap
                switch (notificationId) {
                    case CO_NOTIFICATION_ID: // Karbonmonoksit alarmı
                        isCoAlarmPlaying = false;
                        editor.putBoolean(PREFS_CO_ALARM, false);
                        // Karbonmonoksit alarmı durduğunda tüm output'ları güncelle
                        updateSpecificDeviceOutputs(new String[]{"output_type_1", "output_type_2", "output_type_3"});
                        Log.d("MainActivity", "CO alarm stopped - updating all output types");
                        break;
                    case HUMIDITY_NOTIFICATION_ID: // Nem alarmı
                        isHumidityAlarmPlaying = false;
                        editor.putBoolean(PREFS_HUMIDITY_ALARM, false);
                        // Nem alarmı durduğunda sadece output_type_2'yi güncelle
                        updateSpecificDeviceOutputs(new String[]{"output_type_2"});
                        Log.d("MainActivity", "Humidity alarm stopped - updating only output_type_2");
                        break;
                    case TEMPERATURE_NOTIFICATION_ID: // Sıcaklık alarmı
                        isTemperatureAlarmPlaying = false;
                        editor.putBoolean(PREFS_TEMP_ALARM, false);
                        // Sıcaklık alarmı durduğunda sadece output_type_1'i güncelle
                        updateSpecificDeviceOutputs(new String[]{"output_type_1"});
                        Log.d("MainActivity", "Temperature alarm stopped - updating only output_type_1");
                        break;
                }
                
                editor.apply();
                
                stopAlarmAndVibrate();
                updateNotificationWithoutAlarm(notificationId);
            }
        }
    };
    
    // Belirli output tiplerinin is_alarm_acknowledged değerini güncelleyen metod
    private void updateSpecificDeviceOutputs(String[] outputTypes) {
        DatabaseReference deviceOutputsRef = FirebaseDatabase.getInstance().getReference("device_outputs");
        
        // Güncelleme için map
        Map<String, Object> updates = new HashMap<>();
        updates.put("is_alarm_acknowledged", true);
        
        // Belirtilen output tipleri için güncelleme yap
        for (String outputType : outputTypes) {
            // Lambda içinde kullanılacak değişkeni final olarak tanımla
            final String finalOutputType = outputType;
            
            deviceOutputsRef.child(outputType).updateChildren(updates)
                .addOnSuccessListener(aVoid -> {
                    Log.d("MainActivity", "Device output " + finalOutputType + " is_alarm_acknowledged updated to true");
                })
                .addOnFailureListener(e -> {
                    Log.e("MainActivity", "Error updating device output: " + e.getMessage());
                });
        }
    }
    
    private void updateStopAlarmButtonState(boolean isAnyAlarmActive) {
        if (stopAlarmButton != null) {
            stopAlarmButton.setEnabled(isAnyAlarmActive);
            stopAlarmButton.setBackgroundResource(isAnyAlarmActive ? R.drawable.alarm_trigger : R.drawable.alarm);
        }
    }

    private void setupThresholdChangeListeners() {
        // CO Threshold değişiklik dinleyicisi
        coProgressBar.setThresholdChangeListener(new CustomProgressBar.ThresholdChangeListener() {
            @Override
            public void onThresholdChanged(CustomProgressBar progressBar, float thresholdValue) {
                // Değer değişirken güncelleme
                CO2_THRESHOLD = thresholdValue;
                // Toast mesajı kaldırıldı - değer baloncukta gösteriliyor
            }
            
            @Override
            public void onThresholdChangeFinished(CustomProgressBar progressBar, float thresholdValue) {
                // Kullanıcı sürüklemeyi bitirdiğinde direkt Firebase'e kaydet
                CO2_THRESHOLD = thresholdValue;
                saveThresholdToFirebase("co", thresholdValue);
            }
        });
        
        // Temperature Threshold değişiklik dinleyicisi
        temperatureProgressBar.setThresholdChangeListener(new CustomProgressBar.ThresholdChangeListener() {
            @Override
            public void onThresholdChanged(CustomProgressBar progressBar, float thresholdValue) {
                // Değer değişirken güncelleme
                TEMP_THRESHOLD = thresholdValue;
                // Toast mesajı kaldırıldı - değer baloncukta gösteriliyor
            }
            
            @Override
            public void onThresholdChangeFinished(CustomProgressBar progressBar, float thresholdValue) {
                // Kullanıcı sürüklemeyi bitirdiğinde direkt Firebase'e kaydet
                TEMP_THRESHOLD = thresholdValue;
                saveThresholdToFirebase("temperature", thresholdValue);
            }
        });
        
        // Humidity Threshold değişiklik dinleyicisi
        humidityProgressBar.setThresholdChangeListener(new CustomProgressBar.ThresholdChangeListener() {
            @Override
            public void onThresholdChanged(CustomProgressBar progressBar, float thresholdValue) {
                // Değer değişirken güncelleme
                HUMIDITY_THRESHOLD = thresholdValue;
                // Toast mesajı kaldırıldı - değer baloncukta gösteriliyor
            }
            
            @Override
            public void onThresholdChangeFinished(CustomProgressBar progressBar, float thresholdValue) {
                // Kullanıcı sürüklemeyi bitirdiğinde direkt Firebase'e kaydet
                HUMIDITY_THRESHOLD = thresholdValue;
                saveThresholdToFirebase("humidity", thresholdValue);
            }
        });
    }
    
    private void saveThresholdToFirebase(String sensorType, float thresholdValue) {
        if (mAuth.getCurrentUser() == null) {
            CustomToast.showError(this, "Kullanıcı oturumu açık değil!");
            return;
        }
        
        // Değeri yuvarla (4.4 -> 4, 4.5 -> 5, 4.6 -> 5)
        float roundedValue;
        float decimal = thresholdValue - (int)thresholdValue;
        
        if (decimal < 0.5) {
            // 0.5'ten küçükse aşağı yuvarla
            roundedValue = (float)Math.floor(thresholdValue);
        } else {
            // 0.5 ve üstüyse yukarı yuvarla
            roundedValue = (float)Math.ceil(thresholdValue);
        }
        
        // device_sensors referansı
        DatabaseReference deviceSensorsRef = FirebaseDatabase.getInstance().getReference("device_sensors");
        
        // Hangi sensör tipine göre güncelleme yapacağımızı belirle
        String sensorNodeKey;
        String sensorName;
        switch (sensorType) {
            case "temperature":
                sensorNodeKey = "sensor_type_1"; // Sıcaklık
                sensorName = "Sıcaklık";
                break;
            case "humidity":
                sensorNodeKey = "sensor_type_2"; // Nem
                sensorName = "Nem";
                break;
            case "co":
                sensorNodeKey = "sensor_type_3"; // CO2
                sensorName = "CO2";
                break;
            default:
                Log.e("MainActivity", "Bilinmeyen sensör tipi: " + sensorType);
                CustomToast.showError(this, "Bilinmeyen sensör tipi!");
                return;
        }
        
        // Eşik değerini yuvarlanmış tam sayı olarak formatla
        String thresholdString = String.format(Locale.US, "%.0f", roundedValue);
        
        // Kullanıcıya bilgilendirme toast mesajı göster - daha modern ve şık
        CustomToast.showInfo(this, sensorName + " için limit değeri " + thresholdString + " olarak ayarlanıyor");
        
        // device_sensors/sensor_type_X/limits alanını güncelle
        deviceSensorsRef.child(sensorNodeKey).child("limits").setValue(thresholdString)
            .addOnSuccessListener(aVoid -> {
                // İşlem başarılı olduğunda bildirim göster
                CustomToast.showSuccess(this, sensorName + " limit değeri başarıyla kaydedildi");
                Log.d("MainActivity", "Eşik değeri başarıyla güncellendi: " + sensorType + "=" + thresholdString);
            })
            .addOnFailureListener(e -> {
                // Hata durumunda kullanıcıya bildir
                CustomToast.showError(this, "Limit değeri kaydedilemedi: " + e.getMessage());
                Log.e("MainActivity", "Error saving threshold: " + e.getMessage());
            });
    }
    
    private void loadThresholdValues() {
        if (mAuth.getCurrentUser() == null) {
            return;
        }
        
        // device_sensors referansı
        DatabaseReference deviceSensorsRef = FirebaseDatabase.getInstance().getReference("device_sensors");
        
        // Tüm device_sensors verilerini tek seferde al
        deviceSensorsRef.addListenerForSingleValueEvent(new ValueEventListener() {
            @Override
            public void onDataChange(@NonNull DataSnapshot dataSnapshot) {
                if (dataSnapshot.exists()) {
                    // Sıcaklık eşiği (sensor_type_1)
                    if (dataSnapshot.hasChild("sensor_type_1")) {
                        String tempLimit = dataSnapshot.child("sensor_type_1").child("limits").getValue(String.class);
                        if (tempLimit != null) {
                            try {
                                TEMP_THRESHOLD = Double.parseDouble(tempLimit);
                                temperatureProgressBar.setThresholdValue((float) TEMP_THRESHOLD);
                                Log.d("MainActivity", "Sıcaklık eşiği Firebase'den yüklendi: " + TEMP_THRESHOLD);
                            } catch (NumberFormatException e) {
                                Log.e("MainActivity", "Geçersiz sıcaklık eşiği formatı: " + tempLimit);
                            }
                        }
                    }
                    
                    // Nem eşiği (sensor_type_2)
                    if (dataSnapshot.hasChild("sensor_type_2")) {
                        String humidityLimit = dataSnapshot.child("sensor_type_2").child("limits").getValue(String.class);
                        if (humidityLimit != null) {
                            try {
                                HUMIDITY_THRESHOLD = Double.parseDouble(humidityLimit);
                                humidityProgressBar.setThresholdValue((float) HUMIDITY_THRESHOLD);
                                Log.d("MainActivity", "Nem eşiği Firebase'den yüklendi: " + HUMIDITY_THRESHOLD);
                            } catch (NumberFormatException e) {
                                Log.e("MainActivity", "Geçersiz nem eşiği formatı: " + humidityLimit);
                            }
                        }
                    }
                    
                    // CO eşiği (sensor_type_3)
                    if (dataSnapshot.hasChild("sensor_type_3")) {
                        String coLimit = dataSnapshot.child("sensor_type_3").child("limits").getValue(String.class);
                        if (coLimit != null) {
                            try {
                                CO2_THRESHOLD = Double.parseDouble(coLimit);
                                coProgressBar.setThresholdValue((float) CO2_THRESHOLD);
                                Log.d("MainActivity", "CO eşiği Firebase'den yüklendi: " + CO2_THRESHOLD);
                            } catch (NumberFormatException e) {
                                Log.e("MainActivity", "Geçersiz CO eşiği formatı: " + coLimit);
                            }
                        }
                    }
                }
            }
            
            @Override
            public void onCancelled(@NonNull DatabaseError databaseError) {
                Log.e("MainActivity", "Eşik değerleri yüklenirken hata: " + databaseError.getMessage());
            }
        });
    }
}
