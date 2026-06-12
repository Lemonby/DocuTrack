import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../models/dashboard_data.dart';
import '../../providers/superadmin_provider.dart';

class SuperadminDashboard extends StatefulWidget {
  final User user;
  final DashboardData? data;

  const SuperadminDashboard({super.key, required this.user, this.data});

  @override
  State<SuperadminDashboard> createState() => _SuperadminDashboardState();
}

class _SuperadminDashboardState extends State<SuperadminDashboard> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<SuperadminProvider>().fetchAiSettings();
    });
  }

  void _showSendNotificationDialog() {
    final titleController = TextEditingController();
    final messageController = TextEditingController();
    bool sendEmail = true;

    showDialog(
      context: context,
      builder: (ctx) {
        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              title: const Text('Send Broadcast Notification'),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    TextField(
                      controller: titleController,
                      decoration: const InputDecoration(labelText: 'Title', border: OutlineInputBorder()),
                    ),
                    const SizedBox(height: 16),
                    TextField(
                      controller: messageController,
                      maxLines: 3,
                      decoration: const InputDecoration(labelText: 'Message', border: OutlineInputBorder()),
                    ),
                    const SizedBox(height: 16),
                    CheckboxListTile(
                      title: const Text("Also send via Email"),
                      value: sendEmail,
                      onChanged: (val) {
                        setState(() {
                          sendEmail = val ?? true;
                        });
                      },
                      contentPadding: EdgeInsets.zero,
                    ),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(ctx),
                  child: const Text('Cancel'),
                ),
                ElevatedButton(
                  onPressed: () async {
                    if (titleController.text.trim().isEmpty || messageController.text.trim().isEmpty) return;
                    
                    final provider = context.read<SuperadminProvider>();
                    Navigator.pop(ctx);
                    
                    final result = await provider.sendNotification(
                      titleController.text.trim(),
                      messageController.text.trim(),
                      sendEmail: sendEmail,
                    );
                    
                    if (mounted) {
                      ScaffoldMessenger.of(this.context).showSnackBar(
                        SnackBar(
                          content: Text(result['message'] ?? (result['success'] ? 'Success' : 'Failed')),
                          backgroundColor: result['success'] ? Colors.green : Colors.red,
                        ),
                      );
                    }
                  },
                  child: const Text('Send'),
                ),
              ],
            );
          }
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          _buildHeader(),
          const SizedBox(height: 24),

          // Search and Refresh
          _buildSearchAndRefresh(),
          const SizedBox(height: 24),

          // Performance Stats
          _buildPerformanceStats(context),
          const SizedBox(height: 24),

          // AI Smart Insights
          _buildAiInsights(),
          const SizedBox(height: 24),

          // Recent Submissions
          const Text(
            'Recent Proposal Submissions',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textDark),
          ),
          const SizedBox(height: 12),
          if (widget.data?.recentItems != null && widget.data!.recentItems.isNotEmpty)
            ...widget.data!.recentItems.take(2).map((item) => _buildSubmissionCard(item.namaKegiatan, item.pemilikKegiatan ?? 'Unknown', item.status?.nama ?? 'Menunggu', item.status?.nama == 'Disetujui' ? Colors.green : Colors.blue)),
          const SizedBox(height: 24),

          // Active Directory
          const Text(
            'Recently Active',
            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textMuted, letterSpacing: 1),
          ),
          const SizedBox(height: 12),
          if (widget.data?.activeUsers != null)
            ...widget.data!.activeUsers!.map((u) => _buildActiveUser(u['name'] ?? '', u['role'] ?? '', u['last_seen'] ?? '')),
          const SizedBox(height: 24),

          // System Logs
          _buildSystemLogs(),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildHeader() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: AppTheme.primaryBlue,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 10, offset: const Offset(0, 4))
                ],
              ),
              child: const Icon(Icons.pie_chart, color: Colors.white),
            ),
            const SizedBox(width: 16),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'SuperAdmin Dashboard',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.textDark, letterSpacing: -0.5),
                ),
                const SizedBox(height: 2),
                Text(
                  'Monitoring and system management portal',
                  style: TextStyle(fontSize: 11, color: AppTheme.textMuted, fontStyle: FontStyle.italic),
                ),
              ],
            ),
          ],
        ),
        IconButton(
          icon: const Icon(Icons.campaign, color: AppTheme.primaryBlue),
          tooltip: 'Broadcast Notification',
          onPressed: _showSendNotificationDialog,
        ),
      ],
    );
  }

  Widget _buildSearchAndRefresh() {
    return Row(
      children: [
        Expanded(
          child: Container(
            height: 44,
            decoration: BoxDecoration(
              color: Colors.grey.shade50,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppTheme.borderLight),
            ),
            child: const TextField(
              decoration: InputDecoration(
                hintText: 'Search resources...',
                hintStyle: TextStyle(fontSize: 13),
                prefixIcon: Icon(Icons.search, size: 18),
                border: InputBorder.none,
                contentPadding: EdgeInsets.symmetric(vertical: 12),
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Container(
          height: 44,
          width: 44,
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppTheme.borderLight),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 4)
            ]
          ),
          child: IconButton(
            icon: const Icon(Icons.sync, size: 20, color: AppTheme.textMuted),
            onPressed: () {},
          ),
        )
      ],
    );
  }

  Widget _buildPerformanceStats(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        int crossAxisCount = constraints.maxWidth > 600 ? 4 : 2;
        return GridView.count(
          crossAxisCount: crossAxisCount,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          childAspectRatio: 1.1,
          children: [
            _buildStatBox('CPU', '${widget.data?.serverLoad?['cpu'] ?? 24}%', Icons.memory, Colors.blue),
            _buildStatBox('Memory', '${widget.data?.serverLoad?['ram'] ?? 45}%', Icons.sd_card, Colors.purple),
            _buildStatBox('Storage', '${widget.data?.serverLoad?['disk'] ?? 68}%', Icons.storage, Colors.orange),
            _buildStatBox('Traffic', '${widget.data?.serverLoad?['traffic'] ?? '1.2k'}', Icons.network_check, Colors.green, isTraffic: true),
          ],
        );
      },
    );
  }

  Widget _buildStatBox(String label, String value, IconData icon, Color color, {bool isTraffic = false}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.borderLight),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)
        ]
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, color: color, size: 16),
              ),
              const SizedBox(width: 8),
              Text(
                label.toUpperCase(),
                style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: AppTheme.textMuted, letterSpacing: 1),
              ),
            ],
          ),
          Text(
            value,
            style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: AppTheme.textDark),
          ),
          if (isTraffic)
            Row(
              children: [
                Container(width: 6, height: 6, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
                const SizedBox(width: 4),
                Text('Active Stream', style: TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: color)),
              ],
            )
          else
            Container(
              height: 6,
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(10),
              ),
              child: FractionallySizedBox(
                alignment: Alignment.centerLeft,
                widthFactor: double.parse(value.replaceAll('%', '').replaceAll('k', '')) / 100,
                child: Container(
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildAiInsights() {
    return Consumer<SuperadminProvider>(
      builder: (context, provider, child) {
        return Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppTheme.borderLight),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)
            ]
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: Colors.blue.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(Icons.psychology, color: Colors.blue, size: 20),
                      ),
                      const SizedBox(width: 12),
                      const Text(
                        'AI Monitoring Agents',
                        style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                      ),
                    ],
                  ),
                  if (provider.isLoadingAiSettings)
                    const SizedBox(width: 20, height: 20, child: CircularProgressIndicator())
                  else
                    Switch(
                      value: provider.aiAgentsActive,
                      activeColor: AppTheme.primaryBlue,
                      onChanged: (val) async {
                        final success = await provider.updateAiSettings(val);
                        if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text(success ? 'AI Agents settings updated' : provider.errorMessage),
                              backgroundColor: success ? Colors.green : Colors.red,
                            ),
                          );
                        }
                      },
                    ),
                ],
              ),
              const SizedBox(height: 16),
              Text(
                provider.aiAgentsActive 
                    ? '"The system is running optimally. CPU usage is slightly higher than usual due to recent backup tasks, but within safe limits. Memory usage is stable."'
                    : 'AI Monitoring is currently disabled. Turn on to get real-time insights and security alerts.',
                style: const TextStyle(fontSize: 12, color: AppTheme.textMuted, fontStyle: FontStyle.italic, height: 1.5),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: provider.aiAgentsActive ? () {} : null,
                  icon: const Text('Run Full Analysis', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                  label: const Icon(Icons.chevron_right, size: 16),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primaryBlue,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    elevation: 4,
                    shadowColor: AppTheme.primaryBlue.withOpacity(0.5),
                    disabledBackgroundColor: Colors.grey.shade300,
                  ),
                ),
              )
            ],
          ),
        );
      }
    );
  }

  Widget _buildSubmissionCard(String title, String user, String status, Color statusColor) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.borderLight),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 16,
                backgroundColor: Colors.grey.shade100,
                child: Text(user[0], style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textMuted)),
              ),
              const SizedBox(width: 12),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                  const SizedBox(height: 2),
                  Text(user, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted, fontWeight: FontWeight.w500)),
                ],
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              status,
              style: TextStyle(color: statusColor, fontSize: 9, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActiveUser(String name, String role, String time) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: AppTheme.borderLight),
              ),
              alignment: Alignment.center,
              child: Text(name[0], style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textMuted)),
            ),
            const SizedBox(width: 12),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(name, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                Text(role, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted, fontWeight: FontWeight.w500)),
              ],
            ),
          ],
        ),
        Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.green, shape: BoxShape.circle)),
            const SizedBox(height: 4),
            Text(time, style: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: AppTheme.textMuted)),
          ],
        )
      ],
    );
  }

  Widget _buildSystemLogs() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF1E293B), // slate-800
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10)
        ]
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'RECENT ACTIVITY LOGS',
                style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1),
              ),
              Row(
                children: [
                  Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.grey, shape: BoxShape.circle)),
                  const SizedBox(width: 4),
                  Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.grey, shape: BoxShape.circle)),
                ],
              )
            ],
          ),
          const SizedBox(height: 16),
          if (widget.data?.recentLogs != null)
            ...widget.data!.recentLogs!.map((log) => _buildLogLine(
              log['time'] ?? '',
              log['event'] ?? '',
              log['user'] ?? '',
              log['status'] == 'success' || log['status'] == 'info'
            ))
          else ...[
            _buildLogLine('10:45:21', 'User authentication success', 'Admin', true),
            _buildLogLine('10:42:10', 'System backup completed', 'System', true),
            _buildLogLine('10:40:05', 'Database connection refreshed', 'System', false),
            _buildLogLine('10:35:50', 'New proposal submitted', 'User', false),
          ],
        ],
      ),
    );
  }

  Widget _buildLogLine(String time, String event, String user, bool isSuccess) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('[$time]', style: const TextStyle(fontSize: 10, fontFamily: 'monospace', color: Colors.grey)),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              event,
              style: TextStyle(
                fontSize: 10,
                fontFamily: 'monospace',
                color: isSuccess ? Colors.greenAccent : Colors.blueAccent,
              ),
            ),
          ),
          Text('by $user', style: TextStyle(fontSize: 10, fontFamily: 'monospace', color: Colors.grey.withOpacity(0.5))),
        ],
      ),
    );
  }
}
