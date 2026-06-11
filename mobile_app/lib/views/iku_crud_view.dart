import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../providers/iku_provider.dart';
import '../models/iku.dart';
import '../theme/app_theme.dart';

class IkuCrudView extends StatefulWidget {
  const IkuCrudView({super.key});

  @override
  State<IkuCrudView> createState() => _IkuCrudViewState();
}

class _IkuCrudViewState extends State<IkuCrudView> {
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<IkuProvider>(context, listen: false).fetchIkus(page: 1, isRefresh: true);
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _refresh() async {
    await Provider.of<IkuProvider>(context, listen: false).fetchIkus(page: 1, isRefresh: true);
  }

  void _showIkuFormDialog({Iku? iku}) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _IkuFormModal(iku: iku),
    );
  }

  void _confirmDelete(Iku iku) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
          titlePadding: const EdgeInsets.only(top: 24, left: 24, right: 24),
          title: Column(
            children: [
              Container(
                width: 64,
                height: 64,
                decoration: BoxDecoration(color: Colors.red.shade50, shape: BoxShape.circle),
                child: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 32),
              ),
              const SizedBox(height: 16),
              const Text('Delete Permanently?', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
            ],
          ),
          content: Text('This indicator "${iku.code ?? iku.performanceIndicator}" will be removed from all selection menus. This cannot be undone.', textAlign: TextAlign.center, style: const TextStyle(fontSize: 13, color: AppTheme.textMuted)),
          actionsPadding: const EdgeInsets.all(24),
          actions: [
            Column(
              children: [
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () async {
                      Navigator.pop(context);
                      final provider = Provider.of<IkuProvider>(context, listen: false);
                      final success = await provider.deleteIku(iku.id);
                      if (!mounted) return;
                      if (success) {
                        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Indicator removed from registry'), backgroundColor: AppTheme.accentTeal));
                      } else {
                        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(provider.errorMessage), backgroundColor: Colors.redAccent));
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.redAccent,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      elevation: 4,
                      shadowColor: Colors.redAccent.withOpacity(0.4),
                    ),
                    child: const Text('Confirm Deletion', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  ),
                ),
                const SizedBox(height: 8),
                SizedBox(
                  width: double.infinity,
                  child: TextButton(
                    onPressed: () => Navigator.pop(context),
                    style: TextButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('Cancel', style: TextStyle(color: AppTheme.textMuted, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final ikuProvider = Provider.of<IkuProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final bool canWrite = authProvider.currentUser?.isSuperAdmin ?? false;

    // Filter logic
    final List<Iku> filteredIkus = ikuProvider.ikus.where((iku) {
      if (_searchQuery.isEmpty) return true;
      final query = _searchQuery.toLowerCase();
      return (iku.performanceIndicator.toLowerCase().contains(query)) ||
             (iku.code?.toLowerCase().contains(query) ?? false) ||
             (iku.year?.toString().contains(query) ?? false);
    }).toList();

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
                    child: const Icon(Icons.show_chart, color: Colors.white),
                  ),
                  const SizedBox(width: 16),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('IKU & Renstra Management', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.textDark, letterSpacing: -0.5)),
                      const SizedBox(height: 2),
                      Text('Unified performance metrics for system-wide strategic planning', style: TextStyle(fontSize: 11, color: AppTheme.textMuted, fontStyle: FontStyle.italic)),
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
                        onChanged: (val) {
                          setState(() {
                            _searchQuery = val;
                          });
                        },
                        decoration: const InputDecoration(
                          hintText: 'Search by name, period, or type...',
                          hintStyle: TextStyle(fontSize: 13),
                          prefixIcon: Icon(Icons.search, size: 18),
                          border: InputBorder.none,
                          contentPadding: EdgeInsets.symmetric(vertical: 14),
                        ),
                      ),
                    ),
                  ),
                  if (canWrite) ...[
                    const SizedBox(width: 12),
                    ElevatedButton.icon(
                      onPressed: () => _showIkuFormDialog(),
                      icon: const Icon(Icons.add, size: 18),
                      label: const Text('Create Indicator', style: TextStyle(fontWeight: FontWeight.bold)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.primaryBlue,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        elevation: 4,
                        shadowColor: AppTheme.primaryBlue.withOpacity(0.4),
                      ),
                    ),
                  ]
                ],
              ),
              const SizedBox(height: 24),

              // Info Card
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppTheme.borderLight),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.borderLight)),
                      child: const Icon(Icons.info_outline, color: AppTheme.primaryBlue, size: 20),
                    ),
                    const SizedBox(width: 16),
                    const Expanded(
                      child: Text(
                        'All indicators listed here are unified for the KAK selection process. You can manage both Main Performance (IKU) and Strategic Plans (Renstra) in this single view.',
                        style: TextStyle(fontSize: 11, color: AppTheme.textMuted, height: 1.5),
                      ),
                    ),
                    const SizedBox(width: 24),
                    Container(width: 1, height: 40, color: Colors.grey.shade300),
                    const SizedBox(width: 24),
                    Column(
                      children: [
                        const Text('TOTAL ACTIVE', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                        const SizedBox(height: 4),
                        Text('${filteredIkus.length}', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                      ],
                    ),
                    const SizedBox(width: 24),
                    Container(width: 1, height: 40, color: Colors.grey.shade300),
                    const SizedBox(width: 24),
                    Column(
                      children: [
                        const Text('TOTAL INDICATORS', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                        const SizedBox(height: 4),
                        Text('${ikuProvider.ikus.length}', style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 32),

              // Error or Empty State or Grid
              if (ikuProvider.errorMessage.isNotEmpty && ikuProvider.ikus.isEmpty)
                Center(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(vertical: 40),
                    child: Column(
                      children: [
                        const Icon(Icons.error_outline, size: 48, color: Colors.red),
                        const SizedBox(height: 16),
                        Text(ikuProvider.errorMessage, style: const TextStyle(color: AppTheme.textMuted)),
                      ],
                    ),
                  ),
                )
              else if (filteredIkus.isEmpty && !ikuProvider.isLoading)
                Center(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(vertical: 60),
                    child: Column(
                      children: [
                        Container(
                          width: 80,
                          height: 80,
                          decoration: BoxDecoration(color: Colors.grey.shade50, shape: BoxShape.circle),
                          child: Icon(Icons.folder_open, size: 40, color: Colors.grey.shade300),
                        ),
                        const SizedBox(height: 16),
                        const Text('Indicator Registry is Empty', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                        const SizedBox(height: 4),
                        const Text('Start by creating a new performance metric', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                      ],
                    ),
                  ),
                )
              else
                LayoutBuilder(
                  builder: (context, constraints) {
                    int crossAxisCount = 1;
                    if (constraints.maxWidth > 1200) crossAxisCount = 3;
                    else if (constraints.maxWidth > 700) crossAxisCount = 2;

                    return GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: crossAxisCount,
                        crossAxisSpacing: 24,
                        mainAxisSpacing: 24,
                        mainAxisExtent: 280,
                      ),
                      itemCount: filteredIkus.length,
                      itemBuilder: (context, index) {
                        final iku = filteredIkus[index];
                        final isRenstra = (iku.code ?? '').toUpperCase().contains('RENSTRA');
                        final String type = isRenstra ? 'RENSTRA' : 'IKU';
                        final Color typeColor = isRenstra ? Colors.purple : AppTheme.primaryBlue;

                        // Mock progress calculation
                        final double targetNum = double.tryParse(iku.target?.replaceAll('%', '') ?? '100') ?? 100;
                        final double capaianNum = 0.0; // Assume 0 initially, or fetch from real data if available
                        final double progress = (capaianNum / targetNum).clamp(0.0, 1.0);

                        return Container(
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: AppTheme.borderLight),
                            boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))],
                          ),
                          padding: const EdgeInsets.all(20),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Card Header
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                        decoration: BoxDecoration(color: typeColor.withOpacity(0.1), borderRadius: BorderRadius.circular(6), border: Border.all(color: typeColor.withOpacity(0.2))),
                                        child: Text(type, style: TextStyle(color: typeColor, fontSize: 9, fontWeight: FontWeight.bold, letterSpacing: 1)),
                                      ),
                                      const SizedBox(width: 8),
                                      Text(iku.year != null ? '${iku.year}' : 'N/A', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                                    ],
                                  ),
                                  Switch(
                                    value: true, // Dummy active status
                                    onChanged: (val) {},
                                    activeColor: AppTheme.primaryBlue,
                                    materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                  ),
                                ],
                              ),
                              const SizedBox(height: 16),
                              
                              // Card Content
                              Text(iku.performanceIndicator, maxLines: 2, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textDark, height: 1.3)),
                              const SizedBox(height: 8),
                              Text(iku.description ?? 'No description provided.', maxLines: 2, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted, height: 1.4)),
                              
                              const Spacer(),

                              // Progress Bar
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text('GLOBAL TARGET', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
                                      RichText(
                                        text: TextSpan(
                                          text: '0',
                                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark),
                                          children: [
                                            TextSpan(text: ' / ${iku.target ?? '100%'}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.normal, color: Colors.grey)),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                  Text('${(progress * 100).toInt()}%', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                                ],
                              ),
                              const SizedBox(height: 8),
                              Container(
                                height: 6,
                                width: double.infinity,
                                decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(3)),
                                child: FractionallySizedBox(
                                  alignment: Alignment.centerLeft,
                                  widthFactor: progress,
                                  child: Container(
                                    decoration: BoxDecoration(color: AppTheme.primaryBlue, borderRadius: BorderRadius.circular(3), boxShadow: [BoxShadow(color: AppTheme.primaryBlue.withOpacity(0.4), blurRadius: 4)]),
                                  ),
                                ),
                              ),

                              const SizedBox(height: 16),
                              const Divider(height: 1, color: AppTheme.borderLight),
                              const SizedBox(height: 16),

                              // Actions
                              if (canWrite)
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.end,
                                  children: [
                                    InkWell(
                                      onTap: () => _showIkuFormDialog(iku: iku),
                                      child: Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                        decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(8)),
                                        child: const Row(
                                          children: [
                                            Icon(Icons.edit, size: 12, color: AppTheme.textMuted),
                                            SizedBox(width: 6),
                                            Text('Edit', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.textMuted)),
                                          ],
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    InkWell(
                                      onTap: () => _confirmDelete(iku),
                                      child: Container(
                                        padding: const EdgeInsets.all(6),
                                        decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(8)),
                                        child: const Icon(Icons.delete_outline, size: 14, color: Colors.redAccent),
                                      ),
                                    ),
                                  ],
                                ),
                            ],
                          ),
                        );
                      },
                    );
                  },
                ),
            ],
          ),
        ),
      ),
    );
  }
}

