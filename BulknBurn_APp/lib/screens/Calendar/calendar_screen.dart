import 'package:bulk_n_burn/constants.dart';
import 'package:bulk_n_burn/providers/firebase_providers.dart';
import 'package:bulk_n_burn/models/Event.dart';
import 'package:bulk_n_burn/screens/MainScreen/main_screen.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter/material.dart';
import 'package:table_calendar/table_calendar.dart';

class CalendarScreen extends ConsumerStatefulWidget {
  const CalendarScreen({super.key});

  static const String id = "calendar-screen";
  @override
  ConsumerState createState() => _CalendarScreenState();
}

class _CalendarScreenState extends ConsumerState<CalendarScreen> {
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;
  Map<DateTime, List<Event>> events = {};
  TextEditingController _eventController = TextEditingController();
  late final ValueNotifier<List<Event>> _selectedEvents;

  @override
  void initState() {
    super.initState();
    _selectedDay=_focusedDay;
    _selectedEvents = ValueNotifier(_getEventsForDay(_selectedDay!));
  }

  @override
  void dispose() {
    super.dispose();
  }

  void _onDaySelected(DateTime selectedDay, DateTime focusedDay) {
    setState(() {
      if (!isSameDay(_selectedDay, selectedDay)) {
        setState(() {
          _selectedDay = selectedDay;
          _focusedDay = focusedDay;
          _selectedEvents.value = _getEventsForDay(selectedDay);
        });
      }
    });
  }

  List<Event> _getEventsForDay(DateTime day) {
    return events[day] ?? [];
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: content(),
    );
  }


  Widget content() {
    return Column(
      children: [
        Text("Add Events"),
        FloatingActionButton(
            onPressed: () {
              showDialog(
                  context: context,
                  builder: (context) {
                    return AlertDialog(
                      scrollable: true,
                      title: Text("Event Name"),
                      content: Padding(
                        padding: EdgeInsets.all(8),
                        child: TextField(
                          controller: _eventController,
                        ),
                      ),
                      actions: [
                        ElevatedButton(onPressed: (){
                          events.addAll({_selectedDay!: [Event(_eventController.text)]
                          });
                          Navigator.of(context).pop();
                          _selectedEvents.value = _getEventsForDay(_selectedDay!);
                        }, child: Text("Submit"),
                        )
                      ],
                    );

                  });
            },
        child: Icon(Icons.add),),
        Container(
          child: TableCalendar(
            availableGestures: AvailableGestures.all,
            selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
            focusedDay: _focusedDay,
            firstDay: DateTime.utc(2000),
            lastDay: DateTime.utc(2050),
            onDaySelected: _onDaySelected,
            eventLoader: _getEventsForDay,
          ),
        ),
        SizedBox(height: 8.0),
        Expanded(
          child: ValueListenableBuilder<List<Event>>(valueListenable: _selectedEvents, builder: (context, value, _) {
            return ListView.builder(itemCount: value.length, itemBuilder: (context, index) {
              return Container(
                margin: EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                decoration: BoxDecoration(
                  border: Border.all(),
                  borderRadius: BorderRadius.circular(12)
                ),
                child: ListTile(
                  onTap: () => print('${value[index]}'),
                  title: Text("Jogging 15 Minutes"),
                ),
              );
            });
          }),
        )
      ],

    );
  }
}