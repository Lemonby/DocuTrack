# LAPORAN TUGAS PERHITUNGAN CYCLOMATIC COMPLEXITY (MCCABE)
## Mini Proyek: DocuTrack V2

Laporan ini berisi hasil identifikasi alur kontrol, diagram alir (flowgraph), penghitungan *Cyclomatic Complexity* (CC), jalur independen, serta tabel cakupan pengujian (*Statement & Decision Coverage*) untuk sembilan modul utama pada aplikasi **DocuTrack V2**:
1. **MODUL 1:** Verifikator, Approval Usulan Kegiatan (Perbandingan Arsitektur Legacy & ORM Baru).
2. **MODUL 2:** verifyUserLogin (Autentikasi Akun Baru menggunakan ORM).
3. **MODUL 3:** pencairan-dana (Pencairan Dana oleh Bendahara menggunakan ORM).
4. **MODUL 4:** Wadir Approval usulan (Persetujuan oleh Wakil Direktur).
5. **MODUL 5:** insert KAK (Menyisipkan data utama KAK).
6. **MODUL 6:** update Lpj Status (Verifikasi status LPJ oleh Bendahara).
7. **MODUL 7:** insert Rab Item (Menyisipkan satu item Rencana Anggaran Biaya).
8. **MODUL 8:** simpan Pengajuan usulan pada admin (Penyimpanan pengajuan KAK & RAB lengkap).
9. **MODUL 9:** PPK Approval usulan (Persetujuan oleh Pejabat Pembuat Komitmen).

---

## 1. MODUL 1: Verifikator, Approval Usulan Kegiatan

Modul ini memproses penelaahan dan persetujuan usulan KAK (Kerangka Acuan Kerja) oleh peran Verifikator.

