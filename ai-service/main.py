from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from graph import run_workflow

frommmmmm palen

app = FastAPI(title="EquiTask AI Service", version="2.0")
class ModulRequest(BaseModel):
    modul_teks: str
    target_bloom: str = "C2"

@app.post("/generate-soal")
def generate_soal_endpoint(request: ModulRequest):
    try:
        if not request.modul_teks:
            raise HTTPException(status_code=400, detail="Teks modul tidak boleh kosong.")
        
        # Jalankan workflow multi-agen LangGraph dengan prompt OpenAI
        hasil_soal = run_workflow(request.modul_teks, request.target_bloom)
        
        return {
            "status": "success",
            "message": "Paket asesmen diferensiasi inklusif berhasil divalidasi oleh Multi-Agent AI.",
            "data": hasil_soal
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8000, reload=True)