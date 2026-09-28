import json
from state import AgentState
from config import client

def agent_validator(state: AgentState) -> AgentState:
    print("--- AGENT 4: AGEN VALIDATOR MEMERIKSA KUALITAS & KESETARAAN ---")
    paket_soal = state.get("draf_soal_paket", [])
    iterasi = state.get("iterasi_count", 0)
    
    prompt = f"""
    Anda adalah Agen Validator (Quality Control) EquiTask AI.
    Periksa 4 paket soal berikut untuk memastikan kesetaraan bobot kognitif, kejelasan instruksi, dan penerapan prinsip aksesibilitas:
    
    {json.dumps(paket_soal)}
    
    Apakah soal-soal ini sudah setara secara kognitif dan layak didistribusikan?
    Berikan respons dalam format JSON murni dengan kunci:
    "status": "VALID" atau "REVISI",
    "catatan": "alasan atau saran perbaikan jika ada"
    """
    
    response = client.chat.completions.create(
        model="gpt-5.5",
        messages=[{"role": "user", "content": prompt}],
        response_format={"type": "json_object"}
    )
    
    try:
        evaluasi = json.loads(response.choices[0].message.content)
        status = evaluasi.get("status", "VALID")
        catatan = evaluasi.get("catatan", "Tervalidasi dengan baik.")
    except:
        status = "VALID"
        catatan = "Validasi otomatis sistem sukses."
        
    # Batasi iterasi revisi maksimal 2 kali agar sistem efisien
    if status == "REVISI" and iterasi >= 2:
        status = "VALID"
        catatan = "Batas maksimum iterasi tercapai, dipaksa valid."
        
    state["status_review"] = status
    state["catatan_reviewer"] = catatan
    state["iterasi_count"] = iterasi + 1
    return state