import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../../../shared/services/token_store.dart';
import '../../../core/dio_client.dart';
import '../data/auth_repository.dart';

class AuthState {
  final bool loggedIn;
  final String? email;
  const AuthState({required this.loggedIn, this.email});
}

final tokenStoreProvider = Provider<TokenStore>((_) => TokenStore());
final dioClientProvider = Provider<DioClient>((ref) => DioClient(ref.watch(tokenStoreProvider)));
final dioAuthedProvider = Provider<Dio>((ref) => ref.watch(dioClientProvider).dio);

final authRepoProvider = Provider<AuthRepository>((ref) => AuthRepository(ref.watch(dioAuthedProvider)));

class AuthController extends StateNotifier<AuthState> {
  AuthController(this._repo, this._store) : super(const AuthState(loggedIn: false));
  final AuthRepository _repo;
  final TokenStore _store;

  Future<void> login(String email, String password) async {
    final tokens = await _repo.login(email, password);
    await _store.save(tokens['accessToken'], tokens['refreshToken']);
    // Lấy info user
    final me = await _repo.me();
    state = AuthState(loggedIn: true, email: me['email'] as String? ?? email);
  }

  Future<void> logout() async {
    await _store.clear();
    state = const AuthState(loggedIn: false);
  }
}

final authControllerProvider =
    StateNotifierProvider<AuthController, AuthState>(
      (ref) => AuthController(ref.watch(authRepoProvider), ref.watch(tokenStoreProvider)),
    );
