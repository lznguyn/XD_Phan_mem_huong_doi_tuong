import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../features/auth/ui/login_screen.dart';
import '../features/auth/ui/splash_screen.dart';
import '../features/auth/ui/onboarding_screen.dart';
import '../features/requests/ui/order_list_screen.dart';
import '../features/requests/ui/create_request_screen.dart';
import '../features/booking/ui/schedule_screen.dart';
import '../features/profile/ui/profile_screen.dart';
import 'shell.dart';

/// Trạng thái đăng nhập đơn giản (placeholder). Sau này thay bằng AuthController.
final authStateProvider = StateProvider<bool>((_) => false);

final appRouterProvider = Provider<GoRouter>((ref) {
  final loggedIn = ref.watch(authStateProvider);
  return GoRouter(
    initialLocation: '/splash',
    redirect: (ctx, state) {
      final isAuthRoute = state.matchedLocation.startsWith('/auth');
      final publicRoute = state.matchedLocation == '/onboarding' || state.matchedLocation == '/splash';
      if (!loggedIn && !isAuthRoute && !publicRoute) return '/auth/login';
      if (loggedIn && isAuthRoute) return '/orders';
      return null;
    },
    routes: [
      GoRoute(path: '/splash', builder: (_, __) => const SplashScreen()),
      GoRoute(path: '/onboarding', builder: (_, __) => const OnboardingScreen()),
      GoRoute(path: '/auth/login', builder: (_, __) => const LoginScreen()),
      ShellRoute(
        builder: (_, __, child) => HomeShell(child: child),
        routes: [
          GoRoute(path: '/orders', builder: (_, __) => const OrderListScreen()),
          GoRoute(path: '/create', builder: (_, __) => const CreateRequestScreen()),
          GoRoute(path: '/schedule', builder: (_, __) => const ScheduleScreen()),
          GoRoute(path: '/profile', builder: (_, __) => const ProfileScreen()),
        ],
      ),
    ],
  );
});
