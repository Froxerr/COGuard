package com.example.finalapp;

import android.app.Dialog;
import android.content.Context;
import android.os.Handler;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

public class CustomDialog {
    private Dialog dialog;

    public CustomDialog(Context context) {
        dialog = new Dialog(context);
        dialog.setContentView(R.layout.dialog_custom);
        dialog.setCancelable(true);
    }

    public void show(String message) {
        TextView messageTextView = dialog.findViewById(R.id.dialogMessage);
        Button okButton = dialog.findViewById(R.id.dialogOkButton);

        messageTextView.setText(message);
        okButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });

        dialog.show();

        // Automatically dismiss the dialog after 3 seconds
        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                dialog.dismiss();
            }
        }, 3000); // 3000 milliseconds = 3 seconds
    }

    public void dismiss() {
        if (dialog != null && dialog.isShowing()) {
            dialog.dismiss();
        }
    }
} 