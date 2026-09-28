import json
from state import AgentState
from config import client

def agent_analyzer(state: AgentState) -> AgentState:
    print("--- AGENT 1: AGEN KURIKULUM MENGEKSTRAK MATERI ---")
    teks_modul = state.get("modul_teks", "")
    target_bloom = state.get("target_bloom", "C2")
    
    prompt = f"""
    Anda adalah Agen Kurikulum dalam sistem EquiTask AI. Analisis teks modul berikut:
    "{teks_modul}"
    
    Tugas Anda:
    1. Identifikasi topik utama, tujuan pembelajaran, dan konsep kunci.
    2. Klasifikasikan kompetensi ke dalam target Taksonomi Bloom yaitu {target_bloom}.
    
    Berikan output dalam format JSON murni dengan struktur kunci:
    {{"topik_utama": "...", "tujuan_pembelajaran": "...", "kompetensi_inti": "...", "level_bloom": "{target_bloom}"}}
    """
    
    response = client.chat.completions.create(
        model="gpt-5.5",
        messages=[{"role": "user", "content": prompt}],
        response_format={"type": "json_object"}
    )
    
    try:
        hasil = json.loads(response.choices[0].message.content)
    except:
        hasil = {"topik_utama": "Gagal Analisis", "level_bloom": target_bloom}
        
    state["hasil_kurikulum"] = hasil
    return state