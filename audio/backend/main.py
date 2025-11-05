import os
import time
import uuid
import logging
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.responses import FileResponse
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
import uvicorn
from processing import extract_notes_from_audio_bytes  # ensure this module has no stray backslashes

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
UPLOAD_DIR = os.path.join(BASE_DIR, "uploads")
OUTPUT_DIR = os.path.join(BASE_DIR, "outputs")
os.makedirs(UPLOAD_DIR, exist_ok=True)
os.makedirs(OUTPUT_DIR, exist_ok=True)

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("mutrapro")

app = FastAPI(title="Music Transcriber - Offline")
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

# serve outputs for MIDI download (static)
app.mount("/outputs", StaticFiles(directory=OUTPUT_DIR), name="outputs")

@app.post("/trans")
async def trans(file: UploadFile = File(...)):
    # basic validation
    if not file.filename.lower().endswith((".wav", ".flac", ".mp3", ".ogg", ".aiff")):
        raise HTTPException(status_code=400, detail="Only audio files are supported (.wav .mp3 .flac .ogg .aiff)")

    contents = await file.read()
    # save upload for reference
    fname = f"{uuid.uuid4().hex}_{file.filename}"
    fpath = os.path.join(UPLOAD_DIR, fname)
    with open(fpath, "wb") as f:
        f.write(contents)
    logger.info("Saved uploaded file to %s", fpath)

    try:
        # extract_notes_from_audio_bytes should return (events, pretty_midi_object)
        events_raw, pm = extract_notes_from_audio_bytes(contents)
    except Exception as e:
        logger.exception("Error extracting notes")
        raise HTTPException(status_code=500, detail=f"Processing error: {e}")

    # normalize events to list of dicts {note, start, end}
    events = []
    for item in events_raw:
        # accept tuple/list (note, t0, t1) or dict
        if isinstance(item, (list, tuple)) and len(item) >= 3:
            note, t0, t1 = item[0], float(item[1]), float(item[2])
        elif isinstance(item, dict):
            note = item.get("note") or item.get("name") or item.get(0)
            t0 = float(item.get("start", item.get("t0", 0)))
            t1 = float(item.get("end", item.get("t1", t0 + 0.0)))
        else:
            continue
        events.append({"note": str(note), "start": float(t0), "end": float(t1)})

    # build transcription_text (durations)
    text_parts = []
    for e in events:
        dur = e["end"] - e["start"]
        text_parts.append(f"{e['note']}({dur:.3f}s)")
    text_output = " ".join(text_parts)

    # write midi if pretty_midi object present
    midi_name = f"{os.path.splitext(fname)[0]}.mid"
    midi_path = os.path.join(OUTPUT_DIR, midi_name)
    midi_rel_url = None
    if pm is not None:
        try:
            pm.write(midi_path)
            # ensure file exists on disk
            timeout = 3.0
            waited = 0.0
            while not os.path.isfile(midi_path) and waited < timeout:
                time.sleep(0.05)
                waited += 0.05
            if os.path.isfile(midi_path):
                midi_rel_url = f"/outputs/{midi_name}"
                logger.info("Wrote MIDI to %s", midi_path)
            else:
                logger.warning("MIDI write attempted but file not found after waiting")
        except Exception as e:
            logger.exception("Failed to write MIDI file: %s", e)
            midi_rel_url = None

    response = {
        "success": True,
        "transcription_text": text_output,
        "events": events,   # normalized events as list of dicts
        "midi_file": midi_rel_url
    }
    return response

@app.get("/trans/midi/{midi_filename}")
def get_midi(midi_filename: str):
    path = os.path.join(OUTPUT_DIR, midi_filename)
    if not os.path.isfile(path):
        raise HTTPException(status_code=404, detail="MIDI not found")
    return FileResponse(path, media_type="audio/midi", filename=midi_filename)

if __name__ == "__main__":
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)
