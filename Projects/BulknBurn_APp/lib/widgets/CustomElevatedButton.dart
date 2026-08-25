import 'package:flutter/material.dart';

class CustomElevatedButton extends StatelessWidget {
  final String text;
  final Color color;
  final Function onPress;

  const CustomElevatedButton({
    super.key,
    this.text = "",
    this.color = Colors.white,
    required this.onPress,
  });

  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
      onPressed: () {
        onPress();
      },
      style: ElevatedButton.styleFrom(
        shape: const StadiumBorder(),
        padding: const EdgeInsets.symmetric(vertical: 16),
        backgroundColor: color,
      ),
      child: Text(
        text,
        style: const TextStyle(fontSize: 25, color: Colors.white),
      ),
    );
  }
}
