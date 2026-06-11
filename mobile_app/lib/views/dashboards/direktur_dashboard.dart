import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../models/dashboard_data.dart';
import 'package:intl/intl.dart';

class DirekturDashboard extends StatefulWidget {
  final User user;
  final DashboardData? data;

  const DirekturDashboard({super.key, required this.user, this.data});

  @override
  State<DirekturDashboard> createState() => _DirekturDashboardState();
}

class _DirekturDashboardState extends State<DirekturDashboard> {
  String _selectedUnit = 'Institusi';
  
  @override
  Widget build(BuildContext context) {
    if (widget.data == null) {
      return const Center(child: CircularProgressIndicator());
    }

    return SingleChildScrollView(
      padding: const EdgeInsets.all(20.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Welcome Banner & Executive Intelligence
          _buildExecutiveHeader(),
          const SizedBox(height: 24),

          // Unit Matrix
          const Text(
            'Unit Analysis Matrix',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textMuted, letterSpacing: 1.2),
          ),
          const SizedBox(height: 12),
          _buildUnitMatrix(),
          const SizedBox(height: 24),

          // Financial Pulse
          _buildFinancialPulse(),
          const SizedBox(height: 24),

          // Health / Insights
          _buildHealthAndInsight(),
          const SizedBox(height: 24),

          // Strategic IKU Progress
          _buildIkuProgress(),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildExecutiveHeader() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Executive Intelligence',
                  style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppTheme.textDark, letterSpacing: -0.5),
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.green, shape: BoxShape.circle)),
                    const SizedBox(width: 6),
                    const Text(
                      'REAL-TIME FISCAL OVERVIEW',
                      style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: AppTheme.textMuted, letterSpacing: 1),
                    ),
                  ],
                ),
              ],
            ),
          ],
        ),
        const SizedBox(height: 16),
        // Year Selector
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: ['2024', '2025', '2026', '2027'].map((year) {
              bool isActive = year == '2026';
              return Container(
                margin: const EdgeInsets.only(right: 8),
                child: ElevatedButton(
                  onPressed: () {},
                  style: ElevatedButton.styleFrom(
                    backgroundColor: isActive ? AppTheme.primaryBlue : Colors.grey.shade100,
                    foregroundColor: isActive ? Colors.white : AppTheme.textMuted,
                    elevation: isActive ? 4 : 0,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 0),
                  ),
                  child: Text(year, style: TextStyle(fontWeight: isActive ? FontWeight.bold : FontWeight.normal)),
                ),
              );
            }).toList(),
          ),
        ),
      ],
    );
  }

  Widget _buildUnitMatrix() {
    final List<String> listJurusan = widget.data?.listJurusan ?? ['TIK', 'TGP', 'Elektro', 'Mesin', 'Sipil'];
    
    final List<Map<String, dynamic>> units = [
      {'name': 'Institusi', 'icon': Icons.public},
      ...listJurusan.take(6).map((j) {
        IconData iconData = Icons.account_balance;
        if (j.contains('TIK')) iconData = Icons.computer;
        if (j.contains('TGP')) iconData = Icons.color_lens;
        if (j.contains('Elektro')) iconData = Icons.electrical_services;
        if (j.contains('Mesin')) iconData = Icons.engineering;
        if (j.contains('Sipil')) iconData = Icons.architecture;
        if (j.contains('Akuntansi')) iconData = Icons.calculate;
        return {'name': j, 'icon': iconData};
      }),
    ];

    return Wrap(
      spacing: 12,
      runSpacing: 12,
      children: units.map((u) {
        bool isActive = _selectedUnit == u['name'];
        return InkWell(
          onTap: () => setState(() => _selectedUnit = u['name']),
          borderRadius: BorderRadius.circular(20),
          child: Container(
            width: 100,
            padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 8),
            decoration: BoxDecoration(
              color: isActive ? AppTheme.primaryBlue : Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: isActive ? AppTheme.primaryBlue : AppTheme.borderLight),
              boxShadow: isActive ? [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 4))] : [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)],
            ),
            child: Column(
              children: [
                Icon(u['icon'] as IconData, color: isActive ? Colors.white : AppTheme.textMuted, size: 28),
                const SizedBox(height: 8),
                Text(
                  (u['name'] as String).toUpperCase(),
                  textAlign: TextAlign.center,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.bold,
                    color: isActive ? Colors.white : AppTheme.textMuted,
                    letterSpacing: 0.5,
                  ),
                ),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _buildFinancialPulse() {
    final budget = widget.data?.budget;
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    
    final totalRealized = budget?['total_realized'] ?? 0;
    final totalAllocated = budget?['total_allocated'] ?? 1;
    final remaining = budget?['remaining'] ?? 0;
    final percentage = budget?['percentage'] ?? 0.0;

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(colors: [Color(0xFF0F172A), Color(0xFF1E3A8A)], begin: Alignment.topLeft, end: Alignment.bottomRight),
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(color: const Color(0xFF1E3A8A).withOpacity(0.4), blurRadius: 20, offset: const Offset(0, 10)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.white.withOpacity(0.05)),
            ),
            child: const Text(
              'TOTAL REALISASI DANA',
              style: TextStyle(color: Colors.lightBlueAccent, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1),
            ),
          ),
          const SizedBox(height: 16),
          Text(
            formatter.format(totalRealized),
            style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.w900, letterSpacing: -1),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 4),
          Text(
            'Pagu Tahunan: ${formatter.format(totalAllocated)}',
            style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 12, fontWeight: FontWeight.w500),
          ),
          const SizedBox(height: 32),
          
          // Stats Row
          Row(
            children: [
              _buildPulseStat('SISA SALDO', 'Rp ${(remaining / 1000000).toStringAsFixed(1)}jt', Icons.account_balance_wallet, Colors.blueAccent),
              const SizedBox(width: 8),
              _buildPulseStat('SERAPAN', '$percentage%', Icons.pie_chart, Colors.greenAccent),
              const SizedBox(width: 8),
              _buildPulseStat('USULAN', '${widget.data?.stats.totalUsulan ?? 0}', Icons.receipt_long, Colors.purpleAccent),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPulseStat(String label, String value, IconData icon, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.05),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: Colors.white.withOpacity(0.05)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(height: 8),
            Text(
              label,
              style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 8, fontWeight: FontWeight.bold, letterSpacing: 1),
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.bold),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHealthAndInsight() {
    return Row(
      children: [
        // Fiscal Health
        Expanded(
          child: Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              border: Border.all(color: AppTheme.borderLight),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: const Offset(0, 8))],
            ),
            child: Column(
              children: [
                Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    border: Border.all(color: AppTheme.accentTeal, width: 6),
                  ),
                  alignment: Alignment.center,
                  child: const Text('94.2', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark)),
                ),
                const SizedBox(height: 12),
                const Text('Fiscal Health', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
              ],
            ),
          ),
        ),
        const SizedBox(width: 16),
        // Quick Insight
        Expanded(
          flex: 2,
          child: Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppTheme.primaryBlue,
              borderRadius: BorderRadius.circular(24),
              boxShadow: [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 8))],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.lightbulb, color: Colors.yellowAccent, size: 16),
                    const SizedBox(width: 6),
                    Text('QUICK INSIGHT', style: TextStyle(color: Colors.white.withOpacity(0.9), fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1)),
                  ],
                ),
                const SizedBox(height: 12),
                const Text(
                  '"Efisiensi operasional meningkat signifikan bulan ini."',
                  style: TextStyle(color: Colors.white, fontSize: 12, fontStyle: FontStyle.italic, height: 1.5),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildIkuProgress() {
    final List<dynamic> ikuAchievements = widget.data?.ikuAchievements ?? [];

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF020617), // slate-950
        borderRadius: BorderRadius.circular(32),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.2), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'STRATEGIC IKU PROGRESS',
            style: TextStyle(color: Colors.blueAccent, fontSize: 11, fontWeight: FontWeight.bold, letterSpacing: 1.5),
          ),
          const SizedBox(height: 24),
          
          if (ikuAchievements.isEmpty)
            const Text('Belum ada data IKU.', style: TextStyle(color: Colors.white54, fontSize: 12))
          else
            ...ikuAchievements.map((iku) {
              final String title = iku['nama'] ?? '';
              final double target = (iku['target'] ?? 100).toDouble();
              final double capaian = (iku['capaian'] ?? 0).toDouble();
              final double percentage = target > 0 ? (capaian / target) * 100 : 0;
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: _buildIkuBar(title, percentage),
              );
            }),
          
          const SizedBox(height: 16),
          const Divider(color: Colors.white12),
          const SizedBox(height: 16),
          
          Center(
            child: Column(
              children: [
                Text(
                  'SISA WAKTU ANGGARAN',
                  style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1),
                ),
                const SizedBox(height: 8),
                const Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('224', style: TextStyle(color: Colors.white, fontSize: 36, fontWeight: FontWeight.w900, height: 1)),
                    SizedBox(width: 8),
                    Padding(
                      padding: EdgeInsets.only(bottom: 6),
                      child: Text('Hari Kerja', style: TextStyle(color: Colors.white54, fontSize: 10, fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
              ],
            ),
          )
        ],
      ),
    );
  }

  Widget _buildIkuBar(String title, double percentage) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(
              child: Text(
                title.toUpperCase(),
                style: const TextStyle(color: Colors.white70, fontSize: 10, fontWeight: FontWeight.bold),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            Text(
              '${percentage.toInt()}%',
              style: const TextStyle(color: Colors.blueAccent, fontSize: 12, fontWeight: FontWeight.w900),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Container(
          height: 6,
          width: double.infinity,
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: FractionallySizedBox(
            alignment: Alignment.centerLeft,
            widthFactor: (percentage / 100).clamp(0.0, 1.0),
            child: Container(
              decoration: BoxDecoration(
                color: Colors.blueAccent,
                borderRadius: BorderRadius.circular(10),
                boxShadow: [BoxShadow(color: Colors.blueAccent.withOpacity(0.5), blurRadius: 6)],
              ),
            ),
          ),
        ),
      ],
    );
  }
}
