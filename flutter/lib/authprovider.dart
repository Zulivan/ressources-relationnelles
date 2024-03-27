import 'package:flutter/material.dart';

class AuthProvider with ChangeNotifier {
  String? token;

  void setToken(String? value) {
    token = value;
    notifyListeners();
  }
}