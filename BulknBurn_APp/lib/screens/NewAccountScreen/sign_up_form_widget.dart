import 'package:bulk_n_burn/screens/FirstLoginScreen/first_login_screen.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:bulk_n_burn/providers/auth_provider.dart';
import 'package:bulk_n_burn/providers/firebase_providers.dart';

final consInputDec = InputDecoration(
    hintText: "Email",
    hintStyle: const TextStyle(color: Colors.white),
    border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(18.0), borderSide: BorderSide.none),
    fillColor: Colors.white.withOpacity(0.4),
    filled: true,
    prefixIcon: const Icon(Icons.person));

class SignUpWidget extends ConsumerStatefulWidget {
  const SignUpWidget({super.key});

  @override
  ConsumerState<SignUpWidget> createState() => _SignUpWidgetState();
}

class _SignUpWidgetState extends ConsumerState<SignUpWidget> {
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
          decoration: consInputDec,
        ),
        const SizedBox(height: 10),
        TextField(
          style: const TextStyle(color: Colors.white),
          onChanged: (val) {
            password = val;
          },
          decoration: consInputDec.copyWith(
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
                .signup(email: email, password: password);
            print(ref.read(firebaseAuthProvider).currentUser);
            if (ref.read(firebaseAuthProvider).currentUser != null) {
              Navigator.pushReplacementNamed(context, FirstLoginScreen.id);
            }
          },
          style: ElevatedButton.styleFrom(
            shape: const StadiumBorder(),
            padding: const EdgeInsets.symmetric(vertical: 16),
            backgroundColor: Colors.purple,
          ),
          child: const Text(
            "Sign up",
            style: TextStyle(fontSize: 20, color: Colors.white),
          ),
        )
      ],
    );
  }
}
