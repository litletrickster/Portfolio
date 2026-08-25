import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/providers/firebase_providers.dart';
import 'package:bulk_n_burn/screens/MainScreen/main_screen.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter/material.dart';

class FirstLoginScreen extends ConsumerStatefulWidget {
  const FirstLoginScreen({super.key});
  static const String id = "first-login-screen";
  @override
  ConsumerState createState() => _FirstLoginScreenState();
}

class _FirstLoginScreenState extends ConsumerState<FirstLoginScreen> {
  //final userName = ref.read(firebaseAuthProvider).currentUser;

  String fullname = "";
  String gender = "";
  String height = "";
  String age = "";
  DateTime selectedDate = DateTime(2000, 10, 26);

  void _showDialog(Widget child) {
    showCupertinoModalPopup<void>(
      context: context,
      builder: (BuildContext context) => Container(
        height: 216,
        padding: const EdgeInsets.only(top: 6.0),
        // The Bottom margin is provided to align the popup above the system
        // navigation bar.
        margin: EdgeInsets.only(
          bottom: MediaQuery.of(context).viewInsets.bottom,
        ),
        // Provide a background color for the popup.
        color: CupertinoColors.systemBackground.resolveFrom(context),
        // Use a SafeArea widget to avoid system overlaps.
        child: SafeArea(
          top: false,
          child: child,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 45, vertical: 30),
          height: MediaQuery.of(context).size.height,
          color: kBackgroundColor,
          child: ListView(
            children: [
              const Text(
                'Bulk & Burn',
                style: kHeadlineTextStyle,
              ),
              const Text(
                textAlign: TextAlign.center,
                'Let`s get to know each other...',
                style: kNormalTextStyle,
              ),
              const SizedBox(
                height: 20,
              ),
              TextField(
                scrollPadding: EdgeInsets.only(
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20 * 4),
                onChanged: (val) {
                  fullname = val;
                },
                style: const TextStyle(color: Colors.white),
                decoration: kConsInputDec.copyWith(
                  hintText: 'Full name',
                  prefixIcon: const Icon(Icons.person),
                ),
              ),
              const SizedBox(
                height: 15,
              ),
              TextField(
                scrollPadding: EdgeInsets.only(
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20 * 4),
                onChanged: (val) {
                  gender = val;
                },
                style: const TextStyle(color: Colors.white),
                decoration: kConsInputDec.copyWith(
                  hintText: 'Gender',
                  prefixIcon: const Icon(Icons.circle),
                ),
              ),
              const SizedBox(
                height: 15,
              ),
              TextField(
                scrollPadding: EdgeInsets.only(
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20 * 4),
                onChanged: (val) {
                  height = val;
                },
                style: const TextStyle(color: Colors.white),
                decoration: kConsInputDec.copyWith(
                  hintText: 'Height (cm)',
                  prefixIcon: const Icon(Icons.person),
                ),
              ),
              const SizedBox(
                height: 15,
              ),
              TextField(
                onChanged: (val) {
                  age = val;
                },
                style: const TextStyle(color: Colors.white),
                decoration: kConsInputDec.copyWith(
                  hintText: 'Age',
                  prefixIcon: const Icon(Icons.lock_clock),
                ),
                scrollPadding: EdgeInsets.only(
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20 * 4),
              ),
              const SizedBox(
                height: 20,
              ),
              Container(
                decoration: BoxDecoration(
                  color: Colors.white38,
                  borderRadius: BorderRadius.circular(18),
                ),
                child: _DatePickerItem(
                  children: <Widget>[
                    const Text(
                      'Birthday',
                      style: TextStyle(color: Colors.white),
                    ),
                    CupertinoButton(
                      // Display a CupertinoDatePicker in date picker mode.
                      onPressed: () => _showDialog(
                        CupertinoDatePicker(
                          initialDateTime: selectedDate,
                          mode: CupertinoDatePickerMode.date,
                          use24hFormat: true,
                          // This shows day of week alongside day of month
                          showDayOfWeek: true,
                          // This is called when the user changes the date.
                          onDateTimeChanged: (DateTime newDate) {
                            setState(() => selectedDate = newDate);
                          },
                        ),
                      ),
                      child: Text(
                        '${selectedDate.month}-${selectedDate.day}-${selectedDate.year}',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 22.0,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(
                height: 15,
              ),
              ElevatedButton(
                onPressed: () {
                  if (fullname != "" &&
                      gender != "" &&
                      height != "" &&
                      age != "") {
                    String? email =
                        ref.read(firebaseAuthProvider).currentUser?.email;
                    String? uid =
                        ref.read(firebaseAuthProvider).currentUser?.uid;
                    /*
                    UserApp user = UserApp(
                        full_name: fullname,
                        email: email!,
                        sex: gender,
                        height: height,
                        age: age);
                    */
                    ref
                        .read(firebaseFirestoreProvider)
                        .collection('Users')
                        .doc(uid)
                        .set({
                      'email': email,
                      'full_name': fullname,
                      'gender': gender,
                      'height': height,
                      'age': age,
                      'birth_day': selectedDate,
                    });
                    //TODO: validate fields
                    Navigator.pushReplacementNamed(context, MainScreen.id);
                  } else {
                    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                      content: Text('Please fill in all fields!'),
                    ));
                  }
                },
                style: ElevatedButton.styleFrom(
                  shape: const StadiumBorder(),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  backgroundColor: Colors.purple,
                ),
                child: const Text(
                  "Save!",
                  style: TextStyle(fontSize: 20, color: Colors.white),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}

class _DatePickerItem extends StatelessWidget {
  const _DatePickerItem({required this.children});

  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return DecoratedBox(
      decoration: const BoxDecoration(
        border: Border(
          top: BorderSide(
            color: CupertinoColors.inactiveGray,
            width: 0.0,
          ),
          bottom: BorderSide(
            color: CupertinoColors.inactiveGray,
            width: 0.0,
          ),
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16.0),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: children,
        ),
      ),
    );
  }
}
