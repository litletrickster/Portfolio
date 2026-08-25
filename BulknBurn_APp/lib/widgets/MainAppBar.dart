import 'package:flutter/material.dart';
import 'package:bulk_n_burn/constants.dart';

class MainAppBar extends StatelessWidget implements PreferredSizeWidget {
  const MainAppBar({
    super.key,
    this.titleText = "",
  });
  @override
  Size get preferredSize => const Size.fromHeight(kToolbarHeight);
  final String titleText;
  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: kBackgroundColor,
      title: Center(
        child: Text(
          titleText,
          style: kHeadlineTextStyle,
        ),
      ),
      leading: Builder(
        builder: (BuildContext context) {
          return IconButton(
            icon: const Icon(
              Icons.menu,
              size: 45,
            ),
            onPressed: () {
              Scaffold.of(context).openDrawer();
            },
            tooltip: MaterialLocalizations.of(context).openAppDrawerTooltip,
          );
        },
      ),
    );
  }
}
