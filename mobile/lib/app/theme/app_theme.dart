import 'package:flutter/material.dart';

abstract final class AppTheme {
  static ThemeData light() {
    const primary = Color(0xFF2A6DDB);
    const border = Color(0xFF88AEEA);
    const fill = Color(0xFFFFFEFE);

    final colorScheme =
        ColorScheme.fromSeed(
          seedColor: primary,
          brightness: Brightness.light,
        ).copyWith(
          primary: primary,
          secondary: const Color(0xFF5A8DE7),
          surface: Colors.white,
        );

    return ThemeData(
      useMaterial3: true,
      fontFamily: 'FacebookSans',
      colorScheme: colorScheme,
      scaffoldBackgroundColor: const Color(0xFFFFFCFC),
      inputDecorationTheme: InputDecorationTheme(
        filled: false,
        fillColor: fill,
        hintStyle: const TextStyle(color: Color(0xFFC9C5C8), fontSize: 14),
        contentPadding: const EdgeInsets.symmetric(horizontal: 0, vertical: 14),
        prefixIconColor: primary,
        enabledBorder: const UnderlineInputBorder(
          borderSide: BorderSide(color: border, width: 1.4),
        ),
        focusedBorder: const UnderlineInputBorder(
          borderSide: BorderSide(color: primary, width: 1.8),
        ),
        border: const UnderlineInputBorder(
          borderSide: BorderSide(color: border, width: 1.4),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          elevation: 0,
          backgroundColor: primary,
          foregroundColor: Colors.white,
          textStyle: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          minimumSize: const Size(double.infinity, 56),
        ),
      ),
      snackBarTheme: const SnackBarThemeData(
        behavior: SnackBarBehavior.floating,
      ),
    );
  }
}
