from typing import TypedDict, List, Dict, Any

class AgentState(TypedDict):
    modul_teks: str                 # Teks mentah modul dari Laravel
    target_bloom: str               # Target level Bloom (misal: C2, C4)
    hasil_kurikulum: dict           # Output Agent 1: Analisis kompetensi & Bloom
    hasil_psikologi: dict           # Output Agent 2: Pemetaan UDL & Easy Read
    draf_soal_paket: List[Dict]     # Output Agent 3: 4 Paket Soal Konkret
    status_review: str              # Output Agent 4: "VALID" atau "REVISI"
    catatan_reviewer: str           # Umpan balik / catatan perbaikan[cite: 1, 2]
    iterasi_count: int              # Penghitung batas maksimal iterasi revisi (maks 2)[cite: 1, 2]
    soal_final_json: str            # Output akhir siap kirim ke Laravel[cite: 1, 2]