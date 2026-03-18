import 'package:flutter/material.dart';

void main() {
  runApp(const PdmuomsApp());
}

class PdmuomsApp extends StatelessWidget {
  const PdmuomsApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'PDMUOMS Mobile',
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF1B7F5A)),
        scaffoldBackgroundColor: const Color(0xFFF4F7F4),
      ),
      home: const HomePage(),
    );
  }
}

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 4,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('PDMUOMS'),
          centerTitle: false,
          bottom: const TabBar(
            isScrollable: true,
            tabs: [
              Tab(text: 'Home', icon: Icon(Icons.home_outlined)),
              Tab(text: 'Tasks', icon: Icon(Icons.checklist_rounded)),
              Tab(text: 'Messages', icon: Icon(Icons.chat_bubble_outline)),
              Tab(text: 'Profile', icon: Icon(Icons.person_outline)),
            ],
          ),
        ),
        body: const SafeArea(
          child: TabBarView(
            children: [
              _TabContent(
                title: 'Home',
                description: 'Overview content goes here.',
              ),
              _TabContent(
                title: 'Tasks',
                description: 'Task and workflow content goes here.',
              ),
              _TabContent(
                title: 'Messages',
                description: 'Conversation content goes here.',
              ),
              _TabContent(
                title: 'Profile',
                description: 'Account and settings content goes here.',
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _TabContent extends StatelessWidget {
  const _TabContent({required this.title, required this.description});

  final String title;
  final String description;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: theme.textTheme.headlineMedium?.copyWith(
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            description,
            style: theme.textTheme.bodyLarge?.copyWith(height: 1.5),
          ),
          const SizedBox(height: 24),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: const [
                BoxShadow(
                  color: Color(0x14000000),
                  blurRadius: 18,
                  offset: Offset(0, 10),
                ),
              ],
            ),
            child: Text(
              'Replace this placeholder with the $title screen.',
              style: theme.textTheme.bodyMedium?.copyWith(height: 1.5),
            ),
          ),
        ],
      ),
    );
  }
}
