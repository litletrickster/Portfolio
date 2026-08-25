import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/models/DateModel.dart';
import 'package:bulk_n_burn/providers/ConsumedCalories.dart';
import 'package:bulk_n_burn/providers/firebase_providers.dart';
import 'package:bulk_n_burn/providers/userApp_provider.dart';
import 'package:bulk_n_burn/widgets/MainAppBar.dart';
import 'package:bulk_n_burn/widgets/RoundIconButtonWidget.dart';
import 'package:bulk_n_burn/widgets/app_drawer_widget.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

class UserProfileScreen extends ConsumerStatefulWidget {
  const UserProfileScreen({super.key});
  static const id = 'user-profile-screen';

  @override
  ConsumerState<UserProfileScreen> createState() => _UserProfileScreenState();
}

class _UserProfileScreenState extends ConsumerState<UserProfileScreen> {
  final kTextInfoStyle = const TextStyle(fontSize: 30, color: Colors.white);
  String age = "";
  String gender = "";
  String email = "";
  String fullname = "";
  DateTime birthDay = DateTime.now();
  String height = "";
  String uid = "";
  @override
  void initState() {
    // TODO: implement initState
    uid = ref.read(firebaseAuthProvider).currentUser!.uid;

    ref.read(userAppSnapshotProvider).then((snapShot) {
      Map<dynamic, dynamic>? values = snapShot.data();
      if (values != null) {
        age = values['age'];
        gender = values['gender'];
        email = values['email'];
        fullname = values['full_name'];
        height = values['height'];
        birthDay = values['birth_day']!.toDate();
        setState(() {});
      }
    });

    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    int todaysCalories = ref.watch(consumedCaloriesController).caloriesToday;
    return Scaffold(
      drawer: const AppDrawerWidget(),
      appBar: const MainAppBar(),
      body: SafeArea(
        child: Container(
          padding: const EdgeInsets.all(30),
          color: kBackgroundColor,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      RoundIconButtonWidget(
                        onPressed: () {},
                        icon: Icons.person,
                        iconColor: Colors.black,
                      ),
                      Text(
                        fullname,
                        style: kTextInfoStyle,
                      ),
                    ],
                  ),
                  const SizedBox(
                    height: 20,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Gender', style: kTextInfoStyle),
                      Text(gender, style: kTextInfoStyle),
                    ],
                  ),
                  const SizedBox(
                    height: 20,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Age', style: kTextInfoStyle),
                      Text(age, style: kTextInfoStyle),
                    ],
                  ),
                  const SizedBox(
                    height: 20,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Birthday', style: kTextInfoStyle),
                      Text("${birthDay.day}-${birthDay.month}-${birthDay.year}",
                          style: kTextInfoStyle.copyWith(fontSize: 25)),
                    ],
                  ),
                  const SizedBox(
                    height: 20,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      //todo: calculate lost/gained calories
                      Text('Calories Gained', style: kTextInfoStyle),
                      Text(
                          '+${NumberFormat.decimalPattern('en_US').format(todaysCalories)}',
                          style: kTextInfoStyle),
                    ],
                  ),
                ],
              ),
              HistoryFoodListWidget(
                eatenMeals: ref
                    .read(firebaseFirestoreProvider)
                    .collection('consumed_food')
                    .doc(uid != "" ? uid : "21-01-2024")
                    .collection(DateModel.getCurrentDate().toString())
                    .snapshots(),
                uid: uid != "" ? uid : "21-01-2024",
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class HistoryFoodListWidget extends StatefulWidget {
  const HistoryFoodListWidget({
    super.key,
    required this.eatenMeals,
    required this.uid,
  });
  final String uid;
  final Stream<QuerySnapshot> eatenMeals;
  @override
  State<HistoryFoodListWidget> createState() => _HistoryFoodListWidgetState();
}

class _HistoryFoodListWidgetState extends State<HistoryFoodListWidget> {
  @override
  Widget build(BuildContext context) {
    return StreamBuilder<QuerySnapshot>(
        stream: widget.eatenMeals,
        builder: (BuildContext context, AsyncSnapshot<QuerySnapshot> snapshot) {
          if (snapshot.hasError) {
            print("hopa");
            return const Text('Something went wrong');
          }

          if (snapshot.connectionState == ConnectionState.waiting) {
            print('asteapta');
            return const Text(
              "Loading",
              style: TextStyle(color: Colors.red, fontSize: 50),
            );
          }

          return Column(
            children: [
              const Text("Food History Today", style: kNormalTextStyle),
              Container(
                color: Colors.grey,
                height: 320,
                child: ListView(
                  children: snapshot.data!.docs
                      .map((DocumentSnapshot document) {
                        Map<String, dynamic> data =
                            document.data()! as Map<String, dynamic>;
                        return (data['name'] != null)
                            ? Center(
                                child: Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    '${data['name']}',
                                    style: const TextStyle(
                                        color: Colors.white, fontSize: 25),
                                  ),
                                  Text(
                                    '${data['grams']}g',
                                    style: const TextStyle(
                                        color: Colors.white, fontSize: 25),
                                  ),
                                  Text(
                                    '${data['calories']} cal',
                                    style: const TextStyle(
                                        color: Colors.white, fontSize: 25),
                                  ),
                                ],
                              ))
                            : const Text('');
                      })
                      .toList()
                      .cast(),
                ),
              ),
            ],
          );
        });
  }
}

/*
Text(
                'History',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 30),
              ),
 */
