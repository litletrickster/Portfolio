# LLM Advisor Pipeline: Iteration History & Performance Logs

This document tracks the iterative improvements made to the ZMQ-based LLM Advisory pipeline, transitioning from basic unstructured outputs to a robust, structured JSON format with dynamic RAG retrieval.

---

## Phase 1: Old Setup but with Qwen (Baseline)

### Overall Idea
Testing the newly integrated Qwen 2.5 (1.5B) model using the original prompt architecture. The prompt relied heavily on negative constraints (e.g., "Do NOT repeat", "Do NOT explain") and asked for a single, unstructured sentence.

### Changes Made
* Swapped out the previous model for Qwen 2.5-1.5B.
* Kept the original `build_prompt` and main loop logic intact.

### Evaluation
* **Good:** The model successfully executed the instructions and generated concise, single-sentence outputs. Execution time was fast (under 6 seconds).
* **Bad:** The reliance on negative constraints is brittle for small models. The output was raw text, making it difficult to safely extract metadata or handle errors programmatically if the model added conversational filler (e.g., "Sure, here is your tip:").

### Output Data
```text
LLM Advisor (TTS mode) listening on port 5555...
Waiting for payloads...

[RECEIVED] {'microsleep_events': 1, 'drowsy_events': 0, 'longest_microsleep_frames': 17, 'longest_drowsy_frames': 0, 'drowsy_trend': 0}
[LLM] (3.69s) severity=LOW
[TTS READY] → Wake up and take a short nap if you feel drowsy.
[REFS USED] ['nhtsa_micro_sleep_001', 'nhtsa_intervention_002']

[RECEIVED] {'microsleep_events': 4, 'drowsy_events': 0, 'longest_microsleep_frames': 388, 'longest_drowsy_frames': 0, 'drowsy_trend': 3}
[LLM] (2.92s) severity=CRITICAL
[TTS READY] → Pull over immediately and take a nap for at least 20 minutes.
[REFS USED] ['nhtsa_micro_sleep_001', 'nhtsa_intervention_002']

[RECEIVED] {'microsleep_events': 0, 'drowsy_events': 1, 'longest_microsleep_frames': 0, 'longest_drowsy_frames': 46, 'drowsy_trend': -58}
[LLM] (4.69s) severity=LOW
[TTS READY] → Take a short nap in a safe spot.
[REFS USED] ['allstate_006', 'nhtsa_intervention_002']

```

---

## Phase 2: Qwen 2.5 Strict JSON Setup (Before Random Retrieval)

### Overall Idea

Transforming the LLM from a simple text generator into a structured data tool. By forcing the LLM to output a strict JSON payload, we can safely separate the spoken message from backend metadata.

### Changes Made

* Redesigned the prompt using positive constraints ("Act as an AI co-pilot").
* Enforced a strict JSON schema requiring three keys: `actionable_tip`, `estimated_urgency`, and `intervention_type`.
* Pre-filled the assistant's generation with `{` to lock the model into JSON formatting.
* Added granular timing metrics (LLM execution time vs. Total Pipeline time).

### Evaluation

* **Good:** Highly reliable structure. The output is easily parsed by Python, and the addition of `intervention_type` provides useful metadata for the UI/HUD.
* **Bad:** The RAG retrieval was purely deterministic based on keyword scoring. Because the knowledge base heavily featured "nap" and "caffeine" chunks, they scored the highest every time, resulting in repetitive advice regardless of the specific telemetry data.

### Output Data

```text
LLM Advisor (TTS JSON mode) listening on port 5555...
Waiting for payloads...

[RECEIVED] {'microsleep_events': 5, 'drowsy_events': 0, 'longest_microsleep_frames': 416, 'longest_drowsy_frames': 0, 'drowsy_trend': 0}
[LLM ONLY TIME]  → 7.34s
[TOTAL PIPELINE] → 7.34s (From Payload to Output)
[METADATA]       → Urgency: HIGH | Type: Focus
[TTS READY]      → Stay focused. You're driving a vehicle, not a sleeping bag. If you feel sleepy, pull over and take a nap. It's safer than risking a crash. Don't risk it.
[REFS USED]      → ['nhtsa_micro_sleep_001', 'nhtsa_intervention_002']

[RECEIVED] {'microsleep_events': 0, 'drowsy_events': 6, 'longest_microsleep_frames': 0, 'longest_drowsy_frames': 514, 'drowsy_trend': 41}
[LLM ONLY TIME]  → 8.33s
[TOTAL PIPELINE] → 8.33s (From Payload to Output)
[METADATA]       → Urgency: HIGH | Type: Rest
[TTS READY]      → It's crucial to stay alert while driving. The longer you drive, the more drowsiness sets in. Pull over and take a short break, drink a cup of coffee, and rest. Remember, caffeine can help, but sleep is essential for complete recovery.
[REFS USED]      → ['allstate_006', 'nhtsa_intervention_002']

```

