"""
label_parser.py

Parses DMD OpenLABEL/VCD JSON annotation files (face_setup_n_labels.json)
and fills the `class` column of landmarks_setup_n.csv with per-frame labels.

Classes (per README):
    alert | drowsy | microsleep

Priority when multiple annotations overlap on the same frame:
    microsleep > drowsy > alert

Temporal Logic:
    - Normal blinks override 'closing/close' states to prevent false drowsy flags.
    - 'eyes_state/close' lasting >= 0.5s is continuously and retroactively labeled 'microsleep'.
"""

import json
from pathlib import Path
import pandas as pd

REPO_ROOT      = Path(__file__).resolve().parent
PROJECT_ROOT   = REPO_ROOT.parent
VIDEO_DATASET  = PROJECT_ROOT / "Video Dataset"
CSV_DATASET    = PROJECT_ROOT / "CSV Dataset"

SETUPS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15]
FPS = 30  # Assumed frames per second for microsleep calculation

def build_frame_labels(json_path: Path, num_frames: int) -> list[str]:
    """
    Read the JSON's action catalog and produce a label for every frame from
    0 to num_frames-1, applying temporal logic for blinks and microsleeps.
    """
    with open(json_path, "r", encoding="utf-8") as f:
        data = json.load(f)

    actions = data["openlabel"].get("actions", {})

    # 1. Pre-compute active actions per frame for fast lookup
    active_actions_per_frame = [set() for _ in range(num_frames)]
    unmapped_types: set[str] = set()
    
    known_action_types = {
        "eyes_state/open", "eyes_state/opening", "eyes_state/close", "eyes_state/closing",
        "blinks/blinking", "yawning/Yawning with hand", "yawning/Yawning without hand"
    }

    for _action_id, action_def in actions.items():
        atype = action_def.get("type", "")
        
        if atype not in known_action_types:
            unmapped_types.add(atype)
            continue

        for interval in action_def.get("frame_intervals", []):
            start = max(interval["frame_start"], 0)
            end   = min(interval["frame_end"], num_frames - 1)
            for frame in range(start, end + 1):
                active_actions_per_frame[frame].add(atype)

    if unmapped_types:
        print(f"  [info] ignored (not in temporal mapping): {sorted(unmapped_types)}")

    # 2. Iterate chronologically to apply temporal and priority logic
    labels = ["alert"] * num_frames
    
    microsleep_threshold = int(FPS * 0.5)  # e.g., 15 frames for 30fps
    consecutive_close_count = 0
    close_start_idx = -1

    for frame in range(num_frames):
        active_actions = active_actions_per_frame[frame]

        is_blinking = "blinks/blinking" in active_actions
        has_yawn = "yawning/Yawning with hand" in active_actions or "yawning/Yawning without hand" in active_actions
        is_eye_closed = "eyes_state/close" in active_actions
        
        is_eye_closing = "eyes_state/closing" in active_actions

        # Apply base hierarchy (Drowsy > Alert, ignoring standard blinks)
        if has_yawn:
            labels[frame] = "drowsy"
        elif (is_eye_closed or is_eye_closing) and not is_blinking:
            labels[frame] = "drowsy"
        else:
            labels[frame] = "alert"

        # 3. Track Microsleep (Microsleep > Drowsy > Alert)
        if is_eye_closed and not has_yawn:  # ← don't count eye closure during yawns
            if consecutive_close_count == 0:
                close_start_idx = frame
            consecutive_close_count += 1

            if consecutive_close_count >= microsleep_threshold:
                for i in range(close_start_idx, frame + 1):
                    labels[i] = "microsleep"
        else:
            consecutive_close_count = 0
            close_start_idx = -1

    return labels

def process_setup(setup_n: int) -> None:
    """Patch the `class` column of one setup's landmarks CSV in place."""
    csv_path  = CSV_DATASET / f"landmarks_setup_{setup_n}.csv"
    json_path = VIDEO_DATASET / f"setup_{setup_n}" / f"face_setup_{setup_n}_labels.json"

    if not csv_path.exists():
        print(f"[skip] setup_{setup_n}: CSV not found at {csv_path}")
        return
    if not json_path.exists():
        print(f"[skip] setup_{setup_n}: JSON not found at {json_path}")
        return

    print(f"[run]  setup_{setup_n}")
    df = pd.read_csv(csv_path)

    if "class" not in df.columns:
        print(f"  [warn] no 'class' column in {csv_path.name}; skipping")
        return

    num_frames = len(df)
    labels     = build_frame_labels(json_path, num_frames)

    df["class"] = labels

    counts = df["class"].value_counts().to_dict()
    print(f"  frames: {num_frames}, label counts: {counts}")

    df.to_csv(csv_path, index=False)
    print(f"  [ok] wrote {csv_path.name}")

def main() -> None:
    print(f"Video dataset: {VIDEO_DATASET}")
    print(f"CSV dataset:   {CSV_DATASET}\n")

    for n in SETUPS:
        process_setup(n)

if __name__ == "__main__":
    main()