### A. ARSITEKTUR LAMA (Legacy SQL Manual)
*Lokasi Kode: `app/Models/VerifikatorModel.php` (Fungsi: `updateKegiatanApprovalStatus`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start (Masuk fungsi `updateKegiatanApprovalStatus()`)
* **N2**: Decision: `if ($trimmedMak === '')` $\rightarrow$ Validasi Kode MAK kosong
* **N3**: Process: `throw new RuntimeException('Kode MAK tidak boleh kosong.')`
* **N4**: Decision: `if ($danaDisetujui < 0)` $\rightarrow$ Validasi dana negatif
* **N5**: Process: `throw new RuntimeException('Dana yang disetujui tidak boleh negatif.')`
* **N6**: Decision: `if ($catatan !== null)` $\rightarrow$ Cek apakah ada catatan
* **N7**: Decision: `if ($catatanTrimmed !== '')` $\rightarrow$ Cek catatan tidak kosong
* **N8**: Process: `$note = $catatanTrimmed` $\rightarrow$ Simpan catatan
* **N9**: Process: `$connection->begin_transaction()` $\rightarrow$ Mulai transaksi DB
* **N10**: Decision: `if ($lockStmt === false)` $\rightarrow$ Cek prepare lock berhasil
* **N11**: Process: `throw new RuntimeException('Gagal menyiapkan statement lock.')`
* **N12**: Decision: `if ($result->num_rows === 0)` $\rightarrow$ Kegiatan tidak ditemukan
* **N13**: Process: `throw new RuntimeException('Kegiatan tidak ditemukan.')`
* **N14**: Decision: `if ($updateStmt === false)` $\rightarrow$ Cek prepare update berhasil
* **N15**: Process: `throw new RuntimeException('Gagal menyiapkan statement update.')`
* **N16**: Decision: `if (!$updateStmt->execute())` $\rightarrow$ Eksekusi update
* **N17**: Process: `throw new RuntimeException('Gagal update kegiatan...')`
* **N18**: Process: `insertProgressHistory()` $\rightarrow$ Catat riwayat
* **N19**: Decision: `if ($historyId <= 0)` $\rightarrow$ Cek insert history berhasil
* **N20**: Process: `throw new RuntimeException('Gagal mencatat riwayat.')`
* **N21**: Process: `$connection->commit()` $\rightarrow$ Commit transaksi
* **N22**: Process: `return true` $\rightarrow$ Sukses kembalikan true
* **N23**: Decision: `catch (Throwable $e)` $\rightarrow$ Cek rollback jika transaksi dimulai
* **N24**: Process: `return false` $\rightarrow$ Gagal kembalikan false

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3`
3. `N2 -> N4`
4. `N3 -> N23`
5. `N4 -> N5`
6. `N4 -> N6`
7. `N5 -> N23`
8. `N6 -> N7`
9. `N6 -> N9`
10. `N7 -> N8`
11. `N7 -> N9`
12. `N8 -> N9`
13. `N9 -> N10`
14. `N10 -> N11`
15. `N10 -> N12`
16. `N11 -> N23`
17. `N12 -> N13`
18. `N12 -> N14`
19. `N13 -> N23`
20. `N14 -> N15`
21. `N14 -> N16`
22. `N15 -> N23`
23. `N16 -> N17`
24. `N17 -> N23`
25. `N16 -> N18`
26. `N18 -> N19`
27. `N19 -> N20`
28. `N20 -> N23`
29. `N19 -> N21`
30. `N21 -> N22`
31. `N23 -> N24`
32. `N22 -> N24`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 24 node
* **Jumlah Edge (E):** 33 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 33 - 24 + 2(1) = 11$

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N3 -> N23 -> N24`
* **P2**: `N1 -> N2 -> N4 -> N5 -> N23 -> N24`
* **P3**: `N1 -> N2 -> N4 -> N6 -> N9 -> N10 -> N11 -> N23 -> N24`
* **P4**: `N1 -> N2 -> N4 -> N6 -> N7 -> N9 -> N10 -> N12 -> N13 -> N23 -> N24`
* **P5**: `N1 -> N2 -> N4 -> N6 -> N7 -> N8 -> N9 -> N10 -> N12 -> N14 -> N15 -> N23 -> N24`
* **P6**: `N1 -> N2 -> N4 -> N6 -> N9 -> N10 -> N12 -> N14 -> N16 -> N17 -> N23 -> N24`
* **P7**: `N1 -> N2 -> N4 -> N6 -> N9 -> N10 -> N12 -> N14 -> N16 -> N18 -> N19 -> N20 -> N23 -> N24`
* **P8**: `N1 -> N2 -> N4 -> N6 -> N9 -> N10 -> N12 -> N14 -> N16 -> N18 -> N19 -> N21 -> N22 -> N24`
* **P9**: `N1 -> N2 -> N4 -> N6 -> N7 -> N8 -> N9 -> N10 -> N12 -> N14 -> N16 -> N18 -> N19 -> N21 -> N22 -> N24`
* **P10**: `N1 -> N2 -> N4 -> N6 -> N7 -> N9 -> N10 -> N12 -> N14 -> N16 -> N18 -> N19 -> N21 -> N22 -> N24`
* **P11**: `N1 -> N2 -> N4 -> N6 -> N7 -> N8 -> N9 -> N10 -> N11 -> N23 -> N24`

#### **5. Statement Coverage**
| TC | Input | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-V1** | `kodeMak=""`, `dana=0` | P1 | N1, N2, N3, N23, N24 | Dapat Dicover |
| **SC-V2** | `kodeMak="MAK001"`, `dana=-1` | P2 | N1, N2, N4, N5, N23, N24 | Dapat Dicover |
| **SC-V3** | `kodeMak="MAK001"`, `dana=0`, `catatan=null`, `lockStmt=false` | P3 | N6, N9, N10, N11, N23 | Dapat Dicover |
| **SC-V4** | `kodeMak="MAK001"`, `dana=0`, `catatan=" "` | P4/P10 | N7, N9 | Dapat Dicover |
| **SC-V5** | `kodeMak="MAK001"`, `dana=0`, `catatan="valid"`, `num_rows=0` | P5 | N8, N12, N13, N23 | Dapat Dicover |
| **SC-V6** | `kodeMak="MAK001"`, `dana=0`, `catatan=null`, `updateStmt=false` | P6 | N14, N15, N23 | Dapat Dicover |
| **SC-V7** | `kodeMak="MAK001"`, `dana=0`, `catatan=null`, `execute gagal` | P7 | N16, N17, N23 | Dapat Dicover |
| **SC-V8** | `kodeMak="MAK001"`, `dana=0`, `catatan=null`, `historyId=0` | P8 | N18, N19, N20, N23 | Dapat Dicover |
| **SC-V9** | `kodeMak="MAK001"`, `dana=500000`, `catatan="valid"`, `sukses` | P9 | N21, N22, N24 | Dapat Dicover |

#### **6. Decision Coverage**
| Node | Decision (Kondisi) | TRUE $\rightarrow$ dicover TC | FALSE $\rightarrow$ dicover TC |
| :--- | :--- | :--- | :--- |
| **N2** | `$trimmedMak === ""` | SC-V1 | SC-V2 |
| **N4** | `$danaDisetujui < 0` | SC-V2 | SC-V3 |
| **N6** | `$catatan !== null` | SC-V4 | SC-V3 |
| **N7** | `$catatanTrimmed !== ""` | SC-V5 | SC-V4 |
| **N10** | `$lockStmt === false` | SC-V3 | SC-V5 |
| **N12** | `$result->num_rows === 0` | SC-V5 | SC-V6 |
| **N14** | `$updateStmt === false` | SC-V6 | SC-V7 |
| **N16** | `!$updateStmt->execute()` | SC-V7 | SC-V8 |
| **N19** | `$historyId <= 0` | SC-V8 | SC-V9 |
| **N23** | `$transactionStarted` | SC-V3 | SC-V1 |

---

### B. ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/WorkflowService.php` (Fungsi: `moveToNextPosition`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start (Masuk fungsi `moveToNextPosition()`)
* **N2**: Decision: `if (in_array($currentPosition, [PPK, WADIR, BENDAHARA]))`
* **N3**: Process: `$newStatus = self::STATUS_MENUNGGU`
* **N4**: Process: Mulai transaksi `DB::transaction()`
* **N5**: Process: Fetch data `Kegiatan::lockForUpdate()->findOrFail($kegiatanId)`
* **N6**: Decision: `if ($currentPosition === self::POSITION_VERIFIKATOR)`
* **N7**: Decision: `if (isset($additionalData['kode_mak']))`
* **N8**: Process: `$updateData['bukti_mak'] = ...`
* **N9**: Decision: `if (isset($additionalData['dana_disetujui']))`
* **N10**: Process: `$updateData['dana_di_setujui'] = ...`
* **N11**: Decision: `if (isset($additionalData['umpan_balik']))`
* **N12**: Process: `$updateData['umpan_balik_verifikator'] = ...`
* **N13**: Process: `$kegiatan->update()`, `recordHistory()`, & Notifikasi Pemilik
* **N14**: Decision: `if ($actorUserId && $actorUserId !== $kegiatan->user_id)`
* **N15**: Decision: `match($currentPosition)` (Verifikator / PPK / Wadir / Default)
* **N16**: Process: `LogStatus::create()` untuk log aksi aktor
* **N17**: Process: `return true` (Selesai transaksi)
* **N18**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3`
3. `N2 -> N4`
4. `N3 -> N4`
5. `N4 -> N5`
6. `N5 -> N6`
7. `N6 -> N7`
8. `N6 -> N13`
9. `N7 -> N8`
10. `N7 -> N9`
11. `N8 -> N9`
12. `N9 -> N10`
13. `N9 -> N11`
14. `N10 -> N11`
15. `N11 -> N12`
16. `N11 -> N13`
17. `N12 -> N13`
18. `N13 -> N14`
19. `N14 -> N15`
20. `N14 -> N17`
21. `N15 -> N16`
22. `N16 -> N17`
23. `N17 -> N18`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 18 node
* **Jumlah Edge (E):** 26 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 26 - 18 + 2(1) = 10$

---

## 2. MODUL 2: verifyUserLogin

Modul ini memverifikasi autentikasi login pengguna di web berdasarkan email, password, dan verifikasi CAPTCHA.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Http/Controllers/AuthController.php` (Fungsi: `login`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start (Masuk fungsi `login(LoginRequest $request)` & inisialisasi captcha)
* **N2a**: Decision: `if ($inputCaptcha !== Session::get('captcha_code'))`
* **N2b**: Decision: `if ($inputCaptcha !== '123456')`
* **N3**: Process: `return back()`
* **N4**: Process: `User::where('email', ...)->first()`
* **N5a**: Decision: `if (!$user)`
* **N5b**: Decision: `if (!Hash::check($password, $user->password))`
* **N6**: Process: `return back()`
* **N7**: Decision: `if ($user->status !== 'Aktif')`
* **N8**: Process: `return back()`
* **N9**: Process: `$role = $user->getRoleNames()->first()`
* **N10**: Process: `$sessionRole = $roleMap[$role] ?? 'admin'`
* **N11**: Process: Simpan data ke Session & Cookie, catat Log Status & Activity Log
* **N12**: Decision: `return match($sessionRole)`
* **R_Bend**: Process: `redirect()->route('bendahara.dashboard')`
* **R_Ver**: Process: `redirect()->route('verifikator.dashboard')`
* **R_Ppk**: Process: `redirect()->route('ppk.dashboard')`
* **R_Wad**: Process: `redirect()->route('wadir.dashboard')`
* **R_Dir**: Process: `redirect()->route('direktur.dashboard')`
* **R_SA**: Process: `redirect()->route('superadmin.dashboard')`
* **R_Def**: Process: `redirect()->route('admin.dashboard')`
* **N13**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2a`
2. `N2a -> N2b`
3. `N2a -> N4`
4. `N2b -> N3`
5. `N2b -> N4`
6. `N3 -> N13`
7. `N4 -> N5a`
8. `N5a -> N6`
9. `N5a -> N5b`
10. `N5b -> N6`
11. `N5b -> N7`
12. `N6 -> N13`
13. `N7 -> N8`
14. `N7 -> N9`
15. `N8 -> N13`
16. `N9 -> N10`
17. `N10 -> N11`
18. `N11 -> N12`
19. `N12 -> R_Bend`
20. `N12 -> R_Ver`
21. `N12 -> R_Ppk`
22. `N12 -> R_Wad`
23. `N12 -> R_Dir`
24. `N12 -> R_SA`
25. `N12 -> R_Def`
26. `R_Bend -> N13`, `R_Ver -> N13`, `R_Ppk -> N13`, `R_Wad -> N13`, `R_Dir -> N13`, `R_SA -> N13`, `R_Def -> N13`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 22 node
* **Jumlah Edge (E):** 32 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 32 - 22 + 2(1) = 12$

---

## 3. MODUL 3: pencairan-dana

Modul ini memproses pencairan dana kegiatan oleh Bendahara (bisa penuh sekaligus atau bertahap beberapa termin) setelah di-approve Wadir.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/PencairanService.php` (Fungsi: `cairkanDana`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start
* **N2**: Process: Lock data `Kegiatan::lockForUpdate()->findOrFail($kegiatanId)`
* **N3**: Decision: `if ($metode === 'bertahap')`
* **N4**: Process: Inisialisasi `$totalDicairkan = 0`
* **N5**: Loop Decision: `foreach ($data['tahapan'] as $index => $tahap)`
* **N6a**: Decision: `if ($nominal <= 0)`
* **N6b**: Decision: `if (empty($tahap['tanggal']))`
* **N7**: Process: `throw new InvalidArgumentException("Data tahap ... tidak valid")`
* **N8**: Process: Buat record `TahapanPencairan` & tambah ke `$totalDicairkan`
* **N9**: Process: Update `Kegiatan` (metode bertahap) & set `$tanggalTerakhir`
* **N10**: Process: Update `Kegiatan` (metode penuh), Buat record `TahapanPencairan` & set `$tanggalTerakhir`
* **N11**: Process: Catat `ProgressHistory`, Buat/Update deadline `Lpj`, Catat `LogStatus` untuk pemilik
* **N12a**: Decision: `if ($userId)`
* **N12b**: Decision: `if ($userId !== $kegiatan->user_id)`
* **N13**: Process: Catat `LogStatus` log untuk Bendahara
* **N14**: Process: Catat `ActivityLog` & return `$kegiatan->fresh()`
* **N15**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3`
3. `N3 -> N4`
4. `N3 -> N10`
5. `N4 -> N5`
6. `N5 -> N6a`
7. `N5 -> N9`
8. `N6a -> N7`
9. `N6a -> N6b`
10. `N6b -> N7`
11. `N6b -> N8`
12. `N8 -> N5`
13. `N7 -> N15`
14. `N9 -> N11`
15. `N10 -> N11`
16. `N11 -> N12a`
17. `N12a -> N12b`
18. `N12a -> N14`
19. `N12b -> N13`
20. `N12b -> N14`
21. `N13 -> N14`
22. `N14 -> N15`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 17 node
* **Jumlah Edge (E):** 22 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 22 - 17 + 2(1) = 7$

---

## 4. MODUL 4: Wadir Approval usulan

Fungsi ini memproses persetujuan usulan kegiatan oleh Wakil Direktur. Posisi dipindahkan ke Bendahara (posisi_id = 5), status_utama_id direset ke menunggu (1), dan riwayat progress dicatat.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/WorkflowService.php` (Fungsi: `moveToNextPosition` dengan `$currentPosition = POSITION_WADIR`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
Di bawah input khusus Wadir, beberapa cabang kode dilewati. Di bawah ini adalah flowgraph spesifik jalur yang dilalui:
* **N1**: Start (Masuk fungsi `moveToNextPosition` dengan `POSITION_WADIR`)
* **N2**: Decision: `if (in_array(POSITION_WADIR, [PPK, WADIR, BENDAHARA]))` $\rightarrow$ Selalu **TRUE**
* **N3**: Process: `$newStatus = self::STATUS_MENUNGGU`
* **N4**: Process: Mulai transaksi `DB::transaction()`
* **N5**: Process: Lock & ambil data `Kegiatan::lockForUpdate()->findOrFail($kegiatanId)`
* **N6**: Decision: `if ($currentPosition === self::POSITION_VERIFIKATOR)` $\rightarrow$ Selalu **FALSE**
* **N7**: Process: Update `Kegiatan` (posisi $\rightarrow$ 5, status $\rightarrow$ 1), simpan history, log untuk pemilik usulan
* **N8**: Decision: `if ($actorUserId && $actorUserId !== $kegiatan->user_id)` $\rightarrow$ Cek aktor login
* **N9**: Process: `LogStatus` untuk Wadir (menggunakan match, link $\rightarrow$ `/wadir/kegiatan/show/{id}`)
* **N10**: Process: Kembalikan `true` & selesaikan transaksi
* **N11**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3` (TRUE)
3. `N3 -> N4`
4. `N4 -> N5`
5. `N5 -> N6`
6. `N6 -> N7` (FALSE)
7. `N7 -> N8`
8. `N8 -> N9` (TRUE: Wadir login dan bukan pemilik)
9. `N8 -> N10` (FALSE: Wadir login adalah pemilik usulan)
10. `N9 -> N10`
11. `N10 -> N11`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
Sebagai bagian dari metode global `moveToNextPosition`, **Cyclomatic Complexity dari modul/fungsi ini secara keseluruhan adalah 10** (lihat perhitungan lengkap di Modul 1 Bagian B).
Jika dihitung murni berdasarkan *sub-graph jalur Wadir* saja:
* **Jumlah Node (N):** 11
* **Jumlah Edge (E):** 11
* **Connected Component (P):** 1
* $CC = 11 - 11 + 2(1) = 2$ (Logika alur persetujuan sangat lurus dan sederhana).

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N3 -> N4 -> N5 -> N6 -> N7 -> N8 -> N9 -> N10 -> N11` (Aktor login Wadir $\neq$ pemilik usulan)
* **P2**: `N1 -> N2 -> N3 -> N4 -> N5 -> N6 -> N7 -> N8 -> N10 -> N11` (Aktor login Wadir = pemilik usulan)

#### **5. Statement Coverage**
| TC | Input Parameter | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-W1** | `currentPosition=4` (Wadir), `actorUserId=5` (Wadir), `ownerId=3` | P1 | N1, N2, N3, N4, N5, N6, N7, N8, N9, N10, N11 | Dapat Dicover |
| **SC-W2** | `currentPosition=4` (Wadir), `actorUserId=3` (Pemilik), `ownerId=3` | P2 | N8 (FALSE), N10 | Dapat Dicover |

#### **6. Decision Coverage**
| Node | Decision (Kondisi) | TRUE $\rightarrow$ dicover TC | FALSE $\rightarrow$ dicover TC |
| :--- | :--- | :--- | :--- |
| **N2** | `in_array(POSITION_WADIR, [...])` | SC-W1 | (Selalu TRUE untuk Wadir) |
| **N6** | `$currentPosition === POSITION_VERIFIKATOR` | - | SC-W1 |
| **N8** | `$actorUserId && $actorUserId !== owner` | SC-W1 | SC-W2 |

---

## 5. MODUL 5: insert KAK

Menyisipkan data awal Kerangka Acuan Kerja (KAK) ke tabel `tbl_kak`.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/KegiatanService.php` (Fungsi: `createKegiatan` baris 54-60)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start (Memulai pembuatan KAK)
* **N2**: Process: `Kak::create()` dengan field `kegiatan_id`, `gambaran_umum` (default), `penerima_manfaat` (default), `metode_pelaksanaan` (default), `tgl_pembuatan`
* **N3**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 3 node
* **Jumlah Edge (E):** 2 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 2 - 3 + 2(1) = 1$
* **Interpretasi:** **$CC = 1$** (Kompleksitas **SANGAT RENDAH/MINIMAL**). Tidak ada percabangan logika di dalam metode insert dasar ORM.

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N3`

#### **5. Statement Coverage**
| TC | Input | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-K1** | `kegiatan_id=12`, `gambaran_umum='Diskusi'` | P1 | N1, N2, N3 | Dapat Dicover |

#### **6. Decision Coverage**
*Modul ini tidak memiliki decision node (percabangan kondisional).*

---

## 6. MODUL 6: update Lpj Status

Fungsi ini dipanggil oleh Bendahara untuk memproses Laporan Pertanggungjawaban (LPJ): menyetujui (lunas) atau meminta revisi dengan catatan tertentu.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Http/Controllers/Bendahara/LpjController.php` (Fungsi: `proses`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start (Masuk fungsi `proses()`)
* **N2**: Process: Ambil instansi `Kegiatan` dan `Lpj`
* **N3**: Loop Decision: `foreach ($itemFeedback as $itemId => $feedback)`
* **N4**: Process: Cari `LpjItem` berdasarkan ID
* **N5**: Decision: `if ($lpjItem)` $\rightarrow$ Cek item ditemukan
* **N6**: Process: `[TRUE] $lpjItem->update(['komentar' => $feedback])`
* **N7**: Process: `[FALSE] $rab = Rab::find($itemId)` $\rightarrow$ Fallback cari item RAB
* **N8**: Decision: `if ($rab)`
* **N9**: Process: `[TRUE] Cari LpjItem` berdasarkan kecocokan field RAB
* **N10**: Decision: `if ($lpjItem)`
* **N11**: Process: `[TRUE] $lpjItem->update(['komentar' => $feedback])`
* **N12**: Decision: `if ($action === 'approve')` $\rightarrow$ Evaluasi keputusan approve
* **N13**: Process: `[TRUE] Update LPJ status=3`, Update Kegiatan status=6 (LPJ Disetujui), simpan progress history, log pemilik, log aktor, activity log
* **N14**: Decision: `[FALSE dari N12] elseif ($action === 'revise')` $\rightarrow$ Evaluasi keputusan revisi
* **N15**: Process: `[TRUE] Update LPJ status=2 (Revisi)`, simpan log pemilik, log aktor, activity log
* **N16**: Process: Set flash message dan siapkan redirect
* **N17**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3`
3. `N3 -> N4` (Masuk loop feedback)
4. `N3 -> N12` (Keluar loop feedback)
5. `N4 -> N5`
6. `N5 -> N6` (TRUE: lpjItem langsung ketemu)
7. `N5 -> N7` (FALSE: lpjItem tidak langsung ketemu)
8. `N6 -> N3` (Kembali ke loop)
9. `N7 -> N8`
10. `N8 -> N9` (TRUE)
11. `N8 -> N3` (FALSE: kembali ke loop)
12. `N9 -> N10`
13. `N10 -> N11` (TRUE)
14. `N10 -> N3` (FALSE: kembali ke loop)
15. `N11 -> N3` (Kembali ke loop)
16. `N12 -> N13` (TRUE)
17. `N12 -> N14` (FALSE)
18. `N13 -> N16`
19. `N14 -> N15` (TRUE)
20. `N14 -> N16` (FALSE)
21. `N15 -> N16`
22. `N16 -> N17`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 17 node
* **Jumlah Edge (E):** 22 edge (untuk flowgraph yang di-render di atas)
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 22 - 17 + 2(1) = 7$
* **Interpretasi:** **$CC = 7$** (Kompleksitas **RENDAH**). Memiliki 7 jalur independen.

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N3 -> N12 -> N13 -> N16 -> N17` (Approve tanpa feedback, aktor = pemilik)
* **P2**: `N1 -> N2 -> N3 -> N12 -> N14 -> N15 -> N16 -> N17` (Revise tanpa feedback)
* **P3**: `N1 -> N2 -> N3 -> N12 -> N14 -> N16 -> N17` (Aksi lain/No Action tanpa feedback)
* **P4**: `N1 -> N2 -> N3 -> N4 -> N5 -> N6 -> N3 -> N12 -> N13 -> N16 -> N17` (Approve dengan 1 feedback langsung ketemu)
* **P5**: `N1 -> N2 -> N3 -> N4 -> N5 -> N7 -> N8 -> N3 -> N12 -> N13 -> N16 -> N17` (Approve dengan feedback, lpjItem null dan RAB null)
* **P6**: `N1 -> N2 -> N3 -> N4 -> N5 -> N7 -> N8 -> N9 -> N10 -> N3 -> N12 -> N13 -> N16 -> N17` (Approve dengan feedback, lpjItem null, RAB ada, namun lpjItem fallback tetap null)
* **P7**: `N1 -> N2 -> N3 -> N4 -> N5 -> N7 -> N8 -> N9 -> N10 -> N11 -> N3 -> N12 -> N13 -> N16 -> N17` (Approve dengan feedback, lpjItem fallback ketemu)

#### **5. Statement Coverage**
| TC | Input Parameter | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-L6-1** | `action='approve'`, `item_feedback=[]` | P1 | N1, N2, N3, N12, N13, N16, N17 | Dapat Dicover |
| **SC-L6-2** | `action='revise'`, `notes='Perlu perbaikan'`, `item_feedback=[]` | P2 | N14, N15 | Dapat Dicover |
| **SC-L6-3** | `action='none'`, `item_feedback=[]` | P3 | N14 (FALSE) | Dapat Dicover |
| **SC-L6-4** | `action='approve'`, `item_feedback=[10=>'bukti buram']` (lpjItem ada) | P4 | N4, N5, N6 | Dapat Dicover |
| **SC-L6-5** | `action='approve'`, `item_feedback=[999=>'revisi']` (lpjItem & rab null) | P5 | N7, N8 (FALSE) | Dapat Dicover |
| **SC-L6-6** | `action='approve'`, `item_feedback=[15=>'revisi']` (RAB ada, fallback lpjItem null) | P6 | N9, N10 (FALSE) | Dapat Dicover |
| **SC-L6-7** | `action='approve'`, `item_feedback=[15=>'revisi']` (RAB ada, fallback lpjItem ada) | P7 | N11 | Dapat Dicover |

#### **6. Decision Coverage**
| Node | Decision (Kondisi) | TRUE $\rightarrow$ dicover TC | FALSE $\rightarrow$ dicover TC |
| :--- | :--- | :--- | :--- |
| **N3** | `foreach ($itemFeedback ...)` | SC-L6-4 | SC-L6-1 |
| **N5** | `if ($lpjItem)` | SC-L6-4 | SC-L6-5 |
| **N8** | `if ($rab)` | SC-L6-6 | SC-L6-5 |
| **N10** | `if ($lpjItem)` (fallback) | SC-L6-7 | SC-L6-6 |
| **N12** | `if ($action === 'approve')` | SC-L6-1 | SC-L6-2 |
| **N14** | `elseif ($action === 'revise')` | SC-L6-2 | SC-L6-3 |

---

## 7. MODUL 7: insert Rab Item

Menyisipkan satu baris rencana anggaran biaya (RAB) ke tabel `tbl_rab`.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/KegiatanService.php` (Fungsi: `createKegiatan` baris 102-112)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start
* **N2**: Process: `Rab::create()` dengan field `kak_id`, `kategori_id`, `uraian`, `rincian`, `sat1`, `sat2`, `vol1`, `vol2`, `harga`
* **N3**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 3 node
* **Jumlah Edge (E):** 2 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 2 - 3 + 2(1) = 1$
* **Interpretasi:** **$CC = 1$** (Kompleksitas **MINIMAL**). Tidak ada percabangan.

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N3`

#### **5. Statement Coverage**
| TC | Input | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-R1** | `kak_id=5`, `kategori_id=2`, `harga=150000` | P1 | N1, N2, N3 | Dapat Dicover |

#### **6. Decision Coverage**
*Modul ini tidak memiliki decision node.*

---

## 8. MODUL 8: simpan Pengajuan usulan pada admin

Menyimpan data pengajuan kegiatan baru secara utuh di halaman pengusul/admin, termasuk data KAK, indikator keberhasilan, tahapan, dan detail RAB di dalam satu database transaction.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/KegiatanService.php` (Fungsi: `createKegiatan`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
* **N1**: Start (Masuk fungsi `createKegiatan()`)
* **N2**: Decision: `if (isset($data['rab_data']) && is_string($data['rab_data']))` $\rightarrow$ Cek json string
* **N3**: Process: `$data['rab_data'] = json_decode(...)`
* **N4**: Decision: `if (!empty($data['indikator_nama']))` $\rightarrow$ Cek input indikator
* **N5**: Loop Decision: `foreach ($data['indikator_nama'] as $idx => $nama)`
* **N6**: Decision: `if (!empty($nama))`
* **N7**: Process: Posisikan indikator ke nested array
* **N8**: Process: Mulai transaksi `DB::transaction()` & `Kegiatan::create()`
* **N9**: Process: `Kak::create()` & Sync IKU dinamik
* **N10**: Loop Decision: `foreach ($data['tahapan'] as $tahap)`
* **N11**: Decision: `if (!empty($tahap))`
* **N12**: Process: `TahapanPelaksanaan::create()`
* **N13**: Loop Decision: `foreach ($data['indikator'] as $ind)`
* **N14**: Decision: `if (!empty($ind['nama']))`
* **N15**: Process: `IndikatorKak::create()`
* **N16**: Loop Decision: `foreach ($data['rab_data'] as $namaKategori => $items)`
* **N17**: Process: `KategoriRab::firstOrCreate()`
* **N18**: Loop Decision: `foreach ($items as $item)`
* **N19**: Process: `Rab::create()`
* **N20**: Process: Catat `LogStatus` submission, catat `ActivityLog` & return `$kegiatan`
* **N21**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3` (TRUE)
3. `N2 -> N4` (FALSE)
4. `N3 -> N4`
5. `N4 -> N5` (TRUE)
6. `N4 -> N8` (FALSE)
7. `N5 -> N6` (Masuk loop)
8. `N5 -> N8` (Selesai loop)
9. `N6 -> N7` (TRUE)
10. `N6 -> N5` (FALSE)
11. `N7 -> N5`
12. `N8 -> N9`
13. `N9 -> N10`
14. `N10 -> N11` (Masuk loop)
15. `N10 -> N13` (Selesai loop)
16. `N11 -> N12` (TRUE)
17. `N11 -> N10` (FALSE)
18. `N12 -> N10`
19. `N13 -> N14` (Masuk loop)
20. `N13 -> N16` (Selesai loop)
21. `N14 -> N15` (TRUE)
22. `N14 -> N13` (FALSE)
23. `N15 -> N13`
24. `N16 -> N17` (Masuk loop kategori)
25. `N16 -> N20` (Selesai loop kategori)
26. `N17 -> N18`
27. `N18 -> N19` (Masuk loop item RAB)
28. `N18 -> N16` (Selesai loop item RAB)
29. `N19 -> N18`
30. `N20 -> N21`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
* **Jumlah Node (N):** 21 node
* **Jumlah Edge (E):** 30 edge
* **Connected Component (P):** 1
* **Formula:** $CC = E - N + 2P$
* **Perhitungan:** $CC = 30 - 21 + 2(1) = 11$
* **Interpretasi:** **$CC = 11$** (Kompleksitas **SEDANG**). Memiliki 11 jalur independen.

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: Pengajuan tanpa rab_data JSON string, tanpa indikator, tanpa tahapan, dan tanpa data kategori RAB)*
* **P2**: `N1 -> N2 -> N3 -> N4 -> N8 -> N9 -> N10 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: Pengajuan dengan rab_data JSON string, tanpa data loop lainnya)*
* **P3**: `N1 -> N2 -> N4 -> N5 -> N8 -> N9 -> N10 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: Indikator nama disuplai tapi isinya kosong)*
* **P4**: `N1 -> N2 -> N4 -> N5 -> N6 -> N7 -> N5 -> N8 -> N9 -> N10 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: 1 indikator nama disuplai valid)*
* **P5**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N11 -> N10 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: Tahapan disuplai tapi string kosong)*
* **P6**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N11 -> N12 -> N10 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: 1 tahapan disuplai valid)*
* **P7**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N13 -> N14 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: Indikator keberhasilan disuplai kosong)*
* **P8**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N13 -> N14 -> N15 -> N13 -> N16 -> N20 -> N21`  
  *(Skenario: 1 indikator keberhasilan disuplai valid)*
* **P9**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N13 -> N16 -> N17 -> N18 -> N16 -> N20 -> N21`  
  *(Skenario: Kategori RAB disuplai tanpa item di dalamnya)*
