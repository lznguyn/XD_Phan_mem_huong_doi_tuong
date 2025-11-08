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
import '../features/auth/logic/auth_controller.dart';

final appRouterProvider = Provider<GoRouter>((ref) {
  final auth = ref.watch(authControllerProvider);
  return GoRouter(
    initialLocation: '/auth/login',
    redirect: (ctx, s) {
      final loggingIn = s.matchedLocation.startsWith('/auth');
      final publicRoute = s.matchedLocation == '/onboarding' || s.matchedLocation == '/splash';
      if (!auth.loggedIn && !loggingIn && !publicRoute) return '/auth/login';
      if (auth.loggedIn && loggingIn) return '/orders';
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
