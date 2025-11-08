import 'package:dio/dio.dart';
import '../../../core/config.dart';

class AuthRepository {
  AuthRepository(this._dio);
  final Dio _dio;

  Future<Map<String, dynamic>> login(String email, String password) async {
    final res = await _dio.post(
      '${AppConfig.apiBase}/auth/login',
      data: {'email': email, 'password': password},
    );
    return Map<String, dynamic>.from(res.data);
  }

  Future<Map<String, dynamic>> me() async {
    final res = await _dio.get('${AppConfig.apiBase}/me');
    return Map<String, dynamic>.from(res.data);
  }
}
