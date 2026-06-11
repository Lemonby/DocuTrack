import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/bendahara_provider.dart';
import '../../theme/app_theme.dart';
import 'pencairan_tab.dart';
import 'lpj_tab.dart';
import 'bendahara_riwayat_tab.dart';

class BendaharaMainView extends StatefulWidget {
  const BendaharaMainView({super.key});

  @override
  State<BendaharaMainView> createState() => _BendaharaMainViewState();
}

class _BendaharaMainViewState extends State<BendaharaMainView> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    
    // Initial fetch
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<BendaharaProvider>(context, listen: false);
      provider.fetchPencairanList(isRefresh: true);
      provider.fetchLpjList(isRefresh: true);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Keuangan (Bendahara)', style: TextStyle(fontWeight: FontWeight.bold)),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primaryBlue,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primaryBlue,
          tabs: const [
            Tab(text: 'Pencairan Dana'),
            Tab(text: 'Verifikasi LPJ'),
            Tab(text: 'Riwayat Verifikasi'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: const [
          PencairanTab(),
          LpjTab(),
          BendaharaRiwayatTab(),
        ],
      ),
    );
  }
}
