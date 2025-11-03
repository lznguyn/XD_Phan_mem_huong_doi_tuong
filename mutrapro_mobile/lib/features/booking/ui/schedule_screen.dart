import 'package:flutter/material.dart';

class ScheduleScreen extends StatefulWidget {
  const ScheduleScreen({super.key});
  @override
  State<ScheduleScreen> createState() => _ScheduleScreenState();
}

class _ScheduleScreenState extends State<ScheduleScreen> {
  DateTime day = DateTime.now();
  List<_Slot> slots = List.generate(8, (i) => _Slot(hour: 9 + i, available: i % 3 != 0));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Lịch phòng thu')),
      body: Column(
        children: [
          Row(mainAxisAlignment: MainAxisAlignment.center, children: [
            IconButton(onPressed: () => setState(() => day = day.subtract(const Duration(days: 1))), icon: const Icon(Icons.chevron_left)),
            Text('${day.year}-${day.month}-${day.day}'),
            IconButton(onPressed: () => setState(() => day = day.add(const Duration(days: 1))), icon: const Icon(Icons.chevron_right)),
          ]),
          Expanded(
            child: ListView.separated(
              itemCount: slots.length,
              separatorBuilder: (_, __) => const Divider(height: 1),
              itemBuilder: (_, i) {
                final s = slots[i];
                return ListTile(
                  title: Text('${s.hour}:00 - ${s.hour + 1}:00'),
                  trailing: s.available
                      ? FilledButton(onPressed: () => setState(() => s.available = false), child: const Text('Đặt'))
                      : const Text('Đã kín'),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _Slot {
  _Slot({required this.hour, required this.available});
  int hour;
  bool available;
}