---

## Phase 3: JSON Setup with Random Retrieval & Updated Severity

### Overall Idea

Fixing the repetitive advice issue by introducing controlled randomness into the RAG pipeline, and tuning the severity algorithm to account for continuous frame durations (e.g., distinguishing between a 1-second blink and a 15-second microsleep).

### Changes Made

* Updated `retrieve_chunks` to score all references, take the Top 5, and *randomly* select 2 to pass to the LLM.
* Increased prompt `max_tokens` and instructed the LLM to write 2-3 natural conversational sentences instead of just one.
* Updated `compute_severity` to trigger CRITICAL/HIGH alerts based on raw frame counts (`longest_microsleep_frames`, `longest_drowsy_frames`).

### Evaluation

* **Good:** The TTS tips became significantly more varied, empathetic, and conversational. The AI sounded much more like a natural co-pilot.
* **Bad:** The new frame-based `compute_severity` logic was slightly over-engineered. Generation times increased (13–16s) due to the model generating 3 full sentences.

### Output Data

```text
LLM Advisor (TTS JSON mode) listening on port 5555...
Waiting for payloads...

[RECEIVED] {'microsleep_events': 0, 'drowsy_events': 2, 'longest_microsleep_frames': 0, 'longest_drowsy_frames': 70, 'drowsy_trend': -48}
[LLM ONLY TIME]  → 13.44s
[TOTAL PIPELINE] → 13.44s (From Payload to Output)
[METADATA]       → Urgency: HIGH | Type: Nap
[TTS READY]      → Pull over safely, drink one to two cups of coffee, and take a brief nap. Caffeine may take around 30 minutes to take effect, so rest is essential.
[REFS USED]      → ['allstate_006', 'nhtsa_intervention_002']

[RECEIVED] {'microsleep_events': 3, 'drowsy_events': 0, 'longest_microsleep_frames': 517, 'longest_drowsy_frames': 0, 'drowsy_trend': -1}
[LLM ONLY TIME]  → 16.43s
[TOTAL PIPELINE] → 16.43s (From Payload to Output)
[METADATA]       → Urgency: HIGH | Type: Rest
[TTS READY]      → It's crucial to stay alert and focused on the road. If you've had a microsleep, take a moment to regain your alertness. Even a short nap can help improve your alertness. Remember, drowsy driving is a serious risk, so it's important to stay awake and alert at all times.
[REFS USED]      → ['nhtsa_micro_sleep_001', 'nhtsa_intervention_002']

```

---

## Phase 4: Most Updated Code (Reverting to Original `compute_severity`)

### Overall Idea

Locking in the best parts of the pipeline (JSON enforcement, multi-sentence conversational TTS, randomized RAG selection) while rolling back the severity logic to its simpler, more stable original form.

### Changes Made

* Maintained the strict JSON prompt and the updated 2-3 sentence length requirement.
* Maintained the randomized RAG retrieval to keep advice varied.
* **Reverted** the `compute_severity` Python function back to solely tracking event counts (`microsleep_events`, `drowsy_events`) and `drowsy_trend`, dropping the frame-duration logic.

### Evaluation

* **Good:** The severity classification is simpler and highly predictable. The TTS output remains varied and naturally phrased.
* **Bad:** By reverting the logic, the system no longer differentiates between a single, short drowsy event and a massive, continuous 800-frame (26-second) event—both are treated equally if the event count is the same.

### Output Data

```text
LLM Advisor (TTS JSON mode) listening on port 5555...
Waiting for payloads...

[RECEIVED] {'microsleep_events': 0, 'drowsy_events': 1, 'longest_microsleep_frames': 0, 'longest_drowsy_frames': 811, 'drowsy_trend': -1}
[LLM ONLY TIME]  → 13.18s
[TOTAL PIPELINE] → 13.18s (From Payload to Output)
[METADATA]       → Urgency: HIGH | Type: Nap
[TTS READY]      → Pull over to a safe location and take a 20-minute nap. It can help improve alertness. If you can't take a nap, try drinking a caffeinated beverage. Caffeine can help you stay awake for about an hour, but it's not a long-term solution.
[REFS USED]      → ['sleepfoundation_003', 'gcphn_004']

[RECEIVED] {'microsleep_events': 3, 'drowsy_events': 0, 'longest_microsleep_frames': 561, 'longest_drowsy_frames': 0, 'drowsy_trend': 0}
[LLM ONLY TIME]  → 12.19s
[TOTAL PIPELINE] → 12.19s (From Payload to Output)
[METADATA]       → Urgency: HIGH | Type: Focus
[TTS READY]      → Hey driver, it looks like you've had some microsleeps recently. It's important to stay alert and focused. Take a moment to stretch your legs or find a safe place to rest. Remember, staying awake is crucial for your safety and the safety of others on the road.
[REFS USED]      → ['gcphn_high_risk_times_009', 'nhtsa_micro_sleep_001']

```
