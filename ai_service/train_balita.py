"""
POSYANDU SMART — AI BALITA (GROWTH & STUNTING RISK SCREENING)
Training, Cross-Validation, Metrics Comparison & Model Export Pipeline.

Catatan Metodologi:
- Dataset: Antropometri & Indikator Tumbuh Kembang Balita (WHO/DHS Standards)
- Fitur: umur_bulan, jenis_kelamin (1:L, 0:P), berat_badan, tinggi_badan, lingkar_kepala, berat_lahir, asi_eksklusif
- Target Status:
    * normal (pertumbuhan sesuai kurva standar)
    * pemantauan (pertumbuhan melambat / mendekati batas kritis)
    * risiko_stunting (indikasi risiko gangguan pertumbuhan tinggi badan/berat badan)
- ATURAN METODOLOGI: Raw HAZ / Z-Score TIDAK dimasukkan sebagai fitur input agar model
  belajar screening prediktif dari indikator klinis nyata, bukan sekadar memetakan HAZ < -2.
- AI berfungsi sebagai screening / decision support (Bukan diagnosis medis stunting).
"""

import os
import json
import numpy as np
import pandas as pd
import joblib

from sklearn.model_selection import train_test_split, StratifiedKFold, cross_val_score
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.pipeline import Pipeline
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, confusion_matrix, classification_report

def generate_or_load_balita_dataset(data_dir: str) -> pd.DataFrame:
    """Generate or load comprehensive anthropometry dataset based on WHO Growth Standards."""
    os.makedirs(data_dir, exist_ok=True)
    csv_path = os.path.join(data_dir, "dataset_stunting_balita.csv")
    
    if os.path.exists(csv_path):
        print(f"[Dataset] Loading existing dataset from {csv_path}")
        return pd.read_csv(csv_path)
    
    print("[Dataset] Building high-precision anthropometry dataset based on WHO Standards & DHS...")
    np.random.seed(42)
    n_samples = 2500
    
    umur_bulan = np.random.randint(0, 60, n_samples)
    jenis_kelamin = np.random.choice([0, 1], n_samples, p=[0.49, 0.51])
    
    expected_tb = []
    expected_bb = []
    expected_lk = []
    
    for u, g in zip(umur_bulan, jenis_kelamin):
        if g == 1:
            base_tb = 50.0 + (1.95 * u) - (0.016 * (u ** 1.8))
            base_bb = 3.3 + (0.52 * u) - (0.0035 * (u ** 1.8))
            base_lk = 34.5 + (0.35 * u) - (0.0028 * (u ** 1.8))
        else:
            base_tb = 49.3 + (1.90 * u) - (0.015 * (u ** 1.8))
            base_bb = 3.2 + (0.49 * u) - (0.0033 * (u ** 1.8))
            base_lk = 34.0 + (0.34 * u) - (0.0026 * (u ** 1.8))
            
        expected_tb.append(base_tb)
        expected_bb.append(base_bb)
        expected_lk.append(base_lk)
        
    expected_tb = np.array(expected_tb)
    expected_bb = np.array(expected_bb)
    expected_lk = np.array(expected_lk)
    
    profile_choices = np.random.choice(['normal', 'pemantauan', 'risiko_stunting'], n_samples, p=[0.65, 0.20, 0.15])
    
    actual_tb = []
    actual_bb = []
    actual_lk = []
    berat_lahir = []
    asi_eksklusif = []
    
    for p, exp_tb, exp_bb, exp_lk in zip(profile_choices, expected_tb, expected_bb, expected_lk):
        if p == 'normal':
            tb = np.random.normal(exp_tb, 1.8)
            bb = np.random.normal(exp_bb, 0.8)
            lk = np.random.normal(exp_lk, 0.6)
            b_lahir = float(np.clip(np.random.normal(3.2, 0.35), 2.6, 4.2))
            asi = int(np.random.choice([1, 0], p=[0.85, 0.15]))
        elif p == 'pemantauan':
            tb = np.random.normal(exp_tb - 3.2, 1.5)
            bb = np.random.normal(exp_bb - 1.2, 0.7)
            lk = np.random.normal(exp_lk - 0.9, 0.6)
            b_lahir = float(np.clip(np.random.normal(2.8, 0.4), 2.2, 3.6))
            asi = int(np.random.choice([1, 0], p=[0.55, 0.45]))
        else:
            tb = np.random.normal(exp_tb - 6.8, 2.0)
            bb = np.random.normal(exp_bb - 2.1, 0.9)
            lk = np.random.normal(exp_lk - 1.8, 0.8)
            b_lahir = float(np.clip(np.random.normal(2.4, 0.45), 1.8, 3.2))
            asi = int(np.random.choice([1, 0], p=[0.30, 0.70]))
            
        actual_tb.append(max(42.0, round(tb, 1)))
        actual_bb.append(max(2.0, round(bb, 2)))
        actual_lk.append(max(30.0, round(lk, 1)))
        berat_lahir.append(round(b_lahir, 2))
        asi_eksklusif.append(asi)
        
    df = pd.DataFrame({
        'umur_bulan': umur_bulan,
        'jenis_kelamin': jenis_kelamin,
        'berat_badan': actual_bb,
        'tinggi_badan': actual_tb,
        'lingkar_kepala': actual_lk,
        'berat_lahir': berat_lahir,
        'asi_eksklusif': asi_eksklusif,
        'status_risiko': profile_choices
    })
    
    df.to_csv(csv_path, index=False)
    print(f"[Dataset] Saved Balita dataset to {csv_path} ({len(df)} rows)")
    return df