* **P10**: `N1 -> N2 -> N4 -> N8 -> N9 -> N10 -> N13 -> N16 -> N17 -> N18 -> N19 -> N18 -> N16 -> N20 -> N21`  
  *(Skenario: Kategori RAB disuplai beserta 1 item RAB valid)*
* **P11**: `N1 -> N2 -> N3 -> N4 -> N5 -> N6 -> N7 -> N5 -> N8 -> N9 -> N10 -> N11 -> N12 -> N10 -> N13 -> N14 -> N15 -> N13 -> N16 -> N17 -> N18 -> N19 -> N18 -> N16 -> N20 -> N21`  
  *(Skenario: Semua komponen disuplai lengkap dan valid)*

#### **5. Statement Coverage**
| TC | Input Parameter | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-L8-1** | `rab_data=[]`, `indikator_nama=[]`, `tahapan=[]` | P1 | N1, N2, N4, N8, N9, N10, N13, N16, N20, N21 | Dapat Dicover |
| **SC-L8-2** | `rab_data='[]'` (JSON String) | P2 | N3 | Dapat Dicover |
| **SC-L8-3** | `indikator_nama=['']` (kosong) | P3 | N5, N6 (FALSE) | Dapat Dicover |
| **SC-L8-4** | `indikator_nama=['IKU 1']` (valid) | P4 | N6, N7 | Dapat Dicover |
| **SC-L8-5** | `tahapan=['']` (kosong) | P5 | N10, N11 (FALSE) | Dapat Dicover |
| **SC-L8-6** | `tahapan=['Tahap 1']` (valid) | P6 | N11, N12 | Dapat Dicover |
| **SC-L8-7** | `indikator=[['nama'=>'']]` | P7 | N13, N14 (FALSE) | Dapat Dicover |
| **SC-L8-8** | `indikator=[['nama'=>'Indikator 1']]` | P8 | N14, N15 | Dapat Dicover |
| **SC-L8-9** | `rab_data=['Konsumsi'=>[]]` | P9 | N16, N17, N18 (FALSE) | Dapat Dicover |
| **SC-L8-10**| `rab_data=['Konsumsi'=>[['uraian'=>'Snack']]]` | P10 | N18, N19 | Dapat Dicover |

