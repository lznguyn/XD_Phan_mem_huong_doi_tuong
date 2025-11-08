import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStore {
  final _s = const FlutterSecureStorage();
  static const _kAT = 'auth.at';
  static const _kRT = 'auth.rt';

  Future<void> save(String accessToken, String refreshToken) async {
    await _s.write(key: _kAT, value: accessToken);
    await _s.write(key: _kRT, value: refreshToken);
  }

  Future<String?> accessToken() => _s.read(key: _kAT);
  Future<String?> refreshToken() => _s.read(key: _kRT);
  Future<void> clear() async => _s.deleteAll();
}
