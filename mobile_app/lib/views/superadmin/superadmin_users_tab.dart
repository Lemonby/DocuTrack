import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../models/user.dart';
import '../../services/api_service.dart';

class SuperadminUsersTab extends StatefulWidget {
  final User user;
  const SuperadminUsersTab({super.key, required this.user});

  @override
  State<SuperadminUsersTab> createState() => _SuperadminUsersTabState();
}

class _SuperadminUsersTabState extends State<SuperadminUsersTab> {
  final TextEditingController _searchController = TextEditingController();
  String _filterRole = 'All';
  String _filterStatus = 'All';

  List<dynamic> _users = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchUsers();
    });
  }
  
  Future<void> _fetchUsers() async {
      setState(() { _isLoading = true; });
      try {
          final api = Provider.of<ApiService>(context, listen: false);
          final res = await api.client.get('/v1/superadmin/users');
          if (res.data['success']) {
             if(mounted) {
                 setState(() {
                    _users = res.data['data'] ?? [];
                    _isLoading = false;
                 });
             }
          }
      } catch (e) {
         if(mounted) {
             setState(() { _isLoading = false; });
             ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal memuat pengguna')));
         }
      }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Color _getRoleColor(String role) {
    switch (role) {
      case 'SuperAdmin':
        return Colors.indigo;
      case 'Admin':
        return Colors.blue;
      case 'Verifikator':
        return Colors.purple;
      case 'PPK':
        return Colors.teal;
      case 'Bendahara':
        return Colors.amber.shade700;
      case 'Direktur':
      case 'Wadir':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  void _showUserModal({Map<String, dynamic>? user}) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _buildUserModal(user: user),
    );
  }

  Widget _buildUserModal({Map<String, dynamic>? user}) {
    final isEdit = user != null;
    String name = user?['nama'] ?? '';
    String email = user?['email'] ?? '';
    String role = user?['role'] ?? 'Pengusul';
    String department = user?['nama_jurusan'] ?? 'Teknik Informatika';
    bool isActive = user?['status'] == 'Aktif';
    
    return Container(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(isEdit ? 'Edit User Profile' : 'Create New User', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                    Text(isEdit ? 'Update user account information' : 'Register a new member to the system', style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                  ],
                ),
                IconButton(
                  icon: const Icon(Icons.close, color: Colors.grey),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
            const SizedBox(height: 24),
            _buildTextField('Full Name', 'Enter full name', initialValue: name, onChanged: (v) => name = v),
            const SizedBox(height: 16),
            _buildTextField('Email Address', 'Enter email address', isEmail: true, initialValue: email, onChanged: (v) => email = v),
            const SizedBox(height: 16),
            _buildDropdown('User Role', ['SuperAdmin', 'Admin', 'Verifikator', 'PPK', 'Bendahara', 'Direktur', 'Wadir', 'Pengusul'], initialValue: role, onChanged: (v) => role = v!),
            const SizedBox(height: 16),
            _buildDropdown('Department', ['Teknik Informatika', 'Teknik Elektro', 'Akuntansi', 'Manajemen', 'Pusat'], initialValue: department, onChanged: (v) => department = v!),
            const SizedBox(height: 16),
            if (isEdit) SwitchListTile(
              title: const Text('Status Aktif', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
              value: isActive,
              activeColor: AppTheme.primaryBlue,
              onChanged: (v) => setState(() => isActive = v),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () async {
                  final api = Provider.of<ApiService>(context, listen: false);
                  try {
                      if (isEdit) {
                          await api.client.put('/v1/superadmin/users/${user['user_id']}', data: {
                              'nama': name,
                              'email': email,
                              'role': role,
                              'jurusan': department,
                              'status': isActive ? 'Aktif' : 'Non-Aktif'
                          });
                      } else {
                          await api.client.post('/v1/superadmin/users', data: {
                              'nama': name,
                              'email': email,
                              'password': 'password',
                              'role': role,
                              'jurusan': department,
                          });
                      }
                      
                      if (!mounted) return;

                      Navigator.pop(context);
                      _fetchUsers();
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(isEdit ? 'User updated successfully' : 'User created successfully')));
                  } catch (e) {
                      if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menyimpan user')));
                      }
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryBlue,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text(isEdit ? 'Save Changes' : 'Create User', style: const TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTextField(String label, String hint, {bool isEmail = false, String? initialValue, Function(String)? onChanged}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
        const SizedBox(height: 8),
        TextFormField(
          initialValue: initialValue,
          onChanged: onChanged,
          keyboardType: isEmail ? TextInputType.emailAddress : TextInputType.text,
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 14),
            filled: true,
            fillColor: Colors.grey.shade50,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppTheme.primaryBlue)),
          ),
        ),
      ],
    );
  }

  Widget _buildDropdown(String label, List<String> items, {String? initialValue, Function(String?)? onChanged}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label.toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: items.contains(initialValue) ? initialValue : null,
          decoration: InputDecoration(
            filled: true,
            fillColor: Colors.grey.shade50,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppTheme.primaryBlue)),
          ),
          items: items.map((e) => DropdownMenuItem(value: e, child: Text(e, style: const TextStyle(fontSize: 14)))).toList(),
          onChanged: onChanged ?? (val) {},
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: AppTheme.primaryBlue,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.3), blurRadius: 10, offset: const Offset(0, 4))],
                ),
                child: const Icon(Icons.people_alt, color: Colors.white),
              ),
              const SizedBox(width: 16),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('User Management', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.textDark, letterSpacing: -0.5)),
                  const SizedBox(height: 2),
                  Text('Manage system access, roles, and security', style: TextStyle(fontSize: 11, color: AppTheme.textMuted, fontStyle: FontStyle.italic)),
                ],
              ),
            ],
          ),
          const SizedBox(height: 24),

          // Search and Action Bar
          Row(
            children: [
              Expanded(
                child: Container(
                  height: 48,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade50,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppTheme.borderLight),
                  ),
                  child: TextField(
                    controller: _searchController,
                    decoration: const InputDecoration(
                      hintText: 'Search user...',
                      hintStyle: TextStyle(fontSize: 13),
                      prefixIcon: Icon(Icons.search, size: 18),
                      border: InputBorder.none,
                      contentPadding: EdgeInsets.symmetric(vertical: 14),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              ElevatedButton.icon(
                onPressed: () => _showUserModal(),
                icon: const Icon(Icons.add, size: 18),
                label: const Text('Add User', style: TextStyle(fontWeight: FontWeight.bold)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryBlue,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 4,
                  shadowColor: AppTheme.primaryBlue.withOpacity(0.4),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Filters
          Row(
            children: [
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade50,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: AppTheme.borderLight),
                  ),
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      isExpanded: true,
                      value: _filterRole,
                      icon: const Icon(Icons.keyboard_arrow_down, size: 16),
                      style: const TextStyle(fontSize: 12, color: AppTheme.textDark, fontWeight: FontWeight.bold),
                      items: ['All', 'SuperAdmin', 'Admin', 'Verifikator', 'PPK', 'Bendahara', 'Direktur', 'Wadir', 'Pengusul'].map((String value) {
                        return DropdownMenuItem<String>(value: value, child: Text(value == 'All' ? 'All Roles' : value));
                      }).toList(),
                      onChanged: (newValue) {
                        setState(() {
                          _filterRole = newValue!;
                        });
                      },
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(
                    color: Colors.grey.shade50,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: AppTheme.borderLight),
                  ),
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      isExpanded: true,
                      value: _filterStatus,
                      icon: const Icon(Icons.keyboard_arrow_down, size: 16),
                      style: const TextStyle(fontSize: 12, color: AppTheme.textDark, fontWeight: FontWeight.bold),
                      items: ['All', 'Aktif', 'Non-Aktif'].map((String value) {
                        return DropdownMenuItem<String>(value: value, child: Text(value == 'All' ? 'All Status' : value));
                      }).toList(),
                      onChanged: (newValue) {
                        setState(() {
                          _filterStatus = newValue!;
                        });
                      },
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),

          // Users List
          if (_isLoading)
            const Center(child: CircularProgressIndicator())
          else if (_users.isEmpty)
            const Center(child: Text("Tidak ada data pengguna"))
          else
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _users.length,
            itemBuilder: (context, index) {
              final user = _users[index];
              
              // Apply filters
              if (_filterRole != 'All' && user['role'] != _filterRole) return const SizedBox.shrink();
              if (_filterStatus != 'All' && user['status'] != _filterStatus) return const SizedBox.shrink();
              
              final roleColor = _getRoleColor(user['role'] ?? '');

              return Container(
                margin: const EdgeInsets.only(bottom: 12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppTheme.borderLight),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)],
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Row(
                    children: [
                      // Avatar
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: Colors.grey.shade50,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: AppTheme.borderLight),
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          (user['nama'] ?? 'U').toString().substring(0, 1).toUpperCase(),
                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textMuted),
                        ),
                      ),
                      const SizedBox(width: 16),
                      
                      // User Info
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(user['nama'] ?? '-', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                            const SizedBox(height: 2),
                            Text(user['email'] ?? '-', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                            const SizedBox(height: 8),
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(color: roleColor.withOpacity(0.1), borderRadius: BorderRadius.circular(6)),
                                  child: Text(user['role'] ?? '-', style: TextStyle(color: roleColor, fontSize: 9, fontWeight: FontWeight.bold)),
                                ),
                                const SizedBox(width: 8),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(6)),
                                  child: Text(user['nama_jurusan'] ?? '-', style: const TextStyle(color: AppTheme.textMuted, fontSize: 9, fontWeight: FontWeight.bold)),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      
                      // Action & Status
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Switch(
                            value: user['status'] == 'Aktif',
                            onChanged: (val) async {
                              final api = Provider.of<ApiService>(context, listen: false);
                              try {
                                  await api.client.put('/v1/superadmin/users/${user['user_id']}', data: {
                                      'status': val ? 'Aktif' : 'Non-Aktif'
                                  });
                                  _fetchUsers();
                              } catch(e) {}
                            },
                            activeColor: AppTheme.primaryBlue,
                            materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              InkWell(
                                onTap: () => _showUserModal(user: user), // Edit Action
                                child: const Icon(Icons.edit, size: 16, color: Colors.grey),
                              ),
                              const SizedBox(width: 12),
                              InkWell(
                                onTap: () async {
                                    final api = Provider.of<ApiService>(context, listen: false);
                                    try {
                                        await api.client.delete('/v1/superadmin/users/${user['user_id']}');
                                        _fetchUsers();
                                    } catch(e) {}
                                },
                                child: const Icon(Icons.delete_outline, size: 16, color: Colors.redAccent),
                              ),
                            ],
                          )
                        ],
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
          const SizedBox(height: 32),
        ],
      ),
    );
  }
}
