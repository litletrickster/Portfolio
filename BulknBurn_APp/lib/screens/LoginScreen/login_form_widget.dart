import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/screens/MainScreen/main_screen.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:bulk_n_burn/providers/auth_provider.dart';
import 'package:bulk_n_burn/providers/firebase_providers.dart';

class LogInWidget extends ConsumerStatefulWidget {
  const LogInWidget({super.key});

  @override
  ConsumerState<LogInWidget> createState() => _SignUpWidgetState();
}

class _SignUpWidgetState extends ConsumerState<LogInWidget> {
  String email = "";
  String password = "";
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        TextField(
          style: const TextStyle(color: Colors.white),
          onChanged: (val) {
            email = val;
          },
          decoration: kConsInputDec,
        ),
        const SizedBox(height: 10),
        TextField(
          onChanged: (val) {
            password = val;
          },
          style: const TextStyle(color: Colors.white),
          decoration: kConsInputDec.copyWith(
            hintText: 'Password',
            prefixIcon: const Icon(Icons.lock),
          ),
          obscureText: true,
        ),
        const SizedBox(height: 10),
        ElevatedButton(
          onPressed: () async {
            await ref
                .read(authNotifierProvider.notifier)
                .login(email: email, password: password);
            if (ref.read(firebaseAuthProvider).currentUser != null) {
              Navigator.pushNamed(context, MainScreen.id);
            }
          },
          style: ElevatedButton.styleFrom(
            shape: const StadiumBorder(),
            padding: const EdgeInsets.symmetric(vertical: 16),
            backgroundColor: Colors.purple,
          ),
          child: const Text(
            "Log In",
            style: TextStyle(fontSize: 20, color: Colors.white),
          ),
        )
      ],
    );
  }
}
