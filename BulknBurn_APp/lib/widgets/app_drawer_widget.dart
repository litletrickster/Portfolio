import 'package:bulk_n_burn/providers/auth_provider.dart';
import 'package:bulk_n_burn/screens/FoodDictionaryScreen/food_dictionary_screen.dart';
import 'package:bulk_n_burn/screens/MainScreen/main_screen.dart';
import 'package:bulk_n_burn/screens/UserProfileScreen/user_profile_screen.dart';
import 'package:bulk_n_burn/screens/loading_screen.dart';
import 'package:flutter/material.dart';
import 'package:bulk_n_burn/constants.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class AppDrawerWidget extends ConsumerWidget {
  const AppDrawerWidget({
    super.key,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Drawer(
      child: Container(
        color: kContractColor,
        child: ListView(
          // Important: Remove any padding from the ListView.
          padding: EdgeInsets.zero,
          children: [
            const DrawerHeader(
              decoration: BoxDecoration(
                color: kBackgroundColor,
              ),
              child: Text(''),
            ),
            ListTile(
              title: const Text(
                'User Profile',
                style: TextStyle(fontSize: 40),
              ),
              onTap: () {
                Navigator.pushReplacementNamed(context, UserProfileScreen.id);
              },
            ),
            ListTile(
              title: const Text(
                'Main Menu',
                style: TextStyle(fontSize: 40),
              ),
              onTap: () {
                Navigator.pushReplacementNamed(context, MainScreen.id);
              },
            ),
            ListTile(
              title: const Text(
                'Food Calories',
                style: TextStyle(fontSize: 40),
              ),
              onTap: () {
                Navigator.pushReplacementNamed(
                    context, FoodDictionaryScreen.id);
              },
            ),
            ElevatedButton(
              onPressed: () {
                ref.read(authDataSourceProvider).logout();
                Navigator.pushReplacementNamed(context, LoadingScreen.id);
              },
              child: const Text('Disconnect'),
            ),
          ],
        ),
      ),
    );
  }
}
