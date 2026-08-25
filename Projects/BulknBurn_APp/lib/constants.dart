import 'package:flutter/material.dart';

const kBackgroundColor = Color(0xff972626);
const kContractColor = Color(0x5F4B8BFF);

final kConsInputDec = InputDecoration(
    hintText: "Email",
    hintStyle: const TextStyle(color: Colors.white),
    border: OutlineInputBorder(
      borderRadius: BorderRadius.circular(18.0),
      borderSide: BorderSide.none,
    ),
    fillColor: Colors.white.withOpacity(0.4),
    filled: true,
    prefixIcon: const Icon(Icons.person));
const kHeadlineTextStyle = TextStyle(
    color: Colors.white,
    fontSize: 50,
    fontWeight: FontWeight.bold,
    fontFamily: 'Inter');

const kNormalTextStyle = TextStyle(
    color: Colors.white,
    fontSize: 25,
    fontWeight: FontWeight.bold,
    fontFamily: 'Inter');
