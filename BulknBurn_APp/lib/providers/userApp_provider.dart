import 'package:bulk_n_burn/providers/firebase_providers.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

final userAppSnapshotProvider =
    Provider<Future<DocumentSnapshot<Map<String, dynamic>>>>((ref) {
  String? uid = ref.read(firebaseAuthProvider).currentUser?.uid;

  final val =
      ref.watch(firebaseFirestoreProvider).collection('Users').doc(uid).get();
  return val;
/*
  return UserApp(
    age: age,
    full_name: fullname,
    email: email,
    height: height,
    sex: gender,
  );*/
});
