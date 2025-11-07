# Sử dụng Python base image
FROM python:3.11-slim

# Tạo thư mục làm việc trong container
WORKDIR /app

# Sao chép file requirements trước để cài dependencies
COPY requirements.txt .

# Cài đặt dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Sao chép toàn bộ code backend vào container
COPY . .

# Expose cổng 8000 cho FastAPI
EXPOSE 8000

# Chạy FastAPI server bằng uvicorn
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
