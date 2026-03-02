import 'package:flutter/widgets.dart';
import 'prefs.dart';

class LocalizationService {
  static final ValueNotifier<Locale> currentLocale = ValueNotifier(const Locale('es'));

  static const Map<String, Map<String, String>> _localizedValues = {
    'es': {
      'login_title': 'Iniciar sesión',
      'login_btn': 'Acceder',
      'login_hint_email': 'Email',
      'login_hint_pass': 'Contraseña',
      'login_config': 'Configurar API',
      'login_welcome': 'Bienvenido a tu comunidad\nde Tequila y Mezcal',
      'login_error_empty': 'Ingresa Email y Contraseña',
      'home_title': 'Tequila App',
      'home_tab_cava': 'CAVA',
      'home_tab_scan': 'ESCANEAR',
      'home_tab_chat': 'CHAT',
      'home_btn_settings': 'Configuración',
      'home_btn_logout': 'Cerrar sesión',
      'settings_title': 'Configuración',
      'settings_lang': 'Idioma / Language',
      'scan_title': 'Escanear',
      'scan_not_found': 'No encontrado',
      'scan_ok': 'OK: {name} (guardado local)',
      'chat_title': 'Chat (API)',
      'chat_open': 'Abrir chat',
      'chat_send': 'Enviar',
      'chat_msg_hint': 'Mensaje',
      'chat_uid_hint': 'User ID destino',
      'menu_reviews': 'Reseñas',
      'menu_events': 'Eventos',
      'menu_tequila': 'Tequila',
      'menu_mezcal': 'Mezcal',
      'reviews_title': 'Reseñas',
      'reviews_add': 'Agregar Reseña',
      'events_title': 'Eventos',
      'info_tequila': 'Tequila', // Title
      'info_mezcal': 'Mezcal', // Title
      'chats_title': 'Chats',
      'chats_new': 'Nuevo Chat',
      'chats_empty': 'No hay chats activos',
    },
    'en': {
      'login_title': 'Sign In',
      'login_btn': 'Login',
      'login_hint_email': 'Email',
      'login_hint_pass': 'Password',
      'login_config': 'Configure API',
      'login_welcome': 'Welcome to your\nTequila & Mezcal community',
      'login_error_empty': 'Enter Email and Password',
      'home_title': 'Tequila App',
      'home_tab_cava': 'CELLAR',
      'home_tab_scan': 'SCAN',
      'home_tab_chat': 'CHAT',
      'home_btn_settings': 'Settings',
      'home_btn_logout': 'Logout',
      'settings_title': 'Settings',
      'settings_lang': 'Language',
      'scan_title': 'Scan',
      'scan_not_found': 'Not found',
      'scan_ok': 'OK: {name} (saved locally)',
      'chat_title': 'Chat (API)',
      'chat_open': 'Open chat',
      'chat_send': 'Send',
      'chat_msg_hint': 'Message',
      'chat_uid_hint': 'Target User ID',
      'menu_reviews': 'Reviews',
      'menu_events': 'Events',
      'menu_tequila': 'Tequila',
      'menu_mezcal': 'Mezcal',
      'reviews_title': 'Reviews',
      'reviews_add': 'Add Review',
      'events_title': 'Events',
      'info_tequila': 'Tequila',
      'info_mezcal': 'Mezcal',
      'chats_title': 'Chats',
      'chats_new': 'New Chat',
      'chats_empty': 'No active chats',
    },
  };

  static Future<void> init() async {
    final lang = await Prefs.getLanguage();
    currentLocale.value = Locale(lang);
  }

  static Future<void> changeLocale(String lang) async {
    await Prefs.setLanguage(lang);
    currentLocale.value = Locale(lang);
  }

  static String tr(String key, {Map<String, String>? args}) {
    final lang = currentLocale.value.languageCode;
    String str = _localizedValues[lang]?[key] ?? _localizedValues['es']?[key] ?? key;
    
    if (args != null) {
      args.forEach((k, v) {
        str = str.replaceAll('{$k}', v);
      });
    }
    return str;
  }
}
