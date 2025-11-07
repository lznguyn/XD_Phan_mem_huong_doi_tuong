<?php
include '../user/config.php';
session_start();

// Kiểm tra đăng nhập
$expert_id = $_SESSION['transcription_id'] ?? null;
if (!$expert_id) {
    header('location:login.php');
    exit();
}

// Xử lý upload bản phối cuối cùng
$upload_message = "";
if (isset($_POST['upload_mix'])) {
    $request_id = mysqli_real_escape_string($conn, $_POST['request_id']);

    if (isset($_FILES['mix_file']) && $_FILES['mix_file']['error'] == 0) {
        $file_name = $_FILES['mix_file']['name'];
        $file_tmp = $_FILES['mix_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['mid'];
        if (in_array($file_ext, $allowed_ext)) {
            $upload_dir = 'uploaded_mixes/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $new_name = uniqid('mix_', true) . '.' . $file_ext;
            move_uploaded_file($file_tmp, $upload_dir . $new_name);

            mysqli_query($conn, "UPDATE music_submissions 
                SET title='$new_name', status='completed' 
                WHERE id='$request_id' AND target_role='arrangement'") 
                or die('Lỗi cập nhật bản phối!');   

            $upload_message = "✅ Upload bản phối thành công!";
        } else {
            $upload_message = "Định dạng file không hợp lệ. Chỉ mp3, wav, flac.";
        }
    } else {
        $upload_message = "Vui lòng chọn file để upload.";
    }
}

// Lấy danh sách bài nhạc gán cho chuyên gia
$submissions = mysqli_query($conn, "
    SELECT ms.*, u.name AS customer_name 
    FROM music_submissions ms
    JOIN users u ON ms.user_id = u.id
    WHERE ms.target_role='transcription'
    ORDER BY ms.created_at DESC
") or die('Query failed');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chuyên gia Hòa âm - Quản lý bản nhạc</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* --- Phần giao diện MuTraPro --- */
    :root{
      --bg: #f8fafc; --card: #ffffff; --muted: #475569; --accent: #06b6d4;
      font-family: Inter, Roboto, "Helvetica Neue", Arial;
    }
    #mutrapro-container{ margin-top: 2rem; }
    .card{ background:var(--card); border-radius:12px; padding:16px; box-shadow: 0 8px 28px rgba(15,23,42,0.06); margin-bottom:14px; }
    button.trans-btn{ background:var(--accent); color:#ffffff; border:0; padding:8px 12px; border-radius:8px; cursor:pointer; font-weight:600; }
    #out{ white-space:pre-wrap; color:var(--muted); font-family: monospace; margin-top:8px; }
    canvas{ background: linear-gradient(180deg, rgba(15,23,42,0.03), rgba(15,23,42,0.01)); border-radius:6px; display:block; }
    .progress{ height:8px; background:rgba(15,23,42,0.06); border-radius:999px; overflow:hidden; }
    .progress > i{ display:block; height:100%; width:0%; background:linear-gradient(90deg,#7dd3fc,#06b6d4); transition: width .2s; }
    a.link{ color:var(--accent); text-decoration:none; font-weight:600; }
  </style>
</head>

<?php include 'transcription_header.php'; ?>
<body class="bg-gray-50 mt-10">

<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6">Danh sách bản nhạc cần xử lý</h1>

    <?php if ($upload_message != ""): ?>
        <div class="mb-4 text-center text-green-700 font-semibold"><?php echo $upload_message; ?></div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php while($s = mysqli_fetch_assoc($submissions)): ?>
            <div class="bg-white rounded-xl shadow p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold"><?php echo htmlspecialchars($s['title']); ?></h3>
                        <p class="text-gray-600 text-sm mb-1">Khách hàng: <?php echo htmlspecialchars($s['customer_name']); ?></p>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fas fa-calendar-alt mr-1"></i> <?php echo $s['created_at']; ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            Trạng thái: 
                            <?php 
                            $status = $s['status'];
                            $color = $status=='completed' ? 'bg-green-500' : ($status=='pending' ? 'bg-blue-500' : 'bg-yellow-500');
                            $label = $status=='completed' ? 'Hoàn tất' : ($status=='pending' ? 'Đang xử lý' : 'Chờ xác nhận');
                            ?>
                            <span class="px-2 py-1 text-white text-xs rounded <?php echo $color; ?>"><?php echo $label; ?></span>
                        </p>
                        <?php if($s['file_name']): ?>
                            <a href="uploaded_mixes/<?php echo $s['file_name']; ?>" target="_blank" class="text-indigo-600 text-sm underline mt-1 block">
                                <i class="fas fa-play mr-1"></i> Nghe bản phối
                            </a>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="flex flex-col items-end gap-2">
                        <input type="hidden" name="request_id" value="<?php echo $s['id']; ?>">
                        <input type="file" name="mix_file" accept=".mid" required>
                        <button type="submit" name="upload_mix" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                            Upload bản phối
                        </button>
                    </form>
                </div>

                <!-- Nút mở giao diện phân tích -->
                <div class="mt-3">
                    <button class="trans-btn" onclick="showMuTraPro()">Phân tích nốt nhạc (MuTraPro)</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Khối giao diện MuTraPro -->
    <div id="mutrapro-container" style="display:none;">
      <div class="card">
        <div class="flex gap-3 items-center">
          <input id="file" type="file" accept=".wav,.mp3,.flac,.ogg,.aiff"/>
          <button id="sendBtn" class="trans-btn">Transcribe</button>
          <div class="text-sm text-gray-500 flex-1 text-right">API: <span id="apiUrl"></span></div>
        </div>
        <div class="mt-3 progress"><i id="bar"></i></div>
        <div id="out" class="text-sm text-gray-600 mt-3">
          Chưa có kết quả. Chọn audio và bấm "Transcribe".
        </div>
      </div>

      <div id="visualArea" style="display:none;">
        <div class="card">
          <div class="flex justify-between items-center mb-2">
            <strong>Kết quả phân tích nốt</strong>
            <a id="midiLink" class="link" href="#" target="_blank" style="display:none;">Tải MIDI</a>
          </div>
          <canvas id="prCanvas" height="280"></canvas>
          <table class="mt-2 w-full text-sm border-t" id="notesTable">
            <thead><tr><th>#</th><th>Nốt</th><th>Bắt đầu (s)</th><th>Kết thúc (s)</th><th>Độ dài (s)</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
</div>

<script>
// ======= MuTraPro Logic (JS) =======
const API_BASE = 'http://localhost:8000';
const API = API_BASE + '/trans';
document.getElementById('apiUrl').innerText = API;

const fileInput = document.getElementById('file');
const sendBtn = document.getElementById('sendBtn');
const out = document.getElementById('out');
const bar = document.getElementById('bar');
const visualArea = document.getElementById('visualArea');
const notesTableBody = document.querySelector('#notesTable tbody');
const prCanvas = document.getElementById('prCanvas');
const midiLink = document.getElementById('midiLink');

function showMuTraPro(){
  document.getElementById('mutrapro-container').style.display='block';
  window.scrollTo({top:document.body.scrollHeight, behavior:'smooth'});
}
function setProgress(p){ bar.style.width = Math.max(0, Math.min(100,p*100)) + '%'; }
function clearResults(){ notesTableBody.innerHTML = ''; visualArea.style.display = 'none'; midiLink.style.display = 'none'; }

function noteNameToMidi(note){
  const letters = {'C':0,'C#':1,'D':2,'D#':3,'E':4,'F':5,'F#':6,'G':7,'G#':8,'A':9,'A#':10,'B':11};
  const m = note.match(/^([A-G]#?)(-?\d+)/);
  if(!m) return null;
  const name = m[1], oct = parseInt(m[2],10);
  return (oct+1)*12 + letters[name];
}
function roundRect(ctx, x, y, w, h, r){
  ctx.beginPath();
  ctx.moveTo(x+r,y);
  ctx.arcTo(x+w,y, x+w,y+h, r);
  ctx.arcTo(x+w,y+h, x,y+h, r);
  ctx.arcTo(x,y+h, x,y, r);
  ctx.arcTo(x,y, x+w,y, r);
  ctx.closePath();
  ctx.fill();
}
function drawPianoRoll(events){
  const ctx = prCanvas.getContext('2d');
  const maxT = Math.max(...events.map(e=>e.end), 1);
  const midiVals = events.map(e=>noteNameToMidi(e.note)).filter(v=>v!==null);
  const minP = Math.min(...midiVals)-2, maxP = Math.max(...midiVals)+2;
  const w = Math.max(800, Math.round(maxT*120));
  prCanvas.width = w;
  ctx.clearRect(0,0,w,prCanvas.height);
  const pxPerSec = prCanvas.width / (maxT+1e-6);
  const rowH = prCanvas.height / (maxP-minP+1);
  for(let ev of events){
    const midi = noteNameToMidi(ev.note);
    const px = ev.start*pxPerSec, pw = (ev.end-ev.start)*pxPerSec;
    const py = (maxP - midi) * rowH;
    const hue = 200 - ((midi % 12) / 12) * 120;
    ctx.fillStyle = `hsla(${hue},80%,60%,0.9)`;
    roundRect(ctx, px+2, py+2, pw-4, Math.max(6,rowH-4), 3);
    ctx.fillStyle = '#0f172a';
    ctx.font = '11px Inter';
    ctx.fillText(ev.note, px+6, py+rowH-6);
  }
}
function makeAbsoluteUrl(path){
  if(!path) return null;
  if(path.startsWith('http')) return path;
  if(path.startsWith('/')) return API_BASE + path;
  return API_BASE + '/' + path;
}
function displayResults(events, midi_file){
  notesTableBody.innerHTML = '';
  events.forEach((ev,i)=>{
    notesTableBody.innerHTML += `<tr><td>${i+1}</td><td>${ev.note}</td><td>${ev.start.toFixed(2)}</td><td>${ev.end.toFixed(2)}</td><td>${(ev.end-ev.start).toFixed(2)}</td></tr>`;
  });
  if(midi_file){
    midiLink.href = makeAbsoluteUrl(midi_file);
    midiLink.style.display = 'inline';
  }
  visualArea.style.display = 'block';
  drawPianoRoll(events);
}

sendBtn.onclick = async ()=>{
  if(!fileInput.files.length){ alert("Chọn file audio trước"); return; }
  const f = fileInput.files[0];
  clearResults();
  out.innerText = `Đang tải ${f.name} ...`;
  setProgress(0.1);
  const form = new FormData();
  form.append('file', f, f.name);
  try{
    const resp = await fetch(API, { method:'POST', body: form });
    if(!resp.ok) throw new Error('Lỗi server ' + resp.status);
    const j = await resp.json();
    setProgress(0.9);
    let events = j.events || j.notes || [];
    events = events.map(e=>({
      note: e.note || e[0],
      start: parseFloat(e.start || e[1] || 0),
      end: parseFloat(e.end || e[2] || 0)
    }));
    displayResults(events, j.midi_file);
    out.innerText = "Hoàn tất.";
    setProgress(1);
  }catch(err){
    out.innerText = "Lỗi: " + err;
    setProgress(0);
  }
};
</script>
</body>
</html>
