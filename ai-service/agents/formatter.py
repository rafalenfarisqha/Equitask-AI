import json
from state import AgentState

def agent_formatter(state: AgentState) -> AgentState:
    print("--- FORMATTER MENYUSUN JSON AKHIR UNTUK LARAVEL ---")
    paket = state.get("draf_soal_paket", [])
    
    output_final = {
        "status_validasi": state.get("status_review"),
        "catatan_validator": state.get("catatan_reviewer"),
        "paket_asesmen_diferensiasi": paket
    }
    
    state["soal_final_json"] = json.dumps(output_final, indent=4, ensure_ascii=False)
    return state