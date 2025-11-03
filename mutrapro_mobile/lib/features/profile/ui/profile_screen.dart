import 'package:flutter/material.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Hồ sơ')),
      body: ListView(
        children: const [
          ListTile(title: Text('Tên'), subtitle: Text('Demo User')),
          ListTile(title: Text('Email'), subtitle: Text('demo@mutrapro.com')),
        ],
      ),
    );
  }
}
