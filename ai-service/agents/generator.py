import json
from state import AgentState
from config import client

def agent_generator_and_weight(state: AgentState) -> AgentState:
    print("--- AGENT 2 & 3: AGEN PSIKOLOGI ABK & PENYETARA BOBOT BEKERJA ---")
    kurikulum = state.get("hasil_kurikulum", {})
    
    prompt = f"""
    Berdasarkan data kurikulum berikut:
    {json.dumps(kurikulum)}
    
    Buatlah 4 paket soal asesmen diferensiasi yang setara secara kognitif namun disesuaikan formatnya:
    1. Profil Reguler (Teks standar, naratif, struktur kompleks).
    2. Profil ADHD (Instruksi ringkas, minim distorsi visual, penekanan fokus).
    3. Profil Down Syndrome (Kalimat pendek, bahasa sederhana, dukungan visual konkret).
    4. Profil ASD / Autisme (Meminimalkan ambiguitas, instruksi lugas tanpa tafsir ganda).
    
    Masing-masing paket harus mengukur kompetensi yang sama persis namun dengan variasi konteks (context variation) agar tidak bisa saling contek.
    
    Berikan output dalam format JSON murni berupa list dengan 4 objek paket soal yang memiliki kunci: 
    "profil", "deskripsi_format", "soal_list" (berisi list soal dengan "id", "pertanyaan", "bobot").
    """
    
    response = client.chat.completions.create(
        model="gpt-5.5",
        messages=[{"role": "user", "content": prompt}],
        response_format={"type": "json_object"}
    )
    
    try:
        content = json.loads(response.choices[0].message.content)
        paket = content.get("paket_soal", [
            {"profil": "Reguler", "soal_list": [{"id": 1, "pertanyaan": "Contoh soal reguler", "bobot": 10}]},
            {"profil": "Down Syndrome", "soal_list": [{"id": 1, "pertanyaan": "Contoh soal modifikasi", "bobot": 10}]}
        ])
    except:
        paket = []
        
    state["draf_soal_paket"] = paket
    return state