import 'package:flutter/material.dart';
import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/screens/LoginScreen/login_form_widget.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  static const String id = 'login-screen';

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Container(
          width: double.infinity,
          color: kBackgroundColor,
          padding: const EdgeInsets.symmetric(
            horizontal: 15,
            vertical: 35,
          ),
          child: Column(
            children: [
              const SizedBox(
                height: 50,
              ),
              const Text(
                'Bulk & Burn',
                style: kHeadlineTextStyle,
              ),
              Text(
                'Log in',
                style: kHeadlineTextStyle.copyWith(fontSize: 35),
              ),
              const SizedBox(
                height: 15,
              ),
              const LogInWidget(),
            ],
          ),
        ),
      ),
    );
  }
}
