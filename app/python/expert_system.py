import json
import sys
import os

class HPExpertSystem:
    def __init__(self):
        self.rules = self.load_rules()
        self.recommendations = []
        
    def load_rules(self):
        return {
            # Segmentasi Budget
            "budget_rules": [
                {
                    "name": "Entry Level",
                    "condition": lambda data: data.get('budget', 0) <= 2000000,
                    "action": lambda data: {
                        "tipe_pengguna": "Entry-Level",
                        "spesifikasi_minimal": {"RAM": "≤ 4GB", "Chipset": "Low-End", "Fungsi": "dasar saja"}
                    }
                },
                {
                    "name": "Mid Range", 
                    "condition": lambda data: 2000000 < data.get('budget', 0) <= 4000000,
                    "action": lambda data: {
                        "tipe_pengguna": "Mid-Range",
                        "spesifikasi_minimal": {"RAM": "6–8GB", "Chipset": "Mid-Range", "Baterai": "≥ 5000 mAh"}
                    }
                },
                {
                    "name": "Flagship",
                    "condition": lambda data: data.get('budget', 0) >= 5000000,
                    "action": lambda data: {
                        "tipe_pengguna": "Flagship", 
                        "spesifikasi_minimal": {"RAM": "12–16GB", "Chipset": "Flagship", "Baterai": "≥ 6000 mAh"}
                    }
                }
            ],
            
            # Kebutuhan Utama
            "kebutuhan_rules": [
                {
                    "name": "Fotografi",
                    "condition": lambda data: data.get('kebutuhan') == "Fotografi",
                    "action": lambda data: {"prioritas": ["Sensor besar", "OIS", "EIS", "Lensa berkualitas"]}
                },
                {
                    "name": "Videografi",
                    "condition": lambda data: data.get('kebutuhan') == "Videografi", 
                    "action": lambda data: {"prioritas": ["OIS+EIS", "Perekaman 4K", "Chipset kuat", "Mikrofon berkualitas"]}
                },
                {
                    "name": "Gaming Durasi Tinggi",
                    "condition": lambda data: data.get('kebutuhan') == "Gaming" and data.get('durasi_gaming', 0) >= 2,
                    "action": lambda data: {"cooling": "Wajib cooling bagus"}
                },
                {
                    "name": "Gaming Durasi Rendah", 
                    "condition": lambda data: data.get('kebutuhan') == "Gaming" and data.get('durasi_gaming', 0) < 2,
                    "action": lambda data: {"cooling": "Cooling standar cukup"}
                },
                {
                    "name": "Gaming Budget Tinggi",
                    "condition": lambda data: data.get('kebutuhan') == "Gaming" and data.get('budget', 0) >= 8000000,
                    "action": lambda data: {"spesifikasi": {"RAM": "≥ 12GB", "Chipset": "Flagship"}}
                },
                {
                    "name": "Produktivitas",
                    "condition": lambda data: data.get('kebutuhan') == "Produktivitas",
                    "action": lambda data: {"prioritas": ["Storage UFS", "RAM besar", "Multitasking lancar"]}
                },
                {
                    "name": "Harian", 
                    "condition": lambda data: data.get('kebutuhan') == "Harian",
                    "action": lambda data: {"prioritas": ["Baterai besar", "Chipset efisien"]}
                }
            ],
            
            # Trade-off Rules
            "tradeoff_rules": [
                {
                    "name": "Budget Rendah Gaming",
                    "condition": lambda data: data.get('budget', 0) <= 3000000 and data.get('kebutuhan') == "Gaming",
                    "action": lambda data: {"trade_off": "Mengorbankan kamera + layar demi chipset kuat"}
                },
                {
                    "name": "Budget Rendah Fotografi",
                    "condition": lambda data: data.get('budget', 0) <= 3000000 and data.get('kebutuhan') == "Fotografi",
                    "action": lambda data: {"trade_off": "Meningkatkan kamera meski chipset sedang"}
                }
            ],
            
            # Avoid Rules
            "avoid_rules": [
                {
                    "name": "Hindari Non-AMOLED Budget Tinggi",
                    "condition": lambda data: data.get('budget', 0) >= 4000000 and data.get('layar') != "AMOLED",
                    "action": lambda data: {"rekomendasi": "Tolak / Hindari", "alasan": "Layar bukan AMOLED di budget tinggi"}
                },
                {
                    "name": "Hindari Chipset Low-End Budget Tinggi",
                    "condition": lambda data: data.get('budget', 0) >= 4000000 and data.get('chipset') == "Low-End",
                    "action": lambda data: {"rekomendasi": "Tolak / Hindari", "alasan": "Chipset Low-End di budget tinggi"}
                }
            ]
        }
    
    def forward_chaining(self, user_data):
        """Forward Chaining: Data-driven reasoning"""
        results = {}
        
        # Eksekusi semua rules
        for rule_type, rules in self.rules.items():
            for rule in rules:
                try:
                    if rule["condition"](user_data):
                        result = rule["action"](user_data)
                        results.update(result)
                        self.recommendations.append({
                            "rule": rule["name"],
                            "result": result
                        })
                except Exception as e:
                    print(f"Error in rule {rule['name']}: {e}")
        
        return results
    
    def get_recommendation(self, user_data):
        """Main method untuk mendapatkan rekomendasi"""
        self.recommendations = []
        result = self.forward_chaining(user_data)
        
        # Generate final recommendation
        final_recommendation = self.generate_final_recommendation(result, user_data)
        
        return {
            "user_data": user_data,
            "rule_results": result,
            "recommendations": self.recommendations,
            "final_recommendation": final_recommendation
        }
    
    def generate_final_recommendation(self, results, user_data):
        """Generate final recommendation berdasarkan semua rules"""
        budget = user_data.get('budget', 0)
        kebutuhan = user_data.get('kebutuhan', '')
        
        # Base recommendation berdasarkan budget
        if budget <= 2000000:
            base = "Rekomendasi Entry-Level: Fokus pada value for money, baterai tahan lama"
        elif budget <= 4000000:
            base = "Rekomendasi Mid-Range: Balance antara performa dan fitur"
        else:
            base = "Rekomendasi Flagship: Performa maksimal dengan fitur premium"
        
        # Spesifik berdasarkan kebutuhan
        if kebutuhan == "Gaming":
            spec = "Prioritas: Chipset gaming, cooling system, refresh rate tinggi"
        elif kebutuhan == "Fotografi":
            spec = "Prioritas: Kamera multi-lens, OIS, sensor besar"
        elif kebutuhan == "Videografi":
            spec = "Prioritas: Stabilisasi video, kualitas audio, storage besar"
        elif kebutuhan == "Produktivitas":
            spec = "Prioritas: RAM besar, multitasking, layar nyaman"
        else:  # Harian
            spec = "Prioritas: Baterai besar, daya tahan, performa stabil"
        
        return f"{base}. {spec}"

def main():
    # Read input from Laravel
    input_data = sys.stdin.read()
    user_data = json.loads(input_data)
    
    # Process with expert system
    expert = HPExpertSystem()
    result = expert.get_recommendation(user_data)
    
    # Output result for Laravel
    print(json.dumps(result))

if __name__ == "__main__":
    main()