// Stateful widget for the Form Modal to handle Radio button state
class _IkuFormModal extends StatefulWidget {
  final Iku? iku;
  const _IkuFormModal({this.iku});

  @override
  State<_IkuFormModal> createState() => _IkuFormModalState();
}

class _IkuFormModalState extends State<_IkuFormModal> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _yearController;
  late TextEditingController _targetController;
  late TextEditingController _descController;
  String _selectedType = 'IKU';

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.iku?.performanceIndicator);
    _yearController = TextEditingController(text: widget.iku?.year?.toString());
    _targetController = TextEditingController(text: widget.iku?.target);
    _descController = TextEditingController(text: widget.iku?.description);
    
    if (widget.iku != null && (widget.iku!.code ?? '').toUpperCase().contains('RENSTRA')) {
      _selectedType = 'RENSTRA';
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _yearController.dispose();
    _targetController.dispose();
    _descController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Form(
          key: _formKey,
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
                      Text(widget.iku == null ? 'Create Indicator' : 'Update Indicator', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppTheme.textDark)),
                      const Text('Configure performance metric for the entire system', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                    ],
                  ),
                  IconButton(icon: const Icon(Icons.close, color: Colors.grey), onPressed: () => Navigator.pop(context)),
                ],
              ),
              const SizedBox(height: 24),

              // Classification Radio
              const Text('INDICATOR CLASSIFICATION', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
              const SizedBox(height: 8),
              Row(
                children: [
                  Expanded(
                    child: InkWell(
                      onTap: () => setState(() => _selectedType = 'IKU'),
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        decoration: BoxDecoration(
                          color: _selectedType == 'IKU' ? AppTheme.primaryBlue.withOpacity(0.05) : Colors.white,
                          border: Border.all(color: _selectedType == 'IKU' ? AppTheme.primaryBlue : Colors.grey.shade200, width: 2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        alignment: Alignment.center,
                        child: Text('IKU', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: _selectedType == 'IKU' ? AppTheme.primaryBlue : Colors.grey.shade400)),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: InkWell(
                      onTap: () => setState(() => _selectedType = 'RENSTRA'),
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        decoration: BoxDecoration(
                          color: _selectedType == 'RENSTRA' ? AppTheme.primaryBlue.withOpacity(0.05) : Colors.white,
                          border: Border.all(color: _selectedType == 'RENSTRA' ? AppTheme.primaryBlue : Colors.grey.shade200, width: 2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        alignment: Alignment.center,
                        child: Text('RENSTRA', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: _selectedType == 'RENSTRA' ? AppTheme.primaryBlue : Colors.grey.shade400)),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),

              _buildField('NAME / TITLE', 'Enter metric name', _nameController, required: true),
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(child: _buildField('YEAR / PERIOD', 'e.g. 2024', _yearController, required: true, isNumber: true)),
                  const SizedBox(width: 16),
                  Expanded(child: _buildField('SYSTEM TARGET (%)', 'e.g. 80%', _targetController, required: true)),
                ],
              ),
              const SizedBox(height: 16),
              _buildField('INDICATOR DESCRIPTION', 'Describe what this indicator measures...', _descController, required: true, maxLines: 3),
              const SizedBox(height: 32),

              Row(
                children: [
                  Expanded(
                    child: TextButton(
                      onPressed: () => Navigator.pop(context),
                      style: TextButton.styleFrom(padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                      child: const Text('Cancel', style: TextStyle(color: AppTheme.textMuted, fontWeight: FontWeight.bold)),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: () async {
                        if (!_formKey.currentState!.validate()) return;
                        final provider = Provider.of<IkuProvider>(context, listen: false);
                        Navigator.pop(context);

                        final int? parsedYear = int.tryParse(_yearController.text);
                        bool success;
                        
                        if (widget.iku == null) {
                          success = await provider.createIku(
                            code: _selectedType, // Using code field to store type locally for now since we don't have separate type field in model
                            performanceIndicator: _nameController.text,
                            description: _descController.text,
                            target: _targetController.text,
                            year: parsedYear,
                          );
                        } else {
                          success = await provider.updateIku(
                            id: widget.iku!.id,
                            code: _selectedType,
                            performanceIndicator: _nameController.text,
                            description: _descController.text,
                            target: _targetController.text,
                            year: parsedYear,
                          );
                        }

                        if (success && mounted) {
                          provider.fetchIkus(page: 1, isRefresh: true);
                          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(widget.iku == null ? 'New indicator registered successfully' : 'Indicator updated successfully'), backgroundColor: AppTheme.accentTeal));
                        } else if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(provider.errorMessage), backgroundColor: Colors.redAccent));
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.primaryBlue,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        elevation: 4,
                        shadowColor: AppTheme.primaryBlue.withOpacity(0.4),
                      ),
                      child: const Text('Save Indicator', style: TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildField(String label, String hint, TextEditingController controller, {bool required = false, bool isNumber = false, int maxLines = 1}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey, letterSpacing: 1)),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          keyboardType: isNumber ? TextInputType.number : TextInputType.text,
          maxLines: maxLines,
          validator: required ? (v) => v == null || v.trim().isEmpty ? 'Required field' : null : null,
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
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
}