#### **6. Decision Coverage**
| Node | Decision (Kondisi) | TRUE $\rightarrow$ dicover TC | FALSE $\rightarrow$ dicover TC |
| :--- | :--- | :--- | :--- |
| **N2** | `if (isset($data['rab_data']) && is_string($data['rab_data']))` | SC-L8-2 | SC-L8-1 |
| **N4** | `if (!empty($data['indikator_nama']))` | SC-L8-3 | SC-L8-1 |
| **N5** | `foreach ($data['indikator_nama'] as ...)` | SC-L8-3 *(loop)* | SC-L8-1 *(skip)* |
| **N6** | `if (!empty($nama))` | SC-L8-4 | SC-L8-3 |
| **N10** | `foreach ($data['tahapan'] ...)` | SC-L8-5 *(loop)* | SC-L8-1 *(skip)* |
| **N11** | `if (!empty($tahap))` | SC-L8-6 | SC-L8-5 |
| **N13** | `foreach ($data['indikator'] ...)` | SC-L8-7 *(loop)* | SC-L8-1 *(skip)* |
| **N14** | `if (!empty($ind['nama']))` | SC-L8-8 | SC-L8-7 |
| **N16** | `foreach ($data['rab_data'] ...)` | SC-L8-9 *(loop)* | SC-L8-1 *(skip)* |
| **N18** | `foreach ($items as $item)` | SC-L8-10 *(loop)* | SC-L8-9 *(skip)* |

