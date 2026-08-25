import 'package:bulk_n_burn/screens/LoginScreen/login_account_screen.dart';
import 'package:bulk_n_burn/screens/NewAccountScreen/new_account_screen.dart';
import 'package:flutter/material.dart';
import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/widgets/CustomElevatedButton.dart';
import 'package:sign_in_button/sign_in_button.dart';

class LoadingScreen extends StatelessWidget {
  static const String id = 'loading-screen';

  const LoadingScreen({super.key});
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
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(
                height: 50,
              ),
              const Text(
                'Bulk & Burn',
                textAlign: TextAlign.center,
                style: kHeadlineTextStyle,
              ),
              Text(
                'Welcome!',
                textAlign: TextAlign.center,
                style: kHeadlineTextStyle.copyWith(fontSize: 35),
              ),
              const SizedBox(
                height: 50,
              ),
              CustomElevatedButton(
                text: "Login",
                color: Colors.purple,
                onPress: () {
                  Navigator.pushNamed(context, LoginScreen.id);
                },
              ),
              const SizedBox(
                height: 25,
              ),
              CustomElevatedButton(
                text: "New Account",
                color: Colors.purple,
                onPress: () {
                  Navigator.pushNamed(context, NewAccountScreen.id);
                },
              ),
              const SizedBox(
                height: 25,
              ), //todo: google log in option
              SignInButton(
                Buttons.googleDark,
                onPressed: () {},
                text: 'Sign in Google',
              ),
            ],
          ),
        ),
      ),
    );
  }
}