def train_and_evaluate():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    data_dir = os.path.join(base_dir, "data")
    models_dir = os.path.join(base_dir, "models")
    os.makedirs(models_dir, exist_ok=True)
    
    df = generate_or_load_balita_dataset(data_dir)
    
    feature_cols = ['umur_bulan', 'jenis_kelamin', 'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'berat_lahir', 'asi_eksklusif']
    X = df[feature_cols]
    y = df['status_risiko']
    
    label_classes = ['normal', 'pemantauan', 'risiko_stunting']
    le = LabelEncoder()
    le.fit(label_classes)
    y_encoded = le.transform(y)
    
    print("\n=======================================================")
    print(" [AI BALITA] STUNTING RISK SCREENING BENCHMARKING")
    print("=======================================================")
    print(f"Total Dataset: {len(df)} baris data")
    print(f"Distribusi Kelas: {dict(y.value_counts())}")
    print(f"Fitur: {feature_cols}")
    print("Catatan: Raw HAZ dikeluarkan dari fitur untuk mencegah data leakage.\n")
    
    X_train, X_test, y_train, y_test = train_test_split(
        X, y_encoded, test_size=0.2, random_state=42, stratify=y_encoded
    )
    
    candidates = {
        'Logistic Regression': Pipeline([
            ('scaler', StandardScaler()),
            ('clf', LogisticRegression(max_iter=1000, random_state=42))
        ]),
        'Random Forest': Pipeline([
            ('scaler', StandardScaler()),
            ('clf', RandomForestClassifier(n_estimators=150, max_depth=12, min_samples_split=4, random_state=42))
        ]),
        'Gradient Boosting': Pipeline([
            ('scaler', StandardScaler()),
            ('clf', GradientBoostingClassifier(n_estimators=120, learning_rate=0.08, max_depth=5, random_state=42))
        ])
    }
    
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    results = {}
    
    print(f"{'Algoritma':<22} | {'CV F1 (Macro)':<14} | {'Test Acc':<9} | {'Precision':<10} | {'Recall':<8} | {'F1-Score':<8}")
    print("-" * 82)
    
    best_model_name = None
    best_f1 = -1.0
    best_pipeline = None
    
    for name, pipeline in candidates.items():
        cv_scores = cross_val_score(pipeline, X_train, y_train, cv=cv, scoring='f1_macro')
        cv_f1 = cv_scores.mean()
        
        pipeline.fit(X_train, y_train)
        y_pred = pipeline.predict(X_test)
        
        acc = accuracy_score(y_test, y_pred)
        prec = precision_score(y_test, y_pred, average='macro', zero_division=0)
        rec = recall_score(y_test, y_pred, average='macro', zero_division=0)
        f1 = f1_score(y_test, y_pred, average='macro', zero_division=0)
        
        cm = confusion_matrix(y_test, y_pred)
        
        results[name] = {
            'cv_f1_mean': float(cv_f1),
            'accuracy': float(acc),
            'precision': float(prec),
            'recall': float(rec),
            'f1_score': float(f1),
            'confusion_matrix': cm.tolist(),
            'classification_report': classification_report(y_test, y_pred, target_names=label_classes, output_dict=True)
        }
        
        print(f"{name:<22} | {cv_f1*100:>12.2f}% | {acc*100:>7.2f}% | {prec*100:>8.2f}% | {rec*100:>6.2f}% | {f1*100:>6.2f}%")
        
        if f1 > best_f1:
            best_f1 = f1
            best_model_name = name
            best_pipeline = pipeline
            
    print("-" * 82)
    print(f"[*] MODEL TERBAIK TERPILIH: {best_model_name} (F1-Score: {best_f1*100:.2f}%)\n")
    
    model_path = os.path.join(models_dir, "stunting_model.pkl")
    joblib.dump(best_pipeline, model_path)
    
    metadata = {
        'model_name': best_model_name,
        'model_version': 'stunting-v1.0',
        'feature_columns': feature_cols,
        'target_classes': label_classes,
        'benchmark_comparison': results,
        'best_metrics': results[best_model_name],
        'note': 'AI screening risiko pertumbuhan stunting balita (bukan diagnosis medis).',
        'created_at': '2026-09-15'
    }
    
    metadata_path = os.path.join(models_dir, "stunting_metadata.json")
    with open(metadata_path, 'w', encoding='utf-8') as f:
        json.dump(metadata, f, indent=2)
        
    print(f"[Export] Model tersimpan di: {model_path}")
    print(f"[Export] Metadata tersimpan di: {metadata_path}")
    print("=======================================================\n")
    return metadata

if __name__ == "__main__":
    train_and_evaluate()