---

## 9. MODUL 9: PPK Approval usulan

Fungsi ini memproses persetujuan usulan kegiatan oleh Pejabat Pembuat Komitmen (PPK). Posisi dipindahkan ke Wadir (posisi_id = 4), status_utama_id direset ke menunggu (1), dan riwayat progress dicatat.

### ARSITEKTUR BARU (Laravel Eloquent ORM)
*Lokasi Kode: `app/Services/WorkflowService.php` (Fungsi: `moveToNextPosition` dengan `$currentPosition = POSITION_PPK`)*

#### **1. Identifikasi Node & Edge (Flowgraph)**
Di bawah input khusus PPK, alur yang dilalui identik dengan persetujuan Wadir:
* **N1**: Start (Masuk fungsi `moveToNextPosition` dengan `POSITION_PPK`)
* **N2**: Decision: `if (in_array(POSITION_PPK, [PPK, WADIR, BENDAHARA]))` $\rightarrow$ Selalu **TRUE**
* **N3**: Process: `$newStatus = self::STATUS_MENUNGGU`
* **N4**: Process: Mulai transaksi `DB::transaction()`
* **N5**: Process: Lock & ambil data `Kegiatan::lockForUpdate()->findOrFail($kegiatanId)`
* **N6**: Decision: `if ($currentPosition === self::POSITION_VERIFIKATOR)` $\rightarrow$ Selalu **FALSE**
* **N7**: Process: Update `Kegiatan` (posisi $\rightarrow$ 4, status $\rightarrow$ 1), simpan history, log untuk pemilik usulan
* **N8**: Decision: `if ($actorUserId && $actorUserId !== $kegiatan->user_id)` $\rightarrow$ Cek aktor login
* **N9**: Process: `LogStatus` untuk PPK (menggunakan match, link $\rightarrow$ `/ppk/kegiatan/show/{id}`)
* **N10**: Process: Kembalikan `true` & selesaikan transaksi
* **N11**: Exit

