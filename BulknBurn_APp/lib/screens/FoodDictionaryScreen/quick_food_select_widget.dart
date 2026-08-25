import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/models/FoodDish.dart';
import 'package:bulk_n_burn/providers/food_selected_provider.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class QuickFoodSelectWidget extends ConsumerStatefulWidget {
  const QuickFoodSelectWidget({super.key});

  @override
  ConsumerState<QuickFoodSelectWidget> createState() =>
      _QuickFoodSelectWidgetState();
}

class _QuickFoodSelectWidgetState extends ConsumerState<QuickFoodSelectWidget> {
  int selectedIndex = 0;

  void changeSelectedIndex(int ind) {
    ref.read(foodController).changeFood(ind);
  }

  @override
  Widget build(BuildContext context) {
    return SizedBox(
        height: 250,
        width: 350,
        child: ListView(children: [
          SizedBox(
            height: 350,
            child: GridView.builder(
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 3, mainAxisSpacing: 15, crossAxisSpacing: 15),
              physics: const NeverScrollableScrollPhysics(),
              itemCount: 9,
              itemBuilder: (context, index) {
                return ImgBox(
                  borderColorat: index == ref.watch(foodController).fd.index,
                  onTap: changeSelectedIndex,
                  index: index,
                  image: NetworkImage(dishList[index].imageLink),
                );
              },
            ),
          ),
        ]));
  }
}

class ImgBox extends StatelessWidget {
  const ImgBox({
    super.key,
    required this.image,
    required this.borderColorat,
    required this.onTap,
    required this.index,
  });

  final Function onTap;
  final ImageProvider image;
  final double imageSize = 90;
  final int index;
  final bool borderColorat;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        onTap(index);
      },
      child: Container(
        height: imageSize,
        width: imageSize,
        decoration: BoxDecoration(
          border: Border.all(
              width: 7,
              color: borderColorat ? Colors.orange : kBackgroundColor),
          image: DecorationImage(
            fit: BoxFit.fill,
            image: image,
          ),
        ),
      ),
    );
  }
}
