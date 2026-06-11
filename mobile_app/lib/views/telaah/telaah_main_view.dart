import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'telaah_list_view.dart';
import 'monitoring_list_view.dart';

class TelaahMainView extends StatelessWidget {
  const TelaahMainView({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 3,
      child: Column(
        children: [
          Container(
            color: Colors.white,
            child: const TabBar(
              labelColor: AppTheme.primaryBlue,
              unselectedLabelColor: AppTheme.textMuted,
              indicatorColor: AppTheme.primaryBlue,
              tabs: [
                Tab(text: 'Daftar Telaah'),
                Tab(text: 'Riwayat'),
                Tab(text: 'Monitoring'),
              ],
            ),
          ),
          const Expanded(
            child: TabBarView(
              children: [
                TelaahListView(),
                MonitoringListView(isRiwayat: true),
                MonitoringListView(isRiwayat: false),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
