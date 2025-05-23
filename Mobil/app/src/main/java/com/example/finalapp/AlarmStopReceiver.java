package com.example.finalapp;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

public class AlarmStopReceiver extends BroadcastReceiver {
    @Override
    public void onReceive(Context context, Intent intent) {
        if ("com.example.finalapp.ACTION_STOP_ALARM".equals(intent.getAction())) {
            if (context instanceof MainActivity) {
                MainActivity mainActivity = (MainActivity) context;
                mainActivity.stopAlarmAndVibrate();
            }
        }
    }
}