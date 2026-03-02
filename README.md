# Tequila App (Flutter) v1.3.2 (API Token)

- Conecta a API con Bearer Token.
- Endpoints configurables dentro de la app.

## Pasos
1) `flutter create tequila_app`
2) Reemplaza `pubspec.yaml` y `lib/` con los del zip
3) `flutter clean && flutter pub get`
4) Agrega permisos en `android/app/src/main/AndroidManifest.xml` antes de `<application>`:
```xml
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
<uses-feature android:name="android.hardware.camera.any" android:required="true" />
<uses-permission android:name="android.permission.INTERNET" />
```
5) Build:
`flutter build apk --release --target-platform android-arm64`

APK: `build/app/outputs/flutter-apk/app-release.apk`


## Login real (API)
- Endpoint configurable en Settings: `ep_login` (default: `/auth/login`)
- POST envía: `email`, `username` (igual al email) y `password`.
- Respuesta esperada: `token` o `access_token` o `jwt` (también dentro de `data`).


## Ícono de la app
Ejecuta:
```bash
flutter pub get
flutter pub run flutter_launcher_icons
```
