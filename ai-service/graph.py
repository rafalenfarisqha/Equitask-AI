import json
from langgraph.graph import StateGraph, END
from state import AgentState
from agents.analyzer import agent_analyzer
from agents.generator import agent_generator_and_weight
from agents.reviewer import agent_validator
from agents.formatter import agent_formatter

def run_workflow(modul_teks: str, target_bloom: str = "C2"):
    workflow = StateGraph(AgentState)

    # Tambahkan node agen
    workflow.add_node("analyzer", agent_analyzer)
    workflow.add_node("generator", agent_generator_and_weight)
    workflow.add_node("validator", agent_validator)
    workflow.add_node("formatter", agent_formatter)

    # Tentukan alur sekuensial
    workflow.set_entry_point("analyzer")
    workflow.add_edge("analyzer", "generator")
    workflow.add_edge("generator", "validator")

    # Logika conditional loop self-correction (maksimal 2 iterasi)
    def check_validation(state: AgentState):
        if state.get("status_review") == "REVISI":
            return "generator"
        return "formatter"

    workflow.add_conditional_edges(
        "validator", 
        check_validation, 
        {
            "generator": "generator", 
            "formatter": "formatter"
        }
    )
    
    workflow.add_edge("formatter", END)
    app_graph = workflow.compile()

    initial_state = {
        "modul_teks": modul_teks,
        "target_bloom": target_bloom,
        "hasil_kurikulum": {},
        "hasil_psikologi": {},
        "draf_soal_paket": [],
        "status_review": "",
        "catatan_reviewer": "",
        "iterasi_count": 0,
        "soal_final_json": ""
    }
    
    final_state = app_graph.invoke(initial_state)
    return json.loads(final_state.get("soal_final_json", "{}"))