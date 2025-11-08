import 'package:dio/dio.dart';
import '../core/config.dart';
import '../shared/services/token_store.dart';

class DioClient {
  DioClient(this._tokenStore) {
    dio.options.baseUrl = AppConfig.apiBase;
    dio.options.connectTimeout = const Duration(seconds: 20);
    dio.options.receiveTimeout = const Duration(seconds: 60);

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (o, h) async {
        final t = await _tokenStore.accessToken();
        if (t != null && t.isNotEmpty) {
          o.headers['Authorization'] = 'Bearer $t';
        }
        h.next(o);
      },
      onError: (e, h) async {
        // Auto refresh nếu 401
        if (e.response?.statusCode == 401) {
          final ok = await _refresh();
          if (ok) {
            final clone = await dio.fetch(e.requestOptions);
            return h.resolve(clone);
          }
        }
        h.next(e);
      },
    ));
  }

  final TokenStore _tokenStore;
  final Dio dio = Dio();

  Future<bool> _refresh() async {
    final rt = await _tokenStore.refreshToken();
    if (rt == null) return false;
    try {
      final res = await dio.post('/auth/refresh', data: {'refreshToken': rt});
      final at = res.data['accessToken'] as String?;
      final nr = res.data['refreshToken'] as String?;
      if (at != null) {
        await _tokenStore.save(at, nr ?? rt);
        return true;
      }
    } catch (_) {}
    return false;
  }
}
