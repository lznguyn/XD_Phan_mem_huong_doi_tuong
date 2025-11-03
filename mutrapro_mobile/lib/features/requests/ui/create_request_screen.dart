import 'package:flutter/material.dart';

class CreateRequestScreen extends StatefulWidget {
  const CreateRequestScreen({super.key});
  @override
  State<CreateRequestScreen> createState() => _CreateRequestScreenState();
}

class _CreateRequestScreenState extends State<CreateRequestScreen> {
  int step = 0;
  String type = 'transcription';
  final desc = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Tạo yêu cầu')),
      body: Stepper(
        currentStep: step,
        onStepContinue: () => setState(() => step = (step + 1).clamp(0, 3)),
        onStepCancel: () => setState(() => step = (step - 1).clamp(0, 3)),
        steps: [
          Step(
            title: const Text('Dịch vụ'),
            content: Column(
              children: [
                RadioListTile(value: 'transcription', groupValue: type, onChanged: (v) => setState(() => type = v!), title: const Text('Transcription')),
                RadioListTile(value: 'arrangement', groupValue: type, onChanged: (v) => setState(() => type = v!), title: const Text('Arrangement')),
                RadioListTile(value: 'recording', groupValue: type, onChanged: (v) => setState(() => type = v!), title: const Text('Recording Session')),
              ],
            ),
          ),
          Step(
            title: const Text('Mô tả'),
            content: TextField(
              controller: desc,
              maxLines: 5,
              decoration: const InputDecoration(border: OutlineInputBorder(), hintText: 'Mô tả yêu cầu, tempo, key, định dạng mong muốn...'),
            ),
          ),
          Step(
            title: const Text('Upload'),
            content: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: const [
                Placeholder(fallbackHeight: 120),
                SizedBox(height: 8),
                Text('Khu vực upload (sẽ tích hợp Dio + file_picker sau)'),
              ],
            ),
          ),
          const Step(title: Text('Xác nhận'), content: Text('Thanh toán đặt cọc sau khi tạo yêu cầu.')),
        ],
      ),
    );
  }
}
