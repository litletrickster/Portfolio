## 📦 Model Setup (Qwen2.5 + Piper TTS Update)

This project uses:

* **Qwen2.5-1.5B-Instruct-Q4_K_M.gguf** as the primary LLM for the advisory system.
* **Piper TTS (en_US-lessac-medium)** for local text-to-speech generation.

Before running the project, download the required model files and place them inside the `Models/` directory.

### Required Files

#### LLM

* `Qwen2.5-1.5B-Instruct-Q4_K_M.gguf`

#### Piper TTS

* `en_US-lessac-medium.onnx`
* `en_US-lessac-medium.onnx.json`

---

### Download Instructions

Run the following commands in your terminal:

```bash
mkdir -p Models

# Qwen2.5 LLM
wget https://huggingface.co/bartowski/Qwen2.5-1.5B-Instruct-GGUF/resolve/main/Qwen2.5-1.5B-Instruct-Q4_K_M.gguf \
  -O Models/Qwen2.5-1.5B-Instruct-Q4_K_M.gguf

# Piper TTS Model
wget https://huggingface.co/rhasspy/piper-voices/resolve/main/en/en_US/lessac/medium/en_US-lessac-medium.onnx \
  -O Models/en_US-lessac-medium.onnx

# Piper TTS Configuration
wget https://huggingface.co/rhasspy/piper-voices/resolve/main/en/en_US/lessac/medium/en_US-lessac-medium.onnx.json \
  -O Models/en_US-lessac-medium.onnx.json
```

The resulting directory structure should look like:

```text
Models/
├── Qwen2.5-1.5B-Instruct-Q4_K_M.gguf
├── en_US-lessac-medium.onnx
└── en_US-lessac-medium.onnx.json
```

---

---

## 📦 Legacy Model Setup (SmolLM2)

> This section is retained for historical reference. The project now uses **Qwen2.5-1.5B** as the primary model.

Before running older versions of the project, you need to download the required model file:

**`SmolLM2-135M-Instruct-Q4_K_M.gguf`**

It should be placed inside the `Models/` directory.

---

### Download Instructions

Run the following commands in your terminal:

```bash
mkdir -p Models

wget https://huggingface.co/unsloth/SmolLM2-135M-Instruct-GGUF/resolve/main/SmolLM2-135M-Instruct-Q4_K_M.gguf \
  -O Models/SmolLM2-135M-Instruct-Q4_K_M.gguf
```
