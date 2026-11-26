CAPACITOR ANDROID PACKAGING

This folder documents the Android wrapper setup. The Android project itself is generated on your development PC with Capacitor CLI after deploying the PHP backend to HTTPS hosting.

Requirements: Node.js LTS, Android Studio, JDK supported by your Android Studio version.

Commands (run from project root):
  npm init -y
  npm install @capacitor/core @capacitor/cli @capacitor/android
  npx cap init RAAZ com.raaz.privatechat --web-dir www
  npx cap add android
  npx cap sync android
  npx cap open android

Important:
- PHP/MySQL backend must be hosted on an HTTPS server; Android cannot execute PHP/MySQL locally as a normal web app.
- Set server.url in capacitor.config.json to your actual HTTPS deployment URL.
- For FLAG_SECURE, add native Android code in MainActivity and test on real devices. This package does not claim website screenshot blocking.
- The Android folder is a setup guide, not a prebuilt APK or complete generated Android Studio project.
