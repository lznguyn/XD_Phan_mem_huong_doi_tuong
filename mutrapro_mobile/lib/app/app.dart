import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:easy_localization/easy_localization.dart';
import 'router.dart';
import 'theme.dart';

class MuTraProApp extends ConsumerWidget {
  const MuTraProApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final router = ref.watch(appRouterProvider);
    return MaterialApp.router(
      title: 'MuTraPro',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      darkTheme: AppTheme.dark(),
      routerConfig: router,
      supportedLocales: const [Locale('vi'), Locale('en')],
      localizationsDelegates: context.localizationDelegates,
      locale: context.locale,
    );
  }
}
