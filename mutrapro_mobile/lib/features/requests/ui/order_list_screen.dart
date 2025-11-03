import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class OrderListScreen extends StatelessWidget {
  const OrderListScreen({super.key});
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Đơn hàng')),
      body: ListView.separated(
        itemCount: 8, // mock
        separatorBuilder: (_, __) => const Divider(height: 1),
        itemBuilder: (ctx, i) => ListTile(
          title: Text('Yêu cầu #$i'),
          subtitle: const Text('status: In Progress'),
          trailing: const Icon(Icons.chevron_right),
          onTap: () {},
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.go('/create'),
        label: const Text('Tạo yêu cầu'),
        icon: const Icon(Icons.add),
      ),
    );
  }
}
