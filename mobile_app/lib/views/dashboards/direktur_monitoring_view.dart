import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/monitoring_provider.dart';
import 'package:intl/intl.dart';

class DirekturMonitoringView extends StatefulWidget {
  const DirekturMonitoringView({super.key});

  @override
  State<DirekturMonitoringView> createState() => _DirekturMonitoringViewState();
}

class _DirekturMonitoringViewState extends State<DirekturMonitoringView> {
  final TextEditingController _searchController = TextEditingController();
  bool _isLoadingRankings = false;
  List<dynamic> _rankings = [];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() => _isLoadingRankings = true);
    final provider = context.read<MonitoringProvider>();
    final result = await provider.fetchIntegritasRanking();
    
    if (mounted) {
      setState(() {
        if (result['success']) {
          _rankings = result['data'] ?? [];
        }
        _isLoadingRankings = false;
      });
    }
  }

  Future<void> _refresh() async {
    await _loadData();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: RefreshIndicator(
        onRefresh: _refresh,
        color: AppTheme.primaryBlue,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildHeader(),
              const SizedBox(height: 32),
              _buildRankingSection(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: AppTheme.primaryBlue,
                borderRadius: BorderRadius.circular(8),
                boxShadow: [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 2))],
              ),
              child: const Text('ANALYTICAL INTELLIGENCE', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1)),
            ),
            const SizedBox(width: 8),
            Container(width: 8, height: 8, decoration: BoxDecoration(color: AppTheme.accentTeal, shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 2))),
          ],
        ),
        const SizedBox(height: 12),
        const Text('Ranking Integritas Jurusan', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: -0.5)),
        const SizedBox(height: 4),
        const Text('Berdasarkan kalkulasi MAUT (Waktu, Anggaran, IKU, LPJ).', style: TextStyle(fontSize: 12, color: AppTheme.textMuted, fontStyle: FontStyle.italic)),
      ],
    );
  }

  Widget _buildRankingSection() {
    if (_isLoadingRankings) {
      return const Center(child: Padding(padding: EdgeInsets.all(40), child: CircularProgressIndicator()));
    }

    if (_rankings.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 60),
          child: Column(
            children: [
              Icon(Icons.analytics_outlined, size: 48, color: Colors.blue.shade100),
              const SizedBox(height: 16),
              const Text('Data Belum Tersedia', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
              const SizedBox(height: 8),
              const Text('Kalkulasi ranking akan muncul saat LPJ diselesaikan.', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
            ],
          ),
        ),
      );
    }

    return Column(
      children: _rankings.asMap().entries.map((entry) {
        int index = entry.key;
        var data = entry.value;
        return _buildRankCard(index + 1, data);
      }).toList(),
    );
  }

  Widget _buildRankCard(int rank, dynamic data) {
    final String jurusan = data['nama_jurusan'] ?? 'Unknown';
    final double score = double.tryParse(data['final_score']?.toString() ?? '0') ?? 0;
    
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: rank == 1 ? Colors.amber.shade200 : AppTheme.borderLight),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Row(
        children: [
          Container(
            width: 40, height: 40,
            decoration: BoxDecoration(
              color: rank == 1 ? Colors.amber : (rank == 2 ? Colors.grey.shade300 : (rank == 3 ? Colors.orange.shade300 : Colors.blue.shade50)),
              shape: BoxShape.circle,
            ),
            child: Center(child: Text('#$rank', style: TextStyle(fontWeight: FontWeight.bold, color: rank == 1 || rank == 3 ? Colors.white : Colors.blue.shade900))),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(jurusan, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                const SizedBox(height: 4),
                LinearProgressIndicator(
                  value: score,
                  backgroundColor: Colors.grey.shade100,
                  color: score > 0.8 ? Colors.green : (score > 0.5 ? Colors.blue : Colors.orange),
                  borderRadius: BorderRadius.circular(10),
                  minHeight: 6,
                )
              ],
            ),
          ),
          const SizedBox(width: 24),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(score.toStringAsFixed(3), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
              const Text('UTILITY SCORE', style: TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
            ],
          )
        ],
      ),
    );
  }
}
