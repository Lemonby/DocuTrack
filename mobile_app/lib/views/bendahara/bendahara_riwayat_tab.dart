import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import 'bendahara_pencairan_detail_view.dart';
import 'bendahara_lpj_detail_view.dart';

class BendaharaRiwayatTab extends StatelessWidget {
  const BendaharaRiwayatTab({super.key});

  @override
  Widget build(BuildContext context) {
    // Dummy Data for Riwayat Bendahara
    final List<Map<String, dynamic>> riwayatItems = [
      {
        'id': 102,
        'type': 'pencairan',
        'title': 'Workshop AI for Students',
        'pengusul': 'Andi Rahman',
        'prodi': 'Sistem Informasi',
        'date': '15 Jun 2026',
        'status': 'Sudah Dicairkan',
        'statusColor': Colors.teal,
      },
      {
        'id': 203,
        'type': 'lpj',
        'title': 'Seminar Nasional Teknologi',
        'pengusul': 'Nia Ramadhani',
        'prodi': 'Sistem Informasi',
        'date': '28 Jun 2026',
        'status': 'Disetujui',
        'statusColor': Colors.teal,
      },
    ];

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      body: ListView.builder(
        padding: const EdgeInsets.all(20.0),
        itemCount: riwayatItems.length,
        itemBuilder: (context, index) {
          final item = riwayatItems[index];
          final bool isPencairan = item['type'] == 'pencairan';

          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(
                  color: Colors.green.withOpacity(0.05),
                  blurRadius: 15,
                  offset: const Offset(0, 8),
                )
              ],
              border: Border(
                left: BorderSide(
                  color: Colors.green.shade600,
                  width: 6,
                ),
              ),
            ),
            child: Material(
              color: Colors.transparent,
              child: InkWell(
                borderRadius: BorderRadius.circular(20),
                onTap: () {
                  if (isPencairan) {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => BendaharaPencairanDetailView(id: item['id'], status: item['status']),
                      ),
                    );
                  } else {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => BendaharaLpjDetailView(id: item['id'], status: item['status']),
                      ),
                    );
                  }
                },
                child: Padding(
                  padding: const EdgeInsets.all(20.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.green.shade50,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              (item['status'] as String).toUpperCase(),
                              style: TextStyle(
                                color: Colors.green.shade700,
                                fontSize: 10,
                                fontWeight: FontWeight.w900,
                                letterSpacing: 1,
                              ),
                            ),
                          ),
                          Text(
                            item['date'],
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.blueGrey),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Text(
                        item['title'],
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.textDark, height: 1.3),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Icon(Icons.history_rounded, size: 14, color: Colors.grey.shade400),
                          const SizedBox(width: 4),
                          Text(
                            isPencairan ? 'Riwayat Pencairan Dana' : 'Riwayat Verifikasi LPJ',
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade600),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Icon(Icons.person_outline, size: 14, color: Colors.grey.shade400),
                          const SizedBox(width: 4),
                          Text(
                            item['pengusul'],
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade600),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Icon(Icons.apartment_rounded, size: 14, color: Colors.grey.shade400),
                          const SizedBox(width: 4),
                          Text(
                            item['prodi'],
                            style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade600),
                          ),
                        ],
                      ),
                      const Padding(
                        padding: EdgeInsets.symmetric(vertical: 16),
                        child: Divider(height: 1),
                      ),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Icon(isPencairan ? Icons.account_balance_wallet_rounded : Icons.receipt_long_rounded, size: 16, color: Colors.blueGrey),
                              const SizedBox(width: 6),
                              Text(isPencairan ? 'Rp 15.000.000' : 'Dokumen LPJ', style: const TextStyle(fontWeight: FontWeight.w900, color: Colors.blueGrey, fontSize: 13)),
                            ],
                          ),
                          Row(
                            children: [
                              Text(
                                'DETAIL',
                                style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Colors.green.shade600),
                              ),
                              const SizedBox(width: 4),
                              Icon(Icons.arrow_forward_rounded, size: 14, color: Colors.green.shade600),
                            ],
                          )
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}