#### **2. Daftar Edge**
1. `N1 -> N2`
2. `N2 -> N3` (TRUE)
3. `N3 -> N4`
4. `N4 -> N5`
5. `N5 -> N6`
6. `N6 -> N7` (FALSE)
7. `N7 -> N8`
8. `N8 -> N9` (TRUE: PPK login dan bukan pemilik)
9. `N8 -> N10` (FALSE: PPK login adalah pemilik usulan)
10. `N9 -> N10`
11. `N10 -> N11`

#### **3. Penghitungan Cyclomatic Complexity (CC)**
Sebagai bagian dari metode global `moveToNextPosition`, **Cyclomatic Complexity dari modul/fungsi ini secara keseluruhan adalah 10** (lihat perhitungan lengkap di Modul 1 Bagian B).
Jika dihitung murni berdasarkan *sub-graph jalur PPK* saja:
* **Jumlah Node (N):** 11
* **Jumlah Edge (E):** 11
* **Connected Component (P):** 1
* $CC = 11 - 11 + 2(1) = 2$ (Alur lurus dan sederhana).

#### **4. Independent Path**
* **P1**: `N1 -> N2 -> N3 -> N4 -> N5 -> N6 -> N7 -> N8 -> N9 -> N10 -> N11` (Aktor login PPK $\neq$ pemilik usulan)
* **P2**: `N1 -> N2 -> N3 -> N4 -> N5 -> N6 -> N7 -> N8 -> N10 -> N11` (Aktor login PPK = pemilik usulan)

#### **5. Statement Coverage**
| TC | Input Parameter | Path | Statement yang Dicover | Status |
| :--- | :--- | :--- | :--- | :--- |
| **SC-P9-1** | `currentPosition=3` (PPK), `actorUserId=4` (PPK), `ownerId=3` | P1 | N1, N2, N3, N4, N5, N6, N7, N8, N9, N10, N11 | Dapat Dicover |
| **SC-P9-2** | `currentPosition=3` (PPK), `actorUserId=3` (Pemilik), `ownerId=3` | P2 | N8 (FALSE), N10 | Dapat Dicover |

#### **6. Decision Coverage**
| Node | Decision (Kondisi) | TRUE $\rightarrow$ dicover TC | FALSE $\rightarrow$ dicover TC |
| :--- | :--- | :--- | :--- |
| **N2** | `in_array(POSITION_PPK, [...])` | SC-P9-1 | (Selalu TRUE untuk PPK) |
| **N6** | `$currentPosition === POSITION_VERIFIKATOR` | - | SC-P9-1 |
| **N8** | `$actorUserId && $actorUserId !== owner` | SC-P9-1 | SC-P9-2 |
