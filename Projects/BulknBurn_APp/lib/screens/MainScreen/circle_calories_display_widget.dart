import 'package:bulk_n_burn/models/DateModel.dart';
import 'package:bulk_n_burn/providers/ConsumedCalories.dart';
import 'package:bulk_n_burn/providers/firebase_providers.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

class CircleCaloriesDisplay extends ConsumerWidget {
  const CircleCaloriesDisplay({
    super.key,
  });

  static const kCalButStyle =
      TextStyle(color: Colors.white, fontSize: 30, fontWeight: FontWeight.bold);
//todo: realtime calories tracker
  //todo: screen height
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    String? uid = ref.read(firebaseAuthProvider).currentUser?.uid;
    int todaysCalories = ref.watch(consumedCaloriesController).caloriesToday;
    ref
        .read(firebaseFirestoreProvider)
        .collection('consumed_food')
        .doc(uid!)
        .collection(DateModel.getCurrentDate().toString())
        .doc('Calories')
        .get()
        .then((snapShot) {
      Map<dynamic, dynamic>? values = snapShot.data();
      if (values != null) {
        //checking todays calories
        //todo: solve the double calorie counter
        if (ref.read(consumedCaloriesController).caloriesToday == 0) {
          //print(ref.read(consumedCaloriesController).caloriesToday);
          ref
              .read(consumedCaloriesController)
              .setCalories(values['calories'].toInt());
        }
      }
    });
    return Container(
      width: 250,
      height: 250,
      decoration: const BoxDecoration(
        color: Colors.deepOrangeAccent,
        shape: BoxShape.circle,
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            '+${NumberFormat.decimalPattern('en_US').format(todaysCalories)}',
            style: kCalButStyle,
          ),
          const SizedBox(
            height: 10,
          ),
          const Text('Calories Today', style: kCalButStyle)
        ],
      ),
    );
  }
}
