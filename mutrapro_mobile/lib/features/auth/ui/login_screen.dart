import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../app/router.dart';
import 'package:go_router/go_router.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});
  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _form = GlobalKey<FormState>();
  final _email = TextEditingController(text: 'demo@mutrapro.com');
  final _password = TextEditingController(text: 'demopass');
  bool _loading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Đăng nhập')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _form,
          child: Column(
            children: [
              TextFormField(
                controller: _email,
                decoration: const InputDecoration(labelText: 'Email'),
                validator: (v) => v != null && v.contains('@') ? null : 'Email không hợp lệ',
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _password,
                obscureText: true,
                decoration: const InputDecoration(labelText: 'Mật khẩu'),
                validator: (v) => v != null && v.length >= 6 ? null : 'Tối thiểu 6 ký tự',
              ),
              const SizedBox(height: 24),
              FilledButton(
                onPressed: _loading
                    ? null
                    : () async {
                        if (!_form.currentState!.validate()) return;
                        setState(() => _loading = true);
                        await Future.delayed(const Duration(milliseconds: 600)); // giả lập call
                        // set logged in
                        ref.read(authStateProvider.notifier).state = true;
                        if (mounted) context.go('/orders');
                        setState(() => _loading = false);
                      },
                child: _loading
                    ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Đăng nhập'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
