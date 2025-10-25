
Music Transcriber (Offline) - Windows scaffold
----------------------------------------------

Content:
  - backend/               FastAPI server, processing code, uploads, outputs
  - frontend/              Simple HTML page to upload audio and view results
  - run.bat                Run script for Windows (activates venv if present)

Quick start on Windows:
  1. Install Python 3.10+ and check "Add Python to PATH".
  2. Open PowerShell or CMD in project root.
  3. Create venv:  python -m venv venv
  4. Activate:     venv\Scripts\activate
  5. Install deps: pip install -r backend\requirements.txt
  6. Run server:   run.bat   (or: uvicorn backend.main:app --reload --port 8000)
  7. Open frontend: open frontend\index.html in a browser (or serve via a simple http server).
  8. Upload a WAV/MP3 file and press Send.
