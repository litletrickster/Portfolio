
class FoodDish {
  String name;
  int calories;
  int index = 0;
  String imageLink;
  FoodDish(
      {required this.name,
      required this.calories,
      required this.imageLink,
      required this.index});
}

List<FoodDish> dishList = [
  FoodDish(
      name: 'Eggs',
      calories: 196,
      index: 0,
      imageLink:
          'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Fried_Egg_2.jpg/1280px-Fried_Egg_2.jpg'),
  FoodDish(
      name: 'Salad',
      calories: 105,
      index: 1,
      imageLink:
          'https://img.freepik.com/premium-photo/low-resolution-purism-vibrant-salad-white-background_899449-47480.jpg?w=740'),
  FoodDish(
      name: 'Pasta',
      calories: 131,
      index: 2,
      imageLink:
          'https://www.allrecipes.com/thmb/P9cv42sw6zLSdZPE2MA2Bv9ShEQ=/750x0/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/GettyImages-1228681870-2000-eece91bc1bdf4d0aa9fbcf992755cd67.jpg'),
  FoodDish(
      name: "Burger",
      calories: 254,
      imageLink:
          'https://www.shutterstock.com/image-photo/classic-hamburger-stock-photo-isolated-600nw-2282033179.jpg',
      index: 3),
  FoodDish(
      name: 'Fries',
      calories: 350,
      imageLink:
          'https://media.istockphoto.com/id/1352426221/photo/falling-french-fries-potato-fry-isolated-on-white-background-clipping-path-full-depth-of-field.jpg?s=2048x2048&w=is&k=20&c=ARIQL1jnElP2h2nfdFD2HKkEV9CY7lvmubn_7L3Vp3w=',
      index: 4),
  FoodDish(
      name: "Icecream",
      calories: 201,
      imageLink:
          'https://www.thespruceeats.com/thmb/AZAoM4fi-iItnKzoWslIwl1KrGg=/750x0/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/GettyImages-90053856-588b7aff5f9b5874ee534b04.jpg',
      index: 5),
  FoodDish(
      name: 'Donut',
      calories: 250,
      imageLink:
          'https://t3.ftcdn.net/jpg/02/45/78/98/360_F_245789812_U5YAKoU5bO1vPUlmMygG3RYwRQcDsjuD.jpg',
      index: 6),
  FoodDish(
      name: "Cheese",
      calories: 450,
      imageLink:
          'https://t3.gstatic.com/licensed-image?q=tbn:ANd9GcQgMUgf_R3eQVAHpU26GzPq8wqTC7_wVt16FVSS1dMQESbpe1F_7iUuTAmutUQmINDL',
      index: 7),
  FoodDish(
      calories: 49,
      index: 8,
      imageLink:
          'https://qph.cf2.quoracdn.net/main-qimg-cd057f217a31706b797735b6cc9ed86c',
      name: 'Milk'),
  FoodDish(
    name: '',
    imageLink:
        'https://images.emojiterra.com/google/noto-emoji/unicode-15.1/color/svg/1f468-1f373.svg',
    index: 9,
    calories: 0,
  ),
];
